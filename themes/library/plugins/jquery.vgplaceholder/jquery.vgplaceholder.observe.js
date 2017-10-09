jQuery(document).ready(function() {
	jQuery("input:text, input:password, textarea").each(function() {
		var label = jQuery(this).prev("label").text();
		
		if(label !== undefined) {
			label= label.replace(/^\s+|\s+$/g,"");
			if(label.length > 0) {
				if(jQuery(this).attr("type") == "password") {
					jQuery(this).attr("placeholder", ("********"));
					//jQuery(this).val("********");
				} else {
					jQuery(this).attr("placeholder", label);
					//jQuery(this).val(label);
				}
			}			
			jQuery(this).prev("label").hide();
		}
	});
	
	jQuery("input[placeholder], textarea[placeholder]").focus(function(){ 
		//if(jQuery(this).val() == jQuery(this).attr("placeholder")) {
  		//	jQuery(this).val('');
  			//jQuery(this).prev("label").slideDown();
		//}
	});

	jQuery("input[placeholder], textarea[placeholder]").blur(function() {
		//if(jQuery(this).val() == '') {
  		//	jQuery(this).val(jQuery(this).attr("placeholder"));
  			//jQuery(this).prev("label").slideUp(); 
		//} 
	}); 
});