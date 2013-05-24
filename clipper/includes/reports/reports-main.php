<?php
/*
* Report this coupon module
* Based off the WP-ReportPost plugin
*
*/

# Variables
include_once('report.class.php');
$reports = NULL;
global $app_abbr;



function reports_init()
{
	if(is_admin())
	{
		// add_action('admin_menu', 'reports_custom_field');
		// add_action('save_post', 'reports_custom_field_save');
	}
		
	// If Admin Pages, No need for Styles & Scripts
	if(is_admin() || is_feed())
		return;
	add_action('wp','reports_wp');
}

add_action('init','reports_init');

# Attach Autometic Report IF ENABLED
function reports_wp()
{
	global $app_abbr;
	
	if(is_admin() || is_feed()) // Again to check FEEDS
		return; 
	// Load all options
	$usersonly = (int) get_option($app_abbr.'_rp_registeronly');
	
	// Check Registered Users Only 
	if($usersonly == 1 && is_user_logged_in() == false)
		return;
	
	/*
	if(is_page() || is_front_page() || is_home() ){

		if($rp_page != 1)	// Return IF PAGE OPTION DISABLED
			return false;
		
	}else{
		
		// Get More Options
		$rp_if = (int) get_option("rp_if");

		if($rp_if != 3) // BY THEME EDIT
		{
			add_filter('the_content','reports_attach_report',100); // Call Attach Report Option
			add_filter('get_the_excerpt', 'reports_get_the_excerpt',1);
		}
		
	} // END IF
	*/
	
	// Add jQuery
	// wp_enqueue_script('jquery');
	add_action('wp_head','reports_head');	// Adding Scripts and CSS to header	
	
	return true;
}

#Except Exception
function reports_get_the_excerpt($output)
{
	// Remove Content Filter
	remove_filter('the_content', reports_attach_report, 100);
	return $output;
}

# Attach REPORT to THE_CONTENT
function reports_attach_report($text)
{
	# Get Report option to Validate Custom Fields
	$rp_if = (int) get_option("rp_if");
	global $post;
	
	if($rp_if == 2) // Repeats as Above But for ALL INC Single
	{
		$custom_field = (int)get_post_meta($post->ID, 'reports', true);
		
		if(!$custom_field || empty($custom_field) || !is_numeric($custom_field) || $custom_field!=1)
			return $text; // Return Contents as it is....
	}

	//get_the_category()
	# Validate Category IF SELECTED!
	$cat_selected = get_option("rp_categories");
	
	if($cat_selected && !empty($cat_selected) && $cat_selected != 0 && !is_page()) // Means Category selected to Filter out
	{
		$cat_selected = explode(",",$cat_selected ); // CONVERT TO ARRAY
		
		if(in_category($cat_selected) || post_is_in_descendant_category($cat_selected))
		{
			return reports_report_form($text);
		}
		
		return $text;
	}

	// Call Attach to Add HTML at the END!
	// Allways Return the Contents... Or it will Display EMPTY STRING!
	return reports_report_form($text);
}

