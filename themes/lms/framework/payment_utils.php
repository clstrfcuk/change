<?php require_once("../../../../wp-load.php"); ?>
<?php
	$paymenttype = $_REQUEST['paymenttype'];
	$level = $_REQUEST['level'];
	$description = $_REQUEST['description'];
	$currency = $_REQUEST['currency'];
	$price = $_REQUEST['price'];
	$period = $_REQUEST['period'];
	$term = $_REQUEST['term'];
	$cbproductno = $_REQUEST['cbproductno'];
	$cbskin = $_REQUEST['cbskin'];
	$cbflowid = $_REQUEST['cbflowid'];
	
	$payment_url = '';
	
	if($paymenttype == 'stripe') {
		
		$payment_url = do_shortcode('[s2Member-Pro-Stripe-Form level="'.$level.'" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
		
	} else if($paymenttype == 'authnet') {
		
		$payment_url = do_shortcode('[s2Member-Pro-AuthNet-Form level="'.$level.'" desc="'.$description.'" cc="'.$currency.'" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" /]');
		
	} else if($paymenttype == 'clickbank') {
		
		$cb_productno = dttheme_option('dt_course','s2member-cb-productno');
		$cb_skin = dttheme_option('dt_course','s2member-cb-skin');
		$cb_flowid = dttheme_option('dt_course','s2member-cb-flowid');
		
		$payment_url = do_shortcode('[s2Member-Pro-ClickBank-Button cbp="'.$cb_productno.'" cbskin="'.$cb_skin.'" cbfid="'.$cb_flowid.'" cbur="" cbf="auto" level="'.$level.'" desc="'.$description.'" custom="'.$_SERVER["HTTP_HOST"].'" rp="'.$period.'" rt="'.$term.'" rr="0" image="default" output="anchor" /]');
		
	} else if($paymenttype == 'paypal') {
		
		$payment_url = do_shortcode('[s2Member-Pro-PayPal-Form level="'.$level.'" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="2" image="" output="url"/]');
	
	} else if($paymenttype == 'paypal-default') {
		
		$payment_url = do_shortcode('[s2Member-PayPal-Button level="'.$level.'" desc="'.$description.'" ps="paypal" lc="" cc="'.$currency.'" dg="0" ns="1" custom="'.$_SERVER["HTTP_HOST"].'" ra="'.$price.'" rp="'.$period.'" rt="'.$term.'" rr="BN" rrt="" rra="1" image="" output="url"/]');
		
	}
	
	echo ($payment_url != '') ? $payment_url : '';
?>