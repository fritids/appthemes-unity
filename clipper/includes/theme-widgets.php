<?php

// Most Commented Coupons Widget
class Widget_Popular_Coupons extends WP_Widget {

	function Widget_Popular_Coupons() {
		$widget_ops = array( 'description' => __( 'Display the most commented on coupons.', APP_TD ), 'classname' => 'widget-custom-coupons' );
		$this->WP_Widget( 'custom-coupons', __( 'Clipper Popular Coupons', APP_TD ), $widget_ops );
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Popular Coupons', APP_TD ) : $instance['title']);

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		if (strpos($before_widget, 'customclass') !== false)
			$before_widget = str_replace('customclass', 'cut', $before_widget);
		else
			$before_widget = str_replace('customclass', '', $before_widget);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;		

		$popular_posts = new WP_Query( array( 'post_type' => APP_POST_TYPE, 'posts_per_page' => $number, 'orderby' => 'comment_count', 'order' => 'DESC' ) );
		$result = '';

		if ($popular_posts->have_posts()) {
			$result .= '<div class="coupon-ticker"><ul class="list">';
			while ($popular_posts->have_posts()) {
					$popular_posts->the_post();
					$result .= '<li><a href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a> - ' . get_comments_number('0', '1', '%') . '&nbsp;' . __( 'comments', APP_TD ) . '</li>';
			}
			$result .= '</ul></div>';
		}

		wp_reset_query();

		echo $result;

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr($instance['title']);
		$number = absint($instance['number']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of coupons to show:', APP_TD ); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php
	}
}


// Most Popular Stores Widget
class Widget_Popular_Stores extends WP_Widget {

	function Widget_Popular_Stores() {
		$widget_ops = array( 'description' => __( 'Display the most popular stores.', APP_TD ), 'classname' => 'widget-custom-stores' );
		$this->WP_Widget( 'custom-stores', __( 'Clipper Popular Stores', APP_TD ), $widget_ops );
	}

	function widget($args, $instance) {
		global $wpdb;
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Popular Stores', APP_TD ) : $instance['title']);

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		if (strpos($before_widget, 'customclass') !== false)
			$before_widget = str_replace('customclass', 'cut', $before_widget);
		else
			$before_widget = str_replace('customclass', '', $before_widget);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

			echo '<div class="store-widget"><ul class="list">';

			$hidden_stores = clpr_hidden_stores();
			$tax_array = get_terms( APP_TAX_STORE, array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => 1, 'show_count' => 1, 'pad_counts' => 0, 'app_pad_counts' => 1, 'exclude' => $hidden_stores ) );
			$i = 0;

			if ($tax_array && is_array($tax_array)):
				foreach ( $tax_array as $tax_val ) {
					if ( $i >= $number )
						continue;
					$link = get_term_link($tax_val, APP_TAX_STORE);
					echo '<li><a class="tax-link" href="' . $link . '">' . $tax_val->name . '</a> - ' . $tax_val->count . '&nbsp;' . __( 'coupons', APP_TD ) . '</li>';
					$i++;
				}
			endif;

			echo '</ul></div>';	

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr($instance['title']);
		$number = absint($instance['number']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of stores to show:', APP_TD ); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php
	}
}


// Coupon Catetories Widget
class Widget_Coupon_Categories extends WP_Widget {

	function Widget_Coupon_Categories() {
		$widget_ops = array( 'description' => __( 'Display the coupon categories.', APP_TD ), 'classname' => 'widget-coupon-cats' );
		$this->WP_Widget( 'coupon-cats', __( 'Clipper Coupon Categories', APP_TD ), $widget_ops );
	}

	function widget($args, $instance) {
		global $wpdb;
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Coupon Categories', APP_TD ) : $instance['title']);

		if (strpos($before_widget, 'customclass') !== false)
			$before_widget = str_replace('customclass', 'cut', $before_widget);
		else
			$before_widget = str_replace('customclass', '', $before_widget);

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

			$tax_name = APP_TAX_CAT;
			echo '<div class="coupon-cats-widget"><ul class="list">';

			wp_list_categories("orderby=name&order=asc&hierarchical=1&show_count=1&pad_counts=0&app_pad_counts=1&use_desc_for_title=1&hide_empty=0&depth=1&number=&title_li=&taxonomy=$tax_name");

			echo '</ul></div>';	

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr($instance['title']);
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<?php
	}
}


// Email Subscription Widget
class Widget_Clipper_Subscribe extends WP_Widget {

