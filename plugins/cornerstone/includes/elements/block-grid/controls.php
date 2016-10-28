<?php

/**
 * Element Controls: Block Grid
 */

return array(

	'elements' => array(
		'type' => 'sortable',
		'ui' => array(
			'title' => __( 'Block Grid Items', csl18n() ),
      'tooltip' =>__( 'Add a new item to your Block Grid.', csl18n() ),
    ),
		'options' => array(
			'element' => 'block-grid-item',
			'newTitle' => __( 'Block Grid Item %s', csl18n() ),
			'floor'   => 2,
		),
		'context' => 'content',
		'suggest' => array(
	    array( 'title' => __( 'Block Grid Item 1', csl18n() ) ),
      array( 'title' => __( 'Block Grid Item 2', csl18n() ) )
	  )

	),

	'type' => array(
		'type' => 'select',
		'ui'   => array(
			'title'   => __( 'Columns', csl18n() ),
			'tooltip' => __( 'Select how many columns of items should be displayed on larger screens. These will update responsively based on screen size.', csl18n() ),
		),
		'options' => array(
			'choices' => array(
				array( 'value' => 'two-up',   'label' => __( '2', csl18n() ) ),
				array( 'value' => 'three-up', 'label' => __( '3', csl18n() ) ),
				array( 'value' => 'four-up',  'label' => __( '4', csl18n() ) )
	    )
		)
	),

);