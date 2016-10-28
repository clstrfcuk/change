<?php

/**
 * Class Woocommerce_German_Market_Auto_Update
 */
class Woocommerce_German_Market_Auto_Update {

	/**
	 * @var null|Woocommerce_German_Market_Auto_Update
	 */
	private static $instance = NULL;

	/**
	 * Check if the plugin comes from marketpress
	 * dashboard
	 *
	 * @since	0.1
	 * @var		bool
	 */
	private static $is_marketpress = FALSE;

	/**
	 * The name of the parent class
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $parent_class = '';

	/**
	 * The URL for the update check
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $url_update_check = '';

	/**
	 * The URL for the update package
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $url_update_package = '';

	/**
	 * The holder for all our licenses
	 *
	 * @since	0.1
	 * @var		array
	 */
	public static $licenses = '';

	/**
	 * The license key
	 *
	 * @since	0.1
	 * @var		array
	 */
	public static $key = '';

	/**
	 * The URL for the key check
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $url_key_check = '';

	/**
	 * @var StdClass
	 */
	private $plugin_data;

	/**
	 * Name of the plugin sanitized
	 *
	 * @var string
	 */
	private $sanitized_plugin_name;

	/**
	 * @return Woocommerce_German_Market_Auto_Update
	 */
	public static function get_instance() {

		if ( self::$instance === NULL ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setting up some data, all vars and start the hooks
	 * needs from main plugin: plugin_name, plugin_base_name, plugin_url
	 *
	 * @param   stdClass $plugindata
	 *
	 * @return  void
	 */
	public function setup( $plugindata ) {

		require_once ABSPATH . 'wp-includes/pluggable.php';

		$this->plugin_data = $plugindata;
		$this->sanitized_plugin_name = $plugindata->plugin_slug;

		// Get all our licenses
		$this->get_key();

		// Setting up the license checkup URL
		self::$url_key_check = 'http://marketpress.com/mp-key/' . self::$key . '/' . $this->sanitized_plugin_name . '/' . sanitize_title_with_dashes( network_site_url() );
		self::$url_update_check = 'http://marketpress.com/mp-version/' . self::$key . '/' . $this->sanitized_plugin_name . '/' . sanitize_title_with_dashes( network_site_url() );
		self::$url_update_package = 'http://marketpress.com/mp-download/' . self::$key . '/' . $this->sanitized_plugin_name . '/' . sanitize_title_with_dashes( network_site_url() );

		// Parse the plugin Row Stuff
		add_filter( 'after_plugin_row_' . $this->plugin_data->plugin_base_name , array( $this, 'license_row' ) );

		// Add Admin Notice for the MarketPress Dashboard
		add_filter( 'network_admin_notices', array( $this, 'marketpress_dashboard_notice' ) );

		// Add regular admin notices
		add_filter( 'admin_notices', array( $this, 'marketpress_dashboard_notice' ) );

		// Due to we cannot update a form inside of a form
		// we need to redirect the update license request to the needed form
		if (
			isset( $_REQUEST[ 'license_key_' . $this->sanitized_plugin_name ] ) &&
			$_REQUEST[ 'license_key_' . $this->sanitized_plugin_name ] != '' &&
			isset( $_REQUEST[ 'submit_wgm_key' ] )
		) {
			wp_safe_redirect( admin_url( 'admin-post.php?action=update_license_key_' . 'wgm' . '&key=' . $_REQUEST[ 'license_key_' . $this->sanitized_plugin_name ] ) );
		}

		// Add Set License Filter
		add_filter( 'admin_post_update_license_key_' . 'wgm', array( $this, 'update_license' ) );

		// Remove Key Filter
		add_filter( 'admin_post_remove_license_key_' . 'wgm', array( $this, 'remove_license_key' ) );

		// add scheduled event for the key checkup
		add_filter( 'wgm' . '_license_key_checkup', array( $this, 'license_key_checkup' ) );
		if ( ! wp_next_scheduled( 'wgm' . '_license_key_checkup' ) )
			wp_schedule_event( time(), 'daily', 'wgm' . '_license_key_checkup' );

		// Add Filter for the license check ( the cached state of the checkup )
		add_filter( 'wgm' . '_license_check', array( $this, 'license_check' ) );

		// Version Checkup
		if ( self::$is_marketpress == TRUE ) {
			$user_data = get_site_option( 'marketpress_user_data' );
			if ( isset( $user_data[ $this->sanitized_plugin_name ] ) && $user_data[ $this->sanitized_plugin_name ] == 'false' )
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugin_version' ) );
		} else {
			$license_check = apply_filters( 'wgm' . '_license_check', FALSE );
			if ( $license_check )
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugin_version' ) );
		}
		// disable autoupdater on module list -> set dont show to TRUE
		add_filter( 'mlp_dont_show_module_class-Multilingual_Press_Auto_Update', '__return_true' );

	}

	/**
	 * Setting up the key
	 *
	 * @since	0.1
	 * @uses	get_site_option
	 * @return	void
	 */
	public function get_key() {

		// Check if theres a key in the config
		if ( defined( 'MARKETPRESS_KEY' ) && MARKETPRESS_KEY != '' )
			self::$key = MARKETPRESS_KEY;

		// MarketPress Key
		if ( self::$key == '' && get_site_option( 'marketpress_license' ) != '' )
			self::$key = get_site_option( 'marketpress_license' );

		// Check if the plugin is valid
		$user_data = get_site_option( 'marketpress_user_data' );
		if ( isset( $user_data[ $this->sanitized_plugin_name ] ) && $user_data[ $this->sanitized_plugin_name ]->valid == 'false' ) {
			self::$key = '';
		} else if ( isset( $user_data[ $this->sanitized_plugin_name ] ) && $user_data[ $this->sanitized_plugin_name ]->valid == 'true' ) {
			self::$key = '';
			self::$is_marketpress = TRUE;
		}

		// Get all our licenses
		self::$licenses = get_site_option( 'inpsyde_licenses' );
		if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) ) {
			self::$key = self::$licenses[ $this->sanitized_plugin_name . '_license' ];
			self::$is_marketpress = FALSE;
		}
	}

