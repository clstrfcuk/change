<?php

/**
 * Element Definition: Icon List
 */

class CSE_Icon_List {

	public function ui() {
		return array(
      'title'       => __( 'Icon List', csl18n() ),
    );
	}

	public function flags() {
		return array(
			'dynamic_child' => true
		);
	}

}