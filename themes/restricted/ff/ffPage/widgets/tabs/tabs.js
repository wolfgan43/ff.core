ff.ffPage.tabs = (function () {

/* privates */
var tabs = [];

var that = { /* publics */
__ff : true, /* used to recognize ff'objects */

"addTab" : function (id) {
	tabs[id] = jQuery("#tabs_" + id).tabs({
		"create" : function (event, ui) {
			that.doEvent({
				"event_name" : "onCreate"
				, "event_params" : [id, event, ui]
			});
			ff.doEvent({
				"event_name" : "initIFElement"
				, "event_params" : [id, "tabs"]
			});
			ff.pluginAddInit("ff.ffPage.dialog", function () {
				ff.ffPage.dialog.addEvent({
					"event_name" : "resize"
					, "func_name" : function(dlg, ui, tab) {
						if (ff.struct.get("comps").get(tab) && ff.struct.get("comps").get(tab).ctx === dlg) {
							if (ui === undefined || ui.originalSize.width != ui.size.width) {
								jQuery.fn.escapeGet("tabs_" + tab).width(jQuery.fn.escapeGet("ffWidget_dialog_container_" + dlg).width() - 20);
							}
						}
					}
					, "additional_data" : [id]
				});
			});
		}
		
		, "beforeActivate": function(event, ui) {
			that.doEvent({
				"event_name" : "onBeforeActivate"
				, "event_params" : [id, event, ui]
			});
		}
		
		, "activate": function(event, ui) {
			that.doEvent({
				"event_name" : "onActivate"
				, "event_params" : [id, event, ui]
			});
		}
//		"activate": function(event, ui) {
//			 var hash = ui.newTab.context.hash.replace(/^.*#/, "");
//			 jQuery.historyLoad(hash);
//
//			 var escaped_newurl = escape(window.location.href.replace(/http\:\/\/[^\/]*/, "")).replace(/\//g, "%2F");
//
//			 jQuery("#frmMain").get(0).action = jQuery("#frmMain").get(0).action.replace(/#.*/, "") + "#" + hash;
//
//			 jQuery("a[class!='tab-bt']", tabs[id]).each(function () {
//				 var this_ret_url = ff.history.gup("ret_url", this.href);
//				 if (this_ret_url.length > 0) {
//					 if (this_ret_url != ori_ret_url)
//						 this.href = this.href.replace(/ret_url=[^&]*/, "ret_url=" + escaped_newurl);
//					 this.href = this.href.replace(/#.*/, "") + "#" + hash;
//				 }
//			 });
//
//			 if(jQuery("#tabs_" + id).closest(".ui-dialog-content").attr("id") !== undefined) {
//				 ff.ffPage.dialog.refresh(jQuery("#tabs_" + id).closest(".ui-dialog-content").attr("id").replace("ffWidget_dialog_container_", ""), true, false);
//			 }
//		}
	});
//	var tab = ff.history.gup("tabs" + id);
//	var ori_ret_url = ff.history.gup("ret_url");
//
//	if (tab.length) {
//		tabs[id].tabs('select', parseFloat(tab));
//	}
//
//	if (window.location.href.indexOf('#') >= 0) {
//		var escaped_newurl = escape(window.location.href.replace(/http\:\/\/[^\/]*/, "")).replace(/\//g, "%2F");
//		var hash = window.location.href.replace(/^.*#/, "");
//
//		jQuery("a[class!='tab-bt']", tabs[id]).each(function () {
//			var this_ret_url = ff.history.gup("ret_url", this.href);
//			if (this_ret_url.length > 0) {
//				if (this_ret_url != ori_ret_url)
//					this.href = this.href.replace(/ret_url=[^&]*/, "ret_url=" + escaped_newurl);
//				this.href = this.href.replace(/#.*/, "") + "#" + hash;
//			}
//		});
//	}
	/* set onlick event for buttons */
/*	jQuery("a[class='tab-bt']", tabs[id]).click(function(){
		return false;
	});*/
}

}; /* publics' end */

return that;

/* code's end. */
})();


/*	function tabs_form_url(param, value)
{
	form = jQuery("#frmMain").get(0);
	url = form.action;
	if (url == "")
		url = window.location.href;

	url_parts = url.split("?");
	if (url_parts.length == 1)
	{
		url += "?" + param + "=" + value + "&";
	}
	else
	{
		query_parts = url_parts[1].split("&");
		var found = false;
		var new_query = "";
		for (var i = 0; i < query_parts.length; i++)
		{
			if (query_parts[i].length)
			{
				param_parts = query_parts[i].split("=");
				if (param_parts[0] == param)
				{
					found = true;
					param_parts[1] = value;
				}
				new_query += param_parts[0] + "=" + param_parts[1] + "&";
			}
		}
		if (!found)
		{
			new_query += param + "=" + value + "&";
		}
		url = url_parts[0] + "?" + new_query;
	}
	form.action = url;
}
*/