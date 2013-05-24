<?php
/**
 *
 * Keeps track of votes for coupons
 * @author AppThemes
 *
 *
 */

// define the table names used
$table_votes = $wpdb->clpr_votes;
$table_votes_total = $wpdb->clpr_votes_total;


// main voting function included on each coupon
// called via jQuery thumbsVote function
function clpr_vote_update() {
	global $wpdb, $app_abbr, $table_votes, $table_votes_total;
	
	// set all the params passed in via the jQuery click
	$post_id  = trim($_POST['pid']);
	$user_id  = trim($_POST['uid']);
	$vote_val = trim($_POST['vid']);	
	
	// get the local time based off WordPress setting
	$nowisnow = date('Y-m-d H:i:s', current_time('timestamp'));
	
	// get the visitors IP
	$user_ip = appthemes_get_ip();

	// update the votes up/down field depending on value passed in
	(($vote_val == 1) ? $set_vote = 'votes_up' : $set_vote = 'votes_down');

	// first try and update the existing post total counter
	$query = $wpdb->prepare("UPDATE $table_votes_total SET votes_total = votes_total+1, $set_vote = $set_vote+1 WHERE post_id = %s LIMIT 1", $post_id);
	$results = $wpdb->query($query);
	
	// no results found so let's add a new record for the post
	if ($results == 0)	
	    $wpdb->insert( $table_votes_total, array( 'post_id' => "$post_id", "$set_vote" => 1, 'votes_total' => 1, 'last_update' => "$nowisnow" ));
	
	
	// now lets update the votes table which contains all vote transactions
	
	// must be a guest visitor
	if ($user_id < 1) {		
		// first try and update the existing guest record based on IP
		$data = array(
			"post_id" => $post_id,
			"vote" => $vote_val,
			"date_stamp" => $nowisnow,
		);

		$where = array(
			"user_id" => "0",
			"post_id" => $post_id,
			"ip_address" => $user_ip
		);
		$results = $wpdb->update($table_votes, $data, $where);
		
		// no results found so let's add a new record for the guest
		if ($results == 0)
			$wpdb->insert( $table_votes, array( 'post_id' => "$post_id", 'user_id' => 0, 'vote' => "$vote_val", 'ip_address' => "$user_ip", 'date_stamp' => "$nowisnow" ));
			
	} else {
	
		// first try and update the existing logged in user record
		$data = array(
			"post_id" => $post_id,
			"vote" => $vote_val,
			"date_stamp" => $nowisnow,
		);

		$where = array(
			"user_id" => $user_id,
			"post_id" => $post_id,
			"ip_address" => $user_ip
		);
		$results = $wpdb->update($table_votes, $data, $where);
				
		// no results found so let's add a new record for the logged in user
		if ($results == 0)
			$wpdb->insert( $table_votes, array( 'post_id' => "$post_id", 'user_id' => "$user_id", 'vote' => "$vote_val", 'ip_address' => "$user_ip", 'date_stamp' => "$nowisnow" ));		
	}
	
	
	// now lets get all post ids this visitor or user has voted on already
	// so we can set the transient values in the db
	
	// must be a guest visitor
	if ($user_id < 1) {
		$query = $wpdb->prepare("SELECT post_id FROM $table_votes WHERE user_id = 0 AND ip_address = %s", $user_ip);
		$myrows = $wpdb->get_col($query);
	// must be a registered user
	} else {
		$query = $wpdb->prepare("SELECT post_id FROM $table_votes WHERE user_id = %s", $user_id);
		$myrows = $wpdb->get_col($query);
	}

	$myrows = array_values($myrows);

	// create a unique name based off the IP so we can find it in the options table
	$unique_name = md5($user_ip);
	
	// first remove the existing unique transient (if any) just to be safe	 
	delete_transient('clpr_'.$unique_name);
	
	// set the unique transient with results array to expire in 30 days
	set_transient('clpr_'.$unique_name, $myrows, 60*60*24*30); 
	
	// grab the new votes up/down for the post 
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT votes_up AS votesup, votes_down AS votesdown, votes_total AS votestotal FROM $table_votes_total WHERE post_id = %d", $post_id ) );

	// calculate the total successful percentage and round to remove all decimals	
	$votes_percent = round($row->votesup / $row->votestotal * 100);
	
	// update/create meta keys on the post so it's easy to call from the loop
	update_post_meta($post_id, $app_abbr. '_votes_up', $row->votesup);
	update_post_meta($post_id, $app_abbr. '_votes_down', $row->votesdown);
	update_post_meta($post_id, $app_abbr. '_votes_percent', $votes_percent);
	
  // updates coupon status (unreliable/publish)
  clpr_status_update($post_id);
	
	echo $votes_percent . '%'; // send back the % result so we can update the coupon % value in real-time
	die; // so it doesn't return an extra zero	
}


