<?php

global $default_posttypes, $dtthemes_columns, $dtthemes_sample_layouts, $text_config, $dt_modules, $enable_widget, $theme_name, $dtthemes_modules, $default_animation_type, $default_animation_delay, $enable_pb_default, $dt_module_titles, $dt_widget_titles, $dt_wp_editor, $enable_animation_effects;


/* Theme name to get our custom widgets */
$theme_data = wp_get_theme ();
$theme_name = $theme_data->get ( 'Name' );

/* Wordpress Default Editor Name */
$dt_wp_editor = __('Wordpress Editor', 'dt_themes');


/* Seperate modules into different categories */
$dtthemes_modules = $dt_modules;
$dt_module_titles = array(
	'general' => 'General',
	'unique' => 'Unique',
	'headers' => 'Headers',
	'others' => 'Others', 
);

/* To split widgets into different categories provide search key(first 2 letters of widgets) */
$dt_widget_titles = array(
	'default_widget' => array('search_key' => '*', 'name' => 'WP Widgets'), // Use * to bring rest all widgets under this title
	'custom_widget' => array('search_key' => 'MY', 'name' => $theme_name.' Widgets'),
	'buddypress_widget' => array('search_key' => 'BP', 'name' => 'BuddyPress Widgets'),
	'events_widget' => array('search_key' => 'Tr', 'name' => 'Events Widgets'),
	'woocommerce_widget' => array('search_key' => 'WC', 'name' => 'WooCommerce Widgets'),
	'sensei_widget' => array('search_key' => 'Wo', 'name' => 'Sensei Widgets'),
);


/* Default animation type and animation delay for columns */
$default_animation_type = '';
$default_animation_delay = 400;


/* Enable page builder as default while creating new page
   Switch between 0 / 1 */
$enable_pb_default = 0;

/* To enable animation effects for columns */
$enable_animation_effects = false;

/* To make widget droppable inside module */
$enable_widget = true;


/* Defalt post types to activate page builder */
$default_posttypes = array ();


/* Configure UI messages here.
   Note: Don't change any hook name in this array */ 
$text_config = array(
	'confirm_message' => __('Permanently delete this module?', 'dt_themes'), 
	'confirm_clear_all_message' => __('Permanently delete all modules?', 'dt_themes'), 
	'confirm_custom_layout_delete_message' => __('Permanently delete this layout?', 'dt_themes'), 
	'create_layout_name' => __('Layout Name', 'dt_themes'), 
	'create_layout_confirm_message_yes' => __('Create', 'dt_themes'), 
	'create_layout_confirm_message_no' => __('Cancel', 'dt_themes'), 
	'create_layout_description_text' => __('* new layout will appear under the Sample Layout tab after page update', 'dt_themes'), 
	'confirm_message_yes' => __('Yes', 'dt_themes'), 
	'confirm_message_no' => __('No', 'dt_themes'), 
	'saving_text' => __('Saving...', 'dt_themes'), 
	'layout_saved_text' => __('Layout Saved.', 'dt_themes'), 
	'customcss_saved_text' => __('Custom CSS Saved.', 'dt_themes'), 
	'columnoptions_saved_text' => __('Column Options Saved.', 'dt_themes'),
	'sectionoptions_saved_text' => __('Section Options Saved.', 'dt_themes')
);


/* Configure any number of Sample Layout here */

$dir = plugin_dir_path ( __FILE__ ) . "samplelayouts/";

if (is_dir ( $dir )) {
	
	foreach( glob( $dir . "*.txt" ) as $filepath )
	{
	
		$content = file_get_contents($filepath);
		$fn = explode('/', $filepath);
		$fn = $fn[count($fn)-1];
		$fn = explode('.', $fn);
		$filename = $fn[0];
		
		$ft = explode('-', $filename);
		$filetitle = '';
		
		foreach($ft as $x) {
			$filetitle .= ucfirst($x).' ';	
		}
		
		$dtthemes_sample_layouts[$filename] = array(
			'name' => $filetitle,
			'content' => $content
		);
		
	}

}

?>