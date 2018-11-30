ff.ffField.listgroup = (function () {
	var separator		= [];

	var that = { /* publics*/
		__ff : "ff.ffField.listgroup", /* used to recognize ff'objects*/
		"addValue" : function (params) {
			separator[params.id]			= params.separator;
		},
	    "move" : function (fbox,tbox, repository) {
	        var oFbox = document.getElementById(fbox);
	        var oTbox = document.getElementById(tbox);
	        
	        var i = 0;                    
	        if(oFbox.value != "") {
	            var no = new Option();
	            no.value = oFbox.value;
	            no.text = oFbox.value;

	            oTbox.options[oTbox.options.length] = no;
	            oFbox.value = "";
	            
	            ff.ffField.listgroup.recalc(tbox, repository);
	        }                        
	    },                       
	    "removeMe" : function (control, repository) {
	        var oControl = document.getElementById(control);
	        
	        var boxLength = oControl.length;
	        var arrSelected = new Array();
	                                        
	        for (var i = 0; i < boxLength; i++) {
	            if (oControl.length > i && oControl.options[i].selected) {                            
	                oControl.options[i] = null;                            
	                i--;                                
	            }                            
	        }
	        ff.ffField.listgroup.recalc(control, repository);                    
	    },
		"recalc" : function (control, repository)
		{
	        var oControl = document.getElementById(control);
	        var oRepository = document.getElementById(repository);

	        var i = 0;
	        var tmp = "";
	                            
	        if (jQuery(oControl)) {
	            while(i < oControl.options.length) {                        
	                if (tmp.length)
	                    tmp = tmp + separator[repository];
	                tmp = tmp + oControl.options[i].value;
	                i+=1;
	            }
	            oRepository.value = tmp
	        }            
		}
	};

    window.addEventListener('load', function () {
        ff.initExt(that);
    });

	return that;
	
})();