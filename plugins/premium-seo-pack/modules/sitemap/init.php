<?php
/**
 * Sitemap Generator Class
 * http://www.aa-team.com
 * ======================
 *
 * @package         pspSeo
 * @author          AA-Team
 */
class pspSeoSitemap
{
    /*
    * Some required plugin information
    */
    const VERSION = '1.0';

    /*
    * Store some helpers config
    */
    public $the_plugin = null;

    protected $module_folder = '';
    protected $module_folder_path = '';
    private $module = '';
    
    protected $settings = array();
    protected $video_include = array();

    static protected $_instance;
    
    /**
    *
    * @var XMLWriter
    */
    private $writer;
    private $domain;
    private $path;
    private $filename = 'sitemap';
    private $current_item = 0;
    private $current_sitemap = 0;
    
    const EXT = '.xml';
    private static $SCHEMA = array(
       'xmlns'                           => 'http://www.sitemaps.org/schemas/sitemap/0.9',
       'xmlns:xsi'                       => 'http://www.w3.org/2001/XMLSchema-instance',
       'xmlns:schemaLocation'            => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
    );
    const SCHEMA_IMG = 'http://www.google.com/schemas/sitemap-image/1.1';
    const SCHEMA_VIDEO = 'http://www.google.com/schemas/sitemap-video/1.1';
    const DEFAULT_PRIORITY = 0.5;
    const DEFAULT_FREQUENCY = 'monthly';
    const ITEM_PER_SITEMAP = 100000;
    const SEPERATOR = '-';
    const INDEX_SUFFIX = 'index';
    
    /**
     * Images & Video sitemap
     */
    const VIDEOAPI_FORCE_SITEMAP = false;
    const VIDEOAPI_FORCE_CONTENT = false;
    static private $metaLifetime = 1209600; // lifetime in seconds! - 2 weeks
    static private $metaRandomLifetime = 43200;
    static private $imageIdentifiers = array(
        'default'   => '[\'\"]((?:http|https):\/\/.[^\'\"]+\.(?:jpe?g|png|gif))[\'\"]',
        'mysql'     => '[\'\"](http|https):\/\/.[^\'\"]+\.(jpe?g|png|gif)[\'\"]',
        'validate'  => '((?:http|https):\/\/.[^\'\"]+\.(?:jpe?g|png|gif))',
    );
    static private $videoIdentifies = array();
    private $file_cache_directory = '/psp-videos';
    
    /**
     * Update january 2015 - new options
     */
    private static $comments = array();
    private static $has_stylesheet = false;
    private static $xslfile = 'sitemap';
    private static $do_compress = false;
    private static $is_sitemap_url = false;
    private $posts_allowed = array('include' => array(), 'exclude' => array());
    
    private $home_url = '';
    private $permalink_struct = '';


    /*
    * Required __construct() function
    */
    public function __construct()
    {
        global $psp;

        require_once('opt.inc.php');
        self::$videoIdentifies = $pspSitemapVideosOpt;

        $this->the_plugin = $psp;
        $this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/sitemap/';
        $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/sitemap/';
        $this->module = $this->the_plugin->cfg['modules']['sitemap'];
        
        $this->settings = $this->the_plugin->getAllSettings( 'array', 'sitemap' );
        $this->video_include = isset($this->settings['video_include']) ? $this->settings['video_include'] : '';
        
        $this->home_url = $this->get_home_url();
        $this->permalink_struct = get_option('permalink_structure'); 
        
        $selfhost = parse_url( $this->home_url ); $selfhost = $selfhost['host'];
        self::$videoIdentifies = array_merge( self::$videoIdentifies, array(
            'localhost' => array(
                //'mysql'           => '\.(mp?4|avi|flv|wmv|mov|mpg|m4p|mkv|3GPP|ogv|MPEGPS|wmv|3gp|WebM|divx|rm|mpe|mpeg|mpeg2|mpeg4|DAT)',
                'mysql'         => '',
                'default'       => ''
                    .preg_quote($selfhost)
                    .'.*\.(?:mp?4|avi|flv|wmv|mov|mpg|m4p|mkv|3GPP|ogv|MPEGPS|wmv|3gp|WebM|divx|rm|mpe|mpeg|mpeg2|mpeg4|DAT)$'
            )
        ));
        self::$metaRandomLifetime = range(43200, 432000, 43200); // random range in seconds!
        
        if ( !$this->the_plugin->verify_module_status( 'sitemap' ) ) ; //module is inactive
        else {
            if ( is_admin() ) {
            } else {
                add_action( 'after_setup_theme', array( $this, 'query_load_reducing' ), 99 );
                add_filter( 'the_content', array( $this, 'content_add_video_snippets' ), 6, 1 );
                
                // opengraph related!
                if ( isset($this->settings['video_social_force']) && $this->settings['video_social_force']=='yes' ) {
                    add_action( 'premiumseo_opengraph', array( $this, 'video_opengraph' ) );
                    add_filter( 'premiumseo_opengraph_type', array( $this, 'video_opengraph_type' ), 10, 1 );
                    add_filter( 'premiumseo_opengraph_title', array( $this, 'video_opengraph_title' ), 10, 1 );
                    add_filter( 'premiumseo_opengraph_description', array( $this, 'video_opengraph_description' ), 10, 1 );
                    add_filter( 'premiumseo_opengraph_image', array( $this, 'video_opengraph_image' ), 10, 1 );
                }
            }
        }
   
        if ( $this->the_plugin->is_admin !== true ) {
   
            $this->detect_sitemap_page();
            //add_action('wp', array( $this->pluginDepedencies, 'detect_sitemap_page' ), 0);
        }
    }
    
    /**
    * Singleton pattern
    *
    * @return pspSeoSitemap Singleton instance
    */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
    
    private function query_load_reducing() {
        $retModules = $this->detect_if_sitemap_page();
        if( self::$is_sitemap_url && !empty($retModules) ) {
            remove_all_actions( 'widgets_init' );
        }
    }