	function Widget_Clipper_Subscribe() {
		$widget_ops = array( 'description' => __( 'Display The Coupons in Your Inbox Box', APP_TD ), 'classname' => 'widget-newsletter-subscription' );
		$control_ops = array( 'width' => 500, 'height' => 350 );
		$this->WP_Widget( 'newsletter-subscribe', __( 'Coupons in Your Inbox!', APP_TD ), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Coupons in Your Inbox!', APP_TD ) : $instance['title']);
		$text = (empty($instance['text']) ? __( 'Receive coupons by email, subscribe now!', APP_TD ) : $instance['text']);
		$action = (empty($instance['action']) ? '#' : $instance['action']);

?>
		<div class="sidebox subscribe-box">

			<div class="sidebox-content">
				<div class="sidebox-heading"><h2><?php echo $title; ?></h2></div>
				<div class="subscribe-holder">

					<div class="text-box">
					<p><?php echo $text; ?></p></div>

					<form action="<?php echo $action; ?>" class="subscribe-form">
						<fieldset>
							<div class="row">
								<div class="text"><input type="text" class="text" value="<?php _e( 'Enter Email Address', APP_TD ); ?>" onfocus="clearAndColor(this)" onblur="reText(this)"/></div>
							</div>
							<div class="row">
								<button name="submit" value="Submit" id="submit" title="<?php _e( 'Subscribe', APP_TD ); ?>" type="submit" class="btn-submit"><span><?php _e( 'Subscribe', APP_TD ); ?></span></button>
							</div>
						</fieldset>

						<input type="hidden" name="<?php echo $instance['hname1']; ?>" value="<?php echo $instance['hvalue1']; ?>" />
						<input type="hidden" name="<?php echo $instance['hname2']; ?>" value="<?php echo $instance['hvalue2']; ?>" />
						<input type="hidden" name="<?php echo $instance['hname3']; ?>" value="<?php echo $instance['hvalue3']; ?>" />
						<input type="hidden" name="<?php echo $instance['hname4']; ?>" value="<?php echo $instance['hvalue4']; ?>" />
						<input type="hidden" name="<?php echo $instance['hname5']; ?>" value="<?php echo $instance['hvalue5']; ?>" />
						<input type="hidden" name="<?php echo $instance['hname6']; ?>" value="<?php echo $instance['hvalue6']; ?>" />

					</form>
				</div>
			</div>
			<br clear="all" />
		</div>

<?php

	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['action'] = strip_tags($new_instance['action']);
		$instance['hname1'] = strip_tags($new_instance['hname1']);
		$instance['hvalue1'] = strip_tags($new_instance['hvalue1']);
		$instance['hname2'] = strip_tags($new_instance['hname2']);
		$instance['hvalue2'] = strip_tags($new_instance['hvalue2']);
		$instance['hname3'] = strip_tags($new_instance['hname3']);
		$instance['hvalue3'] = strip_tags($new_instance['hvalue3']);
		$instance['hname4'] = strip_tags($new_instance['hname4']);
		$instance['hvalue4'] = strip_tags($new_instance['hvalue4']);
		$instance['hname5'] = strip_tags($new_instance['hname5']);
		$instance['hvalue5'] = strip_tags($new_instance['hvalue5']);
		$instance['hname6'] = strip_tags($new_instance['hname6']);
		$instance['hvalue6'] = strip_tags($new_instance['hvalue6']);

		return $instance;
	}

	function form($instance) {

		$defaults = array( 
			'title' => __( 'Coupons in Your Inbox!', APP_TD ),
			'text' => __( 'Receive coupons by email, subscribe now!', APP_TD ),
			'action' => '#',
			'hname1' => '',
			'hvalue1' => '',
			'hname2' => '',
			'hvalue2' => '',
			'hname3' => '',
			'hvalue3' => '',
			'hname4' => '',
			'hvalue4' => '',
			'hname5' => '',
			'hvalue5' => '',
			'hname6' => '',
			'hvalue6' => ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = esc_attr($instance['title']);
		$text = esc_attr($instance['text']);
		$action = esc_attr($instance['action']);
		$hname1 = esc_attr($instance['hname1']);
		$hvalue1 = esc_attr($instance['hvalue1']);
		$hname2 = esc_attr($instance['hname2']);
		$hvalue2 = esc_attr($instance['hvalue2']);
		$hname3 = esc_attr($instance['hname3']);
		$hvalue3 = esc_attr($instance['hvalue3']);
		$hname4 = esc_attr($instance['hname4']);
		$hvalue4 = esc_attr($instance['hvalue4']);
		$hname5 = esc_attr($instance['hname5']);
		$hvalue5 = esc_attr($instance['hvalue5']);
		$hname6 = esc_attr($instance['hname6']);
		$hvalue6 = esc_attr($instance['hvalue6']);
?>

			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

			<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e( 'Text:', APP_TD ); ?> <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" /></label></p>

			<p><label for="<?php echo $this->get_field_id('action'); ?>"><?php _e( 'Form Post Action:', APP_TD ); ?> <input class="widefat" id="<?php echo $this->get_field_id('action'); ?>" name="<?php echo $this->get_field_name('action'); ?>" type="action" value="<?php echo $action; ?>" /></label>
			<small><?php _e( 'Enter the url where the email subscribe form should post to.<br /> i.e. http://www.aweber.com/', APP_TD ); ?></small>
			</p>

			<p style="margin-bottom:-1px;">
			<label><?php _e( 'Advanced Options:', APP_TD ); ?></label>
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 1:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname1'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname1'); ?>" name="<?php echo $this->get_field_name('hname1'); ?>" value="<?php echo $instance['hname1']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue1'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue1'); ?>" name="<?php echo $this->get_field_name('hvalue1'); ?>" value="<?php echo $instance['hvalue1']; ?>" style="width:175px;" />
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 2:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname2'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname2'); ?>" name="<?php echo $this->get_field_name('hname2'); ?>" value="<?php echo $instance['hname2']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue2'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue2'); ?>" name="<?php echo $this->get_field_name('hvalue2'); ?>" value="<?php echo $instance['hvalue2']; ?>" style="width:175px;" />
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 3:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname3'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname3'); ?>" name="<?php echo $this->get_field_name('hname3'); ?>" value="<?php echo $instance['hname3']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue3'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue3'); ?>" name="<?php echo $this->get_field_name('hvalue3'); ?>" value="<?php echo $instance['hvalue3']; ?>" style="width:175px;" />
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 4:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname4'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname4'); ?>" name="<?php echo $this->get_field_name('hname4'); ?>" value="<?php echo $instance['hname4']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue4'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue4'); ?>" name="<?php echo $this->get_field_name('hvalue4'); ?>" value="<?php echo $instance['hvalue4']; ?>" style="width:175px;" />
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 5:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname5'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname5'); ?>" name="<?php echo $this->get_field_name('hname5'); ?>" value="<?php echo $instance['hname5']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue5'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue5'); ?>" name="<?php echo $this->get_field_name('hvalue5'); ?>" value="<?php echo $instance['hvalue5']; ?>" style="width:175px;" />
			</p>

			<p class="email-hidden-widget"><label><?php _e( 'Hidden Field 6:', APP_TD ); ?></label><br />
			<label for="<?php echo $this->get_field_id('hname6'); ?>"><?php _e( 'Name:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hname6'); ?>" name="<?php echo $this->get_field_name('hname6'); ?>" value="<?php echo $instance['hname6']; ?>" style="width:175px;" />&nbsp;&nbsp;&nbsp;
			<label for="<?php echo $this->get_field_id('hvalue6'); ?>"><?php _e( 'Value:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('hvalue6'); ?>" name="<?php echo $this->get_field_name('hvalue6'); ?>" value="<?php echo $instance['hvalue6']; ?>" style="width:175px;" />
			</p>

		<?php
	}
}

