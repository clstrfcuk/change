<?php
/*
* Define class pspOnPageOptimization
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspOnPageOptimization') != true) {
    class pspOnPageOptimization
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

		static protected $_instance;
	
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/on_page_optimization/';
			$this->module = $this->the_plugin->cfg['modules']['on_page_optimization'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));

           		if ( $this->the_plugin->capabilities_user_has_module('on_page_optimization') ) {
					add_action( 'save_post', array( $this, 'auto_optimize_on_save' ));
           		}

				// ajax optimize helper
				add_action('wp_ajax_pspOptimizePage', array( &$this, 'optimize_page' ));
				add_action('wp_ajax_pspGetSeoReport', array( &$this, 'get_seo_report' ));
				add_action('wp_ajax_pspQuickEdit', array( &$this, 'quick_edit_post' ));
			}
        }
        
        /**
         * add Custom Coloumns to pages | posts | custom post types - listing!
         *
         */

        public function page_seo_info() 
        {
	    	$post_types = get_post_types(array(
	    		'public'   => true
	    	));
	    	//unset media - images | videos are treated as belonging to post, pages, custom post types
	    	unset($post_types['attachment'], $post_types['revision']);
	    
	    	$screens = $post_types;
		    foreach ($screens as $screen) {

				//add_filter( 'manage_edit-' . $screen . '_columns', array( &$this, 'custom_col_head' ), 10, 1 );
				add_filter( 'manage_' . $screen . '_posts_columns', array( &$this, 'custom_col_head' ), 10, 1 );
				add_action( 'manage_' . $screen . '_posts_custom_column', array( &$this, 'custom_col_content' ), 10, 2 );
				add_action( 'manage_edit-' . $screen . '_sortable_columns', array( &$this, 'custom_col_sort' ), 10, 2 );
		    }
			add_action( 'restrict_manage_posts', array( &$this, 'custom_col_sort_select' ) );
			add_filter( 'request', array( &$this, 'custom_col_sort_orderby' ) );
        }
        
        public function custom_col_head( $columns ) {

		    $new_columns['psp_seo_score'] 		= __('SEO Score ', 'psp');
			$new_columns['psp_seo_title'] 		= __('SEO Title', 'psp');
		    $new_columns['psp_seo_fkw'] 		= __('SEO Focus KW', 'psp');
		
		    return array_merge( $columns, $new_columns );
        }
        
        public function custom_col_content( $column_name, $post_id ) {

			if( isset($post_id) && (int)$post_id > 0 ){

				$display = '';

				$score = get_post_meta( $post_id, 'psp_score', true );
				$score = isset($score) && !empty($score) ? $score : 0;
				$focus_kw = get_post_meta( $post_id, 'psp_kw', true );
				$meta = get_post_meta( $post_id, 'psp_meta', true );
				
				$seo_title = isset($meta['title']) ? $meta['title'] : '';

				switch ($column_name) {
					case 'psp_seo_score' :
						$display = '<div class="psp-progress">';
						$display .= 	'<div class="psp-progress-bar" id="psp-custom-col-progress-bar-'.$post_id.'" title="' . esc_attr( $seo_title ) . '" alt="' . esc_attr( $seo_title ) . '"></div>';
						$display .= '	<div class="psp-progress-score">'.$score.'%</div>';
						$display .= '</div>';
						$this->do_progress_bar( '#psp-custom-col-progress-bar-'.$post_id, $score );
						break;

					case 'psp_seo_title' :
						$display = esc_html( $seo_title );
						break;

					case 'psp_seo_fkw' :
						$display = esc_html( $focus_kw );
						break;

					default;
						break;
				} // end switch
				echo $display;
			}
        }
        
        public function custom_col_sort( $columns ) {
        	$new_columns['psp_seo_score']    = 'psp_seo_score';
        	return array_merge( $columns, $new_columns );
        }
        
        public function custom_col_sort_orderby( $request ) {
			if ( isset( $_GET['psp_score_select'] ) ) { // score select / drop-down
				
				$selVal = $_GET['psp_score_select'];

				$interval = false;
				if ( $selVal == 'none' )
					$interval = 0;
				else if ( $selVal == 'bad' )
					$interval = array(0.1, 25.9);
				else if ( $selVal == 'poor' )
					$interval = array(26, 45.9);
				else if ( $selVal == 'ok' )
					$interval = array(46, 65.9);
				else if ( $selVal == 'good' )
					$interval = array(66, 79.9);
				else if ( $selVal == 'excellent' )
					$interval = array(80, 100);

				if ( $interval!==false )
					if ( $interval == 0 )
						$request = array_merge($request, array(
							'meta_query' => array(
								'relation' => 'AND'
								,array(
									'key' 		=> 'psp_score',
									'value' 	=> '', // this is ignored, but is necessary
									'compare' 	=> 'NOT EXISTS', // works
								)/*,
								,'relation' => 'OR'
								,array(
									'key'     	=> 'psp_score',
									'value'   	=> array(0.1, 100),
									'type'    	=> 'NUMERIC',
									'compare' 	=> 'NOT IN BETWEEN'
								)*/
							)
						));
					else if ( is_array($interval) && count($interval)>=2 )
						$request = array_merge($request, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_score',
									'value'   	=> $interval,
									'type'    	=> 'NUMERIC',
									'compare' 	=> 'BETWEEN'
								)
							)
						));
			}

			if ( isset( $request['orderby'] ) && $request['orderby'] == 'psp_seo_score' ) { // score column
				$request = array_merge($request, array(
					'meta_key' => 'psp_score',
					'orderby'  => 'meta_value_num'
				));
			}
			return $request;
        }

        public function custom_col_sort_select()
        {
			global $pagenow;
			if ( $pagenow == 'upload.php' )
				return false;
	
			$html = array();
			$html[] = '<select name="psp_score_select">';
			$html[] = '<option value="all">' . __( "Premium SEO Pack: All Scores", 'psp' ) . '</option>';
			$values = array(
				'none'      	=> __( 'Premium SEO Pack: No Score', 'psp' ),
				'bad'     		=> __( 'Premium SEO Pack: Bad', 'psp' ),
				'poor'    		=> __( 'Premium SEO Pack: Poor', 'psp' ),
				'ok'      		=> __( 'Premium SEO Pack: Ok', 'psp' ),
				'good'    		=> __( 'Premium SEO Pack: Good', 'psp' ),
				'excellent'		=> __( 'Premium SEO Pack: Excellent', 'psp' )
			);
			foreach ( $values as $key => $val ) {
				$html[] = '<option ' . (isset( $_GET['psp_score_select'] ) && $_GET['psp_score_select'] == $key ? ' selected="selected" ' : '') . 'value="' . $key . '">' . $val . '</option>';
			}
			$html[] = '</select>';
			echo implode('', $html);
        }
        

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages()
	       		->_registerMetaBoxes();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		if ( $this->the_plugin->capabilities_user_has_module('on_page_optimization') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Mass Optimization', 'psp'),
		            __('Mass Optimization', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_massOptimization",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		/**
	    * Register plug-in admin metaboxes
	    */
	    protected function _registerMetaBoxes()
	    {
	    	if ( $this->the_plugin->capabilities_user_has_module('on_page_optimization') ) {
		    	//posts | pages | custom post types
		    	$post_types = get_post_types(array(
		    		'public'   => true
		    	));
		    	//unset media - images | videos are treated as belonging to post, pages, custom post types
		    	unset($post_types['attachment'], $post_types['revision']);
	
		    	$screens = $post_types;
			    foreach ($screens as $key => $screen) {
			    	$screen = str_replace("_", " ", $screen);
					$screen = ucfirst($screen);
			        add_meta_box(
			            'psp_onpage_optimize_meta_box',
			            $screen . ' - ' . __( 'SEO Settings', $this->the_plugin->localizationName ),
			            array($this, 'display_meta_box'),
			            $key
			        );
			    }
	    	}
		    
	        return $this;
	    }

		public function auto_optimize_on_save()
		{
			wp_reset_query();

			global $post;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      			return;
			
			$postID = isset($post->ID) && (int) $post->ID > 0 ? $post->ID : 0;
			if( $postID > 0 ){
				$focus_kw = isset($_REQUEST['psp-field-focuskw']) ? $_REQUEST['psp-field-focuskw'] : '';
				$__stat = $this->optimize_page( $postID, $focus_kw );
			}
		}

		public function display_meta_box()
		{
			$this->printBoxInterface();
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		

		/*
		* printBoxInterface, method
		* -------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function makePrintBoxParams( $tax=false ) 
		{
			$ret = array(
				'ga'						=> null,
				'__istax'					=> $this->the_plugin->__tax_istax( $tax ),

				'post'						=> null,
				'post_id'					=> 0,
				'post_content'				=> '',
				'post_type'					=> '',
				
				'seo'						=> null,
				'psp_option'				=> array(),
				
				'focus_kw'					=> '',
				'psp_meta'					=> array(),
				'psp_sitemap_isincluded'	=> '',
				'seo_data'					=> '',
				'summary_seo_data'			=> '',
				'seo_title'					=> '',
				
				'__nb_words'				=> 0,
				'__kw_occurences'			=> 0,
				'__density'					=> 0,
				
				'fb_default_img'			=> '',
				'fb_isactive'				=> '',
				'fb_opengraph'				=> '',
				
				'parse_shortcodes'			=> false
			);
			
			// base info!
			if ( $this->the_plugin->__tax_istax( $tax ) ) { //taxonomy data!

				$post = $tax;

				$post_id = (int) $post->term_id;
				//$post_content = $this->the_plugin->getPageContent( $post, $post->description, true );
				$post_type = '';
				
				$postIdentifier = (object) array('term_id' => (int) $post->term_id, 'taxonomy' => $post->taxonomy);
				
				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $post );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_seo_status = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $post_id, 'psp_status' );

			} else {

				global $post;
				$post_id = (int) $post->ID;
				//$post_content = $this->the_plugin->getPageContent( $post, $post->post_content );
				$post_type = $post->post_type;
				
				$postIdentifier = $post_id;
				
				$post_seo_status = get_post_meta( $post_id, 'psp_status', true);
			}
			
			$ret = array_merge($ret, array(
				'post'				=> $post,
				'post_id'			=> $post_id,
				//'post_content'		=> $post_content
			));
			
			//seo check script!
			$seo = pspSeoCheck::getInstance();

			//title meta format options!
			$psp_option = $this->the_plugin->get_theoption('psp_title_meta_format');
			// check if isset and string have content
			//if(isset($psp_option) && trim($psp_option) != ""){
			//	$psp_option = unserialize($psp_option);
			//}
			$ret = array_merge($ret, array(
				'seo'			=> $seo,
				'psp_option'	=> $psp_option
			));
			
			
			//focus keyword & meta info!
			if ( $this->the_plugin->__tax_istax( $tax ) ) { //taxonomy data!
  
				//$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $post );
				//if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
				//	$psp_current_taxseo = array();

				$focus_kw = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $post, 'psp_kw' );
				$psp_meta = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $post, 'psp_meta' );
				$psp_sitemap_isincluded = '';

			} else { // is post | page | custom post type edit page!
				
				$focus_kw = get_post_meta( $post_id, 'psp_kw', true );
				$psp_meta = get_post_meta( $post_id, 'psp_meta', true );
				$psp_sitemap_isincluded = get_post_meta( $post_id, 'psp_sitemap_isincluded', true );
			}

			if ( !is_array($psp_meta) )
				$psp_meta = array();

			$seo_data = $this->get_seo_report($postIdentifier, $focus_kw, 'array', 'large');
			$summary_seo_data = $this->get_seo_report($postIdentifier, $focus_kw, 'array', 'summary');
			$seo_title = isset($psp_meta['title']) ? $psp_meta['title'] : '';

			// keyword density
			$__density = isset($post_seo_status["kw_density"]["details"]) ? $post_seo_status["kw_density"]["details"] : array();
			$__nb_words = isset($__density['nb_words']) ? $__density['nb_words'] : '';
			$__kw_occurences = isset($__density['kw_occurences']) ? $__density['kw_occurences'] : '';
			$__density = isset($__density['density']) ? $__density['density'] : 0;

			$ret = array_merge($ret, array(
				'focus_kw'					=> $focus_kw,
				'psp_meta'					=> $psp_meta,
				'psp_sitemap_isincluded'	=> $psp_sitemap_isincluded,
				'seo_data'					=> $seo_data,
				'summary_seo_data'			=> $summary_seo_data,
				'seo_title'					=> $seo_title,
				
				'__nb_words'				=> $__nb_words,
				'__kw_occurences'			=> $__kw_occurences,
				'__density'					=> $__density
			));

			$optimizeSettings = $this->the_plugin->getAllSettings( 'array', 'on_page_optimization' );
			if ( !isset($optimizeSettings['parse_shortcodes']) 
				|| ( isset($optimizeSettings['parse_shortcodes']) && $optimizeSettings['parse_shortcodes'] != 'yes' ) ) {

				if ( $this->the_plugin->__tax_istax( $tax ) ) { //taxonomy data!
					$__row_actions = $this->the_plugin->edit_post_inline_data( $post_id, $seo, $tax );
				} else {
					$__row_actions = $this->the_plugin->edit_post_inline_data( $post_id, $seo );
				}
				$ret['__row_actions'] = $__row_actions;
			}
			else {

				$ret['parse_shortcodes'] = true;
			} // end parse_shortcodes
			
			//facebook image
			if ( $this->the_plugin->__tax_istax( $tax ) ) { //taxonomy data!
				$fb_default_img = ''; // no facebook image for custom taxonomy!
			}
			else {
				$__featured_image = '';
				if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_id ) ) {
					$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
					$__featured_image = $__featured_image[0];
				}
	
				$fb_default_img = '';
				if ( isset($psp_option['social_default_img']) && !empty($psp_option['social_default_img']) ) //default image
					$fb_default_img = $psp_option['social_default_img'];
				if ( isset($__featured_image) && !empty($__featured_image) ) //featured image
					$fb_default_img = $__featured_image;
				if ( isset($psp_meta['facebook_image']) && !empty($psp_meta['facebook_image']) ) //custom image
					$fb_default_img = $psp_meta['facebook_image'];
			}

			//facebook is active
			$fb_isactive = 'yes';
			if ( isset($psp_option['social_use_meta']) && !empty($psp_option['social_use_meta']) )
				$fb_isactive = $psp_option['social_use_meta'];
			if ( isset($psp_meta['facebook_isactive']) && !empty($psp_meta['facebook_isactive']) )
				$fb_isactive = $psp_meta['facebook_isactive'];

			//open graph type
			$fb_opengraph = 'default';
			if ( isset($psp_option['social_opengraph_default']) && !empty($psp_option['social_opengraph_default'])
				&& !$this->the_plugin->__tax_istax( $tax ) )
				$ogdef  = $psp_option['social_opengraph_default']["{$post_type}"];
			if ( isset($ogdef) && !empty($ogdef) )
				$fb_opengraph = $ogdef;
			if ( isset($psp_meta['facebook_opengraph_type']) && !empty($psp_meta['facebook_opengraph_type']) )
				$fb_opengraph = $psp_meta['facebook_opengraph_type'];
				
			$ret = array_merge($ret, array(
				'fb_default_img'			=> $fb_default_img,
				'fb_isactive'				=> $fb_isactive,
				'fb_opengraph'				=> $fb_opengraph
			));
			
			// post has twitter app card type
			$twc_app_isactive = 'no';
			//if ( isset($psp_option['psp_twc_site_app']) && !empty($psp_option['psp_twc_site_app']) )
			//	$twc_app_isactive = $psp_option['psp_twc_site_app'];
			if ( isset($psp_meta['psp_twc_app_isactive']) && !empty($psp_meta['psp_twc_app_isactive']) )
				$twc_app_isactive = $psp_meta['psp_twc_app_isactive'];
				
			// post twitter card type
			$twc_post_cardtype = 'none';
			if ( isset($psp_option['psp_twc_cardstype_default'], $psp_option['psp_twc_cardstype_default']["{$post_type}"]) && !empty($psp_option['psp_twc_cardstype_default']) )
				$twc_post_cardtype = $psp_option['psp_twc_cardstype_default']["{$post_type}"];
			if ( isset($psp_meta['psp_twc_post_cardtype']) && !empty($psp_meta['psp_twc_post_cardtype']) )
				$twc_post_cardtype = $psp_meta['psp_twc_post_cardtype'];

			// post twitter card thumb size
			$twc_post_thumbsize = 'none';
			if ( isset($psp_option['psp_twc_thumb_sizes']) && !empty($psp_option['psp_twc_thumb_sizes']) )
				$twc_post_thumbsize = $psp_option['psp_twc_thumb_sizes'];
			if ( isset($psp_meta['psp_twc_post_thumbsize']) && !empty($psp_meta['psp_twc_post_thumbsize']) )
				$twc_post_thumbsize = $psp_meta['psp_twc_post_thumbsize'];

			$ret = array_merge($ret, array(
				'twc_app_isactive'			=> $twc_app_isactive,
				'twc_post_cardtype'			=> $twc_post_cardtype,
				'twc_post_thumbsize'		=> $twc_post_thumbsize
			));

			unset($ret['seo'], $ret['psp_option']);
			return $ret;
		}
		
		private function printBoxInterface( $tax=false )
		{
			$ret = $this->makePrintBoxParams( $tax );
			extract( $ret );
			
			if( isset($post_id) && $post_id > 0 ){
?>
			<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
			<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
			<div id="psp-meta-box-preload" style="height:200px; position: relative;">
				<!-- Main loading box -->
				<div id="psp-main-loading" style="display:block;">
					<div id="psp-loading-box" style="top: 50px">
						<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
						<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
					</div>
				</div>
			</div>
			
			<div class="psp-meta-box-container" style="display:none;">
				<!-- box Tab Menu -->
				<div class="psp-tab-menu">
					<a href="#dashboard" class="open"><?php _e('Dashboard', 'psp');?></a>
					<a href="#page_meta"><?php _e('The Meta', 'psp');?></a>
					<a href="#page_status"><?php _e('Page Status', 'psp');?></a>
					<a href="#social_settings"><?php _e('Social Settings', 'psp');?></a>
					<a href="#advance_seo"><?php _e('Advanced SEO', 'psp');?></a>
					<a href="#twitter_cards"><?php _e('Twitter Cards', 'psp');?></a>
				</div>
				
				<!-- box Data -->
				<div id="psp-inline-row-data" class="hide" style="display: none;">
					<div class="psp-post-postId"><?php echo $post_id; ?></div>
					<div class="psp-post-score"><?php echo $seo_data['score']; ?></div>
					<div class="psp-post-total-kw"><?php echo $__nb_words; ?></div>
					<div class="psp-post-total-focus-kw"><?php echo $__kw_occurences; ?></div>
					<div class="psp-post-total-density"><?php echo $__density; ?></div>
					<?php echo $__row_actions; ?>
				</div>

				<div class="psp-tab-container">
				
					<!-- box Dashboard -->
					<div id="psp-tab-div-id-dashboard" style="display:block;">
						<div class="psp-dashboard-box span_3_of_3" rel="psp-box-id-visits-and-serp">
							<h1><?php _e('SEO Status', 'psp');?></h1>
							<div class="psp-dashboard-box-content">
								<table id="psp-seo-score-box" style="width:100%;">
									<tr>
										<td width="200">
											<h3><?php _e('Your Focus Keywords', 'psp');?></h3>
										</td>
										<td valign="top">
											<?php if( isset($focus_kw) && trim($focus_kw) != "" ) { ?>
												<h2 style="display:inline-block; width: auto; margin-right: 10px;"><strong><?php echo $focus_kw; ?></strong></h2>
											<?php 
												}
											?>
											<?php if( isset($focus_kw) && trim($focus_kw) != "" ) { ?>
												<a style="position: relative; bottom: -8px;" id="psp-edit-focus-keywords" class="psp-button blue" href="#edit-focus-keywords">
													<?php _e('Edit Focus Keywords', 'psp');?>
												</a>
											<?php 
												}else{
											?>
												<a style="position: relative; bottom: 0px;" id="psp-edit-focus-keywords" class="psp-button blue" href="#edit-focus-keywords">
													<?php _e('Add Focus Keywords', 'psp');?>
												</a>
											<?php
												}
											?>
											<?php if ( !$parse_shortcodes ) { ?>
											<a style="position: relative; bottom: -8px; margin-left:5px;" id="psp-btn-metabox-autofocus2" class="psp-button blue" href="#btn-metabox-autofocus2">
												<?php _e('Auto-complete fields', 'psp');?>
											</a>
											<?php } ?>
										</td>
									</tr>
									
									<tr>
										<td>
											<h3><?php _e('Seo Score', 'psp');?></h3>
										</td>
										<td valign="top">
											
											<?php
											// seo score
											$display = '<div class="psp-progress">';
											$display .= 	'<div class="psp-progress-bar" id="psp-item-score-progress-bar-'.$post_id.'" title="' . esc_attr( $seo_title ) . '" alt="' . esc_attr( $seo_title ) . '"></div>';
											$display .= '	<div class="psp-progress-score">'.$seo_data['score'].'%</div>';
											$display .= '</div>';
											echo $display;
											
											$this->do_progress_bar( '#psp-item-score-progress-bar-'.$post_id, $seo_data['score'] );
											?>
										</td>
									</tr>
									<tr>
										<td>
											<h3><?php _e('Keyword Density', 'psp');?></h3>
										</td>
										<td>
										<?php
										// density
										$display = '<div class="psp-progress">';
										$display .= 	'<div class="psp-progress-bar" id="psp-item-density-progress-bar-'.$post_id.'" title="' . esc_attr( $focus_kw ) . '" alt="' . esc_attr( $focus_kw ) . '"></div>';
										$display .= '	<div class="psp-progress-score">'.$__density.'%</div>';
										$display .= '</div>';
										echo $display;
										
										$dens = 10;
										if ( $__density>=2 && $__density<=4.5 ) {
											$dens = 100;
										}else if ( $__density>1.5 && $__density<2 ) {
											$dens = 70;
										}
										else if ( $__density>1 && $__density<1.5 ) {
											$dens = 30;
										}
										$this->do_progress_bar( '#psp-item-density-progress-bar-'.$post_id, $dens );
										?>
										</td>
									</tr>
									
									<tr>
										<td valign="top">
											<h3 style="margin-top: 10px;"><?php _e('Summary Analytics', 'psp');?></h3>
										</td>
										<td valign="top">
											<div class="psp-seo-score-summary psp-seo-status-container">
												<?php
												echo $summary_seo_data['html'];
												?>
											</div>
										</td>
									</tr>
									
								</table>
							</div>
						</div>

					</div>

					<!-- box Page Meta Tags -->
					<div id="psp-tab-div-id-page_meta" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
							<h1><?php _e('Page Meta', 'psp');?></h1>
							<div class="psp-dashboard-box-content">
								<table class="form-table" id="psp-form-meta-tags">
									<tbody>
										<tr>
											<td valign="top">
												<?php _e('Snippet Preview:', 'psp');?><br />
												<i style="font-size: 10px; color: #ccc;"><?php _e('Auto-Refresh each 2 seconds:', 'psp');?></i>
											</td>
											<td>
												<div class="psp-prev-box">
													<!--span class="psp-prev-focuskw"></span-->
													<a href="#" class="psp-prev-title"></a>
													<a href="#" class="psp-prev-url"></a>
													<p class="psp-prev-desc"></p>
													<?php if ( !$parse_shortcodes ) { ?>
													<a style="margin-top:5px;" id="psp-btn-metabox-autofocus" class="psp-button blue" href="#metabox-autofocus"><?php _e('Auto-complete fields', 'psp');?></a>
													<?php } ?>
												</div>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<label for="psp-field-focuskw"><?php _e('Focus Keyword:', 'psp');?></label>
											</td>
											<td>
												<input type="text" class="large-text" style="width: 300px;" value="<?php echo $focus_kw;?>" name="psp-field-focuskw" autocomplete="off" id="psp-field-focuskw">
											</td>
										</tr>
										<tr>
											<td valign="top">
												<label for="psp-field-title"><?php _e('SEO Title:', 'psp');?></label>
											</td>
											<td>
												<input type="text" class="large-text" value="<?php echo ( isset($psp_meta['title']) ? $psp_meta['title'] : '' );?>" name="psp-field-title" id="psp-field-title" maxlength="70">
												<br>
												<p><?php _e('Title display in search engines is limited to 70 chars, <span id="psp-field-title-length"  class="psp-chars-left"></span> chars left.', 'psp');?></p>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<label for="psp-field-metadesc"><?php _e('Meta Description:', 'psp');?></label>
											</td>
											<td>
												<textarea name="psp-field-metadesc" id="psp-field-metadesc" rows="3" class="large-text" maxlength="160"><?php echo isset($psp_meta['description']) ? $psp_meta['description'] : '';?></textarea>
												<p><?php _e('The meta description will be limited to 160 chars, <span id="psp-field-metadesc-length"  class="psp-chars-left"></span> chars left.', 'psp');?> </p>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<label for="psp-field-metakeywords"><?php _e('Meta Keywords:', 'psp');?></label>
											</td>
											<td>
												<textarea name="psp-field-metakewords" id="psp-field-metakeywords" rows="3" class="large-text" maxlength="160"><?php echo isset($psp_meta['keywords']) ? $psp_meta['keywords'] : '';?></textarea>
												<p><?php _e('The meta metakewords will be limited to 160 chars, <span id="psp-field-metakeywords-length" class="psp-chars-left"></span> chars left.', 'psp');?> </p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- box Social Settings -->
					<div id="psp-tab-div-id-social_settings" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
						<h1><?php _e('Social settings', 'psp');?></h1>
						<div class="psp-dashboard-box-content">
							<table class="form-table">
								<tbody>
									<tr>
										<td valign="top">
											<label for="psp-field-facebook-isactive"><?php _e('Use Facebook Meta:', 'psp');?></label>
										</td>
										<td>
											<select name="psp-field-facebook-isactive" id="psp-field-facebook-isactive">
												<option value="default" <?php echo $fb_isactive=='default' ? 'selected="true"' : ''; ?> ><?php _e('Default settings', 'psp');?></option>
												<option value="yes" <?php echo $fb_isactive=='yes' ? 'selected="true"' : ''; ?> ><?php _e('Yes', 'psp');?></option>
												<option value="no" <?php echo $fb_isactive=='no' ? 'selected="true"' : ''; ?> ><?php _e('No', 'psp');?></option>
											</select>
											<p><?php _e('Choose Yes if you want to use Facebook Meta Tags on your page', 'psp');?></p>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<label for="psp-field-facebook-titlu"><?php _e('Facebook Title:', 'psp');?></label>
										</td>
										<td>
											<input type="text" class="large-text" style="width: 300px;" value="<?php echo !empty($psp_meta['facebook_titlu']) ? $psp_meta['facebook_titlu'] : ''/*$psp_meta['title']*/;?>" name="psp-field-facebook-titlu" autocomplete="off" id="psp-field-facebook-titlu">
											<p><?php _e('Add a custom title for your post. This will be used to post on an user\'s wall when they like/share this post on Facebook.', 'psp');?></p>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<label for="psp-field-facebook-desc"><?php _e('Facebook Description:', 'psp');?></label>
										</td>
										<td>
											<textarea name="psp-field-facebook-desc" id="psp-field-facebook-desc" rows="3" class="large-text"><?php echo !empty($psp_meta['facebook_desc']) ? $psp_meta['facebook_desc'] : ''/*$psp_meta['description']*/;?></textarea>
											<p><?php _e('Add a custom description for your post. This will be used to post on an user\'s wall when they share this post on Facebook.', 'psp');?></p>
										</td>
									</tr>
									<?php if ( !$__istax ) { ?>
									<tr>
										<td valign="top">
											<label for="psp-field-facebook-image"><?php _e('Facebook Image:', 'psp');?></label>
										</td>
										<td>
										<?php
											echo $this->uploadImage( 
												array(
												 	'psp-field-facebook-image' => array(
												 		'db_value'	=> isset($psp_meta['facebook_image']) ? $psp_meta['facebook_image'] : '',

														'type' 		=> 'upload_image',
														//'std'		=> $fb_default_img,
														'size' 		=> 'large',
														'title' 	=> 'Facebook image',
														'value' 	=> 'Upload image',
														'thumbSize' => array(
															'w' => '100',
															'h' => '100',
															'zc' => '2',
														),
														'desc' 		=> __('Choose the image', 'psp')
													)
												));
										?>
											<p><?php _e('Add a custom image for your post. This will be used to post on an user\'s wall when they share this post on Facebook.', 'psp');?></p>
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td valign="top">
											<label for="psp-field-facebook-opengraph-type"><?php _e('Open Graph Type:', 'psp');?></label>
										</td>
										<td>
										<?php
											echo $this->OpenGraphTypes( 
												'psp-field-facebook-opengraph-type',
												$fb_opengraph
											);
										?>
										<!--<p><?php _e('Choose Open Graph Type.', 'psp');?></p>-->
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

					<!-- box Page Status -->
					<div id="psp-tab-div-id-page_status" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
							<h1><?php _e('Page Status', 'psp');?></h1>
							<div class="psp-dashboard-box-content psp-seo-status-container">
								<?php
								echo $seo_data['html'];
								?>
							</div>
						</div>
					</div>
	
					<!-- box Advanced SEO -->
					<div id="psp-tab-div-id-advance_seo" style="display:none;">
					<div class="psp-dashboard-box span_3_of_3">
						<h1><?php _e('Advanced SEO', 'psp');?></h1>
						<div class="psp-dashboard-box-content psp-seo-status-container">
							<table class="form-table">
								<tbody>
									<tr>
										<td valign="top">
											<label for="psp-field-meta_robots_index"><?php _e('Meta Robots Index:', 'psp');?></label>
										</td>
										<td>
											<select name="psp-field-meta_robots_index" id="psp-field-meta_robots_index">
												<option value="default" <?php echo isset($psp_meta['robots_index']) && $psp_meta['robots_index']=='default' ? 'selected="true"' : ''; ?> ><?php _e('Default settings', 'psp');?></option>
												<option value="index" <?php echo isset($psp_meta['robots_index']) && $psp_meta['robots_index']=='index' ? 'selected="true"' : ''; ?> ><?php _e('Index', 'psp'); ?></option>
												<option value="noindex" <?php echo isset($psp_meta['robots_index']) && $psp_meta['robots_index']=='noindex' ? 'selected="true"' : ''; ?> ><?php _e('NO Index', 'psp'); ?></option>
											</select>
											<p><?php _e('Tell robots not to index the content of a page.', 'psp');?></p>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<label for="psp-field-meta_robots_follow"><?php _e('Meta Robots Follow:', 'psp');?></label>
										</td>
										<td>
											<select name="psp-field-meta_robots_follow" id="psp-field-meta_robots_follow">
												<option value="default" <?php echo isset($psp_meta['robots_follow']) && $psp_meta['robots_follow']=='default' ? 'selected="true"' : ''; ?> ><?php _e('Default settings', 'psp');?></option>
												<option value="follow" <?php echo isset($psp_meta['robots_follow']) && $psp_meta['robots_follow']=='follow' ? 'selected="true"' : ''; ?> ><?php _e('Follow', 'psp'); ?></option>
												<option value="nofollow" <?php echo isset($psp_meta['robots_follow']) && $psp_meta['robots_follow']=='nofollow' ? 'selected="true"' : ''; ?> ><?php _e('NO Follow', 'psp'); ?></option>
											</select>
											<p><?php _e('Tell robots not to scan page for links to follow.', 'psp');?></p>
										</td>
									</tr>
									<?php if ( !$__istax ) { ?>
									<tr>
										<td valign="top">
											<label for="psp-field-include-sitemap"><?php _e('Include in Sitemap:', 'psp');?></label>
										</td>
										<td>
											<select name="psp-field-include-sitemap" id="psp-field-include-sitemap">
												<option value="default" <?php echo $psp_sitemap_isincluded=='default' ? 'selected="true"' : ''; ?> ><?php _e('Default settings', 'psp');?></option>
												<option value="always_include" <?php echo $psp_sitemap_isincluded=='always_include' ? 'selected="true"' : ''; ?> ><?php _e('Always include', 'psp');?></option>
												<option value="never_include" <?php echo $psp_sitemap_isincluded=='never_include' ? 'selected="true"' : ''; ?> ><?php _e('Never include', 'psp');?></option>
											</select>
											<p><?php _e('Should this page be in the XML Sitemap?', 'psp');?></p>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<label for="psp-field-priority-sitemap"><?php _e('Sitemap Priority:', 'psp');?></label>
										</td>
										<td>
											<select name="psp-field-priority-sitemap" id="psp-field-priority-sitemap">
												<option value="-" <?php echo isset($psp_meta['priority']) && in_array($psp_meta['priority'], array('', '-')) ? 'selected="true"' : ''; ?> ><?php _e('Automatic', 'psp');?></option>
												<?php
												$__range = range(0, 1, 0.1);
												$__range2 = array();
												for ($i=(count($__range)-1); $i>=0; $i--)
													$__range2[] = $__range[ $i ];
												foreach ($__range2 as $kk => $vv){
													$__priorityText = '';
													$vv = (string) $vv;
													if ( $vv=='1' )
														$__priorityText = ' - ' . __('Highest priority', 'psp');
													else if ( $vv=='0.5' )
														$__priorityText = ' - ' . __('Medium priority', 'psp');
													else if ( $vv=='0.1' )
														$__priorityText = ' - ' . __('Lowest priority', 'psp');
														
													echo '<option value="' . ( $vv ) . '" ' . ( isset($psp_meta['priority']) && $psp_meta['priority'] == $vv ? 'selected="true"' : '' ) . '>' . ( $vv . $__priorityText ) . '</option>';
												}
												?>
											</select>
											<p><?php _e('Should this page be in the XML Sitemap?', 'psp');?></p>
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td valign="top">
											<label for="psp-field-canonical"><?php _e('Canonical url:', 'psp');?></label>
										</td>
										<td>
											<input type="text" class="large-text" value="<?php echo isset($psp_meta['canonical']) ? $psp_meta['canonical'] : ''; ?>" name="psp-field-canonical" id="psp-field-canonical">
											<p><?php _e('A canonical page is the preferred version of a set of pages with highly similar content.', 'psp');?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
					<!-- box Twitter Cards -->
<?php
// Twitter Cards ajax action & public methods!
require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'utils/twitter_cards.php' );
$twc = new pspTwitterCards( $this->the_plugin );
?>
					<div id="psp-tab-div-id-twitter_cards" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
						
						<div class="psp-dashboard-box-content psp-seo-status-container">
							<table class="form-table">
								<tbody>
									<tr>
										<td valign="top">
											<label for="psp_twc_post_thumbsize"><?php _e('Image thumb size:', 'psp');?></label>
										</td>
										<td>
										<?php
											echo $this->TwitterCardThumbSize( 
												'psp_twc_post_thumbsize',
												$twc_post_thumbsize
											);
										?>
											<span><?php _e('Choose Post|Page Image Thumb Size', 'psp');?></span>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<label for="psp_twc_post_cardtype"><?php _e('Add Post|Page Twitter Card Type:', 'psp');?></label>
										</td>
										<td>
										<?php
											echo $this->TwitterCardTypes( 
												'psp_twc_post_cardtype',
												$twc_post_cardtype
											);
										?>
											<span><?php _e('Choose Post|Page Twitter Card Type', 'psp');?></span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
							<!-- ajax response - Creating the option fields -->
							<div class="psp-form" id="psp-twittercards-post-response">
							</div>
										</td>
									</tr>
									
									<tr>
										<td valign="top">
											<label for="psp-field-twc-app-isactive"><?php _e('Add Twitter App Card Type:', 'psp');?></label>
										</td>
										<td colspan="2">
							<select name="psp_twc_app_isactive" id="psp_twc_app_isactive" style="width:300px;">
								<option value="default" <?php echo $twc_app_isactive=='default' ? 'selected="true"' : ''; ?> ><?php _e('Use Website Generic App Twitter Card Type', 'psp');?></option>
								<option value="yes" <?php echo $twc_app_isactive=='yes' ? 'selected="true"' : ''; ?> ><?php _e('Yes', 'psp');?></option>
								<option value="no" <?php echo $twc_app_isactive=='no' ? 'selected="true"' : ''; ?> ><?php _e('No', 'psp');?></option>
							</select>
							<!--<span><?php _e('Choose Yes if you want to add Twitter App Card Type', 'psp');?></span>-->
										</td>
									</tr>
									<tr>
										<td colspan="2">
							<!-- ajax response - Creating the option fields -->
							<div class="psp-form" id="psp-twittercards-app-response">
							</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						</div>
					</div>
				
				
				</div>
			<div style="clear:both"></div>
		</div>
<?php
			} //end if post_id
		}

		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('on_page_optimization|on_page_optimization')->show_menu();
			?>
			
			<div id="psp-lightbox-overlay">
				<div id="psp-lightbox-container">
					<h1 class="psp-lightbox-headline">
						<img class="psp-lightbox-icon" src="<?php echo $this->the_plugin->cfg['paths']['freamwork_dir_url'];?>images/light-bulb.png">
						<span><?php _e('PSP SEO Report for post ID:', 'psp');?> <i></i></span>
						<a href="#" class="psp-close-btn" title="<?php _e('Close Lightbox', 'psp'); ?>"></a>
					</h1>

					<div class="psp-seo-status-container">
						<div id="psp-lightbox-seo-report-response"></div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>

			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Content -->
			<div id="psp-content">
				
				<h1 class="psp-section-headline">
					<?php echo $this->module['on_page_optimization']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['on_page_optimization']['description'];?></span>
					<?php
					$has_help = isset($this->module['on_page_optimization']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['on_page_optimization']['help']['type']) && $this->module['on_page_optimization']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['on_page_optimization']['help']['url'] ) . '">HELP</a>';
						} 
					} 
					?>
				</h1>

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix">

						<!-- Content Area -->
						<div id="psp-content-area">
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<img src="<?php echo $this->the_plugin->cfg['paths']['plugin_dir_url'];?>/modules/Social_Stats/assets/menu_icon.png">
											<?php _e('Mass Optimization', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspPageOptimization',
													'show_header' 		=> true,
													'items_per_page' 	=> '10',
													'post_statuses' 	=> 'all',
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', 'psp'),
															'td'	=> '%ID%',
															'width' => '40'
														),

														'title'		=> array(
															'th'	=> __('Title', 'psp'),
															'td'	=> '%title_and_actions%',
															'align' => 'left'
														),

														'score'		=> array(
															'th'	=> __('Score', 'psp'),
															'td'	=> '%score%',
															'width' => '130',
															'css' 	=> array(
																'padding' => '0px',
																'background' => '#fcfcfc'
															)
														),

														'focus_keyword'	=> array(
															'th'	=> __('Focus Keyword', 'psp'),
															'td'	=> '%focus_keyword%',
															'align' => 'left',
															'width' => '350'
														),

														'seo_report'	=> array(
															'th'	=> __('Seo report', 'psp'),
															'td'	=> '%seo_report%',
															'align' => 'center',
															'width' => '110'
														),

														'date'		=> array(
															'th'	=> __('Date', 'psp'),
															'td'	=> '%date%',
															'width' => '120'
														),

														'optimize_btn' => array(
															'th'	=> __('Action', 'psp'),
															'td'	=> '%button%',
															'option' => array(
																'value' => __('Optimize', 'psp'),
																'action' => 'do_item_optimize',
																'color' => 'orange'
															),
															'width' => '80'
														),
													)
												))
												->print_html();
								            ?>
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}

		/*
		* get_seo_report, method
		* ----------------------
		*
		* this will return a SEO score, as HTML
		*/
		public function get_seo_report( $id=0, $kw='', $returnAs='die', $data='large' )
		{ 
			$html = array();
			$summary = array();
			$score = 0;
			$request = array(
				'id' => isset($_REQUEST['id']) ? $_REQUEST['id'] : $id,
				'kw' => isset($_REQUEST['kw']) ? $_REQUEST['kw'] : $kw
			);
			
			$request['kw'] = strtolower( trim( $request['kw'] ) );

			if ( $this->the_plugin->__tax_istax( $request['id'] ) ) { //taxonomy data!

				// $psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $request['id'] );
				// if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
				//	$psp_current_taxseo = array();

				// $post_seo_status = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $request['id'], 'psp_status' );
				// treated just bellow, because on taxonomy always refresh psp_status!

			} else {

				$request['id'] = (int) $request['id'];
				$post_seo_status = get_post_meta( $request['id'], 'psp_status', true);
			}

			if( !isset($post_seo_status) || count($post_seo_status) == 0 || @trim($post_seo_status) == ""
				|| $this->the_plugin->__tax_istax( $request['id'] ) ) { //for taxonomy refresh psp_status!

				$seo = pspSeoCheck::getInstance();
				$post_seo_status = $seo->get_seo_score( $request['id'], $request['kw'], 'array');

				$__seo_status = $post_seo_status;
				$this->save_seo_score( $request['id'], $__seo_status['data'], $__seo_status['score'], $__seo_status['kw'] );
				$post_seo_status = $post_seo_status['data'];
			}

			if( is_array($post_seo_status) && count($post_seo_status) > 0 ) {
				
				if ( $this->the_plugin->__tax_istax( $request['id'] ) ) { //taxonomy data!
					foreach ( array('images_alt', 'html_italic', 'html_bold') as $k=>$v )
						unset( $post_seo_status["$v"] );
				}
					 	
				foreach ($post_seo_status as $key => $value) { //get score
					$score = $score + $value["score"];
				}

				if ( $score > 0 )
					$score = number_format( ( ( 100 * $score ) / count($post_seo_status) ), 1 );
				else
					$score = '0';
				$score_view = $score.'%';
				
				$score_html_class = 'bad';
				if( $score > 0 && $score < 50 ){
					$score_html_class = 'poor';
				}else if ( $score >= 50 ){
					$score_html_class = 'good';
				}
				
				$html[] = '<div class="psp-seo-rule-row">';
				$html[] = 	'<div class="left-col">';
				$html[] = 		'<span class="psp-seo-status-icon ' . ( $score_html_class ) . '"></span>';
				$html[] = 	'</div>';

				$html[] = 	'<div class="middle-col">' . ( __('Score', 'psp') ) . '</div>';
				$html[] = 	'<div class="right-col">';
				$html[] = 		'<p><strong>' . $score_view . '</strong>' . '</p>';
				$html[] = 	'</div>';
				//$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
				//$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . __('Score: ', 'psp') . $score_view . '</div>';
				
				$html[] = '</div>';
					
				foreach ($post_seo_status as $key => $value) {
					
					$score_html_class = 'bad';
					if( $value["score"] > 0 && $value["score"] < 1 ){
						$score_html_class = 'poor';
					}elseif ( $value["score"] == 1 ){
						$score_html_class = 'good';
					}
					if (is_null($value)) $score_html_class = '';
					
					 if ( $this->the_plugin->__tax_istax( $request['id'] )
					 	&& in_array( $key, array('images_alt', 'html_italic', 'html_bold') ) ) //taxonomy data!
					 	continue 1;

					if (!is_null($value)) {
						$html[] = '<div class="psp-seo-rule-row">';
						$html[] = 	'<div class="left-col">';
						$html[] = 		'<span class="psp-seo-status-icon ' . ( $score_html_class ) . '"></span>';
						$html[] = 	'</div>';
					}
					
					 if ( is_null($value) ) continue 1;
					 
					 if ( !isset($value['debug']) ) $value['debug'] = array('str' => '');
					 
					if( $key == 'kw_density' ){
						$html[] = 	'<div class="middle-col">' . ( __('Keyword density', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						//$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 		'<p>Keyword density: <strong>' . ( (string) $value['details']['density'] ) . '%</strong>. Number of content words: <strong>' . ( (string) $value['details']['nb_words'] ) . '</strong>. Keyword occurences in content: <strong>' . ( (string) $value['details']['kw_occurences'] ) . '</strong></p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					if( $key == 'title' ){
						$html[] = 	'<div class="middle-col">' . ( __('Title', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 		'<p><strong>Length:</strong> ' . ( $this->the_plugin->utf8->strlen($value['debug']['str']) ) . ' character(s)</p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'meta_description' ){
						$html[] = 	'<div class="middle-col">' . ( __('Meta Description', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 		'<p><strong>Length:</strong> ' . ( $this->the_plugin->utf8->strlen($value['debug']['str']) ) . ' character(s)</p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'meta_keywords' ){
						$html[] = 	'<div class="middle-col">' . ( __('Meta Keywords', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'permalink' ){
						$html[] = 	'<div class="middle-col">' . ( __('Permalink', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'images_alt' ){
						$html[] = 	'<div class="middle-col">' . ( __('Images', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
						$html[] = 	'</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'first_paragraph' ){
						$html[] = 	'<div class="middle-col">' . ( __('First Paragraph', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<p>' . ( $value['debug']['str'] ) . '</p>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'embedded_content' ){
						$html[] = 	'<div class="middle-col">' . ( __('Embedded Content', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
						$html[] = 	'</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'enough_words' ){
						$html[] = 	'<div class="middle-col">' . ( __('Enough Words', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
						$html[] = 	'</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'html_italic' ){
						$html[] = 	'<div class="middle-col">' . ( __('Mark as Italic', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
						$html[] = 	'</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					else if( $key == 'html_bold' ){
						$html[] = 	'<div class="middle-col">' . ( __('Mark as Bold', 'psp') ) . '</div>';
						$html[] = 	'<div class="right-col">';
						$html[] = 		'<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
						$html[] = 	'</div>';

						$summary[] = '<div class="message-box ' . ( $score_html_class ) . '">' . ( $value['msg'] ) . '</div>';
					}

					if (!is_null($value)) {
						$html[] = '</div>';
					}
				}

				$ret = array(
					'status' 	=> 'valid',
					'score'		=> $score,
					'post_id'	=> $request['id'],
					'html'		=> ( $data == 'large' ? implode("\n", $html) : implode("\n", $summary) )
				); 
				if( $returnAs == 'die' ){
					die(json_encode($ret));
				}
				elseif( $returnAs == 'array' ){
					return $ret;
				}
			}

			$ret = array(
				'status' 	=> 'invalid',
				'score'		=> 0,
				'post_id'	=> $request['id'],
				'html'		=> ''
			);

			if( $returnAs == 'die' ){
				die(json_encode($ret));
			}
			elseif( $returnAs == 'array' ){
				return $ret;
			}
		}

		/*
		* optimize_page, method
		* ---------------------
		*
		* this will create force optimization of your page, and return a SEO score
		*/
		public function optimize_page( $id="", $kw="" )
		{
			$request = array(
				'action'	=> isset($_REQUEST['action']) ? $_REQUEST['action'] : 'default',
				'id' 		=> isset($_REQUEST['id']) ? $_REQUEST['id'] : $id,

				'kw' 		=> isset($_REQUEST['kw']) ? $_REQUEST['kw'] : $kw,
				//'meta_focus_kw'		=> isset($_REQUEST['psp-editpost-meta-focus-kw']) ? trim($_REQUEST['psp-editpost-meta-focus-kw']) : '', //!!! i use what it's in kw!
				'meta_title'		=> isset($_REQUEST['psp-editpost-meta-title']) ? trim($_REQUEST['psp-editpost-meta-title']) : '',
				'meta_description'	=> isset($_REQUEST['psp-editpost-meta-description']) ? trim($_REQUEST['psp-editpost-meta-description']) : '',
				'meta_keywords'		=> isset($_REQUEST['psp-editpost-meta-keywords']) ? trim($_REQUEST['psp-editpost-meta-keywords']) : '',
				'meta_canonical' 	=> isset($_REQUEST['psp-editpost-meta-canonical']) ? trim($_REQUEST['psp-editpost-meta-canonical']) : '',
				'meta_robots_index'	=> isset($_REQUEST['psp-editpost-meta-robots-index']) ? trim($_REQUEST['psp-editpost-meta-robots-index']) : '',
				'meta_robots_follow'=> isset($_REQUEST['psp-editpost-meta-robots-follow']) ? trim($_REQUEST['psp-editpost-meta-robots-follow']) : ''
			);
			foreach ( $request as $k => $v ) {
				if ( in_array($k, array('id')) ) continue 1;
				$request[ $k ] = trim( $v );
			}
			foreach ( $request as $k => $v ) {
				if ( in_array($k, array('id', 'action', 'meta_title', 'meta_description', 'meta_canonical')) ) continue 1;
				$request[ $k ] = strtolower( $v );
				$request[ $k ] = strip_tags( $v );
				$request[ $k ] = stripslashes( $v );
				
				/*if ( $k == 'kw' )
					$request[ $k ] = preg_replace('/[^a-zA-Z0-9\s]/', '', $v);*/
			}
			
			if ( !in_array($request['action'], array('pspOptimizePage', 'pspQuickEdit')) ) {
				if ( isset($_REQUEST['post_ID']) && !empty($_REQUEST['post_ID']) ) {
					$request['id'] = (int) $_REQUEST['post_ID'];
				}
			}

			// Step 1, generate meta keywords, and description for your requested item
			$seo = pspSeoCheck::getInstance();

			if ( $this->the_plugin->__tax_istax( $request['id'] ) ) { //taxonomy data!

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $request['id'] );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();
				
				$post_metas = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $request['id'], 'psp_meta' );
				$post = $this->the_plugin->__tax_get_post( $request['id'], ARRAY_A );
				$post_title = $post['name'];
				$post_content = $this->the_plugin->getPageContent( $post, $post['description'], true );

			} else {

				$request['id'] = (int) $request['id'];

				$post_metas = get_post_meta( $request['id'], 'psp_meta', true);
				$post = get_post( $request['id'], ARRAY_A);
				$post_title = $post['post_title'];
				$post_content = $this->the_plugin->getPageContent( $post, $post['post_content'] );
			}

			if( !isset($post_metas) || count($post_metas) <= 0 || !is_array($post_metas) ) {
				$post_metas = array();
			}
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

			// get info!
			if( !is_null($post) && count($post) > 0 ) {
				// if post don't have meta, setup the one
				if( !isset($post_metas['focus_keyword']) || trim($post_metas['focus_keyword']) == "" ){

					$post_metas['focus_keyword'] = $post_title;
				}
				if( !isset($post_metas['title']) || trim($post_metas['title']) == "" ){

					$post_metas['title'] = $post_title;
				}
				if( !isset($post_metas['description']) || trim($post_metas['description']) == "" ){

					// meta description
					$first_ph = $seo->get_first_paragraph( $post_content );
					$gen_meta_desc = $seo->gen_meta_desc( $first_ph );

					$post_metas['description'] = $gen_meta_desc;
				}
				if( !isset($post_metas['keywords']) || trim($post_metas['keywords']) == "" ){

					// meta keywords
					$gen_meta_keywords = array();
					if ( !empty($post_metas['focus_keyword']) )
						$gen_meta_keywords[] = $post_metas['focus_keyword'];
					$__tmp = $seo->gen_meta_keywords( $post_content );
					if ( !empty($__tmp) )
						$gen_meta_keywords[] = $__tmp;
					
					$post_metas['keywords'] = implode(', ', $gen_meta_keywords);
				}

				if ( $request['action']=='pspOptimizePage' ) { //ajax request from plugin module!

					if( isset($request['kw']) && trim($request['kw']) != "" ){
						$post_metas['focus_keyword'] = $request['kw'];
					}

				}
				else if ( $request['action']=='pspQuickEdit' ) { //ajax request from plugin module!
					
					$post_metas = array_merge($post_metas, array(
						'title'						=> $request['meta_title'],
						'description'				=> $request['meta_description'],
						'keywords'					=> $request['meta_keywords'],
						//'focus_keyword'			=> $request['meta_focus_kw'], //!!! i use what it's in kw!
						'focus_keyword'				=> $request['kw'],
						
						'robots_index'				=> $request['meta_robots_index'],
						'robots_follow'				=> $request['meta_robots_follow'],
						
						'canonical'					=> $request['meta_canonical']
					));
				}
				else { //new or edit post/tax action - from meta_box!

					//$__cleanFocusKW = preg_replace('/[^a-zA-Z0-9\s]/', '', $_REQUEST['psp-field-focuskw']); // clean focus keyword
					$__cleanFocusKW = isset($_REQUEST['psp-field-focuskw']) ? $_REQUEST['psp-field-focuskw'] : ''; // clean focus keyword
					
					$post_metas = array_merge($post_metas, array(
						'title'						=> isset($_REQUEST['psp-field-title']) ? trim( $_REQUEST['psp-field-title'] ) : '',
						'description'				=> isset($_REQUEST['psp-field-metadesc']) ? trim( $_REQUEST['psp-field-metadesc'] ) : '',
						'keywords'					=> isset($_REQUEST['psp-field-metakewords']) ? trim( $_REQUEST['psp-field-metakewords'] ) : '',
						'focus_keyword'				=> trim( $__cleanFocusKW ),
						
						'facebook_isactive'			=> isset($_REQUEST['psp-field-facebook-isactive']) ? trim( $_REQUEST['psp-field-facebook-isactive'] ) : '',
						'facebook_titlu'			=> isset($_REQUEST['psp-field-facebook-titlu']) ? trim( $_REQUEST['psp-field-facebook-titlu'] ) : '',
						'facebook_desc'				=> isset($_REQUEST['psp-field-facebook-desc']) ? trim( $_REQUEST['psp-field-facebook-desc'] ) : '',
						'facebook_image'			=> isset($_REQUEST['psp-field-facebook-image']) ? trim( $_REQUEST['psp-field-facebook-image'] ) : '',
						'facebook_opengraph_type'	=> isset($_REQUEST['psp-field-facebook-opengraph-type']) ? trim( $_REQUEST['psp-field-facebook-opengraph-type'] ) : '',
						
						'robots_index'				=> isset($_REQUEST['psp-field-meta_robots_index']) ? trim( $_REQUEST['psp-field-meta_robots_index'] ) : '',
						'robots_follow'				=> isset($_REQUEST['psp-field-meta_robots_follow']) ? trim( $_REQUEST['psp-field-meta_robots_follow'] ) : '',
						
						'priority'					=> isset($_REQUEST['psp-field-priority-sitemap']) ? trim( $_REQUEST['psp-field-priority-sitemap'] ) : '',
						'canonical'					=> isset($_REQUEST['psp-field-canonical']) ? trim( $_REQUEST['psp-field-canonical'] ) : ''
					));
					
					// Twitter Cards ajax action & public methods!
					require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'utils/twitter_cards.php' );
					$twc = new pspTwitterCards( $this->the_plugin );

					$post_metas = array_merge($post_metas, $twc->save_meta());

					if ( !$this->the_plugin->__tax_istax( $request['id'] ) ) //not taxonomy data!
						update_post_meta( $request['id'], 'psp_sitemap_isincluded', isset($_REQUEST['psp-field-include-sitemap']) ? trim($_REQUEST['psp-field-include-sitemap']) : '' );
				}
				
				// update post/tax meta data!
				if ( $this->the_plugin->__tax_istax( $request['id'] ) ) { //taxonomy data!

					$this->the_plugin->__tax_update_post_meta( $request['id'], array(
						'psp_kw'		=> $post_metas['focus_keyword'],
						'psp_meta'		=> $post_metas
					));
					
				} else {

					update_post_meta( $request['id'], 'psp_kw', $post_metas['focus_keyword'] );
					update_post_meta( $request['id'], 'psp_meta', $post_metas );

				}
				
				// get SEO score
				$retType = 'array';
				$post_seo_status = $seo->get_seo_score( $request['id'], $post_metas['focus_keyword'], $retType, $post_content );
				$__seo_status = $post_seo_status;

				$this->save_seo_score( $request['id'], $__seo_status['data'], $__seo_status['score'], $__seo_status['kw'] );

				if ( $request['action']=='pspQuickEdit' || $request['action']=='pspOptimizePage' ) {
					$__editInline = $this->the_plugin->edit_post_inline_data( $request['id'], $seo, false, $post_content );
					$post_seo_status = array_merge($post_seo_status, array(
						'status' => 'valid',
						'edit_inline_new'	=> $__editInline
					));
					die(json_encode($post_seo_status));
				}

				if ( $retType=='array' )
					$post_seo_status = $post_seo_status['data'];
				return $post_seo_status;
			}
		}
		
		/**
		* Save score
		*
		* @return string
		*/
		public function save_seo_score( $p=0, $status=array(), $score=0, $kw='' )
		{
			if ( $this->the_plugin->__tax_istax( $p ) ) //taxonomy data!
				$post_id = (int) $p->term_id;
			else
				$post_id = (int) $p;
				
			if( count($status) <= 0 || $post_id <= 0 )
				return false;
			
			if ( $this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!
				
					$this->the_plugin->__tax_update_post_meta( $p, array(
						'psp_status'	=> $status,
						'psp_score'		=> $score,
						'psp_kw'		=> $kw,
					));

			} else {

				update_post_meta( $p, 'psp_status', $status );
				update_post_meta( $p, 'psp_score', $score );
				update_post_meta( $p, 'psp_kw', $kw );

			}
			return true;
		}
		
		/**
		 * Upload Image Button
		 *
		 * is based on settings option:
		 * $elm_id is the array KEY
		 * $elm_data is the array VALUE, which is also an array
		 	'image' => array(
				'type' 		=> 'upload_image',
				'size' 		=> 'large',
				'title' 	=> 'Quiz image',
				'value' 	=> 'Upload image',
				'thumbSize' => array(
					'w' => '100',
					'h' => '100',
					'zc' => '2',
				),
				'desc' 		=> 'Choose the image'
			)
		 */
		private function uploadImage( $elm ) {
			global $psp;

			// loop the box elements now
			foreach ( $elm as $elm_id => $value ){
				
				$val = '';
				
				// Set default value to $val
				if ( isset( $value['std'] ) && !empty( $value['std'] ) ) {
					$val = $value['std'];
				}
				
				// If the option is already saved, ovveride $val
				if ( isset( $value['db_value'] ) && !empty( $value['db_value'] ) ) {
					$val = $value['db_value'];
				}

				$html[] = '<table border="0">';
				$html[] = '<tr>';
				$html[] = 	'<td>';
				$html[] = 		'<input class="upload-input-text" name="' . ( $elm_id ) . '" id="' . ( $elm_id ) . '_upload" type="text" value="' . ( $val ) . '" />';
	
				$html[] = 		'<script type="text/javascript">
											jQuery("#' . ( $elm_id ) . '_upload").data({
												"w": ' . ( $value['thumbSize']['w'] ) . ',
												"h": ' . ( $value['thumbSize']['h'] ) . ',
												"zc": ' . ( $value['thumbSize']['zc'] ) . '
											});
										</script>';
	
				$html[] = 	'</td>';
				$html[] = '<td>';
				$html[] = 		'<a href="#" class="button upload_button" id="' . ( $elm_id ) . '">' . ( $value['value'] ) . '</a> ';
				//$html[] = 		'<a href="#" class="button reset_button ' . $hide . '" id="reset_' . ( $elm_id ) . '" title="' . ( $elm_id ) . '">' . __('Remove', 'psp') . '</a> ';
				$html[] = '</td>';
				$html[] = '</tr>';
				$html[] = '</table>';
	
				$html[] = '<a class="thickbox" id="uploaded_image_' . ( $elm_id ) . '" href="' . ( $val ) . '" target="_blank">';
	
				if(!empty($val)){
					$imgSrc = $psp->image_resize( $val, $value['thumbSize']['w'], $value['thumbSize']['h'], $value['thumbSize']['zc'] );
					$html[] = '<img style="border: 1px solid #dadada;" id="image_' . ( $elm_id ) . '" src="' . ( $imgSrc ) . '" />';
				}
				$html[] = '</a>';
	
				$html[] = 		'<script type="text/javascript">
											psp_loadAjaxUpload( jQuery("#' . ( $elm_id ) . '") );
										</script>';
			}
			
			// return the $html
			return implode("\n", $html);
		}
		
		private function OpenGraphTypes( $field_name, $db_meta_name ) {
			//ob_start();
			$html = '
			';
				$val = '';
				if( isset($db_meta_name) ){
					$val = $db_meta_name;
				}

				$html .= '
				<select id="' . $field_name . '" name="' . $field_name . '" style="width:120px;">
					<option value="default" ' . ($val=='default' ? 'selected="true"' : '') . '>' . __('Default settings', 'psp') . '</option>
					<option value="none" ' . ($val=='none' ? 'selected="true"' : '') . '>' . __('None', 'psp') . '</option>
				';
					$opengraph_defaults = array(
						'Internet' 	=> array(
							'article'				=> __('Article', 'psp'),
							'blog'					=> __('Blog', 'psp'),
							'profile'				=> __('Profile', 'psp'),
							'website'				=> __('Website', 'psp')
						),
						'Products' 	=> array(
							'book'					=> __('Book', 'psp')
						),
						'Music' 	=> array(
							'music.album'			=> __('Album', 'psp'),
							'music.playlist'		=> __('Playlist', 'psp'),
							'music.radio_station'	=> __('Radio Station', 'psp'),
							'music.song'			=> __('Song', 'psp')
						),
						'Videos' => array(
							'video.movie'			=> __('Movie', 'psp'),
							'video.episode'			=> __('TV Episode', 'psp'),
							'video.tv_show'			=> __('TV Show', 'psp'),
							'video.other'			=> __('Video', 'psp')
						),
					);
					foreach ($opengraph_defaults as $k => $v){
						$html .= '<optgroup label="' . $k . '">';
						foreach ($v as $kk => $vv){
							$html .= 	'<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
						}
						$html .= '</optgroup>';
					}
				$html .= '
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		';
			//$output = ob_get_contents();
			//ob_end_clean();
			return $html;
		}
		
		private function TwitterCardTypes( $field_name, $db_meta_name ) {
			//ob_start();
			$html = '
			';
				$val = '';
				if( isset($db_meta_name) ){
					$val = $db_meta_name;
				}

				$html .= '
				<select id="' . $field_name . '" name="' . $field_name . '" style="width:120px;">
					<option value="none" ' . ($val=='none' ? 'selected="true"' : '') . '>' . __('None', 'psp') . '</option>
				';
					$opengraph_defaults = array(
							'summary'				=> __('Summary Card', 'psp'),
							'summary_large_image'		=> __('Summary Card with Large Image', 'psp'),
							'photo'					=> __('Photo Card', 'psp'),
							'gallery'				=> __('Gallery Card', 'psp'),
							'player'				=> __('Player Card', 'psp'),
							'product'				=> __('Product Card', 'psp')
					);
					foreach ($opengraph_defaults as $k => $v){
						$html .= 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
					}
				$html .= '
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		';
			//$output = ob_get_contents();
			//ob_end_clean();
			return $html;
		}
		
		private function TwitterCardThumbSize( $field_name, $db_meta_name ) {
			//ob_start();
			$html = '
			';
				$val = '';
				if( isset($db_meta_name) ){
					$val = $db_meta_name;
				}

				$html .= '
				<select id="' . $field_name . '" name="' . $field_name . '" style="width:120px;">
					<option value="none" ' . ($val=='none' ? 'selected="true"' : '') . '>' . __('Don\'t make a thumbnail from the image', 'psp') . '</option>
				';
					$opengraph_defaults = array(
							'435x375' => __('Web: height is 375px, width is 435px', 'psp'),
							'280x375' => __('Mobile (non-retina displays): height is 375px, width is 280px', 'psp'),
							'560x750' => __('Mobile (retina displays): height is 750px, width is 560px', 'psp'),
							'280x150' => __('Small: height is 150px, width is 280px', 'psp'),
							'120x120' => __('Smallest: height is 120px, width is 120px', 'psp')
					);
					foreach ($opengraph_defaults as $k => $v){
						$html .= 	'<option value="' . ( $k ) . '" ' . ( $val == $k ? 'selected="true"' : '' ) . '>' . ( $v ) . '</option>';
					}
				$html .= '
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		';
			//$output = ob_get_contents();
			//ob_end_clean();
			return $html;
		}
		
		public function do_progress_bar($elem, $score) {
			?>
			<script type="text/javascript">
			var psp_progress_bar = (function ($) {
				(function init() {
					$(document).ready(function(){
						do_progress_bar( '<?php echo $elem; ?>', '<?php echo $score; ?>' );
					});
				})();
				
				function do_progress_bar( elem, score ) {
					score = score || 0;

					var progress_wrap = $('.psp-progress'),
					progress_bar = progress_wrap.find( elem );
					//var progress_score = progress_wrap.find('.psp-progress-score');

					progress_bar.attr('class', 'psp-progress-bar');

					
					//var width = progress_bar.width();
					//width = parseFloat( parseFloat( parseFloat( score / 100 ).toFixed(2) ) * width ).toFixed(1);

					var size_class = 'size_';
				
					if ( score >= 20 && score < 40 ){
						size_class += '20_40';
					}
					else if ( score >= 40 && score < 60 ){
						size_class += '40_60';
					}
					else if( score >= 60 && score < 80 ){
						size_class += '60_80';
					}
					else if( score >= 80 && score <= 100 ){
						size_class += '80_100';
					}
					else{
						size_class += '0_20';
					}
				
					progress_bar
					.addClass( size_class )
					.width( score + '%' );
				
					//progress_score.text( score + "%" );
				}
			})(jQuery);
			</script>
			<?php
		}
		
		public function quick_edit_post() {
			$req = array(
				'id'	=> isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0
			);

			$postID = $req['id'];
			if( $postID > 0 ) {
				return $this->optimize_page( $postID );
			}
			die(json_encode( array('status' => 'invalid') ));
		}
		
		/**
	    * Singleton pattern
	    *
	    * @return pspOnPageOptimization Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        if ( self::$_instance->the_plugin->capabilities_user_has_module('on_page_optimization') ) {
				add_action( 'admin_init', array( self::$_instance, 'page_seo_info' ) );
				self::$_instance->_customMetaBox(); //meta box for: category | tag | custom taxonomy
	        }

	        return self::$_instance;
	    }
	    
	    
	    /**
	     * Taxonomy meta box methods!
	     */
	    
		/**
	    * Register plug-in admin metaboxes
	    */
		public function _customMetaBox()
		{
			$taxonomy = isset( $_GET['taxonomy'] ) ? $_GET['taxonomy'] : null;
			if ( is_admin() && !is_null($taxonomy) )
				add_action( $taxonomy . '_edit_form', array( &$this, '_tax_meta_box' ), 10, 1 );
				add_action( 'edit_term', array( &$this, '_tax_meta_update' ), 99, 3 );
		}
		
		public function _tax_meta_box( $term ) 
		{
			?>
			<table class="form-table">
				<tbody>
					<tr class="form-field">
						<th valign="top" scope="row">
							<?php echo __('SEO Settings', 'psp');?>
						</th>
						<td>
			<?php
			echo '
				<div id="psp_onpage_optimize_meta_box" class="postbox psp-tax-meta-box">
					<div class="inside">';
			$this->printBoxInterface( $term );
			echo '	</div>
				</div>';?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php	
		}

		public function _tax_meta_update( $term_id, $tt_id, $taxonomy ) 
		{
			
			$post = $taxonomy;
			$post_id = $term_id;

			$postID = isset($post_id) && (int) $post_id > 0 ? $post_id : 0;
			if( $postID > 0 ){
				$focus_kw = isset($_REQUEST['psp-field-focuskw']) ? $_REQUEST['psp-field-focuskw'] : '';
				$__stat = $this->optimize_page( (object) array('term_id' => $term_id, 'taxonomy' => $taxonomy), $focus_kw );
			}
		}
    }
}

// Initialize the pspOnPageOptimization class
//$pspOnPageOptimization = new pspOnPageOptimization($this->cfg, ( isset($module) ? $module : array()) ); 
$pspOnPageOptimization = pspOnPageOptimization::getInstance($this->cfg, ( isset($module) ? $module : array()) );