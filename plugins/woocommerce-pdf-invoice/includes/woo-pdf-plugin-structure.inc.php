<?php

/*
 * Returns configuration for this plugin
 * 
 * @return array
 */
function woo_pdf_plugin_settings()
{
    return array(
        'general_settings' => array(
            'title' => __('Settings', 'woo_pdf'),
            'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
            'children' => array(
                'invoice_settings' => array(
                    'title' => __('Regular Invoice Settings', 'woo_pdf'),
                    'children' => array(
                        'enabled' => array(
                            'title' => __('Enable regular invoicing', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Regular invoices are generated as soon as orders are marked as completed.</p>', 'woo_pdf'),
                        ),
                        'allow_download' => array(
                            'title' => __('Allow download', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, clients will be able to download invoices from order details page.</p>', 'woo_pdf'),
                        ),
                        'send_email' => array(
                            'title' => __('Send by email', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, PDF invoice will be emailed to the client along with Order Completed email.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'invoice_settings_numbering' => array(
                    'title' => __('Regular Invoice Numbering', 'woo_pdf'),
                    'children' => array(
                        'numbering_method' => array(
                            'title' => __('Numbering method', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Internal sequence plus prefix/suffix', 'woo_pdf'),
                                '1' => __('Order numbers plus prefix/suffix', 'woo_pdf'),
                                '2' => __('Order numbers', 'woo_pdf'),
                            ),
                            'hint' => __('<p>It is highly recommended that you stick to the standard internal numbering sequence which ensures there will be no gaps in the sequence (common problem with order numbers).</p> <p>Only change this value if you know what you are doing.</p>', 'woo_pdf'),
                        ),
                        'next_invoice_number' => array(
                            'title' => __('Next number', 'woo_pdf'),
                            'type' => 'text',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                        ),
                        'number_prefix' => array(
                            'title' => __('Prefix', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Leave blank to use no prefix.</p> <p>The following macros are available: {{year}}, {{month}}, {{day}}.</p>', 'woo_pdf'),
                        ),
                        'number_suffix' => array(
                            'title' => __('Suffix', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Leave blank to use no suffix.</p> <p>The following macros are available: {{year}}, {{month}}, {{day}}.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'proforma_invoices' => array(
                    'title' => __('Proforma Invoice Settings', 'woo_pdf'),
                    'children' => array(
                        'proforma_enabled' => array(
                            'title' => __('Enable proforma invoicing', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Proforma invoices are generated as soon as order is placed.</p><p>Order IDs are used for proforma invoice numbering.</p><p>Proforma invoices are not available after order is marked as completed.</p>', 'woo_pdf'),
                        ),
                        'allow_proforma_download' => array(
                            'title' => __('Allow download', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, clients will be able to download proforma invoices from order details page.</p>', 'woo_pdf'),
                        ),
                        'send_proforma_email' => array(
                            'title' => __('Send by email', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, PDF proforma invoice will be emailed to the client along with Processing Order email.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
            ),
        ),
        'advanced_settings' => array(
            'title' => __('Advanced', 'woo_pdf'),
            'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
            'children' => array(
                'other_invoice_settings' => array(
                    'title' => __('Invoice Settings', 'woo_pdf'),
                    'children' => array(
                        'display_product_id' => array(
                            'title' => __('Display product ID/SKU', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Do not display', 'woo_pdf'),
                                '1' => __('Display product ID (WP post ID)', 'woo_pdf'),
                                '2' => __('Display SKU', 'woo_pdf'),
                            ),
                            'hint' => __('<p>If enabled, product ID/SKU will be displayed for each item just before its name.</p>', 'woo_pdf'),
                        ),
                        'display_category' => array(
                            'title' => __('Display product category', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product category below each order item on the invoice.</p> <p>If there are multiple categories, they will be displayed in one line separated by commas.</p>', 'woo_pdf'),
                        ),
                        'display_short_description' => array(
                            'title' => __('Display short description', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product short description below each order item on the invoice.</p> <p>This extension attempts to convert HTML to text but this feature is experimental. Use plain text to be sure that the final result looks as expected.</p>', 'woo_pdf'),
                        ),
                        'display_product_thumbnails' => array(
                            'title' => __('Display product images', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product images below product name whenever available.</p> <p>This feature is experimental - use at your own risk.</p>', 'woo_pdf'),
                        ),
                        'display_currency_symbol' => array(
                            'title' => __('Display currency symbol', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, currency symbol (e.g. $) will be displayed next to every amount on the invoice. Currency code (e.g. USD) is displayed next to total amount in any way.</p>', 'woo_pdf'),
                        ),
                        'display_free_shipping' => array(
                            'title' => __('Display shipping when free', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, shipping row will be displayed even if shipping is free (zero value).</p>', 'woo_pdf'),
                        ),
                        'amount_in_words' => array(
                            'title' => __('Display amount in words', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Depending on your country, you may need to print amount in words on your invoices.</p><p>Use translation file that comes with this plugin to translate numbers, set your own currency and plural forms.</p><p>This feature is experimental - use at your own risk.</p>', 'woo_pdf'),
                        ),
                        'reset_each_year' => array(
                            'title' => __('Reset numbering each year', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, next invoice number will be reset to 1 when the first invoice is generated each year.</p> <p>For this to take effect, there must be at least one invoice generated last year and no invoices generated this year.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'tax_settings' => array(
                    'title' => __('Tax Settings', 'woo_pdf'),
                    'children' => array(
                        'list_tax' => array(
                            'title' => __('Display tax rows', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 2,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '2' => __('When tax is not displayed inline', 'woo_pdf'),
                                '1' => __('Always', 'woo_pdf'),
                                '0' => __('Never', 'woo_pdf'),
                            ),
                            'hint' => __('<p>If enabled, all applicable taxes will be listed just above (if Subtotal is exclusive of tax) or below (if Subtotal is inclusive of tax) Total row.</p>', 'woo_pdf'),
                        ),
                        'display_tax_inline' => array(
                            'title' => __('Display tax inline', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('When different rates are present', 'woo_pdf'),
                                '1' => __('Always', 'woo_pdf'),
                                '2' => __('Never', 'woo_pdf'),
                            ),
                            'hint' => __('<p>You may need to display net amount, tax rate and tax amount individually for each line item.</p> <p>This is useful when different rates of the same tax are used for different items on the same invoice, e.g. reduced VAT rate is applied to specific group of products.</p>', 'woo_pdf'),
                        ),
                        'inclusive_tax' => array(
                            'title' => __('Display amounts inclusive of tax', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, line item price, line subtotal and subtotal will be displayed inclusive of tax. This plugin always overrides related WooCommerce settings.</p><p>This setting is ignored when tax is displayed inline.</p>', 'woo_pdf'),
                        ),
                        'total_excl_tax' => array(
                            'title' => __('Display total excl. tax row', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, additional line will be added to the totals block that displays total exclusive of tax.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'other_settings' => array(
                    'title' => __('Other Settings', 'woo_pdf'),
                    'children' => array(
                        'display_orders_page_button' => array(
                            'title' => __('Display order page buttons', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, download button will be displayed for a customer next to each order in the orders list.</p> <p>These buttons are optional - if you disable them, invoice download links will still be displayed on a single order details page if <strong>Allow download</strong> is enabled.</p>', 'woo_pdf'),
                        ),
                        'attach_to_new_order' => array(
                            'title' => __('Send proforma invoice to admin', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, proforma invoices will be attached to New Order email that is sent to store administrator or manager.</p> <p>Please note that proforma invoicing must be enabled for this feature to take effect.</p>', 'woo_pdf'),
                        ),
                        'allow_delete' => array(
                            'title' => __('Allow admins to delete invoices', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>You are strongly advised against activating and using this feature as it may cause more harm than good.</p> <p>When you delete an invoice and regenerate it, invoice number will be different (if you use internal numbering sequence).</p> <p>It is against accounting standards to have gaps in the invoice numbering system - you must account for each number in the sequence.</p> <p>Use at your own risk.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
            ),
        ),
        'seller_details' => array(
            'title' => __('Seller & Buyer', 'woo_pdf'),
            'icon' => '<i class="fa fa-briefcase" style="font-size: 0.8em;"></i>',
            'children' => array(
                'seller_block' => array(
                    'title' => __('Seller Block', 'woo_pdf'),
                    'children' => array(
                        'seller_logo' => array(
                            'title' => __('Logo image', 'woo_pdf'),
                            'type' => 'media',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'url',
                                'empty' => true
                            ),
                            'hint' => __('<p>Enter URL or select image from Media Library.</p>', 'woo_pdf'),
                        ),
                        'seller_logo_resize' => array(
                            'title' => __('Logo resize factor (in percent)', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '100',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                            'hint' => __('<p>Increase this number if you want to make your logo larger on the invoice and vice versa.</p>', 'woo_pdf'),
                        ),
                        'title_seller' => array(
                            'title' => __('Block title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Seller', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'seller_name' => array(
                            'title' => __('Company name', 'woo_pdf'),
                            'type' => 'text',
                            'default' => get_bloginfo(),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'seller_content' => array(
                            'title' => __('Company details', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => 'Tax ID: 123456789'. PHP_EOL . 'Demo Address #123'. PHP_EOL . 'London NW1 6XE'. PHP_EOL . 'United Kingdom',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Use this field to set up your company details, including address, company registration number, tax code etc.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'buyer_block' => array(
                    'title' => __('Buyer Block', 'woo_pdf'),
                    'children' => array(
                        'title_buyer' => array(
                            'title' => __('Block title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Buyer', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'buyer_content' => array(
                            'title' => __('Buyer details layout', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => '{{billing_address_1}}' . PHP_EOL . '{{billing_address_2}}' . PHP_EOL . '{{billing_postcode}} {{billing_city}} {{billing_state}}' . PHP_EOL . '{{billing_country}}',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => sprintf(__('<p>Use this field to set up the layout of the buyer details block.</p> <p>The following macros are available: %s</p> <p>You can use custom fields in the same way, e.g. {{my_custom_field_key}}.</p> <p>Do not include buyer first name, last name and company name - these fields are displayed automatically.</p>', 'woo_pdf'), '<br />{{billing_address_1}}<br />{{billing_address_2}}<br />{{billing_postcode}}<br />{{billing_city}}<br />{{billing_state}}<br />{{billing_country}}<br />{{billing_email}}<br />{{billing_phone}}'),
                        ),
                        'buyer_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, all lines that contain macros with empty values only will be removed.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
            ),
        ),
        'content_blocks' => array(
            'title' => __('Content Blocks', 'woo_pdf'),
            'icon' => '<i class="fa fa-edit" style="font-size: 0.8em;"></i>',
            'children' => array(
                'block_footer' => array(
                    'title' => __('Page footer', 'woo_pdf'),
                    'children' => array(
                        'footer' => array(
                            'title' => __('Content', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'footer_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If you are using macros in your content, you may wish to remove lines on which all macros are empty.</p>', 'woo_pdf'),
                        ),
                    ),
                ),
                'block_1' => array(
                    'title' => __('Custom Block #1', 'woo_pdf'),
                    'children' => array(
                        'block_1_title' => array(
                            'title' => __('Title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => 'Notes',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_1_content' => array(
                            'title' => __('Content', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => 'Thank you for your order!',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_1_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If you are using macros in your content, you may wish to remove lines on which all macros are empty.</p>', 'woo_pdf'),
                        ),
                        'block_1_show' => array(
                            'title' => __('Displayed on', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Both types of invoices', 'woo_pdf'),
                                '1' => __('Regular invoices', 'woo_pdf'),
                                '2' => __('Proforma invoices', 'woo_pdf'),
                            ),
                        ),
                    ),
                ),
                'block_2' => array(
                    'title' => __('Custom Block #2', 'woo_pdf'),
                    'children' => array(
                        'block_2_title' => array(
                            'title' => __('Title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_2_content' => array(
                            'title' => __('Content', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_2_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If you are using macros in your content, you may wish to remove lines on which all macros are empty.</p>', 'woo_pdf'),
                        ),
                        'block_2_show' => array(
                            'title' => __('Displayed on', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Both types of invoices', 'woo_pdf'),
                                '1' => __('Regular invoices', 'woo_pdf'),
                                '2' => __('Proforma invoices', 'woo_pdf'),
                            ),
                        ),
                    ),
                ),
                'block_3' => array(
                    'title' => __('Custom Block #3', 'woo_pdf'),
                    'children' => array(
                        'block_3_title' => array(
                            'title' => __('Title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_3_content' => array(
                            'title' => __('Content', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_3_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If you are using macros in your content, you may wish to remove lines on which all macros are empty.</p>', 'woo_pdf'),
                        ),
                        'block_3_show' => array(
                            'title' => __('Displayed on', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Both types of invoices', 'woo_pdf'),
                                '1' => __('Regular invoices', 'woo_pdf'),
                                '2' => __('Proforma invoices', 'woo_pdf'),
                            ),
                        ),
                    ),
                ),
                'block_4' => array(
                    'title' => __('Custom Block #4', 'woo_pdf'),
                    'children' => array(
                        'block_4_title' => array(
                            'title' => __('Title', 'woo_pdf'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_4_content' => array(
                            'title' => __('Content', 'woo_pdf'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_4_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_pdf'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If you are using macros in your content, you may wish to remove lines on which all macros are empty.</p>', 'woo_pdf'),
                        ),
                        'block_4_show' => array(
                            'title' => __('Displayed on', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Both types of invoices', 'woo_pdf'),
                                '1' => __('Regular invoices', 'woo_pdf'),
                                '2' => __('Proforma invoices', 'woo_pdf'),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'translation' => array(
            'title' => __('Localization', 'woo_pdf'),
            'icon' => '<i class="fa fa-font" style="font-size: 0.8em;"></i>',
            'children' => array(
                'date_time' => array(
                    'title' => __('Date & Time', 'woo_pdf'),
                    'children' => array(
                        'date_format' => array(
                            'title' => __('Date format', 'woo_pdf'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('mm/dd/yy', 'woo_pdf'),
                                '1' => __('mm/dd/yyyy', 'woo_pdf'),
                                '2' => __('dd/mm/yy', 'woo_pdf'),
                                '3' => __('dd/mm/yyyy', 'woo_pdf'),
                                '4' => __('yy-mm-dd', 'woo_pdf'),
                                '5' => __('yyyy-mm-dd', 'woo_pdf'),
                                '6' => __('Month dd, yyyy', 'woo_pdf'),
                                '7' => __('dd.mm.yyyy', 'woo_pdf'),
                                '8' => __('dd-mm-yyyy', 'woo_pdf'),
                            ),
                        ),
                    ),
                ),
                'translation' => array(
                    'title' => __('Field Labels', 'woo_pdf'),
                    'children' => array(
                        'document_name' => array(
                            'title' => __('Invoice', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Invoice', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'proforma_name' => array(
                            'title' => __('Proforma Invoice', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Proforma Invoice', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_date' => array(
                            'title' => __('Invoice Date', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Invoice Date', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_amount' => array(
                            'title' => __('Order Amount', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Order Amount', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_product' => array(
                            'title' => __('Product', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Product', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_price' => array(
                            'title' => __('Price', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Price', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_quantity' => array(
                            'title' => __('Quantity', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Qty.', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_net' => array(
                            'title' => __('Net', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Net', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line net amount column when different tax rates are used for different items.</p>', 'woo_pdf'),
                        ),
                        'title_tax_percent' => array(
                            'title' => __('Tax %', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Tax %', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line tax rate column when different tax rates are used for different items.</p>', 'woo_pdf'),
                        ),
                        'title_tax' => array(
                            'title' => __('Tax', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Tax', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line tax total column when different tax rates are used for different items.</p>', 'woo_pdf'),
                        ),
                        'title_line_total' => array(
                            'title' => __('Line Total', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Line Total', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_shipping' => array(
                            'title' => __('Shipping', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Shipping', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_shipping_tax' => array(
                            'title' => __('Shipping Tax', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Shipping Tax', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Only displayed when different tax rates are used for different items. In this case, all other tax amounts are displayed inline on the main table and only shipping tax row is displayed on the totals block.</p>', 'woo_pdf'),
                        ),
                        'title_subtotal' => array(
                            'title' => __('Subtotal', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Subtotal', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_cart_discount' => array(
                            'title' => __('Cart Discount', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Cart Discount', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_order_discount' => array(
                            'title' => __('Order Discount', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Order Discount', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_total' => array(
                            'title' => __('Total', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Total', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_total_excl_tax' => array(
                            'title' => __('Total Excluding Tax', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Total Excl. Tax', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_category' => array(
                            'title' => __('Category', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Category', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_description' => array(
                            'title' => __('Description', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Description', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_notes' => array(
                            'title' => __('Notes', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Notes', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_amount_in_words' => array(
                            'title' => __('Amount in words', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Amount in words', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_page' => array(
                            'title' => __('Page', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Page', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_additional_page' => array(
                            'title' => __('(additional page)', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('(additional page)', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_download_invoice' => array(
                            'title' => __('Download Invoice', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Download Invoice', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_download_proforma' => array(
                            'title' => __('Download Proforma Invoice', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('Download Proforma Invoice', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_filename_prefix' => array(
                            'title' => __('invoice_', 'woo_pdf'),
                            'type' => 'text',
                            'default' => __('invoice_', 'woo_pdf'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Value of this field is used as a file name prefix for regular invoices (e.g. invoice_ABC_123.pdf).</p>', 'woo_pdf'),
                        ),
                    ),
                ),
            ),
        ),
        'batch_download' => array(
            'title' => __('Batch Download', 'woo_pdf'),
            'icon' => '<i class="fa fa-download" style="font-size: 0.8em;"></i>',
            'children' => array(
                
            ),
        ),
    );
}

?>