// check if the visitor or user has already voted
// called within the coupon loop
function clpr_vote_check($post_id, $the_trans) {
  
  // see if the transient is an array
  if (!is_array($the_trans))
    return false;	

	// see if the post id exists in the array meaning they already voted
	if ( in_array($post_id, $the_trans))
		return true; 
	else 
		return false; 		
}

// gets the transient array holding all the post ids the visitor has voted for
// called before the coupon loop and used within the loop
function clpr_vote_transient() {
	
	// setup the unique transient name based on md5ing their IP address
	$trans_name = md5(appthemes_get_ip());
	
	// get existing transient data for the visitor or user
	$the_transient = get_transient('clpr_'.$trans_name);

	return $the_transient;
}

// delete all votes when the admin option has been selected
function clpr_reset_votes(){
	global $wpdb, $app_abbr;

	// empty both voting tables
	$wpdb->query("TRUNCATE $wpdb->clpr_votes_total ;");
	$wpdb->query("TRUNCATE $wpdb->clpr_votes ;");
	
	// now clear out all visitor transients from the options table
	$sql = "DELETE FROM ". $wpdb->options ." WHERE option_name LIKE '_transient_".$app_abbr."_%' OR option_name LIKE '_transient_timeout_".$app_abbr."_%'";
	$wpdb->query($sql);
	
	// update clpr_votes_up and clpr_votes_down to 0 votes
	$sql = "UPDATE ". $wpdb->postmeta ." SET meta_value = '0' WHERE meta_key = '".$app_abbr."_votes_up' OR meta_key = '".$app_abbr."_votes_down'";
	$wpdb->query($sql);

	// update clpr_votes_percent to 100%
	$sql = "UPDATE ". $wpdb->postmeta ." SET meta_value = '100' WHERE meta_key = '".$app_abbr."_votes_percent'";
	$wpdb->query($sql);
}

// delete all votes for individual coupon
// called via jQuery resetVotes function
function clpr_reset_coupon_votes_ajax() {
	global $wpdb, $app_abbr, $current_user;
	$coupon_id  = trim($_POST['pid']);

	if( is_user_logged_in() && is_numeric($coupon_id) ) :
		if( current_user_can('manage_options') ) {
			// empty votes from both voting tables
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->clpr_votes_total WHERE post_id = '%d'", $coupon_id ) );
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->clpr_votes WHERE post_id = '%d'", $coupon_id ) );

			// update clpr_votes_down and clpr_votes_up to 0 votes
			update_post_meta($coupon_id, $app_abbr.'_votes_down', '0');
			update_post_meta($coupon_id, $app_abbr.'_votes_up', '0');

			// update clpr_votes_percent to 100%
			update_post_meta($coupon_id, $app_abbr.'_votes_percent', '100');

			// now clear out coupon id from visitor transients
			$sql = "SELECT * FROM ". $wpdb->options ." WHERE option_name LIKE '_transient_".$app_abbr."_%' AND option_value LIKE '%\"".$coupon_id."\"%'";
			$results = $wpdb->get_results($sql);
			if($results) {
				foreach($results as $result) {
					$voted_coupons = unserialize($result->option_value);
					if(!empty($voted_coupons) && is_array($voted_coupons)) {
						foreach($voted_coupons as $key => $value) {
							if($coupon_id == $value)
								unset($voted_coupons[$key]);
						}
						update_option($result->option_name, $voted_coupons);
					}
				}
			}

		}
	endif;

	die; // so it doesn't return an extra zero	
}

