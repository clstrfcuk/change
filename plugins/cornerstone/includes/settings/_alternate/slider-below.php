<?php

class CS_Settings_Slider_Below extends Cornerstone_Legacy_Setting_Section {

  public function data() {
    return array(
      'name'        => 'slider-below',
      'title'       => __( 'Slider Settings: Below Masthead', csl18n() ),
      'priority' => '28'
    );
  }

  public function condition() {
    return ( apply_filters( 'x_settings_pane', false ) && ( class_exists( 'RevSlider' ) || class_exists( 'LS_Sliders' ) ) );
  }

  public function controls() {

    global $post;

    if ( $post->post_type == 'page') {
      $this->pageControls();
    }

  }

  public function pageControls() {

    global $post;

    $choices = array(
      array( 'value' => '', 'label' => __( 'Deactivated', csl18n() ) )
    );

    $sliders = apply_filters( 'x_sliders_meta', array() );

    foreach ( $sliders as $key => $value ) {
			$choices[] = array( 'value' => $key, 'label' => $value['source'] . ': ' . $value['name'] );
    }

    $default_slider = get_post_meta( $post->ID, '_x_slider_below', true );

    $this->addControl(
      'x_slider_below',
      'select',
      __( 'Slider', csl18n() ),
      __( 'To activate your slider, select an option from the dropdown. To deactivate your slider, set the dropdown back to "Deactivated."', csl18n() ),
      $default_slider,
      array(
        'choices' => $choices,
        'notLive' => 'settings-theme-changed'
      )
    );

    $this->addControl(
      'x_slider_below_bg_video',
      'text',
      __( 'Optional Background Video', csl18n() ),
      __( 'Include your video URL(s) here. If using multiple sources, separate them using the pipe character (|) and place fallbacks towards the end (i.e. .webm then .mp4 then .ogv).', csl18n() ),
      get_post_meta( $post->ID, '_x_slider_below_bg_video', true ),
      array(
        'notLive' => 'settings-theme-changed'
      )
    );

    $this->addControl(
      'x_slider_below_bg_video_poster',
      'text',
      __( 'Video Poster Image (For Mobile)', csl18n() ),
      __( 'Click the button to upload your video poster image to show on mobile devices, or enter it in manually using the text field above. Only select one image for this field. To clear, delete the image URL from the text field and save your page.', csl18n() ),
      get_post_meta( $post->ID, '_x_slider_below_bg_video_poster', true ),
      array(
        'notLive' => 'settings-theme-changed'
      )
    );

    $this->addControl(
      'x_slider_below_scroll_bottom_anchor_enable',
      'toggle',
      __( 'Enable Scroll Bottom Anchor', csl18n() ),
      __( 'Select to enable the scroll bottom anchor for your slider.', csl18n() ),
      ( 'on' == get_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_enable', true ) ),
      array(
        'notLive' => 'settings-theme-changed'
      )
    );

    $alignments = array(
      array( 'value' => 'top left', 'label'      => __( 'Top Left', csl18n() ) ),
      array( 'value' => 'top center', 'label'    => __( 'Top Center', csl18n() ) ),
      array( 'value' => 'top right', 'label'     => __( 'Top Right', csl18n() ) ),
      array( 'value' => 'bottom left', 'label'   => __( 'Bottom Left', csl18n() ) ),
      array( 'value' => 'bottom center', 'label' => __( 'Bottom Center', csl18n() ) ),
      array( 'value' => 'bottom right', 'label'  => __( 'Bottom Right', csl18n() ) )
    );

    $this->addControl(
      'x_slider_below_scroll_bottom_anchor_alignment',
      'select',
      __( 'Scroll Bottom Anchor Alignment', csl18n() ),
      __( 'Select the alignment of the scroll bottom anchor for your slider.', csl18n() ),
      get_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_alignment', true ),
      array(
        'choices' => $alignments,
        'notLive' => 'settings-theme-changed'
      )
    );

    $this->addControl(
      'x_slider_below_scroll_bottom_anchor_color',
      'text',
      __( 'Scroll Bottom Anchor Color', csl18n() ),
      __( 'Select the color of the scroll bottom anchor for your slider.', csl18n() ),
      get_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_color', true ),
      array(
        'notLive' => 'settings-theme-changed'
      )
    );

    $this->addControl(
      'x_slider_below_scroll_bottom_anchor_color_hover',
      'text',
      __( 'Scroll Bottom Anchor Color Hover', csl18n() ),
      __( 'Select the hover color of the scroll bottom anchor for your slider.', csl18n() ),
      get_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_color_hover', true ),
      array(
        'notLive' => 'settings-theme-changed'
      )
    );

  }

  public function handler( $atts ) {

  	global $post;

    if ( $post->post_type != 'page' )
    	return;

  	extract( $atts );

  	update_post_meta( $post->ID, '_x_slider_below', $x_slider_below );
    update_post_meta( $post->ID, '_x_slider_below_bg_video', $x_slider_below_bg_video );
    update_post_meta( $post->ID, '_x_slider_below_bg_video_poster', $x_slider_below_bg_video_poster );
    update_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_enable', ( $x_slider_below_scroll_bottom_anchor_enable == 'true' ) ? 'on' : '' );
    update_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_alignment', $x_slider_below_scroll_bottom_anchor_alignment );
    update_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_color', $x_slider_below_scroll_bottom_anchor_color );
    update_post_meta( $post->ID, '_x_slider_below_scroll_bottom_anchor_color_hover', $x_slider_below_scroll_bottom_anchor_color_hover );


  }

}