jQuery(document).ready(function($){
	
	
	// To switch between Wordpress Default Editor and DT Page Builder //

	$('#dtthemes-metabox').hide();
	$('<a class="dt-pb-switch dt_button" href="#">' + dtthemes_options.theme_name + '</a>').insertAfter('div#titlediv').wrap('<p class="dt-composer-switch" />');
	
	$('.dt-pb-switch').click(function(e){
		if($(this).html() == dtthemes_options.theme_name) {
			$('div#postdivrich').attr('style', 'display:none');	
			$(this).html(dtthemes_options.dt_wp_editor);
			$('#dtthemes-metabox').show();
			$('<div id="dt-metabox-id" style="display:inline-block; width:100%"></div>').insertAfter('#dtthemes-metabox');
			dt_calculate_modules();
		} else if($(this).html() == dtthemes_options.dt_wp_editor) {
			$('div#postdivrich').attr('style', 'display:block');	
			$(this).html(dtthemes_options.theme_name);
			$('#dtthemes-metabox').hide();
			$( 'div#dt-metabox-id' ).remove();
		}
		e.preventDefault();
	});
	
	
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
	
	/*Tooltip*/
	if(jQuery(".dt-sc-tooltip").length){
		jQuery(".dt-sc-tooltip").each(function(){ jQuery(this).tipTip({maxWidth: "auto",defaultPosition: "top"}); });
	}/*Tooltip End*/
	
	
	// To enable / disable DT Page Builder on page load //
	
	if($('div#dt_enable_builder').hasClass('chkbx-switch-on')) {
		$('div#postdivrich').attr('style', 'display:none');	
		$('.dt-pb-switch').html(dtthemes_options.dt_wp_editor);
		$('#dtthemes-metabox').show();
		$('<div id="dt-metabox-id" style="display:inline-block; width:100%"></div>').insertAfter('#dtthemes-metabox');
		dt_reactivate_ui_actions();
		dt_reactivate_ui_actions_all();
	} else if($('div#dt_enable_builder').hasClass('chkbx-switch-off')) {
		$('div#postdivrich').attr('style', 'display:block');	
		$('.dt-pb-switch').html(dtthemes_options.theme_name);
		$('#dtthemes-metabox').hide();
		$( 'div#dt-metabox-id' ).remove();
		dt_deactivate_ui_actions();
		dt_deactivate_ui_actions_all();
	} else {
		$('div#postdivrich').attr('style', 'display:block');	
		$('.dt-pb-switch').html(dtthemes_options.theme_name);
		$('#dtthemes-metabox').hide();
		$( 'div#dt-metabox-id' ).remove();
		dt_deactivate_ui_actions();
		dt_deactivate_ui_actions_all();
	}
	
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
	
	$('.dt_module .dt_hide').each( function(){
		if($(this).attr('title') == 'Hide') {
			$(this).closest('.dt_module').css('opacity',0.4);
			$(this).parents('.dt_module_options').find('.dt_settings_arrow_module, .dt_clone_module, .dt_delete, .dt_move, .dt_settings_arrow_widget').css('display','none');
		}
	});
	
});