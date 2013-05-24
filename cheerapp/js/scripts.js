// run jQuery in no conflict mode to prevent from conflicts with another libraries that use $ dollar symbol
jQuery.noConflict();

jQuery.ajaxSetup({
	cache : false
});

jQuery.support.placeholder = (function(){
    var i = document.createElement('input');
    return 'placeholder' in i;
})();

jQuery(document).ready(function(){

	jQuery("input:checkbox, select").uniform();
	
	jQuery('.spinner.big').spin({
		frames			:	11,
		size			:	21,
		fps				:	20
	});
	jQuery('.spinner.small').spin({
		frames			:	11,
		size			:	17,
		fps				:	20
	});
	
	jQuery( '#nav' ).tinyNav({
		active			:	'current-menu-item',
		header			:	true,
		titleText		:	ajaxVars.navigationTitle
	});
	
	jQuery( '#kb-categories' ).tinyNav({
		active			:	'current-cat',
		header			:	true,
		titleText		:	ajaxVars.categoryBrowserTitle,
		prepend			:	'#main .inner-wrap'
	});
	
	jQuery( '.phone-nav' ).mobileSelectNav();
	
	jQuery( '#uniform-login-menu' ).loginUserAvatar();
	
	jQuery('img').removeBorder();
	
	jQuery( '.flexslider' ).loadSlider({
		showTimeout		:	300,
		animation		:	'slide',
		animationLoop	:	false,
		slideshow		:	false
	});
	
	jQuery( 'body.single-topic .breadcrumb, body.single-reply .breadcrumb, body.post-type-archive-forum .breadcrumb' ).fixBreadcrumb();
	
	jQuery('.tabs').simpleTabs();
	
	jQuery('.content').royalImagePreloader();
	
	jQuery("a[rel^='prettyPhoto']").prettyPhoto({
		social_tools	:	'',
		deeplinking		:	false
	});
	
	jQuery("figure.lightbox a").prettyPhoto({
		social_tools	:	'',
		deeplinking		:	false
	});
	
	jQuery('.tooltip, .forum-icon, .topic-post-count, .bbp-reply-permalink, .bbp-topic-permalink, a.topic-status, .bbp-author-name, .bbp-author-avatar').tipsy({fade: false, gravity: 's', delayIn: 0, delayOut: 0, opacity: 1});
	jQuery('.tooltip-white').tipsy({ fade: false, gravity: 's', delayIn: 0, delayOut: 0, opacity: 1, tipClass: 'tipsy-white' });
	jQuery('.tooltip-focus').tipsy({ trigger: 'focus', fade: false, gravity: 's', delayIn: 0, delayOut: 0, opacity: 1 });
	jQuery('.like').tipsy({manual: true, fade: false, gravity: 'se', delayIn: 0, delayOut: 0, opacity: 1});
	
	jQuery('.like').royalLike({
		useHelper		:	true,
		helperSelector	:	'.button-like'
	});
	
	jQuery('.toggle, .faq > li').royalToggle();
	
	jQuery('.info-box, .bbp-template-notice').dismissMessage();
	
	jQuery('#contact-form').contactForm();
	
	if( !jQuery.support.placeholder ) {
		jQuery('[placeholder]').royalInputPlaceholder();
	}
	
	jQuery('.feedback-button').kbShowContactForm();
	
	// Live Search
	var searchForm = jQuery('#searchform');
	if(searchForm.length) {
	searchForm.royalLiveSearch({
		searchTimeout		:	200,
		showDelay			:	65,
		showTransitionSpeed	:	300,
		hideTransitionSpeed	:	200
	});
	}
	
	jQuery().cssHelper();
});

// AJAX Live Search
(function($){
	
	var LiveSearch = function() {
	
		var ignoredKeyCodes = new Array(32, 16, 17, 18, 91, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121);
		var options;
		
		return {
		
			getIgnoredKeyCodes : function() {
				return ignoredKeyCodes;
			},
			
			pushOptions : function(vars) {
				options = vars;
			},
			
			getOptions : function() {
				return options;
			}
			
		};
		
	}
	
	var Form = function(form) {
	
		this.form = form;
		this.searchfield;
		this.initialHeight;
		this.checkboxFields = new Array();
		this.hiddenFields = new Array();
		this.responseContainer;
		this.doingAjax = false;
		
		var obj = this;
		
		this.init = function() {
		
			this.initialHeight = this.form.height();
		
			var cont = this.form.find('.results');
			if( !cont.length ) {
				this.form.append($('<div class="results"></div>'));
				cont = this.form.find('.results');
				this.responseContainer = new ResponseContainer(cont, this);
			}
			
			var list = this.responseContainer.container.find('ul');
			if( !list.length ) {
				this.responseContainer.container.append($('<ul></ul>'));
				list = this.responseContainer.container.find('ul');
				this.responseContainer.container.list = list;
			}
			
			this.submitButton = this.form.find('input[type=submit]');
			
			var searchField,
				checkboxFields = [],
				hiddenFields = [];
			
			this.form.find('input').each(function() {
				var field = $(this);
				if(field.attr('type') === 'text') {
					searchField = new SearchField(field, obj);
				}
				else if(field.attr('type') === 'checkbox') {
					checkboxFields.push( new CheckboxField(field, obj) );
				}
				else if(field.attr('type') === 'hidden') {
					var name	=	field.attr('name');
						value	=	field.attr('value');
						
					hiddenFields[name] = value;
				}
			});
			
			this.searchField = searchField;
			this.checkboxFields = checkboxFields;
			this.hiddenFields = hiddenFields;
			
		}
		
		this.init();
		this.setFormActions();
		
	}
	
	Form.prototype = {
	
		constructor		:	Form,
		
		collectFormData	:	function() {
		
			var formData = new Array();
			
			var key		=	this.searchField.field.attr('name'),
				value	=	this.searchField.field.attr('value');
			
			formData[key] = value;
			
			for( var i in this.checkboxFields ) {
				var field	=	this.checkboxFields[i].field,
					key		=	field.attr('name'),
					value	=	field.attr('value');
					
				formData[key] = value;
			}
			
			this.sendAjaxData(formData);
			
		},
		
		sendAjaxData		:	function(formData) {
			
			var ajaxNonce	=	this.hiddenFields['search-nonce'],
				obj			=	this,
				s			=	{};
			
			s.data			=	{};
			s.data			=	$.extend(s.data, formData);
			
			s.response		=	'ajax-response';
			s.url			=	ajaxVars.ajax_url;
			s.data			=	$.extend(s.data, { action: 'live_search', _ajax_nonce: ajaxNonce });
			s.global		=	false;
			s.timeout		=	20000;
			s.type			=	'post';
			s.success		=	function(response) {
				var res = wpAjax.parseAjaxResponse(response, this.response);
				
				obj.doingAjax = false;
				
				for( var i in res.responses ) {
					switch( res.responses[i].what ) {
						case 'results' :
							obj.responseContainer.displayResults(res.responses[i].supplemental);
							break;
						
						case 'no-posts' :
							obj.responseContainer.nothingFound(res.responses[i].supplemental.message);
							break;
					}
				}
			}
			s.error			=	function(response) {
				obj.doingAjax = false;
			}
			
			this.doingAjax = true;
			$.ajax(s);
			
		},
		
		updateStatus		:	function( status ) {
		
			var obj = this;
			
			if( status == 'loading' ) {
				this.form.removeClass( 'loaded' ).addClass( 'loading' );
				if( this.form.find( '.spinner' ).length ) {
					clearTimeout( this.spinTimer );
				}
				else {
					this.spinner = $( '<span class="spinner small"></span>' );
					this.spinner.appendTo( this.submitButton.parent() ).spin({
						frames			:	11,
						size			:	17,
						fps				:	20
					});
				}
			}
			else if( status == 'loaded' ) {
				var that = this;
				this.responseContainer.container.find( '.more-results' ).click( function() {
					that.form.submit();
				});
				
				this.spinTimer = setTimeout( function() {
					obj.spinner.remove();
					obj.form.removeClass( 'loading' ).addClass( 'loaded' );
				}, 500 );				
			}
			else {
				if( obj.spinner ) obj.spinner.remove();
				obj.form.removeClass( 'loaded' ).addClass( status );
			}
		
		},
		
		updateSize		:	function( event ) {
		
			var listHeight	=	this.responseContainer.container.list.height(),
				obj			=	this,
				targetHeight;
			
			targetHeight = this.initialHeight + listHeight;
			if( targetHeight <= this.initialHeight ) targetHeight = this.initialHeight;
			
			if( event.type == 'blur' ) {
				obj.timer = setTimeout( function() {
					obj.animateSize( obj.initialHeight );
					obj.form.removeClass( 'opened' );
				}, 300 );
			}
			else if( event.type == 'focus' ) {
				clearTimeout( obj.timer );
				obj.animateSize( targetHeight );
				obj.form.addClass( 'opened' );
			}
			else if( event.type == 'mousedown' ) {
				clearTimeout( obj.timer );
			}
			else if( event == 'results' ) {
				obj.animateSize( targetHeight );
				obj.form.addClass( 'opened' );
			}
					
		},
		
		animateSize		:	function( size ) {
		
			var obj = this;
			
			this.form.stop().animateWithCSS({ height: size }, 200, 'easeInOutSine', function() {
				obj.checkAjaxStatus();
			});
			
		},
		
		checkAjaxStatus:	function() {
		
			if( this.doingAjax == true ) {
				this.updateStatus('loading');
			}
		
		},
		
		setFormActions		:	function() {
		
			var obj = this;
		
			this.searchField.field.blur(function( event ) {
				obj.updateSize( event );
			}).blur();
			
			this.searchField.field.focus(function( event ) {
				if( obj.form.is('.loaded') ) {
					obj.updateSize( event );
				}
			});
			
			this.submitButton.click(function( event ) {
				obj.searchField.field.focus();
				event.preventDefault();
				if( obj.form.is('.loaded') ) {
					obj.searchField.clearField();
				}
			});
			
			this.form.mousedown( function( event ) {
				obj.updateSize( event );
			});
			
			this.form.mouseup( function( event ) {
				obj.searchField.field.focus();
			});
			
		}
		
	}
	
	var SearchField = function(field, form) {
	
		this.field		=	field;
		this.value		=	this.field.val();
		this.parentForm	=	form;
		this.timer;
		
		var obj			=	this;
		
		this.field.bind('keyup keydown', function(event) {
			obj.captureInput(event);
		});
		
	}
	
	SearchField.prototype = {
	
		constructor		:	SearchField,
		
		captureInput	:	function(event) {
		
			var keyCode			=	event.keyCode,
				ignoredKeyCodes	=	$.LiveSearch.getIgnoredKeyCodes();
			
			// If ESC is pressed blur the input
			if( keyCode == 27 ) {
				this.field.blur().blur();
			}
			
			// If Return is pressed - do nothing
			else if( keyCode == 13 ) {
				event.preventDefault();
			}
			
			// If the key pressed is not in array of ignored keys (ALT, CMD, SHIFT etc.)
			else if( event.type == 'keyup' && $.inArray(keyCode, ignoredKeyCodes) == -1 ) {
			
				this.value = this.field.val();
				// If at least 2 characters are typed
				if( this.value.length >= 2 ) {
					// Start the timer
					this.setTimer();
				}
				else {
					this.parentForm.responseContainer.clearResults();
					this.parentForm.updateStatus('');
				}
				
			}
			
		},
		
		setTimer		:	function() {
		
			var obj = this,
				options = $.LiveSearch.getOptions();
				
			// Check if the timer is already running, if yes - clear it
			if( this.timer ) {
				clearTimeout(this.timer);
			}
			
			// Set the timer
			this.timer = setTimeout(function() {
				// Call a function to collect form data
				obj.parentForm.collectFormData();
				obj.parentForm.updateStatus('loading');
			}, options.searchTimeout);
			
		},
		
		clearField		:	function() {
			this.field.attr('value', '');
			this.parentForm.responseContainer.clearResults();
		}
	}
	
	var CheckboxField = function(field, form) {
	
		this.field		=	field;
		this.value		=	this.field.val();
		this.parentForm	=	form;
		
	}
	
	var ResponseContainer = function(container, form) {
	
		this.container = container;
		this.container.list;
		this.parentForm = form;
		this.previousResults = new Array();
		
		this.init = function() {
		
			this.container.css({ height: 0 });
		
		}
		
		this.displayResults = function(results) {
		
			for( var i in this.previousResults ) {
				
				this.previousResults[i].remove();
				
				delete this.previousResults[i];			

			}
		
			for( var j in results ) {
				
				var element = $( results[j] );
				
				this.previousResults[j] = element;
				this.container.list.append( this.previousResults[j] );
				
			}
			
			this.parentForm.updateStatus('loaded');
			this.parentForm.updateSize( 'results' );
			
		}
		
		this.clearResults = function() {
			
			for( var i in this.previousResults ) {
				
				this.previousResults[i].remove();
				
				delete this.previousResults[i];			

			}
			
			this.parentForm.updateStatus( 'dupa' );
			this.parentForm.updateSize( 'results' );
			
		}
		
		this.nothingFound = function(message) {
			
			for( var i in this.previousResults ) {
				
				this.previousResults[i].remove();
				
				delete this.previousResults[i];			

			}
			
			var element = $( "<li class='no-results'></li>" ).html( message );
			
			this.previousResults[0] = element;
			this.container.list.append( this.previousResults[0] );
				
			
			this.parentForm.updateStatus('loaded');
			this.parentForm.updateSize( 'results' );
		
		}
		
	}
	
	$.extend($.fn, {
	
		royalLiveSearch : function(vars) {
			var defaults = {
				searchTimeout		:	200,
				showDelay			:	50,
				showTransitionSpeed	:	250,
				hideTransitionSpeed	:	150
			};
			
			var options = $.extend(defaults, vars);
			$.LiveSearch.pushOptions(options);
			
			var form = new Form( $(this[0]) );
			var container = new ResponseContainer( $('.results', form.form) );
		},
		
		search : function(query) {
		
		},
		
		clearForm : function() {
		
		}
	
	});
	
	$.LiveSearch = new LiveSearch();
	
})(jQuery);