// Coupon Categories
class Widget_Tabbed_Blog extends WP_Widget {

	function Widget_Tabbed_Blog() {
		$widget_ops = array( 'description' => __( 'Display a tabbed widget for blog posts.', APP_TD ), 'classname' => 'widget-tabbed-blog' );
		$this->WP_Widget( 'tabbed-blog', __( 'Clipper Tabbed Blog Widget', APP_TD ), $widget_ops );
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);

	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	jQuery(document).ready(function() {
		( function($) {
			$.fn.at_switcher = function(options) {
				var defaults = {
					slides: '>div',
					activeClass: 'active',
					linksNav: '',
					findParent: true, //use parent elements in defining lengths
					lengthElement: 'li', //parent element, used only if findParent is set to true
					useArrows: false,
					arrowLeft: 'prevlink',
					arrowRight: 'nextlink',
					auto: false,
					autoSpeed: 5000
				};

				var options = $.extend(defaults, options);

				return this.each( function() {

					var slidesContainer = jQuery(this);
					slidesContainer.find(options.slides).hide().end().find(options.slides).filter(':first').css('display','block');

					var linkSwitcher = jQuery(options.linksNav);

					linkSwitcher.click( function() {
						var targetElement;

						if (options.findParent)
							targetElement = jQuery(this).parent();
						else
							targetElement = jQuery(this);

						if (targetElement.hasClass('active')) return false;

						targetElement.siblings().removeClass('active').end().addClass('active');

						var ordernum = targetElement.prevAll(options.lengthElement).length;

						slidesContainer.find(options.slides).filter(':visible').hide().end().end().find(options.slides).filter(':eq('+ordernum+')').stop().fadeIn(700);
						return false;
					});

					jQuery('#'+options.arrowRight+', #'+options.arrowLeft).click( function() {

						var slideActive = slidesContainer.find(options.slides).filter(":visible"),
						nextSlide = slideActive.next(),
						prevSlide = slideActive.prev();

						if (jQuery(this).attr("id") == options.arrowRight) {
							if (nextSlide.length) {
								var ordernum = nextSlide.prevAll().length;
							} else {
								var ordernum = 0;
							}
						};

						if (jQuery(this).attr("id") == options.arrowLeft) {
							if (prevSlide.length) {
								var ordernum = prevSlide.prevAll().length;
							} else {
								var ordernum = slidesContainer.find(options.slides).length-1;
							}
						};

						slidesContainer.find(options.slides).filter(':visible').hide().end().end().find(options.slides).filter(':eq('+ordernum+')').stop().fadeIn(700);
						return false;
					});

					if (options.auto) {
						interval = setInterval( function() {
							var slideActive = slidesContainer.find(options.slides).filter(":visible"),
							nextSlide = slideActive.next();

							if (nextSlide.length) {
								var ordernum = nextSlide.prevAll().length;
							} else {
								var ordernum = 0;
							}

							linkSwitcher.filter(':eq('+ordernum+')').trigger("click");
						}, options.autoSpeed);
					};

				});

			}
		})(jQuery);

		var $all_tabs = jQuery('#blog-tabs');

		if ($all_tabs.length) {
			$all_tabs.at_switcher({
				linksNav: 'ul#blog_tab_controls li a'
			});

		};
	});
	//-->!]]>
	</script>

