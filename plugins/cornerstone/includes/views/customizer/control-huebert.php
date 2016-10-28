<?php
	$default_value = '';
	if ( $this->setting->default );
		$default_value = cs_att('data-huebert-default-value', $this->setting->default );
?>
<label>
	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	<input data-cs-customizer-control="huebert" type="text" id="input_<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" <?php echo $default_value . ' '; $this->link(); ?>/>
</label>

<script>
var options = {};
<?php if ( $this->setting->default ) { ?>
	options.reset = 'default';
<?php } ?>
jQuery(document).ready(function($) { $('#input_<?php echo $this->id; ?>').huebert(options); });
</script>