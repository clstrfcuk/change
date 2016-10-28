<?php
/**
 * SEO check Class
 * http://www.aa-team.com
 * ======================
 *
 * @package			pspSeoCheck
 * @author			AA-Team
 */
class pspSeoCheck
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
	
    /*
    * Required __construct() function
    */
    public function __construct()
    {
    	global $psp;

    	$this->the_plugin = $psp;

		require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/php-query.php' );
    }

	/**
    * Singleton pattern
    *
    * @return pspSeoCheck Singleton instance
    */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }


	/**
	* Auto generate meta description from string
	*
	* @return string
	*/
	public function get_seo_score( $p=0, $kw='', $returnAs='die', $post_content='empty' )
	{
		if ( $this->the_plugin->__tax_istax( $p ) ) //taxonomy data!
			$post_id = (int) $p->term_id;
		else
			$post_id = (int) $p;
		
		$status = array();
		if( $post_id == 0 ) die( __('Invalid Post ID', $this->the_plugin->localizationName) );

		if ( $this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!
			$post = $this->the_plugin->__tax_get_post( $p, ARRAY_A );
			if( trim($post_content) == "empty" ){
				$post_content = $this->the_plugin->getPageContent( $post, $post['description'], true );
			}
			$post_title = $post['name'];
		}
		else {
			$post = get_post( (int) $p, ARRAY_A);
			
			if( trim($post_content) == "empty" ){  
				$post_content = $this->the_plugin->getPageContent( $post, $post['post_content'] );
			}
			$post_content = $this->strip_shortcode($post_content);
			$post_title = $post['post_title'];
		}
		
		if( count($post) > 0 ){
	
			if ( $this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!

				$psp_current_taxseo = $this->the_plugin->__tax_get_post_meta( null, $p );
				if ( is_null($psp_current_taxseo) || !is_array($psp_current_taxseo) )
					$psp_current_taxseo = array();

				$post_metas = $this->the_plugin->__tax_get_post_meta( $psp_current_taxseo, $p, 'psp_meta' );

			} else
				$post_metas = get_post_meta( (int) $p, 'psp_meta', true);
				
			if ( !is_array($post_metas) )
				$post_metas = array();

			$status['kw_density'] 		= $this->score_keyword_density( $this->get_keyword_density($post_content, $kw, false) );
			$status['title'] 			= $this->score_title( $post_title, $kw );
			$status['meta_description'] = $this->score_meta_description( isset($post_metas['description']) ? $post_metas['description'] : '', $kw );
			$status['meta_keywords'] 	= $this->score_meta_keywords( isset($post_metas['keywords']) ? $post_metas['keywords'] : '', $kw );
			$status['permalink'] 		= $this->score_permalink( $this->get_permalink($p), $kw );
			
			if ( !$this->the_plugin->__tax_istax( $p ) ) //taxonomy data!
				$status['images_alt'] 		= $this->score_images_alt( $post_content, $kw );
				
			$status['first_paragraph'] 	= $this->score_first_paragraph( $this->get_first_paragraph($post_content), $kw );
			$status['embedded_content'] = $this->score_embedded_content( $post_content );
			$status['enough_words'] 	= $this->score_enough_words( $post_content, ($this->the_plugin->__tax_istax( $p ) ? 50 : 250) );
			
			if ( !$this->the_plugin->__tax_istax( $p ) ) { //taxonomy data!
				$status['html_bold'] 		= $this->score_html_bold( $post_content, $kw );
				$status['html_italic'] 		= $this->score_html_italic( $post_content, $kw );
			}

			// calculate the scores
			$score = 0;
			foreach ($status as $key => $value) {
				$score = $score + $value["score"];
			}

			// transform in percents
			if ( $score > 0 )
				$score = number_format( ( ( 100 * $score ) / count($status) ), 1 );
			else
				$score = '0';

			// save the status into DB
			//$this->save_seo_score( $post_id, $status, $score, $kw );

			$ret = array(
				'status' 		=> 'valid',
				'post_id'		=> $post_id,
				'score'			=> $score,
				'kw'			=> $kw,
				'data'			=> $status
			);

			if( $returnAs == 'die' ){
				die(json_encode($ret));
			}
			elseif( $returnAs == 'array' ){
				return $ret;
			}
		}
	}

	/**
	* Save score
	*
	* @return string
	*/
	public function save_seo_score( $post_id=0, $status=array(), $score=0, $kw='' )
	{
		if( count($status) > 0 && $post_id > 0 ){

			update_post_meta( $post_id, 'psp_status', $status );
			update_post_meta( $post_id, 'psp_score', $score );
			update_post_meta( $post_id, 'psp_kw', $kw );
		}
	}
	
	function strip_shortcode( $text ) {
		return preg_replace( '`\[[^\]]+\]`s', '', $text );
	}
	
	/**
	* Get first paragraph from a WordPress post. Use inside the Loop.
	*
	* @return string
	*/
	public function get_first_paragraph( $str )
	{
		$str = wpautop( $this->strip_shortcode($str) );

		$base = '';
		$c = 0; $pos = 0; $pos2 = 0;
		do {

			$str = substr($str, $pos);
			$pos2 = strpos( $str, '</p>' ) + 4;
			
			$base = substr($str, 0, $pos2);
			$base = strip_tags($base);
			$base = preg_replace('/\s(\s+)/im', ' ', $base);
			$base = trim($base);

			$pos = $pos2;
			$c++;
		} while ( $c < 20 && empty($base) );

		return $base;
	}
	
	/**
	* Get first paragraph from a WordPress post. Use inside the Loop.
	*
	* @return string
	*/
	public function get_permalink( $post )
	{
		$url = '';
		if ( $this->the_plugin->__tax_istax( $post ) ) { //taxonomy data!
			$url = get_term_link( $post->term_id, $post->taxonomy );
		} else {
			$url = get_permalink( (int) $post );
		}
		return $url;
	}


	/**
	* Auto generate meta description from string
	*
	* @return string
	*/
	public function gen_meta_desc( $str='' )
	{
		// return $str;
		$base = '';
  
		$str =  $this->strip_shortcode( $str );
		$str = strip_tags($str);
		$str = preg_replace('/\s(\s+)/im', ' ', $str);
		$str = trim($str);
  
		if(trim($str) != ""){
			$base = $this->the_plugin->utf8->substr($str, 0, 157);
			if( $this->the_plugin->utf8->strlen($base) == 157 ){
				$base .= '...';
			}
		}

		return $base;
	}

	/**
	* Auto generate meta keywords from string
	*
	* @return string
	*/
	public function gen_meta_keywords( $str='', $return_nr=10, $uniqueWords=true )
	{
		$str =  $this->strip_shortcode( $str );
		
		$base = '';
		$str = preg_replace('#<br\s*/?>#i', " ", $str);
		$str = strip_tags($str);

		// get custom user stop words list
		$stopwords = array("a", "you", "if");
		$stopwords_db = $optimizeSettings['meta_keywords_stop_words'];  
		
		if( isset($stopwords_db) && trim($stopwords_db) != '' ) {
			$stopwords_db = explode(',', $stopwords_db);
			$stopwords = array_map('trim', $stopwords_db);
		}

		$base = $this->extract_common_words($str, $stopwords, $return_nr);
		if ( $uniqueWords ) {
			return implode(", ", array_keys($base) );
		} else {
			return $base;
		}
	}

	/**
	* number of words from string
	*
	* @return string
	*/
	public function gen_count_words( $str='', $uniqueWords=true )
	{
		if (empty($str)) return 0;

		$words = $this->gen_meta_keywords( $str, 50000, $uniqueWords );

		if ( $uniqueWords ) {
			$word_count = explode(',', $words);
			$word_count = array_map('trim', $word_count);
			$word_count = count($word_count);
		} else {
			$word_count = array_sum($words);
		}

		return $word_count;
	}
	
	/**
	* number of occurences of needle in string
	*
	* @return string
	*/
	public function gen_count_occurences( $string, $needle, $case_sensitive = false )
	{
        if ($case_sensitive === false) {
            $string = $this->the_plugin->utf8->strtolower($string);
            $needle = $this->the_plugin->utf8->strtolower($needle);
        }

        return $this->the_plugin->utf8->substr_count($string, $needle);
	}
	
	/**
	* Get first paragraph from a WordPress post. Use inside the Loop.
	*
	* @return string
	*/
	public function get_keyword_density( $content, $kw, $single=true )
	{
		//the total number of words in the post content
		$nb_words = 0;
		if ( !empty( $content ) )
			$nb_words = $this->gen_count_words( $content, false );

		//the total number of focus keyword occurences in the post cotent
		$kw_occ = 0;
		if ( !empty( $content ) && !empty( $kw ) )
			$kw_occ = $this->gen_count_occurences( $content, $kw );

		$__density = 0;
		if ( $nb_words>0 && $kw_occ>0 ) {
			$__density = ( $kw_occ / $nb_words ) * 100;
			$__density = number_format($__density, 1);
		}
		
		$ret = array(
			'content'		=> $content,
			'kw'			=> $kw,
			'nb_words' 		=> $nb_words,
			'kw_occurences' => $kw_occ,
			'density'		=> $__density
		);
		return ($single!==true ? $ret : $ret['density']);
	}


	/**
	* Most used words from string
	*
	* @return string
	*/
	private function extract_common_words($string, $stop_words, $max_count = 10) 
	{
		$string = preg_replace('/ss+/i', '', $string);
		$string = trim($string); // trim the string
		//$string = preg_replace('/[^a-zA-Z -]/', '', $string); // only take alphabet characters, but keep the spaces and dashes too…
		$string = $this->the_plugin->utf8->strtolower($string); // make it lowercase

		//preg_match_all('/\b.*?\b/i', $string, $match_words);
		preg_match_all('/\\pL[\\pL\\p{Mn}\'-]*/u', $string, $match_words);
		$match_words = $match_words[0];
		foreach ( $match_words as $key => $item ) {
			if ( $item == '' || in_array($this->the_plugin->utf8->strtolower($item), $stop_words) || $this->the_plugin->utf8->strlen($item) <= 3 ) {
				unset($match_words[$key]);
			}
		}

		$word_count = $this->the_plugin->utf8->str_word_count( implode(" ", $match_words) , 1);
		$frequency = array_count_values($word_count);
		arsort($frequency);

		//arsort($word_count_arr);
		$keywords = array_slice($frequency, 0, $max_count);

		return $keywords;
	}

	/**
	 * Check if the keyword is contained in the title.
	 *
	 * @param string  $page_title
	 * @param string  $focus_kw
	 * @return array $results   The results array.
	 */
	function score_title( $page_title, $focus_kw )
	{
		$str = trim($page_title);
		$str = $this->the_plugin->utf8->strtolower($page_title);
		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$min_length    = 5;
		$max_length    = 70;

		$msgs = array(
			'missing' 			=> __( "Bad, please create a page title.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page title, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'less_characters' 	=> __( "Bad, the page title contains %d characters, which is less than the recommended minimum of %d characters", $this->the_plugin->localizationName ),
			'more_characters' 	=> __( "Bad, the page title contains %d characters, which is more than the viewable limit of %d characters", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the keyword / phrase <strong>%s</strong> does not appear in the page title.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your title contains between %d and %d characters and contains your most important keywords.", $this->the_plugin->localizationName )
		);
        
        $ret = array(
            'debug'     => array(
                'str'       => $str,
                'focus_kw'  => $focus_kw
            )
        );

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			$length = $this->the_plugin->utf8->strlen( $str );

			if ( $length < $min_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_characters'], $length, $min_length)
				));
			}elseif( $length > $max_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['more_characters'], $length, $max_length)
				));
			}
			else{

				if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
					return array_merge($ret, array(
						'score' 	=> 0.5,
						'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw)
					));
				}else{
					return array_merge($ret, array(
						'score' 	=> 1,
						'msg'		=> sprintf($msgs['good'], $min_length, $max_length),
					));
				}
			}
		}
	}

	/**
	 * Check if the keyword is contained in the meta description.
	 *
	 * @param string  $page_meta_description
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_meta_description( $page_meta_description, $focus_kw )
	{
		$str = trim($page_meta_description);
		$str = $this->the_plugin->utf8->strtolower($page_meta_description);
		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$min_length    = 70;
		$max_length    = 160;

		$msgs = array(
			'missing' 			=> __( "Bad, please create a page meta description.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page meta description, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'less_characters' 	=> __( "Bad, the page meta description contains %d characters, which is less than the recommended minimum of %d characters", $this->the_plugin->localizationName ),
			'more_characters' 	=> __( "Bad, the page meta description contains %d characters, which is more than the viewable limit of %d characters", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the keyword / phrase <strong>%s</strong> does not appear in the page meta description.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your meta description contains between %d and %d characters and contains your most important keywords.", $this->the_plugin->localizationName )
		);
        
        $ret = array(
            'debug'     => array(
                'str'       => $str,
                'focus_kw'  => $focus_kw
            )
        );

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			$length = $this->the_plugin->utf8->strlen( $str );

			if ( $length < $min_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_characters'], $length, $min_length)
				));
			}elseif( $length > $max_length ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['more_characters'], $length, $max_length)
				));
			}
			else{
				if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
					return array_merge($ret, array(
						'score' 	=> 0.5,
						'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
						'debug'		=> array(
							'str' 		=> $str,
							'focus_kw' 	=> $focus_kw
						)
					));
				}else{
					return array_merge($ret, array(
						'score' 	=> 1,
						'msg'		=> sprintf($msgs['good'], $min_length, $max_length),
					));
				}
			}
		}
	}

	/**
	 * Check if the keyword is contained in the meta keywords.
	 *
	 * @param string  $page_meta_keywords
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_meta_keywords( $page_meta_keywords, $focus_kw )
	{
		$str = trim($page_meta_keywords);
		$str = $this->the_plugin->utf8->strtolower($page_meta_keywords);
		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, please create page meta keywords.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have page meta keywords, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the keyword / phrase <strong>%s</strong> does not appear in the page meta keywords.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your meta keywords contains contains your most important keywords.", $this->the_plugin->localizationName )
		);
        
        $ret = array(
            'debug'     => array(
                'str'       => $str,
                'focus_kw'  => $focus_kw
            )
        );

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw)
				));
			}else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if the keyword is contained in the permalink.
	 *
	 * @param string  $page_id
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_permalink( $url, $focus_kw )
	{
		$str = trim($url);
		$str = $this->the_plugin->utf8->strtolower($url);
		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);
		$focus_kw = sanitize_title($focus_kw);
  
		$msgs = array(
			'missing' 			=> __( "Bad, please create a page permalink.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have a page permalink, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the keyword / phrase <strong>%s</strong> does not appear in the page permalink.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your permalink contains your most important keywords.", $this->the_plugin->localizationName )
		);
        
        $ret = array(
            'debug'     => array(
                'str'       => $str,
                'focus_kw'  => $focus_kw
            )
        );

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw)
				));
			}else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if the keyword is contained in the permalink.
	 *
	 * @param string  $page_first_paragraph
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_first_paragraph( $page_first_paragraph, $focus_kw )
	{
		$str = trim($page_first_paragraph);
		$str = $this->the_plugin->utf8->strtolower($page_first_paragraph);
		$focus_kw = trim($focus_kw);
		$focus_kw = $this->the_plugin->utf8->strtolower($focus_kw);

		$msgs = array(
			'missing' 			=> __( "Bad, please create at least one paragraph on your page content.", $this->the_plugin->localizationName ),
			'missing_focus_kw'	=> __( "Bad, you have at least one paragraph on your page content, but you must create a focus keyword.", $this->the_plugin->localizationName ),
			'no_focus_kw' 		=> __( "Bad, the keyword / phrase <strong>%s</strong> does not appear in your content first paragraph.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your your content first paragraph contains your most important keywords.", $this->the_plugin->localizationName )
		);
        
        $ret = array(
            'debug'     => array(
                'str'       => $str,
                'focus_kw'  => $focus_kw
            )
        );

		if ( $str == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));
		}
		else if ( $focus_kw == "" ) {
			return array_merge($ret, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing_focus_kw']
			));
		}
		else {
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $str) == false ){
				return array_merge($ret, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['no_focus_kw'], $focus_kw),
				));
			}else{
				return array_merge($ret, array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good']),
				));
			}

		}
	}

	/**
	 * Check if content have embedded content
	 *
	 * @param string  $page_content
	 * @return array  $results   The results array.
	 */
	function score_embedded_content( $page_content )
	{
		if ( !empty($this->the_plugin->charset) )
			$html = pspphpQuery::newDocumentHTML( $page_content, $this->the_plugin->charset );
		else
			$html = pspphpQuery::newDocumentHTML( $page_content );
		$page_content = trim($page_content);

		$msgs = array(
			'missing' 			=> __( "Embedded content - "."Bad, please add some content for your page.", $this->the_plugin->localizationName ),
			'frame_detect' 		=> __( "Bad, frames can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'iframe_detect' 	=> __( "Bad, iframes can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'flash_detect' 		=> __( "Bad, flash can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'video_detect' 		=> __( "Bad, video can cause problems on your web page because search engines will not crawl or index the content within them.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your your content don't have any embedded content <i>(frame, iframe, object, embed or HTML5 video)</i>.", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			/*return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);*/
		}
		else {
			if( $html->find('frame')->size() > 0 ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['frame_detect']
				);
			}elseif( $html->find('iframe')->size() > 0 ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['iframe_detect']
				);
			}elseif( $html->find('embed, object')->size() > 0 ){
				return array(
					'score' 	=> 0,
					'msg'		=> $msgs['flash_detect']
				);
			}elseif( $html->find('video')->size() > 0 ){
				return array(
					'score' 	=> 0.7,
					'msg'		=> $msgs['video_detect']
				);
			}else{
				return array(
					'score' 	=> 1,
					'msg'		=> $msgs['good']
				);
			}

		}
	}

	/**
	 * Check if content have enough words
	 *
	 * @param string  $page_content
	 * @return array  $results   The results array.
	 */
	function score_enough_words( $page_content, $chars_limit = 250 )
	{
		$good_words_count   = isset($chars_limit) && $chars_limit>0 ? $chars_limit : 250;
		$page_content = strip_tags($page_content);
		$words = (int) @$this->the_plugin->utf8->str_word_count($page_content);

		$page_content = strip_tags($page_content);
		$page_content = trim($page_content);

		$msgs = array(
			'missing' 			=> __( "Enough words - "."Bad, please add some content for your page.", $this->the_plugin->localizationName ),
			'less_words' 		=> __( "Bad, the main content contains %d word(s), which is less than the recommended minimum of %d words", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, the main content contains %d words and the recommended minimum is %d words.", $this->the_plugin->localizationName )
		);

		if ( $page_content == "" ) {
			/*return array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			);*/
		}
		else {
			$lenght = $words;

			if( $lenght < $good_words_count ){
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less_words'], $lenght, $good_words_count)
				);
			}else{
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $lenght, $good_words_count)
				);
			}
		}
	}

	/**
	 * Check if the keyword is contained in the images alt.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_images_alt( $page_content, $focus_kw )
	{
		if ( !empty($this->the_plugin->charset) )
			$html = pspphpQuery::newDocumentHTML( $page_content, $this->the_plugin->charset );
		else
			$html = pspphpQuery::newDocumentHTML( $page_content );

		$msgs = array(
			'missing' 			=> __( "Bad, your content has no images.", $this->the_plugin->localizationName ),
			'less' 				=> __( "Bad, your content has %d images and %d of this contains your most important keywords.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your content has %d images and %d of this contains your most important keywords.", $this->the_plugin->localizationName )
		);

		$total_images = $html->find('img')->size();
		if( $total_images > 0 ){
			
			// fix case sensivity problem!
			$kw_images = 0;
			$imgList = $html->find('img');
			foreach( $imgList as $tag ) {
				$tag = pspPQ($tag); // cache the object
				$attrAlt = $tag->attr('alt');
				$attrAlt = $this->the_plugin->utf8->strtolower($attrAlt);
				if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $attrAlt) == true ){
					$kw_images++;
				}
			}
			//$kw_images = $html->find('img[alt="' . ( $focus_kw ) . '"]')->size();
			$kw_images = isset($kw_images) ? $kw_images : 0;
			if( $kw_images > 0 ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_images, $kw_images )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_images, $kw_images )
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'] )
			);
		}
	}

	/**
	 * Check if the keyword is contained in the HTML bold / strong tag.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_html_bold( $page_content, $focus_kw )
	{
		if ( !empty($this->the_plugin->charset) )
			$html = pspphpQuery::newDocumentHTML( $page_content, $this->the_plugin->charset );
		else
			$html = pspphpQuery::newDocumentHTML( $page_content );

		$msgs = array(
			'missing' 				=> __( "Bad, your content has no bold elements.", $this->the_plugin->localizationName ),
			'less' 					=> __( "Bad, your content has %d bold elements and none of this contains your most important keywords.", $this->the_plugin->localizationName ),
			'good' 					=> __( "Great, your content has %d bold elements and at least 1 contains your most important keywords.", $this->the_plugin->localizationName )
		);

		$total_bolds = $html->find('bold,strong')->size();
		if( $total_bolds > 0 ){
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $html->find('bold,strong')->text()) == true ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_bolds )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_bolds )
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'], $total_bolds )
			);
		}
	}

	/**
	 * Check if the keyword is contained in the HTML italic tag.
	 *
	 * @param string  $page_content
	 * @param string  $focus_kw
	 * @return array  $results   The results array.
	 */
	function score_html_italic( $page_content, $focus_kw )
	{
		if ( !empty($this->the_plugin->charset) )
			$html = pspphpQuery::newDocumentHTML( $page_content, $this->the_plugin->charset );
		else
			$html = pspphpQuery::newDocumentHTML( $page_content );

		$msgs = array(
			'missing' 			=> __( "Bad, your content has no italic elements.", $this->the_plugin->localizationName ),
			'less' 				=> __( "Bad, your content has %d italic elements and none of this contains your most important keywords.", $this->the_plugin->localizationName ),
			'good' 				=> __( "Great, your content has %d italic elements and at least 1 contains your most important keywords.", $this->the_plugin->localizationName )
		);

		$total_italics = $html->find('em,i')->size();
		if( $total_italics > 0 ){
			if( preg_match('/' . preg_quote($focus_kw, '/') . '/i', $html->find('em,i')->text()) == true ){
				return array(
					'score' 	=> 1,
					'msg'		=> sprintf($msgs['good'], $total_italics )
				);
			}
			else{
				return array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['less'], $total_italics )
				);
			}
		} else {
			return array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing'], $total_italics )
			);
		}
	}
	
	/**
	 * Give a score based on keyword density
	 *
	 * @param string  $page_title
	 * @param string  $focus_kw
	 * @return array $results   The results array.
	 */
	function score_keyword_density( $density_arr=array() )
	{
		//keyword density
		$__nb_words = $density_arr['nb_words'];
		$__kw_occurences = $density_arr['kw_occurences'];
		$__density = $density_arr['density'];

		$msgs = array(
			'missing' 			=> __( "Keyword density - "."Bad, please add some content for your page.", $this->the_plugin->localizationName ),
			'missing_kw' 		=> __( "Bad, keyword density is 0%%, because the keyword / phrase <strong>%s</strong> does not appear in your page content.", $this->the_plugin->localizationName ),
			'bad' 		=> __( "Bad, keyword density is %.1f%% and it's not between %.1f%% and %.1f%%, which is the recommended interval. Your content have %d allowed words and the number of keyword occurences in the content is %d.", $this->the_plugin->localizationName ),
			'poor' 		=> __( "Poor, keyword density is %.1f%% and it's not between %.1f%% and %.1f%%, which is the recommended interval. Your content have %d allowed words and the number of keyword occurences in the content is %d.", $this->the_plugin->localizationName ),
			'good'		=> __( "Great, keyword density is %.1f%% and it's between %.1f%% and %.1f%%, which is the recommended interval. Your content have %d allowed words and the number of keyword occurences in the content is %d.", $this->the_plugin->localizationName )
		);
		
		$details = array(
			'details'		=> array(
				'nb_words' 		=> $__nb_words,
				'kw_occurences' => $__kw_occurences,
				'density'		=> $__density
			)
		);

		if ( $__nb_words == 0 ) {
			/*return array_merge($details, array(
				'score' 	=> 0,
				'msg'		=> $msgs['missing']
			));*/
		} else if ( $__kw_occurences == 0 ) {
			return array_merge($details, array(
				'score' 	=> 0,
				'msg'		=> sprintf($msgs['missing_kw'], $density_arr['kw'])
			));
		} else {
			
			if ( $__density>=2 && $__density<=4.5 ) {
					return array_merge($details, array(
						'score' 	=> 1,
						'msg'		=> sprintf($msgs['good'], $__density, 2, 4.5, $__nb_words, $__kw_occurences)
					));
			} else if ( $__density>=1 && $__density<=6 ) {
					return array_merge($details, array(
						'score' 	=> 0.5,
						'msg'		=> sprintf($msgs['poor'], $__density, 2, 4.5, $__nb_words, $__kw_occurences)
					));
			} else {
				return array_merge($details, array(
					'score' 	=> 0,
					'msg'		=> sprintf($msgs['bad'], $__density, 2, 4.5, $__nb_words, $__kw_occurences)
				));
			}
		}
	}
}