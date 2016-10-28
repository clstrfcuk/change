<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/TMG/REGISTER.PHP
// -----------------------------------------------------------------------------
// Registers the plugins to be included via the TMG Plugin Activation class.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Register Theme Plugins
//   02. Remove "Install Plugins" Submenu Item
// =============================================================================

// Register Theme Plugins
// =============================================================================

if ( ! function_exists( 'x_register_theme_plugins' ) ) :
  function x_register_theme_plugins() {

    //
    // Bundled plugins.
    //

    $bundled = array(

      'cornerstone' => array(
        'name'               => 'Cornerstone',
        'slug'               => 'cornerstone',
        'source'             => 'cornerstone.zip',
        'required'           => true,
        'version'            => '',
        'force_activation'   => true,
        'force_deactivation' => false,
        'external_url'       => '',
        'x_plugin'           => 'cornerstone/cornerstone.php',
        'x_author'           => 'Themeco',
        'x_description'      => 'This plugin is required to run X. It provides a front end page editor and all the shortcodes used in X.',
        'x_logo'             => '//theme.co/media/x_extensions/200-200-no-title-cornerstone.png',
        'x_manage_upgrade'   => false
      )

    );


    //
    // Remote plugins.
    //

    $extensions = array();
    $addons     = get_site_option( 'x_extension_list', false );

    if ( is_array( $addons ) ) {

      foreach ( $addons as $ext => $data ) {

        $extensions[$ext] = array(
          'name'               => $data['title'],
          'slug'               => $data['slug'],
          'source'             => $data['package'],
          'required'           => false,
          'version'            => $data['new_version'],
          'force_activation'   => false,
          'force_deactivation' => false,
          'external_url'       => '',
          'x_plugin'           => $data['plugin'],
          'x_author'           => $data['author'],
          'x_description'      => $data['description'],
          'x_logo'             => $data['logo_url'],
          'x_manage_upgrade'   => false
        );

      }

    }


    //
    // Merge bundled and remote plugins.
    //

    $plugins = array_merge( $bundled, $extensions );


    //
    // TMG configuration.
    //

    $config = array(
      'domain'           => '__x__',
      'default_path'     => X_TEMPLATE_PATH . '/framework/plugins/',
      'parent_menu_slug' => 'themes.php',
      'parent_url_slug'  => 'themes.php',
      'menu'             => 'install-required-plugins',
      'has_notices'      => true,
      'dismissable'      => true,
      'dismiss_msg'      => '',
      'is_automatic'     => true,
      'message'          => '',
      'strings'          => array(
        'page_title'                      => __( 'Install Required Plugins', '__x__' ),
        'menu_title'                      => __( 'Install Plugins', '__x__' ),
        'installing'                      => __( 'Installing Plugin: %s', '__x__' ),
        'oops'                            => __( 'Something went wrong with the plugin API.', '__x__' ),
        'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ),
        'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ),
        'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ),
        'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ),
        'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ),
        'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ),
        'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ),
        'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ),
        'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
        'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
        'return'                          => __( 'Return to Required Plugins Installer', '__x__' ),
        'plugin_activated'                => __( 'Plugin activated successfully.', '__x__' ),
        'complete'                        => __( 'All plugins installed and activated successfully. %s', '__x__' ),
        'nag_type'                        => 'updated'
      )
    );

    tgmpa( $plugins, $config );

  }

  add_action( 'tgmpa_register', 'x_register_theme_plugins' );
endif;



// Remove "Install Plugins" Submenu Item
// =============================================================================

if ( ! function_exists( 'x_remove_tgm_install_menu_item' ) ) :
  function x_remove_tgm_install_menu_item() {

    remove_submenu_page( 'themes.php', 'install-required-plugins' );

  }
  add_action( 'admin_menu', 'x_remove_tgm_install_menu_item', 9999 );
endif;