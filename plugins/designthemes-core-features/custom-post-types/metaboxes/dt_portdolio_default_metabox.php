<?php
global $post;
$portfolio_settings = get_post_meta ( $post->ID, '_portfolio_settings', TRUE );
$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();?>

<!-- Sub Title -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Sub Title','dt_themes');?></label>
	</div>

	<div class="column five-sixth last">
	<?php $subtitle = array_key_exists ( "sub-title", $portfolio_settings ) ? $portfolio_settings ['sub-title'] : '';?>
    
		<input id="sub-title" name="sub-title" type="text" class="widefat" 	value="<?php echo $subtitle;?>" />
		<p class="note"> <?php _e("If you wish! You can add sub title.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Sub Title End -->

<!-- Layout -->
<div class="custom-box ">
	<div class="column one-sixth">
		<label><?php _e('Layout','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<ul class="dt-bpanel-layout-set"><?php
		
		$layouts = array (
				'single-portfloio-layout-one' => 'portfolio-fullwidth',
				'single-portfloio-layout-two' => 'portfolio-with-left-gallery',
				'single-portfloio-layout-three' => 'portfolio-with-right-gallery' 
		);
		
		$v = array_key_exists ( "layout", $portfolio_settings ) ? $portfolio_settings ['layout'] : 'single-portfloio-layout-one';
		foreach ( $layouts as $key => $value ) {
			$class = ($key == $v) ? " class='selected' " : "";
			echo "<li><a href='#' rel='{$key}' {$class}><img src='" . plugin_dir_url ( __FILE__ ) . "images/columns/{$value}.png' alt='' /></a></li>";
		}
		?></ul>
		<?php $v = array_key_exists("layout",$portfolio_settings) ? $portfolio_settings['layout'] : 'single-portfloio-layout-one';?>
		<input id="mytheme-portfolio-layout" name="layout" type="hidden"
			value="<?php echo $v;?>" />
		<p class="note"> <?php _e("You can choose between a left, right or full width.",'dt_themes');?> </p>
	</div>

</div>
<!-- Layout End-->


<!-- Client Name-->
<div class="custom-box">

	<div class="column one-sixth">
		<label><?php _e('Client Name','dt_themes');?></label>
	</div>

	<div class="column five-sixth last">
	<?php $clientname = array_key_exists ( "client-name", $portfolio_settings ) ? $portfolio_settings ['client-name'] : '';?>
    
		<input id="client-name" name="client-name" type="text" class="widefat" 	value="<?php echo $clientname;?>" />
		<p class="note"> <?php _e("If you wish! You can add client name.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Client Name End -->

<!-- Website Link-->
<div class="custom-box">

	<div class="column one-sixth">
		<label><?php _e('Website Link','dt_themes');?></label>
	</div>

	<div class="column five-sixth last">
	<?php $websitelink = array_key_exists ( "website-link", $portfolio_settings ) ? $portfolio_settings ['website-link'] : '';?>
    
		<input id="website-link" name="website-link" type="text" class="widefat" value="<?php echo $websitelink;?>" />
		<p class="note"> <?php _e("If you wish! You can add client website url.",'dt_themes');?> </p>
        <div class="clear"></div>
	</div>
</div><!-- Website Link-->



<!-- Show Related Posts -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Show Related Projects','dt_themes');?></label>
	</div>
	<div class="column five-sixth last"><?php
	
	$switchclass = array_key_exists ( "show-related-items", $portfolio_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
	$checked = array_key_exists ( "show-related-items", $portfolio_settings ) ? ' checked="checked"' : '';
	?><div data-for="mytheme-related-item"
			class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
		<input id="mytheme-related-item" class="hidden" type="checkbox"
			name="mytheme-related-item" value="true" <?php echo $checked;?> />
		<p class="note"> <?php _e('Would you like to show the related projects at the bottom','dt_themes');?> </p>
	</div>
</div>
<!-- Show Related Posts End-->

<!-- Show Social Share -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Show Social Share','dt_themes');?></label>
	</div>
	<div class="column five-sixth last"><?php
	$switchclass = array_key_exists ( "show-social-share", $portfolio_settings ) ? 'checkbox-switch-on' : 'checkbox-switch-off';
	$checked = array_key_exists ( "show-social-share", $portfolio_settings ) ? ' checked="checked"' : '';
	?><div data-for="mytheme-social-share"
			class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
		<input id="mytheme-social-share" class="hidden" type="checkbox"
			name="mytheme-social-share" value="true" <?php echo $checked;?> />
		<p class="note"> <?php _e('Would you like to show the social share at the bottom','dt_themes');?> </p>
	</div>
</div>
<!-- Show Social Share End -->

<!-- Medias -->
<div class="custom-box">
	<div class="column one-sixth">
		<label><?php _e('Images','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<div class="dt-media-btns-container">
			<a href="#" class="dt-open-media custom-button-style"><?php _e( 'Click Here to Add Images', 'dt_themes' );?> </a>
			<a href="#" class="dt-add-video custom-button-style"><?php _e( 'Click Here to Add Video', 'dt_themes' );?> </a>
		</div>
		<div class="clear"></div>

		<div class="dt-media-container">
			<ul class="dt-items-holder"><?php
			if (array_key_exists ( "items", $portfolio_settings )) {
				foreach ( $portfolio_settings ["items_thumbnail"] as $key => $thumbnail ) {
					$item = $portfolio_settings ['items'] [$key];
					$out = "";
					$name = "";
					$foramts = array (
							'jpg',
							'jpeg',
							'gif',
							'png' 
					);
					$parts = explode ( '.', $item );
					$ext = strtolower ( $parts [count ( $parts ) - 1] );
					if (in_array ( $ext, $foramts )) {
						$name = $portfolio_settings ['items_name'] [$key];
						$out .= "<li>";
						$out .= "<img src='{$thumbnail}' alt='' />";
						$out .= "<span class='dt-image-name'>{$name}</span>";
						$out .= "<input type='hidden' name='items[]' value='{$item}' />";
					} else {
						$name = "video";
						$out .= "<li>";
						$out .= "<span class='dt-video'></span>";
						$out .= "<input type='text' name='items[]' value='{$item}' />";
					}
					
					$out .= "<input class='dt-image-name' type='hidden' name='items_name[]' value='{$name}' />";
					$out .= "<input type='hidden' name='items_thumbnail[]' value='{$thumbnail}' />";
					$out .= "<span class='my_delete'></span>";
					$out .= "</li>";
					echo $out;
				}
			}
			?></ul>
		</div>
	</div>
</div>
<!-- Medias End -->