<?php
/*
* Define class pspMinify
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspMinify') != true) {
    class pspMinify {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
        private $module_folder_path = '';
		private $module = '';
		
		private $settings = array();

		static protected $_instance;
        
        private static $alias;
        public static $paths;
        private static $CACHE_CONFIG_LIFE = 14400; // cache lifetime in minutes
        private static $CACHE_FOLDER = null;
        private static $CACHE_FOLDER_SAVE_REMOTE = null;
        
        // minify scripts objects
        private static $script_cssmin = null;
        private static $script_jsmin = null;
        
        private static $assetsExcluded = array('js' => array(), 'css' => array()); // excluded assets list
        
        private $currentType = '';
        private $currentScriptUrl = '';
        private $remoteScripts = array();
		
		
        /*
        * Required __construct() & init methods!
        */
        public function __construct() {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Minify/';
            $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Minify/';
			$this->module = $this->the_plugin->cfg['modules']['Minify'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'Minify' );
            
            self::$alias = $this->the_plugin->alias . '-min-';
            if ( isset($this->settings['cache_expiration']) && !empty($this->settings['cache_expiration']) ) {
                self::$CACHE_CONFIG_LIFE = (int) $this->settings['cache_expiration'];
            }
            if ( !$this->build_cache_folder() ) {
                // todo: Error - could not create cache folder!
                return;
            }
			
			if ( $this->the_plugin->is_admin === true ) { // admin init!
			    $this->initAdmin();
			}
			
			if ( $this->the_plugin->is_admin !== true && !$this->is_login_page() ) { // frontend init!
				$this->initFrontend();
			}
        }
        
        private function initAdmin() {
        }
        
		private function initFrontend() {
			if ( !$this->the_plugin->verify_module_status( 'Minify' ) ) return; //module is inactive
			if ( !$this->the_plugin->capabilities_user_has_module('Minify') ) return; // module not in capabilities
			
            $minEnabled = array();
            if ( isset($this->settings['enable_minify']) && !empty($this->settings['enable_minify']) ) {
                $minEnabled = (array) $this->settings['enable_minify'];
            }
            if ( empty($minEnabled) ) return;

			$this->build_excluded_assets();
            
            // javascript files
            if ( in_array('js', $minEnabled) ) {
                //add_filter( 'print_scripts_array', array( $this, 'watch_js' ) );
                //add_action( 'wp_head', array( $this, 'join_js' ), 99998 );
                //add_action( 'wp_footer', array( $this, 'join_js_footer' ), 99999 );
                add_action( 'wp_print_scripts', array( $this, 'print_js' ), 20 );
            }

            // css files
            if ( in_array('css', $minEnabled) ) {
                //add_filter( 'print_styles_array', array( $this, 'watch_css' ) );
                //add_filter( 'wp_head', array( $this, 'join_css' ), 99999 );
                add_action( 'wp_print_styles', array( $this, 'print_css' ), 20 );
            }
            
            // remove browser pre-fetching of next page from link rel='next' tags
            remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
            remove_action( 'wp_head', 'adjacent_posts_rel_link' );
		}
        
        public function print_css() {
            $this->print_process('css', array('header'));
        }

        public function print_js() {
            $this->print_process('js', array('header', 'footer'));
        }
        
        private function print_process( $type, $pos ) {
            $this->currentType = $type;
  
            // get queue scripts and build the exclude assets lists
            switch ($type) {
                case 'css':
                    $wp_scripts = $this->get_wp_styles();
                    //var_dump('<pre>',$wp_scripts->queue,'</pre>');

                    break;
                    
                case 'js':
                    $wp_scripts = $this->get_wp_scripts();
                    //var_dump('<pre>',$wp_scripts->queue,'</pre>');

                    break;
            }

            // build exclude assets lists & scripts queues 
            if ( 1 ) {
                $queueObj = $this->get_queue_obj($type);
                
                $scripts = $queueObj->get_new_queue( $wp_scripts->queue, $wp_scripts->registered );
                //var_dump('<pre>print_process js:', 'header', array_keys($scripts['header']), 'footer', array_keys($scripts['footer']), '</pre>');
                if ( empty($scripts) || !is_array($scripts) ) return;
                if ( count($scripts)==1 && empty($scripts['header']) ) return;
                if ( count($scripts)==2 && empty($scripts['header']) && empty($scripts['footer']) ) return;
                    
                $__scripts = $scripts;

                // build exclude assets lists
                foreach ($pos as $posVal) {
                    array_walk($scripts["$posVal"], array($this, '_get_script_handle2src'));
                    $this->set_assets_list( $scripts["$posVal"], $type, $posVal );
                }
            }
  
            // load minify engines
            $this->set_scripts_minify();

            foreach ($pos as $posVal) {
                //if ( in_array($posVal, array('header', 'footer')) ) continue 1;
                // minify process
                $results = $this->process_enqueue_files( $__scripts["$posVal"], $type, $posVal );
                $results = array_merge($results, array('queueObj' => $queueObj));
                
                // register the scripts in WP system
                $this->process_register_files( $results );
            }
            
            //var_dump('<pre>',$wp_scripts->queue,'</pre>');  
            //$wp_scripts = $this->get_wp_scripts();
            //foreach ($wp_scripts->queue as $_val) {
            //    var_dump('<pre>',$_val, $wp_scripts->registered["$_val"],'</pre>');  
            //}
        }

        private function set_assets_list( $new, $type, $pos='header' ) {
            $ret = array('css' => array('header' => array()), 'js' => array('header' => array(), 'footer' => array()));

            $current = get_option('psp_Minify_assets', true);
            if ( empty($current) || !is_array($current) ) {
                $current = $ret;
            }

            if (1) {
                if ( !isset($current["$type"]) || !isset($current["$type"]["$pos"])
                    || !is_array($current["$type"]["$pos"]) ) {
                    $current["$type"]["$pos"] = array();
                } else {
                    $current["$type"]["$pos"] = array_merge($current["$type"]["$pos"], (array) $new);
                }
            }

            update_option('psp_Minify_assets', $current);
        } 
        
        private function process_enqueue_files( $scripts, $type, $pos='header' ) {
 
            $first = array_shift(array_slice($scripts, 0, 1));
            $currentGroup = isset($first->extra, $first->extra['group']) && $first->extra['group'] == 1 ? 1 : 0;

            $taskObj = $this->get_task_obj($type, $pos);

            foreach ( $scripts as $handle => $scriptObj ) {
                //var_dump('<pre>process_enqueue_files:',$handle, $scriptObj,'</pre>');  
                if ( !isset($scriptObj->src) || empty($scriptObj->src) ) continue 1;
                
                // header vs footer script position!
                $_currentGroup = isset($scriptObj->extra, $scriptObj->extra['group']) && $scriptObj->extra['group'] == 1 ? 1 : 0;
                if ( $currentGroup != $_currentGroup ) {
                    // end current running task if necessary
                    if ( $taskObj->taskIsRunning ) {
                        $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                        $taskObj->end_task($src_min);
                    }
                    $currentGroup = $_currentGroup;
                }
  
                // excluded file
                if ( $this->is_script_excluded($handle, $type) ) {

                    // end current running task if necessary
                    if ( $taskObj->taskIsRunning ) {
                        $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                        $taskObj->end_task($src_min);
                    }
                    $taskObj->add_not_minified($scriptObj);
                    continue 1;
                }
                
                // case only for styles: style is wrong
                if ( $type == 'css' && $this->is_style_wrong($scriptObj) ) {

                    // end current running task if necessary
                    if ( $taskObj->taskIsRunning ) {
                        $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                        $taskObj->end_task($src_min);
                    }
                    $taskObj->add_not_minified($scriptObj);
                    continue 1;
                }
                
                // case only for styles: don't include conditional styles
                if ( $type == 'css' && isset($scriptObj->extra, $scriptObj->extra['conditional']) ) {

                    // end current running task if necessary
                    if ( $taskObj->taskIsRunning ) {
                        $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                        $taskObj->end_task($src_min);
                    }
                    $taskObj->add_not_minified($scriptObj);
                    continue 1;
                }
                
                // remote file
                if ( $this->is_script_remote($scriptObj->src) ) {
  
                    // save remote files is enabled
                    if ( isset($this->settings['enable_remote']) && $this->settings['enable_remote'] != 'yes' ) {

                        // end current running task if necessary
                        if ( $taskObj->taskIsRunning ) {
                            $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                            $taskObj->end_task($src_min);
                        }
                        $taskObj->add_not_minified($scriptObj);
                        continue 1;
                    }
  
                    // try to download remote file
                    $hash = md5( $scriptObj->src );
                    $remoteFile = array(
                        'hash'          => $hash,
                        'filename'      => $hash . '.' . $type,
                    );
                    $remoteFile['fullpath'] = self::$paths['save_remote_path'] . $remoteFile['filename'];
                    $remoteFile['fullurl'] = self::$paths['save_remote_url'] . $remoteFile['filename'];
                    $original_url = $this->build_remote_url( $scriptObj->src );
                    $remoteFile['content'] = $this->get_remote_content( $original_url, true );
                    
                    $this->remoteScripts["{$remoteFile['fullurl']}"] = $original_url;

                    // try to cache...
                    if ( $this->needNewCache($remoteFile['fullpath'], self::$CACHE_CONFIG_LIFE) ) {
                        $remoteStat = $this->writeCacheFile($remoteFile['fullpath'], $remoteFile['content'], false);
                        
                        // remote file could not be saved
                        if ( $remoteStat === false ) {
                            // end current running task if necessary
                            if ( $taskObj->taskIsRunning ) {
                                $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                                $taskObj->end_task($src_min);
                            }
                            $taskObj->add_not_minified($scriptObj);
                            continue 1;
                        }
                    }

                    // remote file was successfully saved
                    $scriptObj->handle = $remoteFile['filename'];
                    $scriptObj->src = $remoteFile['fullurl'];
                } // end remote

                if ( 1 ) {
                    // begin new running task if necessary
                    if ( !$taskObj->taskIsRunning ) {
                        $taskObj->begin_task( self::$alias . $taskObj->counter . ($pos=='footer' ? '-footer' : '') . '.' . $type );
                    }
                    $taskObj->add_minified($scriptObj);
                    continue 1;
                }
            } // end foreach
            
            if ( 1 ) {
                // end current running task if necessary
                if ( $taskObj->taskIsRunning ) {
                    $src_min = $this->process_minify_files( $taskObj->taskCurrentQueue, $type );
                    $taskObj->end_task($src_min);
                }
            }

            return array(
                'taskObj'       => $taskObj,
            );
        }

        private function process_minify_files( $scripts, $type ) {
            if ( empty($scripts) ) return false;

            $filenames = array();
            foreach ($scripts as $src) {

                $single_file = $this->minify_single_file( $src, $type );
                if ( !empty($single_file) && isset($single_file['filename']) ) {
                    $filenames[] = $single_file['filename'];
                }
            }
  
            $ret = false;
            if ( !empty($filenames) ) {
                $main_file = $this->minify_main_file( $filenames, $type );
                if ( !empty($main_file) && isset($main_file['filename']) ) {
                    $ret = $main_file['fullurl'];
                    
                    // return gzip file?
                    $is_gzip = isset($this->settings['enable_gzip']) && !empty($this->settings['enable_gzip'])
                        ? $this->settings['enable_gzip'] : 'no';
                    if ( $this->the_plugin->is_gzip( $is_gzip, array('ob_get_level' => false) )
                        && $this->verifyFileExists($main_file['fullpath'].'.gz') ) {
                        $ret = $main_file['fullurl'].'.gz';
                    }
                }
            }
  
            return $ret;
        }

        private function minify_single_file( $src, $type ) {
            $src = $this->build_url_without_query($src);

            $_src = array(
                'path'          => $this->build_local_path($src),
                'url'           => $this->build_local_url($src),
            );
  
            $this->currentScriptUrl = $_src['url'];
            if ( isset($this->remoteScripts["{$this->currentScriptUrl}"]) ) {
                $this->currentScriptUrl = $this->remoteScripts["{$this->currentScriptUrl}"];
            }
            
            // verify original script file exists!
            if ( !$this->verifyFileExists($_src['path']) ) {
                return false;
            }
  
            $cache = array();
            
            // last time when the original file was modified
            $last_modified_time = filemtime($_src['path']);
    
            // cache file name will be from original file path & last modified time
            $hash = md5( $_src['path'] . $last_modified_time );
            $cache = array(
                'hash'          => $hash,  
                'filename'      => $hash . $this->fileExtension($_src['path'], true),
            );
            $cache['fullpath'] = self::$paths['cache_path'] . $cache['filename'];
            $cache['fullurl'] = self::$paths['cache_url'] . $cache['filename'];
 
            // try to cache...
            if ( $this->needNewCache($cache['fullpath'], self::$CACHE_CONFIG_LIFE) ) {

                $_src['content'] = $this->getCacheFile($_src['path']);
                if ( !empty($_src['content']) ) {
                    
                    // run minify content operation
                    if ( !$this->verify_filename_ismin( $_src['path'] ) ) { // if filename contains -min or .min => already minified
                        $cache['content'] = $this->run_scripts_minify( $this->fileExtension($_src['path']), $_src['content'] );
                    } else {
                        $cache['content'] = $_src['content'];
                    }
                    
                    // write cache file with minified content
                    $cacheStat = $this->writeCacheFile($cache['fullpath'], $cache['content'], false);
                    if ( !$cacheStat ) return false;
                }
            } else {
                // not needed in method return!
                //$cache['content'] = $this->getCacheFile($cache['fullpath']);
            }
            //var_dump('<pre>',$_src, $cache,'</pre>');  
            return $cache;
        }
        
        private function minify_main_file( $filenames, $type ) {
  
            $hash = md5( implode('', $filenames ) );
            $cache = array(
                'hash'          => $hash,  
                'filename'      => $hash . ('.'.$type),
                'content'       => '',
            );
            $cache['fullpath'] = self::$paths['cache_path'] . $cache['filename'];
            $cache['fullurl'] = self::$paths['cache_url'] . $cache['filename'];
  
            // try to cache...
            if ( $this->needNewCache($cache['fullpath'], self::$CACHE_CONFIG_LIFE) ) {
  
                // build main file
                foreach ($filenames as $filename) {
                    $file_content = $this->getCacheFile( self::$paths['cache_path'] . $filename );
  
                    //$this->append_contents( $cache['fullpath'], $file_content ); // vers.1
                    $cache['content'] .= $file_content; // vers.2
                }
                
                // write main cache file
                $cacheStat = $this->writeCacheFile($cache['fullpath'], $cache['content'], false); // vers.2
  
                // get main file content
                //$cache['content'] = $this->getCacheFile( $cache['fullpath'] ); // vers.1
                
                // gzip main file
                //$this->put_contents_gzip( $cache['fullpath'].'.gz', $cache['content'] );
            } else {
                // not needed in method return!
                //$cache['content'] = $this->getCacheFile($cache['fullpath']);
            }
            return $cache;
        }

        private function process_register_files( $results ) {
            extract($results);
  
            // get current scripts
            switch ($this->currentType) {
                case 'css':
                    $wp_scripts = $this->get_wp_styles();
                    break;
                    
                case 'js':
                    $wp_scripts = $this->get_wp_scripts();
                    break;
            }
            
            // build new scripts
            $wp_scripts->registered = array_merge( $wp_scripts->registered, $taskObj->repository );
            $wp_scripts->queue = array_merge( array_diff( $wp_scripts->queue, $taskObj->queue ), array_keys($taskObj->repository) );
            //$wp_scripts->queue = array_merge( $queueObj->deps, $wp_scripts->queue );
            $wp_scripts->queue = array_unique( $wp_scripts->queue );
  
            //var_dump('<pre>',array_keys($taskObj->repository), $taskObj->queue,'</pre>');
            //foreach ($wp_scripts->queue as $_val) {
            //    var_dump('<pre>',$_val, $wp_scripts->registered["$_val"],'</pre>');  
            //}
            
            //var_dump('<pre>taskObj: ',$taskObj->repository, $taskObj->queue,'</pre>');  
            //foreach ($wp_scripts as $_key=>$_val) {
            //    if ( !in_array($_key, array('queue', 'to_do', 'done', 'args', 'groups', 'group')) ) continue 1;
            //    var_dump('<pre>',$_key, $wp_scripts->$_key,'</pre>');  
            //}

            foreach( $taskObj->queue as $handle ) {
                $wp_scripts->registered[ "$handle" ]->deps = array(); // null => generated warning!
            }
        
            // set/register new scripts
            switch ($this->currentType) {
                case 'css':
                    $wp_scripts = $this->set_wp_styles( $wp_scripts );
                    break;
                    
                case 'js':
                    $wp_scripts = $this->set_wp_scripts( $wp_scripts );
                    break;
            }

            return $wp_scripts;
        }


        /**
         * Cache
         */
        //use cache to limits search accesses!
        private function needNewCache($filename, $cache_life) {
        
            // cache file needs refresh!
            if (($statCache = $this->isCacheRefresh($filename, $cache_life))===true || $statCache===0) {
                return true;
            }
            return false;
        }
        
        // verify cache refresh is necessary!
        private function isCacheRefresh($filename, $cache_life) {
            // cache file exists!
            if ($this->verifyFileExists($filename)) {
                $verify_time = time(); // in seconds
                $file_time = filemtime($filename); // in seconds
                $mins_diff = ($verify_time - $file_time) / 60; // in minutes
                if($mins_diff > $cache_life){
                    // new cache is necessary!
                    return true;
                }
                // cache is empty! => new cache is necessary!
                if (filesize($filename)<=0) return 0;
    
                // NO new cache!
                return false;
            }
            // cache file NOT exists! => new cache is necessary!
            return 0;
        }
    
        // write content to local cached file
        private function writeCacheFile($filename, $content, $use_lock=false) {
            $folder = dirname($filename);
            if ( empty($folder) || $folder == '.' || $folder == '/' ) return false;
  
            // cache folder!
            if ( !$this->makedir($folder) ) return false;
            if ( !is_writable($folder) ) return false;

            $has_wrote = false;
            if ( $use_lock ) {

                $fp = @fopen($filename, "wb");
                if ( @flock($fp, LOCK_EX, $wouldblock) ) { // do an exclusive lock
                    $has_wrote = @fwrite($fp, $content);
                    @flock($fp, LOCK_UN, $wouldblock); // release the lock
                }
                @fclose( $fp );
            } else {

                $wp_filesystem = $this->the_plugin->wp_filesystem;
                $has_wrote = $wp_filesystem->put_contents( $filename, $content );
                if ( !$has_wrote ) {
                    $has_wrote = file_put_contents($filename, $content);
                }
            }
            return $has_wrote;
        }
    
        // cache file
        private function getCacheFile($filename) {
            if ($this->verifyFileExists($filename)) {
                
                $wp_filesystem = $this->the_plugin->wp_filesystem;
                $has_wrote = $wp_filesystem->get_contents( $filename );
                if ( !$has_wrote ) {
                    $has_wrote = file_get_contents($filename);
                }
                $content = $has_wrote;
                return $content;
            }
            return false;
        }
        
        // delete cache
        private function deleteCache($filename) {
            if ($this->verifyFileExists($filename)) {
                return unlink($filename);
            }
            return false;
        }
    
        // verify if file exists!
        private function verifyFileExists($file, $type='file') {
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
    
        // make a folder!
        private function makedir($fullpath) {
            clearstatcache();
            if(file_exists($fullpath) && is_dir($fullpath) && is_readable($fullpath)) {
                return true;
            }else{
                $stat1 = @mkdir($fullpath, 0777, true); // recursive
                $stat2 = @chmod($fullpath, 0777);
                if (!empty($stat1) && !empty($stat2))
                    return true;
            }
            return false;
        }
        
        // get file name/ dot indicate if a .dot will be put in front of image extension, default is not
        private function fileName($fullname)
        {
            $return = substr($fullname, 0, strrpos($fullname, "."));
            return $return;
        }
    
        // get file extension
        private function fileExtension($fullname, $dot=false)
        {
            $return = "";;
            if( $dot == true ) $return .= ".";
            $return .= substr(strrchr($fullname, "."), 1);
            return $return;
        }
    
        private function append_contents( $filename, $contents, $mode = '0777' ) {
            if ( !($fp = @fopen($filename, 'ab')) ) {
                return false;
            }
            $stat1 = @fwrite($fp, $contents);
            @fclose($fp);
            $stat2 = @chmod($filename, $mode);
            if (!empty($stat1) && !empty($stat2))
                return true;
            return false;
        }
        
        private function put_contents_gzip( $filename, $contents ) {
            if ( !function_exists('gzcompress') ) return false;
                
            //$gzip = @gzopen($filename, "w9");
            //if ( $gzip ){
            //    gzwrite($gzip, $contents);
            //    gzclose($gzip);
            //}
            
            $gzip = @fopen( $filename, 'w' );
            if ( $gzip ) {
                //$contents = @gzcompress($contents, 9); //zlib (http deflate)
                $contents = @gzencode($contents, 9); //gzip
                //$contents = @gzdeflate($contents, 1); //raw deflate encoding
                @fwrite($gzip, $contents);
                @fclose($gzip);
            }
    
            return true;
        }

        public function get_folder_files_recursive($path) {
            $size = 0;
            $ignore = array('.', '..', 'cgi-bin', '.DS_Store');
            $files = scandir($path);
  
            foreach ($files as $t) {
                if (in_array($t, $ignore)) continue;
                if (is_dir(rtrim($path, '/') . '/' . $t)) {
                    $size += $this->get_folder_files_recursive(rtrim($path, '/') . '/' . $t);
                } else {
                    $size++;
                }   
            }
            return $size;
        }


        /**
         * Utils
         */
        private function is_login_page() {
            return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
        }

        private function is_script_remote( $src ) {
            if( !$src ) {
                return false;
            }

            $siteUrl = get_home_url();

            // /wp-admin/js/ : in the future for admin
            if( strpos($src, '/wp-includes/') === 0 ) {
                return false;
            }

            if ( !preg_match("/http(|s):\/\//i", $src) ) {
                if ( $src[0] == '/' && $src[1] == '/' ) $src = substr($src, 2);
                else if ( $src[0] == '/' ) $src = substr($src, 1);
                $src = 'http://' . $src;
            }
            if ( preg_match( "/\.php/", $src ) ) {
                return true;
            }
            if( strpos( $src, $siteUrl ) === false ) {
                return true;
            }
            return false;
        }

        private function is_script_excluded( $handle, $type ) {
            return in_array($handle, self::$assetsExcluded["$type"]);
        }
        
        private function is_style_wrong( $scriptObj ) {
            return isset($scriptObj->extra['conditional']);
        }

        private function _get_script_handle2src( &$scriptObj, $handle ) {
            if ( strpos( $handle, self::$alias) === false
                && strpos( $handle, 'ff-minified-') === false ) {
                $scriptObj = $scriptObj->src;
            }
        }

        private function __get_script_handle2src( $enqueueList ) {
            $retList = array();
            if (empty($enqueueList) ) return $retList;

            foreach ( $enqueueList as $handle => $scriptObj) {
                if ( strpos( $handle, self::$alias) === false ) {
                    $retList["$handle"] = $scriptObj->src;
                }
            }
            return $retList;
        }
        
        private function get_wp_scripts() {
            global $wp_scripts;
            if ( !is_a($wp_scripts, 'WP_Scripts') ) {
                $wp_scripts = new WP_Scripts();
            }
            return $wp_scripts;
        }
        
        private function get_wp_styles() {
            global $wp_styles;
            if ( !is_a($wp_styles, 'WP_Styles') ) {
                $wp_styles = new WP_Styles();
            }
            return $wp_styles;
        }

        private function set_wp_scripts( $new ) {
            global $wp_scripts;
            $wp_scripts = $new;
            return $wp_scripts;
        }
        
        private function set_wp_styles( $new ) {
            global $wp_styles;
            $wp_styles = $new;
            return $wp_styles;
        }

        private function set_scripts_minify() {
            $minEnabled = array();
            if ( isset($this->settings['enable_minify']) && !empty($this->settings['enable_minify']) ) {
                $minEnabled = (array) $this->settings['enable_minify'];
            }
 
            // css minify
            if ( in_array('css', $minEnabled) ) {
                require_once($this->module_folder_path . 'scripts/CSSmin.php');
                self::$script_cssmin = new aaCSSmin();
            }
            
            // js minify
            if ( in_array('js', $minEnabled) ) {
                require_once($this->module_folder_path . 'scripts/JSMinPlus.php');
                self::$script_jsmin = aaJSMinPlus::getInstance();
            }
        }
        
        private function run_scripts_minify( $type, $content ) {
            $contentMin = $content;

            try {
                switch ($type) {
                    case 'css':
                        $contentMin = preg_replace_callback('/url\(\'?"?([^\"\')]*)\'?"?\)/i', array( $this, '_build_url_relative2absolute' ), $contentMin);
                        $contentMin = self::$script_cssmin->run( $contentMin );
                        break;
                        
                    case 'js':
                        $contentMin = self::$script_jsmin->_minify( $contentMin );
                        break;
                }
            } catch ( Exception $e ) {
                $contentMin = $content;
            }

            if ( $type == 'js' ) {
                $contentMin .= ';';
                $contentMin = str_ireplace(array('"use strict";', '"use strict"', "'use strict';", "'use strict';"), '', $contentMin);
            }
            return $contentMin;
        }

        private function get_queue_obj( $script_type='css' ) {
            return new pspMinifyQueue($script_type);
        }
        
        private function get_task_obj( $script_type='css', $pos='header' ) {
            return new pspMinifyTask($script_type, $pos);
        }

        private function build_excluded_assets() {
            if ( isset($this->settings['enable_excluding']) && $this->settings['enable_excluding'] == 'yes' ) ;
            else return false;
            
            if ( isset($this->settings['exclude_header_styles']) && !empty($this->settings['exclude_header_styles']) ) {
                self::$assetsExcluded['css'] = array_merge(
                    self::$assetsExcluded['css'],
                    (array) $this->settings['exclude_header_styles']
                );
            }
            
            if ( isset($this->settings['exclude_header_scripts']) && !empty($this->settings['exclude_header_scripts']) ) {
                self::$assetsExcluded['js'] = array_merge(
                    self::$assetsExcluded['js'],
                    (array) $this->settings['exclude_header_scripts']
                );
            }
            if ( isset($this->settings['exclude_footer_scripts']) && !empty($this->settings['exclude_footer_scripts']) ) {
                self::$assetsExcluded['js'] = array_merge(
                    self::$assetsExcluded['js'],
                    (array) $this->settings['exclude_footer_scripts']
                );
            }
        }

        private function get_remote_content( $filename, $force_curl=false ) {
            if( strpos($filename, '?') !== false ) {
                $src = $filename;
                $src = explode('?', $src);
                $filename = $src[0] . '?' . $this->the_plugin->urlencode($src[1]);
            }

            $content = '';
            if ( preg_match( "/\.php/", $filename ) || $force_curl ) {
                $htaccess = false;
                if ( isset($this->settings['remote_username']) && !empty($this->settings['remote_username'])
                    && isset($this->settings['remote_password']) && !empty($this->settings['remote_password']) ) {
                    $htaccess = $this->settings['remote_username'] . ':' . $this->settings['remote_password'];
                }
                if ( $filename == 'http://fonts.googleapis.com/css?family=Open Sans' ) {
                    $filename = 'http://fonts.googleapis.com/css?family=Open+Sans';
                }
                $content = $this->the_plugin->curl( $filename, array(
                    'htaccess'                     => $htaccess,
                ), array(
                    'resp_is_json'                 => true,
                    'resp_add_http_code'           => true,
                ), true );
                if ( $content['status']=='valid' ) {
                    $content = $content['msg'];
                } else {
                    $content = '';
                }
            } else {
  
                $wp_filesystem = $this->the_plugin->wp_filesystem;
                $has_wrote = $wp_filesystem->get_contents( $filename );
                if ( !$has_wrote ) {
                    $has_wrote = file_get_contents($filename);
                }
                $content = $has_wrote;
            }
            return $content;
        }

        private function verify_filename_ismin( $filename ) {
            return preg_match( "/(\-|\.)min\.(?:css|js)/i", $filename ) ? true : false;
        }
        
        private function build_local_path( $src ) {
            $ret = $this->build_full_absolute_paths( $src );
            return $ret['path'];
        }

        private function build_local_url( $src ) {
            $ret = $this->build_full_absolute_paths( $src );
            return $ret['url'];
        }
        
        private function build_remote_url( $src ) {
            $ret = $this->build_full_absolute_paths( $src, true );
            return $ret['url'];
        }

        private function build_full_absolute_paths( $src, $is_remote=false ) {
            $ret = array('url' => '', 'path' => '');
 
            if ( empty($src) ) return $ret;

            if ( !$is_remote ) {
                $siteUrl = get_home_url();
                $sitePath = get_home_path();
            }

            if ( !preg_match("/http(|s):\/\//i", $src) ) {
                if ( !$is_remote ) {
                    $_siteUrl = str_replace(array('http://', 'https://'), array('', ''), $siteUrl);
                    if ( strpos($src, $_siteUrl) !== false ) {
                        $src = str_replace($_siteUrl, '', $src );
                        if ( $src[0] == '/' && $src[1] == '/' ) $src = substr($src, 2);
                        else if ( $src[0] == '/' ) $src = substr($src, 1);
                    }
                    $src = $siteUrl . $src;
                } else {
                    if ( $src[0] == '/' && $src[1] == '/' ) $src = substr($src, 2);
                    else if ( $src[0] == '/' ) $src = substr($src, 1);
                    $src = 'http://' . $src;
                }
            }
            $ret['url'] = $src;
  
            if ( !$is_remote ) {
                $removeBase = str_replace( $siteUrl, '', $src );
                $path = untrailingslashit( $sitePath ) . $removeBase;
                $ret['path'] = $path;
            }
  
            return $ret;
        }
        
        private function build_url_without_query( $src ) {
            if( strpos($src, '?') !== false ) {
                $src = explode('?', $src);
                $src = $src[0];
            }
            return $src;
        }

        private function build_canonical_url( $src ) {
            $src = explode('/', $src);
            $keys = array_keys($src, '..');

            // remove all ../ from source
            foreach ($keys as $pos => $key) {
                array_splice($src, $key - ($pos * 2 + 1), 2);
            }

            $src = implode('/', $src);
            $src = str_replace('./', '', $src);
            return $src;
        }
        
        private function _build_url_relative2absolute( $matches ) {
            if ( !is_array($matches) || count($matches) < 2 ) return '';

            // ../filename.ext
            $urlRel = $matches[1];

            // url('../filename.ext') or url("../filename.ext")
            $urlFull = $matches[0];

            $isDataPos = strpos($urlRel, 'data:');
            if( $isDataPos !== false && strpos($isDataPos, 'data:') < 3 ) {
                return $urlFull;
            }
            if ( preg_match("/http(|s):\/\//i", $urlRel) ) {
                return $urlFull;
            }
            if ( preg_match("/^(?:\/\/|\/|fonts).*/i", $urlRel) ) {
                return $urlFull;
            }

            // server.ext/folder/../filename.ext
            $urlAbs = dirname( $this->currentScriptUrl ) . '/' . $urlRel;
            // server.ext/filename.ext
            $urlAbsClean = $this->build_canonical_url( $urlAbs );

            // url('server.ext/filename.ext') or url("server.ext/filename.ext")
            $urlFullClean = str_replace( $urlRel, $urlAbsClean, $urlFull );
            return $urlFullClean;
        }
        
        private function build_cache_folder() {
            self::$CACHE_FOLDER = substr(self::$alias, 0, strlen(self::$alias) - 1);
            self::$CACHE_FOLDER_SAVE_REMOTE = 'remote';

            // make sure upload dirs exist and set file path and uri
    
            $upload_dir = wp_upload_dir();
            if ( !$this->verifyFileExists($upload_dir['basedir'], 'folder') ) {
                wp_mkdir_p( $upload_dir['basedir'] );   
            }

            self::$paths = array(
                'cache_path'         => $upload_dir['basedir'] . '/' . self::$CACHE_FOLDER . '/',
                'cache_url'          => $upload_dir['baseurl'] . '/' . self::$CACHE_FOLDER . '/',
                'save_remote_path'   => $upload_dir['basedir'] . '/' . self::$CACHE_FOLDER . '/' . self::$CACHE_FOLDER_SAVE_REMOTE . '/',
                'save_remote_url'    => $upload_dir['baseurl'] . '/' . self::$CACHE_FOLDER . '/' . self::$CACHE_FOLDER_SAVE_REMOTE . '/',
            );

            if ( !$this->verifyFileExists(self::$paths['cache_path'], 'folder') ) {
                wp_mkdir_p( self::$paths['cache_path'] );
            }
            if ( !$this->verifyFileExists(self::$paths['save_remote_path'], 'folder') ) {
                wp_mkdir_p( self::$paths['save_remote_path'] );
            }
            
            if ( $this->verifyFileExists(self::$paths['cache_path'], 'folder')
                && is_writable(self::$paths['cache_path']) ) {
                return true;
            }
            return false;
        }


		/**
	    * Singleton pattern
	    *
	    * @return pspMinify Singleton instance
	    */
	    static public function getInstance() {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
    }
}

