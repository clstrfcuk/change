<div class="updated woocommerce-de-message">
    <div class="logo"></div>
	<h4><?php _e( 'Herzlichen Glückwunsch, Sie haben WooCommerce für den deutschsprachigen Raum erfolgreich installiert. ', Woocommerce_German_Market::get_textdomain() ); ?></h4>

	<h5><?php _e( 'Bevor Sie loslegen können, müssen noch ein paar Einstellungen vorgenommen werden', Woocommerce_German_Market::get_textdomain() ); ?></h5>

	<br class="clear" />
	<br/>

	<form action="<?php echo admin_url( 'plugins.php' ); ?>" method="post">
		<?php wp_nonce_field(); ?>

		<label for="woocommerce_de_install_de_options">
		    <input type="checkbox" id="woocommerce_de_install_de_options" name="woocommerce_de_install_de_options" checked="checked" />
		    <?php _e( 'Soll der Shop automatisch auf deutsche Standardwerte eingestellt werden? (Kann jederzeit wieder geändert werden )', Woocommerce_German_Market::get_textdomain() ); ?>
		    <br>
			<ul class="description">
    			<li><?php _e( 'Währung: &euro;,', Woocommerce_German_Market::get_textdomain() ); ?></li>
                <li><?php _e( 'Land: Deutschland,', Woocommerce_German_Market::get_textdomain() ); ?></li>
                <li><?php _e( 'Zeige Warenkorbpreise inklusive MwSt.,', Woocommerce_German_Market::get_textdomain() ); ?></li>
                <li><?php _e( 'Zeige Summe inklusive MwSt.,', Woocommerce_German_Market::get_textdomain() ); ?></li>
                <li><?php _e( 'Zeige Preise inklusive MwSt.,', Woocommerce_German_Market::get_textdomain() ); ?></li>
                <li><?php $default_tax_rates = WGM_Defaults::get_default_tax_rates();
                			printf(
                                __( 'Stelle den MwSt. Satz auf %s%%', Woocommerce_German_Market::get_textdomain() ),
                                number_format( $default_tax_rates[ 0 ][ 'rate' ] , 2, ',', '.' )
                            );
                    ?></li>
                <li><?php printf(
                                __( 'und den reduzierten MwSt. Satz auf %s%%', Woocommerce_German_Market::get_textdomain() ),
                                number_format( $default_tax_rates[ 1 ][ 'rate' ], 2, ',', '.' )
                            );
                    ?></li>

    			<?php _e( 'Maße und Gewichte', Woocommerce_German_Market::get_textdomain() ); ?>:

    			<?php
    			$default_product_attributes = WGM_Defaults::get_default_procuct_attributes();
    			foreach( $default_product_attributes[ 0 ][ 'elements' ] as $scale_units )
    			    echo __( $scale_units[ 'description' ], Woocommerce_German_Market::get_textdomain() ) . ',&nbsp;';
    			?>
    		</ul>
		</label>

		<br><br>

		<?php
        $pages = WGM_Helper::get_default_pages();
        foreach( $pages as $page ){
            $post_titles[] = $page[ 'post_title' ];
        }

        $pages =  sprintf(
                        '<i>%s</i>',
                        implode(
                            ', ',
                            array_map( 'ucfirst', $post_titles )
                        )
                   );

		?>

		<label for="woocommerce_de_install_de_pages">
			<input type="checkbox" id="woocommerce_de_install_de_pages" name="woocommerce_de_install_de_pages" checked="checked" />
		    <?php printf( __(  'Sollen die zusätzlichen Seiten %1$s angelegt werden?', Woocommerce_German_Market::get_textdomain() ), $pages ); ?>
		    <br>
	    	<span class="description">
		    	<?php _e(  'Die Seiten sind Wichtig für einen rechtskonformen Shop in Deutschland.', Woocommerce_German_Market::get_textdomain() ); ?>
		    </span>
		</label>

		<br><br>

		<label for="woocommerce_de_install_de_pages_overwrite">
			<input type="checkbox" id="woocommerce_de_install_de_pages_overwrite" name="woocommerce_de_install_de_pages_overwrite" />
			<?php _e( 'Sollen schon existierende Seiten überschrieben werden?', Woocommerce_German_Market::get_textdomain() ); ?>
			<br>
			<span class="description">
				<?php _e(  'Die Seiten werden durch den Titel identifiziert.', Woocommerce_German_Market::get_textdomain() ); ?>
			</span>
		</label>

		<br class="clear">
		<br class="clear">

	<div>
		<input type="submit" class="button-primary" name="woocommerce_de_install" value="<?php _e(  'Installiere', Woocommerce_German_Market::get_textdomain() );?> - WooCommerce German Market" />
	</div>

	</form>
</div>