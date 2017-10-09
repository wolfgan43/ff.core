jQuery.fn.selectText = function(){
	var doc = document, element = this[0], range, selection;
	if (doc.body.createTextRange) {
		range = document.body.createTextRange();
		range.moveToElementText(element);
		range.select();
	} else if (window.getSelection) {
		selection = window.getSelection();        
		range = document.createRange();
		range.selectNodeContents(element);
		selection.removeAllRanges();
		selection.addRange(range);
	}
};

ff.modules.restricted = (function () {
  	var tme, tml;
  	var dragSelector = ".-draggable";
  	var dropSelector = ".-droppable";
  	var menuSelector = ".-rightview .sidemenu";
  	

	var that = { // publics
		__ff : true, // used to recognize ff'objects
		"init" : function(params) {
			if(params) {
				if(params["menu"]) menuSelector = params["menu"];
				if(params["drag"]) dragSelector = params["drag"];
				if(params["drop"]) dropSelector = params["drop"];
			}
			this.menu();
		},
		"drag" : function(callback) {
			jQuery(dragSelector).mousedown(function() {
				jQuery(this).selectText();
			}); 
			jQuery(dragSelector).on("dragstart", function(e) {
			  /*  var img = document.createElement("img");
			    img.src = "http://kryogenix.org/images/hackergotchi-simpler.png";
			    e.originalEvent.dataTransfer.setDragImage(img, 0, 0);*/
			    
			    jQuery(dropSelector).addClass("-dragging");
			    
			    if(jQuery(this).attr("rel"))
			   		e.originalEvent.dataTransfer.setData("Text", jQuery(this).attr("rel"));
				
				if(callback)
					callback("ondragstart", e);
			});		
			jQuery(dragSelector).on("dragend", function(e) {
				jQuery(dropSelector).removeClass("-dragging");
				jQuery(dropSelector).removeClass("-dragover");
				
				if(callback)
					callback("ondragend", e);
			});
			
			jQuery(dropSelector).on("dragover", function(e) {
				e.originalEvent.preventDefault();
				var dropZone = (jQuery(e.originalEvent.target).hasClass(dropSelector)
					? jQuery(e.originalEvent.target)
					: jQuery(e.originalEvent.target).closest(dropSelector)
				);
				if(!dropZone.hasClass("-dragover")) {
					dropZone.addClass("-dragover");
					if(callback)
						callback("ondragover", e, dropZone);
				}
			});		
			jQuery(dropSelector).on("dragleave", function(e) {
				var dropZone = (jQuery(e.originalEvent.target).hasClass(dropSelector)
					? jQuery(e.originalEvent.target)
					: jQuery(e.originalEvent.target).closest(dropSelector)
				);
				if(dropZone.hasClass("-dragover")) {
					dropZone.removeClass("-dragover");
				    if(callback)
						callback("ondragleave", e, dropZone);
				}
			});		
			jQuery(dropSelector).on("drop", function(e) {
				var dropZone = (jQuery(e.originalEvent.target).hasClass(dropSelector)
					? jQuery(e.originalEvent.target)
					: jQuery(e.originalEvent.target).closest(dropSelector)
				);

			    if(callback)
			    	callback("ondrop", e, dropZone);
			});					

						
			
		},
		"menu" : function () {
			jQuery(".-floating").each(function() {
				jQuery(this).appendTo(jQuery(this).closest("UL").parent()).fadeIn();
			});
		
			jQuery("a[data-toggle='collapse']").click(function() {
				var target = jQuery(this);
				if(jQuery(this).next().hasClass("nav-controls")) {
					 target = jQuery(this).next().find("a[data-toggle=collapse]");
				}

				if(target.attr("class")) {
					if(target.hasClass("collapsed")) {
						target.attr("class", target.attr("class").replace("right", "down"));
					} else {
						target.attr("class", target.attr("class").replace("down", "right"));
					}
				}
			});
		
			jQuery(document).on("mouseenter", menuSelector, function() {
				clearTimeout(tml);
				var elem = this
				tme = setTimeout(function() {
					jQuery(elem).css({
						"z-index" : "9999999"
						, "left": "0"
					});
				}, 300);
			});
			
			jQuery(document).on("mouseleave", menuSelector, function() {
				clearTimeout(tme);
				var elem = this
				tml = setTimeout(function() {
					jQuery(elem).css({
						"left": ""
						, "z-index" : ""	
					});
				}, 400);
			});
		}
	};
	
	jQuery(function() {
		that.init();
	});
	
	return that;
	
})();