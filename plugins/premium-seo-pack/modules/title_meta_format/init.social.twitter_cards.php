<?php
/*
* Define class pspSocialTwitterCards
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSocialTwitterCards') != true) {
    class pspSocialTwitterCards extends pspTitleMetaFormat
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $plugin_settings = array();

		private $module_folder = '';

		static protected $_instance;
		
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			parent::__construct(); //init page types array!

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/title_meta_format/';
			
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_title_meta_format' );
			
			if ( !$this->the_plugin->verify_module_status( 'title_meta_format' ) ) ; //module is inactive
			else {
				$this->init();
			}
        }
        

        /**
         * Head Filters & Init!
         *
         */
		public function init() {
			if ( isset($this->plugin_settings['psp_twc_use_meta']) && $this->plugin_settings['psp_twc_use_meta']=='no' )
				return true;

			add_action( 'premiumseo_twitter_cards', array( &$this, 'twitter_cards_tags' ), 6 );

			add_action( 'premiumseo_head', array( &$this, 'make_twitter_cards' ), 30 );
		}
		
		
		/**
		 * social networks (Twitter Cards)
		 */
		public function make_twitter_cards() {
			wp_reset_query();
			do_action( 'premiumseo_twitter_cards' );
		}
		
		//tags
		public function twitter_cards_tags($ret=false) {
			global $wp_query;
			
			$post = $wp_query->get_queried_object();

			$opt = $this->plugin_settings;
			
			$pm = array();
			// meta info!
			if ( is_singular() || $this->the_plugin->_is_blog_posts_page() ) {
				
				$pm = get_post_meta( $post->ID, 'psp_meta', true );
				
			} else if ( is_home() || is_front_page() ) { //homepage

				$pm = $this->the_plugin->get_theoption('psp_title_meta_format');
			} else if ( is_category() || is_tag() || is_tax() ) { //taxonomy data!
				
				$__objTax = (object) array('term_id' => $post->term_id, 'taxonomy' => $post->taxonomy);

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $__objTax );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$pm = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $__objTax, 'psp_meta' );
			}
  
			//if twitter cards are deactivated!
			if ( isset($opt['psp_twc_use_meta']) && $opt['psp_twc_use_meta']=='no' )
				return false;
			
			// Twitter Cards ajax action & public methods!
			require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'utils/twitter_cards.php' );
			$twc = new pspTwitterCards( $this->the_plugin );
			
			$metatags = array();
			if ( is_singular() || $this->the_plugin->_is_blog_posts_page() ) { //post|page|post_type
				
				$metatags = $twc->get_frontend_meta($pm, 'post');

			} else if ( is_home() || is_front_page() ) { //homepage
			
				$metatags = $twc->get_frontend_meta($pm, 'home');

			} else if ( is_category() || is_tag() || is_tax() ) {
				
				$metatags = $twc->get_frontend_meta($pm, 'post');

			} else if ( is_author() ) {
	
			} else {

			}
			
			$metatags['twitter:url'] = $this->the_url();
  
			$metatags = $this->default_meta($metatags, $post, $pm);
			
			// hooks - in case may need to modify this tags!
			//...
			
			// retrieve metatags
			if ( isset($ret) && $ret===true ) {
				return $metatags;
			}
			
			if ( empty($metatags) ) return false;
			
			//make Tags List
			$__listTags = array();
			foreach ($metatags as $__tag => $val) {
				if ( in_array($__tag, array('twitter:image', 'twitter:image:src', 'twitter:image0', 'twitter:image1', 'twitter:image2', 'twitter:image3')) )
					$val = esc_url( $val );
				else
					$val = esc_attr( $val );

				if ( !empty($val) )
					$__listTags[] = '<meta name="' . ($__tag). '" content="' . ($val) . '"/>';
			}

			$__listTags = implode(PHP_EOL, $__listTags);
			echo $__listTags . PHP_EOL;
		}
		
		private function default_meta($metatags, $post, $psp_meta) {
			
			$opt = $this->plugin_settings;
  
			//title
			if ( isset($metatags[ 'twitter:title' ]) && empty($metatags[ 'twitter:title' ]) )
				$metatags[ 'twitter:title' ] = $this->the_title('');
				
			//description
			if ( isset($metatags[ 'twitter:description' ]) && empty($metatags[ 'twitter:description' ]) ) {
				$metatags[ 'twitter:description' ] = $this->the_meta_description( false );
			}
				
			$img_alias = 'twitter:image';
			if ($metatags[ 'twitter:card' ] == 'summary_large_image') {
				$img_alias = 'twitter:image:src'; 
			}
  
			//image
			if ( isset($metatags[ "$img_alias" ]) && empty($metatags[ "$img_alias" ]) ) {
				if ( isset($opt['psp_twc_default_img']) && !empty($opt['psp_twc_default_img']) )
					$metatags[ "$img_alias" ] = $opt['psp_twc_default_img'];

 				if (isset($opt['psp_twc_image_find']) && !empty($opt['psp_twc_image_find']) ) {
	 				// featured image
	 				if ( $opt['psp_twc_image_find'] == 'featured' ) {
						if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
							$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
							$__featured_image = $__featured_image[0];
							if ( isset($__featured_image) && !empty($__featured_image) )
								$metatags[ "$img_alias" ] = $__featured_image;
						}
	 				}
	 				// custom field image
	 				else if ( $opt['psp_twc_image_find'] == 'customfield' ) {
	 					$__custom_image = get_post_meta($post->ID, $opt['psp_twc_image_customfield'], true);
	
						if ( isset($__custom_image) && !empty($__custom_image) )
							$metatags[ "$img_alias" ] = $__custom_image;
	 				}
	 				else if ( $opt['psp_twc_image_find'] == 'content' ) {
	 					$__first_image = $this->get_content_first_image($post);
	 					
						if ( isset($__first_image) && !empty($__first_image) )
							$metatags[ "$img_alias" ] = $__first_image;
	 				}
 				}
			}
			
			//thumb
			if ( isset($metatags[ "$img_alias" ]) && !empty($metatags[ "$img_alias" ]) ) {
				$__do_thumb = 'none';
				if ( isset($opt['psp_twc_thumb_sizes']) && !empty($opt['psp_twc_thumb_sizes']) )
					$__do_thumb = $opt['psp_twc_thumb_sizes'];
				if ( isset($psp_meta['psp_twc_post_thumbsize']) && !empty($psp_meta['psp_twc_post_thumbsize']) )
					$__do_thumb = $psp_meta['psp_twc_post_thumbsize'];
					
				if ( !empty($__do_thumb) && $__do_thumb!='none' ) {
					$metatags[ "$img_alias" ] = $this->build_thumb($metatags[ "$img_alias" ], $__do_thumb);
				}
					
			}

			return $metatags;
		}
		
		private function get_content_first_image($post) {
			$res = preg_match('/<img.*src=[\'"]([^\'"]+)[\'"].*\/?>/iu', $post->post_content, $matches);
			$img = isset($matches[1]) ? $matches[1] : '';
			return $img;
		}
		
		private function build_thumb($image, $size) {
			
			$opt = $this->plugin_settings;
			
			$finalImg = '{plugin_url}timthumb.php?src={img}&amp;w={thumb_w}&amp;h={thumb_h}&amp;zc={thumb_zc}';

			$img_size = explode('x', $size);
			if ( !is_array($img_size) || count($img_size)!=2 ) {
				$img_size = '120x120';
				$img_size = explode('x', $img_size);
			}
				
			$iscrop = isset($opt['psp_twc_thumb_crop']) && $opt['psp_twc_thumb_crop']=='yes' ? true : false;

			$finalImg = str_replace('{plugin_url}', $this->the_plugin->cfg['paths']['plugin_dir_url'], $finalImg);
			$finalImg = str_replace('{img}', $image, $finalImg);
			$finalImg = str_replace('{thumb_w}', (isset($img_size[0]) ? $img_size[0] : 120), $finalImg);
			$finalImg = str_replace('{thumb_h}', (isset($img_size[1]) ? $img_size[1] : 120), $finalImg);
			$finalImg = str_replace('{thumb_zc}', ($iscrop ? 1 : 2), $finalImg);

			return $finalImg;
		}
		
		
		/**
	    * Singleton pattern
	    *
	    * @return pspSocialTwitterCards Singleton instance
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

// Initialize the pspSocialTwitterCards class
$pspSocialTwitterCards = new pspSocialTwitterCards();