	/**
	 * Checks over the transient-update-check for plugins if new version of
	 * this plugin os available and is it, shows a update-message into
	 * the backend and register the update package in the transient object
	 *
	 * @since	0.1
	 * @param	object $transient
	 * @uses	wp_remote_get, wp_remote_retrieve_body, get_site_option,
	 * 			get_site_transient, set_site_transient
	 * @return	object $transient
	 */
	public function check_plugin_version( $transient ) {

		if ( empty( $transient->checked ) )
			return $transient;

		$response = $this->license_key_checkup();
		if ( $response != 'true' ) {
			if ( isset( $transient->response[ $this->plugin_data->plugin_base_name ] ) )
				unset( $transient->response[ $this->plugin_data->plugin_base_name ] );

			return $transient;
		}

		// Connect to our remote host
		$remote = wp_remote_get( self::$url_update_check );

		// If the remote is not reachable or any other errors occured,
		// we have to break up
		if ( is_wp_error( $remote ) ) {
			if ( isset( $transient->response[ $this->plugin_data->plugin_base_name ] ) )
				unset( $transient->response[ $this->plugin_data->plugin_base_name ] );

			return $transient;
		}

		$response = json_decode( wp_remote_retrieve_body( $remote ) );
		if ( $response->status != 'true' ) {
			if ( isset( $transient->response[ $this->plugin_data->plugin_base_name ] ) )
				unset( $transient->response[ $this->plugin_data->plugin_base_name ] );

			return $transient;
		}

		$version = $response->version;
		$current_version = $this->plugin_data->version;

		// Yup, insert the version
		if ( version_compare( $current_version, $version, '<' ) ) {
			$hashlist	= get_site_transient( 'update_hashlist' );
			$hash		= crc32( __FILE__ . $version );
			$hashlist[]	= $hash;
			set_site_transient( 'update_hashlist' , $hashlist );

			$info				= new stdClass();
			$info->url			= $this->plugin_data->plugin_url;
			$info->slug			= $this->plugin_data->plugin_base_name;
			$info->package		= self::$url_update_package;
			$info->new_version	= $version;

			$transient->response[ $this->plugin_data->plugin_base_name ] = $info;

			return $transient;
		}

		// Always return a transient object
		if ( isset( $transient->response[ $this->plugin_data->plugin_base_name ] ) )
			unset( $transient->response[ $this->plugin_data->plugin_base_name ] );

		return $transient;
	}

