<?php

/**
 * A function to get modules menu list
 */
if ( ! function_exists('dt_get_modules_menu') ){
	function dt_get_modules_menu($show){
		global $dtthemes_columns, $dtthemes_sample_layouts, $post, $postid, $theme_name, $dtthemes_modules, $dt_module_titles, $dt_widget_titles, $dt_widgets;
		
		if(isset($dtthemes_columns) && $show): ?><a href="#" class="dt_add_element dt_add_column"><?php esc_html_e('Columns', 'dt_themes'); ?></a><?php endif;
		
		foreach($dt_module_titles as $dt_mod_key => $dt_module_title) {
			if(isset($dtthemes_modules[$dt_mod_key])) {
				?>
                <a href="#" class="dt_add_element <?php echo 'dt_add_'.$dt_mod_key ; ?>"><?php echo $dt_module_title; ?></a>
                <?php
			}
		}
		foreach($dt_widget_titles as $dt_mod_key => $dt_widget_title) {
			$i = 0;
			foreach ( $dt_widgets as $module_key => $module_settings ){
				if ($dt_widget_title['search_key'] != '*' && strpos($module_key, $dt_widget_title['search_key']) !== false) $i++; elseif ($dt_widget_title['search_key'] == '*') $i++;
			}
			if($i > 0) {
			?>
			<a href="#" class="dt_add_element <?php echo 'dt_add_'.$dt_mod_key ; ?>"><?php echo $dt_widget_title['name']; ?></a>
			<?php
			}
		}
		if(isset($dtthemes_sample_layouts) && $show): ?><a href="#" class="dt_add_element dt_add_sample_layout"><?php esc_html_e('Sample Layouts', 'dt_themes'); ?></a><?php endif;

	?>
	<?php
	}
}

/**
 * A function to get complete modules list
 */
