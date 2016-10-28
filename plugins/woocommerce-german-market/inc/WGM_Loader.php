<?php
/**
 * Automatic class Loader
 * @author ap
 */
class WGM_Loader {

    /**
     * Registers autoloader function to spl_autoload
     * @access public
     * @static
     * @author ap
     * @return void
     */
    public static function register(){
		spl_autoload_register( 'WGM_Loader::load' );
	}

    /**
     * Unregisters autoloader function with spl_autoload
     * @access public
     * @static
     * @author ap
     * @return void
     */
    public static function unregister(){
		spl_autoload_unregister( 'WGM_Loader::load' );
	}

    /**
     * Autloading function
     * @param string $classname
     * @access public
     * @static
     * @author ap
     * @return void
     */
    public static function load( $classname ){
		$file =  dirname( __FILE__ ) . DIRECTORY_SEPARATOR . ucfirst( $classname ) . '.php';
			
		if( file_exists( $file ) ) require_once $file;
	}
}
?>