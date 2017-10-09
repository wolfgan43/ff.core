/**
 * Forms Framework Javascript Handling Object
 *    ajax dinamic content plugin namespace
 */

ff.ffPage.slices = (function () {

/* privates */
var slices			= ff.hash();
var slices_params	= ff.hash();

/* inits */
ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name" : "onUpdateContent"
		, "func_name" : function (params, data, injectid) {
			if (params.ctx && ff.ffPage.slices.exist(params.ctx) && params.component === undefined) {
				//if (!ff.ffPage.slices.get(params.ctx).instance) 
				//	ff.ffPage.slices.makeInstance(params.ctx);
			}
		}
	});
	ff.ajax.addEvent({
		"event_name" : "onRedirect"
		, "func_name" : function (url, data, mydata, params) {
			if (
				(params.ctx && ff.ffPage.slices.get(params.ctx) && ff.ffPage.slices.getInstance(params.ctx))
				&& (params.doredirects || data["doredirects"] !== undefined)
			) {
				ff.ffPage.slices.close(params.ctx);
			}
		}
	});
	ff.ajax.addEvent({
		"event_name" : "ctxInitDone"
		, "func_name" : function (id) {
			if (slices.get(id) && slices.get(id).waiting) {
				ff.ajax.unblockUI();
				//ff.ffPage.slices.refresh(id);
				return true;
			}
		}
	});
}, "498c3a04-083e-4298-858a-b4170f097df9");

var that = { /* publics */
__ff : true, /* used to recognize ff'objects */

"add" : function (params) {
	if (that.exist(params.id))
		return false;
	
	var iid = params.injectid || "#" + params.id;
	var inst = jQuery(iid);
	
	if (!inst.length)
		return false;
	
    slices_params.set(params.id, {
        "callback"		: params.callback,
        "url"			: params.url,
        "params"        : params.params || {},
        "doredirects"	: params.doredirects,
		"injectid"		: iid
    });
	
    slices.set(params.id, {
		"instance"	: inst,
		"params"	: undefined,
	});
	
	ff.ajax.ctxAdd(params.id, that, "slices");

    that.doEvent({
        "event_name"    : "onAdd",
        "event_params"    : [params.id]
    });
	
	return true;
},

"get" : function (id) {
    return slices.get(id);
},

"exist" : function (id) {
	return slices_params.isset(id);
},

"getInstance" : function (id) {
    return slices.get(id).instance;
},

"replaceHTML" : function (id, data) {
	that.getInstance(id).html(data["html"]);
},

"param" : function (id, param, value) {
    if (value !== undefined)
        slices_params.get(id)[param] = value;
    else
        return slices_params.get(id)[param];
},

"load" : function (id, url) {
	if (!that.exist(id))
		return false;
	
    if (url !== undefined && slices_params.get(id) !== undefined )
        slices_params.get(id)["url"] = url;

    if (slices.get(id) && slices.get(id).params) {
        if(slices.get(id).params.current_url != url) {
            ff.ffPage.slices.goToUrl(id, url);
        }
        return;
    }
	
	ff.ajax.ctxGet(id).reset();
	
    slices.get(id).params = jQuery.extend(true, {}, slices_params.get(id));
    slices.get(id).params.current_url = slices.get(id).params.url;
    
    var evres = that.doEvent({"event_name": "load", "event_params" : [that, id, url]});
    if (evres !== true) {
        ff.ajax.doRequest({
            "url"			: that.parseUrl(id, slices.get(id).params.current_url),
            "type"			: "GET",
            "callback"		: that.onSuccess,
            "customdata"	: {
                "id" : id
				, "caller" : {
					"func" : ff.ffPage.slices.load
					, "args" : ff.argsAsArray(arguments)
				}
            },
            "injectid"		: slices.get(id).instance,
            "ctx"			: id,
			"brandnew"		: true,
            "doredirects"	: slices.get(id).params.doredirects
        });
    }
},

"close" : function (id) {
	var params = slices_params.get(id);
    if (params.callback) {
        eval(params.callback);
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

	ff.ajax.ctxGet(id).reset();

	that.replaceHTML(id, {"html" : ""})
	
    that.doEvent({
        "event_name": "onClose"
        , "event_params" : [id]
    });
	
    slices.get(id).params = undefined;
},

"onSuccess" : function (data, customdata) {
    var id = customdata.id;

    if (data === null) {
        slices.get(id).instance && that.close(id);
        return false;
    }

    /**
     *    data.close
     *        true = chiude il tab
     *        false = valorizza (o aggiorna) il tab
     *
     *    data.refresh
     *        true = imposta il tab per l'aggiornamento del chiamante su chiusura
     *
     *    data.html
     *        contenuto del tab se aggiornato
     *
     *    data.url
     *        cambia l'url del tab su redirect interno
     */
    
    if (customdata.callback)
        customdata.callback(id, data);

    if (data.callback)
        eval(data.callback);

	var instance = slices.get(id).instance;
    if (data["close"]) {
        that.close(id);
	} else if (data["url"]) {
		slices.get(id).params.current_url = data["url"];
    } else {
        if (data["html"]) {
			//if (!ff.ajax.ctxGet(id).needInit() && slices.get(id) !== undefined && !instance.dialog("isOpen"))
                //instance.dialog("open");
        }

		if (!ff.ajax.ctxGet(id).needInit()) {
			//ff.ffPage.slices.refresh(id);
		} else {
			slices.get(id).waiting = true;
			ff.ajax.blockUI();
		}
    }
    return true;
},

"doAction" : function (id, action, component, detailaction, action_param, addit_fields) {
    that.param(id, "lastaction", action);
    
    switch (action) {
        case "close":
            that.close(id);
            break;

        default:
			ff.ajax.ctxGet(id).reset();
            that.doEvent({"event_name": "doAction", "event_params" : [id, action, component, detailaction, action_param]});

            var fields = ff.getFields(slices.get(id).instance, id);
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
                 "url"                : that.parseUrl(id, slices.get(id).params.current_url),
                 "type"                : "POST",
                 "fields"            : fields,
                 "callback"            : that.onSuccess,
                 "customdata"        : {
                    "id" : id
					, "caller" : {
						"func" : ff.ffPage.slices.doAction
						, "args" : ff.argsAsArray(arguments)
					}
                 },
                 "injectid"			: slices.get(id).instance,
                 "ctx"				: id,
                 "doredirects"		: slices.get(id).params.doredirects
            });
            break;
    }
},