(function($){
	$.fn.spin = function( vars ) {
		
		var defaults = {
			frames			:	11,
			size			:	16,
			fps				:	11,
			limit			:	2880,
			left			:	'0'
		};
		
		var options = $.extend( defaults, vars );
		
		var Spinner = function( element ) {
			this.element	=	element;
			this.options	=	options;
			this.frame		=	0;
			this.multiplier	=	0;
			this.timer		=	'';
		}
		
		Spinner.prototype = {
		
			constructor		:	Spinner,
			
			startLoop		:	function() {
				var obj = this;
			
				this.timer = setInterval( function() {
					obj.nextFrame();
				}, 1000/this.options.fps)
			},
			
			nextFrame		:	function() {
				if( this.frame >= this.options.limit ) {
					this.stopLoop();
				}
				else {
					if( this.multiplier > this.options.frames ) {
						this.multiplier = 0;
					}
					var pos = this.options.size * this.multiplier;
					var poss = this.options.left + ' -' + pos + 'px';
					$(this.element).css( "background-position", poss );
					
					this.frame++;
					this.multiplier++;
				}
			},
			
			stopLoop		:	function() {
				clearInterval( this.timer );
			}
		
		}
		
		return this.each(function() {
			
			var spinner = new Spinner( this );
			
			spinner.startLoop();
			
		});		
	}
})(jQuery);

// Removes CSS border property from <a> tags that contain only images
(function($){
	$.fn.removeBorder = function(){
		
		return this.each(function(){
			var element = $(this),
				parent = element.parent('a');
			
			$(parent).addClass('no-border');
		});
	}
})(jQuery);

// Appends a tooltip with contents of target element's title attribute
(function($){
	$.fn.royalTooltip = function(){
		return this.each(function(){
			
			var element =	$( this ),
				title	=	element.attr( 'title' ),
				tooltip	=	$( '<span class="tooltip"></span>' );
			
			element.removeAttr( 'title' ).addClass( 'tooltip-parent' ).append( tooltip );
			tooltip = element.find( '.tooltip' );
			tooltip.html( title );
			
			var tooltipWidth	=	tooltip.outerWidth(),
				left			=	tooltipWidth / 2;
				
			tooltip.css({ marginLeft: -left });
			
		});
	}
})(jQuery);

// Adds "toggle" functionality
(function($){
	$.fn.royalToggle = function(vars, callback){
		
		var defaults = {
			animate			:	true
		};
		
		var options = $.extend(defaults, vars);
		
		return this.each(function(){
			var container		=	$(this),
				tSwitch	=	$('a:first', container),
				tTarget	=	$('.target:first, ul:first', container);
				
				
			var methods = {
				
				init : function(){				
					if(container.is('.current, .opened')){
						container.addClass('opened');
					} else {
						container.addClass('closed');
					}
					
					tSwitch.click(function(){
						methods.toggle();
						return false;
					});
				},
				
				toggle : function(){
					if(container.is('.opened')){
						if(options.animate == true){
							tTarget.hide(300, function(){
								container.removeClass('opened').addClass('closed');
							});
						} else {
							container.removeClass('opened').addClass('closed');
						}
					} else {
						if(options.animate == true){
							tTarget.show(300, function(){
								container.removeClass('closed').addClass('opened');
							});
						} else {
							container.removeClass('closed').addClass('opened');
						}
					}
				}
				
			}
			
			methods.init();
		});
	}
})(jQuery);

// Adds "close" button to info boxes
(function($){
	$.fn.dismissMessage = function( vars, callback ){
		
		var defaults = {
			speed			:	300
		};
		
		var options = $.extend( defaults, vars );
		
		return this.each(function(){
			var container		=	$( this ),
				closeButton		=	$( '<span class="close"></span>' ).prependTo( container );
			
			closeButton.click( function(){
				if( Modernizr.opacity ) {
					container.animateWithCSS({ opacity : 0 }, options.speed, 'easeInOutQuad', function() {
						container.remove();
					});
				}
				else {
					container.remove();
				}
			});
				
		});
	}
})(jQuery);

(function($){
	$.fn.loadSlider = function( vars, callback ) {
		
		var defaults = {
			showTimeout			: 200,
			animation			: 'slide',
			animationLoop		: false,
			slideshow			: false
		}
		
		var options = $.extend( defaults, vars );
		
		return this.each( function() {
			
			var slider		= $( this ),
				slides		= slider.find( '.slides' ),
				images		= $( 'img', slides ),
				firstSlide	= slides.find( 'li:first-child' ),
				spinner		= slider.find( '.spinner' );
			
			slider.init = function() {
				
				slider.addSpinner();
				
				if ( slider.checkImages() == true ) {
					slides.royalImagePreloader({
						onDone			:	slider.reveal,
						callbackScope	:	this,
						autoShowHide	:	false
					});
				} else {
					slider.reveal();
				}
				
			}
			
			slider.addSpinner = function() {
				
				if( spinner == null || !spinner.length ) {
					spinner = $('<span class="spinner big"></span>');
					slider.prepend( spinner );
					spinner.spin({
						frames			:	11,
						size			:	21,
						fps				:	20
					});
				}
				
			}
			
			slider.checkImages = function() {
				
				if ( images.length ) {
					return true;
				} else {
					return false;
				}
				
			}
			
			slider.reveal = function() {
				
				setTimeout(function() {
					
					var $new_height = slides.eq(0).height();     
			        slider.height($new_height);
			        
			        setTimeout( function() {
				        spinner.remove();
				        slides.css({ 'visibility' : 'visible' });
				        slider.start();
			        }, 600);
					
				}, options.showTimeout);
				
			}
			
			slider.start = function() {
				
				slider.flexslider({
					animation		:	options.animation,
					animationLoop	:	options.animationLoop,
					slideshow		:	options.slideshow,         
			        before			:	function(slider) { // init the height of the next item before slide
			            var $new_height = slider.slides.eq(slider.animatingTo).height();                
			            if($new_height != slider.height()){
			                slider.height($new_height);
			            }
			        }
				});
				
			}
			
			slider.init();
			
		});
		
	}
})(jQuery);