		<div class="block">

			<div class="block-t">&nbsp;</div>

			<div class="block-c">

				<div class="block-holder" id="blog-tabs">

				<ul id="blog_tab_controls" class="tabset">
					<li class="active"><a href="#" onclick="return false;"><span><?php _e( 'Recent', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
					<li><a href="#" onclick="return false;"><span><?php _e( 'Popular', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
					<li><a href="#" onclick="return false;"><span><?php _e( 'Comments', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
				</ul>

					<div class="tab-content">

						<ul class="list">

							<?php query_posts("showposts=5"); ?>
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

								<li>

									<?php if (has_post_thumbnail()) : ?>

									<div class="image">

										<div class="holder">

											<div class="frame">

												<?php the_post_thumbnail('thumb-small'); ?>

											</div>

										</div>

									</div>

									<?php endif; ?>

									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<p><?php the_time('F j, Y'); ?></p>

								</li>

							<?php endwhile; endif; wp_reset_query(); ?>

						</ul>

					</div>

					<div class="tab-content">

					<ul class="list" >

						<?php global $wpdb;
						$result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , 5");
						foreach ($result as $post) {
							setup_postdata($post);
							$postid = $post->ID;
							$title = $post->post_title;
							$commentcount = $post->comment_count;
							if ($commentcount != 0) { ?>
								<?php query_posts("p=$postid"); ?>
								<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
								<li>

									<?php if (has_post_thumbnail()) : ?>

									<div class="image">

										<div class="holder">

											<div class="frame">

												<?php the_post_thumbnail('thumb-small'); ?>

											</div>

										</div>

									</div>

									<?php endif; ?>

									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<p><?php the_time('F j, Y'); ?></p>

								</li>

								<?php endwhile; endif; wp_reset_query(); ?>

							<?php };
						}; ?>

					</ul>

					</div>

					<div class="tab-content">

						<?php
							global $wpdb;
							$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type,comment_author_url, SUBSTRING(comment_content,1,30) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT 5";

							$comments = $wpdb->get_results($sql);
							$output = $pre_HTML;
							$output .= "\n<ul class=\"list\">";
							foreach ($comments as $comment) {
								$output .= "\n<li>".strip_tags($comment->comment_author) ." on " . "<a href=\"" . get_permalink($comment->ID)."#comment-" . $comment->comment_ID . "\" title=\"on ".$comment->post_title . "\">" . strip_tags($comment->post_title)."</a></li>";
							}
							$output .= "\n</ul>";
							$output .= $post_HTML;
							echo $output;
						?>
					</div>

				</div>

			</div>

			<div class="block-b">&nbsp;</div>

		</div>

		<?php

		// echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr($instance['title']);
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<?php
	}
}

// add_action('widgets_init', create_function('', 'return register_widget("Widget_Tabbed_Blog");'));



// twitter sidebar widget
class AppThemes_Widget_Twitter extends WP_Widget {

	function AppThemes_Widget_Twitter() {
		$widget_ops = array( 'description' => __( 'This places a real-time Twitter feed in your sidebar.', APP_TD ) );
		$this->WP_Widget( false, __( 'Clipper Real-Time Twitter Feed', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$tid = $instance['tid'];
		$api_key = $instance['api_key'];
		$keywords = strip_tags($instance['keywords']);
		$type = $instance['type'];
		$tcount = $instance['tcount'];
		$paging = $instance['paging'];
		$trefresh = $instance['trefresh'];
		$lang = $instance['lang'];
		$follow = isset($instance['follow']) ? $instance['follow'] : false;
		$connect = isset($instance['connect']) ? $instance['connect'] : false;

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;
	?>

		<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/includes/js/jtweetsanywhere/jtweetsanywhere.min.js'></script>
		<?php if($api_key) : ?>
			<script type="text/javascript" src="http://platform.twitter.com/anywhere.js?id=<?php echo $api_key; ?>&v=1"></script>
		<?php endif; ?>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/includes/js/jtweetsanywhere/jtweetsanywhere.css" />

		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#tweetFeed').jTweetsAnywhere({
					//searchParams: ['geocode=48.856667,2.350833,30km'],
				<?php if($type == 'username') { ?>
						username: '<?php echo $tid; ?>',
				<?php } else { ?>
						searchParams: ['q=<?php echo $keywords; ?>', 'lang=<?php echo $lang; ?>'],
				<?php } ?>
					count: <?php echo $tcount; ?>,
				<?php if($follow) echo "showFollowButton: true,"; ?>
				<?php if($connect) echo "showConnectButton: true,"; ?>
					showTweetFeed: {
						expandHovercards: true,
						showSource: true,
						paging: {
							mode: '<?php echo $paging; ?>'
						},
						showTimestamp: {
							refreshInterval: 30
						},
						autorefresh: {
							mode: '<?php echo $trefresh; ?>',
							interval: 20
						}

					},
					onDataRequestHandler: function(stats, options) {
						if (stats.dataRequestCount < 11) {
							return true;
						}
						else {
							stopAutorefresh(options);
							// alert("To avoid struggling with Twitter's rate limit, we stop loading data after 10 API calls.");
						}
					}


				});

			});
		</script>

		<div id="tweetFeed"></div>
		<div class="pad5"></div>

	<?php

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tid'] = strip_tags($new_instance['tid']);
		$instance['api_key'] = strip_tags($new_instance['api_key']);
		$instance['keywords'] = strip_tags($new_instance['keywords']);
		$instance['type'] = $new_instance['type'];
		$instance['trefresh'] = $new_instance['trefresh'];
		$instance['tcount'] = strip_tags($new_instance['tcount']);
		$instance['paging'] = $new_instance['paging'];
		$instance['lang'] = strip_tags($new_instance['lang']);
		$instance['follow'] = $new_instance['follow'];
		$instance['connect'] = $new_instance['connect'];		

		return $instance;
	}

