<?php
// =============================================================================
// FUNCTIONS/ADMIN/CONTROLLER.PHP
// -----------------------------------------------------------------------------
// This file handles all the admin page logic, and loads the view. Included
// from the context of "X_Email_Integration"
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Run Conditionally
//   02. Generate Tabs
//   03. Set Active Provider
//   04. Save Options
//   05. Prepare Additional Data For View
//   06. Tab Setup: Email Forms
//   07. Tab Setup: General Settings
//   08. Tab Setup: Specific Providers
//   09. Load View
// =============================================================================

// Run Conditionally
// =============================================================================

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( __( 'You do not have sufficient permissions to access this page.', '__x__') );
}



// Generate Tabs
// =============================================================================

$tabs = $this->config['admin_tabs'];

foreach ( $this->email_providers as $provider ) {

  $name        = $provider->get_name();
  $tabs[$name] = array(
    'title' => $provider->get_title(),
    'view'  => 'admin/tab-settings'
  );

}

foreach ( $tabs as $name => $tab ) {
  $tabs[$name]['url'] = add_query_arg( array( 'tab' => $name ), $this->get_transport( 'plugin_admin_url' ) );
}

$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->config['default_tab'];

$this->set_transport( 'tabs', $tabs );
$this->set_transport( 'current_tab', $current_tab );
$this->set_transport( 'selected_tab_view', $tabs[$current_tab]['view'] );



// Set Active Provider
// =============================================================================

//
// False if not a provider tab.
//

$provider = $this->resolve_provider( $current_tab );



// Save Options
// =============================================================================

if ( isset( $_POST[ $this->slug . '_form_submitted' ] ) && strtolower( $_POST[ $this->slug . '_form_submitted' ] ) == 'submitted' ) {

  $this->options->validate_form();

  if ( $provider ) {
    $provider->before_save();
  }

  $this->options->save();

}



// Prepare Additional Data For View
// =============================================================================

$about_items = array();
$meta_boxes  = array();
$this->build_master_list();



// Tab Setup: Email Forms
// =============================================================================

if ( $current_tab == 'forms' ) {

  $email_forms_table = new X_Email_Forms_List_Table( $this->config['email_forms_list_table'] );
  $email_forms_table->prepare_items();

  $this->set_transport( 'email_forms_table', $email_forms_table );

}



// Tab Setup: General Settings
// =============================================================================

if ( $current_tab == 'general' ) {

  foreach ( $this->config['settings_metaboxes'] as $key => $item ) {
    $meta_boxes[$key]['title']   = $item['title'];
    $meta_boxes[$key]['content'] = $this->view->make( $item['view'] );
  }

  $about_items = array_merge( $about_items, $this->config['about_items'] );

}



// Tab Setup: Specific Providers
// =============================================================================

if ( $provider ) {

  $provider->settings_page();

  $meta_boxes  = array_merge( $meta_boxes, $provider->get_metaboxes() );
  $about_items = array_merge( $about_items, $provider->get_about_items() );

}



// Load View
// =============================================================================

$this->set_transport( 'about_items', $about_items );
$this->set_transport( 'meta_boxes', $meta_boxes );
$this->view->show( 'admin/options-page' );