<?php
/*
* Define class pspSEOImages
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSEOImages') != true) {
    class pspSEOImages
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
		private $special_tags = array(
			'{focus_keyword}',
			'{title}',
			'{image_name}',
			'{nice_image_name}',
			'{category}'
		);

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/seo_friendly_images/';
			$this->module = $this->the_plugin->cfg['modules']['seo_friendly_images'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'seo_friendly_images' );
			
			if ( !$this->the_plugin->verify_module_status( 'seo_friendly_images' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true )
					add_filter('the_content', array( $this, 'add_images_tags'));
			}
        }
		
		public function add_images_tags( $the_content )
		{
			global $post;

			// php query class
			require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );  
			
			$psp_meta = get_post_meta( $post->ID, 'psp_meta', true );
			
			if( trim($the_content) != "" ){
				if ( !empty($this->the_plugin->charset) )
					$doc = pspphpQuery::newDocument( $the_content, $this->the_plugin->charset );
				else
					$doc = pspphpQuery::newDocument( $the_content );
				
				foreach( pspPQ('img') as $img ) {
					// cache the img object
					$img = pspPQ($img); 
					
			    	$url = $img->attr('src');
					$image_name = '';
					if( trim($url) != "" ){
						$image_name = explode( '.', end( explode( '/', $url ) ) );
						$image_name = $image_name[0]; 
					}
					
			    	$alt = $img->attr('alt');
					
			    	$title = $img->attr('title');
					
					// setup the default settings
					$new_alt = isset($this->settings["image_alt"]) ? $this->settings["image_alt"] : '';
					$new_title = isset($this->settings["image_title"]) ? $this->settings["image_title"] : '';
					
					if( isset($this->settings['keep_default_alt']) && trim($this->settings['keep_default_alt']) != "no" ){
						$new_alt = $alt . ' ' . $new_alt;
					}
					if( isset($this->settings['keep_default_title']) && trim($this->settings['keep_default_title']) != "no" ){
						$new_title = $title . ' ' . $new_title;
					}
						
					// make the replacements 
					foreach ($this->special_tags as $tag) { 
						if( $tag == '{title}' ) {
							if( preg_match("/$tag/iu", $this->settings["image_alt"]) ) {
								$new_alt = str_replace( $tag, $post->post_title, $new_alt ); 
							}
							
							if( preg_match("/$tag/iu", $this->settings["image_title"]) )
								$new_title = str_replace( $tag, $post->post_title, $new_title );
						}
						
						elseif( $tag == '{image_name}' ) {
							if( preg_match("/$tag/iu", $this->settings["image_alt"]) )
								$new_alt = str_replace( $tag, $image_name, $new_alt );
							
							if( preg_match("/$tag/iu", $this->settings["image_title"]) )
								$new_title = str_replace( $tag, $image_name, $new_title );
						}
						
						elseif( $tag == '{focus_keyword}' ) {
							if ( isset($psp_meta['focus_keyword']) && trim($psp_meta['focus_keyword']) != "" ) {
								if( preg_match("/$tag/iu", $this->settings["image_alt"]) )
									$new_alt = str_replace( $tag, $psp_meta['focus_keyword'], $new_alt );
								
								if( preg_match("/$tag/iu", $this->settings["image_title"]) )
									$new_title = str_replace( $tag, $psp_meta['focus_keyword'], $new_title );
							} else {
								$new_alt = str_replace( $tag, '', $new_alt );
								$new_title = str_replace( $tag, '', $new_title );
							}
						}
						
						elseif( $tag == '{nice_image_name}' ) {  
							$image_name = preg_replace("/[^a-zA-Z0-9\s]/", " ", $image_name);
							$image_name = preg_replace('/\d{1,4}x\d{1,4}/i',  '', $image_name);

							if( preg_match("/$tag/iu", $this->settings["image_alt"]) )
								$new_alt = str_replace( $tag, $image_name, $new_alt );
							
							if( preg_match("/$tag/iu", $this->settings["image_title"]) )
								$new_title = str_replace( $tag, $image_name, $new_title );
						}
					}
					
					// if the alt / title was changed
					if( $new_alt != $alt )
						$img->attr( 'alt', trim($new_alt) );
					
					if( $new_title != $title )
						$img->attr( 'title', trim($new_title) );
			    }
					
				return do_shortcode($doc->html());
				
			}else{
				return do_shortcode($the_content);
			}
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

// Initialize the pspSEOImages class
//$pspSEOImages = new pspSEOImages();
$pspSEOImages = pspSEOImages::getInstance();