	/**
	 * Disables the checkup
	 *
	 * @since	0.1
	 * @param	object $transient
	 * @return	object $transient
	 */
	public function dont_check_plugin_version( $transient ) {

		unset( $transient->response[ $this->plugin_data->plugin_base_name ] );

		return $transient;
	}

	/**
	 * Added the Fields for licensing this after Plugin-Row.
	 *
	 * @since	0.1
	 * @uses	is_network_admin, current_user_can, network_admin_url, get_site_option
	 * @return	bool
	 */
	public function license_row() {

		// Security Check
		if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
			if ( ! current_user_can( 'manage_network_plugins' ) )
				return FALSE;
		} else if ( function_exists( 'current_user_can' ) && ! current_user_can( 'activate_plugins' ) ) {
			return FALSE;
		}

		// Enqueue stylesheet
		wp_enqueue_style( 'wgm-admin-css' );

		// Template vars
		$msg     = '';
		$class   = '';
		$classes = array(
			'success' => 'wgm-mpupdater-success',
			'error'   => 'wgm-mpupdater-error',
			'neutral' => 'wgm-mpupdater-neutral'
		);
		$mp_url  = _x( 'marketpress.de', 'MarketPress URL part, should be .de for German languages', 'WooCommerce-German-Market' );

		// Template logic
		if ( self::$is_marketpress == TRUE && is_admin() && current_user_can( 'manage_options' ) ) {

			// Key not valid for plugin
			$user_data = get_site_option( 'marketpress_user_data' );
			if ( isset( $user_data[ $this->sanitized_plugin_name ] ) && $user_data[ $this->sanitized_plugin_name ] == 'false' ) {

				$class = $classes[ 'error' ];
				$msg  = sprintf( __( '<strong>Hoppla!</strong> Der Lizenzschlüssel, den du eingegeben hast, scheint nicht gültig zu sein. Du kannst deinen Lizenzschlüssel stets auf deiner Downloads-Seite bei %s nachsehen. Automatische Updates sind nicht verfügbar, bis du einen gültigen Lizenzschlüssel eingegeben hast.', 'WooCommerce-German-Market' ),
					'<a href="http://' . $mp_url . '/wp-login.php">MarketPress</a>'
				);

			} else {

				$class = $classes[ 'success' ];
				$msg  = sprintf( __( '<strong>Alles ist gut.</strong> Du benutzt einen gültigen Lizenzschlüssel von %s für dieses Plugin. Wenn du einen neuen Lizenzschlüssel eingeben möchtest, überschreibe den jetzigen einfach und speichere.', 'WooCommerce-German-Market' ),
					'<a href="http://' . $mp_url . '/" target="_blank">MarketPress</a>'
				);

			}

		} else {

			$license_check = apply_filters( 'wgm' . '_license_check', 'false' );

			if ( self::$key == '' ) {

				$atts = '';
				$msg  = sprintf( __( 'Gib einen gültigen Lizenzschlüssel von %s ein.', 'WooCommerce-German-Market' ),
					'<a href="http://' . $mp_url . '/" target="_blank">MarketPress</a>'
				);
				$msg .= sprintf( ' (<a href="http://%1$s/wp-login.php" target="_blank">%2$s</a>)',
					$mp_url,
					__( 'Hilfe! Ich muss meinen Lizenzschlüssel nachsehen.', 'WooCommerce-German-Market' )
				);

			} elseif ( $license_check === FALSE ) {

				$class = $classes[ 'error' ];
				$msg  = __( '<strong>Hoppla!</strong> Der Lizenzschlüssel, den du eingegeben hast, scheint nicht gültig zu sein. Automatische Updates sind nicht verfügbar, bis du einen gültigen Lizenzschlüssel eingegeben hast.', 'WooCommerce-German-Market' );

			} else {

				$class = $classes[ 'success' ];
				$msg  = sprintf( __( '<strong>Alles ist gut.</strong> Du benutzt einen gültigen Lizenzschlüssel von %s für dieses Plugin. Wenn du einen neuen Lizenzschlüssel eingeben möchtest, überschreibe den jetzigen einfach und speichere. Oder <a href="%s">lösche ihn einfach</a>.', 'WooCommerce-German-Market' ),
					'<a href="http://' . $mp_url . '/"  target="_blank">MarketPress</a>',
					admin_url( 'admin-post.php?action=remove_license_key_' . 'wgm' )
				);

			}

		}

