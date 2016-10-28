<?php

/**
 * Element Controls: Pricing Table Column
 */

return array(

	'title' => array(
		'type' => 'title',
		'context' => 'content',
		'suggest' => __( 'Standard', csl18n() ),
	),

	'content' => array(
		'type' => 'editor',
		'ui'   => array(
			'title'   => __( 'Content', csl18n() ),
      'tooltip' => __( 'Specify your pricing column content.', csl18n() ),
		),
		'context' => 'content',
		'suggest' => __( "[cs_icon_list]\n    [cs_icon_list_item type=\"check\"]First Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"times\"]Second Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"times\"]Third Feature[/cs_icon_list_item]\n[/cs_icon_list]\n\n[x_button href=\"#\" size=\"large\"]Buy Now![/x_button]", csl18n() ),
	),

	'featured' => array(
		'type' => 'toggle',
		'ui'   => array(
			'title'   => __( 'Featured Column', csl18n() ),
      'tooltip' => __( 'Enable to specify this column as your featured item.', csl18n() ),
		)
	),

	'featured_sub' => array(
		'type' => 'text',
		'ui'   => array(
			'title'   => __( 'Featured Subheading', csl18n() ),
      'tooltip' => __( 'Enter text for your featured column subheading here.', csl18n() ),
		),
		'condition' => array(
      'featured' => true
    )
	),

	'currency' => array(
		'type' => 'text',
		'ui'   => array(
			'title'   => __( 'Currency', csl18n() ),
      'tooltip' => __( 'Enter your desired currency symbol here.', csl18n() ),
		)
	),

	'price' => array(
		'type' => 'text',
		'ui'   => array(
			'title'   => __( 'Price', csl18n() ),
      'tooltip' => __( 'Enter the price for this column.', csl18n() ),
		),
	),

	'interval' => array(
		'type' => 'text',
		'ui'   => array(
			'title'   => __( 'Interval', csl18n() ),
      'tooltip' => __( 'Enter the duration for this payment (e.g. "Weekly," "Per Year," et cetera).', csl18n() ),
		)
	),

);