if(!function_exists("post_is_in_descendant_category"))
{
	function post_is_in_descendant_category( $cats, $_post = null )
	{
		foreach ( (array) $cats as $cat ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $cat, 'category');
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
}


# Adding Scripts and CSS to header
function reports_head()
{	
	// Add CSS
	?>
    <link href="<?php echo get_bloginfo('template_url') ."/includes/reports/css/reports.css" ?>" rel="stylesheet" type="text/css" />
    <?php
	
	// Add  Javascripts to Do the Biddings
	?>
	
    <script type="text/javascript">
		var reportsURL = '<?php bloginfo('template_url'); ?>';
    </script>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_url') ."/includes/reports/js/reports.js"; ?>" ></script>
    <?php
}

/* Attach Reporting Form to Contents
----------------------------------------*/
function clpr_report_coupon($echo = false) // MANUAL ADD CALL
{
	$text = reports_report_form('');
	if($echo)
		echo $text;
		
	return $text;
}

function reports_report_form($text)
{
	// Get Current POST from Global
	global $post;
	global $app_abbr;
	// Get the Options
	$options=get_option($app_abbr.'_rp_options');
	$options=(empty($options)) ? array("Report") : explode('|',$options);

	// Create Options
	$select_options="";
	foreach($options as $opt)
		$select_options .='<option value="'.$opt.'">'.$opt.'</option>'."\n";
	

	$nonce= wp_create_nonce ($post->ID);
	// Create the FORM
	$form='
	<li>
	
	<div class="reports_wrapper">
	
		<div class="reports_report_link" id="reports_report_link_'.$post->ID.'">
		
			<a href="#" onclick="return reports_toggle(\'#reportsform'.$post->ID.'\',\''.$post->ID.'\');" class="problem">'.get_option($app_abbr.'_rp_display_text').'</a>
		</div>		
	
	</div> <!-- #reports_wrapper -->
	
	</li>
	
	<li class="report">
		<div id="reports_message_' . $post->ID . '" class="reports_message"><img src="' . get_bloginfo('template_url') . '/images/loading.gif" title="" alt="" />' . __( 'Processing your request, Please wait....', APP_TD ) . '</div>	
		
		<div class="reports_form" id="reportsform'.$post->ID.'">
			
				<form action="'. $_SERVER['REQUEST_URI'] .'" method="post" enctype="text/plain" onsubmit="return reports_report(this);">
				
					<table>
					  <tr>
						<td class="align-left">
							<select name="report_as">
								'.$select_options.'
							</select>
							<input name="do_report" type="submit" value="' . __( 'Report', APP_TD ) . '" class="reports_submit" />
							<input type="hidden" value="' . $post->ID . '" name="post" />
							<input type="hidden" name="_wpnonce" value="' . $nonce . '" />
						</td>

					  </tr>

					</table>
					
				</form>
			
		</div> <!-- #reports_form -->
	</li>
	';

        // Load registed option
        global $app_abbr;
	$usersonly = (int) get_option($app_abbr.'_rp_registeronly');

	// Display only for registered users
	if($usersonly == 1 && is_user_logged_in() == false){
            return $text;
        }
	
	// Attach Form to the Contents
	$text .= $form;

	// Return Final Contents
	return $text;
}


/* Comon functions */
// COMMON HANDLERS
function url_filter($url, $key) {
    $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    return ($url);
}

/* GUI Support */
add_action('admin_print_scripts', 'reports_js_admin_header' );
function reports_js_admin_header()
{
			
	wp_enqueue_script('jquery');  
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('thickbox');
	
}
add_action('admin_print_styles', 'reports_wp_print_styles');
function reports_wp_print_styles()
{
	wp_enqueue_style('reports-admin', get_bloginfo('template_url')."/includes/reports/css/reports-admin.css"); 
	wp_enqueue_style('thickbox'); 
}

function reports_new ()
{
	// Get the Reports
	include_once("report.class.php");
	
	global $wpdb;
	$reports = new Report;
	
	// Handle Archive & DELETE
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	  	//echo $current_user->ID;
		
		if ( get_magic_quotes_gpc() ) {
			$_POST      = array_map( 'stripslashes_deep', $_POST );
			$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
		}
		
		$selected = $_POST['reportID'];
		
		if($selected && is_array($selected) && count($selected) > 0)
		{
			// If Archive
			if(isset($_POST['archiveit']))
			{
				global $current_user;
				get_currentuserinfo();
				
				$archive_c = $_POST['archive_c'];
				
				foreach($selected as $archive)
				{
					if(!$reports->archive($archive, $current_user->ID, $archive_c))
					{
						echo "ERROR: ".$reports->last_error;
						break; // EXIT LOOP
					}
				}
			}
			
			// DELETE
			if(isset($_POST['deleteit']))
			{
				foreach($selected as $archive)
				{
					if(!$reports->delete($archive))
					{
						echo "ERROR: ".$reports->last_error;
						break; // EXIT LOOP
					}
				}
			}
		}// IF selected
	}
	
	
	
	// Calculate Paggination
	$p = (int) isset($_GET['p']) && is_numeric($_GET['p'])? $_GET['p'] : 1;
	$limit= 20;
	
	$offset = ($limit * ($p - 1));
	
	// Search Based on Paggination
	$results = $reports->findReports('ORDER BY id DESC',$limit, "WHERE status=1", $offset);
	
	// Calculate Pages
	$total_found = $reports->totalRows;
	
	$pages = ceil($total_found / $limit);
?>
<div class="wrap"> 
	<h2><?php _e( 'New Reports', APP_TD ); ?></h2>
	
    <form action="" method="post">
    <div class="reports-info">
    	<div class="reports-buttons">
        	<?php _e( 'selected:', APP_TD ); ?> <input type="button" value="<?php _e( 'Archive it', APP_TD ); ?>" name="expandarchive" class="button-secondary delete" onclick="jQuery('#reports-archive').slideToggle('slow');" /> <?php _e( 'or', APP_TD ); ?> <input type="button" value="<?php _e( 'Delete it', APP_TD ); ?>" name="delete-expand" class="button-secondary delete" onclick="jQuery('#delete-confirm').slideToggle('slow');" /> <small><?php _e( '(* will be removed permanently)', APP_TD ); ?></small>
        </div>
    	<span><?php _e( 'Total Reports:', APP_TD ); ?> <?php echo $total_found; ?></span>
    </div>
    
    <div class="reports-archive" id="reports-archive" style="display:none">
        <?php _e( 'Moderator Comments:', APP_TD ); ?><br />
        <textarea name="archive_c" id="archive_c" style="width:60%;" rows="5"></textarea><br />
        <small><?php _e( '* Your User ID and IP will be logged!', APP_TD ); ?></small><br />
        <input type="submit" value="<?php _e( 'Archive it', APP_TD ); ?>" name="archiveit" class="button-secondary delete" />
    </div>
    
    <div class="reports-archive" id="delete-confirm" style="display:none">
    	<strong><?php _e( 'Once deleted, It will be Permanently removed from database. This Report record canno\'t be found again', APP_TD ); ?></strong><br />
        <?php _e( 'Confirm Deleting?', APP_TD ); ?>  <input type="submit" value="<?php _e( 'Confirm Delete', APP_TD ); ?>" name="deleteit" class="button-secondary delete" /> 
    </div>
    <?php 
	if($total_found > 0):
	
	?>
	<table class="widefat post fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col"><?php _e( 'Post Title', APP_TD ); ?></th>
                <th scope="col" style="width:80px;"><?php _e( '# Reports', APP_TD ); ?></th>
			</tr>
		</thead>
        <tfoot>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col"><?php _e( 'Post Title', APP_TD ); ?></th>
                <th scope="col"><?php _e( '# Reports', APP_TD ); ?></th>
			</tr>
		</tfoot>
		<tbody>
        <?php
		$alt = '';
		foreach($results as $report):
		$alt = ($alt == '') ? ' class = "alt"' : '';
		
		$permalink = add_query_arg( array( 'action' => 'edit', 'post' => $report->postID ), admin_url('post.php') );
		
		?>
			<tr <?php echo $alt;?>>
            	<th scope="row" class="check-column"><input type="checkbox" name="reportID[]" value="<?php echo $report->id;?>" /></th>
				<td><a href="<?php echo $permalink;?>" title="<?php _e( 'Edit The Post', APP_TD ); ?>"><?php echo $report->post_title;?></a></td>
                <td align="center"><a href="<?php echo get_bloginfo('template_url');?>/includes/reports/reports.php?id=<?php echo $report->id;?>&TB_iframe=true&type=reports" title="<?php _e( 'Report Details', APP_TD ); ?>" class="thickbox" onclick="return false;"><?php _e( '# View Details', APP_TD ); ?></a></td>
			</tr>
            <?php endforeach;?>
            
		</tbody>
	</table>
    <?php
	else:
		_e( 'No Reports Found!', APP_TD );
	endif;
	?>
    </form>
    <?php
	
	if($pages > 1)
	{
	?>
    <div class="reports-pages">
    	<ul>
        	<li class="pageinfo"><?php _e( 'Pages:', APP_TD ); ?> </li>
            <?php 
			for($i=1; $i <= $pages; $i++): 
				if($i == $p)
				{?>
                <li class="current"><?php echo $i;?></li>
				<?php 
				continue;
				}
			?>
        	<li><a href="<?php echo admin_url('admin.php') . "?" . url_filter($_SERVER['QUERY_STRING'], 'p') . "&p=" . $i; ?>"><?php echo $i; ?></a></li>
            <?php
			endfor;
			?>
        </ul>
    </div>
    <?php 
	}
	?>
</div>
<?php
function addHeaderCode() {
		echo "HEREME";
}
}