if ( ! function_exists('dt_get_modules_list') ){
	function dt_get_modules_list($modid){
		global $dtthemes_columns, $dtthemes_sample_layouts, $post, $postid, $theme_name, $dt_module_titles, $dtthemes_modules, $dt_widget_titles, $dt_widgets;
		 
		$mid = '';
		if(isset($modid) && $modid != '') $mid = "id='".$modid."'";
		

		// Columns
		if(isset($dtthemes_columns)):
			foreach ( $dtthemes_columns as $column_key => $column_settings ){
				
				if($column_settings['type'] != 'section') {
					
					if($column_key != 'resizable') $extra_cls = 'dt_disable_resize'; else $extra_cls = '';
					echo "<div ".$mid." data-placeholder='" . esc_attr( $column_settings['name'] ) . "' data-name='" . esc_attr( $column_key ) . "' class='" . esc_attr( "dt_module dt_m_column {$extra_cls} dt_m_column_{$column_key}" ) . "'>" .
						'<div class="dt_module_controls">
							<span class="dt_module_name dt_column_name">' . esc_html( $column_settings['name'] ) . '</span>
							<span class="dt_move"></span>
							<div class="dt_module_options">
								<span class="dt_delete_column" title="Delete"></span>
								<span class="dt_clone_column" title="Clone"></span>
								<span class="dt_settings_arrow_column" title="Settings"></span>
								<span class="dt_add_module_column" title="Add Module" style="display:none;">A</span>
							</div>
							<div class="dt_columndata_settings"></div>
						</div><div class="dt_modules_container"></div>
					</div>';
					
				} else {
					
					echo "<div".$mid." data-placeholder='" . esc_attr( $column_settings['name'] ) . "' data-name='".$column_key."' class='" . esc_attr( "dt_module dt_disable_resize dt_fullwidth_section {$column_key}" ) . "'>" .
						'<div class="dt_module_controls">
							<span class="dt_module_name dt_column_name">'.$column_settings['name'].'</span>
							<span class="dt_move"></span>
							<div class="dt_module_options">
								<span class="dt_delete_column" title="Delete"></span>
								<span class="dt_clone_column" title="Clone"></span>
								<span class="dt_settings_arrow_fullwidth" title="Settings"></span>
								<span class="dt_add_module_column" title="Add Module" style="display:none;">A</span>
							</div>
							<div class="dt_fullwidthsection_data_settings"></div>
						</div><div class="dt_fullwidth_section_container"></div>
					</div>';
					
				}
			}
		endif;
		
		// Modules
		foreach($dt_module_titles as $dt_mod_key => $dt_module_title) {
			if(isset($dtthemes_modules[$dt_mod_key])) {
				foreach ( $dtthemes_modules[$dt_mod_key] as $module_key => $module_settings ) {
					$class = "dt_module dt_m_{$module_key} dt_module_".$dt_mod_key;
					if(isset($module_settings['disable_resize'])) $class .= " dt_disable_resize";
					if(isset($module_settings['full_width']) && $module_settings['full_width']) $class .= ' dt_full_width';
					if(isset($module_settings['tooltip'])) $tooltip = $module_settings['tooltip']; else $tooltip = $module_settings['name'];
					if(isset($module_settings['width'])) $spacer_width = " data-width='" . esc_attr( $module_settings['width'] ) . "'"; else $spacer_width = '';
					
					echo "<div ".$mid." data-placeholder='" . esc_attr( $module_settings['name'] ) . "' data-name='" . esc_attr( $module_key ) . "' class='" . esc_attr( $class ) . " dt-sc-tooltip' title='" . $tooltip . "' " . $spacer_width . ">" . 
					'<span class="' . esc_html( $module_settings['icon_class'] ) . ' dt_icon"></span>
					<span class="dt_module_name">' . esc_html( $module_settings['name'] ) . '</span>';
					echo '<div class="dt_module_options">';
						if(!isset($module_settings['disable_resize'])) echo '<span class="dt_move"></span>';
						echo '<span class="dt_showorhide dt_show" title="Show"></span>
						<span class="dt_delete" title="Delete"></span>
						<span class="dt_clone_module" title="Clone"></span>';
						if(isset($module_settings['options'])): echo '<span class="dt_settings_arrow_module" title="Settings"></span>'; endif;
					echo '</div>
						<div class="dt_preview"></div>
						<div class="dt_module_settings"></div>
					</div>';
				}
			}
		}
		
		
		// Widgets
		$srch_arr = $wdkeys = array();
		foreach($dt_widget_titles as $wd_title) { if($wd_title['search_key'] != '*') $wdkeys[] = $wd_title['search_key']; }
		
		foreach($dt_widget_titles as $dt_key => $dt_widget_title) {	
			foreach ( $dt_widgets as $module_key => $module_settings ){
					if ($dt_widget_title['search_key'] != '*' && strpos($module_key, $dt_widget_title['search_key']) !== false) {
						
						$ico_class = explode('-', $module_settings['wpid']);
						$class = "dt_module dt_m_widget dt_m_{$module_key} dt_".$dt_key;
						if(isset( $module_settings['tooltip'])) $tooltip = $module_settings['tooltip']; else $tooltip = $module_settings['name'];
						echo "<div ".$mid." data-placeholder='" . esc_attr( $module_settings['name'] ) . "' data-wpid='" . esc_attr( $module_settings['wpid'] ) . "' data-wpname='" . esc_attr( $module_settings['wpname'] ) . "' data-name='" . esc_attr( $module_key ) . "'  data-attr='" . esc_attr( $module_key ) . "' class='" . esc_attr( $class ) . " dt-sc-tooltip'  title='" . $tooltip . "'>" . 
							'<span class="ico-' . $ico_class[0] . ' dt_icon"></span>
							<span class="dt_module_name">' . esc_html( $module_settings['name'] ) . '</span>
							<div class="dt_module_options">
								<span class="dt_move"></span>
								<span class="dt_showorhide dt_show" title="Show"></span>
								<span class="dt_delete" title="Delete"></span>
								<span class="dt_clone_module" title="Clone"></span>';
								if(isset($module_settings['form'])): echo '<span class="dt_settings_arrow_widget" title="Settings"></span>'; endif;
							echo '</div>
							<div class="dt_preview"></div>
							<div class="dt_module_settings"></div>
						</div>';
						
					} elseif ($dt_widget_title['search_key'] == '*' && !in_array(substr($module_key, 0, 2), $wdkeys)) {
						
						$ico_class = explode('-', $module_settings['wpid']);
						$class = "dt_module dt_m_widget dt_m_{$module_key} dt_".$dt_key;
						if(isset( $module_settings['tooltip'])) $tooltip = $module_settings['tooltip']; else $tooltip = $module_settings['name'];
						echo "<div ".$mid." data-placeholder='" . esc_attr( $module_settings['name'] ) . "' data-wpid='" . esc_attr( $module_settings['wpid'] ) . "' data-wpname='" . esc_attr( $module_settings['wpname'] ) . "' data-name='" . esc_attr( $module_key ) . "'  data-attr='" . esc_attr( $module_key ) . "' class='" . esc_attr( $class ) . " dt-sc-tooltip'  title='" . $tooltip . "'>" . 
							'<span class="ico-' . $ico_class[0] . ' dt_icon"></span>
							<span class="dt_module_name">' . esc_html( $module_settings['name'] ) . '</span>
							<div class="dt_module_options">
								<span class="dt_move"></span>
								<span class="dt_showorhide dt_show" title="Show"></span>
								<span class="dt_delete" title="Delete"></span>
								<span class="dt_clone_module" title="Clone"></span>';
								if(isset($module_settings['form'])): echo '<span class="dt_settings_arrow_widget" title="Settings"></span>'; endif;
							echo '</div>
							<div class="dt_preview"></div>
							<div class="dt_module_settings"></div>
						</div>';
						
					}
			}
		}
		
		if(isset($dtthemes_sample_layouts)):
			foreach ( $dtthemes_sample_layouts as $layout_key => $layout_settings ){
				
				$is_user_sample_layout = isset( $layout_settings['is_user_sample_layout'] ) && $layout_settings['is_user_sample_layout'];
				
				echo "<div ".$mid." data-placeholder='" . esc_attr( $layout_settings['name'] ) . "' data-name='" . esc_attr( $layout_key ) . "' class='" . esc_attr( "dt_module dt_sample_layout" ) . "'>" .
				'<span class="dt_module_name">' . esc_html( $layout_settings['name'] ) . '</span>' .
				'<span class="dt_move"></span>' . ( $is_user_sample_layout ? '<span class="dt_user_layout_delete">x</span>' : '' ) . '</div>';
			}
		endif;	
	}
}


