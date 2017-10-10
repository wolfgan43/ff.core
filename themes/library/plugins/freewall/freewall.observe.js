ff.cms.fn.freewall = function() {
	var targetid = ".free-wall";
	if(targetid.length > 0)
		targetid = targetid + " ";
	
	ff.load("jquery.plugins.freewall", function() {
		jQuery(targetid + ' > DIV').each(function() {
			jQuery(this).addClass('brick');
		});
		
		var wall = {};
		ff.ajax.addEvent({
			"event_name"	: "onEmptyQueue",
			"func_name"		: function (data) {
				wall.fitWidth();
			}
		});
		ff.pluginAddInit("freewall", function() {
			wall = new freewall(targetid);
			wall.reset({
				selector: ".brick",
				animate: true,
				cellW: 150,
				cellH: "auto",
				onResize: function() {
					wall.fitWidth();
				}
			});

		});
	});
};