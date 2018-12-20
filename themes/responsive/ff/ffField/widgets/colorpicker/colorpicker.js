ff.ffField.colorpicker = (function () {
	var that = { /* publics*/
		__ff : "ff.ffField.colorpicker", /* used to recognize ff'objects*/
		"change" : function (source, target) {
			var value = jQuery(source).val();
			if (!value.match(/[0-9abcdef]/))
		        value = value.replace(/[^0-9abcdef]/gi, '');

			jQuery(source).val(value);

			if(jQuery(source).is("[type=color]"))
				value = value.trim("#");
			else 
				value = "#" + value;
				
			if(value.trim("#").length == 6)
				jQuery("#" + target).val(value);
		}
		, "checkHex" : function() {
			var numcheck = /[a-z0-9_-]/;
			return numcheck.test(window.event.keyCode);
		}
	};

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

})();