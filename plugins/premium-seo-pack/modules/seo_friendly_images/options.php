<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'seo_friendly_images' => array(
				'title' 	=> __('SEO Friendly Images', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Basic Setup</h2>
							<p>Automatically adds alt and title attributes to all your images in all your website pages.</p>
							<h3>Alt Rewriter tags</h3>
							<ul>
								<li><code>{focus_keyword}</code> - replaces with your Focus Keywords (if you have)</li>
								<li><code>{title}</code> - replaces with your page title</li>
								<li><code>{image_name}</code> - replaces with image name (without extension)</li>
								<li><code>{nice_image_name}</code> - replaces with image name nice formmated. Exclude special character with space (without extension)</li>
							</ul><br />', 'psp'),
					),
						
					'image_alt' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Image Alternate text:', 'psp'),
						'desc' 		=> __('Your images alternate text attribute. &lt;img src=&quot;images/&quot; width=&quot;&quot; height=&quot;&quot; <strong>alt=&quot;your_alt&quot;</strong>&gt;', 'psp')
					),
					
					'image_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Image  Title text:', 'psp'),
						'desc' 		=> __('Your images title text attribute. &lt;img src=&quot;images/&quot; width=&quot;&quot; height=&quot;&quot; <strong>title=&quot;your_alt&quot;</strong>&gt;', 'psp')
					),
					
					'keep_default_alt' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Keep image alt:', 'psp'),
						'desc' 		=> __('Choose if you want to keep the current images alternate text attribute.', 'psp'),
						'options'	=> array(
							'yes' => __('YES', 'psp'),
							'no' => __('NO', 'psp')
						)
						
					),
					
					'keep_default_title' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Keep image title:', 'psp'),
						'desc' 		=> __('Choose if you want to keep the current images title text attribute.', 'psp'),
						'options'	=> array(
							'yes' => __('YES', 'psp'),
							'no' => __('NO', 'psp')
						)
					),
				)
			)
		)
	)
);