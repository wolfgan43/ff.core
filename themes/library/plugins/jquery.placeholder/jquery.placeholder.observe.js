jQuery(document).ready(function() {
    ff.pluginLoad("jquery.placeholder", "/themes/library/plugins/jquery.placeholder/jquery.placeholder.js", function() {
        jQuery("input:text, textarea").each(function() {
        	if(jQuery(this).attr("title").length > 0) {
        		jQuery(this).attr("placeholder", jQuery(this).attr("title"));
        		jQuery(this).attr("title", "");
			}
		});
        jQuery("input[placeholder], textarea[placeholder]").placeholder();
    });
});