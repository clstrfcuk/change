<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

$__google_locations = array(
	'com' => 'Default - Google.com',
	'as' => 'American Samoa',
	'off.ai' => 'Anguilla',
	'com.ag' => 'Antigua and Barbuda',
	'com.ar' => 'Argentina',
	'com.au' => 'Australia',
	'at' => 'Austria',
	'az' => 'Azerbaijan',
	'be' => 'Belgium',
	'com.br' => 'Brazil',
	'vg' => 'British Virgin Islands',
	'bi' => 'Burundi',
	'ca' => 'Canada',
	'td' => 'Chad',
	'cl' => 'Chile',
	'com.co' => 'Colombia',
	'co.cr' => 'Costa Rica',
	'ci' => 'Cote d\'Ivoire',
	'com.cu' => 'Cuba',
	'cz' => 'Czech Rep.',
	'cd' => 'Dem. Rep. of the Congo',
	'dk' => 'Denmark',
	'dj' => 'Djibouti',
	'com.do' => 'Dominican Republic',
	'com.ec' => 'Ecuador',
	'com.sv' => 'El Salvador',
	'fm' => 'Federated States of Micronesia',
	'com.fj' => 'Fiji',
	'fi' => 'Finland',
	'fr' => 'France',
	'gm' => 'The Gambia',
	'ge' => 'Georgia',
	'de' => 'Germany',
	'com.gi' => 'Gibraltar',
	'com.gr' => 'Greece',
	'gl' => 'Greenland',
	'gg' => 'Guernsey',
	'hn' => 'Honduras',
	'com.hk' => 'Hong Kong',
	'co.hu' => 'Hungary',
	'co.in' => 'India',
	'ie' => 'Ireland',
	'co.im' => 'Isle of Man',
	'co.il' => 'Israel',
	'it' => 'Italy',
	'com.jm' => 'Jamaica',
	'co.jp' => 'Japan',
	'co.je' => 'Jersey',
	'kz' => 'Kazakhstan',
	'co.kr' => 'Korea',
	'lv' => 'Latvia',
	'co.ls' => 'Lesotho',
	'li' => 'Liechtenstein',
	'lt' => 'Lithuania',
	'lu' => 'Luxembourg',
	'mw' => 'Malawi',
	'com.my' => 'Malaysia',
	'com.mt' => 'Malta',
	'mu' => 'Mauritius',
	'com.mx' => 'Mexico',
	'ms' => 'Montserrat',
	'com.na' => 'Namibia',
	'com.np' => 'Nepal',
	'nl' => 'Netherlands',
	'co.nz' => 'New Zealand',
	'com.ni' => 'Nicaragua',
	'com.nf' => 'Norfolk Island',
	'no' => 'Norway',
	'com.pk' => 'Pakistan',
	'com.pa' => 'Panama',
	'com.py' => 'Paraguay',
	'com.pe' => 'Peru',
	'com.ph' => 'Philippines',
	'pn' => 'Pitcairn Islands',
	'pl' => 'Poland',
	'pt' => 'Portugal',
	'com.pr' => 'Puerto Rico',
	'cg' => 'Rep. of the Congo',
	'ro' => 'Romania',
	'ru' => 'Russia',
	'rw' => 'Rwanda',
	'sh' => 'Saint Helena',
	'sm' => 'San Marino',
	'com.sa' => 'Saudi Arabia',
	'com.sg' => 'Singapore',
	'sk' => 'Slovakia',
	'co.za' => 'South Africa',
	'es' => 'Spain',
	'se' => 'Sweden',
	'ch' => 'Switzerland',
	'com.tw' => 'Taiwan',
	'co.th' => 'Thailand',
	'tt' => 'Trinidad and Tobago',
	'com.tr' => 'Turkey',
	'com.ua' => 'Ukraine',
	'ae' => 'United Arab Emirates',
	'co.uk' => 'United Kingdom',
	'com.uy' => 'Uruguay',
	'uz' => 'Uzbekistan',
	'vu' => 'Vanuatu',
	'co.ve' => 'Venezuela',
);
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'serp' => array(
				'title' 	=> 'SERP',
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
				
					'nbreq_max_limit' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 100,
						'size' 		=> 'small',
						'title' 	=> __('Max number of requests:', 'psp'),
						'force_width'=> '100',
						'desc' 		=> __('Max number of requests to Google API (please verify <a href="https://developers.google.com/custom-search/json-api/v1/overview?csw=1" target="_blank">Google price list</a>)', 'psp'),
					),
					
					'developer_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Google Developer Key:', 'psp'),
						'desc' 		=> __('Google Developer Key', 'psp')
					),
					
					'custom_search_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Custom Search Engine ID:', 'psp'),
						'desc' 		=> __('Custom Search Engine ID', 'psp')
					),
					
					'google_country' 	=> array(
						'type' 		=> 'select',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '170',
						'title' 	=> __('Google locations', 'psp'),
						'desc' 		=> __('All possible locations.', 'psp'),
						'options' 	=> $__google_locations
					),
					
					'last_status' 	=> array(
						'type' 		=> 'textarea-array',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Request Last Status:', 'psp'),
						'desc' 		=> __('Last Status retrieved from Google, for the SERP request operation', 'psp')
					),
					
					'top_type' 	=> array(
						'type' 		=> 'select',
						'size' 		=> 'large',
						'title' 	=> __('Top Size:', 'psp'),
						'force_width'=> '100',
						'desc' 		=> __('Check if first X google results', 'psp'),
						'options'	=> $psp->doRange( range(10, 100, 10) )
					),
					
					'cron_email' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'title' 	=> __('Email:', 'psp'),
						'desc' 		=> __('Your email where you will receive cron notifications', 'psp')
					),
					
					array(
						'type' 		=> 'html',
						'html' 		=> '<div style="margin: 20px 0px 20px 20px;">
							<h2>How to setup the Cron Job</h2>
							<p>WordPress comes with its own cron job that allows you to schedule your posts and events. However, in many situations, the WP-Cron is not working well and leads to posts missed their publication schedule and/or scheduled events not executed.<br>
							<span id="more-74"></span><br>
							To understand why this happen, we need to know that the WP-Cron is not a real cron job. It is in fact a virtual cron that only works when a page is loaded. In short, when a page is requested on the frontend/backend, WordPress will first load WP-Cron, follow by the necessary page to display to your reader. The loaded WP-Cron will then check the database to see if there is any thing that needs to be done.</p>
							<p>Reasons for WP-Cron to fail could be due to:</p>
							<ul>
								<li>DNS issue in the server.</li>
								<li>Plugins conflict</li>
								<li>Heavy load in the server which results in WP-Cron not executed fully</li>
								<li>WordPress bug</li>
								<li>Using of cache plugins that prevent the WP-Cron from loading</li>
								<li>And many other reasons</li>
							</ul>
							<p>There are many ways to solve the WP-Cron issue, but the one that I am going to propose here is to disable the virtual WP-Cron and use a real cron job instead.</p>
							<h3>Why use a real cron job?</h3>
							<p>By using a real cron job, you can be sure that all your scheduled items are executed. For popular blogs with high traffic, using a real cron job can also reduce the server bandwidth and reduce the chances of your server crashing, especially when you are experiencing Digg/Slashdot effect.</p>
							<h3>Scheduling a real cron job</h3>
							<p>To configure a real cron job, you will need access to your cPanel or Admin panel (we will be using cPanel in this tutorial).</p>
							<p>1. Log into your cPanel.</p>
							<p>2. Scroll down the list of applications until you see the “<em>cron jobs</em>�? link. Click on it.</p>
							<p><img width="510" height="192" class="aligncenter size-full wp-image-81" alt="wpcron-cpanel" src="{plugin_folder_uri}assets/wpcron-cpanel.png"></p>
							<p>3. Under the <em>Add New Cron Job</em> section, choose the interval that you want it to run the cron job. I have set it to run every 15minutes, but you can change it according to your liking.</p>
							<p><img width="470" height="331" class="aligncenter size-full wp-image-82" alt="wpcron-add-new-cron-job" src="{plugin_folder_uri}/assets/wpcron-add-new-cron-job.png"></p>
							<p>4. In the Command field, enter the following:</p>
						
							<div class="wp_syntax"><div class="code"><pre style="font-family:monospace;" class="bash"><span style="color: #c20cb9; font-weight: bold;">wget</span> <span style="color: #660033;">-q</span> <span style="color: #660033;">-O</span> - </span>' . ( $psp->cfg["paths"]["plugin_dir_url"] ) . '<span style="color: #000000; font-weight: bold;"></span>do-cron.php <span style="color: #000000; font-weight: bold;">&gt;/</span>dev<span style="color: #000000; font-weight: bold;">/</span>null <span style="color: #000000;">2</span><span style="color: #000000; font-weight: bold;">&gt;&amp;</span><span style="color: #000000;">1</span></pre></div></div>
						
							<p>5. Click the “Add New Cron Job�? button. You should now see a message like this:</p>
							<p><img width="577" height="139" class="aligncenter size-full wp-image-83" alt="wpcron-current-cron-job" src="{plugin_folder_uri}/assets/wpcron-current-cron-job.png"></p>
							<p>6. Next, using a FTP program, connect to your server and download the <code>wp-config.php</code> file.</p>
							<p>7. Open the <code>wp-config.php</code> file with a text editor and paste the following line:</p>
						
							<div class="wp_syntax"><div class="code"><pre style="font-family:monospace;" class="php"><span style="color: #990000;">define</span><span style="color: #009900;">(</span><span style="color: #0000ff;">\'DISABLE_WP_CRON\'</span><span style="color: #339933;">,</span> <span style="color: #009900; font-weight: bold;">true</span><span style="color: #009900;">)</span><span style="color: #339933;">;</span></pre></div></div>
						
							<p>8. Save and upload (and replace) this file back to the server. This will disable WordPress internal cron job.</p>
							<p>That’s it.</p>
						
						
							<a href="http://wpdailybits.com/blog/replace-wordpress-cron-with-real-cron-job/74"> Credits </a></div>'
					)

				)
			)
		)
	)
);