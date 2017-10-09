/**
 * Forms Framework Javascript Handling Object
 *	ffDetails' namespace
 */
$.fn.replaceAttr = function(aName, rxString, repString) {
	return this.attr(aName, function() {
		return $(this).attr(aName).replace(rxString, repString);
	});
};
ff.ffDetails = (function () {
	
// inits
ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name"	: "onUpdateComponent",
		"func_name"		: function (componentid, component) {
			if (component.type == "ffDetails") {
				if (component.ctx) {
					var oCtx = ff.ajax.ctxGet(component.ctx);
					oCtx.doRequest({
						"action"        : "refresh",
						"component"        : componentid,
						"chainupdate"    : true
					});
				} else {
					ff.ajax.doRequest({
							"component"    : componentid,
							"chainupdate"    : true
					});
				}
				return true;
			}			
		}
	});
});

//privates
var toggles = ff.hash();
var toggles_rows = ff.hash();
var toggle_all = 0;

var that = { // publics
__ff : true, // used to recognize ff'objects

"turnToggle" : function (component, nome) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[" + nome + "\\]$");
	
	var value = toggles.get(nome);

	if(value > 0) {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", false).trigger("change");
		
		toggles.set(nome, 0);
	} else {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", true).trigger("change");

		toggles.set(nome, 1);
	}
},

"turnToggleRow" : function (component, row) {
	var re = new RegExp("^" + component + "_recordset\\[" + row + "\\]\\[[^\\]]+\\]$");

	var value = toggles_rows.get(row);
	
	if(value > 0) {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", false).trigger("change");
		
		toggles_rows.set(row, 0);
	} else {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", true).trigger("change");

		toggles_rows.set(row, 1);
	}
},

"turnToggleAll" : function (component, row) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[[^\\]]+\\]$");

	if(toggle_all > 0) {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", false).trigger("change");
		
		toggle_all = 0;
	} else {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", true).trigger("change");

		toggle_all = 1;
	}
},

"turnON" : function (component, nome) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[" + nome + "\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", true).trigger("change");
},

"turnOFF" : function (component, nome) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[" + nome + "\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", false).trigger("change");
},

"turnONRow" : function (component, row) {
	var re = new RegExp("^" + component + "_recordset\\[" + row + "\\]\\[[^\\]]+\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", true).trigger("change");
},

"turnOFFRow" : function (component, row) {
	var re = new RegExp("^" + component + "_recordset\\[" + row + "\\]\\[[^\\]]+\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", false).trigger("change");
},

"turnONAll" : function (component) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[[^\\]]+\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", true).trigger("change");
},

"turnOFFAll" : function (component) {
	var re = new RegExp("^" + component + "_recordset\\[\\d+\\]\\[[^\\]]+\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", false).trigger("change");
},
"tabInit" : function (component) {
    if(jQuery("#" + component + "_jtab ul li").length > 0) {
        jQuery("#" + component + "_jtab").hide();

		ff.load("jquery.plugins.blockui", function () {
            jQuery.blockUI.defaults.css = {
                padding:    0,
                margin:        0,
                top:        '40%',
                left:        '45%',
                textAlign:    'center',
                cursor:        'wait'
            };
            jQuery.blockUI.defaults.overlayCSS = {};

            jQuery("#" + component + "_jtab").block({message: '<h1 class="block-loader"></h1>'});

        
            jQuery("#" + component + "_jtab").tabs({ 
                panelTemplate: '<fieldset></fieldset>', 
                "create": function(event, ui) { 
                    jQuery(this).unblock();
                    jQuery(this).fadeIn(function() {
                        if(jQuery(this).find("ul").hasClass('hori')) {
                            jQuery(this).addClass('hori');
                        } else {
                            var ulwidth = 0;
                            jQuery(this).find('li').each(function() {
                                    ulwidth = jQuery(this).width() + ulwidth;
                            });
                            if(ulwidth > jQuery(this).find("ul").width()) {
                                    jQuery(this).find('ul').addClass('hori');
                                    jQuery(this).addClass('hori');
                                    //jQuery(this).find('.fieldsetwrapper').height(jQuery(this).find('ul').outerHeight(true)); //QUESTA RIGA SBALLAVA LA VISUALIZZAZIONE DELLA MODIFICA DI PUBBLICAZIONE
                                    
                            }
                        }
                        if(jQuery("#" + component + "_jtab").closest(".ui-dialog-content").attr("id") !== undefined) {
                           // ff.ffPage.dialog.refresh(jQuery("#" + component + "_jtab").closest(".ui-dialog-content").attr("id").replace("ffWidget_dialog_container_", ""), true, false);
                        }
                    }); 
                },
                "activate": function(event, ui) {
                    if(jQuery("#" + component + "_jtab").closest(".ui-dialog-content").attr("id") !== undefined) {
                       // ff.ffPage.dialog.refresh(jQuery("#" + component + "_jtab").closest(".ui-dialog-content").attr("id").replace("ffWidget_dialog_container_", ""), true, false);
                    }
                }
            });
        });
    }   
}

, "onClearComponent" : function (component) {
	var pComp = ff.struct.get("comps").get(component);
	if (!pComp)
		return;

	if (pComp.type !== "ffDetails")
		return;
	
	var rows = parseInt(jQuery.fn.escapeGet(component + "_rows").val());
	
	// reset per ogni singolo campo
	pComp.fields.each( function (key, field) {
		// per ogni singola riga
		for (var i = 0; i < rows; i++) {
			var tmp_id = component + "_recordset[" + i + "][" + key + "]";
			
			ff.doEvent({
				"event_name"	: "onClearField",
				"event_params"	: [component, key, field, tmp_id]
			});
		}
	});
}

}; // publics' end

ff.pluginAddInitLoad("ff.ffDetails", function () {
	ff.addEvent({"event_name" : "onClearComponent", "func_name" : that.onClearComponent});
});

return that;

// code's end.
})();