// creates reset coupon votes link for admin, use only in loop
function clpr_reset_coupon_votes_link() {
	global $post, $current_user;
	if( is_user_logged_in() ) :
		if( current_user_can('manage_options') ) {
			$response = "<span class=\'text\'>" . __( 'Votes has been reseted!', APP_TD ) . "</span>";
			echo '<p class="edit" id="reset_' . $post->ID . '"><a class="coupon-reset-link" onClick="resetVotes(' . $post->ID . ', \'reset_' . $post->ID . '\', \'' . $response . '\');" title="' . __( 'Reset Coupon Votes', APP_TD ) . '">' . __( 'Reset Votes', APP_TD ) . '</a></p>';
		}
	endif;
}


//Display the coupon voting widget within the loop
function clpr_vote_box($postID, $the_transient) {
    global $user_ID;

	$response = "<span class=\'text\'>" . __( 'Thanks for your response!', APP_TD ) . "</span><span class=\'checkmark\'>&nbsp;</span>";
?>

	<div class="thumbsup-vote">

		<div class="frame" id="vote_<?php the_ID(); ?>">

			<?php if (clpr_vote_check($postID, $the_transient) == false) : ?>

				<span class="text"><?php _e( 'Did this coupon work for you?', APP_TD ); ?></span>
				
				<div id="loading-<?php the_ID(); ?>" class="loading"></div>

				<div id="ajax-<?php the_ID(); ?>">
				
					<span class="vote thumbsup-up">
						<span class="thumbsup" onClick="thumbsVote(<?php echo $postID; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 1, '<?php echo $response; ?>');"></span>
					</span>
					
					<span class="vote thumbsup-down">
						<span class="thumbsdown" onClick="thumbsVote(<?php echo $postID; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 0, '<?php echo $response; ?>');"></span>
					</span>
					
				</div>

			<?php else:?>

				<?php clpr_votes_chart(); ?>
				
			<?php endif; ?>

		</div>

	</div>

<?php
}


//display the coupon success % badge within the loop
function clpr_vote_badge($postID, $the_transient) {
    global $user_ID, $app_abbr;
	
	$vpercent = round(get_post_meta($postID, $app_abbr. '_votes_percent', true));
	// figure out which color badge to show based on percentage
	if ($vpercent >= 75) $vstyle = 'green'; elseif ($vpercent >= 40 && $vpercent < 75) $vstyle = 'orange'; else $vstyle = 'red';	
	
?>
	<span class="thumbsup-badge badge-<?php echo $vstyle; ?>"><span class="percent"><?php echo $vpercent; ?>%</span><span class="success"><?php _e( 'success', APP_TD ); ?></span></span>
<?php
 
}


// display the vote results within the loop and on the admin coupon view
function clpr_votes_chart(){
global $post;
?>
	<div class="results">
		<?php // get the votes for the post
			$votes_up = get_post_meta($post->ID,'clpr_votes_up', true);
			$votes_down = get_post_meta($post->ID,'clpr_votes_down', true);
			
			// do some math
			$votes_total = ($votes_up + $votes_down);	

			// only show the results if there's at least one vote
			if ($votes_total != 0) {
			
				$votes_up_percent = ($votes_up / $votes_total * 100);
				$votes_down_percent = ($votes_down / $votes_total * 100);
				?>
				
				<?php _e( 'Results:', APP_TD ); ?>
					
				<span class="votes-green"><?php echo $votes_up; ?></span> / <span class="votes-red"><?php echo $votes_down; ?></span>			
				<div class="progress progress-green"><span style="width: <?php echo round($votes_up_percent); ?>%;"><b><?php echo round($votes_up_percent); ?>%</b></span></div>
				<div class="progress progress-red"><span style="width: <?php echo round($votes_down_percent); ?>%;"><b><?php echo round($votes_down_percent); ?>%</b></span></div>
	  <?php } ?>
	  
	</div>

<?php
}


