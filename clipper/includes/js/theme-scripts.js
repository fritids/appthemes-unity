/*
 * jQuery functions
 * Written by AppThemes
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.0
 *
 * Left .js uncompressed so it's easier to customize
 */

// <![CDATA[

jQuery(document).ready(function() {
    jQuery(".slide-contain").jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev",
        visible: 5,
		hoverPause:true,
        auto: 2800,
        speed: 1100,
        easing: "easeOutQuint" // for different types of easing, see easing.js
    });

	jQuery('a.mini-comments').live('click', function() {
		// alert (jQuery(this).attr('href'));
		jQuery.fn.colorbox({
			href:jQuery(this).attr('href'),
			rel: function(){ return jQuery(this).data('rel'); },
			transition:'fade'
			});
		return false;
	});

	jQuery('a.mail').live('click', function() {
		// alert (jQuery(this).attr('href'));
		jQuery.fn.colorbox({
			href:jQuery(this).attr('href'),
			transition:'fade'
		});
		return false;
	});

	jQuery('a.show-comments').live('click', function() {
		var postID = jQuery(this).data('rel');
		jQuery("#comments-" + postID ).slideToggle(400, 'easeOutBack');
		return false;		
	});

	jQuery('a.share').live('click', function() {
		jQuery(this).next(".drop").slideToggle(400, 'easeOutBack');
		return false;		
	});

	jQuery('a.coupon-code-link').live('click', function() {
		var couponcode = jQuery(this).data('rel');
		jQuery(this).fadeOut('fast').html('<span>' + couponcode + '</span>').fadeIn('fast');
	});
	
	if(FlashDetect.installed) {
		jQuery(".coupon-code-link").each(function() {
			// Create a new clipboard client
			var clip = new ZeroClipboard.Client('', jQuery(this).attr("id") );
			var binder = jQuery(this).find("span");
			var storeURL = jQuery(this).attr('href');

			// Glue the clipboard client to the last td in each row
			clip.glue(binder[0]);

			// Grab the text from data-rel
			var txt = jQuery(this).data('rel');
			clip.setText(txt);

			//Add a complete event to let the user know the text was copied
			clip.addEventListener('complete', function(client, text) {
				jQuery('#' + client.divId).fadeOut('fast').html('<span>' + text + '</span>').fadeIn('fast');
				window.open(storeURL, '_blank');
			});

			clip.addEventListener( 'onMouseOver', mouse_over_handler );
			clip.addEventListener( 'onMouseOut', mouse_out_handler );

			function mouse_over_handler( client ) {
				jQuery('#' + client.divId).parent().next().toggle();
			}

			function mouse_out_handler( client ) {
				jQuery('#' + client.divId).parent().next().toggle();
			}
		});
	}

	// show the new store name and url fields if "add new" option is selected
	jQuery("#store_name_select").change(function() {
		if (jQuery(this).val() == 'add-new') {
			jQuery('li.new-store').fadeIn('fast');
			jQuery('li.new-store input').addClass('required');
			jQuery('li#new-store-url input').addClass('url');
		} else {
			jQuery('li.new-store').hide();
			jQuery('li.new-store input').removeClass('required invalid');
			jQuery('li#new-store-url input').removeClass('url');
		}
	}).change();

   // show the coupon code or upload coupon field based on type select box
	jQuery('#coupon_type_select').change(function() {	
		if (jQuery(this).val() == 'coupon-code') {
			jQuery('li#ctype-coupon-code').fadeIn('fast');
			jQuery('li#ctype-coupon-code input').addClass('required');
			jQuery('li#ctype-printable-coupon input').removeClass('required invalid');
			jQuery('li#ctype-printable-coupon').hide();
			jQuery('li#ctype-printable-coupon-preview').hide();
		} else if (jQuery(this).val() == 'printable-coupon') {
			jQuery('li#ctype-printable-coupon').fadeIn('fast');
			jQuery('li#ctype-printable-coupon-preview').fadeIn('fast');
			if ( ! jQuery('li#ctype-printable-coupon-preview') )
				jQuery('li#ctype-printable-coupon input').addClass('required');
			jQuery('li#ctype-coupon-code input').removeClass('required invalid');
			jQuery('li#ctype-coupon-code').hide();
		} else {
			jQuery('li.ctype').hide();
			jQuery('li.ctype input').removeClass('required invalid');
		}		
	}).change(); 
	
});