/**
 * A action hook to save layout
 */
add_action( 'wp_ajax_dt_save_layout', 'dt_new_save_layout' );
function dt_new_save_layout(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$dt_convertible_settings = array();

	$dt_convertible_settings['layout_html'] = trim( $_POST['dt_layout_html'] ); 
	$dt_convertible_settings['layout_shortcode'] = $_POST['dt_layout_shortcode'];
	$dt_post_id = (int) $_POST['dt_post_id'];

	$dt_builder_settings = get_post_meta( $dt_post_id, '_dt_builder_settings', true );
	if(empty($dt_builder_settings )) {
		$dt_settings = get_post_meta( $dt_post_id );
		$dt_builder_settings = dt_mb_unserialize($dt_settings['_dt_builder_settings'][0]);
	}

	if ( !empty($dt_builder_settings) ) update_post_meta( $dt_post_id, '_dt_builder_settings', $dt_convertible_settings );
	else add_post_meta( $dt_post_id, '_dt_builder_settings', $dt_convertible_settings, true );
	
	die();
}


/**
 * A action hook to create sample layout
 */
add_action( 'wp_ajax_dt_create_new_sample_layout', 'dt_create_new_sample_layout' );
function dt_create_new_sample_layout(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$dt_layout_html = trim( $_POST['dt_layout_html'] );
	$dt_new_layout_name = sanitize_text_field( $_POST['dt_new_layout_name'] );

	$dtthemes_settings = get_option( 'dtthemes_settings' );

	$custom_layouts = isset( $dtthemes_settings['custom_sample_layouts'] ) ? $dtthemes_settings['custom_sample_layouts'] : array();
	$custom_layouts[] = array( 'name' => $dt_new_layout_name, 'content' => $dt_layout_html, 'is_user_sample_layout' => true );

	$dtthemes_settings['custom_sample_layouts'] = $custom_layouts;

	update_option( 'dtthemes_settings', $dtthemes_settings );

	die();
}


/**
 * A action hook to delete sample layout
 */
add_action( 'wp_ajax_dt_delete_sample_layout', 'dt_delete_sample_layout' );
function dt_delete_sample_layout(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$dt_layout_key = (int) $_POST['dt_layout_key'];

	$dtthemes_settings = get_option( 'dtthemes_settings' );

	if ( isset( $dtthemes_settings['custom_sample_layouts'][$dt_layout_key] ) ){
		unset( $dtthemes_settings['custom_sample_layouts'][$dt_layout_key] );
		$dtthemes_settings['custom_sample_layouts'] = array_values( $dtthemes_settings['custom_sample_layouts'] );
		update_option( 'dtthemes_settings', $dtthemes_settings );
	}

	die();
}


/**
 * A action hook to append sample layout to existing layout
 */
add_action( 'wp_ajax_dt_append_layout', 'dt_new_append_layout' );
function dt_new_append_layout(){
	global $dtthemes_sample_layouts;

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$layout_name = $_POST['dt_layout_name'];
	

	if ( isset( $dtthemes_sample_layouts[$layout_name] ) ) echo stripslashes( $dtthemes_sample_layouts[$layout_name]['content'] );

	die();
}



/**
 * A action hook and funtion to get modules data
 */
add_action( 'wp_ajax_dt_get_module_data', 'dt_get_module_data' );
function dt_get_module_data(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$module_name = $module_type = $matches = $module_class = $dt_module_exact_name = '';
	$module_window = 0;

	$module_class = $_POST['dt_module_class'];
	$dt_module_exact_name = $_POST['dt_module_exact_name'];
	$module_window = (int) $_POST['dt_modal_window'];

	$cors_mod = explode(' ', $module_class);
	foreach($cors_mod as $cmod) {
		if (strpos($cmod, 'dt_module_') !== false) $module_type = str_replace('dt_module_', '', $cmod);	
	}

	preg_match_all( '/dt_m_([^\s])+/', $module_class, $matches );	
	$module_name = str_replace( 'dt_m_', '', $matches[0][0] );

	dt_generate_module_datas( $module_name, $dt_module_exact_name, $module_type );
	
	die();
}


