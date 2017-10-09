/**
 * Forms Framework Javascript Handling Object
 *	ffGrid' namespace
 */

ff.ffGrid = (function () {

// inits
ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name"	: "onUpdateComponent",
		"func_name"		: function (componentid, component, data) {
			if (component.type == "ffGrid") {
				if (component.ctx) {
					var oCtx = ff.ajax.ctxGet(component.ctx);
					oCtx.doRequest({
						"action"		: "refresh",
						"component"		: componentid,
						"section"		: (ff.struct.get("comps").get(componentid).update_all || (data && data.update_all) ? undefined : "GridData"),
						"chainupdate"	: true
					});
				} else {
					ff.ajax.doRequest({
							"component"	: componentid,
							"section"		: (ff.struct.get("comps").get(componentid).update_all || (data && data.update_all) ? undefined : "GridData"),
							"chainupdate"	: true
					});
				}
				return true;
			}
		}
	});
});

// privates

var that = { // publics
__ff : true, // used to recognize ff'objects

"ajaxOrder" : function (id, field, dir, ctx) {
	jQuery("#frmAction").val(id + "_order"); 
	jQuery("#" + id + "_order").val(field);
	if (dir === undefined) {
		if (jQuery("#" + id + "_direction").val() == "DESC")
			jQuery("#" + id + "_direction").val("ASC");
		else
			jQuery("#" + id + "_direction").val("DESC");
	} else {
		jQuery("#" + id + "_direction").val(dir);
	}
	
	if (ctx === undefined) {
	    ff.load("ff.ajax", function() {
			ff.ajax.doRequest({
				 "component"		: id
				 , "section"			: "GridData"
			});
		});
	} else {
		var oCtx = ff.ajax.ctxGet(ctx);
		oCtx.doRequest({
			"action"	: "order"
			, "component" : id
			, "section"	: "GridData"
		});
	}
},

"turnToggle" : function (component, nome, value) {
	var re = new RegExp("^" + component + "_recordset_values\\[\\d+\\]\\[" + nome + "\\]$");

	if(value > 0) {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", false).trigger("change");
		
		return 0;
	} else {
		jQuery("input").filter(function() {
			return this.id.match(re);
		}).prop("checked", true).trigger("change");

		return 1;
	}
},

"turnON" : function (component, nome) {
	var re = new RegExp("^" + component + "_recordset_values\\[\\d+\\]\\[" + nome + "\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", true).trigger("change");
},

"turnOFF" : function (component, nome) {
	var re = new RegExp("^" + component + "_recordset_values\\[\\d+\\]\\[" + nome + "\\]$");
	jQuery("input").filter(function() {
		return this.id.match(re);
	}).prop("checked", false).trigger("change");
},
"advsearchHide" : function(component, id) {
	jQuery('#' + component + 'searched').insertAfter('#' + id + ' .simple'); 
	jQuery('#' + component + 'advsearch').addClass('hidden'); 
},
"advsearchShow" : function(component, id) {
	jQuery('#' + component + 'searched').appendTo('#' + component + 'advsearch');  
	jQuery('#' + component + 'advsearch').removeClass('hidden'); 
},
"advsearchToggle" : function(component, id, visibility) {
	if(visibility === undefined) {
		if(!jQuery('#' + component + 'advsearch').hasClass('hidden')) { 
			ff.ffGrid.advsearchHide(component, id);
		} else { 
			ff.ffGrid.advsearchShow(component, id);
		}
	} else {
		if(visibility == "hidden") {
			ff.ffGrid.advsearchHide(component, id);
		} else {
			ff.ffGrid.advsearchShow(component, id);
		}
	}
},
"dropdownSort" : function(component, id) {
	jQuery("#" + id + " ul.sort").appendTo(jQuery("#" + id + " .dropdown-sort-data"));
	jQuery("#" + id + " ul.sort a.GridSortIcons").hide();

	if(!jQuery("#" + id + " .sort-block").hasClass("loaded")) {
		jQuery("#" + id + " .dropdown-sort").click(function() {
			if(jQuery("#" + id + " ul.sort").is(":visible")) {
				jQuery("#" + id + " ul.sort").slideUp();
			} else {
				jQuery("#" + id + " ul.sort").slideDown();
			}
		});

		jQuery("#" + id + " ul.sort a.FormsGridLabel").bind("click", function() {
			jQuery("#" + id + " ul.sort li").removeClass("current");
			jQuery(this).parent().addClass("current");
			jQuery("#" + id + " .dropdown-sort").html(jQuery(this).text() + '<span class="arrow"></span>');
			jQuery("#" + id + " .dropdown-sort-dir").html(jQuery(this).parent().children(".GridSortIcons").children('span').parent());
			jQuery("#" + id + " .dropdown-sort-dir > *").hide();
			jQuery("#" + id + " .dropdown-sort-dir span." + (jQuery("#" + component + "direction").val() == 'ASC' ? 'descending' : 'ascending')).parent().show();

			jQuery("#" + id + " ul.sort").hide();
		});
		jQuery("#" + id + " .dropdown-sort-dir").bind("click", function() {
			jQuery(this).children("a").hide();
			jQuery("#" + id + " .dropdown-sort-dir span." + (jQuery("#" + component + "direction").val() == 'ASC' ? 'descending' : 'ascending')).parent().show();
		});
		jQuery("#" + id + " .sort-block").addClass("loaded");
	}
}

}; // publics' end

return that;

// code's end.
})();
