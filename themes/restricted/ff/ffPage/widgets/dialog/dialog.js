/**
 * Forms Framework Javascript Handling Object
 *    dialog page' plugin namespace
 */

jQuery.widget("ui.dialog", jQuery.extend({}, jQuery.ui.dialog.prototype, {
    _title: function(title) {
        if (!this.options.title ) {
            title.html("&#160;");
        } else {
            title.html(this.options.title);
        }
    }
}));

ff.ffPage.dialog = (function () {

// inits

var old_overflow = {
	"dialog" : null
	, "value" : null
};

/** mod di alex **/
var responsive = null;
var unique = null;
/** fine mod di alex **/

ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name" : "onUpdateContent"
		, "func_name" : function (params, data, injectid) {
			if (params.ctx && ff.ffPage.dialog.exist(params.ctx) && params.component === undefined) {
				if (!ff.ffPage.dialog.get(params.ctx).instance) 
					ff.ffPage.dialog.makeInstance(params.ctx);

				ff.ffPage.dialog.removeDlgBt(params.ctx, data);
			}
		}
	});
	ff.ajax.addEvent({
		"event_name" : "onRedirect"
		, "func_name" : function (url, data, mydata, params) {
			if (
				(params.ctx && ff.ffPage.dialog.get(params.ctx) && ff.ffPage.dialog.getInstance(params.ctx))
				&& (params.doredirects || data["doredirects"] !== undefined)
			) {
				ff.ffPage.dialog.getInstance(params.ctx).dialog("close"); 
			}
		}
	});
	ff.ajax.addEvent({
		"event_name" : "ctxInitDone"
		, "func_name" : function (id) {
			if (dialogs.get(id) && dialogs.get(id).waiting) {
				ff.ajax.unblockUI();
				ff.ffPage.dialog.refresh(id);
				return true;
			}
		}
	});
}, "a65c581e-64d9-473d-a166-c1848dab04cb");

/*jQuery('html, body').on('touchstart touchmove', function(e){ 
	 //prevent native touch activity like scrolling
	 if (old_overflow !== null) {
		e.preventDefault();
	 }
});*/

/* privates */
var dialogs		= ff.hash();

