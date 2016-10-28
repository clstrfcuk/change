<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/SETUP.PHP
// -----------------------------------------------------------------------------
// Initializes and sets up the X Addons section.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Path
//   02. Get API Key Option Name
//   03. Links
//   04. Require Files
//   05. Setup Menu
//   06. Activation Redirect
//   07. Add Extensions Link to "Add Plugins" Page
//   08. Setup Demo Importer
// =============================================================================

// Set Path
// =============================================================================

$addn_path = X_TEMPLATE_PATH . '/framework/functions/global/admin/addons';



// Get API Key Option Name
// =============================================================================

function x_addons_get_api_key_option_name() {
  return 'x_product_validation_key';
}



// Links
// =============================================================================

function x_addons_get_link_home() {
  return admin_url( 'admin.php?page=x-addons-home' );
}

function x_addons_get_link_customizer_manager() {
  return admin_url( 'admin.php?page=x-addons-customizer-manager' );
}

function x_addons_get_link_demo_content() {
  return admin_url( 'admin.php?page=x-addons-demo-content' );
}

function x_addons_get_link_product_validation() {
  return admin_url( 'admin.php?page=x-addons-product-validation' );
}

function x_addons_get_link_extensions() {
  return admin_url( 'admin.php?page=x-addons-extensions' );
}



// Require Files
// =============================================================================

require_once( $addn_path . '/class-themeco-update-api.php' );
require_once( $addn_path . '/class-x-update-api.php' );
require_once( $addn_path . '/class-theme-updater.php' );
require_once( $addn_path . '/class-plugin-updater.php' );
require_once( $addn_path . '/page-home.php' );
require_once( $addn_path . '/page-customizer-manager.php' );
require_once( $addn_path . '/page-demo-content.php' );
require_once( $addn_path . '/page-product-validation.php' );
require_once( $addn_path . '/page-extensions.php' );
require_once( $addn_path . '/demo/legacy/ajax-handler.php' );
require_once( $addn_path . '/demo/class-x-demo-import-session.php' );
require_once( $addn_path . '/demo/class-x-demo-import-registry.php' );
require_once( $addn_path . '/demo/class-x-demo-import-processor.php' );



// Setup Menu
// =============================================================================

function x_addons_add_menu() {
  add_menu_page( 'X &ndash; Addons: Home', 'Addons', 'manage_options', 'x-addons-home', 'x_addons_page_home', NULL, 3 );
  add_submenu_page( 'x-addons-home', 'X &ndash; Addons: Home', 'Home', 'manage_options', 'x-addons-home', 'x_addons_page_home' );
  add_submenu_page( 'x-addons-home', 'X &ndash; Addons: Customizer Manager', 'Customizer Manager', 'manage_options', 'x-addons-customizer-manager', 'x_addons_page_customizer_manager' );
  add_submenu_page( 'x-addons-home', 'X &ndash; Addons: Demo Content', 'Demo Content', 'manage_options', 'x-addons-demo-content', 'x_addons_page_demo_content' );
  add_submenu_page( 'x-addons-home', 'X &ndash; Addons: Product Validation', 'Product Validation', 'manage_options', 'x-addons-product-validation', 'x_addons_page_product_validation' );
  add_submenu_page( 'x-addons-home', 'X &ndash; Addons: Extensions', 'Extensions', 'manage_options', 'x-addons-extensions', 'x_addons_page_extensions' );
}

add_action( 'admin_menu', 'x_addons_add_menu' );



// Activation Redirect
// =============================================================================

function x_addons_theme_activation_redirect() {

  if ( isset( $_GET['activated'] ) ) {
    wp_redirect( x_addons_get_link_home() );
  }

}

add_action( 'admin_init', 'x_addons_theme_activation_redirect' );



// Add Extensions Link to "Add Plugins" Page
// =============================================================================

function x_addons_add_plugins_extensions_link( $views ) {

  $views['x-addons'] = sprintf( __( '<a href="%s">Extensions</a>', '__x__' ), x_addons_get_link_extensions() );

  return $views;

}

add_filter( 'views_plugin-install', 'x_addons_add_plugins_extensions_link' );



// Setup Demo Importer
// =============================================================================

X_Demo_Import_Session::instance();