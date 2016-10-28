<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/CLASS-THEMECO-UPDATE-API.PHP
// -----------------------------------------------------------------------------
// Shared class between Themeco Products for request update information.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Update API
// =============================================================================

// Update API
// =============================================================================

if ( !class_exists( 'Themeco_Update_Api' ) ) :

	class Themeco_Update_Api {

		private static $instance;
		private static $base_url = 'https://community.theme.co/api-v2/packages/';
		private static $errors = array();

		private $updated = false;

		public function remote_request( ) {

			$args = apply_filters( 'themeco_update_api', array() );

			$args = wp_parse_args( $args, array(
				'api-key'  => 'unverified',
				'siteurl'  => preg_replace( '#(https?:)?//#','', esc_attr( untrailingslashit( network_home_url() ) ) ),
			) );

	    if ( !$args['api-key'] )
	      $args['api-key'] = 'unverified';

	    $request_url = self::$base_url . trailingslashit( $args['api-key'] );

	    unset($args['api-key']);

	    $uri = add_query_arg( $args, $request_url );

	    $request = wp_remote_get( $uri, array( 'timeout' => 15 ) );
	    $connection_error = array( 'code' => 4, 'message' => __( 'Could not establish connection. For assistance, please start by reviewing our article on troubleshooting <a href="https://community.theme.co/kb/connection-issues/">connection issues.</a>', '__x__' ) );

	    if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
	      self::store_error( $request );
	      return $connection_error;
	    }

			$data = json_decode( $request['body'], true );

			if ( defined('THEMECO_PRERELEASES') && THEMECO_PRERELEASES ) {
  			$data = $this->edge_filter( $data );
  		}

  		return $data;

	  }

		//
	  // Save connection errors.
	  //

	  public static function store_error( $wp_error ) {

	    if ( ! isset( self::$errors ) ) {
	      self::$errors = array();
	    }

	    array_push( self::$errors, (array) $wp_error );

	  }

	  public static function get_update_cache() {
	  	return get_site_option( 'themeco_update_cache', array() );
	  }

	  //
	  // Return any saved errors.
	  //

	  public static function get_errors() {

	    return isset( self::$errors ) ? self::$errors : array();

	  }

	  public function edge_filter( $data ) {

	  	if ( isset( $data['themes'] ) ) {

	  		foreach ($data['themes'] as $theme => $theme_data ) {

		  		if ( !isset( $theme_data['edge'] ) ) continue;

					$edge = $theme_data['edge'];
					unset($theme_data['edge']);
		  		$data['themes'][$theme] = array_merge( $theme_data, $edge );

		  	}

	  	}

	  	if ( isset( $data['plugins'] ) ) {

	  		foreach ($data['plugins'] as $plugin => $plugin_data ) {

		  		if ( !isset( $plugin_data['edge'] ) ) continue;

					$edge = $plugin_data['edge'];
					unset($plugin_data['edge']);
		  		$data['plugins'][$plugin] = array_merge( $theme_data, $edge );

		  	}

	  	}

	  	return $data;

	  }

	  public function update( $force = false ) {
	  	if ( $this->updated && !$force ) return;
	  	$response = $this->remote_request();
	  	do_action( 'themeco_update_api_response', $response );
	  	update_site_option( 'themeco_update_cache', apply_filters( 'themeco_update_cache', array(), $response ) );
	  	$this->updated = true;
	  }

		public static function refresh( $force = false ) {

			if ( !is_admin() )
				return false;

			if ( !isset( self::$instance ) )
				self::$instance = new self;

			self::$instance->update( $force );

			return true;
		}

	}

endif;