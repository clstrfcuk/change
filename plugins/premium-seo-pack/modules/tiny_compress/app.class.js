/*
Document   :  Tiny Compress
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspTinyCompress = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var selected_element = [];
    var inAction = 0;

    // init function, autoload
    (function init() {
        // load the triggers
        $(document).ready(function(){
            maincontainer = $("#psp-wrapper");
            loading = maincontainer.find("#psp-main-loading");

            triggers();
        });
    })();
    
    function row_loading( row, status )
    {
        /*if( status == 'show' ){
            row.find('span.psp-smushit-loading').show();
        } else {
            row.find('span.psp-smushit-loading').hide();
        }
        return true;*/

        if( status == 'show' ){
            if( row.size() > 0 ){
                
                if( row.find('.psp-row-loading-marker').size() == 0 ){
                    var row_loading_box = $('<div class="psp-row-loading-marker" style="top: -' + ( parseInt(row.height()/2 - 3) ) + 'px;"><div class="psp-row-loading"><div class="psp-meter psp-animate" style="width:30%; margin: 10px 0px 0px 30%;"><span style="width:100%"></span></div></div></div>')
                    row_loading_box.find('div.psp-row-loading').css({
                        'width': row.width(),
                        'height': row.height()
                    });

                    row.find('td').eq(0).append(row_loading_box);
                }
                row.find('.psp-row-loading-marker').fadeIn('fast');
            }
        }else{
            row.find('.psp-row-loading-marker').fadeOut('fast');
        }
    }

    function smushit( that, callback ) {
        
        var row = that.parents("tr").eq(0),
            id  = row.data('itemid');
        row_loading(row, 'show');

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, {
            'action'        : 'psp_tiny_compress',
            'id'            : id,
            'debug_level'   : debug_level
        }, function(response) {

            var respEl = $('#psp-smushit-resp-'+id);
            respEl.html( response.data );

            /*inAction = 0;
            $('a.psp-smushit-action').unbind('click', false).removeClass('disabled');*/

            if( response.status == 'valid' ) {
                respEl.removeClass('psp-info psp-error').addClass('psp-success');
            } else {
                respEl.removeClass('psp-info psp-success').addClass('psp-error');
            }

            row_loading(row, 'hide');
            if( typeof callback == 'function' ){
                callback();
            }

        }, 'json');
    }
    
    function tailCheckPages()
    {
        if( selected_element.length > 0 ){
            var curr_element = selected_element[0];
            smushit( curr_element.find('.psp-do_item_smushit'), function(){
                selected_element.splice(0, 1);
                
                tailCheckPages();
            });
        }
    }
    
    function massSmushit()
    {
        // reset this array for be sure
        selected_element = [];
        // find all selected items 
        maincontainer.find('.psp-item-checkbox:checked').each(function(){
            var that = $(this),
                row = that.parents('tr').eq(0);
            selected_element.push( row );
        });
        
        tailCheckPages();
    }

    function triggers()
    {
        // smushit action - per row
        /*$("a.psp-smushit-action").click(function (e) {
            e.preventDefault();

            if ( inAction == 1 ) return false;
            $('a.psp-smushit-action').bind('click', false).addClass('disabled');
            inAction = 1;

            var that    = $(this), row = that.parent(),
            itemid  = that.data('itemid');

            smushit( row );
        });*/

        maincontainer.on('click', '.psp-do_item_smushit', function(e){
            e.preventDefault();

            smushit( $(this) );
        });

        maincontainer.on('click', '#psp-do_mass_smushit', function(e){
            e.preventDefault();
            
            massSmushit( $(this) );
        });
        
        // smushit bulk action
        //$('select[name^="action"] option:last-child').before('<option value="psp_smushit_bulk">PSP Smushit bulk</option>');
    }

    // external usage
    return {
    }
})(jQuery);