var that = { /* publics */
__ff : true, /* used to recognize ff'objects */

"dialog_params"        : ff.hash(),
/*"dialog_deps"        : ff.hash(),*/

"addDialog" : function (params) {
	/** mod di alex **/
	if(unique === null && params.unique) {
		unique = params.id;
	}
	unique = null;
	/** fine mod di alex **/
	
	if (that.dialog_params.isset(params.id))
		return;
	
    that.dialog_params.set(params.id, {
        "callback"		: params.callback,
        "url"			: params.url,
        "title"			: params.title,
        "params"        : params.params || {},
        "height"        : params.height,
        "width"			: params.width,
        "resizable"		: params.resizable,
        "position"		: params.position,
        "draggable"		: params.draggable,
        "doredirects"	: params.doredirects,
        "modal"			: (params.modal === undefined ? true : false),
    });
	
	ff.ajax.ctxAdd(params.id, that, "dialog");

	/** mod di alex **/
	if(responsive === null && params.responsive) {
		responsive = params.responsive;
	}
	/** fine mod di alex **/

    that.doEvent({
        "event_name"    : "onAddDialog",
        "event_params"    : [params.id]
    });
},

"get" : function (id) {
    return dialogs.get(id);
},

"exist" : function (id) {
	return that.dialog_params.isset(id);
},

"getInstance" : function (id) {
    return dialogs.get(id).instance;
},

"replaceHTML" : function (id, data) {
	that.getInstance(id).html(data["html"]);
	ff.ffPage.dialog.makeDlgBt(id, data);
},

"param" : function (id, param, value) {
    if (value !== undefined)
        that.dialog_params.get(id)[param] = value;
    else
        return that.dialog_params.get(id)[param];
},

"adjSize" : function (id) {
	var instance = dialogs.get(id).instance;
	var widget = instance.dialog("widget");
	
	var wh = jQuery(window).height();
	var ww = jQuery(window).width();
	
	if (widget.outerHeight(false) > wh * 0.90) {
		instance.dialog("option", "height", wh * 0.90);
	}

	if (widget.outerWidth(false) > ww * 0.90) {
		instance.dialog("option", "width", ww * 0.90);
	}
	
	var tmp_params = that.dialog_params.get(id) || {};
	if (tmp_params.params && tmp_params.params.min_width && widget.outerWidth(false) < tmp_params.params.min_width) {
		instance.dialog("option", "width", tmp_params.params.min_width);
	}
},

"refresh" : function (id) {
	var instance = dialogs.get(id).instance;
		
	that.adjSize(id);
	
	var widget = instance.dialog("widget");
	
	widget.position({my : "left top", at : "left top", of : window});
	widget.position({my : "center center", at : "center center", of : window});

	if (!instance.dialog("isOpen")) {
		instance.dialog("open");
	}
	
	ff.ffPage.dialog.doEvent({
		"event_name" : "resize"
		, "event_params" : [id, undefined]
	});

	return true;
},

"makeInstance" : function (id) {
	if (old_overflow.dialog === null) {
		old_overflow.value = jQuery("body").css("overflow");
		if (old_overflow.value != "hidden") {
			jQuery("body").css("overflow", "hidden");
			old_overflow.dialog = id;
		}
		else
			old_overflow.value = null;
	}
	
	var tmp_params = that.dialog_params.get(id) || {};
	
	dialogs.get(id).instance = jQuery('<div id="ffWidget_dialog_container_' + id + '"></div>').dialog({
		autoOpen: false
		, resizable: tmp_params.resizable
		, position: { my: tmp_params.position, at: "center", of: window }
		, modal: tmp_params.modal
		, draggable: tmp_params.draggable
		, close: function(ev, ui) {
			that.onClose(id);
			if (id === old_overflow.dialog) {
				jQuery("body").css("overflow", old_overflow.value);
				old_overflow.value = null;
				old_overflow.dialog = null;
			}
		}
		, title: tmp_params.title
		, height: tmp_params.height || "auto"
		, width: tmp_params.width || "auto"
		, maxHeight: jQuery(window).height() * 0.90
		, maxWidth: jQuery(window).width() * 0.90
		, minWidth: tmp_params.params.min_width || 350
		, resizeStop : function (ev, ui) {
			var diffx = Math.floor(ui.size.width) - Math.floor(jQuery("#ffWidget_dialog_container_" + id).dialog("widget").width());
			if (diffx) jQuery("#ffWidget_dialog_container_" + id).width(jQuery("#ffWidget_dialog_container_" + id).width() - diffx);
			var diffy = Math.floor(ui.size.height) - Math.floor(jQuery("#ffWidget_dialog_container_" + id).dialog("widget").height());
			if (diffy) jQuery("#ffWidget_dialog_container_" + id).height(jQuery("#ffWidget_dialog_container_" + id).height() - diffy);
			ff.ffPage.dialog.doEvent({
				"event_name" : "resize"
				, "event_params" : [id, ui]
			});
		}
		, hide: {effect: 'fade', duration: 200}
		, open: function() {
			var widget = jQuery(this).dialog("widget");
			//non fa piu ridimensionare le dialog
			/*if (widget.get(0).style.height == "auto")
				widget.height(widget.height());

			if (widget.get(0).style.width == "auto")
				widget.width(widget.width());

			if (jQuery(".ui-dialog-content", widget).get(0).style.height == "auto")
				jQuery(".ui-dialog-content", widget).height(jQuery(".ui-dialog-content", widget).height());*/
        
        	jQuery(".ui-dialog-content", widget).css("width", "");
		}
	});
	return dialogs.get(id).instance;
},

"doOpen" : function (id, url, title, preserveIstance) {
	/** mod di alex **/
	//unique = null;
	if(unique && dialogs.keys.length) {
		preserveIstance = true;
		if(!url)
			url = that.dialog_params.get(id)["url"];
		if(!title)
			title = that.dialog_params.get(id)["title"];

		id = unique;
	}
	/** fine mod di alex **/
	
    if (url !== undefined && that.dialog_params.get(id) !== undefined )
        that.dialog_params.get(id)["url"] = url;
	
	that.updateCursor(id, that.dialog_params.get(id)["url"]);

    if (title !== undefined && title.length > 0 && that.dialog_params.get(id) !== undefined)
        that.dialog_params.get(id)["title"] = title;

    if (dialogs.get(id) && dialogs.get(id).instance) {
        if(dialogs.get(id).params.current_url != url && preserveIstance) {
			/*var breadCrumbs = dialogs.get(id).breadCrumbs;
        	var actionBack = false;
        	if(breadCrumbs.length > 0) {
        		for(var i=0; i< breadCrumbs.length; i++) {
        			if(breadCrumbs[i]["url"] == url) {
        				breadCrumbs = breadCrumbs.splice(i);
        				actionBack = true;
        				break;
					}
				}
        	}
        	if(!actionBack)
        		breadCrumbs.push({"title":  jQuery(".ui-dialog-title > :not(.breadcrumbs)").text(), "url" : dialogs.get(id).params.current_url});
			*/	
            ff.ffPage.dialog.goToUrl(id, url);
        } else {
            dialogs.get(id).instance.dialog("open");
        }
        return;
    }
	
	ff.ajax.ctxGet(id).reset();
	
    dialogs.set(id, {
		"instance"	: null,
		"params"	: jQuery.extend(true, {}, that.dialog_params.get(id)),
		//"breadCrumbs": []
		
	});

    dialogs.get(id).params.current_url = dialogs.get(id).params.url;
    
    var evres = that.doEvent({"event_name": "doOpen", "event_params" : [that, id, url, title]});
    if (evres !== true) {
        ff.ajax.doRequest({
            "url"			: that.parseUrl(id, dialogs.get(id).params.current_url),
            "type"			: "GET",
            "callback"		: that.onSuccess,
            "customdata"	: {
                "id" : id
				, "caller" : {
					"func" : ff.ffPage.dialog.doOpen
					, "args" : ff.argsAsArray(arguments)
				}
            },
            "injectid"		: dialogs.get(id).instance,
            "ctx"			: id,
			"brandnew"		: true,
            "doredirects"	: dialogs.get(id).params.doredirects
        });
    }
},

"close" : function (id) {
	dialogs.get(id).instance.dialog("close");
},

"onSuccess" : function (data, customdata) {
    var id = customdata.id;

    if (data === null) {
        if (dialogs.get(id).params.params && dialogs.get(id).params.params.persistent)
            dialogs.get(id).params.params.persistent = false;
        dialogs.get(id).instance && dialogs.get(id).instance.dialog("close");
        return false;
    }

    /**
     *    data.close
     *        true = chiude il dialog
     *        false = valorizza (o aggiorna) il dialog
     *
     *    data.refresh
     *        true = imposta il dialog per l'aggiornamento del chiamante su chiusura
     *
     *    data.html
     *        contenuto del dialog se aggiornato
     *
     *    data.url
     *        cambia l'url del dialog su redirect interno
     */
    
    if (customdata.callback)
        customdata.callback(id, data);

    if (data.callback)
        eval(data.callback);

	var instance = dialogs.get(id).instance;
    if (data["close"]) {
        instance.dialog("close");
	} else if (data["url"]) {
		dialogs.get(id).params.current_url = data["url"];
		that.updateCursor(id, data["url"]);
	} else if (data["cursor_reload"] && data["cursor_reload"] === id) {
		ff.ffRecord.cursor.reload(id);
    } else {
        if (data["html"]) {
			if (!ff.ajax.ctxGet(id).needInit() && dialogs.get(id) !== undefined && !instance.dialog("isOpen"))
                instance.dialog("open");
        }

		if (!ff.ajax.ctxGet(id).needInit())
			ff.ffPage.dialog.refresh(id);
		else {
			dialogs.get(id).waiting = true;
			ff.ajax.blockUI();
		}

		//instance.dialog("widget").hide();
        //instance.dialog("widget").fadeIn();
    }
    return true;
},

"onClose" : function (id, hide) {
/*    if (that.dialog_deps[id] !== undefined)
        delete that.dialog_deps[id];*/

    if (dialogs.get(id).params.params && dialogs.get(id).params.params.persistent)
        return;

    if (dialogs.get(id).params.callback) {
        eval(dialogs.get(id).params.callback);
    }

    ff.struct.get("comps").each(function (componentid, component) {
        if (component.ctx === id) {
            ff.clearComponent(componentid);
        }
    });

    ff.struct.get("fields").each(function (key, field) {
        if (field.ctx !== undefined && field.ctx === id) {
            ff.doEvent({
                "event_name"    : "onClearField",
                "event_params"    : [undefined, key, field]
            });
            ff.struct.get("fields").unset(key);
        }
    });

    dialogs.get(id).instance.remove();
    dialogs.unset(id);
	ff.ajax.ctxGet(id).reset();

    that.doEvent({
        "event_name": "onClose"
        , "event_params" : [id]
    });
},

"doAction" : function (id, action, component, detailaction, action_param, addit_fields) {
    that.param(id, "lastaction", action);
    
    switch (action) {
        case "close":
            dialogs.get(id).instance.dialog("close");
            break;

        default:
			ff.ajax.ctxGet(id).reset();
            that.doEvent({"event_name": "doAction", "event_params" : [id, action, component, detailaction, action_param]});

            var fields = ff.getFields(dialogs.get(id).instance.closest(".ui-dialog"), id);
            fields.push(
                {name: "frmAction", value: component + action}
            );
            if (detailaction) {
                fields.push(
                    {name: component + "detailaction", value: detailaction}
                );
            }

            if (action == "detail_delete") {
                fields.push(
                    {name: detailaction + "_delete_row", value: action_param}
                );
            }

            if(addit_fields) {
                for(var i in addit_fields) {
                    fields.push(addit_fields[i]);
                }
            }
			
            ff.ajax.doRequest({
                 "url"                : that.parseUrl(id, dialogs.get(id).params.current_url),
                 "type"                : "POST",
                 "fields"            : fields,
                 "callback"            : that.onSuccess,
                 "customdata"        : {
                    "id" : id
					, "caller" : {
						"func" : ff.ffPage.dialog.doAction
						, "args" : ff.argsAsArray(arguments)
					}
                 },
                 "injectid"            : dialogs.get(id).instance,
                 "ctx"				: id,
                 "doredirects"        : dialogs.get(id).params.doredirects
            });
            break;
    }
},

"goToUrl" : function (id, url) {
	ff.ajax.ctxGet(id).reset();
	
    dialogs.get(id).params.current_url = url;
	that.updateCursor(id, url);

    ff.ajax.doRequest({
         "url"                : that.parseUrl(id, dialogs.get(id).params.current_url),
         "type"                : "GET",
         "callback"            : that.onSuccess,
         "customdata"        : {
            "id" : id
			, "caller" : {
				"func" : ff.ffPage.dialog.goToUrl
				, "args" : ff.argsAsArray(arguments)
			}
         },
         "injectid"            : dialogs.get(id).instance,
         "ctx"				: id,
		 "brandnew"			: true,
         "doredirects"        : dialogs.get(id).params.doredirects
    });
},

"doRequest" : function (id, params) {
    /**
     * params
     *    url
     *    component
     *    section
     *    injectid
     *    action
     *    detailaction
     *    callback
     *    action_param
     *    fields            i campi da passare con la richiesta, se no vengono presi quelli di tutta la pagina
     */
    
	ff.ajax.ctxGet(id).reset();
	
    var fields = (params.fields === undefined ? jQuery(":input", dialogs.get(id).instance.closest(".ui-dialog")).not("input:checkbox:not(:checked)").not("input:radio:not(:checked)") : params.fields);

    if (params.action) {
        fields.push(
            {"name": "frmAction", "value": params.action}
        );
    }
        
    if (params.detailaction) {
        fields.push(
            {"name": params.detailaction + "detailaction", "value": params.component}
        );
    }

    if (params.action_param !== undefined) {
        fields.push(
            {"name": params.component + "_delete_row", "value": params.action_param}
        );
    }

    var url = (params.url !== undefined ? params.url : null);

    if (!url && params.component && ff.struct.get("comps").get(params.component) !== undefined)
        url = ff.struct.get("comps").get(params.component).url;

    if (!url)
        url = dialogs.get(id).params.current_url;
        
    ff.ajax.doRequest({
         "url"                : that.parseUrl(id, url),
         "component"        : params.component,
         "section"            : params.section,
         "fields"            : fields,
         "callback"            : that.onSuccess,
         "customdata"        : {
            "id"            : id
            , "callback"    : params.callback
			, "caller" : {
				"func" : ff.ffPage.dialog.doRequest
				, "args" : ff.argsAsArray(arguments)
			}
         },
         "injectid"            : params.injectid,
         "ctx"				: id,
         "chainupdate"        : params.chainupdate, 
         "doredirects"        : dialogs.get(id).params.doredirects
    });
},

"parseUrl" : function (id, url) {
    var parsedurl = url;

    /*if (parsedurl.indexOf('?') > -1) {
        if (parsedurl.substring(parsedurl.length - 1) != "&")
            parsedurl += "&";
        parsedurl += "XHR_THEME=dialog";
    } else
        parsedurl += "?XHR_THEME=dialog";*/

    var regTags = /\[\[([a-zA-Z0-9_\-\[\](?!\]))]+)\]\]/g;
    var ret;
    while ((ret = regTags.exec(url)) !== null) {
        var tmp = ret[1].replace(/\[/g, "\\[").replace(/\]/g, "\\]");  
        var encodeTmp = false;

        if(tmp.indexOf("_ENCODE") > 0) {
            tmp = tmp.replace("_ENCODE", "");
            encodeTmp = true;
        }

        if(tmp.indexOf("_TEXT") > 0) {
            if(jQuery("#" + tmp.replace("_TEXT", "")).is("select")) {
                parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp.replace("_TEXT", "") + " option:selected").text()) : jQuery("#" + tmp.replace("_TEXT", "") + " option:selected").text()), "g");                            
            } else {
                parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp.replace("_TEXT", "")).text()) : jQuery("#" + tmp.replace("_TEXT", "")).text()), "g");                
            }
        } else {
            parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp).val()) : jQuery("#" + tmp).val()), "g");    
        }
    }

    if (dialogs.get(id).params.params !== undefined)
    {

/*        for (i in dialogs.get(id).params.params) {
            parsedurl += "&" + i + "=" + jQuery("#" + dialogs.get(id).params.params[i]).val();
        }*/
    }
    return parsedurl;
},

