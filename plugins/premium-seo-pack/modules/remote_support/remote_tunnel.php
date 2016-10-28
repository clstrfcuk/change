<?php 

/**
 * AA-Team - Remote Support
 * ========================
 *
 * @package	ABM
 * @author	AA-Team
 */
class abmRemoteSupport
{
	/**
	 * Base storages
	 *
	 * @var array
	 */
	private $config = array();
	private $response = array(); 
	
	public function __construct()
	{
		$this->load_config();
		
		$this->validate_connection();
		
		$this->triggers();
	}
	
	private function load_config()
	{
		require_once( 'remote_init.php' );
		$this->config = $aa_tunnel_config;
	}
	
	private function validate_connection()
	{
		if( 
			!isset($this->config['key']) ||
			trim($this->config['key']) == ""
		) {
			$this->print_error( array(
				'code' => 100,
				'msg' => "Unable to load the key from remote_init.php file"
			), 'fatal' );
		}
		
		$coming_key = isset($_REQUEST['connection_key']) ? $_REQUEST['connection_key'] : '';
		if( trim($coming_key) == "" || $coming_key != $this->config['key'] ){
			$this->print_error( array(
				'code' => 101,
				'msg' => "Invalid key!"
			), 'fatal' );
		}
		
		return true;
	}
	
	private function print_error( $error=array(), $type="fatal" )
	{
		if( $type == 'fatal' ){
			var_dump('<pre>',$error,'</pre>'); die;  
		}
	}
	
	private function print_response()
	{
		die( json_encode( $this->response ) );
	}
	
	private function triggers()
	{
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		 
		if( $action == 'browse_folder' ) 
			$this->browse_folder();
		
		if( $action == 'open_file' ) 
			$this->open_file();
		
		if( $action == 'save_file' ) 
			$this->save_file();
		
		$this->print_response();
	}
	
	private function browse_folder()
	{
		$structures = array();
		$the_folder = str_replace("../", "", $_REQUEST['folder']);
		
		$request_path = $this->config['path'] . $the_folder;
		
		if( file_exists($request_path) ) {
		
			$files = glob( $request_path . "*" ); 
			natcasesort( $files );
			if( count($files) > 0 ) {
				// All folders
				foreach( $files as $file ) {
					if( is_dir($file) ) {
						
						$structures['folder'][] = array(
							'name' => end( explode( "/", ($file) ) ),
							'path' => str_replace( $this->config['path'], "", $file )
						);
					}
				}
				
				// All files
				foreach( $files as $file ) {
					if( $file != '.' && $file != '..' && !is_dir($file) ) {
						$structures['file'][] = array(
							'name' => end( explode( "/", ($file) ) ),
							'extension' => end( explode(".", $file ) ),
							'path' => str_replace( $this->config['path'], "", $file ),
							'file_path' => $file,
							'file_alias' => md5( $file ),
						);
					}
				}
			}
			 
			$this->response = array(
				'status' => 'valid',
				'folder_path' => $request_path . $file,
				'data' => $structures
			);
		
		}else {
			
			$this->response = array(
				'status' => 'invalid',
				'msg' => 'request path don\'t exists: ' . $request_path 
			);
		}
	}
	
	private function save_file()
	{
		$file = isset($_REQUEST['file']) ? urldecode($_REQUEST['file']) : '';
		$file_content = isset($_REQUEST['file_content']) ? @base64_decode($_REQUEST['file_content']) : '';
		
		if( file_exists( $file )) {
			$write_file = @file_put_contents( $file, $file_content );

			if( $write_file ){
				$this->response = array(
					'status' => 'valid',
					'file_path' => $file,
					'file_type' => end( explode(".", $file ) ),
				);
			}
			
			else{
				$this->response = array(
					'status' => 'invalid',
					'msg' => 'Unable to write on file',
					'file_type' => end( explode(".", $file ) ),
					'file_path' => $file
				);
			}
			
		}else{
			$this->response = array(
				'status' => 'invalid',
				'msg' => 'Unable find the file',
				'file_type' => end( explode(".", $file ) ),
				'file_path' => $file
			);
		}
	}
	
	private function open_file()
	{
		$file = isset($_REQUEST['file']) ? $this->config['path'] . $_REQUEST['file'] : '';
	
		if( file_exists( $file ) ) {
			$file_content = file_get_contents( $file );
			
			$this->response = array(
				'status' => 'valid',
				'file_path' => $file,
				'file_type' => end( explode(".", $file ) ),
				'file_name' => end( explode("/", $file ) ),
				'file_alias' => md5( $file ),
				'content' => $file_content
			);
		}
	}

} // END class

// create the instance
new abmRemoteSupport();