    private function execution_variables() {
        $ret = array();
   
        if ( 1 ) {
            if ( self::$is_sitemap_url ) {
                $formatting = array(
                    'memory_limit'                  => '256',
                    'execution_time_limit'          => '0',
                );
                if ( isset($this->settings['memory_limit']) ) {
                    $formatting['memory_limit'] = (int) $this->settings['memory_limit'];
                }
                if ( isset($this->settings['execution_time_limit']) ) {
                    $formatting['execution_time_limit'] = (int) $this->settings['execution_time_limit'];
                }
                if ( isset($this->settings['compress']) ) {
                    self::$do_compress = (string) $this->settings['compress'] == 'yes' ? true : false;
                }
                if ( isset($this->settings['stylesheet']) && $this->settings['stylesheet'] == 'yes' ) {
                    self::$has_stylesheet = true;
                }

                @ini_set('memory_limit', $formatting['memory_limit'] . 'M');
                @ini_set('max_execution_time', $formatting['execution_time_limit']);
                @set_time_limit($formatting['execution_time_limit']); // infinte

                // do gzip only if everything it's fine
                if(
                    !self::$do_compress // compressing not activated yet
                    || empty($_SERVER['HTTP_ACCEPT_ENCODING']) // no encoding support
                    || ( // no gzip
                        strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === false
                        && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'x-gzip') === false
                    )
                    || !function_exists("gzwrite") // no PHP gzip support
                    || headers_sent() // headers already sent
                    || ob_get_contents() // already some output...
                    || in_array('ob_gzhandler', ob_list_handlers()) // other plugins (or PHP) is already using gzipp
                    || $this->the_plugin->get_php_ini_bool(ini_get("zlib.output_compression")) // zlib compression in php.ini enabled
                    || ob_get_level() > ( !$this->the_plugin->get_php_ini_bool(ini_get("output_buffering")) ? 0 : 1 ) // another output buffer  is already active, beside the default one
                ) {
                    self::$do_compress = false;
                }

                //ob_clean();
                if ( self::$do_compress ) {
                    @ob_start('ob_gzhandler');
                    $ret['gzipped'] = true;
                } else {
                    ob_start();
                }
            }
        }
        return $ret;
    }
    
    private function detect_if_sitemap_page() {
        $siteurl = get_option('siteurl');
        $parts = parse_url($siteurl);
        $path = isset($parts['path']) ? $parts['path'] : ''; //uncomment this if the sitemap is not generated
 
        $uri = $_SERVER['REQUEST_URI'];
        $path_len = strlen($path);
        if(strlen($uri) > $path_len && substr($uri, 0, $path_len) == $path) {

            $request = substr($uri,$path_len);
            $parts = parse_url($request);

            // 'index', 'images', 'videos', 'site', 'external', 'misc', 'author', 'archive', 'taxonomy', 'posttype'
            $allowedModules = array( 'index', 'images', 'videos', 'site', 'external', 'misc', 'author', 'archive', 'taxonomy', 'posttype' );
            $retModules = $this->detect_sitemap_type( $parts['path'] );
            if ( empty($retModules['mod']) || !in_array($retModules['mod'], $allowedModules) ) {
                return array();
            }

            self::$is_sitemap_url = true;
            return $retModules;
        }
        return array();
    }

    private function detect_sitemap_page()
    {
        $retModules = $this->detect_if_sitemap_page();
        if( self::$is_sitemap_url && !empty($retModules) ) {
            // begin sitemap
            $execution_resp = $this->execution_variables();
            $this->text_xml_header();
  
            self::$xslfile = 'sitemap';
            switch ($retModules['mod']) {

                case 'index':
                    $this->print_sitemap_index();
                    break;

                case 'images':
                    self::$xslfile = 'sitemap-images';
                    $this->print_sitemap( array('mod' => 'images') );
                    break;
                    
                case 'videos':
                    self::$xslfile = 'sitemap-videos';
                    $this->print_sitemap( array('mod' => 'videos') );
                    break;
                    
                default:
                    $this->print_sitemap( $retModules );
                    break;
            }
            //if ( isset($execution_resp['gzipped']) && $execution_resp['gzipped'] ) {
                //ob_end_flush();
            //}
            $xml_source = ob_get_clean();
 
            if ( self::$has_stylesheet ) {

                // Load the XML source
                /*$xml = new DOMDocument;
                $xml->loadXML($xml_source);
    
                $xsl = new DOMDocument;
                $xsl->load($this->module_folder_path . 'xsl/'.self::$xslfile.'.xsl');
    
                // Configure the transformer
                $proc = new XSLTProcessor;
                $proc->importStyleSheet($xsl); // attach the xsl rules
    
                $domTranformObj = $proc->transformToDoc($xml);
    
                // this will also output doctype and comments at top level
                foreach($domTranformObj->childNodes as $node) {
                    echo $domTranformObj->saveXML($node);
                }*/
                echo $xml_source;
            } else {
                echo $xml_source;           
            }
            echo PHP_EOL;
            
            remove_all_actions( 'wp_footer' );
            die; 
        }
    }

    private function detect_sitemap_type( $path ) {
        $ret = array( 'mod' => '', 'submod' => '', 'paginate' => '' );

        $regex_fullpath = 'sitemap-?([^.]*)\.xml$';
        if ( preg_match("~$regex_fullpath~iu", $path, $m) ) {

            $m = isset($m[1]) ? $m[1] : '';
            if ( empty($m) ) {
                return array_merge($ret, array('mod' => 'index'));
            }

            $regexp_module = '
                ([a-zA-Z0-9_]+)
                (?:
                    -
                    ([a-zA-Z0-9_-]+)
                    |
                )
            ';
            if ( preg_match("~$regexp_module~ixu", $m, $m2) ) {

                $m2mod = isset($m2[1]) ? $m2[1] : '';
                $m2submod = isset($m2[2]) ? $m2[2] : '';
   
                if ( empty($m2submod) ) {
                    return array_merge($ret, array('mod' => $m2mod));
                }
                
                $ret = array_merge($ret, array('mod' => $m2mod, 'submod' => $m2submod));
  
                $regex_paginate = '-p([1-9]+)$';
                if ( preg_match("~$regex_paginate~iu", $m2submod, $m3) ) {

                    $m2submod = str_replace($m3[0], '', $m2submod);
                    $m3 = isset($m3[1]) ? $m3[1] : '';
                    
                    return array_merge($ret, array('mod' => $m2mod, 'submod' => $m2submod, 'paginate' => $m3));
                }
            }
        }
        return $ret;
    }
    
    
    /**
     * get Items: posts | pages | custom post types
     *
     */
    
    private function get_items_posttype_old( $post_type='all' )
    {
        /* default arguments!
        $args = array(
            'posts_per_page'   => 5,
            'offset'           => 0,
            'category'         => '',
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'post',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => true );
        */
        $args = array( 
            'posts_per_page' => -1, 
            'offset'=> 1
        );
        
        if( $post_type != "all" ){
            $args['post_type'] = $post_type;
        }

        return get_posts( $args );
    }

    //get all published posts, pages, custom post types!
    private function get_items_posttype( $post_type='post,page', $media_type=array(), $pms=array() ) {
        global $wpdb;

        extract($pms);
        $has_limit = false;
        if ( isset($page) && !empty($page) ) {
            $global_items_per_page = isset($this->settings['global_items_per_page']) && !empty($this->settings['global_items_per_page']) ? (int) $this->settings['global_items_per_page'] : 0;
            if ( !empty($global_items_per_page) ) {
                $start = ($page - 1) * $global_items_per_page;
                $has_limit = true;
            }
        }
        
        $fields = 'a.ID, a.post_type, a.post_mime_type, a.post_parent, a.post_content, a.guid, a.post_title, a.post_excerpt, a.post_modified, a.post_modified_gmt, a.post_date, a.post_date_gmt, a.post_author';
        $fields_count = 'COUNT(a.ID) as count';
        $fields_count_all = 'COUNT(a.ID) as count, a.post_type, MAX(post_date_gmt) AS lastmod, MAX(post_date) as post_date, MAX(post_date_gmt) as post_date_gmt, MAX(post_modified) as post_modified, MAX(post_modified_gmt) as post_modified_gmt';
        
        $sql = "
            SELECT " .
                (isset($count) && $count
                    ?
                    ($count === 'all'
                        ? $fields_count_all
                        : $fields_count
                    )
                    : $fields
                ) . "
            FROM " . $wpdb->posts . " AS a
            WHERE 1=1
                AND ( 1=1 %s %s ) %s " .
                (isset($count) && $count
                    ?
                    ($count === 'all'
                        ? "GROUP BY a.post_type"
                        : ""
                    )
                    : "ORDER BY a.post_date DESC " . ($has_limit ? " LIMIT $start, $global_items_per_page" : "")
                ) . ";";

        $clause = $this->clause_post_type($post_type, 'a');
        
        //sitemap always | never included items & globale exclude items & categories belonging items
        $itemAllowedClause = $this->itemIsAllowed($post_type);
        
        $clause_media = '';
        if ( is_array($media_type) && !empty($media_type) ) {

            if ( in_array('images', array_keys($media_type)) ) {
                $clause_media = $this->clause_content_images('a', $media_type['images']);
            } else if ( in_array('videos', array_keys($media_type)) ) {
                $clause_media = $this->clause_content_videos('a', $media_type['videos']);
            }
        }
        
        $sql = sprintf($sql, $clause, $itemAllowedClause, $clause_media);
        //var_dump('<pre>',$sql,'</pre>'); die;

        if ( isset($count) && $count ) {
            if ( $count === 'all' ) {

                $res = $wpdb->get_results($sql);
                $ret = array();
                if ( !empty($res) ) {
                    foreach ($res as $key => $val) {
                        $ret["{$val->post_type}"] = $val->count; 
                    }
                }
                $res = $ret;
            } else {

                $res = $wpdb->get_var($sql);
            }

            return $res;
        } else {

            $res = $wpdb->get_results( $sql );
        }

        //var_dump('<pre>', $sql, count($res), '</pre>'); die('debug...');
        return $res;
    }
    
    private function itemIsAllowed( $post_type='post,page', $dbAlias='a' ) {
        global $wpdb;
        
        $sql = "
            SELECT a.ID, b.meta_value, a.post_type
             FROM {$wpdb->prefix}posts as a LEFT JOIN {$wpdb->prefix}postmeta AS b
             ON a.ID = b.post_id
             WHERE 1=1
             %s
             AND !isnull(b.post_id)
             AND b.meta_key = 'psp_sitemap_isincluded' AND b.meta_value IN ('always_include', 'never_include')
             ORDER BY a.ID ASC
            ;
        ";
        
        $clause = $this->clause_post_type($post_type, 'a');
        $sql = sprintf($sql, $clause);
 
        $res = $wpdb->get_results( $sql );
        //var_dump('<pre>',$post_type,$sql,'</pre>');  

        $clause = '';
        $ret = array('include' => array(), 'exclude' => array());
        if (is_array($res) && count($res)>0) {
            foreach ($res as $k=>$v) {

                $__post_type = $v->post_type;
                $__meta_value = $v->meta_value;

                if ( $__meta_value == 'always_include' ) {
                    if ( !isset($ret['include']["$__post_type"]) ) {
                        $ret['include']["$__post_type"] = array();
                    }
                    $ret['include']["$__post_type"][] = $v->ID;

                } else if ( $__meta_value == 'never_include' ) {
                    $ret['exclude'][] = $v->ID;
                }
            }
            
            // global excluded items
            $ret['exclude'] = array_merge($ret['exclude'], $this->global_excluded_items());
            
            // global excluded categories (items belonging to them)
            $ret['exclude'] = array_merge($ret['exclude'], $this->global_excluded_categories_items());
  
            // remove always allowed items from excluded items
            $always_include = $this->get_always_included_items($ret['include']);
            $ret['exclude'] = array_diff($ret['exclude'], $always_include);
            
            $this->posts_allowed = array_merge($this->posts_allowed, $ret);
  
            $__clause = array();
            foreach ( $ret as $key => $val ) {
                if ( empty($val) ) continue 1;

                if ( $key == 'include' ) {
                    
                    $post_type2 = explode(',', $post_type);
                    $post_type22 = array();
                    foreach ($val as $key2 => $val2) {
                        if ( !empty($val2) && in_array($key2, $post_type2) ) {
                            $post_type22 = array_merge($post_type22, $val2);
                        }
                    }
                    $val = $post_type22;

                    if ( !empty($val) ) {
                        $val = array_map( array($this, 'prepareForInList'), $val);
                        $val = implode(',', $val);
                        $__clause['include'] = "{$dbAlias}.ID IN ($val)";
                    }
                } else {

                    if ( !empty($val) ) {
                        $val = array_map( array($this, 'prepareForInList'), $val);
                        $val = implode(',', $val);                  
                        $__clause['exclude'] = "{$dbAlias}.ID NOT IN ($val)";
                    }
                }
            }
            switch (count($__clause)) {
                case 2:
                    $clause = ' AND ( ' . implode(' OR ', $__clause) . ' ) ';
                    break;
                    
                case 1:
                    $clause = ( isset($__clause['exclude']) ? ' AND ' : ' OR ' ) . implode('', $__clause) . ' ';
                    break;
                    
                default:
                    break;
            }
        }
        return $clause;
    }

    private function get_always_included_items( $arr ) {
        if ( empty($arr) ) return array();
 
        $ret = array();
        foreach ($arr as $key=>$val) {
            $ret = array_merge($ret, $val);
        }
        return $ret;
    }
    
    private function global_excluded_items() {
        $excluded = isset($this->settings['exclude_posts_ids']) ? explode(',', trim($this->settings['exclude_posts_ids'])) : array();
        $excluded = array_filter( array_map( array($this, 'prepareForDbClean'), $excluded ) );
        return $excluded;
    }
    
    private function global_excluded_categories() {
        $excluded_categ = isset($this->settings['exclude_categories']) ? $this->settings['exclude_categories'] : array();
        if (empty($excluded_categ)) return array();
        return $excluded_categ;
    }
    
    private function global_excluded_categories_items() {
        $excluded_categ = isset($this->settings['exclude_categories']) ? $this->settings['exclude_categories'] : array();
        if (empty($excluded_categ)) return array();
        
        $excluded_categ = array_map( array($this, 'prepareForInList'), $excluded_categ);
        $excluded_categ = implode(',', $excluded_categ);

        global $wpdb;
        
        $sql = "
            SELECT DISTINCT(object_id) FROM {$wpdb->term_relationships}
            WHERE term_taxonomy_id IN (
                SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ( " . $excluded_categ . ")
            )
            ORDER BY object_id ASC
            ;
        ";
        $res = $wpdb->get_results( $sql );

        $ret = array();
        if (is_array($res) && count($res)>0) {
            foreach ($res as $k=>$v) {
                $ret[] = $v->object_id;
            }
        }
        return $ret;
    }
    
    private function clause_post_type( $post_type='post,page', $dbAlias='a' ) {
        if ( empty($post_type) ) return '';
        
        $clause = " AND ( {$dbAlias}.post_status = 'publish' AND {$dbAlias}.post_password = '' ";
        $clause .= "AND {$dbAlias}.post_author != 0 AND {$dbAlias}.post_date != '0000-00-00 00:00:00' ";

        $post_type = explode(',', $post_type);
        $post_type = array_map( array($this, 'prepareForInList'), $post_type);
        $post_type2 = implode(',', $post_type);

        if ( !empty($post_type) ) {
            if (count($post_type)>1) {
                $clause .= " AND {$dbAlias}.post_type IN (" . $post_type2 . ") ";
            } else {
                $clause .= " AND {$dbAlias}.post_type = " . $post_type2 . " ";
            }
        }
        $clause .= " ) ";
        return $clause;
    }
    
    private function clause_content_images( $dbAlias='a', $media=array() ) {

        $clause = " AND ( ";
        $clause .= " {$dbAlias}.post_content regexp \"" . self::$imageIdentifiers['mysql'] . "\" ";
        if ( is_array($media) && !empty($media) ) {

            $list = array_keys($media);
            $list2 = array_map( array($this, 'prepareForInList'), $list);
            $list2 = implode(',', $list2);
            
            if (count($list)>1) {
                $clause .= " OR {$dbAlias}.ID IN (" . $list2 . ") ";
            } else {
                $clause .= " OR {$dbAlias}.ID = " . $list2 . " ";
            }
        }
        $clause .= " ) ";

        if ( preg_match('/\s*(?:and)\s*\(\s*\)\s*/i', $clause) > 0 ) return '';
        return $clause;
    }
    
    private function clause_content_videos( $dbAlias='a', $media=array() ) {

        if ( empty($this->video_include) ) return " AND ( 1 = 2 ) ";

        $videoRegex = array();
        if ( is_array(self::$videoIdentifies) && !empty(self::$videoIdentifies) ) {
            foreach ( self::$videoIdentifies as $key => $val ) {
                if ( $key == 'localhost' ) continue 1;
                $videoRegex[] = $val['mysql'];
            }
        }
        $videoRegex = implode('|', $videoRegex);
        
        $clause = "";
        if ( !in_array('localhost', (array) $this->video_include) ) $clause .= "";
        else
            $clause .= " AND ( {$dbAlias}.post_content regexp \"" . $videoRegex . "\" ";

        if ( is_array($media) && !empty($media) ) {

            $list = array_keys($media);
            $list2 = array_map( array($this, 'prepareForInList'), $list);
            $list2 = implode(',', $list2);
            
            if (count($list)>1) {
                $clause .= ( empty($clause) ? " AND " : " OR ")
                    . "{$dbAlias}.ID IN (" . $list2 . ") "
                    . ( empty($clause) ? "" : " ) ");
            } else {
                $clause .= ( empty($clause) ? " AND " : " OR ")
                    . "{$dbAlias}.ID = " . $list2 . " "
                    . ( empty($clause) ? "" : " ) ");
            }
        } else {
            $clause .= empty($clause) ? "" : " ) ";
        }

        if ( preg_match('/\s*(?:and)\s*\(\s*\)\s*/i', $clause) > 0 ) return '';
        return $clause;
    }


    /**
     * get Images
     *
     */
    //get all attachments - used to find media images!
    private function get_images( $post_type='post,page' ) {
        global $wpdb;
        
        $sql = "
            SELECT a.ID, a.post_type, a.post_mime_type, a.post_parent, a.guid
             FROM " . $wpdb->prefix . "posts AS a
             LEFT JOIN " . $wpdb->prefix . "posts AS b ON a.post_parent=b.ID
             WHERE 1=1
             AND ( a.post_parent>0 AND a.post_type = 'attachment' AND a.post_status = 'inherit' AND a.post_mime_type REGEXP 'image/[[:alpha:]]+' AND a.guid!='' )
             %s
             ORDER BY a.post_date DESC
            ;
        ";

        $clause = $this->clause_post_type($post_type, 'b');
        $sql = sprintf($sql, $clause);

        $res = $wpdb->get_results( $sql );
        return $res;
    }

    //retrieve images
    private function filter_images( $images=array() ) {
        if ( empty($images) ) return array();
        
        $__images = array();

        if ( is_array($images) && count($images)>0 ) {
            foreach ($images as $k=>$post) {
                
                $src = $post->guid;
                $src_valid = $this->is_valid_image($src);
                if ( !$src_valid ) continue 1;
                else $src = $src_valid;

                $__images[ $post->post_parent ][] = $src;
            }
        }
        return $__images;
    }
    private function filter_item_image( $post ) {
        if ( empty($post) ) return array();

        $__images = array();
 
        $pattern = "/" . self::$imageIdentifiers['default'] . "/ui"; //utf-8, case insensitive
        if( preg_match_all($pattern, $post->post_content, $matches, PREG_SET_ORDER) ) {
            foreach($matches as $match) {

                $src = isset($match[1]) ? $match[1] : '';
                $src_valid = $this->is_valid_image($src);
                if ( !$src_valid ) continue 1;
                else $src = $src_valid;
                
                //$__images[ $post->ID ][] = $src; //retrieve only the link!
                $__images[] = $src;
            }
        }
        $__images = array_values( array_unique($__images) );
        return $__images;
    }
    
    private function is_valid_image($src) {
        $parsed_home = parse_url( $this->home_url );
        $host = ''; $scheme = 'http';
        if ( isset($parsed_home['host']) && !empty($parsed_home['host']) ) {
            $host = str_replace( 'www.', '', $parsed_home['host'] );
        }
        if ( isset($parsed_home['scheme']) && !empty($parsed_home['scheme']) ) {
            $scheme = $parsed_home['scheme'];
        }
  
        // validate image src
        $pattern = "/" . self::$imageIdentifiers['validate'] . '|(?:.*attachment_id=([0-9]+))' . "/ui"; //utf-8, case insensitive

        if ( $this->is_url_relative( $src ) === true ) {
            if ( $src[0] !== '/' ) {
                continue 1;
            } else {
                // URL is relative => make it absolute
                $src = $this->home_url . $src;
            }
        } elseif ( strpos( $src, 'http' ) !== 0 ) {
            // url has relative protocol => prefix scheme
            $src = $scheme . ':' . $src;
        }
    
        if ( strpos( $src, $host ) === false ) {
            return false;
        }
        if ( $src != esc_url( $src ) ) {
            return false;
        }
        if( !preg_match($pattern, $src, $matches) || empty($matches) ) {
            return false;
        } else {
            $attachment_id = isset($matches[2]) ? (int) $matches[2] : 0;
            if ( $attachment_id > 0 ) {
                $attachment_url = wp_get_attachment_url( $attachment_id );
                return $attachment_url;
            }
        }
        return $src;
    }
    
    
    /**
     * get Videos
     *
     */
    private function get_video_dbinfo( $guid='' ) {
        if ( empty($guid) ) return array();
        
        global $wpdb;

        $sql = "
            SELECT a.ID, a.post_type, a.post_mime_type, a.post_parent, a.guid, a.post_title, a.post_content, a.post_excerpt, a.post_date_gmt
             FROM " . $wpdb->prefix . "posts as a
             WHERE 1=1
             AND a.guid = %s
             AND ( a.post_password='' AND a.post_parent>0 AND a.post_type = 'attachment' AND a.post_status = 'inherit' AND a.post_mime_type REGEXP 'video/' AND a.guid!='' )
             LIMIT 1
            ;
        ";
        $sql = $wpdb->prepare($sql, $guid);
        $res = $wpdb->get_row( $sql );
        return $res;
    }

    //get all attachments - used to find media videos!
    private function get_videos( $post_type='post,page', $post_id = 0 ) {
   
        if ( empty($this->video_include) ) return array();
        if ( !in_array('localhost', (array) $this->video_include) ) return array();
    
        global $wpdb;

        $sql = "
            SELECT a.ID, a.post_type, a.post_mime_type, a.post_parent, a.guid, a.post_title, a.post_content, a.post_excerpt, a.post_date_gmt
             FROM " . $wpdb->prefix . "posts AS a
             LEFT JOIN " . $wpdb->prefix . "posts AS b ON a.post_parent=b.ID
             WHERE 1=1
             AND ( a.post_password='' AND a.post_parent>0 AND a.post_type = 'attachment' AND a.post_status = 'inherit' AND a.post_mime_type REGEXP 'video/' AND a.guid!='' )
             %s %s
             ORDER BY a.post_date DESC
            ;
        ";
 
        $clause = $this->clause_post_type($post_type, 'b');

        $clause_perpost = '';
        if ( isset($post_id) && $post_id > 0 )
            $clause_perpost = " and b.ID = '$post_id' ";

        $sql = sprintf($sql, $clause_perpost, $clause);

        $res = $wpdb->get_results( $sql );
        return $res;
    }
    
    //retrieve images
    private function filter_videos( $images=array() ) {
        if ( empty($images) ) return array();
        
        if ( empty($this->video_include) ) return array();
        if ( !in_array('localhost', (array) $this->video_include) ) return array();
        
        $__images = array();
        $extrainfo = array();

        if ( is_array($images) && count($images)>0 ) {
            foreach ($images as $k=>$post) {

                if ( empty($post->guid) ) continue 1;

                $__images[ $post->post_parent ]['localhost'][] = $post->guid;
                //$alias = 'post_'.$post->ID;
                $extrainfo[ $post->post_parent ]['localhost'][] = $post;
            }
        }
        
        return array(
            'extrainfo'     => $extrainfo,
            'videos'        => $__images
        );
    }
    private function filter_item_video( $content ) {
        if ( empty($this->video_include) ) return array();

        //$content = $this->strip_shortcode( $content ); // strip shortcodes!
        if ( empty($content) ) return array(); // validate content!
        
        // php query class
        require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
        if ( !empty($this->the_plugin->charset) )
            $doc = pspphpQuery::newDocument( $content, $this->the_plugin->charset );
        else
            $doc = pspphpQuery::newDocument( $content );
        
        $__founds = array();
        
        // Video - wp shortcode! - treated in Just Plain Links!
        /*$regex_video = '
            \[video(?:[^\]]+)?
                (http(?:s|v|vh|vp|a)?:\/\/(?:www\.)?[^\s"]+)
            \]\s*\[\/video\]
        ';
        $regex_video = '/'.$regex_video.'/ixum';
        preg_match_all( $regex_video, $content, $wpvideos, PREG_SET_ORDER );
        if ( !empty($wpvideos) ) { foreach ( $wpvideos as $match ) {
            if ( empty($match[1]) ) continue 1;
            $__founds[] = $match[1];
        } }*/
        
        // Embeds - wp shortcode!
        $regex_embeds = '
            \[embed(?:[^\]]+)?\]
                (http(?:s|v|vh|vp|a)?:\/\/(?:www\.)?[^\s"]+)
            \[\/embed\]
        ';
        $regex_embeds = '/'.$regex_embeds.'/ixum';
        preg_match_all( $regex_embeds, $content, $embeds, PREG_SET_ORDER );
        if ( !empty($embeds) ) { foreach ( $embeds as $match ) {
            if ( empty($match[1]) ) continue 1;
            $__founds[] = $match[1];
        } }

        // Embeds - inside other objects!
        $embeds2 = $doc->find('embed');
        foreach( $embeds2 as $tag ) {
            $tag = pspPQ($tag); // cache the object

            // special cases!
            //if ( preg_match('/flickr\.com/iu', $tag->attr('src')) > 0 ) {
            //  continue 1;
            //}
            $__founds[] = $tag->attr('src');
        }
        
        // IFrames!
        $iframes = $doc->find('iframe');
        foreach( $iframes as $tag ) {
            $tag = pspPQ($tag); // cache the object
            $__founds[] = $tag->attr('src');
        }
        
        // Objects!
        $objects = $doc->find('object');
        foreach( $objects as $tag ) {
            $tag = pspPQ($tag); // cache the object
            $tag = $tag->find('param');
            
            $isSpecial = false; $specialVal = '';
            foreach( $tag as $param ) {
                $param = pspPQ($param); // cache the object
                if ( in_array($param->attr('name'), array('src', 'movie')) ) {

                    // special cases!
                    if ( preg_match('/flickr\.com/iu', $param->attr('value')) > 0 ) {
                        $isSpecial = 'flickr.com';
                        //continue 1;
                    }
                    $__founds[] = $param->attr('value');
                }
                if ( in_array($param->attr('name'), array('flashvars')) ) {
                    if ( preg_match('/photo_id=(\d+)$/iu', $param->attr('value'), $flickrMatch) > 0 )
                        $specialVal = $flickrMatch[1];
                }
            }
            if ( $isSpecial!==false && !empty($specialVal) ) {
                if ( $isSpecial == 'flickr.com' )
                    $__founds[] = "http://www.flickr.com/__flashvars__/$specialVal/";
            }
        }

        // Just Plain Links!
        $regex_links = '
            \s*
            (http(?:s|v|vh|vp|a)?:\/\/(?:www\.)?[^\s"]+)
            \s*
        ';
        $regex_links = '/'.$regex_links.'/ixum';
        preg_match_all( $regex_links, $content, $links, PREG_SET_ORDER );
        if ( !empty($links) ) { foreach ( $links as $match ) {
            if ( empty($match[1]) ) continue 1;
            $__founds[] = $match[1];
        } }
        
        if ( empty($__founds) ) return array(); // validate founds!

        // clean duplicates!
        if ( !empty($__founds) ) {
            $__founds = array_values( array_unique($__founds) );
        }

        // allowed video providers
        $allowedVideoProviders = array();
        if ( is_array(self::$videoIdentifies) && !empty(self::$videoIdentifies) ) {
            foreach ( self::$videoIdentifies as $key => $val ) {

                if ( !in_array($key, (array) $this->video_include) ) continue 1;
                $allowedVideoProviders[ "$key" ] = $val;
            }
        }

        // go through found urls!
        $__images = array();
        foreach ( $__founds as $found ) {

            $found = trim( $found );
            if ( preg_match('/^http/iu', $found) == 0 )
                $found = 'http:' . $found;
                
            $parseUrl = parse_url( $found );
            $host = $parseUrl['host'];
            if ( !isset($host) || empty($host) )
                continue 1;

            if ( is_array($allowedVideoProviders) && !empty($allowedVideoProviders) ) {
                foreach ( $allowedVideoProviders as $key => $val ) {

                    $pattern = '/' . $val['default'] . '/ixu';

                    //if ( $key != 'xyz' ) continue 1;
                    if ( $key == 'xyz' ) {
                        //var_dump('<pre>', $pattern , '</pre>'); die('debug...');
                    }

                    if ( preg_match_all($pattern, $found, $matches, PREG_SET_ORDER)) {

                        if ( $key == 'xyz' ) {
                            //var_dump('<pre>',$matches ,'</pre>');
                        }

                        foreach($matches as $match) {

                            if ( $key == 'localhost' ) {
                                $__images[ "$key" ][] = $found;
                                continue 1;
                            }
                            if ( empty($match[1]) ) continue 1;
                            if ( $key == 'blip' && in_array($match[1], array('api')) ) continue 1;
                            
                            $__images[ "$key" ][] = $match[1];
                        }
                    }
                } // end foreach allowed providers!
            }
        } // end foreach main!

        // clean duplicates
        if ( !empty($__images) ) {
            foreach ( $__images as $kk => $vv) {
                $__images[ "$kk" ] = array_values( array_unique($__images[ "$kk" ]) );
            }
        }
        //var_dump('<pre>', $__images , '</pre>'); die('debug...'); 
        return $__images;
    }
    
    private function getVideosInfo( $videos = array(), $post = null, $extrainfo = array(), $recheckVideos = false ) {
        if ( empty($videos) || is_null($post) || !isset($post->ID) ) return array();

        $post_id = (int) $post->ID;

        $ret = array();

        require($this->module_folder_path . 'video_info.php');
        $pspVideoInfo = new pspVideoInfo( array(
            'vzaar_domain'          => $this->settings['vzaar_domain'],
            'viddler_key'           => $this->settings['viddler_key'],
            'flickr_key'            => $this->settings['flickr_key']
        ));

        $current_metas = array_merge( array(), $this->getVideoMetas( $post_id ) );

        // try to retrieve attachment details (for localhost) based on guid!
        if ( isset($videos['localhost']) && !empty($videos['localhost']) ) {
            foreach ( $videos['localhost'] as $key => $val ) {
                if ( !empty($extrainfo) && isset($extrainfo[ "localhost" ][ "$key" ]) ) ;
                else {
                    $extrainfo[ "localhost" ][ "$key" ] = $this->get_video_dbinfo( $val );
                }
            }
        }

        foreach ( $videos as $k => $v ) {

            if ( is_array($v) && !empty($v) ) {
                foreach ( $v as $key => $val ) {

                    $videoLocalDetails = array();
                    if ( $k == 'localhost' && !empty($extrainfo) && isset($extrainfo[ "$k" ][ "$key" ]) )
                        $videoLocalDetails = $extrainfo[ "$k" ][ "$key" ];

                    if ( $k != 'localhost' )
                        $__vidalias = $val;
                    else if ( $k == 'localhost' && isset($videoLocalDetails->ID) )
                        $__vidalias = 'post_' . ( (int) $videoLocalDetails->ID );
                    if ( empty($__vidalias) )
                        $__vidalias = 'rand_' . $this->the_plugin->generateRandomString(10);
                    $current_alias = $k . '_' . $this->setVideoAlias( $__vidalias );
                    $meta_alias = 'psp_videos_' . $current_alias;

                    $vid_meta = isset($current_metas[ "$meta_alias" ]) ? $current_metas[ "$meta_alias" ] : array();

                    $__doRequestInfo = false;
                    if ( isset($vid_meta['status']) && isset($vid_meta['created']) ) {

                        srand();
                        $__rand = rand(0, count(self::$metaRandomLifetime)-1);
                        $__lifetime = (int) ( self::$metaLifetime + self::$metaRandomLifetime[$__rand] );

                        if ( $recheckVideos || $vid_meta['status'] != 'valid'
                        || ( (int) ($vid_meta['created'] + $__lifetime) < time() ) )
                            $__doRequestInfo = true;
                        else
                            $ret[ "$k" ][ "$key" ] = $vid_meta;
                    } else
                        $__doRequestInfo = true;
                    
                    if ( $__doRequestInfo ) {
                        $ret[ "$k" ][ "$key" ] = $pspVideoInfo->getVideoInfo( $val, $k, array(
                            'post'          => $post,
                            'extrainfo'     => $videoLocalDetails
                        ));
                        $remoteThumb = $this->saveVideoThumbnail( $post_id, $current_alias, $ret[ "$k" ][ "$key" ]['thumbnail'] );
                        if ( $remoteThumb['status'] == 'valid' ) // update with remote thumb
                            $ret[ "$k" ][ "$key" ]['thumbnail'] = $remoteThumb['resp'];

                        update_post_meta( $post_id, $meta_alias, $ret[ "$k" ][ "$key" ] );
                        update_post_meta( $post_id, $meta_alias.'_stat', $ret[ "$k" ][ "$key" ]['status'] );
                    }
                    
                    $ret[ "$k" ][ "$key" ] = array_merge( $ret[ "$k" ][ "$key" ], $this->video_info_check( $ret[ "$k" ][ "$key" ] ) );
                }
            }
        }
        return $ret;
    }

    private function getVideoMetas( $post_id ) {
        global $wpdb;
        
        $sql = "
            SELECT a.*
             FROM " . $wpdb->prefix . "postmeta AS a
             WHERE 1=1
             AND a.post_id = '" . $post_id . "' AND a.meta_key regexp 'psp_videos_'
             ORDER BY a.meta_id ASC
            ;
        ";

        $res = $wpdb->get_results( $sql );
        
        $ret = array();
        if ( is_array($res) && !empty($res) ) {
            foreach ( $res as $key => $val ) {

                if ( isset($val->meta_value) ) {
                    $meta_value = $val->meta_value;
                    $meta_value = unserialize( $meta_value );

                    if ( isset($meta_value['resp']) )
                        unset( $meta_value['resp'] );

                    $ret[ "{$val->meta_key}" ] = $meta_value;
                }
            }
        }
        return $ret;
    }
    
    private function setVideoAlias( $str='' ) {
        if ( !empty($str) ) {
            $str = preg_replace('/[^a-zA-Z0-9\-_]/iu', '-', $str);
            $str = substr($str, 0, 50);
        }
        return $str;
    }
    
    private function saveVideoThumbnail( $post_id=0, $alias='default', $remote_thumb='' ) {

        $ret = array('status' => 'invalid', 'resp' => '');
        
        if ( empty($remote_thumb) )
            return array_merge( $ret, array(
                'resp' => 'Empty remote thumb file!'
            ));
        
        // retrieve the remote thumb!       
        $getdata = $this->the_plugin->remote_get( $remote_thumb, 'default' );
        if ( !isset($getdata) || $getdata['status'] === 'invalid' ) {
            return array_merge( $ret, array(
                'resp' => 'Could not retrieve the remote thumb'
            ));
        }
        $getdata = $getdata['body'];

        // create thumbs directory
        clearstatcache();
        $upload_dir = wp_upload_dir();
        if (! is_dir( $upload_dir['path'] . '' . $this->file_cache_directory ) ) {
            @mkdir( $upload_dir['path'] . '' . $this->file_cache_directory );
            if (! is_dir( $upload_dir['path'] . '' . $this->file_cache_directory ) ) {
                die("Could not create the file cache directory.");
                return array_merge( $ret, array(
                    'resp' => 'Could not create the file cache directory.'
                ));
            }
        }

        $the_image_data = $getdata;

        // save thumb on local server
        $new_image = sprintf($upload_dir['path'] . '' . $this->file_cache_directory . '/%d-%s.jpg', $post_id, $alias);
        $new_image_url = sprintf($upload_dir['url'] . '' . $this->file_cache_directory . '/%d-%s.jpg', $post_id, $alias);
        file_put_contents( $new_image, $the_image_data );

        if ( $this->the_plugin->verifyFileExists($new_image) )
            return array_merge( $ret, array(
                'status'    => 'valid',
                'resp'      => $new_image_url
            ));
            
        return array_merge( $ret, array(
            'resp'      => 'Could not save the file in cache directory.'
        ));
    }
    
    private function video_info_check( $video = array() ) {

        if ( isset($this->settings['video_title_prefix']) && !empty($this->settings['video_title_prefix']) ) {
            if ( @preg_match('/(\s|^)'.preg_quote($this->settings['video_title_prefix']).'(\s|$)/iu', $video['title']) == 0 )
                $video['title'] = $this->settings['video_title_prefix'] . ': ' . $video['title'];
        }
        
        if ( !isset($video['thumbnail']) || empty($video['thumbnail']) )
            if ( isset($this->settings['thumb_default']) && !empty($this->settings['thumb_default']) )
                $video['thumbnail'] = $this->settings['thumb_default'];

        if ( isset($video['content_loc']) )
            if ( preg_match('/\/\/[\w]\.cloudfront\.net/iu', $video['content_loc']) > 0
                || preg_match('/Key\-Pair\-Id=/iu', $video['content_loc']) > 0 )
                $video['content_loc'] = '';

        return (array) $video;
    }
    
    private function get_post_videos( $post=null, $recheckVideos=true ) {

        if ( is_null($post) || !isset($post->ID) ) return array();
        $post_id = (int) $post->ID;

        $ret = array();
        $current_metas = array_merge( array(), $this->getVideoMetas( $post_id ) );
        if ( !empty($current_metas) ) {
            foreach ( $current_metas as $k => $v ) {
                $alias = str_replace('psp_videos_', '', $k);
                $__level_1 = substr($alias, 0, strpos($alias, '_'));
                $__level_2 = str_replace($__level_1.'_', '', $alias);

                if ( !empty($__level_1) && !empty($__level_2) ) {

                    //$__itemImg[ "$__level_1" ][] = $__level_2;

                    $current_alias = $__level_1 . '_' . $__level_2;
                    $meta_alias = 'psp_videos_' . $current_alias;

                    $vid_meta = isset($current_metas[ "$meta_alias" ]) ? $current_metas[ "$meta_alias" ] : array();
                    if ( !empty($vid_meta) ) {

                        $vid_meta = array_merge( $vid_meta, $this->video_info_check( $vid_meta ) );
                        $ret[ "$k" ][ "$key" ] = $vid_meta;
                    }
                }
            }
        }
        $__videoInfo = $ret;
        
        if ( empty($__videoInfo) || $recheckVideos ) {
            
            $images = $this->get_videos( '', $post_id );
            $images_tmp = $this->filter_videos( $images );
            $images = !empty($images_tmp) && isset($images_tmp['videos']) ? $images_tmp['videos'] : array();
            $extrainfo = !empty($images_tmp) && isset($images_tmp['extrainfo']) ? $images_tmp['extrainfo'] : array();
    
            $__itemImg = array();
            $__extrainfo = array();
    
            if ( isset($images[ $post_id ]) && is_array($images[ $post_id ]) && count($images[ $post_id ])>0 ) {
                $__itemImg = $images[ $post_id ];
                $__extrainfo = $extrainfo[ $post_id ];
            }
    
            $__itemImg2 = $this->filter_item_video( $post->post_content ); //retrieve post images from post content
            $__itemImg = array_merge_recursive( $__itemImg, $__itemImg2 );
    
            // clean duplicates
            if ( !empty($__itemImg) ) {
                foreach ( $__itemImg as $kk => $vv) {
                    $__itemImg[ "$kk" ] = array_values( array_unique($__itemImg[ "$kk" ]) );
                }
            }
            
            $__videoInfo = $this->getVideosInfo( $__itemImg, $post, $__extrainfo, $recheckVideos );
        }

        return $__videoInfo;
    }
    
    public function content_add_video_snippets( $content ) {

        global $post;

        // validations!
        if ( is_home() || is_archive() || is_tax() || is_tag() || is_category() || is_feed() )
            return $content;

        if ( !is_object($post) || !isset($post->ID) )
            return $content;

        $videosInfo = $this->get_post_videos( $post, self::VIDEOAPI_FORCE_CONTENT );

        if ( empty($videosInfo) || !is_array($videosInfo) )
            return $content;

        $ret = array();
        $ret[] = PHP_EOL;
        foreach ( $videosInfo as $type => $videos ) {
            foreach ( $videos as $key => $video ) {

                if ( !$this->isVideoValid( $video ) ) continue 1;
                        
                $ret[] = '
                <!--begin psp video snippet : ' . ($type) . '-->
                ';
                $ret[] = '<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
    
                $ret[] = '<meta itemprop="name" content="' . $video['title'] . '">';
                $ret[] = '<meta itemprop="thumbnailURL" content="' . $video['thumbnail'] . '">';
                $ret[] = '<meta itemprop="description" content="' . $video['description'] . '">';
                $ret[] = '<meta itemprop="uploadDate" content="' . $video['publish_date'] . '">';
                if ( isset($video['player_loc']) && !empty($video['player_loc']) )
                    $ret[] = '<meta itemprop="embedURL" content="' . $video['player_loc'] . '">';
                if ( isset($video['content_loc']) && !empty($video['content_loc']) )
                    $ret[] = '<meta itemprop="contentURL" content="' . $video['content_loc'] . '">';
    
                if ( isset($video['duration']) && !empty($video['duration']) )
                    $ret[] = '<meta itemprop="duration" content="' . $this->duration_iso_8601( $video['duration'] ) . '">';
    
                $ret[] = '</div>';
                $ret[] = '
                <!--end psp video snippet : ' . ($type) . '-->
                ';
            }
        }
        $ret[] = PHP_EOL;

        $content .= implode('', $ret);
        return $content;
    }
    
    private function isVideoValid( $video = array() ) {

        // mandatory fields: title, description, thumbnail and ( player loc or content loc )
        $validate = array();
        $validate[0] = (bool) ( !isset($video['title']) || empty($video['title']) );
        $validate[1] = (bool) ( !isset($video['description']) || empty($video['description']) );
        $validate[2] = (bool) ( !isset($video['thumbnail']) || empty($video['thumbnail']) );
        $validate[3] = (bool) ( !isset($video['player_loc']) || empty($video['player_loc']) );
        $validate[4] = (bool) ( !isset($video['content_loc']) || empty($video['content_loc']) );
        if ( $validate[0] || $validate[1] || $validate[2] || ( $validate[3] && $validate[4] ) )
            return false;

        return true;
    }
    
    /**
     * Video Opengraph
     * 
     */
    public function video_opengraph_first_found() {
        global $post;

        // validations!
        if ( is_home() || is_archive() || is_tax() || is_tag() || is_category() || is_feed() )
            return array();

        if ( !is_object($post) || !isset($post->ID) )
            return array();

        $videosInfo = $this->get_post_videos( $post, false );

        if ( empty($videosInfo) || !is_array($videosInfo) )
            return array();

        $ret = array();
        foreach ( $videosInfo as $type => $videos ) {
            foreach ( $videos as $key => $video ) {

                if ( !$this->isVideoValid( $video ) ) continue 1;
                if ( !isset($video['player_loc']) || empty($video['player_loc']) ) continue 1;
                
                return $video;
            }
        }
        return array();
    }
    public function video_opengraph() {
        $video = $this->video_opengraph_first_found();
        if ( !isset($video) || empty($video) ) return false;

        $ret = array();
        $ret[] = '<meta property="og:video" content="' . $video['player_loc'] . '" />';
        $ret[] = '<meta name="medium" content="video" />';
        $ret[] = '<meta name="video_type" content="application/x-shockwave-flash" />';
        $ret[] = '<link rel="image_src" href="' . $video['thumbnail'] . '" />';
        $ret[] = '<link rel="video_src" href="' . $video['player_loc'] . '" />';
        echo implode(PHP_EOL, $ret) . PHP_EOL;
    }
    public function video_opengraph_type( $val = '' ) {
        $video = $this->video_opengraph_first_found();
        if ( isset($video) && !empty($video) )
            return 'video';
        return $val;
    }
    public function video_opengraph_title( $val = '' ) {
        $video = $this->video_opengraph_first_found();
        if ( isset($video) && !empty($video) )
            if ( isset($video['title']) && !empty($video['title']) )
                return $video['title'];
        return $val;
    }
    public function video_opengraph_description( $val = '' ) {
        $video = $this->video_opengraph_first_found();
        if ( isset($video) && !empty($video) )
            if ( isset($video['description']) && !empty($video['description']) )
                return $video['description'];
        return $val;
    }
    public function video_opengraph_image( $val = '' ) {
        $video = $this->video_opengraph_first_found();
        if ( isset($video) && !empty($video) )
            if ( isset($video['thumbnail']) && !empty($video['thumbnail']) )
                return $video['thumbnail'];
        return $val;
    }


    /**
    * Change the header to text/xml
    *
    */
    private function text_xml_header() 
    {
        // if caching is not enabled, send no cache headers
        nocache_headers();

        //header('Cache-Control: no-cache, must-revalidate, max-age=0');
        //header('Pragma: no-cache');
        header('X-Robots-Tag: noindex, follow');
        //if ( self::$has_stylesheet ) {
        //  header('Content-Type: text/html; charset=utf-8');
        //} else {
            header('Content-Type: text/xml; charset=utf-8');            
        //}
    }

    /**
    * Returns root path of the website
    *
    * @return string
    */
    private function getDomain() {
        return $this->domain;
    }
    
    /**
    * Sets root path of the website, starting with http:// or https://
    *
    * @param string $domain
    */
    public function setDomain($domain) {
        $this->domain = $domain;
        return $this;
    }
    
    /**
     * Returns XMLWriter object instance
     *
     * @return XMLWriter
     */
    private function getWriter() {
        return $this->writer;
    }

    /**
     * Assigns XMLWriter object instance
     *
     * @param XMLWriter $writer 
     */
    private function setWriter(XMLWriter $writer) {
        $this->writer = $writer;
    }

    /**
     * Returns path of sitemaps
     * 
     * @return string
     */
    private function getPath() {
        return $this->path;
    }

    /**
     * Sets paths of sitemaps
     * 
     * @param string $path
     * @return Sitemap
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns filename of sitemap file
     * 
     * @return string
     */
    private function getFilename() {
        return $this->filename;
    }

    /**
     * Sets filename of sitemap file
     * 
     * @param string $filename
     * @return Sitemap
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Returns current item count
     *
     * @return int
     */
    private function getCurrentItem() {
        return $this->current_item;
    }

    /**
     * Increases item counter
     * 
     */
    private function incCurrentItem() {
        $this->current_item = $this->current_item + 1;
    }

    /**
     * Returns current sitemap file count
     *
     * @return int
     */
    private function getCurrentSitemap() {
        return $this->current_sitemap;
    }

    /**
     * Increases sitemap file count
     * 
     */
    private function incCurrentSitemap() {
        $this->current_sitemap = $this->current_sitemap + 1;
    }

    /**
     * Prepares sitemap XML document
     * 
     */
    private function startSitemap( $type=array(), $parent_tag='urlset' ) 
    {
        $this->setWriter(new XMLWriter());
        $this->getWriter()->openURI('php://output');
        $this->getWriter()->startDocument('1.0', 'UTF-8');
        $this->getWriter()->setIndent(true);
        
        self::$comments['header'] = array();
        self::$comments['header'][] = 'Generated with Premium SEO Pack Wordpress Plugin [ http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437 ] '
            . PHP_EOL . 'by AA-Team [ http://codecanyon.net/user/AA-Team/portfolio ]'
            //. PHP_EOL . 'This XSLT template is released under the GPL and free to use.'
            . PHP_EOL . 'If you have problems with your sitemap please visit the Premium SEO Pack Wordpress Plugin Support Forum [ http://support.aa-team.com/ ].';
                    
        self::$comments['footer'] = array();            
        self::$comments['footer'][] = 'This is a XML Sitemap which is supposed to be processed by search engines which follow the XML Sitemap standard like Google and Bing.'
            . PHP_EOL . 'It was generated using the Blogging-Software WordPress [http://wordpress.org/]'
            . PHP_EOL . 'and the Premium SEO Pack Wordpress Plugin [http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437]'
            . PHP_EOL . 'You can find more information about XML sitemaps on sitemaps.org [http://sitemaps.org]'
            . PHP_EOL . 'and Google\'s list of sitemap programs [http://code.google.com/p/sitemap-generators/wiki/SitemapGenerators].'
            . PHP_EOL . 'Generated-on=' . ( date("F j, Y, g:i a") );

        $this->getWriter()->writeComment( implode(PHP_EOL, self::$comments['header']) );
        
        if ( self::$has_stylesheet ) {
            $this->getWriter()->writePI("xml-stylesheet", 'type="text/xml" href="' . ($this->module_folder . 'xsl/'.self::$xslfile.'.xsl') . '"');
        }

        $this->getWriter()->startElement($parent_tag);
        foreach (self::$SCHEMA as $schema_key => $schema_value) {
            $this->getWriter()->writeAttribute($schema_key, $schema_value);            
        }

        if ( !empty($type) && in_array('images', $type) )
            $this->getWriter()->writeAttribute('xmlns:image', self::SCHEMA_IMG);
        if ( !empty($type) && in_array('videos', $type) )
            $this->getWriter()->writeAttribute('xmlns:video', self::SCHEMA_VIDEO);
    }

    /**
     * Finalizes tags of sitemap XML document.
     *
     */
    private function endSitemap() {
        $this->getWriter()->endElement();
        
        $this->getWriter()->writeComment( implode(PHP_EOL, self::$comments['footer']) );

        $this->getWriter()->endDocument();
    }
    
    /**
     * write cdata Element
     *
     */
    private function cdataElement( $key='', $val='', $forceEmpty=true ) {
        if ( !$forceEmpty ) return false;

        $val = '<![CDATA[' . $val . ']]>';
        $this->getWriter()->writeElement( $key, $val );
    }
    
    
    /**
     * sitemap website Items: posts | pages
     * 
     */
    //print xml sitemap!
    private function print_sitemap( $sitemap_type, $post_type='post,page' )
    {
        $siteurl = get_option('siteurl');
        $site_parts = parse_url($siteurl);  
        //$this->setDomain( $site_parts['scheme'] . '://' . $site_parts['host'] );
        $this->setDomain( $siteurl );
        $this->setPath( '/' );
        $this->setFilename( 'sitemap' );
        
        $general_sitemap_settings = $this->settings; //$this->the_plugin->get_theoption('psp_sitemap');
        $post_type = array_merge(explode(',', $post_type), $this->get_sitemap_posttypes());
        $post_type = array_unique($post_type);

        $media = array();
        if ( $sitemap_type['mod'] == 'images' ) {
            $images = $this->get_images( implode(',', $post_type) );
            $images = $this->filter_images( $images );
            $media = array('images' => $images);

        } else if ( $sitemap_type['mod'] == 'videos' ) {
            $images = $this->get_videos( implode(',', $post_type) );
            $images_tmp = $this->filter_videos( $images );
            $images = !empty($images_tmp) && isset($images_tmp['videos']) ? $images_tmp['videos'] : array();
            $extrainfo = !empty($images_tmp) && isset($images_tmp['extrainfo']) ? $images_tmp['extrainfo'] : array();
            $media = array('videos' => $images);

        } else {
            if ( $sitemap_type['mod'] == 'posttype' ) {
                if ( $general_sitemap_settings['include_img']=='yes' ) {
                    $images = $this->get_images( $post_type );
                    $images = $this->filter_images( $images );
                    $media = array('images' => $images);
                }
            }
        }

        if ( in_array($sitemap_type['mod'], array('images', 'videos')) ) {
            $items = $this->get_items_posttype( implode(',', $post_type), $media );

        } else if ( $sitemap_type['mod'] == 'posttype' ) {
            $items = $this->get_items_posttype( $sitemap_type['submod']/*implode(',', $post_type)*/, $media, array(
                'page' => $sitemap_type['paginate'],
            ) );

        } else {
            $__func = "get_items_{$sitemap_type['mod']}";
            $items = $this->$__func( $sitemap_type );
        }

        // no items => empty sitemap
        if ( empty($items) ) {
            $this->build_empty_sitemap();
            return;
        }
   
        $valid = 0;        
        if( !empty($items) ) {

            //$this->addItem( $this->home_url, '1.0', 'daily', 'Today' );
  
            foreach ($items as $key => $value) {

                //$sitemap_isincluded = get_post_meta( $value->ID, 'psp_sitemap_isincluded', true );
                //verify per item is included!
                //if ( isset($sitemap_isincluded) && trim($sitemap_isincluded) == "never_include" ) continue 1;

                //$sitemap_settings = get_post_meta( $value->ID, 'psp_meta', true );

                // permalink
                $pms = array();
                if ( isset($sitemap_type['submod']) && $sitemap_type['submod'] == 'psp_locations' ) {
                    $__s = $this->the_plugin->getAllSettings( 'array', 'local_seo' );
                    if ( isset($__s['slug']) && !empty($__s['slug']) ) {
                        $pms['slug'] = $__s['slug'];
                    }
                }
                $permalink = $this->get_permalink($sitemap_type, $value, $pms);
                
                // execlude external URL rewrites by other plugins
                if ( false === strpos( $permalink, $this->home_url ) ) {
                    continue 1;
                }

                // images
                $__itemImg = array(); $mediaInfo = array();
                // $general_sitemap_settings['include_img']=='yes' | $general_sitemap_settings['include_video']=='yes'
                if ( $sitemap_type['mod'] == 'images' ) {

                    if ( isset($images[ $value->ID ]) && is_array($images[ $value->ID ]) && count($images[ $value->ID ])>0 )
                        $__itemImg = $images[ $value->ID ];

                    $__itemImg2 = $this->filter_item_image( $value ); //retrieve post images from post content
                    $__itemImg = array_merge( $__itemImg, $__itemImg2 );
                    $__itemImg = array_values( array_unique($__itemImg) );
                } else if ( $sitemap_type['mod'] == 'videos' ) {

                    $__extrainfo = array();
                    if ( isset($images[ $value->ID ]) && is_array($images[ $value->ID ]) && count($images[ $value->ID ])>0 ) {
                        $__itemImg = $images[ $value->ID ];
                        $__extrainfo = $extrainfo[ $value->ID ];
                    }
        
                    $__itemImg2 = $this->filter_item_video( $value->post_content ); //retrieve post images from post content
                    $__itemImg = array_merge_recursive( $__itemImg, $__itemImg2 );
    
                    // clean duplicates
                    if ( !empty($__itemImg) ) {
                        foreach ( $__itemImg as $kk => $vv) {
                            $__itemImg[ "$kk" ] = array_values( array_unique($__itemImg[ "$kk" ]) );
                        }
                    }
                    $mediaInfo = $this->getVideosInfo( $__itemImg, $value, $__extrainfo, self::VIDEOAPI_FORCE_SITEMAP );
                }
                
                $lastmod = $this->get_lastmod($sitemap_type, $value);
                
                if ( in_array($sitemap_type['mod'], array('images', 'videos')) ) {
                    $priority = null;
                    $changefreq = null;
                } else {
                    // priority
                    $priority = $this->get_priority($sitemap_type, $value);

                    // change frequency
                    $changefreq = $this->get_frequency($sitemap_type, $value);
                }
  
                // add item to sitemap
                if ( in_array($sitemap_type['mod'], array('images', 'videos')) ) {
                    if ( !empty($__itemImg) ) {
                        $this->addItem( $sitemap_type, $permalink, $priority, $changefreq, $lastmod, $__itemImg, $mediaInfo );
                        $valid++;
                    }
                } else {
                    $this->addItem( $sitemap_type, $permalink, $priority, $changefreq, $lastmod, $__itemImg, $mediaInfo );
                    $valid++;
                }
            }

            if ( $valid ) {
                $this->endSitemap();
            }
            
            // no valid items => empty sitemap
            if ( !$valid ) {
                $this->build_empty_sitemap();
            }
        }
    }

    private function build_empty_sitemap() {
        $this->startSitemap( array() );
        $this->incCurrentSitemap();
        if ($this->getWriter() instanceof XMLWriter) {
            $this->endSitemap();
        }
    }
    
    /**
     * Adds an item to sitemap
     *
     * @param string $loc URL of the page. This value must be less than 2,048 characters. 
     * @param string $priority The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.
     * @param string $changefreq How frequently the page is likely to change. Valid values are always, hourly, daily, weekly, monthly, yearly and never.
     * @param string|int $lastmod The date of last modification of url. Unix timestamp or any English textual datetime description.
     * @return Sitemap
     */
    public function addItem($sitemap_type, $loc, $priority = self::DEFAULT_PRIORITY, $changefreq = self::DEFAULT_FREQUENCY, $lastmod = NULL, $media=array(), $mediaInfo=array()) {
        if (($this->getCurrentItem() % self::ITEM_PER_SITEMAP) == 0) {
            if ($this->getWriter() instanceof XMLWriter) {
                $this->endSitemap();
            }
            
            $start_media = array();
            if ( in_array($sitemap_type['mod'], array('posttype', 'images')) ) {
                $start_media = array('images');
            } else if ( $sitemap_type['mod'] == 'videos' ) {
                $start_media = array('videos');
            }
            $this->startSitemap( $start_media );

            $this->incCurrentSitemap();
        }
        $this->incCurrentItem();
        $this->getWriter()->startElement('url');
        //$this->getWriter()->writeElement('loc', $this->getDomain() . $loc);
        $this->getWriter()->writeElement('loc', $loc);

        if ( $sitemap_type['mod'] == 'images' ) {
            if (isset($media) && is_array($media) && count($media)>0) {
                $this->addItemImages($media);
            }
            
        } else if ( $sitemap_type['mod'] == 'videos' ) {
            if (isset($media) && is_array($media) && count($media)>0) {
                $this->addItemVideos($media, $mediaInfo);
            }
        
        } else if ( in_array($sitemap_type['mod'], array('site', 'external', 'misc', 'author', 'archive', 'taxonomy', 'posttype')) ) {
            if (isset($media) && is_array($media) && count($media)>0) {
                $this->addItemImages($media);
            }
        
            $this->getWriter()->writeElement('priority', $priority);
            if ($changefreq) {
                $this->getWriter()->writeElement('changefreq', $changefreq);
            }
            if ($lastmod) {
                //$lastmod = $this->get_lastmod_old($lastmod);
                $this->getWriter()->writeElement('lastmod', $lastmod);
            }
        }

        $this->getWriter()->endElement();
        return $this;
    }
    
    /**
     * Adds an item images to sitemap
     *
     * @param string $images array of item images!
     * @return Sitemap images
     */
    private function addItemImages($images) {
        foreach ($images as $v) {
            $this->getWriter()->startElement('image:image');
            $this->getWriter()->writeElement('image:loc', $v);
            $this->getWriter()->endElement();
        }
        return $this;
    }
    
    /**
     * Adds an item videos to sitemap
     *
     * @param string $videos array of item videos!
     * @return Sitemap videos
     */
    private function addItemVideos($videos, $videosInfo) {
        foreach ( $videos as $k => $v ) {

            if ( is_array($v) && !empty($v) ) {
                foreach ( $v as $key => $val ) {
                    
                    $val = $videosInfo[ "$k" ][ "$key" ];
                    
                    if ( !isset($val['status']) || (isset($val['status']) && $val['status']=='invalid') )
                        continue 1;
                    if ( !$this->isVideoValid( $val ) )
                        continue 1;
                    
                    $this->getWriter()->startElement('video:video');

                    if ( empty($val['player_loc']) )
                        $val['player_loc'] = $val['content_loc'];
                    if ( !empty($val['player_loc']) ) {
                        // You must specify at least one of <video:player_loc> or <video:content_loc> .A URL pointing to a player for a specific video. Usually this is the information in the src element of an <embed> tag and should not be the same as the content of the <loc> tag. The optional attribute allow_embed specifies whether Google can embed the video in search results. Allowed values are Yes or No. The optional attribute autoplay has a user-defined string (in the example above, ap=1) that Google may append (if appropriate) to the flashvars parameter to enable autoplay of the video. For example: <embed src="http://www.example.com/videoplayer.swf?video=123" autoplay="ap=1"/>. Example: Dailymotion: http://www.dailymotion.com/swf/x1o2g
                        $this->getWriter()->startElement('video:player_loc');
                        $this->getWriter()->writeAttribute('allow_embed', 'yes');
                        $this->getWriter()->writeAttribute('autoplay', 'ap=1');
                        $this->getWriter()->writeRaw( (string) $val['player_loc'] );
                        $this->getWriter()->endElement();
                    }
                    if ( !empty($val['author']) ) // The video uploader's name. Only one <video:uploader> is allowed per video. The optional attribute info specifies the URL of a webpage with additional information about this uploader. This URL must be on the same domain as the <loc> tag.
                        $this->getWriter()->writeElement('video:uploader', (string) $val['author']);
                    if ( !empty($val['publish_date']) ) // The date the video was first published, in W3C format. Acceptable values are complete date (YYYY-MM-DD) and complete date plus hours, minutes and seconds, and timezone (YYYY-MM-DDThh:mm:ss+TZD). For example, 2007-07-16T19:20:30+08:00.
                        $this->getWriter()->writeElement('video:publication_date', (string) $val['publish_date']);

                    if ( !empty($val['thumbnail']) ) // mandatory: A URL pointing to the video thumbnail image file. Images must be at least 160 x 90 pixels and at most 1920x1080 pixels. We recommend images in .jpg, .png, or. gif formats.
                        $this->getWriter()->writeElement('video:thumbnail_loc', (string) $val['thumbnail']);
                    if ( !empty($val['title']) ) // mandatory: The title of the video. Maximum 100 characters. The title must be in plain text only, and any HTML entities should be escaped or wrapped in a CDATA block.
                        $this->cdataElement('video:title', (string) $val['title']);
                    if ( !empty($val['description']) ) // mandatory: The description of the video. Maximum 2048 characters. The description must be in plain text only, and any HTML entities should be escaped or wrapped in a CDATA block.
                        $this->cdataElement( 'video:description', (string) $val['description'] );
                    if ( !empty($val['duration']) ) // The duration of the video in seconds. Value must be between 0 and 28800 (8 hours).
                        $this->getWriter()->writeElement('video:duration', (string) $val['duration']);
                    if ( !empty($val['ratings']) ) // The rating of the video. Allowed values are float numbers in the range 0.0 to 5.0.
                        $this->getWriter()->writeElement('video:rating', (string) $val['ratings']);
                    if ( !empty($val['view_count']) ) // The number of times the video has been viewed.
                        $this->getWriter()->writeElement('video:view_count', (string) $val['view_count']);

                    if ( is_array($val['tags']) && !empty($val['tags']) ) {
                        // A tag associated with the video. Tags are generally very short descriptions of key concepts associated with a video or piece of content. A single video could have several tags, although it might belong to only one category. For example, a video about grilling food may belong in the Grilling category, but could be tagged "steak", "meat", "summer", and "outdoor". Create a new <video:tag> element for each tag associated with a video. A maximum of 32 tags is permitted.
                        foreach ( $val['tags'] as $tag )
                            $this->cdataElement('video:tag', (string) $tag);
                    }
                    if ( is_array($val['categories']) && !empty($val['categories']) ) {
                        // The video's category. For example, cooking. The value should be a string no longer than 256 characters. In general, categories are broad groupings of content by subject. Usually a video will belong to a single category. For example, a site about cooking could have categories for Broiling, Baking, and Grilling.
                        foreach ( $val['categories'] as $category )
                            $this->cdataElement('video:category', (string) $category);
                    }

                    $this->getWriter()->endElement();
                }
            }
        }
        return $this;
    }
    

    /**
     * Build sitemap index
     */
    //print xml sitemap!
    public function print_sitemap_index() {
        $s = $this->settings;
        $s_standard = isset($s['standard_content']) ? $s['standard_content'] : array(
            //'site', 'post', 'page', 'category', 'post_tag', 'archive', 'author'
        );
        $s_pt = isset($s['post_types']) ? $s['post_types'] : array();
        $s_tax = isset($s['taxonomies']) ? $s['taxonomies'] : array();
        
        $global_items_per_page = isset($this->settings['global_items_per_page']) && !empty($this->settings['global_items_per_page']) ? (int) $this->settings['global_items_per_page'] : 0;

        $blogUpdate = strtotime( get_lastpostmodified('blog') );
        //$blogUpdate = $this->get_lastmod_old($blogUpdate);

        $siteurl = get_option('siteurl');
        $site_parts = parse_url($siteurl);  
        //$this->setDomain( $site_parts['scheme'] . '://' . $site_parts['host'] );
        $this->setDomain( $siteurl );
        $this->setPath( '/' );
        $this->setFilename( 'sitemap' );

        $valid = 0;
        
        // standard content: 'site', 'external', 'misc', 'archive', 'author'
        foreach ( array('site', 'archive', 'author') as $sitemap_mod ) {
            if ( !in_array($sitemap_mod, $s_standard) ) {
                continue 1;
            }
            $sitemap_type = array('mod' => $sitemap_mod);
            $permalink = $this->get_sitemap_url( $sitemap_type );
            $this->addItem_index( $permalink, null, null, $this->get_lastmod($sitemap_type, (object) array('__lastmod' => $blogUpdate)) );
            $valid++;
        }

        // post types (including post | page)
        $post_types = $this->get_sitemap_posttypes();
        if ( !empty($post_types) ) {
            
            $post_types_count = $this->get_items_posttype( implode(',', $post_types), array(), array('count' => 'all') );
            foreach ( $post_types as $post_type ) {
    
                if ( isset($post_types_count["$post_type"]) && !empty($post_types_count["$post_type"]) ) {
                    $parts_nb = $this->get_parts_number($post_types_count["$post_type"], $global_items_per_page);
                    for($cc=1; $parts_nb>-1 && $cc<=$parts_nb; $cc++) {
          
                        $sitemap_type = array('mod' => 'posttype', 'submod' => $post_type, 'paginate' => $cc);
                        $permalink = $this->get_sitemap_url( $sitemap_type );
                        $this->addItem_index( $permalink, null, null, $this->get_lastmod($sitemap_type, (object) array('__lastmod' => $blogUpdate)) );
                        $valid++;
                    }
                }
            }
        }

        // taxonomies (including category | tag)
        $taxonomies = $this->get_sitemap_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {

            $sitemap_type = array('mod' => 'taxonomy', 'submod' => $taxonomy);
            $permalink = $this->get_sitemap_url( $sitemap_type );
            $this->addItem_index( $permalink, null, null, $this->get_lastmod($sitemap_type, (object) array('__lastmod' => $blogUpdate)) );
            $valid++;
        }

        if ( $valid ) {
            $this->endSitemap();
        } else {
            $this->build_empty_sitemap();
        }
    }
    
    /**
     * Adds an item to sitemap
     *
     * @param string $loc URL of the page. This value must be less than 2,048 characters. 
     * @param string $priority The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.
     * @param string $changefreq How frequently the page is likely to change. Valid values are always, hourly, daily, weekly, monthly, yearly and never.
     * @param string|int $lastmod The date of last modification of url. Unix timestamp or any English textual datetime description.
     * @return Sitemap
     */
    public function addItem_index($loc, $priority = NULL, $changefreq = NULL, $lastmod = NULL) {
        if (($this->getCurrentItem() % self::ITEM_PER_SITEMAP) == 0) {
            if ($this->getWriter() instanceof XMLWriter) {
                $this->endSitemap();
            }
            $this->startSitemap( array(), 'sitemapindex' );
            $this->incCurrentSitemap();
        }
        $this->incCurrentItem();
        $this->getWriter()->startElement('sitemap');
        //$this->getWriter()->writeElement('loc', $this->getDomain() . $loc);
        $this->getWriter()->writeElement('loc', $loc);

        if ($lastmod) {
            //$lastmod = $this->get_lastmod_old($lastmod);
            $this->getWriter()->writeElement('lastmod', $lastmod);
        }

        $this->getWriter()->endElement();
        return $this;
    }


    /**
     * Sitemap related utils
     */
    private function get_home_url() {
        $baseURL = get_bloginfo('url');
        $baseURL = trailingslashit($baseURL);
        return $baseURL;    
    }
    private function get_sitemap_url($pms) {
        $ret = array();
        
        $baseURL = $this->home_url;
        
        $__pms = $pms;
        if ( isset($__pms['paginate']) ) {
            $__pms['paginate'] = 'p' . $__pms['paginate'];
        }
        $__pms = array_merge( array('prefix' => 'sitemap'), $__pms );
        
        $ret = $baseURL . implode('-', $__pms) . '.xml';
        return $ret;
    }

    private function get_sitemap_posttypes() {
        $general_sitemap_settings = $this->settings;
        
        $standard_content = (array) $general_sitemap_settings['standard_content'];
        $custom_posttypes = (array) $general_sitemap_settings['post_types'];

        $post_type = (array) array_intersect( array('post', 'page'), $standard_content );       
        $post_type = array_merge( $post_type, $custom_posttypes );
        return $post_type;
    }
    
    private function get_sitemap_taxonomies() {
        $general_sitemap_settings = $this->settings;
        
        $standard_content = (array) $general_sitemap_settings['standard_content'];
        $custom_taxonomies = (array) $general_sitemap_settings['taxonomies'];

        $taxonomies = (array) array_intersect( array('category', 'post_tag'), $standard_content );       
        $taxonomies = array_merge( $taxonomies, $custom_taxonomies );
        return $taxonomies;
    }
    
    private function get_parts_number( $total, $limit ) {
        if ( empty($total) ) return -1;
        if ( empty($limit) ||  $total < $limit ) return 1;
        return ceil( $total / $limit );
    }

    private function is_using_permalinks() {
        $perma_struct = get_option('permalink_structure');
        return !empty($perma_struct);
    }

    private function get_permalink($sitemap_type, $post, $pms=array()) {
        if ( isset($post->__permalink) ) return $post->__permalink;
        
        $post_type = $post->post_type;

        //$permalink = get_permalink( $value->ID );
        if( $post_type == 'page' || $post_type == 'post' ) {
            $permalink = get_permalink( $post->ID );
        } else {  
            $permalink = get_post_permalink( $post->ID ); 
            $permalink = explode('=', $permalink);
            $permalink = $permalink[1];
            $permalink = explode('&', $permalink);
            $permalink_modified = $permalink[0];
                    
            $permalink_original = get_permalink( $post->ID );
            $permalink_original = explode('/', $permalink_original);
            $permalink_original = array_filter($permalink_original);
            end($permalink_original);
                    
            $custom_post_type_permalink = untrailingslashit($this->home_url).'/'.$permalink[0].'/'.end($permalink_original);
                    
            $permalink = trailingslashit( $custom_post_type_permalink );
            
            if( $post_type == 'psp_locations' ) {
                if ( isset($pms['slug']) && !empty($pms['slug']) ) {
                    $permalink = str_replace('psp_locations/', $pms['slug'].'/', $permalink);
                }
            }
        }
        return $permalink;
    }
    
    private function get_priority($sitemap_type, $post) {
        if ( isset($post->__priority) ) return $post->__priority;

        $s = $this->settings;
        
        $sm = array(); $post_type = $sitemap_type['mod'];
        if ( in_array($post_type, array('posttype')) ) {
            $sm = get_post_meta( $post->ID, 'psp_meta', true );
            $post_type = $post->post_type;
        } else if ( $post_type == 'taxonomy' ) {
            $post_type = !empty($sitemap_type['submod']) && in_array($sitemap_type['submod'], array('category', 'post_tag')) ? $sitemap_type['submod'] : $post_type;
        }
  
        $priority = self::DEFAULT_PRIORITY;
        if ( isset($sm['priority']) ){
            $sm['priority'] = trim($sm['priority']);
            if ( !empty($sm['priority']) && $sm['priority']!='-') { 
                $priority = $sm['priority'];
            }
        } elseif ( isset($s['priority'][$post_type]) && trim($s['priority'][$post_type]) != "") {
            $priority = $s['priority'][$post_type];
        }
        return $priority;
    }
    
    private function get_frequency($sitemap_type, $post) {
        if ( isset($post->__frequency) ) return $post->__frequency;
        
        $s = $this->settings;
  
        $sm = array(); $post_type = $sitemap_type['mod'];
        if ( in_array($post_type, array('posttype')) ) {
            $sm = get_post_meta( $post->ID, 'psp_meta', true );
            $post_type = $post->post_type;
        } else if ( $post_type == 'taxonomy' ) {
            $post_type = !empty($sitemap_type['submod']) && in_array($sitemap_type['submod'], array('category', 'post_tag')) ? $sitemap_type['submod'] : $post_type;
        }
  
        $changefreq = self::DEFAULT_FREQUENCY;
        if( isset($sm['changefreq']) ){
            $sm['changefreq'] = trim($sm['changefreq']);
            if ( !empty($sm['changefreq']) && $sm['changefreq']!='-') { 
                $changefreq = $sm['changefreq'];
            }
        } elseif ( isset($s['changefreq'][$post_type]) && trim($s['changefreq'][$post_type]) != "" && trim($s['changefreq'][$post_type]) != "-") {
            $changefreq = $s['changefreq'][$post_type];
        }
        return $changefreq;
    }

    // Unix timestamp or any English textual datetime description: string Year-Month-Day formatted date
    private function get_lastmod_old($date) {
        if (ctype_digit($date)) {
            return date('Y-m-d\TH:i:s+00:00', $date);
        } else {
            $date = strtotime($date);
            return date('Y-m-d\TH:i:s+00:00', $date);
        }
    }

    private function get_lastmod($sitemap_type, $post) {
        if ( isset($post->__lastmod) ) {
            return $this->format_lastmod($post->__lastmod, true);
        }
        
        $s = $this->settings;
   
        $lastmod  = '';

        $use_gmt = isset($s['lastmod_use_gmt']) && $s['lastmod_use_gmt'] == 'yes' && function_exists('date_default_timezone_set')
            ? true : false;
        $gmt_offset = (float) get_option('gmt_offset');

        $post_modified_field = $use_gmt ? 'post_modified_gmt' : 'post_modified';
        $post_date_field = $use_gmt ? 'post_date_gmt' : 'post_date';

        if ( isset($post->lastmod) ) { // local time

            $lastmod = strtotime($post->lastmod);
            $lastmod = $use_gmt ? ( $lastmod - $gmt_offset * 3600 ) : $lastmod;
        } else if ( isset($post->$post_modified_field) ) {
            
            $lastmod = strtotime($post->$post_modified_field);
        }
        $post_date = isset($post->$post_date_field) ? strtotime($post->$post_date_field) : '';

        $lastmod  = empty($lastmod) || $lastmod < 0 ? $post_date : $lastmod;

        return !empty($lastmod) && $lastmod > 0 ? $this->format_lastmod($lastmod, true) : '';
    }

    private function format_lastmod($lastmod, $is_local=false) {
        $use_gmt = isset($s['lastmod_use_gmt']) && $s['lastmod_use_gmt'] == 'yes' && function_exists('date_default_timezone_set')
            ? true : false;
        $gmt_offset = (float) get_option('gmt_offset');

        if ( empty($lastmod) || $lastmod < 0 ) return '';

        // < PHP 5.1.0
        if ( !function_exists('date_default_timezone_set') ) {
            return $use_gmt ? gmdate('Y-m-d\TH:i:s\Z', (int) $lastmod) : date('c', (int) $lastmod);
        }

        // local time => make it GMT
        $lastmod = $is_local && $use_gmt ? $lastmod - $gmt_offset * 3600 : $lastmod;

        $current_timezone = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $date = date('c', (int) $lastmod);
        if ($use_gmt) {
            return str_replace('+00:00', 'Z', $date); // use GMT
        }

        // calculate the UTC designator, e.g. '+07:00'
        $sign = $gmt_offset > 0 ? '+' : '-';
        $gmt_offset = abs($gmt_offset);

        $hour   = intval($gmt_offset);
        $minute = ( $gmt_offset - $hour ) * 60;

        return str_replace('+00:00', $sign . sprintf('%02d:%02d', $hour, $minute), $date);
    }

    private function get_items_author( $sitemap_type ) {
        global $wpdb;
        
        $s = $this->settings;
        $submod = $sitemap_type['submod'];
        
        // post types (including post | page)
        $post_types = $this->get_sitemap_posttypes();
        $post_type = implode(',', $post_types);

        $allowed_roles = isset($this->settings['author_roles']) && !empty($this->settings['author_roles']) ? $this->settings['author_roles'] : array('administrator', 'editor', 'author', 'contributor', 'subscriber');

        $sql = "
            SELECT
                u.ID,
                u.user_nicename,
                um.meta_value as user_role,
                MAX(p.post_modified_gmt) as lastmod,
                MAX(p.post_date) as post_date,
                MAX(p.post_date_gmt) as post_date_gmt,
                MAX(p.post_modified) as post_modified,
                MAX(p.post_modified_gmt) as post_modified_gmt
            FROM " . $wpdb->users . " as u
            INNER JOIN " . $wpdb->usermeta . " as um
                ON um.user_id = u.ID
            INNER JOIN " . $wpdb->posts . " as p
                ON p.post_author = u.ID
            WHERE 1=1
                AND ( 1=1 %s %s ) "/*AND p.post_status = 'publish'
                AND post_password = ''
                AND post_type = 'post'*/."
                AND um.meta_key = '" . $wpdb->prefix . "capabilities" . "'
            GROUP BY
                u.ID,
                u.user_nicename,
                um.meta_value
            ORDER BY lastmod DESC;";
            
        $clause = $this->clause_post_type($post_type, 'p');
        
        //sitemap always | never included items & globale exclude items & categories belonging items
        $itemAllowedClause = $this->itemIsAllowed($post_type, 'p');

        $sql = sprintf($sql, $clause, $itemAllowedClause);
        //var_dump('<pre>',$sql,'</pre>'); die;
        
        $res = $wpdb->get_results($sql);
 
        if ( !empty($res) ) {
            foreach ($res as $key => $val) {
  
                $user_role = maybe_unserialize($val->user_role);
                $user_role = array_keys($user_role);
                $user_role = reset($user_role); 
  
                if ( !in_array($user_role, $allowed_roles) ) continue 1;
            
                $res["$key"] = (object) array_merge( (array) $res["$key"], array(
                    '__permalink'               => get_author_posts_url($val->ID, $val->user_nicename),
                    //'__lastmod'                 => '',
                    //'__priority'                => '',
                    //'__frequency'               => '',
                ));
            }
        }
 
        //var_dump('<pre>', $sql, count($res), '</pre>'); die('debug...');
        return $res;
    }

    private function get_items_archive( $sitemap_type ) {
        global $wpdb;
        
        $s = $this->settings;
        $submod = $sitemap_type['submod'];
        $archive_type = isset($s['archive_type']) ? $s['archive_type'] : 'monthly';

        // post types (including post | page)
        $post_types = $this->get_sitemap_posttypes();
        $post_type = implode(',', $post_types);

        $now = current_time('mysql', true);

        $sql = "
            SELECT
                YEAR(p.post_date) as year,
                " . ( $archive_type == 'monthly' ? " MONTH(p.post_date) as month, " : "" ) . "
                MAX(p.post_modified_gmt) as lastmod,
                MAX(p.post_date) as post_date,
                MAX(p.post_date_gmt) as post_date_gmt,
                MAX(p.post_modified) as post_modified,
                MAX(p.post_modified_gmt) as post_modified_gmt,
                count(p.ID) as posts_found
            FROM
                " . $wpdb->posts . " as p
            WHERE 1=1
                AND p.post_modified_gmt < '$now'
                AND ( 1=1 %s %s ) "/*AND post_status = 'publish'
                AND post_password = ''
                AND post_type = 'post'*/."
            GROUP BY
               year " . ( $archive_type == 'monthly' ? ", month" : "" ) . "
            ORDER BY
                lastmod DESC;";
            
        //$clause = $this->clause_post_type($post_type, 'p');
        $clause = $this->clause_post_type('post', 'p');
        
        //sitemap always | never included items & globale exclude items & categories belonging items
        $itemAllowedClause = $this->itemIsAllowed($post_type, 'p');

        $sql = sprintf($sql, $clause, $itemAllowedClause);
        //var_dump('<pre>',$sql,'</pre>'); die;
        
        $res = $wpdb->get_results($sql);
 
        if ( !empty($res) ) {
            //$years = array();
            foreach ($res as $key => $val) {

                if ( $val->posts_found <=0 ) continue 1;
                
                if ( $archive->month == date("n") && $archive->year == date("Y") ) {
                    // archive is the current month one!
                }
                
                //$years["{$val->year}"] = $val->year;
                $res["$key"] = (object) array_merge( (array) $res["$key"], array(
                    '__permalink'               => $archive_type == 'monthly' ? get_month_link($val->year, $val->month) : get_year_link($val->year),
                    //'__lastmod'                 => '',
                    //'__priority'                => '',
                    //'__frequency'               => '',
                ));
            }
            
            // add years
            /*$cc = count($res);
            foreach ($years as $year) {
                $res["$cc"] = (object) array_merge( (array) $res["$cc"], array(
                    '__permalink'               => get_year_link($year),
                    //'__lastmod'                 => '',
                    //'__priority'                => '',
                    //'__frequency'               => '',
                ));
                $cc++;
            }*/
        }
 
        //var_dump('<pre>', $sql, count($res), '</pre>'); die('debug...');
        return $res;
    }

    private function get_items_taxonomy( $sitemap_type ) {
        global $wpdb;
        
        $s = $this->settings;
        $taxonomy = $sitemap_type['submod'];
        $taxonomies_zero_posts = isset($s['taxonomies_zero_posts']) && $s['taxonomies_zero_posts'] == 'yes' ? true : false;

        // taxonomies (including category | post_tag)
        $taxonomies = $this->get_sitemap_taxonomies();
        
        // excluded categories
        $exclude_categories = $this->global_excluded_categories();
        
        // post types (including post | page)
        $post_types = $this->get_sitemap_posttypes();
        $post_type = implode(',', $post_types);
        
        // used to filter terms which relate to valid posts
        $sql_posts = "
            SELECT
                MAX(p.post_date) as post_date,
                MAX(p.post_date_gmt) as post_date_gmt,
                MAX(p.post_modified) as post_modified,
                MAX(p.post_modified_gmt) as post_modified_gmt,
                tt.term_id
            FROM " . $wpdb->term_relationships . " as tr
            INNER JOIN " . $wpdb->posts . " as p
                ON tr.object_id = p.ID
            INNER JOIN " . $wpdb->term_taxonomy . " as tt
                ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE 1=1
                AND tt.taxonomy = '%s'
                AND tt.count > 0
                AND ( 1=1 %s %s ) "/*AND p.post_status = 'publish'
                AND post_password = ''
                AND post_type = 'post'*/."
            GROUP BY tt.term_id
            ORDER BY tt.term_id DESC;";
            
        $clause = $this->clause_post_type($post_type, 'p');
        
        //sitemap always | never included items & globale exclude items & categories belonging items
        $itemAllowedClause = $this->itemIsAllowed($post_type, 'p');

        $sql_posts = sprintf($sql_posts, $taxonomy, $clause, $itemAllowedClause);
        //var_dump('<pre>',$sql_posts,'</pre>'); die;
        
        $res_posts = $wpdb->get_results($sql_posts);
        if (empty($res_posts)) return array();
        
        // get available terms/taxonomies
        $sql_terms = "
            SELECT t.*, tt.*
            FROM " . $wpdb->terms  . " as t
            INNER JOIN " . $wpdb->term_taxonomy . " as tt
                ON t.term_id = tt.term_id
            WHERE 1=1 
                AND tt.taxonomy = '%s'
                AND tt.count > 0
            ORDER BY t.term_id DESC;";
            
        $sql_terms = sprintf($sql_terms, $taxonomy);
        //var_dump('<pre>',$sql_terms,'</pre>'); die;
            
        $res = $wpdb->get_results($sql_terms);
        if (empty($res)) return array();
        
        // all fine => build an array with term_id as key
        $terms = array();
        foreach ($res as $val) {
            $term_id = isset($val->term_id) && !empty($val->term_id) ? $val->term_id : 0;
            if ( !empty($term_id) ) {
                $terms["$term_id"] = $val;   
            }
        }
        //var_dump('<pre>', $terms, '</pre>'); die('debug...');
        
        // all fine => build an array with term_id as key
        $term2post = array();
        foreach ($res_posts as $val) {
            $term_id = isset($val->term_id) && !empty($val->term_id) ? $val->term_id : 0;
            if ( !empty($term_id) && !isset($term2post["{$term_id}"]) ) {
                $term2post["$term_id"] = $val;   
            }
        }
        //var_dump('<pre>', $term2post, '</pre>'); die('debug...');
        
        $term2ancestry = (array) $this->build_terms_ancestry( $terms );
        //var_dump('<pre>', $term2ancestry, '</pre>'); die('debug...'); 

        foreach ($res as $key => $val) {
            
            $term_id = $val->term_id;
            $term_slug = $val->slug;
   
            // excluded categories
            if ( in_array($term_id, $exclude_categories) ) {
                unset($res["$key"]);
                continue 1;
            }
  
            if ( !$taxonomies_zero_posts && !isset($term2post["$term_id"]) ) {
                unset($res["$key"]);
                continue 1;
            }

            $res["$key"] = (object) array_merge( (array) $res["$key"], array(
                '__permalink'               => $this->get_term_link($val, $term2ancestry["$term_id"]),
                //'__lastmod'                 => '',
                //'__priority'                => '',
                //'__frequency'               => '',
            ));
        }
   
        //var_dump('<pre>', $res, '</pre>'); die('debug...');  
        //var_dump('<pre>', $sql_posts, $sql_terms, count($res), '</pre>'); die('debug...');
        return $res;
    }

    private function get_items_site( $sitemap_type ) {
        $s = $this->settings;
        $submod = $sitemap_type['submod'];

        $blogUpdate = strtotime( get_lastpostmodified('blog') );
        $permalink = $this->home_url;
        
        $valid = 0;
        if ( 'page' == get_option('show_on_front') && get_option('page_on_front') ) {
            $page_on_front = get_option('page_on_front');
            $post = get_post($page_on_front);
            if ( $post ) {
                $valid++;
            }
        } else {
            $valid++;
        }
 
        $res = array(); 
        if ( $valid ) {
            $res[] = (object) array(
                '__permalink'               => $permalink,
                '__lastmod'                 => $blogUpdate,
                //'__priority'                => '',
                //'__frequency'               => '',
            );
        }
        return $res;
    }
    
    
    /**
     * Extra
     */
    private function build_terms_ancestry($terms) {
        global $wpdb;
 
        if ( empty($terms) ) return array();
        
        $_term = array();
        foreach ($terms as $term) {

            $term_id = isset($term->term_id) ? (int) $term->term_id : 0;
            if ( empty($term_id) ) continue 1;

            !isset($_term["$term_id"]) ? $_term["$term_id"] = array() : ''; 

            $parent = isset($term->parent) ? $term->parent : 0;
            {
                while ( $parent > 0 ) {
                    $_term["$term_id"][] = $terms["$parent"]->slug;
                    $parent = isset($terms["$parent"], $terms["$parent"]->parent) ? $terms["$parent"]->parent : 0;
                }
            }
        }
        ksort($_term);
        return $_term;
    }

    private function get_term_link( $termObj, $ancestry=array() ) {
        $term_link = get_term_link($termObj, $termObj->taxonomy);
        
        //if ( $termObj->taxonomy == 'product_cat' ) return $term_link;

        $url_parts = $this->get_url_parts($term_link);
        if ( !isset($url_parts['query']) || empty($url_parts['query']) ) return $term_link; // already permalink?

        parse_str($url_parts['query'], $qp);
        if ( !isset($qp['term']) || !isset($qp['taxonomy']) || $qp['term'] != $termObj->slug ) return $term_link; // invalid link?

        $home_url = untrailingslashit($this->home_url);
        $_term_link = array();
        $_term_link[] = $home_url;

        if ( $qp['taxonomy'] == 'product_cat' ) {

            $permalinks        = get_option( 'woocommerce_permalinks' );
            //$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'woocommerce' ) : $permalinks['product_base'];
            $product_permalink = empty( $permalinks['category_base'] ) ? _x( 'product-category', 'slug', 'woocommerce' ) : $permalinks['category_base'];
            $_term_link[] = $product_permalink;
            
            if ( !empty($ancestry) ) {
                $ancestry = array_reverse($ancestry, true);
                $_term_link[] = implode('/', $ancestry);
            }
            $_term_link[] = $qp['term'] . '/';

        } else if ( $qp['taxonomy'] == 'product_tag' ) {
            $permalinks        = get_option( 'woocommerce_permalinks' );
            $product_permalink = empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'woocommerce' ) : $permalinks['tag_base'];
            $_term_link[] = $product_permalink;

            $_term_link[] = $qp['term'] . '/';
        } else {

            $_term_link[] = $qp['taxonomy'];
            $_term_link[] = $qp['term'] . '/';
        }
        return implode('/', $_term_link);
    } 


    /**
     * Utils
     *
     */
    private function is_url_relative( $url ) {
        return ( strpos( $url, 'http' ) !== 0 && strpos( $url, '//' ) !== 0 );
    }
    // ISO 8601 compatible duration! length <= 24 hours
    private function duration_iso_8601( $duration ) {

        $ret = array();
        $ret[] = 'PT';
        if ( $duration > 3600 ) { // hours
            $hours = floor( $duration / 3600 );
            $ret[] = $hours . 'H';
            $duration = $duration - ( $hours * 3600 );
        }
        if ( $duration > 60 ) { // minutes
            $minutes = floor( $duration / 60 );
            $ret[] = $minutes . 'M';
            $duration = $duration - ( $minutes * 60 );
        }
        if ( $duration > 0 ) { // seconds
            $ret[] = $duration . 'S';
        }
        return implode('', $ret);
    }

    private function strip_shortcode( $text ) {
        return preg_replace( '`\[[^\]]+\]`s', '', $text );
    }

    private function prepareForInList($v) {
        return "'".$v."'";
    }
    private function prepareForDbClean($v) {
        return trim($v);
    }
    
    private function get_url_parts( $src ) {
        return parse_url( $src );
    }
}
pspSeoSitemap::getInstance();