<?php
if ( !defined('ABSPATH') ) {
	$absolute_path = __FILE__;
	$path_to_file = explode( 'wp-content', $absolute_path );
	$path_to_wp = $path_to_file[0];

	/** Set up WordPress environment */
	if( file_exists( $path_to_wp.'/wp-load.php' ) ) {
		require_once( $path_to_wp.'/wp-load.php' );
	}
	else{
		require_once( '../../../../wp-load.php' );
	}

	global $psp;
 
	$cssFiles = $psp->admin_get_frm_style();

	$buffer = "";
	foreach ($cssFiles as $cssFile) {
		$buffer .= file_get_contents($cssFile);
	}
	 
	// Remove comments
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	 
	// Remove space after colons
	$buffer = str_replace(': ', ':', $buffer);
	 
	// Remove whitespace
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '    ', '    '), '', $buffer);
	 
	// Enable GZip encoding.
	if ( ! ini_get('zlib.output_compression') || 'ob_gzhandler' != ini_get('output_handler') ) ob_start();
	else ob_start("ob_gzhandler");
	 
	// Enable caching
	header('Cache-Control: public');
	 
	// Expire in one day
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
	 
	// Set the correct MIME type, because Apache won't set it for us
	header("Content-type: text/css");
	 
	// Write everything out
	echo $buffer;
    
    // try to write the buffer as .css
    if( !is_file( $psp->cfg['paths']['freamwork_dir_path'] . 'load-styles.css' ) ){
        file_put_contents( $psp->cfg['paths']['freamwork_dir_path'] . 'load-styles.css', $buffer);
    }
}