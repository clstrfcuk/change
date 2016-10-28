/*
Document   :  404 Monitor
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspPriceUpdateMonitor = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var loaded_page = 0;

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
		if( status == 'show' ){
			if( row.size() > 0 ){
				if( row.find('.psp-row-loading-marker').size() == 0 ){
					var row_loading_box = $('<div class="psp-row-loading-marker"><div class="psp-row-loading"><div class="psp-meter psp-animate" style="width:30%; margin: 22px 0px 0px 30%;"><span style="width:100%"></span></div></div></div>')
					row_loading_box.find('div.psp-row-loading').css({
						'width': row.width(),
						'height': row.height(),
						'top': '-16px'
					});

					row.find('td').eq(0).append(row_loading_box);
				}
				row.find('.psp-row-loading-marker').fadeIn('fast');
			}
		}else{
			row.find('.psp-row-loading-marker').fadeOut('fast');
		}
	}

	function server_status( that, action )
	{
		//return false;
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusRequest',
			'sub_action'	: action,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				that.css('background-image', 'none');
				that.html( response.html );
				
				if ( action == 'active_modules' )
					that.data('hasResponse', 'yes');
			}
		}, 'json');
	}
	
	function export_logs( log )
	{
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusRequest',
			'sub_action'	: 'export_log',
			//'log'			: log.html(),
			'log'			: 'test',
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				that.css('background-image', 'none');
				that.html( response.html );
			}
		}, 'json');
	}
	
	function stress_test_step1( that, module )
	{
		var $wrapper = that.parent('div').parent('div.psp-verify-products-test');

		// hide the begin test button
		// that.parent('div').fadeOut();
		
		// show the timeline 
		var timeline = $wrapper.find(".psp-test-timeline");
		timeline.show();
		
		// show log container
		var logs_container = $wrapper.find(".psp-table.psp-logs");
		logs_container.show();
		
		var status_box_step = $wrapper.find(".stepid-step1").find(".psp-step-status"); 
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusVerify',
			'module'		: module,
			'sub_action'	: 'step1',
			'debug_level'	: debug_level
		}, function(response) {
			
			// save the log
			logs_container.find(".logbox-step1").css('opacity', 1);
			logs_container.find(".logbox-step1 .psp-log-details").val( JSON.stringify( response.log ) );
			
			// show the status into timeline
			status_box_step.removeClass('psp-loading-inprogress');
				
			if( response.status == 'valid' ){
				status_box_step.addClass('psp-loading-success');
				status_box_step.html("success <i>" + ( response.execution_time ) + "\"</i>");
				
				// if success, go to step 2
				stress_test_step2( that, module, timeline, logs_container );
			}
			else{
				status_box_step.addClass('psp-loading-error');
				status_box_step.html("error");
			}
			
		}, 'json')
		.error(function(response) { 
			
			// save the log
			logs_container.find(".logbox-step1").css('opacity', 1);
			
			var messge = "Please contact your server administrator and ask about this error: " + response.status + ": " + response.statusText;
			logs_container.find(".logbox-step1 .psp-log-details").val( messge );
			
			status_box_step.removeClass('psp-loading-inprogress').addClass('psp-loading-error');
			status_box_step.html("error");
		});
	}
	
	function stress_test_step2( that, module, timeline, logs_container )
	{
		var $wrapper = that.parent('div').parent('div.psp-verify-products-test');

		// set step 2 timeline loading 
		var status_box_step = $wrapper.find(".stepid-step2").find(".psp-step-status");
		status_box_step.addClass('psp-loading-inprogress');
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusVerify',
			'module'		: module,
			'sub_action'	: 'step2',
			'debug_level'	: debug_level
		}, function(response) {
			
			 
			// save the log
			logs_container.find(".logbox-step2").css('opacity', 1);
			logs_container.find(".logbox-step2 .psp-log-details").val( JSON.stringify( response.log ) );
			
			// show the status into timeline 
				status_box_step.removeClass('psp-loading-inprogress');
				
			if( response.status == 'valid' ){
				status_box_step.addClass('psp-loading-success');
				status_box_step.html("success <i>" + ( response.execution_time ) + "\"</i>");
				
				// if success, go to step 3
				stress_test_step3( that, module, timeline, logs_container );
			}
			else{
				status_box_step.removeClass('psp-loading-inprogress').addClass('psp-loading-error');
				status_box_step.html("error");
			}
			
		}, 'json')
		.error(function(response) { 
			
			// save the log
			logs_container.find(".logbox-step2").css('opacity', 1);
			
			var messge = "Please contact your server administrator and ask about this error: " + response.status + ": " + response.statusText;
			logs_container.find(".logbox-step2 .psp-log-details").val( messge );
			
			status_box_step.addClass('psp-loading-error');
			status_box_step.html("error");
		});
	}
	
	function stress_test_step3( that, module, timeline, logs_container )
	{
		var $wrapper = that.parent('div').parent('div.psp-verify-products-test');

		// set step 3 timeline loading 
		var status_box_step = $wrapper.find(".stepid-step3").find(".psp-step-status");
		status_box_step.addClass('psp-loading-inprogress');
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusVerify',
			'module'		: module,
			'sub_action'	: 'step3',
			'debug_level'	: debug_level
		}, function(response) {
			
			// save the log
			logs_container.find(".logbox-step3").css('opacity', 1);
			logs_container.find(".logbox-step3 .psp-log-details").val( JSON.stringify( response.log ) );
			
			// show the status into timeline 
			status_box_step.removeClass('psp-loading-inprogress');
			
			if( response.status == 'valid' ){
				status_box_step.addClass('psp-loading-success');
				status_box_step.html("success <i>" + ( response.execution_time ) + "\"</i>");
				
				// if success, go to step 4
				stress_test_step4( that, module, timeline, logs_container );
			}
			else{
				status_box_step.addClass('psp-loading-error');
				status_box_step.html("error");
			}
			
		}, 'json')
		.error(function(response) { 
			// save the log
			logs_container.find(".logbox-step3").css('opacity', 1);
			
			var messge = "Please contact your server administrator and ask about this error: " + response.status + ": " + response.statusText;
			logs_container.find(".logbox-step3 .psp-log-details").val( messge );
			
			status_box_step.removeClass('psp-loading-inprogress').addClass('psp-loading-error');
			status_box_step.html("error");
		});
	}
	
	function stress_test_step4( that, module, timeline, logs_container )
	{
		var $wrapper = that.parent('div').parent('div.psp-verify-products-test');

		// set step 4 timeline loading 
		var status_box_step = $wrapper.find(".stepid-step4").find(".psp-step-status");
		status_box_step.addClass('psp-loading-inprogress');
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspServerStatusVerify',
			'module'		: module,
			'sub_action'	: 'step4',
			'debug_level'	: debug_level
		}, function(response) {
			
			// save the log
			logs_container.find(".logbox-step4").css('opacity', 1);
			logs_container.find(".logbox-step4 .psp-log-details").val( JSON.stringify( response.log ) );
			
			// show the status into timeline 
			status_box_step.removeClass('psp-loading-inprogress');
			
			if( response.status == 'valid' ){
				status_box_step.addClass('psp-loading-success');
				status_box_step.html("success <i>" + ( response.execution_time ) + "\"</i>");
			}
			else{
				status_box_step.addClass('psp-loading-error');
				status_box_step.html("error");
			}
			
		}, 'json')
		.error(function(response) { 
			// save the log
			logs_container.find(".logbox-step4").css('opacity', 1);
			
			var messge = "Please contact your server administrator and ask about this error: " + response.status + ": " + response.statusText;
			logs_container.find(".logbox-step4 .psp-log-details").val( messge );
			
			status_box_step.removeClass('psp-loading-inprogress').addClass('psp-loading-error');
			status_box_step.html("error");
		});
	}
	
	function verifyHash() {
		var alias = 'sect-', modules = ['google_analytics', 'google_serp', 'google_pagespeed', 'facebook_planner', 'tiny_compress'], sect = '', doVerify = false,
		url = document.location.href, hash = url.replace( /^[^#]*#?(.*)$/, '$1' );
		
		for (var i in modules) {
			sect = alias + modules[i];
			if ( typeof hash != 'undefined' && hash != '' && sect == hash ) doVerify = true;
		}
		if (doVerify) {
			var $el = $(".psp-loading-ajax-details").filter(function(i, el) {
				return $(this).data('action') == 'active_modules';
			});
			if ( $el.length > 0 ) {
				(function findSect() {
					var timer = setTimeout(function() {
						var hasResp = $el.data('hasResponse');
						if ( typeof hasResp != 'undefined' && hasResp == 'yes' ) {
							clearTimeout( timer ); timer = null;
							pspFreamwork.scrollToElement( '#' + hash, 1000, '-40' );
							return false;							
						}
						findSect();
					}, 50);
				})();
			}
		}
	}
	
	function triggers()
	{
		verifyHash();

		maincontainer.on('click', '.psp-log-title a', function(e){
			e.preventDefault();
			
			var that = $(this),
				parent = that.parent('div');
			
			parent.next(".psp-log-details").show();
		});

		maincontainer.on('click', '.psp-export-logs', function(e){
			e.preventDefault();
			
			export_logs( $(".psp-panel-content > .psp-table" ) );
		});

		$(".psp-loading-ajax-details").each( function(){
			var that = $(this),
				action  = that.data('action');

			server_status( that, action );
		});

		// google analytics!
		maincontainer.on('click', 'a.pspStressTest.verify', function(e){
			e.preventDefault();

			var that = $(this);
				
			stress_test_step1( that, that.data('module') );
		});
	}

	// external usage
	return {
    }
})(jQuery);


if (typeof JSON !== 'object') {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function () {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear() + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate()) + 'T' +
                    f(this.getUTCHours()) + ':' +
                    f(this.getUTCMinutes()) + ':' +
                    f(this.getUTCSeconds()) + 'Z'
                : null;
        };

        String.prototype.toJSON =
            Number.prototype.toJSON =
            Boolean.prototype.toJSON = function () {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = { // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i, // The loop counter.
            k, // The member key.
            v, // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());