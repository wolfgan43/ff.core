ff.ffField.checkgroup = (function () {
	var separator		= [];
	var length			= [];


	var that = { // publics
		"recalc" : function(control) {
			var hidden = document.getElementById(control);
			var tmp = "";
			var element = null;
				
			for (i = 0; i < length[control]; i++)
			{
			
				element = document.getElementById(control + "_" + i);
				if (element.checked)
				{
					if (tmp.length)
						tmp = tmp + separator[control];
					tmp = tmp + element.value;
				}
			}
			hidden.value = tmp;
		},
		"addValue" : function (params) {
			separator[params.id]			= params.separator;
			length[params.id]				= params.length;
		}
	};

	return that;
	
})();