		$msg .= sprintf( '<p><label for="license_key_%1$s">%2$s: <input type="text" name="license_key_%1$s" id="license_key_%1$s" value="%3$s" class="regular-text code" /></label> <input type="submit" name="submit_wgm_key" value="%4$s" class="button-primary action" /></p>',
			$this->sanitized_plugin_name,
			__( 'Lizenzschlüssel', 'WooCommerce-German-Market' ),
			self::$is_marketpress == FALSE ? self::$key : '',
			esc_attr__( 'Aktivieren', 'WooCommerce-German-Market' )
		);

		// Print
		?>
		<tr class="plugin-update-tr">
			<td colspan="4"><?php printf( '<div class="update-message %1$s">%2$s</div>', $class, $msg ); ?></td>
		</tr>
		<?php

		// Return a value for tests
		return TRUE;
	}

	/**
	 * Updates and inserts the license
	 *
	 * @since	0.1
	 * @uses	wp_safe_redirect, admin_url
	 * @return	boolean
	 */
	public function update_license() {

		if ( $_REQUEST[ 'key' ] == '' )
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_wrong_key' ) );

		$response = $this->license_key_checkup( $_REQUEST[ 'key' ] );
		if ( $response == 'true' )
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_plugin_activated' ) );
		else if ( $response == 'wrongkey' )
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_wrong_key' ) );
		else if ( $response == 'wronglicense' )
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_wrong_license' ) );
		else if ( $response == 'wrongurl' )
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_wrong_url' ) );
		else
			wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=marketpress_wrong_anything' ) );

		exit;
	}

	/**
	 * Check the license-key and caches the returned value
	 * in an option
	 *
	 * @since	0.1
	 * @uses	wp_remote_retrieve_body, wp_remote_get, update_site_option, is_wp_error,
	 * 			delete_site_option
	 * @return	boolean
	 */
	public function license_key_checkup( $key = '' ) {

		// Request Key
		if ( $key != '' )
			self::$key = $key;

		// Check if there's a key
		if ( self::$key == '' ) {
			// Deactivate Plugin first
			update_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name, 'false' );
			return 'wrongkey';
		}

		// Update URL Key Checker
		self::$url_key_check = 'http://marketpress.com/mp-key/' . self::$key . '/' . $this->sanitized_plugin_name . '/' . sanitize_title_with_dashes( network_site_url() );

		// Connect to our remote host
		$remote = wp_remote_get( self::$url_key_check );

		// If the remote is not reachable or any other errors occured,
		// we believe in the goodwill of the user and return true
		if ( is_wp_error( $remote ) ) {
			self::$licenses[ $this->sanitized_plugin_name . '_license' ] = self::$key;
			update_site_option( 'inpsyde_licenses' , self::$licenses );
			update_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name, 'true' );
			return 'true';
		}

		// Okay, get the response
		$response = json_decode( wp_remote_retrieve_body( $remote ) );
		if ( ! isset( $response ) || $response == '' ) {
			// Deactivate Plugin first
			delete_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name );

			if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) ) {
				unset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] );
				update_site_option( 'inpsyde_licenses' , self::$licenses );
			}

			return 'wronglicense';
		}

		// Okay, get the response
		$response = json_decode( wp_remote_retrieve_body( $remote ) );

		if ( $response->status == 'noproducts' ) {
			// Deactivate Plugin first
			delete_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name );

			if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) ) {
				unset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] );
				update_site_option( 'inpsyde_licenses' , self::$licenses );
			}

			return 'wronglicense';
		}

		if ( $response->status == 'wronglicense' ) {
			// Deactivate Plugin first
			delete_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name );

			if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) ) {
				unset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] );
				update_site_option( 'inpsyde_licenses' , self::$licenses );
			}

			return 'wronglicense';
		}

		if ( $response->status == 'urllimit' ) {
			// Deactivate Plugin first
			delete_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name );

			if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) ) {
				unset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] );
				update_site_option( 'inpsyde_licenses' , self::$licenses );
			}

			return 'wrongurl';
		}

		if ( $response->status == 'true' ) {

			// Activate Plugin first
			self::$licenses[ $this->sanitized_plugin_name . '_license' ] = self::$key;
			update_site_option( 'inpsyde_licenses' , self::$licenses );
			update_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name, 'true' );

			return 'true';
		}

		exit;
	}

	/**
	 * Checks the cached state of the license checkup
	 *
	 * @since	0.1
	 * @uses	get_site_option
	 * @return	boolean
	 */
	public function license_check() {

		return get_site_option( 'inpsyde_license_status_' . $this->sanitized_plugin_name );
	}

	/**
	 * Checks if the plugin "MarketPress Dashboard" exists.
	 * If not, present a link to download it
	 *
	 * @since	0.1
	 * @uses	get_plugins, sanitize_title_with_dashes, __
	 * @return	boolean
	 */
	public function marketpress_dashboard_notice() {

		if ( isset( $_GET[ 'message_mp_vat' ] ) ) {

			// Template vars
			$class  = '';
			$msg    = '';
			$mp_url = _x( 'marketpress.de', 'MarketPress URL part, should be .de for German languages', 'WooCommerce-German-Market' );

			// Template logic
			switch ( $_GET[ 'message_mp_vat' ] ) {
				case 'license_deleted':

					$class = 'updated';
					$msg   = __( 'Lizenzschlüssel wurde gelöscht.', 'WooCommerce-German-Market' );
					break;

				case 'marketpress_plugin_activated':

					$class = 'updated';
					$msg  .= __( 'Lizenz erfolgreich aktiviert.', 'WooCommerce-German-Market' );
					break;

				case 'marketpress_wrong_key':

					$class = 'error';
					$msg   = sprintf( '<strong>%s</strong> ', __( 'Lizenz kann nicht aktiviert werden.', 'WooCommerce-German-Market' ) );
					$msg  .= __( 'Der Lizenzschlüssel, den du eingegeben hast, ist nicht korrekt.', 'WooCommerce-German-Market' );
					break;

				case 'marketpress_wrong_url':

					$class = 'error';
					$msg   = sprintf( '<strong>%s</strong> ', __( 'Lizenz kann nicht aktiviert werden.', 'WooCommerce-German-Market' ) );
					$msg  .= sprintf( __( 'Du hast das URL-Limit für deinen Lizenzschlüssel erreicht. Bitte aktualisiere deine Lizenz bei %s.', 'WooCommerce-German-Market' ),
						'<a href="http://' . $mp_url . '/" target="_blank">MarketPress</a>'
					);
					break;

				case 'marketpress_wrong_anything':

					$class = 'error';
					$msg   = sprintf( '<strong>%s</strong> ', __( 'Lizenz kann nicht aktiviert werden.', 'WooCommerce-German-Market' ) );
					$msg  .= sprintf( __( 'Etwas ist schief gelaufen. Bitte versuche es später noch einmal und kontaktiere den Support bei %s.', 'WooCommerce-German-Market' ),
						'<a href="http://' . $mp_url . '/" target="_blank">MarketPress</a>'
					);
					break;

				case 'marketpress_wrong_license':

					$class = 'error';
					$msg   = sprintf( '<strong>%s</strong> ', __( 'Lizenz kann nicht aktiviert werden.', 'WooCommerce-German-Market' ) );
					$msg  .= sprintf( __( 'Deine Lizenz scheint nicht gültig zu sein. Bitte aktualisiere deine Lizenz bei %s.', 'WooCommerce-German-Market' ),
						'<a href="http://' . $mp_url . '/" target="_blank">MarketPress</a>'
					);
					break;
			}

			// Print message
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $msg );
		}
	}

	/**
	 * Removes the plugins key from the licenses
	 *
	 * @since	0.1
	 * @uses	update_site_option, wp_safe_redirect, admin_url
	 * @return	void
	 */
	public function remove_license_key() {

		if ( isset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] ) )
			unset( self::$licenses[ $this->sanitized_plugin_name . '_license' ] );

		update_site_option( 'inpsyde_licenses' , self::$licenses );

		self::$key = '';

		// Renew License Check
		$this->license_key_checkup();

		// Redirect
		wp_safe_redirect( network_admin_url( 'plugins.php?message_mp_vat=license_deleted' ) );
	}

}


global $pagenow;

if ( ! class_exists( 'AU_Install_Skin' ) && $pagenow != 'update-core.php' ) {
	// Need this class :(
	if ( ! class_exists( 'WP_Upgrader_Skin' ) )
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	class AU_Install_Skin extends WP_Upgrader_Skin {

		/**
		 * Enforce the Feedback to nothing
		 * @see WP_Upgrader_Skin::feedback()
		 */
		public function feedback( $string ) {

			return NULL;
		}
	}
}