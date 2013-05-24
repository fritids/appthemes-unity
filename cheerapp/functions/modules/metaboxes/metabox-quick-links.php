<div class="royal-metabox quick-links-metabox">

	<div class="royal-meta-section">
		<label for="_royal_meta[url]">
			<?php _e( 'Link URL', 'cheerapp' ); ?>
		</label>
		<input type="text" id="_royal_meta[url]" name="_royal_meta[url]" value="<?php if( !empty( $meta['url'] ) ) echo $meta['url']; ?>" />
	</div>
	
	<div class="royal-meta-section">
		<label for="_royal_meta[description]">
			<?php _e( 'Link Description', 'cheerapp' ); ?>
			<small><?php _e( 'Link description will appear when user hovers mouse over a link', 'cheerapp' ); ?></small>
		</label>
		<input type="text" id="_royal_meta[description]" name="_royal_meta[description]" value="<?php if( !empty( $meta['description'] ) ) echo $meta['description']; ?>" />
	</div>
	
	<?php $icons = array( 'padlock', 'download', 'search', 'rss', 'info', 'sticky', 'heart', 'comment', 'merge', 'split', 'edit', 'trash', 'stop', 'warning', 'replay', 'approve', 'mail', 'twitter', 'subitem' ); ?>
	<div class="royal-meta-section">
		<label for="_royal_meta[icon]">
			<?php _e( 'Link Icon', 'cheerapp' ); ?>
		</label>
		<select id="_royal_meta[icon]" name="_royal_meta[icon]">
			<?php
			foreach( $icons as $icon ) {
				$out = '<option ';
				if( $meta['icon'] == $icon ) $out .= 'selected="selected" ';
				$out .= 'class ="' . $icon . '" ';
				$out .= 'value="' . $icon . '">' . $icon . '</option>' . "\n";
				
				echo $out;
			}
			?>
		</select>
	</div>
 
</div>