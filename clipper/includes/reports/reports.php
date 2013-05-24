<?php
### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

### Use WordPress 2.6 Constants
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}

/* Variables */
$type = (isset($_GET['type']) ? $_GET['type'] : '');
$id = $_GET['id'];

/* Load Reports Class*/
include_once('report.class.php');

global $wpdb;

$reports = new Report;

$report = $reports->findReports('ORDER BY id DESC',1,"WHERE id=".$id);

if(count($report) <= 0)
	die('Error. Unable to Load Details.');

$report = $report[0];
$permalink = add_query_arg( array( 'action' => 'edit', 'post' => $report->postID ), admin_url('post.php') );

//print_r($report);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php _e( 'Report Details', APP_TD ); ?></title>
<style type="text/css">
	html, body{
		background:#f8f8f8;
		font-family:Verdana, Geneva, sans-serif;
		color:#333;
		font-size:0.9em;
	}
	td{
		background-color:#FFF;
		border-bottom:1px dashed #ccc;
	}
	.border td{
		border-bottom:1px dashed #ccc;
		background-color:#f8f8f8;
	}
	.border .alt td{
		background-color:#fff;
	}
	th{
		text-align:left;
		color:#666;
		font-size:90%;
		border-bottom:1px dashed #ccc;
	}
	.archive-comment{
		display:block;
		padding:10px;
		border-bottom:1px dashed #ccc;
		border-top:1px dashed #ccc;
		background-color:#fff;
	}
	.archive-meta{
		font-size:90%;
	}
	
</style>
</head>
<body>
<h3><?php _e( 'Report details', APP_TD ); ?></h3>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	  <tr>
	    <th width="25%"><?php _e( 'Post / Page:', APP_TD ); ?> </td>
	    <td><a href="<?php echo $permalink;?>" title="<?php _e( 'Edit The Post', APP_TD ); ?>" target="_top"><?php echo $report->post_title; ?></a></td>
  </tr>
	<tr>
	    <th><?php _e( 'first reported on:', APP_TD ); ?></td>
	    <td><?php echo ($report->stamp != '' ? date("dS F, Y", $report->stamp) : ''); ?></td>
  </tr>
	  <tr>
	    <th><?php _e( 'Status', APP_TD ); ?></td>
	    <td><?php echo ($report->status == 1)? "New" : "Archived";?></td>
  </tr>
</table>
<h3><?php _e( 'Reports:', APP_TD ); ?></h3>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="border">
	<tr>
    	<th width="15%"><?php _e( 'IP', APP_TD ); ?></td>
        <th width="30%"><?php _e( 'Type', APP_TD ); ?></td>
        <th><?php _e( 'Comments', APP_TD ); ?></td>
    </tr>
<?php
$comments = $reports->getComments($report->id);
$alt ='';
foreach($comments as $comment):
$alt = ($alt=='')? 'class="alt"' : '';
?>
    <tr <?php echo $alt;?>>
    	<td><?php echo $comment->ip;?></td>
        <td><?php echo $comment->type;?></td>
        <td><?php echo nl2br($comment->comment);?></td>
    </tr>
<?php endforeach;?>
</table>

<?php if(isset($_GET['display']) =='archive') : if ($_GET['display'] =='archive'): ?>
<h3><?php _e( 'Archived By', APP_TD ); ?></h3>
<?php
$archive = $reports->getArchive($report->id);
if(count($archive) <=0)
{
	_e( 'Sorry, No Archive Record Found!', APP_TD );
}else{
	$archive = $archive[0];
	
	// Get the User
	$user_info = get_userdata($archive->moderatorID);
	
?>
<div class="archived-by">
	<?php _e( 'Archived By:', APP_TD ); ?> <?php echo $user_info->display_name;?> [user login: <?php echo $user_info->user_login; ?>]
</div>
<div class="archive-comment"><?php echo nl2br($archive->comment);?></div>
<div class="archive-meta">on: <?php echo date("dS F, Y", $archive->stamp);?>, IP: <?php echo $archive->ip;?></div>
<?php	
}
endif;
endif;
?>
</body>
</html>