(function($) {
	$.fn.simpleTabs = function( vars ) {
		var defaults = {
		
		}
		
		var options = $.extend( defaults, vars );
		
		var Tabs = function( container ) {
			this.tabsContainer	=	$( container );
			this.tabsContainerIn=	$( '.tabs-content', this.tabsContainer );
			this.tabs			=	new Array();
			this.hash			=	window.location.hash;
			this.currentTab;
		}
		
		Tabs.prototype = {
		
			constructor			:	Tabs,
			
			init				:	function() {
				var that = this;
			
				$( '.tab-content', this.tabsContainer ).each( function() {
					that.tabs.push( new Tab( this, that ) );
				});
				
				for( var i in this.tabs ) { 
					this.tabs[i].init();
					this.tabs[i].setIndex( i );
					this.tabs[i].bindControls();
				}
				
				this.hash = this.hash.replace( '#', '' );
				this.resolveStartingTab();
			},
			
			resolveStartingTab	:	function() {
				var that = this;
			
				for( var i in that.tabs ) {
					if( that.hash != '' ) {
						if( that.tabs[i].getTabId() == that.hash ) that.showTab( i );
					}
					else if( that.tabs[i].tabControl.is( '.current' ) ) {
						that.showTab( i );
					}
					else if( i == that.tabs.length - 1 ) {
						that.showTab( 0 );
					}
				}
			},
			
			showTab				:	function( index ) {
				for( var i in this.tabs ) {
					this.tabs[i].hide();
				}
				this.tabs[index].show();
				this.currentTab = index;
				this.adjustHeight();
			},
			
			adjustHeight		:	function() {
				var tabHeight = this.tabs[this.currentTab].tab.outerHeight( true );
				
				this.tabsContainerIn.css({ height : tabHeight });
			}
		
		}
		
		var Tab = function( tab, containerObject ) {
			this.tab			=	$( tab );
			this.tabsContainer	=	containerObject;
			this.tabControl;
			this.theIndex;
		}
		
		Tab.prototype = {
			
			constructor			:	Tab,
			
			init				:	function() {
				this.tabControl = $( 'a[href*="#' + this.getTabId() + '"]', this.tabsContainer.tabsContainer );
			},
			
			getTabId			:	function() {
				var id = this.tab.attr( 'id' ).toString();
				return id;
			},
			
			setIndex			:	function( index ) {
				this.theIndex = index;
			},
			
			bindControls		:	function() {
				var that = this;
			
				this.tabControl.click( function() {
					that.tabsContainer.showTab( that.theIndex );
					
					return false;
				});
				
				this.fixTabPaginationLinks();
			},
			
			fixTabPaginationLinks:	function() {
				var that = this;
				
				this.tab.find( '.forum-pagination a, .pagination a' ).each( function() {
					var paginationLink	= $( this ),
						paginationURL	= paginationLink.attr( 'href' );
					
					paginationLink.attr( 'href', paginationURL + '#' + that.getTabId() );
				});
			},
			
			hide				:	function() {
				this.tab.removeClass( 'current' );
				this.tabControl.removeClass( 'current' );
				this.tab.css({ display : 'none' });
			},
			
			show				:	function() {
				this.tab.addClass( 'current' );
				this.tabControl.addClass( 'current' );
				this.tab.css({ display : 'block' });
			}
						
		}
		
		return this.each( function() {
			var tabs = new Tabs( this );
			tabs.init();
		});
	}
})(jQuery);

// Adds image pre-loading effect. Can be called on single image or set of images.
(function($){
	$.fn.royalImagePreloader = function(vars) {
		var defaults = {
			onDone			:	'',
			onLoadError		:	function(img) {
			},
			callbackScope	:	this,
			speed			:	300,
			easing			:	'easeInOutQuad',
			delay			:	100,
			interval		:	200,
			parent			:	'a',
			autoShowHide	:	true
		}
		
		var options = $.extend(defaults, vars);
		
		return this.each(function() {
			var container	=	$(this),
				images		=	$('img', container),
				delayTime	=	0,
				loadError	=	false;
				
			if(!images.length){
				return false;
			}
			
			container.addClass('loading');
			if ( options.autoShowHide == true ) {
				images.css({ opacity: '0', visibility: 'hidden' }).each(function() {
					var e = $(this),
						parent = e.parent(options.parent),
						eDisplay = e.css('display');
					if( parent.length ) {
						parent.addClass('loading');
					}
					else {
						e.wrap('<a class="loading placeholder"/>');
						parent = e.parent('a');
					}
					parent.css({'display':eDisplay});
				});
			}
			
			var timer = setInterval(function() {
				init();
			}, options.interval);
			
			var init = function() {
				images = images.filter(function() {
					
					var e = $(this),
						parent = e.parent();
				
					this.onerror = function() {
						loadError = true;
					};
					
					if( loadError == 1 ) {
						if ( options.autoShowHide == true ) {
							e.css({ visibility: 'visible', opacity: '1' });
							if( parent.is('.placeholder') ) {
								e.unwrap();
							}
							else {
								parent.removeClass('loading').removeAttr('style');
							}
							e.attr('style','').removeAttr('style');
						}
						options.onLoadError(e);
						return null; 
					}
					else if( this.complete && this.naturalWidth !== 0 ) {
						if( options.autoShowHide == true ) {
							delayTime = delayTime + options.delay;
							parent.css({'background-image':'none'});
							
							e.css({ visibility: 'visible' })
							.delay(delayTime).animateWithCSS({ opacity: 1 }, options.speed, options.easing, function() {
								if( parent.is('.placeholder') ) {
									e.unwrap();
								}
								container.removeClass('loading');
								parent.removeClass('loading').removeAttr('style');
								e.attr('style','').removeAttr('style');
							});
						}
					}
					else {
						return this;
					}
					
				});
				
				if( images.length == 0 ) {
					clearInterval(timer);
					if(options.onDone instanceof Function){
						options.onDone.call( options.callbackScope );
					}
				}
			}
		});
	}
})(jQuery);

(function($){
	$.fn.royalLike = function(vars){
	
		var defaults = {
			useHelper		:	false,
			helperSelector	:	'.button-like'
		};
		
		var options = $.extend(defaults, vars);
		
		return this.each(function() {
		
			var element		=	$(this),
				url			=	wpAjax.unserialize(element.attr('href')),
				count		=	$('<span class="count"></span>'),
				elementHTML	=	element.html(),
				helper,
				spinner;
				
			if( options.useHelper == true ) helper = $( options.helperSelector );
				
			element.html(' ');
			element.append(count);
			count.html(elementHTML);
				
			element.click(function(){
				sendAjax();
				return false;
			});
			
			if( options.useHelper == true ) {
				helper.click(function(){
					sendAjax();
					return false;
				});
			}
			
			var sendAjax = function() {
				if( !element.is('.voted') ) {
				
					if( options.useHelper == true ) {
						helper.addClass( 'loading' );
						
						if( !spinner ) {
							spinner = $( '<span class="spinner small"></span>' ).prependTo( helper );
							spinner.spin({
								frames	:	11,
								size	:	17,
								fps		:	20
							});
						}
					}
				
					var s = {};
						s.response = 'ajax-response';
						s.url = ajaxVars.ajax_url;
						s.data = $.extend(s.data, { action: url.action, _ajax_nonce: url._wpnonce, id: url.id });
						s.global = false;
						s.timeout = 30000;
						s.type = 'post';
						s.success = function(r) {
							var res = wpAjax.parseAjaxResponse(r,this.response);
							$(res.responses).each(function() { 
								switch(this.what) {
									case 'like' :
										var likesCount = this.supplemental.count,
											count = $('.count', element),
											newCount = $('<span class="new"></span>');
											
										if( options.useHelper == true ) {
											helper.html( ajaxVars.thanksForVoting );
											helper.removeClass( 'loading' );
										}
											
										element.css({ position: 'absolute' }).append(newCount);
										newCount = $('.new', element);
										newCount.css({ position: 'absolute', opacity: 0, right: 0 });
										newCount.html(likesCount);
											
										count.css({ opacity: 1 }).animateWithCSS({ opacity: 0 }, 'fast');
										newCount.animateWithCSS({ opacity: 1 }, 'fast', function(){
											count.remove();
											newCount.removeAttr('style').addClass('count').removeClass('new');
											element.removeAttr('style');
										});
										
										element.attr( 'title', ajaxVars.thanksForVoting );
										element.tipsy( 'show' );
										setTimeout( function() {
											element.tipsy( 'hide' );
										}, 5000 );
										
										break;
										
									case 'already-voted' :
										if( options.useHelper == true ) {
											helper.html( ajaxVars.alreadyVoted );
											helper.removeClass( 'loading' );
										}
										element.attr( 'title', ajaxVars.alreadyVoted );
										element.tipsy( 'show' );
										setTimeout( function() {
											element.tipsy( 'hide' );
										}, 5000 );
										
										break;
								}
							});
						}
						s.error = function(r) {
							alert(r);
						}
						
					$.ajax(s);
					element.addClass('voted');
					element.removeAttr('href');
				}
			};
		
		});	
	}
})(jQuery);

// Mobile navigation via select menus
(function($){
	$.fn.mobileSelectNav = function(){
		
		var MobileNav = function( element ) {
			
			this.selectElem		= $( element );
			
		}
		
		MobileNav.prototype = {
			
			constructor			:	MobileNav,
			
			init				:	function() {
			
				var that = this;
			
				this.selectElem.change( function() {
					window.location.href = that.selectElem.val();
				});
			
			}
			
		}
		
		return this.each( function() {
			
			var nav = new MobileNav( this );
			nav.init();
			
		});
		
	}
})(jQuery);

// AJAX contact form. Sends form data to sendEmail.php file and receives response.
(function($){
	$.fn.contactForm = function(){
	
		var Form = function( form ) {
			this.form		=	$( form );
			this.submit		=	this.form.find( '#submit' );
			this.response	=	this.form.find( '#response' );
			this.spinner;
			this.target		=	ajaxVars.ajax_url;
			this.action		=	this.form.find( 'input[name=action]' ).val();
			this.nonce		=	this.form.find( 'input[name=_contact_nonce]' ).val();
			this.fields;
			this.enabledFields = new Array();
		}
		
		Form.prototype = {
			
			constructor		:	Form,
			
			init			:	function() {
			
				var that = this;
			
				if( !this.response ) {
					this.response = $( '<div id="response"></div>' ).appendTo( this.form );
				}
				
				this.fields = {
					name		:	this.form.find( '#royal_name' ),
					author		:	this.form.find( '#royal_author' ),
					email		:	this.form.find( '#royal_email' ),
					subject		:	this.form.find( '#royal_subject' ),
					message		:	this.form.find( '#royal_message' ),
					permalink	:	this.form.find( 'input[name=royal_permalink]' )
				}
				
				for( i in this.fields ) {
					if( !that.fields[i].is( ':disabled' ) ) {
						that.enabledFields.push( this.fields[i] );
					}
				}
			
				this.setControls();
				
			},
			
			setControls		:	function() {
			
				var that = this;
				
				this.submit.click( function() {
					if( !that.submit.is( '.loading' ) && !that.submit.is( '.success' ) ) {
						that.setState( 'loading' );
						that.collectData();
					}
					return false;
				});
				
			},
			
			disableFields	:	function() {
				for( i in this.enabledFields ) {
					this.enabledFields[i].attr( 'disabled', 'disabled' );
				}
			},
			
			enableFields	:	function() {
				for( i in this.enabledFields ) {
					this.enabledFields[i].removeAttr( 'disabled' );
				}
			},
			
			setState	:	function( state ) {
			
				var that = this;
				
				switch( state ) {
					case 'loading':
						
						if( that.form.find( '.spinner' ) ) {
							that.spinner = $( '<span class="spinner small"></span>' );
						}
						that.spinner.prependTo( that.submit );
						that.submit.addClass( 'loading' );
						that.spinner.spin({
							frames	:	11,
							size	:	17,
							fps		:	20
						});
						that.disableFields();
						
						break;
						
					case 'loaded':
					
						that.spinner.remove();
						that.submit.removeClass( 'loading' );
						that.enableFields();
					
						break;
						
					case 'success':
					
						that.spinner.remove();
						that.submit.removeClass( 'loading' ).addClass( 'success' );
						that.disableFields();
						that.submit.html( ajaxVars.sent );
						
						break;
				}
				
			},
			
			collectData		:	function() {
			
				var data = {
					royal_name		:	this.fields.name.val(),
					royal_author	:	this.fields.author.val(),
					royal_email		:	this.fields.email.val(),
					royal_subject	:	this.fields.subject.val(),
					royal_message	:	this.fields.message.val(),
					royal_permalink	:	this.fields.permalink.val(),
					action			:	this.action,
					_ajax_nonce		:	this.nonce
				}
				
				this.sendAjax( data );
				
			},
			
			sendAjax		:	function( data ) {
			
				var that = this;
			
				var s			=	{};
					s.data		=	$.extend( s.data, data );
					s.response	=	'ajax-response';
					s.url		=	this.target;
					s.global	=	false;
					s.timeout	=	30000;
					s.type		=	this.form.attr( 'method' );
					s.success	=	function( results ) {
						var res = $( results );
						
						that.response.html( res );
						
						var error = $( '.error', this.form );
						if( error.length ) {
							that.setState( 'loaded' );
						}
						else {
							that.setState( 'success' );
						}
					}
					s.error		=	function( results ) {
						that.response.html( '<p class="error">There was an error while sending your message. Please try again later</p>' );
						that.setState( 'loaded' );
					}
					
					$.ajax( s );
			}
			
		}
	
		return this.each( function(){
		
			var form = new Form( this );
			
			form.init();
			
		});
	}
})(jQuery);

