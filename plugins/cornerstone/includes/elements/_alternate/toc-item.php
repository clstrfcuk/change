<?php

class CS_Toc_Item extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'toc-item',
      'title'       => __( 'TOC Item', csl18n() ),
      'section'     => '_content',
      'render'      => false,
      'delegate'    => true,
      'context'     => 'generator'
    );
  }

  public function controls() { }

}