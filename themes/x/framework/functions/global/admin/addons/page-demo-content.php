<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/PAGE-DEMO-CONTENT.PHP
// -----------------------------------------------------------------------------
// Addons demo content page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Page Output
//   02. Setup
// =============================================================================

// Page Output
// =============================================================================

function x_addons_page_demo_content() {

  $data = x_addons_page_demo_content_setup();

  extract( $data );

  $standard_first = current( $standard_demos );
  $expanded_first = current( $expanded_demos );

  ?>

  <div class="wrap x-addons-demo-content">

    <?php if ( isset( $_GET['clean-cache'] ) ) : ?>
      <div class="notice updated"><p><?php _e( 'Demo content cache has been cleared. All imported content will be considered new.', '__x__' ); ?></p></div>
    <?php endif; ?>

    <header class="x-addons-header">
      <h2><?php _e( 'Demo Content', '__x__' ); ?></h2>
      <p><?php _e( 'Quick start the design process with our Standard and Expanded demos. <a href="https://community.theme.co/kb/demo-content/" target="_blank">Learn More</a>.', '__x__' ); ?></p>
    </header>

    <div class="message"></div>

    <div class="x-addons-postboxes">

      <?php if ( isset( $error ) && is_string( $error ) ) : ?>

        <div class="x-addons-postbox demo-content">
          <div class="error"><p><?php echo $error ?></p></div>
        </div>

      <?php else : ?>

        <div class="x-addons-postbox demo-content">
          <div class="inside">
            <form id="x-demo-content-form" method="post">

              <input type="hidden" name="demo_type" value="standard" />
              <?php wp_nonce_field( 'x-addons-demo-content' ); ?>

              <div class="content-container">

                <div class="content standard selected">
                  <div class="content-inner">
                    <h3><?php _e( 'Standard', '__x__' ); ?></h3>
                    <p>Standard demo content will setup the Customizer settings and homepage design with placeholder content for the demo you select. Additionally, you can include optional example posts or portfolio items to see how certain features work.</p>
                    <select name="standard-demo" class="x-demo-select">
                      <?php foreach ( $standard_demos as $key => $info ) : ?>
                        <option data-demo-url="<?php echo $info['demo_url']; ?>" value="<?php echo $info['url']; ?>"><?php echo $info['title']; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <a href="<?php echo $standard_first['demo_url']; ?>" class="demo-content-link" target="_blank"><span>Demo</span></a>
                    <fieldset>
                      <legend class="screen-reader-text"><span>Import Posts</span></legend>
                      <label for="standard-demo-posts">
                        <input name="standard-demo-posts" type="checkbox" id="standard-demo-posts" value="yes">
                        <span>Import Posts</span>
                      </label>
                    </fieldset>
                    <fieldset>
                      <legend class="screen-reader-text"><span>Import Portfolio Items</span></legend>
                      <label for="standard-demo-portfolio-items">
                        <input name="standard-demo-portfolio-items" type="checkbox" id="standard-demo-portfolio-items" value="yes">
                        <span>Import Portfolio Items</span>
                      </label>
                    </fieldset>
                  </div>
                </div>

                <div class="content expanded">
                  <div class="content-inner">
                    <h3><?php _e( 'Expanded', '__x__' ); ?></h3>
                    <p>Our Expanded Demos allow you to setup complete sites with the click of a mouse! Everything from the customizer settings, to pages with full graphics are implemented for you, giving you ready-made designs in an instant.</p>
                    <select name="expanded-demo" class="x-demo-select">
                      <?php foreach ( $expanded_demos as $name => $info ) : ?>
                        <option data-demo-url="<?php echo $info['demo_url']; ?>" value="<?php echo $name; ?>"><?php echo $info['title']; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <a href="<?php echo $expanded_first['demo_url']; ?>" class="demo-content-link" target="_blank"><span>Demo</span></a>
                  </div>
                </div>

              </div>

              <p class="submit">
                <input type="submit" name="setup" id="x-addons-demo-content-submit" class="button button-primary" value="<?php printf( __( 'Setup Standard Demo: %s', '__x__' ), $standard_first['title'] ); ?>">
              </p>

            </form>
          </div>
        </div>

        <?php

        $troubleshooting_info = '<p>While our Standard and Expanded demo content can be used any time, it is recommended for use on new installations as a starting point. <a href="https://theme.co/go/join-demodashboard.php" target="_blank">Purchase another license</a>.</p>'
                              . '<p>If you feel you have run into a problem, check out our <a href="https://community.theme.co/kb/demo-content/#troubleshooting" target="_blank">troubleshooting tips</a>.</p>';

        if ( ! X_REVOLUTION_SLIDER_IS_ACTIVE ) {
          $troubleshooting_info .= '<p><strong>Please note:</strong> Since Revolution Slider is not currently active, any sliders used in our Expanded demos will not be setup. If you wish for these sliders to be setup, please ensure that you have Revolution Slider installed and activated.</p>';
        }

        ?>

        <div class="troubleshooting"><?php echo $troubleshooting_info; ?></div>

      <?php endif; ?>

    </div>

  </div>

<?php

}



// Setup
// =============================================================================

function x_addons_page_demo_content_setup() {

  $data = array();

  if ( isset( $_GET['clean-cache'] ) ) {
    delete_option( 'x_demo_importer_registry' );
    delete_transient( 'x_demo_listing' );
  }

  //
  // Try restoring from transient first
  //

  $transient = get_transient( 'x_demo_listing' );
  if ( false !== $transient )
    return $transient;


  //
  // Get Remote demo list
  //

  $request = wp_remote_get( 'http://themeco-demo-content.s3.amazonaws.com/x/' . apply_filters( 'x_demo_listing_index', 'index' ) . '.json' );


  //
  // Check if request returns an error.
  //

  if ( is_wp_error( $request ) ) {

    if ( isset( $_GET['x-verbose'] ) && $_GET['x-verbose'] == 1 ) {
      x_dump( $request->get_error_message(), 350, 'var_dump' );
    }

    $data['error'] = __( 'Unable to retrieve demo content. Your WordPress install may be having issues making outbound HTTP requests. For more information, please review the <a href="https://theme.co/community/kb/connection-issues/">connection issues</a> article in our Knowledge Base.', '__x__' );
    return $data;
  }

  $response = json_decode( $request['body'], true );

  if ( is_array($response) && isset( $response['standard'] ) && isset( $response['expanded'] ) && !empty( $response['standard'] ) && !empty( $response['expanded'] ) ) {
    $data['standard_demos'] = $response['standard'];
    $data['expanded_demos'] = $response['expanded'];
    set_transient( 'x_demo_listing', $data, 12 * HOUR_IN_SECONDS );
  } else {
    $data['standard_demos'] = array( 'undefined' => array( 'title' => '', 'url' => '' ) );
    $data['expanded_demos'] = $data['standard_demos'];
    $data['error'] = __( 'No demos found. Refreshing this page may resolve the issue. If it persists, please review the <a href="https://theme.co/community/kb/connection-issues/">connection issues</a> article in our Knowledge Base.', '__x__' );
  }

  return $data;

}