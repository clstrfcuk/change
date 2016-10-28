<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function psp_postTypes_get() {
	global $psp;

	$post_types = get_post_types(array(
		'public'   => true
	));
	//unset media - images | videos are treated as belonging to post, pages, custom post types
	unset($post_types['attachment'], $post_types['revision']);
	return $post_types;
}

function psp_usersList_get() {
	global $psp;

	$args = array(
		'blog_id'      => $GLOBALS['blog_id'],
		'role'         => '',
		'meta_key'     => '',
		'meta_value'   => '',
		'meta_compare' => '',
		'meta_query'   => array(),
		'include'      => array(),
		'exclude'      => array(),
		'orderby'      => 'login',
		'order'        => 'ASC',
		'offset'       => '',
		'search'       => '',
		'number'       => '',
		'count_total'  => false,
		'fields'       => 'all',
		'who'          => ''
	);
	
	$users = array();
	$blogusers = get_users($args);
    foreach ($blogusers as $user) {
		$user_id = $user->ID;  
    	$username = $user->user_login;
		$role = $user->roles[0]; 
        $users["$user_id"] = $username . "&nbsp;&nbsp;($role)"; // $user->user_email;
    }
	return $users;
}

global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'google_authorship' => array(
				'title' 	=> __('Google Authorship', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> array(
					'save' => array(
						'value' => __('Save settings', 'psp'),
						'color' => 'green',
						'action'=> 'psp-saveOptions'
					)
				), // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					array(
						'type' 		=> 'message',
						
						'html' 		=> __('<div style="margin: 20px 0px 20px -20px;">
							<h2>Setup</h2>
		<h3>A. Link your content to your Google+ profile</h3>

		<ol class="google_authorship_help_step1">

			<li>Follow this link to open your Google+ profile: <a target="_blank" href="http://plus.google.com/me">http://plus.google.com/me</a></li>

			<li>
				Copy your Google+ profile url from the address bar to clipboard (see below picture):
				<br><img style="" alt="" src="{plugin_folder_uri}/assets/googleplus-profile.png">
			</li>

			<li>
				Go to your Wordpress Admin Panel and click Users, Your Profile. Paste the above mentioned google+ profile url to the "PSP Google+ Authorship" box.
				<br><img style="" alt="" src="{plugin_folder_uri}/assets/googleplus-profile-update.png">
			</li>

			<li>Click Update Profile.</li>

			<li>Repeat the above steps with all the other Wordpress users/authors on your blog.</li>

		</ol>

		<br>
		<h4>B. Add a reciprocal link back from your profile to the website you\'ve just updated at A.</h4>

		<ol class="google_authorship_help_step2">

			<li>Follow this link to edit the Contributor To section: <a target="_blank" href="http://plus.google.com/me/about/edit/co">http://plus.google.com/me/about/edit/co</a></li>


			<li>A dialog will appear. Scroll down to the "Contributor to" section. Click "Add custom link" and enter your website url: ' . get_bloginfo('url') . '</li>

			<li>Optional: For the label, you can use your website title: ' . get_bloginfo('name') . '</li>

			<li>If you want, click the drop-down list to specify who can see the link.</li>

			<li>Click Save.</li>

			<li>Tell your users/authors that they should also do the B. steps</li>

		</ol>

		<br>
		<h4>C. Testing</h4>

		<ol class="google_authorship_help_step3">

			<li>To see what author data Google can extract from your page, use the <a href="http://www.google.com/webmasters/tools/richsnippets" target="_blank">Google Rich Snippets Testing Tool</a>.</li>

		</ol>
		
		<br>
		<h4>Details</h4>
		<ul>
			<li>- this module also integrated <strong>coauthors_posts_links</strong> function from <strong>co-authors-plus plugin</strong>!</li>
		</ul>
							</div>', 'psp'),
					),

					array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Publisher Settings</h2>', 'psp'),
					),
					
					'publisher_google_url' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '500',
						'title' 	=> __('Google+ Profile URL: ', 'psp'),
						'desc' 		=> __('the url to your google+ profile to linked it with your website.', 'psp')
					),
					
					'publisher_location' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'header',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Location:', 'psp'),
						'desc' 		=> __('where you want the google+ profile for publisher should be displayed', 'psp'),
						'options' 	=> array(
							'disable'		=> __('Disabled', 'psp'), 
							'header'		=> __('In the header (not visible to website visitors) - recommended', 'psp'),
							'footer'		=> __('In the footer', 'psp')
						)
					),
					
					'publisher_visibility' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'hidden',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Visibility:', 'psp'),
						'desc' 		=> __('if you want the google+ profile for publisher to be displayed (available only for Location: In the footer)', 'psp'),
						'options' 	=> array(
							'visible'		=> __('Visible', 'psp'),
							'hidden'		=> __('Hidden', 'psp')
						)
					),

					array(
						'type' 		=> 'message',
						
						'html' 		=> __('
							<h2>Authorship Settings</h2>', 'psp'),
					),
						
					'post_types' 	=> array(
						'type' 		=> 'multiselect',
						'std' 		=> array('post','page'),
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('Post Types:', 'psp'),
						'desc' 		=> __('Select post types for whom you want to include the google+ url.', 'psp'),
						'options' 	=> psp_postTypes_get()
					),
					
					'homepage_authors' => array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('Homepage authors:', 'psp'),
						'desc' 		=> __('Select the homepage users/authors.', 'psp'),
						'options' 	=> psp_usersList_get()
					),
					
					'category_authors' => array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('Category authors:', 'psp'),
						'desc' 		=> __('Select the category pages users/authors.', 'psp'),
						'options' 	=> psp_usersList_get()
					),
					
					'tag_authors' => array(
						'type' 		=> 'multiselect',
						'std' 		=> array(),
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('Tag authors:', 'psp'),
						'desc' 		=> __('Select the tag pages users/authors.', 'psp'),
						'options' 	=> psp_usersList_get()
					),
					
					'author_location' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'header',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Location:', 'psp'),
						'desc' 		=> __('generic setting for all authors: where you want the google+ profile for author to be displayed', 'psp'),
						'options' 	=> array(
							'disabled'		=> __('Disabled', 'psp'),
							'header'		=> __('In the header (not visible to site visitors) - recommended', 'psp'),
							'footer'		=> __('In the footer', 'psp'),
							'replace'		=> __('Replace author link with the authors Google+ link (verify that your theme support it!)', 'psp'),
							'content_top'	=> __('In the content (top)', 'psp'),
							'content_bottom'=> __('In the content (bottom)', 'psp')
						)
					),
					
					'author_visibility' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'visible',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Visibility:', 'psp'),
						'desc' 		=> __('generic setting for all authors: if you want the google+ profile for author to be displayed (available only for Location: In the footer, In the content (top or bottom))', 'psp'),
						'options' 	=> array(
							'visible'		=> __('Visible', 'psp'),
							'hidden'		=> __('Hidden', 'psp')
						)
					),
					
					'author_feed' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Display in feeds? ', 'psp'),
						'desc' 		=> __('generic setting for all authors: if you want the google+ profile for author to be include in the feeds', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'author_newwindow' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Open link in new window?', 'psp'),
						'desc' 		=> __('generic setting for all authors: open the url to google+ profile for author in new window', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'author_title' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'Google+ Author',
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('URL Title: ', 'psp'),
						'desc' 		=> __('generic setting for all authors: url title - to google+ profile for author.', 'psp')
					),
					
					'author_text' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'Google+',
						'size' 		=> 'large',
						'force_width'=> '300',
						'title' 	=> __('URL Text: ', 'psp'),
						'desc' 		=> __('generic setting for all authors: url text - to google+ profile for author.', 'psp')
					)
				)
			)
		)
	)
);