jQuery(document).ready(function($){

	var dt_builder_width = $('#dt_layout').width(),
		$dt_builder_add_links = $( '.dt_builder_controls a.dt_add_element' );
		dt_module_settings_clicked = false,
		dt_widget_settings_clicked = false,
		dt_hidden_editor_object = tinyMCEPreInit.mceInit['dtthemes_hidden_editor'];

	/* Settings panel start */
	
	/* Module options settings */
	$( 'body' ).delegate( 'span.dt_settings_arrow_module', 'click', function(){
		var $this_setting_link = $(this),
			$settings_window = $('#dt_active_module_settings'),
			$dt_active_module = $this_setting_link.closest('.dt_module');

		if ( dt_module_settings_clicked ) return false;
		else dt_module_settings_clicked = true;

		$('#dt_layout .dt_module').css( 'z-index', '1' );

		if ( $('#dt_modules').is(':hidden') ) $dt_builder_add_links.eq(0).trigger('click');

		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : 'dt_show_module_options',
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_module_class : $(this).closest('.dt_module').attr('class'),
				dt_modal_window : 0,
				dt_module_exact_name : $(this).closest('.dt_module').attr('data-placeholder'),
			},
			error: function( xhr, ajaxOptions, thrownError ){
				dt_module_settings_clicked = false;
			},
			success: function( data ){
									
				$dt_active_module.addClass('dt_active');

				$settings_window.html('');
				$settings_window.hide().append(data).slideDown();
				
				$settings_window.find('.html-active').removeClass('html-active').addClass('tmce-active');
				
				$('#dt_module_separator').show();
				
				$('html:not(:animated),body:not(:animated)').animate({ scrollTop: $('#dt_page_builder').offset().top - 100 }, 500);

				dt_deactivate_ui_actions();
				dt_module_settings_clicked = false;
	
				$( '#dt_module_settings .dtthemes_option' ).each( function(){
					var $this_option = $(this),
						this_option_id = $this_option.attr('id'),
						$found_element = $dt_active_module.find('.dt_module_settings .dt_module_setting.' + this_option_id);
					
					if ( $this_option.is('select') ){
						if($found_element.html() != undefined && $found_element.html() != '') {
							var opts = $found_element.html().split(',');
							if(opts.length > 1) {
								for(var i = 0; i <= opts.length; i++) {
									$this_option.find("option[value='" + opts[i] + "']").attr('selected','selected');
								}
							} else {
								$this_option.find("option[value='" + $found_element.html() + "']").attr('selected','selected');
							}
						}
					} else if ( $this_option.is('input[type=checkbox]') ){
						if($found_element.html() == undefined || $found_element.html() == '')
							$this_option.removeAttr('checked');
						else
							$this_option.attr('value', $found_element.html()).attr('checked','checked');
					} else if($(this).hasClass('color-field')) {
						$this_option.val( $found_element.html() );
					} else if ( $this_option.is('input') ){
						$this_option.val( $found_element.html() );
					} else if ( $this_option.hasClass('dtthemes_wp_editor') ) {
						$this_option.html( '' );
					} else {
						$this_option.html( $found_element.html() );
					}
					
					 if($(this).hasClass('color-field')) {
							$(this).wpColorPicker();
					 }
					 
					if ( $this_option.hasClass('dtthemes_wp_editor') && typeof tinyMCE != undefined ) {
						tinyMCE.execCommand( "mceAddEditor", true, this_option_id );
						dt_init_new_editor( this_option_id );
						tinyMCE.get( this_option_id ).execCommand( "mceInsertContent", false, $found_element.html() );
					}

				});
				
			}
		});
	} );


	/* Widget options settings */
	$( 'body' ).delegate( 'span.dt_settings_arrow_widget', 'click', function(){
		var $this_setting_link = $(this),
			$settings_window = $('#dt_active_module_settings'),
			$dt_active_module = $this_setting_link.closest('.dt_module');

		if ( dt_widget_settings_clicked ) return false;
		else dt_widget_settings_clicked = true;

		$('#dt_layout .dt_module').css( 'z-index', '1' );

		if ( $('#dt_modules').is(':hidden') ) $dt_builder_add_links.eq(0).trigger('click');
		
		$('html:not(:animated),body:not(:animated)').animate({ scrollTop: $('#dt_page_builder').offset().top - 100 }, 500);

		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : 'dt_show_widget_options',
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_modal_window : 0,
				dt_module_class : $(this).closest('.dt_module').attr('class'),
				dt_module_exact_name : $(this).closest('.dt_module').attr('data-placeholder'),
			},
			error: function( xhr, ajaxOptions, thrownError ){
				dt_widget_settings_clicked = false;
			},
			success: function( data ){
				
				$dt_active_module.addClass('dt_active');

				$settings_window.html('');
				$settings_window.hide().append(data).slideDown();
				
				$settings_window.find('.html-active').removeClass('html-active').addClass('tmce-active');
				
				$('#dt_module_separator').show();

				$('#dt_layout .dt_module:not(.dt_active,.dt_m_column)').css('opacity',0.5);

				dt_deactivate_ui_actions();
				dt_widget_settings_clicked = false;

				$( '#dt_widget_settings .dtthemes_widget_attr' ).each( function(){
					
					var $this_option = $(this),
						this_option_id = $this_option.attr('id'),
						$found_element = $dt_active_module.find('.dt_module_settings .dt_module_setting.' + this_option_id),
						$found_element_id = $dt_active_module.find('.dt_module_settings .dt_module_setting.' + this_option_id).attr('data-option_id');

					if ( $this_option.is('select') ){
						if($found_element.html() != undefined && $found_element.html() != '') {
							var opts = $found_element.html().split(',');
							if(opts.length > 1) {
								for(var i = 0; i <= opts.length; i++) {
									$this_option.find("option[value='" + opts[i] + "']").attr('selected','selected');
								}
							} else {
								if($this_option.find("option:contains('" + $found_element.html() + "')").length != 0) {
									$this_option.find("option:contains('" + $found_element.html() + "')").attr('selected','selected');
								} else {
									$this_option.find("option[value='" + $found_element.html() + "']").attr('selected','selected');
								}
							}
						}
					} else if ( $this_option.is('input[type=checkbox]') ){
						if($this_option.attr('id') == $found_element_id) { $this_option.attr('checked','checked'); }
						else { $this_option.removeAttr('checked'); }
					} else if ( $this_option.is('input') ){
						$this_option.val( $found_element.html() );
					} else if ( $this_option.is('textarea') ){
						$this_option.val( $found_element.html() );
					} else {
						$this_option.html( $found_element.html() );
					}

				} );


			}
		});
	} );


	/* Column options settings */
	$( 'body' ).delegate( '.dt_settings_arrow_column', 'click', function(){
		var dt_action = 'dt_show_columoptions_panel', this_column = $(this), dt_column_css = dt_column_animation_type = dt_column_animation_delay = '';
		$(this).closest('.dt_m_column').addClass('dt_active_column');
		var dt_data = $(this).closest('.dt_m_column').find('.dt_columndata_settings').html();
		if(dt_data != '') {
			dt_column_css = $(dt_data).attr('data-column_css');
			dt_column_animation_type = $(dt_data).attr('data-column_animation_type');
			dt_column_animation_delay = $(dt_data).attr('data-column_animation_delay');
		}
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : dt_action,
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_column_css : dt_column_css,
				dt_column_animation_type : dt_column_animation_type,
				dt_column_animation_delay : dt_column_animation_delay
			},
			success: function( data ){
				$('body').append( '<div id="dt_dialog_modal" style="position:absolute;">' + data + '</div> <div class="dt_modal_blocker"></div>' );
				$('#dt_dialog_modal').draggable( { 'handle' : 'div.dt_dialog_handle' } );
				$('html:not(:animated),body:not(:animated)').animate({ scrollTop: 0 }, 500);
			}
		});
	});

	/* Fullwidth section options settings */
	$( 'body' ).delegate( '.dt_settings_arrow_fullwidth', 'click', function(){
		var dt_action = 'dt_show_fullwidthsection_options', this_section = $(this);
		$(this).closest('.dt_fullwidth_section').addClass('dt_active_section');
		
		var dt_section_key = $(this).closest('.dt_fullwidth_section').attr('data-name');
		
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : dt_action,
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_section_key : dt_section_key,
			},
			success: function( data ){
				
				$('body').append( '<div id="dt_dialog_modal" style="position:absolute;">' + data + '</div> <div class="dt_modal_blocker"></div>' );
				$('#dt_dialog_modal').draggable( { 'handle' : 'div.dt_dialog_handle' } );
				
				var $dt_active_module = this_section.closest('.dt_module');
				
				$( '#dt_fullwidth_section_settings .dtthemes_fws_option' ).each( function(){
					var $this_option = $(this),
						this_option_id = $this_option.attr('id'),
						$found_element = $dt_active_module.find('.dt_fullwidthsection_data_settings .dt_module_setting.' + this_option_id);
					
					if ( $this_option.is('select') ){
						if($found_element.html() != undefined && $found_element.html() != '') {
							var opts = $found_element.html().split(',');
							if(opts.length > 1) {
								for(var i = 0; i <= opts.length; i++) {
									$this_option.find("option[value='" + opts[i] + "']").attr('selected','selected');
								}
							} else {
								$this_option.find("option[value='" + $found_element.html() + "']").attr('selected','selected');
							}
						}
					} else if ( $this_option.is('input[type=checkbox]') ){
						if($found_element.html() == undefined || $found_element.html() == '')
							$this_option.removeAttr('checked');
						else
							$this_option.attr('value', $found_element.html()).attr('checked','checked');
					} else if ( $this_option.is('input') ){
						$this_option.val( $found_element.html() );
					} else if ( $this_option.hasClass('dtthemes_wp_editor') ) {
						$this_option.html( '' );
					} else {
						$this_option.html( $found_element.html() );
					}
					
					if ( $this_option.hasClass('dtthemes_wp_editor') && typeof tinyMCE != undefined ) {
						tinyMCE.execCommand( "mceAddEditor", true, this_option_id );
						dt_init_new_editor( this_option_id );
						tinyMCE.get( this_option_id ).execCommand( "mceInsertContent", false, $found_element.html() );
					}

				});
				
				$('html:not(:animated),body:not(:animated)').animate({ scrollTop: 0 }, 500);
				
			}
		});
	});

	/* Custom CSS options settings */
	$( 'span#dt_add_customcss' ).click(function(){
		var dt_action = 'dt_show_customcss_panel';
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : dt_action,
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_post_id : $('input#post_ID').val()
			},
			success: function( data ){
				$('body').append( '<div id="dt_dialog_modal" style="position:absolute;">' + data + '</div> <div class="dt_modal_blocker"></div>' );
				$('#dt_dialog_modal').draggable( { 'handle' : 'div.dt_dialog_handle' } );
				$('html:not(:animated),body:not(:animated)').animate({ scrollTop: 0 }, 500);
			}
		});
	});

	/* Settings panel end */

	/* Show / Hide module settings */
	$( 'body' ).delegate( '.dt_module .dt_showorhide', 'click', function(){
		if($(this).attr('title') == 'Show') {
			$(this).attr('title', 'Hide');
			$(this).removeClass('dt_show');
			$(this).addClass('dt_hide');
			$(this).closest('.dt_module').css('opacity',0.4);
			$(this).parents('.dt_module_options').find('.dt_settings_arrow_module, .dt_clone_module, .dt_delete, .dt_move, .dt_settings_arrow_widget').css('display','none');
		} else if($(this).attr('title') == 'Hide') {
			$(this).attr('title', 'Show');
			$(this).removeClass('dt_hide');
			$(this).addClass('dt_show');
			$(this).closest('.dt_module').css('opacity',1);
			$(this).parents('.dt_module_options').find('.dt_settings_arrow_module, .dt_clone_module, .dt_delete, .dt_move, .dt_settings_arrow_widget').css('display','block');
		}
		dt_layout_save( false );
	});

	
	/* Show modules in popup window itself */
	$( 'body' ).delegate( '#dt_popup_modules a.dt_add_element', 'click', function(){
		
		var $dt_clicked_link = $(this),
			$dt_popup_container = $('#dt_popup_modules'),
			open_modules_window = false;

		if ( $dt_clicked_link.hasClass('dt_active') ) return false;
		
		$dt_popup_container.find('.dt_module').css( { 'opacity' : 0, 'display' : 'none' } );

		modid = $dt_clicked_link.attr('class').replace('dt_add_element dt_add_','');
		if($dt_popup_container.find('.dt_module.dt_module_' + modid).length > 0)
			$dt_popup_container.find('.dt_module.dt_module_' + modid).css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
		else
			if($dt_popup_container.find('.dt_module.dt_' + modid).length > 0)
				$dt_popup_container.find('.dt_module.dt_' + modid).css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
			else if(modid == 'column')
				$dt_popup_container.find('.dt_module.dt_m_' + modid).css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
		
		if ( $dt_popup_container.is(':hidden') || open_modules_window ) {
			$dt_popup_container.slideDown(700);
		}

		$('.dt_add_element').removeClass('dt_active');
		$dt_clicked_link.addClass('dt_active');

		return false;

	});
		
	$( 'body' ).delegate( 'div#dt_popup_module', 'click', function(){		
		get_module_data($(this), 'popup', $(this), '');		
	});
	
	/* Save panel datas start */
	
	/* To save module(shortcode) options */
	$( 'body' ).delegate( 'form#dt_module_settings span#dt_save_data, #dt_close_module_settings', 'click', function(){
		var $dt_active_module_settings = $('.dt_active .dt_module_settings'), $dt_preview_module_settings = $('.dt_active .dt_preview'), show_data = true;
		$dt_active_module_settings.empty();

		$('form#dt_module_settings .dtthemes_option').each( function(){
			var dt_option_value, dt_option_class,
				this_option_id = $(this).attr('id'), show_data = true;

			dt_option_class = this_option_id + ' dt_module_setting';

			if ( $(this).hasClass('dtthemes_wp_editor') ){
				if ( typeof tinyMCE != undefined ){
					dt_option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
					tinyMCE.execCommand("mceRemoveEditor", false, this_option_id);
				} else {
					dt_option_value = $(this).val();
				}
			}
			else if ( $(this).is('input[type=checkbox]')) {
				if($(this).attr('checked') == 'checked')
					dt_option_value = $(this).val();
				else
					show_data = false;
			}
			else if ( $(this).is('input') && $(this).hasClass('color-field') ) {
				dt_option_value = $(this).val();
			}
			else if ( $(this).is('select, input') ) {
				dt_option_value = $(this).val();
			}
			else {
				dt_option_value = $(this).html();
			}

			if ( $(this).hasClass('dtthemes_module_content') ) dt_option_class += ' dtthemes_module_content';
			
			if(show_data)
				$dt_active_module_settings.append( '<div data-option_name="' + this_option_id + '" class="' + dt_option_class + '">' + dt_option_value + '</div>' );
			
			if($('#dt_saved_module_name').val().indexOf("doshortcode") < 0) {		
				if(this_option_id == 'title' || this_option_id.indexOf("title") >= 0) {
					$dt_preview_module_settings.html('<span class="title">' + dt_option_value + '</span>');
				}
			}

		} );

		$( '#dt_layout .dt_module' ).removeClass('dt_active').css('opacity',1);

		$(this).closest('#dt_active_module_settings').slideUp().find('form#dt_module_settings').remove();
		$('#dt_module_separator').hide();

		$('#dt_layout').css( 'height', 'auto' );

		dt_reactivate_ui_actions();

		dt_layout_save( true );
		
		return false;
	} );
	
	
	/* To save widget options */
	$( 'body' ).delegate( 'form#dt_widget_settings span#dt_save_data, #dt_close_widget_settings', 'click', function(){
		var $dt_active_module_settings = $('.dt_active .dt_module_settings'), 
			$dt_preview_module_settings = $('.dt_active .dt_preview');
		
		$dt_active_module_settings.empty();
	
		$('form#dt_widget_settings .dtthemes_widget_attr').each( function(){
			var dt_option_value, dt_option_class,
				this_option_id = $(this).attr('id'), 
				this_option_class = $(this).attr('class'), 
				this_wp_name = $('form#dt_widget_settings #dt_widget_module_optionname').val(),
				show_data = true;
			
			var this_option_name = '';	
			if(this_option_id != undefined) {
				if(this_option_id.indexOf("--") >= 0) {
					var arr = this_option_id.split('--');
					this_option_name = arr[1];
				} else {
					var wpt = this_wp_name + '-';
					var arr = this_option_id.split(wpt);
					var arr = arr[1];
					
					if(arr.substr(arr.length - 1) == '-') {
						opt_str = arr.substr(0, arr.length - 1);
					}
					opt_str = opt_str.replace('-', '_');
					this_option_name = opt_str;
				}
			}

			dt_option_class = this_option_id + ' dt_module_setting';
	
			if ( $(this).is('input[type=checkbox]')) {
				if($(this).attr('checked') == 'checked') {
					dt_option_value = 1;
				} else {
					show_data = false;
				}
			}
			else if ( $(this).is('select, input') ) {
				dt_option_value = $(this).val();
			}
			else if ( $(this).is('textarea') ) {
				dt_option_value = $(this).val();
			}
			
			if(dt_option_value == undefined) dt_option_value = '';
			
			if(show_data)
				$dt_active_module_settings.append( '<div data-option_id="' + this_option_id + '" data-option_name="' + this_option_name + '" class="' + dt_option_class + '">' + dt_option_value + '</div>' );
				
			if(this_option_id != undefined) {	
				if(this_option_id == 'title' || this_option_id.indexOf("title") >= 0) {
					$dt_preview_module_settings.html('<span class="title">' + dt_option_value + '</span>');
				}
			}
	
		} );
	
		$( '#dt_layout .dt_module' ).removeClass('dt_active').css('opacity',1);
	
		$(this).closest('#dt_active_module_settings').slideUp().find('form#dt_widget_settings').remove();
		$('#dt_module_separator').hide();
	
		$('#dt_layout').css( 'height', 'auto' );
	
		dt_reactivate_ui_actions();
	
		dt_layout_save( true );
	
		return false;
	} );

	
	/* To save column options */
	$( 'body' ).delegate( 'form#dt_column_settings span#save_columnoptions', 'click', function(){
		
		var column_css = column_animation_type = column_animation_delay = '', $save_message = $("#dtthemes_ajax_save");
		
		$('.dt_active_column .dt_columndata_settings').html('');
		$save_message.children("img").css("display","block");
		$save_message.children("span").css("margin","6px 0px 0px 30px").html( dtthemes_options.saving_text );
		$save_message.fadeIn('fast');
		
		if($('#dt_column_settings #dt_column_css').val() != '' && $('#dt_column_settings #dt_column_css').val() != undefined)
			column_css = ' data-column_css="' + $('#dt_column_settings #dt_column_css').val() + '"';
		if($('#dt_column_settings #dt_column_animation_type').val() != '' && $('#dt_column_settings #dt_column_animation_type').val() != undefined)
			column_animation_type = ' data-column_animation_type="' + $('#dt_column_settings #dt_column_animation_type').val() + '"';
		if($('#dt_column_settings #dt_column_animation_delay').val() != '' && $('#dt_column_settings #dt_column_animation_delay').val() != undefined)
			column_animation_delay = ' data-column_animation_delay="' + $('#dt_column_settings #dt_column_animation_delay').val() + '"';

		$('.dt_active_column .dt_columndata_settings').append( '<div id="column_data" ' + column_css + column_animation_type + column_animation_delay + '></div>' );
		$('.dt_m_column').removeClass('dt_active_column');
		
		dt_layout_save( false );
		
		$('#dt_close_column_settings').trigger('click');
		$save_message.children("img").css("display","none");
		$save_message.children("span").css("margin","0px").html( dtthemes_options.columnoptions_saved_text );
		setTimeout(function(){
			$save_message.fadeOut("slow");
		},600);

	});
	
	/* To save fullwidth section options */
	$( 'body' ).delegate( 'form#dt_fullwidth_section_settings span#save_fullwidthoptions', 'click', function(){
		
		var $save_message = $("#dtthemes_ajax_save");
		$save_message.children("img").css("display","block");
		$save_message.children("span").css("margin","6px 0px 0px 30px").html( dtthemes_options.saving_text );
		$save_message.fadeIn('fast');
		
		$('.dt_active_section .dt_fullwidthsection_data_settings').html('');
		
		$('form#dt_fullwidth_section_settings .dtthemes_fws_option').each( function(){
			var this_option_id = $(this).attr('id');
			var dt_option_class = this_option_id + ' dt_module_setting';
			
			if ( $(this).hasClass('dtthemes_wp_editor') ){
				if ( typeof tinyMCE != undefined ){
					dt_option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
					tinyMCE.execCommand("mceRemoveEditor", false, this_option_id);
				} else {
					dt_option_value = $(this).val();
				}
			}
			else if ( $(this).is('input[type=checkbox]')) {
				if($(this).attr('checked') == 'checked')
					dt_option_value = $(this).val();
				else
					show_data = false;
			}
			else if ( $(this).is('input') && $(this).hasClass('dt-color-field') ) {
				dt_option_value = $(this).val();
			}
			else if ( $(this).is('select, input') ) {
				dt_option_value = $(this).val();
			}
			else {
				dt_option_value = $(this).html();
			}

			$('.dt_active_section .dt_fullwidthsection_data_settings').append( '<div data-option_name="' + this_option_id + '" class="' + dt_option_class + '">' + dt_option_value + '</div>' );
			
		} );

		$('.dt_fullwidth_section').removeClass('dt_active_section');
		
		dt_layout_save( false );
				
		$('#dt_close_sections_settings').trigger('click');
		$save_message.children("img").css("display","none");
		$save_message.children("span").css("margin","0px").html( dtthemes_options.sectionoptions_saved_text );
		setTimeout(function(){
			$save_message.fadeOut("slow");
		},600);

	});

	
	/* To save custom css options */
	$( 'body' ).delegate( 'form#dt_customcss_settings span#save_customcss', 'click', function(){	
		var dt_action = 'dt_save_customcss_data', $save_message = $("#dtthemes_ajax_save");
		
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : dt_action,
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_post_id : $('input#post_ID').val(),
				dt_customcss_data : $('#dt_customcss_data').val()
			},
			beforeSend: function ( xhr ){
				$save_message.children("img").css("display","block");
				$save_message.children("span").css("margin","6px 0px 0px 30px").html( dtthemes_options.saving_text );
				$save_message.fadeIn('fast');
			},
			success: function( data ){
				$('#dt_close_customcss_settings').trigger('click');
				$save_message.children("img").css("display","none");
				$save_message.children("span").css("margin","0px").html( dtthemes_options.customcss_saved_text );
				setTimeout(function(){
					$save_message.fadeOut("slow");
				},600);
			}
		});
	});
	

	/* To load modal dialog datas into text editor */
	$( 'body' ).delegate( 'form#dt_dialog_settings span#dt_save_data', 'click', function(){
		var $dt_dialog_form = $('form#dt_dialog_settings'),
			dt_module_type = $dt_dialog_form.find('input#dt_module_type').val(),
			dt_current_module_name = 'dt_sc_' + $dt_dialog_form.find('input#dt_saved_module_name').val(),
			dt_shortcode_text, dt_shortcode_content = '',
			advanced_option = false,
			editor_id = $dt_dialog_form.find('input#dt_paste_to_editor_id').val(),
			$current_textarea,
			current_textarea_value;

		if(dt_current_module_name.indexOf("doshortcode") >= 0 ) disable_sc_wrapper = true;
		else disable_sc_wrapper = false;
		
		if($dt_dialog_form.find('input#dt_module_type').val() == 'column')
			dt_current_module_name = dt_current_module_name + '_inner';
		
		if(dt_module_type != 'widget') {
			
			if($dt_dialog_form.find('input#dt_disable_resize').val() != '') {
				
				dt_shortcode_text = '[' + dt_current_module_name + ' /]';
				
			} else {
			
				dt_shortcode_text = '[' + dt_current_module_name;
	
				$dt_dialog_form.find('.dtthemes_option').each( function(){
					var dt_option_value,
						this_option_id = $(this).attr('id'),
						shortcode_option_id = this_option_id.replace('dt_dialog_','');
	
						if ( $(this).hasClass('dtthemes_wp_editor') ){
							if ( typeof tinyMCE != undefined ){
								dt_option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
								tinyMCE.execCommand("mceRemoveEditor", false, this_option_id);
							} else {
								dt_option_value = $('#' + this_option_id).val();
							}
						}
						else if ( $(this).is(':checkbox') ){
							dt_option_value = ( $(this).is(':checked') ) ? 'first' : '';
						}
						else if ( $(this).is('select, input') ) {
							dt_option_value = $(this).val();
						}
	
						if ( $(this).hasClass('dtthemes_module_content') ) {
							dt_shortcode_content = dt_option_value;
						} else {
							if(shortcode_option_id == 'first')
								dt_shortcode_text += ' ' + dt_option_value + ' ';
							else
								dt_shortcode_text += ' ' + shortcode_option_id + '="' + dt_option_value + '"';
						}
	
				} );
				
				if ( ! advanced_option ) dt_shortcode_text += ']' + dt_shortcode_content + '[/' + dt_current_module_name + ']';
				else dt_shortcode_text += '[/' + dt_current_module_name + ']';
				
				if(disable_sc_wrapper == true) dt_shortcode_text = dt_shortcode_content;
			
			}
		
		} else {
			
			dt_shortcode_text = '[dt_sc_widgets';

			dt_shortcode_text += ' widget_name="' + $dt_dialog_form.find('input#dt_widget_module_name').val() + '"';
			dt_shortcode_text += ' widget_wpname="' + $dt_dialog_form.find('input#dt_widget_module_wpname').val() + '"';
			dt_shortcode_text += ' widget_wpid="' + $dt_dialog_form.find('input#dt_widget_module_wpid').val() + '"';
		
			$dt_dialog_form.find('.dtthemes_widget_attr').each( function(){
				
				dt_option_value = '';
				dt_current_module_name =  $(this).attr('id');
				var this_option_id = $(this).attr('id'), 
					this_option_class = $(this).attr('class');
	
				if(this_option_id.indexOf("--") >= 0) {
					var arr = this_option_id.split('--');
					this_option_name = arr[1];
				}

				if ( $(this).is('input[type=checkbox]')) {
					if($(this).attr('checked') == 'checked')	{
						dt_option_value = this_option_name;
					} else {
						show_data = false;
					}
				}
				else if ( $(this).is('select, input') ) {
					dt_option_value = $(this).val();
				}
				else if ( $(this).is('textarea') ) {
					dt_option_value = $(this).val();
				}
				
				if(dt_option_value != '')
					dt_shortcode_text +=  ' ' + this_option_name + '="' + dt_option_value + '" ';
		
	
			} );
			
			dt_shortcode_content = '';
			
			dt_shortcode_text += '][/dt_sc_widgets]';
		
		}
	
		if ( typeof tinyMCE != undefined ){
			switchEditors.go(editor_id,'tmce');
			tinyMCE.get( editor_id ).execCommand("mceInsertContent", false, dt_shortcode_text);
		} else {
			$current_textarea 		= $('#dt_module_settings ' + '#' + editor_id);
			current_textarea_value 	= $current_textarea.val();
			$current_textarea.val( current_textarea_value + dt_shortcode_text );
		}

		dt_close_modal_window();

		return false;
	} );

	/* Save panel datas end */


	/* Close settings panel start */

	/* To close popup modules panel */
	$( 'body' ).delegate( '#dt_close_popup_modules', 'click', function(){
		var $dt_dialog_form = $('form#dt_popup_modules');
		dt_close_modal_window();
		return false;
	});
	
	/* To close column settings panel */
	$( 'body' ).delegate( '#dt_close_column_settings', 'click', function(){
		var $dt_dialog_form = $('form#dt_column_settings');
		dt_close_modal_window();
		return false;
	});

	/* To close column settings panel */
	$( 'body' ).delegate( '#dt_close_sections_settings', 'click', function(){
		var $dt_dialog_form = $('form#dt_fullwidth_section_settings');
		$('.dt_fullwidth_section').removeClass('dt_active_section');
		dt_close_modal_window();
		return false;
	});

	/* To close custom css settings panel */
	$( 'body' ).delegate( '#dt_close_customcss_settings', 'click', function(){
		var $dt_dialog_form = $('form#dt_customcss_settings');
		dt_close_modal_window();
		return false;
	});

	/* To close dialog settings panel */
	$( 'body' ).delegate( '#dt_close_dialog_settings', 'click', function(){
		var $dt_dialog_form = $('form#dt_dialog_settings');

		$dt_dialog_form.find('.dtthemes_wp_editor').each( function(){
			if ( typeof tinyMCE != undefined ) tinyMCE.execCommand("mceRemoveEditor", false, $(this).attr('id'));
		} );

		dt_close_modal_window();

		return false;
	});

	/* Close settings panel end */


	/* To clone column */
	$( 'body' ).delegate( 'span.dt_clone_column', 'click', function(){
		var dclass = $(this).closest('.dt_module').attr('class');
		var dname = $(this).closest('.dt_module').attr('data-name');
		var dplaceholder = $(this).closest('.dt_module').attr('data-placeholder');
		var dstyle = $(this).closest('.dt_module').attr('style');
		
		var new_html =  $(this).closest('.dt_module').html().replace('<div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div>','');
		var new_html =  new_html.replace('<div style="z-index: 90;" class="ui-resizable-handle ui-resizable-e"></div>','');
		
		var content = '<div class="' + dclass + '" data-name="' + dname + '" data-placeholder="' + dplaceholder + '" style="' + dstyle + '">' + new_html + '</div';
		$(content).insertAfter( $(this).closest(".dt_module") );
		
		dt_init_modules_js( 0 );
		dt_calculate_modules();
		dtthemes_bg_effects(200);
		dt_layout_save( false );
	} );

	/* To clone module */
	$( 'body' ).delegate( 'span.dt_clone_module', 'click', function(){
		var dclass = $(this).parents('.dt_module').attr('class');
		var dname = $(this).parents('.dt_module').attr('data-name');
		var dplaceholder = $(this).parents('.dt_module').attr('data-placeholder');
		var dstyle = $(this).parents('.dt_module').attr('style');
	    var dattr = dwpname = dwpid = '';
		
		if($(this).parents('.dt_module').attr('data-attr') != '' && $(this).parents('.dt_module').attr('data-attr') != undefined)
			dattr = " data-attr='" + $(this).parents('.dt_module').attr('data-attr') + "'";
		if($(this).parents('.dt_module').attr('data-wpname') != '' && $(this).parents('.dt_module').attr('data-wpname') != undefined)
			dwpname = " data-wpname='" + $(this).parents('.dt_module').attr('data-wpname') + "'";
		if($(this).parents('.dt_module').attr('data-wpid') != '' && $(this).parents('.dt_module').attr('data-wpid') != undefined)
			dwpid = " data-wpid='" + $(this).parents('.dt_module').attr('data-wpid') + "'";

		var new_html =  $(this).parents('.dt_module').html().replace('<div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div>','');
		var new_html =  new_html.replace('<div style="z-index: 90;" class="ui-resizable-handle ui-resizable-e"></div>','');

		var content = '<div class="' + dclass + '" data-name="' + dname + '" data-placeholder="' + dplaceholder + '" style="' + dstyle + '" ' + dattr + dwpname + dwpid + '>' + new_html + '</div';
		$(content).insertAfter( $(this).closest(".dt_module") );

		dt_init_modules_js( 0 );
		dt_calculate_modules();
		dtthemes_bg_effects(200);
		dt_layout_save( false );
	} );
	

	/* To delete column and module */
	$( 'body' ).delegate( 'span.dt_delete, span.dt_delete_column', 'click', function(){
		var $this_delete_button = $(this);

		if ( $this_delete_button.hasClass('dt_delete') || $this_delete_button.hasClass('dt_delete_column') ){
			if ( $this_delete_button.parent().find('.dtthemes_delete_confirmation').length ){
				$this_delete_button.parent().find('.dtthemes_delete_confirmation').remove();
			} else {
				$(this).parent().append( '<span class="dtthemes_delete_confirmation">' + '<span>' + dtthemes_options.confirm_message + '</span>' + '<a href="#" class="dtthemes_delete_confirm_yes">' + dtthemes_options.confirm_message_yes + '</a><a href="#" class="dtthemes_delete_confirm_no">' + dtthemes_options.confirm_message_no + '</a></span>' );
			}
			return false;
		}

		dtthemes_delete_module( $this_delete_button.closest('.dt_module') );
		dt_layout_save( false );
	} );


	$(document).on("keypress", "#dtthemes_new_layout_name", function(e) {
		// if the user hits enter, create new sample layout and make sure the form isn't submitted
		if ( e.which == 13 ) {
			$(this).siblings( '.dtthemes_delete_confirm_yes' ).trigger( 'click' );
			return false;
		}
	});


	/* Delete sample layout created by user */
	$( 'body' ).delegate( '.dt_user_layout_delete', 'click', function(){
		var $this_delete_button = $(this);

		if ( $this_delete_button.find('.dtthemes_delete_confirmation').length ){
			$this_delete_button.find('.dtthemes_delete_confirmation').remove();
		} else {
			$this_delete_button.append( '<span class="dtthemes_delete_confirmation">' + '<span>' + dtthemes_options.confirm_custom_layout_delete_message + '</span>' + '<a href="#" class="dtthemes_delete_confirm_yes">' + dtthemes_options.confirm_message_yes + '</a><a href="#" class="dtthemes_delete_confirm_no">' + dtthemes_options.confirm_message_no + '</a></span>' );
		}
		return false;
	} );


	/* When user clicks no do nothing */
	$( 'body' ).delegate( '.dtthemes_delete_confirm_no', 'click', function(){
		$(this).closest('.dtthemes_delete_confirmation').remove();
		return false;
	} );
	
	
	/* Delete module when user clicks yes */
	$( 'body' ).delegate( 'a.dtthemes_delete_confirm_yes', 'click', function(){
		var $this_button = $(this);

		if ( $this_button.closest('#dtthemes_clear_all_wrapper').length ){
			$('#dt_layout').html( '' );
			$('#dtthemes_helper').show();
			$this_button.closest('.dtthemes_delete_confirmation').remove();
			dt_layout_save( true );
		} else if ( $this_button.closest('.dt_sample_layout').length ) {
			$.ajax({
				type: "POST",
				url: dtthemes_options.ajaxurl,
				data:
				{
					action : 'dt_delete_sample_layout',
					dt_load_nonce : dtthemes_options.dt_load_nonce,
					dt_layout_key : $this_button.closest('.dt_sample_layout').attr('data-name')
				},
				success: function( data ){
					$this_button.closest('.dt_sample_layout').remove();
				}
			});
		} else if ( $this_button.closest('#dtthemes_create_layout_wrapper').length && $this_button.siblings('#dtthemes_new_layout_name').val() != '' ) {
			var layout_html = $('#dt_layout').html(),
				$save_message = jQuery("#dtthemes_ajax_save");
			$.ajax({
				type: "POST",
				url: dtthemes_options.ajaxurl,
				data:
				{
					action : 'dt_create_new_sample_layout',
					dt_load_nonce : dtthemes_options.dt_load_nonce,
					dt_layout_html : layout_html,
					dt_new_layout_name : $this_button.siblings('#dtthemes_new_layout_name').val()
				},
				beforeSend: function ( xhr ){
					$save_message.children("img").css("display","block");
					$save_message.children("span").css("margin","6px 0px 0px 30px").html( dtthemes_options.saving_text );
					$save_message.fadeIn('fast');
				},
				success: function( data ){
					$save_message.children("img").css("display","none");
					$save_message.children("span").css("margin","0px").html( dtthemes_options.layout_saved_text );

					setTimeout(function(){
						$save_message.fadeOut("slow");
					},500);

					$this_button.closest('.dtthemes_delete_confirmation').remove();
				}
			});
		} else {
			dtthemes_delete_module( $(this).closest('.dt_module') );
		}

		return false;
	} );
	

	/* To clear page builder canvas area */
	$( '#dtthemes_clear_all' ).click( function(){
		var $this_button = $(this);

		if ( $this_button.siblings('.dtthemes_delete_confirmation').length ){
			$this_button.siblings('.dtthemes_delete_confirmation').remove();
		} else {
			$this_button.closest('span#dtthemes_clear_all_wrapper').append( '<span class="dtthemes_delete_confirmation">' + '<span>' + dtthemes_options.confirm_clear_all_message + '</span>' + '<a href="#" class="dtthemes_delete_confirm_yes">' + dtthemes_options.confirm_message_yes + '</a><a href="#" class="dtthemes_delete_confirm_no">' + dtthemes_options.confirm_message_no + '</a></span>' );
		}

		return false;
	} );

	
	/* To create sample layout */
	$( '#dtthemes_create_layout' ).click( function(){
		var $this_button = $(this);

		if ( $this_button.siblings('.dtthemes_delete_confirmation').length ){
			$this_button.siblings('.dtthemes_delete_confirmation').remove();
		} else {
			$this_button.closest('span#dtthemes_create_layout_wrapper').append( '<span class="dtthemes_delete_confirmation">' + '<label for="dtthemes_new_layout_name">' + dtthemes_options.create_layout_name + ':</label>' + '<input type="text" value="" id="dtthemes_new_layout_name" name="dtthemes_new_layout_name" />' + '<small>' + dtthemes_options.create_layout_description_text + '</small>' + '<a href="#" class="dtthemes_delete_confirm_yes">' + dtthemes_options.create_layout_confirm_message_yes + '</a><a href="#" class="dtthemes_delete_confirm_no">' + dtthemes_options.create_layout_confirm_message_no + '</a></span>' );
		}

		return false;
	} );


	// To add modules or columns to layout by clicking
	$( 'body' ).delegate( '#dt_modules .dt_module', 'click', function(){
		if(!$(this).hasClass('disable_onclick')) {
			if($(this).hasClass('dt_sample_layout')) {
				dtthemes_append_sample_layout($(this));	
			} else {
				get_module_data($(this), 'layout', $('#dt_layout'), 'yes');
				$('#dtthemes_helper').hide();
			}
			dtthemes_bg_effects(1000);
		}
	});
	
	/* Functions */

    /* Page builder dashboard resize */
	$(window).resize( function(){
		dt_calculate_modules();
	} );
	
	$(window).load( function(){
		dt_calculate_modules();
	} );
	
	/* Function to switch between Visual / Text in editor */
	function dt_init_new_editor( editor_id ){
		if ( typeof tinyMCEPreInit.mceInit[editor_id] != undefined ) {
			quicktags( { id : editor_id, buttons:"strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close" } );
			return;
		}
	}

	/* Function to add effect once modules added */
	function dtthemes_bg_effects(delay){
		$('.dt_layout_highlighter').fadeIn('fast').delay(delay).fadeOut('fast');
	}

	/* Function to delete module */
	function dtthemes_delete_module( $module ){
		$module.remove();
		dt_init_modules_js( 0 );
		dt_layout_save( false );
	}

	/* To generate shortcode for created layout */
	function dtthemes_generate_layout_shortcode( html_element ){

		var shortcode_output = '';
		html_element.find( ' > .dt_module' ).each( function(){
			var $this_module = $(this),
				module_content = '';
	
			if($this_module.hasClass('dt_fullwidth_section')) {	

				var module_name = 'dt_sc_' + $this_module.attr('data-name');
				shortcode_output += '[' + module_name;
				
				$this_module.find('.dt_fullwidthsection_data_settings .dt_module_setting').each( function(){
					var $this_option = $(this),
						option_name = $this_option.attr('data-option_name'),
						option_value = $this_option.html();							
					shortcode_output += ' ' + option_name + '="' + option_value + '"';
				});
				
				if ( $this_module.hasClass('dt_first') && module_name != 'dt_sc_doshortcode' ) shortcode_output += ' first]'; else shortcode_output += ' ]';
				
				$this_module.find('.dt_fullwidth_section_container > .dt_module').each( function() {
					
					var $current_module = $(this);
					
					if($current_module.hasClass('dt_m_column')) {
						
						var module_name = 'dt_sc_' + $current_module.attr('data-name');
						shortcode_output += '[' + module_name;
										
						if($current_module.find('#column_data').attr('data-column_css') != undefined && $current_module.find('#column_data').attr('data-column_css') != '')			
							shortcode_output += ' class="' + $current_module.find('#column_data').attr('data-column_css') + '"';
						if($current_module.find('#column_data').attr('data-column_animation_type') != undefined && $current_module.find('#column_data').attr('data-column_animation_type') != 'None')			
							shortcode_output += ' animation="' + $current_module.find('#column_data').attr('data-column_animation_type') + '"';
						if($current_module.find('#column_data').attr('data-column_animation_delay') != undefined && $current_module.find('#column_data').attr('data-column_animation_delay') != '')			
							shortcode_output += ' animation_delay="' + $current_module.find('#column_data').attr('data-column_animation_delay') + '"';
												
						if ( $current_module.hasClass('dt_first') && module_name != 'dt_sc_doshortcode' ) shortcode_output += ' first]'; else shortcode_output += ' ]';
						
						$current_module.find('.dt_modules_container .dt_module').each( function() {
							if(!$(this).find('> .dt_module_options .dt_showorhide').hasClass('dt_hide')) {
								var shortcode = false;
								if($(this).hasClass('dt_m_widget')) {
									shortcode_output += '[dt_sc_widgets';
									shortcode_output += ' widget_name="' + $(this).attr('data-attr') + '"';
									shortcode_output += ' widget_wpname="' + $(this).attr('data-wpname') + '"';
									shortcode_output += ' widget_wpid="' + $(this).attr('data-wpid') + '"';
									shortcode = false;
								} else if ($(this).attr('data-name').indexOf("doshortcode") >= 0) {
									shortcode_output += '';
									shortcode = true;
								} else {
									var colmodule_name = 'dt_sc_' + $(this).attr('data-name');
									shortcode_output += '[' + colmodule_name;
									shortcode = false;
								}
								
								var module_content = '';
								$(this).find('> .dt_module_settings .dt_module_setting').each( function(){
									var $this_option = $(this),
										option_name = $this_option.attr('data-option_name'),
										option_value = $this_option.html();							
									
									if ( $(this).hasClass('dtthemes_module_content') ){
										if(shortcode == true)
											shortcode_output += option_value;
										else
											module_content = option_value;
									} else {
										shortcode_output += ' ' + option_name + '="' + option_value + '"';
									}
								
								});
								
								if ($(this).attr('data-name').indexOf("doshortcode") >= 0) {
									shortcode_output += '';
								} else if(module_content != '') {
									if($(this).hasClass('dt_m_widget')) {
										shortcode_output += ']' + module_content + '[/dt_sc_widgets]';
									} else {
										shortcode_output += ']' + module_content + '[/' + colmodule_name + ']' + '';
									}
								} else {
									shortcode_output += ' /]';
								}
							
							}
						});
						
						shortcode_output += '[/' + module_name + ']';					
						
						
					} else {
					
						if(!$current_module.find('> .dt_module_options .dt_showorhide').hasClass('dt_hide')) {
							
							if($current_module.hasClass('dt_disable_resize')) {
								
								var module_name = 'dt_sc_' + $current_module.attr('data-name');
								shortcode_output += '[' + module_name + ' /]';
								
							} else {
							
								if($current_module.hasClass('dt_m_widget')) {
									var module_name = 'dt_sc_widgets';
									shortcode_output += '[' + module_name;
								} else if ($current_module.attr('data-name').indexOf("doshortcode") >= 0) {
									var module_name = ''
									shortcode_output += '';
								} else {
									var module_name = 'dt_sc_' + $current_module.attr('data-name');
									shortcode_output += '[' + module_name;
								}
								
								
								if($current_module.hasClass('dt_m_widget')) {
									shortcode_output += ' widget_name="' + $current_module.attr('data-attr') + '"';
									shortcode_output += ' widget_wpname="' + $current_module.attr('data-wpname') + '"';
									shortcode_output += ' widget_wpid="' + $current_module.attr('data-wpid') + '"';
								}
								
								var module_content = '';
								$current_module.find('> .dt_module_settings .dt_module_setting').each( function(){
									var $this_option = $(this),
										option_name = $this_option.attr('data-option_name'),
										option_value = $this_option.html();							
									if ( $this_option.hasClass('dtthemes_module_content') ){
										module_content = option_value;
									} else {
										shortcode_output += ' ' + option_name + '="' + option_value + '"';
									}
					
								} );
				
								if ($current_module.attr('data-name').indexOf("doshortcode") >= 0) {
									shortcode_output += module_content + '';
								} else if(module_content != '') {
									shortcode_output += ']';	
									shortcode_output += module_content + '[/' + module_name + ']' + '';
								} else {
									shortcode_output += ' /]';
								}
								
							}
						
						}
						
					}
					
				});
				
				shortcode_output += '[/' + module_name + ']' + '';					
			
			} else if($this_module.hasClass('dt_m_column_resizable') || $this_module.hasClass('dt_m_column')) {
				
				var module_name = 'dt_sc_' + $this_module.attr('data-name');
				shortcode_output += '[' + module_name;
				
				var $this_module_width = $this_module.find('.dt_module_controls > .dt_module_name > .dt_module_width');
				if ( $this_module_width.length && $this_module_width.text() !== '' ) shortcode_output += ' width="' + parseInt( $this_module_width.text().replace(/[()]/g,'') ) + '"';
				if($this_module.find('#column_data').attr('data-column_css') != undefined && $this_module.find('#column_data').attr('data-column_css') != '')			
					shortcode_output += ' class="' + $this_module.find('#column_data').attr('data-column_css') + '"';
				if($this_module.find('#column_data').attr('data-column_animation_type') != undefined && $this_module.find('#column_data').attr('data-column_animation_type') != 'None')			
					shortcode_output += ' animation="' + $this_module.find('#column_data').attr('data-column_animation_type') + '"';
				if($this_module.find('#column_data').attr('data-column_animation_delay') != undefined && $this_module.find('#column_data').attr('data-column_animation_delay') != '')			
					shortcode_output += ' animation_delay="' + $this_module.find('#column_data').attr('data-column_animation_delay') + '"';
				
				if ( $this_module.hasClass('dt_first') && module_name != 'dt_sc_doshortcode' ) shortcode_output += ' first]'; else shortcode_output += ' ]';
				
				$this_module.find('.dt_modules_container .dt_module').each( function() {
					if(!$(this).find('> .dt_module_options .dt_showorhide').hasClass('dt_hide')) {
						
						if($(this).hasClass('dt_disable_resize')) {
							
							var module_name = 'dt_sc_' + $(this).attr('data-name');
							shortcode_output += '[' + module_name + ' /]';
							
						} else {
						
							var shortcode = false;
							if($(this).hasClass('dt_m_widget')) {
								shortcode_output += '[dt_sc_widgets';
								shortcode_output += ' widget_name="' + $(this).attr('data-attr') + '"';
								shortcode_output += ' widget_wpname="' + $(this).attr('data-wpname') + '"';
								shortcode_output += ' widget_wpid="' + $(this).attr('data-wpid') + '"';
								shortcode = false;
							} else if ($(this).attr('data-name').indexOf("doshortcode") >= 0) {
								shortcode_output += '';
								shortcode = true;
							} else {
								var colmodule_name = 'dt_sc_' + $(this).attr('data-name');
								shortcode_output += '[' + colmodule_name;
								shortcode = false;
							}
							
							var module_content = '';
							$(this).find('> .dt_module_settings .dt_module_setting').each( function(){
								var $this_option = $(this),
									option_name = $this_option.attr('data-option_name'),
									option_value = $this_option.html();							
								
								if ( $(this).hasClass('dtthemes_module_content') ){
									if(shortcode == true)
										shortcode_output += option_value;
									else
										module_content = option_value;
								} else {
									shortcode_output += ' ' + option_name + '="' + option_value + '"';
								}
							
							});
							
							if ($(this).attr('data-name').indexOf("doshortcode") >= 0) {
								shortcode_output += '';
							} else if(module_content != '') {
								if($(this).hasClass('dt_m_widget')) {
									shortcode_output += ']' + module_content + '[/dt_sc_widgets]';
								} else {
									shortcode_output += ']' + module_content + '[/' + colmodule_name + ']' + '';
								}
							} else {
								shortcode_output += ' /]';
							}
						
						}
						
					}
				});
				
				shortcode_output += '[/' + module_name + ']';					
					
			} else {
				
				if(!$this_module.find('> .dt_module_options .dt_showorhide').hasClass('dt_hide')) {
					
					if($this_module.hasClass('dt_disable_resize')) {
						
						var module_name = 'dt_sc_' + $this_module.attr('data-name');
						shortcode_output += '[' + module_name + ' /]';
						
					} else {
						
						var $this_module_width = $this_module.find('.dt_module_name > .dt_module_width');
						
						if ( $this_module_width.length && $this_module_width.text() !== '' ) {
							
							shortcode_output += '[dt_sc_doshortcode ';
							
							if ( $this_module_width.length && $this_module_width.text() !== '' ) shortcode_output += ' width="' + parseInt( $this_module_width.text().replace(/[()]/g,'') ) + '"';
							if ( $this_module.hasClass('dt_first') && module_name != 'dt_sc_doshortcode' ) shortcode_output += ' first';
							shortcode_output += '] ';
						
						}
						
						if($this_module.hasClass('dt_m_widget')) {
							var module_name = 'dt_sc_widgets';
							shortcode_output += '[' + module_name;
						} else if ($this_module.attr('data-name').indexOf("doshortcode") >= 0) {
							var module_name = ''
							shortcode_output += '';
						} else {
							var module_name = 'dt_sc_' + $this_module.attr('data-name');
							shortcode_output += '[' + module_name;
						}
						
						
						if($this_module.hasClass('dt_m_widget')) {
							shortcode_output += ' widget_name="' + $this_module.attr('data-attr') + '"';
							shortcode_output += ' widget_wpname="' + $this_module.attr('data-wpname') + '"';
							shortcode_output += ' widget_wpid="' + $this_module.attr('data-wpid') + '"';
						}
						
						var module_content = '';
						$this_module.find('> .dt_module_settings .dt_module_setting').each( function(){
							var $this_option = $(this),
								option_name = $this_option.attr('data-option_name'),
								option_value = $this_option.html();							
							if ( $this_option.hasClass('dtthemes_module_content') ){
								module_content = option_value;
							} else {
								if($this_module.hasClass('dt_m_WP_Widget_Text')) {
									shortcode_output += ' ' + option_name + "='" + option_value + "'";
								} else {
									shortcode_output += ' ' + option_name + '="' + option_value + '"';
								}
							}
			
						} );
		
						if ($this_module.attr('data-name').indexOf("doshortcode") >= 0) {
							shortcode_output += module_content + '';
						} else if(module_content != '') {
							shortcode_output += ']';	
							shortcode_output += module_content + '[/' + module_name + ']' + '';
						} else {
							shortcode_output += ' /]';
						}
						
						if ( $this_module_width.length && $this_module_width.text() !== '' ) {
							
							shortcode_output += '[/dt_sc_doshortcode]';
							
						}
					
					}
				
				}
				
			}
				
				
		} );
			
		return shortcode_output;
	}
	
	
	/* Funtion to close modal dialog window */
	function dt_close_modal_window(){
		$( 'div#dt_dialog_modal, div.dt_modal_blocker' ).remove();
		$('html:not(:animated),body:not(:animated)').animate({ scrollTop: $('#dt_page_builder').offset().top - 100 }, 500);
	}
	
	/* Funtion to make droppable editor */
	function dt_make_editor_droppable(){
		$( '#dt_active_module_settings .wp-editor-container' ).droppable({
			accept: ".dt_module",
			hoverClass: 'dt_editor_hover',
			greedy: true,
			drop: function( event, ui ) {
				if(ui.draggable.attr('class').indexOf("dt_fullwidth_section") >= 0 ) {
					return false;
				}
								
				var dt_paste_to_editor_id = $(this).find('.dtthemes_wp_editor').attr('id');
				
				if(ui.draggable.attr('class').indexOf("dt_m_widget") >= 0 )
					dt_action = 'dt_show_widget_options';
				else
					dt_action = 'dt_show_module_options';

				// don't allow inserting module into the same module
				if ( $('#dt_layout .dt_active').attr('data-placeholder') == ui.draggable.attr('data-placeholder') ) return;
				if ( dtthemes_options.enable_widget != 1 && ui.draggable.hasClass('dt_m_widget') ) return;
				if ( ui.draggable.hasClass('dt_sample_layout') ) return;
				
				if ( ui.draggable.hasClass('dt_m_column') ) dt_action = 'dt_show_column_options';

				$.ajax({
					type: "POST",
					url: dtthemes_options.ajaxurl,
					data:
					{
						action : dt_action,
						dt_load_nonce : dtthemes_options.dt_load_nonce,
						dt_module_class : ui.draggable.attr('class'),
						dt_modal_window : 1,
						dt_paste_to_editor_id : dt_paste_to_editor_id,
						dt_module_exact_name : ui.draggable.attr('data-placeholder'),
						dt_widget_id : ui.draggable.attr('data-placeholder')
					},
					success: function( data ){
						$('body').append( '<div id="dt_dialog_modal" style="position:absolute;">' + '<div class="dt_dialog_handle">Insert Shortcode</div>' + data + '</div> <div class="dt_modal_blocker"></div>' );
						$('#dt_dialog_modal').draggable( { 'handle' : 'div.dt_dialog_handle' } );

						$( '#dt_dialog_settings .dtthemes_option' ).each( function(){
							var $this_option = $(this),
								this_option_id = $this_option.attr('id');

							if ( $this_option.hasClass('dtthemes_wp_editor') && typeof tinyMCE != undefined ) {
								tinyMCE.execCommand( "mceAddEditor", true, this_option_id );
							}
						} );

						$('html:not(:animated),body:not(:animated)').animate({ scrollTop: 0 }, 500);
					}
				});
			}
		});
	}
	

	/* Function to save layout */
	function dt_layout_save( show_save_message ){
		var layout_html = $('#dt_layout').html(),
			layout_shortcode = dtthemes_generate_layout_shortcode( $('#dt_layout') ),
			$save_message = jQuery("#dtthemes_ajax_save");
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : 'dt_save_layout',
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_layout_html : layout_html,
				dt_layout_shortcode : layout_shortcode,
				dt_post_id : $('input#post_ID').val()
			},
			beforeSend: function ( xhr ){
				if ( show_save_message ){
					$save_message.css("display","block");
					$save_message.children("span").css("margin","6px 0px 0px 30px").html( dtthemes_options.saving_text );
					$save_message.fadeIn('fast');
				}
			},
			success: function( data ){
				if ( show_save_message ){
					$save_message.children("span").css("margin","0px").html( dtthemes_options.layout_saved_text );
	
					setTimeout(function() {
						$save_message.fadeOut("slow");
					}, 500 );
				}
			}
		});
	}


	/* Function to append sample layout to canvas */
	function dtthemes_append_sample_layout( $layout_module ){
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
			data:
			{
				action : 'dt_append_layout',
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_layout_name : $layout_module.attr('data-name')
			},
			success: function( data ){
				$( '#dt_layout' ).append( data );
				$( '#dt_layout .dt_module .ui-resizable-handle' ).remove();
				dt_init_modules_js( 0 );
				dt_layout_save(true);
			}
		});
	}

	/* Function to get default module data to show dummy content */
	function get_module_data(mod, loctype, locdata, initmod) {
		var html = mod.html();
		var dt_action = 'dt_get_module_data';
		var dclass = mod.attr('class');
		dclass = dclass.replace('dt-sc-tooltip', '');
		dclass = dclass + ' dt_first';
		var dname = mod.attr('data-name');
		var dplaceholder = mod.attr('data-placeholder');
		var dstyle = 'opacity: 1;';
		
		var dattr = dwpname = dwpid = '';
		
		if(mod.attr('data-attr') != '' && mod.attr('data-attr') != undefined)
			dattr = " data-attr='" + mod.attr('data-attr') + "'";
		if(mod.attr('data-wpname') != '' && mod.attr('data-wpname') != undefined)
			dwpname = " data-wpname='" + mod.attr('data-wpname') + "'";
		if(mod.attr('data-wpid') != '' && mod.attr('data-wpid') != undefined)
			dwpid = " data-wpid='" + mod.attr('data-wpid') + "'";
		
		$.ajax({
			type: "POST",
			url: dtthemes_options.ajaxurl,
  			data:
			{
				action : dt_action,
				dt_load_nonce : dtthemes_options.dt_load_nonce,
				dt_module_class : mod.attr('class'),
				dt_modal_window : 0,
				dt_module_exact_name : mod.attr('data-placeholder'),
			},
			success: function( data ){
				fhtml = html.replace('<div class="dt_module_settings"></div>', data);
				var content = '';
				if(loctype == 'layout') {
					content = '<div class="' + dclass + '" data-name="' + dname + '" data-placeholder="' + dplaceholder + '" style="' + dstyle + '" ' + dattr + dwpname + dwpid + ' >' + fhtml + '</div>';
					locdata.append(content);
				} else if(loctype == 'column') {
					content = '<div class="' + dclass + '" data-name="' + dname + '" data-placeholder="' + dplaceholder + '" style="' + dstyle + '" ' + dattr + dwpname + dwpid + ' >' + fhtml + '</div>';
					locdata.append(content);
				} else if(loctype == 'popup') {
					content = '<div class="' + dclass + '" data-name="' + dname + '" data-placeholder="' + dplaceholder + '" style="' + dstyle + '" ' + dattr + dwpname + dwpid + ' >' + fhtml + '</div>';
					$('.dt_active_column .dt_modules_container').append(content);
					$('.dt_m_column').removeClass('dt_active_column');		
					$('#dt_close_popup_modules').trigger('click');
				}
				if(initmod == 'yes') {
					dt_init_modules_js(1);
				}
				dt_calculate_modules();
				dt_layout_save( false );
			}
		});
	}	
	
	/* Initialize sortable, droppable, draggable functions for modules and widgets */
	(function dt_init_ui(){
		$( '#dt_layout' ).droppable({
			accept: ":not(.ui-sortable-helper)",
			placeholder: 'dt_module_placeholder',
			greedy: true,
			drop: function( event, ui ) {
				$('div.dt_module').removeClass('dt-sc-tooltip-top');
				$('div.dt_module').removeAttr('title');
				if(ui.draggable.attr('id') != 'dt_dialog_modal') {
					if ( ui.draggable.hasClass('dt_sample_layout') ){
						dtthemes_append_sample_layout( ui.draggable );
						return;
					} else if(ui.draggable.hasClass('dt_m_column')) {
						ui.draggable.clone().appendTo( this );
						dt_layout_save( false );
					} else {
						get_module_data(ui.draggable, 'layout', $(this), '');
					}
					dt_init_modules_js( 0 );
					dtthemes_bg_effects(1000);
				}
			}
		}).sortable({
			forcePlaceholderSize: true,
			placeholder: 'dt_module_placeholder',
			cursor: 'move',
			distance: 1,
			connectWith: '#dt_layout, .dt_modules_container, .dt_fullwidth_section_container',
			start: function(event, ui) {
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", true );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", true );
					
				if (ui.item.hasClass("dt_fullwidth_section") || ui.item.hasClass("dt_m_column_resizable") || ui.item.hasClass("dt_module_resizable") || ui.item.hasClass("dt_m_column_full_width")) {
					$(this).sortable("option", "connectWith", '#dt_layout');
					$(this).sortable("refresh");
				} else if (ui.item.hasClass("dt_m_column")) {
					$(this).sortable("option", "connectWith", '#dt_layout, .dt_fullwidth_section_container');
					$(this).sortable("refresh");
				} else {
					$(this).sortable("option", "connectWith", '#dt_layout, .dt_modules_container, .dt_fullwidth_section_container');
					$(this).sortable("refresh");
				}
				ui.placeholder.text( ui.item.attr('data-placeholder') );
				ui.placeholder.width(ui.helper.outerWidth());
				ui.placeholder.height(ui.helper.outerHeight());
				ui.placeholder.css( 'margin-left', '4px' );
			},
			sort: function(event, ui){
				if(!ui.item.hasClass('dt_m_column')) {
					ui.placeholder.width(ui.placeholder.parent().width());
				}
			},
			update: function(event, ui){
				dt_init_modules_js( 0 );
			},
			stop: function(event, ui) {
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", false );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", false );
				dt_layout_save( false );
			}
		});

		$( '#dt_modules .dt_module' ).draggable({
			revert: 'invalid',
			zIndex: 100,
			distance: 1,
			cursor: 'move',
			helper: 'clone'
		});
		
	})();


	$( '#dt_layout .dt_module .ui-resizable-handle' ).remove();
	dt_init_modules_js( 1 );


	/* Resizable and sortable initialization */
	function dt_init_modules_js( dt_first_time ){
												
		var $dt_helper_text = $('#dtthemes_helper');

		$( '#dt_layout > .dt_m_column' ).each( function(){
			$(this).removeClass('dt_m_column_no_modules');
			if ( ! $(this).find('.dt_modules_container .dt_module').length ) $(this).addClass('dt_m_column_no_modules');
			else {
				$(this).addClass('dt_m_column_has_modules');
			}
		} );

		$( '#dt_layout > .dt_module:not(.dt_disable_resize)' ).resizable({
			handles: 'e',
			containment: 'parent',
			start: function(event, ui) {
				ui.helper.addClass('dt_module_resizable');
				ui.helper.css({position: ""}); // firefox fix
				ui.helper.css({
					position: "relative !important",
					top: "0 !important",
					left: "0 !important"
				});
			},
			stop: function(event, ui) {
				
				var module_width = ui.size.width,
				new_width = Math.ceil(( module_width / $('#dt_layout').width() ) * 100);
				if(new_width >= 100 && ui.helper.hasClass('dt_module_resizable')) {
					ui.helper.removeClass('dt_module_resizable');
				}
				
				ui.helper.css({
					position: "",
					top: "",
					left: ""
				});
				dt_calculate_modules();
				dt_layout_save( false );
			},
			resize: function(event, ui) {

				var module_width = ui.size.width,
					new_width = Math.ceil(( module_width / $('#dt_layout').width() ) * 100);
				
				if(ui.helper.hasClass('dt_m_column_resizable')) {
					var $module_width = ui.helper.find('> .dt_module_controls span.dt_module_name > span.dt_module_width');
				} else {
					var	$module_width = ui.helper.find('> span.dt_module_name > span.dt_module_width');
				}

				$(this).css( 'width', parseInt( new_width ) + '%' );

				ui.helper.css({
					top: "",
					left: ""
				});

				if ( new_width >= 100 ) new_width = '';
				else new_width = ' (' + new_width + '%)';
				
				if ( $module_width.length ){
					$module_width.html( new_width );
				} else {
					if(ui.helper.hasClass('dt_m_column_resizable'))
						ui.helper.find('> .dt_module_controls span.dt_module_name').append('<span class="dt_module_width">' + new_width + '</span>')
					else
						ui.helper.find('> span.dt_module_name').append('<span class="dt_module_width">' + new_width + '</span>')
				}

				if ( ui.helper.hasClass('dt_m_column_resizable') || ui.helper.hasClass('dt_module_resizable')) ui.helper.css('height','auto');
			}
		});

		$( '#dt_layout .dt_m_column .dt_modules_container' ).droppable({
			accept: ".dt_module:not(.dt_m_column,.dt_fullwidth_section,.dt_m_column_resizable, .dt_module_resizable)",
			hoverClass: 'dt_column_active',
			greedy: true,
			drop: function( event, ui ) {
				$('div.dt_module').removeClass('dt-sc-tooltip');
				$('div.dt_module').removeAttr('title');
				// return if we're moving modules inside the column
				if ( ui.draggable.parents('.dt_m_column').length && $(this).find('.ui-sortable-helper').length ) return;

				if (ui.draggable.find('.dt_module_settings').length && ui.draggable.find('.dt_module_settings').html().length){
					ui.draggable.clone().appendTo( this ).attr( 'style', 'opacity: 1;' ).find('span.dt_module_width, div.ui-resizable-handle').remove();
					dt_layout_save( false );
				} else {
					ui.draggable.find('span.dt_module_width, div.ui-resizable-handle').remove();
					get_module_data(ui.draggable, 'column', $(this), '');
				}
				
				if ( ui.draggable.parents('#dt_layout').length ){
					ui.draggable.remove();
				}
				dt_layout_save( false );
				
				dt_init_modules_js( 0 );
			}
		}).sortable({
			forcePlaceholderSize: false,
			cancel: 'span.dt_column_name',
			placeholder: 'dt_module_placeholder',
			cursor: 'move',
			distance: 2,
			connectWith: '#dt_layout, .dt_modules_container, .dt_fullwidth_section_container',
			zIndex: 10,
			start: function(event, ui) {
				
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", true );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", true );

				ui.placeholder.text( ui.item.attr('data-placeholder') );
				ui.placeholder.width(ui.helper.outerWidth());
				ui.placeholder.height(ui.helper.outerHeight());
				ui.placeholder.css( 'margin-left', '4px' );
				ui.item.closest('.dt_m_column').css( 'z-index', '1' );
			},
			sort: function(event, ui){
				if(!ui.item.hasClass('dt_m_column')) {
					ui.placeholder.width(ui.placeholder.parent().width());
				}
			},
			stop: function(event, ui) {
				
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", false );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", false );
				
				$( '#dt_layout .dt_m_column' ).css( 'z-index', '1' );
				dt_layout_save( false );
			}
		});

		//Fullwidth section container
		$( '#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container' ).droppable({
			accept: ".dt_module:not(.dt_m_column_full_width, .dt_fullwidth_section, .dt_m_column_resizable, .dt_module_resizable)",
			hoverClass: 'dt_column_active',
			greedy: true,
			drop: function( event, ui ) {
				$('div.dt_module').removeClass('dt-sc-tooltip');
				$('div.dt_module').removeAttr('title');
				if ( ui.draggable.parents('.dt_fullwidth_section').length && $(this).find('.ui-sortable-helper').length ) return;

				if ( (ui.draggable.find('.dt_columndata_settings').length && ui.draggable.find('.dt_columndata_settings').html().length) || (ui.draggable.find('.dt_module_settings').length && ui.draggable.find('.dt_module_settings').html().length) ){
					ui.draggable.clone().appendTo( this ).attr( 'style', 'opacity: 1;' ).find('span.dt_module_width, div.ui-resizable-handle').remove();
					dt_layout_save( false );
					dt_calculate_modules();
					dt_init_modules_js( 0 );
				} else {
					ui.draggable.find('span.dt_module_width, div.ui-resizable-handle').remove();
					get_module_data(ui.draggable, 'column', $(this), 'yes');
				}

				if ( ui.draggable.parents('#dt_layout').length ){
					$(ui.draggable).remove();
				}
			}
		}).sortable({
			forcePlaceholderSize: false,
			cancel: 'span.dt_column_name',
			placeholder: 'dt_module_placeholder',
			cursor: 'move',
			distance: 2,
			connectWith: '#dt_layout, .dt_fullwidth_section_container, .dt_modules_container',
			zIndex: 10,
			start: function(event, ui) {
				
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", true );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", true );
				
				if (ui.item.hasClass("dt_m_column")) {
					$(this).sortable("option", "connectWith", '#dt_layout, .dt_fullwidth_section_container');
					$(this).sortable("refresh");
				} else {
					$(this).sortable("option", "connectWith", '#dt_layout, .dt_modules_container, .dt_fullwidth_section_container');
					$(this).sortable("refresh");
				}
				ui.placeholder.text( ui.item.attr('data-placeholder') );
				ui.placeholder.width(ui.helper.outerWidth());
				ui.placeholder.height(ui.helper.outerHeight());
				ui.placeholder.css( 'margin-left', '4px' );
				ui.item.closest('.dt_fullwidth_section').css( 'z-index', '1' );
			},
			sort: function(event, ui){
				if(!ui.item.hasClass('dt_m_column')) {
					ui.placeholder.width(ui.placeholder.parent().width());
				}
			},
			stop: function(event, ui) {
				
				if ($('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable()) $('#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container').droppable( "option", "disabled", false );
				if ($('#dt_layout .dt_m_column .dt_modules_container').droppable()) $('#dt_layout .dt_m_column .dt_modules_container').droppable( "option", "disabled", false );
				
				$( '#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container .dt_m_column' ).css( 'z-index', '1' );
				dt_calculate_modules();
				dt_layout_save( false );
			}
		});


		if ( $( '#dt_layout > .dt_module' ).length ) $dt_helper_text.hide();
		else $dt_helper_text.show();

		$( '#dt_layout .dt_module' ).css( { 'position' : '', 'top' : '', 'left' : '', 'height' : 'auto !important' } ).removeClass('ui-sortable-helper').removeClass('dt_column_active');

		if ( dt_first_time != 1 ) dt_calculate_modules();
		if ( typeof tinyMCE === "undefined" ) $('body').addClass( 'dt_visual_editor_disabled' );
	}

	
	/* To calculate modules width */
	function dt_calculate_modules(){

		var dt_row_width = 0;
		dt_main_module_width = $('#dt_layout').width();

		$( '#dt_layout > .dt_module' ).removeClass('dt_first').each( function(index){
			if ( index === 0 || dt_row_width === 0 ) $(this).addClass('dt_first');

			dt_row_width += $(this).width();
			if ( dt_row_width === $('#dt_layout').outerWidth(true) ){
				$(this).next('.dt_module').addClass('dt_first');
				dt_row_width = 0;
			} else if ( Math.floor(dt_row_width) > $('#dt_layout').outerWidth(true) ){
				$(this).addClass('dt_first');
				dt_row_width = $(this).outerWidth(true);
			}
		} );
		
		
		var dt_fullwidth_row = 0;
		$( '#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container > .dt_module' ).removeClass('dt_first').each( function(index){
			if ( index === 0 || dt_fullwidth_row === 0 ) $(this).addClass('dt_first');

			dt_fullwidth_row += $(this).width();
			if ( dt_fullwidth_row === $('#dt_layout').outerWidth(true) ){
				$(this).next('.dt_module').addClass('dt_first');
				dt_fullwidth_row = 0;
			} else if ( Math.floor(dt_fullwidth_row) > $('#dt_layout').outerWidth(true) ){
				$(this).addClass('dt_first');
				dt_fullwidth_row = $(this).outerWidth(true);
			}
		} );

	}

	/* Media uploader functions */
	(function dt_integrate_media_uploader(){
		var dt_image_frame,
			$upload_field_input,
			dt_image_changed = false,
			lb_upload_button_clicked = false;

		function dt_open_image_frame() {
			if ( dt_image_frame ) {
				dt_image_frame.open();
				return;
			}

			dt_image_frame = wp.media.frames.dt_image_frame = wp.media({
				library: {
					type: 'image'
				},
				multiple: false
			});

			dt_image_frame.on( 'select', function() {
				dt_attachment = dt_image_frame.state().get('selection').first().toJSON();

				dt_attachment_image_selected( dt_attachment );
			});

			dt_image_frame.open();
		}

		function dt_attachment_image_selected( dt_attachment ) {
			var change_image = dt_image_changed ? 1 : 0,
				dt_ajax_data = {
					action : 'dt_add_slider_item',
					dt_load_nonce : dtthemes_options.dt_load_nonce,
					dt_attachment_id : dt_attachment.id,
					dt_change_image : change_image
				};

			$.ajax({
				type: "POST",
				url: dtthemes_options.ajaxurl,
				dataType: change_image ? 'json' : 'html',
				data: dt_ajax_data,
				success: function( data ){
					if ( lb_upload_button_clicked ){
						$upload_field_input.val( dt_attachment.url );
					}
				}
			});
		}

		$( 'body' ).delegate( 'a.dtthemes_upload_button', 'click', function(){
			dt_image_changed = false;
			lb_upload_button_clicked = true;
			$upload_field_input = $(this).siblings('.dtthemes_upload_field');
			
			dt_open_image_frame();
		});
		
	})();


	/* Functoin to switch between modules, widgets, columns and sample layout */
	$dt_builder_add_links.click( function(){
		
		var $dt_clicked_link = $(this),
			$dt_modules_container = $('#dt_modules'),
			open_modules_window = false,
			modid;

		if ( $dt_clicked_link.hasClass('dt_active') ) return false;
		
		$dt_modules_container.find('.dt_module').css( { 'opacity' : 0, 'display' : 'none' } );

		modid = $dt_clicked_link.attr('class').replace('dt_add_element dt_add_','');
		if($dt_modules_container.find('.dt_module.dt_module_' + modid).length > 0)
			$dt_modules_container.find('.dt_module.dt_module_' + modid).css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
		else
			if($dt_modules_container.find('.dt_module.dt_' + modid).length > 0)
				$dt_modules_container.find('.dt_module.dt_' + modid).css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
			else if(modid == 'column')
				$dt_modules_container.find('.dt_module.dt_m_' + modid + ', .dt_module.dt_fullwidth_section').css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
		
		if ( $dt_modules_container.is(':hidden') || open_modules_window ) {
			$dt_modules_container.slideDown(700);
		}

		$dt_builder_add_links.removeClass('dt_active');
		$dt_clicked_link.addClass('dt_active');

		return false;
	} );


	$("div#dt_enable_builder").click(function(){
	  var $ele = '#'+$(this).attr("data-for");
	  $(this).toggleClass('chkbx-switch-off chkbx-switch-on');
	  if($(this).hasClass('chkbx-switch-on')){
		  $($ele).attr("checked","checked");
	  }else{
		  $($ele).removeAttr("checked");
	  }
	});
	  
	/* Activate or Deactivate functons */
	function dt_deactivate_ui_actions(){
		$('#dt_modules .dt_module').addClass('disable_onclick');
		$('#dt_layout .dt_m_column .dt_module_controls .dt_column_name').css('opacity',0.5);
		$( '#dt_layout' ).droppable( "disable" ).sortable( "disable" );
		$( '#dt_layout .dt_m_column .dt_modules_container' ).droppable( "disable" );
		$( '#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container' ).droppable( "disable" );
		
		$( '#dt_layout > .dt_module span.dt_move, #dt_layout > .dt_module span.dt_delete, #dt_layout > .dt_module span.dt_settings_arrow_module, #dt_layout > .dt_module span.dt_settings_arrow_widget, #dt_layout > .dt_module span.dt_clone_module, #dt_layout > .dt_module span.dt_clone_column, #dt_layout > .dt_module span.dt_delete_column, #dt_layout > .dt_module span.dt_showorhide, #dt_layout > .dt_module span.dt_settings_arrow_column, #dt_layout > .dt_module span.dt_settings_arrow_fullwidth' ).css( 'display', 'none' );
		
		$('#dt_layout .dt_module:not(.dt_active)').attr('style', 'opacity: 0.5; z-index: 1;');
		$( '#dt_layout .dt_module' ).each( function(){
			if($(this).find('.dt_modules_container .dt_module').hasClass('dt_active')) {
				$(this).attr('style', 'opacity: 1; z-index: 1;');
				$(this).find('.dt_modules_container .dt_module.dt_active').attr('style', 'opacity: 1; z-index: 1;');
			}
			if($(this).find('.dt_fullwidth_section_container .dt_module').hasClass('dt_active')) {
				$(this).attr('style', 'opacity: 1; z-index: 1;');
				$(this).find('.dt_fullwidth_section_container .dt_module.dt_active').attr('style', 'opacity: 1; z-index: 1;');
			}
		});

		dt_make_editor_droppable();
	}

	function dt_reactivate_ui_actions(){
		$('#dt_modules .dt_module').removeClass('disable_onclick');
		$('#dt_layout .dt_m_column .dt_module_controls .dt_column_name').css('opacity',1);
		$( '#dt_layout' ).droppable( "enable" ).sortable( "enable" );
		$( '#dt_layout .dt_m_column .dt_modules_container' ).droppable( "enable" );
		$( '#dt_layout .dt_fullwidth_section .dt_fullwidth_section_container' ).droppable( "enable" );
		
		$( '#dt_layout > .dt_module span.dt_showorhide, #dt_layout > .dt_module span.dt_clone_column, #dt_layout > .dt_module span.dt_delete_column, #dt_layout > .dt_module span.dt_settings_arrow_column, #dt_layout > .dt_module span.dt_settings_arrow_fullwidth' ).css( 'display', 'block' );

		$( '#dt_layout .dt_module .dt_module_options' ).each( function(){
			if($(this).find('.dt_showorhide').attr('title') == 'Hide') {
				$(this).closest('.dt_module').css('opacity',0.4);
				$(this).find('.dt_settings_arrow_module, .dt_clone_module, .dt_delete, .dt_move, .dt_settings_arrow_widget').css('display','none');
			} else {
				$(this).closest('.dt_module').css('opacity',1);
				$(this).find('.dt_settings_arrow_module, .dt_clone_module, .dt_delete, .dt_move, .dt_settings_arrow_widget').css('display','block');
			}
		});
	}

	function dt_deactivate_ui_actions_all(){
		$('#dt_layout .dt_module, #dt_layout .dt_m_column').css('opacity',0.5);
		$( '.dt_builder_controls, #dt_modules' ).css('opacity',0.5);
		$( 'span#dtthemes_clear_all, span#dtthemes_create_layout' ).css( 'display', 'none' );
		$( 'div.dt_button').css( 'display', 'none' );
	}
	
	function dt_reactivate_ui_actions_all(){
		$( '#dt_layout .dt_module, #dt_layout .dt_m_column' ).removeClass('dt_active').css('opacity',1);
		$( '.dt_builder_controls, #dt_modules' ).css('opacity',1);
		$( 'span#dtthemes_clear_all, span#dtthemes_create_layout' ).css( 'display', 'block' );
		$( 'div.dt_button').css( 'display', 'inline-block' );
	}


	/* Activate or Deactivate page builder form on 'Enable page builder' checkbox click */
	$('div#dt_enable_builder').bind('click',function() {
		if($('input[type="checkbox"]#dtthemes-enable-builder').is(':checked')) {
			dt_reactivate_ui_actions();
			dt_reactivate_ui_actions_all();
		 } else {
			dt_deactivate_ui_actions();
			dt_deactivate_ui_actions_all();
		}
	});			
	
	$('#dt_modules').find('.dt_module').css( { 'opacity' : 0, 'display' : 'none' } );
	$('#dt_page_builder #dt_modules').css({'display':'inline-block', 'opacity' : 1});
	$('#dt_page_builder #dt_modules').find('.dt_m_column, .dt_fullwidth_section').css({'display':'inline-block', 'opacity' : 1});
	$('#dt_page_builder .dt_add_column').addClass('dt_active');
			
});