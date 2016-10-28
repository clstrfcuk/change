<?php

// =============================================================================
// VIEWS/SITE/CONTENT-DOCK.PHP
// -----------------------------------------------------------------------------
// Plugin site output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Options
//   02. Output
// =============================================================================

// Require Options
// =============================================================================

require( X_VIDEO_LOCK_PATH . '/functions/options.php' );



// Output
// =============================================================================

if ( is_page( $x_video_lock_entries_include ) || is_single( $x_video_lock_entries_include ) ) :

  //
  // Enqueue library.
  //

  wp_enqueue_script( 'mediaelement' );


  //
  // Heading output.
  //

  if ( ( $x_video_lock_heading_enable == 1 ) ) {
    $heading_output = '<h1 style="color: ' . $x_video_lock_heading_color . ';">' . $x_video_lock_heading . '</h1>';
  } else {
    $heading_output = '';
  }


  //
  // Subheading output.
  //

  if ( ( $x_video_lock_subheading_enable == 1 ) ) {
    $subheading_output = '<p style="color: ' . $x_video_lock_subheading_color . ';">' . $x_video_lock_subheading . '</p>';
  } else {
    $subheading_output = '';
  }


  //
  // Text output.
  //

  if ( $heading_output != '' || $subheading_output != '' ) {
    $text_output = '<div class="x-video-lock-text">' . $heading_output . $subheading_output . '</div>';
  } else {
    $text_output = '';
  }


  //
  // Video output.
  //

  if ( $x_video_lock_source == 'self-hosted' ) {
    $autoplay       = ( $x_video_lock_video_autoplay_enable  == 1 ) ? 'true' : 'false';
    $controls       = ( $x_video_lock_video_controls_disable == 1 ) ? 'true' : 'false';
    $video          = do_shortcode( '[x_video_player m4v="' . $x_video_lock_video . '" poster="' . $x_video_lock_video_poster . '" hide_controls="' . $controls . '" autoplay="' . $autoplay . '" preload="metadata" loop="false" muted="false" no_container="true"]' );
    $video_output   = str_replace( array( "'", '</script>' ), array( '"', '<\/script>' ), $video );
  } elseif ( $x_video_lock_source == 'third-party' ) {
    $video_output = do_shortcode( '[x_video_embed no_container="true"]' . $x_video_lock_embed . '[/x_video_embed]' );
  }


  //
  // Button output.
  //

  if ( strpos( $x_video_lock_button_style, 'marketing' ) !== false ) {
    $button = do_shortcode( '[button class="' . $x_video_lock_button_style . '" href="' . $x_video_lock_button_link . '" size="large" circle="true"]' . $x_video_lock_button_text . '[/button]' );
  } else {
    $button = do_shortcode( '[button href="' . $x_video_lock_button_link . '" size="large"]' . $x_video_lock_button_text . '[/button]' );
  }

  $button_output = str_replace( "'", '"', $button );


  //
  // Close output.
  //

  if ( ( $x_video_lock_close_enable == 1 ) ) {
    $close_output = '<a href="#" class="x-video-lock-close"><i class="x-icon-close" data-x-icon="&#xf00d;"></i></a>';
  } else {
    $close_output = '';
  }


  //
  // Miscellaneous.
  //

  $x_video_lock_delay          = $x_video_lock_delay * 1000;
  $x_video_lock_button_delay   = $x_video_lock_button_delay * 1000;
  $x_video_lock_button_display = ( $x_video_lock_button_delay == 0 ) ? 'block' : 'none';

  ?>

  <script id="x-video-lock-js">

    jQuery(document).ready(function($) {

      var videoLockOutput = '<div class="x-video-lock-overlay">' +
                              '<?php echo $close_output; ?>' +
                              '<div class="x-video-lock-wrap-outer">' +
                                '<div class="x-video-lock-wrap-inner">' +
                                  '<div class="x-video-lock" style="max-width: <?php echo $x_video_lock_width . "px"; ?>;">' +
                                    '<?php echo $text_output; ?>' +
                                    '<?php echo $video_output; ?>' +
                                    '<div class="x-video-lock-btn-wrap" style="display: <?php echo $x_video_lock_button_display; ?>;">' +
                                      '<div class="x-video-lock-btn-wrap-inner">' +
                                        '<?php echo $button_output; ?>' +
                                      '</div>' +
                                    '</div>' +
                                  '</div>' +
                                '</div>' +
                              '</div>' +
                            '</div>';

      setTimeout( function() {

        $('html, body').css({ 'overflow' : 'hidden' });
        $('.site').append( videoLockOutput );
        xData.api.process();

        <?php if ( $x_video_lock_button_delay != 0 ) { ?>

          setTimeout( function() {
            $('.x-video-lock-btn-wrap').slideDown();
          }, <?php echo $x_video_lock_button_delay; ?> );

        <?php } ?>

        <?php if ( $close_output != '' ) { ?>

          $('.x-video-lock-close').click(function(e) {
            e.preventDefault();
            $('html, body').removeAttr('style');
            $('.x-video-lock-overlay').remove();
          });

        <?php } ?>

      }, <?php echo $x_video_lock_delay; ?> );

    });

  </script>

<?php endif;