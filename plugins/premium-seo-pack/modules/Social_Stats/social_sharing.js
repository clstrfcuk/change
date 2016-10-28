/*
Document	:  Social Sharing Frontend
Author		:  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspSocialSharing = (function ($) {
"use strict";

// public
var debug_level = 0;
var ajaxurl = '';
var toolbars = {};

var socialNetworks = {
	'none'			: {}
	,'print'		: {}
	,'email'		: {}
	,'facebook'		: {}
	,'twitter'		: {
		'src'			: "https://platform.twitter.com/widgets.js"
	}
	,'plusone'		: {
		'src'			: "https://apis.google.com/js/platform.js",
		'pms'			: { 'async' : true, 'callback' : function() { gapi.plusone.go(); } }
	}
	,'linkedin'		: {
		'src'			: "http://platform.linkedin.com/in.js?async=true",
		'pms'			: { 'callback' : function() { IN.init({ onLoad: function() {} }); } }
	}
	,'stumbleupon'	: {
		'src'			: "https://platform.stumbleupon.com/1/widgets.js",
		'pms'			: { 'async' : true }
	}
	,'digg'			: {
		'src'			: "http://widgets.digg.com/buttons.js",
		'pms'			: { 'async' : true }
	}
	,'delicious'	: {}
	,'pinterest'	: {
		'src'			: "//assets.pinterest.com/js/pinit.js",
		'pms'			: { 'async' : true }
	}
	,'xing'			: {
		'src'			: "https://www.xing-share.com/js/external/share.js"
	}
	,'buffer'		: {
		'src'			: "http://static.bufferapp.com/js/button.js" // "https://d389zggrogs7qo.cloudfront.net/js/button.js"
	}
	,'flattr'		: {
		'src'			: "//api.flattr.com/js/0.6/load.js?mode=auto",
		'pms'			: { 'async' : true, 'callback': function() { FlattrLoader.setup(); } }
	}
	,'tumblr'		: {
		'src'			: "http://platform.tumblr.com/v1/share.js",
		'pms'			: { 'async' : false }
	}
	,'reddit'		: {}
};
var socialNetworksCustomBtn = [];

// init function, autoload
(function init() {
	// load the triggers
	$(document).ready(function() {
		ajaxurl = pspSocialSharing_ajaxurl;

		toolbars = {
			'floating'				: $('.psp-sshare-wrapper').filter('.box-floating'),
			'content_horizontal'	: $('.psp-sshare-wrapper').filter('.box-panel'),
			'content_vertical'		: $('.psp-sshare-wrapper').filter('.box-panel-vertical')
		};
		
		triggers();
	});
})();

function setAjaxUrl( url ) {
	if ( typeof url != 'undefined' && url != '' )
		ajaxurl = url;
}

/* create all toolbars on the page */
function createToolbars() {
	var opt = $('#psp-sshare-toolbars-options').data('options');

	var isEmpty = true; // verify if any toolbar defined!
	for (var i in opt) { isEmpty = false; }

	if ( isEmpty ) return false; // no toolbar!

	if ( !isEmpty ) socialButtons.init(); // init the toolbars buttons
	
	getCount( opt, true ); // get networks count
	
	for (var i in opt) { // go through all toolbars
		var settings = opt[i];
		
		toolbars[i].each(function (i, el) {
			var $el = $(el), itemid = $el.data('itemid');
			
			settings.itemid = itemid;
			settings.currentToolbar = $el;
			build_toolbar( settings );
		});
	}
}