if ( ! function_exists('dt_generate_module_datas') ){
	function dt_generate_module_datas( $module_name, $dt_module_exact_name, $module_type ){
		global $dtthemes_modules;
		
		$dtthemes_modules_new = $dtthemes_modules;

		if(isset($dtthemes_modules_new[$module_type][$module_name]['options'])):
			$out = '<div class="dt_module_settings">';
			foreach ( $dtthemes_modules_new[$module_type][$module_name]['options'] as $option_slug => $option_settings ){
				$content_class = $opt_name = $opt_content = '';
				$opt_name = esc_attr( $option_slug );
				if(isset( $option_settings['is_content'] )):
					if(isset($option_settings['default_value'])) $opt_content = $option_settings['default_value'];
					else $opt_content = '';
					$content_class = isset( $option_settings['is_content'] ) ? 'dtthemes_module_content ' : '';
				else:
					$default_value = isset( $option_settings['default_value'] ) ? $option_settings['default_value'] : '';
					if(is_array($default_value)):
						$opt_content = implode(',', $default_value);
					else:
						$opt_content = ('' != $default_value) ? esc_attr( $default_value ) : '';
					endif;
				endif;
				$out .= '<div data-option_name="' . $opt_name . '" class="' . $content_class . $opt_name . ' dt_module_setting">' . $opt_content . '</div>';
			}
			$out .= '</div>';
			echo $out;
		else:
			echo '<div class="dt_module_settings"></div>';
		endif;
		
	}
}


/**
 * A action hook and funtion to show module options
 */
add_action( 'wp_ajax_dt_show_module_options', 'dt_new_show_module_options' );
function dt_new_show_module_options(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$module_name = $module_type = $matches = $paste_to_editor_id = '';
	$module_window = 0;

	$module_class = $_POST['dt_module_class'];
	$dt_module_exact_name = $_POST['dt_module_exact_name'];
	$module_window = (int) $_POST['dt_modal_window'];

	$cors_mod = explode(' ', $module_class);
	foreach($cors_mod as $cmod) {
		if (strpos($cmod, 'dt_module_') !== false && $cmod != 'dt_module_resizable') $module_type = str_replace('dt_module_', '', $cmod);	
	}

	preg_match_all( '/dt_m_([^\s])+/', $module_class, $matches );	
	$module_name = str_replace( 'dt_m_', '', $matches[0][0] );
	$paste_to_editor_id = isset( $_POST['dt_paste_to_editor_id'] ) ? $_POST['dt_paste_to_editor_id'] : '';

	dt_generate_module_options( $module_name, $module_window, $paste_to_editor_id, $dt_module_exact_name, $module_type );
	
	die();
}

