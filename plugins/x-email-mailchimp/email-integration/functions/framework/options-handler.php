<?php

// =============================================================================
// FUNCTIONS/FRAMEWORK/OPTIONS-HANDLER.PHP
// -----------------------------------------------------------------------------
// Provides an abstraction for handling plugin options between classes.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_Integration_Options_Handler {

  protected $plugin, $key, $options, $modified, $errors, $validators;

  function __construct( $plugin, $key) {

    $this->plugin = $plugin;
    $this->key = $key;
    $this->modified = array();
    $this->validators = array();
    $this->errors = array();

  }


  //
  // Load all options.
  //

  function load( $defaults = array() ) {
    $this->options = wp_parse_args( get_option($this->key), $defaults );
  }


  //
  // Return all options as an array and passes errors back.
  //

  function all() {
    return $this->options;
  }

  //
  // Retrieve a single option.
  //

  function get( $key ) {
    return ( isset( $this->options[$key] ) ) ? $this->options[$key] : false;
  }


  //
  // Set an option. This caches it locally, but doesn't save to the
  // database yet.
  //

  function set( $key, $value, $persist = false ) {


    //
    // If this option changed, flag it as modified.
    //

    if ( $this->options[$key] != $value ) {
      $this->modified[] = $key;
    }


    //
    // Save just this key without altering the cached options.
    //

    if ( $persist ) {
      $temp       = get_option( $this->key );
      $temp[$key] = $value;
      update_option( $this->key, $temp );
    }

    return $this->options[$key] = $value;

  }


  //
  // Delete an option. It is removed from the cache and the database.
  //

  function delete( $key ) {

    //
    // Delete from the plugin's available options.
    //

    unset( $this->options[$key] );


    //
    // Delete from the database. We do this manually instead of calling save
    // because it preserves the state.
    //

    $temp = get_option( $this->key );
    unset( $temp[$key] );
    return update_option( $this->key, $temp );

  }


  //
  // Persists the cached options to the database.
  //

  function save() {
    $this->modified = array();
    return update_option( $this->key, $this->options );
  }


  //
  // Condition. Returns if an option has been modified since the last
  // database update.
  //

  function was_modified( $key ) {
    return in_array( $key, $this->modified );
  }


  //
  // If any errors are set, return them.
  //

  function errors() {
    return empty( $this->errors ) ? array() : array( 'errors' => $this->errors );
  }


  //
  // Form validation. When the form is submitted, check if any options are set.
  // Overwrite the current options with the new ones. This does not save the
  // options to the database. You must call the "save" method afterwards.
  //

  function validate_form() {

    foreach ( $this->options as $key => $old_value ) {
      if ( isset( $_POST[$this->key][$key] ) ) {
        $value = strip_tags( $_POST[$this->key][$key] );
        if ( $value != $old_value ) {
          $validate = $this->validate_option( $key, $value );
          if ( is_wp_error( $validate ) ) {
            $this->errors[] = $validate;
          } else {
            $this->set( $key, $value );
          }
        }
      }
    }

  }


  //
  // Associate a callback with an option key. When that option needs to be
  // validated, the associated function will run.
  //

  function add_validator( $key, $callback ) {
    $this->validators[$key] = $callback;
  }


  //
  // Call the validator function for a given option.
  //

  function validate_option( $key, $value ) {
    return call_user_func( ( isset( $this->validators[$key] ) ? $this->validators[$key] : array( $this, 'default_validator' ) ), $value );
  }


  //
  // If an option doesn't have a validator, it will always be true.
  //

  function default_validator( $value ) {
    return true;
  }

}