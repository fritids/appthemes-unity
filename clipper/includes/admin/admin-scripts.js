/*
 * Admin jQuery functions
 * Written by AppThemes
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.0
 *
 */

// <![CDATA[

jQuery(document).ready(function() {

	/* initialize the tooltip feature */
	jQuery("td.titledesc a").easyTooltip();

	/* hide all the content boxes on the dashboard page */
	// jQuery('.insider').hide();

	/* admin option pages tabs */
	jQuery("#tabs-wrap").tabs( {
		fx: {opacity: 'toggle', duration: 200},
		selected: theme_scripts_admin.setTabIndex,
		show: function() {
			var newIdx = jQuery('#tabs-wrap').tabs('option', 'selected');
			jQuery('#setTabIndex').val(newIdx); // hidden field
		}
	});

	/* strip out all the auto classes since they create a conflict with the calendar */
	jQuery('#tabs-wrap').removeClass('ui-tabs ui-widget ui-widget-content ui-corner-all')
	jQuery('ul.ui-tabs-nav').removeClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all')
	jQuery('div#tabs-wrap div').removeClass('ui-tabs-panel ui-widget-content ui-corner-bottom')

	/* initialize the datepicker feature */
	jQuery(".datepicker").datepicker({ dateFormat: 'mm-dd-yy', minDate: 0 });	

});



// ]]>