"removeDlgBt" : function(id, data) {
	var instance = dialogs.get(id).instance;

	/*
	if(data["html"].indexOf("dialogSubTitleTab")) {
		jQuery(".dlgTab", instance.dialog("widget")).remove();
	}
	jQuery(".dlgSubTitle", instance.dialog("widget")).remove();
	*/
	if(data["html"].indexOf("dialogActionsPanel top")) {
		jQuery(".dlgTopPanel", instance.dialog("widget")).remove();
	}

	if(data["html"].indexOf("dialogActionsPanel")) {
		jQuery(".dlgBottomPanel", instance.dialog("widget")).remove();
	}
},
"makeDlgBt" : function(id, data) {
	var instance = dialogs.get(id).instance;

	var countRecord = 0;
	var countGrid = 0;
	var countDetail = 0;
	if(data["html"]) {
		ff.struct.get("comps").each(function(key, value) {
			switch(value["type"]) {
				case "ffRecord":
					if (value["ctx"] === id)
						countRecord++;
					return;
				case "ffGrid":
					if (value["ctx"] === id)
						countGrid++;
					break;
				case "ffDetails":
					if (value["ctx"] === id)
						countDetail++;
				default:
			}
		});	
	}
	
	if(countDetail && !countRecord) // ??
		return;

	if(!countRecord && countGrid === 1) {
		jQuery(".ffGrid .heading", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel");
		jQuery(".ffGrid .FormsGridCommands.top", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel");
		jQuery(".ffGrid .FormsGridCommands.bottom", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel");
	}

	if(countRecord === 1) {
		jQuery(".dialogActionsPanel:last", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
	}
	
	/*
	if(jQuery(".dialogTitle", instance).length) {
		jQuery(".ui-dialog-title").addClass(jQuery(".dialogTitle", instance).attr("class")).removeClass("dialogTitle");
		jQuery(".dialogTitle:first", instance).appendTo(jQuery(".ui-dialog-title", instance.dialog("widget")).empty()).attr("class", "dlgTitle");
	}
	
	if(jQuery(".dialogSubTitleTab", instance).length) {
		jQuery(".dialogSubTitleTab", instance).replaceWith(function(i) { 
			jQuery(this).parent().addClass("dlg-tab dlg-tab" + i); 
			return  '<a class="' + jQuery(this).attr("class") + '" href="javascript:void(0);" rel="dlg-tab' + i + '">' + jQuery(this).html() + '</a>'
		});
		jQuery(".dialogSubTitleTab", instance).appendTo(jQuery(".ui-dialog-title", instance.dialog("widget"))).removeClass("dialogSubTitleTab").wrapAll('<div class="dlgTab" />');
		jQuery(".dlgTab a", instance.dialog("widget")).click(function() {
			jQuery(".dlg-tab", instance).hide();
			jQuery("." + jQuery(this).attr("rel"), instance).show();
			
			ff.ffPage.dialog.refresh(id);

			jQuery(".dlgTab a", instance.dialog("widget")).removeClass("selected");
			jQuery(this).addClass("selected");
			
		});
		jQuery(".dlgTab a:first", instance.dialog("widget")).click();
	}
	
	if(jQuery(".dialogSubTitle", instance).length) {
		jQuery(".dialogSubTitle", instance).insertAfter(jQuery(".ui-dialog-titlebar", instance.dialog("widget"))).addClass("dlgSubTitle").removeClass("dialogSubTitle");
	}
	
	if(jQuery(".dialogActionsPanel.top", instance).length) {
		jQuery(".dialogActionsPanel.top.force", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel").removeClass(".dialogActionsPanel top force");
		if(!countRecord && countGrid == 1)
			jQuery(".dialogActionsPanel.top:not(.force)", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel").removeClass("dialogActionsPanel top");
	}

	if(jQuery(".dialogActionsPanel:not(.top)", instance).length) {
		jQuery(".dialogActionsPanel.force", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel force");

		if(countRecord) {
				jQuery(".dialogActionsPanel:not(.force):last", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
			} else if(countGrid == 1) {
				jQuery(".dialogActionsPanel:not(.force)", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
		}
	}
	/*
	if(dialogs.get(id).breadCrumbs.length) {
		var breadCrumbs = "";
		for(var i=0; i<dialogs.get(id).breadCrumbs.length; i++) {
			var brdUrl = 'ff.ffPage.dialog.doOpen(\'' + id + '\', \'' + dialogs.get(id).breadCrumbs[i]["url"] + '\',\'' + dialogs.get(id).breadCrumbs[i]["title"] + '\', true);';
			breadCrumbs = breadCrumbs + '<li><a href="javascript:void(0);" onClick="' + brdUrl + '">' + dialogs.get(id).breadCrumbs[i]["title"] + '</a></li>';
		}
		if(breadCrumbs) {
			jQuery('<span class="dlgBrdCrumbs"><a href="javascript:void(0);" onClick="' + brdUrl + '" class="dlgBrdBack"></a><ul>' + breadCrumbs + '</ul></span>').prependTo(jQuery(".ui-dialog-title", instance.dialog("widget")));
		}
	}*/
},

"updateCursor" : function (id, url) {
	var tmp = null;
	if (tmp = ff.getURLParameter("cursor[id]", url)) {
		that.dialog_params.get(id)["cursor"] = {
			"id" : tmp,
			"rrow" : ff.getURLParameter("cursor[rrow]", url),
			"rows" : ff.getURLParameter("cursor[rows]", url),
		};
	}
}

}; /* publics' end */

return that;

/* code's end. */
})();
