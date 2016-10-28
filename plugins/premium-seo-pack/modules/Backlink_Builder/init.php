<?php
/*
* Define class pssBacklinkBuilder
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pssBacklinkBuilder') != true) {
    class pssBacklinkBuilder
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
		
		static private $importDirectoryRowsUrl = 'http://cc.aa-team.com/utils/premiumseopack-backlinkbuilder-list.json';

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Backlink_Builder/';
			$this->module = $this->the_plugin->cfg['modules']['Backlink_Builder'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			// ajax helper
			if ( $this->the_plugin->is_admin === true ) {
				add_action('wp_ajax_pspPageBuilderRequest', array( $this, 'ajax_request' ));
			}
        }
        

		/**
	    * Singleton pattern
	    *
	    * @return pssBacklinkBuilder Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('Backlink_Builder') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Backlink Builder', 'psp'),
		            __('Backlink Builder', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_Backlink_Builder",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
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
				
			$table_name = $wpdb->prefix . "psp_web_directories";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				
				if ($__stat!== false)
					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => ''
					)) );
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
			global $wpdb;
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('off_page_optimization|Backlink_Builder')->show_menu();
			?>
			<div id="psp-lightbox-overlay">
				<div id="psp-lightbox-container">
					<h1 class="psp-lightbox-headline">
						<span style="left: 10px;"><?php _e('The submit status was:', 'psp');?></span>
						<a href="#" class="psp-close-btn" title="Close Lightbox"></a>
					</h1>

					<div class="psp-seo-status-container" style="margin: 30px 0 0;">
			
						<div id="psp-lightbox-backlink-builder-response" style="text-align: center;">
							<br /><br />
							<a href="#" data-status="success" class="psp-button green psp-submit-status"><?php _e('Success submited', 'psp');?></a>&nbsp;
							<a href="#" data-status="error" class="psp-button red psp-submit-status"><?php _e('Error on submit', 'psp');?></a>
						</div>
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
					<?php echo $this->module['Backlink_Builder']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['Backlink_Builder']['description'];?></span>
					<?php
					$has_help = isset($this->module['Backlink_Builder']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['Backlink_Builder']['help']['type']) && $this->module['Backlink_Builder']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['Backlink_Builder']['help']['url'] ) . '">HELP</a>';
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
											<img src="<?php echo $this->module_folder;?>assets/link.png">
											<?php _e('Semi-automatic Backink Builder', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<div style="display: none;" id="psp-submit-status-values">
											<div class="submit_never"><?php ( _e('Never submit', 'psp') ); ?></div>
											<div class="submit_inprogress"><?php ( _e('Submit in progress', 'psp') ); ?></div>
											<div class="submit_error"><?php ( _e('Error on submit', 'psp') ); ?></div>
											<div class="submit_success"><?php ( _e('Submit successfully', 'psp') ); ?></div>
											
											
										</div>
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspWebDirectories',
													'custom_table'		=> "psp_web_directories",
													'custom_table_force_action' => true,
													//'deleted_field'		=> true,
													'show_header' 		=> true,
													'items_per_page' 	=> '10',
													'post_statuses' 	=> 'all',
													'notices'			=> array(
														'default_clause'	=> 'empty',
														'default'			=> '<span class="psp-message psp-warning" style="display: block;">' . __('Click the Import directory rows button to get the directory index urls!', 'psp') . '</span>'
													),
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),
														
														'submit_btn'		=> array(
															'th'	=> __('Submit', 'psp'),
															'td'	=> '%submit_btn%',
															'align' => 'center',
															'width' => '120'
														),
														
														
														'submit_status'		=> array(
															'th'	=> __('Submit status', 'psp'),
															'td'	=> '%submit_status%',
															'align' => 'center',
															'width' => '120'
														),

														'directory_name'		=> array(
															'th'	=> __('Directory Name', 'psp'),
															'td'	=> '%directory_name%',
															'align' => 'left'
														),

														'pagerank'		=> array(
															'th'	=> '<img src="' . ( $this->module_folder ) . 'assets/google.png" style="position: relative;bottom: -3px; left: -2px;"> ' . __('Pagerank', 'psp'),
															'td'	=> '%pagerank%',
															'align' => 'center',
															'width' => '80'
														),
														
														'alexa'		=> array(
															'th'	=> '<img src="' . ( $this->module_folder ) . 'assets/alexa.png" style="position: relative;bottom: -3px; left: -2px"> ' . __('Alexa', 'psp'),
															'td'	=> '%alexa%',
															'align' => 'center',
															'width' => '70'
														)
													),
													'mass_actions' 	=> array(
														
														'import' => array(
															'value' => __('Import directory rows', 'psp'),
															'action' => 'import_directory_rows',
															'color' => 'blue'
														),
														'delete_directory' => array(
															'value' => __('Delete selected rows', 'psp'),
															'action' => 'do_bulk_delete_directory_rows',
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
							
							<?php
							$website_profile = get_option( 'psp_website_profile', true );
							$website_profile = maybe_unserialize( $website_profile );

							if( $website_profile === true || count($website_profile) == 0 ){
								global $current_user;
	      						get_currentuserinfo();
								$page_details = $this->the_plugin->get_page_meta( home_url() );
								$website_profile_values = array(
									'page_title' => $page_details['page_title'],
									'page_meta_description' => $page_details['page_meta_description'],
									'page_meta_keywords' => $page_details['page_meta_keywords'],
									'author_name' => $current_user->user_firstname . " " . $current_user->user_lastname,
									'author_email' => $current_user->user_email
								);
							}else{
								
								$website_profile_values = array(
									'page_title' => $website_profile['website_title'],
									'page_meta_description' => $website_profile['website_meta_description'],
									'page_meta_keywords' => $website_profile['website_meta_keywords'],
									'author_name' => $website_profile['website_author_name'],
									'author_email' => $website_profile['website_author_email']
								);
							}
							?>
							<div class="psp-grid_4">
								<div class="psp-panel">
									<div class="psp-panel-header">
										<span class="psp-panel-title"> 
											<img src="<?php echo $this->module_folder;?>assets/website.png">
											<?php _e('Autofill options', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form action="#save_with_ajax" id="psp_website_profile" class="psp-form">
											
											<div class="psp-message" style="padding-left: 10px;">
												<?php _e('Drag this button to your bookmark bar:', 'psp'); ?>
												<a class="psp-button orange" style="display:inline-block; margin: 0px 0px 0px 10px; position: relative; bottom: -6px;" href="javascript:(function(){document.body.appendChild(document.createElement('script')).src='<?php echo $this->module_folder;?>/backlink.php';})();"><?php _e('Autofill', 'psp'); ?> <?php echo get_bloginfo();?> <?php _e('Metas', 'psp'); ?></a><br>
											</div>
											
											<input type="hidden" value="psp_website_profile" name="box_id" id="box_id">
											<input type="hidden" id="box_nonce" name="box_nonce" value="<?php echo wp_create_nonce( 'psp_website_profile-nonce');?>" />
											
											<div class="psp-form-row">
												<label for="services"><?php _e('Your Name:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><span style="color:red">*</span> <?php _e('This field is required.', 'psp'); ?></span>
													<input type="text" value="<?php echo $website_profile_values['author_name'];?>" name="website_author_name" id="website_author_name" style="width:30%">
												</div>
											</div>
											
											<div class="psp-form-row">
												<label for="services"><?php _e('Your Email:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><span style="color:red">*</span> <?php _e('This field is required.', 'psp'); ?></span>
													<input type="text" value="<?php echo $website_profile_values['author_email'];?>" name="website_author_email" id="website_author_email" style="width:35%">
												</div>
											</div>
											
											<div class="psp-form-row">
												<label for="services"><?php _e('Title:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><span style="color:red">*</span> <?php _e('This field is required.', 'psp'); ?></span>
													<input type="text" value="<?php echo $website_profile_values['page_title'];?>" name="website_title" id="website_title" style="width:40%">
												</div>
											</div>
											<div class="psp-form-row">
												<label for="services"><?php _e('URL:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><span style="color:red">*</span> <?php _e('This field is required.', 'psp'); ?></span>
													<input type="text" readonly value="<?php echo home_url();?>" name="website_url" id="website_url" style="width:60%">
												</div>
											</div>
											<div class="psp-form-row">
												<label for="services"><?php _e('Meta Description:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><?php _e('This field is not required.', 'psp'); ?></span>
													<textarea name="website_meta_description" id="website_meta_description" style="width:40%"><?php echo $website_profile_values['page_meta_description'];?></textarea>
												</div>
											</div>
											<div class="psp-form-row">
												<label for="services"><?php _e('Meta Keywords:', 'psp'); ?></label>
												<div class="psp-form-item large">
													<span class="formNote"><?php _e('This field is not required.', 'psp'); ?></span>
													<input type="text" value="<?php echo $website_profile_values['page_meta_keywords'];?>" name="website_meta_keywords" id="website_meta_keywords">
												</div>
											</div>
											<div style="display:none;" id="psp-status-box" class="psp-message"></div>
											<div class="psp-button-row">
												<input type="submit" class="psp-button green psp-saveOptions" value="<?php _e('Save the settings', 'psp'); ?>">
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
		* ajax_request, method
		* --------------------
		*
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			global $wpdb;
			$request = array(
				'id' 			=> isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0,
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? ($_REQUEST['sub_action']) : ''
			);

			if( $request['sub_action'] == 'changeStatus' ){
				$request['new_status'] = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : '';
				if( $request['new_status'] == 'in_progress' ){
					$request['new_status'] = 2;
				}
				
				if( $request['new_status'] == 'success' ){
					$request['new_status'] = 1;
				}
				
				if( $request['new_status'] == 'error' ){
					$request['new_status'] = 3;
				}
  
				if( (int)$request['new_status'] > 0 ){
					
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);
						
					$wpdb->update( 
						$wpdb->prefix . "psp_web_directories", 
						array( 
							'status' => $request['new_status']
						), 
						array( 'ID' => $request['id'] ), 
						array( 
							'%d'
						), 
						array( '%d' ) 
					);
					
					die( json_encode(array(
						'status' => 'valid'
					)) );
				}
			}

			if( $request['sub_action'] == 'getLightbox' ){
				$html = array();
				$row = $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "psp_web_directories WHERE 1=1 and id=" . ( $request['id'] ) . ";", ARRAY_A );
				if( $row != false && isset($row['id']) && $row['id'] == $request['id'] ){
					
					//$html[] = '<iframe id="website_frame" src="' .  home_url('?pspGetRemoteWebsite&url=') . ( $row['submit_url'] ) . '" frameborder="0" width="100%"></iframe>';
					$html[] = '<iframe id="website_frame" src="' .  ( $row['submit_url'] ) . '" frameborder="0" width="100%"></iframe>';
					die( json_encode(array(
						'status' => 'valid',
						'data' => $row,
						'html'	=> implode( "\n", $html )
					)) );
				}
			}
			
			if( $request['sub_action'] == 'removeDirectories' ){
				$this->delete_rows();
			}
			
			if ( $request['sub_action'] == 'import_directory_rows' ) {
				$response = $this->import_directory_rows();

				die( json_encode($response) );
			}
			
			die( json_encode(array(
				'status' => 'invalid'
			)) );
		}

		public function import_directory_rows() {
			$file_url = self::$importDirectoryRowsUrl;

			$ret = array(
				'status'		=> 'invalid',
				'html'			=> ''
			);

			$response = $this->the_plugin->remote_get( $file_url, 'noproxy' );
			if ( $response['status'] != 'valid' ) {
				return array_merge($ret, array('html' => $response['msg']));
			}
  
			// valid file request
			$file_content = $response['body'];
			$rows = json_decode($file_content);
			if ( !is_array($rows) || empty($rows) ) {
				return array_merge($ret, array('html' => __('invalid rows in json file!', 'psp')));
			}
			
			// valid file content
			global $wpdb;
			$table_name = $wpdb->prefix . "psp_web_directories";

			$total = count($rows); $c = 0;
			foreach ($rows as $k => $v) {
				$q = "insert ignore into `$table_name` (`id`, `directory_name`, `submit_url`, `pagerank`, `alexa`, `status`) values (%s, %s, %s, %s, %s, %s);";
				$res = $wpdb->query( $wpdb->prepare($q, $v->id, $v->directory_name, $v->submit_url, $v->pagerank, $v->alexa, $v->status) );
				if ( $res!==false && $res > 0 ) { // success
					$c++;
				}
			}

			return array_merge($ret, array('status' => 'valid', 'html' => sprintf( __('total rows in remote file: %s; inserted rows: %s. Reload page?', 'psp'), $total, $c)));
		}
		
		public function get_remote_website_content()
		{
			if( isset($_REQUEST['pspGetRemoteWebsite']) ){
				die;
				$url = isset($_REQUEST['url']) && trim($_REQUEST['url']) != "" ? $_REQUEST['url'] : '';
				// !! the best way is to made a duble check if the url is in you web directories DB
				
				
				$response = wp_remote_get( $url, array( 'timeout' => 15 ) ); 
				$html_data = wp_remote_retrieve_body( $response );
				
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
				if ( !empty($this->the_plugin->charset) )
					$doc = pspphpQuery::newDocument( $html_data, $this->the_plugin->charset );
				else
					$doc = pspphpQuery::newDocument( $html_data );
				
				$the_url = parse_url($url);
				$doc->find('head')->prepend( '<base href="' . ( $the_url['scheme'] . '://' . $the_url['host'] ) . '">' );
				
				// try to find the main submit form
				$submit_form = $doc->find('form[method="post"]');
				if( $submit_form->attr('action') == "" ){
					$submit_form->attr( 'action', $url );
				}
				
				$submit_form->attr( 'target', "_blank" );
				
				die( $doc->html() );
			}
		}
    }
}

// Initialize the pssBacklinkBuilder class
//$pssBacklinkBuilder = new pssBacklinkBuilder();
$pssBacklinkBuilder = pssBacklinkBuilder::getInstance();