// display the coupon voting widget and success % badge within the loop
function clpr_vote_box_badge($postID, $the_transient) {
	global $user_ID, $app_abbr;

	$response = "<span class=\'text\'>" . __( 'Thanks for voting!', APP_TD ) . "</span>";

	$vpercent = round(get_post_meta($postID, $app_abbr. '_votes_percent', true));
	// figure out which color badge to show based on percentage
	if ($vpercent >= 75) $vstyle = 'green'; elseif ($vpercent >= 40 && $vpercent < 75) $vstyle = 'orange'; else $vstyle = 'red';

?>

	<div class="thumbsup-vote">

		<div class="stripe-badge">
			<span class="success"><?php _e( 'success', APP_TD ); ?></span>
			<span class="thumbsup-stripe-badge stripe-badge-<?php echo $vstyle; ?>"><span class="percent"><?php echo $vpercent; ?>%</span></span>
		</div>

		<div class="frame" id="vote_<?php the_ID(); ?>">

			<?php if (clpr_vote_check($postID, $the_transient) == false) : ?>

				<div id="loading-<?php the_ID(); ?>" class="loading"></div>

				<div id="ajax-<?php the_ID(); ?>">

					<span class="vote thumbsup-up">
						<span class="thumbsup" onClick="thumbsVote(<?php echo $postID; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 1, '<?php echo $response; ?>');"></span>
					</span>

					<span class="vote thumbsup-down">
						<span class="thumbsdown" onClick="thumbsVote(<?php echo $postID; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 0, '<?php echo $response; ?>');"></span>
					</span>

				</div>

			<?php else:?>

				<?php clpr_votes_chart_numbers(); ?>

			<?php endif; ?>

		</div>

	</div>

<?php
}


// display the vote results within the loop
function clpr_votes_chart_numbers() {
	global $post;
?>
	<div class="results">
		<?php
			// get the votes for the post
			$votes_up = get_post_meta($post->ID, 'clpr_votes_up', true);
			$votes_down = get_post_meta($post->ID, 'clpr_votes_down', true);

			// do some math
			$votes_total = ($votes_up + $votes_down);

			// only show the results if there's at least one vote
			if ($votes_total != 0) {

				$votes_up_percent = ($votes_up / $votes_total * 100);
				$votes_down_percent = ($votes_down / $votes_total * 100);
				?>

				<div class="progress-holder"><span class="votes-raw"><?php echo $votes_up; ?></span><div class="progress progress-green"><span style="width: <?php echo round($votes_up_percent); ?>%;">&nbsp;</span></div></div>
				<div class="progress-holder"><span class="votes-raw"><?php echo $votes_down; ?></span><div class="progress progress-red"><span style="width: <?php echo round($votes_down_percent); ?>%;">&nbsp;</span></div></div>
		<?php } ?>

	</div>

<?php
}


// testing area

// vote 1 = up, 0 = down
// uid 0 = anonymous
// pid = post id

// echo '<br/><a href="'.$_SERVER[PHP_SELF].'?vote=1&uid=&pid=33">Vote Up</a> ';
// echo '<a href="'.$_SERVER[PHP_SELF].'?vote=0&uid=&pid=33">Vote Down</a><br/><br/>';

// if(isset($_GET['vote'])){

// clpr_vote_update($_GET['pid'], $_GET['uid'], $_GET['vote']);

// }
?>
