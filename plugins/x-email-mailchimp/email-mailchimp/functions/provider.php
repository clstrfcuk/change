<?php

// =============================================================================
// FUNCTIONS/PROVIDER.PHP
// -----------------------------------------------------------------------------
// Provides the specific logic for integration with the desired service. It
// extends the base provider class to ensure compatibility.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_MailChimp extends X_Email_Provider {

  //
  // Properties.
  //

  protected $api_wrapper, $api_key, $api_opts;


  //
  // Setup.
  //

  function setup() {
    $this->register_validators( array(
       'mc_api_key' => array( $this, 'validate_mc_api_key' ),
    ) );
  }


  //
  // Integration.
  //

  function settings_page() {

    $mc_list_refresh_url = add_query_arg( array( '_list_refresh_nonce' => wp_create_nonce( 'mc_list_refresh' ) ) );
    $this->plugin->set_transport( 'mc_list_refresh_url', $mc_list_refresh_url );

    if ( isset( $_REQUEST['_list_refresh_nonce'] ) ) {
      if ( wp_verify_nonce( $_REQUEST['_list_refresh_nonce'], 'mc_list_refresh' ) ) {
        $refresh = $this->refresh_list_cache();
        if ( isset( $refresh['message'] ) ) {
          $this->plugin->set_transport( 'mc_message', $refresh['message'] );
        }
      } else {
        wp_die( __( 'Permission denied. Unable to refresh list', '__x__' ) );
      }
    }

    if ( ! $this->plugin->options->get( 'mc_api_key' )) {
      unset( $this->config['settings_metaboxes']['mc_lists'] );
    }

  }


  //
  // API key validation.
  //

  function validate_mc_api_key( $value ) {

    $validate = $this->validate_api_key( $value );
    $this->mc_api_key_validate_failed = is_wp_error( $validate );

    return $this->mc_api_key_validate_failed;
  }


  //
  // Before saving.
  //

  function before_save() {

    $slug = $this->plugin->get_slug();

    if ( ( isset( $this->mc_api_key_validate_failed ) && $this->mc_api_key_validate_failed ) ) {
      $this->plugin->options->set( 'mc_api_key', '' );
      $this->plugin->options->set( 'mc_list_cache', array() );
      $this->plugin->options->set( 'mc_skip_double_opt_in', 'no' );
    } elseif( $this->plugin->options->was_modified( 'mc_api_key') ) {
      $this->refresh_list_cache();
    }

  }


  //
  // Code to interface with MailChimp API.
  //

  function get_wrapper() {
    return ( isset( $this->api_wrapper ) ) ? $this->api_wrapper : $this->make_api_wrapper();
  }


  function make_api_wrapper( $api_key = '', $single = false, $api_opts = array() ) {

    if ( ! class_exists( 'Mailchimp' ) ) {
      require_once( "$this->path/functions/vendor/mcapi/Mailchimp.php" );
    }

    if ( $api_key == '' ) {
      $api_key = $this->plugin->options->get( 'mc_api_key' );
    }


    //
    // Use local opts if set, otherwise those declared in setup if set.
    //

    $opts    = ( empty( $api_opts ) && ! empty( $this->api_opts ) ) ? $this->api_opts : $api_opts;
    $wrapper = new Mailchimp( $api_key, $opts );

    if ( $single ) {
      return $wrapper;
    }

    $this->api_wrapper = $wrapper;

    return $this->api_wrapper;

  }


  function validate_api_key( $key ) {

    $result = true;

    try {

      $mc_api = $this->make_api_wrapper( $key, true );
      $mc_api->call('/users/profile', array() );

    } catch ( Mailchimp_Invalid_ApiKey $e ) {

      $result = new WP_Error( 'x-mailchimp', __( 'Invalid API key.', '__x__' ) );

    } catch ( Exception $e ) {

      $result = new WP_Error( 'x-mailchimp', sprintf( __( 'Error while attempting to validate API key: [%s]', '__x__' ), get_class( $e ) ) );

    }

    return $result;

  }


  function retrieve_lists() {

    try {

      $mc_api = $this->get_wrapper();
      $result = $mc_api->call( '/lists/list', array() );

      if ( ! empty( $result['errors'] ) || ! isset( $result['total'] ) || ! isset( $result['data'] ) ) {
        throw new Exception( implode( ', ', $result['errors'] ) );
      }

    } catch ( Mailchimp_Error $e ) {

      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), get_class( $e ) ) );

    } catch ( Exception $e ) {

      return new WP_Error( 'x-mailchimp', sprintf( __( 'Error attempting to retrieve Mailchimp List: [%s]', '__x__' ), $e->getMessage() ) );
    }

    return $result;

  }


  function refresh_list_cache() {

    $response = array();
    $result   = $this->retrieve_lists();

    if ( is_wp_error( $result ) ) {

      $response['message'] = $result->get_error_message();

    } else {

      $lists = $this->reduce_list_result( $result );

      if ( empty( $lists ) ) {
        $response['message'] = __( 'Your account doesn\'t have any MailChimp lists. You should create some from your MailChimp admin page.', '__x__' );
      }

      if ( $lists == $this->plugin->options->get( 'mc_list_cache' ) ) {
        $response['message'] = __( 'Refresh complete. No updates at this time.', '__x__' );
      } else {
        $response['message'] = __( 'Lists updated!', '__x__' );
      }

      $this->plugin->options->set( 'mc_list_cache', $lists, true );

    }

    return $response;

  }


  function reduce_list_result( $results ) {

    $lists = array();

    foreach ( $results['data'] as $list ) {
      $lists[$list['id']] = array(
        'id'     => $list['id'],
        'web_id' => $list['web_id'],
        'name'   => $list['name']
      );
    }

    return $lists;

  }


  function get_normalized_list() {

    $items = array();
    $cache = $this->plugin->options->get( 'mc_list_cache' );

    foreach ( $cache as $item ) {
      $items[] = array(
        'id'             => $item['id'],
        'name'           => $item['name'],
        'provider'       => $this->config['name'],
        'provider_title' => $this->config['title']
      );
    }

    return $items;

  }


  function subscribe( $list_id, $user_data ) {

    $double_optin = ( 'yes' != $this->plugin->options->get( 'mc_skip_double_opt_in' ) );
    $send_welcome = ( 'yes' == $this->plugin->options->get('mc_send_welcome') || $double_optin == false );

    try {

      $mc_api     = $this->get_wrapper();
      $email      = array( 'email' => ( isset( $user_data['email_address'] ) ) ? $user_data['email_address'] : '' );
      $merge_vars = $this->make_merge_vars( $user_data );
      $result     = $mc_api->lists->subscribe( $list_id, $email, $merge_vars, 'html', $double_optin, false, true, $send_welcome );

      if ( ! isset( $result['email'] ) || ! isset( $result['euid'] ) ) {
        throw new Exception( __( 'Unhandled Exception', '__x__' ) );
      }

    } catch ( Mailchimp_Error $e ) {

      return new WP_Error( 'x-mailchimp', sprintf( __( 'MailChimp Error: %s. [%s]', '__x__' ), $e->getMessage(), get_class( $e ) ) );

    } catch ( Exception $e ) {

      return new WP_Error( 'x-mailchimp', sprintf( __( 'MailChimp Error: %s.', '__x__' ), $e->getMessage() ) );

    }

    return $result;

  }


  function make_merge_vars( $user_data ) {

    $vars     = array();
    $name_set = false;

    if ( isset( $user_data['full_name'] ) && $user_data['full_name'] != '' ) {

      $parts = explode( ' ', trim( $user_data['full_name'] ) );

      if ( count( $parts ) > 1 ) {
        $vars['LNAME'] = trim( array_pop( $parts ) );
        $vars['FNAME'] = trim( ( count( $parts ) > 1 ) ? implode( ' ', $parts ) : array_shift( $parts ) );
      } else {
        $vars['FNAME'] = trim( $user_data['full_name'] );
        $name_set = true;
      }

    }

    if ( isset( $user_data['first_name'] ) && $user_data['first_name'] != '' ) {
      $vars['FNAME'] = $user_data['first_name'];
    }

    if ( isset( $user_data['last_name'] ) && $user_data['last_name'] != '' && ! $name_set ) {
      $vars['LNAME'] = $user_data['last_name'];
    }

    return $vars;

  }

}