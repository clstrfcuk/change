<?php
class DT_RadioButtonTaxonomy {
	public $taxonomy;

	public function __construct( $arg ){
	 	$this->taxonomy = $arg;

	 	// Load admin scripts
      	add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );

	 	require_once plugin_dir_path ( __FILE__ ) . '/class/class.WordPress_Radio_Taxonomy.php';
	 	require_once plugin_dir_path ( __FILE__ ) . '/class/class.Walker_Category_Radio.php';
	 	new DT_WordPress_Radio_Taxonomy( $this->taxonomy );
	}

	public function admin_script() {
		if( empty( $this->taxonomy) ) return;
		if ( function_exists( 'get_current_screen' ) ){
			$screen = get_current_screen();

			if ( ! is_wp_error( $screen ) && in_array( $screen->base, array( 'edit', 'post' ) ) ) {
				wp_enqueue_script ( 'dt-radiotax-script', plugin_dir_url ( __FILE__ ) . 'js/dt.radiotax.js', array ('jquery'), false, true );
			}
		}
	}

}