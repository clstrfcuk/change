<?php
/**
 * Email Functions
 *
 * @author jj, ap
 */
Class WGM_Email {

/**
	* Add legal Text to emails
	*
	* @author jj, et
	* @access public
	* @static
	* @uses get_option, get_post, do_shortcode
	* @return void
	*
	*/
	public static function email_de_footer() {

		WGM_Email::start_email_footer();

		// Impressum
		if ( apply_filters( 'wgm_email_display_imprint', TRUE ) == TRUE ) {
			if( 'yes' == get_option( WGM_Helper::get_wgm_option( 'woocommerce_de_use_backend_footer_text_for_imprint_enabled' ) ) ) {
				$imprint_text = get_option( 'woocommerce_email_footer_text' );
			} else {
				$imprint_page_id = get_option( WGM_Helper::get_wgm_option( 'impressum' ) );
				$imprint_page = get_post( $imprint_page_id );

				$imprint_text = $imprint_page->post_content;
			}

			WGM_Email::the_mail_footer_section(
				__( 'Impressum', Woocommerce_German_Market::get_textdomain() ),
				$imprint_text
			);
		}

        // Allgemeine Geschäftsbedingungen
        if ( apply_filters( 'wgm_email_display_terms', TRUE ) == TRUE ) {
            $terms_page_id = get_option( WGM_Helper::get_wgm_option( 'agb' ) );
            $terms_page	= get_post( $terms_page_id );

            WGM_Email::the_mail_footer_section(
                __( 'Allgemeine Geschäftsbedingungen', Woocommerce_German_Market::get_textdomain() ),
                $terms_page->post_content
            );
        }

        if( ! $_SESSION[ 'WGM_CHECKOUT' ][ 'only_digital' ] ){
		    // Widerrufsrecht
		    if ( apply_filters( 'wgm_email_display_cancellation_policy', TRUE ) == TRUE ) {
			    $withdrawal_page_id = get_option( WGM_Helper::get_wgm_option( 'widerruf' ) );
			    $withdrawal_page	= get_post( $withdrawal_page_id );

			    WGM_Email::the_mail_footer_section(
				    __( 'Widerrufsrecht', Woocommerce_German_Market::get_textdomain() ),
				    $withdrawal_page->post_content
			    );
		    }
        };

        if( $_SESSION[ 'WGM_CHECKOUT' ][ 'has_digital' ] ){
            // Widerrufsrecht digitale Inhalte
            if ( apply_filters( 'wgm_email_display_cancellation_policy_for_digital_goods', TRUE ) == TRUE ) {
                $withdrawal_page_id = get_option( WGM_Helper::get_wgm_option( 'widerruf_fuer_digitale_medien' ) );
                $withdrawal_page	= get_post( $withdrawal_page_id );

                WGM_Email::the_mail_footer_section(
                    __( 'Widerruf für digitale Medien', Woocommerce_German_Market::get_textdomain() ),
                    $withdrawal_page->post_content
                );
            }

            // Hinweis für die Aufgabe des Widerrufsrechts
            if ( apply_filters( 'wgm_email_display_cancellation_policy_for_digital_goods_acknowlagement', TRUE ) == TRUE ) {
                WGM_Email::the_mail_footer_section(
                    __( 'Verzichts auf das Widerrufsrecht', Woocommerce_German_Market::get_textdomain() ),
                    __( 'Sie haben ausdrücklich zugestimmt, dass wir mit der Ausführung des Vertrages vor Ablauf der Widerrufsfrist beginnen. Sie haben durch Ihre Zustimmung ebenfalls erklärt, dass Sie Kenntnis davon haben, dass Sie durch Ihre Zustimmung mit Beginn der Ausführung des Vertrages Ihr Widerrufsrecht verlieren.', Woocommerce_German_Market::get_textdomain() )
                );
            }
        }

        echo '<div style="' . apply_filters( 'wgm_email_footer_style', 'float:left; width: 100%;' ) .' ">
                <h3>'. apply_filters( 'wgm_email_customer_infomation_text',
                    __('Generelle Kundeninformationen zu unserem Shop', Woocommerce_German_Market::get_textdomain() ) ) .'</h3>
            </div>';

        // Versandkosten und Lieferung
        if ( apply_filters( 'wgm_email_display_delivery', TRUE ) == TRUE ) {
            $terms_page_id = get_option( WGM_Helper::get_wgm_option( 'versandkosten__lieferung' ) );
            $terms_page	= get_post( $terms_page_id );

            WGM_Email::the_mail_footer_sub_section(
                __( 'Versandkosten & Lieferung', Woocommerce_German_Market::get_textdomain() ),
                $terms_page->post_content
            );
        }

        // Zahlungsarten
        if ( apply_filters( 'wgm_email_display_payment_methods', TRUE ) == TRUE ) {
            $payment_methods_page_id = get_option( WGM_Helper::get_wgm_option( 'zahlungsarten' ) );
            $payment_methods_page	= get_post( $payment_methods_page_id );

            WGM_Email::the_mail_footer_sub_section(
                __( 'Zahlungsarten', Woocommerce_German_Market::get_textdomain() ),
                $payment_methods_page->post_content
            );
        }

		WGM_Email::end_email_footer();
	}

	/**
	 * Print Mail Footer Section HTML
	 * @param  string $title
	 * @param  string $content
	 * @return void
	 */
	private static function the_mail_footer_section( $title, $content ) {
		?>
		<div style="<?php echo apply_filters( 'wgm_email_footer_style', 'float:left; width: 100%;' ); ?>">
			<h3><?php echo $title; ?></h3>
			<p><?php echo apply_filters( 'the_content', $content ); ?></p>
		</div>
		<?php
	}

    private static function the_mail_footer_sub_section( $title, $content ) {
        ?>
        <div style="<?php echo apply_filters( 'wgm_email_footer_style', 'float:left; width: 100%;' ); ?>">
            <h4><?php echo $title; ?></h4>
            <p><?php echo apply_filters( 'the_content', $content ); ?></p>
        </div>
    <?php
    }

	private static function start_email_footer(){
		echo apply_filters( 'wgm_start_email_footer_html', '<div class="wgm-wrap-email-appendixes">' );
	}

	private static function end_email_footer(){
		echo apply_filters( 'wgm_end_email_footer_html', '</div>' );
	}
}
?>