<?php

/**
 * Element Controls: Pricing Table
 */

return array(

	'elements' => array(
		'type' => 'sortable',
		'ui' => array(
			'title' => __( 'Pricing Table Columns', csl18n() ),
      'tooltip' =>__( 'Add your pricing table columns here.', csl18n() ),
    ),
		'options' => array(
			'element' => 'pricing-table-column',
			'newTitle' => __( 'Column %s', csl18n() ),
			'floor'   => 1,
      'capacity' => 5
		),
		'context' => 'content',
		'suggest' => array(
	    array( 'title' => __( 'Basic', csl18n() ),    'price' => '19', 'featured' => false, 'content' => __( "[cs_icon_list]\n    [cs_icon_list_item type=\"check\"]First Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"times\"]Second Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"times\"]Third Feature[/cs_icon_list_item]\n[/cs_icon_list]\n\n[x_button href=\"#\" size=\"large\"]Buy Now![/x_button]", csl18n() ) ),
      array( 'title' => __( 'Standard', csl18n() ), 'price' => '29', 'featured' => true,  'content' => __( "[cs_icon_list]\n    [cs_icon_list_item type=\"check\"]First Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"check\"]Second Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"times\"]Third Feature[/cs_icon_list_item]\n[/cs_icon_list]\n\n[x_button href=\"#\" size=\"large\"]Buy Now![/x_button]", csl18n() ), 'featured_sub' => 'Most Popular!' ),
      array( 'title' => __( 'Pro', csl18n() ),      'price' => '39', 'featured' => false, 'content' => __( "[cs_icon_list]\n    [cs_icon_list_item type=\"check\"]First Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"check\"]Second Feature[/cs_icon_list_item]\n    [cs_icon_list_item type=\"check\"]Third Feature[/cs_icon_list_item]\n[/cs_icon_list]\n\n[x_button href=\"#\" size=\"large\"]Buy Now![/x_button]", csl18n() ) )
	  )

	),

);