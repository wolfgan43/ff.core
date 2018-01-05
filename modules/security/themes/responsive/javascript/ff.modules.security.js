/**
 * Forms Framework Javascript Handling Object
 *	module security namespace
 */

ff.modules.security = (function () {
// inits
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};

ff.pluginAddInit("ff", function () {
	ff.addEvent({
		"event_name" : "fixPath"
		, "func_name" : function (source, cross_xhr, lastret) {
			var ret = lastret || source;

			if (cross_xhr && ff.modules.security.session.session_name) {
				var session_value = ff.modules.security.session_id();

				if ((session_value && session_value.length > 0)) {
					ret = ff.urlAddParam(ret, ff.modules.security.session.session_name, session_value);
				}
			}

			return ret;
		}
	});
});

ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name"	: "onRedirect"
		, "func_name"	: function (url, data, customdata) {
			if (data && data.modules && data.modules.security && data.modules.security.prompt_login) {
				if (customdata && customdata.params && customdata.params.customdata && customdata.params.customdata.caller) {
					var ev = ff.modules.security.addEvent({
						"event_name" : "login"
						, "func_name" : function () {
							customdata.params.customdata.caller.func.apply(this, customdata.params.customdata.caller.args);
							ev.remove();
						}
					});
				} else if (customdata && customdata.caller) {
					var ev = ff.modules.security.addEvent({
						"event_name" : "login"
						, "func_name" : function () {
							customdata.caller.func.apply(this, customdata.caller.args);
							ev.remove();
						}
					});
				}
				
				ff.modules.security.openLoginDialog(undefined, url, (customdata ? customdata.parsed_url : undefined));
				
				return {"break" : true};
			}
		}
	});
	
	ff.ajax.addEvent({
		"event_name"	: "onSuccess"
		, "func_name"	: function (data, params, injectid) {
			if (data.modules && data.modules.security) {
				// update session data
				ff.modules.security.session.session_name	= data.modules.security.session_name;
				ff.modules.security.session.loggedin		= data.modules.security.loggedin;
				ff.modules.security.session.session_id		= data.modules.security.session_id;
				ff.modules.security.session.UserNID			= data.modules.security.UserNID;
			}
		}
	});
	
	ff.ajax.addEvent({
		"event_name"	: "onRequestDone"
		, "func_name"	: function (data, params, injectid) {
			if (data && data.modules && data.modules.security && data.modules.security.action) {
				var res = ff.modules.security.doEvent({
					"event_name"	: data.modules.security.action,
					"event_params"	: [that, data, params.customdata]
				});
				//return {"break" : true};
			}
		}
	});	
});

// privates
var social_window = undefined;
var dialog_opened = undefined;