if ( ! function_exists('dt_generate_module_options') ){
	function dt_generate_module_options( $module_name, $module_window, $paste_to_editor_id, $dt_module_exact_name, $module_type ){
		global $dtthemes_modules;
		
		$dtthemes_modules_new = $dtthemes_modules;
		
		$i = 1;
		$form_id = ( 0 == $module_window ) ? 'dt_module_settings' : 'dt_dialog_settings';

		echo '<form id="' . esc_attr( $form_id ) . '">';
		echo '<div class="dt_settings_title">' . esc_html( $dt_module_exact_name . ' ' . __('Settings', 'dt_themes') ) . '</div>';

		if ( 0 == $module_window ) echo '<a href="#" id="dt_close_module_settings"></a>';
		else echo '<a href="#" id="dt_close_dialog_settings"></a>';

		if(isset($dtthemes_modules_new[$module_type][$module_name]['options'])):
		
			foreach ( $dtthemes_modules_new[$module_type][$module_name]['options'] as $option_slug => $option_settings ){
				$content_class = isset( $option_settings['is_content'] ) ? ' dtthemes_module_content' : '';
	
				echo '<p>';
				
				if($option_slug == 'info'): echo '<p>'.$option_settings.'</p>'; 
				elseif ( isset( $option_settings['title'] ) ): echo "<label><span class='dt_module_option_number'>{$i}</span>. {$option_settings['title']}</label>"; endif;
				
				if ( 1 == $module_window ) $option_slug = 'dt_dialog_' . $option_slug;
	
				switch ( $option_settings['type'] ) {
					
					case 'wp_editor':
						$opt_content = isset($option_settings['default_value']) ? $option_settings['default_value'] : '';
						wp_editor( $opt_content, $option_slug, array( 'editor_class' => 'dtthemes_wp_editor dtthemes_option ' . $content_class ) );
						
						break;
						
					case 'select':		
						$default_value = isset( $option_settings['default_value'] ) ? $option_settings['default_value'] : array();
						if(isset($option_settings['multiple']) && $option_settings['multiple'] == 1) { $multi_str = 'multiple="multiple"'; $content_class .= ' dt_multiselect'; } else { $multi_str = ''; }
						echo
						'<span class="selection-box"><select name="' . esc_attr( $option_slug ) . '" id="' . esc_attr( $option_slug ) . '" class="dtthemes_option' . $content_class . '" '.$multi_str.'>'
							. '<option value="">  ' . esc_html__('Select', 'dt_themes') . '  </option>';
							foreach ( $option_settings['options'] as  $setting_key => $setting_value ){
								$sel_value =  isset($setting_key) ? $setting_key : $setting_value;
								$sel_res = in_array($sel_value, $default_value) ? ' selected="selected"' : '';
								echo '<option value="' . esc_attr( $sel_value ) . '"' . $sel_res . '>' . esc_html( $setting_value ) . '</option>';
							}
						echo '</select></span>';
						break;
						
					case 'text':
						$default_value = isset( $option_settings['default_value'] ) ? $option_settings['default_value'] : '';
						echo '<input name="' . esc_attr( $option_slug ) . '" type="text" id="' . esc_attr( $option_slug ) . '" value="'.$default_value.'" class="regular-text dtthemes_option' . $content_class . '" />';
						break;
						
					case 'checkbox':
						if(isset($option_settings['default_value']) && $option_settings['default_value'] == $option_settings['returnval']) $chk = checked('checked'); else $chk = '';
						$defval = isset( $option_settings['returnval'] ) ? $option_settings['returnval'] : esc_attr( $option_slug );
						echo '<input name="' . esc_attr( $option_slug ) . '"  value="' . $defval . '" type="checkbox" id="' . esc_attr( $option_slug ) . '" class="dtthemes_option' . $content_class . '" '.$chk.' />';
						break;
						
					case 'upload':
						echo '<input name="' . esc_attr( $option_slug ) . '" type="text" id="' . esc_attr( $option_slug ) . '" value="" class="regular-text dtthemes_option dtthemes_upload_field' . $content_class . '" />' . '<a href="#" class="dtthemes_upload_button">' . esc_html__('Upload', 'dt_themes') . '</a>';
						break;

					case 'colorpicker':		
						dtthemes_color_picker(esc_attr( $option_slug ), '#');
						break;
						
				}
	
				echo '</p>';
	
				++$i;
			}
		
		else:
			echo '<p>This module don\'t have any attribute to set, just click save changes button to add shortcode.</p>';
		endif;
		
		echo '<span id="dt_save_data" class="dt_button dt_save_text">'.__('Save Changes', 'dt_themes').'</span>';
		
		echo '<input type="hidden" id="dt_saved_module_name" value="' . esc_attr( $module_name ) . '" />';
		
		if(isset($dtthemes_modules_new[$module_type][$module_name]['disable_resize'])) $dis_resize = $dtthemes_modules_new[$module_type][$module_name]['disable_resize'];
		else $dis_resize = '';
		
		echo '<input type="hidden" id="dt_disable_resize" value="' . $dis_resize . '" />';

		if ( '' != $paste_to_editor_id ) echo '<input type="hidden" id="dt_paste_to_editor_id" value="' . esc_attr( $paste_to_editor_id ) . '" />';

		echo '</form>';

		
	}
}


/**
 * A action hook and funtion to show widget options
 */
add_action( 'wp_ajax_dt_show_widget_options', 'dt_new_show_widget_options' );
function dt_new_show_widget_options(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$module_name = $module_type = $matches = $paste_to_editor_id = '';

	$module_window = 0;

	$module_class = $_POST['dt_module_class'];
	$dt_module_exact_name = $_POST['dt_module_exact_name'];
	$paste_to_editor_id = isset( $_POST['dt_paste_to_editor_id'] ) ? $_POST['dt_paste_to_editor_id'] : '';
	$module_window = (int) $_POST['dt_modal_window'];

	preg_match_all( '/dt_m_([^\s])+/', $module_class, $matches );
	$module_name = str_replace( 'dt_m_', '', $matches[0][1] );
	dt_generate_widget_options( $module_name, $dt_module_exact_name, $paste_to_editor_id, $module_window );
	
	die();
}

if ( ! function_exists('dt_generate_widget_options') ){
	function dt_generate_widget_options( $module_name, $dt_module_exact_name, $paste_to_editor_id, $module_window ){
		global $dt_widgets;

		if(isset($dt_widgets[$module_name]['form'])):
		
		$form_id = ( 0 == $module_window ) ? 'dt_widget_settings' : 'dt_dialog_settings';
		
		if($form_id == 'dt_dialog_settings') $form_cls = ' class="dt_widget_popup"'; else $form_cls = '';
		
		echo '<form id="'.$form_id.'" '.$form_cls.'>';
		echo '<div class="dt_settings_title">' . esc_html( $dt_module_exact_name . ' ' . __('Settings', 'dt_themes') ) . '</div>';

		if ( 0 == $module_window ) echo '<a href="#" id="dt_close_widget_settings"></a>';
		else echo '<a href="#" id="dt_close_dialog_settings"></a>';

		echo $dt_widgets[$module_name]['form'];
		
		
		echo '<span id="dt_save_data" class="dt_button dt_save_text">'.__('Save Changes', 'dt_themes').'</span>';

		echo '<input type="hidden" id="dt_widget_module_name" value="' . esc_attr( $module_name ) . '" />';
		echo '<input type="hidden" id="dt_widget_module_wpname" value="' . esc_attr( $dt_widgets[$module_name]['wpname'] ) . '" />';
		echo '<input type="hidden" id="dt_widget_module_wpid" value="' . esc_attr( $dt_widgets[$module_name]['wpid'] ) . '" />';
		echo '<input type="hidden" id="dt_widget_module_optionname" value="' . esc_attr( strtolower ($dt_widgets[$module_name]['name']) ) . '" />';
		
		echo '<input type="hidden" id="dt_module_type" value="widget" />';

		if ( '' != $paste_to_editor_id ) echo '<input type="hidden" id="dt_paste_to_editor_id" value="' . esc_attr( $paste_to_editor_id ) . '" />';

		echo '</form>';
		
		else:
			echo '<p>This module don\'t have any attribute to set</p>';
		endif;
		
	}
}