(function($) {
	$.fn.fixBreadcrumb = function() {
		return this.each( function() {
			var breadcrumb	=	$( this ),
				h2			=	breadcrumb.find( 'h2' ),
				small		=	h2.find( 'small' ),
				lastLink	=	small.find( 'a:last-child, span.bbp-breadcrumb-current:last-child' );
				
			lastLink.remove();
			h2.append( lastLink );
		});
	}
})(jQuery);

// Shows contact form on knowledgebase articles
(function($) {
	$.fn.kbShowContactForm = function( vars ) {
		
		return this.each( function() {
		
			var button	=	$( this ),
				form	=	$( '#contact-form' );
				
			if( Modernizr.opacity ) {
				form.css({ opacity: 0 });
				button.css({ opacity: 1 });
			}
			
			button.click( function() {
				
				form.css({ display: 'block' });
				
				if( Modernizr.opacity ) {
					form.animate({ opacity: 1 }, 500, 'easeInOutSine' );
					button.animate({ opacity: 0 }, 500, 'easeInOutSine', function() {
						button.remove();
					});
				}
				else {
					button.remove();
				}
				
			});
		
		});
		
	}
})(jQuery);

// Adds crossbrowser placeholder functionality on input fields.
(function($){
	$.fn.royalInputPlaceholder = function(){
	
		return this.each(function(){
		
			var	input	=	$(this),
				form	=	input.parents('form');
				
			input.focus(function(){
				if(input.val() == input.attr('placeholder')) {
					input.val('')
					input.removeClass('placeholder');
				}
			}).blur(function(){
				if(input.val() == '' || input.val() == input.attr('placeholder')) {
					input.addClass('placeholder');
					input.val(input.attr('placeholder'));
				}
			}).blur();
			
			form.submit(function(){
				if(input.val() == input.attr('placeholder')) {
					input.val('');
				}
			});
		});
	}
})(jQuery);

(function($) {
	$.fn.loginUserAvatar = function() {
		
		return this.each( function() {
		
			var element		=	$( this ),
				avatar		=	$( '.login img.avatar' ).clone();
				
			if( avatar.length ) {
				element.addClass( 'has-avatar' ).prepend( avatar );
			}
			else return;
		
		});
		
	}
})(jQuery);

// Couple of functions that help to achieve things impossible to do in pure CSS.
(function($){
	$.fn.cssHelper = function(){
	
		var init = function(){
			functions.fixSearchFocus();
			functions.fixFormSubmit();
		}
	
		var functions = {
			
			// Adds "focus" class to search form when it's input field is focused.
			fixSearchFocus : function(){
				var input	=	$('#s'),
					form	=	input.parents('form');
					
				input.focus(function(){
					form.addClass('focus');
				});
				input.blur(function(){
					form.removeClass('focus');
				});
			},
			
			fixFormSubmit : function(){
				var forms	=	$('form');
				
				forms.each(function(){
					var form = $(this),
						button = $('#submit', form),
						buttonP = $('p.button', form);
						
					buttonP.click(function(){
						button[0].click();
					});
				});
			}
			
		}
		
		init();
	}
})(jQuery);



// -------------------------
// -------- PLUGINS --------
// -------------------------

// tipsy, facebook style tooltips for jquery
// version 1.0.0a
// (c) 2008-2010 jason frame [jason@onehackoranother.com]
// released under the MIT license
(function($) {
    
    function maybeCall(thing, ctx) {
        return (typeof thing == 'function') ? (thing.call(ctx)) : thing;
    };
    
    function Tipsy(element, options) {
        this.$element = $(element);
        this.options = options;
        this.enabled = true;
        this.fixTitle();
    };
    
    Tipsy.prototype = {
        show: function() {
            var title = this.getTitle();
            if (title && this.enabled) {
                var $tip = this.tip();
                
                $tip.find('.tipsy-inner')[this.options.html ? 'html' : 'text'](title);
                $tip[0].className = 'tipsy'; // reset classname in case of dynamic gravity
                $tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).prependTo(document.body);
                
                var pos = $.extend({}, this.$element.offset(), {
                    width: this.$element[0].offsetWidth,
                    height: this.$element[0].offsetHeight
                });
                
                var actualWidth = $tip[0].offsetWidth,
                    actualHeight = $tip[0].offsetHeight,
                    gravity = maybeCall(this.options.gravity, this.$element[0]);
                
                var tp;
                switch (gravity.charAt(0)) {
                    case 'n':
                        tp = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 's':
                        tp = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'e':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth - this.options.offset};
                        break;
                    case 'w':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset};
                        break;
                }
                
                if (gravity.length == 2) {
                    if (gravity.charAt(1) == 'w') {
                        tp.left = pos.left + pos.width / 2 - 15;
                    } else {
                        tp.left = pos.left + pos.width / 2 - actualWidth + 15;
                    }
                }
                
                $tip.css(tp).addClass('tipsy-' + gravity);
                $tip.addClass(this.options.tipClass);
                $tip.find('.tipsy-arrow')[0].className = 'tipsy-arrow tipsy-arrow-' + gravity.charAt(0);
                if (this.options.className) {
                    $tip.addClass(maybeCall(this.options.className, this.$element[0]));
                }
                
                if (this.options.fade) {
                    $tip.stop().css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: this.options.opacity});
                } else {
                    $tip.css({visibility: 'visible', opacity: this.options.opacity});
                }
            }
        },
        
        hide: function() {
            if (this.options.fade) {
                this.tip().stop().fadeOut(function() { $(this).remove(); });
            } else {
                this.tip().remove();
            }
        },
        
        fixTitle: function() {
            var $e = this.$element;
            if ($e.attr('title') || typeof($e.attr('original-title')) != 'string') {
                $e.attr('original-title', $e.attr('title') || '').removeAttr('title');
            }
        },
        
        getTitle: function() {
            var title, $e = this.$element, o = this.options;
            this.fixTitle();
            var title, o = this.options;
            if (typeof o.title == 'string') {
                title = $e.attr(o.title == 'title' ? 'original-title' : o.title);
            } else if (typeof o.title == 'function') {
                title = o.title.call($e[0]);
            }
            title = ('' + title).replace(/(^\s*|\s*$)/, "");
            return title || o.fallback;
        },
        
        tip: function() {
            if (!this.$tip) {
                this.$tip = $('<div class="tipsy"></div>').html('<div class="tipsy-arrow"></div><div class="tipsy-inner"></div>');
            }
            return this.$tip;
        },
        
        validate: function() {
            if (!this.$element[0].parentNode) {
                this.hide();
                this.$element = null;
                this.options = null;
            }
        },
        
        enable: function() { this.enabled = true; },
        disable: function() { this.enabled = false; },
        toggleEnabled: function() { this.enabled = !this.enabled; }
    };
    
    $.fn.tipsy = function(options) {
        
        if (options === true) {
            return this.data('tipsy');
        } else if (typeof options == 'string') {
            var tipsy = this.data('tipsy');
            if (tipsy) tipsy[options]();
            return this;
        }
        
        options = $.extend({}, $.fn.tipsy.defaults, options);
        
        function get(ele) {
            var tipsy = $.data(ele, 'tipsy');
            if (!tipsy) {
                tipsy = new Tipsy(ele, $.fn.tipsy.elementOptions(ele, options));
                $.data(ele, 'tipsy', tipsy);
            }
            return tipsy;
        }
        
        function enter() {
            var tipsy = get(this);
            tipsy.hoverState = 'in';
            if (options.delayIn == 0) {
                tipsy.show();
            } else {
                tipsy.fixTitle();
                setTimeout(function() { if (tipsy.hoverState == 'in') tipsy.show(); }, options.delayIn);
            }
        };
        
        function leave() {
            var tipsy = get(this);
            tipsy.hoverState = 'out';
            if (options.delayOut == 0) {
                tipsy.hide();
            } else {
                setTimeout(function() { if (tipsy.hoverState == 'out') tipsy.hide(); }, options.delayOut);
            }
        };
        
        if (!options.live) this.each(function() { get(this); });
        
        if (options.trigger != 'manual') {
            var binder   = options.live ? 'live' : 'bind',
                eventIn  = options.trigger == 'hover' ? 'mouseenter' : 'focus',
                eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
            this[binder](eventIn, enter)[binder](eventOut, leave);
        }
        
        return this;
        
    };
    
    $.fn.tipsy.defaults = {
        className: null,
        delayIn: 0,
        delayOut: 0,
        fade: false,
        fallback: '',
        gravity: 'n',
        html: false,
        live: false,
        offset: 0,
        opacity: 0.8,
        title: 'title',
        trigger: 'hover',
        tipClass: 'tipsy-default'
    };
    
    // Overwrite this method to provide options on a per-element basis.
    // For example, you could store the gravity in a 'tipsy-gravity' attribute:
    // return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
    // (remember - do not modify 'options' in place!)
    $.fn.tipsy.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };
    
    $.fn.tipsy.autoNS = function() {
        return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 's' : 'n';
    };
    
    $.fn.tipsy.autoWE = function() {
        return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'e' : 'w';
    };
    
    /**
     * yields a closure of the supplied parameters, producing a function that takes
     * no arguments and is suitable for use as an autogravity function like so:
     *
     * @param margin (int) - distance from the viewable region edge that an
     *        element should be before setting its tooltip's gravity to be away
     *        from that edge.
     * @param prefer (string, e.g. 'n', 'sw', 'w') - the direction to prefer
     *        if there are no viewable region edges effecting the tooltip's
     *        gravity. It will try to vary from this minimally, for example,
     *        if 'sw' is preferred and an element is near the right viewable 
     *        region edge, but not the top edge, it will set the gravity for
     *        that element's tooltip to be 'se', preserving the southern
     *        component.
     */
     $.fn.tipsy.autoBounds = function(margin, prefer) {
		return function() {
			var dir = {ns: prefer[0], ew: (prefer.length > 1 ? prefer[1] : false)},
			    boundTop = $(document).scrollTop() + margin,
			    boundLeft = $(document).scrollLeft() + margin,
			    $this = $(this);

			if ($this.offset().top < boundTop) dir.ns = 'n';
			if ($this.offset().left < boundLeft) dir.ew = 'w';
			if ($(window).width() + $(document).scrollLeft() - $this.offset().left < margin) dir.ew = 'e';
			if ($(window).height() + $(document).scrollTop() - $this.offset().top < margin) dir.ns = 's';

			return dir.ns + (dir.ew ? dir.ew : '');
		}
	};
    
})(jQuery);

