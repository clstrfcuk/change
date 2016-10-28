<?php
/*
* Define class pspMisc
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspMisc') != true) {
    class pspMisc
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';
		
		private $settings = array();
		
		static protected $_instance;
		
		static protected $strlen;
		static protected $strtolower;
		
	
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/misc/';
			$this->module = $this->the_plugin->cfg['modules']['misc'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'misc' );

			$this->setStringFunc(); //string function per encoding!
			
			if ( !$this->the_plugin->verify_module_status( 'misc' ) ) ; //module is inactive
			else {
				if ( $this->settings['slug_isactive'] == 'yes' )
					if ( $this->the_plugin->is_admin === true )
						$this->slug_init();
				if ( $this->settings['insert_code_isactive'] == 'yes' )
					if ( $this->the_plugin->is_admin !== true )
						$this->insert_code_init();
			}
        }
        
        /**
         * slug optimization related methods!
         *
         */
        public function slug_init() {
			// add_filter('wp_unique_post_slug', array( &$this, 'slug_optimizer_save2' ), 100, 6);
			add_filter("name_save_pre", array( &$this, 'slug_optimizer_save' ), 10, 1);

			//sanitize when ajax request is made!
			if ( isset($_POST['action']) && $_POST['action'] == 'sample-permalink' ) {
 				//filter is set before sanitize_title_with_dashes
				add_filter('sanitize_title', array( &$this, 'slug_ajax_optimizer' ), 9);
			}
        }
        
        public function slug_ajax_optimizer($slug) {
        	//echo '<script type="text/javascript">console.log("ajax: ", ' . (json_encode(array($slug, $_POST['new_title']) )) . ')</script>';
        	if ( strcmp( $slug, $_POST['new_title'] ) == 0 ) { //slug was empty, so will use post new title as slug
        		return $this->slug_optimizer($slug);
        	}
        	return $slug;
        }
        
        public function slug_optimizer_save($slug) {
        	return $this->slug_optimizer($slug);
        }
        
        public function slug_optimizer_save2($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug='') {
			return $this->slug_optimizer($slug);
        }
        
        /**
         * main slug optimization method
         * from ajax		: no special consideration, the case is treated in ajax optimizer
         * new post action	: clean slug only if it's not manually edited
         * edit post action : never clean slug!
         *
         */
        public function slug_optimizer($slug) {
	       	$separator = ' ';
  
        	//slug ($_POST['post_name']) is manually edited or we are on post edit action!

        	//@slug must be cleaned only on new posts!
        	if ( isset($_POST['post_name']) && !empty($_POST['post_name']))
        		return $slug;
   
 			//slug is empty
        	if ( empty($slug) )
        		$slug = $_POST['post_title']; //use post title
  
			//new post or edit post action!
			//if ( isset($_POST['action']) && $_POST['action']=='editpost' ) 
			//	$separator = '-';
        	
        	$stop_words = $this->settings['slug_stop_words'];
        	$stop_words = array_map('trim', explode(',', $stop_words));

        	$slug = call_user_func( self::$strtolower, stripslashes( $slug ) );
        	$original_slug = $slug;
        	$slug = explode($separator, $slug);
        	$slug = array_filter($slug, array( $this, 'slug_filter_parts' )); //filter smaller slug parts
        	$slug = array_diff($slug, $stop_words); //filter stop words
  
        	if ( empty($slug) ) {
        		return $original_slug;
        	}
        	else {
        		return implode('-', $slug);
        	}
        }
        
        private function slug_filter_parts($val) {
        	return (bool) (call_user_func( self::$strlen, $val ) >= $this->settings['slug_min_chars']);
        }
        
        private function setStringFunc() {
	    	self::$strlen = (function_exists('mb_strlen')) ? 'mb_strlen' : 'strlen';
	    	self::$strtolower = (function_exists('mb_strtolower')) ? 'mb_strtolower' : 'strtolower';
        }
        
        
        /** 
         * insert code
         */
        public function insert_code_init() {
        	add_action( 'premiumseo_head', array( &$this, 'insert_code_head' ), 32);
        	add_action( 'wp_footer', array( &$this, 'insert_code_footer' ), 10);
        }
        
        public function insert_code_head() {
        	$ret = $this->settings['insert_code_head'];
        	if ( isset($ret) && !empty($ret) && $ret!='' ) {
        		echo "<!-- start/ [inserted code] -->" . PHP_EOL;
				echo $ret . PHP_EOL;
				echo "<!-- end/ [inserted_code] -->" . PHP_EOL;
        	}
			return true;
        }
        
        public function insert_code_footer() {
        	$ret = $this->settings['insert_code_footer'];
        	if ( isset($ret) && !empty($ret) && $ret!='' ) {
        		echo "<!-- start/ " . ($this->the_plugin->details['plugin_name']) . " [inserted code] -->" . PHP_EOL;
				echo $ret . PHP_EOL;
				echo "<!-- end/ " . ($this->the_plugin->details['plugin_name']) . " [inserted code] -->" . PHP_EOL;
        	}
			return true;
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

// Initialize the pspMisc class
//$pspMisc = new pspMisc($this->cfg, ( isset($module) ? $module : array()) );
$pspMisc = pspMisc::getInstance( $this->cfg, ( isset($module) ? $module : array()) );