function reports_archive()
{
	// Get the Reports
	include_once("report.class.php");
	
	global $wpdb;
	$reports = new Report;
	
	// Handle Archive & DELETE
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	  	//echo $current_user->ID;
		
		if ( get_magic_quotes_gpc() ) {
			$_POST      = array_map( 'stripslashes_deep', $_POST );
			$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
		}
		
		$selected = $_POST['reportID'];
		
		if($selected && is_array($selected) && count($selected) > 0)
		{
			// DELETE
			if(isset($_POST['deleteit']))
			{
				foreach($selected as $archive)
				{
					if(!$reports->delete($archive))
					{
						echo "ERROR: ".$reports->last_error;
						break; // EXIT LOOP
					}
				}
			}
		} // IF SELECTED
	}
	
	
	// Calculate Paggination
	$p = (int) isset($_GET['p']) && is_numeric($_GET['p'])? $_GET['p'] : 1;
	$limit= 20;
	
	$offset = ($limit * ($p - 1));
	
	// Search Based on Paggination
	$results = $reports->findArchives('ORDER BY reportID DESC',$limit, '', $offset);
	
	// Calculate Pages
	$total_found = $reports->totalRows;
	
	$pages = ceil($total_found / $limit);
?>
<div class="wrap"> 
	<h2><?php _e( 'Archived Reports', APP_TD ); ?></h2>
	
    <form action="" method="post">
    <div class="reports-info">
    	<div class="reports-buttons">
        	<?php _e( 'selected:', APP_TD ); ?> <input type="button" value="<?php _e( 'Delete it', APP_TD ); ?>" name="delete-expand" class="button-secondary delete" onclick="jQuery('#delete-confirm').slideToggle('slow');" /> <small><?php _e( '(* will be removed permanently)', APP_TD ); ?></small>
        </div>
    	<span><?php _e( 'Total Reports:', APP_TD ); ?> <?php echo $total_found;?></span>
    </div>
    
    <div class="reports-archive" id="delete-confirm" style="display:none">
    	<strong><?php _e( 'Once deleted, It will be Permanently removed from database. This Report record canno\'t be found again', APP_TD ); ?></strong><br />
        <?php _e( 'Confirm Deleting?', APP_TD ); ?>  <input type="submit" value="<?php _e( 'Confirm Delete', APP_TD ); ?>" name="deleteit" class="button-secondary delete" /> 
    </div>
    <?php 
	if($total_found > 0):
	
	?>
	<table class="widefat post fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col"><?php _e( 'Post Title', APP_TD ); ?></th>
                <th scope="col" style="width:80px;"><?php _e( '# Reports', APP_TD ); ?></th>
			</tr>
		</thead>
        <tfoot>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col"><?php _e( 'Post Title', APP_TD ); ?></th>
                <th scope="col"><?php _e( '# Reports', APP_TD ); ?></th>
			</tr>
		</tfoot>
		<tbody>
        <?php
		$alt = '';
		foreach($results as $report):
		$alt = ($alt == '') ? ' class = "alt"' : '';
		
		$permalink = add_query_arg( array( 'action' => 'edit', 'post' => $report->postID ), admin_url('post.php') );
		
		?>
			<tr <?php echo $alt;?>>
            	<th scope="row" class="check-column"><input type="checkbox" name="reportID[]" value="<?php echo $report->reportID; ?>" /></th>
				<td><a href="<?php echo $permalink; ?>" title="<?php _e( 'Edit The Post', APP_TD ); ?>"><?php echo $report->post_title; ?></a></td>
                <td align="center"><a href="<?php echo get_bloginfo('template_url');?>/includes/reports/reports.php?id=<?php echo $report->reportID; ?>&display=archive&TB_iframe=true" title="<?php _e( 'Archived Report Details', APP_TD ); ?>" class="thickbox" onclick="return false;"><?php _e( '# View Details', APP_TD ); ?></a></td>
			</tr>
            <?php endforeach;?>
            
		</tbody>
	</table>
    <?php
	else:
		_e( 'No Reports Found!', APP_TD );
	endif;
	?>
    </form>
    <?php
	
	if($pages > 1)
	{
	?>
    <div class="reports-pages">
    	<ul>
        	<li class="pageinfo"><?php _e( 'Pages:', APP_TD ); ?> </li>
            <?php 
			for($i=1; $i <= $pages; $i++): 
				if($i == $p)
				{?>
                <li class="current"><?php echo $i;?></li>
				<?php 
				continue;
				}
			?>
        	<li><a href="<?php echo admin_url('admin.php') . "?" . url_filter($_SERVER['QUERY_STRING'], 'p') . "&p=" . $i; ?>"><?php echo $i; ?></a></li>
            <?php
			endfor;
			?>
        </ul>
    </div>
    <?php 
	}
	?>
