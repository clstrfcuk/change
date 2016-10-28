<?php
/*
* Define class pspRichSnippets
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspRichSnippets') != true) {
    class pspRichSnippets
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		protected $module_folder = '';
		protected $module_folder_path = '';
		private $module = '';
		
		protected $settings = array();
		
		static protected $_instance;
		
		protected $shortcode = null;
		protected $shortcodeCfg = array();


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/rich_snippets/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/';
			$this->module = $this->the_plugin->cfg['modules']['rich_snippets'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'rich_snippets' );

			if ( !$this->the_plugin->verify_module_status( 'rich_snippets' ) ) ; //module is inactive
			else {
				$this->init();
			}
        }
        
        
        public function init() {
        	$this->the_plugin->loadRichSnippets('init');
        }
        
        protected function shortcode_cfg( $shortcode = null, $cfg = array() ) {
        	
        	$this->shortcode = $shortcode;
        	$this->shortcodeCfg  = $cfg;
        }

        public function shortcode_execute( $html = array(), $atts = array(), $content = null ) {

        	$ret = array();
        	if ( ( $header = $this->shortcode_header( $this->shortcodeCfg['execute'] ) ) != '' ) $ret[] = $header;

        	$ret[] = implode(PHP_EOL, $html);
        	
			if ( ( $footer = $this->shortcode_footer( $this->shortcodeCfg['execute'] ) ) != '' ) $ret[] = $footer;
			return implode("\n", $ret);
        }
        
        protected function shortcode_header( $execute = true ) {

			if( !wp_style_is('psp_'.$this->shortcode.'_css') ) {
				wp_enqueue_style( 'psp_'.$this->shortcode.'_css' , $this->module_folder . 'app.css' );
			}

        	if ( $execute !== true ) return '';

        	$ret = array();
        	
			$ret[] = '
				<!--begin psp rich snippets shortcode : ' . ($this->shortcode) . '-->
				<div class="schema_block schema_'.$this->shortcodeCfg['type'].'">
			';

			$ret = implode('', $ret);
        	return $ret;
        }
        
        protected function shortcode_footer( $execute = true ) {

        	if ( $execute !== true ) return '';

        	$ret = array();
        	
			$ret[] = 	'
				</div>
				<!--end psp rich snippets shortcode : ' . ($this->shortcode) . '-->
			';
			$ret = implode('', $ret);
        	return $ret;
        }
        
        protected function shortcode_atts( $atts = array(), $content = null ) {

        	$defaults = array();

        	$module_config = $this->module_folder_path . 'options.php';

        	if( $this->the_plugin->verifyFileExists( $module_config ) ) {

        		// Turn on output buffering
        		ob_start();

        		require( $module_config  );

        		$options = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer

        		if(trim($options) != "") {
        			$options = json_decode($options, true);

        			if ( is_array($options) && !empty($options) > 0 ) {
        				$options = $options[0];
        				$options = reset($options);
        				$option = $options['elements'];
        				
        				if ( count($option) > 0 ) {
        					foreach ( $option as $key => $val ) {
        						//$defaults[ "$key" ] = $val['std'];
        						$defaults[ "$key" ] = '';
        					}
        				}
        			}
        		}
        	}
        	return $this->safeBoolean( shortcode_atts( $defaults, $atts ) );
        }

		protected function safeBoolean( $atts = array() ) {
			
			if ( !is_array($atts) || empty($atts) ) return array();

			foreach ( $atts as $key => $value ) {
				
				if ( preg_match('/^show_/i', $key) > 0 ) {

					$atts[ "$key" ] = (bool) $value;
					if ( $value === true || $value === 'true' )
						$atts[ "$key" ] = true;
					if ( $value === false || $value === 'false' )
						$atts[ "$key" ] = false;
				}
			}
			return $atts;
		}
		
		protected function getMultipleValues( $att='' ) {
			
			if ( empty($att) ) return array();
			$arr = array();
			
			$__tmp = explode(';;', $att);
			foreach ( $__tmp as $key => $value ) {
				if ( !empty($value) ) $arr[ $key ] = $value;
			}
			return $arr;
		}
        

		/**
	    * Singleton pattern
	    *
	    * @return pspSEOImages Singleton instance
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

// Initialize the pspRichSnippets class
//$pspRichSnippets = new pspRichSnippets();
$pspRichSnippets = pspRichSnippets::getInstance();