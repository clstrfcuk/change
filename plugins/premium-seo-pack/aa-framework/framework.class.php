<?php
/**
 * AA-Team freamwork class
 * http://www.aa-team.com
 * =======================
 *
 * @package		psp
 * @author		Andrei Dinca, AA-Team
 * @version		2.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('psp') != true) {
	class psp {

		const VERSION = 1.0;
        
        // The time interval for the remote XML cache in the database (21600 seconds = 6 hours)
        const NOTIFIER_CACHE_INTERVAL = 21600;

		public $alias = 'psp';
		public $details = array();
		public $localizationName = 'psp';
		
		public $dev = '';
		public $debug = false;
		public $is_admin = false;

		/**
		 * configuration storage
		 *
		 * @var array
		 */
		public $cfg = array();
		
		/**
		 * logic storage
		 *
		 * @var string
		 */
		public $is_plugin_page = false;

		/**
		 * plugin modules storage
		 *
		 * @var array
		 */
		public $modules = null;

		/**
		 * errors storage
		 *
		 * @var object
		 */
		private $errors = null;

		/**
		 * DB class storage
		 *
		 * @var object
		 */
		public $db = array();

		public $facebookInstance = null;
		public $fb_user_profile = null;
		public $fb_user_id = null;

		public $plugin_hash = null;
		public $v = null;
		
		public $utf8;
		
		public $jsFiles = array();
        
        public $wp_filesystem = null;
		
		public $charset = '';
		
		public $pluginDepedencies = null;
		public $pluginName = 'PSP';
		
		public $buddypress_utils = null;
        
        public $app_settings = array(); // all plugin settings


		/**
		 * The constructor
		 */
		function __construct($here = __FILE__)
		{
			$this->is_admin = is_admin() === true ? true : false;
			
			$this->setIniConfiguration();
            
            // load WP_Filesystem 
            include_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
            global $wp_filesystem;
            $this->wp_filesystem = $wp_filesystem;

			$this->update_developer();
			
			$this->plugin_hash = get_option('psp_hash');

			// set the freamwork alias
			$this->buildConfigParams('default', array( 'alias' => $this->alias ));

			// get the globals utils
			global $wpdb;

			// store database instance
			$this->db = $wpdb;

			// instance new WP_ERROR - http://codex.wordpress.org/Function_Reference/WP_Error
			$this->errors = new WP_Error();

			// charset
			$optimizeSettings = $this->getAllSettings( 'array', 'on_page_optimization' );
			if ( isset($optimizeSettings['charset']) && !empty($optimizeSettings['charset']) ) {
			    $this->charset = $optimizeSettings['charset'];
            }
  
			// plugin root paths
			$this->buildConfigParams('paths', array(
				// http://codex.wordpress.org/Function_Reference/plugin_dir_url
				'plugin_dir_url' => str_replace('aa-framework/', '', plugin_dir_url( (__FILE__)  )),

				// http://codex.wordpress.org/Function_Reference/plugin_dir_path
				'plugin_dir_path' => str_replace('aa-framework/', '', plugin_dir_path( (__FILE__) ))
			));

			// add plugin lib design paths and url
			$this->buildConfigParams('paths', array(
				'design_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'lib/design',
				'design_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'lib/design'
			));

			// add plugin scripts paths and url
			$this->buildConfigParams('paths', array(
				'scripts_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'lib/scripts',
				'scripts_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'lib/scripts'
			));

			// add plugin admin paths and url
			$this->buildConfigParams('paths', array(
				'freamwork_dir_url' => $this->cfg['paths']['plugin_dir_url'] . 'aa-framework/',
				'freamwork_dir_path' => $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/'
			));

			// add core-modules alias
			$this->buildConfigParams('core-modules', array(
				'dashboard',
				'modules_manager',
				'setup_backup',
				'support',
				'remote_support',
				'frontend',
				'server_status'
			));

			// list of freamwork css files
			$this->buildConfigParams('freamwork-css-files', array(
				'core' => 'css/core.css',
				'panel' => 'css/panel.css',
				'form-structure' => 'css/form-structure.css',
				'form-elements' => 'css/form-elements.css',
				'form-message' => 'css/form-message.css',
				'button' => 'css/button.css',
				'table' => 'css/table.css',
				'tipsy' => 'css/tooltip.css',
				'admin' => 'css/admin-style.css',
				'additional' => 'css/additional.css'
			));

			// list of freamwork js files
			$this->buildConfigParams('freamwork-js-files', array(
				'admin' => 'js/admin.js',
				'hashchange' => 'js/hashchange.js',
				'ajaxupload' => 'js/ajaxupload.js',
				'tipsy'	=> 'js/tooltip.js',
				'percentageloader-0.1' => 'js/jquery.percentageloader-0.1.min.js',
				'flot-2.0' => 'js/jquery.flot.min.js',
				'flot-tooltip' => 'js/jquery.flot.tooltip.min.js',
				'flot-stack' => 'js/jquery.flot.stack.min.js',
				'flot-pie' => 'js/jquery.flot.pie.min.js',
				'flot-time' => 'js/jquery.flot.time.js',
				'flot-resize' => 'js/jquery.flot.resize.min.js'
			));

            // get plugin text details
            $this->get_plugin_data();

            if ( $this->is_admin ) {

    			// Validation - mandatory step, try to load the validation file
    			$v_file_path = $this->cfg['paths']['plugin_dir_path'] . 'validation.php';
    			if ( $this->verifyFileExists($v_file_path) ) {
    				if ( $this->doPluginValidation( $v_file_path ) ) {
    					require_once( $v_file_path );
    					$this->v = new psp_Validation();
    					$this->v->isReg($this->plugin_hash);
    				}
    			}
			
                // load menu
    			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/menu.php' );
    			
    			// Run the plugins section load method
    			add_action('wp_ajax_pspLoadSection', array( &$this, 'load_section' ));
    			
    			// Plugin Depedencies Verification!
    			if ( get_option('psp_depedencies_is_valid', false) ) {
    				require_once( $this->cfg['paths']['scripts_dir_path'] . '/plugin-depedencies/plugin_depedencies.php' );
    				$this->pluginDepedencies = new aaTeamPluginDepedencies( $this );
    
    				// activation redirect to depedencies page
    				if ( get_option('psp_depedencies_do_activation_redirect', false) ) {
    					add_action('admin_init', array($this->pluginDepedencies, 'depedencies_plugin_redirect'));
    					return false;
    				}
       
       				// verify plugin library depedencies
    				$depedenciesStatus = $this->pluginDepedencies->verifyDepedencies();
    				if ( $depedenciesStatus['status'] == 'valid' ) {
    					// go to plugin license code activation!
    					add_action('admin_init', array($this->pluginDepedencies, 'depedencies_plugin_redirect_valid'));
    				} else {
    					// create depedencies page
    					add_action('init', array( $this->pluginDepedencies, 'initDepedenciesPage' ), 5);
    					return false;
    				}
    			}
			} // end is_admin
			
			// Run the plugins initialization method
			add_action('init', array( &$this, 'initThePlugin' ), 5);

            if ( $this->is_admin ) {

    			// Run the plugins section options save method
    			add_action('wp_ajax_pspSaveOptions', array( &$this, 'save_options' ));
    
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspModuleChangeStatus', array( &$this, 'module_change_status' ));
    			
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspModuleChangeStatus_bulk_rows', array( &$this, 'module_bulk_change_status' ));
    
    			// Run the plugins section options save method
    			add_action('wp_ajax_pspInstallDefaultOptions', array( &$this, 'install_default_options' ));
    
    			// W3CValidate helper
    			add_action('wp_ajax_pspW3CValidate', array( &$this, 'pspW3CValidate' ));
    
    			// W3CValidate helper
    			add_action('wp_ajax_pspUpload', array( &$this, 'upload_file' ));
    			add_action('wp_ajax_pspWPMediaUploadImage', array( &$this, 'wp_media_upload_image' ));
            } // end is_admin
			
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/utf8/utf8.php' );
			$this->utf8 = pspUtf8::getInstance();
			
			// admin ajax action
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/action_admin_ajax.php' );
			new pspActionAdminAjax( $this );
			
            if ( $this->is_admin ) {
    			// import seo data
    			require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/import_seodata.php' );
    			new pspImportSeoData( $this );
            }
			
			// buddy press utils
			if ( $this->is_buddypress() ) {
				require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/utils/buddypress.php' );
				$this->buddypress_utils = new pspBuddyPress( $this );
			}
			
			add_action('admin_init', array($this, 'plugin_redirect'));
			
			if( $this->debug == true ){
				add_action('wp_footer', array($this, 'print_psp_usages') );
				add_action('admin_footer', array($this, 'print_psp_usages') );
			}
            
            if ( $this->is_admin ) {
                add_action( 'admin_bar_menu', array($this, 'update_notifier_bar_menu'), 1000 );
                add_action( 'admin_menu', array($this, 'update_plugin_notifier_menu'), 1000 );
            }
			
            if ( $this->is_admin ) {
                require_once( $this->cfg['paths']['plugin_dir_path'] . 'aa-framework/ajax-list-table.php' );
                new pspAjaxListTable( $this );
            }
			
			// // load textdomain
			// add_action( 'plugins_loaded', array($this, 'psp_load_textdomain') );
			
			// shortcodes
			require_once($this->cfg['paths']['plugin_dir_path'] . 'aa-framework/shortcodes/shortcodes.init.php');
			new aafShortcodes( $this );
			
			// clean cronjobs
			$this->cronjobs_clean_fix();
			
			// fix bugs
			$this->fix_backlinkbuilder_linklist();
			
			$is_installed = get_option( $this->alias . "_is_installed" );
			if( $this->is_admin && $is_installed === false ) {
				add_action( 'admin_print_styles', array( $this, 'admin_notice_install_styles' ) );
			}

			if ( !$this->is_admin ) {
				if ( isset($_POST['ispspreq']) && in_array( $_POST['ispspreq'], array('tax', 'post') ) ) {
					if ( $_POST['ispspreq'] == 'post' )
						add_filter( 'the_content', array( $this, 'mark_content' ), 0, 1 );
					else if ( $_POST['ispspreq'] == 'tax' ) {
						add_filter( 'term_description', 'do_shortcode' );
						add_filter( 'term_description', array( $this, 'mark_content' ), 0, 1 );
					}
					add_action( 'wp', array( $this, 'clean_header' ) );
				}
			}
		}

		public function lang_init() 
		{ 
		    //load_plugin_textdomain( $this->alias, false, $this->cfg['paths']["plugin_dir_path"] . '/languages/');
		} 
		
		// public function psp_load_textdomain() {
			// load_plugin_textdomain( 'psp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		// }
		
		public function mark_content( $content ) 
		{
			return '<div id="psp-content-mark">' . $content . '</div>';
		}
		
		public function getPageContent( $post=null, $oldcontent='', $istax=false ) 
		{
			$optimizeSettings = $this->getAllSettings( 'array', 'on_page_optimization' );
			
			if ( !isset($optimizeSettings['parse_shortcodes']) 
				|| ( isset($optimizeSettings['parse_shortcodes']) && $optimizeSettings['parse_shortcodes'] != 'yes' ) ) {
				return $oldcontent;
			} 

			//if ( !is_singular() ) return false;
			if ( !is_admin() ) return $oldcontent;
			if ( is_null($post) || ( !is_object($post) && !is_array($post) ) ) return $oldcontent;
			if ( $istax ) {
				if ( is_object($post) && !isset($post->term_id) ) return $oldcontent;
				if ( is_array($post) && !isset($post['term_id']) ) return $oldcontent;
			} else {
				if ( is_object($post) && !isset($post->ID) ) return $oldcontent;
				if ( is_array($post) && !isset($post['ID']) ) return $oldcontent;
			}

			if ( $istax ) {
				//return $oldcontent; // unnecessary for taxonomy!
				if ( is_object($post) ) {
					$id = (int) $post->term_id;
				} else if ( is_array($post) ) {
					$id = (int) $post['term_id'];
					$post = (object) $post;
				}
				$url = get_term_link($post);
			} else {
				$id = isset($post) && is_object($post) ? (int) $post->ID : 0;
				$url = wp_get_shortlink($id);
			}
			//$url .= "&ispspreq=yes";

			/*$content = $this->remote_get( $url, 'default', array() );
			//$content = file_get_contents( $url );
			if ( !isset($content) || $content['status'] === 'invalid' ) return $oldcontent;
			$content = $content['body'];*/
			
			//var_dump('<pre>',$url,'</pre>'); die;  
			
			// check if will be redirected
			$headers = @get_headers( $url, 1 );
			if(isset($headers['Location'])) {
        		$url = $headers['Location']; // string
			}
			
			$resp = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 10,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array( 'ispspreq' => ( $istax ? 'tax' : 'post' ) ),
				'cookies' => array()
			));

			if ( is_wp_error( $resp ) ) { // If there's error
				//$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				return $oldcontent;
			}
			$content = wp_remote_retrieve_body( $resp );
  
			//$pattern = "/\[pspmark\].*\[\/pspmark\]/imu";
			//$ret = preg_match($pattern, $content, $matches);
  
			// php query class
			require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
			if ( !empty($this->charset) )
				$doc = pspphpQuery::newDocument( $content, $this->charset );
			else
				$doc = pspphpQuery::newDocument( $content );
			
			$content = pspPQ('#psp-content-mark');
			$content = $content->html();
  
			return $content;
		}

		public function clean_header() 
		{

            remove_action('wp_head', 'feed_links_extra', 3); // This is the main code that removes unwanted RSS Feeds
            remove_action('wp_head', 'feed_links', 2); // Removes Post and Comment Feeds
            remove_action('wp_head', 'rsd_link'); // Removes link to RSD + XML
            remove_action('wp_head', 'wlwmanifest_link'); // Removes the link to Windows manifest
            remove_action('wp_head', 'index_rel_link'); // Removes the index link
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // Remove relational links for the posts adjacent to the current post.
            remove_action('wp_head', 'wp_generator'); // Remove the XHTML generator link
            remove_action('wp_head', 'rel_canonical'); // Remov canonical url
            remove_action('wp_head', 'start_post_rel_link', 10, 0); // Remove start link
            remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Remove previous/next link
            remove_action('wp_head', 'locale_stylesheet'); // Remove local stylesheet from theme
		}

		public function clean_footer() 
		{
			echo ''; 
		}
		
		private function doPluginValidation( $file = '' ) 
		{
			$lines = file( $file );
			
			if ( $lines===false ) return false;
			if ( !is_array($lines) || count($lines) <=1 ) return false;

			if ( trim( $lines[7] ) != "! defined( 'ABSPATH' ) and exit;" ) return false;
			if ( trim( $lines[9] ) != 'if(class_exists(\'psp_Validation\') != true) {' ) return false;
			if ( trim( $lines[29] ) != 'add_action(\'wp_ajax_\' . ( self::ALIAS ) . \'TryActivate\', array( $this, \'aaTeamServerValidate\' ));' ) return false;
			if ( trim( $lines[78] ) != 'function isReg ( $hash )' ) return false;
			if ( trim ( $lines[38] ) != '$input = wp_remote_request( \'http://cc.aa-team.com/validation/validate.php?ipc=\' .' ) return false;
			return true;
		}

		public function admin_notice_install_styles()
		{
			wp_enqueue_style( $this->alias . '-activation', $this->cfg['paths']['freamwork_dir_url'] . 'css/activation.css');
			
			add_action( 'admin_notices', array( $this, 'admin_install_notice' ) );
		}

		public function admin_install_notice()
		{
		?>
		<div id="message" class="updated aaFrm-message_activate wc-connect">
			<div class="squeezer">
				<h4><?php _e( '<strong>Premium SEO Pack</strong> &#8211; You\'re almost ready :)', $this->localizationName ); ?></h4>
				<p class="submit"><a href="<?php echo admin_url( 'admin.php?page=' . $this->alias ); ?>#setup_backup" class="button-primary"><?php _e( 'Install Default Config', $this->localizationName ); ?></a></p>
			</div>
		</div>
		<?php	
		}
		
		public function update_developer()
		{
		    return true;
			if ( in_array($_SERVER['REMOTE_ADDR'], array('86.124.69.217', '86.124.76.250')) ) {
				$this->dev = 'andrei';
			}
			else{
				$this->dev = 'gimi';
			}
		}

		public function plugin_redirect() {
			if (get_option('psp_do_activation_redirect', false)) {
				delete_option('psp_do_activation_redirect');
				wp_redirect( get_admin_url() . 'admin.php?page=psp' );
			}
		}

		public function activate()
		{
			add_option('psp_do_activation_redirect', true);
			add_option('psp_depedencies_is_valid', true);
			add_option('psp_depedencies_do_activation_redirect', true);
		}

		public function get_plugin_status ()
		{
			return $this->v->isReg( get_option('psp_hash') );
		}
		
		public function get_plugin_data()
		{
            $source = file_get_contents( $this->cfg['paths']['plugin_dir_path'] . "/plugin.php" );
            $tokens = token_get_all( $source );
            $data = array();
            if( trim($tokens[1][1]) != "" ){
                $__ = explode(PHP_EOL, $tokens[1][1]);
                foreach ($__ as $key => $value) {
                    $___ = explode(": ", $value);
                    if( count($___) == 2 ){
                        $data[trim(strtolower(str_replace(" ", '_', $___[0])))] = trim($___[1]);
                    }
                }               
            }
  
			$this->details = $data;
			return $data;  
		}

        public function update_plugin_notifier_menu() {
            if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available

                // Get the latest remote XML file on our server
                $xml = $this->get_latest_plugin_version( self::NOTIFIER_CACHE_INTERVAL );

                $plugin_data = get_plugin_data( $this->cfg['paths']['plugin_dir_path'] . 'plugin.php' ); // Read plugin current version from the main plugin file

                if( isset($plugin_data) && count($plugin_data) > 0 ){
                    if( (string)$xml->latest > (string)$plugin_data['Version']) { // Compare current plugin version with the remote XML version
                        add_dashboard_page(
                            $plugin_data['Name'] . ' Plugin Updates',
                            'PSP <span class="update-plugins count-1"><span class="update-count">New Updates</span></span>',
                            'administrator',
                            $this->alias . '-plugin-update-notifier',
                            array( $this, 'update_notifier' )
                        );
                    }
                }
            }
        }

        public function update_notifier() {
            $xml = $this->get_latest_plugin_version( self::NOTIFIER_CACHE_INTERVAL );
            $plugin_data = get_plugin_data( $this->cfg['paths']['plugin_dir_path'] . 'plugin.php' ); // Read plugin current version from the main plugin file
        ?>

            <style>
            .update-nag { display: none; }
            #instructions {max-width: 670px;}
            h3.title {margin: 30px 0 0 0; padding: 30px 0 0 0; border-top: 1px solid #ddd;}
            </style>

            <div class="wrap">

            <div id="icon-tools" class="icon32"></div>
            <h2><?php echo $plugin_data['Name'] ?> Plugin Updates</h2>
            <div id="message" class="updated below-h2"><p><strong>There is a new version of the <?php echo $plugin_data['Name'] ?> plugin available.</strong> You have version <?php echo $plugin_data['Version']; ?> installed. Update to version <?php echo $xml->latest; ?>.</p></div>
            <div id="instructions">
            <h3>Update Download and Instructions</h3>
            <p><strong>Please note:</strong> make a <strong>backup</strong> of the Plugin inside your WordPress installation folder <strong>/wp-content/plugins/<?php echo end(explode('wp-content/plugins/', $this->cfg['paths']['plugin_dir_path'])); ?></strong></p>
            <p>To update the Plugin, login to <a href="http://www.codecanyon.net/?ref=AA-Team">CodeCanyon</a>, head over to your <strong>downloads</strong> section and re-download the plugin like you did when you bought it.</p>
            <p>Extract the zip's contents, look for the extracted plugin folder, and after you have all the new files upload them using FTP to the <strong>/wp-content/plugins/<?php echo end(explode('wp-content/plugins/', $this->cfg['paths']['plugin_dir_path'])); ?></strong> folder overwriting the old ones (this is why it's important to backup any changes you've made to the plugin files).</p>
            <p>If you didn't make any changes to the plugin files, you are free to overwrite them with the new ones without the risk of losing any plugins settings, and backwards compatibility is guaranteed.</p>
            </div>
            <h3 class="title">Changelog</h3>
            <?php echo $xml->changelog; ?>

            </div>
        <?php
        }

        public function update_notifier_bar_menu() {
            if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available
                global $wp_admin_bar, $wpdb;

                // Don't display notification in admin bar if it's disabled or the current user isn't an administrator
                if ( !is_super_admin() || !is_admin_bar_showing() )
                return;

                // Get the latest remote XML file on our server
                // The time interval for the remote XML cache in the database (21600 seconds = 6 hours)
                $xml = $this->get_latest_plugin_version( self::NOTIFIER_CACHE_INTERVAL );

                if ( is_admin() )
                    $plugin_data = get_plugin_data( $this->cfg['paths']['plugin_dir_path'] . 'plugin.php' ); // Read plugin current version from the main plugin file

                    if( isset($plugin_data) && count($plugin_data) > 0 ){

                        if( (string)$xml->latest > (string)$plugin_data['Version']) { // Compare current plugin version with the remote XML version

                        $wp_admin_bar->add_menu(
                            array(
                                'id' => 'plugin_update_notifier',
                                'title' => '<span>' . ( $plugin_data['Name'] ) . ' <span id="ab-updates">New Updates</span></span>',
                                'href' => get_admin_url() . 'index.php?page=' . ( $this->alias ) . '-plugin-update-notifier'
                            )
                        );
                    }
                }
            }
        }

        public function get_latest_plugin_version($interval) {
            $base = array();
            $notifier_file_url = 'http://cc.aa-team.com/apps-versions/index.php?app=' . $this->alias;
            $db_cache_field = $this->alias . '_notifier-cache';
            $db_cache_field_last_updated = $this->alias . '_notifier-cache-last-updated';
            $last = get_option( $db_cache_field_last_updated );
            $now = time();

            // check the cache
            if ( !$last || (( $now - $last ) > $interval) ) {
                // cache doesn't exist, or is old, so refresh it
                if( function_exists('curl_init') ) { // if cURL is available, use it...
                    $ch = curl_init($notifier_file_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    $cache = curl_exec($ch);
                    curl_close($ch);
                } else {
                    // ...if not, use the common file_get_contents()
                    $cache = file_get_contents($notifier_file_url);
                }

                if ($cache) {
                    // we got good results
                    update_option( $db_cache_field, $cache );
                    update_option( $db_cache_field_last_updated, time() );
                }

                // read from the cache file
                $notifier_data = get_option( $db_cache_field );
            }
            else {
                // cache file is fresh enough, so read from it
                $notifier_data = get_option( $db_cache_field );
            }

            // Let's see if the $xml data was returned as we expected it to.
            // If it didn't, use the default 1.0 as the latest version so that we don't have problems when the remote server hosting the XML file is down
            if( strpos((string)$notifier_data, '<notifier>') === false ) {
                $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
            }

            // Load the remote XML data into a variable and return it
            $xml = simplexml_load_string($notifier_data);

            return $xml;
        }


		// add admin js init
		public function createInstanceFreamwork ()
		{
			echo "
			<script type='text/javascript'>
				var psp = pspFacebookPage();
			</script>";
		}

		/**
		 * Create plugin init
		 *
		 *
		 * @no-return
		 */
		public function initThePlugin()
		{
		    $is_admin = is_admin();
			$loadPluginData = false;

			// If the user can manage options, let the fun begin!
			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : ''; 
			if ( $is_admin /*&& current_user_can( 'manage_options' )*/ ) {
				if( (stripos($page,'codestyling') === false) ){
					// Adds actions to hook in the required css and javascript
					add_action( "admin_print_styles", array( &$this, 'admin_load_styles') );
					add_action( "admin_print_scripts", array( &$this, 'admin_load_scripts') );
					
					// get fatal errors
					add_action ( 'admin_notices', array( &$this, 'fatal_errors'), 10 );
	
					// get fatal errors
					add_action ( 'admin_notices', array( &$this, 'admin_warnings'), 10 );
				}
				
				// create dashboard page
				add_action( 'admin_menu', array( &$this, 'createDashboardPage' ) );
				
				$loadPluginData = true;
			} else if ( !$is_admin ) {
				$loadPluginData = true;
			}

			if ( $loadPluginData ) {
				// keep the plugin modules into storage
				$this->load_modules();

				// SEO rules class
				require_once( $this->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
			}
		}

		public function fixPlusParseStr ( $input=array(), $type='string' )
		{
			if($type == 'array'){
				if(count($input) > 0){
					$ret_arr = array();
					foreach ($input as $key => $value){
						$ret_arr[$key] = str_replace("###", '+', $value);
					}

					return $ret_arr;
				}

				return $input;
			}else{
				return str_replace('+', '###', $input);
			}
		}

		// saving the options
		public function save_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);
  
			// unserialize the request options
			$serializedData = $this->fixPlusParseStr(urldecode($_REQUEST['options']));

			$savingOptionsArr = array();

			parse_str($serializedData, $savingOptionsArr);

			$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array');

			// create save_id and remote the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']);

			// Verify that correct nonce was used with time limit.
			if( ! wp_verify_nonce( $savingOptionsArr['box_nonce'], $save_id . '-nonce')) die ('Busted!');
			unset($savingOptionsArr['box_nonce']);

			// special cases! - local seo
			if ( $save_id == 'psp_local_seo' && isset($savingOptionsArr['slug']) ) {
				$savingOptionsArr['slug'] = sanitize_title( $savingOptionsArr['slug'] );
			}
			if ( $save_id == 'psp_socialsharing' /*&& isset($savingOptionsArr['toolbar']) && $savingOptionsArr['toolbar']!='none'*/ ) {
				$__old_saving = get_option('psp_socialsharing', true);
				$__old_saving = maybe_unserialize($__old_saving);
				$__old_saving = (array) $__old_saving;
    
				//foreach (array('floating', 'content_horizontal', 'content_vertical') as $k=>$v) {
					if ( isset($savingOptionsArr['toolbar']) ) {
					foreach (array('-pages', '-exclude-categ') as $kk=>$vv) {
						$__key =  $savingOptionsArr['toolbar'] . $vv;
						if ( !array_key_exists($__key, $savingOptionsArr) )
							$savingOptionsArr["$__key"] = $__old_saving["$__key"] = array();
					}
					}
				//}
				$savingOptionsArr = array_replace_recursive( $__old_saving, $savingOptionsArr );
			}
            if ( $save_id == 'psp_Minify' ) {
                $__old_saving = get_option('psp_Minify', true);
                $__old_saving = maybe_unserialize($__old_saving);
                $__old_saving = (array) $__old_saving;
                
                $savingOptionsArr["cache"] = $__old_saving["cache"];
            }
			
			// options NOT saved to db from options panel!
			$opt_nosave = isset($_REQUEST['opt_nosave']) ? (array) $_REQUEST['opt_nosave'] : array();
			if ( !empty($opt_nosave) ) {
				$__old_saving = get_option($save_id, true);
				$__old_saving = maybe_unserialize($__old_saving);
				$__old_saving = (array) $__old_saving;

				foreach ($opt_nosave as $kk=>$vv) {
					// unset( $savingOptionsArr["$vv"] );
					if ( isset($__old_saving["$vv"]) )
						$savingOptionsArr["$vv"] = $__old_saving["$vv"];
				}
			}
			
			// prepare the data for DB update
			$savingOptionsArr = stripslashes_deep($savingOptionsArr);
			$saveIntoDb = serialize( $savingOptionsArr );

			// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
			update_option( $save_id, $saveIntoDb );

			die(json_encode( array(
				'status' => 'ok',
				'html' 	 => __('Options updated successfully', $this->localizationName)
			)));
		}
		
		public function save_theoption( $option_name, $option_value ) {
			$save_id = $option_name;

			// we receive unserialized option_value
			$savingOptionsArr = $option_value;
			$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array');
			
			// prepare the data for DB update
			$savingOptionsArr = stripslashes_deep($savingOptionsArr);
			$saveIntoDb = serialize( $savingOptionsArr );

			// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
			update_option( $save_id, $saveIntoDb );
		}
		
		public function get_theoption( $option_name ) {
			$opt = get_option( $option_name);
			if ( $opt === false ) return false;
			$opt = maybe_unserialize($opt);
			return $opt;
		}

		// saving the options
		public function install_default_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// unserialize the request options
			$serializedData = urldecode($_REQUEST['options']);

			$savingOptionsArr = array();
			parse_str($serializedData, $savingOptionsArr);
			
			// fix for setup
			if ( $savingOptionsArr['box_id'] == 'psp_setup_box' ) {
				$serializedData = preg_replace('/box_id=psp_setup_box&box_nonce=[\w]*&install_box=/', '', $serializedData);
				$savingOptionsArr['install_box'] = $serializedData;
				$savingOptionsArr['install_box'] = str_replace( "\\'", "\\\\'", $savingOptionsArr['install_box']);
			}
  
			// create save_id and remove the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']);

			// Verify that correct nonce was used with time limit.
			if( ! wp_verify_nonce( $savingOptionsArr['box_nonce'], $save_id . '-nonce')) die ('Busted!');
			unset($savingOptionsArr['box_nonce']);
			
			// default sql - tables & tables data!
			require_once( $this->cfg['paths']['plugin_dir_path'] . 'modules/setup_backup/default-sql.php');
			if ( $save_id != 'psp_setup_box' ) {
				$savingOptionsArr['install_box'] = str_replace( '\"', '"', $savingOptionsArr['install_box']);
			}

			// convert to array
			$pullOutArray = json_decode( $savingOptionsArr['install_box'], true );
			if(count($pullOutArray) == 0){
				die(json_encode( array(
					'status' => 'error',
					'html' 	 => __("Invalid install default json string, can't parse it!", $this->localizationName)
				)));
			}else{
   
				foreach ($pullOutArray as $key => $value){

					// prepare the data for DB update
					$saveIntoDb = ( $value );
					
					if( $saveIntoDb === true ){
						$saveIntoDb = 'true';
					} else if( $saveIntoDb === false ){
						$saveIntoDb = 'false';
					}
					
					//special case - it's not double serialized!
					if ($key=='psp_taxonomy_seo') {
						$saveIntoDb = $value;
						continue 1;
					}

					// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
					update_option( $key, $saveIntoDb );
				}
				
				// update is_installed value to true 
				update_option( $this->alias . "_is_installed", 'true');

				die(json_encode( array(
					'status' => 'ok',
					'html' 	 => __('Install default successful', $this->localizationName)
				)));
			}
		}

		public function submatch ($sub_match) {
			return '\u00' . dechex(ord($sub_match[1]));
		}

		public function options_validate ( $input )
		{
			//var_dump('<pre>', $input  , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		}

		public function module_change_status ( $resp='ajax' )
		{
			// remove action from request
			unset($_REQUEST['action']);

			// update into DB the new status
			$db_alias = $this->alias . '_module_' . $_REQUEST['module'];
			update_option( $db_alias, $_REQUEST['the_status'] );
			
			if ( $_REQUEST['module'] == 'facebook_planner' ) {
				if ( $_REQUEST['the_status'] == 'true' ) {

					// @at plugin/module activation - setup cron
					//wp_schedule_event(time(), 'hourly', 'pspwplannerhourlyevent');
					//add_action('pspwplannerhourlyevent', array( $this, 'fb_wplanner_do_this_hourly' ));
				} else if ( $_REQUEST['the_status'] == 'false' ) {

					// @at plugin/module deactivation - clean the scheduler on plugin deactivation
					//wp_clear_scheduled_hook('pspwplannerhourlyevent');
				}
			}

			if ( !isset($resp) || empty($resp) || $resp == 'ajax' ) {
				die(json_encode(array(
					'status' => 'ok'
				)));
			}
		}
		
		public function module_bulk_change_status ()
		{
			global $wpdb; // this is how you get access to the database

			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : ''
			);

			if (trim($request['id'])!='') {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (string) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}

			if (is_array($__rq2) && count($__rq2)>0) {
				foreach ($__rq2 as $kk=>$vv) {
					$_REQUEST['module'] = $vv;
					$this->module_change_status( 'non-ajax' );
				}
				
				die( json_encode(array(
					'status' => 'valid',
					'msg'	 => 'valid module change status Bulk'
				)) );
			}

			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => 'invalid module change status Bulk'
			)) );
		}

		// loading the requested section
		public function load_section ()
		{
			$request = array(
				'section' 		=> isset($_REQUEST['section']) ? strip_tags($_REQUEST['section']) : false,
				'subsection' 	=> isset($_REQUEST['subsection']) ? strip_tags($_REQUEST['subsection']) : false
			);
   
			// get module if isset
			if(!in_array( $request['section'], $this->cfg['activate_modules'])) die(json_encode(array('status' => 'err', 'msg' => __('invalid section want to load!', $this->localizationName))));

			$tryed_module = $this->cfg['modules'][$request['section']];
			if( isset($tryed_module) && count($tryed_module) > 0 ){
				// Turn on output buffering
				ob_start();
   
				$opt_file_path = $tryed_module['folder_path'] . 'options.php';
				if( is_file($opt_file_path) ) {
					require_once( $opt_file_path  );
				}
				$options = ob_get_contents(); //copy current buffer contents into $message variable and delete current output buffer
				ob_end_clean();
   
				if(trim($options) != "") {
					$options = json_decode($options, true);

					// Derive the current path and load up aaInterfaceTemplates
					$plugin_path = dirname(__FILE__) . '/';
					if(class_exists('aaInterfaceTemplates') != true) {
						require_once($plugin_path . 'settings-template.class.php');

						// Initalize the your aaInterfaceTemplates
						$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

						// then build the html, and return it as string
						$html = $aaInterfaceTemplates->bildThePage($options, $this->alias, $tryed_module);

						// fix some URI
						$html = str_replace('{plugin_folder_uri}', $tryed_module['folder_uri'], $html);

						if(trim($html) != "") {
							$headline = $tryed_module[$request['section']]['menu']['title'] . "<span class='psp-section-info'>" . ( $tryed_module[$request['section']]['description'] ) . "</span>";
							
							$has_help = isset($tryed_module[$request['section']]['help']) ? true : false;
							if( $has_help === true ){
								
								$help_type = isset($tryed_module[$request['section']]['help']['type']) && $tryed_module[$request['section']]['help']['type'] ? 'remote' : 'local';
								if( $help_type == 'remote' ){
									if ( is_array($tryed_module[$request['section']]['help']['url']) ) {
										if ( !empty($request['subsection']) )
											$docRemoteUrl = $tryed_module[$request['section']]['help']['url']["{$request['subsection']}"];
										else {
											reset( $tryed_module[$request['section']]['help']['url'] );
											$firstElem = key( $tryed_module[$request['section']]['help']['url'] );
											$docRemoteUrl = $tryed_module[$request['section']]['help']['url']["$firstElem"];
										}
									} else {
										$docRemoteUrl = $tryed_module[$request['section']]['help']['url'];
									} 
									$headline .= '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $docRemoteUrl ) . '">HELP</a>';
								} 
							}
   
							die( json_encode(array(
								'status' 	=> 'ok',
								'headline'	=> $headline,
								'html'		=> 	$html
							)) );
						}

						die(json_encode(array('status' => 'err', 'msg' => 'invalid html formatter!')));
					}
				}
			}
		}

		public function fatal_errors()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('fatal');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="error"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		public function admin_warnings()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('warning');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="updated"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		/**
		 * Builds the config parameters
		 *
		 * @param string $function
		 * @param array	$params
		 *
		 * @return array
		 */
		protected function buildConfigParams($type, array $params)
		{
			// check if array exist
			if(isset($this->cfg[$type])){
				$params = array_merge( $this->cfg[$type], $params );
			}

			// now merge the arrays
			$this->cfg = array_merge(
				$this->cfg,
				array(	$type => array_merge( $params ) )
			);
		}

		/*
		* admin_load_styles()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_frm_style() {
			$css = array();

			if( isset($this->cfg['freamwork-css-files'])
				&& is_array($this->cfg['freamwork-css-files'])
				&& !empty($this->cfg['freamwork-css-files'])
			) {
				foreach ($this->cfg['freamwork-css-files'] as $key => $value){
					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ) {
						
						$cssId = $this->alias . '-' . $key;
						$css["$cssId"] = $this->cfg['paths']['freamwork_dir_path'] . $value;
						// wp_enqueue_style( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value );
					} else {
						$this->errors->add( 'warning', __('Invalid CSS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong>. Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
			return $css;
		}
		public function admin_load_styles()
		{
			global $wp_scripts;
			
			$javascript = $this->admin_get_scripts();
			
            $style_url = $this->cfg['paths']['freamwork_dir_url'] . 'load-styles.php';
            if ( is_file( $this->cfg['paths']['freamwork_dir_path'] . 'load-styles.css' ) ) {
                $style_url = str_replace('.php', '.css', $style_url);
            }
			wp_enqueue_style( 'psp-aa-framework-styles', $style_url );
			
			if( in_array( 'jquery-ui-core', $javascript ) ) {
				$ui = $wp_scripts->query('jquery-ui-core');
				if ($ui) {
					$uiBase = "//code.jquery.com/ui/{$ui->ver}/themes/smoothness";
					wp_register_style('jquery-ui-core', "$uiBase/jquery-ui.css", FALSE, $ui->ver);
					wp_enqueue_style('jquery-ui-core');
				}
			}
			if( in_array( 'thickbox', $javascript ) ) wp_enqueue_style('thickbox');
		}

		/*
		* admin_load_scripts()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_scripts() {
			$javascript = array();
			
			$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
			$current_url = explode("wp-admin/", $current_url);
			if( count($current_url) > 1 ){ 
				$current_url = "/wp-admin/" . $current_url[1];
			}else{
				$current_url = "/wp-admin/" . $current_url[0];
			}
			
			if ( isset($this->cfg['modules'])
				&& is_array($this->cfg['modules']) && !empty($this->cfg['modules'])
			) {
			foreach( $this->cfg['modules'] as $alias => $module ){

				if( isset($module[$alias]["load_in"]['backend']) && is_array($module[$alias]["load_in"]['backend']) && count($module[$alias]["load_in"]['backend']) > 0 ){
					// search into module for current module base on request uri
					foreach ( $module[$alias]["load_in"]['backend'] as $page ) {
  
						$delimiterFound = strpos($page, '#');
						$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
						$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
						if(
							// $current_url == '/wp-admin/' . $page
							( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=psp' ) || ( !empty($page) && $urlfound > 0 ) )
							&& isset($module[$alias]['javascript']) ) {
  
							$javascript = array_merge($javascript, $module[$alias]['javascript']);
						}
					}
				}
			}
			} // end if

			$this->jsFiles = $javascript;
			return $javascript;
		}
		public function admin_load_scripts()
		{
			// very defaults scripts (in wordpress defaults)
			wp_enqueue_script( 'jquery' );
			
			$javascript = $this->admin_get_scripts();
			
			if( count($javascript) > 0 ){
				$javascript = @array_unique( $javascript );
  
				if( in_array( 'jquery-ui-core', $javascript ) ) wp_enqueue_script( 'jquery-ui-core' );
				if( in_array( 'jquery-ui-widget', $javascript ) ) wp_enqueue_script( 'jquery-ui-widget' );
				if( in_array( 'jquery-ui-mouse', $javascript ) ) wp_enqueue_script( 'jquery-ui-mouse' );
				if( in_array( 'jquery-ui-accordion', $javascript ) ) wp_enqueue_script( 'jquery-ui-accordion' );
				if( in_array( 'jquery-ui-autocomplete', $javascript ) ) wp_enqueue_script( 'jquery-ui-autocomplete' );
				if( in_array( 'jquery-ui-slider', $javascript ) ) wp_enqueue_script( 'jquery-ui-slider' );
				if( in_array( 'jquery-ui-tabs', $javascript ) ) wp_enqueue_script( 'jquery-ui-tabs' );
				if( in_array( 'jquery-ui-sortable', $javascript ) ) wp_enqueue_script( 'jquery-ui-sortable' );
				if( in_array( 'jquery-ui-draggable', $javascript ) ) wp_enqueue_script( 'jquery-ui-draggable' );
				if( in_array( 'jquery-ui-droppable', $javascript ) ) wp_enqueue_script( 'jquery-ui-droppable' );
				if( in_array( 'jquery-ui-datepicker', $javascript ) ) wp_enqueue_script( 'jquery-ui-datepicker' );
				if( in_array( 'jquery-ui-resize', $javascript ) ) wp_enqueue_script( 'jquery-ui-resize' );
				if( in_array( 'jquery-ui-dialog', $javascript ) ) wp_enqueue_script( 'jquery-ui-dialog' );
				if( in_array( 'jquery-ui-button', $javascript ) ) wp_enqueue_script( 'jquery-ui-button' );
				
				if( in_array( 'thickbox', $javascript ) ) wp_enqueue_script( 'thickbox' );
	
				// date & time picker
				if( !wp_script_is('jquery-timepicker') ) {
					if( in_array( 'jquery-timepicker', $javascript ) ) wp_enqueue_script( 'jquery-timepicker' , $this->cfg['paths']['freamwork_dir_url'] . 'js/jquery.timepicker.v1.1.1.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
				}
				
				// star rating - rateit
				if( !wp_script_is('jquery-rateit-js') ) {
					if( in_array( 'jquery-rateit-js', $javascript ) ) {
						
						if( !wp_style_is('jquery-rateit-css') )
							wp_enqueue_style( 'jquery-rateit-css' , $this->cfg['paths']['freamwork_dir_url'] . 'js/rateit/rateit.css' );
						wp_enqueue_script( 'jquery-rateit-js' , 	$this->cfg['paths']['freamwork_dir_url'] . 'js/rateit/jquery.rateit.min.js', array( 'jquery' ) );
					}
				}
			}

			if( count($this->cfg['freamwork-js-files']) > 0 ){
				foreach ($this->cfg['freamwork-js-files'] as $key => $value){

					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ){
						if( in_array( $key, $javascript ) ) wp_enqueue_script( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value );
					} else {
						$this->errors->add( 'warning', __('Invalid JS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong> . Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
		}

		/*
		 * Builds out the options panel.
		 *
		 * If we were using the Settings API as it was likely intended we would use
		 * do_settings_sections here. But as we don't want the settings wrapped in a table,
		 * we'll call our own custom wplanner_fields. See options-interface.php
		 * for specifics on how each individual field is generated.
		 *
		 * Nonces are provided using the settings_fields()
		 *
		 * @param array $params
		 * @param array $options (fields)
		 *
		 */
		public function createDashboardPage ()
		{
			if ( $this->capabilities_user_has_module('dashboard') ) {
			//if( $psp->can_manage('view_seo_dashboard') ){
				add_menu_page(
					__( 'Premium SEO Pack - Dashboard', $this->localizationName ),
					__( 'Premium SEO', $this->localizationName ),
					'read',
					$this->alias,
					array( &$this, 'manage_options_template' ),
					$this->cfg['paths']['plugin_dir_url'] . 'icon_16.png'
				);
			//}
			}
		}

		public function display_index_page()
		{
			echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		}

		public function manage_options_template()
		{
			// Derive the current path and load up aaInterfaceTemplates
			$plugin_path = dirname(__FILE__) . '/';
			if(class_exists('aaInterfaceTemplates') != true) {
				require_once($plugin_path . 'settings-template.class.php');

				// Initalize the your aaInterfaceTemplates
				$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

				// try to init the interface
				$aaInterfaceTemplates->printBaseInterface();
			}
		}

		/**
		 * Getter function, plugin config
		 *
		 * @return array
		 */
		public function getCfg()
		{
			return $this->cfg;
		}

		/**
		 * Getter function, plugin all settings
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllSettings( $returnType='array', $only_box='' )
		{
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "options where 1=1 and option_name REGEXP '" . ( $this->alias) . "_([a-z_]*)$';"; // ORDER BY option_name asc
			if (trim($only_box) != "") {
				$allSettingsQuery = "SELECT option_value, option_name FROM " . $this->db->prefix . "options where option_name = '" . ( $this->alias . '_' . $only_box) . "'";
			}
  
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
  
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					
					//special case - it's not double serialized!
					if ($value['option_name']=='psp_taxonomy_seo') {
						$return[$value['option_name']] = @unserialize($value['option_value']);
						continue 1;
					}
					
					if($value['option_value'] == 'true'){
						$return[$value['option_name']] = true;
					}else{
						$return[$value['option_name']] = maybe_unserialize($value['option_value']);
						$return[$value['option_name']] = maybe_unserialize($return[$value['option_name']]);
					}
				}
			}

			if(trim($only_box) != "" && isset($return[$this->alias . '_' . $only_box])){
				$return = $return[$this->alias . '_' . $only_box];
			}
   
			if($returnType == 'serialize'){
				return serialize($return);

			}else if( $returnType == 'array' ){
				return $return;
			}else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}

		/**
		 * Getter function, all products
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllProductsMeta( $returnType='array', $key='' )
		{
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "postmeta where 1=1 and meta_key='" . ( $key ) . "'";
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					if(trim($value['meta_value']) != ""){
						$return[] = $value['meta_value'];
					}
				}
			}

			if($returnType == 'serialize'){
				return serialize($return);
			}
			else if( $returnType == 'text' ){
				return implode("\n", $return);
			}
			else if( $returnType == 'array' ){
				return $return;
			}
			else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}

		/*
		* GET modules lists
		*/
		function load_modules( $pluginPage='' )
		{
			$folder_path = $this->cfg['paths']['plugin_dir_path'] . 'modules/';
			$cfgFileName = 'config.php';
			
			// static usage, modules menu order
			$menu_order = array();
			
			$modules_list = glob($folder_path . '*/' . $cfgFileName);
			$nb_modules = count($modules_list);
			if ( $nb_modules > 0 ) {
				foreach ($modules_list as $key => $mod_path ) {

					$dashboard_isfound = preg_match("/modules\/dashboard\/config\.php$/", $mod_path);
					$depedencies_isfound = preg_match("/modules\/depedencies\/config\.php$/", $mod_path);
					
					if ( $pluginPage == 'depedencies' ) {
						if ( $depedencies_isfound!==false && $depedencies_isfound>0 ) ;
						else continue 1;
					} else {
						if ( $dashboard_isfound!==false && $dashboard_isfound>0 ) {
							unset($modules_list[$key]);
							$modules_list[$nb_modules] = $mod_path;
						}
					}
				}
			}
  
			foreach($modules_list as $module_config ){
				$module_folder = str_replace($cfgFileName, '', $module_config);
  
				// Turn on output buffering
				ob_start();

				if( is_file( $module_config ) ) {
					require_once( $module_config  );
				}
				$settings = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer
			
				if(trim($settings) != "") {
					$settings = json_decode($settings, true);
					$__settings = array_keys($settings); // e-strict solve!
					$alias = (string)end($__settings);

					// create the module folder URI
					// fix for windows server
					$module_folder = str_replace( DIRECTORY_SEPARATOR, '/',  $module_folder );

					$__tmpUrlSplit = explode("/", $module_folder);
					$__tmpUrl = '';
					$nrChunk = count($__tmpUrlSplit);
					if($nrChunk > 0) {
						foreach ($__tmpUrlSplit as $key => $value){
							if( $key > ( $nrChunk - 4) && trim($value) != ""){
								$__tmpUrl .= $value . "/";
							}
						}
					}

					// get the module status. Check if it's activate or not
					$status = false;

					// default activate all core modules
					if ( $pluginPage == 'depedencies' ) {
						if ( $alias != 'depedencies' ) continue 1;
						else $status = true;
					} else {
						if ( $alias == 'depedencies' ) continue 1;
						
						if(in_array( $alias, $this->cfg['core-modules'] )) {
							$status = true;
						}else{
							// activate the modules from DB status
							$db_alias = $this->alias . '_module_' . $alias;
	
							if(get_option($db_alias) == 'true'){
								$status = true;
							}
						}
					}
					
					// push to modules array
					$this->cfg['modules'][$alias] = array_merge(array(
						'folder_path' 	=> $module_folder,
						'folder_uri' 	=> $this->cfg['paths']['plugin_dir_url'] . $__tmpUrl,
						'db_alias'		=> $this->alias . '_' . $alias,
						'alias' 		=> $alias,
						'status'		=> $status
					), $settings );

					// add to menu order array http://cc.aa-team.com/wp-plugins/smart-seo-v2/wp-admin/admin-ajax.php?action=pspLoadSection&section=Social_Stats
					if(!isset($this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']])){
						$this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']] = $alias;
					}else{
						// add the menu to next free key
						$this->cfg['menu_order'][] = $alias;
					}

					// add module to activate modules array
					if($status == true){
						$this->cfg['activate_modules'][$alias] = true;
					}

					// load the init of current loop module
					$time_start = microtime(true);
					$start_memory_usage = (memory_get_usage());
					
					// in backend
					if( $this->is_admin === true && isset($settings[$alias]["load_in"]['backend']) ){
						
						$need_to_load = false;
						if( is_array($settings[$alias]["load_in"]['backend']) && count($settings[$alias]["load_in"]['backend']) > 0 ){
						
							$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
							$current_url = explode("wp-admin/", $current_url);
							if( count($current_url) > 1 ){ 
								$current_url = "/wp-admin/" . $current_url[1];
							}else{
								$current_url = "/wp-admin/" . $current_url[0];
							}
							foreach ( $settings[$alias]["load_in"]['backend'] as $page ) {

								$delimiterFound = strpos($page, '#');
								$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
								$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
								
								$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
								$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
								if(
									// $current_url == '/wp-admin/' . $page ||
									( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=psp' ) || ( !empty($page) && $urlfound > 0 ) )
									|| ( $action == 'pspLoadSection' && $section == $alias )
									|| substr($action, 0, 3) == 'psp'
								){
									$need_to_load = true;  
								}
							}
						}
  
						if( $need_to_load == false ){
							continue;
						}  
					}
					
					if( $this->is_admin === false && isset($settings[$alias]["load_in"]['frontend']) ){
						
						$need_to_load = false;
						if( $settings[$alias]["load_in"]['frontend'] === true ){
							$need_to_load = true;
						}
						if( $need_to_load == false ){
							continue;
						}  
					}  
					
					if( $status == true && isset( $settings[$alias]['module_init'] ) ){
						if( is_file($module_folder . $settings[$alias]['module_init']) ){
							//if( is_admin() ) {
								$current_module = array($alias => $this->cfg['modules'][$alias]); 
								require_once( $module_folder . $settings[$alias]['module_init'] );
								
								$time_end = microtime(true);
								$this->cfg['modules'][$alias]['loaded_in'] = $time_end - $time_start;
								
								$this->cfg['modules'][$alias]['memory_usage'] = (memory_get_usage() ) - $start_memory_usage;
								if( (float)$this->cfg['modules'][$alias]['memory_usage'] < 0 ){
									 
									$this->cfg['modules'][$alias]['memory_usage'] = 0.0;
								}
							//}
						}
					}
				}
			}

			// order menu_order ascendent
			ksort($this->cfg['menu_order']);
		}

		public function print_psp_usages()
		{
			$html = array();
			
			$html[] = '<style>
				.psp-bench-log {
					border: 1px solid #ccc; 
					width: 450px; 
					position: absolute; 
					top: 92px; 
					right: 2%;
					background: #95a5a6;
					color: #fff;
					font-size: 12px;
					z-index: 99999;
					
				}
					.psp-bench-log th {
						font-weight: bold;
						background: #34495e;
					}
					.psp-bench-log th,
					.psp-bench-log td {
						padding: 4px 12px;
					}
				.psp-bench-title {
					position: absolute; 
					top: 55px; 
					right: 2%;
					width: 425px; 
					margin: 0px 0px 0px 0px;
					font-size: 20px;
					background: #ec5e00;
					color: #fff;
					display: block;
					padding: 7px 12px;
					line-height: 24px;
					z-index: 99999;
				}
			</style>';
			
			$html[] = '<h1 class="psp-bench-title">PSP: Benchmark performance</h1>';
			$html[] = '<table class="psp-bench-log">';
			$html[] = 	'<thead>';
			$html[] = 		'<tr>';
			$html[] = 			'<th>Module</th>';
			$html[] = 			'<th>Loading time</th>';
			$html[] = 			'<th>Memory usage</th>';
			$html[] = 		'</tr>';
			$html[] = 	'</thead>';
			
			
			$html[] = 	'<tbody>';
			
			$total_time = 0;
			$total_size = 0;
			foreach ($this->cfg['modules'] as $key => $module ) {

				$html[] = 		'<tr>';
				$html[] = 			'<td>' . ( $key ) . '</td>';
				$html[] = 			'<td>' . ( number_format($module['loaded_in'], 4) ) . '(seconds)</td>';
				$html[] = 			'<td>' . (  $this->formatBytes($module['memory_usage']) ) . '</td>';
				$html[] = 		'</tr>';
			
				$total_time = $total_time + $module['loaded_in']; 
				$total_size = $total_size + $module['memory_usage']; 
			}

			$html[] = 		'<tr>';
			$html[] = 			'<td colspan="3">';
			$html[] = 				'Total time: <strong>' . ( $total_time ) . '(seconds)</strong><br />';			
			$html[] = 				'Total Memory: <strong>' . ( $this->formatBytes($total_size) ) . '</strong><br />';			
			$html[] = 			'</td>';
			$html[] = 		'</tr>';

			$html[] = 	'</tbody>';
			$html[] = '</table>';
			
			//echo '<script>jQuery("body").append(\'' . ( implode("\n", $html ) ) . '\')</script>';
			echo implode("\n", $html );
		}

		public function check_secure_connection ()
		{

			$secure_connection = false;
			if(isset($_SERVER['HTTPS']))
			{
				if ($_SERVER["HTTPS"] == "on")
				{
					$secure_connection = true;
				}
			}
			return $secure_connection;
		}


		/*
			helper function, image_resize
			// use timthumb
		*/
		public function image_resize ($src='', $w=100, $h=100, $zc=2)
		{
			// in no image source send, return no image
			if( trim($src) == "" ){
				$src = $this->cfg['paths']['freamwork_dir_url'] . '/images/no-product-img.jpg';
			}

			if( is_file($this->cfg['paths']['plugin_dir_path'] . 'timthumb.php') ) {
				return $this->cfg['paths']['plugin_dir_url'] . 'timthumb.php?src=' . $src . '&w=' . $w . '&h=' . $h . '&zc=' . $zc;
			}
		}

		/*
			helper function, upload_file
		*/
		public function upload_file ()
		{
			$slider_options = '';
			 // Acts as the name
            $clickedID = $_POST['clickedID'];
            // Upload
            if ($_POST['type'] == 'upload') {
                $override['action'] = 'wp_handle_upload';
                $override['test_form'] = false;
				$filename = $_FILES [$clickedID];

                $uploaded_file = wp_handle_upload($filename, $override);
                if (!empty($uploaded_file['error'])) {
                    echo json_encode(array("error" => "Upload Error: " . $uploaded_file['error']));
                } else {
                		
                    die( json_encode(array(
							"url" => $uploaded_file['url'],
							"thumb" => $this->image_resize( $uploaded_file['url'], $_POST['thumb_w'], $_POST['thumb_h'], $_POST['thumb_zc'] )
						)
					) );
                } // Is the Response
            }else{
				echo json_encode(array("error" => "Invalid action send" ));
			}

            die();
		}
		
		public function wp_media_upload_image()
		{
			$image = wp_get_attachment_image_src( (int)$_REQUEST['att_id'], 'thumbnail' );
			die(json_encode(array(
				'status' 	=> 'valid',
				'thumb'		=> $image[0]
			)));
		}

		/**
		 * Getter function, shop config
		 *
		 * @params $returnType
		 * @return array
		 */
		public function setConfig( $section='', $key='' ) {
            if( !is_array($this->app_settings) || empty($this->app_settings) ){
                $this->app_settings = $this->getAllSettings();
            }
		}
		public function getConfig( $section='', $key='', $returnAs='echo' )
		{
		    $this->setConfig( $section, $key );
			if( isset($this->app_settings[$this->alias . "_" . $section])) {
				if( isset($this->app_settings[$this->alias . "_" . $section][$key])) {
					if( $returnAs == 'echo' ) echo $this->app_settings[$this->alias . "_" . $section][$key];

					if( $returnAs == 'return' ) return $this->app_settings[$this->alias . "_" . $section][$key];
				}
			}
		}

		public function download_image( $file_url='', $pid=0, $action='insert' )
		{
			if(trim($file_url) != ""){

				// Find Upload dir path
				$uploads = wp_upload_dir();
				$uploads_path = $uploads['path'] . '';
				$uploads_url = $uploads['url'];

				$fileExt = end(explode(".", $file_url));
				$filename = uniqid() . "." . $fileExt;

				// Save image in uploads folder
				$response = wp_remote_get( $file_url );

				if( !is_wp_error( $response ) ){
					$image = $response['body'];
					file_put_contents( $uploads_path . '/' . $filename, $image );

					$image_url = $uploads_url . '/' . $filename; // URL of the image on the disk
					$image_path = $uploads_path . '/' . $filename; // Path of the image on the disk

					// Add image in the media library - Step 3
					$wp_filetype = wp_check_filetype( basename( $image_path ), null );
					$attachment = array(
					   'post_mime_type' => $wp_filetype['type'],
					   'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_path ) ),
					   'post_content'   => '',
					   'post_status'    => 'inherit'
					);

					$attach_id = wp_insert_attachment( $attachment, $image_path, $pid  );
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $image_path );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					return array(
						'attach_id' => $attach_id,
						'image_path' => $image_path
					);
				}
			}
		}

		public function remove_gallery($content) {
		    return str_replace('[gallery]', '', $content);
		}

		public function pspW3CValidate()
		{
			require_once( $this->cfg['modules']['W3C_HTMLValidator']['folder_path'] . 'app.class.php' );
			$pspW3C_HTMLValidator = new pspW3C_HTMLValidator($this->cfg, $module);
			$pspW3C_HTMLValidator->validateLink();
		}

		/**
	    * HTML escape given string
	    *
	    * @param string $text
	    * @return string
	    */
	    public function escape($text)
	    {
	        $text = (string) $text;
	        if ('' === $text) return '';

	        $result = @htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
	        if (empty($result)) {
	            $result = @htmlspecialchars(utf8_encode($text), ENT_COMPAT, 'UTF-8');
	        }

	        return $result;
	    }
		
		public function get_page_meta( $url='' )
		{
			$data = array();
			
			if( trim($url) != "" ){
				// try to get page meta 
				$response = wp_remote_get( $url, array( 'timeout' => 15 ) ); 
            
	            // If there's error
	            if ( is_wp_error( $response ) )
	                return $data;
            
            	$html_data = wp_remote_retrieve_body( $response );
				if( trim($html_data) != "" ){
					require_once( $this->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
					if ( !empty($this->charset) )
						$doc = pspphpQuery::newDocument( $html_data, $this->charset );
					else
						$doc = pspphpQuery::newDocument( $html_data );
					
					// try to get the page title
					$data['page_title'] = $doc->find('title')->text();
					
					// try to get the page meta description
					$data['page_meta_description'] = $doc->find('meta[name="description"]')->attr('content');
					
					// try to get the page meta keywords
					$data['page_meta_keywords'] = $doc->find('meta[name="keywords"]')->attr('content');
				}
				
				return $data;
			}
		}
		
		public function verify_module_status( $module='' ) {
			if ( empty($module) ) return false;

			$mod_active = get_option( 'psp_module_'.$module );
			if ( $mod_active != 'true' )
				return false; //module is inactive!
			return true;
		}
		
		public function edit_post_inline_data( $post_id, $seo=null, $tax=false, $post_content='empty' ) {
  
			if ( $this->__tax_istax( $tax ) ) { //taxonomy data!

				$post = $tax;

				$post_id = (int) $post->term_id;
				if( $post_content == 'empty' ){
					$post_content = $this->getPageContent( $post, $post->description, true );
				}
				$post_title = $post->name;
				
				$psp_current_taxseo = $this->__tax_get_post_meta( null, $post );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_metas = $this->__tax_get_post_meta( $psp_current_taxseo, $post, 'psp_meta' );
			} else {

				// global $post;
				$post = get_post($post_id);
				if ( isset($post) && is_object($post) ) {
					$post_id = (int) $post->ID;
					if( $post_content == 'empty' ){
						$post_content = $this->getPageContent( $post, $post->post_content );
					}
					$post_title = $post->post_title;
				} else {
					$post_id = 0;
					$post_content = '';
					$post_title = '';
				}
				$post_metas = get_post_meta( $post_id, 'psp_meta', true );
			}
			//$post = get_post( $post_id, ARRAY_A);
			//$post_metas = get_post_meta( $post_id, 'psp_meta', true);
			//$post_title = $post['post_title'];
			//$post_content = $post['post_content'];
			//$post_content = $this->getPageContent( $post, $post['post_content'] );
			
			$post_metas = array_merge(array(
				'title'			=> '',
				'description'		=> '',
				'keywords'		=> '',
				'focus_keyword'	=> '',
				'canonical'		=> '',
				'robots_index'	=> '',
				'robots_follow'	=> ''
			), (array) $post_metas);

			if ( is_null($seo) || !is_object($seo) ) {
				//use to generate meta keywords, and description for your requested item
				require_once( $this->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
				$seo = pspSeoCheck::getInstance();
			}

  
			// meta description
			$first_ph = $seo->get_first_paragraph( $post_content );
			$gen_meta_desc = $seo->gen_meta_desc( $first_ph );

			// meta keywords
			$gen_meta_keywords = array();
			//if ( !empty($post_metas['focus_keyword']) )
			//	$gen_meta_keywords[] = $post_metas['focus_keyword'];
			// focus keyword add to keywords is implemented in js file!
			$__tmp = $seo->gen_meta_keywords( $post_content );
			if ( !empty($__tmp) )
				$gen_meta_keywords[] = $__tmp;
			$gen_meta_keywords = implode(', ', $gen_meta_keywords);
			
			$post_metas['robots_index'] = isset($post_metas['robots_index']) && !empty($post_metas['robots_index'])
				? $post_metas['robots_index'] : 'default' ;
			$post_metas['robots_follow'] = isset($post_metas['robots_follow']) && !empty($post_metas['robots_follow'])
				? $post_metas['robots_follow'] : 'default';
			
			$html = array();
			$html[] = '<div class="psp-post-title">' . $post_title . '</div>';
			$html[] = '<div class="psp-post-gen-desc">' . $gen_meta_desc . '</div>';
			$html[] = '<div class="psp-post-gen-keywords">' . $gen_meta_keywords . '</div>';
			$html[] = '<div class="psp-post-meta-title">' . $post_metas['title'] . '</div>';
			$html[] = '<div class="psp-post-meta-description">' . $post_metas['description'] . '</div>';
			$html[] = '<div class="psp-post-meta-keywords">' . $post_metas['keywords'] . '</div>';
			$html[] = '<div class="psp-post-meta-focus-kw">' . $post_metas['focus_keyword'] . '</div>';
			$html[] = '<div class="psp-post-meta-canonical">' . $post_metas['canonical'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-index">' . $post_metas['robots_index'] . '</div>';
			$html[] = '<div class="psp-post-meta-robots-follow">' . $post_metas['robots_follow'] . '</div>';

			return implode(PHP_EOL, $html);
		}
		
		public function edit_post_inline_boxtpl() {
			/*
					<div>
						<span>Focus Keyword: </span>
						<input type="text" class="large-text" style="width: 300px;" value="" name="psp-editpost-meta-focus-kw" id="psp-editpost-meta-focus-kw">
					</div>
			*/
			$html = '
	<table class="psp-inline-edit-post form-table" style="border: 1px solid #dadada;">
		<thead>
			<tr>
				<th width="45%"><strong>PSP Quick SEO Edit</strong></th>
				<th width="30%">' . __('Meta Description', $this->localizationName) . '</th>
				<th width="25%">' . __('Meta Keywords', $this->localizationName) . '</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="45%">
					<div>
						<span>' . __('Meta Title:', $this->localizationName) . '</span>
						<input type="text" class="" style="" value="" name="psp-editpost-meta-title" id="psp-editpost-meta-title">
					</div>
					<div>
						<span>' . __('Canonical URL:', $this->localizationName) . '</span>
						<input type="text" class="" style="" value="" name="psp-editpost-meta-canonical" id="psp-editpost-meta-canonical">
					</div>
					<div>
						<span>' . __('Meta Robots Index:', $this->localizationName) . '</span>
						<select name="psp-editpost-meta-robots-index" id="psp-editpost-meta-robots-index">
							<option value="default" selected="true">' . __('Default settings', $this->localizationName) . '</option>
							<option value="index">' . __('Index', $this->localizationName) . '</option>
							<option value="noindex">' . __('NO Index', $this->localizationName) . '</option>
						</select>
					</div>
					<div>
						<span>' . __('Meta Robots Follow:', $this->localizationName) . '</span>
						<select name="psp-editpost-meta-robots-follow" id="psp-editpost-meta-robots-follow">
							<option value="default" selected="true">Default settings</option>
							<option value="follow">Follow</option>
							<option value="nofollow">NO Follow</option>
						</select>
					</div>
				</td>
				<td>
					<textarea name="psp-editpost-meta-description" id="psp-editpost-meta-description" rows="3" class="large-text"></textarea>
				</td>
				<td>
					<textarea name="psp-editpost-meta-keywords" id="psp-editpost-meta-keywords" rows="3" class="large-text"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<div style="float:left; width:100%;">
						<input type="button" value="' . __('Cancel', $this->localizationName) . '" id="psp-inline-btn-cancel" class="psp-button gray" style="float:left;">
						<input type="button" value="' . __('Save', $this->localizationName) . '" id="psp-inline-btn-save" class="psp-button blue" style="float:right;">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
			';
			return $html;
		}
		
	    /**
	     * Taxonomy meta box methods!
	     */
	    
	    // wp get_post_meta - for taxonomy 
	    public function __tax_get_post_meta( $post_meta=null, $post=null, $key='' ) {
	    	if ( !$this->__tax_istax( $post ) )
	    		return null;

			$psp_taxonomy_seo = $post_meta;
	    	if ( is_null($post_meta) ) {
	    		$psp_taxonomy_seo = get_option( 'psp_taxonomy_seo' );
	    		if ( $psp_taxonomy_seo===false )
	    			return null;
	    	}
	    	if ( is_null($psp_taxonomy_seo) )
	    		return null;
	    	if ( empty($psp_taxonomy_seo) )
				return null;

			if ( is_null($post_meta) ) {
				if ( isset($psp_taxonomy_seo[ "{$post->taxonomy}" ],
					$psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ]) )
					$psp_current_taxseo = $psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ];
				else return null;
			}
			else
				$psp_current_taxseo = $post_meta;

    		if ( !isset($psp_current_taxseo) || !is_array($psp_current_taxseo) )
	    			return null;

	    	if ( $key=='' )
	    		return $psp_current_taxseo;

	    	if ( isset($psp_current_taxseo[ "$key" ]) )
	    		return $psp_current_taxseo[ "$key" ];
	    	return null;
	    }

	    // wp update_post_meta - for taxonomy 
	    public function __tax_update_post_meta( $post=null, $keyval=array() ) {
	    	if ( !$this->__tax_istax( $post ) )
	    		return false;
	    		
	    	$psp_taxonomy_seo = get_option( 'psp_taxonomy_seo' );
	    	if ( $psp_taxonomy_seo===false )
	    		$psp_taxonomy_seo = array();
	    		
			if ( !is_array($keyval) || empty($keyval) ) // mandatory array of (key, value) pairs!
				return false;

	    	if ( empty($psp_taxonomy_seo) )
				$psp_taxonomy_seo = array();

    		$psp_current_taxseo = $psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ];

    		if ( !is_array($psp_current_taxseo) )
    			$psp_current_taxseo = array();

			foreach ( $keyval as $key => $value ) {
				if ( isset($psp_current_taxseo[" $key "]) )
					unset( $psp_current_taxseo[" $key "] );
				$psp_current_taxseo[ "$key" ] = $value;
			}

			$psp_taxonomy_seo[ "{$post->taxonomy}" ][ "{$post->term_id}" ] = $psp_current_taxseo;				
			update_option( 'psp_taxonomy_seo', $psp_taxonomy_seo );
	    }

	    // wp get_post - for taxonomy 
	    public function __tax_get_post( $post=null, $output='OBJECT', $filter='raw' ) {
			if ( !$this->__tax_istax( $post ) )
	    		return null;

			//$__post = get_term_by( 'id', $post->term_id, $post->taxonomy, $output, $filter );
			$__post = get_term( $post->term_id, $post->taxonomy, $output, $filter );
			return $__post!==false ? $__post : null;
	    }
	    
	    // verify a taxonomy is used!
	    public function __tax_istax( $post=null ) {
			$__istax = false; // default is post | page | custom post type edit page!
			if ( is_object($post) && count((array) $post)>=2
				&& isset($post->term_id) && isset($post->taxonomy)
				&& $post->term_id > 0 && !empty($post->taxonomy) )
				$__istax = true; // is category | tag | custom taxonomy edit page!
			return $__istax;
	    }
	    
	    
		/**
	     * remote_get - alternative to wp_remote_get by proxy!
	     */
		// return one random of the most common user agents
		public function fakeUserAgent()
		{
			$userAgents = array(
				'Mozilla/5.0 (Windows; U; Win95; it; rv:1.8.1) Gecko/20061010 Firefox/2.0',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-HK; rv:1.8.1.7) Gecko Firefox/2.0',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; es-AR; rv:1.9) Gecko/2008051206 Firefox/3.0',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6 ; nl; rv:1.9) Gecko/2008051206 Firefox/3.0',
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; es-AR; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11',
				'Mozilla/5.0 (X11; U; Linux x86_64; cy; rv:1.9.1b3) Gecko/20090327 Fedora/3.1-0.11.beta3.fc11 Firefox/3.1b3',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; ja; rv:1.9.2a1pre) Gecko/20090403 Firefox/3.6a1pre',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; Win64; x64; SV1)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; .NET CLR 1.1.4322)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 1.1.4322; InfoPath.2; .NET CLR 3.5.21022)',
				'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 1.1.4322; Tablet PC 2.0; OfficeLiveConnector.1.3; OfficeLivePatch.1.3; MS-RTC LM 8; InfoPath.3)',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; FDM; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 1.1.4322)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC; en) Opera 9.00',
				'Mozilla/5.0 (X11; Linux i686; U; en) Opera 9.00',
				'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC; en) Opera 9.00',
				'Opera/9.00 (Nintindo Wii; U; ; 103858; Wii Shop Channel/1.0; en)',
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.0; pt-br) Opera 9.25',
				'Opera/9.50 (Macintosh; Intel Mac OS X; U; en)',
				'Opera/9.61 (Windows NT 6.1; U; zh-cn) Presto/2.1.1',
				'Mozilla/5.0 (Windows NT 5.0; U; en-GB; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.61',
				'Opera/10.00 (X11; Linux i686; U; en) Presto/2.2.0',
				'Mozilla/5.0 (Macintosh; PPC Mac OS X; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 10.00',
				'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686 ; en) Opera 10.00',
				'Opera/9.80 (Windows NT 6.0; U; fi) Presto/2.2.0 Version/10.00',
				'Mozilla/5.0 (Windows; U; Windows NT 6.1; da) AppleWebKit/522.15.5 (KHTML, like Gecko) Version/3.0.3 Safari/522.15.5',
				'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; ar) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.1 Safari/525.18',
				'Mozilla/5.0 (Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0_1 like Mac OS X; hu-hu) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5G77 Safari/525.20',
				'Mozilla/5.0 (iPod; U; CPU iPhone OS 2_2_1 like Mac OS X; es-es) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5H11 Safari/525.20',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; he-IL) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.16',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_1; zh-CN) AppleWebKit/530.19.2 (KHTML, like Gecko) Version/4.0.2 Safari/530.19'
			);
			
			// rondomize user agents
			shuffle( $userAgents );
			return $userAgents[0];
		}
	
		// requestType : default | proxy | noproxy
		public function remote_get( $url, $requestType='default', $headers=array() ) { 
			$ret = array(
				'status'	=> 'invalid',
				'body'		=> '',
				'msg'		=> ''
			);

			$err = '';

			if ( $requestType == 'default' ) {

				if ( isset($headers) && !empty($headers) )
					$resp = wp_remote_get( $url, $headers );
				else
					$resp = wp_remote_get( $url );

				if ( is_wp_error( $resp ) ) { // If there's error
					$body = false;
					$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				}
				else {
					$body = wp_remote_retrieve_body( $resp );
				}
				//$body = file_get_contents( $url );
				
			}
			else if ( $requestType == 'noproxy' ) { // no Proxy!

				$args = array(
					'user-agent' => $this->fakeUserAgent(),
					'timeout' => 20
				);
				$resp = wp_remote_get( $url, $args );
				if ( is_wp_error( $resp ) ) { // If there's error
					$body = false;
					$err = htmlspecialchars( implode(';', $resp->get_error_messages()) );
				}
				else {
					$body = wp_remote_retrieve_body( $resp );
				}

			}

			if (is_null($body) || !$body || trim($body)=='') { //status is Invalid!
				$ret = array_merge($ret, array(
					'msg'		=> trim($err) != '' ? $err : 'empty body response retrieved!'
				)); //couldn't retrive data!
				return $ret;
			}
			$ret = array_merge($ret, array( //status is valid!
				'status'	=> 'valid',
				'body'		=> $body
			));
			return $ret;
		}
		
		// smushit
		public function smushit_show_sizes_msg_details( $meta=array(), $show_sizes=true ) {

			$ret = array();
            
            // get only selected sizes!
            $selected_sizes = $this->smushit_tinify_option('image_sizes');
			//if ( !isset($meta['psp_smushit']) || empty($meta['psp_smushit']) ) return $ret;

            // original file should be smushed
            if ( in_array('__original', $selected_sizes) ) {
                $ret[] = $meta['psp_smushit']['msg'];
            }

			if ( !$show_sizes )
			return $ret;

			// no media sizes
			if ( !isset($meta['sizes']) || empty($meta['sizes']) )
			return $ret;

			foreach ( $meta['sizes'] as $key => $val ) {
                // current size should be smushed
                if ( !in_array($key, $selected_sizes) ) continue 1;

				$ret[] = $val['psp_smushit']['msg'];
			}
			return $ret;
		}
        public function smushit_tinify_option($opt, $settings=array()) {
            if (empty($settings)) {
                $settings = (array) $this->get_theoption( 'psp_tiny_compress' );
            }

            $ret = null;
            if (!empty($settings) && is_array($settings) && isset($settings["$opt"])) {
                $ret = $settings["$opt"];
            }

            if ( $opt == 'image_sizes' ) {
                $ret = array_merge( array('__original' => '__original'), (array) $ret );
            }
            return $ret;
        }
        
		// rich snippets
		public function loadRichSnippets( $section='init' ) {

			if ( !in_array($section, array('init', 'options')) ) return false;

			$folder_path = $this->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/shortcodes/';

			if ( $section=='options') {
				$cfgFileName = 'options.php';
				$retOpt = array();
			}
			else if ( $section=='init') {
				$cfgFileName = 'init.php';
			}

			foreach(glob($folder_path . '*/' . $cfgFileName) as $module_config ){
				$module_folder = str_replace($cfgFileName, '', $module_config);

				if ( $section=='init') {

					if( $this->verifyFileExists( $module_config ) ) {
						require_once( $module_config  );
					}
				} else if ( $section=='options') {

					if( $this->verifyFileExists( $module_config ) ) {
						// Turn on output buffering
						ob_start();

						require( $module_config  );

						$options = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer

						if(trim($options) != "") {
							$options = json_decode($options, true);

							if ( is_array($options) && !empty($options) > 0 ) {
								$retOpt = array_merge( $retOpt, $options[0] );
							}
						}
					}
				}
			} // end foreach!

			if ( $section=='options')
				return array( $retOpt );
			else if ( $section=='init')
				return true;
		}

		public function generateRandomString( $length = 10 ) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}

		// Cron - facebook post planner
		public function fb_wplanner_do_this_hourly() {
			// Plugin cron class loading
			require_once ( $this->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/app.cron.class.php' );
		}
		
		/**
		 * User Roles - Capabilities
		 */
		public function capabilities_current_user_role() {
			// current user role
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			$user_role = array_shift($roles);
			return $user_role;
		}
		public function capabilities_user_has_module( $module='' ) {
			$user_role = $this->capabilities_current_user_role();
			
			// super admin or admin => has Full access to modules!
			if ( in_array($user_role, array('super_admin', 'administrator')) ) {
				return true;
			}
			
			// verify user has module!
			$capabilitiesRoles = $this->get_theoption('psp_capabilities_roles');
			if ( is_null($capabilitiesRoles) || !$capabilitiesRoles ) { // no capabilities for any user role defined!
				return true;
			}
			if ( isset($capabilitiesRoles["$user_role"]) && !is_null($capabilitiesRoles["$user_role"]) && is_array($capabilitiesRoles["$user_role"]) ) {
				$userModules = $capabilitiesRoles["$user_role"];
				$module = strtolower($module);
				$userModules = array_map('strtolower', $userModules);
				if ( in_array($module, $userModules) ) return true;
			}
			return false;
		}
		
		/**
		 * Cron Jobs - clean fix
		 */
		private function cronjobs_clean_fix() {
			$alreadyCleaned = get_option('psp_cronjobs_clean');
			
			$doit = false;
			if ( !isset($alreadyCleaned) || is_null($alreadyCleaned) || $alreadyCleaned===false || $alreadyCleaned!='done') {
				$doit = true;
			}
			
			// clean cronjobs
			if ( $doit ) {
				$this->cronjobs_clear_all_crons('pspwplannerhourlyevent');
				$this->cronjobs_clear_all_crons('psp_wplanner_hourly_event');
				$this->cronjobs_clear_all_crons('psp_start_cron_serp_check');
				
				update_option( 'psp_cronjobs_clean', 'done' );
			}
		}
		public function cronjobs_clear_all_crons( $hook ) {
			$crons = _get_cron_array();
			if ( empty( $crons ) ) {
				return;
			}
			foreach( $crons as $timestamp => $cron ) {
				if ( !empty( $cron[$hook] ) )  {
					unset( $crons[$timestamp][$hook] );
				}
				if ( empty($crons[$timestamp]) ) {
					unset($crons[$timestamp]);
				}
			}
			_set_cron_array( $crons );
		}
		
		/**
		 * Backlink builder - links list fix
		 */
		private function fix_backlinkbuilder_linklist() {
			$alreadyCleaned = get_option('psp_fix_backlinkbuilder');
			
			$doit = false;
			if ( !isset($alreadyCleaned) || is_null($alreadyCleaned) || $alreadyCleaned===false || $alreadyCleaned!='done') {
				$doit = true;
			}

			// clean cronjobs
			if ( $doit ) {
				global $wpdb;
				
				// delete record
				$table_name = $wpdb->prefix . "psp_web_directories";
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in ('278');";
				$__stat = $wpdb->query($query_delete);
				if ($__stat!== false) {
				}

				update_option( 'psp_fix_backlinkbuilder', 'done' );
			}
		}
		
		private function setIniConfiguration() {
			if ( ($memory_limit = ini_get('memory_limit')) !== false ) {
				if ( (int) $memory_limit < 256) {
					ini_set('memory_limit', '512M');
				}
			}
		}
		
		
		/**
		 * Social Sharing
		 */
		public function admin_notice_details() {
			$isPremium = false;
			if ( is_plugin_active( 'premium-seo-pack/plugin.php' ) ) {
				$__moduleIsActive = get_option('psp_module_Social_Stats');
				$__submoduleSocialShare = get_option('psp_socialsharing');
				if ( isset($__moduleIsActive) && $__moduleIsActive=='true'
				&& isset($__submoduleSocialShare) && $__submoduleSocialShare!==false )
					$isPremium = true;
			}
			
			if ( !$isPremium ) return false;

			wp_enqueue_style( $this->alias . '-activation', $this->cfg['paths']['freamwork_dir_url'] . 'css/activation.css');
			
			add_action( 'admin_notices', array( $this, 'admin_notice_text' ) );
		}

		public function admin_notice_text()
		{
		?>
		<div id="message" class="updated aaFrm-message_activate wc-connect">
			<div class="squeezer">
				<h4><?php _e( 'AA Social Share notice: you already use Premium SEO Pack - Social Stats module, Social Sharing section', $this->localizationName ); ?></h4>
			</div>
		</div>
		<?php	
		}
		
		
		/**
		 * Usefull
		 */
		
		//format right (for db insertion) php range function!
		public function doRange( $arr ) {
			$newarr = array();
			if ( is_array($arr) && count($arr)>0 ) {
				foreach ($arr as $k => $v) {
					$newarr[ $v ] = $v;
				}
			}
			return $newarr;
		}

		//verify if file exists!
		public function verifyFileExists($file, $type='file') {
			clearstatcache();
			if ($type=='file') {
				if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
					return false;
				}
				return true;
			} else if ($type=='folder') {
				if (!is_dir($file) || !is_readable($file)) {
					return false;
				}
				return true;
			}
			// invalid type
			return 0;
		}
		
		// Return current Unix timestamp with microseconds
 		// Simple function to replicate PHP 5 behaviour
		public function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}
		
		public function prepareForInList($v) {
			return "'".$v."'";
		}
		public function prepareForDbClean($v) {
			return trim($v);
		}
		
		public function formatBytes($bytes, $precision = 2) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB');

			$bytes = max($bytes, 0);
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
			$pow = min($pow, count($units) - 1);

			// Uncomment one of the following alternatives
			// $bytes /= pow(1024, $pow);
			$bytes /= (1 << (10 * $pow));

			return round($bytes, $precision) . ' ' . $units[$pow];
		}
		
		public function is_plugin_active( $plugin ) {
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->is_plugin_active_for_network( $plugin );
		}

		public function is_plugin_active_for_network( $plugin ) {
			if ( !is_multisite() )
				return false;
	
			$plugins = get_site_option( 'active_sitewide_plugins');
			if ( isset($plugins[$plugin]) )
				return true;
			return false;
		}
		
		public function print_module_error( $module=array(), $error_number, $title="" )
		{
			$html = array();
			if( count($module) == 0 ) return true;
  
			$html[] = '<div class="psp-grid_4 psp-error-using-module">';
			$html[] = 	'<div class="psp-panel">';
			$html[] = 		'<div class="psp-panel-header">';
			$html[] = 			'<span class="psp-panel-title">';
			$html[] = 				__( $title, $this->localizationName );
			$html[] = 			'</span>';
			$html[] = 		'</div>';
			$html[] = 		'<div class="psp-panel-content">';
			
			$error_msg = isset($module[$module['alias']]['errors'][$error_number]) ? $module[$module['alias']]['errors'][$error_number] : '';
			
			$html[] = 			'<div class="psp-error-details">' . ( $error_msg ) . '</div>';
			$html[] = 		'</div>';
			$html[] = 	'</div>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function convert_to_button( $button_params=array() )
		{
			$button = array();
			$button[] = '<a';
			if(isset($button_params['url'])) 
				$button[] = ' href="' . ( $button_params['url'] ) . '"';
			
			if(isset($button_params['target'])) 
				$button[] = ' target="' . ( $button_params['target'] ) . '"';
			
			$button[] = ' class="psp-button';
			
			if(isset($button_params['color'])) 
				$button[] = ' ' . ( $button_params['color'] ) . '';
				
			$button[] = '"';
			$button[] = '>';
			
			$button[] =  $button_params['title'];
		
			$button[] = '</a>';
			
			return implode("", $button);
		}


		/**
		 * Various
		 */ 
		public function get_wp_type() {
			global $blog_id;

			$wp_type = 'default';
			if( defined( 'SITE_ID_CURRENT_SITE' ) ) {
				if ( $blog_id != SITE_ID_CURRENT_SITE ) {
					$wp_type = 'multi';
				}
			}
			return apply_filters( 'psp_wp_type', $wp_type );
		}
        public function get_wp_pagetype() {
        	$page_type = array(
        		'type' => ''
			);

			//loop through all page types!
			if ( is_admin() ) {
				$page_type['type'] = 'admin';
			}
			else if ( is_feed() ) {
				$page_type['type'] = 'feed';
			}
            else if ( is_search() ) {
				$page_type['type'] = 'search';
            }
   			else if ( is_home() || is_front_page() ) {
            	$page_type['type'] = 'home';
            }
            else if ( is_single() ) {
            	$page_type['type'] = 'post';
            }
            else if ( is_page() ) {
            	$page_type['type'] = 'page';
            }
            else if ( is_attachment() ) { //treated like a page!
            	$page_type['type'] = 'page';
            }
            else if ( is_category() ) {
				$page_type['type'] = 'category';
            }
            else if ( is_tag() ) {
            	$page_type['type'] = 'tag';
            }
            else if ( is_tax() ) {
            	$page_type['type'] = 'taxonomy';
            }
            else if ( is_archive() ) {
            	$page_type['type'] = 'archive';
            }
            else if ( is_author() ) {
            	$page_type['type'] = 'author';
            }
            else if ( is_404() ) { 
				$page_type['type'] = '404';
            }

			$page_type = $page_type['type'];
			return apply_filters( 'premiumseo_seo_pagetype', $page_type );
        }

		public function get_wp_list_pagetypes() {

			$arr = array('home', 'post', 'page', 'category', 'tag', 'taxonomy', 'archive', 'author', 'search', '404');
			return apply_filters( 'premiumseo_seo_list_pagetypes', $arr );
		}
        
        public function get_wp_user_roles( $translate = false ) {
            global $wp_roles;
            if ( !isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
            
            $roles = $wp_roles->get_names();
            
            if ( $translate ) {
                foreach ($roles as $k => $v) {
                    $roles[$k] = __($v, 'psp');
                }
                asort($roles);
                // translation to be implemented!
                return $roles;
            } else {
                //$roles = array_keys($roles);
                foreach ($roles as $k => $v) {
                    $roles[$k] = ucfirst($k);
                }
                asort($roles);
                return $roles;
            }
        }


		/**
		 * Buddy Press
		 */
		public function is_buddypress() {
			if ( !defined( 'BP_VERSION' ) ){
				return false;
			}

	        global $bp;
	        if( $bp->maintenance_mode == 'install' ){
	            if( $_GET['page'] == 'psp' ){
	                $this->errors = __( 'The Buddypress installation it\'s not finished!', $this->localizationName );
	                add_action( 'admin_notices', array($this, 'bp_warning_box'), 1 );
	            }
	            return false;
	        }else{
	            return true;
	        }
		}
		public function is_buddypress_section() {
			if ( !$this->is_buddypress() ) return false;

			global $bp;
			$current_component = bp_current_component();
			$current_action = bp_current_action();

			$ret = array(
				'component' 	=> '',
				'action'		=> ''
			);
			if ( !empty($current_component) ) {
				
				$ret['component'] = $current_component;
				if ( !empty($current_action) ) {
					$ret['action'] = $current_action;
				}
				return $ret;
			}
			return false;
		}

		public function bp_warning_box(){
		?>
		<div class="updated"> <p><?php _e( '<strong>Premium SEO Pack</strong> | ', $this->localizationName ); ?><?php echo $this->errors; ?></p> </div>
        <?php
    	}
		
		// current page is assigned to display the site static front page
		public function _is_static_front_page() {
			return ( is_front_page() && 'page' == get_option( 'show_on_front' ) && is_page( get_option( 'page_on_front' ) ) );
		}
		
		// current page is the blog posts index page and shows posts
		public function _is_home_blog_posts_page() {
			return ( is_home() && 'page' != get_option( 'show_on_front' ) );
		}

		// current page is assigned to display the blog posts index page
		public function _is_blog_posts_page() {
			return ( is_home() && 'page' == get_option( 'show_on_front' ) );
		}
		
		public function get_php_ini_bool($value) {
			$value = (string) $value;
			$value = strtolower($value);
			return in_array($value, array('+', '1', 'y', 'on', 'yes', 'true', 'enabled')) ?
				true : in_array($value, array('-', '0', 'n', 'off', 'no', 'false', 'disabled')) ?
					false : (boolean) $value;
		}
	
        
        /**
         * cURL / Send http requests with curl
         */
        public static function curl($url, $input_params=array(), $output_params=array(), $debug=false) {
            $ret = array('status' => 'invalid', 'http_code' => 0, 'data' => '');

            // build curl options
            $ipms = array_replace_recursive(array(
                'userpwd'                   => false,
                'htaccess'                  => false,
                'post'                      => false,
                'postfields'                => array(),
                'verbose'                   => false,
                'ssl_verifypeer'            => false,
                'ssl_verifyhost'            => false,
                'httpauth'                  => false,
                'failonerror'               => false,
                'returntransfer'            => true,
                'binarytransfer'            => false,
                'header'                    => false,
                'cainfo'                    => false,
                'useragent'                 => false,
            ), $input_params);
            extract($ipms);
            
            $opms = array_replace_recursive(array(
                'resp_is_json'              => false,
                'resp_add_http_code'        => false,
                'parse_headers'             => false,
            ), $output_params);
            extract($opms);
            
            //var_dump('<pre>', $ipms, $opms, '</pre>'); die('debug...'); 

            // begin curl
            $url = trim($url);
            if (empty($url)) return (object) $ret;
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            
            if ( !empty($userpwd) ) {
                curl_setopt($curl, CURLOPT_USERPWD, $userpwd);
            }
            if ( !empty($htaccess) ) {
                $url = preg_replace( "/http(|s):\/\//i", "http://" . $htaccess . "@", $url );
            }
            if (!$post && !empty($postfields)) {
                $url = $url . "?" . http_build_query($postfields);
            }

            if ($post) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
            }
            
            curl_setopt($curl, CURLOPT_VERBOSE, $verbose);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_verifypeer);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl_verifyhost);
            if ( $httpauth!== false ) curl_setopt($curl, CURLOPT_HTTPAUTH, $httpauth);
            curl_setopt($curl, CURLOPT_FAILONERROR, $failonerror);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $returntransfer);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, $binarytransfer);
            curl_setopt($curl, CURLOPT_HEADER, $header);
            if ( $cainfo!== false ) curl_setopt($curl, CURLOPT_CAINFO, $cainfo);
            if ( $useragent!== false ) curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
            if ( $timeout!== false ) curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            
            $data = curl_exec($curl);
            $http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $ret = array_merge($ret, array('http_code' => $http_code));
            if ($debug) {
                $ret = array_merge($ret, array('debug_details' => curl_getinfo($curl)));
            }
            if ( $data === false || curl_errno($curl) ) { // error occurred
                $ret = array_merge($ret, array(
                    'data' => curl_errno($curl) . ' : ' . curl_error($curl)
                ));
            } else { // success
            
                if ( $parse_headers ) {
                    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                    $headers = self::__parse_headers( substr($data, 0, $header_size) ); // response begin with the headers
                    $data = substr($data, $header_size);
                    $ret = array_merge($ret, array('headers' => $headers));
                }
        
                // Add the status code to the json data, useful for error-checking
                if ( $resp_add_http_code && $resp_is_json ) {
                    $data = preg_replace('/^{/', '{"http_code":'.$http_code.',', $data);
                }
                
                $ret = array_merge($ret, array(
                    'status'    => 'valid',
                    'data'       => $data
                ));
            }

            curl_close($curl);
            return $ret;
        }
        private static function __parse_headers($headers) {
            if (!is_array($headers)) {
                $headers = explode("\r\n", $headers);
            }
            $ret = array();
            foreach ($headers as $header) {
                $header = explode(":", $header, 2);
                if (count($header) == 2) {
                    $ret[$header[0]] = trim($header[1]);
                }
            }
            return $ret;
        }

        // php.net/manual/en/function.urlencode.php
        // urlencode function and rawurlencode are mostly based on RFC 1738. however, since 2005 the current RFC in use for URIs standard is RFC 3986.
        public function urlencode($string) {
            $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
            $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
            return str_replace($entities, $replacements, urlencode($string));
        }

        
        /**
         * Utils
         */
        public function is_gzip( $setting=false, $force=array()) {
            $ret = true;
            if ( $setting!==false ) {
                $ret = (string) $setting == 'yes' ? true : false;
            }
  
            // do gzip only if everything it's fine
            if(
                !$ret // compressing not activated yet
                || empty($_SERVER['HTTP_ACCEPT_ENCODING']) // no encoding support
                || ( // no gzip
                    strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === false
                    && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'x-gzip') === false
                )
                || !function_exists("gzwrite") // no PHP gzip support
                || headers_sent() // headers already sent
                || ( ( !isset($force['ob_get_level']) || (isset($force['ob_get_level']) && $force['ob_get_level']) ) && ob_get_contents() ) // already some output...
                || in_array('ob_gzhandler', ob_list_handlers()) // other plugins (or PHP) is already using gzipp
                || $this->get_php_ini_bool(ini_get("zlib.output_compression")) // zlib compression in php.ini enabled
                || ( ( !isset($force['ob_get_level']) || (isset($force['ob_get_level']) && $force['ob_get_level']) )
                     && ( ob_get_level() > ( !$this->get_php_ini_bool(ini_get("output_buffering")) ? 0 : 1 ) ) ) // another output buffer  is already active, beside the default one*/
            ) {
                $ret = false;
            }
            return $ret;
        }
    }
}

if ( !function_exists('array_replace_recursive') ) {
	function array_replace_recursive($base, $replacements)
	{
		foreach (array_slice(func_get_args(), 1) as $replacements) {
			$bref_stack = array(&$base);
			$head_stack = array($replacements);

			do {
				end($bref_stack);

				$bref = &$bref_stack[key($bref_stack)];
				$head = array_pop($head_stack);

				unset($bref_stack[key($bref_stack)]);

				foreach (array_keys($head) as $key) {
					if (isset($key, $bref, $bref[$key], $head[$key]) && is_array($bref[$key]) && is_array($head[$key])) {
						$bref_stack[] = &$bref[$key];
						$head_stack[] = $head[$key];
					} else {
						$bref[$key] = $head[$key];
					}
				}
			} while(count($head_stack));
		}

		return $base;
	}
}