// used for the search box default text
function clearAndColor(el, e2) {
	//grab the current fields value and set a variable
	if (el.defaultValue==el.value) el.value = "";
	//Change the form fields text color
	if (el.style) el.style.color = "#333";

}


// used for the search box default text
function reText(el){
	//Change the form fields text color
	if (el.style) el.style.color = "#ccc";
	if (el.value== "") el.value = el.defaultValue;
}

jQuery(function() {
		
	jQuery( ".newtag" ).autocomplete({
		source: function( request, response ) {
			jQuery.ajax({
				url: ajaxurl + "?action=ajax-tag-search-front&tax=stores",
				dataType: "json",
				data: {
					term: request.term
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					//alert('ERROR!: '+ errorThrown);
					//alert('ERROR!: '+ textStatus);
					//alert('ERROR!: '+ XMLHttpRequest);
				},
				success: function( data ) {
					response( jQuery.map( data, function( item ) {
						return {
							term: item,
							value: item.name
						}
					}));
				}
			});
		},
		minLength: 2,
		select: function(event, ui) {
			// alert (ui.item.term.slug);
			storeurl = ui.item.term.clpr_store_url;
			if (storeurl != 0) {
				jQuery(".clpr_store_url").html('<a href="' + storeurl + '" target="_blank">' + storeurl + '<br /><img src="' + ui.item.term.clpr_store_image_url + '" class="screen-thumb" /></a><input type="hidden" name="clpr_store_id" value="' + ui.item.term.term_id + '" /><input type="hidden" name="clpr_store_slug" value="' + ui.item.term.slug + '" />');
			}	
		}
	});
		
	jQuery( ".newtag" ).keydown(function(event) {
		if (jQuery("#clpr_store_url").length == 0) {
			jQuery(".clpr_store_url").html('<input type="text" class="text" id="clpr_store_url" name="clpr_store_url" value="http://" />');
		}			
	});
		
		
	jQuery("button.comment-submit").live('click', function() {
		
		var comment_post_ID = jQuery(this).next().val(),
		postURL = jQuery("#commentform-" + comment_post_ID).attr('action'),
		author = jQuery('input#author-' + comment_post_ID).val(),
		email = jQuery('#email-' + comment_post_ID).val(),
		url = jQuery('#url-' + comment_post_ID).val(),
		comment = jQuery('#comment-' + comment_post_ID).val();				
		
		var postData = 'author=' + author 
			+ '&email=' + email 
			+ '&url=' + url 
			+ '&comment=' + comment 
			+ '&comment_post_ID=' + comment_post_ID ;
		
		// alert(postData);
		
		jQuery.ajax({
			beforeSend: function() {
				return jQuery("#commentform-" + comment_post_ID).validate({
					errorClass: "invalid",
					errorElement: "div"
				}).form();
			},
			type: 'POST',
			data: postData,
			url: postURL,
			dataType: "json",
			success: function( data ) {
				
				if (data.success == true) {
					//jQuery('.comment-form .post-box').html('<div class="head"><h3>Thanks!</h3></div><div class="text-box"><p>Your comment will appear shortly.</p></div>');
					jQuery.colorbox.close();
					
					if (jQuery("#comments-" + comment_post_ID + " .comments-mini").length == 0 ) {
						jQuery("#comments-" + comment_post_ID).append("<div class='comments-box coupon'><ul class='comments-mini'>" + data.comment + "</ul></div>").fadeOut('slow').fadeIn('slow');
					} else {
						jQuery("#comments-" + comment_post_ID + " .comments-mini").prepend(data.comment).fadeOut('slow').fadeIn('slow');
					}
					
					// update the comment count but delay it a bit
					setTimeout(function() {
						jQuery("#post-" + comment_post_ID + " a.show-comments span").html(data.count).fadeOut('slow').fadeIn('slow');
					}, 2000);
					
				} else {
					jQuery('.comment-form .post-box').html('<div class="head"><h3>Error</h3></div><div class="text-box"><p>Please try again later.</p></div>');
					jQuery.colorbox.resize();
				}				
			}
		});		
	 
	  return false;			
	});
	
	
	// send the coupon via email pop-up form
	jQuery("button.send-email").live('click', function() {
	 
		var post_ID = jQuery(this).next().val();
		var postURL = jQuery("#commentform-" + post_ID).attr('action');
		var author = jQuery('#author-' + post_ID).val();
		var	email = jQuery('#email-' + post_ID).val();
		var	recipients = jQuery('#recipients-' + post_ID).val();
		var	message = jQuery('#message-' + post_ID).val();
		
		var postData = 'author=' + author 
			+ '&email=' + email 
			+ '&recipients=' + recipients 
			+ '&message=' + message 
			+ '&post_ID=' + post_ID ;
		
		// alert (postData);
		
		jQuery.ajax({
			beforeSend: function() {
				return jQuery("#commentform-" + post_ID).validate({
					errorClass: "invalid",
					errorElement: "div"
				}).form();
			},
			type: 'POST',
			data: postData,
			url: postURL,
			dataType: "json",
			success: function( data ) {

				jQuery('.comment-form .post-box').html('<div class="head"><h3>' + theme_scripts_loc.sendEmailHead + '</h3></div><div class="text-box"></div>');

				jQuery.each(data, function(i, val){						
					if (val.success == true){
						jQuery('.comment-form .post-box .text-box').append('<p>' + theme_scripts_loc.sendEmailTrue + ': ' + val.recipient + '</p>');
						jQuery.colorbox.resize();
					} else {
						jQuery('.comment-form .post-box .text-box').append('<p>' + theme_scripts_loc.sendEmailFalse + ': ' + val.recipient + '.</p>');
						jQuery.colorbox.resize();
					}
				});				
			}
		});
		
	 
	  return false;
	});
		
});

