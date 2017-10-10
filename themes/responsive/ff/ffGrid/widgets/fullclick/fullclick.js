(function(jQuery) {
	jQuery.extend({
		jTableFullClick: new function() {
			/* public methods */
			this.construct = function() {
				return this.each(function() {
					var $table = jQuery("TABLE", this);

					jQuery("TR.clickable .ffField:not(.clickable)", $table).bind("click.ff.ffGrid.fullclick", function(e) {
						var target = e.target || e.srcElement;
						if ($table.data("isDragging") || jQuery(target).is("a") || jQuery(target).closest("a").length) 
							return;

						if(jQuery(this).hasClass("custom"))
							eval(jQuery(this).closest("TR.clickable").data("url"));
						else if(jQuery(this).closest("TR.clickable").hasClass("ajax"))
							ff.ffGrid.dialogOpen(jQuery(this).closest("DIV.ffGrid").data("record"), jQuery(this).closest("TR.clickable").data("url"), undefined, jQuery(this).closest("TR.clickable"));
						else
							window.location.href = jQuery(this).closest("TR.clickable").data("url");
					}).css("cursor", "pointer");
					
					jQuery("TR .ffField.clickable", $table).bind("click.ff.ffGrid.fullclick", function(e) {
						var target = e.target || e.srcElement;
						if ($table.data("isDragging") || jQuery(target).is("a") || jQuery(target).closest("a").length) 
							return;

						if(jQuery(this).hasClass("custom"))
							eval(jQuery(this).data("url"));
						else if(jQuery(this).hasClass("ajax"))
							ff.ffGrid.dialogOpen(jQuery(this).closest("DIV.ffGrid").data("record"), jQuery(this).data("url"), undefined, jQuery(this));
						else
							window.location.href = jQuery(this).data("url");
					}).css("cursor", "pointer");					
				});
			}
		}
	});

	jQuery.fn.extend({
		jTableFullClick: jQuery.jTableFullClick.construct
	});
})(jQuery);
