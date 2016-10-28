
<div class="updated woocommerce-de-message warning">
    <div class="logo"></div>
	<h4><?php _e( 'Wichtig! Änderung der Lieferzeiten in Woocommerce German Market', Woocommerce_German_Market::get_textdomain() ); ?></h4>

	<h5>
        <?php
            printf( __(
                'Bislang wurde vor jede Lieferzeit automatisch der Zusatz <i>\'ca.\'</i> gesetzt. Dies ist nun nichtmehr der Fall. Damit du deinen Onlineshop nach wie vor rechtssicher betreiben kannst, empfehlen wir dir, dies selbst in den Lieferzeiten zu ergänzen. Die Änderungen kannst du im <a href="%s">Lieferzeiteneditor</a> vornehmen.',
                Woocommerce_German_Market::get_textdomain()
            ), admin_url( 'edit-tags.php?taxonomy=product_delivery_times&post_type=product' ) );
        ?>
    </h5>

	<br class="clear" />
	<br/>

	<form action="<?php admin_url( 'admin.php' ); ?>" method="post">
		<input type="submit" class="button" name="woocommerce_de_upgrade_deliverytimes" value="<?php _e( ' Diese Meldung ausblenden', Woocommerce_German_Market::get_textdomain() );?>" />
	</form>

	</form>
</div>