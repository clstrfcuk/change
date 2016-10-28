<?php
/*
* Define class pspBuddyPressTags
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspBuddyPressTags') != true) {
    class pspBuddyPressTags extends pspTitleMetaFormat
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
        public function __construct( $parent )
        {
        	//global $psp;
			//parent::__construct(); //init page types array!
        	//$this->the_plugin = $psp;
			
			$this->the_plugin = $parent;

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
			if ( !$this->the_plugin->is_buddypress() )
				return true;

			if ( $this->the_plugin->is_admin !== true ) {

				add_filter( 'premiumseo_seo_settings' , array($this->the_plugin->buddypress_utils, 'add_bp_settings') );
	           	add_filter( 'premiumseo_seo_pagetype', array($this->the_plugin->buddypress_utils, 'get_bp_pagetype') );
				add_filter( 'premiumseo_seo_list_pagetypes', array($this->the_plugin->buddypress_utils, 'get_bp_list_pagetypes') );
        	   	add_filter( 'premiumseo_seo_make_format', array($this, 'make_format'), 1, 3 );
				
				// if post meta entered!
				add_filter( 'premiumseo_seo_title' , array($this, 'meta_seo_title') );
				add_filter( 'premiumseo_seo_meta_description' , array($this, 'meta_seo_description') );
				add_filter( 'premiumseo_seo_meta_keywords' , array($this, 'meta_seo_keywords') );
				add_filter( 'premiumseo_seo_robots' , array($this, 'meta_seo_robots') );
				// add_filter( 'premiumseo_seo_canonical' , array($this, 'fix_canonical_url') ); // fix canonical url!
				
				global $wp_filter;
				if( isset($wp_filter['bp_page_title']) ) {
                	//remove_all_filters( 'bp_page_title' );
                	//add_filter( 'bp_page_title', array(&$this, 'init_seo') , 1 );
				}
			}
		}
	
		/**
	    * Singleton pattern
	    *
	    * @return pspBuddyPressTags Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }


        /**
         * replace shortcodes with values!
         *
         */
		public function make_format($__replace_orig=array(), $type='', $theContent='') {
     
 			if ( empty($theContent) ) return '';

			$component = $this->get_current_component();
			$action = $this->get_current_action();
			$action = $action['name'];
  
 			$__defaults = array( //default params!
 				'component_slug'		=> $component['slug'],
 				'component_name'		=> $component['name'],
 				'component_name_h'		=> ucfirst($component['name']),
 				'action'				=> $action,
 				'action_h'				=> ucfirst(implode(' ', explode('-', $action)))
 			);

 			//to be replaced params
 			$__replace = array_merge($__defaults, array(
 			));

 			//loop through all page types and set some info!
 			//::

			$tags_per_pages = $this->the_plugin->buddypress_utils->tags_per_pages;
			$type2 = str_replace('bp_', '', $type);
			$type2 = implode(',', explode('_', $type2));
  
			// user
			if ( in_array($type2, $tags_per_pages['user']) ) {
				$user = $this->get_user();
 				$__replace = array_merge($__replace, $user);
 			}
			
			// group
			if ( in_array($type2, $tags_per_pages['group']) ) {
				$group = $this->get_group();
 				$__replace = array_merge($__replace, $group);
 			}
			
			// activity
			if ( in_array($type2, $tags_per_pages['activity']) ) {
				$activity = $this->get_activity();
  
 				$__replace = array_merge($__replace, $activity);
 			}

			// forum & forum topic
			if ( in_array($type2, array_merge($tags_per_pages['forum'], $tags_per_pages['forum-topic'])) ) {
				$forum = $this->get_forum();
 				$__replace = array_merge($__replace, $forum);
 			}
			
			//::
 			//end loop through all page types and set some info!

			//var_dump('<pre>bp-replace:', $__replace, '</pre>');
			$__replace = array_merge($__replace_orig, $__replace);
			
  			$tag_default_val = $this->the_plugin->buddypress_utils->get_tag_field_default( $type );
 			
 			//replace shortcodes with values!
 			foreach ($tag_default_val as $aa=>$bb) {
	 			foreach ( $__replace as $shortcode => $value ) {
	 				$tag_default_val["$aa"] = str_replace( sprintf(self::$tplChar, $shortcode), $value, $tag_default_val["$aa"] );
	 			}
			}
	 		$__replace = array_merge($__replace, array(
		 		'title_default'			=> $tag_default_val['title'], 
		 		'desc_default'			=> $tag_default_val['desc'],
		 		'kw_default'			=> $tag_default_val['kw']
	 		));

			$__replace = apply_filters( 'premiumseo_seo_bp_make_format', $__replace, $type, $theContent );
			return $__replace;
		}
		
		/**
		 * component methods
		 */
		private function get_current_component(){
		    global $bp;
  
			$slug = $bp->current_component;
			$name = $this->the_plugin->buddypress_utils->get_component_by_slug( $slug );
			$ret = array(
				'slug'			=> !empty($slug) ? $slug : '',
				'name'			=> !empty($name) ? $name : ''
			);
			return $ret;
		}
		private function get_current_action(){
		    global $bp;
  
			$name = $bp->current_action;
			$ret = array(
				'name'			=> !empty($name) ? $name : ''
			);
			return $ret;
		}
		
		/**
		 * user methods
		 */
		private function get_user() {
    		global $bp;
			
			$ret = array(
				'user_login'			=> '',
				'user_nicename'			=> '',
				'user_registered_date'	=> '',
				'user_display_name'		=> '',
				'user_fullname'			=> ''
			);
			
			if ( isset($bp->displayed_user) ) {
				$user = $bp->displayed_user;
				$ret = array(
					'user_login'			=> $user->userdata->user_login,
					'user_nicename'			=> $user->userdata->user_nicename,
					'user_registered_date'	=> $user->userdata->user_registered,
					'user_display_name'		=> $user->userdata->display_name,
					'user_fullname'			=> $user->fullname
				);
			}
			return $ret;
		}

		/**
		 * group methods
		 */
		private function get_group() {
    		global $bp;
			
			$ret = array(
				'group_name'			=> '',
				'group_desc'			=> '',
			);

			$group_id = (int) $bp->groups->current_group->id;
			if ( $group_id > 0 ) {
				$group = new BP_Groups_Group( $group_id );
				$ret = array(
					'group_name'			=> isset($group->name) ? $group->name : '',
					'group_desc'			=> isset($group->description) ? $group->description : '',
				);
			}
			return $ret;
		}
		
		/**
		 * activity methods
		 */
		private function get_activity() {
    		global $bp;
			global $activities_template;

			$ret = array(
				'activity_content'			=> '',
				'user_login'				=> '',
				'user_nicename'				=> '',
				'user_display_name'			=> '',
				'user_fullname'				=> ''
			);

    		$condition = bp_has_activities();
			if ( !$condition ) return $ret;

			$activity2 = (object) array('activity_content', 'activity_author', 'user_login', 'user_nicename', 'user_display_name', 'user_fullname');
		    foreach( $activities_template->activities AS $activity ){
		        if( $activity->id == $bp->current_action )
		            $activity2 = $activity;
		    }
  
			if ( 1 ) {
				$content = '';
				if ( isset($activity2->action) && !empty($activity2->action) )
					$content = $activity2->action;
				if ( isset($activity2->content) && !empty($activity2->content) )
					$content = $activity2->content;
				
				$ret = array(
					'activity_content'		=> strip_tags($content),
					'user_login'			=> $activity2->user_login,
					'user_nicename'			=> $activity2->user_nicename,
					'user_display_name'		=> $activity2->display_name,
					'user_fullname'			=> $activity2->user_fullname
				);
			}
			return $ret;
		}
		
		/**
		 * forum methods
		 */
		private function get_forum() {
			$ret = array(
				'forum_title'				=> '',
 				'forum_date'				=> '',
 				'forum_description'			=> '',
 				'forum_short_description'	=> '',
 				'forum_author'				=> '',
 				'forum_author_username'		=> '',
 				'forum_author_nickname'		=> '',
 				'forum_author_description'	=> '',

				'topic_title'				=> '',
 				'topic_date'				=> '',
 				'topic_description'			=> '',
 				'topic_short_description'	=> '',
 				'topic_author'				=> '',
 				'topic_author_username'		=> '',
 				'topic_author_nickname'		=> '',
 				'topic_author_description'	=> ''
			);

    		global $bp;
			if ( !bp_is_current_action( 'forum' ) || !function_exists( 'bbpress' ) ) return $ret;
			//$x = $bp->action_variables;
			
			$forum_id = 0; $topic_id = 0;
			
			$forum_ids = bbp_get_group_forum_ids(); //array of ids
			if( $forum_ids ) $forum_id = (int) array_pop ($forum_ids);
   
			//if is single topic
			if ( bp_is_action_variable('topic', 0) && bp_action_variable(1) ) {
				//get the topic as post
				$topics = get_posts( array(
					'name'			=> bp_action_variable(1),
					'post_type'		=> bbp_get_topic_post_type(),
					'per_page'		=> 1
				) );
				//get the id
				if( !empty( $topics ) )
					$topic_id = (int) $topics[0]->ID;
			}

			$found_arr = array();
			if ( $forum_id > 0 ) $found_arr['forum'] = $forum_id;
			if ( $topic_id > 0 ) $found_arr['topic'] = $topic_id;
			
			if ( empty($found_arr) ) return $ret;
			
			foreach ($found_arr as $the_key => $the_id) {
				$the_info = $this->get_post_info( $the_id );
				foreach ($the_info as $k => $v) {
					$ret["{$the_key}_{$k}"] = $v;
				}
			}
  
			return $ret;
		}

		private function get_post_info($post_id=0) {
			$__postClean = array();
			
			if ( empty($post_id) ) return $__postClean;
			
			$__post = get_post($post_id);

			//post title
			$__postClean['title'] = strip_tags( apply_filters( 'single_post_title', $__post->post_title ) );

			//post date
			$__postClean['date'] = '';
 			if ( isset($__post->post_date) && !empty($__post->post_date) ) {
 				$__postClean['date'] = mysql2date( get_option( 'date_format' ), $__post->post_date );
 			}
 					
 			//post description
			$__postClean['description'] = strip_shortcodes( $__post->post_content );

 			//post short description!
 			if ( !empty($__post->post_excerpt) ) {
 				$__postClean['short_description'] = strip_tags( $__post->post_excerpt );
 			} else {
 				$__postClean['short_description'] = wp_html_excerpt( strip_shortcodes( $__post->post_content ), 200 );
 			}
 					
			//post author
			$__author_id = $__post->post_author;
 			$__postClean = array_merge($__postClean, array(
	 			'author'			=> get_the_author_meta( 'display_name', $__author_id ),
	 			'author_username'	=> get_the_author_meta( 'user_login', $__author_id ),
	 			'author_nickname'	=> get_the_author_meta( 'nickname', $__author_id ),
	 			'author_description'=> get_the_author_meta( 'description', $__author_id )
 			));

			return $__postClean;
		}


		/**
		 * get post meta SEO
		 */
		public function fix_canonical_url($url) {
			if( bp_is_blog_page() || bp_is_directory() )
				return $url;
			 
			//in other case, return current url without the query string
			$url = $this->bp_get_page_url();
			return $url;
		}
		public function bp_get_page_url() {
			$page_url = 'http';
			 
			if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' )
				$page_url .= 's';
				$page_url .= '://';
			 
			if ($_SERVER['SERVER_PORT'] != '80')
				$page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
			else
				$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			 
			//find if the url has query variables
			$query_pos = strpos( $page_url, '?' );
			 
			//let us exclude that section from url
			if( $query_pos )
				$page_url = substr( $page_url, 0, $query_pos );
			 
			return $page_url;
		}

		public function meta_seo_title($value) {
			return $this->get_meta_seo($value, 'title');
		}
		public function meta_seo_description($value) {
			return $this->get_meta_seo($value, 'description');
		}
		public function meta_seo_keywords($value) {
			return $this->get_meta_seo($value, 'keywords');
		}
		public function meta_seo_robots($value) {
			return $this->get_meta_seo($value, 'robots');
		}
		
		private function get_meta_seo($value, $field) {

    		global $bp;
  
			// forum
			if ( bp_is_current_action( 'forum' ) && function_exists( 'bbpress' ) ) {
			//$x = $bp->action_variables;
			
			$forum_id = 0; $topic_id = 0; $post_id = 0;
			
			$forum_ids = bbp_get_group_forum_ids(); //array of ids
			if( $forum_ids ) $forum_id = (int) array_pop ($forum_ids);
			
			$post_id = $forum_id;
   
			//if is single topic
			if ( bp_is_action_variable('topic', 0) && bp_action_variable(1) ) {
				//get the topic as post
				$topics = get_posts( array(
					'name'			=> bp_action_variable(1),
					'post_type'		=> bbp_get_topic_post_type(),
					'per_page'		=> 1
				) );
				//get the id
				if( !empty( $topics ) )
					$topic_id = (int) $topics[0]->ID;
				
				$post_id = $topic_id;
			}
			
			if ( $post_id > 0 ) {
				$the_info = get_post_meta( $post_id, 'psp_meta', true );
  
				foreach (array('title', 'description', 'keywords', 'robots') as $v) {
					if ( $field == $v ) {
							
						if ( $field == 'keywords' && isset($the_info["focus_keyword"])
							&& !empty($the_info["focus_keyword"]) ) {
							return $the_info["focus_keyword"];
						}

						if ( $v == 'robots' ) {
							$__robots_generaltag = (array) explode(',', $value);
							
							$__robots_item = array();
							if ( isset($the_info["robots_index"])
								&& !empty($the_info["robots_index"])
								&& $the_info["robots_index"]!='default' )
								$__robots_item[] = $the_info["robots_index"];
							if ( isset($the_info["robots_follow"])
								&& !empty($the_info["robots_follow"])
								&& $the_info["robots_follow"]!='default' )
								$__robots_item[] = $the_info["robots_follow"];
$x = $this->get_robots_values(array(
								'item' 			=> $__robots_item,
								'generaltag'	=> $__robots_generaltag
							));
  
							return $this->get_robots_values(array(
								'item' 			=> $__robots_item,
								'generaltag'	=> $__robots_generaltag
							));
						}

						if ( isset($the_info["$v"]) && !empty($the_info["$v"]) ) {
  							return $the_info["$field"];
						}
					}
				}
			}
			
			}
			// component directory
			else if( bp_is_blog_page() || bp_is_directory() ) {
				// treated in module init file : get_current_field
			}
			
			return $value;
		}

		private function get_robots_values($meta_robots) {
        	$m = $meta_robots;
        	$mi = isset($m['item']) ? (array) $m['item'] : array();
        	$mt = isset($m['generaltag']) ? (array) $m['generaltag'] : array();

        	$__meta_robots = array();
        	if ( in_array('index', $mi) )
				$__meta_robots[] = 'index';
        	else if ( in_array('noindex', $mi) )
				$__meta_robots[] = 'noindex';
        	else if ( in_array('index', $mt) )
				$__meta_robots[] = 'index';
        	else if ( in_array('noindex', $mt) )
				$__meta_robots[] = 'noindex';
			else
				$__meta_robots[] = 'index';

        	if ( in_array('follow', $mi) )
				$__meta_robots[] = 'follow';
        	else if ( in_array('nofollow', $mi) )
				$__meta_robots[] = 'nofollow';
        	else if ( in_array('follow', $mt) )
				$__meta_robots[] = 'follow';
        	else if ( in_array('nofollow', $mt) )
				$__meta_robots[] = 'nofollow';
			else
				$__meta_robots[] = 'follow';

			$__meta_robots_extra = array();
        	if ( in_array('noarchive', $mt) )
				$__meta_robots_extra[] = 'noarchive';
        	if ( in_array('noodp', $mt) )
				$__meta_robots_extra[] = 'noodp';
			$__meta_robots_extra = implode(',', $__meta_robots_extra);
				
			$__meta_robots = implode(',', $__meta_robots);
			if ( ($found = preg_match('/^index,follow/i', $__meta_robots))!==false && $found>0 )
				$__meta_robots = '';

			$__meta_robots = $__meta_robots
				. ( $__meta_robots!='' && $__meta_robots_extra!='' ? ',' : '') . $__meta_robots_extra;
				
			return $__meta_robots;
		}
    }
}

// Initialize the pspBuddyPressTags class
//$pspBuddyPressTags = new pspBuddyPressTags();