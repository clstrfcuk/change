<?php

// =============================================================================
// FUNCTIONS/FRAMEWORK/VIEW-HANDLER.PHP
// -----------------------------------------------------------------------------
// Provides an abstraction for rendering views.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_Integration_View_Handler {

  public $context;
  protected $plugin;

  function __construct( $plugin, $context ) {
    $this->plugin  = $plugin;
    $this->context = $context;
  }


  //
  // Render a view, and output immediately. Injects the plugin's options and
  // tranports data into the view.
  //

  function show( $view_name, $data = array() ) {

    $data = array_merge( $this->get_plugin_data(), $data );

    return $this->render( $this->context, $view_name, $data );

  }


  //
  // Render a view, but don't output it (i.e. just return HTML). Injects the
  // plugin's options and tranports data into the view.
  //

  function make( $view_name, $data = array() ) {

    $data = array_merge( $this->get_plugin_data(), $data );

    return $this->render( $this->context, $view_name, $data, false );

  }


  //
  // Used by the display functions to actually create a view.
  //

  function render( $context, $view_name, $data = array(), $echo = true ) {

    //
    // $echo is true by default, so this function will output the view. If set
    // to false it won't be output, but you can use the returned HTML.
    //

    ob_start();


    //
    // Load the request file, and pass in the provided data.
    //

    $this->require_if_exists( "{$context}/views/{$view_name}.php", $data );

    $buffer = ob_get_clean();

    if ( $echo == true ) {
      echo $buffer;
    }

    return $buffer;

  }


  //
  // Allows view data to be cached. This way we can nest views with the same data.
  //

  function get_plugin_data() {
    return ( isset( $this->data_cache ) ) ? $this->data_cache : $this->plugin->get_view_data();
  }


  //
  // Check to make sure a file exists before loading. Also extracts $data for
  // use within the file. This can be called directly if needed. (e.g. loading
  // the enqueue files).
  //

  function require_if_exists( $include_filename, $data = array() ) {

    if ( file_exists( $include_filename ) ) {

      if ( is_array( $data ) ) {
        $data['view']     = $this;
        $this->data_cache = $data;
        extract( $data );
        unset( $data );
      } else {
        trigger_error( __( '$data should be an array.', '__x__' ), E_USER_WARNING );
      }

      $this->inside_view = true;
      require( $include_filename );
      $this->inside_view = false;

      unset( $this->data_cache );

    } else {

      trigger_error( sprintf( __( 'View file does not exist: %s', '__x__' ), $include_filename ), E_USER_WARNING );

    }
  }

}