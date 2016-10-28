<?php 
$options = $currentSource->getParameterHTML();
?>

<div class="wrap">
	<h1><?php _e('GeoIP Detection', 'geoip-detect');?></h1>
	<p><a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Test IP Detection Lookup', 'geoip-detect')?></a></p>
	<?php if (!empty($message)): ?>
		<p class="geoip_detect_error">
		<?php echo $message; ?>
		</p>
<?php endif; ?>
	
	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
	<p>
		<?php echo $currentSource->getStatusInformationHTML(); ?>
	</p>
	<?php if ($options) : ?>
	<h2><?php _e('Options for this data source', 'geoip-detect'); ?></h2>
	<p>
		<form method="post" action="#">
			<input type="hidden" name="action" value="options-source" />
			<?php wp_nonce_field( 'geoip_detect_options-source' ); ?>
			<p><?php echo $options; ?></p>
			<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
			</p>	
		</form>
	</p>
	<?php endif; ?>
	<br/>

	<br /><br />
	<form method="post" action="#">
		<input type="hidden" name="action" value="choose" />
		<?php wp_nonce_field( 'geoip_detect_choose' ); ?>
		<h2><?php _e('Choose data source:', 'geoip-detect'); ?></h2>
		<a href="https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#which-data-source-should-i-choose">Help</a>
		<?php foreach ($sources as $s) : $id = $s->getId();?>
			<p><input type="radio" name="options[source]" value="<?php echo $id ?>" <?php if ($currentSource->getId() == $id) { echo 'checked="checked"'; } ?> /><?php echo $s->getLabel(); ?></p>
			<span class="detail-box">
				<?php echo $s->getDescriptionHTML(); ?>
			</span>
		<?php endforeach; ?>
		<br />
		<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
	</form>
	<form method="post" action="#">
		<input type="hidden" name="action" value="options" />
		<?php wp_nonce_field( 'geoip_detect_options' ); ?>
		<h3><?php _e('General Options', 'geoip-detect'); ?></h3>
		<p>
			<input type="checkbox" name="options[set_css_country]" value="1" <?php if (!empty($wp_options['set_css_country'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Add a country-specific CSS class to the &lt;body&gt;-Tag.', 'geoip-detect'); ?><br />
		</p>
		<p>
			<input type="checkbox" name="options[disable_pagecache]" value="1" <?php if (!empty($wp_options['disable_pagecache'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Disable caching a page that contains a shortcode or API call to geo-dependent functions.', 'geoip-detect'); ?><br />
			<span class="detail-box">
				<?php _e('At least WP SuperCache, W3TotalCache and ZenCache are supported.', 'geoip-detect'); ?>
			</span>	
				<?php if (!empty($wp_options['set_css_country']) && !empty($wp_options['disable_pagecache'])): ?>
				<span class="geoip_detect_error"><?php _e('Warning: As the CSS option above is active, this means that all pages are not cached.', 'geoip-detect'); ?></span>
				<?php endif; ?>
		</p>
		
		<p>
			<input type="checkbox" name="options[has_reverse_proxy]" value="1" <?php if (!empty($wp_options['has_reverse_proxy'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('The server is behind a reverse proxy', 'geoip-detect')?><em>
			<span class="detail-box">
			<?php if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) : ?>
			<?php printf(__('(With Proxy: %s - Without Proxy: %s - Client IP with current configuration: %s)', 'geoip-detect'), $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR'], geoip_detect2_get_client_ip()); ?><br />
			<?php else: ?>
			<?php echo __("(This doesn't seem to be the case.)", 'geoip-detect'); ?>
			<?php endif; ?>
			</em>
			</span>
		</p>
		<p>
			<?php _e('External IP of this server:', 'geoip-detect'); ?> <input type="text" name="options[external_ip]" value="<?php echo esc_attr($wp_options['external_ip']); ?>" placeholder="<?php _e('detect automatically', 'geoip-detect'); ?>" />
			<span class="detail-box">
			<?php _e('Current value:', 'geoip-detect'); ?> <?php echo geoip_detect2_get_external_ip_adress(); ?><br />
			<?php _e('If empty: Try to use an ip service to detect it (Internet connection is necessary). If this is not possible, 0.0.0.0 will be returned.', 'geoip-detect'); ?><br />
			<?php _e('(This external adress will be used when the request IP adress is not a public IP, e.g. 127.0.0.1)', 'geoip-detect'); ?>
			</span>
		</p>


		<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
		</p>
	</form>
	<?php if (!$ipv6_supported) : ?>
	<div class="geoip_detect_error">
		<h3><?php _e('IPv6 not supported', 'geoip-detect'); ?></h3>
		<p>
			<?php _e('Your version of PHP is compiled without IPv6-support, so it is not possible to lookup adresses like "2001:4860:4801:5::91". For more information see <a href="https://php.net/manual/en/function.inet-pton.php">PHP documentation & user comments</a>.', 'geoip-detect'); ?>
		</p>
	</div>
	<?php endif; ?>
	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>
<style>
.geoip_detect_error {
	display:block;
	clear: both;
    background-color: rgb(255, 255, 255);
    border-left: rgb(255, 0, 0) solid 4px;
    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);
    display: inline-block;
    font-size: 14px;
    line-height: 19px;
    margin-bottom: 0;
    margin-left: 2px;
    margin-right: 20px;
    margin-top: 25px;
    padding-bottom: 11px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 11px;
    text-align: left;
}
.detail-box {
	display: block;
	margin-left: 50px;
	color: #777;
}

</style>