/**
 * A action hook and funtion to show column options
 */
add_action( 'wp_ajax_dt_show_column_options', 'dt_new_show_column_options' );
function dt_new_show_column_options(){
	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$module_class = $_POST['dt_module_class'];

	preg_match( '/dt_m_column_([^\s])+/', $module_class, $matches );
	$module_name = str_replace( 'dt_m_column_', '', $matches[0] );

	$paste_to_editor_id = isset( $_POST['dt_paste_to_editor_id'] ) ? $_POST['dt_paste_to_editor_id'] : '';

	dt_generate_column_options( $module_name, $paste_to_editor_id );

	die();
}

if ( ! function_exists('dt_generate_column_options') ){
	function dt_generate_column_options( $column_name, $paste_to_editor_id ){
		global $dtthemes_columns;

		$module_name = $dtthemes_columns[$column_name]['name'];
		echo '<form id="dt_dialog_settings">'
				. '<div class="dt_settings_title">' . esc_html( ucfirst( $module_name ) . ' ' . __('Settings', 'dt_themes') ) . '</div>'
				. '<a href="#" id="dt_close_dialog_settings"></a>';

		if ( 'resizable' == $column_name ) echo '<p><label>' . esc_html__('Column width (%)', 'dt_themes') . ':</label> <input name="dt_dialog_width" type="text" id="dt_dialog_width" value="100" class="regular-text dtthemes_option" /></p>';

		echo  '<p><input type="checkbox" id="dt_dialog_first" name="dt_dialog_first" value="" class="dtthemes_option" /> ' . esc_html__('This is the first column in the row', 'dt_themes') . '</p>';

		echo '<span id="dt_save_data" class="dt_button dt_save_text">'.__('Save Changes', 'dt_themes').'</span>';

		echo '<input type="hidden" id="dt_saved_module_name" value="' . esc_attr( "{$column_name}" ) . '" />';
		echo '<input type="hidden" id="dt_module_type" value="column" />';

		if ( '' != $paste_to_editor_id ) echo '<input type="hidden" id="dt_paste_to_editor_id" value="' . esc_attr( $paste_to_editor_id ) . '" />';

		echo '</form>';
	}
}


/**
 * A action hook and funtion to show column additional options
 */
add_action( 'wp_ajax_dt_show_columoptions_panel', 'dt_show_columoptions_panel' );
function dt_show_columoptions_panel(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);
	$dt_column_css = isset($_POST['dt_column_css']) ? $_POST['dt_column_css'] : '';
	$dt_column_animation_type = isset($_POST['dt_column_animation_type']) ? $_POST['dt_column_animation_type'] : '';
	$dt_column_animation_delay = isset($_POST['dt_column_animation_delay']) ? $_POST['dt_column_animation_delay'] : '';

	dt_generate_columnoptions_panel($dt_column_css, $dt_column_animation_type, $dt_column_animation_delay);
	
	die();
}

if ( ! function_exists('dt_generate_columnoptions_panel') ){
	function dt_generate_columnoptions_panel( $dt_column_css, $dt_column_animation_type, $dt_column_animation_delay ){
		global $dt_animation_types, $default_animation_type, $default_animation_delay, $enable_animation_effects;
		echo '<div class="dt_dialog_handle">' . esc_html( __('Additional Column Options', 'dt_themes') ) . '</div><a href="#" id="dt_close_dialog_settings"></a>';
		echo '<form id="dt_column_settings">'
				. '<a href="#" id="dt_close_column_settings"></a>'
				. '<p><label>Custom Class</label><input type="text" class="regular-text dtthemes_option" name="dt_column_css" id="dt_column_css" value="'.$dt_column_css.'"  /></p>';
					
			$dt_animation_types = !empty( $dt_animation_types ) ? $dt_animation_types : '';
			
			if($enable_animation_effects && $dt_animation_types != '') {
			
				$sel_value = !empty($dt_column_animation_type) ? $dt_column_animation_type : $default_animation_type;	
					
				echo
				'<p><label>Custom Animation Type</label><span class="selection-box"><select name="dt_column_animation_type" id="dt_column_animation_type" >'
					. '<option value="None">  ' . esc_html__('Select', 'dt_themes') . '  </option>';
					foreach ( $dt_animation_types as  $type ){
						echo '<option value="' . esc_attr( $type ) . '" ' . selected( $sel_value, $type, false ) . '>' . esc_html( $type ) . '</option>';
					}
				echo '</select></span></p>';
				
				$dt_column_animation_delay = !empty($dt_column_animation_delay) ? $dt_column_animation_delay : $default_animation_delay;	
				
				echo
				'<p><label>Custom Animation Delay</label><input type="number" class="regular-text dtthemes_option" name="dt_column_animation_delay" id="dt_column_animation_delay" value="'.$dt_column_animation_delay.'"  /></p>';
			
			}
		echo '<span id="save_columnoptions" class="dt_button save_columnoptions">'.__('Save Changes', 'dt_themes').'</span>';
		echo '</form>';
	}
}


