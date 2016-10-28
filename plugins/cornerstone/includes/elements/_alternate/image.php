<?php

class CS_Image extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'image',
      'title'       => __( 'Image', csl18n() ),
      'section'     => 'media',
      'description' => __( 'Image description.', csl18n() ),
      'supports'    => array( 'id', 'class', 'style' ),
      'empty'       => array( 'src' => '' )
    );
  }

  public function controls() {

    $this->addControl(
      'image_style',
      'select',
      __( 'Style', csl18n() ),
      __( 'Select the image style.', csl18n() ),
      'none',
      array(
        'choices' => array(
          array( 'value' => 'none',      'label' => __( 'None', csl18n() ) ),
          array( 'value' => 'thumbnail', 'label' => __( 'Thumbnail', csl18n() ) ),
          array( 'value' => 'rounded',   'label' => __( 'Rounded', csl18n() ) ),
          array( 'value' => 'circle',    'label' => __( 'Circle', csl18n() ) )
        )
      )
    );

    $this->addControl(
      'src',
      'image',
      __( 'Src', csl18n() ),
      __( 'Enter your image.', csl18n() ),
      ''
    );

    $this->addControl(
      'alt',
      'text',
      __( 'Alt', csl18n() ),
      __( 'Enter in the alt text for your image', csl18n() ),
      ''
    );

    $this->addControl(
      'link',
      'toggle',
      __( 'Link', csl18n() ),
      __( 'Select to wrap your image in an anchor tag.', csl18n() ),
      false
    );

    $this->addControl(
      'href',
      'text',
      __( 'Href', csl18n() ),
      __( 'Enter in the URL you want to link to.', csl18n() ),
      '#',
      array(
      	'condition' => array(
          'link' => true
        )
      )

    );

    $this->addControl(
      'href_title',
      'text',
      __( 'Link Title Attribute', csl18n() ),
      __( 'Enter in the title attribute you want for your link.', csl18n() ),
      '',
      array(
      	'condition' => array(
          'link' => true
        )
      )
    );

    $this->addControl(
      'href_target',
      'toggle',
      __( 'Open Link in New Window', csl18n() ),
      __( 'Select to open your link in a new window.', csl18n() ),
      false,
      array(
      	'condition' => array(
          'link' => true
        )
      )
    );

    $this->addControl(
      'info',
      'select',
      __( 'Info', csl18n() ),
      __( 'Select whether or not you want to add a popover or tooltip to your image.', csl18n() ),
      'none',
      array(
        'choices' => array(
          array( 'value' => 'none',    'label' => __( 'None', csl18n() ), ),
          array( 'value' => 'popover', 'label' => __( 'Popover', csl18n() ), ),
          array( 'value' => 'tooltip', 'label' => __( 'Tooltip', csl18n() ), )
        ),
        'condition' => array(
          'link' => true
        )
      )
    );

    $this->addControl(
      'info_place',
      'choose',
      __( 'Info Placement', csl18n() ),
      __( 'Select where you want your popover or tooltip to appear.', csl18n() ),
      'top',
      array(
        'columns' => '4',
        'choices' => array(
          array( 'value' => 'top',    'icon' => fa_entity('arrow-up'),    'tooltip' => __( 'Top', csl18n() ) ),
          array( 'value' => 'right',  'icon' => fa_entity('arrow-right'), 'tooltip' => __( 'Right', csl18n() ) ),
          array( 'value' => 'bottom', 'icon' => fa_entity('arrow-down'),  'tooltip' => __( 'Bottom', csl18n() ) ),
          array( 'value' => 'left',   'icon' => fa_entity('arrow-left'),  'tooltip' => __( 'Left', csl18n() ) )
        ),
        'condition' => array(
          'link' => true,
          'info' => array( 'popover', 'tooltip' )
        )
      )
    );

    $this->addControl(
      'info_trigger',
      'select',
      __( 'Info Trigger', csl18n() ),
      __( 'Select what actions you want to trigger the popover or tooltip.', csl18n() ),
      'hover',
      array(
        'choices' => array(
          array( 'value' => 'hover', 'label' => __( 'Hover', csl18n() ) ),
          array( 'value' => 'click', 'label' => __( 'Click', csl18n() ) ),
          array( 'value' => 'focus', 'label' => __( 'Focus', csl18n() ) )
        ),
        'condition' => array(
          'link' => true,
          'info' => array( 'popover', 'tooltip' )
        )
      )
    );

    $this->addControl(
      'info_content',
      'text',
      __( 'Info Content', csl18n() ),
      __( 'Extra content for the popover.', csl18n() ),
      '',
      array(
        'condition' => array(
          'link' => true,
          'info' => array( 'popover', 'tooltip' )
        )
      )
    );

  }

  public function render( $atts ) {

    extract( $atts );

    $href_target = ( $href_target == 'true' ) ? 'blank' : '';

    $shortcode = "[x_image type=\"$image_style\" src=\"$src\" alt=\"$alt\" link=\"$link\" href=\"$href\" title=\"$href_title\" target=\"$href_target\" info=\"$info\" info_place=\"$info_place\" info_trigger=\"$info_trigger\" info_content=\"$info_content\"{$extra}]";

    return $shortcode;

  }

}