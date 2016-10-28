<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $x_video_lock_options;

if ( isset( $_POST['x_video_lock_form_submitted'] ) ) {
  if ( strip_tags( $_POST['x_video_lock_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    //
    // Set "Include" settings to an empty array if no value is set to avoid
    // array_map() notice if second parameter isn't an array.
    //

    $x_video_lock_entries_include_post = ( isset( $_POST['x_video_lock_entries_include'] ) ) ? $_POST['x_video_lock_entries_include'] : array();

    $kses_allowed_tags = array(
      'iframe' => array( 'src' => array(), 'width' => array(), 'height' => array(), 'frameborder' => array(), 'webkitallowfullscreen' => array(), 'mozallowfullscreen' => array(), 'allowfullscreen' => array() )
    );

    $x_video_lock_options['x_video_lock_enable']                 = ( isset( $_POST['x_video_lock_enable'] ) ) ? strip_tags( $_POST['x_video_lock_enable'] ) : '';
    $x_video_lock_options['x_video_lock_delay']                  = strip_tags( $_POST['x_video_lock_delay'] );
    $x_video_lock_options['x_video_lock_width']                  = strip_tags( $_POST['x_video_lock_width'] );
    $x_video_lock_options['x_video_lock_heading_enable']         = ( isset( $_POST['x_video_lock_heading_enable'] ) ) ? strip_tags( $_POST['x_video_lock_heading_enable'] ) : '';
    $x_video_lock_options['x_video_lock_heading']                = strip_tags( $_POST['x_video_lock_heading'] );
    $x_video_lock_options['x_video_lock_heading_color']          = strip_tags( $_POST['x_video_lock_heading_color'] );
    $x_video_lock_options['x_video_lock_subheading_enable']      = ( isset( $_POST['x_video_lock_subheading_enable'] ) ) ? strip_tags( $_POST['x_video_lock_subheading_enable'] ) : '';
    $x_video_lock_options['x_video_lock_subheading']             = strip_tags( $_POST['x_video_lock_subheading'] );
    $x_video_lock_options['x_video_lock_subheading_color']       = strip_tags( $_POST['x_video_lock_subheading_color'] );
    $x_video_lock_options['x_video_lock_source']                 = strip_tags( $_POST['x_video_lock_source'] );
    $x_video_lock_options['x_video_lock_video']                  = strip_tags( $_POST['x_video_lock_video'] );
    $x_video_lock_options['x_video_lock_video_poster']           = strip_tags( $_POST['x_video_lock_video_poster'] );
    $x_video_lock_options['x_video_lock_video_autoplay_enable']  = ( isset( $_POST['x_video_lock_video_autoplay_enable'] ) ) ? strip_tags( $_POST['x_video_lock_video_autoplay_enable'] ) : '';
    $x_video_lock_options['x_video_lock_video_controls_disable'] = ( isset( $_POST['x_video_lock_video_controls_disable'] ) ) ? strip_tags( $_POST['x_video_lock_video_controls_disable'] ) : '';
    $x_video_lock_options['x_video_lock_embed']                  = stripslashes( wp_kses( $_POST['x_video_lock_embed'], $kses_allowed_tags ) );
    $x_video_lock_options['x_video_lock_button_text']            = strip_tags( $_POST['x_video_lock_button_text'] );
    $x_video_lock_options['x_video_lock_button_link']            = strip_tags( $_POST['x_video_lock_button_link'] );
    $x_video_lock_options['x_video_lock_button_style']           = strip_tags( $_POST['x_video_lock_button_style'] );
    $x_video_lock_options['x_video_lock_button_delay']           = strip_tags( $_POST['x_video_lock_button_delay'] );
    $x_video_lock_options['x_video_lock_close_enable']           = ( isset( $_POST['x_video_lock_close_enable'] ) ) ? strip_tags( $_POST['x_video_lock_close_enable'] ) : '';
    $x_video_lock_options['x_video_lock_entries_include']        = array_map( 'strip_tags', $x_video_lock_entries_include_post );

    update_option( 'x_video_lock', $x_video_lock_options );

  }

}



// Get Options
// =============================================================================

$x_video_lock_options = apply_filters( 'x_video_lock_options', get_option( 'x_video_lock' ) );

if ( $x_video_lock_options != '' ) {

  $x_video_lock_enable                 = $x_video_lock_options['x_video_lock_enable'];
  $x_video_lock_delay                  = $x_video_lock_options['x_video_lock_delay'];
  $x_video_lock_width                  = $x_video_lock_options['x_video_lock_width'];
  $x_video_lock_heading_enable         = $x_video_lock_options['x_video_lock_heading_enable'];
  $x_video_lock_heading                = $x_video_lock_options['x_video_lock_heading'];
  $x_video_lock_heading_color          = $x_video_lock_options['x_video_lock_heading_color'];
  $x_video_lock_subheading_enable      = $x_video_lock_options['x_video_lock_subheading_enable'];
  $x_video_lock_subheading             = $x_video_lock_options['x_video_lock_subheading'];
  $x_video_lock_subheading_color       = $x_video_lock_options['x_video_lock_subheading_color'];
  $x_video_lock_source                 = $x_video_lock_options['x_video_lock_source'];
  $x_video_lock_video                  = $x_video_lock_options['x_video_lock_video'];
  $x_video_lock_video_poster           = $x_video_lock_options['x_video_lock_video_poster'];
  $x_video_lock_video_autoplay_enable  = $x_video_lock_options['x_video_lock_video_autoplay_enable'];
  $x_video_lock_video_controls_disable = $x_video_lock_options['x_video_lock_video_controls_disable'];
  $x_video_lock_embed                  = $x_video_lock_options['x_video_lock_embed'];
  $x_video_lock_button_text            = $x_video_lock_options['x_video_lock_button_text'];
  $x_video_lock_button_link            = $x_video_lock_options['x_video_lock_button_link'];
  $x_video_lock_button_style           = $x_video_lock_options['x_video_lock_button_style'];
  $x_video_lock_button_delay           = $x_video_lock_options['x_video_lock_button_delay'];
  $x_video_lock_close_enable           = $x_video_lock_options['x_video_lock_close_enable'];
  $x_video_lock_entries_include        = $x_video_lock_options['x_video_lock_entries_include'];

}