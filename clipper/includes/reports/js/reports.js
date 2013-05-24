jQuery(document).ready(function() {
	if (window.location.hash == '#problem')
	{
		reports_toggle('.reports_form', '')
	}
});

function reports_toggle(form_name, postID) {
	jQuery(form_name).slideToggle(400, 'easeOutBack');
	return false;
}

function reports_report(form) {
	// get POST DATA
	var post_ID=form.post.value;
	var reportas=form.report_as.value;
	// var desc=form.description.value;
	var nonce=form._wpnonce.value;
		
	// Hide All Other than Message
	jQuery('#reports_report_link_'+post_ID).hide();
	jQuery('#reportsform'+post_ID).hide();
	jQuery('#reports_message_'+post_ID).fadeIn(200);
	jQuery('#reports_message_'+post_ID).html('<img src="' + reportsURL + '/includes/reports/images/loading.gif" title="" alt="" />');
	
	// Send Ajax
	jQuery.post(reportsURL + '/includes/reports/reports-ajax.php', { 
		postID: post_ID, 
		report_as: reportas, 
		do_ajax_report: "true", 
		wpnonce: nonce 
	},
	  function(data){
		// Display the return
		jQuery('#reports_message_'+post_ID).html(data).delay(2000).fadeOut('slow');
	  });
	
	return false;
}