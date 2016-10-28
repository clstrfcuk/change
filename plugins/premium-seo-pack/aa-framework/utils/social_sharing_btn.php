<?php
/*
* Define class pspSocialSharingButtons
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSocialSharingButtons') != true) {
	class pspSocialSharingButtons
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
		
		protected $module_folder = '';
		protected $module_folder_path = '';

		static protected $_instance;
		
		private $shareInfo;
		private $post = null;
		
		private $pms;

		private $toolbarType;
		private $btnSize;
		private $btnWithText;
		private $btnViewCount;


		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct( $parent, $pms=array() )
		{
			$this->the_plugin = $parent;
			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_socialsharing' );
			
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Social_Stats/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Social_Stats/';
			
			$this->pms = $pms;
			
			$toolbarType = $pms['toolbarType'];
			$this->toolbarType = $toolbarType;
			$this->btnSize = $this->get_property( $toolbarType . '-btnsize', 'string' );
			$this->btnWithText = $this->get_property( $toolbarType . '-withtext', 'string' );
			$this->btnViewCount = $this->get_property( $toolbarType . '-viewcount', 'string' );
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
		
		
		/**
		 * Main methods
		 */
		public function setPostInfo( $post=null, $info=array() ) {
			$this->post = $post;
			$this->shareInfo = $info;
		}

		private function buildButtonUrl($netUrl, $network='') {

			$urlroot = $this->shareInfo->urlroot;
			$url = $this->shareInfo->url;
			$title = $this->shareInfo->title;
			
			$doUrlEncode = true;
			switch ($network) {
				case 'facebook':
					$count = 'button';
					if ( $this->btnViewCount == 'yes' ) $count = 'button_count';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					
					$size = array('width' => 48, 'height' => 20);
					//if ( $this->btnSize == 'large' ) $size = array('width' => 80, 'height' => 25);
					if ( $this->btnViewCount == 'yes' ) $size = array('width' => 80, 'height' => 24);
					$netUrl = str_replace('{WIDTH}', $size['width'], $netUrl);
					$netUrl = str_replace('{HEIGHT}', $size['height'], $netUrl);
					break;
					
				case 'twitter':
					$doUrlEncode = false;

					$user = $this->get_property( 'twitter_id', 'string' );
					$netUrl = str_replace('{USER}', $user, $netUrl);

					$count = 'none';
					if ( $this->btnViewCount == 'yes' ) $count = 'horizontal';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					
					$size = 'medium';
					if ( $this->btnSize == 'large' ) $size = 'large';
					$netUrl = str_replace('{SIZE}', $size, $netUrl);
					break;
					
				case 'plusone':
					$doUrlEncode = false;

					$count = 'none';
					if ( $this->btnViewCount == 'yes' ) $count = 'bubble';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					
					$size = 'medium';
					if ( $this->btnSize == 'large' ) $size = 'standard';
					$netUrl = str_replace('{SIZE}', $size, $netUrl);
					
					$netUrl = str_replace('{WIDTH}', '300', $netUrl);
					break;
					
				case 'linkedin':
					$title = rawurlencode($title);

					$count = '';
					if ( $this->btnViewCount == 'yes' ) $count = 'right';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					
				case 'stumbleupon':
					$count = '4'; $width = '70'; $height = '22';
					if ( $this->btnViewCount == 'yes' && $this->btnSize == 'large' ) { $count = '5'; $height = '80'; }
					else if ( $this->btnViewCount == 'yes' ) { $count = '1'; }
					else if ( $this->btnSize == 'large' ) { $count = '6'; $height = '40'; }
					
					$width .= 'px'; $height .= 'px';
					
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					$netUrl = str_replace('{WIDTH}', $width, $netUrl);
					$netUrl = str_replace('{HEIGHT}', $height, $netUrl);
					break;
					
				case 'digg':
					$netUrl = str_replace('{COUNT}', 'DiggCompact', $netUrl);
					
				case 'delicious':
					$count = '';
					if ( $this->btnViewCount != 'yes' ) { $count = 'display: none;'; }
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					break;
					
				case 'pinterest':
					$netUrl = str_replace('{MEDIA}', '', $netUrl);
					
					$count = 'none';
					if ( $this->btnViewCount == 'yes' ) $count = 'beside';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					break;
					
				case 'xing':
					$count = 'no_count';
					if ( $this->btnViewCount == 'yes' ) $count = 'right';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					break;
					
				case 'buffer':
					$title = rawurlencode($title);

					$count = 'none';
					if ( $this->btnViewCount == 'yes' ) $count = 'horizontal';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);

					$user = $this->get_property( 'twitter_id', 'string' );
					$netUrl = str_replace('{USER}', $user, $netUrl);
					break;
					
				case 'flattr':
					$count = 'data-flattr-button="compact"';
					if ( $this->btnSize == 'large' ) $count = '';
					$netUrl = str_replace('{COUNT}', $count, $netUrl);
					break;
					
				case 'tumblr':
					break;
					
				case 'reddit':
					$size = '7';
					if ( $this->btnSize == 'large' ) $size = '9';
					$netUrl = str_replace('{SIZE}', $size, $netUrl);
					break;

				default:
					break;
			}
			
			if ( $doUrlEncode ) $url = rawurlencode( $url );
			if ( $doUrlEncode ) $urlroot = rawurlencode( $urlroot );

			$netUrl = str_replace('{URL}', $url, $netUrl);
			$netUrl = str_replace('{URL-ROOT}', $urlroot, $netUrl);
			$netUrl = str_replace('{TITLE}', $title, $netUrl);
			
			$netUrl = str_replace('{MEDIA}', '', $netUrl);

			$netUrl = str_replace('{COUNT}', '', $netUrl);
			$netUrl = str_replace('{SIZE}', '', $netUrl);
			$netUrl = str_replace('{WIDTH}', '', $netUrl);
			$netUrl = str_replace('{HEIGHT}', '', $netUrl);
			$netUrl = str_replace('{USER}', '', $netUrl);
			
			// with count, size
			$cssExtra = array();
			if ( $this->btnViewCount == 'yes' ) $cssExtra[] = 'viewcount';
			if ( $this->btnSize == 'large' ) $cssExtra[] = 'large';

			$isCount = true;
			$__tmp = array('tumblr', 'digg', 'xing');
			if ( in_array($network, $__tmp) ) $isCount = false;
			
			return '<div class="social-btn ' . $network . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '" data-network="' . $network . '">'
			. ( $netUrl ) . ( $this->btnViewCount == 'yes' && $isCount ? '<span class="count">0</span>' : '' )
			. '</div>';
		}
		
		
		/**
		 * BUTTONS
		 */
		
		/**
		 * Print & Email buttons
		 * @custom button
		 */
		public function print_btn() {
			$text = __($this->get_property( 'text_print', 'string' ), $this->the_plugin->localizationName);

			// with count, size
			$cssExtra = array();
			if ( $this->btnViewCount == 'yes' ) $cssExtra[] = 'viewcount';
			if ( $this->btnSize == 'large' ) $cssExtra[] = 'large';

			return '<div class="social-btn print' . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '"><a href="#" class="icon"></a>' . ( $this->btnViewCount == 'yes' ? '<span class="text">' . $text . '</span>' : '' ) . '</div>';
		}
		public function email_btn() {
			$text = __($this->get_property( 'text_email', 'string' ), $this->the_plugin->localizationName);
			$email = $this->get_property( 'email', 'string' );

			// with count, size
			$cssExtra = array();
			if ( $this->btnViewCount == 'yes' ) $cssExtra[] = 'viewcount';
			if ( $this->btnSize == 'large' ) $cssExtra[] = 'large';

			return '<div class="social-btn email' . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '"><a href="mailto:' . $email . '" class="icon"></a>' . ( $this->btnViewCount == 'yes' ? '<span class="text">' . $text . '</span>' : '' ) . '</div>';
		}
		public function more_btn( $btnList=array() ) {
			$text = __($this->get_property( 'text_more', 'string' ), $this->the_plugin->localizationName);

			// with count, size
			$cssExtra = array();
			if ( $this->btnViewCount == 'yes' ) $cssExtra[] = 'viewcount';
			if ( $this->btnSize == 'large' ) $cssExtra[] = 'large';

			if ( empty($btnList) ) return '';
			$btnList = implode('', $btnList);
			return '
			<div class="social-btn more' . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '"><a href="#" class="icon"></a>' . ( $this->btnViewCount == 'yes' ? '<span class="text">' . $text . '</span>' : '' ) . '</div>
			<div class="more-list' . ( !empty($cssExtra) ? ' ' . implode(' ', $cssExtra) : '' ) . '">' . $btnList . '</div>
			';
		}
		
		/**
		 * Facebook
		 * https://developers.facebook.com/docs/plugins/like-button
		 * 
		 */
		public function facebook_btn() {
			// $netUrl = '<iframe src="https://www.facebook.com/plugins/like.php?app_id=&href={URL}&share=false&layout={COUNT}&width={WIDTH}&show_faces=false&action=like&colorscheme=light&height={HEIGHT}" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{WIDTH}px; height:{HEIGHT}px;" allowTransparency="true"></iframe>';
			$netUrl = '<a href="http://www.facebook.com/sharer.php?u={URL}&t={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'facebook' );
			return $netUrl;
		}
		
		/**
		 * Twitter
		 * https://dev.twitter.com/docs/tweet-button
		 *
		 */
		public function twitter_btn() {
			// $netUrl = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="{URL}" data-counturl="{URL}" data-text="{TITLE}" data-count="{COUNT}" data-via="{USER}" data-size="{SIZE}"></a>';
			$netUrl = '<a href="https://twitter.com/intent/tweet?source={URL-ROOT}&text={TITLE}&url={URL}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}" data-source="{URL-ROOT}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'twitter' );
			return $netUrl;
		}
		
		/**
		 * Google Plus / Google+
		 * https://developers.google.com/+/web/+1button/
		 */
		public function plusone_btn() {
			// $netUrl = '<!-- Place this tag where you want the +1 button to render. --><div class="g-plusone" data-href="{URL}" data-annotation="{COUNT}" data-size="{SIZE}" data-width="{WIDTH}"></div>';
			$netUrl = '<a href="https://plus.google.com/share?url={URL}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'plusone' );
			return $netUrl;
		}
		
		/**
		 * Linkedin
		 * https://developer.linkedin.com/plugins/share-plugin-generator
		 */
		public function linkedin_btn() {
			// $netUrl = '<script type="IN/Share" data-url="{URL}" data-counter="{COUNT}"></script>'; // @not working
			// $netUrl = '<a href="http://www.linkedin.com/shareArticle?mini=true" data-url="{URL}" data-title="{TITLE}" data-summary="{TITLE}" data-source="{URL-ROOT}"><span class="icon"></span></a>';
			$netUrl = '<a href="http://www.linkedin.com/shareArticle?mini=true&url={URL}&title={TITLE}&summary={TITLE}&source={URL-ROOT}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}" data-summary="{TITLE}" data-source="{URL-ROOT}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'linkedin' );
			return $netUrl;
		}
		
		/**
		 * StumbleUpon
		 * http://www.stumbleupon.com/dt/badges/create
		 */
		public function stumbleupon_btn() {
			// $netUrl = '<iframe src="http://www.stumbleupon.com/badge/embed/{COUNT}/?url={URL}" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:{WIDTH}; height: {HEIGHT};" allowTransparency="true"></iframe>';
			// $netUrl = '<!-- Place this tag where you want the su badge to render --><su:badge layout="{COUNT}" location="{URL}"></su:badge>';
			$netUrl = '<a href="http://www.stumbleupon.com/submit?url={URL}&title={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'stumbleupon' );
			return $netUrl;
		}

		/**
		 * Digg
		 */
		public function digg_btn() {
			// $netUrl = '<a class="DiggThisButton {COUNT}" href="http://digg.com/submit?url={URL}&title={TITLE}"></a>';
			$netUrl = '<a href="http://digg.com/submit?url={URL}&title={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'digg' );
			return $netUrl;
		}
		
		/**
		 * Delicious
		 * @custom button
		 */
		public function delicious_btn() {
			// $netUrl = '<a href="http://delicious.com/save" data-url="{URL}" data-title="{TITLE}"><span class="icon"></span><span class="psp-sshare-delicious-count" style="{COUNT}">0</span></a>';
			$netUrl = '<a href="http://delicious.com/save?v=5&noui&jump=close&url={URL}&title={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'delicious' );
			return $netUrl;
		}
		
		/**
		 * Pinterest
		 * http://business.pinterest.com/widget-builder/#do_pin_it_button
		 * https://developers.pinterest.com/pin_it/
		 */
		public function pinterest_btn() {
			// $netUrl = '<a href="//www.pinterest.com/pin/create/button/?url={URL}&description={TITLE}" data-pin-do="buttonPin" data-pin-config="{COUNT}"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>';
			$netUrl = '<a href="http://pinterest.com/pin/create/button/?url={URL}&media={MEDIA}&description={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}" data-media="{MEDIA}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'pinterest' );
			return $netUrl;
		}
		
		/**
		 * Xing
		 * https://dev.xing.com/plugins/share_button
		 * @custom button
		 */
		public function xing_btn() {
			// $netUrl = '<script data-counter="{COUNT}" data-type="XING/Share" data-url="{URL}"></script>'; // @not working
			// $netUrl = '<a href="https://www.xing.com/social_plugins/share?sc_p=xing-share;h=1;url=" data-url="{URL}" data-title="{TITLE}"><span class="icon"></span></a>';
			$netUrl = '<a href="https://www.xing.com/social_plugins/share?sc_p=xing-share;h=1;url={URL}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'xing' );
			return $netUrl;
		}
		
		/**
		 * Buffer
		 * https://bufferapp.com/extras/button
		 */
		public function buffer_btn() {
			// $netUrl = '<a href="http://bufferapp.com/add" class="buffer-add-button" data-text="{TITLE}" data-url="{URL}" data-via="{USER}" data-count="{COUNT}">Buffer</a>';
			$netUrl = '<a href="http://bufferapp.com/add?&text={TITLE}&url={URL}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'buffer' );
			return $netUrl;
		}
		
		/**
		 * Flattr
		 * http://developers.flattr.net/button/
		 * @custom button
		 */
		public function flattr_btn() {
			// $netUrl = '<a class="FlattrButton" style="display:none;" title="{TITLE}" data-flattr-uid="flattr" data-flattr-tags="text, opensource" data-flattr-category="text" href="{URL}" {COUNT}>Flattr</a>'; // @not working
			// $netUrl = '<a href="https://flattr.com/submit/auto?user_id=flattr&language=en_GB&category=text" data-url="{URL}" data-title="{TITLE}"><span class="icon"></span></a>';
			$netUrl = '<a href="https://flattr.com/submit/auto?user_id=flattr&language=en_GB&category=text&url={URL}&title={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'flattr' );
			return $netUrl;
		}
		
		/**
		 * Tumblr
		 * http://www.tumblr.com/buttons
		 */
		public function tumblr_btn() {
			// $netUrl = '<a href="http://www.tumblr.com/share?link={URL}" title="{TITLE}" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url(\'http://platform.tumblr.com/v1/share_2.png\') top left no-repeat transparent;">Share on Tumblr</a>';
			$netUrl = '<a href="http://www.tumblr.com/share/link?url={URL}&name={TITLE}&description={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'tumblr' );
			return $netUrl;
		}
		
		/**
		 * Reddit
		 * http://www.reddit.com/buttons/
		 * @custom button
		 */
		public function reddit_btn() {
			// $netUrl = '<a href="http://www.reddit.com/submit" data-url="{URL}" data-title="{TITLE}"> <img src="http://www.reddit.com/static/spreddit{SIZE}.gif" alt="submit to reddit" border="0" /> </a>';
			$netUrl = '<a href="http://www.reddit.com/submit?url={URL}&title={TITLE}" class="icon" target="_blank" data-url="{URL}" data-title="{TITLE}"></a>';
			$netUrl = $this->buildButtonUrl( $netUrl, 'reddit' );
			return $netUrl;
		}


		/**
		 * UTILS
		 */
		private function get_property( $key, $type='string', $default='' ) {
			$opt = $this->plugin_settings;
			switch ($type) {
				case 'string' :
					$prop = isset($opt["$key"]) ? $opt["$key"] : ( !empty($default) ? $default : '' );
					break;
					
				case 'array' :
					$prop = isset($opt["$key"]) && is_array($opt["$key"]) ? $opt["$key"] : ( !empty($default) ? $default : array() );
					break;
			}
			return $prop;
		}
	}
}

// Initialize the pspSocialSharingButtons class
//$pspSocialSharingButtons = new pspSocialSharingButtons();
