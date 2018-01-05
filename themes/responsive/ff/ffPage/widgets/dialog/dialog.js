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

jQuery.ui.dialog.prototype._position = jQuery.noop;

ff.ffPage.dialog = (function () {

// inits
//overflow manage
var old_overflow = {
	"dialog" : null
	, "style" : null
};

var unique = null;
//overflow manage
/*
jQuery('html, body').on('touchstart touchmove', function(e){ 
	 //prevent native touch activity like scrolling
	 if (old_overflow !== null) {
		e.preventDefault();
	 }
});*/

/*ff.pluginAddInit("ff.ffPage.tabs", function () {
	ff.ffPage.tabs.addEvent({
		"event_name"	: "onActivate",
		"func_name"		: function (id, event, ui) {
			var dialog = ui.newTab.closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				var id = dialog.replace("ffWidget_dialog_container_", "");
				ff.ffPage.dialog.refresh(id);
			}
		}
	});
});*/

/* causa un brutto brutto bug su firefox quando ci sono tanti combo, da rivedere
ff.pluginAddInit("ff.ffField.activecomboex", function () {
	ff.ffField.activecomboex.addEvent({
		"event_name"	: "refill",
		"func_name"		: function (control, node) {
			var dialog = jQuery(node).closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				ff.ffPage.dialog.adjSize(dialog.replace("ffWidget_dialog_container_", ""));
				var id = dialog.replace("ffWidget_dialog_container_", "");
				var instance = dialogs.get(id).instance;
				instance.dialog("widget").center(false);
			}
		}
	});
});*/

/*ff.pluginAddInit("ff.ffField.ckeditor", function () {
	ff.ffField.ckeditor.addEvent({
		"event_name"	: "onCreate",
		"func_name"		: function (node) {
			var dialog = jQuery(node).closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				ff.ffPage.dialog.adjSize(dialog.replace("ffWidget_dialog_container_", ""));
			}
		}
	});
});

ff.pluginAddInit("ff.ffField.tinymce", function () {
	ff.ffField.tinymce.addEvent({
		"event_name"	: "onCreate",
		"func_name"		: function (node) {
			var dialog = jQuery(node).closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				ff.ffPage.dialog.adjSize(dialog.replace("ffWidget_dialog_container_", ""));
			}
		}
	});
});

ff.pluginAddInit("ff.ffField.codemirror", function () {
	ff.ffField.codemirror.addEvent({
		"event_name"	: "onCreate",
		"func_name"		: function (node, editor) {
			var dialog = jQuery(node).closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				ff.ffPage.dialog.adjSize(dialog.replace("ffWidget_dialog_container_", ""));
			}
		}
	});
});

ff.pluginAddInit("ff.ffField.editarea", function () {
	ff.ffField.editarea.addEvent({
		"event_name"	: "onCreate",
		"func_name"		: function (node) {
			var dialog = jQuery(node).closest(".ui-dialog-content").attr("id");
			if (dialog !== undefined) {
				ff.ffPage.dialog.adjSize(dialog.replace("ffWidget_dialog_container_", ""));
			}
		}
	});
});*/

/* privates */
var dialogs		= ff.hash();
var inits_by_dlg = ff.hash();
var inits_by_wdg = ff.hash();

function initsReset(id) {
	inits_by_dlg.set(id, ff.hash());
	ff.ffPage.dialog.needInit(id, false);
}

var that = { /* publics */
__ff : true, /* used to recognize ff'objects */

"dialog_params"        : ff.hash(),
/*"dialog_deps"        : ff.hash(),*/

"addDialog" : function (params) {
	if(unique === null && params.unique) {
		unique = params.id;
	}
unique = null;
    that.dialog_params.set(params.id, {
        "callback"		: params.callback,
        "url"			: params.url,
        "title"			: params.title,
        "params"        : params.params,
        "height"        : params.height,
        "width"			: params.width,
        "resizable"		: params.resizable,
        "position"		: params.position,
        "draggable"		: params.draggable,
        "doredirects"	: params.doredirects,
        "class"	        : params.dialogClass,
		"need_init"		: false
    });

    that.doEvent({
        "event_name"    : "onAddDialog",
        "event_params"    : [params.id]
    });
},

"needInit" : function (id, value) {
	return that.param(id, "need_init", value);
},

"get" : function (id) {
    return dialogs.get(id);
},

"param" : function (id, param, value) {
    if (value !== undefined)
        that.dialog_params.get(id)[param] = value;
    else
        return that.dialog_params.get(id)[param];
},
"getDimensions" : function(elem) {
	var width = elem.outerWidth();
	var scrollHeight = elem.get(0).scrollHeight;
	
	//console.log(width);

	for(var i=300; i < width; i+=50) {
			elem.width(i);
	//console.log("width " +  elem.innerWidth() + " -/- " +( elem.get(0).scrollWidth  )+ ": " + elem.get(0).scrollHeight + " - " + scrollHeight + " = "  + (elem.get(0).scrollHeight - scrollHeight));

		if(elem.get(0).scrollHeight <= scrollHeight && elem.get(0).scrollWidth <= elem.innerWidth()) {
			width = elem.get(0).scrollWidth + 50;  
			break;
		}
	}


//console.log(width);
	return {
		width: width
		, height: elem.get(0).scrollHeight + 20
	}; 
},
"adjSize" : function (id) {
	if(id === undefined) {
		id = dialogs.keys[dialogs.keys.length - 1];
	}
	
	var instance = dialogs.get(id).instance;
	var widget = instance.dialog("widget");
	
	var wh = jQuery(window).height();
	var ww = jQuery(window).width();
	

	
	if(instance.dialog("option", "width") == "auto") {
		instance.dialog("option", "width", undefined);
		
		//if(!jQuery("#ffWidget_dialog_container_" + id + " TEXTAREA").length)
			instance.dialog("option", "width", that.getDimensions(widget).width);
	}

	//that.getDimensions(jQuery(".ui-widget-content", widget));
	if (widget.outerHeight(false) > wh * 0.90) {
		instance.dialog("option", "height", wh * 0.90);
	}
	if (widget.outerWidth(false) > ww * 0.90) {
		instance.dialog("option", "width", ww * 0.90);
	}
},

"refresh" : function (id, show) {
	if(id === undefined) {
		id = dialogs.keys[dialogs.keys.length - 1];
	}
	
	var instance = dialogs.get(id).instance;
		
	//widget.position({my : "center center", at : "center center", of : window});
	
	//if(parseInt(jQuery(widget).css("top")) < 0) {
	//	jQuery(widget).css("top", Math.round((jQuery(window).height()) - jQuery(widget).height()) / 2 );
	//}
	//jQuery(widget).css("top", 0);
	//jQuery(widget).css("left", 0);
	
	
	if (!instance.dialog("isOpen")) {
		instance.dialog("open");
	}

	
	that.adjSize(id);
	
	var widget = instance.dialog("widget");
	
//	widget.position();	
	widget.position({my : "center center", at : "center center", of : window});

	
	if(show === true)
		jQuery(widget).parent().fadeTo("fast", 1, function() {
		    that.doEvent({
		        "event_name"    : "onDisplayedDialog",
		        "event_params"    : [id]
		    });		
		});
	else if(show === false)
		jQuery(widget).parent().css("opacity", 0);



	return true;
},

"makeInstance" : function (id) {
	//overflow manage
	if (old_overflow.dialog === null) {
		jQuery("body").addClass("ff-modal-open");
		//old_overflow.style = jQuery("body").attr("style");
		//jQuery("body").css("overflow", "hidden");
		old_overflow.dialog = id;
	}

	dialogs.get(id).instance = jQuery('<div id="ffWidget_dialog_container_' + id + '"></div>').dialog({
		autoOpen: false
		, dialogClass : "ff-modal-dialog" + (that.dialog_params.get(id).class ? " " + that.dialog_params.get(id).class : "")
		, resizable: that.dialog_params.get(id).resizable
		, position: that.dialog_params.get(id).position
		, modal: false
		, closeText : ''
		, draggable: that.dialog_params.get(id).draggable
		, create: function(event, ui ) {
			var widget = jQuery(this).dialog("widget");
			var zIndex = 90;
			$("BODY *").each(function() {
				if($(this).css("position") == "fixed") {
				    var current = parseInt($(this).css("z-index"), 10);
				    if(current < 2000 && zIndex < current) zIndex = current;
				}
			});			
    		zIndex++;

			jQuery(widget).wrapAll('<div class="ff-modal"/>').parent().css({"opacity": 0, "z-index": zIndex});		
			
		}
		, close: function(ev, ui) {
			var widget = jQuery(this).dialog("widget");
			jQuery(widget.parent()).remove();	
			
			that.onClose(id);
			//overflow manage
			if (id === old_overflow.dialog) {
				jQuery("body").removeClass("ff-modal-open");
				//jQuery("body").css("overflow", "");
				//old_overflow.style = null;
				old_overflow.dialog = null;
			}
		}
		, title: that.dialog_params.get(id).title
		, height: that.dialog_params.get(id).height || 'auto'
		, width: that.dialog_params.get(id).width || 'auto'
		/*, maxHeight: jQuery(window).height() * 0.90*/
		/*, maxWidth: jQuery(window).width() * 0.90*/
		, minWidth: 500  
		, hide: {effect: 'fade', duration: 200}
		, open: function() {
			var widget = jQuery(this).dialog("widget");
			
			//widget.appendTo(jQuery(widget).prev());

			//non fa piu ridimensionare le dialog
			/*if (widget.get(0).style.height == "auto")
				widget.height(widget.height());

			if (widget.get(0).style.width == "auto")
				widget.width(widget.width());

			if (jQuery(".ui-dialog-content", widget).get(0).style.height == "auto")
				jQuery(".ui-dialog-content", widget).height(jQuery(".ui-dialog-content", widget).height());*/
        
        	//jQuery(".ui-dialog-content", widget).css("width", "");
		}
		, resizeStart: function(event, ui) {
			var widget = jQuery(this).dialog("widget");
			
			widget.css("position", "relative");
		}
	});
	return dialogs.get(id).instance;
},

"doOpen" : function (id, url, title, preserveIstance, elemHighlight) {
//unique = null;
	if(unique && dialogs.keys.length) {
		preserveIstance = true;
		if(!url)
			url = that.dialog_params.get(id)["url"];
		if(!title)
			title = that.dialog_params.get(id)["title"];

		id = unique;
	}
    if (url !== undefined && that.dialog_params.get(id) !== undefined )
        that.dialog_params.get(id)["url"] = url;

    if (title !== undefined && title.length > 0 && that.dialog_params.get(id) !== undefined)
        that.dialog_params.get(id)["title"] = title;

    if (dialogs.get(id) && dialogs.get(id).instance) {
        if(dialogs.get(id).params.current_url != url && preserveIstance) {
			var breadCrumbs = dialogs.get(id).breadCrumbs;
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
				
            ff.ffPage.dialog.goToUrl(id, url);
        } else {
            dialogs.get(id).instance.dialog("open");
        }
        return;
    }
	
	initsReset(id);
	
    dialogs.set(id, {
		"instance"	: null,
		"params"	: jQuery.extend(true, {}, that.dialog_params.get(id)), 
        "elemHighlight" : elemHighlight,
		"breadCrumbs": []
		
	});

    dialogs.get(id).params.current_url = dialogs.get(id).params.url;
    
    var evres = that.doEvent({"event_name": "doOpen", "event_params" : [that, id, url, title]});
    if (evres !== true) {
        var fields = [
            {name: "XHR_DIALOG_ID", value: id}
        ];
             
        ff.ajax.doRequest({
            "url"			: that.parseUrl(id, dialogs.get(id).params.current_url),
            "type"			: "GET",
            "fields"		: fields,
            "callback"		: that.onSuccess,
            "customdata"	: {
                "id"                    : id
                , "elemHighlight"       : elemHighlight
				, "caller"              : {
					"func"              : ff.ffPage.dialog.doOpen
					, "args"            : ff.argsAsArray(arguments)
				}
            },
            "injectid"		: dialogs.get(id).instance,
            "dialog"		: id,
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
    
    if(dialogs.get(id).elemHighlight)
        jQuery(dialogs.get(id).elemHighlight).addClass("ff-modal-highlight");

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
    } else {
		that.makeDlgBt(id, data);
		
        if (data["html"]) {
			if (!that.needInit(id) && dialogs.get(id) !== undefined && !instance.dialog("isOpen"))
                instance.dialog("open");
        } else if (data["url"]) {
            dialogs.get(id).params.current_url = data["url"];
        }

		if (!that.needInit(id))
			ff.ffPage.dialog.refresh(id, true);
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
    if(dialogs.get(id).elemHighlight)
        jQuery(dialogs.get(id).elemHighlight).removeClass("ff-modal-highlight");
    
    if (dialogs.get(id).params.params && dialogs.get(id).params.params.persistent)
        return;

    if (dialogs.get(id).params.callback) {
        eval(dialogs.get(id).params.callback);
    }

    ff.struct.each(function (componentid, component) {
        if (component.dialog === id) {
            ff.clearComponent(componentid);
        }
    });

    ff.struct.fields.each(function (key, field) {
        if (field.dialog !== undefined && field.dialog === id) {
            ff.doEvent({
                "event_name"    : "onClearField",
                "event_params"    : [undefined, key, field]
            });
            ff.struct.fields.unset(key);
        }
    });

    dialogs.get(id).instance.remove();
    dialogs.unset(id);
	inits_by_dlg.unset(id);

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
			initsReset(id);
            that.doEvent({"event_name": "doAction", "event_params" : [id, action, component, detailaction, action_param]});

            var fields = ff.getFields(dialogs.get(id).instance, id);
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

            fields.push(
                {name: "XHR_DIALOG_ID", value: id}
            );

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
                 "dialog"            : id,
                 "doredirects"        : dialogs.get(id).params.doredirects
            });
            break;
    }
},

"goToUrl" : function (id, url) {
	initsReset(id);
	
    dialogs.get(id).params.current_url = url;

    var fields = [
        {name: "XHR_DIALOG_ID", value: id}
    ];
    ff.ajax.doRequest({
         "url"                : that.parseUrl(id, dialogs.get(id).params.current_url),
         "type"                : "GET",
         "fields"            : fields,
         "callback"            : that.onSuccess,
         "customdata"        : {
            "id" : id
			, "caller" : {
				"func" : ff.ffPage.dialog.goToUrl
				, "args" : ff.argsAsArray(arguments)
			}
         },
         "injectid"            : dialogs.get(id).instance,
         "dialog"            : id,
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
    
	initsReset(id);
	
    var fields = (params.fields === undefined ? jQuery(":input", dialogs.get(id).instance).not("input:checkbox:not(:checked)").not("input:radio:not(:checked)") : params.fields);

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

    fields.push(
        {"name": "XHR_DIALOG_ID", "value": id}
    );

    var url = (params.url !== undefined ? params.url : null);

    if (!url && params.component && ff.struct.get(params.component) !== undefined)
        url = ff.struct.get(params.component).url;

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
         "dialog"            : id,
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

"initInspect" : function (id, component) {
	var widgets = null;
	var dlg_inits = inits_by_dlg.get(id);
	
	/*if (component === undefined) {
		if (ff.struct.page !== undefined && ff.struct.page.widget !== unidefined && ff.struct.page.widget !== "") {
			widgets = ff.struct.page.widget.split(",");
			widgets.each(function (k, widget) {
				dlg_inits.page.set(widget, false);
				if (glb_inits.page.get(widget) === undefined)
					glb_inits.page.set(widget, ff.hash());
				glb_inits.page.get(widget).set(id);
				dlg_inits.need_init = true;
			});
		}
	
		if (ff.struct.fields.length) ff.struct.fields.each( function (fld_id, field) {
			if (field.widget !== undefined && field.widget !== "") {
				if (dlg_inits.field.get(fld_id) === undefined)
					dlg_inits.field.set(fld_id, ff.hash());

				widgets = field.widget.split(",");
				widgets.each(function (k, widget) {
					dlg_inits.field.get(fld_id).set(widget, false);
					glb_inits.field.set(fld_id, id);
					dlg_inits.need_init = true;
				});
			}
		});
	}*/
		
	ff.struct.each(function (com_id, com, i) {
		if (component !== undefined && com_id !== component)
			return;
		
		if (com.dialog === id) {			
			if (com.widgets !== undefined) com.widgets.each(function (k, widget) {
				if (dlg_inits.get(widget.id) === undefined)
					dlg_inits.set(widget.id, ff.hash());					
				dlg_inits.get(widget.id).set(widget.type, false);

				if (inits_by_wdg.get(widget.type) === undefined)
					inits_by_wdg.set(widget.type, ff.hash());
				inits_by_wdg.get(widget.type).set(widget.id, id);

				that.needInit(id, true);
			});
			
			if (com.fields.length) com.fields.each( function (fld_id, field) {
				if (field.widgets !== undefined) field.widgets.each(function (k, widget) {
					if (dlg_inits.get(widget.id) === undefined)
						dlg_inits.set(widget.id, ff.hash());					
					dlg_inits.get(widget.id).set(widget.type, false);

					if (inits_by_wdg.get(widget.type) === undefined)
						inits_by_wdg.set(widget.type, ff.hash());
					inits_by_wdg.get(widget.type).set(widget.id, id);

					that.needInit(id, true);
				});
			});
		}
	});
	
	return dlg_inits.need_init;
},
"removeDlgBt" : function(id, data) {
	var instance = dialogs.get(id).instance;

	if(data["html"].indexOf("dialogSubTitleTab")) {
		jQuery(".dlgTab", instance.dialog("widget")).remove();
	}
	jQuery(".dlgSubTitle", instance.dialog("widget")).remove();
	
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
		struct.each(function(key, value) {
			switch(value["type"]) {
				case "ffRecord":
					if(data["html"].indexOf('id="' + key + '"') >= 0)
						countRecord++;
					return;
				case "ffGrid":
					if(data["html"].indexOf('id="' + key + '"') >= 0)
						countGrid++;
					break;
				case "ffDetails":
					if(data["html"].indexOf('id="' + key + '"') >= 0)
						countDetail++;
				default:
			}
		});	
	}
	if(countDetail && !countRecord)
		return;

	if(jQuery(".dialogTitle", instance).length) {
		jQuery(".ui-dialog-title").addClass(jQuery(".dialogTitle:first", instance).attr("class")).removeClass("dialogTitle");
		jQuery(".dialogTitle:first", instance).appendTo(jQuery(".ui-dialog-title", instance.dialog("widget")).empty()).attr("class", "dlgTitle");
	}
	
	if(jQuery(".dialogSubTitleTab", instance).length) {
		var startSel = 0;
		jQuery(".dialogSubTitleTab", instance).replaceWith(function(i) { 
			jQuery(this).removeClass("dialogSubTitleTab");

			if(jQuery(this).hasClass("selected")) {
				startSel = i;
			
				jQuery(this).removeClass("selected");
			}
			var depClass = jQuery(this).attr("class").replace("dep-", "dlg-");
			
			//jQuery(this).parent().addClass("dlg-tab " + depClass); 

			return '<a class="dialogSubTitleTab" href="javascript:void(0);" rel="' + depClass + '">' + jQuery(this).html() + '</a>';
		});
		jQuery(".dialogSubTitleTab", instance).appendTo(jQuery(".ui-dialog-title", instance.dialog("widget"))).removeClass("dialogSubTitleTab").wrapAll('<div class="dlgTab" />');
		jQuery(".dlgTab a", instance.dialog("widget")).click(function() {
			/*var rel = '';
			var arrRel = jQuery(this).attr("rel").split(' ');

			arrRel.each(function(key, value) {
				if(value.indexOf("dlg-") === 0) {
					rel = value;
					return true;
				}
			});*/
			var rel = jQuery(this).attr("rel");
			if(rel) {
				jQuery(".dlg-tab", instance).hide();

				jQuery("." + rel, instance).show();
				
				ff.ffPage.dialog.refresh(id);

				jQuery(".dlgTab a", instance.dialog("widget")).removeClass("selected");
				jQuery(this).addClass("selected");
			}
		});

		jQuery(".dlgTab a:eq(" + startSel + ")", instance.dialog("widget")).click();
	}
	
	if(jQuery(".dialogSubTitle", instance).length) {
		jQuery(".dialogSubTitle", instance).insertAfter(jQuery(".ui-dialog-titlebar", instance.dialog("widget"))).addClass("dlgSubTitle").removeClass("dialogSubTitle");
	}

    var skipForceBt = jQuery(".dlgTab").length;
	if(jQuery(".dialogActionsPanel.top", instance).length) {
        if(!skipForceBt)
		    jQuery(".dialogActionsPanel.top.force", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel").removeClass("dialogActionsPanel top force");
		if(!countRecord && countGrid == 1)
			jQuery(".dialogActionsPanel.top:not(.force)", instance).insertBefore(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgTopPanel").removeClass("dialogActionsPanel top");
	}

	if(jQuery(".dialogActionsPanel:not(.top)", instance).length) {
        if(!skipForceBt)
		    jQuery(".dialogActionsPanel.force", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel force");

		if(countRecord) {
			jQuery(".dialogActionsPanel:not(.force):last", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
		} else if(countGrid == 1) {
			jQuery(".dialogActionsPanel:not(.force)", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
		} else if(!countRecord && !countGrid) {
			jQuery(".dialogActionsPanel", instance).insertAfter(jQuery(".ui-dialog-content", instance.dialog("widget"))).addClass("dlgBottomPanel").removeClass("dialogActionsPanel");
		}
	}
	
	if(dialogs.get(id).breadCrumbs.length) {
		var breadCrumbs = "";
		for(var i=0; i<dialogs.get(id).breadCrumbs.length; i++) {
			var brdUrl = 'ff.ffPage.dialog.doOpen(\'' + id + '\', \'' + dialogs.get(id).breadCrumbs[i]["url"] + '\',\'' + dialogs.get(id).breadCrumbs[i]["title"] + '\', true);';
			breadCrumbs = breadCrumbs + '<li><a href="javascript:void(0);" onClick="' + brdUrl + '">' + dialogs.get(id).breadCrumbs[i]["title"] + '</a></li>';
		}
		if(breadCrumbs) {
			if(!jQuery(".dlgBrdCrumbs", instance.dialog("widget")).length)
				jQuery('<span class="dlgBrdCrumbs"><a href="javascript:void(0);" onClick="' + brdUrl + '" class="dlgBrdBack"></a><ul>' + breadCrumbs + '</ul></span>').prependTo(jQuery(".ui-dialog-title", instance.dialog("widget")));
		}
	}
},
"initEvent" : function (widget_id, widget_type) {
	var id = undefined;
	
	if (inits_by_wdg.get(widget_type) !== undefined)
		id = inits_by_wdg.get(widget_type).get(widget_id);
	
	if (id === undefined) return;
	
	var dlg_inits = inits_by_dlg.get(id);
	
	dlg_inits.get(widget_id).set(widget_type, true);
	inits_by_wdg.get(widget_type).unset(widget_id);
	
	// check if init is done
	var tmp_still_waiting = false;
	dlg_inits.each(function (k, v) {
		v.each(function(wk, wv) {
			if (!wv)
				tmp_still_waiting = true;
		});
	});
	
	if (!tmp_still_waiting) {
		if (dialogs.get(id).waiting) {
			ff.ajax.unblockUI();
			ff.ffPage.dialog.refresh(id, true);
		} else {
			that.needInit(id, false);
		}
	}
		
}

}; /* publics' end */

ff.pluginAddInit("ff", function () {
	ff.addEvent({
		"event_name"  : "initIFElement"
		, "func_name" : that.initEvent
	});
});

return that;

/* code's end. */
})();
