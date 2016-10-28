<?php
/*
* Define class pspVideoInfo
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspVideoInfo') != true) {
    class pspVideoInfo
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';
        
        private $videoInfo = array();
        
        private $atts = array();

	
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $atts = array() )
        {
	        $this->initVideoInfo();
	        $this->atts = $atts;
        }
        
        private function initVideoInfo() {
        	$this->videoInfo = array(
	        	'status'				=> 'invalid',
	        	'resp'					=> '',
	        	'created'				=> time(),
	        	'type'					=> '',
	        	'videoid'				=> '',
	        	
		        'tags'					=> array(),
		        'categories'			=> array(),

	        	'publish_date'			=> '',
	        	'author'				=> '',

		        'title' 				=> '',
		        'description' 			=> '',
		        'thumbnail'				=> '',

	        	'player_loc'			=> '',
	        	'content_loc'			=> '',
		        'duration' 				=> '',
		        'ratings' 				=> '',
		        'view_count'			=> ''
	        );
        }
        
		/**
	    * Singleton pattern
	    *
	    * @return pspSEOImages Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
	    
	    /**
	     * connect remote
	     */
	    private function remote_get( $api_url, $output='default' ) {

	    	global $psp;
	    	
	    	$ret = array('status' => 'invalid', 'resp' => '');
	    	
	    	//$getdata = simplexml_load_file($api_url);

	    	$getdata = $psp->remote_get( $api_url, 'default' );
	    	if ( !isset($getdata) || $getdata['status'] === 'invalid' ) {
	    		return array_merge( $ret, array(
	    			'resp' => $api_url . ' / ' . $getdata['msg']
	    		));
	    	}

	    	$getdata = $getdata['body'];

	    	if ( $output == 'json' )
	    		$getdata = json_decode( $getdata );
	    	else if ( $output == 'xml' )
	    		$getdata = simplexml_load_string( $getdata, 'SimpleXMLElement', LIBXML_NOCDATA );
	    	else if ( $output == 'serialized' )
	    		$getdata = unserialize( $getdata );
	    	else ;

	    	return array_merge( $ret, array(
	    		'status' => 'valid',
	    		'resp' => $getdata
	    	));
	    }
	    
	    
	    /**
	     * clean array elements
	     */
	    private function cleanInfo( $arr=array() ) {
	    	if ( empty($arr) ) return array();
	    	
	    	foreach ( $arr as $key => $val ) {

	    		// 'status', 'resp', 'created', 'type', 'videoid', 'tags', 'categories', 'publish_date', 'author', 'title', 'description', 'thumbnail', 'player_loc', 'duration', 'ratings', 'view_count'

	    		if ( in_array($key, array('tags', 'categories', 'author', 'title', 'description')) ) {

		    		if ( is_array($val) && !empty($val) ) {
		    			foreach ( $val as $kk => $vv ) {

		    				$vv = trim( $vv );
		    				$vv = strip_tags( $vv );
		    				$vv = htmlspecialchars( $vv, ENT_QUOTES, 'UTF-8' );
		    				$arr[ "$key" ][ "$kk" ] = $vv;
		    			}
		    		} else {

	    				$val = trim( $val );
		    			$val = strip_tags( $val );
		    			$val = htmlspecialchars( $val, ENT_QUOTES, 'UTF-8' );
		    			$arr[ "$key" ] = $val;
		    		}
		    		
		    		if ( $key == 'description' )
		    			$arr[ "$key" ] = substr( preg_replace( '/\s+/', ' ', $val ), 0, 350 );
	    		} else {
	    			$val = trim( $val );
		    		$val = strip_tags( $val );
		    		$val = htmlspecialchars( $val, ENT_QUOTES, 'UTF-8' );
		    		$arr[ "$key" ] = $val;
	    		}
	    	}
	    	return $arr;
	    }
	    
	    
	    /**
	     * get Video Info
	     */
	    public function getVideoInfo( $video_id='', $type='localhost', $atts=array() ) {

	    	try {
	    		$this->initVideoInfo();
	    		
	    		$post = null; $extrainfo = array();
	    		if ( !empty($atts) ) {
	    			if ( isset($atts['post']) )
	    				$post = $atts['post'];
	    			if ( isset($atts['extrainfo']) ) // attachments info!
	    				$extrainfo = $atts['extrainfo'];
	    		}

	    		if ( empty($video_id) ) return array_merge( $this->videoInfo, array('resp' => 'empty video id!') );
	    		
	    		if ( !is_null($post) && isset($post->ID) ) {

	    			$post_id = (int) $post->ID;
	    			$post_title = (string) $post->post_title;
	    			$publish_date = date( DATE_W3C, strtotime( $post->post_date_gmt ) );

	    			$post_excerpt = ( $post->post_excerpt != "" ) ? $post->post_excerpt : $post->post_title;
	    			$post_excerpt = substr( preg_replace( '/\s+/', ' ', $post_excerpt ), 0, 350 );
	    			
					$author_id = $post->post_author;
					$author_name = get_the_author_meta( 'first_name', $author_id ) . ' ' . get_the_author_meta( 'last_name', $author_id );

    				$categories = $this->format_items( get_the_category( $post_id ), 'categories', '', 'localhost' );
    				if ( count($categories) > 0 )
    					$category = (string) array_shift($categories);

    				$focus_kw = get_post_meta( $post_id, 'psp_kw', true );
    				if ( !empty($focus_kw) ) {
    					if ( ($__findsep = strpos($focus_kw, ',') ) !== false )
    						$focus_kw = substr($focus_kw, 0, $__findsep);
    				}
  				
    				$tags = array();
    				$thetags = $this->format_items( get_the_tags( $post_id ), 'tags', '', 'localhost' );
    				if ( !empty($categories) ) $tags = array_merge($tags, (array) $categories);
    				if ( !empty($focus_kw) ) $tags = array_merge($tags, (array) $focus_kw);
    				if ( !empty($thetags) ) $tags = array_merge($tags, (array) $thetags);
    				$tags = $this->format_items( $tags, 'tags', '' );

					$__featured_image = '';
					if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_id ) ) {
	    				$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
	    				$__featured_image = $__featured_image[0];
					}
					$thumb_default = $__featured_image;
	    			
			        $this->videoInfo = array_merge( $this->videoInfo, array(
			        	'type'					=> $type,
			        	'videoid'				=> $video_id,
			        	
				        'tags'					=> $tags,
				        'categories'			=> $category,

			        	'publish_date'			=> $publish_date,
			        	'author'				=> $author_name,
		
				        'title' 				=> $post_title,
				        'description' 			=> $post_excerpt,
				        'thumbnail'				=> $thumb_default
			        ));
			        //var_dump('<pre>orig:',$this->videoInfo ,'</pre>');
	    		}
	    		
	    		if ( !empty($extrainfo) ) {

	    			$__post_id = (int) $extrainfo->ID;
	    			
	    			$__post_title = $this->videoInfo['title'];
	    			if ( $extrainfo->post_title != "" )
	    				$__post_title = $extrainfo->post_title;
	    			
	    			$__publish_date = $this->videoInfo['publish_date'];
	    			if ( $extrainfo->post_date_gmt != "" )
	    				$__publish_date = date( DATE_W3C, strtotime( $extrainfo->post_date_gmt ) );

	    			$__post_excerpt = $this->videoInfo['description'];
	    			if ( $extrainfo->post_content != "" )
	    				$__post_excerpt = strip_tags( $this->strip_shortcode( $extrainfo->post_content ) );
	    			if ( $extrainfo->post_excerpt != "" )
	    				$__post_excerpt = $extrainfo->post_excerpt;
	    			if ( $__post_excerpt != "" )
						$__post_excerpt = substr( preg_replace( '/\s+/', ' ', $__post_excerpt ), 0, 350 );
					
					$video_details = get_post_meta( $__post_id, '_wp_attachment_metadata', true );
					$duration = '';
					if ( isset($video_details['length']) )
						$duration = (string) $video_details['length'];
						
	    			$content_loc = (string) $extrainfo->guid;

			        $this->videoInfo = array_merge( $this->videoInfo, array(
			        	'publish_date'			=> $__publish_date,
			        	'title' 				=> $__post_title,
				        'description' 			=> $__post_excerpt,
				        'duration'				=> $duration,
				        'content_loc'			=> $content_loc
			        ));
			        //var_dump('<pre>orig:',$this->videoInfo ,'</pre>');
	    		}

	    		$output = 'json';
	    		switch ( $type ) {

					// localhost
	    			default:
	    			case 'localhost':
		    				return array_merge( $this->cleanInfo( $this->videoInfo ), array(
			    				'status'		=> 'valid',
			    				'type' 			=> 'localhost'
			    			));
	    				break;
	    				
	    			case 'youtube':
	    				$api_url = "https://gdata.youtube.com/feeds/api/videos/$video_id?v=2&alt=json";
	    				break;
	    				
	    			case 'dailymotion':
	    				$api_url = "https://api.dailymotion.com/video/$video_id?fields=title,duration,description,thumbnail_360_url,tags,created_time,owner,embed_url,views_total,rating,ratings_total";
	    				break;
	    				
	    			case 'vimeo':
	    				$api_url = "http://vimeo.com/api/v2/video/$video_id.json";
	    				// $api_url = "http://vimeo.com/api/oembed.json?url=http://vimeo.com/$video_id";
	    				break;
	    				
	    			case 'metacafe':
	    				$output = 'xml';
	    				$api_url = "http://www.metacafe.com/api/item/$video_id/";
	    				break;
	    				
	    			case 'veoh':
	    				$output = 'xml';
	    				$api_url = "http://www.veoh.com/rest/video/$video_id/details";
	    				break;
	    				
	    			case 'screenr':
	    				$api_url = "http://www.screenr.com/api/oembed.json?url=http://www.screenr.com/$video_id";
	    				break;
	    				
	    			case 'wistia':
	    				$api_url = "http://fast.wistia.com/oembed?url=http://home.wistia.com/medias/$video_id";
	    				break;
	    				
	    			case 'vzaar':
	    				$api_url = "http://vzaar.com/api/videos/$video_id.json";
	    				break;
	    				
	    			case 'viddler':
	    				$output = 'serialized';
	    				// $api_url = "http://www.viddler.com/oembed/?url=http://www.viddler.com/v/$video_id&format=json&Submt=submit";
	    				$api_url = "http://api.viddler.com/api/v2/viddler.videos.getDetails.php?key={key}&video_id=$video_id";
	    				if ( isset($this->atts['viddler_key']) && !empty($this->atts['viddler_key']) )
	    					$api_url = str_replace('{key}', $this->atts['viddler_key'], $api_url);
	    				break;
	    				
	    			case 'blip':
	    				$output = 'xml';
	    				$api_url = "http://blip.tv/rss/view/$video_id";
	    				break;
	    				
	    			case 'dotsub':
	    				$api_url = "http://dotsub.com/services/oembed?url=http://dotsub.com/view/$video_id&format=json";
	    				break;
	    				
	    			case 'flickr':
	    				$api_url = "http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key={key}&photo_id=$video_id&format=json&nojsoncallback=1";
	    				if ( isset($this->atts['flickr_key']) && !empty($this->atts['flickr_key']) )
	    					$api_url = str_replace('{key}', $this->atts['flickr_key'], $api_url);
	    				break;
	    		}

				$getdata = $this->remote_get( $api_url, $output );
				if ( !isset($getdata) || $getdata['status'] === 'invalid' ) {
					return array_merge( $this->cleanInfo( $this->videoInfo ), array('resp' => $getdata['resp']) );
				}

				// response it's OK => parse it!
				$resp = $this->parseResponse( $video_id, $type, $getdata['resp'] );
				if ( $type == 'xyz' ) {
					//var_dump('<pre>', $resp , '</pre>'); die('debug...');
				}

				if ( $resp ) {
		    		return array_merge( $this->cleanInfo( $this->videoInfo ), array(
		    			'status'		=> 'valid'
		    			//,'resp' 			=> $getdata['resp']
		    		));
				}

				return array_merge( $this->videoInfo, array('resp' => 'error during response parsing!') );
	    	}
	    	catch (Exception $e) {
	    		return array_merge( $this->videoInfo, array('resp' => 'unknown error occured!') );
	    	}
	    }
	    
	    /**
	     * parse remote Response
	     */
	    private function parseResponse( $video_id, $type, $resp ) {
	    
	    	$this->videoInfo = array_merge( $this->videoInfo, array(
	    		'type'					=> $type
	    	));

	    	$ret = false;
	    	switch ( $type ) {

	    		default:
	    			break;

	    		case 'youtube':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://www.youtube.com/v/$video_id"
					));
	    			$ret = $this->youtube( $resp );
	    			break;
	    			
	    		case 'dailymotion':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://www.dailymotion.com/swf/$video_id" // swf | embed/video
					));
	    			$ret = $this->dailymotion( $resp );
	    			break;
	    			
	    		case 'vimeo':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						// 'player_loc'	=> "http://player.vimeo.com/video/$video_id"
						'player_loc'	=> "http://www.vimeo.com/moogaloop.swf?clip_id=$video_id"
					));
	    			$ret = $this->vimeo( $resp );
	    			break;
	    			
	    		case 'metacafe':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://www.metacafe.com/fplayer/$video_id/.swf"
					));
	    			$ret = $this->metacafe( $resp );
	    			break;
	    			
	    		case 'veoh':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						// 'player_loc'	=> "http://www.veoh.com/veohplayer.swf?permalinkId=$video_id"
						'player_loc'	=> "http://www.veoh.com/static/swf/veoh/SPL.swf?permalinkId=$video_id"
					));
	    			$ret = $this->veoh( $resp );
	    			break;
	    			
	    		case 'screenr':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://www.screenr.com/embed/$video_id"
					));
	    			$ret = $this->screenr( $resp );
	    			break;
	    			
	    		case 'wistia':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://fast.wistia.net/embed/iframe/$video_id/"
					));
	    			$ret = $this->wistia( $resp );
	    			break;
	    			
	    		case 'vzaar':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://vzaar.com/videos/$video_id"
					));
					if ( isset($this->atts['vzaar_domain']) && !empty($this->atts['vzaar_domain']) )
						$this->videoInfo['player_loc'] = 'http://' . $this->atts['vzaar_domain'] . '/' . $video_id;
	    			$ret = $this->vzaar( $resp );
	    			break;
	    			
	    		case 'viddler':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						// 'player_loc'	=> "http://www.viddler.com/embed/$video_id?Submt=submit/"
						'player_loc'	=> "http://www.viddler.com/player/$video_id/"
					));
	    			$ret = $this->viddler( $resp );
	    			break;
	    			
	    		case 'blip':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> ""
					));
	    			$ret = $this->blip( $resp );
	    			break;
	    			
	    		case 'dotsub':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> "http://dotsub.com/media/$video_id/embed/"
					));
	    			$ret = $this->dotsub( $resp );
	    			break;
	    			
	    		case 'flickr':
	    			$this->videoInfo = array_merge( $this->videoInfo, array(
						'player_loc'	=> ""
					));
	    			$ret = $this->flickr( $resp );
	    			break;
	    	}
	    	usleep(500);
	    	return $ret;
	    }
	    
	    /**
	     * return api response
	     *
	     */
	    private function getApiResponse( $vars = array() ) {

	    	if ( !empty($vars) ) extract($vars);

	    	// return array
	    	$ret = array();

	    	if ( isset($category) && !empty($category) ) {
	    		if ( is_array($category) )
	    			$ret['categories'] = (string) array_shift($category);
	    		else
	    			$ret['categories'] = (string) $category;
	    	}

			$ret['tags'] = array();
			$thetags = $this->format_items( array_merge($this->videoInfo['tags'], (array) $tags), 'tags', '' );
			if ( !empty($category) ) $ret['tags'] = array_merge($ret['tags'], (array) $category);
			if ( !empty($thetags) ) $ret['tags'] = array_merge($ret['tags'], (array) $thetags);
			$ret['tags'] = $this->format_items( $ret['tags'], 'tags', '' );

	    	if ( isset($publish_date) && !empty($publish_date) )
	    		$ret['publish_date'] = $publish_date;
	    	if ( isset($author) && !empty($author) )
	    		$ret['author'] = $author;

	    	if ( isset($title) && !empty($title) )
	    		$ret['title'] = $title;
	    	if ( isset($description) && !empty($description) )
	    		$ret['description'] = $description;
	    	if ( isset($thumbnail) && !empty($thumbnail) )
	    		$ret['thumbnail'] = $thumbnail;

	    	if ( isset($duration) && !empty($duration) )
	    		$ret['duration'] = $duration;
	    	if ( isset($ratings) && !empty($ratings) )
	    		$ret['ratings'] = $ratings;
	    	if ( isset($view_count) && !empty($view_count) )
	    		$ret['view_count'] = $view_count;

	    	if ( isset($player_loc) && !empty($player_loc) )
	    		$ret['player_loc'] = $player_loc;
	    		
	    	if ( isset($content_loc) && !empty($content_loc) )
	    		$ret['content_loc'] = $content_loc;

	    	$this->videoInfo = array_merge( $this->videoInfo, $ret );

	    	if ( $this->videoInfo['type'] == 'xyz' ) {
	    		//var_dump('<pre>', $this->videoInfo , '</pre>'); die('debug...');
	    	}

	    	if ( !empty($ret) && (
	    		( isset($this->videoInfo['player_loc']) && !empty($this->videoInfo['player_loc']) )
	    		|| 
	    		( isset($this->videoInfo['content_loc']) && !empty($this->videoInfo['content_loc']) ) )
	    	)
	    		return true;
	    	return false;
	    }

	    // youtube - json
	    private function youtube( $resp ) {

	    	$category = (string) $resp->{'entry'}->{'media$group'}->{'media$category'}[0]->{'$t'};

	    	$publish_date = (string) $resp->{'entry'}->published->{'$t'};
	    	$author = (string) $resp->{'entry'}->{'author'}[0]->{'name'}->{'$t'};
	    	
	    	$title = (string) $resp->{'entry'}->title->{'$t'};
	    	$description = (string) $resp->{'entry'}->{'media$group'}->{'media$description'}->{'$t'};
	    	$thumbnail = (string) $resp->{'entry'}->{'media$group'}->{'media$thumbnail'}[0]->url;
	    	
	    	$duration = (string) $resp->{'entry'}->{'media$group'}->{'media$content'}[0]->duration;
	    	$ratings = (string) $resp->{'entry'}->{'gd$rating'}->{'average'};
	    	$view_count = (string) $resp->{'entry'}->{'yt$statistics'}->{'viewCount'};


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // dailymotion - json
	    private function dailymotion( $resp ) {

	    	$tags = $this->format_items( (array) $resp->tags, 'tags', '' );

	    	$publish_date = (string) $resp->created_time;
			$publish_date = date( DATE_W3C, $publish_date );

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->description;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_360_url;
	    	
	    	$duration = (string) $resp->duration;
	    	$ratings = (string) $resp->rating;
	    	$view_count = (string) $resp->views_total;
	    	
	    	// author
	    	$author_id = (string) $resp->owner;
	    	$api_url_user = "https://api.dailymotion.com/user/$author_id?fields=screenname,username"; //fullname,first_name,last_name,
	    	$getdata = $this->remote_get( $api_url_user, 'json' );
	    	if ( !isset($getdata) || $getdata['status'] === 'invalid' ) {
	    		$author = '';
	    	}
	    	//if ( isset($getdata['resp']->fullname) )
	    	//	$author = (string) $getdata['resp']->fullname;
	    	//if ( empty($author)
	    	//	&& ( isset($getdata['resp']->first_name) && isset($getdata['resp']->last_name) ) )
	    	//	$author = ( (string) $getdata['resp']->first_name . ' ' . (string) $getdata['resp']->last_name );
	    	if ( empty($author) && isset($getdata['resp']->screenname) )
	    		$author = (string) $getdata['resp']->screenname;
	    	if ( empty($author) && isset($getdata['resp']->username) )
	    		$author = (string) $getdata['resp']->username;

	    	$__player_loc = (string) $resp->embed_url;
			if ( !empty($__player_loc) )
	    		$player_loc = $__player_loc;


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // vimeo - json
	    private function vimeo( $resp ) {

	    	$resp = $resp[0];

	    	$tags = $this->format_items( $resp->tags, 'tags', ',' );

	    	$publish_date = (string) $resp->upload_date;
			$publish_date = date( DATE_W3C, strtotime($publish_date) );

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->description;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_large;
	    	if ( empty($thumbnail) )
	    		$thumbnail = (string) $resp->thumbnail_medium;
	    	
	    	$duration = (string) $resp->duration;
	    	$ratings = '';
	    	$view_count = (string) $resp->stats_number_of_plays;
	    	
			$author = (string) $resp->user_name;


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // metacafe - xml
	    private function metacafe( $resp ) {

			$category = (string) $resp->channel[0]->item->category;

	    	$publish_date = (string) $resp->channel[0]->item->pubDate;
			$publish_date = date( DATE_W3C, strtotime($publish_date) );

	    	$title = (string) $resp->channel[0]->item->title;
	    	$description = (string) $resp->channel[0]->item->description;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) current( $resp->xpath('/rss/channel/item/media:thumbnail/@url') );
	    	
	    	$ratings = (string) $resp->channel[0]->item->rank;
	    	$view_count = '';
	    	
			$author = (string) $resp->channel[0]->item->author;
			
			$__player_loc = (string) current( $resp->xpath('/rss/channel/item/media:content/@url') );
			if ( !empty($__player_loc) )
				$player_loc = $__player_loc;
			
	    	$duration = (string) current( $resp->xpath('/rss/channel/item/media:content/@duration') );


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // veoh - xml
	    private function veoh( $resp ) {

			$category = $resp->xpath('/videos/video/categories/category');
			$category = (string) $category[0];

	    	$tags = $this->format_items( (string) current( $resp->xpath('/videos/video/@tags') ), 'tags', ',' );

	    	$publish_date = (string) current( $resp->xpath('/videos/video/@dateAdded') );
			$publish_date = date( DATE_W3C, strtotime($publish_date) );

	    	$title = (string) current( $resp->xpath('/videos/video/@title') );
	    	$description = (string) current( $resp->xpath('/videos/video/@description') );
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) current( $resp->xpath('/videos/video/@fullMedResImagePath') );
	    	
	    	$ratings = (string) current( $resp->xpath('/videos/video/@rating') );
	    	$view_count = (string) current( $resp->xpath('/videos/video/@numRatingVotes') );
	    	
			$author = (string) current( $resp->xpath('/videos/video/@username') );
			
	    	$duration = (string) current( $resp->xpath('/videos/video/@length') );
	    	sscanf( $duration, "%d:%d:%d", $hours, $minutes, $seconds );
			$duration = (string) ( isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes );


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // screenr - json
	    private function screenr( $resp ) {

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->description;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_url;
	    	
	    	// author
	    	$author = (string) $resp->author_name;


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // wistia - json
	    private function wistia( $resp ) {

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->title;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_url;

	    	$duration = (string) $resp->duration;
	    	
	    	$html = (string) $resp->html;
	    	if ( preg_match( '/<iframe src=(?:\'|")(.*?)(?:\'|")/iu', $html, $match ) ) {

	    		$content_loc = $match[1];

	    		$getdata = $this->remote_get( $match[1] );
	    		if ( !isset($getdata) || $getdata['status'] === 'invalid' ) ;
	    		else {
	    			if ( preg_match( '/<a href=(?:\'|")(.*?)(?:\'|")\s+id=(?:\'|")wistia_fallback(?:\'|")/iu', (string) $getdata['resp'], $match2 ) )
	    				$content_loc = $match2[1];
	    		}
	    	}


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // vzaar - json
	    private function vzaar( $resp ) {

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->title;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_url;

	    	$duration = (string) $resp->duration;
	    	
	    	$__player_loc = (string) $resp->video_url;
			if ( !empty($__player_loc) )
				$player_loc = $__player_loc;


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // viddler - serialized
	    private function viddler( $resp ) {

	    	//$title = (string) $resp->title;
	    	//$description = (string) $resp->title;
			//$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	//$thumbnail = (string) $resp->thumbnail_url;

	    	$tags = $this->format_items( (array) $resp['video']['tags'], 'tags', '', 'viddler' );
	    	
			$title = (string) $resp['video']['title'];
			$description = (string) $resp['video']['description'];
			$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
			$thumbnail = (string) $resp['video']['thumbnail_url'];
			
			$duration = (string) $resp['video']['length'];
			
			$view_count = (string) $resp['video']['view_count'];
	    	
			$files = $video['video']['files'];
			if ( isset($files) && is_array($files) && !empty($files) ) {
				foreach ( $files as $file ) {
					if ( $file['ext'] == 'mp4' )
						$content_loc = $file['url'];
				}
			}


			// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // blip - xml
	    private function blip( $resp ) {

	    	$tags = $this->format_items( (array) $resp->xpath('/rss/channel/item/category'), 'tags', '', 'blip' );

	    	$publish_date = (string) current( $resp->xpath('/rss/channel/item/pubDate') );
			$publish_date = date( DATE_W3C, strtotime($publish_date) );

	    	$title = (string) current( $resp->xpath('/rss/channel/item/title') );
	    	$description = (string) current( $resp->xpath('/rss/channel/item/description') );
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);

	    	$thumbnail = (string) current( $resp->xpath('/rss/channel/item/media:thumbnail/@url') );
	    	
	    	$ratings = (string) current( $resp->xpath('/rss/channel/item/blip:rating') );
	    	$view_count = '';
	    	
			$author = (string) current( $resp->xpath('/rss/channel/item/blip:user') );
			
			$duration = (string) current( $resp->xpath('/rss/channel/item/blip:runtime') );
			
	    	$player_loc = (string) current( $resp->xpath('/rss/channel/item/blip:embedUrl') );
	    	if ( empty($player_loc) )
				$player_loc = (string) current( $resp->xpath('/rss/channel/item/media:player@url') );
				
			$content_loc = (string) current( $resp->xpath('/rss/channel/item/enclosure/@url') );


	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // dotsub - json
	    private function dotsub( $resp ) {

	    	$title = (string) $resp->title;
	    	$description = (string) $resp->title;
	    	$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
	    	$thumbnail = (string) $resp->thumbnail_url;

	    	// author
	    	$author = (string) $resp->author_name;
	    	
	    	$html = (string) $resp->html;
	    	if ( preg_match( '/<iframe src=(?:\'|")(.*?)(?:\'|")/iu', $html, $match ) ) {

	    		$content_loc = $match[1];

	    		$getdata = $this->remote_get( $match[1] );
	    		if ( !isset($getdata) || $getdata['status'] === 'invalid' ) ;
	    		else {
	    			if ( preg_match( '/"file":\s*(?:"|\')([^"\']+)(?:"|\')\s*,/iu', (string) $getdata['resp'], $match2 ) )
	    				$content_loc = $match2[1];
	    		}
	    	}
	    	
	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    // flickr - json
	    private function flickr( $resp ) {

			if ( !isset($resp->photo->media) || $resp->photo->media != 'video' ) return array();
			$resp = $resp->photo;

			$video_id = (string) $resp->id;
			$photo_secret = (string) $resp->secret;
			$photo_farm = (string) $resp->farm;
			$photo_server = (string) $resp->server;
			
	    	$tags = $this->format_items( (array) $resp->tags->tag, 'tags', '', 'flickr' );
	    	
			$title = (string) $resp->title->_content;
			$description = (string) $resp->description->_content;
			$description = preg_replace('/<br \/>/iu', PHP_EOL, $description);
			$thumbnail = (string) "http://farm$photo_farm.staticflickr.com/$photo_server/{$video_id}_{$photo_secret}.jpg";
			
			if ( isset($resp->dateuploaded) )
				$publish_date = (string) $resp->dateuploaded;
			if ( isset($resp->dates->posted) )
				$publish_date = (string) $resp->dates->posted;
			if ( isset($publish_date) && !empty($publish_date) )
				$publish_date = date( DATE_W3C, $publish_date );
			
			$duration = (string) $resp->video->duration;
			
			$view_count = (string) $resp->views;
			
			if ( isset($resp->owner->username) )
				$author = (string) $resp->owner->username;
			if ( isset($resp->owner->realname) )
				$author = (string) $resp->owner->realname;

			$player_loc = "http://www.flickr.com/apps/video/stewart.swf?v=109786&intl_lang=en_us&photo_secret=$photo_secret&photo_id=$video_id";

	    	// return array
	    	return $this->getApiResponse( compact(
	    		'tags', 'category', 'publish_date', 'author', 'title', 'description', 'thumbnail',
	    		'duration', 'ratings', 'view_count', 'player_loc', 'content_loc'
	    	));
	    }
	    
	    
	    /**
	     * Video Utils
	     * 
	     */
	    
	    // format tags | categories received in api response!
	    private function format_items( $items, $type='tags', $sep=',', $api='' ) {

			if ( empty($items) || $items === false ) return '';
			
			if ( !is_array($items) )
				$items = explode($sep, $items);

			if ( is_array($items) && !empty($items) ) {
				$ret = array();
				$count = 0;
				foreach ( $items as $item ) {
					if ( $type=='tags' && ( $count++ > 32 ) ) break;
					
					$val = $item;
					switch ($api) {
						case 'localhost':
							$val = $item->name;
							break;

						case 'blip':
							$val = $item[0];
							break;
							
						case 'viddler':
							$val = $item['text'];
							break;
							
						case 'flickr':
							$val = $item->raw;
							break;
					}
					$ret[] = (string) trim( $val );
				}
				return $ret;
			} else {
				return $items;
			}
	    }
	    
	    private function strip_shortcode( $text ) {
	    	return preg_replace( '`\[[^\]]+\]`s', '', $text );
	    }
    }
}

// Initialize the pspVideoInfo class
$pspVideoInfo = new pspVideoInfo();