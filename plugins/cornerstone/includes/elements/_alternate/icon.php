<?php

class CS_Icon extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'icon',
      'title'       => __( 'Icon', csl18n() ),
      'section'     => 'typography',
      'description' => __( 'Icon description.', csl18n() ),
      'supports'    => array( 'id', 'class', 'style' )
    );
  }

  public function controls() {

    $this->addControl(
      'type',
      'icon-choose',
      __( 'Icon', csl18n() ),
      __( 'Specify the icon you would like to use.', csl18n() ),
      'check'
    );

    $this->addControl(
      'icon_color',
      'color',
      __( 'Icon Color &amp; Background Color', csl18n() ),
      __( 'Specify custom colors for your icon if desired.', csl18n() ),
      ''
    );

    $this->addControl(
      'bg_color',
      'color',
      NULL,
      NULL,
      ''
    );

    $this->addControl(
      'icon_size',
      'text',
      __( 'Icon Size &amp; Background Size', csl18n() ),
      __( 'Specify custom dimensions for your icon for use in situations other than inline.', csl18n() ),
      ''
    );

    $this->addControl(
      'bg_size',
      'text',
      NULL,
      NULL,
      ''
    );

    $this->addControl(
      'bg_border_radius',
      'text',
      __( 'Background Border Radius', csl18n() ),
      __( 'Give your icon\'s background a custom border radius.', csl18n() ),
      ''
    );

  }

  public function render( $atts ) {

    extract( $atts );

    $shortcode = "[x_icon type=\"{$type}\" icon_color=\"{$icon_color}\" bg_color=\"{$bg_color}\" icon_size=\"{$icon_size}\" bg_size=\"{$bg_size}\" bg_border_radius=\"{$bg_border_radius}\"{$extra}]";

    return $shortcode;

  }

}