<?php
/*
* Define class pspGoogleAuthorship
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspGoogleAuthorship') != true) {
	class pspGoogleAuthorship
	{
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*
		*/
		public $cfg = array();

		/*
		* Store some helpers config
		*/
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';

		private $settings = array();

		static protected $_instance;

		private $google_publisher = array(
			'url'		=> '', // google+ profile url
			'loc'		=> '' // location: header, footer
		);
		private $google_authorship = array(
			// array of arrays of pairs (url, loc)
		);


		/**
	    	* Singleton pattern
	    	*
	    	* @return pspGoogleAuthorship Singleton instance
	    	*/
		static public function getInstance()
		{
			if (!self::$_instance) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct()
		{
			global $psp;
   
			$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/google_authorship/';
			$this->module = $this->the_plugin->cfg['modules']['google_authorship'];

			if ( $this->the_plugin->is_admin !== true ) {
				$this->settings = $this->the_plugin->getAllSettings( 'array', 'google_authorship' );
			}
   
			if (is_admin()) {
				// add_action('admin_menu', array( &$this, 'adminMenu' ));
   
				add_action('edit_user_profile', array( $this, 'user_profile_metabox' ));
				add_action('show_user_profile', array( $this, 'user_profile_metabox' ));
				
				add_action('personal_options_update', array( $this, 'user_profile_save_metabox' ));
				add_action('edit_user_profile_update', array( $this, 'user_profile_save_metabox' ));
			}
			
			if ( $this->the_plugin->is_admin === true ) {
				// ajax handler
				// add_action('wp_ajax_pspGoogleAuthorship_test', array( &$this, 'test' ));
			} else {
				$this->google_publisher();

				add_action( 'premiumseo_head', array( $this, 'the_header' ), 11 );
				add_action( 'premiumseo_footer', array( $this, 'the_footer' ), 11 );
				add_filter( 'the_content', array( $this, 'the_content' ), 11 );
				add_filter( 'author_link', array( $this, 'author_link_filter' ), 999, 3 );
			}
		}


		/**
		 * Frontend
		 */
		public function the_header() {
			
			$authors = array();
			$pageHasAuthor = false;
			if ( is_home() || is_front_page() ) {
				$pageHasAuthor = true;
  
				// publisher
				if ( !empty($this->google_publisher)
					&& $this->google_publisher['loc']=='header'
					&& !empty($this->google_publisher['url']) ) {
					echo $this->google_publisher['url'] . PHP_EOL;
				}
					
				// authorship
				$authors = $this->google_authorship('home');
			}
			
			// authorship
			if ( is_category() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('category');
			}
			if ( is_tag() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('tag');
			}
			
			if ( is_singular() ) {
				$pageHasAuthor = true;

				global $post;
				if ( is_feed() ) {
					$authors = $this->google_authorship('content', $post, true);
				} else {
					$authors = $this->google_authorship('content', $post);				
				}
			}
			
			if ( $pageHasAuthor ) {
				if ( isset($authors['header']) && !empty($authors['header']) )
					echo implode(PHP_EOL, $authors['header']) . PHP_EOL;
			}
		}
		
		public function the_footer() {
			
			$authors = array();
			$pageHasAuthor = false;
			if ( is_home() || is_front_page() ) {
				$pageHasAuthor = true;
  
				// publisher
				if ( !empty($this->google_publisher)
					&& $this->google_publisher['loc']=='footer'
					&& !empty($this->google_publisher['url']) ) {
					echo $this->google_publisher['url'] . PHP_EOL;
				}

				// authorship
				$authors = $this->google_authorship('home');
			}
			
			// authorship
			if ( is_category() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('category');
			}
			if ( is_tag() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('tag');
			}
			
			if ( is_singular() ) {
				$pageHasAuthor = true;

				global $post;
				if ( is_feed() ) {
					$authors = $this->google_authorship('content', $post, true);
				} else {
					$authors = $this->google_authorship('content', $post);				
				}
			}
			
			if ( $pageHasAuthor ) {
				if ( isset($authors['footer']) && !empty($authors['footer']) )
					echo implode(PHP_EOL, $authors['footer']) . PHP_EOL;
			}
		}
		
		public function the_content( $content ) {

			$authors = array();
			if ( !is_singular() ) {
				return $content;
			}

			global $post;

			// verify allowed post type
			$post_type = (string) $post->post_type;
			$allowedPostTypes = (array) ( isset($this->settings['post_types']) ? $this->settings['post_types'] : array() );
			if ( empty($allowedPostTypes) || !in_array($post_type, $allowedPostTypes) )
				return $content;

			if ( is_feed() ) {
				$authors = $this->google_authorship('content', $post, true);
			} else {
				$authors = $this->google_authorship('content', $post);				
			}

			if ( isset($authors['content_top']) && !empty($authors['content_top']) ) {
				$content = implode(PHP_EOL, $authors['content_top']) . PHP_EOL
				. $content;
			}
			if ( isset($authors['content_bottom']) && !empty($authors['content_bottom']) ) {
				$content = $content
				. implode(PHP_EOL, $authors['content_bottom']) . PHP_EOL;
			}

			return $content;
		}
		
		public function author_link_filter( $link, $author_id, $author_nicename ) {
  
			$authors = array();
			$pageHasAuthor = false;
			if ( is_home() || is_front_page() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('home');
			}
			if ( is_category() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('category');
			}
			if ( is_tag() ) {
				$pageHasAuthor = true;
				$authors = $this->google_authorship('tag');
			}
			if ( is_singular() ) {
				$pageHasAuthor = true;

				global $post;
				if ( is_feed() ) {
					$authors = $this->google_authorship('content', $post, true);
				} else {
					$authors = $this->google_authorship('content', $post);				
				}
			}
  
			if ( $pageHasAuthor ) {
				if ( !empty($authors['replace']) && in_array($author_id, array_keys($authors['replace'])) ) {
					return $authors['replace']["$author_id"];
				}
			}
			return $link;
		}
		
		public function google_publisher() {
			$ps = $this->settings; // publisher settings
  
			if ( isset($ps['publisher_google_url']) && !empty($ps['publisher_google_url'])
				&& isset($ps['publisher_location']) && $ps['publisher_location'] != 'disabled' ) {
				$this->google_publisher['loc'] = $ps['publisher_location'];
				if ( $ps['publisher_location'] == 'header') {

					$this->google_publisher['url'] = '<link rel="publisher" href="' . $ps['publisher_google_url'] . '" />';
				} else if ( $ps['publisher_location'] == 'footer') {

					$this->google_publisher['url'] = '<a href="' . $ps['publisher_google_url'] . '" rel="publisher">' . __('Google+ Publisher', 'psp') . '</a>';
					if ( isset($ps['publisher_visibility']) && $ps['publisher_visibility'] == 'hidden' ) {
						$this->google_publisher['url'] = '<div style="display:none;">'
							. $this->google_publisher['url'] . '</div>';
					}
				}
			}
		}
		
		public function google_authorship( $pagetype, $post=null, $isfeed=false ) {
			$as = $this->settings; // authorship settings
			
			$users = array();
			if ( $pagetype == 'home' && isset($as['homepage_authors']) ) {
				$users = $as['homepage_authors'];
			}
			else if ( $pagetype == 'category' && isset($as['category_authors']) ) {
				$users = $as['category_authors'];
			}
			else if ( $pagetype == 'tag' && isset($as['tag_authors']) ) {
				$users = $as['tag_authors'];
			}
			else if ( $pagetype == 'content' ) {
				$users = array($post->post_author);
				
				// co authors plus integration!
				if ( $this->the_plugin->is_plugin_active( 'co-authors-plus/co-authors-plus.php' ) ) {
					if ( function_exists('get_coauthors') ) {
						$cousers = get_coauthors( $post->ID );
						if ( is_array($cousers) && !empty($cousers) ) {
							$users = array();
							foreach ($cousers as $key => $value) {
								if ( isset($value->data->ID) )
									$users[] = $value->data->ID;
							}
						}
					}
				}
			}

			$authorsUrls = array(
				'header'			=> array(),
				'footer'			=> array(),
				'replace'			=> array(),
				'content_top'		=> array(),
				'content_bottom'	=> array()
			);
			if ( !empty($users) ) {
				foreach ($users as $user_id) {
					if ( in_array($pagetype, array('home', 'category', 'tag')) )
						$authorInfo = $this->set_author_link( $user_id, 'header' );
					else
						$authorInfo = $this->set_author_link( $user_id );

					if ( empty($authorInfo) ) continue 1;

					$pos = $authorInfo['loc'];
					$authorsUrls["$pos"]["$user_id"] = $authorInfo['url'];
					
					if ( $isfeed && $authorInfo['feed'] != 'yes' )
						unset($authorsUrls["$pos"]["$user_id"]); 
				}
				$this->google_authorship = $authorsUrls;
				return $authorsUrls;
			}
		}
		
		public function set_author_link( $user_id, $force_loc=false ) {
			$as = $this->settings; // authorship settings
			
			$ret = array('url' => '', 'loc' => '');
  
			$user_meta = get_user_meta( $user_id, 'psp_google_authorship', true );
			$user_meta = $user_meta!=false && isset($user_meta['google_authorship_meta']) ? $user_meta['google_authorship_meta'] : array();
 
			// validate
			if ( !isset($user_meta['google_url']) || empty($user_meta['google_url']) )
				return false;

			if ( isset($user_meta['author_location']) && $user_meta['author_location'] == 'disabled' )
				return false;
			else if ( !isset($user_meta['author_location']) && isset($as['author_location']) && $as['author_location'] == 'disabled' )
				return false;

			// build attributes values!
			$attr = array('author_location' => '', 'author_visibility' => '', 'author_feed' => '', 'author_newwindow' => '', 'author_title' => '', 'author_text' => '');
			foreach ( $attr as $k => $v ) {
				if ( isset($as["$k"]) && !empty($as["$k"]) )
					$attr["$k"] = $as["$k"];
				if ( isset($user_meta["$k"]) && !empty($user_meta["$k"]) )
					$attr["$k"] = $user_meta["$k"];
			}
			
			// force location!
			if ( !empty($force_loc) ) $attr['author_location'] = 'header';
    
			// build link!
			$ret['loc'] = $attr['author_location'];
			$ret['feed'] = ( $attr['author_feed'] == 'yes' ? 'yes' : 'no' );
			if ( $attr['author_location'] == 'header') {

				$ret['url'] = '<link rel="author" href="' . $user_meta['google_url'] . '" />';
			} else if ( in_array($attr['author_location'], array('footer', 'content_top', 'content_bottom')) ) {
				
				$user_meta['google_url'] .= '?rel=author';
				$ret['url'] = '<a href="' . $user_meta['google_url'] . '" rel="author" title="' . __($attr['author_title'], 'psp') . '"' . ($attr['author_newwindow']=='yes' ? ' target="_blank"' : '') . '>' . __($attr['author_text'], 'psp') . '</a>';
				if ( isset($attr['author_visibility']) && $attr['author_visibility'] == 'hidden' ) {
					$ret['url'] = '<div style="display:none;">'
						. $ret['url'] . '</div>';
				}
			} else if ( $attr['author_location'] == 'replace') {
				$user_meta['google_url'] .= '?rel=author';
				$ret['url'] = $user_meta['google_url'];
			}
			return $ret;
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
			if ( $this->the_plugin->capabilities_user_has_module('google_authorship') ) {
				add_submenu_page(
					$this->the_plugin->alias,
					$this->the_plugin->alias . " " . __('Google Authorship', 'psp'),
					__('Google Authorship', 'psp'),
					'read',
					$this->the_plugin->alias . "_google_authorship",
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
	    	if ( $this->the_plugin->capabilities_user_has_module('google_authorship') ) {
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
			            'psp_google_authorship_meta_box',
			            $screen . ' - ' . __( 'PSP Google+ Authorship', 'psp' ),
			            array($this, 'display_meta_box'),
			            $key
			        );
			    }
	    	}
		    
	        return $this;
	    }

		public function display_meta_box()
		{
			// $this->printBoxInterface();
		}

		public function display_index_page()
		{
			// $this->printBaseInterface();
		}
		
		
		/** 
		 * user profile box
		 */
		public function user_profile_metabox($user) {
			$user_id = (int) $user->ID;
  
			// load the settings template class
			require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_plugin->cfg);
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$user_meta = get_user_meta( $user_id, 'psp_google_authorship', true );
			 
			// then build the html, and return it as string
			$html_information = $aaInterfaceTemplates->bildThePage( $this->information_options( $user_meta ) , $this->the_plugin->alias, array(), false);
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
			
			<div class="psp-meta-box-container" id="psp-user-authorship" style="display:none; border:0px;">
				<!-- box Tab Menu -->
				<!--<div class="psp-tab-menu">
					<a href="#dashboard" class="open"><?php _e('PSP Google+ Authorship', 'psp');?></a>
				</div>-->
				
				<!-- box Data -->
				<div class="psp-tab-container">
				
					<!-- box Dashboard -->
					<div id="psp-tab-div-id-dashboard" style="display:block;">
						<div class="psp-dashboard-box span_3_of_3">
							<!-- Creating the option fields -->
							<div class="psp-form">
								<?php echo $html_information;?>
							</div>
						</div>
					</div>
					
				</div>
			<div style="clear:both"></div>
		</div>
<?php
		}

		public function information_options( $defaults=array() )
		{
			$psp = $this->the_plugin;

			if( !is_array($defaults) ) $defaults = array();
  
			$options = array(
				array(
					/* define the form_sizes  box */
					'google_authorship_meta' => array(
						'title' 	=> __('Google+ Authorship Settings', $psp->localizationName),
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
					'google_url' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '500',
						'title' 	=> __('Google+ Profile URL: ', $psp->localizationName),
						'desc' 		=> __('the url to your google+ profile to linked it with your website.', $psp->localizationName)
					),

					'author_location' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'header',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Location:', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: where you want the google+ profile for author to be displayed', $psp->localizationName),
						'options' 	=> array(
							'disabled'		=> __('Disabled', $psp->localizationName),
							'header'		=> __('In the header (not visible to site visitors) - recommended', $psp->localizationName),
							'footer'		=> __('In the footer', $psp->localizationName),
							'replace'		=> __('Replace author link with the authors Google+ link (verify that your theme support it!)', $psp->localizationName),
							'content_top'	=> __('In the content (top)', $psp->localizationName),
							'content_bottom'=> __('In the content (bottom)', $psp->localizationName)
						)
					),
					
					'author_visibility' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'visible',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Visibility:', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: if you want the google+ profile for author to be displayed (available only for Location: In the footer, In the content (top or bottom))', $psp->localizationName),
						'options' 	=> array(
							'visible'		=> __('Visible', $psp->localizationName),
							'hidden'		=> __('Hidden', $psp->localizationName)
						)
					),
					
					'author_feed' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Display in feeds? ', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: if you want the google+ profile for author to be include in the feeds', $psp->localizationName),
						'options'	=> array(
							'yes' 	=> __('YES', $psp->localizationName),
							'no' 	=> __('NO', $psp->localizationName)
						)
					),
					
					'author_newwindow' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Open link in new window?', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: open the url to google+ profile for author in new window', $psp->localizationName),
						'options'	=> array(
							'yes' 	=> __('YES', $psp->localizationName),
							'no' 	=> __('NO', $psp->localizationName)
						)
					),
					
					'author_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'Google+ Author',
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('URL Title: ', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: url title - to google+ profile for author.', $psp->localizationName)
					),
					
					'author_text' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'Google+',
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('URL Text: ', $psp->localizationName),
						'desc' 		=> __('generic setting for all authors: url text - to google+ profile for author.', $psp->localizationName)
					)
						)
					)
				)
			);
  
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box){
						if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$box_id][$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$box_id][$elm_id];
								}
							}
						}
					}
				}
 				
				// than update the options for returning
				$options = array( $option );
			}
  
			return $options;
		}

		/* when the post is saved, save the custom data */
		public function user_profile_save_metabox( $user_id ) 
		{
			if( 1 ) {
				if ( 1 ) {

					$user_meta = array();

					$options = array();
					$options = array_merge_recursive( $options, reset( $this->information_options() ) );

					foreach ($options as $box_id => $box){
						foreach ($box['elements'] as $elm_id => $element){

							if ( $element['type'] == 'html'
								&& !in_array($elm_id, array('xyz')) ) {

								continue 1;
							}
							$user_meta[$box_id][$elm_id] = $_POST[$elm_id];
						}
					}

					update_user_meta( $user_id, 'psp_google_authorship', $user_meta );
				}
			}
		}

	}
}
// Initialize the your pspGoogleAuthorship
//$pspGoogleAuthorship = new pspGoogleAuthorship();
$pspGoogleAuthorship = pspGoogleAuthorship::getInstance();