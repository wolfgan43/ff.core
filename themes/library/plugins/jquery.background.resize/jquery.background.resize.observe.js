jQuery(document).ready(function() { 
	ff.load("jquery.plugins.background-resize", function(){
		jQuery("body").ezBgResize({
			img : "/themes/site/images/background.jpg",
			opacity : 1, 
			center : true
		});
	});
});