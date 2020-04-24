ff.ffField.uploadex.plugins.fancybox = (function () {
	
/* privates */
	
var that = { /* publics*/
__ff : true, /* used to recognize ff'objects*/

"showImage" : function (params, obj, idx) {
	var fancybox_data = [];
	
	if (params.multi) {
		for (var i = obj.getindex(idx); i < obj.length; i++) {
			var tmp_href = jQuery(".view-link", obj.indexget(i).el).attr("href");
			if (tmp_href.toLowerCase().substr(-4) !== ".pdf") {
				fancybox_data.push({
					"href" : tmp_href,
					"title" : obj.indexgetkey(i).name
				});
			}
		}

		for (var i = 0; i < obj.getindex(idx); i++) {
			var tmp_href = jQuery(".view-link", obj.indexget(i).el).attr("href");
			if (tmp_href.toLowerCase().substr(-4) !== ".pdf") {
				fancybox_data.push({
					"href" : tmp_href,
					"title" : obj.indexgetkey(i).name
				});
			}
		}
	} else {
		var tmp_href = jQuery(".view-link", obj).attr("href");
		if (tmp_href.toLowerCase().substr(-4) !== ".pdf") {
			fancybox_data.push({
				"href" : tmp_href,
				"title" : name
			});
		}
	}
	
	jQuery.fancybox(fancybox_data);
	
	return false;
},

};
	
return that;
	
})();