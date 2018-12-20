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
__ff : "ff.ffGrid", // used to recognize ff'objects
"icons" : {
	"sort" : "fa-sort"
	, "sortAsc" : "fa-sort-asc"
	, "sortDesc" : "fa-sort-desc"
},
"class" : {
	"sort" : {
		"sortable" : "sorting",
		"ASC" : "sorting_asc",
		"DESC" : "sorting_desc"
	}
},
"properties" : {
	"aria-sort" : {
        "ASC" : "ascending",
        "DESC" : "descending"
	}
},
"ajaxOrder" : function (elem, id, field, dir, ctx) {
	jQuery("#frmAction").val(id + "_order");
    if (dir === undefined) {
        if (jQuery("#" + id + "_order").val() != field || jQuery("#" + id + "_direction").val() == "DESC")
            jQuery("#" + id + "_direction").val("ASC");
        else
            jQuery("#" + id + "_direction").val("DESC");
    } else {
        jQuery("#" + id + "_direction").val(dir);
    }
    jQuery("#" + id + "_order").val(field);
    var direction = jQuery("#" + id + "_direction").val(); // ASC || DESC
    jQuery(elem).parent().find("TH").removeClass([this.class.sort["sortable"], this.class.sort["ASC"], this.class.sort["DESC"]]).addClass(this.class.sort["sortable"]).attr("aria-sort", false);
    jQuery(elem).removeClass(this.class.sort["sortable"]).addClass(this.class.sort[direction]);
    jQuery(elem).attr("aria-sort", this.properties["aria-sort"][direction]);

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
"advsearchHide" : function(component, hideClass) {
    if(!hideClass) {
        hideClass = 'hidden';
    }

	jQuery('#' + component + ' .adv-search').addClass('hidden'); 

//	jQuery('#' + component + '_search').removeClass('hidden');
//	jQuery('#' + component + '_searchadv').addClass('hidden');
},
"advsearchShow" : function(component, hideClass) {
    if(!hideClass) {
        hideClass = 'hidden';
    }

	jQuery('#' + component + ' .adv-search').removeClass('hidden');
	
//	jQuery('#' + component + '_search').addClass('hidden');
//	jQuery('#' + component + '_searchadv').removeClass('hidden');
},
"advsearchToggle" : function(component, hideClass) {
	if(!hideClass) {
        hideClass = 'hidden';
	}
    jQuery('#' + component + ' .adv-search').toggleClass(hideClass);
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
},
"dialogOpen" : function(record_id, url, title, elemHighlight) {
	ff.ffPage.dialog.doOpen(record_id, url, title, undefined, elemHighlight);
	
},

"searchHistoryPush" : function(key) {
	var search = ff.updateQueryString(location.search, key + "_src", jQuery("#" + key + "_src").val(), "?");

	if(history.pushState) {
        history.pushState(null, null, (search ? search : window.location.pathname));
    } else {
        location.search = search;
    }
}

}; // publics' end

    /* Init obj */
    function constructor() { // NB: called below publics
        ff.initExt(that);
    }

    if(document.readyState == "complete") {
        //  constructor(); //va in contrasto con libLoaded
    } else {
        window.addEventListener('load', function () {
            constructor();
        });
    }

return that;

// code's end.
})();
