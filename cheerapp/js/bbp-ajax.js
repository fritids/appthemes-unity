// run jQuery in no conflict mode to prevent from conflicts with another libraries that use $ dollar symbol
jQuery.noConflict();

jQuery.ajaxSetup({
	cache : false
});

jQuery(document).ready( function() {

	jQuery( '.topic-info-item.subscribe-item, .topic-info-item.fav-item' ).topicAjax();

} );

(function($){
	$.fn.topicAjax = function( vars ) {
		
		var defaults = {
		
		};
		
		var options = $.extend( defaults, vars );
		
		return this.each( function() {
		
			var	element		=	$( this ),
				url			=	wpAjax.unserialize( element.attr( 'rel' ) ),
				text		=	element.find( '.text' );
				
			element.hover(
				function() {
					if( !element.is( '.inactive' ) ) {
						if( element.is( '.fav-item' ) ) text.html( bbpTopicVars.favDel );
						if( element.is( '.subscribe-item' ) ) text.html( bbpTopicVars.subDel );
					}
				},
				function() {
					if( !element.is( '.inactive' ) ) {
						if( element.is( '.fav-item' ) ) text.html( bbpTopicVars.isFav );
						if( element.is( '.subscribe-item' ) ) text.html( bbpTopicVars.isSubscribed );
					}
				}
			);
				
			element.click( function() {
				element.removeClass( 'inactive' ).addClass( 'waiting' );
			
				var s			=	{};
					s.response	=	'ajax-response';
					s.url		=	ajaxVars.ajax_url;
					s.data		=	$.extend( s.data, { action: url.action, _ajax_nonce: url._wpnonce, id: url.topic_id } );
					s.global	=	false;
					s.timeout	=	30000;
					s.type		=	'get';
					s.success	=	function( r ) {
						var res = wpAjax.parseAjaxResponse( r, this.response );
						$( res.responses ).each(function() {
							switch( this.what ) {
								case 'removed' :
									element.removeClass( 'waiting' ).addClass( 'inactive' );
									if( element.is( '.fav-item' ) ) {
										text.html( bbpTopicVars.favAdd );
									}
									else if ( element.is( '.subscribe-item' ) ) {
										text.html( bbpTopicVars.subAdd );
									}
									
									break;
									
								case 'added' :
									element.removeClass( 'waiting inactive' );
									if( element.is( '.fav-item' ) ) {
										text.html( bbpTopicVars.isFav );
									}
									else if ( element.is( '.subscribe-item' ) ) {
										text.html( bbpTopicVars.isSubscribed );
									}
									
									break;
							}
						});
					};
					s.error		= function( r ) {
						console.log( 'Ajax error.' );
					};
					
				$.ajax( s );
				
				return false;
			} );
		} );
		
	}
})(jQuery);