/* get social networks count */
function getCount( opt, requestPerItem ) {
	
	// get common buttons for all toolbars
	var btnList = [], urlList = [], __urlListTmp = [];
	for (var i in opt) {
		btnList = btnList.concat( opt[i].buttons.split(',') );
		
		toolbars[i].each(function (i, el) {
			var $el = $(el), itemid = $el.data('itemid'), url = $el.data('url');
			
			// if ( itemid=='3126' ) url = 'https://twitter.com/';
			// if ( itemid=='3099' ) url = 'http://www.wordpress.org';
			// if ( itemid=='3042' ) url = 'http://www.facebook.com';

			if ( $.inArray(itemid, __urlListTmp) == -1 ) {
				urlList.push( { 'id': itemid, 'url': url } );
				__urlListTmp.push( itemid );
			}
		});
	}
	btnList = misc.arrayUnique( btnList );
	urlList = misc.arrayUnique( urlList );
	
	// remove no-count buttons
	var __tmp = ['tumblr', 'digg', 'xing'];
	for (var c in __tmp) btnList = misc.arrayRemoveElement(btnList, __tmp[c]);
	
	if ( btnList != 'null' && btnList.length > 0 && urlList != 'null' && urlList.length > 0 ) ;
	else return true;

	if ( requestPerItem ) {
			for (var key in urlList) {
				var val = urlList[key];
 
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, {
					'action' 		: 'pspSocialSharingFrontend',
					'sub_action' 	: 'getCount',
					'buttons' 		: btnList.join(','),
					'urls'			: [val],
					'debug_level' 	: debug_level
				}, function(response) {
			
					if (response.status == 'valid') {
						var res = response.results;
  
						for (var i in opt) {
							toolbars[i].filter(function () {
								return $(this).data('itemid') == misc.arrayGetElement( res, 'key' );
							}).each(function (i2, el) {
								var $el = $(el), itemid = $el.data('itemid');
								var countList = res[itemid];
								$el.find('.social-btn').not('.print, .email').each(function (i3, elem){
									var $elem = $(elem), network = $elem.data('network'), count = countList[network];
									$elem.find('.count').html( count );
								});
							});
						}
					}
			
				}, 'json');
			}
			return true;
	}

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, {
		'action' 		: 'pspSocialSharingFrontend',
		'sub_action' 	: 'getCount',
		'buttons' 		: btnList.join(','),
		'urls'			: urlList,
		'debug_level' 	: debug_level
	}, function(response) {

		if (response.status == 'valid') {
			var res = response.results;
			
			for (var i in opt) {
				toolbars[i].each(function (i2, el) {
					var $el = $(el), itemid = $el.data('itemid');
					var countList = res[itemid];
					$el.find('.social-btn').not('.print, .email').each(function (i3, elem){
						var $elem = $(elem), network = $elem.data('network'), count = countList[network];
						$elem.find('.count').html( count );
					});
				});
			}
		}

	}, 'json');
}

// ajust toolbar position
function fixToolbarPosition(toolbar, opt) {
	var size = opt.size || 'normal', count = opt.count || 'no';
  
	var winW = $(window).width(), winH = $(window).height(),
	tbW = toolbar.width(), tbH = toolbar.height(),
	itemW = {'normal': 30, 'normal_count': 76, 'large': 50, 'large_count': 127},
	itemH = {'normal': 30, 'large': 50}, itemPadding = {'normal': 7, 'large': 12},
	totalItems = toolbar.find('.social-btn').length;

	var __itemW = itemW['normal'], __itemH = itemH['normal'], __itemPadding = itemPadding['normal'];
	if ( size == 'large' && count == 'yes' ) {
		__itemW = itemW['large_count']; __itemH = itemH['large']; __itemPadding = itemPadding['large'];
	} else if ( count == 'yes' ) {
		__itemW = itemW['normal_count'];
	} else if ( size == 'large' ) {
		__itemW = itemW['large']; __itemH = itemH['large']; __itemPadding = itemPadding['large'];
	}
	
	// fix height
	if ( winH < tbH ) {
		var newtbH = winH - (2 * __itemPadding), columnItems = Math.floor( newtbH / (__itemH + __itemPadding) ), totalColumns = Math.ceil( totalItems / columnItems ),
		newtbW = parseInt( tbW * totalColumns + (__itemPadding + 1) * totalColumns );
		//toolbar.height( newtbH );//.width( newtbW );
	}
}

