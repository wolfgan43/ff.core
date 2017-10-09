/**
 * Forms Framework Javascript Handling Object
 */

// Rewrites
if(!Array.indexOf){
	Array.prototype.indexOf = function(obj){
		for(var i=0; i<this.length; i++){
		    if(this[i]==obj){
		        return i;
		    }
		}
		return -1;
	}
}

Array.prototype.each = function(func){
	for(var i=0; i<this.length; i++){
		var rc = func(i, this[i]);
		if (rc)
			break;
	}
	return i;
}




String.prototype.capitalize = function(){
    return this.replace( /(^|\s)([a-z])/g , function(m,p1,p2){
		return p1+p2.toUpperCase();
    });
};

String.prototype.escapeRegExp = function(){
    return this.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
};

String.prototype.trim = function (c) {
	if (c === undefined)
		return this.replace(/^\s*|\s*$/g, "");
	else
		return this.replace(new RegExp("^" + c.escapeRegExp()  + "*|" + c.escapeRegExp() + "*$", "g"), "");
}
String.prototype.ltrim = function (c) {
    if (c === undefined)
        return this.replace(/^\s*/g, "");
    else
        return this.replace(new RegExp("^" + c.escapeRegExp() + "*", "g"), "");
}
String.prototype.rtrim = function (c) {
	if (c === undefined)
		return this.replace(/\s*$/g, "");
	else
		return this.replace(new RegExp(c.escapeRegExp() + "*$", "g"), "");
}

String.prototype.parseUri = function() {
		// parseUri 1.2.2
		// (c) Steven Levithan <stevenlevithan.com>
		// MIT License

        var     o   = String.prototype.parseUri.options,
                m   = o.parser[o.strictMode ? "strict" : "loose"].exec(this),
                uri = {},
                i   = 14;

        while (i--) uri[o.key[i]] = m[i] || "";

        uri[o.q.name] = {};
        uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
                if ($1) uri[o.q.name][$1] = $2;
        });

        return uri;
};

String.prototype.parseUri.options = {
        strictMode: false,
        key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
        q:   {
                name:   "queryKey",
                parser: /(?:^|&)([^&=]*)=?([^&]*)/g
        },
        parser: {
                strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
        }
};

(function(arr,i,name) {
  while(name = arr[i++]) {
    Math["$"+name] = Function("a","b","return Math."+name+"(a*(b=Math.pow(10,b||0)))/b");
  }
})(["floor","ceil","round"],0);

jQuery.fn.outerHTML = function(s) {
    return s
        ? this.before(s).remove()
        : jQuery("<p>").append(this.eq(0).clone()).html();
};

jQuery.fn.escape = function (str) {
	if (typeof str === "number")
		str = str.toString();
	if (typeof str !== "string")
		throw  "FF - Invalid param for jQuery.fn.escape";
	else
		return str.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|\/])/g, '\\$1');
};

jQuery.fn.escapeGet = function (str) {
	return jQuery("#" + jQuery.fn.escape(str));
};

jQuery.fn.comboGetSelIndex = function (id) {
	if (jQuery("#" + jQuery.fn.escape(id) + " optgroup").length)
		return jQuery("#" + jQuery.fn.escape(id) + " option:selected").index("option");
	else
		return jQuery("#" + jQuery.fn.escape(id) + " option:selected").index();
};

jQuery.fn.comboGetIndexByVal = function (id, value) {
	if (jQuery("#" + jQuery.fn.escape(id) + " optgroup").length)
		return jQuery("#" + jQuery.fn.escape(id) + " option[value='" + jQuery.fn.escape(ff.coalesce(value, "")) + "']").index("option");
	else
		return jQuery("#" + jQuery.fn.escape(id) + " option[value='" + jQuery.fn.escape(ff.coalesce(value, "")) + "']").index();
};

/* http://andreaslagerkvist.com/jquery/center/#jquery-plugin-source */
jQuery.fn.center = function (absolute) {
    return this.each(function () {
        var t = jQuery(this);

        t.css({
            position:    absolute ? 'absolute' : 'fixed', 
            left:        '50%', 
            top:        '50%'
            /*zIndex:        '99'*/
        }).css({
            marginLeft:    '-' + (t.outerWidth() / 2) + 'px', 
            marginTop:    '-' + (t.outerHeight() / 2) + 'px'
        });

        if (absolute) {
            t.css({
                marginTop:    parseInt(t.css('marginTop'), 10) + jQuery(window).scrollTop(), 
                marginLeft:    parseInt(t.css('marginLeft'), 10) + jQuery(window).scrollLeft()
            });
        }
    });
};

