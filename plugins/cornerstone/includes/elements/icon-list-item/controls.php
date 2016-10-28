<?php

/**
 * Element Controls: Icon List Item
 */

return array(

	'title' => array(
		'type' => 'title',
		'context' => 'content',
		'suggest' => __( 'New Item', csl18n() ),
	),

	'type' => array(
		'type' => 'icon-choose',
		'ui'   => array(
			'title'   => __( 'Icon', csl18n() ),
			'tooltip' => __( 'Specify the icon you would like to use as the bullet for your Icon List Item.', csl18n() ),
		)
	),

	'icon_color' => array(
		'type' => 'color',
		'ui' => array(
			'title'   => __( 'Icon Color', csl18n() ),
			'tooltip' =>__( 'Choose a custom color for your Icon List Item\'s icon.', csl18n() ),
		)
	),


	'link' => array(

		'mixin' => 'link',

		'enabled' => array(
			'type' => 'toggle',
			'ui'   => array(
				'title'   => __( 'Link', csl18n() ),
				'tooltip' => __( 'Add a link to the text for this item.', csl18n() ),
			)
		),

		'url' => array(
			'condition' => array(
				'group::enabled' => true
			)
		),

		'title' => array(
			'condition' => array(
				'group::enabled' => true
			)
		),

		'new_tab' => array(
			'condition' => array(
				'group::enabled' => true
			)
		)

	),

);