	function form($instance) {

		$defaults = array( 
			'title' => 'Twitter Updates',
			'tid' => 'appthemes',
			'api_key' => 'ZSO1guB57M6u0lm4cwqA',
			'keywords' => 'wordpress', 
			'tcount' => '5',
			'type' => 'keyword',
			'paging' => 'prev-next',
			'trefresh' => 'trigger-insert',
			'lang' => 'en',
			'follow' => '',
			'connect' => ''
		);

		$instance = wp_parse_args((array) $instance, $defaults);
	?>

		<p>
			<label><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Twitter Username:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tid'); ?>" name="<?php echo $this->get_field_name('tid'); ?>" value="<?php echo $instance['tid']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Twitter API Key:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" value="<?php echo $instance['api_key']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Keyword Tweets:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('keywords'); ?>" name="<?php echo $this->get_field_name('keywords'); ?>" value="<?php echo $instance['keywords']; ?>" />
		</p>

		<p>
			<label><?php _e( 'Display Type:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" >
				<option value="username" <?php if ('username' == $instance['type']) echo 'selected="selected"'; ?>><?php _e( 'Show Username Tweets', APP_TD ); ?></option>
				<option value="keywords" <?php if ('keywords' == $instance['type']) echo 'selected="selected"'; ?>><?php _e( 'Show Keyword Tweets', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<label><?php _e( 'Refresh Mode:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('trefresh'); ?>" name="<?php echo $this->get_field_name('trefresh'); ?>" >
				<option value="none" <?php if ('none' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e( 'None', APP_TD ); ?></option>
				<option value="auto-insert" <?php if ('auto-insert' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e( 'Real-Time Updates', APP_TD ); ?></option>
				<option value="trigger-insert" <?php if ('trigger-insert' == $instance['trefresh']) echo 'selected="selected"'; ?>><?php _e( 'Click Button Updates', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<label><?php _e( 'Paging Style:', APP_TD ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('paging'); ?>" name="<?php echo $this->get_field_name('paging'); ?>" >
				<option value="more" <?php if ('more' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e( 'More Button', APP_TD ); ?></option>
				<option value="prev-next" <?php if ('prev-next' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e( 'Next &amp; Previous Buttons', APP_TD ); ?></option>
				<option value="endless-scroll" <?php if ('endless-scroll' == $instance['paging']) echo 'selected="selected"'; ?>><?php _e( 'Endless Scrolling', APP_TD ); ?></option>
			</select>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('tcount'); ?>" name="<?php echo $this->get_field_name('tcount'); ?>" value="<?php echo $instance['tcount']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('tcount'); ?>"><?php _e( 'Tweets Shown', APP_TD ); ?></label>
		</p>

		<p>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" value="<?php echo $instance['lang']; ?>" style="width:30px;" />
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e( 'Default Language', APP_TD ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['follow'], 'on'); ?> id="<?php echo $this->get_field_id('follow'); ?>" name="<?php echo $this->get_field_name('follow'); ?>" />
			<label for="<?php echo $this->get_field_id('follow'); ?>"><?php _e( 'Show Follow Button', APP_TD ); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php checked($instance['connect'], 'on'); ?> id="<?php echo $this->get_field_id('connect'); ?>" name="<?php echo $this->get_field_name('connect'); ?>" />
			<label for="<?php echo $this->get_field_id('connect'); ?>"><?php _e( 'Show Connect Button', APP_TD ); ?></label>
		</p>


	<?php
	}
}

// facebook like box sidebar widget
class AppThemes_Widget_Facebook extends WP_Widget {

	function AppThemes_Widget_Facebook() {
		$widget_ops = array( 'description' => __( 'This places a Facebook page Like Box in your sidebar to attract and gain Likes from visitors.', APP_TD ) );
		$this->WP_Widget( false, __( 'Clipper Facebook Like Box', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$fid = $instance['fid'];
		$connections = $instance['connections'];
		$width = $instance['width'];
		$height = $instance['height'];

		echo $before_widget;

		if ($title) echo $before_title . $title . $after_title;

	?>
		<div class="pad5"></div>
		<iframe src="http://www.facebook.com/plugins/likebox.php?id=<?php echo $fid; ?>&amp;connections=<?php echo $connections; ?>&amp;stream=false&amp;header=true&amp;width=<?php echo $width; ?>&amp;height=<?php echo $height; ?>" scrolling="no" frameborder="0" style="border:none; background-color: transparent; overflow:hidden; width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;"></iframe>
		<div class="pad5"></div>
	<?php

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fid'] = strip_tags( $new_instance['fid'] );
		$instance['connections'] = strip_tags($new_instance['connections']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);

		return $instance;
	}

	function form($instance) {

		$defaults = array( 'title' => __( 'Facebook Friends', APP_TD ), 'fid' => '137589686255438', 'connections' => '10', 'width' => '268', 'height' => '290' );
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('fid'); ?>"><?php _e( 'Facebook ID:', APP_TD ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('fid'); ?>" name="<?php echo $this->get_field_name('fid'); ?>" value="<?php echo $instance['fid']; ?>" />
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('connections'); ?>" name="<?php echo $this->get_field_name('connections'); ?>" value="<?php echo $instance['connections']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('connections'); ?>"><?php _e( 'Connections', APP_TD ); ?></label>			
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Width', APP_TD ); ?></label>
		</p>

		<p style="text-align:left;">
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" style="width:50px;" />
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e( 'Height', APP_TD ); ?></label>
		</p>

	<?php
	}
}


// ad tags and categories cloud widget
class Widget_Coupons_Tag_Cloud extends WP_Widget {

	function Widget_Coupons_Tag_Cloud() {
		$widget_ops = array( 'description' => __( 'Your most used coupon tags in cloud format', APP_TD ) );
		$this->WP_Widget( 'coupon_tag_cloud', __( 'Clipper Coupon Tag Cloud', APP_TD ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( APP_POST_TYPE == $current_taxonomy ) {
				$title = __( 'Coupon Tags', APP_TD );
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tagcloud">';
		wp_tag_cloud( apply_filters('widget_tag_cloud_args', array('taxonomy' => $current_taxonomy) ) );
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
		return $instance;
	}

	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy($instance);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e( 'Taxonomy:', APP_TD ); ?></label>

			<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
			<?php foreach ( get_object_taxonomies(APP_POST_TYPE) as $taxonomy ) :
					$tax = get_taxonomy($taxonomy);
					if ( !$tax->show_tagcloud || empty($tax->labels->name) )
						continue;
			?>
				<option value="<?php echo esc_attr($taxonomy); ?>" <?php selected($taxonomy, $current_taxonomy); ?>><?php echo $tax->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
		</p>
	<?php
	}

	function _get_current_taxonomy($instance) {
		if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
			return $instance['taxonomy'];

		return 'post_tag';
	}
}


// footer contact form widget
class WP_Widget_Contact_Footer extends WP_Widget {

	function WP_Widget_Contact_Footer() {
		$widget_ops = array( 'classname' => 'widget_contact_form', 'description' => __( 'A simple contact form designed for the footer.', APP_TD ) );
		$control_ops = array( 'width' => 400, 'height' => 350 );
		$this->WP_Widget( 'contact_form', __( 'Footer Contact Form', APP_TD ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'Contact Form', APP_TD ) : $instance['title'], $instance, $this->id_base);
		$action = (empty($instance['action']) ? '#' : $instance['action']);
		$class = empty($instance['class']) ? '' : $instance['class'];
		if ($class)
			$before_widget = str_replace('customclass', $class, $before_widget);
		else
			$before_widget = str_replace('customclass', 'contact', $before_widget);

		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>				
			<form class="contact-form" action="<?php echo $instance['action']; ?>" method="post">
				<fieldset>
					<input type="text" name="full_name" value="<?php _e( 'Your name', APP_TD ); ?>" class="text">
					<input type="text" name="email_address" value="<?php _e( 'Your email address', APP_TD ); ?>" class="text">
					<input type="hidden" name="submitted" value="submitted" class="text">
					<textarea rows="10" cols="30" class="text-area" name="comments"></textarea>
					<div class="row">
						<button onsubmit="this.where.reset();return false;" name="submit" value="Submit" id="submit" title="<?php _e( 'Send', APP_TD ); ?>" type="submit" class="btn-submit"><span style="margin-top: -1px;"><?php _e( 'Send', APP_TD ); ?></span></button>
					</div>
				</fieldset>
			</form>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['class'] = strip_tags($new_instance['class']);
		$instance['action'] = strip_tags($new_instance['action']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'class' => '', 'action' => '' ) );
		$title = strip_tags($instance['title']);
		$class = strip_tags($instance['class']);
		$action = strip_tags($instance['action']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('class'); ?>"><?php _e( 'Class:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo esc_attr($class); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('action'); ?>"><?php _e( 'Post Action:', APP_TD ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('action'); ?>" name="<?php echo $this->get_field_name('action'); ?>" type="text" value="<?php echo esc_attr($action); ?>" /></p>


		<?php
	}
}


// share coupon button widget
class Widget_Share_Coupon extends WP_Widget {

	function Widget_Share_Coupon() {
		$widget_ops = array( 'description' => __( 'Share a coupon button for use in sidebar', APP_TD ) );
		$this->WP_Widget('share_coupon_button', __( 'Clipper Share Coupon Button', APP_TD ), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Share a Coupon', APP_TD ) : $instance['title'] );
		$description = apply_filters('widget_title', $instance['description'] );

?>
		<a href="<?php echo clpr_get_submit_coupon_url(); ?>" class="share-box">
			<img src="<?php bloginfo('template_url'); ?>/images/share_icon.png" title="" alt="" />
			<span class="lgheading"><?php echo $title; ?></span>
			<span class="smheading"><?php echo $description; ?></span>
		</a>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => __( 'Share a Coupon', APP_TD ), 'description' => __( 'Spread the Savings with Everyone!', APP_TD ) );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags($instance['title']);
		$description = strip_tags($instance['description']);
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e( 'Description:', APP_TD ); ?><input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" type="text" value="<?php echo esc_attr($description); ?>" /></label></p>
<?php
	}
}


// Most Searched Phrases Widget
class Widget_Popular_Searches extends WP_Widget {

	function __construct() {
		$widget_ops = array('description' => __( 'Display the most searched phrases.', APP_TD ), 'classname' => 'widget-coupon-searches' );
		parent::__construct('popular-searches', __( 'Clipper Popular Searches', APP_TD ), $widget_ops);
	}

	function widget($args, $instance) {
		global $wpdb;
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __( 'Popular Searches', APP_TD ) : $instance['title']);

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		$sql = "SELECT terms, SUM(count) as total_count FROM $wpdb->clpr_search_total WHERE last_hits > 0 GROUP BY terms ORDER BY total_count DESC LIMIT %d";
		$popular_searches = $wpdb->get_results( $wpdb->prepare( $sql, $number ) );
		$result = '';

		if ( $popular_searches ) {
			$result .= '<div class="coupon-searches-widget"><ul class="list">';
			foreach ($popular_searches as $searched) {
				$url = add_query_arg( array( 's' => urlencode($searched->terms), 'Search' => __( 'Search', APP_TD ) ), home_url('/') );
				$count = sprintf( _n( '%s time', '%s times', $searched->total_count, APP_TD ), $searched->total_count );
				$result .= '<li><a href="'. $url .'">'. $searched->terms .'</a> - '. $count. '</li>';
			}
			$result .= '</ul></div>';
		}

		echo $result;

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];

		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 10 ) );
		$title = esc_attr($instance['title']);
		$number = absint($instance['number']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', APP_TD ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of phrases to show:', APP_TD ); ?></label>
				<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
		<?php
	}
}


// register the custom sidebar widgets
function clpr_widgets_init() {
	if ( !is_blog_installed() )
		return;

	register_widget('Widget_Clipper_Subscribe');
	//register_widget('AppThemes_Widget_125_Ads');
	//register_widget('AppThemes_Widget_Blog_Posts');
	//register_widget('CP_Widget_Search');
	//register_widget('CP_Widget_Top_Ads_Today');
	//register_widget('CP_Widget_Top_Ads_Overall');
	//register_widget('WP_Widget_Contact_Footer');
	register_widget('AppThemes_Widget_Twitter');
	register_widget('AppThemes_Widget_Facebook');
	register_widget('Widget_Coupons_Tag_Cloud');
	register_widget('Widget_Popular_Stores');
	register_widget('Widget_Popular_Coupons');
	register_widget('Widget_Coupon_Categories');
	register_widget('Widget_Share_Coupon');
	register_widget('Widget_Popular_Searches');

	do_action('widgets_init');
}

add_action( 'init', 'clpr_widgets_init', 1 );


// remove some of the default sidebar widgets
// uncomment any widgets you wish to unregister
function clpr_unregister_widgets() {
	//unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	//unregister_widget('WP_Widget_Archives');
	//unregister_widget('WP_Widget_Links');
	//unregister_widget('WP_Widget_Categories');
	//unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Search');
	//unregister_widget('WP_Widget_Tag_Cloud');
}

add_action( 'widgets_init', 'clpr_unregister_widgets' );



?>
