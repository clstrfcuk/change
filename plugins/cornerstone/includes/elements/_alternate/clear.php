<?php

class CS_Clear extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'clear',
      'title'       => __( 'Clear', csl18n() ),
      'section'     => 'structure',
      'description' => __( 'Clear description.', csl18n() ),
      'supports'    => array( 'id', 'class', 'style' ),
      'can_preview' => false
    );
  }

  public function controls() { }

  public function render( $atts ) {

    extract( $atts );

    return "[x_clear{$extra}]";

  }

}