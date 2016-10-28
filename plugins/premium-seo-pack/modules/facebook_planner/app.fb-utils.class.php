<?php
/**
 * Facebook Post Planner
 * http://www.aa-team.com
 * ======================
 *
 * @package			psp_fbPlannerUtils
 * @author			AA-Team
 */

// Plugin facebook SDK load
global $psp;
require_once ( $psp->cfg['paths']['scripts_dir_path'] . '/facebook/facebook.php' );

class psp_fbPlannerUtils
{
    // Hold an instance of the class
    private static $instance;
	
	// Hold an utils of the class
    private static $utils;
	
    private $fb;
    
    public $the_plugin = null;
    
    private $fb_details = null;
    	
 
    // The singleton method getInstance
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new psp_fbPlannerUtils;
        }
        return self::$instance;
    }
	
	// The constructor, call on class instance
	public function __construct(){
		
        global $psp;

        $this->the_plugin = $psp;
	
		$this->fb_details = $this->the_plugin->getAllSettings('array', 'facebook_planner');
		
		// create utils
		self::$utils = array(
			'token'		=> get_option('psp_fb_planner_token'),
			'appId'		=> $this->fb_details['app_id'],
			'secret'	=> $this->fb_details['app_secret'],
			'inputs_available' => $this->fb_details['inputs_available']
		); 

		// try to login on fb with static facebook key
		if(!$this->fb_login()){
			die('Invalid FB login!');
		}
	}
	
	public function fb_login() {
		// Create our Application instance (replace this with your appId and secret).
		$this->fb = new psp_Facebook(array(
			'appId'  => self::$utils['appId'],
			'secret' => self::$utils['secret'],
		));
		
		// set saved access token
		$this->fb->setAccessToken(self::$utils['token']);
		
		// Get User ID
		$user = $this->fb->getUser();
		if(trim($user) == ""){
			return false;
		}
		
		return true;
	}
	
	public function getFbUserData() {
		if($this->fb_login()){
			return $this->fb->api('/me');
		}else{
			return array();
		}
	}
	
	public function publishToWall($id, $whereToPost, $postPrivacy, $postData = NULL) {

		// retrive WP post metadata
		if( is_null($postData) ) {
			$postData = $this->getPostByID($id);
		}

		// where to publish post
		$whereToPost = unserialize($whereToPost);
		
		if(trim($whereToPost['profile']) == '' && trim($whereToPost['page_group']) == '')
			return false;

		if(count($postData) > 0) {
			try {
				$post_link = trim($postData['link']) == 'post_link' ? get_permalink($id) : $postData['link'];
				
				if($postPrivacy == 'CUSTOM') {
					$q_postPrivacy = array('value' => $postPrivacy, 'friends' => 'SELF');
				}else{
					$q_postPrivacy = array('value' => $postPrivacy);
				}

				$finalImg = '';
				if ( $postData['use_picture'] == 'yes' ) {
				
					if ( empty($postData['picture']) ) {

						if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $id ) ) {
							$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
							$__featured_image = $__featured_image[0];
							if ( !empty($__featured_image) )
								$postData['picture'] = $__featured_image;
						}
					}

					if ( !empty($postData['picture']) ) {
	
						$finalImg = '{plugin_url}timthumb.php?src={img}&amp;w={thumb_w}&amp;h={thumb_h}&amp;zc={thumb_zc}';
	
						$img_size = explode('x', $this->fb_details['featured_image_size']);
	
						$finalImg = str_replace('{plugin_url}', $this->the_plugin->cfg['paths']['plugin_dir_url'], $finalImg);
						$finalImg = str_replace('{img}', $postData['picture'], $finalImg);
						$finalImg = str_replace('{thumb_w}', (isset($img_size[0]) ? $img_size[0] : 450), $finalImg);
						$finalImg = str_replace('{thumb_h}', (isset($img_size[1]) ? $img_size[1] : 320), $finalImg);
						$finalImg = str_replace('{thumb_zc}', ($this->fb_details['featured_image_size_crop'] == 'true' ? 1 : 2), $finalImg);
					}
				} // end use picture!

				$arrFbData = array(
					'link'			=> $post_link,
					'name' 		=> stripslashes($postData['name']),
					'description' 		=> stripslashes($postData['description'])
				);
				
				if ( is_array(self::$utils['inputs_available']) && !empty(self::$utils['inputs_available']) ) {
					$arrFbData = array_merge($arrFbData, array(
						'picture'	 	=> in_array('image', self::$utils['inputs_available']) ? $finalImg : '',
						'caption'		=> in_array('caption', self::$utils['inputs_available']) ? stripslashes($postData['caption']) : '',
						'message' 		=> in_array('message', self::$utils['inputs_available']) ? stripslashes($postData['message']) : ''
					));
				}

				if( trim($whereToPost['profile']) == 'on' ) {
					$arrFbData['privacy'] = $q_postPrivacy;
 					
					$statusUpdate = $this->fb->api(
						'/me/feed', 
						'post',
						$arrFbData
					); 
 
				}

				if ( trim($whereToPost['page_group']) != '' ) {
					unset( $arrFbData['privacy'] );
					$args = $arrFbData;					

					$page_access_token = null;
					$whereToPost = explode('##', $whereToPost['page_group']);
					$postTo_ident = $whereToPost[0];
					$postTo_id = $whereToPost[1];
					
					if($postTo_ident == 'page') {
						$page_access_token = $whereToPost[2];
						
						if( !empty($page_access_token) ) {
							$args['access_token'] = $page_access_token;
						}
					}
					
					$statusUpdate = $this->fb->api(
						'/' . $postTo_id . '/feed', 
						'post',
						$args
					);
				}
				
				return true;
			} catch (psp_FacebookApiException $e) {
				var_dump('<pre>',$e ,'</pre>'); die; 
				return false;
			}
		}
	}
	
	public function getPostByID($id){
		if((int)$id > 0){
			return array(
				'name' 			=> get_post_meta($id, 'psp_wplannerfb_title', true),
				'link' 			=> get_post_meta($id, 'psp_wplannerfb_permalink', true),
				'description' 	=> get_post_meta($id, 'psp_wplannerfb_description', true),
				'caption' 		=> get_post_meta($id, 'psp_wplannerfb_caption', true),
				'message' 		=> get_post_meta($id, 'psp_wplannerfb_message', true),
				'picture'	 	=> get_post_meta($id, 'psp_wplannerfb_image', true),
				'use_picture'	=> get_post_meta($id, 'psp_wplannerfb_useimage', true)
			);
		}
		return array();
	}
}