</div>
<?php
function addHeaderCode() {
		echo "HEREME";
}
}

function reports_settings ()
{
/*
	SETTINGS Options for WP-REPORTPOST
	V1.2
*/


global $current_user;
get_currentuserinfo();
				
### If Form Is Submitted
if(isset($_POST['saveChanges'])) {
	
	if ( get_magic_quotes_gpc() ) {
		$_POST      = array_map( 'stripslashes_deep', $_POST );
		$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
	}
	
	// Update options After Validating
	$rp_send_email = isset($_POST['rp_send_email']) && is_numeric($_POST['rp_send_email']) ? $_POST['rp_send_email'] : 0;
	$rp_email_address = isset($_POST['rp_email_address']) ? $_POST['rp_email_address'] : get_option('admin_email');
	$rp_display_text = isset($_POST['rp_display_text']) ? $_POST['rp_display_text'] : __( '[!] Report This Post', APP_TD );
	$rp_thanks_msg = isset($_POST['rp_thanks_msg']) ? $_POST['rp_thanks_msg'] : '<strong>' . __( 'Thanks for Reporting [post_title]', APP_TD ) . '</strong>';
	$report_options = isset($_POST['reportoptions']) ? $_POST['reportoptions'] : __( 'Report contents', APP_TD );
	$report_options = str_replace("\n", "|", $report_options);
	$report_options = str_replace("\r", "", $report_options);
	$report_options = str_replace("\t", "", $report_options);
	// $report_if = (int)isset($_POST['report_if']) ? $_POST['report_if'] : 1;
	
	$rp_registeronly = (int)isset($_POST['registeronly']) ? $_POST['registeronly'] : 0;
	// $rp_page = (int)isset($_POST['is_page']) ? $_POST['is_page'] : 0;
	// $rp_categories = isset($_POST['post_category']) ? $_POST['post_category'] : array();
	// $rp_categories = implode(",", $rp_categories);
	
	// Update
	$update_query = array();
	$update_text = array();
	
	$update_query[] = update_option("rp_send_email", $rp_send_email);
	$update_query[] = update_option("rp_email_address", $rp_email_address);
	$update_query[] = update_option("rp_display_text", $rp_display_text);
	$update_query[] = update_option("rp_thanks_msg", $rp_thanks_msg);
	$update_query[] = update_option("rp_options", $report_options);
	// $update_query[] = update_option("rp_if", $report_if);
	
	$update_query[] = update_option("rp_registeronly", $rp_registeronly);
	// $update_query[] = update_option("rp_page", $rp_page);
	// $update_query[] = update_option("rp_categories", $rp_categories);

	$update_text[] = __( 'Sending Email Option', APP_TD );
	$update_text[] = __( 'Sending Email Address', APP_TD );
	$update_text[] = __( 'Link Text', APP_TD );
	$update_text[] = __( 'Thank you Message', APP_TD );
	$update_text[] = __( 'Report Options', APP_TD );
	$update_text[] = __( 'Attach Option', APP_TD );

	$update_text[] = __( 'Register users Only', APP_TD );
	$update_text[] = __( 'Display on Pages', APP_TD );
	$update_text[] = __( 'Limited Categories', APP_TD );

	$i = 0;
	$text = '';
	foreach($update_query as $u_query) {
		if($u_query) {
			$text .= '<font color="green">' . $update_text[$i] . ' ' . __( 'Updated', APP_TD ) . '</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">' . __( 'No Option Updated', APP_TD ) . '</font>';
	}
	
	
} // End IF


### Needed Variables
$rp_send_email = intval(get_option("rp_send_email"));
$rp_email_address = get_option("rp_email_address");
$rp_display_text = get_option("rp_display_text");
$rp_thanks_msg = get_option("rp_thanks_msg");
$report_options = get_option("rp_options");

$rp_registeronly = (int)get_option("rp_registeronly");

global $wpdb;

?>

<div class="wrap"> 
	<h2><?php _e( 'Settings', APP_TD ); ?></h2>
	
	<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
	    
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
    
	<div style="background-color:#FFF; border:1px dotted #CCC; padding:10px;">
    
    <table class="form-table" border="0">
    	<tr>
    	  <th><?php _e( 'Users Only:', APP_TD ); ?></th>
    	  <td><input type="checkbox" name="registeronly" value="1" <?php if($rp_registeronly > 0) echo ' checked="checked"'; ?> /> <?php _e( 'Tick this box, If you want to Limit to Logged-in users only can report.', APP_TD ); ?></td>
  	  </tr>
    	<!--
		<tr>
        	<th>Display for Pages?</th>
            <td><input type="checkbox" name="is_page" value="1" <?php if($rp_page > 0) echo ' checked="checked"'; ?> /> <?php _e( 'Tick this box, If you want to Attach Report POST Form to your Page Contents', APP_TD ); ?></td>
        </tr>
		-->
        <tr>
        	<td colspan="2" style="border-top:1px dotted #CCC; font-size:1px;">&nbsp;</td>
        </tr>
		<!--
		<tr>
        	<th>Attach Form to</th>
            <td><select name="report_if">
            	<option value="1" <?php if($report_if==1) echo 'selected="Selected"';?>>Attach to All Posts</option>
                <option value="2" <?php if($report_if==2) echo 'selected="Selected"';?>>Attach Only to Selected Posts</option>
                <option value="3" <?php if($report_if==3) echo 'selected="Selected"';?>>Manually [Theme Edit]</option>
            </select></td>
        </tr>
		-->
		<!--
        <tr>
        	<th>Limit Categories</th>
            <td>
            <div class="categoryList">
            <ul>
            <?php 
				//$categories =  get_categories('hierarchical=1&hide_empty=false'); 
				//print_r($categories);
				
			?>
            <?php wp_category_checklist(0, false, $rp_categories); ?>
            </ul>
            
            </div>
            <small>This option will work only "Attach to All Posts" selected.<br />
			* Selecting this option could Increase your Server Resource usage. <br  />* Select Parent Category and child-category contents will be included (When attaching report form)<br />
* Select NONE, and All categories will be included.</small>
            </td>
        </tr>
		-->
        <tr>
        	<th><?php _e( 'Report Post Link text', APP_TD ); ?></th>
        	<td><textarea name="rp_display_text" style="width:70%;" cols="30" rows="5"><?php echo $rp_display_text; ?></textarea><br /><small><?php _e( 'You can use HTML', APP_TD ); ?></small></td>
        </tr>
        
        <tr>
        	<th style="border-top:1px dotted #CCC;"><?php _e( 'Options', APP_TD ); ?></th>
        	<td style="border-top:1px dotted #CCC;"><textarea name="reportoptions" cols="30" rows="5" style="width:70%;"><?php echo str_replace("|","\n",$report_options); ?></textarea><br /><small><?php _e( '* One Per Line', APP_TD ); ?></small></td>
        </tr>
        <tr>
        	<th style="border-top:1px dotted #CCC;"><?php _e( 'Thank you Message', APP_TD ); ?></th>
        	<td style="border-top:1px dotted #CCC;"><textarea name="rp_thanks_msg" cols="30" rows="5" style="width:70%;"><?php echo $rp_thanks_msg; ?></textarea><br /><small><?php _e( '* [post_title] will be Replaced with Original Post Title on reporting. you can use HTML', APP_TD ); ?></small></td>
        </tr>
        
        <tr>
        	<th style="border-top:1px dotted #CCC;"><?php _e( 'Send Email:', APP_TD ); ?> </th>
        	<td style="border-top:1px dotted #CCC;">
            	<input type="checkbox" name="rp_send_email" value="1" <?php if($rp_send_email==1){ echo 'checked="checked"';}?>/> <?php _e( 'to:', APP_TD ); ?> <input type="text" name="rp_email_address" value="<?php echo $rp_email_address; ?>" /><br />
                <small><?php _e( '* Only One Email Will be Send Per POST', APP_TD ); ?></small>
            </td>
        </tr>
        <tr>
        	<td colspan="2" style="border-top:1px dotted #CCC; font-size:1px;">&nbsp;</td>
        </tr>
        
    </table>
	<p class="submit" style="text-align:right">
		<input class="button-primary" type="submit" name="saveChanges" value="<?php _e( 'Save Changes', APP_TD ); ?>" />
	</p>
    <p><?php _e( 'You should edit your theme and include this PHP code <em>&lt;?php reports(true);?&gt;</em> to include the report form.', APP_TD ); ?></p>
    </div>
    

	</form>
    
</div>
<?php
/* HACK to wp_category_checklist*/
if(!function_exists("wp_category_checklist"))
{
function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null ) {
	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Walker_Category_Checklist;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array();

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id )
		$args['selected_cats'] = wp_get_post_categories($post_id);
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );

	if ( $descendants_and_self ) {
		$categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
		$self = get_category( $descendants_and_self );
		array_unshift( $categories, $self );
	} else {
		$categories = get_categories('get=all');
	}

	// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
	$checked_categories = array();
	$keys = array_keys( $categories );

	foreach( $keys as $k ) {
		if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
			$checked_categories[] = $categories[$k];
			unset( $categories[$k] );
		}
	}

	// Put checked cats on top
	echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	// Then the rest of them
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}
}
}

?>