ff.ffPage.disclosures = (function () {

var that = { /* publics */
__ff : "ff.ffPage.disclosures", /* used to recognize ff'objects */

"toggle" : function (element) {
	element = jQuery("#" + element).get(0);
	if (element.discl_state == "opened") {
		jQuery(element).addClass("discl_open");
		jQuery(element).removeClass("discl_close");
		jQuery(element).each(function(){this.discl_state = "closed";});
		jQuery("#" + element.discl_section).hide();
	} else {
		jQuery(element).removeClass("discl_open");
		jQuery(element).addClass("discl_close");
		jQuery(element).each(function(){this.discl_state = "opened";});
		jQuery("#" + element.discl_section).show();
	}
},

"init" : function (element, section, state) {
	if (state == "opened") {
		jQuery("#" + element).removeClass("discl_open");
		jQuery("#" + element).addClass("discl_close");
		jQuery("#" + element).each(function(){this.discl_state = "opened"; this.discl_section = section;});
		jQuery("#" + section).show();
	} else {
		jQuery("#" + element).addClass("discl_open");
		jQuery("#" + element).removeClass("discl_close");
		jQuery("#" + element).each(function(){this.discl_state = "closed"; this.discl_section = section;});
		jQuery("#" + section).hide();
	}
}

}; /* publics' end */

    /* Init obj */
    function constructor() { // NB: called below publics
        ff.initExt(that);
    }

    if(document.readyState == "complete") {
        //  constructor(); //va in contrasto con libLoaded
    } else {
        window.addEventListener('load', function () {
            constructor();
        });
    }

return that;

/* code's end. */
})();
