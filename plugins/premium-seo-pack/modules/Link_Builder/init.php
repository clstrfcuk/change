<?php
/*
* Define class pspLinkBuilder
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLinkBuilder') != true) {
    class pspLinkBuilder
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
		
		//search phrase pattern
		//eliminated cases: (a,h,script,embed) tags and also any tag attributes!
		static protected $pattern = '/{phrase}(?!((?i:[^<]*<\s*\/?(?:a|h\d{1}|script|embed)>)|[^<]*>))/';
		
		static protected $strtolower;

		
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Link_Builder/';
			$this->module = $this->the_plugin->cfg['modules']['Link_Builder'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'Link_Builder' );
			
			$this->setStringFunc(); //string function per encoding!
	
			if ( $this->the_plugin->is_admin === true ) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));

				// ajax handler
				add_action('wp_ajax_pspGetUpdateDataBuilder', array( &$this, 'ajax_request' ));
				add_action('wp_ajax_pspAddToBuilder', array( &$this, 'addToBuilder' ));
				add_action('wp_ajax_pspRemoveFromBuilder', array( &$this, 'removeFromBuilder' ));
				add_action('wp_ajax_pspUpdateToBuilder', array( &$this, 'updateToBuilder' ));
				add_action('wp_ajax_pspGetHitsByPhrase', array( &$this, 'getHitsByPhrase' ));
				
				//delete bulk rows!
				add_action('wp_ajax_pspLinkBuilder_do_bulk_delete_rows', array( &$this, 'delete_rows' ));
			}
			
			// init module!
			if ( $this->the_plugin->is_admin !== true ) {
				$this->init();
			}
        }
        
		private function init() {
			if ( !$this->the_plugin->verify_module_status( 'Link_Builder' ) ) ; //module is inactive
			else {
				//if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') ) {
					$this->addFrontFilters();
				//}
			}
			//$this->createTable();
		}

		/**
	    * Singleton pattern
	    *
	    * @return pspLinkBuilder Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Link Builder', $this->the_plugin->localizationName),
		            __('Link Builder', $this->the_plugin->localizationName),
		            'read',
		           	$this->the_plugin->alias . "_Link_Builder",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		
		/**
		 * frontend methods: replace phrase with link!
		 *
		 */
		public function addFrontFilters() {
			add_filter('the_content', array( &$this, 'do_link'), 999);

			if ($this->settings['is_comment']=='yes')
				add_filter('comment_text', array( &$this, 'do_link'), 999);
		}
		
		public function do_link($content) {
			// use in this way for work with the shortcodes too
			$theContent = do_shortcode( $content );

			// get phrases to be replaced!
			$phrases = $this->getLinks();

			// set pattern
			self::$pattern .= 'um'; //default utf-8
			if ($this->settings['case_sensitive']!='yes') { //case insensitive!
				self::$pattern .= 'i';
			}
			
			// replace phrases with link aliases!
			if (is_array($phrases) && count($phrases)>0) {
				//$__phrases = '('.implode(')|(', array_keys($phrases)).')';
				foreach ($phrases as $phrase=>$linkInfo) {
					$linkAlias = '<a href="' . ($linkInfo['url']) . '" rel="' . ($linkInfo['rel']) . '" target="' . ($linkInfo['target']) . '">' . ($linkInfo['title']) . '</a>';
					
					if ( $linkInfo['max_replacements'] > 0 && $linkInfo['max_replacements'] <= 10 )
						$max_replacements = (int) $linkInfo['max_replacements'];
					/*else
						$max_replacements = (int) $this->settings['max_replacements'];*/
					if ( $max_replacements <= 0 || $max_replacements > 10 ) //default in anything went wrong!
						$max_replacements = 10;

					$pattern = $this->set_pattern( self::$pattern, $phrase );
					$theContent = preg_replace($pattern, $linkAlias, $theContent, $max_replacements, $nbFound);
				}
			}
			return $theContent;
		}
		
		private function set_pattern($pattern, $phrase) {
			return str_replace('{phrase}', $phrase, $pattern);
		}
		
		private function getLinks() {
			global $wpdb;
			
			$result_query = "SELECT a.url, a.phrase, a.title, a.rel, a.target, a.max_replacements FROM " . $wpdb->prefix . "psp_link_builder as a WHERE 1=1 and a.publish='Y' order by a.id asc;";
			$res = $wpdb->get_results( $result_query, ARRAY_A );

			$ret = array();
			if (is_array($res) && count($res)>0) {
				foreach ($res as $k=>$v) {
					$ret["{$v['phrase']}"] = $v;
				}
			}
			return $ret;
		}
		
		
		/**
		 * backend methods: build the admin interface
		 *
		 */
		private function createTable() {
			global $wpdb;
			
			// check if table exist, if not create table
			$table_name = $wpdb->prefix . "psp_link_builder";
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

				$sql = "
					CREATE TABLE IF NOT EXISTS " . $table_name . " (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `hits` int(10) DEFAULT '0',
					  `url` varchar(200) DEFAULT NULL,
					  `rel` enum('no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag') DEFAULT 'no',
					  `title` varchar(100) DEFAULT NULL,
					  `target` enum('no','_blank','_parent','_self','_top') DEFAULT 'no',
					  `phrase` varchar(100) DEFAULT NULL,
					  `post_id` int(10) DEFAULT '0',
					  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `publish` char(1) DEFAULT 'Y',
					  `max_replacements` smallint(2) DEFAULT '1',
					  PRIMARY KEY (`id`),
					  UNIQUE INDEX `unique` (`phrase`,`url`),
					  KEY `publish` (`publish`),
					  KEY `url` (`url`)
					);
					";
				//KEY `deleted` (`deleted`,`publish`),
				//`deleted` smallint(1) DEFAULT '0',

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				dbDelta($sql);
			}
		}
		
		/*
		* addToBuilder, method
		* ---------------------
		*
		* add new row into link builder table 
		*/
		public function addToBuilder( $info=array() )
		{
			global $wpdb;

			$request = array(
				'url' 		=> isset($_REQUEST['new_url']) ? trim($_REQUEST['new_url']) : '',
				'phrase' 	=> isset($_REQUEST['new_text']) ? trim($_REQUEST['new_text']) : '',
				'rel' 		=> isset($_REQUEST['new_rel']) ? trim($_REQUEST['new_rel']) : '',
				'title' 	=> isset($_REQUEST['new_title']) ? trim($_REQUEST['new_title']) : '',
				'target' 	=> isset($_REQUEST['new_target']) ? trim($_REQUEST['new_target']) : '',
				'hits' 		=> isset($_REQUEST['new_hits']) ? trim($_REQUEST['new_hits']) : '0',
				'max_replacements' 		=> isset($_REQUEST['new_max_replacements']) ? trim($_REQUEST['new_max_replacements']) : '1',
				
				'itemid' 	=> isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : $itemid
			);

			if ($request['url']=='' || $request['phrase']=='' || $request['title']=='') {
					die(json_encode(array(
						'status' => 'invalid',
						'data' => ''
					)));
			}

				$wpdb->insert( 
					$wpdb->prefix . "psp_link_builder", 
					array( 
						'url' 		=> $request['url'],
						'phrase' 	=> $request['phrase'],
						'rel'		=> $request['rel'],
						'title' 	=> $request['title'],
						'target' 	=> $request['target'],
						'hits'		=> $request['hits'],
						'max_replacements'	=> $request['max_replacements']
					), 
					array( 
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%d',
						'%d'
					)
				);
				$insert_id = $wpdb->insert_id;
				if ($insert_id<=0) {
					die(json_encode(array(
						'status' => 'invalid',
						'data' => ''
					)));
				}

			//keep page number & items number per page
			$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
					
			// return for ajax
			die(json_encode( array(
				'status' => 'valid',
				'data' => '' //$wpdb->last_query
			)));
		}
		
		/*
		* updateToBuilder, method
		* --------------------------
		*
		* update row from link builder table
		*/
		public function updateToBuilder()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
				'subaction' => isset($_REQUEST['subaction']) ? trim($_REQUEST['subaction']) : '',
				'rel' 		=> isset($_REQUEST['new_rel2']) ? trim($_REQUEST['new_rel2']) : '',
				'title' 	=> isset($_REQUEST['new_title2']) ? trim($_REQUEST['new_title2']) : '',
				'target' 	=> isset($_REQUEST['new_target2']) ? trim($_REQUEST['new_target2']) : '',
				'max_replacements' 	=> isset($_REQUEST['new_max_replacements2']) ? trim($_REQUEST['new_max_replacements2']) : '1'
			);
			
			if( $request['itemid'] > 0 ) {
				$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_link_builder WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				
				$row_id = (int)$row['id'];

				if ($row_id>0) {
				
					// publish/unpublish
					if ( $request['subaction']=='publish' ) {
						$wpdb->update( 
							$wpdb->prefix . "psp_link_builder", 
							array( 
								'publish'		=> $row['publish']=='Y' ? 'N' : 'Y'
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
					} else { // update row info!
						$wpdb->update( 
							$wpdb->prefix . "psp_link_builder", 
							array( 
								'rel'		=> $request['rel'],
								'title' 	=> $request['title'],
								'target' 	=> $request['target'],
								'max_replacements' 	=> $request['max_replacements']
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s',
								'%s',
								'%s',
								'%d'
							), 
							array( '%d' )
						);
					}
					
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);

					die(json_encode(array(
						'status' => 'valid'
					)));
				
				}

			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		/*
		* removeFromTable method
		* --------------------------
		*
		* remove (url,phrase) pair from table!
		*/
		public function removeFromBuilder()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ) {
				$wpdb->delete( 
					$wpdb->prefix . "psp_link_builder", 
					array( 'id' => $request['itemid'] ) 
				);
				
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
				
				die(json_encode(array(
					'status' => 'valid'
				)));
			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		/**
		 * delete Bulk rows!
		 */
		public function delete_rows() {
			global $wpdb; // this is how you get access to the database
			
			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 0
			);
			if ($request['id']!=0) {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (int) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}
				
			$table_name = $wpdb->prefix . "psp_link_builder";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				/*$query_update = "UPDATE " . ($table_name) . " set
						deleted=1
						where id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_update);*/
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
				
					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => ''
					)) );
				}
			}
			
			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => ''
			)) );
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
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
		<div id="psp-wrapper" class="fluid wrapper-psp">
			
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('off_page_optimization|Link_Builder')->show_menu();
			?>
		
			<div id="psp-lightbox-overlay">
				<div id="psp-lightbox-container">
					<h1 class="psp-lightbox-headline">
						<img class="psp-lightbox-icon" src="<?php echo $this->the_plugin->cfg['paths']['freamwork_dir_url'];?>images/light-bulb.png">
						<span id="link-title-details"><?php _e('Details:', $this->the_plugin->localizationName);?></span>
						<span id="link-title-add"><?php _e('Add new link:', $this->the_plugin->localizationName);?></span>
						<span id="link-title-upd"><?php _e('Update link:', $this->the_plugin->localizationName);?></span>
						<a href="#" class="psp-close-btn" title="<?php _e('Close Lightbox', $this->the_plugin->localizationName); ?>"></a>
					</h1>

					<div class="psp-seo-status-container">
						<div id="psp-lightbox-seo-report-response-details">
								<table width="100%">
									<tr>
										<td width="120"><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_url"></span></td>
									</tr>
									<tr>
										<td><label><?php _e('Text:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_text"></span></td>
									</tr>
									<tr>
										<td><label><?php _e('Title:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_title"></span></td>
									</tr>
									<tr>
										<td><label><?php _e('Rel:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_rel"></span></td>
									</tr>
									<tr>
										<td><label><?php _e('Target:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_target"></span></td>
									</tr>
									<tr>
										<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
										<td><span id="details_max_replacements"></span></td>
									</tr>
								</table>
						</div>
					
						<div id="psp-lightbox-seo-report-response">
							<form class="psp-add-link-form">
								<input type="hidden" id="new_hits" name="new_hits" value="0" />
								<table width="100%">
									<tr>
										<td width="80"><label><?php _e('Text:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_text" name="new_text" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_url" name="new_url" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="button" class="psp-button blue" value="Verify founds" id="psp-builder-verify-hits"><span style="margin-left:10px;" id="psp-builder-text-hits"><span style="font-weight:bold;"></span><?php _e(' posts|pages in which the text was found!', $this->the_plugin->localizationName); ?></span>
										</td>
									</tr>
									<tr>
										<td><label><?php _e('Title:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_title" name="new_title" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td><label><?php _e('Rel:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="rel" name="new_rel">
												<?php 
													$arr_rel = array( 'no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag' );
													foreach ($arr_rel as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td><label><?php _e('Target:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="target" name="new_target">
												<?php 
													$arr_target = array( 'no','_blank','_parent','_self','_top' );
													foreach ($arr_target as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="max_replacements" name="new_max_replacements">
												<?php 
													$arr_target = range(1, 10, 1);
													foreach ($arr_target as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="button" class="psp-button green" value="<?php _e('Add this new link', $this->the_plugin->localizationName); ?>" id="psp-submit-to-builder">
										</td>
									</tr>
								</table>
								
							</form>
						</div>
						
						<div id="psp-lightbox-seo-report-response2">
							<form class="psp-update-link-form">
								<input type="hidden" id="upd-itemid" name="upd-itemid" value="" />
								<table width="100%">
									<tr>
										<td width="80"><label><?php _e('Text:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_text2" name="new_text2" value="" class="psp-add-link-field" readonly disabled="disabled" /></td>
									</tr>
									<tr>
										<td><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_url2" name="new_url2" value="" class="psp-add-link-field" readonly disabled="disabled" /></td>
									</tr>
									<tr>
										<td><label><?php _e('Title:', $this->the_plugin->localizationName);?></label></td>
										<td><input type="text" id="new_title2" name="new_title2" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td><label><?php _e('Rel:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="rel2" name="new_rel2">
												<?php 
													$arr_rel = array( 'no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag' );
													foreach ($arr_rel as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td><label><?php _e('Target:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="target2" name="new_target2">
												<?php 
													$arr_target = array( 'no','_blank','_parent','_self','_top' );
													foreach ($arr_target as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
										<td>
											<select id="max_replacements2" name="new_max_replacements2">
												<?php 
													$arr_target = range(1, 10, 1);
													foreach ($arr_target as $key => $value) {
														echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
													}												
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="button" class="psp-button green" value="<?php _e('Update link info', $this->the_plugin->localizationName); ?>" id="psp-submit-to-builder2">
										</td>
									</tr>
								</table>
								
							</form>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>

			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>
			
			<!-- Content -->
			<div id="psp-content">
				
				<h1 class="psp-section-headline">
					<?php echo $this->module['Link_Builder']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['Link_Builder']['description'];?></span>
					<?php
					$has_help = isset($this->module['Link_Builder']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['Link_Builder']['help']['type']) && $this->module['Link_Builder']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['Link_Builder']['help']['url'] ) . '">HELP</a>';
						} 
					} 
					?>
				</h1>

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix" style="padding-top: 20px;">

						<!-- Content Area -->
						<div id="psp-content-area">
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<?php _e('SEO link builder (internal/external)', $this->the_plugin->localizationName);?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspLinkBuilder',
													'custom_table'		=> "psp_link_builder",
													'custom_table_force_action' => true,
													//'deleted_field'		=> true,
													'force_publish_field'=> false,
													'show_header' 		=> true,
													'items_per_page' 	=> 10,
													'post_statuses' 	=> 'all',
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', $this->the_plugin->localizationName),
															'td'	=> '%id%',
															'width' => '20'
														),

														'hits'		=> array(
															'th'	=> __('Hits', $this->the_plugin->localizationName),
															'td'	=> '%hits%',
															'width' => '15'
														),

														'url'		=> array(
															'th'	=> __('URL', $this->the_plugin->localizationName),
															'td'	=> '%builder_url%',
															'align' => 'left'
														),

														'phrase'		=> array(
															'th'	=> __('Phrase', $this->the_plugin->localizationName),
															'td'	=> '%builder_phrase%',
															'align' => 'center',
															'width' => '150'
														),
														
														/*'title'	=> array(
															'th'	=> __('Title', $this->the_plugin->localizationName),
															'td'	=> '%custom_title%',
															'align' => 'center',
															'width' => '80'
														),

														'rel'	=> array(
															'th'	=> __('Rel', $this->the_plugin->localizationName),
															'td'	=> '%builder_rel%',
															'align' => 'center',
															'width' => '30'
														),

														'target'	=> array(
															'th'	=> __('Target', $this->the_plugin->localizationName),
															'td'	=> '%builder_target%',
															'align' => 'center',
															'width' => '30'
														),*/
														
														'url_attributes'	=> array(
															'th'	=> __('Link Attributes', $this->the_plugin->localizationName),
															'td'	=> '%url_attributes%',
															'align' => 'center',
															'width' => '100'
														),

														'created'		=> array(
															'th'	=> __('Creation Date', $this->the_plugin->localizationName),
															'td'	=> '%created%',
															'width' => '115'
														),
														
																'publish_btn' => array(
																	'th'	=> __('Status', $this->the_plugin->localizationName),
																	'td'	=> '%button_publish%',
																	//'td'	=> '%button_html5data%',
																	'option' => array(
																		'value' => __('Unpublish', $this->the_plugin->localizationName),
																		'value_change' => __('Publish', $this->the_plugin->localizationName),
																		'action' => 'do_item_publish',
																		'color'	=> 'orange',
																	),
																	'width' => '40'/*,
																	'html5_data' => array(
																		'publish'	=> __('Publish', $this->the_plugin->localizationName),
																		'unpublish'	=> __('Unpublish', $this->the_plugin->localizationName),
																	)*/
																),
														
																'update_btn' => array(
																	'th'	=> __('Update', $this->the_plugin->localizationName),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Update', $this->the_plugin->localizationName),
																		'action' => 'do_item_update',
																		'color'	=> 'blue',
																	),
																	'width' => '30'
																),
					
																'delete_btn' => array(
																	'th'	=> __('Delete', $this->the_plugin->localizationName),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Delete', $this->the_plugin->localizationName),
																		'action' => 'do_item_delete',
																		'color'	=> 'red',
																	),
																	'width' => '30'
																)
													),
													'mass_actions' 	=> array(
														'add_new_link' => array(
															'value' => __('Add new link', $this->the_plugin->localizationName),
															'action' => 'do_add_new_link',
															'color' => 'blue'
														),
														'delete_all_rows' => array(
															'value' => __('Delete selected rows', $this->the_plugin->localizationName),
															'action' => 'do_bulk_delete_rows',
															'color' => 'red'
														)
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
		
		public function getHitsByPhrase() {
			global $wpdb;

			$postTypes = $this->get_postTypes();
			$postTypes2 = array_map( array($this, 'prepareForInList'), $postTypes);
			$postTypes2 = implode(',', $postTypes2);
			
			$postStatus = 'publish,private'; //publish,pending,draft,auto-draft,future,private,inherit,trash
			$postStatus2 = array_map( array($this, 'prepareForInList'), explode(',', $postStatus));
			$postStatus2 = implode(',', $postStatus2);

			$request = array(
				'phrase' 	=> isset($_REQUEST['phrase']) ? trim($_REQUEST['phrase']) : ''
			);
			
			$request['phrase'] = call_user_func( self::$strtolower, $request['phrase'] );
			
			$res = $wpdb->get_var( "SELECT count(a.id) as nb from " . $wpdb->prefix . "posts as a WHERE 1=1 and a.post_type in (". $postTypes2 .") and a.post_status in (". $postStatus2 .") and lower(a.post_content) REGEXP '[[:<:]]". (strtolower($request['phrase'])) ."[[:>:]]';" );
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> $res,
				'sql'	=> '' //$wpdb->last_query
			)) );
		}
		
		public function getHitsById( $itemid=array() ) {
			if ( !(is_array($itemid) && count($itemid)>0) ) {
				die(json_encode(array(
					'status' => 'invalid',
					'data' => ''
				)));
			}
			
			$postTypes = $this->get_postTypes();
			$postTypes2 = array_map( array($this, 'prepareForInList'), $postTypes);
			$postTypes2 = implode(',', $postTypes2);
			
			$ret = array();
			foreach ($itemid as $id) {

				$ret["$id"] = 0;
				$phrase = $wpdb->get_var( "SELECT phrase from " . $wpdb->prefix . "psp_link_builder WHERE 1=1 and id=" . ( $id ) . ";" );
				if (!is_null($phrase)) {

					$phrase = call_user_func( self::$strtolower, $phrase );
					
					$res = $wpdb->get_var( "SELECT count(a.id) as nb from " . $wpdb->prefix . "posts as a WHERE 1=1 and a.post_type in (".$postTypes2.") and a.post_content REGEXP '[[:<:]]".$phrase."[[:>:]]';" );
					$ret["$id"] = $res;
				}
			}
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> $ret
			)) );
		}
		
		public function get_postTypes() {
			$post_types = get_post_types(array(
				'public'   => true
			));
			//unset unusefull post types!
			unset($post_types['attachment'], $post_types['revision']);
			return $post_types;
		}

		public function ajax_request()
		{
			global $wpdb;

			$request = array(
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "psp_link_builder WHERE 1=1 and id=" . ( $request['itemid'] ) . ";" )
			)) );
		}
		
        private function setStringFunc() {
	    	self::$strtolower = (function_exists('mb_strtolower')) ? 'mb_strtolower' : 'strtolower';
        }
		
		private function prepareForInList($v) {
			return "'".$v."'";
		}

    }
}

// Initialize the pspLinkBuilder class
$pspLinkBuilder = pspLinkBuilder::getInstance();