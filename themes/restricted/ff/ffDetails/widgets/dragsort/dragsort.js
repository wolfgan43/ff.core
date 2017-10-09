ff.ffDetails.dragsort = (function () {

var that = { /* publics */
__ff : true, /* used to recognize ff'objects*/

"inst" : ff.hash(),

"init" : function (params) {
	that.inst.set(params.id, params);
	jQuery("#" + params.id).jTableOrder(params.id);
	jQuery("#" + params.id + " table").addClass("draggable");
	/*jQuery("#" + params.id + " table tbody tr").each(function() {
	});*/
},

"reorder" : function (id) {
	var params = that.inst.get(id);
	var $table = jQuery(".ffDetails", jQuery("#" + id));

	var toBeSent = [];
	var position = 1;
	if(params.service_path) 
	{
		$table.find("tbody tr").each(function () {
			toBeSent.push({"name" : "positions[]", "value" : jQuery(this).data("sort_id")});
			position += 1;
		});

		toBeSent.push({name: "resource", value: params.resource_id});
		
		ff.load("ff.ajax", function() {
			ff.ajax.blockUI();
			jQuery.ajax({
				  "url"		: params.service_path
				, "async"	: true
				, "type"	: "POST"
				, "data"	: toBeSent
				, "success" : function (data) {ff.ajax.unblockUI();}
				, "mydata"	: id
			});
		});
	}
}

}; /* publics' end*/

return that;

/* code's end.*/
})();
