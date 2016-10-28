<?php
/**
 * SERP check Class
 * http://www.aa-team.com
 * ======================
 *
 * @package			pspSERPCheck
 * @author			AA-Team
 */

class pspSERPCheck
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

	static protected $_instance;
	
	private $settings = array();
	
	// cache folder & files
	private static $__google_url = "https://www.googleapis.com/customsearch/v1?q={q}&cx={cx}&gl={gl}&key={key}&num={num}&start={start}";
	private static $CACHE_FOLDER = null;
	private static $CACHE_CONFIG_LIFE = 1440; // cache lifetime in minutes /1 day
	
	// debug only, the html file have result of search "test" on google.com
	private static $__isdebug = false;
	private static $__debug_url = '';

	private $config = array();
	
	private static $apiMaxNbReq = 100;
	
	private static $saveLog = false;
	
    /*
    * Required __construct() function
    */
    public function __construct()
    {
    	global $psp;

    	$this->the_plugin = $psp;
    	
    	$this->settings = $this->the_plugin->getAllSettings( 'array', 'serp' );
		
		self::$__debug_url = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'test-google.json';

		// cache folder & files
		self::$CACHE_FOLDER = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/serp/cache/';
		
		$this->config = array(
			'key' => $this->settings['developer_key'],
			'cx' => $this->settings['custom_search_id'],
			'gl' => $this->settings['google_country']
		);
		
		self::$apiMaxNbReq = $this->settings['nbreq_max_limit'];
    }

	/**
    * Singleton pattern
    *
    * @return pspSERPCheck Singleton instance
    */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
	
	public function saveLog( $val=false ) {
		self::$saveLog = $val;
	}
    
    
	/**
	* GET SERP
	*
	* @return string
	*/
	public function __get_serp_score( $pms )
	{
		// validations
		extract($pms);
		if (!isset($dopause)) $dopause = 0;
		if (!isset($link)) $link = trim($link);

		if (!isset($engine) || empty($engine)
			|| !isset($keyword) || empty($keyword)
			|| !isset($link) || empty($link))
			return false; //mandatory params missing!
			
		if (preg_match("/google/i", $engine)) ;
		else return false;


		$currentReqInfo = get_option('psp_serp_nbrequests');
		$currentNbReq = (int) $currentReqInfo['nbreq'];
		$currentData = $currentReqInfo['data'];

		if ( $currentData != date('Y-m-d') ) {
			 // reset number of requests - based on date
			update_option( 'psp_serp_nbrequests', array(
				'nbreq' => 0,
				'data'	=> date('Y-m-d')
			) );
		}
		
		$currentReqInfo = get_option('psp_serp_nbrequests');
		$currentNbReq = (int) $currentReqInfo['nbreq'];
		$currentData = $currentReqInfo['data'];
  
		if ( $currentNbReq >= self::$apiMaxNbReq ) {
			$msg = __('You\'ve reached the maximum allowed number of requests for this day.', $this->the_plugin->localizationName);
			if ( self::$saveLog ) {
				$last_status = array('last_status' => array('status' => 'error', 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
				$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp_last_status', $last_status );
				$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp', array_merge( (array) $this->settings, $last_status ) );
			}
			return array(
				'status'	=> 'invalid',
				'msg'		=> $msg
			); //couldn't retrive data!
		}
		
		// body
		$dataToSave = array();
		$topType = $this->settings['top_type'];
        $topType_max = 88; // changed in 2015 - google returns only 8 items per request

		$cachename = $engine . '||' . strip_tags($keyword) . '||' . strip_tags($link);
		$filename = self::$CACHE_FOLDER . ( md5($cachename) ) . '.json';

		// read from cache!
		if ( $this->needNewCache($filename) !== true ) { // no need for new cache!

			$body = $this->getCacheFile($filename);
			
			if (is_null($body) || !$body || trim($body)=='') {
				$msg = __('cache file is empty!', $this->the_plugin->localizationName);
				if ( self::$saveLog ) {
					$last_status = array('last_status' => array('status' => 'error', 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp_last_status', $last_status );
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp', array_merge( (array) $this->settings, $last_status ) );
				}
				return array(
					'status'	=> 'invalid',
					'msg'		=> $msg
				); //couldn't retrive data!
			}

			$parseResp = $this->parseApiResponse( array_merge($pms, array(
				'content'		=> $body
			)), 'cache' ); // parse json response

			if ( $parseResp['status'] == 'valid' ) {
			}

		} else {

			// API Request
			$contor = 0; $linkFound = false;
			do {

				$currentReqInfo = get_option('psp_serp_nbrequests');
				$currentNbReq = (int) $currentReqInfo['nbreq'];
				$currentData = $currentReqInfo['data'];
				
				$apiURL = $this->buildApiUrl( $contor + 1, $keyword ); // build Api URL
			
				$resp = $this->the_plugin->remote_get( $apiURL ); // get json response from API
		
				// validate response!
				if ( is_array($resp) && isset($resp['status']) && $resp['status'] == 'valid' )
					$body = $resp['body'];
				else
					$body = false;

                $msg = $resp;
				if (is_null($body) || !$body || trim($body)=='') {
					if ( self::$saveLog ) {
						$last_status = array('last_status' => array('status' => 'error', 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
						$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp_last_status', $last_status );
						$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp', array_merge( (array) $this->settings, $last_status ) );
					}
					return array(
						'status'	=> 'invalid',
						'msg'		=> $msg
					); //couldn't retrive data!
				} else {
					$bodyStat = json_decode($body);
					if ( isset( $bodyStat->error->code ) ) {
						//foreach ( $bodyStat->error->errors as $key => $val )
						//	if ( $val->reason == 'dailyLimitExceeded' )
								if ( self::$saveLog ) {
									$last_status = array('last_status' => array('status' => 'error', 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
									$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp_last_status', $last_status );
									$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp', array_merge( (array) $this->settings, $last_status ) );
								}
								return array(
									'status'	=> 'invalid',
									'msg'		=> serialize( json_encode( $bodyStat ) )
								); //couldn't retrive data!
					}
				}
				
				// increase number of requests
				if ( $currentData == date('Y-m-d') ) {
					update_option( 'psp_serp_nbrequests', array(
						'nbreq' => (int) ( $currentNbReq + 1 ),
						'data'	=> $currentData
					) );
				}
				else { // reset number of requests - based on date
					update_option( 'psp_serp_nbrequests', array(
						'nbreq' => 0,
						'data'	=> date('Y-m-d')
					) );
				}
	
				$parseResp = $this->parseApiResponse( array_merge($pms, array(
					'content'		=> $body,
					'pageNumber'	=> $contor
				)), 'api' ); // parse json response
				
				if ( $parseResp['status'] == 'valid' ) {
					$dataToSave = array_merge( $dataToSave, $parseResp['top100'] );
					
					if ( $parseResp['pos'] > 0 && $parseResp['pos'] <= 100 )
						$linkFound = true;
				}
				
				$contor += isset($body['searchInformation'], $body['searchInformation']['totalResults']) ? (int) $body['searchInformation']['totalResults'] : 8;
	
			} while ( ( $contor < $topType ) && ( $contor < $topType_max ) && !$linkFound && $currentNbReq < self::$apiMaxNbReq );
	
			// write cache!
			if ( !empty($dataToSave) ) {
				$parseResp['top100'] = $dataToSave;
				$dataToSave = json_encode( array( 'items' => $dataToSave ) );
				$this->writeCacheFile( $filename, $dataToSave ); // write new local cached file! - append new data
			}
		
		}

		$msg = $parseResp;
		if ( self::$saveLog ) {
			$last_status = array('last_status' => array('status' => 'success', 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
			$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp_last_status', $last_status );
			$this->the_plugin->save_theoption( $this->the_plugin->alias . '_serp', array_merge( (array) $this->settings, $last_status ) );
		}
  
		return $parseResp;
	}
    
    
    private function buildApiUrl( $start=0, $keyword='' ) 
    {
		if (self::$__isdebug) {
			$url = self::$__debug_url;
			return $url;
		}

    	$url = self::$__google_url;

    	$url = str_replace('{key}', $this->config['key'], $url);
    	$url = str_replace('{cx}', urlencode($this->config['cx']), $url);
    	$url = str_replace('{gl}', $this->config['gl'], $url);

		$url = str_replace('{num}', 8, $url);
		$url = str_replace('{start}', $start, $url);
		$url = str_replace('{q}', urlencode(htmlspecialchars_decode($keyword, ENT_QUOTES)), $url);

		return $url;
    }

	private function parseApiResponse( $pms, $dataFrom='api' ) 
	{
		extract($pms);

		$content = json_decode( $content, true );
		$content = $content['items'];

		$pos = $dataFrom == 'api' ? $pageNumber : 0;
		$linkToFind = $link;

		$top100 = array();
		$your_position_today = 999; // not in top 100
		$googleRespStatus = false;
		
		if ( !is_array( $content ) ) {
			$__ret = array( //Wrong response!
				'status'	=> 'invalid',
				'msg'		=> 'invalid content received!'
			);
			return $__ret;
		}

		foreach ( $content as $tag ) {
			$googleRespStatus = true;

			$url = (string) $tag['link'];
			
			if ( preg_match("/^http:|https:/i", $url) )
				$isURL = true;
			else if ( preg_match("/^www/i", $url) )
				$isURL = true;

			if ( $isURL ) {
				
				$pos++;

				$top100[ $pos ] = array(
					'position' 	=> $pos,
					'link'		=> $url,
					'title'		=> (string) $tag['title'],
					'keyword'	=> $keyword,
					'engine'	=> $engine
				);

				$cleanUrl = $this->cleanUrl( $url );
				$cleanLinkToFind = $this->cleanUrl( $linkToFind );

				if( ( $cleanLinkToFind == $cleanUrl ) ) {
					$your_position_today = $pos;
				}
			}
		}

		if ( $googleRespStatus ) { //right response from google retrieved!
			$__ret = array(
				'status'	=> 'valid',
				'msg'		=> 'ok',
				'url' 		=> $linkToFind,
				'keyword' 	=> $keyword,
				'pos' 		=> $your_position_today,
				'top100' 	=> $top100
			);
			return $__ret;
		}

		$__ret = array( //Wrong response!
			'status'	=> 'invalid'
		);
		return $__ret;
	}
	
	//clean url for comparation!
	private function cleanUrl( $url ) {
		$url = str_replace('https://', '', $url);
		$url = str_replace('http://', '', $url);
		$url = str_replace('www.', '', $url);
		$url = rtrim($url, '/');
		return $url;
	}
	
	//use cache to limits search accesses!
	public function needNewCache($filename) {
	
		// cache file needs refresh!
		if (($statCache = $this->isCacheRefresh($filename))===true || $statCache===0) {
			return true;
		}
		return false;
	}

	
	// verify cache refresh is necessary!
	private function isCacheRefresh($filename) {
		$cache_life = self::$CACHE_CONFIG_LIFE;

		// cache folder!
		$this->makedir(self::$CACHE_FOLDER);

		// cache file exists!
		if ($this->verifyFileExists($filename)) {
			$verify_time = time();
			$file_time = filemtime($filename);
			$mins_diff = ($verify_time - $file_time) / 60;
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
	public function writeCacheFile($filename, $content) {
		return file_put_contents($filename, $content);
	}

	// cache file
	public function getCacheFile($filename) {
		if ($this->verifyFileExists($filename)) {
			$content = file_get_contents($filename);
			return $content;
		}
		return false;
	}
	
	// delete cache
	public function deleteCache($cache_file) {
		$filename = self::$CACHE_FOLDER . $cache_file;

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
	private function makedir($path, $folder='') {
		$fullpath = $path . $folder;

		clearstatcache();
		if(file_exists($fullpath) && is_dir($fullpath) && is_readable($fullpath)) {
			return true;
		}else{
			$stat1 = @mkdir($fullpath);
			$stat2 = @chmod($fullpath, 0777);
			if ($stat1===true && $stat2===true)
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
	
	private function fakeUserAgent()
	{
		return $this->the_plugin->fakeUserAgent();
	}
	
}
new pspSERPCheck();