function moreBtnList(tbl, opt) {
	var toolbar = tbl, o = opt, size = opt.btnsize || 'normal', count = opt.viewcount || 'no';
	
  	function build_position() {
		var winW = $(window).width(), winH = $(window).height(),
		winLeft = $(window).scrollLeft(), winTop = $(window).scrollTop(),
		tbW = toolbar.outerWidth(), tbH = toolbar.outerHeight(),
		moreBtn = toolbar.find('.social-btn.more'),
		moreBtnW = moreBtn.outerWidth(true),
		moreBtnOffset = moreBtn.offset(),
		moreBtnPos = moreBtn.position(),
		moreBtnWinTop = parseInt( moreBtnOffset.top - winTop ),
		moreBtnWinLeft = parseInt( moreBtnOffset.left - winLeft ),
		mTb = moreBtn.next('.more-list'), mTbW = mTb.outerWidth(), mTbH = mTb.outerHeight(),
		itemPadding = {'normal': 7, 'large': 12},
		totalItems = mTb.find('.social-btn').length,
		mTbMargin = { top: 3, left: 3 };
		
		var __itemPadding = itemPadding['normal'];
		if ( size == 'large' ) __itemPadding = itemPadding['large'];
 
		var css = { 
			'position'		: 'absolute',
			'top'			: '',
			'left'			: '',
			'bottom'		: '',
			'right'			: '',
			'height'		: '',
			'overflow-y'	: ''
		};
		
		// scroll through more list buttons!
		/*if ( totalItems > 5 ) {
			css['overflow-y'] = 'scroll';
			css.height = 192;
			if ( size == 'large') css.height = 310;
		}*/
		
		if ( mTbMargin.top != 0 ) mTbMargin.top = parseInt( mTbMargin.top );
		if ( mTbMargin.left != 0 ) mTbMargin.left = parseInt( mTbMargin.left );

		// fix vertical
		var spaceFreeTop = parseInt( winH - moreBtnWinTop );
		if ( spaceFreeTop < mTbH ) {
			css.bottom = 0;
			// fixes!
			if ( o.type == 'content_horizontal' ) css.bottom += __itemPadding;
			css.bottom += mTbMargin.top; 		
		} else {
			css.top = parseInt( moreBtnPos.top );
			// fixes!
			if ( o.type != 'content_horizontal' ) css.top += parseInt( moreBtn.css('margin-top') );
			css.top += mTbMargin.top;
		}
		
		// fix horizontal
		var spaceFreeLeft = parseInt( winW - moreBtnWinLeft );
		if ( spaceFreeLeft < mTbW ) {
			css.right = moreBtnW;
			css.right += mTbMargin.left;
		} else {
			css.left = parseInt( moreBtnPos.left + moreBtnW );
			// fixes!
			if ( o.type == 'content_vertical' ) css.left += __itemPadding;
			css.left += mTbMargin.left;
		}
   
		// open more list
		for (var i in css) {
			if ( $.inArray(i, ['top', 'left', 'bottom', 'right', 'height']) != -1 && css[i] != '' ) {
				css[i] += 'px';
			}
		}
 
		var $moreList = mTb;
		$moreList.css( css ).stop()/*.css({'display': 'block'});*/.fadeIn('fast');
	}
 
 	function triggers() {
 		// more button
		toolbar.find('.social-btn.more')
		.mouseenter(function(e) {
			e.stopPropagation();
			var $this = $(this), $moreList = $this.next('.more-list');
	
			build_position();
		})
		.mouseleave(function(e) {
			e.stopPropagation();
			var $this = $(this), $moreList = $this.next('.more-list');
	
			// close more list with delay timer to obtain time to hover over more list
			var moreTimeout = setTimeout(function () {
				$moreList.stop(true, false)/*.css({'display': 'none'});*/.fadeOut('fast');
			}, 500);
			toolbar.data('moreTimeout', moreTimeout);
		});
	
		// more list of buttons!
		toolbar.find('.more-list')
		.mouseenter(function(e) {
			e.stopPropagation();
			var $this = $(this);
	 
	 		// reset delay timer
			if ( toolbar.data('moreTimeout') ) clearTimeout( toolbar.data('moreTimeout') );
	 	})
		.mouseleave(function(e) {
			e.stopPropagation();
			var $this = $(this);
	 
	 		// close more list
			$this.stop()/*.css({'display': 'none'});*/.fadeOut('fast');
		});
	}
	
	triggers();
}