"goToUrl" : function (id, url) {
	ff.ajax.ctxGet(id).reset();
	
    slices.get(id).params.current_url = url;

    ff.ajax.doRequest({
         "url"                : that.parseUrl(id, slices.get(id).params.current_url),
         "type"                : "GET",
         "callback"            : that.onSuccess,
         "customdata"        : {
            "id" : id
			, "caller" : {
				"func" : ff.ffPage.slices.goToUrl
				, "args" : ff.argsAsArray(arguments)
			}
         },
         "injectid"			: slices.get(id).instance,
         "ctx"				: id,
		 "brandnew"			: true,
         "doredirects"		: slices.get(id).params.doredirects
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
	
    var fields = (params.fields === undefined ? jQuery(":input", slices.get(id).instance).not("input:checkbox:not(:checked)").not("input:radio:not(:checked)") : params.fields);

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
        url = slices.get(id).params.current_url;
        
    ff.ajax.doRequest({
         "url"				: that.parseUrl(id, url),
         "component"		: params.component,
         "section"			: params.section,
         "fields"			: fields,
         "callback"			: that.onSuccess,
         "customdata"		: {
            "id"				: id
            , "callback"		: params.callback
			, "caller"			: {
				"func"				: ff.ffPage.slices.doRequest
				, "args"			: ff.argsAsArray(arguments)
			}
         },
         "injectid"			: params.injectid,
         "ctx"				: id,
         "chainupdate"		: params.chainupdate, 
         "doredirects"		: slices.get(id).params.doredirects
    });
},

"parseUrl" : function (id, url) {
    var parsedurl = url;

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

    if (slices.get(id).params.params !== undefined)
    {
/*        for (i in slices.get(id).params.params) {
            parsedurl += "&" + i + "=" + jQuery("#" + slices.get(id).params.params[i]).val();
        }*/
    }
    return parsedurl;
},

}; /* publics' end */

return that;

/* code's end. */
})();