var that = { // publics
__ff : true, // used to recognize ff'objects

services : {
	"login" : null
	, "check_session" : null
	, "oauth2" : null
},
session : {
	"session_name" : null
	, loggedin : null
	, "session_id" : null
	, "UserNID" : null
},

"session_id" : function () {
	return (
				ff.modules.security.session.session_id ? ff.modules.security.session.session_id
				: (jQuery.cookie ? jQuery.cookie(ff.modules.security.session.session_name) : undefined)
			);
},

openLoginDialog : function (title, url, ret_url, id) {
	title = title || "Login";
	dialog_opened = id || "loginDlg";
	url = url || that.services.login + (ret_url ? "?ret_url=" + encodeURIComponent(ret_url) : "");
	//window.location.origin + window.location.pathname.rtrim("/") + '/login' + (ret_url ? "?ret_url=" + encodeURIComponent(ret_url) : "");
	
	//, '{_login_title}'
	ff.pluginLoad("ff.ajax", "/themes/library/ff/ajax.js", function() {
		ff.pluginLoad("jquery-ui", "/themes/library/jquery-ui/jquery-ui.js", function() {
			ff.pluginLoad("ff.ffPage.dialog", "/themes/restricted/ff/ffPage/widgets/dialog/dialog.js");
		});
	});

	ff.pluginAddInit("ff.ffPage.dialog", function () {
		ff.ffPage.dialog.addDialog({
			"id" : dialog_opened,
			"callback" : "",
			"url" : "",
			"resizable" : true,
			"position" : "center",
			"draggable" : true,
			"title" : title,
			"height" : 500,
			"width" : 800
		});
		ff.ffPage.dialog.addEvent({
			"event_name"	: "onClose"
			, "func_name"	: function (id) {
				dialog_opened = undefined;
			}
		});
		ff.ffPage.dialog.doOpen(dialog_opened, url, title);
	});
},
reloadPageBySocialLogin : function(ret_url) {
	if(ret_url && ret_url.indexOf("/login") < 0 && window.opener.location.pathname == "/") {
        window.opener.location = ret_url; 
	} else if(window.opener.location.pathname.indexOf("/login") >= 0) {
		window.opener.location = "/"; 
	} else {
	    window.opener.location.reload();
	}

    window.close(); 
},	
social : {
	"requestLogin" : function (title, url) {
		social_window = window.open(
				ff.fixPath(url)
				, title
				, "menubar=no, status=no, height=500, width= 500"
			);
	
		ff.modules.security.social.checkLoginEvent();
	},
	
	"checkLoginEvent" : function () {
		try {
			if (social_window.ff.fn.modsec.login_status) {
				social_window.close();
				ff.modules.security.doEvent({
					"event_name"	: "login",
					"event_params"	: []
				});
				return;
			}
		} catch (e) {
		}

		try {
			if (social_window.window) {
				//console.log("listening..", that.new_window);
				setTimeout(ff.modules.security.social.checkLoginEvent, 3000);
			}
		} catch (e) {
		}
	}
},
onSubmit : function(containerSuccess) {
	ff.pluginAddInit("ff.ajax", function () {
		var ev = ff.ajax.addEvent({
			"event_name"	: "onSuccess"
			, "func_name"	: function (data, params, injectid) {
				if (data.modules && data.modules.security && data.modules.security.action) {
					if(data.success) {
						if(!containerSuccess)
							containerSuccess = injectid;

						ff.injectCSS("ff.modules.security", "/cm/showfiles.php/modules/security/responsive/css/ff.modules.security.css", function() {
							jQuery(containerSuccess).replaceWith(data.modules.security.message);
						});
					} else {
						jQuery(injectid + " .secError").remove();
	                    jQuery(injectid + " .disabled").css({'opacity': '', 'pointer-events': ''}).removeClass("disabled"); 
	                    jQuery(injectid).find("input:first").parent().prepend(data.modules.security.error).children(":first").addClass("secError");
					}
				}

                ev.remove();
			}
		});
	});
},
submit : function(action, component, elem, url, containerSuccess) { 
	if(!component)
		component = dialog_opened;

	var componentID = "#" + component;
		
	if(!action)
		action = (!ff.group ? "login" : "logout");

	if(!url)
		url = '/login';

	var ret_url = getParameterByName("ret_url");
	if(ret_url)
		url = ff.urlAddParam(url, "ret_url", ret_url);

	if(jQuery(componentID).length) {
		if(dialog_opened) {
			if(elem)
				jQuery(elem).addClass("disabled").css({'opacity': 0.5, 'pointer-events': 'none'}); 

			ff.ffPage.dialog.doRequest(dialog_opened, {
					'action' : action
					, 'injectid'    : componentID
			});		
		} else {
			if(elem)
				jQuery(elem).addClass("disabled").css({'opacity': 0.5, 'pointer-events': 'none'}); 

            ff.pluginLoad("ff.ajax", "/themes/library/ff/ajax.js", function() {
                ff.ajax.doRequest({
                    'action' : action
                    , 'formName'    : component 
                    , 'injectid'    : componentID  
                    , 'url'         : url
                });
            });
		}
		
		this.onSubmit(containerSuccess);
	}
}

}; // publics' end

return that;

// code's end.
})();
