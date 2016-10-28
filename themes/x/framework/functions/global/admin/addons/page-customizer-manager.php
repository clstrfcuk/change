<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/PAGE-CUSTOMIZER-MANAGER.PHP
// -----------------------------------------------------------------------------
// Addons Custoimzer manager page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Turn on Output Buffering
//   02. Page Output
//   03. Import
//   04. Export
//   05. Reset
// =============================================================================

// Turn on Output Buffering
// =============================================================================

ob_start();



// Page Output
// =============================================================================

function x_addons_page_customizer_manager() { ?>

  <div class="wrap x-addons-customizer-manager">

    <header class="x-addons-header">
      <h2>Customizer Manager</h2>
      <p>Easily manage your Customizer settings with X's custom import, export, and reset functionality.</p>
    </header>

    <div class="x-addons-postboxes">
      <?php x_addons_customizer_manager_import_output(); ?>
      <?php x_addons_customizer_manager_export_output(); ?>
      <?php x_addons_customizer_manager_reset_output(); ?>
    </div>

  </div>

<?php }



// Import
// =============================================================================

function x_addons_customizer_import_functionality() {

  if ( isset( $_FILES['import'] ) && check_admin_referer( 'x-addons-customizer-manager-import' ) ) {
    if ( $_FILES['import']['error'] > 0 ) {
      wp_die( 'An import error occured. Please try again.' );
    } else {
      $file_name  = $_FILES['import']['name'];
      $file_array = explode( '.', $file_name );
      $file_ext   = strtolower( end( $file_array ) );
      $file_size  = $_FILES['import']['size'];
      if ( ( $file_ext == 'json' ) && ( $file_size < 500000 ) ) {
        $encoded_options = file_get_contents( $_FILES['import']['tmp_name'] );
        $options         = json_decode( $encoded_options, true );
        foreach ( $options as $key => $value ) {
          update_option( $key, $value );
        }
        x_bust_google_fonts_cache();
        echo '<div class="updated"><p><strong>Huzzah!</strong> All Customizer settings were successfully restored!</p></div>';
      } else {
        echo '<div class="error"><p><strong>Uh oh.</strong> Invalid file type provided or file size too big. Please try again.</p></div>';
      }
    }
  }

}


function x_addons_customizer_manager_import_output() { ?>

  <div class="x-addons-postbox customizer-manager import">
    <?php x_addons_customizer_import_functionality(); ?>
    <h3 class="title"><span class="dashicons dashicons-upload"></span> <span>Import</span></h3>
    <div class="inside">
      <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field( 'x-addons-customizer-manager-import' ); ?>
        <p>Howdy! Upload your X Customizer Settings (XCS) file and we&apos;ll import the options into this site.</p>
        <p>Choose a XCS (.json) file to upload, then click "Upload File and Import."</p>
        <p>Choose a file from your computer: <input type="file" id="x-addons-customizer-manager-import" name="import"></p>
        <p class="submit">
          <input type="submit" name="submit" id="x-addons-customizer-manager-import-submit" class="button button-primary" value="Upload File and Import" disabled>
        </p>
      </form>
    </div>
  </div>

<?php }



// Export
// =============================================================================

function x_addons_customizer_export_functionality() {

  GLOBAL $customizer_settings_data;

  $blogname  = strtolower( str_replace( ' ', '-', get_option( 'blogname' ) ) );
  $file_name = $blogname . '-xcs';

  foreach ( $customizer_settings_data as $option => $default ) {
    $value         = maybe_unserialize( get_option( $option ) );
    $data[$option] = $value;
  }

  $json_data = json_encode( $data );


  //
  // We wrap the content of our JSON data with ob_clean() and exit() to ensure
  // that $json_data doesn't contain any extra data. This works in conjunction
  // with ob_start() at the top of the file, which prevents header errors from
  // occuring (i.e. extra whitespace somewhere in the code).
  //

  ob_clean();

  echo $json_data;

  header( 'Content-Type: text/json; charset=' . get_option( 'blog_charset' ) );
  header( 'Content-Disposition: attachment; filename="' . $file_name . '.json"' );

  exit();

}


function x_addons_customizer_manager_export_output() {

  if ( ! isset( $_POST['export'] ) ) {

  ?>

    <div class="x-addons-postbox customizer-manager export">
      <h3 class="title"><span class="dashicons dashicons-download"></span> <span>Export</span></h3>
      <div class="inside">
        <form method="post">
          <?php wp_nonce_field( 'x-addons-customizer-manager-export' ); ?>
          <p>When you click the button below WordPress will create a JSON file for you to save to your computer.</p>
          <p>This format, which we call X Customizer Settings or XCS, will contain your Customizer settings for X.</p>
          <p>Once you&apos;ve saved the download file, you can use the Customizer Import section to import the previusly exported settings.</p>
          <p class="submit">
            <input type="submit" name="export" class="button button-primary" value="Download XCS File">
          </p>
        </form>
      </div>
    </div>

  <?php

  } elseif ( check_admin_referer( 'x-addons-customizer-manager-export' ) ) {
    x_addons_customizer_export_functionality();
  }

}



// Reset
// =============================================================================

function x_addons_customizer_reset_functionality() {

  if ( isset( $_POST['reset'] ) && check_admin_referer( 'x-addons-customizer-manager-reset' ) ) {

    GLOBAL $customizer_settings_data;

    foreach ( $customizer_settings_data as $option => $default ) {
      delete_option( $option );
    }

    x_bust_google_fonts_cache();

    echo '<div class="updated"><p>All Customizer settings were successfully reset.</p></div>';

  }

}


function x_addons_customizer_manager_reset_output() { ?>

  <div class="x-addons-postbox customizer-manager reset">
    <?php x_addons_customizer_reset_functionality(); ?>
    <h3 class="title"><span class="dashicons dashicons-update"></span> <span>Reset</span></h3>
    <div class="inside">
      <form method="post">
        <?php wp_nonce_field( 'x-addons-customizer-manager-reset' ); ?>
        <p>When you click the button below WordPress will reset your Customizer settings as if it were a brand new installation.</p>
        <p>Be extremely careful using this option as there is no way to revert this option once it has been made unless you previously exported your settings to use as a backup.</p>
        <p class="submit">
          <input type="submit" id="x-addons-customizer-manager-reset-submit" class="button button-primary" value="Reset Customizer Settings">
          <input type="hidden" name="reset" value="reset">
        </p>
      </form>
    </div>
  </div>

<?php }