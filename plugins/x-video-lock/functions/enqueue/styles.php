<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output Site Styles
//   02. Enqueue Admin Styles
// =============================================================================

// Output Site Styles
// =============================================================================

function x_video_lock_output_site_styles() {

  require( X_VIDEO_LOCK_PATH . '/functions/options.php' );

  if ( isset( $x_video_lock_enable ) && $x_video_lock_enable == 1 ) :

    $included = $x_video_lock_entries_include;

    if ( is_page( $included ) || is_single( $included ) ) :

      $admin_bar_is_showing = is_admin_bar_showing();

      ?>

      /*
      // Base styles.
      */

      .x-video-lock-overlay {
        position: fixed;
        top: <?php echo ( $admin_bar_is_showing ) ? '32px' : '0' ; ?>;
        left: 0;
        right: 0;
        bottom: 0;
        overflow-x: hidden;
        overflow-y: auto;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 9999;
      }

      .x-video-lock-wrap-outer {
        display: table;
        width: 100%;
        height: 100%;
      }

      .x-video-lock-wrap-inner {
        display: table-cell;
        vertical-align: middle;
        padding: 25px;
      }

      .x-video-lock {
        display: block;
        overflow: auto;
        margin: 0 auto;
        padding: 0 25px;
        text-align: center;
        background-color: #fff;
        box-shadow: 0 0.085em 1.25em 0 rgba(0, 0, 0, 0.85);
      }

      .x-video-lock h1 {
        margin: 0;
        font-size: 32px;
        line-height: 1;
      }

      .x-video-lock p {
        margin: 0;
        font-size: 18px;
        line-height: 1.5;
      }

      .x-video-lock h1 + p {
        margin-top: 10px;
      }

      .x-video-lock .x-video {
        margin: 0 -25px;
      }


      /*
      // Containers.
      */

      .x-video-lock-text,
      .x-video-lock-btn-wrap-inner {
        padding: 25px 0;
      }


      /*
      // Close.
      */

      .x-video-lock-close {
        display: block;
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        font-size: 18px;
        line-height: 1;
        text-align: center;
        color: rgba(255, 255, 255, 0.1);
      }

      .x-video-lock-close:hover {
        color: rgba(255, 255, 255, 0.35);
      }


      /*
      // Button styles.
      */

      <?php if ( strpos( $x_video_lock_button_style, 'marketing' ) !== false ) : ?>

        .x-video-lock .x-btn {
          margin: 0 0 0.25em;
          padding: 0.575em 1.125em 0.675em;
          font-size: 18px;
          line-height: 1.3;
          text-transform: uppercase;
          background-repeat: repeat-x;
          border-radius: 5px;
          z-index: 10;
        }

        .x-video-lock .x-btn-circle-wrap {
          margin: 0;
        }

      <?php endif; ?>

      <?php if ( $x_video_lock_button_style == 'marketing-red' ) : ?>

        .x-video-lock .x-btn {
          border: 1px solid #ac1100;
          text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
          color: #fff;
          background-color: #ff2a13;
          background-image: -webkit-linear-gradient(top, #ff2a13, #d6200a);
          background-image: linear-gradient(top, #ff2a13, #d6200a);
          box-shadow: 0 0.25em 0 0 #a71000, 0 4px 9px rgba(0, 0, 0, 0.75);
        }

      <?php elseif ( $x_video_lock_button_style == 'marketing-yellow' ) : ?>

        .x-video-lock .x-btn {
          border: 1px solid #f1c600;
          text-shadow: 0 1px 1px rgba(255, 255, 255, 0.775);
          color: #1e2756;
          background-color: #f9ff00;
          background-image: -webkit-linear-gradient(top, #f9ff00, #ffcb00);
          background-image: linear-gradient(top, #f9ff00, #ffcb00);
          box-shadow: 0 0.25em 0 0 #d7b100, 0 4px 9px rgba(0, 0, 0, 0.75);
        }

      <?php elseif ( $x_video_lock_button_style == 'marketing-green' ) : ?>

        .x-video-lock .x-btn {
          border: 1px solid #177b41;
          text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
          color: #fff;
          background-color: #2ecc71;
          background-image: -webkit-linear-gradient(top, #2ecc71, #27ae60);
          background-image: linear-gradient(top, #2ecc71, #27ae60);
          box-shadow: 0 0.25em 0 0 #1a894a, 0 4px 9px rgba(0, 0, 0, 0.75);
        }

      <?php endif; ?>


      /*
      // Responsive.
      */

      <?php if ( $admin_bar_is_showing ) : ?>

        @media (max-width: 782px) {
          .x-video-lock-overlay {
            top: 46px;
          }
        }

        @media (max-width: 600px) {
          .x-video-lock-overlay {
            top: 0;
          }
        }

      <?php endif; ?>

    <?php endif;

  endif;

}

add_action( 'x_head_css', 'x_video_lock_output_site_styles' );



// Enqueue Admin Styles
// =============================================================================

function x_video_lock_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-video-lock' ) {

    wp_enqueue_style( 'x-video-lock-admin-css', X_VIDEO_LOCK_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_video_lock_enqueue_admin_styles' );