ff.ffPage.activebuttons = (function () {

var that = { /* publics */
	__ff : true, /* used to recognize ff'objects */
	"init" : function (spinnerClass) {
		jQuery('.activebuttons').each(function() {
			// your button
	        var btn = $(this); 

	        // original click handler
	        var clickhandler = btn.attr("onclick");

	        btn.attr("onclick", null).attr("class", btn.attr("class").replace("activebuttons", "activatedbuttons")); 
            
	        // new click handler
	        btn.on("click.activebuttons", function() {
	            if(spinnerClass && jQuery(this).is("a")) {
	            	jQuery(this).attr("class", jQuery(this).attr("class").substring(0, jQuery(this).attr("class").indexOf("activatedbuttons") + 13));
	            	jQuery(this).prepend('<i class="' + spinnerClass + '"></i>'); 
				}
	            jQuery(this).css({"opacity": "0.6", "pointer-events": "none"});

	            if(!jQuery(this).is("a")) 
            		jQuery(this).attr("disabled", "disabled");

				if(clickhandler)
	                eval(clickhandler);
	        });
		});
	}
}; /* publics' end */

return that;

/* code's end. */
})();