// build & position the toolbars
function build_toolbar(pms) {
	
	var recursion = {
		'timeInterval'	: 300,	// milliseconds
		'nbSteps'		: 10,	// number of steps allowed
		'cSteps'		: 0,	// current steps performed
		'timer'			: null
	};

	var opt = {
		'currentToolbar'	: '',
		'type'				: '',
		'itemid'			: 0,
		'position'			: {
			'horizontal'		: 'left',
			'vertical'			: 'top'
		},
		'margin'			: {
			'horizontal'		: 0,
			'vertical'			: 0
		},
		'viewcount'			: 'no',
		'btnsize'			: 'normal',
		'buttons'			: ''
	};
	var o = $.extend(opt, pms);
	
	var currentToolbar = o.currentToolbar/*toolbars[o.type].filter(function () {
		return $(this).data('itemid') == o.itemid;
	})*/, elContent = currentToolbar.parent(), makeFloaingActive = false;

	function build_position( doRecursion ) {
		o.margin.horizontal = ( o.margin.horizontal == '' || isNaN(o.margin.horizontal) ? 0 : parseInt( o.margin.horizontal ) );
		o.margin.vertical = ( o.margin.vertical == '' || isNaN(o.margin.vertical) ? 0 : parseInt( o.margin.vertical ) );
	
		var adminBarH = ( o.is_admin_bar_showing == 'yes' ? 30 : 0 ), css = {};
		switch (o.type) {
			case 'floating' :
				switch ( o.position.vertical ) {
					case 'top':
						css.top = adminBarH;
						break;
						
					case 'bottom':
						css.top = parseInt( $(window).height() - currentToolbar.innerHeight() );
						break;
						
					case 'center':
						css.top = parseInt( ( $(window).height() - currentToolbar.innerHeight() ) / 2 );
						break;
				}
				o.margin.vertical != 0 ? css.top = parseInt( css.top + o.margin.vertical ) : '';

				switch ( o.position.horizontal ) {
					case 'left':
						css.left = 0;
						break;
						
					case 'right':
						css.left = parseInt( $(window).width() - currentToolbar.innerWidth() );
						break;
						
					case 'center':
						css.left = parseInt( ( $(window).width() - currentToolbar.innerWidth() ) / 2 );
						break;
				}
				o.margin.horizontal != 0 ? css.left = parseInt( css.left + o.margin.horizontal ) : '';
				break;

			case 'content_horizontal' :
				delete css.top; delete css.left; // remove top & bottom properties

				// vertical positioning is made in wp content hook

				switch ( o.position.horizontal ) {
					case 'left':
						css.float = 'left';
						break;
						
					case 'right':
						css.float = 'right';
						break;
					
					case 'center':
						css['margin'] = '0 auto';
				}
				o.margin.horizontal != 0 ? css['margin-left'] = parseInt( o.margin.horizontal ) : '';
				o.margin.vertical != 0 ? css['margin-top'] = parseInt( o.margin.vertical ) : '';
				break;
	
			case 'content_vertical' :
				var $toolbar = currentToolbar, $elContent = elContent,
				tbWidth = $toolbar.innerWidth(), tbHeight = $toolbar.innerHeight(),
				tbWidthOuter = $toolbar.outerWidth(), tbHeightOuter = $toolbar.outerHeight();

				var $elMarkTop = $('body').find( $elContent ).find('.psp-social-content-mark-top'),
				$elMarkBottom = $('body').find( $elContent ).find('.psp-social-content-mark-bottom');
				// var $elContent = $elMarkTop.parent(); // $('#content .entry-content')
				var elWidth = $elContent.width(), elHeight = $elContent.height(), elOffset = $elContent.offset();
				
				$toolbar.appendTo('body'); // move content vertical toolbar (which use position absolute inside document) outside post content
 
				// make it floating
				if ( o.make_floating == 'yes' ) {
					var winTop = $(window).scrollTop(),
					winBottom = parseInt( winTop + $(window).height() ),
					elMarkTopOffset = $elMarkTop.offset(),
					elMarkBottomOffset = $elMarkBottom.offset(),
					__win_pos_top = {},
					__limit  = { top: '', bottom: '' },
					posIsFixed = ( currentToolbar.css('position') == 'fixed' );
					
					var isVerticalTop = o.position.vertical == 'top' ? true : false,
					isVerticalBottom = o.position.vertical == 'bottom' ? true : false,
					isVerticalCenter = o.position.vertical == 'center' ? true : false;
 
					__win_pos_top = {
						top			: parseInt( winTop + adminBarH ),
						bottom		: parseInt( winTop + adminBarH + $(window).height() ),
						center		: parseInt( winTop + adminBarH + ( $(window).height() / 2 ) )
					}
					__limit.top = parseInt( elMarkTopOffset.top );
					__limit.bottom = parseInt( elMarkBottomOffset.top + adminBarH );
					
					if ( o.position.vertical == 'top' ) 
						__limit.bottom -= parseInt( tbHeightOuter );

					// add margin setting 
 					o.margin.vertical != 0 ? __limit.top += parseInt( o.margin.vertical ) : '';
 
 					// bellow the content
					if ( (
							( isVerticalTop && __limit.bottom < __win_pos_top.top )
							|| ( isVerticalBottom && __limit.bottom < __win_pos_top.bottom )
							|| ( isVerticalCenter && __limit.bottom < __win_pos_top.center )
						) && o.floating_beyond_content != 'yes' ) {

						makeFloaingActive = true;
						css.position = 'absolute';
						css.top = parseInt( elOffset.top + elHeight - tbHeight );

					}
					// between the content limits
					else if ( (
							( isVerticalTop && __limit.top < __win_pos_top.top )
							|| ( isVerticalBottom && __limit.top < __win_pos_top.bottom )
							|| ( isVerticalCenter && __limit.top < __win_pos_top.center )
						) && !posIsFixed ) {

						makeFloaingActive = true;
						css.position = 'fixed';
						if ( o.position.vertical == 'top' )
							css.top = adminBarH;
						else if ( o.position.vertical == 'bottom' )
							css.top = parseInt( $(window).height() - tbHeight );
						else if ( o.position.vertical == 'center' )
							css.top = parseInt( ( $(window).height() - tbHeight ) / 2 );

					}
					// above the content
					else if ( (
							( isVerticalTop && __limit.top > __win_pos_top.top )
							|| ( isVerticalBottom && __limit.top > __win_pos_top.bottom )
							|| ( isVerticalCenter && __limit.top > __win_pos_top.center )
						) && posIsFixed ) {

						makeFloaingActive = true;
						css.position = 'absolute';
						if ( o.position.vertical == 'top' )
							css.top = __limit.top;
						else if ( o.position.vertical == 'bottom' )
							css.top = parseInt( elOffset.top + elHeight - tbHeight );
						else if ( o.position.vertical == 'center' )
							css.top = parseInt( elOffset.top + ( elHeight / 2 ) - tbHeight );

					}
				}

				if ( !makeFloaingActive ) {
				switch ( o.position.vertical ) {
					case 'top':
						css.top = parseInt( elOffset.top );
						break;
						
					case 'bottom':
						css.top = parseInt( elOffset.top + elHeight - tbHeight );
						break;
						
					case 'center':
						css.top = parseInt( elOffset.top + ( elHeight / 2 ) - tbHeight );
						break;
				}
				o.margin.vertical != 0 ? css.top = parseInt( css.top + o.margin.vertical ) : '';
				}
 
				switch ( o.position.horizontal ) {
					case 'left':
						css.left = parseInt( elOffset.left - tbWidth );
						break;
						
					case 'right':
						css.left = parseInt( elOffset.left + elWidth + tbWidth );
						break;
						
					case 'center':
						css.left = parseInt( elOffset.left + ( elWidth / 2 ) + tbWidth );
						break;
				}
				o.margin.horizontal != 0 ? css.left = parseInt( css.left + o.margin.horizontal ) : '';
				break;
		}

		for (var i in css) {
			if ( $.inArray(i, ['top', 'left', 'margin-left', 'margin-top']) != -1 ) {
				css[i] += 'px';
			}
		}
	
		currentToolbar.css( css );
		//if ( o.type == 'floating' )
		//	fixToolbarPosition(currentToolbar, o); // floating toolbar resize
 
		if ( typeof doRecursion != 'undefined' && doRecursion === true && recursion.cSteps < recursion.nbSteps ) {
			recursion.cSteps++;
			clearTimeout( recursion.timer ); recursion.timer = null;
			recursion.timer = setTimeout(function() {
				build_position( true );
			}, recursion.timeInterval);
		} else {
			clearTimeout( recursion.timer ); recursion.timer = null;
		}
	}
	
	function toolbar_triggers() {
		moreBtnList(currentToolbar, o);

		if ( o.type == 'content_vertical' ) { // bug fix for vertical content toolbar positioning!
			build_position();
			recursion.timer = setTimeout(function() {
				build_position( true );
			}, recursion.timeInterval);
		} else {
			build_position();
		}
		
		$(window).resize(function() {
			build_position();
		});
		
		if ( o.type == 'content_vertical' ) {
			$(window).scroll(function() {
				build_position();
			});
		}
		
		socialButtons.load( o ); // load toolbar buttons
	}
	toolbar_triggers();
}

