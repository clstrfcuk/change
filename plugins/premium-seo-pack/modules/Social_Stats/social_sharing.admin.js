/*
Document   :  Social Sharing Admin
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspSocialSharingAdmin = (function ($) {
"use strict";

// public
var debug_level = 0;
var maincontainer = null;
var loading = null;
var socialBox = null;
var socialBoxResp = null;
var IDs = [];
var loaded_page = 0;
var tblPrev = null;

/*var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';*/

// init function, autoload
(function init() {
	// load the triggers
	$(document).ready(function(){
		maincontainer = $("#psp-wrapper")
		loading = maincontainer.find("#main-loading");
		socialBox = maincontainer.find('#psp-ajax-response');
		socialBoxResp = socialBox.find('#psp-socialsharing-ajax');

		triggers();
	});
})();

function ajaxLoading()
{
	var loading = $('<div id="psp-ajaxLoadingBox" class="psp-panel-widget">loading</div>');
	// append loading
	socialBoxResp.html(loading);
}

function get_options( tbl ) {
	ajaxLoading();

	//var theTrigger = socialBox.find('#toolbar'), theTriggerVal = theTrigger.val();
	var theTrigger = tbl, theTriggerVal = tbl.attr('id').replace('tab-item-', '');

	if ( $.inArray(theTriggerVal, ['none', 'no']) > -1 ) {
		socialBoxResp.html('').hide();
		return false;
	}

	tblPrev.parent().removeClass('active');
	theTrigger.parent().addClass('active');

	$.post(ajaxurl, {
		'action' 		: 'pspSocialSharing',
		'sub_action'	: 'getToolbarOptions',
		'toolbar'		: theTriggerVal //socialBox.find('#toolbar').val()
	}, function(response) {

		if ( response.status == 'valid' ) {
			socialBoxResp.html( response.html ).show();

			socialBoxResp.find('input#box_id, input#box_nonce').remove();
			socialBoxResp.find('#psp-status-box').remove();
			
			// special case! - design fix
			$('select#content_vertical-exclude-categ,select#content_horizontal-exclude-categ,select#floating-exclude-categ').prev('.formNote').css({'width': '75%'});
			
			triggers_sortable();
			
			// color picker
			var $pickColor = socialBox.find('input.socialshare-color-picker'), __bkcolor = $pickColor.data('background_color');
			var pickColorOpt = { 
				eventName		: 'click',
				onSubmit		: function(hsb, hex, rgb, el) {
					$pickColor.val(hex);
					$pickColor.ColorPickerHide();
				},
				onBeforeShow	: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			};
			if ( typeof __bkcolor != 'undefined' && __bkcolor != null && __bkcolor != '' ) pickColorOpt.color = __bkcolor;
			$pickColor
			.ColorPicker( pickColorOpt )
			.bind('keyup', function(){
				$(this).ColorPickerSetColor( this.value );
			});

			return true;
		}
		return false;
	}, 'json');
}

function triggers_sortable()
{
	// sortable list buttons
	socialBox.find('.btn-available').sortable({
		placeholder: 'placeholder',
		connectWith: '.btn-sortable',
		forcePlaceholderSize: true,
		dropOnEmpty: true
	}).disableSelection();

	socialBox.find('.btn-selected').sortable({
		placeholder: 'placeholder',
		connectWith: '.btn-sortable',
		forcePlaceholderSize: true,
		dropOnEmpty: true
	});

	socialBox.find('.btn-selected').on('click', '.delete', function() {
		var $item = $(this).parent();
		socialBox.find('.btn-available').prepend( $item );
	});	
}

function triggers()
{
	var tblWrap = socialBox.find('ul.psp-socialshare-tbl-tabs'),
	tblSel = tblWrap.find('li.tab-item').first().find('> input[type=checkbox]');
	tblPrev = tblSel;

	get_options( tblSel );
	
	tblWrap.on('click', 'li.tab-item', function (e) {
		e.preventDefault();

		var $this = $(this), __sel = $this.find('> input[type=checkbox]');
		get_options( __sel );
		tblPrev = __sel;
	});
	
	/*socialBox.find('#toolbar').on('change', function (e) {
		e.preventDefault();

		get_options();
	});*/
	
	socialBox.on('click', '.psp-saveOptions', function(e) {

		// selected buttons for toolbar
		var $btnItems = socialBox.find('ul.btn-selected li.block'), btnList = [];
		$btnItems.each(function (i) {
			btnList.push( $(this).data('btn') );
		});
		socialBox.find('#btn-selected-list').val( btnList.join(',') );

		var currentTb = tblPrev.attr('id').replace('tab-item-', ''),
		currentTbStatus = socialBox.find('#'+currentTb+'-enabled').val();
		if ( currentTbStatus == 'yes' )
			tblPrev.prop('checked', true);
		else 
			tblPrev.prop('checked', false);

		/*
		// enabled toolbars
		var currentTbText = socialBox.find('#toolbar option:selected').text(), currentTb = socialBox.find('#toolbar').val(),
		currentTbStatus = socialBox.find('#'+currentTb+'-enabled').val(), currentTbList = [], toolbarsList = [];

		socialBox.find('.toolbars-enabled li').each(function (i) {
			currentTbList.push( $(this).data('tbtype') );
		});
		socialBox.find('#toolbar option').each(function (i) {
			var el = $(this), elVal = el.val(), elText = el.html();
			if ( elVal == currentTb ) {
				if ( currentTbStatus == 'yes' )
					toolbarsList.push( '<li data-tbtype="' + elVal + '">' + elText + '</li>' );
			} else {
				if ( $.inArray(elVal, currentTbList) != -1 ) {
					toolbarsList.push( '<li data-tbtype="' + elVal + '">' + elText + '</li>' );
				}
			}
		});
		socialBox.find('ul.toolbars-enabled').html( toolbarsList.join('') );
		*/
		
	});
}

// external usage
return {
}
})(jQuery);
