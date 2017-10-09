/**
 * Forms Framework Javascript Handling Object
 *	Ajax' namespace
 */

ff.ajax = (function () {

// privates
var blocked_ui		= 0;

var that = { // publics
__ff : true, // used to recognize ff'objects
defaults : {
	css			: {
			padding:	0,
			margin:		0,
			top:		'40%',
			left:		'45%',
			textAlign:	'center',
			cursor:		'wait'
		},
	overlayCSS	: {},
	message		: '<h1 class="block-loader"></h1>',
	display: false
},

"chainupdate"	: {},

"getblocked_ui" : function () {
	return blocked_ui;
},

"blockUI" : function (chainupdate) {
	blocked_ui++;

	if (blocked_ui > 1 || chainupdate)
		return;

	that.chainupdate.resources = ff.hash();
	that.chainupdate.updated = ff.hash();
	if(that.defaults.display) {
		if(jQuery.blockUI === undefined) {
			ff.pluginLoad("jquery.fn.block", "/themes/library/plugins/jquery.blockui/jquery.blockui.js", function() {
				if (that.defaults.css !== undefined) jQuery.blockUI.defaults.css = that.defaults.css;
				if (that.defaults.overlayCSS !== undefined) jQuery.blockUI.defaults.overlayCSS = that.defaults.overlayCSS;
				jQuery.blockUI(
					(that.defaults.message !== undefined ? {message : that.defaults.message} : {})
				);
			}, false);
		} else {
			if (that.defaults.css !== undefined) jQuery.blockUI.defaults.css = that.defaults.css;
			if (that.defaults.overlayCSS !== undefined) jQuery.blockUI.defaults.overlayCSS = that.defaults.overlayCSS;
			jQuery.blockUI(
				(that.defaults.message !== undefined ? {message : that.defaults.message} : {})
			);
		}
	} else {
		jQuery("body").addClass("pbar");
/*	
		jQuery("html").prepend('<span class="pr-bar" />')
		jQuery("html > .pr-bar").css({
			"position": "fixed"
			, "left": 0
			, "top" : 0
			, "z-index": 99999
			, "height": "2px"
			, "background-color": "#FF4715"
			, "width": "0%"
		}).animate({
			"width": "100%"
		}, 3000, function() {
			longAnimation = function() {
				jQuery(".pr-bar > .pr-long-bar").css("left", "0");
				jQuery(".pr-bar > .pr-long-bar").animate({
					"left" : "100%"
				}, 2000, longAnimation);
			};
			jQuery(this).append('<span class="pr-long-bar" />');
			jQuery(".pr-bar > .pr-long-bar").css({
				"position": "absolute"
				, "left": 0
				, "bottom": 0
				, "top" : 0
				, "height": "100%"
				, "background-color": "#FFAE98"
				, "width": "4%"
			});
			longAnimation();
		});
		
		

		jQuery("body").css({
			"pointer-events": "none"
		});
*/
	}
},

"unblockUI" : function (chainupdate, reset, data) {
	if (reset || blocked_ui === 1) if (chainupdate === undefined && that.chainupdate.resources.length) {
		var chainupdated = false;
		ff.struct.each(function (componentid, component) {if (that.chainupdate.updated.isset(componentid) === undefined) {
			var update = false;
			for (var i = 0; i < component.resources.length; i++) {
				if (that.chainupdate.resources.isset(component.resources[i]) !== undefined) {
					update = true;
				}
			}
			if (update) {
				that.chainupdate.updated.set(componentid, true);
				var res = that.doEvent({
					"event_name"	: "onUpdateComponent",
					"event_params"	: [componentid, component, data]
				});
				for (var i = 0; i < res.length; i++) {
					if (res[i]) {
						chainupdated = true; // ?? capire da dove arriva
						break;
					}
				}
			} else {
				component.fields.each(function (fieldid, field) {
					var tmp = componentid + "_" + fieldid;
					if (that.chainupdate.updated.isset(tmp) === undefined) {
						update = false;
						for (var i = 0; i < field.resources.length; i++) {
							if (that.chainupdate.resources.isset(field.resources[i]) !== undefined) {
								update = true;
							}
						}
						if (update) {
							that.chainupdate.updated.set(tmp, true);
							that.doEvent({
								"event_name"	: "onUpdateField",
								"event_params"	: [componentid, fieldid, field, data]
							});
						}
					}
				});
			}
		}});

		ff.struct.fields.each(function (fieldid, field) {if (that.chainupdate.updated.isset(fieldid) === undefined) {
			var update = false;
			for (var i = 0; i < field.resources.length; i++) {
				if (that.chainupdate.resources.isset(field.resources[i]) !== undefined) {
					update = true;
				}
			}
			if (update) {
				that.chainupdate.updated.set(fieldid, true);
				that.doEvent({
					"event_name"	: "onUpdateField",
					"event_params"	: [undefined, fieldid, field, data]
				});
			}
		}});
		that.chainupdate.resources.each(function (key, value, index) {
			that.doEvent({
				"event_name"	: "onUpdate",
				"event_params"	: [key, value, data]
			});
		});
		if (!chainupdated) {
			that.doEvent({
				"event_name" : "onEmptyQueue",
				"event_params"	: [data]
			});
			if(that.defaults.display)
				jQuery.unblockUI();
			else {
				/*
				jQuery("html > .pr-bar > .pr-long-bar").stop(true, true).remove();
				jQuery("html > .pr-bar").stop(true,true);
				jQuery("body").css({
					"pointer-events": ""
				});
				setTimeout('jQuery("html > .pr-bar").remove();', 700);
				*/
				setTimeout('jQuery("body").removeClass("pbar");', 700);
			}
		}
	} else {
		if(that.doEvent !== undefined)
			that.doEvent({
				"event_name" : "onEmptyQueue",
				"event_params"	: [data]
			});
			
		if(that.defaults.display) {
			if(jQuery.blockUI !== undefined)
			jQuery.unblockUI();
		} else {/*
			jQuery("html > .pr-bar > .pr-long-bar").stop(true, true).remove();
			jQuery("html > .pr-bar").stop(true,true);
			jQuery("body").css({
				"pointer-events": ""
			});
			setTimeout('jQuery("html > .pr-bar").remove();', 700);
			*/
			setTimeout('jQuery("body").removeClass("pbar");', 700);   
		}
			
	}
	
	if (reset)
		blocked_ui = 0;
	else
		blocked_ui--;

	if (blocked_ui > 0) {
		return;
	}
},

/**
 * params (object)
 *	url				l'indirizzo della richiesta. se omesso corrisponde all'url corrente
 *	type			il tipo di richiesta, se GET o POST. di default è POST
 *	component		il componente richiesto
 *	section			la sezione del componente richiesto (richiede che sia settato component)
 *	fields			i campi da passare con la richiesta, se no vengono presi quelli di tutta la pagina
 *	addFields		eventuali campi da aggiungere a quelli standard
 *	callback		la funzione da richiamare su completamento della richiesta
 *	customdata		dati aggiuntivi da passare con la richiesta alla callback
 *	injectid		l'id dell'elemento DOM da sostituire in caso di successo
 *	dialog			se la richiesta è avvenuta tramite un dialog
 *	action			sovrascrive o aggiunge frmAction
 *	replace			sovrascrive l'item
 *	doredirects		esegue i redirect invece di seguirli con chiamate ajax a cascata
 *  formName		nome alternativo del form default: frmMain
 *  stickycomp		normalmente false, impedisce l'aggiornamento dei componenti
 *  wholePage		normalmente false, richiede l'intera pagina invece del solo contenuto
 *  jsonp			normalmente false, permette di fare chiamate jsonp su richiesta
 *  async			normalmente true, permette di forzare una richiesta sincrona
 */
"doRequest" : function (params) {
	var async	= params.async !== undefined ? params.async : true;
	var form	= (params.formName ? jQuery("#" + params.formName) : (jQuery("#frmMain").length ? jQuery("#frmMain") : jQuery("#" + params.component)));

	var url		= (params.url ? params.url : (function () {
		if (params.component) {
			var tmp = ff.struct.get(params.component);
			if (tmp !== undefined && tmp.url)
				return tmp.url;
		}
		return document.location.href
	})());

	url = ff.fixPath(url);
	
	if (params.wholePage)
		url = ff.urlAddParam(url, "XHR_GET_FULL");
	
	var fields		= (params.fields === undefined ? ff.getFields(form) : params.fields);
	var type		= (params.type === undefined ? "POST" : params.type);

	if (params.addFields) {
		if (Object.prototype.toString.call(fields) == "[object Array]")
			fields = fields.concat(params.addFields);
		else
			fields = fields.add(params.addFields);
	}

	if (params.component) {
		fields.push({name: "XHR_COMPONENT", value: params.component});

		if (params.section && jQuery("#" + params.component + "_" + params.section).attr("id") !== undefined)
			fields.push({name: "XHR_SECTION", value: params.section});
	}

	if (params.action) {
		for (var i = 0; i < fields.length; i++) {
			if (fields[i].name == "frmAction") {
				fields.splice([i], 1);
				i--;
			}
		}
		fields.push({name: "frmAction", value: params.action});
	}

	ff.ajax.blockUI(params.chainupdate);
	
	var dataType = (params.jsonp !== undefined ? (params.jsonp ? "jsonp json" : "json") : (ff.origin != ff.httpGetOrigin() ? "jsonp json" : "json"));

	jQuery.ajax({
		  "url"			: url
		, "async"		: async
		, "data"		: fields
		, "type"		: type
		, "dataType"	: dataType
		, "jsonp"		: (dataType == "jsonp json" ? "XHR_JSONP" : undefined)
		, "success"		: ff.ajax.onSuccess
		, "error"		: ff.ajax.onError
		, "cache"		: false
		, "mydata"		: {
			"params"		: params,
			"parsed_url"	: url,
			"parsed_fields" : fields,
			"caller" : {
				"func" : ff.ajax.doRequest
				, "args" : ff.argsAsArray(arguments)
			}
		}
	});
},

"jsonpProxy" : function () {
}, 

"onError" : function (jqXHR, textStatus, errorThrown) {
	that.unblockUI(false, true);
	alert("Impossibile connettersi con il server, riprovare più tardi " + errorThrown) ;

	if (this.mydata && this.mydata.params.callback !== undefined) {
		this.mydata.params.callback(null, this.mydata.params.customdata);
	}
	return false;
},

"onSuccess" : function (data) {
	// shortcuts
	var params = this.mydata.params;
	var injectid	= (params.injectid
						? params.injectid 
						: "#" + (params.component 
									? (params.section && jQuery("#" + params.component + "_" + params.section).attr("id") !== undefined
										? params.component + "_" + params.section 
										: params.component
									) 
									: "content"
								)
					);
	if (data === null) { 
        //that.onError(null, null, "error");
		if (params.callback !== undefined)
			params.callback(null, params.customdata);
		return false;
	}
	
	var res = that.doEvent({
		"event_name"	: "onSuccess",
		"event_params"	: [data, params, injectid]
	});

	// aggiorno lo stato degli oggetti correlati
	if (params.chainupdate !== true) {
		if (data.resources && (data["insert_id"] !== undefined || data.refresh)) {
			ff.struct.each(function (key, value) {
				if (data["url"] !== undefined && value.dialog === params.dialog)
					that.chainupdate.updated.set(key, true);
				else if (params.component !== undefined && key === params.component)
					that.chainupdate.updated.set(key, true);

				if (data.resources.length) {
					for (var c = 0; c < data["resources"].length; c++) {
						that.chainupdate.resources.set(data["resources"][c], (data["insert_id"] !== undefined ? data["insert_id"] : true));
					}
				}
			});
		}
	}

	// elimina gli oggetti implicati nell'azione, in modo che possano essere ricreati
	if (!params.stickycomp) {
		if (data["html"] !== undefined || data["url"] !== undefined) {
			if (params.component !== undefined) { // un unico componente
				if (params.section) { // solo un pezzo
					//console.log("TODO: aggiornamento componente/sezione", params.component, params.section);
				} else { // l'intero componente
					ff.clearComponent(params.component);
				}
			} else { // più di un componente
				if (params.dialog) { // internamente al dialog
					ff.struct.each(function (key, value) {
						if (value.dialog === params.dialog)
							ff.clearComponent(key);
					});
				} else { // l'intero contenuto della pagina
					ff.struct.each(function (key) {
						ff.clearComponent(key);
					});
				}
			}
		}
	}

	if (data["html"] !== undefined) {
		// inserisco il nuovo contenuto
		if (data["headers"] !== undefined) {
			var tmpdiv = ff.getUniqueID();
			jQuery("body").append('<div id="fftmp' + tmpdiv + '">' + data["headers"] + '<script type="text/javascript">jQuery("#fftmp' + tmpdiv + '").remove();</script></div>');
		}

		if(params.dialog && ff.ffPage.dialog.get(params.dialog) !== undefined)
			ff.ffPage.dialog.initInspect(params.dialog, params.component);
			
		if (params.component === undefined) {
			if (params.dialog) {
				if (!ff.ffPage.dialog.get(params.dialog).instance) 
					ff.ffPage.dialog.makeInstance(params.dialog);

				var dlgInst = ff.ffPage.dialog.get(params.dialog).instance;
				
				ff.ffPage.dialog.removeDlgBt(params.dialog, data);
			}
			
			if (injectid == "#content") {
				if(params.dialog) {
					dlgInst.html(data["html"]);
				} else {
					if(data["injectid"] !== undefined) {
						jQuery("#" + data["injectid"]).html(data["html"]);
					} else {
						jQuery(injectid).html(data["html"]);
					}
				}
			} else {
                if(params.dialog) {
					dlgInst.html(data["html"]);
                } else {
				    if (params.replace || jQuery(data["html"]).is(injectid.replace("#", "")) || jQuery(injectid, data["html"]))
					    jQuery(injectid).replaceWith(data["html"]);
				    else
					    jQuery(injectid).html(data["html"]);
                }
			}
			//if(params.dialog)
				//ff.ffPage.dialog.makeDlgBt(params.dialog, data);			
		} else {
			if(params.component === null && injectid == "#content" && data["injectid"] !== undefined) {
				jQuery("#" + data["injectid"]).replaceWith(data["html"]);
			} else {
				if (params.replace === undefined || params.replace === true)
					jQuery(injectid).replaceWith(data["html"]); //--errore-- problema con il record
				else
					jQuery(injectid).html(data["html"]); //--errore-- problema con il record
			}

			if (data["hidden"] !== undefined)
				jQuery("#" + params.component + "_hidden").replaceWith(data["hidden"]);
		}
        		
		if (data["footers"] !== undefined) {
			var tmpdiv = ff.getUniqueID();
			jQuery("body").append('<div id="fftmp' + tmpdiv + '">' + data["footers"] + '<script type="text/javascript">jQuery("#fftmp' + tmpdiv + '").remove();</script></div>');
		}
		that.doEvent({
			"event_name" : "onUpdatedContent"
			, "event_params" : [params, data]
		});

		if (params.callback !== undefined && !params.chainupdate)
			params.callback(data, params.customdata);
		
		var res = that.doEvent({
			"event_name"	: "onRequestDone",
			"event_params"	: [data, params, injectid]
		});

		ff.ajax.unblockUI(params.chainupdate, false, data);
	} else if (data["url"] !== undefined) {
		if (params.doredirects || data["doredirects"] !== undefined) {
			if (params.dialog && ff.ffPage.dialog.get(params.dialog) && ff.ffPage.dialog.get(params.dialog).instance) {
				ff.ffPage.dialog.get(params.dialog).instance.dialog("close"); 
			}
			
			var res = that.doEvent({
				"event_name"	: "onRedirect",
				"event_params"	: [data["url"], data, this.mydata]
			});

			if (res !== undefined && res[res.length - 1]) {
				ff.ajax.unblockUI(params.chainupdate, false, data);		
				return;
			}

			top.location.href = data["url"];
			return true;
		}

		var res = that.doEvent({
			"event_name"	: "onRedirect",
			"event_params"	: [data["url"], data, this.mydata]
		});

		var lastres = null;
		if (res !== undefined && res[res.length - 1]) {
			lastres = res[res.length - 1];
			
			if (lastres.break) {
				ff.ajax.unblockUI(params.chainupdate, false, data);
				return;
			}
		}
		
		if (params.callback !== undefined && !params.chainupdate) {
			var res = params.callback(data, params.customdata);
			if(res === "return") {
				ff.ajax.unblockUI(params.chainupdate, false, data);
				return true;
			}
		}
		
		if (lastres) {
			params.callback = lastres.callback;
		} /*else {  on redirect se abilitato manda in blocco l'unblock ui .... record modify --> redirect sullo stesso record --> agiorna --> si blocca la ui
			params.callback = undefined;
		}*/
		
		/*if(!params.chainupdate)
			ff.ajax.unblockUI(params.chainupdate, false, data); //se inserito nella if della callback l'activecombo nn triggera e nn seleziona il valore appena inserito

        if (params.callback !== undefined) {
            var res = params.callback(data, params.customdata);
            if(res === "return") {
                return true;
            }
        }*/

		var res = that.doEvent({
			"event_name"	: "onRequestDone",
			"event_params"	: [data, params, injectid]
		});
		if (res !== undefined && res[res.length - 1] && res[res.length - 1].break) {
			ff.ajax.unblockUI(params.chainupdate, false, data);
			return;
		}
		
		// do redirect
		var fields = [];
	
		if(data["component"] !== undefined && data["component"]) {
			fields.push({name: "XHR_COMPONENT", value: data["component"]});
			params.component = data["component"];
			
			if (data["section"] && jQuery("#" + params.component + "_" + data["section"]).attr("id") !== undefined) {
				fields.push({name: "XHR_SECTION", value: data["section"]});
				params.section = data["section"];
			}
			
			if(data["injectid"] !== undefined)
				params.injectid = "#" + data["injectid"];
		}
            
		if (params.dialog) {
            fields.push({name: "XHR_DIALOG_ID", value: params.dialog});
        }
			
		var dataType = (params.jsonp !== undefined ? (params.jsonp ? "jsonp json" : "json") : (ff.origin != ff.httpGetOrigin() ? "jsonp json" : "json"));

		jQuery.ajax({
			  "url"			: ff.fixPath(data["url"])
			, "cache"		: false
			, "async"		: true
			, "data"		: fields
			, "type"		: "GET"
			, "dataType"	: dataType
			, "jsonp"		: (dataType == "jsonp" ? "XHR_JSONP" : undefined)
			, "success"		: ff.ajax.onSuccess
			, "mydata"		: {
				"params"		: params,
				"parsed_url"	: ff.fixPath(data["url"]),
				"parsed_fields" : fields
			}
		});
	} else {
		if (params.callback !== undefined && !params.chainupdate) {
			ff.ajax.unblockUI(params.chainupdate, false, data);
			params.callback(data, params.customdata);
		}

		var res = that.doEvent({
			"event_name"	: "onRequestDone",
			"event_params"	: [data, params, injectid]
		});
		if (res !== undefined && res[res.length - 1] && res[res.length - 1].break) {
			ff.ajax.unblockUI(params.chainupdate, false, data);
			return;
		}
		
		if (params.doredirects && !(params.dialog && data["close"] === false)) {
			top.location.reload(true);
			return true;
		}
		
		if (params.callback === undefined) {
			ff.ajax.unblockUI(params.chainupdate, false, data);		
		}
	}
	return true;
}

}; // publics' end


return that;

// code's end.
})();
