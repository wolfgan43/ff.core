jQuery(document).ready(function() {
    ff.load("jquery.plugins.placeholder", function() {
        jQuery("input:text, textarea").each(function() {
        	if(jQuery(this).attr("title").length > 0) {
        		jQuery(this).attr("placeholder", jQuery(this).attr("title"));
        		jQuery(this).attr("title", "");
			}
		});
        jQuery("input[placeholder], textarea[placeholder]").placeholder();
    });
});