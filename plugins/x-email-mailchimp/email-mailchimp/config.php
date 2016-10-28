<?php

// =============================================================================
// CONFIG.PHP
// -----------------------------------------------------------------------------
// The provider configuration sets up general information, metaboxes, default
// options, and about items to be used specifically for the provider.
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
  // General info.
  //

  'name'  => 'mailchimp',
  'title' => 'MailChimp',


  //
  // Default options.
  //

  'default_options' => array(
    'mc_api_key'            => '',
    'mc_list_cache'         => array(),
    'mc_skip_double_opt_in' => 'no',
    'mc_send_welcome'       => 'no'
  ),


  //
  // Settings page metaboxes.
  //

  'settings_metaboxes' => array(
    'mc_general' => array(
      'title' => __( 'Settings', '__x__' ),
      'view'  => 'admin/metabox-settings'
    ),
    'mc_lists' => array(
      'title' => __( 'Lists', '__x__' ),
      'view'  => 'admin/metabox-lists'
    )
  ),


  //
  // About items.
  //

  'about_items' => array(
    'mc_api_key' => array(
      'title'   => __( 'API Key', '__x__' ),
      'content' => __( 'MailChimp requires an API key. You can generate one from your <a href="https://admin.mailchimp.com/account/api/" target="_blank">MailChimp account</a>. ', '__x__' ),
    ),
    'mc_lists' => array(
      'title'   => __( 'Lists', '__x__' ),
      'content' => __( 'You will need to create a list with Mailchimp. You can do that from your <a href="https://admin.mailchimp.com/lists/" target="_blank">MailChimp Lists Page</a>. Any preexisting lists should be shown, otherwise you can use the <b>Refresh</b> button to check for recently created ones.', '__x__' ),
    ),
    'mc_support' => array(
      'title'   => __( 'Support', '__x__' ),
      'content' => __( 'For questions, please visit our <a href="//theme.co/x/member/kb/extension-email-forms/" target="_blank">Knowledge Base tutorial</a> for this plugin.', '__x__' ),
    )
  ),

);