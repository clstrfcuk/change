<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		pspAjaxListTable
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('pspAjaxListTable') != true) {
	class pspAjaxListTable {

		/*
        * Some required plugin information
        */
        const VERSION = '1.0';

		/*
        * Singleton pattern
        */
		static protected $_instance;

		/*
        * Store some helpers
        */
		public $the_plugin = null;

		/*
        * Store some default options
        */
		public $default_options = array(
			'id' 					=> '', /* string, uniq list ID. Use for SESSION filtering / sorting actions */
			'debug_query' 			=> false, /* default is false */
			'show_header' 			=> true, /* boolean, true or flase */
			'list_post_types' 		=> 'all', /* array('post', 'pages' ... etc) or 'all' */
			'items_per_page' 		=> 10, /* number. How many items per page */
			'post_statuses' 		=> 'all',
			'search_box' 			=> true, /* boolean, true or flase */
			'show_statuses_filter' 	=> true, /* boolean, true or flase */
			'show_pagination' 		=> true, /* boolean, true or flase */
			'show_category_filter' 	=> false, /* boolean, true or flase */
			'columns' 				=> array(),
			'custom_table' 			=> '',
			'requestFrom'			=> 'init', /* values: init | ajax */
			
			'custom_table_force_action' 	=> false,
			'deleted_field' 				=> false,
			'force_publish_field' 			=> false,
			'show_header_buttons' 			=> false
		);
		private $items;
		private $items_nr;
		private $args;

		public $opt = array();
		
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
        	$this->the_plugin = $parent;
			add_action('wp_ajax_pspAjaxList', array( $this, 'request' ));

            $session_id = isset($_COOKIE["PHPSESSID"]) ? session_id($_COOKIE["PHPSESSID"]) : session_id();
            if(!$session_id) {
			    // session isn't started
			    session_start();
			}
        }

		/**
	    * Singleton pattern
	    *
	    * @return class Singleton instance
	    */
	    static public function getInstance( $parent )
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self($parent);
	        }

	        return self::$_instance;
	    }

		/**
	    * Setup
	    *
	    * @return class
	    */
		public function setup( $options=array() )
		{
			global $psp;
			$this->opt = array_merge( $this->default_options, $options );

			//unset($_SESSION['pspListTable']); // debug

			// check if set, if not, reset
			if ( isset($options['requestFrom']) && $options['requestFrom'] == 'ajax' ) ;
			else {

				$keepvar = isset($_SESSION['pspListTable']['keepvar']) ? $_SESSION['pspListTable']['keepvar'] : '';
				$sess = isset($_SESSION['pspListTable'][$this->opt['id']]['params']) ? $_SESSION['pspListTable'][$this->opt['id']]['params'] : array();

				$options['params']['posts_per_page'] = isset($sess['posts_per_page']) ? $sess['posts_per_page'] : $this->opt['items_per_page'];
				if ( isset($keepvar) && isset($keepvar['paged']) ) {
					$options['params']['paged'] = isset($sess['paged']) ? $sess['paged'] : 1;
					unset( $keepvar['paged'] );
					$_SESSION['pspListTable']['keepvar'] = $keepvar;
				}

			}
			$_SESSION['pspListTable'][$this->opt['id']] = $options;

			return $this;
		}

		/**
	    * Singleton pattern
	    *
	    * @return class Singleton instance
	    */
		public function request()
		{
			$request = array(
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
				'ajax_id' 		=> isset($_REQUEST['ajax_id']) ? $_REQUEST['ajax_id'] : '',
				'params' 		=> isset($_REQUEST['params']) ? $_REQUEST['params'] : '',
			);

			if( $request['sub_action'] == 'post_per_page' ){
				$new_post_per_page = $request['params']['post_per_page'];

				if( $new_post_per_page == 'all' ){
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = '-1';
				}
				elseif( (int)$new_post_per_page == 0 ){
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = $this->opt['items_per_page'];
				}
				else{
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = $new_post_per_page;
				}

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}

			if( $request['sub_action'] == 'paged' ){
				$new_paged = $request['params']['paged'];
				if( $new_paged < 1 ){
					$new_paged = 1;
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = $new_paged;
			}

			if( $request['sub_action'] == 'post_type' ){
				$new_post_type = $request['params']['post_type'];
				if( $new_post_type == "" ){
					$new_post_type = "";
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['post_type'] = $new_post_type;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}

			if( $request['sub_action'] == 'post_status' ){
				$new_post_status = $request['params']['post_status'];
				if( $new_post_status == "all" ){
					$new_post_status = "";
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['post_status'] = $new_post_status;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}
			
			if( $request['sub_action'] == 'search' ){
				$search_text = $request['params']['search_text'];
				
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['search_text'] = $search_text;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}
			

			// create return html
			ob_start();

			$_SESSION['pspListTable'][$request['ajax_id']]['requestFrom'] = 'ajax';

			$this->setup( $_SESSION['pspListTable'][$request['ajax_id']] );
			$this->print_html();
			$html = ob_get_contents();
			ob_clean();

			die( json_encode(array(
				'status' 	=> 'valid',
				'html'		=> $html
				//,'sess'		=> $_SESSION['pspListTable'][$request['ajax_id']]['params']
			)) );
		}

		/**
	    * Helper function
	    *
	    * @return object
	    */
		public function get_items()
		{
			global $wpdb;

			$ses = isset($_SESSION['pspListTable'][$this->opt['id']]['params']) ? $_SESSION['pspListTable'][$this->opt['id']]['params'] : array();
			
			$this->args = array(
				'posts_per_page'  	=> ( isset($ses['posts_per_page']) ? $ses['posts_per_page'] : $this->opt['items_per_page'] ),
				'paged'				=> ( isset($ses['paged']) ? $ses['paged'] : 1 ),
				'category'        	=> ( isset($ses['category']) ? $ses['category'] : '' ),
				'orderby'         	=> 'post_date',
				'order'          	=> 'DESC',
				'post_type'       	=> ( isset($ses['post_type']) && trim($ses['post_type']) != "all" ? $ses['post_type'] : array_keys($this->get_list_postTypes()) ),
				'post_status'     	=> ( isset($ses['post_status']) ? $ses['post_status'] : '' ),
				'suppress_filters' 	=> true
			);

			if ( in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) { // smushit
				$this->args = array_merge($this->args, array(
					'post_type'			=> 'attachment',
					'post_status'		=> 'inherit',
					'post_mime_type'	=> array('image/jpeg', 'image/jpg', 'image/png')
				));
				$this->args = array_merge( $this->args, $this->post_media_getQuery( isset($ses['post_status']) ? $ses['post_status'] : '' ) );
			}

			// if custom table, make request in the custom table not in wp_posts
			if( trim($this->opt["custom_table"]) != ""){
				$pages = array();

			    // select all pages and post from DB
			    $myQuery = "SELECT * FROM " . $wpdb->prefix . ( $this->opt["custom_table"] ) . " WHERE 1=1 ";

			    $__limitClause = $this->args['posts_per_page']>0 ? " 1=1 limit " . (($this->args['paged'] - 1) * $this->args['posts_per_page']) . ", " . $this->args['posts_per_page'] : '1=1 ';
				$result_query = str_replace("1=1 ", $__limitClause, $myQuery);
				
				if( $this->opt["custom_table"] == 'psp_serp_reporter' ) {
					$result_query = str_replace('1=1 limit', " 1=1 ORDER BY focus_keyword desc limit ", $result_query);
			    	if (isset($_SESSION['psp_serp']['search_engine']) && !empty($_SESSION['psp_serp']['search_engine']) && $_SESSION['psp_serp']['search_engine']!='--all--') {
			    		$myQuery = str_replace("1=1 ", " 1=1 and search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $myQuery);
			    		$result_query = str_replace("1=1 ", " 1=1 and search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $result_query);
			    	}
				}

				//publish field
			    if ($this->opt["force_publish_field"]) {
			    	$myQuery = str_replace("1=1 ", " 1=1 and publish='Y' ", $myQuery);
			    	$result_query = str_replace("1=1 ", " 1=1 and publish='Y' ", $result_query);
			    }
			    
			    //deleted field
			    if ($this->opt["deleted_field"]) {
			    	$myQuery = str_replace("1=1 ", " 1=1 and deleted=0 ", $myQuery);
			    	$result_query = str_replace("1=1 ", " 1=1 and deleted=0 ", $result_query);
			    }


			    $myQuery .= ";"; $result_query .= ";"; //query end!

			    $query = $wpdb->get_results( $result_query, ARRAY_A);
			    foreach ($query as $key => $myrow){
			    	if( $this->opt["custom_table"] == 'psp_monitor_404' ) {
			    		$pages[$myrow['id']] = array(
							'id' 			=> $myrow['id'],
							'hits' 			=> $myrow['hits'],
							'url'			=> $myrow['url'],
							'referrers' 	=> $myrow['referrers'],
							'user_agents' 	=> $myrow['user_agents'],
							'data' 			=> $myrow['data'],
						);
			    	}
					
					else if( $this->opt["custom_table"] == 'psp_web_directories' ) {
						$pages[$myrow['id']] = array(
							'id' 					=> $myrow['id'],
							'directory_name' 		=> $myrow['directory_name'],
							'submit_url' 			=> $myrow['submit_url'],
				    		'pagerank' 				=> $myrow['pagerank'],
				    		'alexa'					=> $myrow['alexa'],
				    		'status'				=> $myrow['status']
						);
					}
					
					else if( $this->opt["custom_table"] == 'psp_link_builder' ) {
						$pages[$myrow['id']] = array(
							'id' 			=> $myrow['id'],
							'url' 			=> $myrow['url'],
				    		'phrase' 		=> $myrow['phrase'],
				    		'rel'			=> $myrow['rel'],
				    		'title'			=> $myrow['title'],
				    		'target'		=> $myrow['target'],
							'hits' 			=> $myrow['hits'],
							'created' 		=> $myrow['created'],
							'publish'		=> $myrow['publish']
						);
					}
					
					else if( $this->opt["custom_table"] == 'psp_proxy_lists' ) {
						$pages[$myrow['id']] = array(
							'id' 				=> $myrow['id'],
							'ip' 				=> $myrow['ip'],
				    		'port' 				=> $myrow['port'],
				    		'speed'				=> $myrow['speed'],
				    		'hits'				=> $myrow['hits'],
							'created' 			=> $myrow['created'],
							'publish'			=> $myrow['publish']
						);
					}
					
					else if( $this->opt["custom_table"] == 'psp_link_redirect' ) {
						$pages[$myrow['id']] = array(
							'id' 				=> $myrow['id'],
							'url' 				=> $myrow['url'],
				    		'url_redirect' 		=> $myrow['url_redirect'],
							'hits' 				=> $myrow['hits'],
							'created' 			=> $myrow['created']
						);
					}
					
					else if( $this->opt["custom_table"] == 'psp_serp_reporter' ) {
						$pages[$myrow['id']] = array(
							'id' 					=> $myrow['id'],
							'focus_keyword' 		=> $myrow['focus_keyword'],
							'url' 					=> $myrow['url'],
				    		'position' 				=> $myrow['position'],
				    		'position_prev'			=> $myrow['position_prev'],
				    		'position_worst'		=> $myrow['position_worst'],
				    		'position_best'			=> $myrow['position_best'],
							'visits' 				=> $myrow['visits'],
							'created' 				=> $myrow['created'],
							'publish'				=> $myrow['publish'],
							'engine_location'		=> substr($myrow['search_engine'], strpos($myrow['search_engine'], '.'))
						);
					}
					
			    	else if( $this->opt["custom_table"] == 'psp_post_planner_cron' ) {
			    		$pages[$myrow['id']] = array(
							'id' 					=> $myrow['id'],
							'id_post' 				=> $myrow['id_post'],
							'post_to'				=> $myrow['post_to'],
							'post_to_group' 	=> $myrow['post_to-page_group'],
							'post_privacy' 			=> $myrow['post_privacy'],
							'email_at_post' 		=> $myrow['email_at_post'],
							'status' 				=> $myrow['status'],
							'response' 				=> $myrow['response'],
							'started_at' 			=> $myrow['started_at'],
							'ended_at' 				=> $myrow['ended_at'],
							'run_date' 				=> $myrow['run_date'],
							'repeat_status' 		=> $myrow['repeat_status'],
							'repeat_interval' 		=> $myrow['repeat_interval'],
							'attempts' 				=> $myrow['attempts']
						);
			    	}
			    }
				
				if( $this->opt['debug_query'] == true ){
					echo '<script>console.log("' . $result_query . '");</script>';
				}

				$this->items = $pages;

				$this->items_nr = $wpdb->get_var( str_replace("*", "count(id) as nbRow", $myQuery) );

			}else{

				// remove empty array
				$this->args = array_filter($this->args);

				//hook retrieve posts where clause
				add_filter( 'posts_where' , array( &$this, 'search_posts_where' ) );

				$args = array_merge($this->args, array(
					'suppress_filters' => false
				));

				$this->items = get_posts( $args );

				// get all post count
				$nb_args = $args;
				$nb_args['posts_per_page'] = '-1';
 				$nb_args['fields'] = 'ids';
				$this->items_nr = (int) count( get_posts( $nb_args ) );
				
				if( $this->opt['debug_query'] == true ){
					$query = new WP_Query( $this->args );
					echo '<script>console.log("' . $query->request . '");</script>';
				}
			}

			return $this;
		}
		
		public function search_posts_where( $where ) {

			if( is_admin() ) {
				$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

				//search text
				$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';
				$search_text = trim( $search_text );
				$esc_search_text = esc_sql($search_text);
					
				if ( isset( $search_text ) && $search_text!='' ) {
					if ( $search_text!='' && $this->the_plugin->utf8->strlen($search_text)<200 )
						$where .= " AND ( post_title regexp '" . $esc_search_text . "' OR post_content regexp '" . $esc_search_text . "' ) ";
				}
			}
			return $where;
		}

		private function getAvailablePostStatus()
		{
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			//post type
			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';
			$post_type = trim( $post_type );
			$qClause = '';
			if ( $post_type!='' && $post_type!='all' )
				$qClause .= " AND post_type = '" . ( esc_sql($post_type) ) . "' ";
			else
				$qClause .= " AND post_type IN ( " . implode( ',', array_map( array($this->the_plugin, 'prepareForInList'), array_keys($this->get_list_postTypes()) ) ) . " ) ";

			//search text
			$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';
			$search_text = trim( $search_text );
			if ( $search_text!='' && $this->the_plugin->utf8->strlen($search_text)<200 )
				$qClause .= " AND ( post_title regexp '" . ( esc_sql($search_text) ) . "' OR post_content regexp '" . ( esc_sql($search_text) ) . "' ) ";
			
			$sql = "SELECT count(id) as nbRow, post_status, post_type FROM " . ( $this->the_plugin->db->prefix ) . "posts WHERE 1 = 1 ".$qClause." group by post_status";
			$sql = preg_replace('~[\r\n]+~', "", $sql);
			//$sql = $wpdb->prepare( $sql );

			return $this->the_plugin->db->get_results( $sql, ARRAY_A );
		}

		private function get_list_postTypes()
		{
			// overwrite wrong post-type value
			if( !isset($this->opt['list_post_types']) ) $this->opt['list_post_types'] = 'all';

			// custom array case
			if( is_array($this->opt['list_post_types']) && count($this->opt['list_post_types']) > 0 ) return $this->opt['list_post_types'];

			// all case
			$_builtin = get_post_types(array('show_ui' => TRUE, 'show_in_nav_menus' => TRUE, '_builtin' => TRUE), 'objects');
			if ( !is_array($_builtin) || count($_builtin)<0 )
				$_builtin = array();

			$_notBuiltin = get_post_types(array('show_ui' => TRUE, 'show_in_nav_menus' => TRUE, '_builtin' => FALSE), 'objects');
			if ( !is_array($_notBuiltin) || count($_notBuiltin)<0 )
				$_notBuiltin = array();
				
			$exclude = array();
			$ret = array_merge($_builtin, $_notBuiltin);
			if (!empty($exclude)) foreach ( $exclude as $exc) if ( isset($ret["$exc"]) ) unset($ret["$exc"]);
  
			return $ret;
		}
		
		public function post_statuses_filter()
		{
			$html = array();

			$availablePostStatus = $this->getAvailablePostStatus();
			
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			$curr_post_status = isset($ses['post_status']) && trim($ses['post_status']) != "" ? $ses['post_status'] : 'all';

			if( $this->opt['post_statuses'] == 'all' ){
				$postStatuses = array(
				    'all'   	=> __('All', $this->the_plugin->localizationName),
				    'publish'   => __('Published', $this->the_plugin->localizationName),
				    'future'    => __('Scheduled', $this->the_plugin->localizationName),
				    'private'   => __('Private', $this->the_plugin->localizationName),
				    'pending'   => __('Pending Review', $this->the_plugin->localizationName)
				);
			}
			else{
				die('invalid value of <i>post_statuses</i>. Only implemented value is: <i>all</i>!');
			}


			$html[] = 		'<ul class="subsubsub psp-post_status-list">';

			$cc = 0;
			// add into _postStatus array only if have equivalent into query results
			$_postStatus = array();
			$totals = 0;
			foreach ($availablePostStatus as $key => $value){

				if( !in_array($value["post_status"], array("auto-draft", "inherit")) ) {
					if( in_array($value['post_status'], array_keys($postStatuses))){
						 
						$_postStatus[$value['post_status']] = $value['nbRow'];
						$totals = $totals + $value['nbRow'];
					}
				}
			}

			foreach ($postStatuses as $key => $value){
				$cc++;

				if( $key == 'all' || in_array($key, array_keys($_postStatus)) ){
					$html[] = 		'<li class="ocs_post_status">';
					$html[] = 			'<a href="#post_status=' . ( $key ) . '" class="' . ( $curr_post_status == $key ? 'current' : '' ) . '" data-post_status="' . ( $key ) . '">';
					$html[] = 				$value . ' <span class="count">(' . ( ( $key == 'all' ? $totals : $_postStatus[$key] ) ) . ')</span>';
					$html[] = 			'</a>' . ( count($_postStatus) > ($cc) ? ' |' : '');
					$html[] = 		'</li>';
				}
			}

			$html[] = 		'</ul>';

			return implode("\n", $html);
		}
		
		
		/**
		 * Media files
		 *
		 */
		
		private function post_media_getQuery( $key='' ) {
			
				$nb_args = array();
				switch ($key) {

					case 'smushed':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> array('reduced', 'nosave'),
									'type'    	=> 'CHAR',
									'compare' 	=> 'IN'
								)
							)
						));
						break;

					case 'not_processed':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> '',
									'compare' 	=> 'NOT EXISTS'
								)
							)
						));
						break;

					case 'with_errors':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> 'invalid',
									'type'    	=> 'CHAR',
									'compare' 	=> '='
								)
							)
						));
						break;

					default:
						break;
				}
				return $nb_args;
		}
		
		private function post_media_statusDetails()
		{
			$ret = array();

			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			//post type
			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';
			$post_type = trim( $post_type );


			$args = array_merge($this->args, array(
				'post_type'			=> 'attachment',
				'post_status'		=> 'inherit',
				'post_mime_type'	=> array('image/jpeg', 'image/jpg', 'image/png')
			));
			
			// remove empty array
			$args = array_filter( $args );

			//hook retrieve posts where clause
			add_filter( 'posts_where' , array( &$this, 'search_posts_where' ) );

			$args = array_merge($args, array(
				'suppress_filters' => false
			));

			// get all post count
			$nb_args = $args;
			$nb_args['posts_per_page'] = '-1';
			$nb_args['fields'] = 'ids';
			
			$postStatuses = $this->post_media_status();

			foreach ($postStatuses as $key => $value){

				if ( $key == 'all' ) continue 1;
				
				$nb_args = array_merge( $nb_args, $this->post_media_getQuery( $key ) );

				$ret["$key"] = array(
					'post_status'	=> $key,
					'nbRow'			=> (int) count( get_posts( $nb_args ) )
				);
			}
			return $ret;
		}
		
		private function post_media_status() {

			$postStatuses = array(
				'all'   			=> __('All', $this->the_plugin->localizationName),
				'smushed'   		=> __('Smushed', $this->the_plugin->localizationName),
				'not_processed'   	=> __('Not processed', $this->the_plugin->localizationName),
				'with_errors'   	=> __('With errors', $this->the_plugin->localizationName)
			);
			return $postStatuses;
		}
		
		public function post_media_filter( $return='output' )
		{
			$html = array();

			$availablePostStatus = $this->post_media_statusDetails();
			
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			$curr_post_status = isset($ses['post_status']) && trim($ses['post_status']) != "" ? $ses['post_status'] : 'all';

			if( $this->opt['post_statuses'] == 'all' ){
				$postStatuses = $this->post_media_status();
			}
			else{
				die('invalid value of <i>post_statuses</i>. Only implemented value is: <i>all</i>!');
			}


			$html[] = 		'<ul class="subsubsub psp-post_status-list">';

			$cc = 0;
			// add into _postStatus array only if have equivalent into query results
			$_postStatus = array();
			$totals = 0;
			foreach ($availablePostStatus as $key => $value){

				if( in_array($value['post_status'], array_keys($postStatuses))){
					$_postStatus[$value['post_status']] = $value['nbRow'];
					$totals = $totals + $value['nbRow'];
				}
			}

			foreach ($postStatuses as $key => $value){
				$cc++;

				if ( $return == 'array' && $key == 'all' ) unset($postStatuses[$key]);
					
				if( $key == 'all' || in_array($key, array_keys($_postStatus)) ){
					
					$html[] = 		'<li class="ocs_post_status">';
					$html[] = 			'<a href="#post_status=' . ( $key ) . '" class="' . ( $curr_post_status == $key ? 'current' : '' ) . '" data-post_status="' . ( $key ) . '">';
					$html[] = 				$value . ' <span class="count">(' . ( ( $key == 'all' ? $totals : $_postStatus[$key] ) ) . ')</span>';
					$html[] = 			'</a>' . ( count($_postStatus) > ($cc) ? ' |' : '');
					$html[] = 		'</li>';
				} else {
					if ( $return == 'array' ) unset($postStatuses[$key]);
				}
			}

			$html[] = 		'</ul>';
			
			if ( $return == 'array' ) return $postStatuses;

			return implode("\n", $html);
		}

		private function get_pagination()
		{
			$html = array();

			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];
			$posts_per_page = ( isset($ses['posts_per_page']) ? $ses['posts_per_page'] : $this->opt['items_per_page'] );
			$paged = ( isset($ses['paged']) ? $ses['paged'] : 1 );
			$total_pages = ceil( $this->items_nr / $posts_per_page );

			if( $this->opt['show_pagination'] ){
				$html[] = 	'<div class="psp-list-table-right-col">';


				$html[] = 		'<div class="psp-box-show-per-pages">';
				$html[] = 			'<select name="psp-post-per-page" id="psp-post-per-page" class="psp-post-per-page">';


				$html[] = 				'<option val="1" ' . ( $posts_per_page == 1 ? 'selected' : '' ). '>1</option>';
				foreach( range(5, 50, 5) as $nr => $val ){
					$html[] = 			'<option val="' . ( $val ) . '" ' . ( $posts_per_page == $val ? 'selected' : '' ). '>' . ( $val ) . '</option>';
				}
				foreach( range(100, 500, 100) as $nr => $val ){
					$html[] = 			'<option val="' . ( $val ) . '" ' . ( $posts_per_page == $val ? 'selected' : '' ). '>' . ( $val ) . '</option>';
				}

				$html[] = 				'<option value="all" ' . ($posts_per_page == -1 ? 'selected' : '') . '>';
				$html[] =				__('Show All', $this->the_plugin->localizationName);
				$html[] = 				'</option>';
				$html[] =			'</select>';
				$html[] = 			'<label for="psp-post-per-page" style="width:57px">' . __('per pages', $this->the_plugin->localizationName) . '</label>';
				$html[] = 		'</div>';

				$html[] = 		'<div class="psp-list-table-pagination tablenav">';

				$html[] = 			'<div class="tablenav-pages">';
				$html[] = 				'<span class="displaying-num">' . ( $this->items_nr ) . ' ' . __('items', $this->the_plugin->localizationName) . '</span>';
				if( $total_pages > 1 ){

				$html[] = 				'<span class="pagination-links"><a class="first-page ' . ( $paged == 1 ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the first page', $this->the_plugin->localizationName) . '" href="#paged=1">«</a>';
					$html[] = 				'<a class="prev-page ' . ( $paged == 1 ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the previous page', $this->the_plugin->localizationName) . '" href="#paged=' . ( $paged > 2 ? ($paged - 1) : 1 ) . '">‹</a>';
					$html[] = 				'<span class="paging-input"><input class="current-page" title="' . __('Current page', $this->the_plugin->localizationName) . '" type="text" name="paged" value="' . ( $paged ) . '" size="2" style="width: 45px;"> ' . __('of', $this->the_plugin->localizationName) . ' <span class="total-pages">' . ( ceil( $this->items_nr / $this->args['posts_per_page'] ) ) . '</span></span>';
					$html[] = 				'<a class="next-page ' . ( ( $paged == ($total_pages)) ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the next page', $this->the_plugin->localizationName) . '" href="#paged=' . ( $paged < $total_pages ? $paged + 1 : $total_pages ) . '">›</a>';
					$html[] = 				'<a class="last-page ' . ( $paged ==  ($total_pages - 1) ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the last page', $this->the_plugin->localizationName	) . '" href="#paged=' . ( $total_pages ) . '">»</a></span>';
				}
				$html[] = 			'</div>';
				$html[] = 		'</div>';

				$html[] = 	'</div>';
			}

			return implode("\n", $html);
		}

		public function print_header()
		{
			$html = array();
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';

			$html[] = '<div id="psp-list-table-header">';

			if( trim($this->opt["custom_table"]) == ""){
				$html[] = '<div class="psp-list-table-left-col">';

                if ( !in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) { // if NOT smushit

				$html[] = 		'<select name="psp-filter-post_type" class="psp-filter-post_type">';
				$html[] = 			'<option value="all" >';
				$html[] =			__('Show All', $this->the_plugin->localizationName);
				$html[] = 			'</option>';

				if ( in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) { // smushit
					$filterArr = $this->post_media_filter('array');
				} else {
					$filterArr = $this->get_list_postTypes();
				}
				
				foreach ( $filterArr as $name => $postType ){

					$html[] = 		'<option ' . ( $name == $post_type ? 'selected' : '' ) . ' value="' . ( $this->the_plugin->escape($name) ) . '">';
					$html[] = 			( is_object($postType) ? ucfirst($this->the_plugin->escape($name)) : ucfirst($name) );
					$html[] = 		'</option>';
				}
				$html[] = 		'</select>';

				} // end if NOT smushit!


				if( $this->opt['show_statuses_filter'] ){
					
                    if ( in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) { // smushit
						$html[] = $this->post_media_filter();
					} else {
						$html[] = $this->post_statuses_filter();
					}
				}
				$html[] = 		'</div>';

				if( $this->opt['search_box'] ){

					$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';

					$html[] = 	'<div class="psp-list-table-right-col">';
					$html[] = 		'<div class="psp-list-table-search-box">';
					$html[] = 			'<input type="text" name="psp-search-text" id="psp-search-text" value="'.($search_text).'" class="'.($search_text!='' ? 'search-highlight' : '').'" >';
					$html[] = 			'<input type="button" name="psp-search-btn" id="psp-search-btn" class="button" value="' . __('Search Posts', $this->the_plugin->localizationName) . '">';
					$html[] = 		'</div>';
					$html[] = 	'</div>';
				}

				/*if( $this->opt['show_category_filter'] && 3==4 ){
					$html[] = '<div class="psp-list-table-left-col" >';
					$html[] = 	'<select name="psp-filter-post_type" class="psp-filter-post_type">';
					$html[] = 		'<option value="all" >';
					$html[] =		__('Show All', $this->the_plugin->localizationName);
					$html[] = 		'</option>';
					$html[] =	'</select>';
					$html[] = '</div>';
				}*/
			}else{
				$show_notice = false;
				/*if ( isset($this->opt['notices']['default']) ) {
					if ( isset($this->opt['notices']['default_clause'])
						&& $this->opt['notices']['default_clause']=='empty'
						&& count($this->items) <= 0 ) {
						$show_notice = true;
						$html[] = '<div class="psp-list-table-left-col">' . $this->opt['notices']['default'] . '</div>';
					}
				}*/
				
				if ( !$show_notice )
					$html[] = '<div class="psp-list-table-left-col">&nbsp;</div>';
			}
			
			// buttons
			if ( $this->opt["show_header_buttons"] ) {
			if( trim($this->opt["custom_table"]) == "" || $this->opt["custom_table_force_action"]){

				if( isset($this->opt['mass_actions']) && ($this->opt['mass_actions'] === false) ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;</div>';
				}elseif( isset($this->opt['mass_actions']) && count($this->opt['mass_actions']) > 0 ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';

					foreach ($this->opt['mass_actions'] as $key => $value){
						$html[] = 	'<input type="button" value="' . ( $value['value'] ) . '" id="psp-' . ( $value['action'] ) . '" class="psp-button ' . ( $value['color'] ) . '">';
					}
					$html[] = '</div>';
				}else{
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
					$html[] = 	'<input type="button" value="' . __('Auto detect focus keyword for All', $this->the_plugin->localizationName) . '" id="psp-all-auto-detect-kw" class="psp-button blue">';
					$html[] = 	'<input type="button" value="' . __('Optimize All', $this->the_plugin->localizationName) . '" id="psp-all-optimize" class="psp-button blue">';
					$html[] = '</div>';
				}
				
				if( $this->opt['id'] == 'pspPageOptimization' ){
					$html[] = '<div id="psp-inline-editpost-boxtpl" style="display: none;">';
					$html[] = $this->the_plugin->edit_post_inline_boxtpl();
					$html[] = '</div>';
				}
			}
			else{
				$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
				$html[] = '</div>';
			}
			}

			$html[] = $this->get_pagination();

			$html[] = '</div>';

            echo implode("\n", $html);

			return $this;
		}

		public function print_main_table( $items )
		{
			$html = array();

			$html[] = '<div id="psp-list-table-posts">';
			$html[] = 	'<table class="psp-table" style="border: none;border-bottom: 1px solid #dadada;">';
			$html[] = 		'<thead>';
			$html[] = 			'<tr>';
  
			foreach ($this->opt['columns'] as $key => $value){
				if( $value['th'] == 'checkbox' ){
					$html[] = '<th class="checkbox-column" width="20"><input type="checkbox" id="psp-item-check-all" checked></th>';
				}
				else{
					$html[] = '<th' . ( isset($value['width']) && (int)$value['width'] > 0 ? ' width="' . ( $value['width'] ) . '"' : '' ) . '' . ( isset($value['align']) && $value['align'] != "" ? ' align="' . ( $value['align'] ) . '"' : '' ) . '>' . ( $value['th'] ) . '</th>';
				}
			}

			$html[] = 			'</tr>';
			$html[] = 		'</thead>';

			$html[] = 		'<tbody>';
			
			if( $this->opt['id'] == 'pspPageOptimization' ){
				//use to generate meta keywords, and description for your requested item
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
				$seo = pspSeoCheck::getInstance();
			}
			
			$show_notice = false;
			if ( isset($this->opt['notices']['default']) ) {
				if ( isset($this->opt['notices']['default_clause'])
					&& $this->opt['notices']['default_clause']=='empty'
					&& count($this->items) <= 0 ) {
					$show_notice = true;
					$html[] = '<tr><td colspan=15 style="height: 37px; text-align: left;">' . $this->opt['notices']['default'] . '</td></tr>';
				}
			}
 
			foreach ($this->items as $post){
				if( isset($post->ID) ){
					$item_data = array(
						'score' 	=> get_post_meta( $post->ID, 'psp_score', true )
					);
				}

				$html[] = 			'<tr data-itemid="' . ( ( isset($post->ID) ? $post->ID : $post['id'] ) ) . '">';
				foreach ($this->opt['columns'] as $key => $value){

					$html[] = '<td style="'
						. ( isset($value['align']) && $value['align'] != "" ? 'text-align:' . ( $value['align'] ) . ';' : '' ) . ''
						. ( isset($value['css']) && count($value['css']) > 0 ? $this->print_css_as_style($value['css']) : '' ) . '"
						class="' . ( isset($value['class']) ? $value['class'] : '' ) . '"
						>';

					if( $value['td'] == 'checkbox' ){
						$html[] = '<input type="checkbox" class="psp-item-checkbox" name="psp-item-checkbox-' . ( isset($post->ID) ? $post->ID : $post['id'] ) . '" checked>';
					}
					elseif( $value['td'] == '%score%' ){
						$score = (float)$item_data['score'];
						$size_class = 'size_';
						if ( $score >= 20 && $score < 40 ){
							$size_class .= '20_40';
						}elseif ( $score >= 40 && $score < 60 ){
							$size_class .= '40_60';
						}
						elseif ( $score >= 60 && $score < 80 ){
							$size_class .= '60_80';
						}elseif ( $score >= 80 && $score <= 100 ){
							$size_class .= '80_100';
						}else{
							$size_class .= '0_20';
						}

						if( !isset($item_data['score']) || trim($item_data['score']) == "" ){
							$item_data['score'] = 0;
						}
						$html[] = '<div class="psp-progress">';
						$html[] = 	'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
						$html[] = 	'<div class="psp-progress-score">' . ( $item_data['score'] ) . '%</div>';
						$html[] = '</div>';
					}
					elseif( $value['td'] == '%focus_keyword%' ){
						$focus_kw = get_post_meta( $post->ID, 'psp_kw', true );
						$html[] = '<div class="psp-focus-kw-box">';
						$html[] = 	'<input type="text" class="psp-text-field-kw" id="psp-focus-kw-' . ( $post->ID ) . '" value="' . ( $focus_kw ) . '" />';
						$html[] = 	'<input type="button" class="psp-auto-detect-kw-btn psp-button gray" value="' . __('Auto detect', $this->the_plugin->localizationName) . '" />';
						/*$html[] = 	'<a class="psp-button green psp-suggest-kw-btn" href="#">
                                            <img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/light.png">
                                        	Suggest
                                        </a>';*/
						$html[] = '</div>';
					}
					elseif( $value['td'] == '%seo_report%' ){
						$html[] = '<a class="psp-button green psp-seo-report-btn" href="#" data-itemid="' . ( $post->ID ) . '">
                                        <img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/light.png">
                                    	' . __('SEO Report', $this->the_plugin->localizationName) . '
                                    </a>';
					}
					elseif( strtolower($value['td']) == '%id%' ){
						$html[] = is_object($post) ? (isset($post->ID) ? $post->ID : $post->id) : (isset($post['ID']) ? $post['ID'] : $post['id']);
					}
					elseif( $value['td'] == '%title%' ){
						$html[] = '<input type="hidden" id="psp-item-title-' . ( $post->ID ) . '" value="' . ( str_replace('"', "'", $post->post_title) ) . '" />';
						$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">';
						
						if ( $post->post_status == 'inherit' && $post->post_type == 'attachment' ) { // media image file

							$html[] = 	( $post->post_title . ( isset($post->post_mime_type) && preg_match('/^image\//i', $post->post_mime_type) > 0 ? ' <span class="item-state">- ' . strtoupper(str_replace('image/', '', $post->post_mime_type)) : '</span>') );
							$html[] = '</a>';
							$html[] = '
							<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post->ID ) . '">
								<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">Edit</a>
								 | <a href="' . ( wp_get_attachment_url( $post->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
							</span>';
						} else {
						
							$html[] = 	( $post->post_title . ( $post->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($post->post_status) : '</span>') );
							$html[] = '</a>';
						}
					}
					elseif( $value['td'] == '%title_and_actions%' ){
						$html[] = '<input type="hidden" id="psp-item-title-' . ( $post->ID ) . '" value="' . ( str_replace('"', "'", $post->post_title) ) . '" />';
						$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">';
						$html[] = 	( $post->post_title . ( $post->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($post->post_status) : '</span>') );
						$html[] = '</a>';
						
						$__row_actions = $this->the_plugin->edit_post_inline_data( $post->ID, $seo );
						$html[] = '
						<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post->ID ) . '">
							<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">Edit</a>
							 | <a href="#" class="editinline" title="' . __('Edit this item inline', $this->the_plugin->localizationName) . '">' . __('Quick Edit', $this->the_plugin->localizationName) . '</a>
							 | <a href="' . ( get_permalink( $post->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
						</span>';
						$html[] = '
						<div id="psp-inline-row-data-' . ( $post->ID ) . '" class="hide" style="display: none;">
							'.$__row_actions.'
						</div>
						';
					}
					elseif( $value['td'] == '%custom_title%' ){
						$html[] = '<i>' . ( $post['title'] ) . '</i>';
					}
					elseif( $value['td'] == '%button%' ){
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $value['option']['value'] ) . '" class="psp-button ' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '">';
					}
					elseif( $value['td'] == '%button_publish%' ){
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $post['publish']=='Y' ? $value['option']['value'] : $value['option']['value_change'] ) . '" class="psp-button ' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '">';
					}
					elseif( $value['td'] == '%button_html5data%' ){
						$__html5data = array();
						foreach ($value['html5_data'] as $ttk=>$ttv) {
							$__html5data[] = "data-" . $ttk . "=\"" . $ttv . "\"";
						}
						$__html5data = ' ' . implode(' ', $__html5data) . ' ';
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $value['option']['value'] ) . '" class="psp-button ' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '"
						' . $__html5data . '
						>';
					}
					elseif( $value['td'] == '%date%' ){
						$html[] = '<i>' . ( $post->post_date ) . '</i>';
					}
					else if( $value['td'] == '%created%' ){
						$html[] = '<i>' . ( $post['created'] ) . '</i>';
					}
					elseif( $value['td'] == '%hits%' ){
						$html[] = '<i>' . ( $post['hits'] ) . '</i>';
					}
					elseif( $value['td'] == '%url%' ){
						$html[] = '<i>' . ( $post['url'] ) . '</i>';
					}
					elseif( $value['td'] == '%bad_url%' ){
						$html[] = '<i>' . ( $post['url'] ) . '</i>';
					}
					elseif( $value['td'] == '%phrase%' ){
						$html[] = '<i>' . ( $post['phrase'] ) . '</i>';
					}
					elseif( $value['td'] == '%referrers%' ){
						$html[] = (trim($post['referrers']) != "" ? '<a href="#referrers" class="psp-button gray psp-btn-referrers-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
					}
					elseif( $value['td'] == '%user_agents%' ){
						$html[] = (trim($post['user_agents']) != "" ? '<a href="#user_agents" class="psp-button gray psp-btn-user_agents-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
					}
					elseif( $value['td'] == '%last_date%' ){
						$html[] = '<i>' . ( $post['data'] ) . '</i>';
					}
					
                    if ( in_array($this->opt['id'], array('pspSmushit', 'pspTinyCompress')) ) {
						
						$id = intval( $post->ID );

						if( $value['td'] == '%thumbnail%' ){

							$attachment_img_thumb = wp_get_attachment_image( $id, 'thumbnail' );
							$patterns = array(
								'/<img(.*?)width="(.*?)"(.*?)>/',
								'/<img(.*?)height="(.*?)"(.*?)>/'
							);
							$replacements = array(
								'<img\1width="60"\3>',
								'<img\1height="60"\3>'
							);
							$html[] = preg_replace( $patterns, $replacements, $attachment_img_thumb );
						}
						else if( $value['td'] == '%smushit_status%' ){
							
							//$html[] = '<div class="psp-message">';
							//$html[] = 	'<span class="psp-smushit-loading"></span>';
							
							// retrieve the existing value(s) for this meta field. This returns an array
							$meta_new = wp_get_attachment_metadata( $id );

							if ( isset($meta_new['psp_smushit']) && !empty($meta_new['psp_smushit']) ) {
			
								$msg = (array) $this->the_plugin->smushit_show_sizes_msg_details( $meta_new ); $__msg = array();
								if ( isset($meta_new['psp_smushit_errors']) && ( (int) $meta_new['psp_smushit_errors'] ) > 0 ) {
									$status = 'invalid';
									$msg_cssClass = 'psp-error';
									$__msg = array( __('errors occured during smushit operation!', $this->the_plugin->localizationName) );
								} else {
									$status = 'valid';
									$msg_cssClass = 'psp-success';
								}
								$msg = implode('<br />', array_merge($__msg, $msg));
								
								$html[] = '<div id="' . ('psp-smushit-resp-'.$id) . '" class="psp-message ' . $msg_cssClass . '">' . $msg . '</div><br />';
							} else {
								
								$html[] = '<div id="' . ('psp-smushit-resp-'.$id) . '" class="psp-message psp-info">' . __( 'not processed!', $this->the_plugin->localizationName ) . '</div><br />';
							}
							//$html[] = '</div>';
				
						}
					}
					
					if( $this->opt['id'] == 'pspPageSpeed' ){
						if( $value['td'] == '%mobile_score%' ){
							$mobile = get_post_meta( $post->ID, 'psp_mobile_pagespeed', true ); 
							
							if( isset($mobile['score']) ){
								 
								$score = (int) $mobile['score'];
								$size_class = 'size_';
								if ( $score >= 20 && $score < 40 ){
									$size_class .= '20_40';
								}elseif ( $score >= 40 && $score < 60 ){
									$size_class .= '40_60';
								}
								elseif ( $score >= 60 && $score < 80 ){
									$size_class .= '60_80';
								}elseif ( $score >= 80 && $score <= 100 ){
									$size_class .= '80_100';
								}else{
									$size_class .= '0_20';
								}
		
								$html[] = '<div class="psp-progress" style="margin-right:4px">';
								$html[] = 	'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
								$html[] = '</div>';
							}else{
								$html[] = '<i>Never Checked</i>';
							}
						}
						
						if( $value['td'] == '%desktop_score%' ){
							$desktop = get_post_meta( $post->ID, 'psp_desktop_pagespeed', true ); 
							
							if( isset($desktop['score']) ){
								 
								$score = (int) $desktop['score'];
								$size_class = 'size_';
								if ( $score >= 20 && $score < 40 ){
									$size_class .= '20_40';
								}elseif ( $score >= 40 && $score < 60 ){
									$size_class .= '40_60';
								}
								elseif ( $score >= 60 && $score < 80 ){
									$size_class .= '60_80';
								}elseif ( $score >= 80 && $score <= 100 ){
									$size_class .= '80_100';
								}else{
									$size_class .= '0_20';
								}
		
								$html[] = '<div class="psp-progress" style="margin-right:4px">';
								$html[] = 	'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
								$html[] = '</div>';
							}else{
								$html[] = '<i>Never Checked</i>';
							}
						}
					}
					
					if( $this->opt['id'] == 'pspProxyLists' ){
						if( $value['td'] == '%ip_address%' ){
							$html[] = '<strong>' . ( $post['ip'] ) . '</strong>';
						}
						else if( $value['td'] == '%port%' ){
							$html[] = '<strong>' . ( $post['port'] ) . '</strong>';
						}
						elseif( $value['td'] == '%speed%' ){
							$score = (float)$post['speed'];
							$size_class = 'size_';
							if ( $score >= 20 && $score < 40 ){
								$size_class .= '20_40';
							}elseif ( $score >= 40 && $score < 60 ){
								$size_class .= '40_60';
							}
							elseif ( $score >= 60 && $score < 80 ){
								$size_class .= '60_80';
							}elseif ( $score >= 80 && $score <= 100 ){
								$size_class .= '80_100';
							}else{
								$size_class .= '0_20';
							}
	
							$html[] = '<div class="psp-progress" style="margin-right:4px">';
							$html[] = 	'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
							$html[] = '</div>';
						}
						/*elseif( $value['td'] == '%connection_time%' ){
							$score = (float)$post['connection_time'];
							$size_class = 'size_';
							if ( $score >= 20 && $score < 40 ){
								$size_class .= '20_40';
							}elseif ( $score >= 40 && $score < 60 ){
								$size_class .= '40_60';
							}
							elseif ( $score >= 60 && $score < 80 ){
								$size_class .= '60_80';
							}elseif ( $score >= 80 && $score <= 100 ){
								$size_class .= '80_100';
							}else{
								$size_class .= '0_20';
							}
	
							$html[] = '<div class="psp-progress" style="margin-right:4px">';
							$html[] = 	'<div class="psp-progress-bar ' . ( $size_class ) . '" style="width:' . ( $score ) . '%"></div>';
							$html[] = '</div>';
						}*/
					}
					if( $this->opt['id'] == 'pspLinkBuilder' ){
						if( $value['td'] == '%builder_phrase%' ){
							$html[] = '<input type="text" value="' . ( $post['phrase'] ) . '" readonly />';
						}
						else if( $value['td'] == '%builder_url%' ){
							$html[] = '<input type="text" value="' . ( $post['url'] ) . '" readonly />';
						}
						else if( $value['td'] == '%builder_rel%' ){
							$html[] = '<i>' . ( $post['rel'] ) . '</i>';
						}
						else if( $value['td'] == '%builder_target%' ){
							$html[] = '<i>' . ( $post['target'] ) . '</i>';
						}
						else if( $value['td'] == '%url_attributes%' ){
							$html[] = (1==1 ? '<a href="#url_attributes" class="psp-button gray psp-btn-url-attributes-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
						}
					}
					
					if( $this->opt['id'] == 'pspLinkRedirect' ){
						if( $value['td'] == '%linkred_url%' ){
							$html[] = '<input type="text" value="' . ( $post['url'] ) . '" readonly />';
						}
						else if( $value['td'] == '%linkred_url_redirect%' ){
							$html[] = '<input type="text" value="' . ( $post['url_redirect'] ) . '" readonly />';
						}
					}
					
					if( $this->opt['id'] == 'pspSocialStats' ){
						$page_permalink = get_permalink( $post->ID );
						$social_data = $this->get_page_social_stats( $post->ID, $page_permalink );
						
						$dashboard_module_url = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/dashboard/';
						
						if( $value['td'] == '%ss_facebook%' ){
							$html[] = '<div class="psp-social-status" style="color: #3c5b9b">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/facebook-icon.png" class="psp-lists-icon">';
							$html[] = 	'<span>' . ( isset($social_data['facebook']['share_count']) ? number_format($social_data['facebook']['share_count'], 0) : '&ndash;' ) . '</span>';
							$html[] = 	'<label>' . ( __("shares", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = '</div>';
							/*
							$html[] = '<div class="psp-social-status" style="color: #3c5b9b">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/facebook-like-icon.png" class="psp-lists-icon">';
							$html[] = 	'<span>' . ( isset($social_data['facebook']['like_count']) ? number_format($social_data['facebook']['like_count'], 0) : '&ndash;' ) . '</span>';
							$html[] = 	'<label>' . ( __("likes", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = '</div>';
							
							$html[] = '<div class="psp-social-status" style="color: #3c5b9b">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/facebook-comments-icon.png" class="psp-lists-icon">';
							$html[] = 	'<span>' . ( isset($social_data['facebook']['comment_count']) ? number_format($social_data['facebook']['comment_count'], 0) : '&ndash;' ) . '</span>';
							$html[] = 	'<label>' . ( __("comments", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = '</div>';
							
							$html[] = '<div class="psp-social-status" style="color: #3c5b9b">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/facebook-icon.png" class="psp-lists-icon">';
							$html[] = 	'<span>' . ( isset($social_data['facebook']['click_count']) ? number_format($social_data['facebook']['click_count'], 0) : '&ndash;' ) . '</span>';
							$html[] = 	'<label>' . ( __("clicks", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = '</div>';*/
							
						}
						elseif( $value['td'] == '%ss_stumbleupon%' ){
							$html[] = '<div class="psp-social-status" style="color: #3fbd46">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/stumbleupon-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("views", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['stumbleupon']) ? number_format($social_data['stumbleupon'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						}
						elseif( $value['td'] == '%ss_twitter%' ){
							$html[] = '<div class="psp-social-status" style="color: #00aced">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/twitter-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("retweets", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['twitter']) ? number_format($social_data['twitter'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						} 
						elseif( $value['td'] == '%ss_google%' ){
							$html[] = '<div class="psp-social-status" style="color: #d23e2b">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/google-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("share", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['google']) ? number_format($social_data['google'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						}  
						elseif( $value['td'] == '%ss_digg%' ){
							$html[] = '<div class="psp-social-status" style="color: #2c2c2c">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/delicious-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("posts", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['delicious']) ? number_format($social_data['delicious'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						} 
						elseif( $value['td'] == '%ss_pinterest%' ){
							$html[] = '<div class="psp-social-status" style="color: #ca4638">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/pinterest-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("pins", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['pinterest']) ? number_format($social_data['pinterest'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						} 
						elseif( $value['td'] == '%ss_linkedin%' ){
							$html[] = '<div class="psp-social-status" style="color: #007ab9">';
							$html[] = 	'<img src="' . ( $dashboard_module_url ) . 'assets/stats/linkedin-icon.png" class="psp-lists-icon">';
							$html[] = 	'<label>' . ( __("backlinks", $this->the_plugin->localizationName) ) . '</label>';
							$html[] = 	'<span>' . ( isset($social_data['linkedin']) ? number_format($social_data['linkedin'], 0) : '&ndash;' ) . '</span>';
							$html[] = '</div>';
						}
					}

					if( $this->opt['id'] == 'pspWebDirectories' ){
						if( $value['td'] == '%directory_name%' ){
							$html[] = '<a href="' . ( $post['submit_url'] ) . '" target="_blank">' . ( $post['directory_name'] ) . '</a>';
						}
						elseif( $value['td'] == '%pagerank%' || $value['td'] == '%alexa%' ){
							$html[] = '<code>' . ( $post[$key] ) . '</code>';
						}
						elseif( $value['td'] == '%submit_btn%' ){
							$html[] = '<a href="' . ( $post['submit_url'] ) . '" target="_blank" class="psp-button blue psp-btn-submit-website" data-itemid="' . ( $post['id'] ) . '">' . ( __('Submit website', $this->the_plugin->localizationName) ) . '</a>';
						}

						elseif( $value['td'] == '%submit_status%' ){
							// never submited / $post['status'] = 2;
							$html_status = '<div class="psp-message" style="padding: 5px;">' . ( __('Never submit', $this->the_plugin->localizationName) ) . '</div>';
							if( $post['status'] == 2 ){
								$html_status = '<div class="psp-message psp-info" style="padding: 5px;background-image: none;">' . ( __('Submit in progress', $this->the_plugin->localizationName) ) . '</div>';
							}
							elseif( $post['status'] == 3 ){
								$html_status = '<div class="psp-message psp-error" style="padding: 5px;background-image: none;">' . ( __('Error on submit', $this->the_plugin->localizationName) ) . '</div>';
							}
							elseif( $post['status'] == 1 ){
								$html_status = '<div class="psp-message psp-success" style="padding: 5px;background-image: none;">' . ( __('Submit successfully', $this->the_plugin->localizationName) ) . '</div>';
							}
							
							$html[] = $html_status;
						}
					}
						
					if( $this->opt['id'] == 'pspPageHTMLValidation' ){

						// get html verify data
						$html_verify_details = get_post_meta( $post->ID, 'psp_w3c_validation', true );
						if( $value['td'] == '%nr_of_errors%' ){
							$html[] = '<i class="' . ( $key ) . '">' . ( isset($html_verify_details['nr_of_errors']) ? $html_verify_details['nr_of_errors'] : $value['def'] ) . '</i>';
						}
						elseif( $value['td'] == '%nr_of_warning%' ){
							$html[] = '<i class="' . ( $key ) . '">' . ( isset($html_verify_details['nr_of_warning']) ? $html_verify_details['nr_of_warning'] : $value['def'] ) . '</i>';
						}
						elseif( $value['td'] == '%status%' ){
							$html[] = '<strong class="' . ( $key ) . '" style="' . ( isset($html_verify_details['status']) && $html_verify_details['status'] == 'Invalid' ? 'color: red;' : 'color: green' ) . '">' . ( isset($html_verify_details['status']) ? $html_verify_details['status'] : $value['def'] ) . '</strong>';
						}
						elseif( $value['td'] == '%last_check_at%' ){
							$html[] = '<i class="' . ( $key ) . '">' . ( isset($html_verify_details['last_check_at']) ? $html_verify_details['last_check_at'] : $value['def'] ) . '</i>';
						}
						elseif( $value['td'] == '%view_full_report%' ){
							$html[] = '<a target="_blank" href="' . ( 'http://validator.w3.org/check?uri=' . get_permalink( $post->ID ) ) . '" class="psp-button gray">' . ( __('View report', $this->the_plugin->localizationName) ) . '</a>';
						}
					}
					
					if( $this->opt['id'] == 'pspSERPKeywords' ){
						$rank_data = $post;
						
						if( $value['td'] == '%serp_focus_keyword%' ){
							$html[] = '<input type="text" value="' . ( $post['focus_keyword'] ) . '" />';
						}
						else if( $value['td'] == '%serp_url%' ){
							$html[] = '<input type="text" value="' . ( $post['url'] ) . '" />';
						}
						elseif( $value['td'] == '%serp_location%' ){
							$html[] = '<i>' . ( $post['engine_location'] ) . '</i>';
						}
						
						else if( $value['td'] == '%serp_google%' ){

							if( isset($rank_data) && is_array($rank_data) && count($rank_data) > 0 ){
								// get best rank
								$best_pos = (int) $post['position_best'];
								
								// get worst
								$worst_pos = (int) $post['position_worst'];
								
								// current rank
								$current_pos = (int) $rank_data['position'];
								
								// previous rank
								$previous_pos = (int) $rank_data['position_prev'];

								//direction icon!
								$icon = 'same';
								if( $current_pos > $previous_pos ){
									$icon = 'down';
								}
								if( $current_pos < $previous_pos ){
									$icon = 'up';
								}
								
								$__notInTop100 = __('Not in top', $this->the_plugin->localizationName);
								$__icon_not100 = '<i class="serp-icon notintop100" title="' . $__notInTop100 . '"></i>';

								$__icon = '<i class="serp-icon ' . $icon . '"></i>';
								$__iconExtra = '';
								if ($icon=='up') {
									$__iconExtra .= '('.($previous_pos==999 ? '~' : '').'&#43;' . ( $previous_pos==999 ? (int) (100 - $current_pos) : (int) ($previous_pos - $current_pos) ) . ')';
								}
								else if($icon=='down') {
									$__iconExtra .= '('.($current_pos==999 ? '~' : '').'&minus;' . ($current_pos==999 ? (int) (100 - $previous_pos) : (int) ($current_pos - $previous_pos) ) . ')';
								}
								$__icon .= $__iconExtra;

								$html[] = '<div class="serp-rank-container">';
								$html[] = 	'<table class="serp-tbody-rank">';
								$html[] = 		'<tbody>';
								$html[] = 			'<tr>';
								$html[] = 					'<td width="57">';
								if( $current_pos==999 ){
									$html[] = 					'<div class="psp-rank-container-block-extra">' . ( $__icon_not100 ) . '</div>';
								}else{
								
									$html[] = 					'<div class="psp-rank-container-block">';
									$html[] = 						'<span class="the_pos">' . ( '#' . $current_pos ) . '</span>';
									
									
									$cur_pos_dir = $previous_pos - $current_pos; 
									$cur_pos_dir_symbol = '';
									if( $cur_pos_dir > 0 ){
										$cur_pos_dir_symbol = '+'; 
									}elseif( $cur_pos_dir < 0 ){
										$cur_pos_dir_symbol = '-'; 
									}
									$html[] = 						'<span class="the_status ' . ( $icon ) . '">' . ( $cur_pos_dir_symbol ) . ( abs($cur_pos_dir) ) . '</span>';
									$html[] = 					'</div>';
								}
								$html[] = 					'</td>';
								$html[] = 					'<td width="35"><div class="psp-rank-container-block-extra">' . ( $best_pos==999 ? $__icon_not100 : '#'.$best_pos ) . '</div></td>';
								$html[] = 					'<td><div class="psp-rank-container-block-extra">' . ( $worst_pos==999 ? $__icon_not100 : '#'.$worst_pos ) . '</div></td>';
								$html[] = 			'</tr>';
								$html[] = 		'</tbody>';
								$html[] = 	'</table>';
								$html[] = '</div>';
							}
						}
						
						else if( $value['td'] == '%serp_start_date%' ){
							$html[] = '<i>' . ( $post['created'] ) . '</i>';
						}
						
						else if( $value['td'] == '%serp_visits%' ){
							$html[] = '<i>' . ( $post['visits'] ) . '</i>';
						}
						
					}
					
					if( $this->opt['id'] == 'pspFacebookPlanner' ){
						
						if( $value['td'] == '%post_id%' ){
							$html[] = $post['id_post'];
						}
						elseif( $value['td'] == '%post_name%' ){
							$__postInfo = get_post( $post['id_post'], OBJECT );
							$html[] = '<input type="hidden" id="psp-item-title-' . ( $post['id'] ) . '" value="' . ( str_replace('"', "'", $__postInfo->post_title) ) . '" />';
							$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $__postInfo->ID)) . '">';
							$html[] = 	( $__postInfo->post_title . ( $__postInfo->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($__postInfo->post_status) : '</span>') );
							$html[] = '</a>';
							
							$html[] = '
							<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post['id'] ) . '">
								<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $__postInfo->ID)) . '">Edit</a>
								 | <a href="' . ( get_permalink( $__postInfo->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
							</span>';
						}
						else if( $value['td'] == '%status%' ){

							$__statusVals = array(
								0 	=> __( "New", $this->the_plugin->localizationName ),
								1	=> __( "Finished", $this->the_plugin->localizationName ),
								2	=> __( "Running", $this->the_plugin->localizationName ),
								3	=> __( "Error", $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['status'] ];
						}
						else if( $value['td'] == '%attempts%' ){
							$html[] = $post['attempts'];
						}
						else if( $value['td'] == '%response%' ){
							$html[] = $post['response'];
						}
						else if( $value['td'] == '%post_to%' ){
							
							$pg = get_option('psp_fb_planner_user_pages');
							if(trim($pg) != ""){
								$pg = @json_decode($pg);
							}

							$post_to = '';
							$serialize = $post['post_to'];
							$arr = unserialize($serialize);

							if( trim($arr['profile']) == 'on' ) {
								$post_to = '- Profile';
							}

							if( trim($arr['page_group']) != '' ) {
								$page_group = explode('##', $arr['page_group']);
								$post_to .= trim($post_to) != '' ? '<br />' : '';

								if($page_group[0] == 'page') {
									foreach($pg->pages as $k => $v) {
										if($v->id == $page_group[1]) {
											$post_to_title = $v->name;
										}
									}
								}else if($page_group[0] == 'group') {
									foreach($pg->groups as $k => $v) {
										if($v->id == $page_group[1]) {
											$post_to_title = $v->name;
										}
									}
								}

								$post_to .= "- ".(ucfirst($page_group[0])).": " . $post_to_title;
							}

							$html[] = $post_to;
						}
						else if( $value['td'] == '%email_at_post%' ){

							$__statusVals = array(
								'on' 	=> __( 'ON', $this->the_plugin->localizationName ), 
								'off'	=> __( 'OFF', $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['email_at_post'] ];
						}
						else if( $value['td'] == '%repeat_status%' ){

							$__statusVals = array(
								'on' 	=> __( 'ON', $this->the_plugin->localizationName ), 
								'off'	=> __( 'OFF', $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['repeat_status'] ];
						}
						else if( $value['td'] == '%repeat_interval%' ){
							$html[] = $post['repeat_interval'];
						}
						else if( $value['td'] == '%run_date%' ){
							$html[] = $post['run_date'];
						}
						else if( $value['td'] == '%started_at%' ){
							$html[] = $post['started_at'];
						}
						else if( $value['td'] == '%ended_at%' ){
							$html[] = $post['ended_at'];
						}
						else if( $value['td'] == '%post_privacy%' ){
							
							$__statusVals = array(
		        				"EVERYONE" => __('Everyone', $this->the_plugin->localizationName),
		        				"ALL_FRIENDS" => __('All Friends', $this->the_plugin->localizationName),
		        				"NETWORKS_FRIENDS" => __('Networks Friends', $this->the_plugin->localizationName),
		        				"FRIENDS_OF_FRIENDS" => __('Friends of Friends', $this->the_plugin->localizationName),
		        				"CUSTOM" => __('Private (only me)', $this->the_plugin->localizationName)
							);
							//$html[] = $__statusVals[ $post['post_privacy'] ];
							$html[] = $post['post_privacy'];
						}
					}

					$html[] = '</td>';
				}

				$html[] = 			'</tr>';
			}

			$html[] = 		'</tbody>';

			$html[] = 	'';

			$html[] = 	'</table>';

			// buttons
			if( trim($this->opt["custom_table"]) == "" || $this->opt["custom_table_force_action"]){

				if( isset($this->opt['mass_actions']) && ($this->opt['mass_actions'] === false) ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;</div>';
				}elseif( isset($this->opt['mass_actions']) && count($this->opt['mass_actions']) > 0 ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';

					foreach ($this->opt['mass_actions'] as $key => $value){
						$html[] = 	'<input type="button" value="' . ( $value['value'] ) . '" id="psp-' . ( $value['action'] ) . '" class="psp-button ' . ( $value['color'] ) . '">';
					}
					$html[] = '</div>';
				}else{
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
					$html[] = 	'<input type="button" value="' . __('Auto detect focus keyword for All', $this->the_plugin->localizationName) . '" id="psp-all-auto-detect-kw" class="psp-button blue">';
					$html[] = 	'<input type="button" value="' . __('Optimize All', $this->the_plugin->localizationName) . '" id="psp-all-optimize" class="psp-button blue">';
					$html[] = '</div>';
				}
				
				if( $this->opt['id'] == 'pspPageOptimization' ){
					$html[] = '<div id="psp-inline-editpost-boxtpl" style="display: none;">';
					$html[] = $this->the_plugin->edit_post_inline_boxtpl();
					$html[] = '</div>';
				}
			}
			else{
				$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
				$html[] = '</div>';
			}

			$html[] = $this->get_pagination();

			$html[] = '</div>';

            echo implode("\n", $html);

			return $this;
		}
		
		public function print_html()
		{
			$html = array();

			$items = $this->get_items();

			$html[] = '<input type="hidden" class="psp-ajax-list-table-id" value="' . ( $this->opt['id'] ) . '" />';

			// header
			if( $this->opt['show_header'] === true ) $this->print_header();

			// main table
			$this->print_main_table( $items );

			echo implode("\n", $html);

			return $this;
		}

		private function print_css_as_style( $css=array() )
		{
			$style_css = array();
			if( isset($css) && count($css) > 0 ){
				foreach ($css as $key => $value) {
					$style_css[] = $key . ": " . $value;
				}
			}

			return ( count($style_css) > 0 ? implode(";", $style_css) : '' );
		}
	
		public function get_page_social_stats( $postID, $website_url='' )
		{
			/*
			$__ = array(
				'http://mashable.com',
				'http://facebook.com',
				'http://stiumuzica.ro',
				'http://themeforest.net',
				'http://codecanyon.net'
			);
			
			shuffle($__);
			$website_url = $__[0];*/
			
			$cache_life_time = 240 * 10; // in seconds
			
			$the_db_cache = get_post_meta( $postID, '_psp_social_stats', true );
			
			// check if cache NOT expires 
			if( isset($the_db_cache['_cache_date']) && ( time() <= ( $the_db_cache['_cache_date'] + $cache_life_time ) )  ) {
				return $the_db_cache;
			}
			
			$db_cache = array();
			$db_cache['_cache_date'] = time();
			
			// Facebook
			$fql  = "SELECT url, normalized_url, share_count, like_count, comment_count, ";
			$fql .= "total_count, commentsbox_count, comments_fbid, click_count FROM ";
			$fql .= "link_stat WHERE url = '{$website_url}'";
			$apiQuery = "https://api.facebook.com/method/fql.query?format=json&query=" . urlencode($fql);
			$fb_data = $this->getRemote( $apiQuery );
			$fb_data = isset($fb_data[0]) ? $fb_data[0] : array();
			
			// Twitter
			$apiQuery = "http://urls.api.twitter.com/1/urls/count.json?url=" . $website_url;
			$tw_data = (array) $this->getRemote( $apiQuery );
			
			// LinkedIn
			$apiQuery = "http://www.linkedin.com/countserv/count/share?format=json&url=" . $website_url;
			$ln_data = (array) $this->getRemote( $apiQuery );
			
			// Pinterest
			$apiQuery = "http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . $website_url;
			$pn_data = (array) $this->getRemote( $apiQuery );
			
			// StumbledUpon
			$apiQuery = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=" . $website_url;
			$st_data = (array) $this->getRemote( $apiQuery );
			
			// Delicious
			$apiQuery = "http://feeds.delicious.com/v2/json/urlinfo/data?url=" . $website_url;
			$de_data = $this->getRemote( $apiQuery ); 
			$de_data = isset($de_data[0]) ? $de_data[0] : array();
		
			// Google Plus
			$apiQuery = "https://plusone.google.com/_/+1/fastbutton?bsv&size=tall&hl=it&url=" . $website_url;			
			$go_data = $this->getRemote( $apiQuery, false ); 
			
			require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
			if ( !empty($this->the_plugin->charset) )
				$html = pspphpQuery::newDocumentHTML( $go_data, $this->the_plugin->charset );
			else
				$html = pspphpQuery::newDocumentHTML( $go_data );

			$go_data = $html->find("#aggregateCount")->text();
			
			$db_cache['facebook'] = array(
				'share_count' => isset($fb_data['share_count']) ? $fb_data['share_count'] : 0,
				'like_count' => isset($fb_data['like_count']) ? $fb_data['like_count'] : 0,
				'comment_count' => isset($fb_data['comment_count']) ? $fb_data['comment_count'] : 0,
				'click_count' => isset($fb_data['click_count']) ? $fb_data['click_count'] : 0
			);
			 
			$db_cache['google'] = $go_data;
			$db_cache['twitter'] = isset($tw_data['count']) ? $tw_data['count'] : 0;
			$db_cache['linkedin'] = isset($ln_data['count']) ? $ln_data['count'] : 0;
			$db_cache['pinterest'] = isset($pn_data['count']) ? $pn_data['count'] : 0;
			$db_cache['stumbleupon'] = isset($st_data['result']['views']) ? $st_data['result']['views'] : 0;
			$db_cache['delicious'] = isset($de_data['total_posts']) ? $de_data['total_posts'] : 0;
			 
			// create a DB cache of this
			update_post_meta( $postID, '_psp_social_stats', $db_cache );
			
			return $db_cache;  
		}

		private function getRemote( $the_url, $parse_as_json=true )
		{ 
			$response = wp_remote_get( $the_url, array('user-agent' => "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0", 'timeout' => 10) ); 
			// If there's error
            if ( is_wp_error( $response ) ){
            	return array(
					'status' => 'invalid'
				);
            }
        	$body = wp_remote_retrieve_body( $response );
			
			if( $parse_as_json == true ){
				// trick for pinterest
				if( preg_match('/receiveCount/i', $body)){
					$body = str_replace("receiveCount(", "", $body);
					$body = str_replace(")", "", $body);
				}
				
	        	return json_decode( $body, true );
			}
			
			return $body;
		}
		
	}
}