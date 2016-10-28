<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/TMG/UPDATES.PHP
// -----------------------------------------------------------------------------
// Allows for TGM added plugins to automatically update from the theme's plugin
// folder.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Automatic Updates
// =============================================================================

// Automatic Updates
// =============================================================================

if ( ! class_exists( 'X_TGM_Automatic_Update' ) ) :

  class X_TGM_Automatic_Update {

    function __construct() {
      add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'x_add_updates' ) );
      add_filter( 'plugins_api', array( $this, 'x_override_update_api' ), 10, 3 );
    }


    //
    // Block wordpress.org repo for plugins with x_manage_upgrade set.
    //

    function x_override_update_api( $res, $action, $args ) {

      foreach ( TGM_Plugin_Activation::$instance->plugins as $plugin ) {
        if ( isset( $args->slug ) && $args->slug == $plugin['slug'] && isset( $plugin['x_manage_upgrade'] ) && $plugin['x_manage_upgrade'] == true ) {

          $res           = new stdClass;
          $res->name     = $plugin['name'];
          $res->slug     = $plugin['slug'];
          $res->version  = $plugin['version'];
          $res->package  = $plugin['source'];
          $res->sections = array( 'description' => 'This plugin is bundled with X.' );

          return $res;

        }
      }

      return $res;

    }


    //
    // Add outdated plugins to the update transient.
    //

    function x_add_updates( $transient ) {

      $installed_plugins = get_plugins();

      foreach ( TGM_Plugin_Activation::$instance->plugins as $plugin ) {
        if ( isset( $installed_plugins[$plugin['x_plugin']]['Version'] ) && isset( $plugin['version'] ) && isset( $plugin['x_manage_upgrade'] ) && $plugin['x_manage_upgrade'] == true && version_compare( $installed_plugins[$plugin['x_plugin']]['Version'], $plugin['version'], '<' ) ) {

          $response                 = new stdClass;
          $response->url            = '';
          $response->slug           = $plugin['slug'];
          $response->upgrade_notice = '';
          $response->new_version    = $plugin['version'];
          $response->package        = X_TEMPLATE_URL . '/framework/plugins/' . $plugin['source'];

          $transient->response[$plugin['x_plugin']] = $response;

        }
      }

      return $transient;

    }
  }

  if ( class_exists( 'TGM_Plugin_Activation' ) ) {
    new X_TGM_Automatic_Update();
  }

endif;