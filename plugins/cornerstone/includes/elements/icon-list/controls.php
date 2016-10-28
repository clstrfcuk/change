<?php

/**
 * Element Controls: Icon List
 */

return array(

	'elements' => array(
		'type' => 'sortable',
		'options' => array(
			'element' => 'icon-list-item',
			'floor'   => 1,
		),
		'context' => 'content',
		'suggest' => array(
	    array( 'title' => __( 'Icon List Item 1', csl18n() ), 'type' => 'check' ),
	    array( 'title' => __( 'Icon List Item 2', csl18n() ), 'type' => 'check' ),
	    array( 'title' => __( 'Icon List Item 3', csl18n() ), 'type' => 'times' )
	  )

	),

);