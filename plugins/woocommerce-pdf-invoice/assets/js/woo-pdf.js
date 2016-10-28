/**
 * WooCommerce PDF Invoice Plugin JavaScript
 */

/**
 * Based on jQuery
 */
jQuery(document).ready(function() {

    /**
     * Conditional fields
     */
    function woo_pdf_toggle_conditional_fields_numbering()
    {
        jQuery('#woo_pdf_numbering_method').each(function() {
            var current_value = jQuery(this).val();

            if (current_value === '0') {
                jQuery('#woo_pdf_next_invoice_number').parent().parent().show();
                jQuery('#woo_pdf_reset_each_year').parent().parent().show();
                jQuery('#woo_pdf_number_prefix').parent().parent().show();
                jQuery('#woo_pdf_number_suffix').parent().parent().show();
            }
            else if (current_value === '1') {
                jQuery('#woo_pdf_next_invoice_number').parent().parent().hide();
                jQuery('#woo_pdf_reset_each_year').parent().parent().hide();
                jQuery('#woo_pdf_number_prefix').parent().parent().show();
                jQuery('#woo_pdf_number_suffix').parent().parent().show();
            }
            else if (current_value === '2') {
                jQuery('#woo_pdf_next_invoice_number').parent().parent().hide();
                jQuery('#woo_pdf_reset_each_year').parent().parent().hide();
                jQuery('#woo_pdf_number_prefix').parent().parent().hide();
                jQuery('#woo_pdf_number_suffix').parent().parent().hide();
            }
        });
    }
    woo_pdf_toggle_conditional_fields_numbering();
    jQuery('#woo_pdf_numbering_method').change(woo_pdf_toggle_conditional_fields_numbering);

    /**
     * Admin hints
     */
    jQuery('form').each(function(){
        jQuery(this).find(':input').each(function(){
            if (typeof woo_pdf_hints !== 'undefined' && typeof woo_pdf_hints[this.id] !== 'undefined') {
                jQuery(this).parent().parent().find('th').append('<div class="woo_pdf_tip" title="'+woo_pdf_hints[this.id]+'"><i class="fa fa-question"></div>');
            }
        });
    });
    jQuery.widget('ui.tooltip', jQuery.ui.tooltip, {
        options: {
            content: function() {
                return jQuery(this).prop('title');
            }
        }
    });
    jQuery('.woo_pdf_tip').tooltip();

    /**
     * Logo upload
     */
    jQuery('#woo_pdf_seller_logo_upload_button').click(function() {

        // Store original send_to_editor function
        window.woo_pdf_restore_send_to_editor = window.send_to_editor;

        // Overwrite send to editor function with ours
        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');

            // Handle cases when allow_url_fopen is disabled in PHP configuration
            if (typeof woo_pdf_uploads_url !== 'undefined' && typeof woo_pdf_uploads_path !== 'undefined') {
                if (typeof woo_pdf_url_fopen_allowed !== 'undefined' && woo_pdf_url_fopen_allowed == '0') {
                    imgurl = imgurl.replace(woo_pdf_uploads_url, woo_pdf_uploads_path);
                }
            }

            jQuery('#woo_pdf_seller_logo').val(imgurl);
            tb_remove();
            window.send_to_editor = window.woo_pdf_restore_send_to_editor;
        };

        formfield = jQuery('woo_pdf_seller_logo').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });
    /*window.send_to_editor = function(html) {
        imgurl = jQuery('img',html).attr('src');
        jQuery('#woo_pdf_seller_logo').val(imgurl);
        tb_remove();
    };*/

    /**
     * Date picker
     */
    jQuery('#woo_pdf_download_from').datepicker({
        dateFormat : 'yy-mm-dd'
    });
    jQuery('#woo_pdf_download_to').datepicker({
        dateFormat : 'yy-mm-dd'
    });

    /**
     * Batch download call
     */
    jQuery('#woo_pdf_batch_download').click(function() {
        var from = jQuery('#woo_pdf_download_from').val();
        var to = jQuery('#woo_pdf_download_to').val();
        location.href = woo_pdf_home_url + '?woo_pdf_download_from='+from+'&woo_pdf_download_to='+to;
    });
});
