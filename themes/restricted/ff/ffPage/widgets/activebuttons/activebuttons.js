ff.ffPage.activebuttons = (function () {

var that = { /* publics */
	__ff : true, /* used to recognize ff'objects */
	"init" : function () {
		jQuery('.button:not(.noactivebuttons, .activebuttons)').bind("click.activebuttons", function() {
			/*jQuery('.button').unbind(".activebuttons");*/

			jQuery(this).val(jQuery(this).val() + "...");
            jQuery(this).css("opacity", "0.6"); 
            jQuery(this).attr("disabled", "disabled");
			/*jQuery('.button').bind("click", function() {return false});*/
		}).addClass("activebuttons");
		/*jQuery(".button").button();
		jQuery(".detailSubmit").button();
		jQuery(".addNew").button();*/
	}
}; /* publics' end */

return that;

/* code's end. */
})();