/**
 * A action hook and funtion to show fullwidth section options
 */
add_action( 'wp_ajax_dt_show_fullwidthsection_options', 'dt_show_fullwidthsection_options' );
function dt_show_fullwidthsection_options(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);
	$dt_section_key = $_POST['dt_section_key'];

	dt_generate_fullwidthsection_options($dt_section_key);
	
	die();
}

if ( ! function_exists('dt_generate_fullwidthsection_options') ){
	function dt_generate_fullwidthsection_options( $dt_section_key ){
		global $dtthemes_columns;
		
		echo '<div class="dt_dialog_handle">' . esc_html( __('Fullwidth Section Options', 'dt_themes') ) . '</div><a href="#" id="dt_close_sections_settings"></a>';
		echo '<form id="dt_fullwidth_section_settings">';
				
				foreach($dtthemes_columns[$dt_section_key]['options'] as $field_key => $field_settings) {
					$content_class = isset( $field_settings['is_content'] ) ? ' dtthemes_module_content' : '';
					
					switch ( $field_settings['type'] ) {
						
						case 'select':		
							$default_value = isset( $field_settings['default_value'] ) ? array($field_settings['default_value']) : array();
							
							if(isset($field_settings['multiple']) && $field_settings['multiple'] == 1) { $multi_str = 'multiple="multiple"'; $content_class .= ' dt_multiselect'; } else { $multi_str = $content_class = ''; }
							echo '<p><label>'.$field_settings['title'].'</label> ';							
							echo
							'<span class="selection-box"><select name="' . esc_attr( $field_key ) . '" id="' . esc_attr( $field_key ) . '" class="dtthemes_fws_option' . $content_class . '" '.$multi_str.'>'
								. '<option value="">  ' . esc_html__('Select', 'dt_themes') . '  </option>';
								foreach ( $field_settings['options'] as  $setting_key => $setting_value ){
									$sel_value =  isset($setting_key) ? $setting_key : $setting_value;
									$sel_res = in_array($sel_value, $default_value) ? ' selected="selected"' : '';
									echo '<option value="' . esc_attr( $sel_value ) . '"' . $sel_res . '>' . esc_html( $setting_value ) . '</option>';
								}
							echo '</select></span>';
							echo '</p>';
							break;
							
						case 'text':
							$default_value = isset( $field_settings['default_value'] ) ? $field_settings['default_value'] : '';
							echo '<p><label>'.$field_settings['title'].'</label> ';
							echo '<input name="' . esc_attr( $field_key ) . '" type="text" id="' . esc_attr( $field_key ) . '" value="'.$default_value.'" class="regular-text dtthemes_fws_option' . $content_class . '" />';
							echo '</p>';
							break;
							
						case 'checkbox':
							if(isset($field_settings['default_value']) && $field_settings['default_value'] == $field_settings['returnval']) $chk = checked('checked'); else $chk = '';
							$defval = isset( $field_settings['returnval'] ) ? $field_settings['returnval'] : esc_attr( $field_key );
							echo '<p><label>'.$field_settings['title'].'</label> ';
							echo '<input name="' . esc_attr( $field_key ) . '"  value="' . $defval . '" type="checkbox" id="' . esc_attr( $field_key ) . '" class="dtthemes_fws_option' . $content_class . '" '.$chk.' />';
							echo '</p>';
							break;
							
						case 'upload':
							echo '<p><label>'.$field_settings['title'].'</label> ';
							echo '<input name="' . esc_attr( $field_key ) . '" type="text" id="' . esc_attr( $field_key ) . '" value="" class="regular-text dtthemes_fws_option dtthemes_upload_field' . $content_class . '" />' . '<a href="#" class="dtthemes_upload_button">' . esc_html__('Upload', 'dt_themes') . '</a>';
							echo '</p>';
							break;
	
						case 'colorpicker':	
							echo '<p><label>'.$field_settings['title'].'</label> ';
							echo "<input type='text' class='dtthemes_fws_option dt-color-field ".$field_key." medium' name='".$field_key."' id='".$field_key."'  />";
							echo '</p>';
							echo '<script type="text/javascript">
									jQuery(document).ready(function($){
										var color_val = jQuery(".dt_active_section .dt_fullwidthsection_data_settings .'.$field_key.'").html();
										jQuery("#'.$field_key.'").val( color_val );
										jQuery("#'.$field_key.'").wpColorPicker();
									});
								</script>';
							break;
							
					}
					
				}
				
		echo '<span id="save_fullwidthoptions" class="dt_button save_fullwidthoptions">'.__('Save Changes', 'dt_themes').'</span>';
		echo '</form>';
	}
}


