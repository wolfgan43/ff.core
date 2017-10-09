/**
 * Forms Framework Javascript Handling Object
 *	ffPageNavigator' namespace
 */

ff.ffPageNavigator = (function () {

// inits
ff.pluginAddInitLoad("ff.ffPageNavigator", function () {
	ff.addEvent({
		'event_name' : 'onClearComponent'
		, 'func_name' : function (component) {
			if (ff.struct.get("comps").get(component).type === "ffGrid") {
				ff.ffPageNavigator.deleteNavigator(component);
			}
		}
	});
}, 'b30117b6-9893-4c30-8561-3ef9fd653018');

ff.pluginAddInit("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name"	: "onUpdatedContent",
		"func_name"		: function (params, data) {
			if (params.component !== undefined && params.section == "GridData" && ff.struct.get("comps").get(params.component).type == "ffGrid") {
				ff.ffPageNavigator.updateButtons(params.component, data["rows"], data["page"]);
			}
		}
	});
});

// privates
var navigators = [];

function calcPage(id) {
	navigators[id].totpage = Math.ceil(navigators[id].totrec / navigators[id].rec_per_page);
	navigators[id].totframe = Math.ceil(navigators[id].totpage / navigators[id].page_per_frame);

	if (navigators[id].page > navigators[id].totpage)
		navigators[id].page = navigators[id].totpage;
	else if (navigators[id].page < 1)
		navigators[id].page = 1;

	if (navigators[id].totpage > navigators[id].page_per_frame) {
		navigators[id].start_page = navigators[id].page - navigators[id].half_frame;
		if (navigators[id].start_page < 1)
			navigators[id].start_page = 1;

		navigators[id].end_page = navigators[id].start_page + navigators[id].page_per_frame - 1;
		if (navigators[id].end_page > navigators[id].totpage)
			navigators[id].end_page = navigators[id].totpage;

		navigators[id].start_page = navigators[id].end_page - navigators[id].page_per_frame + 1;
	} else {
		navigators[id].start_page = 1;
		navigators[id].end_page = navigators[id].totpage;
	}

	jQuery("." + id + "_pageNavigator").find(".totpage").html(navigators[id].totpage);
}

function drawButtons(id) {
	jQuery("." + id + "_pageNavigator .pages").hide();

	if(navigators[id].totpage == 1) {
		jQuery("." + id + "_pageNavigator .page").remove();
        jQuery("." + id + "_pageNavigator .choice").hide();
	} else {
	    if(navigators[id].code_template) {
		    //jQuery("." + id + "_pageNavigator").each(function () {  
			    jQuery("." + id + "_pageNavigator .page").remove();
			    
                var processed_code_template = '';
			    for (var i = navigators[id].start_page; i <= navigators[id].end_page; i++) {
				    processed_code_template = processed_code_template + navigators[id].code_template.replace(/\[page\]/g, i);
			    }    

                jQuery("." + id + "_pageNavigator ." + id + "_pagetemplate").after(processed_code_template);
                jQuery("." + id + "_pageNavigator .pages").fadeIn();
                jQuery("." + id + "_pageNavigator .choice").fadeIn();
		    //});
	    }
	}
}

