<?php
/*
* Define class aafShortcodes
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('aafShortcodes') != true) {
    class aafShortcodes
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		static protected $_instance;
		
		protected $module_folder = '';
		protected $module_folder_path = '';


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
        	$this->the_plugin = $parent;

			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'aa-framework/shortcodes/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'aa-framework/shortcodes/';
			
			//if ( $this->the_plugin->dev != 'gimi' ) return ;

        	add_action('admin_init', array( $this, 'admin_init') );
			//add_action('admin_head', array( $this, 'admin_init') );
        	add_action('init', array( $this, 'init') );
        }

        public function init() {

			global $typenow;
        	if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') )
        		&& get_user_option('rich_editing') ) {
 
 				// verify the post type
    			//if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        		//	return;

        		add_filter( 'mce_external_plugins', array( $this, 'tinymce_add_plugin' ) );
        		add_filter( 'mce_buttons', array( $this, 'tinymce_add_buttons' ) );
        		return true;
        	}
        	return false;
        }
        
        public function tinymce_add_plugin( $plugin_array ) {

        	// array key must be the name used in js plugin!
        	if ( floatval(get_bloginfo('version')) >= 3.9){
        		$plugin_array['aafShortcodes'] = $this->module_folder . 'js/tinymce.plugin.v4.js';
			} else {
				$plugin_array['aafShortcodes'] = $this->module_folder . 'js/tinymce.plugin.js';
			}
        	return $plugin_array;
        }

        public function tinymce_add_buttons( $buttons ) {

        	$modules = $this->load_modules_active();
        	if ( !is_array($modules) || empty($modules) ) return $buttons;
    
        	/*foreach ( $modules as $module => $val ) {

	        	// buttons must have the same names as used in js plugin!
    	    	//array_push( $buttons, "|", ($this->the_plugin->alias . '_' . $module) );
    	    	array_push( $buttons, ($this->the_plugin->alias . '_' . $module) );
        	}*/
			array_push( $buttons, ($this->the_plugin->alias . '_' . 'editor_shortcodes') );
        	return $buttons;
        }

        public function admin_init() {

        	/*// css
        	wp_enqueue_style( 'psp-shortcodes-popup', $this->module_folder .  'css/tinymce.popup.css', false, '1.0', 'all' );

        	// javascript
        	wp_enqueue_script( 'psp-shortcodes-popup', $this->module_folder . 'js/tinymce.popup.js', array('jquery'), '1.0', false );*/
        	
        	wp_enqueue_style( 'psp-shortcodes-plugin', $this->module_folder .  'css/tinymce.plugin.css', false, '1.0', 'all' );
        	
			// Localizes a script, but only if script has already been added. Can also be used to include arbitrary Javascript data in a page.
			$localize = array();
			$localize['plugin_alias'] = $this->the_plugin->alias;
			$localize['plugin_url'] = $this->module_folder;
			$localize['plugin_btn_name'] = ($this->the_plugin->alias . '_' . 'editor_shortcodes');
			$localize['plugin_btn_title'] = __('Premium SEO Pack Shortcodes', $this->the_plugin->localizationName);
			
        	$modules = $this->load_modules_active();
        	if ( is_array($modules) && count($modules) > 0 ) {
        		$localize['modules'] = $modules;
        	}
  
			wp_localize_script( 'jquery', 'aafShortcodes', $localize );
        }

        public function load_modules_active() {

        	$mactive = $this->the_plugin->cfg['activate_modules'];
        	if ( count($mactive) <= 0 ) {
        		return array();
        	}
  
        	$ret = array();
        	foreach ( $mactive as $alias => $a ) { // foreach main

				$tryed_module = $this->the_plugin->cfg['modules'][ "$alias" ];
				if( isset($tryed_module) && count($tryed_module) > 0 ) {

					$new = array();

					$config = $tryed_module[ "$alias" ];
					if ( isset($config['shortcodes_btn']) && !empty($config['shortcodes_btn']) ) {
						$new['button'] 		= $config['shortcodes_btn'];
						$new['folder_uri'] 	= $tryed_module['folder_uri'];
					}

					// Turn on output buffering
					ob_start();

					// shortcodes options
					$opt_file_path = $tryed_module['folder_path'] . 'options-shortcodes.php';
					if( is_file($opt_file_path) ) {
						require( $opt_file_path  );
					}
					$options = ob_get_clean(); //copy current buffer contents into variable and delete current output buffer

					if(trim($options) != "") {
						$options = json_decode($options, true);
					}

					if ( is_array($options) && !empty($options) > 0 ) {

						foreach ($options as $opt ) {
							foreach ( $opt as $key => $shortcode ) {

								if ( !isset($new['shortcodes']) ) $new['shortcodes'] = array();
								$new['shortcodes'][] = array(
									'name'				=> $key,
									'title' 			=> $shortcode['title'],
									'exclude_empty'	=> ( isset($shortcode['exclude_empty_fields']) && ( $shortcode['exclude_empty_fields'] || $shortcode['exclude_empty_fields'] == 'true' ) ? 'yes' : 'no' )
								);
							}
						}
					}

					if ( isset($new['button']) && isset($new['shortcodes']) ) {
						$ret[ "$alias" ] = $new;
					}

				}
        	} // end foreach main
        	return $ret;
        }


        /**
	    * Singleton pattern
	    *
	    * @return Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        return self::$_instance;
	    }
    }
}

// Initialize the aafShortcodes class
//$aafShortcodes = new aafShortcodes();