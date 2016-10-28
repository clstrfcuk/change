<?php

class CS_Protect extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'protect',
      'title'       => __( 'Protect', csl18n() ),
      'section'     => 'content',
      'description' => __( 'Protect description.', csl18n() ),
      'helpText'    => array(
        'title'     => __( 'How does this work?', csl18n() ),
        'message'   => __( 'This element offers simple protection based on being logged in. Logged out users will be prompted to login before viewing the content.', csl18n() ),
      ),
      'supports'    => array( 'id', 'class', 'style' ),
      'empty'       => array( 'content' => '' )
    );
  }

  public function controls() {

    $this->addControl(
      'content',
      'textarea',
      __( 'Content', csl18n() ),
      __( 'Enter the text to go inside your Protect shortcode. This will only be visible to users who are logged in.', csl18n() ),
      ''
    );

  }

  public function render( $atts ) {

    extract( $atts );

    $shortcode = "[x_protect{$extra}]{$content}[/x_protect]";

    return $shortcode;

  }

}