var that = { // publics
__ff : true, // used to recognize ff'objects

"addNavigator" : function (params) {
	if (navigators[params.id] === undefined) {
	    navigators[params.id] = {
		      "page_parname"				: params.page_parname
		    , "page"						: params.page
		    , "rec_per_page_parname"		: params.rec_per_page_parname
		    , "rec_per_page"				: params.rec_per_page
		    , "page_per_frame"				: params.page_per_frame
		    , "totrec"						: params.totrec
		    , "doAjax"						: params.doAjax
	    }

	    // adjust values
        if(navigators[params.id].page_per_frame > 0) {
	        navigators[params.id].half_frame		= Math.floor(navigators[params.id].page_per_frame / 2);
	        navigators[params.id].page_per_frame	= (navigators[params.id].half_frame * 2) + 1;
        }

	    if (navigators[params.id].code_template === undefined) {
		    jQuery("." + params.id + "_pageNavigator").each(function () {
			    var template = jQuery("." + params.id + "_pagetemplate", this);
			    navigators[params.id].code_template = template.removeAttr("style").outerHTML().replace(params.id + "_pagetemplate", "page");
                template.html("");
                template.hide();
		    });   
            
	    }

	    calcPage(params.id);
	    drawButtons(params.id);
	    
	    that.updateButtons(params.id);
    }

	

	jQuery("." + params.id + "_pageNavigator").css("visibility", "visible");
    
	that.doEvent({
		"event_name"	: "addNavigator",
		"event_params"	: [params.id, navigators]
	});
},

"deleteNavigator" : function (id) {
	if (navigators[id] !== undefined)
		delete navigators[id];
},

"updateButtons" : function(id, newTotPage, page) {
	if(navigators[id] === undefined)
		return;
	
	if(page !== undefined) {
		navigators[id].page = page;
	}
		
	if (newTotPage !== undefined && navigators[id].totrec !== newTotPage) {
		navigators[id].totrec = newTotPage;
		calcPage(id);
		drawButtons(id);
	}

	//drawButtons(id);

	jQuery("." + id + "_pageNavigator").find(".currentpage").val(navigators[id].page);
	
    jQuery("." + id + "_pageNavigator").find(".totelem").children("span").text(navigators[id].totrec);

	jQuery("." + id + "_pageNavigator").each(function () {
		jQuery(".page", this).removeClass("current");
		jQuery(".selectors", this).removeClass("current");
		jQuery(".pages > .page:eq(" + (navigators[id].page - navigators[id].start_page) + ")", this).addClass("current");
		jQuery(".perPage > .selector_" + navigators[id].rec_per_page, this).addClass("current");
		
		jQuery(".perPage > .selectors:not(.selector_all)", this).each(function() {
			if(parseInt(jQuery(this).attr("class").split(" ")[1].split("_")[1])	> navigators[id].totrec) {
				jQuery(this).hide();
			} else {
				jQuery(this).show();
			}
		});

/*		jQuery(".page", this).hide();
		for (var i = navigators[id].start_page - 1; i < navigators[id].end_page; i++) {
			jQuery(".page:eq(" + i + ")", this).show();
		}
*/
		jQuery("*", this).unbind(".ff.ffPageNavigator");

		if (navigators[id].totpage > 1) {
			jQuery(".prev", this).removeClass("disabled");
			jQuery(".next", this).removeClass("disabled");
			//jQuery(".prev", this).show();
			//jQuery(".next", this).show();
			jQuery(".prev", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.prevPage);
			jQuery(".next", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.nextPage);
		} else {
			jQuery(".prev", this).addClass("disabled");
			jQuery(".next", this).addClass("disabled");
			//jQuery(".prev", this).hide();
			//jQuery(".next", this).hide();
			jQuery(".prev", this).unbind(".ff.ffPageNavigator");
			jQuery(".next", this).unbind(".ff.ffPageNavigator");
		}

		if (navigators[id].start_page > 1) { 
			jQuery(".prev-frame", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.prevFrame);
			jQuery(".prev-frame", this).show();
			//jQuery(".prev-frame", this).removeClass("disabled");
		} else {
			jQuery(".prev-frame", this).hide();
			//jQuery(".prev-frame", this).addClass("disabled");
		}

		if (navigators[id].end_page < navigators[id].totpage) {
			jQuery(".next-frame", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.nextFrame);
			jQuery(".next-frame", this).show();
			//jQuery(".next-frame", this).removeClass("disabled");
		} else {
			jQuery(".next-frame", this).hide();
			//jQuery(".next-frame", this).addClass("disabled");
		}
        
        if (navigators[id].page > 1) {
            jQuery(".first", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.firstPage);
            //jQuery(".first", this).show();
            jQuery(".first", this).removeClass("disabled");
        } else {
        	//jQuery(".first", this).hide();
            jQuery(".first", this).addClass("disabled");
        }

        if (navigators[id].page < navigators[id].totpage) {
            jQuery(".last", this).bind("click.ff.ffPageNavigator", {"id" : id}, that.lastPage);
            //jQuery(".last", this).show();
            jQuery(".last", this).removeClass("disabled");
        } else {
        	//jQuery(".last", this).hide();
            jQuery(".last", this).addClass("disabled");
        }
	});

	that.doEvent({
		"event_name"	: "onUpdateButtons",
		"event_params"	: [id, navigators]
	});
},

"goToPage" : function (id, page, records_per_page, doAjax) {
	if (page !== null) {
		navigators[id].page = page;
		jQuery("#" + id + "_" + navigators[id].page_parname).val(page);
	}
    
    if(doAjax !== false) {
        doAjax = navigators[id].doAjax;
    }
	if (records_per_page !== undefined) {
		if(records_per_page > navigators[id].totrec)
			records_per_page = navigators[id].totrec;

		navigators[id].rec_per_page = records_per_page;
		jQuery("#" + id + "_" + navigators[id].rec_per_page_parname).val(records_per_page);
	}

	calcPage(id);
	drawButtons(id);
	that.updateButtons(id);
	
	jQuery("#frmAction").val(id + "_nav");
	if (doAjax)
	{
		var ctx = ff.struct.get("comps").get(id).ctx;
		if (ctx)
		{
			var oCtx = ff.ajax.ctxGet(ctx);
			oCtx.doRequest({
				"component" : id,
				"section"	: "GridData",
				"callback"	: function () {
					jQuery("a", oCtx.getInstance()).each(function () {
						var ret_url = ff.history.gup("ret_url", this.href);
						if (ret_url.length)
							this.href = this.href.replace(/ret_url=[^&#\'\"]*/, "ret_url=" + ff.ffPageNavigator.fixUrl(ret_url, id, records_per_page));
					});
					that.doEvent({
						"event_name"	: "onGoToPage",
						"event_params"	: [id, page]
					});
				}
			});
		}
		else
		{
			ff.ajax.doRequest({
				"component" : id,
				"section"	: "GridData",
				"callback"	: function () {
					jQuery("a").each(function () {
						var ret_url = ff.history.gup("ret_url", this.href);
						if (ret_url.length)
							this.href = this.href.replace(/ret_url=[^&#\'\"]*/, "ret_url=" + ff.ffPageNavigator.fixUrl(ret_url, id, records_per_page));
					});
					that.doEvent({
						"event_name"	: "onGoToPage",
						"event_params"	: [id, page]
					});
				}
			});
		}
	} else {
    var query = window.location.search.substring(1);
    query = query.split("&");
        var newQuery = [];
        query.each(function(i, value) {
            if(value) {
                switch(value.split("=")[0]) {
                    case id + "_" + navigators[id].page_parname:
                    case id + "_" + navigators[id].rec_per_page_parname:
                        break;
                    default:
                        newQuery.push(value);
                }
            }
        });
        
        newQuery.push(id + "_" + navigators[id].page_parname + "=" + navigators[id].page); 
        if(records_per_page || jQuery("." + id + "_pageNavigator .perPage .selectors:not(.selector_all)").length) 
            newQuery.push(id + "_" + navigators[id].rec_per_page_parname + "=" + navigators[id].rec_per_page);
        
        window.location.href = window.location.pathname + "?" + newQuery.join("&") + window.location.hash;
/*
		jQuery("#" + id + "_" + navigators[id].page_parname).val(navigators[id].page);
		jQuery("#" + id + "_" + navigators[id].rec_per_page_parname).val(navigators[id].rec_per_page);
		jQuery("#frmMain").submit();
*/
	}
},

"prevPage" : function (ev) {
	navigators[ev.data.id].page--;
	if (navigators[ev.data.id].page < 1)
		navigators[ev.data.id].page = navigators[ev.data.id].totpage;
	that.goToPage(ev.data.id, navigators[ev.data.id].page);
},

"nextPage" : function (ev) {
	navigators[ev.data.id].page++;
	if (navigators[ev.data.id].page > navigators[ev.data.id].totpage)
		navigators[ev.data.id].page = 1;
	that.goToPage(ev.data.id, navigators[ev.data.id].page);
},

"prevFrame" : function (ev) {
	navigators[ev.data.id].start_page -= navigators[ev.data.id].page_per_frame;
	if (navigators[ev.data.id].start_page < 1)
		navigators[ev.data.id].start_page = 1;
	navigators[ev.data.id].end_page = navigators[ev.data.id].start_page + navigators[ev.data.id].page_per_frame - 1;
	if (navigators[ev.data.id].end_page > navigators[ev.data.id].totpage)
		navigators[ev.data.id].end_page = navigators[ev.data.id].totpage;

	that.updateButtons(ev.data.id);
},

"nextFrame" : function (ev) {
	navigators[ev.data.id].end_page += navigators[ev.data.id].page_per_frame;
	if (navigators[ev.data.id].end_page > navigators[ev.data.id].totpage)
		navigators[ev.data.id].end_page = navigators[ev.data.id].totpage;
	navigators[ev.data.id].start_page = navigators[ev.data.id].end_page - navigators[ev.data.id].page_per_frame + 1;
	if (navigators[ev.data.id].start_page < 1)
		navigators[ev.data.id].start_page = 1;
	
	that.updateButtons(ev.data.id);
},

"firstPage" : function (ev) {
	navigators[ev.data.id].page = 1;

	that.goToPage(ev.data.id, navigators[ev.data.id].page);

	that.doEvent({
		"event_name"	: "firstPage",
		"event_params"	: [ev.data.id, navigators]
	});
},

"lastPage" : function (ev) {
	navigators[ev.data.id].page = navigators[ev.data.id].totpage;

	that.goToPage(ev.data.id, navigators[ev.data.id].page);

	that.doEvent({
		"event_name"	: "lastPage",
		"event_params"	: [ev.data.id, navigators]
	});
},

"fixUrl" : function (ret_url, id, records_per_page) {
	ret_url = unescape(ret_url);

	var params = [];
	var hash_params = ff.hash();

	var new_ret_url	= "";

	if (ret_url.indexOf("?") >= 0)
	{
		new_ret_url	= ret_url.substring(0, ret_url.indexOf("?") + 1);

		ret_url = ret_url.substring(ret_url.indexOf("?") + 1);
		if (ret_url.length) {
			params	= ret_url.split("&");

			for (var i = 0; i < params.length; i++) {
				if (params[i].indexOf("=") >= 0) {
					var parts = params[i].split("=");
					hash_params.set(parts[0], parts[1]);
				}
			}
		}
	}
	else
	{
		new_ret_url	= ret_url + "?";
	}

	hash_params.set(id + "_" + navigators[id].page_parname, navigators[id].page);
	if (records_per_page !== undefined)
		hash_params.set(id + "_" + navigators[id].rec_per_page_parname, navigators[id].rec_per_page);

	hash_params.each(function (key, value) {
		new_ret_url += key + "=" + value + "&";
	});

	return escaped_newurl = escape(new_ret_url).replace(/\//g, "%2F");
}

}; // publics' end

return that;

// code's end.
})();
