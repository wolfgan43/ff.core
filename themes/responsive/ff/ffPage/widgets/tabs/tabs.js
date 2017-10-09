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
				tabs[id] = jQuery("#tab-" + id).tabs({
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