/**
 * A action hook and funtion to show modules in pop up panel
 */
add_action( 'wp_ajax_dt_show_modules_panel', 'dt_show_modules_panel' );
function dt_show_modules_panel(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	dt_generate_modules_panel();
	
	die();
}

if ( ! function_exists('dt_generate_modules_panel') ){
	function dt_generate_modules_panel(  ){
		global $dtthemes_modules, $default_widgets;
		echo '<div class="dt_dialog_handle">' . esc_html( __('Add Modules', 'dt_themes') ) . '</div><a href="#" id="dt_close_dialog_settings"></a>';
		echo '<form id="dt_popup_modules">'
				. '<a href="#" id="dt_close_popup_modules"></a>';
			
					dt_get_modules_menu(false);
					dt_get_modules_list('dt_popup_module');

		echo '</form>';
	}
}


/**
 * A action hook and funtion to show custom css options
 */
add_action( 'wp_ajax_dt_show_customcss_panel', 'dt_show_customcss_panel' );
function dt_show_customcss_panel(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);
	$dt_post_id = (int) $_POST['dt_post_id'];
	$dt_customcss_data = isset($_POST['dt_customcss_data']) ? $_POST['dt_customcss_data'] : '';
	if($dt_customcss_data == '') $dt_customcss_data = get_post_meta( $dt_post_id, '_dt_customcss_data', true );

	dt_generate_customcss_options( $dt_customcss_data );
	
	die();
}

if ( ! function_exists('dt_generate_customcss_options') ){
	function dt_generate_customcss_options( $cssdata ){

		echo '<div class="dt_dialog_handle">' . esc_html( __('Add Custom CSS', 'dt_themes') ) . '</div><a href="#" id="dt_close_dialog_settings"></a>';
		echo '<form id="dt_customcss_settings">'
				. '<a href="#" id="dt_close_customcss_settings"></a>'
				. '<p><textarea cols="70" rows="10" name="dt_customcss_data" id="dt_customcss_data" class="text">'.$cssdata.'</textarea></p>'
			    . '<span id="save_customcss" class="dt_button save_customcss">'.__('Save Changes', 'dt_themes').'</span>';
		echo '</form>';
	}
}


/**
 * A action hook to save custom css data
 */
add_action( 'wp_ajax_dt_save_customcss_data', 'dt_save_customcss_data' );
function dt_save_customcss_data(){

	if ( ! wp_verify_nonce( $_POST['dt_load_nonce'], 'dt_load_nonce' ) ) die(-1);

	$dt_post_id = (int) $_POST['dt_post_id'];
	$dt_customcss_data = isset($_POST['dt_customcss_data']) ? $_POST['dt_customcss_data'] : '';
	update_post_meta( $dt_post_id, '_dt_customcss_data', $dt_customcss_data );
	
	die();
}


/**
 * A action hook to display custom css data in corresponding pages
 */
add_action('wp_head', 'dtthemes_add_customcss', 9);
function dtthemes_add_customcss() {
	
	if(get_post() != '') {
		$builder_enable = get_post_meta( get_the_ID(), '_dt_enable_builder', true );
		if($builder_enable == 1):
			$dt_post_id = get_the_ID();
			$output = get_post_meta( $dt_post_id, '_dt_customcss_data', true );;
	
			if (!empty($output)) :
				$output = "\r".'<style type="text/css">'."\r".$output."\r".'</style>'."\r";
				echo $output;
			endif;
		endif;
	}
	
}

function dtthemes_color_picker($name, $value) {
	global $wp_version;
	
	$output = "";	
	if (( float ) $wp_version >= 3.5) :
		$output .= "<input type='text' class='dtthemes_option color-field medium' name='{$name}' id='{$name}' value='{$value}' />";
	 else :
		$output .= "<input type='text' class='medium color_picker_element' name='{$name}' id='{$name}' value='{$value}' />";
		$output .= "<div class='color_picker'></div>";
	endif;
	echo $output;
}


add_filter( 'wp_default_editor', 'dt_force_tmce_editor' );
function dt_force_tmce_editor( $editor_mode ) {
	return 'tinymce';
}

function dt_mb_unserialize($string) {
	$string = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $string);
	return unserialize($string);
}
?>