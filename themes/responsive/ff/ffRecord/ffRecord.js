/**
 * Forms Framework Javascript Handling Object
 *	ffRecord' namespace
 */

ff.ffRecord = (function () {
//privates

var that = { // publics
__ff : "ff.ffRecord", // used to recognize ff'objects
"init" : function(component) {
	jQuery("#" + component + " .ffCheckDep").click();
	ff.pluginLoad("ff.ajax", "/themes/library/ff/ajax.js", function() {
		ff.ajax.addEvent({
			"event_name"	: "onEmptyQueue",
			"func_name"		: function (data) {
				jQuery("#" + component + " .ffCheckDep").click();
			}
		});
	});
}
, "displayFieldSetElem" : function (legend, control) {
	var checkControl = false;
	if(jQuery("#" + control).is("INPUT[type=checkbox]")) {
		checkControl = jQuery("#" + control).is(":checked");
	} else {
		checkControl = jQuery("#" + control).val();
	}

	if(checkControl) {
		jQuery(legend).parent().children("*:not(legend)").css({
			"opacity": ""
			, "pointer-events" : ""
		});
	} else {
		jQuery(legend).parent().children("*:not(legend)").css({
			"opacity": "0.5"
			, "pointer-events" : "none"
		});
	}
}
, "cursor" : {
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
			"event_params"	: [component, key, field, pComp.id + "_" + field.id]
		});
	});
}

}; // publics' end

    /* Init obj */
    function constructor() { // NB: called below publics
        ff.initExt(that);
        ff.addEvent({"event_name" : "onClearComponent", "func_name" : that.onClearComponent});

        jQuery(document).on("click.activebuttons", ".activebuttons", function() {
            if(jQuery(this).is("a")) {

                jQuery(this).attr("data-class", jQuery(this).attr("class"));
                jQuery(this).attr("class", jQuery(this).attr("class").substring(0, jQuery(this).attr("class").indexOf("activebuttons") - 1));
                jQuery(this).addClass("activatedbuttons");
                jQuery(this).prepend('<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-eclipse"><path ng-attr-d="{{config.pathCmd}}" ng-attr-fill="{{config.color}}" stroke="none" d="M10 50A40 40 0 0 0 90 50A40 42 0 0 1 10 50" fill="#cacaca" transform="rotate(269.759 50 51)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 51;360 50 51" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></path></svg>');
            }
            jQuery(this).css({"opacity": "0.6", "pointer-events": "none"});

            if(!jQuery(this).is("a"))
                jQuery(this).attr("disabled", "disabled");
        });
    }

    if(document.readyState == "complete") {
    	if(!ff.ffRecord) {
            constructor(); //va in contrasto con libLoaded
        }
    } else {
        window.addEventListener('load', function () {
            constructor();
        });
    }

return that;

// code's end.
})();
