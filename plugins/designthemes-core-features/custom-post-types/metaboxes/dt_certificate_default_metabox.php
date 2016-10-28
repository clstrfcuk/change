<?php
global $post;
$post_id = $post->ID;

?>

<!-- Bacground Image -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Bacground Image', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $background_image = get_post_meta ( $post->ID, 'background-image', TRUE ); ?>
        <input name="background-image" type="text" class="uploadfield medium" readonly value="<?php echo $background_image;?>"/>
        <input type="button" value="<?php _e('Upload','dt_themes');?>" class="upload_image_button show_preview button-primary" />
        <input type="button" value="<?php _e('Remove','dt_themes');?>" class="upload_image_reset button-primary" />
        <?php if( !empty($background_image) ) dttheme_adminpanel_image_preview($background_image );?>
        <p class="note"> <?php _e('Choose background image or pattern for your certificate.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Bacground Image End -->


<!-- Custom Class -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Custom Class', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $custom_class = get_post_meta ( $post->ID, 'custom-class', TRUE ); ?>
        <input type="text" id="custom-class" name="custom-class" value="<?php echo $custom_class; ?>" class="large">
        <p class="note"> <?php _e('Add your custom class name here.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Custom Class End -->

<!-- Custom CSS -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Custom CSS', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php $custom_css = get_post_meta ( $post->ID, 'custom-css', TRUE ); ?>
        <textarea id="custom-css" name="custom-css" class="large" rows="6"><?php echo $custom_css; ?></textarea>
        <p class="note"> <?php _e('Add your custom CSS here.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Custom CSS End -->

<!-- Enable Print -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Enable Print', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
			<?php
			$enable_print = get_post_meta ( $post->ID, "enable-print",true);
            $switchclass = ($enable_print != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($enable_print != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="enable-print" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="enable-print" class="hidden" type="checkbox" name="enable-print" value="true" <?php echo $checked;?> />
        <p class="note"> <?php _e('You can enable print button for your certificate.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Enable Print End -->

<!-- Shortcodes -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Shortcode', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
		<?php
		echo '<div class="dt-cert-sc-container">';
			echo __('<strong>Add below shortcode to make use of default certificate layout</strong>', 'dt_themes');
			echo '<div class="dt-cert-sc-box">';
			echo 	'<i>[dt_sc_certificate_template type="type1" certificate_title="" certificate_subtitle="" certificate_bg_image="" logo_topleft="" logo_topright="" logo_bottomcenter="" authority_sign="" authority_sign_name="" show_certificate_issueddate=""]<br />
					....<br />
					[/dt_sc_certificate_template]</i>';			
			echo '</div>';
			
			echo '<p>'.__('Available Types : ', 'dt_themes').'type1,type2,type3</p>';
			echo '<div class="dt-clear"> </div>';
			echo __('<strong>Use below shortcode to add certificate content dynamically</strong>', 'dt_themes');
			
			echo '<br /><br /><i> [dt_sc_certificate item="*" /] </i><br /><br />';
			echo __('Here instead of * you can replace with following fields, ', 'dt_themes');
			echo '<ul>';
			echo '<li><i>student_name</i> - '.__('To display student name', 'dt_themes').'</li>';
			echo '<li><i>course_name</i> - '.__('To display corresponding course name', 'dt_themes').'</li>';
			echo '<li><i>student_percent</i> - '.__('To display student percentage', 'dt_themes').'</li>';
			echo '<li><i>student_email</i> - '.__('To display student email', 'dt_themes').'</li>';
			echo '</ul>';
		echo '</div>';
		?>	
        <p class="note"> <?php _e('You can make use of above shortcode to display relavant data dynamically.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Shortcodes End -->