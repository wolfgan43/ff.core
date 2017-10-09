jQuery(document).ready(function() { 
	ff.pluginLoad("jquery.fn.background.resize", "/themes/library/plugins/jquery.background.resize/jquery.background.resize.js", function(){
		jQuery("body").ezBgResize({
			img : "/themes/site/images/background.jpg",
			opacity : 1, 
			center : true
		});
	});
});