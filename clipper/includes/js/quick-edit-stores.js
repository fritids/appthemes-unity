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

    jQuery(document).ready(function(){
        jQuery('#posts-filter').delegate('.editinline', 'click', function(){
            var tag_id = jQuery(this).closest('tr').attr('id');
            var clpr_store_url = jQuery('.clpr_store_url', '#'+tag_id).text();
            var clpr_store_aff_url = jQuery('.clpr_store_aff_url', '#'+tag_id).text();
            jQuery(':input[name="clpr_store_url"]', '.inline-edit-row').val(clpr_store_url);
            jQuery(':input[name="clpr_store_aff_url"]', '.inline-edit-row').val(clpr_store_aff_url);
            if(jQuery('#'+tag_id+' .active-yes').length != 0){
              jQuery("select option[value='yes']", '.inline-edit-row').attr("selected", "selected");
            }else{
               jQuery("select option[value='no']", '.inline-edit-row').attr("selected", "selected");
            }
        });
    });

