<?php

// =============================================================================
// FUNCTIONS/PLUGIN.PHP
// -----------------------------------------------------------------------------
// Inherits from base plugin. This is the core plugin class where feature
// specific code is handled.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_Integration extends X_Email_Integration_Base {

  //
  // Properties.
  //

  protected $email_providers = array();


  //
  // Initialize plugin.
  //

  function init() {

    //
    // Setup post types.
    //

    include( $this->path . '/functions/post-types.php' );


    //
    // Possibly subscribe users on registration.
    //

    if ( $this->options->get( 'opt_in_new_users') == 'yes' ) {
      add_action( 'user_register', array( $this, 'user_register' ) );
    }
  }


  //
  // Admin setup.
  //

  function admin_init() {

    //
    // Create a master list from the active email providers.
    // 1. Create reference to forms and their IDs.
    //

    $this->build_master_list();
    $this->set_transport( 'plugin_admin_url', 'admin.php?page=x-extensions-email-forms' );
    $this->set_transport( 'email_forms', $this->get_all_forms() ); // 1

    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
    add_action( 'x_email_forms_list_table_column_shortcode' , array( $this, 'shortcode_post_type_column' ), 10, 2 );

  }


  //
  // Register meta boxes.
  //

  function register_meta_boxes() {
    include( $this->path . '/functions/admin/cpt-metaboxes.php' );
  }


  //
  // Load options page.
  //

  function admin_controller() {
    include( $this->path . '/functions/admin/controller.php' );
  }

  //
  // Render post type column for shortcode.
  //

  function shortcode_post_type_column( $result, $item ) {
    return "<input type=\"text\" readonly=\"readonly\" value='[x_subscribe form=\"{$item->ID}\"]'>";
  }


  //
  // Allow provider registration.
  //

  function register_provider( $provider_name, $file ) {

    $class = 'X_Email_' . $provider_name;

    if ( class_exists( $class ) ) {
      $this->email_providers[$provider_name] = new $class( $this, $file );
    }

  }


  //
  // Return the provider associated with the given name.
  //

  function resolve_provider( $provider_name ) {

    foreach ($this->email_providers as $provider) {
      if ( $provider->get_name() == $provider_name ) {
        return $provider;
      }
    }

    return false;

  }


  //
  // Update default options to include those from the email providers.
  //

  function extra_default_options() {

    $extra_options = array();

    foreach ( $this->email_providers as $provider ) {
      $extra_options = array_merge( $extra_options, $provider->get_default_options() );
    }

    return $extra_options;
  }


  //
  // Abstract subscribe method. This resolves a setting identifier
  // (e.g. mailchimp_123) to an actual list, then requests a subscription.
  //

  function subscribe( $identifier, $user_data ) {

    $parts = explode('_', $identifier);

    $provider = $this->resolve_provider( $parts[0] );
    if ( $provider ) {
      return $provider->subscribe( $parts[1], $user_data );
    } else {
      return new WP_Error( 'x-email-forms', __( 'Error: Email provider not active.', '__x__' ) );
    }
  }


  //
  // Helper methods.
  //

  function build_master_list() {

    $master_list = array();

    foreach ( $this->email_providers as $provider ) {
      $master_list = array_merge( $master_list, $provider->get_normalized_list() );
    }

    $this->set_transport( 'master_list', $master_list );

  }

  function format_master_list_for_mb() {

    $formatted = array();

    foreach ( $this->get_transport( 'master_list' ) as $item ) {
      $formatted[] = "{$item['provider']}_{$item['id']}**{$item['name']} ({$item['provider_title']})";
    }

    return $formatted;

  }

  function get_all_forms() {

    $forms = array();
    $posts = get_posts( array( 'post_type' => 'x-email-forms', 'posts_per_page' => 999 ) );

    foreach ( $posts as $form ) {
      $forms[(string)$form->ID] = $form->post_title;
    }

    return $forms;
  }


  //
  // Subscribe users when they register.
  //

  function user_register( $user_id ) {

    $user_data                  = array();
    $user                       = get_userdata( $user_id );
    $user_data['email_address'] = $user->user_email;

    if ( isset( $user->user_firstname ) ) {
      $user_data['first_name'] = $user->user_firstname;
    }

    if ( isset( $user->user_lastname ) ) {
      $user_data['last_name'] = $user->user_lastname;
    }

    if ( isset( $user->display_name ) ) {
      $user_data['full_name'] = $user->display_name;
    }

    $this->subscribe( $this->options->get( 'opt_in_new_users_list' ), $user_data );

  }

}