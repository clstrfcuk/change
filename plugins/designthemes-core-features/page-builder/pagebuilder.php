<?php

if (! class_exists ( 'DTPageBuilder' )) {

	class DTPageBuilder {

		/**
		 */
		function __construct() {
			
			require_once DESIGNTHEMES_PB_DIR . 'functions.php';
			
			// Add Hook into the 'init()' action
			add_action ( 'admin_init', array (
					$this,
					'dtthemes_modules_init' 
			) );
			
			// Add Hook into the 'admin_init()' action
			add_action ( 'admin_init', array (
					$this,
					'dt_admin_init' 
			) );


			add_action ( 'save_post', array (
					$this,
					'save_post_meta' 
			), 10, 2 );
			
			
		}

	
		/**
		 * A function hook that the WordPress core launches at 'admin_init' points
		 */
		function dt_admin_init() {

			add_action ( 'add_meta_boxes', array (
					$this,
					'dt_add_custom_metabox' 
			) );
			
			add_action ( 'dt_before_page_builder', array (
					$this,
					'dt_disable_builder_option' 
			) );
			
		}


		/**
		 * To add metabox for all selected post types
		 */
		function dt_add_custom_metabox(){
			global $default_posttypes, $post, $theme_name;
		
			$pboptions = get_option('mytheme');
			if ( isset($pboptions['pagebuilder']) )
				$dtthemes_active_posttypes = $pboptions['pagebuilder'];
			else
				$dtthemes_active_posttypes = $default_posttypes;
			
			$post_types = isset( $dtthemes_active_posttypes ) ? (array) $dtthemes_active_posttypes : array();
			$ptid = $post->ID;
			
			foreach ( $post_types as $post_type ){
				add_meta_box( 'dtthemes-metabox', $theme_name.__( ' Page Builder', 'dt_themes' ), array ($this, 'dtthemes_custom_metabox_layout'), $post_type, 'normal', 'high', array( 'id' => $ptid) );
			}

		}

		function dtthemes_custom_metabox_layout( $post, $data ){
			global $postid;
			$postid = $data['args']['id'];
			require_once DESIGNTHEMES_PB_DIR . 'metabox.php';
		}


		function save_post_meta( $post_id, $post ){
				remove_action('save_post', array ($this, 'save_post_meta' ));

				global $pagenow;
			
				if ( 'post.php' != $pagenow ) return $post_id;
			
				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
					return $post_id;
			
				$post_type = get_post_type_object( $post->post_type );
				if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
					return $post_id;

				if ( ! isset( $_POST['dt_builder_settings_nonce'] ) || ! wp_verify_nonce( $_POST['dt_builder_settings_nonce'], basename( __FILE__ ) ) )
					return $post_id;

					
				if ( isset( $_POST['dtthemes-enable-builder'] ) ) {
					update_post_meta( $post_id, '_dt_enable_builder', 1 );
					$builder_layout = get_post_meta( get_the_ID(), '_dt_builder_settings', true );
					if(empty($builder_layout )) {
						$dt_settings = get_post_meta( $post_id );
						$builder_layout = dt_mb_unserialize($dt_settings['_dt_builder_settings'][0]);
					}
					$builder_layout = $builder_layout['layout_shortcode'];
				} else {
					update_post_meta( $post_id, '_dt_enable_builder', 0 );
					$dt_post = get_post($post_id); 
					$dt_contet = $dt_post->post_content ;
					$builder_layout = $dt_contet;
				}
		
				$my_post = array(		  
				  'ID'           => get_the_ID(),
				  'post_content' => $builder_layout
				);
		
				wp_update_post( $my_post );

				add_action('save_post', array ($this, 'save_post_meta' ), 10, 2);
				
		}


		/**
		 * A function hook that the WordPress core launches at 'init' points
		 */
		function dtthemes_modules_init(){
			
			global $dtthemes_columns, $dtthemes_sample_layouts, $theme_name, $dt_widgets;

			$dtthemes_columns = apply_filters( 'dtthemes_columns', $dtthemes_columns );

			global $wp_widget_factory;
				
			foreach($wp_widget_factory->widgets as $class => $info){
			
				$widget = new $class();
				
				$id = $info->id;
				
				$wt_name = $widget->name;
				$wt_wpname = $widget->widget_options['classname'];
				$wt_class = $class;
				
				$wt_tooltip = '';
				if(isset($widget->widget_options['description'])) $wt_tooltip = $widget->widget_options['description'];
			
				ob_start();
				$widget->form(array());
				$form = ob_get_clean();
				
				$exp = preg_quote($widget->get_field_name('____'));
				$exp = str_replace('____', '(.*?)', $exp);
				$dt_form = preg_replace('/'.$exp.'/', "widgets[$id][$1]", $form);
				$dt_form = str_replace('<br>', '', $dt_form);
				
				$doc = new DomDocument();
				$file = @$doc->loadHTML($dt_form);
				$opt = array();
	
				$inputtag = $doc->getElementsByTagName('input');
				foreach($inputtag AS $item)
				{
					$prev_class = $item->getAttribute('class');
					$item->setAttribute('class', $prev_class.' dtthemes_widget_attr');
					
					$input_type = $item->getAttribute('type'); 
					if($input_type == 'checkbox') {
						$e = $item->parentNode;
						$e->setAttribute('class', 'dtthemes_checkbox');
					}
				}
				
				$textareatag = $doc->getElementsByTagName('textarea');
				foreach($textareatag AS $item)
				{
					$prev_class = $item->getAttribute('class');
					$item->setAttribute('class', $prev_class.' dtthemes_widget_attr');
				}
	
				$selecttag = $doc->getElementsByTagName('select');
				foreach($selecttag AS $item)
				{
										
					$sname = $item->getAttribute('name');
					$sname = str_replace('[]', '', $sname);
					$item->setAttribute('name', $sname);
	
					$sid = $item->getAttribute('id');
					$sid = str_replace('[]', '', $sid);
					$item->setAttribute('id', $sid);
					
					$prev_class = $item->getAttribute('class');
					if($item->getAttribute('multiple') == 'multiple') $prev_class .= ' dt_multiselect';
					$item->setAttribute('class', $prev_class.' dtthemes_widget_attr');
										
				}
				
				$modified_form = @$doc->saveHTML();
				
				$dt_widgets[$wt_class] = array( 'name' => $wt_name, 'wpname' => $wt_wpname, 'wpid' => $id, 'tooltip' => $wt_tooltip, 'form' => $modified_form );
								
			}

			$dtthemes_settings = get_option( 'dtthemes_settings' );
		
			if ( isset( $dtthemes_settings['custom_sample_layouts'] ) )
				$dtthemes_sample_layouts = array_merge( (array) $dtthemes_sample_layouts, (array) $dtthemes_settings['custom_sample_layouts'] );
	
			$dtthemes_sample_layouts = apply_filters( 'dtthemes_sample_layouts', $dtthemes_sample_layouts );
			
		}

		function dt_disable_builder_option(){

			global $postid, $enable_pb_default;
			$dt_builder_enable = get_post_meta( $postid, '_dt_enable_builder', true );

			$pboptions = get_option('mytheme');
			$pboption = isset($pboptions['pagebuilder']) ? $pboptions['pagebuilder'] : false;
			if(!isset($dt_builder_enable)) {
				if($pboption != false && $pboption['enable-pagebuilder'] == true) $dt_builder_enable = 1;
				else $dt_builder_enable = $enable_pb_default;
			}

			wp_nonce_field( basename( __FILE__ ), 'dt_builder_settings_nonce' );
		
			if($dt_builder_enable == 1) {
				$switch_cls = 'chkbx-switch-on'; $switch_chk = 'checked="checked"'; 
			} else {
				$switch_cls = 'chkbx-switch-off'; $switch_chk = ''; 
			}
			
			echo '<div class="dt_builder_option">'
					. __( 'Enable page builder', 'dt_themes' ) .' <div data-for="dtthemes-enable-builder" id="dt_enable_builder" class="chkbx-switch '.$switch_cls.'"></div>' 
					. '<label for="dt_builder_enable" class="selectit">'
						. '<input name="dtthemes-enable-builder" type="checkbox" id="dtthemes-enable-builder" '.$switch_chk.' /> '
					. ' </label>'
						
				. '</div>';
		}


	}
}
?>