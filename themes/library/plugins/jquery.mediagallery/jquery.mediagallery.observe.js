jQuery(document).ready(function(){
	ff.injectCSS("mediagallery", "/themes/library/plugins/jquery.mediagallery/mediagallery-normal.css");
	ff.pluginLoad("jquery-ui", "/themes/library/jquery-ui/jquery-ui.js", function() {
		ff.pluginLoad("jquery.fn.mediaGallery", "/themes/library/plugins/jquery.mediagallery/jquery.mediagallery.js", function() {
			ff.pluginLoad("swfobject", "/themes/library/swfobject/swfobject.js", function() {
				ff.pluginLoad("jquery.fn.mousewheel", "/themes/library/plugins/jquery.mousewheel/jquery.mousewheel.js", function() {
		                	jQuery(".mediagallery").mediaGallery({autoplay:0}); 
				});
			});
	    });
	});
});
