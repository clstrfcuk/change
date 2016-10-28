<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/PAGE-EXTENSIONS.PHP
// -----------------------------------------------------------------------------
// Addons extensions page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Page Output
// =============================================================================

// Page Output
// =============================================================================

function x_addons_page_extensions() { ?>

  <?php

  //
  // Allow cache to be cleared manually.
  //

  if ( isset( $_GET['force-check'] ) && $_GET['force-check'] == 1 ) {
    delete_site_option( 'x_extension_list' );
  }


  //
  // Retrieve addons from cache, or check immediately if they were just cleared.
  //

  $addons_cache = X_Update_API::get_cached_addons();


  //
  // Show connection errors on screen.
  //

  if ( isset( $_GET['x-verbose'] ) && $_GET['x-verbose'] == 1  ) {
  	$message = ( isset( $addons_cache['verbose'] ) ) ? $addons_cache['verbose'] : 'No connection errors';
    x_dump( $message, 350, 'var_dump' );
  }

  ?>

  <div class="wrap x-addons-extensions">

    <header class="x-addons-header">
      <h2>Extensions</h2>
      <p>Custom and third party plugins you can use for free (over $1,000 in value) with updates!</p>

      <?php if ( isset( $addons_cache['error'] ) && $addons_cache['error'] ) : ?>
        <div class="error"><p><?php echo $addons_cache['message']; ?></p></div>
      <?php endif; ?>

    </header>

    <ul class="x-addons-extensions-list cf" id="x-addons-extensions-list">

      <?php

      $plugins = TGM_Plugin_Activation::$instance->plugins;

      foreach ( $plugins as $key => $plugin ) {
        if ( $plugin['slug'] == 'cornerstone' ) {
          $cornerstone = $plugin;
          unset( $plugins[$key] );
        }
      }

      array_unshift( $plugins, $cornerstone );

      foreach ( $plugins as $plugin ) :

      ?>

        <?php

        if ( x_plugin_exists( $plugin['x_plugin'] ) ) :
          if ( is_plugin_active( $plugin['x_plugin'] ) ) {
            $status         = 'active';
            $status_message = 'Active';
          } else {
            $status         = 'inactive';
            $status_message = 'Inactive';
          }
          $button = '<a class="x-addon-button button" href="' . admin_url( 'plugins.php' ) . '">Manage Plugin</a>';
        else :
          if ( $plugin['source'] == NULL ) {
            $url   = x_addons_get_link_product_validation();
            $text  = 'Validate Purchase to Install';
            $class = 'x-addon-button button';
          } else {
            $url   = wp_nonce_url( add_query_arg( array( 'page' => TGM_Plugin_Activation::$instance->menu, 'plugin' => $plugin['slug'], 'plugin_name' => $plugin['name'], 'plugin_source' => $plugin['source'], 'tgmpa-install' => 'install-plugin', ), admin_url( TGM_Plugin_Activation::$instance->parent_url_slug ) ), 'tgmpa-install' );
            $text  = 'Install Plugin';
            $class = 'x-addon-button button button-primary';
          }
          $status         = 'not-installed';
          $status_message = 'Not Installed';
          $button         = '<a class="' . $class . '" href="' . $url . '">' . $text . '</a>';
        endif;

        ?>

        <li class="x-addons-extension <?php echo $status; ?>" id="<?php echo $plugin['slug']; ?>">
          <div class="top cf">
            <img src="<?php echo $plugin['x_logo']; ?>" class="img">
            <div class="info">
              <h4 class="title"><?php echo $plugin['name']; ?></h4>
              <span class="status <?php echo $status; ?>"><?php echo $status_message; ?></span>
              <p class="desc"><?php echo $plugin['x_description']; ?></p>
              <p class="author"><cite>By <?php echo $plugin['x_author']; ?></cite></p>
            </div>
          </div>
          <div class="bottom cf"><?php echo $button; ?></div>
        </li>

      <?php endforeach; ?>

    </ul>

  </div>

<?php }