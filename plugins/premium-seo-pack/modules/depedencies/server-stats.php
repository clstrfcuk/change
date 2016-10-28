<?php
// soap
if (extension_loaded('soap')) {
?>
<div class="psp-message psp-success">
	SOAP extension installed on server
</div>
<?php
}else{
?>
<div class="psp-message psp-error">
	SOAP extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}

// Woocommerce
if( class_exists( 'Woocommerce' ) ){
?>
<div class="psp-message psp-success">
	 WooCommerce plugin installed
</div>
<?php
}else{
?>
<div class="psp-message psp-error">
	WooCommerce plugin not installed, in order the product to work please install WooCommerce wordpress plugin.
</div>
<?php
}

// curl
if ( function_exists('curl_init') ) {
?>
<div class="psp-message psp-success">
	cURL extension installed on server
</div>
<?php
}else{
?>
<div class="psp-message psp-error">
	cURL extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}
?>
<?php
