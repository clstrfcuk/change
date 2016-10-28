<?php
global $dtthemes_columns, $dtthemes_sample_layouts, $post, $default_widgets, $custom_widgets, $woocom_widgets, $postid, $theme_name, $modules_general, $modules_dividers, $modules_contact, $modules_others, $postid, $enable_pb_default;

$dt_helper_class = $tooltip = '';
$dt_convertible_settings = get_post_meta( $postid, '_dt_builder_settings', true );
if(empty($dt_convertible_settings )) {
	$dt_settings = get_post_meta( $postid );
	$dt_convertible_settings = isset($dt_settings['_dt_builder_settings'][0]) ? dt_mb_unserialize($dt_settings['_dt_builder_settings'][0]) : array();
}

do_action( 'dt_before_page_builder' ); 

?>

<div id="dtthemes_save">
    <span id="dtthemes_clear_all_wrapper">
        <span id="dtthemes_clear_all" class="dt_button dtthemes_clearall"><?php echo __('Clear All', 'dt_themes'); ?></span>
    </span>
    <span id="dtthemes_create_layout_wrapper">
        <span id="dtthemes_create_layout" class="dt_button dtthemes_createlayout"><?php echo __('Create Sample Layout', 'dt_themes'); ?></span>
    </span>
    <div class="dt_button">
        <span id="dt_add_customcss" class="dt_customcss_icon"></span>
        <span id="dt_add_customcss" class="dt_customcss_text"><?php echo __('Add Custom CSS', 'dt_themes'); ?></span>
    </div>
</div>

<div id="dt_page_builder">
	<div class="dt_builder_controls">
		<?php dt_get_modules_menu(true); ?>
	</div> <!-- #dt_builder_controls -->
	<div id="dt_modules">
		<?php dt_get_modules_list(''); ?>
		<div id="dt_module_separator"></div>
		<div id="dt_active_module_settings"></div>
	</div> <!-- #dt_modules -->
	
    <div id="dt_dragdrop_container">
    
    	<div class="dt_layout_highlighter" style="display:none;"></div>
        <div id="dt_layout_container">
            <div id="dt_layout">
                <?php
                    if ( is_array( $dt_convertible_settings ) && $dt_convertible_settings['layout_html'] ) {
                        echo stripslashes( $dt_convertible_settings['layout_html'] );
                        $dt_helper_class = ' class="hidden"';
                    }
                ?>
            </div> <!-- #dt_layout -->
            <div id="dtthemes_helper"<?php echo $dt_helper_class; ?>></div>
        </div> <!-- #dt_layout_container -->
        
    </div>
	<div style="display: none;">
		<?php wp_editor( ' ', 'dtthemes_hidden_editor' );  ?>
	</div>
</div> <!-- #dt_page_builder -->

<div id="dtthemes_ajax_save">
	<span><?php esc_html_e( 'Saving...', 'dt_themes' ); ?></span>
</div>