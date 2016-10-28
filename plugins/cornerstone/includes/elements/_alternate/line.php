<?php

class CS_Line extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'line',
      'title'       => __( 'Line', csl18n() ),
      'section'     => 'structure',
      'description' => __( 'Line description.', csl18n() ),
      'supports'    => array( 'id', 'class', 'style' )
    );
  }

  public function controls() {

  	$this->addControl(
      'line_color',
      'color',
      __( 'Color', csl18n() ),
      __( 'Choose a specific color for this line. Reset the color picker to inherit a color.', csl18n() ),
      ''
    );

    $this->addControl(
      'line_height',
      'text',
      __( 'Height', csl18n() ),
      __( 'Specify a height for this line.', csl18n() ),
      '1px'
    );

  }

  public function attribute_injections( $atts ) {

  	if ( isset( $atts['line_color'] ) && '' != $atts['line_color'] )
			$atts['injected_styles'][] = 'border-top-color: ' . $atts['line_color'] . ';';

		if ( isset( $atts['line_height'] ) && '' != $atts['line_height'] )
			$atts['injected_styles'][] = 'border-top-width: ' . $atts['line_height'] . ';';

  	return $atts;
  }


  public function render( $atts ) {

    extract( $atts );

    $shortcode = "[x_line{$extra}]";

    return $shortcode;

  }

}