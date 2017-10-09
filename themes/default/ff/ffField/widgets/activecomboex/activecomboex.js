/**
 * Forms Framework Javascript Handling Object
 *	activecombo fields' plugin namespace
 */

ff.ffField.activecomboex = (function () {

// private vars
var innerURL			= "";
var theme_dir			= "";

var attr			= ff.hash();
var data			= ff.hash();
var selectedvalues	= ff.hash();

var sources			= ff.hash();
var controls_waiting = ff.hash();

var that = { // publics
__ff : true, // used to recognize ff'objects

"init" : function (params) {
	innerURL			= params.innerURL;
	theme_dir			= params.theme_dir;

    ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearField});
    ff.ajax.addEvent({"event_name" : "onUpdateField", "func_name" : that.onUpdateField});
},

"getAttr" : function (control) {
	if (control === undefined)
		return attr;
	else
		return attr.get(control);
},

"getSelectedValue" : function (control) {
	if (control === undefined)
		return selectedvalues;
	else
		return selectedvalues.get(control);
},

"addCombo" : function (params) {
	if (attr.isset(params.id) === undefined) {
		attr.set(params.id, params.attr);
		data.set(params.id, params.data);
		selectedvalues.set(params.id, params.selected_value);

		that.doEvent({
			"event_name"	: "onAddCombo",
			"event_params"	: [params.id]
		});

	}
},

"deleteCombo" : function (id) {
	var tmp_attr = attr.get(id);
	if(tmp_attr !== undefined)
		sources.unset(tmp_attr["data_src"]);
	
	attr.unset(id);
	data.unset(id);
	selectedvalues.unset(id); 
},

"chainreset" : function (control) {
	var tmp_attr = attr.get(control);
	if (tmp_attr !== undefined && tmp_attr["child"].length > 0) {
		for (var a = 0; a < tmp_attr["child"].length; a++) {
			selectedvalues.set(tmp_attr["child"][a], null);
			ff.ajax.chainupdate.updated.set(tmp_attr["child"][a]);
			that.chainreset(tmp_attr["child"][a]);
		}
	}
},

"select" : function (control, value) {
	var father_value = null;
	var tmp_attr = attr.get(control);

	selectedvalues.set(control, value);
	that.chainreset(control);

	if (tmp_attr !== undefined && tmp_attr["father"]) {
		father_value = document.getElementById(tmp_attr["father"]).value;
	}

	that.refill(control, father_value, null, false);
},

"dialog_success" : function (control, resource) {
	var father_value = null;
	var tmp_attr = attr.get(control);
	
	if (resource) {
		if (ff.ajax.chainupdate.resources.get(resource) !== undefined) {
			selectedvalues.set(control, ff.ajax.chainupdate.resources.get(resource));
			ff.ajax.chainupdate.updated.set(control)
			that.chainreset(control);

			if (tmp_attr !== undefined && tmp_attr["father"]) {
				father_value = document.getElementById(tmp_attr["father"]).value;
			}

			that.refill(control, father_value, null, false);
		}
	} else {
		if (tmp_attr !== undefined && tmp_attr["father"]) {
			father_value = document.getElementById(tmp_attr["father"]).value;
		}

		that.refill(control, father_value, null, false);
	}
},

"refill" : function (control, father_value, selected_value, use_cache) {
	var node = document.getElementById("activecomboex_" + control);
	var tmp_attr = attr.get(control);
	var tmp_data = data.get(control);

	if(tmp_attr !== undefined) {
        if (tmp_attr["data_src"] == "") { // like activecombo, preloaded in page
		    var buffer = that.parseheader(control);

	        if(tmp_attr["control_type"] == "label") {
				for (var a = 0; a < tmp_data.length; a++) {
			        if ((father_value == null && tmp_attr["father"] == null) || tmp_data[a][0] == father_value) {
				        if (
						        !tmp_attr["limit_select"]
						        || (tmp_attr["limit_select"] && tmp_data[a][1] == selected_value)
					        ) {
					        buffer += ' value="' + tmp_data[a][1] + '" ';
					        break;
				        }
			        }
		        }
        		buffer += ' />';
			    node.innerHTML = buffer;
			} else if(tmp_attr["control_type"] == "checkbox") {
                var separator = tmp_attr["separator"];
                if(!(separator.length > 0))
                    separator = ",";

                var arrSelectedValue = selected_value.split(separator);
                for (var a = 0; a < tmp_data.length; a++) {
                    if ((father_value == null && tmp_attr["father"] == null) || tmp_data[a][0] == father_value) {
                        if (
                                !tmp_attr["limit_select"]
                                || (tmp_attr["limit_select"] && tmp_data[a][1] == selected_value)
                            ) {
                            buffer += '<div class="row"><input type ="checkbox" id="' + control + '_' + a + '" value="' + tmp_data[a][1] + '" ';
                            for (var x = 0; x < arrSelectedValue.length; x++) {
                                if (tmp_data[a][1] == arrSelectedValue[x])
                                    buffer += 'checked="checked" ';
                            }
                            
                            buffer += ' onChange="';
                            buffer += 'ff.ffField.activecomboex.recalc(\'' + control + '\',\'' + tmp_data.length + '\',\'' + separator + '\');" ';
                            buffer += ' /><label>' + tmp_data[a][2] + '</label></div>';
                        }
                    }
                }
                buffer += '</div>';
                node.innerHTML = buffer; 
               // ff.ffField.activecomboex.recalc(control, tmp_data.length, separator);
            } else {
                for (var a = 0; a < tmp_data.length; a++) {
			        if ((father_value == null && tmp_attr["father"] == null) || tmp_data[a][0] == father_value) {
				        if (
						        !tmp_attr["limit_select"]
						        || (tmp_attr["limit_select"] && tmp_data[a][1] == selected_value)
						        //|| (tmp_attr["control_type"] == "label" && tmp_data[a][1] == selected_value)
					        ) {
					        buffer += '<option value="' + tmp_data[a][1] + '" ';
					        if (tmp_data[a][1] == selected_value)
						        buffer += 'selected ';
					        buffer += '>' + tmp_data[a][2] + '</option>';
				        }
			        }
		        }
		        buffer += '</select>';
                if(tmp_attr["addPlus"]) {
                    buffer += '<a class="qta-piu" href="javascript:void(0);" onClick="jQuery(this).prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev(\'select\').children(\'option:selected\').val()) + 1); jQuery(this).prev(\'select\').children(\'option:selected\').text(jQuery(this).prev(\'select\').children(\'option:selected\').val());">+</a><a class="qta-meno" href="javascript:void(0);" onClick="if(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) >= 2) { jQuery(this).prev().prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) - 1); jQuery(this).prev().prev(\'select\').children(\'option:selected\').text(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()); }">-</a>';
                }
                node.innerHTML = buffer; 
            }
	    } else { 
		    // async loading
		    that.cascade_disable(control, true);  // disable fathers
		    that.cascade_disable(control, false); // disable childs
		    that.display_loading(control);
			
		    var str_data = "father_value=" + escape(father_value) + "&data_src=" + escape(tmp_attr["data_src"]);
			
			// get ancestor's data
			var ancest_data = "";
			var tmp_father = tmp_attr["father"];
			while (tmp_father !== null) {
				var ancest_attr = that.getAttr(tmp_father);
				ancest_data = "&ffActex_parent_data[" + ancest_attr["name"] + "]=" + that.getSelectedValue(tmp_father) + ancest_data;
				tmp_father = ancest_attr["father"];
			}
			str_data = str_data + ancest_data;
		    
		    ff.ffField.activecomboex.delayed_request(control, str_data, tmp_attr["data_src"], use_cache);
	    }
    }
    
    var childvalue = null;

    if (tmp_attr !== undefined && tmp_attr["child"].length > 0) {
        for (a = 0; a < tmp_attr["child"].length; a++) {
            if (selected_value != null) {
                childvalue = selectedvalues.get(tmp_attr["child"][a]);
            }

            that.refill(tmp_attr["child"][a], selected_value, childvalue, use_cache);
        }
    }
	
},
"recalc" : function(control, countCheck, separator) {
    var hidden = document.getElementById(control);
    var tmp = "";
    var element = null;
    
    for (i = 0; i < countCheck; i++)
    {
    
        element = document.getElementById(control + "_" + i);
        if (element.checked)
        {
            if (tmp.length)
                tmp = tmp + separator;
            tmp = tmp + element.value;
        }
    }
    hidden.value = tmp;
},
"delayed_request" : function (control, str_data, data_src, use_cache) {
    if(use_cache)
    {   
	    if (sources.isset(data_src))
	    {
		    if (sources.get(data_src) === true)
		    {
                controls_waiting.set(control, true);
			    return;
		    }

		    that.json_refill(sources.get(data_src), control);
		    return;
	    }

	    sources.set(data_src, true);
	}
    
	var dataType = (ff.origin != ff.httpGetOrigin() ? "jsonp json" : "json");
	
	jQuery.ajax({
		  url		: ff.fixPath(innerURL)
		, async		: true
		, data		: str_data
		, type		: "GET"
		, dataType	: dataType
		, jsonp		: (dataType == "jsonp json" ? "XHR_JSONP" : undefined)
		, success	: ff.ffField.activecomboex.async_refill
		, error		: ff.ffField.activecomboex.async_error
		, mydata	: {"control" : control, "data_src" : data_src}
	});
},

"parent_enable" : function (control) {
	var tmp_attr = attr.get(control);
	if (!tmp_attr)
		return;
	
	if (tmp_attr["father"]) {
		var node = document.getElementById("" + tmp_attr["father"]);
		if (node && node.firstChild)
			node.firstChild.disabled = false;

		that.parent_enable(tmp_attr["father"]);
	}
},

"cascade_disable" : function (control, moveup) {
	var node = document.getElementById("" + control);

	var tmp_attr = attr.get(control);
	if (!tmp_attr)
		return;
	
	if (node && node.firstChild)
		node.firstChild.disabled = true;

	if (moveup && tmp_attr["father"]) {
		that.cascade_disable(tmp_attr["father"], true);
	} else if (!moveup && tmp_attr["child"].length > 0) {
		for (var a = 0; a < tmp_attr["child"].length; a++) {
			that.cascade_disable(tmp_attr["child"][a], false);
		}
	}
},

"display_loading" : function (control) {
	var node = document.getElementById("activecomboex_" + control);
	node.innerHTML = 'Loading..&nbsp;&nbsp;<img src="' + ff.fixPath(theme_dir + '/ajax-loader.gif') + '" />';
},

"updatebt" : function (control) {
	var normalized_id = control.replace(/\[/g, "\\[").replace(/\]/g, "\\]").replace(/\-/g, "\\-");
	var tmp_attr = attr.get(control);
    if (!tmp_attr)
    return;
    
	if (tmp_attr["limit_select"]) {
		jQuery("#activecomboex_" + normalized_id + "_dialogaddlink").hide();
	} else {
		jQuery("#activecomboex_" + normalized_id + "_dialogaddlink").show();
	}

	var value = jQuery("#" + normalized_id).val();
	selectedvalues.set(control, value);
	
	if (tmp_attr["select_one"] && tmp_attr["select_one_val"] == value) {
		jQuery("#activecomboex_" + normalized_id + "_dialogeditlink").hide();
		jQuery("#activecomboex_" + normalized_id + "_dialogdeletelink").hide();
	} else if (tmp_attr["select_noone"] && tmp_attr["select_noone_val"] == value) {
		jQuery("#activecomboex_" + normalized_id + "_dialogeditlink").hide();
		jQuery("#activecomboex_" + normalized_id + "_dialogdeletelink").hide();
	} else {
		jQuery("#activecomboex_" + normalized_id + "_dialogeditlink").show();
		jQuery("#activecomboex_" + normalized_id + "_dialogdeletelink").show();
	}
},

"parseheader" : function (control) {
	var tmp_attr = attr.get(control);
    if (!tmp_attr)
    return;
    
	var buffer = '';
	if (tmp_attr["control_type"] == "label") {
		buffer = '<input class="' + tmp_attr["class"] + '" ';
		buffer += 'name="' + control + '" id="' + control + '" ' + tmp_attr["properties"] + ' ';
	} else if(tmp_attr["control_type"] == "checkbox") {
        buffer = '<input type="hidden" id="' + control + '" name="' + control + '" value="' + selectedvalues.get(control) + '" />';
        buffer += '<div class="' + (tmp_attr["class"].length > 0 ? tmp_attr["class"] + ' ' : '') + 'checkgroup">';
    } else {
		buffer = '<select class="' + tmp_attr["class"] + '" ';
		buffer += 'name="' + control + '" id="' + control + '" ' + tmp_attr["properties"];
		buffer += ' onChange="';
		if (tmp_attr["child"].length > 0) {
			for (var a = 0; a < tmp_attr["child"].length; a++) {
				buffer += 'ff.ffField.activecomboex.refill(\'' + tmp_attr["child"][a] +'\', this.value, null, false); ';
			}
		}
		buffer += 'ff.ffField.activecomboex.updatebt(\'' + control + '\'); ' + tmp_attr["onchange"] + '" >';

		if (tmp_attr["select_one"]) {
			buffer += '<option value="' + tmp_attr["select_one_val"] + '"';
			if (tmp_attr["select_one_val"] == selectedvalues.get(control))
				buffer += 'selected ';
			buffer += '>' + tmp_attr["select_one_label"] + '</option>';
		}

		if (tmp_attr["select_noone"]) {
			buffer += '<option value="' + tmp_attr["select_noone_val"] + '"';
			if (tmp_attr["select_noone_val"] == selectedvalues.get(control))
				buffer += 'selected ';
			buffer += '>' + tmp_attr["select_noone_label"] + '</option>';
		}
	}

	return buffer;
},

"async_error" : function () {
	var control = this.mydata;
	
	that.child_error_display(control);
},

"child_error_display" : function (control) {
	var node = document.getElementById("activecomboex_" + control);
	if (node) {
		node.innerHTML = 'Impossibile connettersi con il server, riprovare più tardi.';
		var tmp_attr = attr.get(control);
		if (tmp_attr && tmp_attr["child"])
			that.child_error_display(tmp_attr["child"]);
	}
},
"async_refill" : function (data, textStatus) {
	var control = this.mydata.control;
	var data_src = this.mydata.data_src;
	sources.set(data_src, data);
	that.json_refill(data, control);

	controls_waiting.each(function (k, v, i) {
//		console.log(k, v, i);
		var tmp_attr = attr.get(k);
		if (tmp_attr["data_src"] == data_src) {
			that.json_refill(data, k);
			controls_waiting.unset(k);
		}
	});
},

"json_refill" : function (data, control) {
	var node = document.getElementById("activecomboex_" + control);
	var tmp_attr = attr.get(control);
//alert("asd");
	var buffer = "";
	
	if (data === null) {
		that.child_error_display(control);
		return;
	} else {
        if(tmp_attr !== undefined) {
            buffer = that.parseheader(control);

            var opt_value = "";
            var opt_text = "";

            if(tmp_attr["control_type"] == "label") {
			    for (var i = 0; i < data.length; i++) {
                    opt_value = data[i].value;
                    opt_text = data[i].desc;
			        if (!tmp_attr["limit_select"] || (tmp_attr["limit_select"] && opt_value == selectedvalues.get(control))) {
				        buffer += ' value="' + opt_value + '" ';
				        break;
			        }
		        }
        	    buffer += ' />';
		        node.innerHTML = buffer;
		    } else if(tmp_attr["control_type"] == "checkbox") {
                var separator = tmp_attr["separator"];
                if(!(separator.length > 0))
                    separator = ",";

                var arrSelectedValue = selectedvalues.get(control).split(separator);

                for (var i = 0; i < data.length; i++) {
                    opt_value = data[i].value;
                    opt_text = data[i].desc;
                    if (
                            !tmp_attr["limit_select"]
                            || (tmp_attr["limit_select"] && opt_value == selectedvalues.get(control))
                        ) {
                        buffer += '<div class="row"><input type ="checkbox" id="' + control + '_' + i + '" value="' + opt_value  + '" ';
                        for (var x = 0; x < arrSelectedValue.length; x++) {
                            if (opt_value == arrSelectedValue[x])
                                buffer += 'checked="checked" ';
                        }

                        buffer += ' onChange="';
                        buffer += 'ff.ffField.activecomboex.recalc(\'' + control + '\',\'' + data.length + '\',\'' + separator + '\');" ';
                        buffer += ' /><label>' + opt_text + '</label></div>';
                    }
                }
                buffer += '</div>'; 
                node.innerHTML = buffer; 
                //ff.ffField.activecomboex.recalc(control, data.length, separator);
            } else {
		        for (var i = 0; i < data.length; i++) {
                    opt_value = data[i].value;
                    opt_text = data[i].desc;
			        if (!tmp_attr["limit_select"] || (tmp_attr["limit_select"] && opt_value == selectedvalues.get(control))) {
				        buffer += '<option value="' + opt_value + '" ';
				        if (opt_value == selectedvalues.get(control))
					        buffer += 'selected ';
				        buffer += '>' + opt_text + '</option>';
			        }
		        }
		        buffer += '</select>';
                if(tmp_attr["addPlus"]) {
                    buffer += '<a class="qta-piu" href="javascript:void(0);" onClick="jQuery(this).prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev(\'select\').children(\'option:selected\').val()) + 1); jQuery(this).prev(\'select\').children(\'option:selected\').text(jQuery(this).prev(\'select\').children(\'option:selected\').val());">+</a><a class="qta-meno" href="javascript:void(0);" onClick="if(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) >= 2) { jQuery(this).prev().prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) - 1); jQuery(this).prev().prev(\'select\').children(\'option:selected\').text(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()); }">-</a>';
                }
		        node.innerHTML = buffer;
            }
            
            if (tmp_attr["child"].length > 0) {
                for (var a = 0; a < tmp_attr["child"].length; a++) {
                    that.refill(tmp_attr["child"][a], selectedvalues.get(control), null, true);
                }
            }
        }
	}
	that.parent_enable(control);
	that.updatebt(control);
},

"onClearField" : function (component, key, field) {
	if (field.widget == "activecomboex") {
		if (component !== undefined) {
			switch (ff.struct.get(component).type) {
				case "ffDetails":
					var rows = parseInt(jQuery("#" + component + "_rows").val());
					for (var i = 0; i < rows; i++) {
						ff.ffField.activecomboex.deleteCombo(component + "_recordset[" + i + "][" + key + "]");
					}
					break;

				default:
					ff.ffField.activecomboex.deleteCombo(component + "_" + key);
			}
		} else {
			ff.ffField.activecomboex.deleteCombo(key);
		}
	}
},

"onUpdateField" : function (component, key, field) {
	if (field.widget == "activecomboex") {
		if (component !== undefined) {
			switch (ff.struct.get(component).type) {
				case "ffDetails":
					var rows = parseInt(jQuery("#" + component + "_rows").val());
					for (var r = 0; r < rows; r++) {
						if (ff.ajax.chainupdate.updated.isset(component + "_recordset[" + r + "][" + key + "]") === undefined) {
							that.dialog_success(component + "_recordset[" + r + "][" + key + "]");
						}
					}
					break;

				default:
					that.dialog_success(component + "_" + key);
			}
		} else {
			that.dialog_success(key);
		}
	}
}

}; // publics' end

//if(ff.addEvent !== undefined)
//    ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearField});

//if(ff.ajax.addEvent !== undefined)
//    ff.ajax.addEvent({"event_name" : "onUpdateField", "func_name" : that.onUpdateField});

return that;

// code's end.
})();