/* popup window */
function popup(url, title, pms) {
	window.open(url, title, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=550,height=550');
}

/* delicious count */
function getCount_delicious() {
	// get count info
	var $btn = $('.psp-sshare-wrapper').find('.social-btn.delicious'), $a = $btn.find('a'), __url = $a.data('url'), __title = $a.data('title');

	$.getJSON("http://feeds.delicious.com/v2/json/urlinfo/data?url=" + encodeURI(__url) + "&callback=?", function (data) {
		var msg = "", count = 0;
		if (data.length > 0) {
			count = data[0].total_posts;
			if (count == 0) { msg = "0"; }
			else if (count == 1) { msg = "1"; }
			else { msg = count }
		}
		else { msg = "0"; }
		$btn.find(".count").text(msg);
	});
}

/* custom buttons */
var socialButtons = {
	btnList: {
		'print'			: {},
		'email'			: {},
		'facebook'		: {},
		'twitter'		: {},
		'plusone'		: {},
		'linkedin'		: {},
		'stumbleupon'	: {},
		'digg'			: {},
		'delicious'		: {},
		'pinterest'		: {},
		'xing'			: {},
		'buffer'		: {},
		'flattr'		: {},
		'tumblr'		: {},
		'reddit'		: {}
	}
	,init: function() {
		var self = this, c = 1;

		for (var i in self.btnList) {
			self.btnList[i] = $.extend(self.btnList[i], { 'isInit': [] });
			c++;
		}
	}
	,load: function( opt ) {
		var self = this, itemid = opt.itemid, c = 1;

		for (var i in self.btnList) {
			var v = self.btnList[i];
			//if ( $.inArray( itemid, v.isInit ) == -1 ) { // event for this button from the itemid toolbar not yet asigned!
				self.btnEvent( opt, { 'network' : i } );
			//	self.btnList[i].isInit.push( itemid );
			//}
			c++;
		}
		//self.getCount( opt );
	}
	,btnEvent: function( opt, pms ) {
		var self = this, nw = pms.network, itemid = opt.itemid, currentToolbar = opt.currentToolbar;

		currentToolbar/*$('.psp-sshare-wrapper').filter(function () {
			return $(this).data('itemid') == itemid;
		})*/.find('.social-btn.' + nw).click(function(e) {
			e.preventDefault();

			var $this = $(this);
			if ( !$this.hasClass(nw) ) return false;

			self[nw]( $this, pms );
			return false;
		});
	}
	,print: function( el, pms ) {
		window.print();
	}
	,email: function( el, pms ) {}
	,facebook: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');
 
		popup( 'http://www.facebook.com/sharer.php?u=' + __url + '&t=' + __title, nw );
	}
	,twitter: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title'), __urlroot = $a.data('source');

		popup( 'https://twitter.com/intent/tweet?source=' + __urlroot + '&text=' + __title + '&url=' + __url, nw );
	}
	,plusone: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'https://plus.google.com/share?url=' + __url, nw );
	}
	,linkedin: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title'), __summary = $a.data('summary'), __urlroot = $a.data('source');

		popup( 'http://www.linkedin.com/shareArticle?mini=true&url=' + __url + '&title=' + __title + '&summary=' + __summary + '&source=' + __urlroot, nw );
	}
	,stumbleupon: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://www.stumbleupon.com/submit?url=' + __url + '&title=' + __title, nw );
	}
	,digg: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://digg.com/submit?url=' + __url + '&title=' + __title, nw );
	}
	,delicious: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://delicious.com/save?v=5&noui&jump=close&url=' + __url + '&title=' + __title, nw );
	}
	,pinterest: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title'), __media = $a.data('media');

		popup( 'http://pinterest.com/pin/create/button/?url=' + __url + '&media=' + __media + '&description=' + __title, nw );
	}
	,xing: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'https://www.xing.com/social_plugins/share?sc_p=xing-share;h=1;url=' + __url, nw );
	}
	,buffer: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://bufferapp.com/add?&text=' + __title + '&url=' + __url, nw );
	}
	,flattr: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'https://flattr.com/submit/auto?user_id=flattr&language=en_GB&category=text&url=' + __url + '&title=' + __title, nw );
	}
	,tumblr: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://www.tumblr.com/share/link?url=' + __url + '&name=' + __title + '&description=' + __title, nw );
	}
	,reddit: function( el, pms ) {
		var $a = el.find('a'), nw = pms.network,
		__href = $a.attr('href'), __url = $a.data('url'), __title = $a.data('title');

		popup( 'http://www.reddit.com/submit?url=' + __url + '&title=' + __title, nw );
	}
};

