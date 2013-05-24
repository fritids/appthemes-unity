<div class="royal-metabox quick-links-metabox">

	<?php
	$terms = get_terms( 'faq_category', array( 'hide_empty' => false ) );
	?>
	
	<div class="royal-meta-section">
		<label for="_royal_meta[faq_group]">
			<?php _e( 'Attach FAQ Group', 'cheerapp' ); ?>
		</label>
		<select id="_royal_meta[faq_group]" name="_royal_meta[faq_group]">
			<option><?php _e( 'Select Group', 'cheerapp' ); ?></option>
			<?php
			foreach( $terms as $term ) {
				$out = '<option ';
				if( $meta['faq_group'] == $term->slug ) $out .= 'selected="selected" ';
				$out .= 'class ="' . $term->slug . '" ';
				$out .= 'value="' . $term->slug . '">' . $term->name . '</option>' . "\n";
				
				echo $out;
			}
			?>
		</select>
	</div>
 
</div>