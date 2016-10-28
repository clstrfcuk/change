<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function __pspTC_get_image_sizes() {
    global $psp, $_wp_additional_image_sizes;

    $cache_name = 'psp_tiny_compress_wp_sizes';
    $cacheSizes = get_transient( $cache_name );

    $sizes = array();
    if ( !empty($cacheSizes) && is_array($cacheSizes) ) {
        $sizes = $cacheSizes;
    } else {

        // original image
        {
            $sizes[ '__original' ] = array(
                'width' => 0,
                'height' => 0,
            );
        }
            
        $get_intermediate_image_sizes = get_intermediate_image_sizes();
    
        // create array with sizes.
        // original source: http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
        foreach ( $get_intermediate_image_sizes as $_size ) {
    
            //if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {}
            $w = get_option( $_size . '_size_w' );
            $h = get_option( $_size . '_size_h' );
    
            if ( $w && $h );
            else {
                $w = $_wp_additional_image_sizes[ $_size ]['width'];
                $h = $_wp_additional_image_sizes[ $_size ]['height'];
            }
    
            $sizes[ $_size ] = array(
                'width' => $w,
                'height' => $h,
            );
        }

        // basic cache sistem
        set_transient( $cache_name, $sizes, 1200 ); // cache expires in 20 minutes
    }

    // display
    $_sizes = array();
    foreach ($sizes as $key => $size) {
        $_sizes["$key"] = $size['width'] && $size['height'] ?
            sprintf( '%s ( %d x %d )', $key, $size['width'], $size['height'] ) : sprintf( '%s', $key );
    }
    return $_sizes;
}

function __pspTC_connection_status() {
    global $psp;
    
    $html = array();
    
    // get the module init file
    require_once( $psp->cfg['paths']['plugin_dir_path'] . 'modules/tiny_compress/init.php' );
    // Initialize the pspTinyCompress class
    $pspTinyCompress = new pspTinyCompress();
    
    $connection_status = $pspTinyCompress->get_connection_status();
    $compress_limits = $pspTinyCompress->get_compress_limits();
    
    ob_start();
    ?>
        <div class="psp-form-row">
            <div class="psp-message psp-<?php echo $connection_status['status'] == 'valid' ? 'success' : 'error'; ?>">
                <p><?php echo __('Connection status: ', 'psp') . $connection_status['msg']; ?></p>
            </div>
        </div>
        
        <div class="psp-form-row">
            <div class="psp-message psp-<?php echo $compress_limits['status'] == 'valid' ? 'success' : 'error'; ?>">
                <p><?php echo __('Monthly limit: ', 'psp') . $compress_limits['msg']; ?></p>
            </div>
        </div>
    <?php
    $content = ob_get_contents();
    ob_end_clean();
    $html[] = $content;
    
    return implode( "\n", $html );
}

global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'tiny_compress' => array(
				'title' 	=> __('Tiny Compress', 'psp'),
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
							<p><strong>TinyPNG.com API</strong> uses optimization techniques specific to image format to remove unnecessary bytes from image files. It is a "lossless" tool, which means it optimizes the images without changing their look or visual quality.</p>
							<ul>
							    <li style="color: blue;">You only pay for what you use. The first 500 compression each month are free. You will only be billed if you compress more than 500 images in a month. <a href="https://tinypng.com/developers" target="_blank">More details here</a> - see bottom "Pricing" section.</li>
								<li>The TinyPNG.com service will download the image via the URL and will then return a URL to the new version of the image, which will be downloaded and will replace the original image on your server.</li>
								<li>The image must be less than 2 megabyte in size. This is a limit of the TinyPNG.com service.</li>
								<li>The image must be accessible from non-https URL. This is a limit of the TinyPNG.com service.</li>
								<li>The TinyPNG.com service needs to download the image via a URL and the image needs to be on a public server and not a local local development system. This is a limit of the TinyPNG.com service.</li>
								<li>The image must be local to the site, not stored on a CDN (Content Delivery Networks).</li>
							</ul>
						', 'psp'),
					),
					
                    '_connection_status' => array(
                        'type' => 'html',
                        'html' => __pspTC_connection_status()
                    ),
					
					'resp_timeout' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '60',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Response timeout: ', 'psp'),
						'desc' 		=> __('enter the maximum number of seconds you want to wait for response from TinyPNG.com service.', 'psp')
					),
					'do_upload' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('TinyPNG.com on Upload: ', 'psp'),
						'desc' 		=> __('TinyPNG.com on media image upload', 'psp'),
						'options'	=> array(
							'yes' 		=> __('YES', 'psp'),
							'no' 		=> __('NO', 'psp')
						)
					),
					'same_domain_url' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Image same domain: ', 'psp'),
						'desc' 		=> __('image url must be on same domain as website home url!', 'psp'),
						'options'	=> array(
							'yes' 		=> __('YES', 'psp'),
							'no' 		=> __('NO', 'psp')
						)
					),

                    'tiny_key'  => array(
                        'type'      => 'text',
                        'std'       => '',
                        'size'      => 'large',
                        'force_width'=> '250',
                        'title'     => __('TinyPNG.com API Key: ', 'psp'),
                        'desc'      => __('(required) get a free key from <a href="https://tinypng.com/developers" target="_blank">TinyPNG.com Developer section</a> (works for both PNG and JPEG).', 'psp')
                    ),
                    'image_sizes'  => array(
                        'type'      => 'multiselect_left2right',
                        'std'       => array('__original'),
                        'size'      => 'large',
                        'rows_visible'  => 8,
                        'force_width'=> '300',
                        'title'     => __('Select wp image sizes', $psp->localizationName),
                        'desc'      => __('(__original = original image file; we\'ll always shrink the original image file and optionally, we can compress the other image sizes created by wordpress). <span style="color: blue; font-weight: bold;">attention: each additional image size will affect your TinyPNG.com monthly limit.</span>', $psp->localizationName),
                        'info'      => array(
                            'left' => __('All wp image sizes list', $psp->localizationName),
                            'right' => __('Your chosen wp image sizes from list', $psp->localizationName),
                        ),
                        'options'   => __pspTC_get_image_sizes()
                    ),
                    'last_status'   => array(
                        'type'      => 'textarea-array',
                        'std'       => '',
                        'size'      => 'large',
                        'force_width'=> '400',
                        'title'     => __('Request Last Status:', 'psp'),
                        'desc'      => __('Last Status for smushit operation over an image', 'psp')
                    )

				)
			)
			
		)
	)
);