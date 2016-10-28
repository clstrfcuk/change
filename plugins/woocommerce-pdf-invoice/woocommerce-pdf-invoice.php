<?php

/**
 * Plugin Name: WooCommerce PDF Invoice
 * Plugin URI: http://www.rightpress.net/woocommerce-pdf-invoice
 * Description: Generate perfect PDF invoices for your WooCommerce orders.
 * Version: 2.1.2
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 * Requires at least: 3.5
 * Tested up to: 3.6
 *
 * Text Domain: woo_pdf
 * Domain Path: /languages
 * 
 * @package WooCommerce_PDF_Invoice
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('WOOPDF_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('WOOPDF_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('WOOPDF_VERSION', '2.1.2');

if (!class_exists('WooPDF')) {

    /**
     * Main plugin class
     * 
     * @class WooPDF
     * @package WooCommerce_PDF_Invoice
     * @author RightPress
     */
    class WooPDF
    {

        /**
         * Class constructor
         * 
         * @access public
         * @return void
         */
        public function __construct()
        {
            $this->date_from = null;
            $this->date_to = null;
            $this->temp_order_id = null;
            $this->batch_invoice_id_counter = null;

            // Load translation
            load_plugin_textdomain('woo_pdf', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            foreach (glob(WOOPDF_PLUGIN_PATH . '/includes/*.inc.php') as $filename)
            {
                require $filename;
            }

            // Load plugin configuration
            $this->get_config();

            // Load options
            $this->opt = $this->get_options();

            // Additional plugin page links
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugin_settings_link'));

            // Add settings page
            if (is_admin()) {
                add_action('admin_menu', array($this, 'add_admin_menu'));
                add_action('admin_init', array($this, 'admin_construct'));
            }

            // Load resources conditionally
            if (preg_match('/page=woo-pdf/i', $_SERVER['QUERY_STRING'])) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            }

            // Hook into WooCommerce / WordPress
            if ($this->opt['woo_pdf_enabled'] || $this->opt['woo_pdf_proforma_enabled']) {
                add_filter('woocommerce_email_attachments', array($this, 'send_by_email'), 10, 3);
                add_action('woocommerce_order_status_completed_notification', array($this, 'process_completed'), 1);
                add_action('template_redirect', array($this, 'hide_attachment_pages'), 1);
                add_action('woocommerce_admin_order_actions', array($this, 'admin_invoice_link'));
                add_action('woocommerce_order_details_after_order_table', array($this, 'user_invoice_link'));
                add_filter('woocommerce_my_account_my_orders_actions', array($this, 'orders_actions'), 10, 2);
                add_action('add_meta_boxes', array($this, 'add_woo_pdf_metabox'));
            }

            // Intercept download calls
            if (isset($_GET['wpd_invoice'])) {
                add_action('init', array($this, 'push_invoice'));
            }
            if (isset($_GET['wpd_proforma'])) {
                add_action('init', array($this, 'push_proforma'));
            }
            if (isset($_GET['wpd_delete_invoice'])) {
                add_action('init', array($this, 'delete_invoice'));
            }
            if (isset($_GET['wpd_generate_invoice'])) {
                add_action('init', array($this, 'generate_invoice'));
            }
            if (isset($_GET['woo_pdf_download_from']) && isset($_GET['woo_pdf_download_to'])) {
                add_action('init', array($this, 'batch_download'));
            }
        }

        /**
         * Loads/sets configuration values from structure file and database
         * 
         * @access public
         * @return void
         */
        public function get_config()
        {
            // Settings tree
            $this->settings = woo_pdf_plugin_settings();

            // Load some data from config
            $this->hints = $this->options('hint');
            $this->validation = $this->options('validation', true);
            $this->titles = $this->options('title');
            $this->options = $this->options('values');
            $this->section_info = $this->get_section_info();
        }

        /**
         * Get settings options: default, hint, validation, values
         * 
         * @access public
         * @param string $name
         * @param bool $split_by_page
         * @return array
         */
        public function options($name, $split_by_page = false)
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page => $page_value) {
                $page_options = array();

                foreach ($page_value['children'] as $section => $section_value) {
                    foreach ($section_value['children'] as $field => $field_value) {
                        if (isset($field_value[$name])) {
                            $page_options['woo_pdf_' . $field] = $field_value[$name];
                        }
                    }
                }

                $results[preg_replace('/_/', '-', $page)] = $page_options;
            }

            $final_results = array();

            if (!$split_by_page) {
                foreach ($results as $value) {
                    $final_results = array_merge($final_results, $value);
                }
            }
            else {
                $final_results = $results;
            }

            return $final_results;
        }

        /**
         * Get array of section info strings
         * 
         * @access public
         * @return array
         */
        public function get_section_info()
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page_value) {
                foreach ($page_value['children'] as $section => $section_value) {
                    if (isset($section_value['info'])) {
                        $results[$section] = $section_value['info'];
                    }
                }
            }

            return $results;
        }

        /*
         * Get plugin options set by user
         * 
         * @access public
         * @return array
         */
        public function get_options()
        {
            $saved_options = get_option('woo_pdf_options', $this->options('default'));

            if (is_array($saved_options)) {
                return array_merge($this->options('default'), $saved_options);
            }
            else {
                return $this->options('default');
            }
        }

        /*
         * Update options
         * 
         * @access public
         * @return bool
         */
        public function update_options($args = array())
        {
            return update_option('woo_pdf_options', array_merge($this->get_options(), $args));
        }

        /**
         * Add link to admin page under Woocommerce menu
         * 
         * @access public
         * @return void
         */
        public function add_admin_menu()
        {            
            global $current_user;

            get_currentuserinfo();
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            if (!in_array($user_role, array('administrator', 'shop_manager'))) {
                return;
            }

            global $submenu;

            if (isset($submenu['woocommerce'])) {
                add_submenu_page(
                    'woocommerce',
                    __('WooCommerce PDF Invoices', 'woo_pdf'),
                    __('PDF Invoices', 'woo_pdf'),
                    'edit_posts',
                    'woo-pdf',
                    array($this, 'set_up_admin_page')
                );
            }
        }

        /*
         * Set up admin page
         * 
         * @access public
         * @return void
         */
        public function set_up_admin_page()
        {
            // Check for general warnings
            if (!$this->image_library_exists()) {
                add_settings_error(
                    'woo_pdf',
                    'general',
                    __('Image processing library not found on your server.<br>You must have either GD or Imagick extension enabled on your server for this module to work correctly.', 'woo_pdf')
                );
            }

            // Print notices
            settings_errors('woo_pdf');

            $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general_settings';
            $current_tab = isset($this->settings[$current_tab]) ? $current_tab : 'general_settings';

            // Print page tabs
            $this->render_tabs($current_tab);

            // Print page content
            $this->render_page($current_tab);

        }

        /**
         * Admin interface constructor
         * 
         * @access public
         * @return void
         */
        public function admin_construct()
        {
            global $current_user;

            get_currentuserinfo();
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            if (!in_array($user_role, array('administrator', 'shop_manager'))) {
                return;
            }

            // Iterate pages
            foreach ($this->settings as $page => $page_value) {

                register_setting(
                    'woo_pdf_opt_group_' . $page,               // Option group
                    'woo_pdf_options',                          // Option name
                    array($this, 'options_validate')            // Sanitize
                );

                // Iterate sections
                foreach ($page_value['children'] as $section => $section_value) {

                    add_settings_section(
                        $section,
                        $section_value['title'],
                        array($this, 'render_section_info'),
                        'woo-pdf-admin-' . str_replace('_', '-', $page)
                    );

                    // Iterate fields
                    foreach ($section_value['children'] as $field => $field_value) {

                        add_settings_field(
                            'woo_pdf_' . $field,                                     // ID
                            $field_value['title'],                                      // Title 
                            array($this, 'render_options_' . $field_value['type']),     // Callback
                            'woo-pdf-admin-' . str_replace('_', '-', $page),            // Page
                            $section,                                                   // Section
                            array(                                                      // Arguments
                                'name' => 'woo_pdf_' . $field,
                                'options' => $this->opt,
                            )
                        );

                    }
                }
            }
        }

        /**
         * Render admin page navigation tabs
         * 
         * @access public
         * @param string $current_tab
         * @return void
         */
        public function render_tabs($current_tab = 'general-settings')
        {
            $current_tab = preg_replace('/-/', '_', $current_tab);

            // Output admin page tab navigation
            echo '<div class="woo_pdf_tabs_container">';
            echo '<div id="icon-woo-pdf" class="icon32 icon32-woo-pdf"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($this->settings as $page => $page_value) {
                $class = ($page == $current_tab) ? ' nav-tab-active' : '';
                echo '<a class="nav-tab'.$class.'" href="?page=woo-pdf&tab='.$page.'">'.((isset($page_value['icon']) && !empty($page_value['icon'])) ? $page_value['icon'] . '&nbsp;' : '').$page_value['title'].'</a>';
            }
            echo '</h2>';
            echo '</div>';
        }

        /**
         * Render settings page
         * 
         * @access public
         * @param string $page
         * @return void
         */
        public function render_page($page){

            $page_name = preg_replace('/_/', '-', $page);

            // Is this a batch download page?
            if ($page == 'batch_download') {
                ?>
                    <div class="wrap woocommerce woo-pdf">
                    <div class="woo_pdf_container">
                        <h3><?php _e('Batch Invoice Download', 'woo_pdf'); ?></h3>

                        <form class="woo_pdf_batch_download">
                            <table class="form-table">
                                <tbody>
                                    <tr valign="tr">
                                        <th scope="row"><?php _e('Date from', 'woo_pdf'); ?></th>
                                        <td>
                                            <input type="text" id="woo_pdf_download_from" name="woo_pdf_download_from" value="">
                                        </td>
                                    </tr>
                                    <tr valign="tr">
                                        <th scope="row"><?php _e('Date to', 'woo_pdf'); ?></th>
                                        <td>
                                            <input type="text" id="woo_pdf_download_to" name="woo_pdf_download_to" value="">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="woo_pdf_section_info"><?php _e('Please note that only regular invoices are available for batch download.', 'woo_pdf'); ?></div>

                            <p class="submit">
                                <button type="button" name="submit" id="woo_pdf_batch_download" class="button button-primary"><?php _e('Download Invoices', 'woo_pdf'); ?></button>
                            </p>
                        </form>

                    </div>
                    </div>
                <?php
            }
            // Is this a standard settings page?
            else {
                ?>

                    <div class="wrap woocommerce woo-pdf">
                    <div class="woo_pdf_container">
                        <form method="post" action="options.php" enctype="multipart/form-data">
                            <input type="hidden" name="current_tab" value="<?php echo $page_name; ?>" />

                            <?php if ($page == 'content_blocks'): ?>
                                <div class="woo_pdf_content_tab_description">
                                    <h3><?php _e('Macros', 'woo_pdf'); ?></h3>
                                    <p class="woo_pdf_about"><?php _e('Footer and all custom blocks support the following macros in both title and content:', 'woo_pdf'); ?></p>
                                        <div>
                                            <div style="float: left;">
                                                <ul class="woo_pdf_macros">
                                                    <li><strong>{{order_id}}</strong></li>
                                                    <li><strong>{{order_date}}</strong></li>
                                                    <li><strong>{{customer_id}}</strong></li>
                                                    <li><strong>{{customer_note}}</strong></li>
                                                </ul>
                                            </div>
                                            <div style="float: left;">
                                                <ul class="woo_pdf_macros">
                                                    <li><strong>{{billing_email}}</strong></li>
                                                    <li><strong>{{billing_phone}}</strong></li>
                                                    <li><strong>{{payment_method}}</strong></li>
                                                    <li><strong>{{shipping_method}}</strong></li>
                                                </ul>
                                            </div>
                                            <div style="float: left;">
                                                <ul class="woo_pdf_macros">
                                                    <li><strong>{{shipping_first_name}}</strong></li>
                                                    <li><strong>{{shipping_last_name}}</strong></li>
                                                    <li><strong>{{shipping_company}}</strong></li>
                                                    <li><strong>{{shipping_address_1}}</strong></li>
                                                </ul>
                                            </div>
                                            <div style="float: left;">
                                                <ul class="woo_pdf_macros">
                                                    <li><strong>{{shipping_address_2}}</strong></li>
                                                    <li><strong>{{shipping_city}}</strong></li>
                                                    <li><strong>{{shipping_postcode}}</strong></li>
                                                    <li><strong>{{shipping_country}}</strong></li>
                                                </ul>
                                            </div>
                                            <div style="float: left;">
                                                <ul class="woo_pdf_macros">
                                                    <li><strong>{{shipping_state}}</strong></li>
                                                </ul>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    <p class="woo_pdf_about_inverse"><?php _e('You can insert any other order field (including custom fields) in the same way, e.g.', 'woo_pdf'); ?> <strong>{{my_custom_field_key}}</strong>.</p>

                                </div>
                            <?php endif; ?>

                            <?php if ($page == 'general_settings'): ?>
                                <input type="hidden" name="woo_pdf_options[woo_pdf_next_invoice_number_original_value]" value="<?php echo $this->opt['woo_pdf_next_invoice_number']; ?>">
                            <?php endif; ?>

                            <?php
                                settings_fields('woo_pdf_opt_group_' . $page);
                                do_settings_sections('woo-pdf-admin-' . $page_name);

                                echo '<div></div>';

                                submit_button();
                            ?>

                        </form>
                    </div>
                    </div>
                <?php
            }

            // Get uploads url and path
            $uploads_dir = wp_upload_dir();

            // Pass variables to JavaScript
            ?>
                <script language="JavaScript">
                    var woo_pdf_hints = <?php echo json_encode($this->hints); ?>;
                    var woo_pdf_home_url = '<?php echo home_url(); ?>';
                    var woo_pdf_url_fopen_allowed = '<?php echo (ini_get('allow_url_fopen') ? '1' : '0'); ?>';
                    var woo_pdf_uploads_url = '<?php echo $uploads_dir['baseurl']; ?>';
                    var woo_pdf_uploads_path = '<?php echo $uploads_dir['basedir']; ?>';
                </script>
            <?php
        }

        /**
         * Render section info
         * 
         * @access public
         * @param array $section
         * @return void
         */
        public function render_section_info($section)
        {
            if (isset($this->section_info[$section['id']])) {
                echo $this->section_info[$section['id']];
            }
        }

        /*
         * Render a text field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_text($args = array())
        {
            printf(
                '<input type="text" id="%s" name="woo_pdf_options[%s]" value="%s" class="woo-pdf-field-width" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a text area
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_textarea($args = array())
        {
            printf(
                '<textarea id="%s" name="woo_pdf_options[%s]" class="woo_pdf_textarea">%s</textarea>',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a checkbox
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_checkbox($args = array())
        {
            printf(
                '<input type="checkbox" id="%s" name="woo_pdf_options[%s]" value="1" %s />',
                $args['name'],
                $args['name'],
                checked($args['options'][$args['name']], true, false)
            );
        }

        /*
         * Render a dropdown
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_dropdown($args = array())
        {
            printf(
                '<select id="%s" name="woo_pdf_options[%s]" class="woo-pdf-field-width">',
                $args['name'],
                $args['name']
            );
            foreach ($this->options[$args['name']] as $key => $name) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    $key,
                    selected($key, $args['options'][$args['name']], false),
                    $name
                );
            }
            echo '</select>';
        }

        /**
         * Render select from media library field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_media($args = array())
        {
            // Render text input field
            printf(
                '<input id="%s" type="text" name="woo_pdf_options[%s]" value="%s" class="woo-pdf-field-width" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );

            // Render "Open Library" button 
            printf(
                '<input id="%s_upload_button" type="button" value="%s" />',
                $args['name'],
                __('Open Library', 'woo_pdf')
            );
        }

        /**
         * Validate admin form input
         * 
         * @access public
         * @param array $input
         * @return array
         */
        public function options_validate($input)
        {
            $current_tab = isset($_POST['current_tab']) ? $_POST['current_tab'] : 'general-settings';
            $output = $this->get_options();

            $errors = array();

            // Avoid accidental next invoice number overwrite


            // Iterate over fields and validate/sanitize input
            foreach ($this->validation[$current_tab] as $field => $rule) {

                // Different routines for different field types
                switch($rule['rule']) {

                    // Validate numbers
                    case 'number':

                        // Exception - make sure we do not accidentally overwrite next invoice number
                        $next_invoice_number_error = false;

                        if ($field == 'woo_pdf_next_invoice_number') {
                            if ($input['woo_pdf_numbering_method'] == '0' && isset($input['woo_pdf_next_invoice_number_original_value']) && ($input['woo_pdf_next_invoice_number_original_value'] != $this->opt['woo_pdf_next_invoice_number'])) {
                                array_push($errors, array('setting' => $field, 'code' => 'number'));
                                $next_invoice_number_error = true;
                            }
                        }

                        if (!$next_invoice_number_error) {
                            if (is_numeric($input[$field]) || ($input[$field] == '' && $rule['empty'] == true)) {
                                $output[$field] = $input[$field];
                            }
                            else {
                                array_push($errors, array('setting' => $field, 'code' => 'number'));
                            }
                        }

                        break;

                    // Validate boolean values (actually 1 and 0)
                    case 'bool':
                        $input[$field] = (!isset($input[$field]) || $input[$field] == '') ? '0' : $input[$field];
                        if (in_array($input[$field], array('0', '1')) || ($input[$field] == '' && $rule['empty'] == true)) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'bool'));
                        }
                        break;

                    // Validate predefined options
                    case 'option':
                        if (isset($input[$field]) && (isset($this->options[$field][$input[$field]]) || ($input[$field] == '' && $rule['empty'] == true))) {
                            $output[$field] = $input[$field];
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'option'));
                        }
                        break;

                    // Validate emails
                    case 'email':
                        if (isset($input[$field]) && (filter_var(trim($field), FILTER_VALIDATE_EMAIL) || ($input[$field] == '' && $rule['empty'] == true))) {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'email'));
                        }
                        break;

                    // Validate URLs
                    case 'url':
                        // FILTER_VALIDATE_URL for filter_var() does not work as expected
                        if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
                            array_push($errors, array('setting' => $field, 'code' => 'url'));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        break;

                    // Default validation rule (text fields etc)
                    default:
                        if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
                            array_push($errors, array('setting' => $field, 'code' => 'string'));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        break;
                }
            }

            // Display settings updated message
            add_settings_error(
                'woo_pdf',
                'woo_pdf_' . 'settings_updated',
                __('Your settings have been saved.', 'woo_pdf'),
                'updated'
            );

            // Display errors
            foreach ($errors as $error) {
                $reverted = __('Reverted to a previous value.', 'woo_pdf');

                $messages = array(
                    'number' => __('must be numeric', 'woo_pdf') . '. ' . $reverted,
                    'bool' => __('must be either 0 or 1', 'woo_pdf') . '. ' . $reverted,
                    'option' => __('is not allowed', 'woo_pdf') . '. ' . $reverted,
                    'email' => __('is not a valid email address', 'woo_pdf') . '. ' . $reverted,
                    'url' => __('is not a valid URL', 'woo_pdf') . '. ' . $reverted,
                    'string' => __('is not a valid text string', 'woo_pdf') . '. ' . $reverted,
                );

                add_settings_error(
                    'woo_pdf',
                    $error['code'],
                    __('Value of', 'woo_pdf') . ' "' . $this->titles[$error['setting']] . '" ' . $messages[$error['code']]
                );
            }

            return $output;
        }

        /**
         * Generate regular invoice
         * 
         * @access public
         * @param object $order
         * @return void
         */
        public function make_invoice($order)
        {
            // Is invoicing enabled?
            if (!$this->opt['woo_pdf_enabled']) {
                return;
            }

            // Is image processing extension enabled? (required by tcpdf)
            if (!$this->image_library_exists()) {
                return;
            }

            // Load PDF class
            if (!class_exists('TCPDF')) {
                require WOOPDF_PLUGIN_PATH.'/includes/tcpdf/tcpdf.php';
            }
            if (!class_exists('WooPdfInvoice')) {
                require WOOPDF_PLUGIN_PATH.'/includes/woo-pdf-invoice.class.php';
            }

            // Get invoice number
            if ($this->opt['woo_pdf_numbering_method'] == 0) {
                $next_invoice_number = $this->get_next_invoice_number();
            }
            else if ($this->opt['woo_pdf_numbering_method'] == 1) {
                $next_invoice_number = $order->get_order_number();
                $next_invoice_number = preg_replace('/[^0-9.]+/', '', $next_invoice_number);
            }
            else {
                $next_invoice_number = $order->get_order_number();
            }

            // Get random code for file name
            $random_name = substr(md5(time()), 0, 5).substr($next_invoice_number, -3, 3);
            $file_name = $random_name.'.pdf';

            // Get prefix and suffix
            if ($this->opt['woo_pdf_numbering_method'] == 2) {
                $invoice_number_prefix = '';
                $invoice_number_suffix = '';
            }
            else {
                $invoice_number_prefix = $this->replace_prefix_suffix_macros($this->opt['woo_pdf_number_prefix'], $order, 'prefix');
                $invoice_number_suffix = $this->replace_prefix_suffix_macros($this->opt['woo_pdf_number_suffix'], $order, 'suffix');
            }

            // Initialize tcpdf
            $info = array(
                'id' => $next_invoice_number,
                'code' => $random_name,
                'prefix' => $invoice_number_prefix,
                'suffix' => $invoice_number_suffix,
            );
            $pdf = new WooPdfInvoice(array('order' => $order, 'options' => $this->opt, 'info' => $info, 'type' => 'invoice'), 'P', 'pt', 'A4');
            $pdf->CreateInvoice();

            // Set up file directory
            $upload_dir = wp_upload_dir();
            $location = $upload_dir['basedir'] . '/' . 'woocommerce_pdf_invoices';
            if (!file_exists($location)) {
                mkdir($location, 0755, true);
            }

            // Save file to selected directory
            $pdf->Output($location . '/' . $file_name, 'F');

            // From here on we don't need hash tag or other special characters before invoice number
            $next_invoice_number = preg_replace('/[^0-9.]+/', '', $next_invoice_number);

            // Save invoice as order attachment
            $attachment = array(
                'post_title' => __('Invoice #', 'woo_pdf') . $next_invoice_number,
                'post_content' => '',
                'post_status' => 'draft',
                'post_mime_type' => 'application/pdf'
            );
            $attach_id = wp_insert_attachment($attachment, $location . '/' . $file_name, $order->id);

            if (!function_exists('wp_generate_attachment_metadata')) {
                require(ABSPATH . 'wp-admin/includes/image.php');
            }

            $attach_data = wp_generate_attachment_metadata($attach_id, $location . '/' . $file_name);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Push invoice number and random name to order meta
            add_post_meta($order->id, 'woo_pdf_invoice_id', $next_invoice_number, true);
            add_post_meta($order->id, 'woo_pdf_invoice_prefix', $invoice_number_prefix, true);
            add_post_meta($order->id, 'woo_pdf_invoice_suffix', $invoice_number_suffix, true);
            add_post_meta($order->id, 'woo_pdf_invoice_code', $random_name, true);
        }

        /**
         * Get invoice prefix, suffix, ID and code name
         * 
         * @access public
         * @param string $order_id
         * @return array
         */
        public function get_invoice($order_id)
        {
            $attachments = get_children(array(
                'post_parent' => $order_id,
                'post_type' => 'attachment',
                'post_mime_type' => 'application/pdf',
                'numberposts' => 1
            ));

            // Get invoice data from post meta
            $id = get_post_meta($order_id, 'woo_pdf_invoice_id', true);
            $prefix = get_post_meta($order_id, 'woo_pdf_invoice_prefix', true);
            $suffix = get_post_meta($order_id, 'woo_pdf_invoice_suffix', true);
            $code = get_post_meta($order_id, 'woo_pdf_invoice_code', true);

            // Return false if no invoice data found
            if (empty($attachments) || empty($id) || empty($code)) {
                return false;
            }

            // Otherwise, return invoice data
            return array(
                'id' => $id,
                'prefix' => $prefix,
                'suffix' => $suffix,
                'code' => $code
            );
        }

        /**
         * Process order status change
         * 
         * @access public
         * @param string $order_id
         * @return void
         */
        public function process_completed($order_id)
        {
            // Is invoicing enabled?
            if (!$this->opt['woo_pdf_enabled']) {
                return;
            }

            // Load order
            $order = new WC_Order($order_id);
            if (!$order) {
                return;
            }

            // Check maybe we already have invoice for this order
            $invoice_id = get_post_meta($order->id, 'woo_pdf_invoice_id', true);
            if (!empty($invoice_id)) {
                return;
            }

            // Allow developers to cancel generating invoices
            if (!apply_filters('woo_pdf_generate_regular_invoice', true, $order)) {
                return;
            }

            // If not - create a new one
            $this->make_invoice($order);
        }

        /**
         * Send invoice by email
         * 
         * @access public
         * @param string/array $attachments
         * @return string/array
         */
        public function send_by_email($attachments, $email_type = null, $order = null)
        {
            // Check if required properties were passed from WooCommerce
            if (!isset($email_type) || !isset($order->id)) {
                return $attachments;
            }

            // Allow developers to cancel attaching invoices (e.g. to only send invoices with certain payment methods)
            if (!apply_filters('woo_pdf_send_by_email', true, $order, $email_type, $attachments)) {
                return $attachments;
            }

            // Is this manual customer invoice email?
            if ($email_type == 'customer_invoice') {

                // Check if we already have regular invoice for this order
                $invoice_id = get_post_meta($order->id, 'woo_pdf_invoice_id', true);

                // We do not have invoice - send proforma
                if (empty($invoice_id)) {
                    if ($this->opt['woo_pdf_proforma_enabled']) {
                        $manual_customer_processing_order = true;
                    }
                }
                // Send regular
                else {
                    $manual_customer_completed_order = true;
                }
            }

            // Send to admin?
            if ($email_type == 'new_order' && $this->opt['woo_pdf_attach_to_new_order']) {
                $admin_new_order_email = true;
            }

            // Attach regular invoice
            if ($this->opt['woo_pdf_enabled'] && ($email_type == 'customer_completed_order' || isset($manual_customer_completed_order))) {

                // Check if "Send by email" is enabled
                if (!$this->opt['woo_pdf_send_email'] && !isset($manual_customer_completed_order)) {
                    return $attachments;
                }

                // Get invoice details
                $invoice = $this->get_invoice($order->id);

                // Get invoice path
                $upload_dir = wp_upload_dir();
                $location = $upload_dir['basedir'] . '/' . 'woocommerce_pdf_invoices';
                $invoice_path = $location . '/' . $invoice['code'] . '.pdf';

                $original_file = file_get_contents($invoice_path);

                // Use our own /tmp directory to store a copy (to avoid open_basedir / safe_mode errors)
                $temp_location = $location . '/' . 'tmp';
                if (!file_exists($temp_location)) {
                    mkdir($temp_location, 0755, true);
                }

                // Create temporary file with human-readable file name
                $file_name = _x($this->opt['woo_pdf_title_filename_prefix'], 'file name prefix', 'woo_pdf') . ($invoice['prefix'] != '' ? $invoice['prefix'] . '_' : '') . $invoice['id'] . ($invoice['suffix'] != '' ? '_' . $invoice['suffix'] : '') . '.pdf';
                $temp_file = $temp_location . '/' . $file_name;

                // Push to attachments
                if (file_put_contents($temp_file, $original_file)) {
                    if (gettype($attachments) == 'string') {
                        if ($attachments == '') {
                            $attachments = $temp_file;
                        }
                        else {
                            $attachments = PHP_EOL . $temp_file;
                        }
                    }
                    else if (gettype($attachments) == 'array') {
                        array_push($attachments, $temp_file);
                    }
                }

                // Make sure to delete temporary file
                register_shutdown_function(array($this, 'delete_email_file'), $temp_file);

            }

            // Else attach proforma invoice
            else if ($this->opt['woo_pdf_proforma_enabled'] && ($email_type == 'customer_processing_order' || isset($manual_customer_processing_order) || isset($admin_new_order_email))) {

                if (!$this->opt['woo_pdf_send_proforma_email']  && !isset($manual_customer_processing_order) && !isset($admin_new_order_email)) {
                    return $attachments;
                }

                // Get (temporary) proforma invoice path
                $proforma_path = $this->get_proforma($order->id);

                // Push to attachments
                if ($proforma_path) {
                    if (gettype($attachments) == 'string') {
                        if ($attachments == '') {
                            $attachments = $proforma_path;
                        }
                        else {
                            $attachments = PHP_EOL . $proforma_path;
                        }
                    }
                    else if (gettype($attachments) == 'array') {
                        array_push($attachments, $proforma_path);
                    }
                }

                // Make sure to delete temporary file
                register_shutdown_function(array($this, 'delete_email_file'), $proforma_path);
            }

            return $attachments;
        }

        /**
         * Get next invoice number
         * 
         * @access public
         * @return int
         */
        public function get_next_invoice_number()
        {
            // Maybe reset counter each year
            $reset_internal_sequence = false;

            if ($this->opt['woo_pdf_reset_each_year']) {
                if ($last_invoice_year = get_option('woo_pdf_last_invoice_year')) {
                    if ((int) $last_invoice_year < (int) date('Y')) {
                        $reset_internal_sequence = true;
                    }
                }
            }

            // Track invoice numbers when generating invoices in batches
            if ($this->batch_invoice_id_counter !== null) {
                $this->batch_invoice_id_counter = $this->batch_invoice_id_counter;
            }
            else {
                $this->batch_invoice_id_counter = ($reset_internal_sequence ? 1 : $this->opt['woo_pdf_next_invoice_number']);
            }

            $next_invoice_number = $this->batch_invoice_id_counter;

            // Increment for next invoice
            $this->batch_invoice_id_counter++;

            // Store next invoice number in options storage
            $this->update_options(array('woo_pdf_next_invoice_number' => ($next_invoice_number + 1)));

            // Track year of last invoice generated for counter reset functionality
            update_option('woo_pdf_last_invoice_year', date('Y'));

            return $next_invoice_number;
        }

        /**
         * Render admin invoice download link
         * 
         * @access public
         * @param string $content
         * @return string
         */
        public function admin_invoice_link($content)
        {
            global $post;
            global $woocommerce;

            $order = new WC_Order($post->ID);

            if (!$order) {
                return $content;
            }

            $invoice = $this->get_invoice($post->ID);

            // WooCommerce styling fix (covering multiple versions...)
            if (isset($woocommerce->version) && version_compare($woocommerce->version, '2.1', '>=')) {
                $button_style = 'style="display:block;text-indent:-9999px;position:relative;padding:6px 4px;height:2em!important;width:2em;"';
            }
            else {
                $button_style = '';
            }

            // Show invoice link
            if (is_array($invoice) && !empty($invoice) && $this->opt['woo_pdf_enabled']) {
                $data = $invoice['id'].'|'.$invoice['prefix'].'|'.$invoice['code'].'|'.$invoice['suffix'];
                $download_code = base64_encode($data);
                $download_url = home_url('/?wpd_invoice='.$download_code);

                $download_button = '<a id="" class="button tips" ' . $button_style . ' href="'.$download_url.'" data-tip="'.__('Invoice', 'woo_pdf').'">' .
                                   '<img src="'.WOOPDF_PLUGIN_URL.'/assets/images/download.png'.'" alt="'.__('Invoice', 'woo_pdf').'" width="14">' .
                                   '</a>';
                echo $download_button;
            }

            // Show proforma link
            else if (!is_array($invoice) && $this->opt['woo_pdf_proforma_enabled'] && $order->status != 'completed') {
                $download_url = home_url('/?wpd_proforma='.$post->ID);
                $download_button = '<a id="" class="button tips" ' . $button_style . ' href="'.$download_url.'" data-tip="'.__('Proforma', 'woo_pdf').'">' .
                                   '<img src="'.WOOPDF_PLUGIN_URL.'/assets/images/download.png'.'" alt="'.__('Proforma', 'woo_pdf').'" width="14">' .
                                   '</a>';
                echo $download_button;
            }

            return $content;
        }

        /**
         * Render user invoice download link
         * 
         * @access public
         * @param string $content
         * @return string
         */
        public function user_invoice_link($order)
        {
            $invoice = $this->get_invoice($order->id);

            // Show invoice link
            if (is_array($invoice) && !empty($invoice) && $this->opt['woo_pdf_enabled'] && $this->opt['woo_pdf_allow_download'] && apply_filters('woo_pdf_allow_regular_invoice_download', true, $order, 'single')) {
                $data = $invoice['id'].'|'.$invoice['prefix'].'|'.$invoice['code'].'|'.$invoice['suffix'];
                $download_code = base64_encode($data);
                $download_url = home_url('/?wpd_invoice='.$download_code);
                $download_button = '<p class="woo_pdf_download_link" style="padding: 15px 0;"><a id="woo_pdf_invoice_download_link" href="'.$download_url.'" data-tip="Invoice">' .
                                   '<img style="position: relative; top: 4px;" src="'.WOOPDF_PLUGIN_URL.'/assets/images/pdf.png'.'" alt="Invoice" width="20" height="20">' .
                                   '<span style="padding-left: 10px;">' . $this->opt['woo_pdf_title_download_invoice'] . '</span>' .
                                   '</a></p>';
                echo $download_button;
            }

            // Show proforma link
            else if (!is_array($invoice) && $this->opt['woo_pdf_proforma_enabled'] && $this->opt['woo_pdf_allow_proforma_download'] && $order->status != 'completed' && apply_filters('woo_pdf_allow_proforma_invoice_download', true, $order, 'single')) {
                $download_url = home_url('/?wpd_proforma='.$order->id);
                $download_button = '<p class="woo_pdf_download_link" style="padding: 15px 0;"><a id="woo_pdf_proforma_download_link" href="'.$download_url.'" data-tip="Invoice">' .
                                   '<img style="position: relative; top: 4px;" src="'.WOOPDF_PLUGIN_URL.'/assets/images/pdf.png'.'" alt="Invoice" width="20" height="20">' .
                                   '<span style="padding-left: 10px;">' . $this->opt['woo_pdf_title_download_proforma'] . '</span>' .
                                   '</a></p>';
                echo $download_button;
            }
        }

        /**
         * Batch download invoices
         * 
         * @access public
         * @return void
         */
        public function batch_download()
        {

            // Check if zip extension is present
            if (!extension_loaded('zip')) {
                exit();
            }

            // Get dates
            $this->date_from = $_GET['woo_pdf_download_from'];
            $this->date_to = $_GET['woo_pdf_download_to'];

            // Prepare query
            $args = array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_mime_type' => 'application/pdf',
                'post_status' => 'inherit',
            );

            // Load invoices
            add_filter('posts_where', array($this, 'filter_where'));
            $attachments = query_posts($args);
            remove_filter('posts_where', array($this, 'filter_where'));

            // Set up file directory
            $upload_dir = wp_upload_dir();
            $location = $upload_dir['basedir'] . '/' . 'woocommerce_pdf_invoices/tmp';
            if (!file_exists($location)) {
                mkdir($location, 0755, true);
            }

            // Generate zip file
            $file = tempnam($location, 'woo_pdf');
            $zip = new ZipArchive();
            $zip->open($file, ZipArchive::OVERWRITE);

            $file_added = false;

            // Add files to zip
            foreach ($attachments as $attachment) {
                $order = get_post($attachment->post_parent);

                if (!$order || $order->post_type != 'shop_order') {
                    continue;
                }

                $id = get_post_meta($order->ID, 'woo_pdf_invoice_id', true);
                $prefix = get_post_meta($order->ID, 'woo_pdf_invoice_prefix', true);
                $suffix = get_post_meta($order->ID, 'woo_pdf_invoice_suffix', true);

                if ($id == '') {
                    continue;
                }

                $file_path = get_attached_file($attachment->ID);
                $file_name = _x($this->opt['woo_pdf_title_filename_prefix'], 'file name prefix', 'woo_pdf') . (!empty($prefix) ? $prefix . '_' : '') . $id . (!empty($suffix) ? '_' . $suffix : '') . '.pdf';
                $zip->addFile($file_path, $file_name);

                $file_added = true;
            }

            // Add dummy data if no files were added
            if (!$file_added) {
                $zip->addFromString('no invoices for selected period', '');
            }

            // Close and output
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: attachment; filename="file.zip"');
            readfile($file);
            unlink($file);
        }

        /**
         * Date filter for batch downloads
         * 
         * @access public
         * @param string $where
         * @return string
         */
        public function filter_where($where = '')
        {
            $where .= " AND post_date >= '" . date('Y-m-d H:i:s', strtotime($this->date_from)) .
                    "' AND post_date <= '" . date('Y-m-d H:i:s', (strtotime('tomorrow', strtotime($this->date_to)) - 1)) . "'";

            return $where;
        }

        /**
         * Pushes invoice file to the browser
         * 
         * @access public
         * @return void
         */
        public function push_invoice()
        {
            $invoice = explode('|', base64_decode($_GET['wpd_invoice']));

            if (count($invoice) != 4) {
                exit;
            }

            // Get file path
            $upload_dir = wp_upload_dir();
            $location = $upload_dir['basedir'] . '/' . 'woocommerce_pdf_invoices';
            $file_path = $location . '/' . $invoice[2] . '.pdf';

            // Push file to browser
            if ($fp = fopen($file_path, 'rb')) {
                header('Content-Type: application/pdf');
                header('Content-Length: ' . filesize($file_path));
                header('Content-disposition: attachment; filename="'._x($this->opt['woo_pdf_title_filename_prefix'], 'file name prefix', 'woo_pdf') . (!empty($invoice[1]) ? $invoice[1] . '_' : '') . $invoice[0] . (!empty($invoice[3]) ? '_' . $invoice[3] : '') . '.pdf"');
                fpassthru($fp);
            }

            exit;
        }

        /**
         * Generates and pushes proforma invoice to the browser
         * 
         * @access public
         * @return void
         */
        public function push_proforma()
        {
            if (!$this->opt['woo_pdf_proforma_enabled']) {
                return;
            }

            $order_id = $_GET['wpd_proforma'];

            if (!class_exists('WC_Order')) {
                exit();
            }

            // Load order
            $order = new WC_Order($order_id);         

            if (!$order) {
                return;
            }

            // Check if user has a right to get this document
            $current_user = wp_get_current_user();
            $user_ok = false;

            if ($current_user instanceof WP_User) {
                if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
                    $user_ok = true;
                }
                else if ($current_user->ID == $order->user_id) {
                    $user_ok = true;
                }
            }

            if (!$user_ok) {
                exit;
            }

            // Load PDF class
            if (!class_exists('TCPDF')) {
                require WOOPDF_PLUGIN_PATH.'/includes/tcpdf/tcpdf.php';
            }
            if (!class_exists('WooPdfInvoice')) {
                require WOOPDF_PLUGIN_PATH.'/includes/woo-pdf-invoice.class.php';
            }

            $display_order_id = $order->get_order_number();

            $info = array(
                'id' => $display_order_id,
                'prefix' => '',
                'suffix' => '',
                'code' => ''
            );

            // We don't need hash tag before invoice name for file name
            $display_order_id = preg_replace('/[^0-9.]+/', '', $display_order_id);

            // Generate proforma and push it directly to browser
            $pdf = new WooPdfInvoice(array('order' => $order, 'options' => $this->get_options(), 'info' => $info, 'type' => 'proforma'), 'P', 'pt', 'A4');
            $pdf->CreateInvoice();
            $pdf->Output($display_order_id.'.pdf', 'D');
            exit();
        }

        /**
         * Delete regular invoice on specified order
         * 
         * @access public
         * @return false
         */
        public function delete_invoice()
        {
            // Check if user has rights to delete invoices
            $current_user = wp_get_current_user();
            $user_ok = false;

            if ($current_user instanceof WP_User) {
                if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
                    $user_ok = true;
                }
            }

            if (!$user_ok) {
                return;
            }

            // Extract request data
            $invoice = explode('|', base64_decode($_GET['wpd_delete_invoice']));

            if (count($invoice) != 4) {
                return;
            }

            // Get all post attachments (to find the one that needs to be removed)
            $attachments = get_children($_GET['order_id']);

            if (!is_array($attachments) || empty($attachments)) {
                return;
            }

            // Find and delete post attachment that represents invoice
            foreach ($attachments as $attachment) {
                if ($attachment->post_mime_type == 'application/pdf' && preg_match('/^invoice\-.+/', $attachment->post_name)) {
                    wp_delete_attachment($attachment->ID);
                    break;
                }
            }

            // Remove post meta from order post
            delete_post_meta($_GET['order_id'], 'woo_pdf_invoice_id');
            delete_post_meta($_GET['order_id'], 'woo_pdf_invoice_prefix');
            delete_post_meta($_GET['order_id'], 'woo_pdf_invoice_suffix');
            delete_post_meta($_GET['order_id'], 'woo_pdf_invoice_code');

            // Redirect back to order page
            wp_redirect(admin_url('/post.php?post='.$_GET['order_id'].'&action=edit'));
            exit;
        }

        /**
         * Generate regular invoice manually on specified order
         * 
         * @access public
         * @return false
         */
        public function generate_invoice()
        {
            if (!class_exists('WC_Order')) {
                return;
            }

            // Load order
            $order = new WC_Order($_GET['wpd_generate_invoice']);         

            if (!$order) {
                return;
            }

            // Check if user has rights to generate invoices
            $current_user = wp_get_current_user();
            $user_ok = false;

            if ($current_user instanceof WP_User) {
                if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
                    $user_ok = true;
                }
            }

            if (!$user_ok) {
                return;
            }

            // Check maybe we already have invoice for this order
            $invoice_id = get_post_meta($order->id, 'woo_pdf_invoice_id', true);
            if (!empty($invoice_id)) {
                wp_redirect(admin_url('/post.php?post='.$_GET['wpd_generate_invoice'].'&action=edit'));
                exit;
            }

            // If not - create a new one
            $this->make_invoice($order);

            // Redirect back to order page
            wp_redirect(admin_url('/post.php?post='.$_GET['wpd_generate_invoice'].'&action=edit'));
            exit;
        }

        /**
         * Generate proforma invoice and store it temporary
         * 
         * @access public
         * @param string $order_id
         * @return string
         */
        public function get_proforma($order_id)
        {
            if (!$this->opt['woo_pdf_proforma_enabled']) {
                return;
            }

            if (!class_exists('WC_Order')) {
                exit();
            }

            // Load order
            $order = new WC_Order($order_id);

            if (!$order) {
                return;
            }

            // Load PDF class
            if (!class_exists('TCPDF')) {
                require WOOPDF_PLUGIN_PATH.'/includes/tcpdf/tcpdf.php';
            }
            if (!class_exists('WooPdfInvoice')) {
                require WOOPDF_PLUGIN_PATH.'/includes/woo-pdf-invoice.class.php';
            }

            $info = array(
                'id' => $order->get_order_number(),
                'prefix' => '',
                'suffix' => '',
                'code' => ''
            );

            // Create temporary file
            $upload_dir = wp_upload_dir();
            $temp_location = $upload_dir['basedir'] . '/' . 'woocommerce_pdf_invoices' . '/' . 'tmp';
            if (!file_exists($temp_location)) {
                mkdir($temp_location, 0755, true);
            }
            $temp_file = $temp_location . '/' . $order_id . '.pdf';

            // Generate proforma and save it to disk temporary
            $pdf = new WooPdfInvoice(array('order' => $order, 'options' => $this->get_options(), 'info' => $info, 'type' => 'proforma'), 'P', 'pt', 'A4');
            $pdf->CreateInvoice();
            $pdf->Output($temp_file, 'F');

            return $temp_file;
        }

        /**
         * Load scripts required for admin
         * 
         * @access public
         * @return void
         */
        public function enqueue_scripts() {
            // Scripts
            wp_enqueue_script('jquery');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-tooltip');

            // Our own
            wp_register_script('woo-pdf-js', WOOPDF_PLUGIN_URL . '/assets/js/woo-pdf.js', array('jquery'), WOOPDF_VERSION);
            wp_enqueue_script('woo-pdf-js');

            // Styles
            wp_register_style('woo-pdf-css', WOOPDF_PLUGIN_URL . '/assets/css/style.css', array(), WOOPDF_VERSION);
            wp_enqueue_style('woo-pdf-css');
            wp_register_style('woo-pdf-jquery-ui', WOOPDF_PLUGIN_URL . '/assets/css/jquery-ui.css', array(), '1.10.3');
            wp_enqueue_style('woo-pdf-jquery-ui');
            wp_register_style('woo-pdf-font-awesome', WOOPDF_PLUGIN_URL . '/assets/css/font-awesome/css/font-awesome.min.css', array(), '4.0.3');
            wp_enqueue_style('woo-pdf-font-awesome');
            wp_enqueue_style('thickbox');
        }

        /**
         * Check if PHP image processing extension is installed
         * 
         * @access public
         * @return bool
         */
        public function image_library_exists()
        {
            if (extension_loaded('imagick') || (extension_loaded('gd') && function_exists('gd_info'))) {
                return true;
            }

            return false;
        }

        /**
         * Hide attachment pages
         * 
         * @access public
         * @return void
         */
        public function hide_attachment_pages()
        {
            global $post;
            if (is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent != 0)) {
                $parent = get_post($post->post_parent);
                if ($post->post_mime_type == 'application/pdf' && $parent->post_type == 'shop_order') {
                    wp_redirect(home_url(), 301);
                }
            }
        }

        /**
         * Add settings link on plugins page
         * 
         * @access public
         * @return void
         */
        public function plugin_settings_link($links)
        {
            $settings_link = '<a href="http://support.rightpress.net/" target="_blank">'.__('Support', 'woo_pdf').'</a>';
            array_unshift($links, $settings_link);
            $settings_link = '<a href="admin.php?page=woo-pdf">'.__('Settings', 'woo_pdf').'</a>';
            array_unshift($links, $settings_link);
            return $links; 
        }

        /**
         * Maybe show invoice download button on the orders page
         * 
         * @access public
         * @param array $actions
         * @return array
         */
        public function orders_actions($actions, $order)
        {
            if ($this->opt['woo_pdf_display_orders_page_button']) {

                $invoice = $this->get_invoice($order->id);

                // Show invoice link
                if (is_array($invoice) && !empty($invoice) && $this->opt['woo_pdf_enabled'] && $this->opt['woo_pdf_allow_download'] && apply_filters('woo_pdf_allow_regular_invoice_download', true, $order, 'list')) {
                    $data = $invoice['id'].'|'.$invoice['prefix'].'|'.$invoice['code'].'|'.$invoice['suffix'];
                    $download_code = base64_encode($data);
                    $download_url = home_url('/?wpd_invoice='.$download_code);
                    $title = $this->opt['woo_pdf_document_name'];
                }

                // Show proforma link
                else if (!is_array($invoice) && $this->opt['woo_pdf_proforma_enabled'] && $this->opt['woo_pdf_allow_proforma_download'] && $order->status != 'completed' && apply_filters('woo_pdf_allow_proforma_invoice_download', true, $order, 'list')) {
                    $download_url = home_url('/?wpd_proforma='.$order->id);
                    $title = $this->opt['woo_pdf_proforma_name'];
                }

                if (isset($download_url) && isset($title)) {
                    $actions['invoice'] = array(
                        'url' => $download_url,
                        'name' => $title,
                    );
                }
            }

            return $actions;
        }

        /**
         * Add admin meta box with actions
         * 
         * @access public
         * @return void
         */
        public function add_woo_pdf_metabox()
        {
            global $post;

            if (!$post) {
                return;
            }

            $order = new WC_Order($post->ID);

            if (!$order) {
                return;
            }

            $invoice = $this->get_invoice($post->ID);

            if ((is_array($invoice) && !empty($invoice) && $this->opt['woo_pdf_enabled']) || (!is_array($invoice) && $this->opt['woo_pdf_proforma_enabled'] && ($order->status != 'completed' || $this->opt['woo_pdf_enabled'])) || ($this->opt['woo_pdf_enabled'])) {
                add_meta_box('woo_pdf_metabox', __('PDF Invoices', 'woo_pdf'), array($this, 'woo_pdf_metabox_content'), 'shop_order', 'side', 'default');
            }
        }

        /**
         * Add admin meta box content
         * 
         * @access public
         * @return void
         */
        public function woo_pdf_metabox_content()
        {
            global $post;

            if (!$post) {
                return;
            }

            $order = new WC_Order($post->ID);

            if (!$order) {
                return;
            }

            $invoice = $this->get_invoice($post->ID);

            echo '<table class="form-table">';

            if (is_array($invoice) && !empty($invoice) && $this->opt['woo_pdf_enabled']) {

                $data = $invoice['id'].'|'.$invoice['prefix'].'|'.$invoice['code'].'|'.$invoice['suffix'];
                $download_code = base64_encode($data);
                $download_url = home_url('/?wpd_invoice='.$download_code);
                $delete_url = home_url('/?wpd_delete_invoice='.$download_code.'&order_id='.$post->ID);

                ?>
                    <tr>
                        <td>
                            <a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Download regular invoice', 'woo_pdf'); ?>"><?php _e('Invoice', 'woo_pdf'); ?></a>
                            <?php if ($this->opt['woo_pdf_allow_delete']): ?>
                                <a class="button tips" href="<?php echo $delete_url; ?>" data-tip="<?php _e('Delete invoice so you can regenerate it if needed', 'woo_pdf'); ?>"><?php _e('Delete Invoice', 'woo_pdf'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
            }
            else if (!is_array($invoice) && $this->opt['woo_pdf_proforma_enabled'] && ($order->status != 'completed' || $this->opt['woo_pdf_enabled'])) {

                $download_url = home_url('/?wpd_proforma='.$post->ID);
                $generate_url = home_url('/?wpd_generate_invoice='.$post->ID);

                ?>
                    <tr>
                        <td>
                            <?php if ($order->status != 'completed'): ?>
                                <a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Download proforma invoice', 'woo_pdf'); ?>"><?php _e('Proforma', 'woo_pdf'); ?></a>
                            <?php endif; ?>
                            <?php if ($this->opt['woo_pdf_enabled']): ?>
                                <a class="button tips" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Manually generate regular invoice', 'woo_pdf'); ?>"><?php _e('Generate Invoice', 'woo_pdf'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
            }
            else if ($this->opt['woo_pdf_enabled']) {

                $generate_url = home_url('/?wpd_generate_invoice='.$post->ID);

                ?>
                    <tr>
                        <td>
                            <a class="button tips" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Manually generate regular invoice', 'woo_pdf'); ?>"><?php _e('Generate Invoice', 'woo_pdf'); ?></a>
                        </td>
                    </tr>
                <?php
            }

            echo '</table>';
        }

        /**
         * Delete temporary file (shutdown function)
         * 
         * @param string $file
         * @return void
         */
        public function delete_email_file($file)
        {
            unlink($file);
        }

        /**
         * Replace prefix/suffix macros
         * 
         * @access public
         * @param string $string
         * @param object $order
         * @param string $position
         * @return string
         */
        public function replace_prefix_suffix_macros($string, $order, $position = 'prefix')
        {
            // Define macros
            $macros = array(
                '{{year}}'  => (in_array($this->opt['woo_pdf_date_format'], array('0', '2', '4')) ? date('y') : date('Y')),
                '{{month}}' => (in_array($this->opt['woo_pdf_date_format'], array('0', '1', '2', '3')) ? date('n') : date(($this->opt['woo_pdf_date_format'] == '6') ? ('F') : ('m'))),
                '{{day}}'   => (in_array($this->opt['woo_pdf_date_format'], array('4', '5', '7', '8')) ? date('d') : date('j')),
            );

            // Allow developers to add their own macros
            $macros = apply_filters('woo_pdf_prefix_suffix_macros', $macros, $order, $position);

            foreach ($macros as $key => $value) {
                $string = preg_replace('/' . preg_quote($key) . '/i', $value, $string);
            }

            return $string;
        }


    }

}

$GLOBALS['WooPDF'] = new WooPDF();

?>
