ff.ffPage.tabs = (function () {

/* privates */
var tabs = [];

var that = { /* publics */
	__ff : true, /* used to recognize ff'objects */
	"addTab" : function (id, type) {
		switch (type) { 
			case "base":
				break;
			case "bootstrap":
			case "bootstrap-fluid":
				ff.doEvent({
					"event_name" : "initIFElement"
					, "event_params" : [id, "tabs"]
				});
				$('a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
				 	that.doEvent({
						"event_name" : "onActivate"
						, "event_params" : [id, event, { "newPanel": jQuery(jQuery(this).attr("href"))}]
					});
				});
				break;
			case "foundation":
			case "foundation-fluid":
				//jQuery(document).foundation('tab', 'reflow');
				ff.doEvent({
					"event_name" : "initIFElement"
					, "event_params" : [id, "tabs"]
				});
				break;
			default:	
				ff.pluginAddInit("jquery-ui", function () {
					jQuery.widget("ui.tabs", jQuery.ui.tabs, {
						_getList: function() {
							if (this.options.tabselector !== undefined) {
								var list = jQuery("#tab-menu-" + id);
								return list.length ? list.eq( 0 ) : this._super();
							} else {
								return this._super();
							}
						}
					});
				});

				tabs[id] = jQuery("#tab-" + id).tabs({
					"tabselector" : "#tab-menu-" + id,
					"create" : function (event, ui) {
						that.doEvent({
							"event_name" : "onCreate"
							, "event_params" : [id, event, ui]
						});
						ff.doEvent({
							"event_name" : "initIFElement"
							, "event_params" : [id, "tabs"]
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
				 });	
		}
	}
}; /* publics' end */

return that;

/* code's end. */
})();