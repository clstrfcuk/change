<?php
// =============================================================================
// CONFIG.PHP
// -----------------------------------------------------------------------------
// The framework configuration sets up metaboxes, about items, shortcodes, and
// widgets to be used for the core of the plugin.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Configuration
// =============================================================================

// Configuration
// =============================================================================

return array(

  //
  // List Table.
  //

  'email_forms_list_table' => array(
    'name'      => 'email_forms',
    'post_type' => 'x-email-forms',
    'singular'  => 'email_form',
    'plural'    => 'email_forms',
    'columns'   => array(
      'title'     => __( 'Title', '__x__' ),
      'shortcode' => __( 'Shortcode', '__x__' ),
      'date'      => __( 'Date', '__x__' )
    )
  ),


  //
  // Tabs.
  //

  'admin_tabs' => array(
    'forms' => array(
      'title' => __( 'Email Forms', '__x__' ),
      'view'  => 'admin/tab-email-forms'
    ),
    'general' => array(
      'title' => __( 'General Settings', '__x__' ),
      'view'  => 'admin/tab-settings'
    ),
  ),

  'default_tab' => 'forms',


  //
  // Settings metaboxes.
  //

  'settings_metaboxes' => array(
    'general' => array(
      'title' => __( 'Settings', '__x__' ),
      'view'  => 'admin/metabox-general'
    ),
  ),


  //
  // About items.
  //

  'about_items' => array(
    'general' => array(
      'title'   => __( 'General Settings', '__x__' ),
      'content' => __( 'This is for integrating your provider with various parts of WordPress.', '__x__' ),
    ),
    'providers' => array(
      'title'   => __( 'Providers', '__x__' ),
      'content' => __( 'On the tab navigation, you should see items for any active email providers. You can use multiple providers without needing to change any of the general settings. This allows you to switch if needed without any changes to the frontend of your site. Just edit a form, and reassign the list.', '__x__' ),
    ),
    'support' => array(
      'title'   => __( 'Support', '__x__' ),
      'content' => __( 'For questions, please visit our <a href="//theme.co/x/member/kb/extension-email-forms/" target="_blank">Knowledge Base tutorial</a> for this plugin.', '__x__' ),
    )
  ),


  //
  // Shortcodes (shortname / handling class).
  //

  'shortcodes' => array(
    'x_subscribe' => 'X_Shortcode_X_Subscribe'
  ),


  //
  // Widgets.
  //

  'widgets' => array(
    'X_Widget_X_Subscribe'
  )
);