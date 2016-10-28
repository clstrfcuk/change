<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Theme scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
//   02. Enqueue Admin Scripts
//   03. Enqueue Customizer Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

if ( ! function_exists( 'x_enqueue_site_scripts' ) ) :
  function x_enqueue_site_scripts() {

    wp_register_script( 'x-site-head', X_TEMPLATE_URL . '/framework/js/dist/site/x-head.min.js', array( 'jquery' ), X_VERSION, false );
    wp_register_script( 'x-site-body', X_TEMPLATE_URL . '/framework/js/dist/site/x-body.min.js', array( 'jquery' ), X_VERSION, true );
    wp_register_script( 'x-site-icon', X_TEMPLATE_URL . '/framework/js/dist/site/x-icon.min.js', array( 'jquery' ), X_VERSION, true );

    wp_enqueue_script( 'x-site-head' );
    wp_enqueue_script( 'x-site-body' );

    if ( x_get_stack() == 'icon' ) {
      wp_enqueue_script( 'x-site-icon' );
    }

    if ( is_singular() ) {
      wp_enqueue_script( 'comment-reply' );
    }

    if ( X_BUDDYPRESS_IS_ACTIVE ) {
      wp_dequeue_script( 'bp-legacy-js' );
      wp_dequeue_script( 'bp-parent-js' );
      wp_enqueue_script( 'x-site-buddypress', X_TEMPLATE_URL . '/framework/js/dist/site/x-buddypress.js', bp_core_get_js_dependencies(), X_VERSION, false );
      wp_localize_script( 'x-site-buddypress', 'BP_DTheme', x_buddypress_core_get_js_strings() );
    }

  }
  add_action( 'wp_enqueue_scripts', 'x_enqueue_site_scripts' );
endif;



// Enqueue Admin Scripts
// =============================================================================

if ( ! function_exists( 'x_enqueue_post_meta_scripts' ) ) :
  function x_enqueue_post_meta_scripts( $hook ) {

    GLOBAL $post;
    GLOBAL $wp_customize;

    if ( isset( $wp_customize ) ) {
      return;
    }

    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'x-confirm-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-confirm.min.js', array( 'jquery' ), X_VERSION, true );


    if ( strpos( $hook, 'x-addons-customizer-manager' ) != false ) {
      wp_enqueue_script( 'x-customizer-admin-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-customizer-admin.min.js', array( 'jquery' ), X_VERSION, true );
    }

    if ( strpos( $hook, 'x-addons-demo-content' ) != false ) {

      wp_register_script( 'x-demo-content-admin-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-demo-content-admin.min.js', array( 'jquery' ), X_VERSION, true );

      wp_localize_script( 'x-demo-content-admin-js', 'xDemoContent', array(
        'debug'          => ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ),
        'start'          => __( 'Let&apos;s get started!', '__x__' ),
        'complete'       => __( 'Have fun!', '__x__' ),
        'simulated'      => __( 'Working on it...', '__x__' ),
        'confirm'        => __( 'Installing demo content will not alter any of your pages or posts, but it will overwrite your Customizer settings. This is not reversible unless you have previously made a backup of your settings. Are you sure you want to proceed?', '__x__' ),
        'timeout1'       => __( 'Working on it...', '__x__' ),
        'timeout2'       => __( 'Hang in there, trying to reconnect...', '__x__' ),
        'timeout3'       => __( 'Experiencing technical difficulties...', '__x__' ),
        'failure'        => __( 'We&apos;re sorry, the demo failed to finish importing.', '__x__' ),
        'buttonStandard' => __( 'Setup Standard Demo: %s', '__x__' ),
        'buttonExpanded' => __( 'Setup Expanded Demo: %s', '__x__' ),
      ) );

      wp_enqueue_script( 'x-demo-content-admin-js' );

    }

    if ( $hook == 'widgets.php' ) {
      wp_enqueue_script( 'x-widgets-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-widgets.min.js', array( 'jquery' ), X_VERSION, true );
    }

    if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' ) {
      wp_enqueue_script( 'x-meta-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-meta.min.js', array( 'jquery', 'media-upload', 'thickbox' ), X_VERSION, true );
    }

    if ( $hook == 'post.php' || $hook == 'post-new.php' || strpos( $hook, 'x-extensions' ) != false ) {
      wp_enqueue_script( 'jquery-ui-datepicker' );
    }

  }
  add_action( 'admin_enqueue_scripts', 'x_enqueue_post_meta_scripts' );
endif;



// Enqueue Customizer Scripts
// =============================================================================

//
// Controls.
//

if ( ! function_exists( 'x_enqueue_customizer_controls_scripts' ) ) :
  function x_enqueue_customizer_controls_scripts() {

    wp_register_script( 'x-customizer-controls-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-customizer-controls.min.js', array( 'jquery' ), X_VERSION, true );

    wp_localize_script( 'x-customizer-controls-js', 'x_customizer_controls_data', array(
      'x_fonts_data' => x_fonts_data()
    ) );

    wp_enqueue_script( 'x-customizer-controls-js' );

  }
  add_action( 'customize_controls_print_footer_scripts', 'x_enqueue_customizer_controls_scripts' );
endif;


//
// Preview.
//

if ( ! function_exists( 'x_enqueue_customizer_preview_scripts' ) ) :
  function x_enqueue_customizer_preview_scripts() {

    wp_register_script( 'x-customizer-preview-js', X_TEMPLATE_URL . '/framework/js/dist/admin/x-customizer-preview.min.js', array( 'jquery', 'customize-preview', 'heartbeat' ), X_VERSION, true );

    wp_localize_script( 'x-customizer-preview-js', 'x_customizer_preview_data', array(
      'x_woocommerce_is_active' => X_WOOCOMMERCE_IS_ACTIVE
    ) );

    wp_enqueue_script( 'x-customizer-preview-js' );

  }
  add_action( 'customize_preview_init', 'x_enqueue_customizer_preview_scripts' );
endif;