// coupon ajax vote function. calls clpr_vote_update() in theme-voting.php 
function thumbsVote(postID, userID, elementID, voteVal, afterVote) {
	var postData = 'vid=' + voteVal + '&uid=' + userID + '&pid=' + postID;
	var theTarget = document.getElementById(elementID);	// pass in the vote_# css id so we know where to update

	jQuery.ajax({
			target: theTarget,
			type: 'POST',
			beforeSend: function() {
				jQuery('#loading-' + postID).fadeIn('fast'); // show the loading image
				jQuery('#ajax-' + postID).fadeOut('fast'); // fade out the vote buttons
			},
			data: postData,
			url: ajaxurl + "?action=ajax-thumbsup",
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert('Error: '+ errorThrown + ' - '+ textStatus + ' - '+ XMLHttpRequest);
			},
			success: function( data, statusText ) {
				theTarget.innerHTML = afterVote;
				jQuery('#post-' +postID + ' span.percent').html(data).fadeOut('slow').fadeIn('slow');
			}			
		});	
	
	return false;		
}

// coupon ajax reset votes function. calls clpr_reset_coupon_votes_ajax() in theme-voting.php
function resetVotes(postID, elementID, afterReset) {
	var postData = 'pid=' + postID;
	var theTarget = document.getElementById(elementID);	// pass in the reset_# css id so we know where to update

	jQuery.ajax({
			target: theTarget,
			type: 'POST',
			data: postData,
			url: ajaxurl + "?action=ajax-resetvotes",
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert('Error: '+ errorThrown + ' - '+ textStatus + ' - '+ XMLHttpRequest);
			},
			success: function( data, statusText ) {
				theTarget.innerHTML = afterReset;
			}
		});

	return false;	
}

// ]]>
