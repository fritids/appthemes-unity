<div class="royal-metabox featured-metabox">

	<div class="royal-meta-section">
		<div class="small-input">
			<label for="_royal_meta[price]">
				<?php _e( 'Price', 'cheerapp' ); ?>
			</label>
			<input type="text" id="_royal_meta[price]" name="_royal_meta[price]" value="<?php if( !empty( $meta['price'] ) ) echo $meta['price']; ?>" />
		</div>
		<div class="small-input">
			<label for="_royal_meta[pricing_info]">
				<?php _e( 'Pricing info', 'cheerapp' ); ?>
			</label>
			<input type="text" id="_royal_meta[pricing_info]" name="_royal_meta[pricing_info]" value="<?php if( !empty( $meta['pricing_info'] ) ) echo $meta['pricing_info']; ?>" />
		</div>
		<div class="clear"></div>
	</div>

	<div class="royal-meta-section">
		
		<div class="small-input">
			<label for="_royal_meta[url]">
				<?php _e( 'Button URL', 'cheerapp' ); ?>
			</label>
			<input type="text" id="_royal_meta[url]" name="_royal_meta[url]" value="<?php if( !empty( $meta['url'] ) ) echo $meta['url']; ?>" />
		</div>
		<div class="small-input">
			<label for="_royal_meta[button_text]">
				<?php _e( 'Button text', 'cheerapp' ); ?>
			</label>
			<input type="text" id="_royal_meta[button_text]" name="_royal_meta[button_text]" value="<?php if( !empty( $meta['button_text'] ) ) echo $meta['button_text']; ?>" />
		</div>
		<div class="clear"></div>
	</div>

	<?php
	$features = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' );
	
	foreach( $features as $f ) {
	?>
	
		<?php $pf = !empty( $meta['plan_features'][$f] ) ? $meta['plan_features'][$f] : null; ?>
	
		<div class="royal-meta-section">
	
			<p><strong><?php printf( __( 'Feature %d', 'cheerapp' ), $f ); ?></strong></p>
		
			<div class="small-input">
				<label for="_royal_meta[plan_features][<?php echo $f; ?>][value]">
					<?php _e( 'Value', 'cheerapp' ); ?>
				</label>
				<input type="text" id="_royal_meta[plan_features][<?php echo $f; ?>][value]" name="_royal_meta[plan_features][<?php echo $f; ?>][value]" value="<?php if( !empty( $pf['value'] ) ) echo $pf['value']; ?>" />
			</div>
			
			<div class="small-input">
				<label for="_royal_meta[plan_features][<?php echo $f; ?>][key]">
					<?php _e( 'Key', 'cheerapp' ); ?>
				</label>
				<input type="text" id="_royal_meta[plan_features][<?php echo $f; ?>][key]" name="_royal_meta[plan_features][<?php echo $f; ?>][key]" value="<?php if( !empty( $pf['key'] ) ) echo $pf['key']; ?>" />
			</div>
			
			<div class="small-input">
				<label for="_royal_meta[plan_features][<?php echo $f; ?>][detail]">
					<?php _e( 'Detail', 'cheerapp' ); ?>
				</label>
				<input type="text" id="_royal_meta[plan_features][<?php echo $f; ?>][detail]" name="_royal_meta[plan_features][<?php echo $f; ?>][detail]" value="<?php if( !empty( $pf['detail'] ) ) echo $pf['detail']; ?>" />
			</div>
			
			<div class="clear"></div>
			
		</div>
	
	<?php
	}
	?>
 
</div>