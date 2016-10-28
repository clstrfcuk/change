<?php

/**
 * Element Definition: Block Grid
 */

class CSE_Block_Grid {

	public function ui() {
		return array(
      'title'       => __( 'Block Grid', csl18n() ),
    );
	}

	public function register_shortcode() {
  	return false;
  }

	public function update_build_shortcode_atts( $atts ) {
		return $atts;
	}

}