/* load dynamic social networks scripts */
function addScriptDom(id, script, pms) {
	var d = document, s = 'script';
	var js, fjs = d.getElementsByTagName(s)[0];
	if (!d.getElementById(id)) {
		js = d.createElement(s);
		js.id = id;
		js.src = script;
		js.type = "text/javascript";
		if ( typeof pms != 'undefined' && 'async' in pms )
			js.async = true;
		fjs.parentNode.insertBefore(js, fjs);
	}
}

function addScriptJQuery(script, pms) {
	$.getScript( script )
	.done(function( data, textStatus ) {
		// console.log( 'end', script, textStatus );
		if ( typeof pms != 'undefined' && 'callback' in pms && $.isFunction(pms.callback) ) {
			pms.callback();
		}
	})
	.fail(function( jqxhr, settings, exception ) {
		// console.log( 'end', script, "Triggered ajaxError handler."  );
	});
}

function loadSocialScripts() {
	var c = 1;
	for (var i in socialNetworks) {
		var v = socialNetworks[i];
		if ( i == 'none' || $.inArray(i, socialNetworksCustomBtn) != -1 ) continue;

		(function (c, i, v){
			setTimeout(function () {
				//addScriptDom( (i+'-wjs'), v.src, v.pms );
				addScriptJQuery( v.src, v.pms );
			}, parseInt(c*200));
		})(c, i, v);
		c++;
	}
}