/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

/*
 *
 * TERMS OF USE - EASING EQUATIONS
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
 */
 
/* Modernizr 2.5.3 (Custom Build) | MIT & BSD
 * Build: http://www.modernizr.com/download/#-opacity-csstransitions-touch-cssclasses-teststyles-testprop-testallprops-prefixes-domprefixes
 */
;window.Modernizr=function(a,b,c){function z(a){j.cssText=a}function A(a,b){return z(m.join(a+";")+(b||""))}function B(a,b){return typeof a===b}function C(a,b){return!!~(""+a).indexOf(b)}function D(a,b){for(var d in a)if(j[a[d]]!==c)return b=="pfx"?a[d]:!0;return!1}function E(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:B(f,"function")?f.bind(d||b):f}return!1}function F(a,b,c){var d=a.charAt(0).toUpperCase()+a.substr(1),e=(a+" "+o.join(d+" ")+d).split(" ");return B(b,"string")||B(b,"undefined")?D(e,b):(e=(a+" "+p.join(d+" ")+d).split(" "),E(e,b,c))}var d="2.5.3",e={},f=!0,g=b.documentElement,h="modernizr",i=b.createElement(h),j=i.style,k,l={}.toString,m=" -webkit- -moz- -o- -ms- ".split(" "),n="Webkit Moz O ms",o=n.split(" "),p=n.toLowerCase().split(" "),q={},r={},s={},t=[],u=t.slice,v,w=function(a,c,d,e){var f,i,j,k=b.createElement("div"),l=b.body,m=l?l:b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),k.appendChild(j);return f=["&#173;","<style>",a,"</style>"].join(""),k.id=h,m.innerHTML+=f,m.appendChild(k),l||(m.style.background="",g.appendChild(m)),i=c(k,a),l?k.parentNode.removeChild(k):m.parentNode.removeChild(m),!!i},x={}.hasOwnProperty,y;!B(x,"undefined")&&!B(x.call,"undefined")?y=function(a,b){return x.call(a,b)}:y=function(a,b){return b in a&&B(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=u.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(u.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(u.call(arguments)))};return e});var G=function(c,d){var f=c.join(""),g=d.length;w(f,function(c,d){var f=b.styleSheets[b.styleSheets.length-1],h=f?f.cssRules&&f.cssRules[0]?f.cssRules[0].cssText:f.cssText||"":"",i=c.childNodes,j={};while(g--)j[i[g].id]=i[g];e.touch="ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch||(j.touch&&j.touch.offsetTop)===9},g,d)}([,["@media (",m.join("touch-enabled),("),h,")","{#touch{top:9px;position:absolute}}"].join("")],[,"touch"]);q.touch=function(){return e.touch},q.opacity=function(){return A("opacity:.55"),/^0.55$/.test(j.opacity)},q.csstransitions=function(){return F("transition")};for(var H in q)y(q,H)&&(v=H.toLowerCase(),e[v]=q[H](),t.push((e[v]?"":"no-")+v));return z(""),i=k=null,e._version=d,e._prefixes=m,e._domPrefixes=p,e._cssomPrefixes=o,e.testProp=function(a){return D([a])},e.testAllProps=F,e.testStyles=w,g.className=g.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(f?" js "+t.join(" "):""),e}(this,this.document);

/*! http://tinynav.viljamis.com v1.03 by @viljamis */
/*! Modified for CheerApp */
(function( $, i, g ){
	$.fn.tinyNav	=	function( vars ){
		var options	= $.extend({
			active		:	'selected',
			header		:	!1,
			titleText	:	'Navigation',
			prepend		:	false
		}, vars);
		
		return this.each( function(){
			g++;
			var h	=	$( this ),
				d	=	'tinynav' + g,
				e	=	'.l_' + d,
				b	=	$( '<select/>' ).addClass( 'tinynav ' + d ).attr( 'id', 'tinynav-' + h.attr( 'id' ) );
				
			if( h.is( 'ul,ol' ) ){
				options.header && b.append( $( '<option value="#" />' ).text( options.titleText ) );
				
				var f	=	'';
				
				h.addClass( 'l_' + d ).find( 'a' ).each( function(){
					var link = $( this );
					f += '<option value="' + $( this ).attr( 'href' ) + '"';
					if( link.parent( 'li' ).is( '.' + options.active ) ) {
						f += ' selected="selected"';
					}
					f += '>' + $( this ).text() + '</option>';
				});
				
				b.append( f );
				
				b.change( function(){
					i.location.href = $( this ).val()
				});
				
				if( options.prepend ) {
					$( options.prepend ).prepend( b )
				}
				else {
					$( e ).after( b )
				}
				
				$.isFunction( $.uniform ) || b.uniform()
			}
		})
	}
})(jQuery,this,0);

/*

Uniform v1.7.5
Copyright © 2009 Josh Pyles / Pixelmatrix Design LLC
http://pixelmatrixdesign.com

Requires jQuery 1.4 or newer

Much thanks to Thomas Reynolds and Buck Wilson for their help and advice on this

Disabling text selection is made possible by Mathias Bynens <http://mathiasbynens.be/>
and his noSelect plugin. <http://github.com/mathiasbynens/noSelect-jQuery-Plugin>

Also, thanks to David Kaneda and Eugene Bond for their contributions to the plugin

License:
MIT License - http://www.opensource.org/licenses/mit-license.php

Enjoy!

*/

(function(a){a.uniform={options:{selectClass:"selector",radioClass:"radio",checkboxClass:"checker",fileClass:"uploader",filenameClass:"filename",fileBtnClass:"action",fileDefaultText:"No file selected",fileBtnText:"Choose File",checkedClass:"checked",focusClass:"focus",disabledClass:"disabled",buttonClass:"button",activeClass:"active",hoverClass:"hover",useID:true,idPrefix:"uniform",resetSelector:false,autoHide:true},elements:[]};if(a.browser.msie&&a.browser.version<7){a.support.selectOpacity=false}else{a.support.selectOpacity=true}a.fn.uniform=function(k){k=a.extend(a.uniform.options,k);var d=this;if(k.resetSelector!=false){a(k.resetSelector).mouseup(function(){function l(){a.uniform.update(d)}setTimeout(l,10)})}function j(l){$el=a(l);$el.addClass($el.attr("type"));b(l)}function g(l){a(l).addClass("uniform");b(l)}function i(o){var m=a(o);var p=a("<div>"),l=a("<span>");p.addClass(k.buttonClass);if(k.useID&&m.attr("id")!=""){p.attr("id",k.idPrefix+"-"+m.attr("id"))}var n;if(m.is("a")||m.is("button")){n=m.text()}else{if(m.is(":submit")||m.is(":reset")||m.is("input[type=button]")){n=m.attr("value")}}n=n==""?m.is(":reset")?"Reset":"Submit":n;l.html(n);m.css("opacity",0);m.wrap(p);m.wrap(l);p=m.closest("div");l=m.closest("span");if(m.is(":disabled")){p.addClass(k.disabledClass)}p.bind({"mouseenter.uniform":function(){p.addClass(k.hoverClass)},"mouseleave.uniform":function(){p.removeClass(k.hoverClass);p.removeClass(k.activeClass)},"mousedown.uniform touchbegin.uniform":function(){p.addClass(k.activeClass)},"mouseup.uniform touchend.uniform":function(){p.removeClass(k.activeClass)},"click.uniform touchend.uniform":function(r){if(a(r.target).is("span")||a(r.target).is("div")){if(o[0].dispatchEvent){var q=document.createEvent("MouseEvents");q.initEvent("click",true,true);o[0].dispatchEvent(q)}else{o[0].click()}}}});o.bind({"focus.uniform":function(){p.addClass(k.focusClass)},"blur.uniform":function(){p.removeClass(k.focusClass)}});a.uniform.noSelect(p);b(o)}function e(o){var m=a(o);var p=a("<div />"),l=a("<span />");if(!m.css("display")=="none"&&k.autoHide){p.hide()}p.addClass(k.selectClass);if(k.useID&&o.attr("id")!=""){p.attr("id",k.idPrefix+"-"+o.attr("id"))}var n=o.find(":selected:first");if(n.length==0){n=o.find("option:first")}l.html(n.html());o.css("opacity",0);o.wrap(p);o.before(l);p=o.parent("div");l=o.siblings("span");o.bind({"change.uniform":function(){l.text(o.find(":selected").html());p.removeClass(k.activeClass)},"focus.uniform":function(){p.addClass(k.focusClass)},"blur.uniform":function(){p.removeClass(k.focusClass);p.removeClass(k.activeClass)},"mousedown.uniform touchbegin.uniform":function(){p.addClass(k.activeClass)},"mouseup.uniform touchend.uniform":function(){p.removeClass(k.activeClass)},"click.uniform touchend.uniform":function(){p.removeClass(k.activeClass)},"mouseenter.uniform":function(){p.addClass(k.hoverClass)},"mouseleave.uniform":function(){p.removeClass(k.hoverClass);p.removeClass(k.activeClass)},"keyup.uniform":function(){l.text(o.find(":selected").html())}});if(a(o).attr("disabled")){p.addClass(k.disabledClass)}a.uniform.noSelect(l);b(o)}function f(n){var m=a(n);var o=a("<div />"),l=a("<span />");if(!m.css("display")=="none"&&k.autoHide){o.hide()}o.addClass(k.checkboxClass);if(k.useID&&n.attr("id")!=""){o.attr("id",k.idPrefix+"-"+n.attr("id"))}a(n).wrap(o);a(n).wrap(l);l=n.parent();o=l.parent();a(n).css("opacity",0).bind({"focus.uniform":function(){o.addClass(k.focusClass)},"blur.uniform":function(){o.removeClass(k.focusClass)},"click.uniform touchend.uniform":function(){if(!a(n).attr("checked")){l.removeClass(k.checkedClass)}else{l.addClass(k.checkedClass)}},"mousedown.uniform touchbegin.uniform":function(){o.addClass(k.activeClass)},"mouseup.uniform touchend.uniform":function(){o.removeClass(k.activeClass)},"mouseenter.uniform":function(){o.addClass(k.hoverClass)},"mouseleave.uniform":function(){o.removeClass(k.hoverClass);o.removeClass(k.activeClass)}});if(a(n).attr("checked")){l.addClass(k.checkedClass)}if(a(n).attr("disabled")){o.addClass(k.disabledClass)}b(n)}function c(n){var m=a(n);var o=a("<div />"),l=a("<span />");if(!m.css("display")=="none"&&k.autoHide){o.hide()}o.addClass(k.radioClass);if(k.useID&&n.attr("id")!=""){o.attr("id",k.idPrefix+"-"+n.attr("id"))}a(n).wrap(o);a(n).wrap(l);l=n.parent();o=l.parent();a(n).css("opacity",0).bind({"focus.uniform":function(){o.addClass(k.focusClass)},"blur.uniform":function(){o.removeClass(k.focusClass)},"click.uniform touchend.uniform":function(){if(!a(n).attr("checked")){l.removeClass(k.checkedClass)}else{var p=k.radioClass.split(" ")[0];a("."+p+" span."+k.checkedClass+":has([name='"+a(n).attr("name")+"'])").removeClass(k.checkedClass);l.addClass(k.checkedClass)}},"mousedown.uniform touchend.uniform":function(){if(!a(n).is(":disabled")){o.addClass(k.activeClass)}},"mouseup.uniform touchbegin.uniform":function(){o.removeClass(k.activeClass)},"mouseenter.uniform touchend.uniform":function(){o.addClass(k.hoverClass)},"mouseleave.uniform":function(){o.removeClass(k.hoverClass);o.removeClass(k.activeClass)}});if(a(n).attr("checked")){l.addClass(k.checkedClass)}if(a(n).attr("disabled")){o.addClass(k.disabledClass)}b(n)}function h(q){var o=a(q);var r=a("<div />"),p=a("<span>"+k.fileDefaultText+"</span>"),m=a("<span>"+k.fileBtnText+"</span>");if(!o.css("display")=="none"&&k.autoHide){r.hide()}r.addClass(k.fileClass);p.addClass(k.filenameClass);m.addClass(k.fileBtnClass);if(k.useID&&o.attr("id")!=""){r.attr("id",k.idPrefix+"-"+o.attr("id"))}o.wrap(r);o.after(m);o.after(p);r=o.closest("div");p=o.siblings("."+k.filenameClass);m=o.siblings("."+k.fileBtnClass);if(!o.attr("size")){var l=r.width();o.attr("size",l/10)}var n=function(){var s=o.val();if(s===""){s=k.fileDefaultText}else{s=s.split(/[\/\\]+/);s=s[(s.length-1)]}p.text(s)};n();o.css("opacity",0).bind({"focus.uniform":function(){r.addClass(k.focusClass)},"blur.uniform":function(){r.removeClass(k.focusClass)},"mousedown.uniform":function(){if(!a(q).is(":disabled")){r.addClass(k.activeClass)}},"mouseup.uniform":function(){r.removeClass(k.activeClass)},"mouseenter.uniform":function(){r.addClass(k.hoverClass)},"mouseleave.uniform":function(){r.removeClass(k.hoverClass);r.removeClass(k.activeClass)}});if(a.browser.msie){o.bind("click.uniform.ie7",function(){setTimeout(n,0)})}else{o.bind("change.uniform",n)}if(o.attr("disabled")){r.addClass(k.disabledClass)}a.uniform.noSelect(p);a.uniform.noSelect(m);b(q)}a.uniform.restore=function(l){if(l==undefined){l=a(a.uniform.elements)}a(l).each(function(){if(a(this).is(":checkbox")){a(this).unwrap().unwrap()}else{if(a(this).is("select")){a(this).siblings("span").remove();a(this).unwrap()}else{if(a(this).is(":radio")){a(this).unwrap().unwrap()}else{if(a(this).is(":file")){a(this).siblings("span").remove();a(this).unwrap()}else{if(a(this).is("button, :submit, :reset, a, input[type='button']")){a(this).unwrap().unwrap()}}}}}a(this).unbind(".uniform");a(this).css("opacity","1");var m=a.inArray(a(l),a.uniform.elements);a.uniform.elements.splice(m,1)})};function b(l){l=a(l).get();if(l.length>1){a.each(l,function(m,n){a.uniform.elements.push(n)})}else{a.uniform.elements.push(l)}}a.uniform.noSelect=function(l){function m(){return false}a(l).each(function(){this.onselectstart=this.ondragstart=m;a(this).mousedown(m).css({MozUserSelect:"none"})})};a.uniform.update=function(l){if(l==undefined){l=a(a.uniform.elements)}l=a(l);l.each(function(){var n=a(this);if(n.is("select")){var m=n.siblings("span");var p=n.parent("div");p.removeClass(k.hoverClass+" "+k.focusClass+" "+k.activeClass);m.html(n.find(":selected").html());if(n.is(":disabled")){p.addClass(k.disabledClass)}else{p.removeClass(k.disabledClass)}}else{if(n.is(":checkbox")){var m=n.closest("span");var p=n.closest("div");p.removeClass(k.hoverClass+" "+k.focusClass+" "+k.activeClass);m.removeClass(k.checkedClass);if(n.is(":checked")){m.addClass(k.checkedClass)}if(n.is(":disabled")){p.addClass(k.disabledClass)}else{p.removeClass(k.disabledClass)}}else{if(n.is(":radio")){var m=n.closest("span");var p=n.closest("div");p.removeClass(k.hoverClass+" "+k.focusClass+" "+k.activeClass);m.removeClass(k.checkedClass);if(n.is(":checked")){m.addClass(k.checkedClass)}if(n.is(":disabled")){p.addClass(k.disabledClass)}else{p.removeClass(k.disabledClass)}}else{if(n.is(":file")){var p=n.parent("div");var o=n.siblings(k.filenameClass);btnTag=n.siblings(k.fileBtnClass);p.removeClass(k.hoverClass+" "+k.focusClass+" "+k.activeClass);o.text(n.val());if(n.is(":disabled")){p.addClass(k.disabledClass)}else{p.removeClass(k.disabledClass)}}else{if(n.is(":submit")||n.is(":reset")||n.is("button")||n.is("a")||l.is("input[type=button]")){var p=n.closest("div");p.removeClass(k.hoverClass+" "+k.focusClass+" "+k.activeClass);if(n.is(":disabled")){p.addClass(k.disabledClass)}else{p.removeClass(k.disabledClass)}}}}}}})};return this.each(function(){if(a.support.selectOpacity){var l=a(this);if(l.is("select")){if(l.attr("multiple")!=true){if(l.attr("size")==undefined||l.attr("size")<=1){e(l)}}}else{if(l.is(":checkbox")){f(l)}else{if(l.is(":radio")){c(l)}else{if(l.is(":file")){h(l)}else{if(l.is(":text, :password, input[type='email']")){j(l)}else{if(l.is("textarea")){g(l)}else{if(l.is("a")||l.is(":submit")||l.is(":reset")||l.is("button")||l.is("input[type=button]")){i(l)}}}}}}}}})}})(jQuery);

/*
 * jQuery FlexSlider v2.1
 * Copyright 2012 WooThemes
 * Contributing Author: Tyler Smith
 */
;(function(d){d.flexslider=function(i,k){var a=d(i),c=d.extend({},d.flexslider.defaults,k),e=c.namespace,p="ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch,t=p?"touchend":"click",l="vertical"===c.direction,m=c.reverse,h=0<c.itemWidth,r="fade"===c.animation,s=""!==c.asNavFor,f={};d.data(i,"flexslider",a);f={init:function(){a.animating=!1;a.currentSlide=c.startAt;a.animatingTo=a.currentSlide;a.atEnd=0===a.currentSlide||a.currentSlide===a.last;a.containerSelector=c.selector.substr(0,
 c.selector.search(" "));a.slides=d(c.selector,a);a.container=d(a.containerSelector,a);a.count=a.slides.length;a.syncExists=0<d(c.sync).length;"slide"===c.animation&&(c.animation="swing");a.prop=l?"top":"marginLeft";a.args={};a.manualPause=!1;var b=a,g;if(g=!c.video)if(g=!r)if(g=c.useCSS)a:{g=document.createElement("div");var n=["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"],e;for(e in n)if(void 0!==g.style[n[e]]){a.pfx=n[e].replace("Perspective","").toLowerCase();
 a.prop="-"+a.pfx+"-transform";g=!0;break a}g=!1}b.transitions=g;""!==c.controlsContainer&&(a.controlsContainer=0<d(c.controlsContainer).length&&d(c.controlsContainer));""!==c.manualControls&&(a.manualControls=0<d(c.manualControls).length&&d(c.manualControls));c.randomize&&(a.slides.sort(function(){return Math.round(Math.random())-0.5}),a.container.empty().append(a.slides));a.doMath();s&&f.asNav.setup();a.setup("init");c.controlNav&&f.controlNav.setup();c.directionNav&&f.directionNav.setup();c.keyboard&&
 (1===d(a.containerSelector).length||c.multipleKeyboard)&&d(document).bind("keyup",function(b){b=b.keyCode;if(!a.animating&&(39===b||37===b))b=39===b?a.getTarget("next"):37===b?a.getTarget("prev"):!1,a.flexAnimate(b,c.pauseOnAction)});c.mousewheel&&a.bind("mousewheel",function(b,g){b.preventDefault();var d=0>g?a.getTarget("next"):a.getTarget("prev");a.flexAnimate(d,c.pauseOnAction)});c.pausePlay&&f.pausePlay.setup();c.slideshow&&(c.pauseOnHover&&a.hover(function(){!a.manualPlay&&!a.manualPause&&a.pause()},
 function(){!a.manualPause&&!a.manualPlay&&a.play()}),0<c.initDelay?setTimeout(a.play,c.initDelay):a.play());p&&c.touch&&f.touch();(!r||r&&c.smoothHeight)&&d(window).bind("resize focus",f.resize);setTimeout(function(){c.start(a)},200)},asNav:{setup:function(){a.asNav=!0;a.animatingTo=Math.floor(a.currentSlide/a.move);a.currentItem=a.currentSlide;a.slides.removeClass(e+"active-slide").eq(a.currentItem).addClass(e+"active-slide");a.slides.click(function(b){b.preventDefault();var b=d(this),g=b.index();
 !d(c.asNavFor).data("flexslider").animating&&!b.hasClass("active")&&(a.direction=a.currentItem<g?"next":"prev",a.flexAnimate(g,c.pauseOnAction,!1,!0,!0))})}},controlNav:{setup:function(){a.manualControls?f.controlNav.setupManual():f.controlNav.setupPaging()},setupPaging:function(){var b=1,g;a.controlNavScaffold=d('<ol class="'+e+"control-nav "+e+("thumbnails"===c.controlNav?"control-thumbs":"control-paging")+'"></ol>');if(1<a.pagingCount)for(var n=0;n<a.pagingCount;n++)g="thumbnails"===c.controlNav?
 '<img src="'+a.slides.eq(n).attr("data-thumb")+'"/>':"<a>"+b+"</a>",a.controlNavScaffold.append("<li>"+g+"</li>"),b++;a.controlsContainer?d(a.controlsContainer).append(a.controlNavScaffold):a.append(a.controlNavScaffold);f.controlNav.set();f.controlNav.active();a.controlNavScaffold.delegate("a, img",t,function(b){b.preventDefault();var b=d(this),g=a.controlNav.index(b);b.hasClass(e+"active")||(a.direction=g>a.currentSlide?"next":"prev",a.flexAnimate(g,c.pauseOnAction))});p&&a.controlNavScaffold.delegate("a",
 "click touchstart",function(a){a.preventDefault()})},setupManual:function(){a.controlNav=a.manualControls;f.controlNav.active();a.controlNav.live(t,function(b){b.preventDefault();var b=d(this),g=a.controlNav.index(b);b.hasClass(e+"active")||(g>a.currentSlide?a.direction="next":a.direction="prev",a.flexAnimate(g,c.pauseOnAction))});p&&a.controlNav.live("click touchstart",function(a){a.preventDefault()})},set:function(){a.controlNav=d("."+e+"control-nav li "+("thumbnails"===c.controlNav?"img":"a"),
 a.controlsContainer?a.controlsContainer:a)},active:function(){a.controlNav.removeClass(e+"active").eq(a.animatingTo).addClass(e+"active")},update:function(b,c){1<a.pagingCount&&"add"===b?a.controlNavScaffold.append(d("<li><a>"+a.count+"</a></li>")):1===a.pagingCount?a.controlNavScaffold.find("li").remove():a.controlNav.eq(c).closest("li").remove();f.controlNav.set();1<a.pagingCount&&a.pagingCount!==a.controlNav.length?a.update(c,b):f.controlNav.active()}},directionNav:{setup:function(){var b=d('<ul class="'+
 e+'direction-nav"><li><a class="'+e+'prev" href="#">'+c.prevText+'</a></li><li><a class="'+e+'next" href="#">'+c.nextText+"</a></li></ul>");a.controlsContainer?(d(a.controlsContainer).append(b),a.directionNav=d("."+e+"direction-nav li a",a.controlsContainer)):(a.append(b),a.directionNav=d("."+e+"direction-nav li a",a));f.directionNav.update();a.directionNav.bind(t,function(b){b.preventDefault();b=d(this).hasClass(e+"next")?a.getTarget("next"):a.getTarget("prev");a.flexAnimate(b,c.pauseOnAction)});
 p&&a.directionNav.bind("click touchstart",function(a){a.preventDefault()})},update:function(){var b=e+"disabled";1===a.pagingCount?a.directionNav.addClass(b):c.animationLoop?a.directionNav.removeClass(b):0===a.animatingTo?a.directionNav.removeClass(b).filter("."+e+"prev").addClass(b):a.animatingTo===a.last?a.directionNav.removeClass(b).filter("."+e+"next").addClass(b):a.directionNav.removeClass(b)}},pausePlay:{setup:function(){var b=d('<div class="'+e+'pauseplay"><a></a></div>');a.controlsContainer?
 (a.controlsContainer.append(b),a.pausePlay=d("."+e+"pauseplay a",a.controlsContainer)):(a.append(b),a.pausePlay=d("."+e+"pauseplay a",a));f.pausePlay.update(c.slideshow?e+"pause":e+"play");a.pausePlay.bind(t,function(b){b.preventDefault();d(this).hasClass(e+"pause")?(a.manualPause=!0,a.manualPlay=!1,a.pause()):(a.manualPause=!1,a.manualPlay=!0,a.play())});p&&a.pausePlay.bind("click touchstart",function(a){a.preventDefault()})},update:function(b){"play"===b?a.pausePlay.removeClass(e+"pause").addClass(e+
 "play").text(c.playText):a.pausePlay.removeClass(e+"play").addClass(e+"pause").text(c.pauseText)}},touch:function(){function b(b){j=l?d-b.touches[0].pageY:d-b.touches[0].pageX;p=l?Math.abs(j)<Math.abs(b.touches[0].pageX-e):Math.abs(j)<Math.abs(b.touches[0].pageY-e);if(!p||500<Number(new Date)-k)b.preventDefault(),!r&&a.transitions&&(c.animationLoop||(j/=0===a.currentSlide&&0>j||a.currentSlide===a.last&&0<j?Math.abs(j)/q+2:1),a.setProps(f+j,"setTouch"))}function g(){i.removeEventListener("touchmove",
 b,!1);if(a.animatingTo===a.currentSlide&&!p&&null!==j){var h=m?-j:j,l=0<h?a.getTarget("next"):a.getTarget("prev");a.canAdvance(l)&&(550>Number(new Date)-k&&50<Math.abs(h)||Math.abs(h)>q/2)?a.flexAnimate(l,c.pauseOnAction):r||a.flexAnimate(a.currentSlide,c.pauseOnAction,!0)}i.removeEventListener("touchend",g,!1);f=j=e=d=null}var d,e,f,q,j,k,p=!1;i.addEventListener("touchstart",function(j){a.animating?j.preventDefault():1===j.touches.length&&(a.pause(),q=l?a.h:a.w,k=Number(new Date),f=h&&m&&a.animatingTo===
 a.last?0:h&&m?a.limit-(a.itemW+c.itemMargin)*a.move*a.animatingTo:h&&a.currentSlide===a.last?a.limit:h?(a.itemW+c.itemMargin)*a.move*a.currentSlide:m?(a.last-a.currentSlide+a.cloneOffset)*q:(a.currentSlide+a.cloneOffset)*q,d=l?j.touches[0].pageY:j.touches[0].pageX,e=l?j.touches[0].pageX:j.touches[0].pageY,i.addEventListener("touchmove",b,!1),i.addEventListener("touchend",g,!1))},!1)},resize:function(){!a.animating&&a.is(":visible")&&(h||a.doMath(),r?f.smoothHeight():h?(a.slides.width(a.computedW),
 a.update(a.pagingCount),a.setProps()):l?(a.viewport.height(a.h),a.setProps(a.h,"setTotal")):(c.smoothHeight&&f.smoothHeight(),a.newSlides.width(a.computedW),a.setProps(a.computedW,"setTotal")))},smoothHeight:function(b){if(!l||r){var c=r?a:a.viewport;b?c.animate({height:a.slides.eq(a.animatingTo).height()},b):c.height(a.slides.eq(a.animatingTo).height())}},sync:function(b){var g=d(c.sync).data("flexslider"),e=a.animatingTo;switch(b){case "animate":g.flexAnimate(e,c.pauseOnAction,!1,!0);break;case "play":!g.playing&&
 !g.asNav&&g.play();break;case "pause":g.pause()}}};a.flexAnimate=function(b,g,n,i,k){s&&1===a.pagingCount&&(a.direction=a.currentItem<b?"next":"prev");if(!a.animating&&(a.canAdvance(b,k)||n)&&a.is(":visible")){if(s&&i)if(n=d(c.asNavFor).data("flexslider"),a.atEnd=0===b||b===a.count-1,n.flexAnimate(b,!0,!1,!0,k),a.direction=a.currentItem<b?"next":"prev",n.direction=a.direction,Math.ceil((b+1)/a.visible)-1!==a.currentSlide&&0!==b)a.currentItem=b,a.slides.removeClass(e+"active-slide").eq(b).addClass(e+
 "active-slide"),b=Math.floor(b/a.visible);else return a.currentItem=b,a.slides.removeClass(e+"active-slide").eq(b).addClass(e+"active-slide"),!1;a.animating=!0;a.animatingTo=b;c.before(a);g&&a.pause();a.syncExists&&!k&&f.sync("animate");c.controlNav&&f.controlNav.active();h||a.slides.removeClass(e+"active-slide").eq(b).addClass(e+"active-slide");a.atEnd=0===b||b===a.last;c.directionNav&&f.directionNav.update();b===a.last&&(c.end(a),c.animationLoop||a.pause());if(r)p?(a.slides.eq(a.currentSlide).css({opacity:0,
 zIndex:1}),a.slides.eq(b).css({opacity:1,zIndex:2}),a.animating=!1,a.currentSlide=a.animatingTo):(a.slides.eq(a.currentSlide).fadeOut(c.animationSpeed,c.easing),a.slides.eq(b).fadeIn(c.animationSpeed,c.easing,a.wrapup));else{var q=l?a.slides.filter(":first").height():a.computedW;h?(b=c.itemWidth>a.w?2*c.itemMargin:c.itemMargin,b=(a.itemW+b)*a.move*a.animatingTo,b=b>a.limit&&1!==a.visible?a.limit:b):b=0===a.currentSlide&&b===a.count-1&&c.animationLoop&&"next"!==a.direction?m?(a.count+a.cloneOffset)*
 q:0:a.currentSlide===a.last&&0===b&&c.animationLoop&&"prev"!==a.direction?m?0:(a.count+1)*q:m?(a.count-1-b+a.cloneOffset)*q:(b+a.cloneOffset)*q;a.setProps(b,"",c.animationSpeed);if(a.transitions){if(!c.animationLoop||!a.atEnd)a.animating=!1,a.currentSlide=a.animatingTo;a.container.unbind("webkitTransitionEnd transitionend");a.container.bind("webkitTransitionEnd transitionend",function(){a.wrapup(q)})}else a.container.animate(a.args,c.animationSpeed,c.easing,function(){a.wrapup(q)})}c.smoothHeight&&
 f.smoothHeight(c.animationSpeed)}};a.wrapup=function(b){!r&&!h&&(0===a.currentSlide&&a.animatingTo===a.last&&c.animationLoop?a.setProps(b,"jumpEnd"):a.currentSlide===a.last&&(0===a.animatingTo&&c.animationLoop)&&a.setProps(b,"jumpStart"));a.animating=!1;a.currentSlide=a.animatingTo;c.after(a)};a.animateSlides=function(){a.animating||a.flexAnimate(a.getTarget("next"))};a.pause=function(){clearInterval(a.animatedSlides);a.playing=!1;c.pausePlay&&f.pausePlay.update("play");a.syncExists&&f.sync("pause")};
 a.play=function(){a.animatedSlides=setInterval(a.animateSlides,c.slideshowSpeed);a.playing=!0;c.pausePlay&&f.pausePlay.update("pause");a.syncExists&&f.sync("play")};a.canAdvance=function(b,g){var d=s?a.pagingCount-1:a.last;return g?!0:s&&a.currentItem===a.count-1&&0===b&&"prev"===a.direction?!0:s&&0===a.currentItem&&b===a.pagingCount-1&&"next"!==a.direction?!1:b===a.currentSlide&&!s?!1:c.animationLoop?!0:a.atEnd&&0===a.currentSlide&&b===d&&"next"!==a.direction?!1:a.atEnd&&a.currentSlide===d&&0===
 b&&"next"===a.direction?!1:!0};a.getTarget=function(b){a.direction=b;return"next"===b?a.currentSlide===a.last?0:a.currentSlide+1:0===a.currentSlide?a.last:a.currentSlide-1};a.setProps=function(b,g,d){var e,f=b?b:(a.itemW+c.itemMargin)*a.move*a.animatingTo;e=-1*function(){if(h)return"setTouch"===g?b:m&&a.animatingTo===a.last?0:m?a.limit-(a.itemW+c.itemMargin)*a.move*a.animatingTo:a.animatingTo===a.last?a.limit:f;switch(g){case "setTotal":return m?(a.count-1-a.currentSlide+a.cloneOffset)*b:(a.currentSlide+
 a.cloneOffset)*b;case "setTouch":return b;case "jumpEnd":return m?b:a.count*b;case "jumpStart":return m?a.count*b:b;default:return b}}()+"px";a.transitions&&(e=l?"translate3d(0,"+e+",0)":"translate3d("+e+",0,0)",d=void 0!==d?d/1E3+"s":"0s",a.container.css("-"+a.pfx+"-transition-duration",d));a.args[a.prop]=e;(a.transitions||void 0===d)&&a.container.css(a.args)};a.setup=function(b){if(r)a.slides.css({width:"100%","float":"left",marginRight:"-100%",position:"relative"}),"init"===b&&(p?a.slides.css({opacity:0,
 display:"block",webkitTransition:"opacity "+c.animationSpeed/1E3+"s ease",zIndex:1}).eq(a.currentSlide).css({opacity:1,zIndex:2}):a.slides.eq(a.currentSlide).fadeIn(c.animationSpeed,c.easing)),c.smoothHeight&&f.smoothHeight();else{var g,n;"init"===b&&(a.viewport=d('<div class="'+e+'viewport"></div>').css({overflow:"hidden",position:"relative"}).appendTo(a).append(a.container),a.cloneCount=0,a.cloneOffset=0,m&&(n=d.makeArray(a.slides).reverse(),a.slides=d(n),a.container.empty().append(a.slides)));
 c.animationLoop&&!h&&(a.cloneCount=2,a.cloneOffset=1,"init"!==b&&a.container.find(".clone").remove(),a.container.append(a.slides.first().clone().addClass("clone")).prepend(a.slides.last().clone().addClass("clone")));a.newSlides=d(c.selector,a);g=m?a.count-1-a.currentSlide+a.cloneOffset:a.currentSlide+a.cloneOffset;l&&!h?(a.container.height(200*(a.count+a.cloneCount)+"%").css("position","absolute").width("100%"),setTimeout(function(){a.newSlides.css({display:"block"});a.doMath();a.viewport.height(a.h);
 a.setProps(g*a.h,"init")},"init"===b?100:0)):(a.container.width(200*(a.count+a.cloneCount)+"%"),a.setProps(g*a.computedW,"init"),setTimeout(function(){a.doMath();a.newSlides.css({width:a.computedW,"float":"left",display:"block"});c.smoothHeight&&f.smoothHeight()},"init"===b?100:0))}h||a.slides.removeClass(e+"active-slide").eq(a.currentSlide).addClass(e+"active-slide")};a.doMath=function(){var b=a.slides.first(),d=c.itemMargin,e=c.minItems,f=c.maxItems;a.w=a.width();a.h=b.height();a.boxPadding=b.outerWidth()-
 b.width();h?(a.itemT=c.itemWidth+d,a.minW=e?e*a.itemT:a.w,a.maxW=f?f*a.itemT:a.w,a.itemW=a.minW>a.w?(a.w-d*e)/e:a.maxW<a.w?(a.w-d*f)/f:c.itemWidth>a.w?a.w:c.itemWidth,a.visible=Math.floor(a.w/(a.itemW+d)),a.move=0<c.move&&c.move<a.visible?c.move:a.visible,a.pagingCount=Math.ceil((a.count-a.visible)/a.move+1),a.last=a.pagingCount-1,a.limit=1===a.pagingCount?0:c.itemWidth>a.w?(a.itemW+2*d)*a.count-a.w-d:(a.itemW+d)*a.count-a.w-d):(a.itemW=a.w,a.pagingCount=a.count,a.last=a.count-1);a.computedW=a.itemW-
 a.boxPadding};a.update=function(b,d){a.doMath();h||(b<a.currentSlide?a.currentSlide+=1:b<=a.currentSlide&&0!==b&&(a.currentSlide-=1),a.animatingTo=a.currentSlide);if(c.controlNav&&!a.manualControls)if("add"===d&&!h||a.pagingCount>a.controlNav.length)f.controlNav.update("add");else if("remove"===d&&!h||a.pagingCount<a.controlNav.length)h&&a.currentSlide>a.last&&(a.currentSlide-=1,a.animatingTo-=1),f.controlNav.update("remove",a.last);c.directionNav&&f.directionNav.update()};a.addSlide=function(b,e){var f=
 d(b);a.count+=1;a.last=a.count-1;l&&m?void 0!==e?a.slides.eq(a.count-e).after(f):a.container.prepend(f):void 0!==e?a.slides.eq(e).before(f):a.container.append(f);a.update(e,"add");a.slides=d(c.selector+":not(.clone)",a);a.setup();c.added(a)};a.removeSlide=function(b){var e=isNaN(b)?a.slides.index(d(b)):b;a.count-=1;a.last=a.count-1;isNaN(b)?d(b,a.slides).remove():l&&m?a.slides.eq(a.last).remove():a.slides.eq(b).remove();a.doMath();a.update(e,"remove");a.slides=d(c.selector+":not(.clone)",a);a.setup();
 c.removed(a)};f.init()};d.flexslider.defaults={namespace:"flex-",selector:".slides > li",animation:"fade",easing:"swing",direction:"horizontal",reverse:!1,animationLoop:!0,smoothHeight:!1,startAt:0,slideshow:!0,slideshowSpeed:7E3,animationSpeed:600,initDelay:0,randomize:!1,pauseOnAction:!0,pauseOnHover:!1,useCSS:!0,touch:!0,video:!1,controlNav:!0,directionNav:!0,prevText:"Previous",nextText:"Next",keyboard:!0,multipleKeyboard:!1,mousewheel:!1,pausePlay:!1,pauseText:"Pause",playText:"Play",controlsContainer:"",
 manualControls:"",sync:"",asNavFor:"",itemWidth:0,itemMargin:0,minItems:0,maxItems:0,move:0,start:function(){},before:function(){},after:function(){},end:function(){},added:function(){},removed:function(){}};d.fn.flexslider=function(i){void 0===i&&(i={});if("object"===typeof i)return this.each(function(){var a=d(this),c=a.find(i.selector?i.selector:".slides > li");1===c.length?(c.fadeIn(400),i.start&&i.start(a)):void 0===a.data("flexslider")&&new d.flexslider(this,i)});var k=d(this).data("flexslider");
 switch(i){case "play":k.play();break;case "pause":k.pause();break;case "next":k.flexAnimate(k.getTarget("next"),!0);break;case "prev":case "previous":k.flexAnimate(k.getTarget("prev"),!0);break;default:"number"===typeof i&&k.flexAnimate(i,!0)}}})(jQuery);

// Animate using CSS transitions.
//Fallback to default animate function when CSS transitions are not available
(function($){
        function getPrefix( prop ){
        var prefixes = ['Moz','Webkit','Khtml','O','ms'],
            elem     = document.createElement('div'),
            upper      = prop.charAt(0).toUpperCase() + prop.slice(1),
            pref     = "";
        for(var len = prefixes.length; len--;){
            if((prefixes[len] + upper) in elem.style){
                pref = (prefixes[len]);
            }
        }
        if(prop in elem.style){
            pref = (prop);
        }
        return '-' + pref.toLowerCase() + '-';
        }
        $.fn.extend({
            defaultAnimate: $.fn.animate,
            animateWithCSS: function(props, speed, easing, callback) {
                var options = speed && typeof speed === "object" ? jQuery.extend({}, speed) :{
                        complete: callback || !callback && easing ||
                        jQuery.isFunction( speed ) && speed,
                        duration: speed,
                        easing: callback && easing || easing && !jQuery.isFunction(easing) && easing
                    };
                  return $(this).each(function() {
                    var $this = $(this),
                        altTransition,
                        easing = (options.easing) ? easing : 'ease-in-out',
                        prefix = (getPrefix('transition'));
                        if (Modernizr.csstransitions)
                        {
                              $this.css(prefix + 'transition', 'all ' + speed / 1000 + 's ease-in-out').css('transition', 'all ' + speed / 1000 + 's ease-in-out').css(props);
                              setTimeout(function() {
                                $this.css(prefix + 'transition', altTransition);
                                if ($.isFunction(options.complete)) {
                                     options.complete();
                                }
                              }, speed);
                        }
                        else{
                             $this.defaultAnimate(props, options);
                        }
                })
            }
        });
})(jQuery);