if (class_exists('pspMinifyQueue') != true) {
    class pspMinifyQueue {

        private $script_type;
        private $repository;
        private $queue;
        public $deps;
        
        public function __construct( $script_type='css' ) {
            $this->script_type = isset($script_type) && !empty($script_type) ? $script_type : 'css';
        }
        
        public function get_new_queue( $oldQueue, $repository ) {
            $this->do_reset($repository);
            if ( empty($oldQueue) ) return $this->queue;
            
            foreach ( $oldQueue as $scriptName ) {
                $this->process( $scriptName );
            }
            $this->deps = array_unique( $this->deps );
            return $this->queue;
        }
        
        private function do_reset( $repository ) {
            $this->repository = $repository;
            switch ($this->script_type) {
                case 'css':
                    $this->queue = array('header' => array());
                    break;
                    
                case 'js':
                    $this->queue = array('header' => array(), 'footer' => array());
                    break;
            }
            $this->deps = array();
        }
        
        private function process( $scriptName ) {
            if ( $this->already_processed($scriptName) ) {
                return 0;
            }
            
            $scriptObj = $this->get_info($scriptName);
            if ( empty($scriptObj) ) {
                return 0;
            }
            if ( !empty($scriptObj->deps) ) {
                foreach( $scriptObj->deps as $subItem ) {
                    $this->process( $subItem );
                    $this->deps[] = $subItem;
                }
            }
            
            switch ($this->script_type) {
                case 'css':
                    $this->queue['header']["$scriptName"] = $scriptObj;
                    break;
                    
                case 'js':
                    if( isset($scriptObj->extra, $scriptObj->extra['group']) && $scriptObj->extra['group'] == 1 ) {
                        $this->queue['footer']["$scriptName"] = $scriptObj;
                    } else {
                        $this->queue['header']["$scriptName"] = $scriptObj;
                    }
                    break;
            }
        }
        
        private function get_info( $scriptName ) {
            if ( isset($this->repository["$scriptName"]) ) {
                return $this->repository["$scriptName"];
            }
            return false;
        }
        
        private function already_processed( $scriptName ) {
            switch ($this->script_type) {
                case 'css':
                    return isset($this->queue['header']["$scriptName"]);
                    break;
                    
                case 'js':
                    return isset($this->queue['footer']["$scriptName"])
                        || isset($this->queue['header']["$scriptName"]);
                    break;
            }
            return false;
        }
    }
}

