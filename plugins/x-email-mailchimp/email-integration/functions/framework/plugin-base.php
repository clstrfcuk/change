<?php

// =============================================================================
// FUNCTIONS/FRAMEWORK/BASE-PLUGIN.PHP
// -----------------------------------------------------------------------------
// This serves as a framework for the plugin. It provides underlying structure,
// and helper functions. The main plugin class will inherit from it. The idea
// is that this file should rarely change, while functionality can be built
// into the inheriting class more rapidly.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

class X_Email_Integration_Base {

  //
  // Declare class variables.
  //

  protected $file, $url, $path, $slug, $config, $widgets, $shortcodes;

  protected $default_options = array();
  protected $transport       = array();
  protected $validators      = array();

  public $view, $options;


  //
  // Plugin Setup.
  //

  function __construct( $file, $slug, $folder = '' ) {

    //
    // Define plugin variables.
    //

    $this->slug = $slug;
    $this->path = untrailingslashit( plugin_dir_path( $file ) . $folder );
    $this->url  = plugins_url( $folder, $file );


    //
    // Load dependancies.
    //

    require( $this->path . '/functions/includes.php' );


    //
    // Create view handler with this plugin folder.
    //

    $this->view = new X_Email_Integration_View_Handler( $this, $this->path );


    //
    // Create options handler with this plugin's slug as a key.
    //

    $this->options = new X_Email_Integration_Options_Handler( $this, $this->slug );


    //
    // Define transport items. These will be exposed to the view layer, but
    // not saved as options.
    //

    $this->set_transport( 'plugin_slug', $this->slug );
    $this->set_transport( 'plugin_title', str_replace( '_', '-', sanitize_title_with_dashes( $this->slug ) ) );
    $this->set_transport( 'plugin_url',  $this->url );
    $this->set_transport( 'errors', array() );


    //
    // Register general hooks.
    //

    add_action( 'init', array( $this, 'base_init' ) );
    add_action( 'init', array( $this, 'init' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'site_enqueue_scripts' ) );
    add_action( 'x_head_css', array( $this, 'site_enqueue_styles' ) );


    //
    // Load plugin configuration.
    //

    $this->load_config();
    $this->setup();


    //
    // Register shortcodes.
    //

    $this->register_shortcodes();


    //
    // Register widget.
    //

    add_action( 'widgets_init', array( $this, 'register_widgets' ) );

  }


  //
  // Loads the plugin config file.
  //

  function load_config() {

    $this->config = array();
    $file         = $this->path . '/config.php';
    $config       = ( file_exists( $file ) ) ? include( $file ) : false;
    $this->config = ( is_array( $config ) ) ? $config : array();

  }


  //
  // Base plugin initialization.
  //

  function base_init() {

    //
    // Populate options with settings from the database. If we haven't saved
    // yet, the plugin default_options will be provided.
    //

    $file            = $this->path . '/functions/options.php';
    $plugin_defaults = ( file_exists( $file ) ) ? include( $file ) : false;
    $extra_defaults  = $this->extra_default_options();
    $options         = ( is_array( $plugin_defaults ) ) ? array_merge( $plugin_defaults, $extra_defaults ) : $extra_defaults;

    $this->options->load( $options );

  }


  //
  // Transport data. Allow data injection into every view, but doesn't
  // store it in the option.
  //

  function set_transport( $key, $value ) {
    $this->transport[$key] = $value;
  }

  function get_transport( $key ) {
    return $this->transport[$key];
  }


  //
  // Inject plugin options and transport data into every view.
  //

  function get_view_data() {
    return array_merge( $this->options->all(), $this->transport );
  }


  //
  // Load admin scripts/styles. Passes hook variable into include.
  //

  function admin_enqueue_scripts( $hook_suffix ) {

    $data = array_merge( $this->get_view_data(), array( 'hook_suffix' => $hook_suffix ) );
    $this->view->require_if_exists( $this->path . '/functions/enqueue/admin/scripts.php', $data );

  }

  function admin_enqueue_styles( $hook_suffix ) {

    $data = array_merge( $this->get_view_data(), array('hook_suffix' => $hook_suffix) );
    $this->view->require_if_exists( $this->path . '/functions/enqueue/admin/styles.php', $data );

  }


  //
  // Load site scripts/styles.
  //

  function site_enqueue_scripts() {
    $this->view->require_if_exists( $this->path . '/functions/enqueue/site/scripts.php', $this->get_view_data() );
  }

  function site_enqueue_styles() {
    $this->view->require_if_exists( $this->path . '/functions/enqueue/site/styles.php', $this->get_view_data() );
  }


  //
  // Load shortcodes file. Each declared shortcode will be loaded. Array keys
  // should be the desired shortcode name, and the values should be classes
  // containing a handler.
  //

  function register_shortcodes() {

    $shortcode_classes = ( isset( $this->config['shortcodes'] ) && is_array( $this->config['shortcodes'] ) ) ? $this->config['shortcodes'] : array();

    foreach ( $shortcode_classes as $name => $class ) {
      if ( class_exists( $class ) ) {
        $this->shortcodes[$class] = new $class( $name, $this );
      }
    }

  }


  //
  // Load widgets file. If Widgets are declared, they will be registered with WordPress.
  //

  function register_widgets() {

    $widget_classes = ( isset( $this->config['widgets'] ) && is_array( $this->config['widgets'] ) ) ? $this->config['widgets'] : array();

    GLOBAL $wp_widget_factory;

    foreach ( $widget_classes as $widget_class ) {

      //
      // 1. Register widget(s) with WordPress.
      // 2. Inject plugin when possible.
      //

      register_widget( $widget_class );                     // 1
      $widget = $wp_widget_factory->widgets[$widget_class]; // 2

      if ( method_exists( $widget, 'set_plugin' ) ) {
        $widget->set_plugin( $this );
        $this->widgets[$widget_class] = $widget;
      }

    }

  }


  //
  // Utility functions.
  //

  function get_path() {
    return $this->path;
  }

  function get_url() {
    return $this->url;
  }

  function get_slug() {
    return $this->slug;
  }

  function warn_x_required() {
    return _doing_it_wrong( $this->slug, sprintf(__( '%s requires X Theme to be active.','__x__'), $this->slug ), '1.0' );
  }


  //
  // Stub Functions. These should be overridden in the child class.
  //

  function init() { }
  function admin_init() { }
  function setup() { }
  function admin_menu() { }
  function extra_default_options() { return array(); }

}