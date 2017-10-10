(function(jQuery) {
	jQuery.extend({
		jTableFullClick: new function() {
			/* public methods */
			this.construct = function() {
				return this.each(function() {
					var $table = jQuery(".ffGrid", this);
					
					jQuery("td:has(a[target!=_blank]:not(.nofullclick))", $table).bind("click.ff.ffGrid.fullclick", function(e) {
						if ($table.data("isDragging")) {
							$table.data("isDragging", false);
							return;
						}

						var links = jQuery("a", this);
						if (links[0]) {
							if (e.target.tagName !== "TD")
								return;
							
							if (links[0].onclick)
								links[0].onclick();

							if (links[0].href.indexOf("javascript:") === 0)
								eval(decodeURIComponent(links[0].href));
							else
								window.location = links[0].href;
							return;
						}
					}).css("cursor", "pointer");
					
					jQuery("th:has(a)", $table).unbind(".ff.ffGrid.fullclick");
					jQuery("th:has(a)", $table).bind("click.ff.ffGrid.fullclick", function(e) {
						if ($table.data("isDragging")) {
							$table.data("isDragging", false);
							return;
						}
						
						if (e.target.tagName !== "TH")
							return;

						var links = jQuery("a", this);
						if (links.length) {
							if (links[0].onclick)
								links[0].onclick();
							
							if (links[0].href.indexOf("javascript:") === 0)
								eval(decodeURIComponent(links[0].href));
							else
								window.location = links[0].href;
							return;
						}
					}).css("cursor", "pointer").addClass("clickable");
					
				});
			}
		}
	});

	jQuery.fn.extend({
		jTableFullClick: jQuery.jTableFullClick.construct
	});
	
})(jQuery);