// Forms Framework Main Object
var ff = (function () {
// private vars
var loaded_css		= undefined;

var plugins			= undefined;
var plugins_loads	= undefined;
var plugins_inits	= undefined;

var inits_uuid		= undefined;

var unique_ids		= 0;

function pluginInit(id) {
	var inits ;

	if ((inits = plugins_inits.get(id)) !== undefined) {
		for (var i = 0; i < inits.length; i++) {
			inits[i]();
		}
		plugins_inits.set(id, []);
	}
}

function initEvents(key) {
	var parts = key.split(".");
	if (parts.length > 1 && parts[0] == "ff") {
		var path = "ff";
		for (var i = 1; i < parts.length; i++) {
			path += "." + parts[i];
			if (eval(path + ' === undefined'))
				eval(path + " = {};");

			if (eval('typeof(' + path + ')') === "object" && eval(path + ".events === undefined"))
				eval('jQuery.extend(true, ' + path + ', ff.ffEvents());');
		}
	}
}

function initLibs(ref, path) {
	for (property in ref) {
		if (typeof(ref[property]) === "object" && ref[property].__ff === true) {
			var found = false;
			that.libs.each(function(key,value){
				if (value === ref[property]){
					return found = true; // break current cycle
				}
			});
			
			if (!found) {
				that.libs.set(path + "." + property, null);
				that.pluginLoad(path + "." + property, undefined, undefined, false);
				initLibs(ref[property], path + "." + property);
			}
		}
	}
}

var that = { // publics

"site_path"		: undefined,
"theme"			: undefined,
"theme_ui"		: undefined,
"page_path"		: undefined,
"language" 		: undefined, 
"layer" 		: undefined, 
"group"         : undefined, 
"origin"		: undefined,
"domain"		: undefined,
"js_path"		: undefined,
"struct"		: undefined,
"libs"			: undefined,
"fn"			: {},
"modules"		: {
	"__ff" : true
},

"coalesce" : function (value, ifnull) {
	if (value === null)
		return ifnull;
	return value;
},
	
"getUniqueID" : function() {
	return unique_ids++;
},

"httpGetOrigin" : function() {
	return window.location.protocol + "//" + window.location.hostname;
},

"getURLParameter" : function(name) {
    return decodeURIComponent(
        (RegExp(name.replace(/\[/g, "\\[").replace(/\]/g, "\\]") + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
},

"encodeURI" : function (str) {
	/*
	 * follow the more recent RFC3986 for URL's, making square brackets reserved 
	 * (for IPv6) and thus not encoded 
	 * when forming something which could be part of a URL (such as a host)
	 * 
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
	 */
    return encodeURI(str).replace(/%5B/g, '[').replace(/%5D/g, ']');
},

"encodeURIComponent" : function (str) {
	/*
	 * more stringent in adhering to RFC 3986 (which reserves !, ', (, ), and *)
	 * even though these characters have no formalized URI delimiting uses
	 * 
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURI
	 */
    return encodeURIComponent(str).
        // Note that although RFC3986 reserves "!", RFC5987 does not,
        // so we do not need to escape it
        replace(/['()]/g, escape). // i.e., %27 %28 %29
        replace(/\*/g, '%2A').
            // The following are not required for percent-encoding per RFC5987, 
            //  so we can allow for a little better readability over the wire: |`^
            replace(/%(?:7C|60|5E)/g, unescape);
},

"doubleEncodeURIComponent" : function (str) {
	return encodeURIComponent(ff.encodeURIComponent(str));
},

"urlAddParam" : function(url, name, value) {
	var parts = url.split("#");

	var url = parts[0];
	var anchor = parts[1];

	parts = url.split("?");
	url = parts[0];
	var query = parts[1];

	url += "?" + name;
	if (value !== undefined)
		url += "=" + ff.encodeURIComponent(value);
	url += "&";
	
	if (query) {
		parts = query.trim("&").split("&");
		var newquery = "";
		for (var i = 0; i < parts.length; i++) {
			var subparts = parts[i].split("=");
			if (subparts[0] != name) {
				newquery = newquery + subparts[0];

				if (subparts[1] !== undefined)
					newquery += "=" + subparts[1];

				newquery += "&";
			}
		}
		
		url = url + newquery;
	}

	if (anchor !== undefined)
		url = url + "#" + anchor;
	
	return url;
},

"argsAsArray" : function (args) {
	var arrArgs = [];
	for (p in args) {
		if (args.hasOwnProperty(p))
			arrArgs.push(args[p]);
	}
	return arrArgs;
},

"fixPath" : function(source) {
	if(that.site_path !== undefined && that.site_path.length > 0 && source.indexOf("/") == 0 && source.indexOf(that.site_path) != 0)
		source = that.site_path + source;
	
	var req_origin = null;
	var ret = source.parseUri();
	if (ret.host != "")
		req_origin = (ret.protocol == "" ? "http" : ret.protocol) + "://" + ret.host;
	else if(that.origin !== undefined && that.origin.length > 0)
		req_origin = that.origin;

	var cross_xhr = false;
	if(req_origin !== null  && that.httpGetOrigin() != req_origin) {
		var cross_xhr = true;
		
		if (source.indexOf(req_origin) != 0)
			source = req_origin + source;
		
		source = ff.urlAddParam(source, "__FORCE_XHR__");
	}
	
	var res = ff.doEvent({
		"event_name"	: "fixPath",
		"event_params"	: [source, cross_xhr]
            });
            
	if (res !== undefined && res[res.length - 1]) {
		source = res[res.length - 1];
	}
	
	return source;
},

"initFF" : function (params) {
    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }
    that.site_path				= (params.site_path === undefined ? "/" : params.site_path);
    that.theme					= (params.theme === undefined ? "default" : params.theme);
    that.theme_ui				= (params.theme_ui === undefined ? "" : params.theme_ui);

    if(that.site_path.length > 0 && that.site_path != '/') {
            that.page_path 			= window.location.pathname.substr(that.site_path.length);
    } else {
            that.page_path 			= window.location.pathname;
    }
    that.origin					= (params.origin !== undefined ? params.origin : that.httpGetOrigin());
    that.domain 				= window.location.hostname;
    that.language 				= getCookie("lang") || (params.language === undefined ? "ITA" : params.language);
    that.locale 				= (params.locale === undefined ? "it_IT" : params.locale);
    that.layer 					= (params.layer === undefined ? "empty" : params.layer);
    that.group                                  = getCookie("group") || (params.group === undefined ? "" : params.group);
    that.js_path				= (params.js_path === undefined ? that.site_path + '/themes/' + that.theme + '/javascript' : params.js_path);

    if(params.lazyImg) {
        jQuery(function() {
            ff.lazyImg();
        });
    }
    if (params.struct !== undefined)
            that.struct = params.struct;
    else {
            that.struct		= that.hash();
    }
    if (that.struct.fields === undefined)
            that.struct.fields = that.hash();

    if (params.libs !== undefined)
            that.libs = params.libs;
    else {
            that.libs = that.hash();
    }

    // used to avoid cache bug
    that.struct.each(function (key, value, index) {
            value.url = window.location.href;
    });

    // ------------------------------------------------------------------------------------------------------
    // force event's infrastructure loading at start (when not already present)
    that.pluginLoad("ff.ffEvent",		"/themes/library/ff/ffEvent.js",	undefined, false);
    that.pluginLoad("ff.ffEvents",		"/themes/library/ff/ffEvents.js",	undefined, false);
    // ------------------------------------------------------------------------------------------------------

    that.libs.each(function (key, value) {
            that.pluginLoad(key, value && (that.site_path + value), undefined, false);
    });

    initLibs(that, "ff");
},

"extend" : function (object) {
	jQuery.extend(true, ff, object);
},

"pluginLoad" : function (id, source, callback, async) {
	if (callback !== undefined)
		that.pluginAddInitLoad(id, callback);

	if (plugins.isset(id) === undefined) {
		plugins.set(id, false);
		//jQuery.getScript(source, function (data, textStatus) {

		var objFF = id.split(".");
		var strFF = '';
		var objFF_is_loaded = true;

		for (var x=0; x<objFF.length; x++)
		{
			if(strFF == '') {
				strFF = objFF[x];
				if(strFF == 'jquery') {
					strFF = 'jQuery';
				}

				strFF = 'window' + '["' + strFF  + '"]';
			} else {
				strFF = strFF + '["' + objFF[x] + '"]';
			}

			try {
				if(eval(strFF) === undefined) {
					objFF_is_loaded = false;
					break;
				}
			} catch(error){
				objFF_is_loaded = false;
				break;
			}
		}

		if(objFF_is_loaded) {
			if (id === "ff.ffEvents") {
				ff.extend(that.ffEvents());
				jQuery.extend(true, ff.fn, ff.ffEvents());
				ff.pluginInitLoad("ff");
			}

			ff.pluginInitLoad(id);
		} else {
			jQuery.ajax({
				"async": async,
				"url": that.fixPath(source),
				/*"dataType": "script",*/
	//			"cache": false,
				"success": function (data) {
					if (id === "ff.ffEvents") {
						ff.extend(that.ffEvents());
						jQuery.extend(true, ff.fn, ff.ffEvents());
						ff.pluginInitLoad("ff");
					}

					//ff.pluginInitLoad(id);
					jQuery("head").append('<script type="text/javascript">ff.pluginInitLoad("' + id + '");</script>');
				}
			});
		}
	}
},

"pluginAddInitLoad" : function (id, callback, uuid) {
	if (uuid !== undefined) {
		if (inits_uuid.isset(uuid) !== undefined)
			return;
		else
			inits_uuid.set(uuid);
	}
	
	if (plugins.get(id) !== true) {
		if (plugins_loads.isset(id) === undefined)
			plugins_loads.set(id, []);
		
		plugins_loads.get(id).push(callback);
	} else
		callback();
},

"pluginInitLoad" : function (id) {
	if (id !== "ff.ffEvent" && id !== "ff.ffEvents")
		initEvents(id);

	var loads;
	if ((loads = plugins_loads.get(id)) !== undefined) {
		for (var i = 0; i < loads.length; i++) {
			loads[i]();
		}
		plugins_loads.set(id, []);
	}
	pluginInit(id);
	plugins.set(id, true);
},

"pluginAddInit" : function (id, callback, uuid) {
	if (uuid !== undefined) {
		if (inits_uuid.isset(uuid) !== undefined)
			return;
		else
			inits_uuid.set(uuid);
	}
	
	if (plugins.get(id) !== true) {
		if (plugins_inits.isset(id) === undefined)
			plugins_inits.set(id, []);

		plugins_inits.get(id).push(callback);
	} else
		callback();
},

"CSSload" : function(link, callback) {
    var cssLoaded = false;
    if(!step) step = {};

    try {
        if (link.sheet && link.sheet.cssRules.length > 0 ) {
            cssLoaded = true;
        } else if (link.styleSheet && link.styleSheet.cssText.length > 0 ) {
            cssLoaded = true;
        } else if (link.innerHTML && link.innerHTML.length > 0 ) {
            cssLoaded = true;
        }
    } catch(ex) { 
    	step[link] = 10;
	}
	
    if (cssLoaded) {
    	if(callback !== undefined)
        	callback();
    } else {
    	if(parseInt(step[link]) < 10) {
    		step[link] = parseInt(step[link]) + 1;
	        setTimeout(function () {
	            CSSload(link, callback, step);
	        }, 10);
		}
    }
},
"preloadCSS" : function(id) {
	loaded_css.set(id, true);
},
"injectCSS" : function (id, source, callback, media) {
	//document.styleSheets
	if (loaded_css.get(id)) {
		if (callback)
			callback();

		return;
	}

	loaded_css.set(id, true);
	source = that.fixPath(source);
	if(source) {
		/* ie only */
		/*if ('\v' == 'v')  {
			var css = document.createStyleSheet(source);
			
			if(media) 
				css.media = media;

		} else {
			var attrMedia = "";

			if(media) {
				attrMedia = 'media="' + media + '"';
			}

			var css = jQuery('<link rel="stylesheet" href="' + source + '" ' + attrMedia + ' />');
			if(css)
				jQuery('head').append(css);
			
			//ff.CSSload(css.get(0), callback);
		}*/

		var css = document.createElement("link");
		css.setAttribute('rel', 'stylesheet');
		css.setAttribute('type', 'text/css');
		css.setAttribute('href', source);
		if(media)
			css.setAttribute('media', media);
		
		/*css.addEventListener('load', function() { 
		    if(callback)
		        callback();
		});*/
		document.getElementsByTagName('head')[0].appendChild(css);			
		
	}
	
	if(callback)
		callback();
    //if(callback !== undefined)
    //	callback();

	/*
	var async = true;
	var css = document.createElement('link');
	css.setAttribute('rel', 'stylesheet');
	css.setAttribute('href', source);
	css.addEventListener('load', function() { alert('foo'); }, false);
	document.getElementsByTagName('head')[0].appendChild(css);
	
	jQuery.ajax({
			"async": async,
			"url": source,
			"dataType": "text",
			"success": function (data) {
//					jQuery("head").append('<style type="text/css">' + data + '</style>');
				if (callback !== undefined)
					callback();
			}
		});*/
},

"addLoadEvent" : function (func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
},

"submitProcessKey" : function (e, button) {
    if (null == e)
        e = window.event;
    if (e.keyCode == 13)  {
    	document.getElementById(button).focus();
        document.getElementById(button).click();
        return false;
    }
},

"clearComponent" : function (component) {
	if (ff.struct.get(component) !== undefined) {
		if (ff.struct.get(component).type == "ffGrid")
			if(ff.ffPageNavigator !== undefined) {
				try {
					ff.ffPageNavigator.deleteNavigator(component);
				} catch (e) {};
			}
		ff.struct.get(component).fields.each( function (key, field) {
			ff.doEvent({
				"event_name"	: "onClearField",
				"event_params"	: [component, key, field]
			});
		});
		ff.struct.unset(component);
	}
},

"utf8" : {
	"encode" : function(s) {
	  return unescape(encodeURIComponent(s));
	},

	"decode" : function(s) {
	  return decodeURIComponent(escape(s));
	}
},
"encodeEntities" : function(s) {
	return jQuery("<div/>").text(s).html();
},
"decodeEntities" : function(s) {
	return jQuery("<div/>").html(s).text(); 
},
"getFields" : function (container, dialog) {
	that.doEvent({"event_name": "getFields", "event_params" : [container, dialog]});

	if (container !== undefined)
		return jQuery(':input[name][name!=""]', container).not("input:checkbox:not(:checked)").not("input:radio:not(:checked)");
	else
		return jQuery(':input[name][name!=""]').not("input:checkbox:not(:checked)").not("input:radio:not(:checked)");

},
"numberToCurrency" : function(number, decimalSeparator, thousandsSeparator, nDecimalDigits){
    //default values
	decimalSeparator = decimalSeparator === undefined ? "," : decimalSeparator;
	thousandsSeparator = thousandsSeparator === undefined ? "." : thousandsSeparator;
    nDecimalDigits = nDecimalDigits === undefined ? 2 : nDecimalDigits;

    var fixed = number.toFixed(nDecimalDigits), //limit/add decimal digits
        parts = new RegExp('^(-?\\d{1,3})((?:\\d{3})+)(\\.(\\d{'+ nDecimalDigits +'}))?$').exec( fixed ); //separate begin [$1], middle [$2] and decimal digits [$4]

    if(parts){ //number >= 1000 || number <= -1000
        return parts[1] + parts[2].replace(/\d{3}/g, thousandsSeparator + '$&') + (parts[4] ? decimalSeparator + parts[4] : '');
    }else{
        return fixed.replace('.', decimalSeparator);
     }
},
"isMobile" : function(type) {
	var res = false;
	
    switch(type) {
		case "Android":
			res = navigator.userAgent.match(/Android/i);
		case "BlackBerry":
			res = navigator.userAgent.match(/BlackBerry/i);
		case "iOS":
			res = navigator.userAgent.match(/iPhone|iPad|iPod/i);
		case "iPad":
			res = navigator.userAgent.match(/iPad/i);
		case "Opera":
			res = navigator.userAgent.match(/Opera Mini/i);
		case "Windows":
			res = navigator.userAgent.match(/IEMobile/i);
		default:		
			res = navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);	
    }
    return res;
},
"browser" : function ( ua ) {
  var matched, browser;

  if(ua === undefined)
  	ua = nagivator.userAgent;

    ua = ua.toLowerCase();

    matched = ff.getBrowser( ua );
    browser = {};

    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }

    // Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }

    return browser;
},
"getBrowser" : function( ua ) {
    // Use of jQuery.browser is frowned upon.
    // More details: http://api.jquery.com/jQuery.browser
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
},
"slug" : function(slugcontent) {
    slugcontent = slugcontent.toLowerCase();
    
    slugcontent = slugcontent.replace(/[\xC0-\xC5\xE0-\xE5]/g, 'a'); /* accentate tradotte nella versione normale*/
    slugcontent = slugcontent.replace(/[\xC8-\xCB\xE8-\xEB]/g, 'e');
    slugcontent = slugcontent.replace(/[\xCC-\xCF\xEC-\xEF]/g, 'i');
    slugcontent = slugcontent.replace(/[\xD2-\xD6\xF2-\xF6]/g, 'o');
    slugcontent = slugcontent.replace(/[\xD9-\xDC\xF9-\xFC]/g, 'u');
    slugcontent = slugcontent.replace(/[\x9F\xDD\xFD\xFF]/g, 'y');
    slugcontent = slugcontent.replace(/[\x8E\x9E]/g, 'z');
    slugcontent = slugcontent.replace(/[\x8A\x9A]/g, 's');
    slugcontent = slugcontent.replace(/[\xD1\xF1]/g, 'n');

    slugcontent = slugcontent.replace(/[^a-z0-9]/g, '-'); /* tutti i non-alfanumerici con uno trattino*/
    slugcontent = slugcontent.replace(/\-+/g, '-'); /* piÃƒÂ¹ di un trattino con un solo trattino*/
    slugcontent = slugcontent.replace(/^\-/g,''); /* rimuove eventuali trattini iniziali*/
    slugcontent = slugcontent.replace(/\-$/g,''); /* rimuove eventuali trattini finali*/ 
    
    return slugcontent;
},
"inView" : function(el, margin) {
 if(!margin)
	margin = 1;

 if(typeof el == "string")
 	el = jQuery(el)[0];
 
 if(typeof el == "object") {
	 var rect = el.getBoundingClientRect();
         var wh = parseInt(window.innerHeight || document.documentElement.clientHeight);

	 if(margin)
	 	margin = wh * parseFloat(margin); 

     return (
	    (rect.top + rect.height + margin >= 0
                && rect.top <= wh + margin 
            )
           /* && (rect.left >= 0
                || rect.left + margin <= (window.innerWidth || document.documentElement.clientWidth)
            )*/
	 )
 }
},
"lazyImg" : function(){
    var images = jQuery('img.lazy, iframe.lazy');

    jQuery(window).unbind("scroll.lazyImg");
    jQuery(images).load(function() {
        var placeholder = jQuery(this).prevAll(".lazyloader").get(0) || jQuery(this).parent().prevAll(".lazyloader").get(0);
        if(placeholder)
            jQuery(placeholder).remove();
    });

    function loadImage (el, fn) {
        var src = el.getAttribute('data-src');
	if(src) {
            el.setAttribute("src", src);
            el.removeAttribute('data-src');

            fn ? fn() : null;
	}
    };

    var processScroll = function(){
      for (var i = 0; i < images.length; i++) {
        var placeholder = jQuery(images[i]).prev(".lazyloader").get(0) || jQuery(images[i]).parent().prev(".lazyloader").get(0);
        
        if (/*jQuery(images[i]).is(":hidden") ||*/ ff.inView(placeholder || images[i])) {
          loadImage(images[i], function () { 
            jQuery(images[i]).prevAll("source").each(function() {
                var srcset = jQuery(this).attr('data-srcset');
                jQuery(this).attr("srcset", srcset);
		jQuery(this).removeAttr('data-srcset');
            });
            jQuery(images[i]).removeClass("lazy");

            //if(jQuery(images[i]).is(":hidden") && placeholder)
             //   placeholder.remove();
                
            images.splice(i, 1);
            i--;
          });
        }
      }

      if(!images.length) 
      	jQuery(window).unbind("scroll.lazyImg");
    };
    
    if(images.length) {
        jQuery(window).bind("scroll.lazyImg", processScroll); 
        processScroll();
    }
},
// *******************************
//  Hash Table - version 1.6

"hash" : function (initdata) {
	// privates
	
	var that = {
	// publics
	"__class__"	: "hash",

	"keys"		: [],
	"values"	: [],
	"length"	: 0,

	//functions
	"set" : function (key, value) {
		var index = that.keys.indexOf(key);
		if (index > -1) {
			that.values[index] = value;
			return index;
		} else {
			that.keys.push(key);
			that.values.push(value);
			that.length++;
			return that.length - 1;
		}
	},

	"get" : function (key) {
		var index = that.keys.indexOf(key);
		if (index > -1)
			return that.values[index];
		else
			return undefined;
	},

	"indexget" : function (index) {
		if (index > 0 && index < that.length)
			return that.values[index];
		else
			return undefined;
	},

	"isset" : function (key) {
		var index = that.keys.indexOf(key);
		if (index > -1)
			return index;
		else
			return undefined;
	},

	"find" : function (value, offset) {
		var index = that.values.indexOf.apply(that.values, arguments);
		if (index > -1) {
			return {"index" : index, "key" : that.keys[index]};
		} else {
			return undefined;
		}
	},

	"keyfind" : function (value, key) {
		var index;

		if (key === undefined)
			index = that.values.indexOf(value);
		else {
			index = that.keys.indexOf(key);
			if (index > -1)
				index = that.values.indexOf(value, index);
			else
				return undefined;
		}

		if (index > -1) {
			return {"index" : index, "key" : that.keys[index]};
		} else {
			return undefined;
		}
	},

	"unset" : function (key) {
		var index = that.keys.indexOf(key);
		if (index > -1) {
			that.keys.splice(index, 1);
			that.values.splice(index, 1);
			that.length--;
			return that.length;
		} else {
			return undefined;
		}
	},

	"indexunset" : function (index) {
		if (index > 0 && index < that.length)
			return undefined;
		else {
			that.keys.splice(index, 1);
			that.values.splice(index, 1);
			that.length--;
			return that.length;
		}
	},

	"each" : function (func) {
		var tmp_keys	= that.keys.slice();
		var tmp_values	= that.values.slice();
		var l = tmp_keys.length;

		for (var i = 0; i < l; i++) {
			$rc = func(tmp_keys[i], tmp_values[i], i);
			if ($rc)
				break;
		}

		return l;
	},

	"clear" : function () {
		that.keys.length = 0;
		that.values.length = 0;
		that.length = 0;
		return that;
	},

	"merge" : function (obj) {
		obj.each(function (key, value, index) {
			if (typeof(value) == "object" && value.__class__ == "hash")
			{
				var tmp = that.get(key);
				if (typeof(tmp) == "object" && tmp.__class__ == "hash")
					tmp.merge(value);
				else
					that.set(key, value);
			}
			else
				that.set(key, value);
		});
		return that;
	},
	
	"dump" : function (prefix) {
		prefix = (prefix === undefined ? "" : prefix);
		that.each(function (key, value, index) {
			if (prefix)
				console.log(prefix + ">", index, key, value);
			else
				console.log(index, key, value);
			if (typeof(value) == "object" && value.__class__ == "hash")
				value.dump(prefix + "--");
		});
	}
};

// constructor
if (typeof(initdata) == "object") {
	if (initdata.__class__ == "hash") {
		that.keys	= initdata.keys;
		that.length = initdata.length;
		
		for (var i = 0; i < initdata.values.length; i++) {
			if (typeof(initdata.values[i] == "object") && initdata.values[i].__class__ == "hash")
				that.values[i] = ff.hash(initdata.values[i]);
			else
				that.values[i] = initdata.values[i];
		}
	}
}

return that;
}
// ********* Hash Table *********

}; // publics' end

// hash initializations
loaded_css		= that.hash();
plugins			= that.hash();
plugins_loads	= that.hash();
plugins_inits	= that.hash();
inits_uuid		= that.hash();

return that;

// code' end.
})();
