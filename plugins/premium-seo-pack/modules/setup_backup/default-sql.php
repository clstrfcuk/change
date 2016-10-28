<?php
/*
* Define class pspSetupBackup
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSetupBackup') != true) {
    class pspSetupBackup
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
		
		private $settings = array();
		
		static protected $_instance;
		

		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/setup_backup/';
			$this->module_folderPath = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/setup_backup/';
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'setup_backup' );
			

			@ini_set('memory_limit', '512M');
			@set_time_limit ( 0 );

			$filename_tables = $this->module_folderPath . 'db/tables.sql';
			$this->install_tables( $filename_tables );

			$filename_tables_data = $this->module_folderPath . 'db/tables_data.sql';
			$this->install_tables_data( $filename_tables_data );
        }


        public function install_tables( $filename ) {
			if ( $this->the_plugin->verifyFileExists( $filename ) ) { //verify file existance!

				global $wpdb;
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				$wpdb->show_errors();

				$sql = file_get_contents( $filename );
				if ( $sql === false ) return false;

				$sql = str_replace('{wp_prefix}', $wpdb->prefix, $sql);
				$sql = $this->find_table_text( $sql );
				if ( is_array($sql) && count($sql)>0 )
					foreach ( $sql as $key => $val )
						dbDelta( $val );
			}
			return false; //return error!
        }

        public function install_tables_data( $filename ) {
			if ( $this->the_plugin->verifyFileExists( $filename ) ) { //verify file existance!

        		global $wpdb;

				$file_handle = fopen( $filename, "rb" );
				if ( $file_handle === false ) return false;
				while ( !feof( $file_handle ) ) {

					   $sql = fgetss( $file_handle );
					   if ( $sql === false || empty( $sql ) || trim( $sql ) == '' ) continue 1;

					   $sql = str_replace('{wp_prefix}', $wpdb->prefix, $sql);
					   $wpdb->query( $sql );
				}
				fclose( $file_handle );
			}
			return false; //return error!
        }
        
        private function find_table_text( $str='' ) {
        	$start = 'CREATE TABLE';
        	$end = ';';
        	$pattern = sprintf( '/(%s.+?%s)/ims', preg_quote($start, '/'), preg_quote($end, '/') );

        	if ( preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER) ) {
        		return $matches[1];
        	}
        	return array();
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
    }
}

// Initialize the pspSetupBackup class
$pspSetupBackup = new pspSetupBackup($this->cfg);