if (class_exists('pspMinifyTask') != true) {
    class pspMinifyTask {
        private $script_type;
        private $position;
        
        public $counter; // number of tasks
        public $repository; // repository (similar to wp registered) with new main minified scripts & remained not minified scripts - pair (script handle, script object)
        public $queue; // all files processed, minified or not - pair (numeric key, script handle)
        public $taskCurrentQueue;
        public $taskIsRunning; // current task (containing current queued scripts) is running
        private $taskName; // current task name
        private $isFooter; // current task (main minified script) is in footer
        private $inlineContent; // current task (main minified script) has inline content
        
        public function __construct( $script_type='css', $pos='header' ) {
            $this->script_type = isset($script_type) && !empty($script_type) ? $script_type : 'css';
            $this->position = isset($pos) && !empty($pos) ? $pos : 'header';

            $this->counter = 0;
            $this->repository = array();
            $this->queue = array();
            
            $this->do_reset();
        }
        
        // reinit current task as clean new task
        public function do_reset() {
            $this->taskCurrentQueue = array();
            $this->taskIsRunning = false;
            $this->taskName = '';
            $this->isFooter = false;
            $this->inlineContent = array();
        }
        
        public function add_minified( $script ) {
            if ( in_array($script->handle, $this->queue)) return;
            //var_dump('<pre>ok:',$script->handle,'</pre>');

            if ( $this->script_type == 'js' ) {
                if( isset($script->extra['group']) && $script->extra['group']==1 ) {
                    $this->isFooter = true;
                }
                if( isset($script->extra['data']) ) {
                    $this->inlineContent = array_merge($this->inlineContent, array($script->extra['data']));
                }
                if( isset($script->extra['l10n']) ) {
                    $object_name = $script->extra['l10n'][0];
    
                    $content = "\tvar $object_name = {\n";
                    $eol = '';
                    foreach ( $script->extra['l10n'][1] as $var => $val ) {
                        $content .= "$eol\t\t$var: \"" . js_escape( $val ) . '"';
                        $eol = ",\n";
                    }
                    $content .= "\n\t};\n\n";
            
                    $this->inlineContent = array_merge($this->inlineContent, array($content));            
                }
            } else if ( $this->script_type == 'css' ) {
                if( isset($script->extra['after']) ) {
                    $this->inlineContent = array_merge($this->inlineContent, array("{$script->handle}" => implode("\n", $script->extra['after'])));
                }
            }
  
            $this->queue[] = $script->handle; // add original minified script
            $this->taskCurrentQueue[] = $script->src;
        }
        
        public function add_not_minified( $script ) {
            if ( in_array($script->handle, $this->queue)) return;
            //var_dump('<pre>not:',$script->handle,'</pre>');

            if ( $this->script_type == 'js' ) {
                $script->deps = null;
            }
  
            $this->repository["{$script->handle}"] = $script; // add original not minfied script
            $this->queue[] = $script->handle; // add original not minified script
        }
        
        public function begin_task( $taskName ) {
            $this->do_reset();

            $this->taskIsRunning = true;
            $this->taskName = $taskName;
        }
        
        // add the new main minified file to repository and queues and then reset current running task
        public function end_task( $src_min ) {
            if ( !empty($src_min) ) {
                $script = new _WP_Dependency();
                $script->handle = $this->taskName;
                $script->src = $src_min;
                $script->ver = '1.0';
                $script->deps = array();
                $script->args = null;
                $script->extra = array();
                
                if ( $this->isFooter ) {
                    $script->extra['group'] = 1;
                }
                if ( !empty($this->inlineContent) ) {
                    if ( $this->script_type == 'js' ) {
                        $script->extra['data'] = implode("\t", $this->inlineContent);                    
                    } else if ( $this->script_type == 'css' ) {
                        $script->extra['after'] = array(implode("\t", $this->inlineContent));
                    }
                }
                $this->repository["{$script->handle}"] = $script; // and new main minified script
                $this->counter++;
                
                //var_dump('<pre>end_task:',$script, $this->repository, $this->queue,'</pre>');
            }
            
            $this->do_reset();
        }
    }
}

// Initialize the pspMinify class
$pspMinify = pspMinify::getInstance();