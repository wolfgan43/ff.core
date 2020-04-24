/**
 * Forms Framework Javascript Handling Object
 *	ffRecord' namespace
 */

ff.ffRecord = (function () {
//privates

var that = { // publics
__ff : true, // used to recognize ff'objects

"cursor" : {
	"reload" : function (ctx) {
		var dialog = ff.ajax.ctxGet(ctx);
		var params = ff.ffPage.dialog.param(ctx, "cursor");
		var row = parseInt(params.rrow);
		
		that.cursor.move(ctx, params["id"], dialog, row);
	},
	"first" : function (ctx) {
		var dialog = ff.ajax.ctxGet(ctx);
		var params = ff.ffPage.dialog.param(ctx, "cursor");
		
		that.cursor.move(ctx, params["id"], dialog, 0);
	},
	"last" : function (ctx) {
		var dialog = ff.ajax.ctxGet(ctx);
		var params = ff.ffPage.dialog.param(ctx, "cursor");
		var rows = parseInt(params["rows"]);
		
		that.cursor.move(ctx, params["id"], dialog, rows - 1);
	},
	"next" : function (ctx) {
		var dialog = ff.ajax.ctxGet(ctx);
		var params = ff.ffPage.dialog.param(ctx, "cursor");
		var rows = parseInt(params["rows"]);
		
		var row = parseInt(params.rrow) + 1;
		if (row >= rows) {
			row = 0;
		}
		
		that.cursor.move(ctx, params["id"], dialog, row);
	},
	"prev" : function (ctx) {
		var dialog = ff.ajax.ctxGet(ctx);
		var params = ff.ffPage.dialog.param(ctx, "cursor");
		var rows = parseInt(params["rows"]);
		
		var row = parseInt(params.rrow) - 1;
		if (row < 0) {
			row = rows - 1;
		}
		
		that.cursor.move(ctx, params["id"], dialog, row);
	},
	"move" : function (ctx, grid, dialog, row) {
		ff.ajax.doRequest({
			"component" : grid, 
			"addFields" : [
				{"name" : "XHR_FORMAT", "value" : "json"}, 
				{"name" : grid + "_rrow", "value" : row}
			],
			"callback" : function (data) {
				if (!parseInt(data["rows"]))
					dialog.close(ctx);
				
				var url = data["data"][0]["url"]["modify"];
				
				var cursor_params = ff.ffPage.dialog.param(ctx, "cursor");
				cursor_params["rrow"] = parseInt(data["rrow"]);
				cursor_params["rows"] = parseInt(data["rows"]);
				ff.ffPage.dialog.param(ctx, "cursor", cursor_params);
				
				dialog.goToUrl(url);
			}
		});
	}
}

, "onClearComponent" : function (component) {
	var pComp = ff.struct.get("comps").get(component);
	if (!pComp)
		return;

	if (pComp.type !== "ffRecord")
		return;
	
	// reset per ogni singolo campo
	pComp.fields.each( function (key, field) {
		ff.doEvent({
			"event_name"	: "onClearField",
			"event_params"	: [component, key, field, (pComp.id || component) + "_" + (field.id || key)]
		});
	});
}

}; // publics' end

ff.pluginAddInitLoad("ff.ffRecord", function () {
	ff.addEvent({"event_name" : "onClearComponent", "func_name" : that.onClearComponent});
});

return that;

// code's end.
})();
