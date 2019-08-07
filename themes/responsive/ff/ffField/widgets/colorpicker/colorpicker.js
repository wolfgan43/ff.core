ff.ffField.colorpicker = (function () {
	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
		"change" : function (source, target) {
			console.log(jQuery(this).id);
			var value = jQuery(source).val();
			if (!value.match(/[0-9abcdef]/))
		        value = value.replace(/[^0-9abcdef]/gi, '');

			jQuery(source).val(value);

			if(jQuery(source).is("[type=color]"))
				value = value.trim("#");
			else 
				value = "#" + value;
				
			if(value.trim("#").length == 6) {
                jQuery("input[id*=_"+target+"]").val(value);
                jQuery("input[id*="+target+"_]").val("#"+value);
            }
		}
		, "checkHex" : function() {
			numcheck = /[a-z0-9_-]/;
			return numcheck.test(window.event.keyCode);
		}
	};

	return that;

})();