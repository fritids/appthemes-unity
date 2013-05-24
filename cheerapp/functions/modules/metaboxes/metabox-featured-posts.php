<div class="royal-metabox featured-metabox">

	<div class="royal-meta-section">
		<label for="_royal_meta[tagline]">
			<?php _e( 'Slide tagline', 'cheerapp' ); ?>
			<small><?php _e( 'The tagline will appear above slide title, only if left- or right-oriented layout is selected below', 'cheerapp' ); ?></small>
		</label>
		<input type="text" id="_royal_meta[tagline]" name="_royal_meta[tagline]" value="<?php if( !empty( $meta['tagline'] ) ) echo $meta['tagline']; ?>" />
	</div>

	<div class="royal-meta-section">
		<div>
			<label><?php _e( 'Slide layout', 'cheerapp' ); ?></label>
			
			<input type="radio" class="slide-layout" id="text-left" name="_royal_meta[layout]" value="left"<?php if( !$meta['layout'] || $meta['layout'] == 'left' ) { ?> checked="checked"<?php } ?> />
			<label for="text-left"> <?php _e( 'Text on the left side', 'cheerapp' ); ?> </label>
			
			<input type="radio" class="slide-layout" id="text-right" name="_royal_meta[layout]" value="right"<?php if( $meta['layout'] == 'right' ) { ?> checked="checked"<?php } ?> />
			<label for="text-right"> <?php _e( 'Text on the right side', 'cheerapp' ); ?> </label>
			
			<input type="radio" class="slide-layout" id="centered" name="_royal_meta[layout]" value="center"<?php if( $meta['layout'] == 'center' ) { ?> checked="checked"<?php } ?> />
			<label for="centered"> <?php _e( 'Centered', 'cheerapp' ); ?> </label>
		</div>
	</div>
	
	<div class="royal-meta-section">
		<div class="video-radios">
			<label><?php _e( 'Video slide', 'cheerapp' ); ?></label>
				
				<div>
					<input type="radio" name="_royal_meta[use_video]" id="zero" value="0" <?php if( !$meta['use_video'] || $meta['use_video'] == '0' ) { ?>checked="checked"<?php } ?> />
					<label for="zero"> <?php _e( 'Do not show video', 'cheerapp' ); ?> </label>
				</div>
				<div>
					<input type="radio" name="_royal_meta[use_video]" id="lightbox" value="lightbox" <?php if( $meta['use_video'] == 'lightbox' ) { ?>checked="checked"<?php } ?> />
					<label for="lightbox"> <?php _e( 'Show video in lightbox after clicking on featured image', 'cheerapp' ); ?> </label>
				</div>
				
		</div>
		<br />
		<p>
			<label for="_royal_meta[video]"><?php _e( 'Video URL', 'cheerapp' ); ?></label>
			<textarea id="_royal_meta[video]" name="_royal_meta[video]"><?php if( !empty( $meta['video'] ) ) echo $meta['video']; ?></textarea>
			<span class="description"><?php _e( 'Paste YouTube / Vimeo URL to show video in a lightbox (only YouTube and Vimeo are supported in lightbox view)', 'cheerapp' ); ?></span>
		</p>
	</div>
 
</div>