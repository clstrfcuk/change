<?php
// =============================================================================
// FUNCTIONS/EMAIL-PROVIDER.PHP
// -----------------------------------------------------------------------------
// Holds the common functionality for email providers.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_Provider {

  protected $config, $path, $file, $plugin, $view;

  function __construct( $plugin, $file ) {

    //
    // The main plugin instance is injected, so we can reference options
    // anywhere.
    //

    $this->plugin = $plugin;


    //
    // Set the path for this provider.
    //

    $this->file = $file;
    $this->path = dirname( $file );
    $this->load_config();


    //
    // Create a view handler for this email provider. Allows the use of the
    // local view folder.
    //

    $this->view = new X_Email_Integration_View_Handler( $this->plugin, $this->path );


    //
    // Register general hooks.
    //

    add_action( 'init', array( $this, 'init' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );


    //
    // Inheriting class can execute at constructor time.
    //

    $this->setup();

  }


  //
  // Load configuration.
  //

  function load_config() {

    $this->config = array();
    $file         = $this->path . '/config.php';
    $config       = ( file_exists( $file ) ) ? include( $file ) : false;
    $this->config = ( is_array( $config ) ) ? $config : array();

  }


  //
  // Accessor Methods (Allows read only access to provider data).
  //

  function get_default_options() {
    return $this->config['default_options'];
  }

  function get_name() {
    return $this->config['name'];
  }

  function get_title() {
    return $this->config['title'];
  }

  function get_about_items() {
    return $this->config['about_items'];
  }

  function get_metaboxes() {

    $metaboxes = array();
    foreach ( $this->config['settings_metaboxes'] as $name => $mb ) {
      $metaboxes[$name] = array(
        'title'   => $mb['title'],
        'content' => $this->view->make( $mb['view'] )
      );
    }

    return $metaboxes;

  }

  function register_validators( $validators = array() ) {
    foreach ( $validators as $key => $callback ) {
      $this->plugin->options->add_validator( $key, $callback );
    }
  }


  //
  // Stub functions.
  // These should be overridden in the child class (i.e. specific providers).
  //

  function init() { }
  function admin_init() { }
  function setup() { }
  function before_save() { }
  function settings_page() { }
  function get_normalized_list() { return array(); }
  function subscribe( $list, $userdata ) { return new WP_Error( 'x-email-forms', __( ' Email provider lacking subscription method.' ) ); }

}