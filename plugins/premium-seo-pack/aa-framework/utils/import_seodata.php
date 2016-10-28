<?php
/*
* Define class pspImportSeoData
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspImportSeoData') != true) {
    class pspImportSeoData
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
		
	
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
			$this->the_plugin = $parent;
			add_action('wp_ajax_pspimportSEOData', array( $this, 'import_seo_data' ));
        }
        
		/**
	    * Singleton pattern
	    *
	    * @return pspFileEdit Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        return self::$_instance;
	    }
	    
	    
		public function import_seo_data() {
			global $wpdb;
			
			$__importSEOFields = array(
				'WooThemes SEO Framework' => array(
					'meta title' 			=> 'seo_title',
					'meta description' 		=> 'seo_description',
					'meta keywords' 		=> 'seo_keywords'
				),
				'All-in-One SEO Pack - old version' => array(
					'meta title' 			=> 'title',
					'meta description' 		=> 'description',
					'meta keywords' 		=> 'keywords'
				),
				'All-in-One SEO Pack' => array(
					'meta title' 			=> '_aioseop_title',
					'meta description' 		=> '_aioseop_description',
					'meta keywords' 		=> '_aioseop_keywords'
				),
				'SEO Ultimate' => array(
					'meta title' 			=> '_su_title',
					'meta description' 		=> '_su_description',
					'meta keywords' 		=> '_su_keywords',
					'noindex' 				=> '_su_meta_robots_noindex',
					'nofollow' 				=> '_su_meta_robots_nofollow'
				),
				'Yoast WordPress SEO' => array(
					'meta title' 			=> '_yoast_wpseo_title',
					'meta description' 		=> '_yoast_wpseo_metadesc',
					'meta keywords' 		=> '_yoast_wpseo_metakeywords',
					'noindex' 				=> '_yoast_wpseo_meta-robots-noindex',
					'nofollow' 				=> '_yoast_wpseo_meta-robots-nofollow',
					'canonical url' 		=> '_yoast_wpseo_canonical',
					'focus keyword'			=> '_yoast_wpseo_focuskw',
					'sitemap include'		=> '_yoast_wpseo_sitemap-include',
					'sitemap priority'		=> '_yoast_wpseo_sitemap-prio',
					'facebook description'	=> '_yoast_wpseo_opengraph-description'
				)
			);
			
			$__pspSEOFields = array(
				'meta title' 			=> array( array( 'title', 'psp_meta' ) ),
				'meta description' 		=> array( array( 'description', 'psp_meta' ) ),
				'meta keywords' 		=> array( array( 'keywords', 'psp_meta' ) ),
				'noindex' 				=> array( array( 'robots_index', 'psp_meta' ) ),
				'nofollow' 				=> array( array( 'robots_follow', 'psp_meta' ) ),
				'canonical url' 		=> array( array( 'canonical', 'psp_meta' ) ),
				'focus keyword' 		=> array( array( 'focus_keyword', 'psp_meta' ), array( 'psp_kw' ) ),
				'sitemap include' 		=> array( array( 'psp_sitemap_isincluded' ) ),
				'sitemap priority' 		=> array( array( 'priority', 'psp_meta' ) ),
				'facebook description'	=> array( array( 'facebook_desc', 'psp_meta' ) )
			);
			
			$__convertValues = array(
				'noindex' => array(
					0		=> 'default',
					1		=> 'noindex',
					2		=> 'index'
				),
				'nofollow' => array(
					0		=> 'follow',
					1		=> 'nofollow'
				),
				'sitemap include' => array(
					'-'			=> 'default',
					'always'	=> 'always_include',
					'never'		=> 'never_include'
				)
			);

			$ret = array(
				'status'		=> 'invalid',
				'html'			=> 'No updates made.',
				'dbg'			=> ''
			);

			// import meta data!
			$pluginFrom = isset($_REQUEST['from']) ? str_replace('+', ' ', trim($_REQUEST['from'])) : '';
			$subaction = isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : '';
			$rowsperstep = isset($_REQUEST['rowsperstep']) ? $_REQUEST['rowsperstep'] : 10;
			$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 0;

			if ( empty($pluginFrom) ) // validate selection!
				return die(json_encode($ret));

			// execute import!
			$pluginFrom = $__importSEOFields[ "$pluginFrom" ];
			$fromMetaKeys = array_values($pluginFrom);
			
			if ( !empty($subaction) && $subaction == 'nbres' ) {
				// number of rows: get all post Ids which have metas from old plugin!
				$sql_nb = "select count(a.post_id) as nb from $wpdb->postmeta as a where 1=1 and a.meta_key regexp '^(" . implode('|', $fromMetaKeys) . ")';";
				$res_nb = $wpdb->get_var( $sql_nb );
				
				return die(json_encode(array_merge($ret, array(
					'status'		=> 'valid',
					'nbrows'		=> $res_nb,
					'html'			=> sprintf( __('Total rows: %s.', $this->the_plugin->localizationName), $res_nb )
				))));
			}

			// get all post Ids which have metas from old plugin!
			$sql = "select a.post_id, a.meta_key, a.meta_value from $wpdb->postmeta as a where 1=1 and a.meta_key regexp '^(" . implode('|', $fromMetaKeys) . ")' order by a.post_id asc, a.meta_key asc limit $step, $rowsperstep;";
			$res = $wpdb->get_results( $sql );
			$ret['dbg'] = $res;
			if ( is_null($res) || empty($res) )
				return die(json_encode($ret));

			// statistics array!
			$nbPostsUpdated = 0; $nbPostsOptimized = 0;

			$current_post_id = reset( $res );
			$current_post_id = $current_post_id->post_id;

			$pspMetaValues = array();
			$i = 0; $resFound = count($res);
			foreach ( $res as $__k => $meta ) {

				$i++;
				if ( $current_post_id != $meta->post_id || $i == $resFound ) { // next post Id meta rows

					if ( !empty($pspMetaValues) && is_array($pspMetaValues) ) {

						$pspUpd = 0;
						foreach ( $pspMetaValues as $psp_mk => $psp_mv) { // update metas for current post Id
							
							$psp_current = get_post_meta( $current_post_id, $psp_mk, true);
							if ( empty($psp_current) ) { // update empty meta values!
								
								$updStat = update_post_meta( $current_post_id, $psp_mk, $psp_mv );
	
								if ( $updStat === true || (int) $updStat > 0 ) $pspUpd++;
							} else {
	
								if ( is_array($psp_current) ) { // update only array serialized meta values!
	
									$psp_mv = array_merge( (array) $psp_mv, (array) $psp_current);
									$pspMetaValues[ "$psp_mk" ] = $psp_mv;
									update_post_meta( $current_post_id, $psp_mk, $psp_mv );
	
									if ( $updStat === true || (int) $updStat > 0 ) $pspUpd++;
								}
							}
						}

						if ( $pspUpd ) $nbPostsUpdated++;
						
						// psp specific meta!
						if ( $this->import_seo_data_pspExtra( $current_post_id, $pspMetaValues ) )
							$nbPostsOptimized++;

					}
					//var_dump('<pre>',$current_post_id, $pspMetaValues ,'</pre>'); 

					$current_post_id = $meta->post_id;
					$pspMetaValues = array(); // reset metas to be used by next post Id

				}

				// current post Id meta rows
				$alias = array_search( $meta->meta_key, $pluginFrom );
				$pspMetaKey = $__pspSEOFields[ "$alias" ];

				if ( !is_array($pspMetaKey) || count($pspMetaKey) < 1 ) continue 1;

				foreach ( $pspMetaKey as $psp_ka => $psp_kb ) {

					if ( isset($__convertValues[ "$alias" ])
						&& isset($__convertValues[ "$alias" ][ "{$meta->meta_value}"]) )
						$meta->meta_value = $__convertValues[ "$alias" ][ "{$meta->meta_value}"];

					if ( count($psp_kb) == 2 )
						$pspMetaValues[ "{$psp_kb[1]}" ][ "{$psp_kb[0]}" ] = $meta->meta_value;
					else
						$pspMetaValues[ "{$psp_kb[0]}" ] = $meta->meta_value;
				}
			}

			$msg = array();
			$msg[] = sprintf( __('Rows: <strong>%s - %s</strong>.', $this->the_plugin->localizationName), $step, ( $step + $rowsperstep - 1) );
			$msg[] = sprintf( __('Total number of posts updated: <strong>%s</strong>.', $this->the_plugin->localizationName), $nbPostsUpdated );
			$msg[] = sprintf( __('Total number of posts optimized: <strong>%s</strong>.', $this->the_plugin->localizationName), $nbPostsOptimized );

			return die(json_encode(array_merge($ret, array(
				'status'		=> 'valid',
				'html'			=> implode('<br />', $msg)
			))));
		}
		
		private function import_seo_data_pspExtra( $post_id = 0, $meta = array() ) {
			
			if ( $post_id <= 0 ) return false;
			if ( empty($meta) ) return false;
			
			$post_metas = get_post_meta( $post_id, 'psp_meta', true);

			$post_metas = array_merge(array(
				'title'				=> '',
				'description'		=> '',
				'keywords'			=> '',
				'focus_keyword'		=> '',
	
				'facebook_isactive' => '',
				'facebook_titlu'	=> '',
				'facebook_desc'		=> '',
				'facebook_image'	=> '',
				'facebook_opengraph_type'	=> '',
				
				'robots_index'		=> '',
				'robots_follow'		=> '',
	
				'priority'			=> '',
				'canonical'			=> ''
			), $post_metas);

			// include on page optimization module!
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/on_page_optimization/init.php');
			$pspOnPageOptimization = new pspOnPageOptimization();

			$_REQUEST = array(
				'psp-field-title'					=> $post_metas['title'],
				'psp-field-metadesc'				=> $post_metas['description'],
				'psp-field-metakewords'				=> $post_metas['keywords'],
				'psp-field-focuskw'					=> $post_metas['focus_keyword'],
	
				'psp-field-facebook-isactive'		=> $post_metas['facebook_isactive'],
				'psp-field-facebook-titlu'			=> $post_metas['facebook_titlu'],
				'psp-field-facebook-desc'			=> $post_metas['facebook_desc'],
				'psp-field-facebook-image'			=> $post_metas['facebook_image'],
				'psp-field-facebook-opengraph-type'	=> $post_metas['facebook_opengraph_type'],
	
				'psp-field-meta_robots_index'		=> $post_metas['robots_index'],
				'psp-field-meta_robots_follow'		=> $post_metas['robots_follow'],
	
				'psp-field-priority-sitemap'		=> $post_metas['priority'],
				'psp-field-canonical'				=> $post_metas['canonical']
			);
			$pspOnPageOptimization->optimize_page( $post_id );
			
			return true;
		}
    }
}

// Initialize the pspImportSeoData class
//$pspImportSeoData = new pspImportSeoData();