/* triggers */
function triggers() {
	createToolbars();
}

var misc = {

	hasOwnProperty: function(obj, prop) {
		var proto = obj.__proto__ || obj.constructor.prototype;
		return (prop in obj) &&
		(!(prop in proto) || proto[prop] !== obj[prop]);
	},

	arrayHasOwnIndex: function(array, prop) {
		return array.hasOwnProperty(prop) && /^0$|^[1-9]\d*$/.test(prop) && prop <= 4294967294; // 2^32 - 2
	},

	arrayIntersect: function(a, b) {
    	return $.grep(a, function(i) {
        	return $.inArray(i, b) > -1;
    	});
	},
   
	arrayUnique: function(array) {
    	var a = array.concat();
    	for(var i=0; i<a.length; ++i) {
        	for(var j=i+1; j<a.length; ++j) {
            	if(a[i] === a[j])
                	a.splice(j--, 1);
        	}
    	}
    	return a;
   },
   
    arrayGetElement: function(array, type) { // second parameter possible values: key | value
		for (var i in array) {
			if (misc.hasOwnProperty(array, i)) {
				if ( type == 'key' ) return i;
				return array[i];
			}
		}
   },
   
    arrayRemoveElement: function(array, value) {
		var idx = array.indexOf(value);
		if (idx != -1) array.splice(idx, 1);
		return array;
	}

};

// external usage
return {
	'build_toolbar'		: build_toolbar,
	'setAjaxUrl'		: setAjaxUrl
}
})(jQuery);
