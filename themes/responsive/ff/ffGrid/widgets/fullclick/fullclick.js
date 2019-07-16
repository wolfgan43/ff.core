(function(jQuery) {
    jQuery.extend({
        jTableFullClick: new function() {
            /* public methods */
            this.construct = function() {
                return this.each(function() {
                    var $table = jQuery("TABLE", this);

                    jQuery("TR.clickable .ffField:not(.clickable)", $table).bind("click.ff.ffGrid.fullclick", function(e) {
                        var target = e.target || e.srcElement;
                        if ($table.data("isDragging")
                            || jQuery(target).is("a")
                            || jQuery(target).closest("a").length
                            || jQuery(target).is("input")
                            || jQuery(target).is("select")
                            || jQuery(target).is("textarea")
                            || jQuery(target).is("button")
                            || jQuery(target).is("[onclick]")
                            || jQuery(target).parent().is("[onclick]")
                        )
                            return;

                        if(jQuery(this).hasClass("custom"))
                            eval(jQuery(this).closest("TR.clickable").data("url"));
                        else if(jQuery(this).closest("TR.clickable").hasClass("ajax"))
                            ff.ffGrid.dialogOpen(jQuery(this).closest("DIV.ffGrid").data("record"), jQuery(this).closest("TR.clickable").data("url"), undefined, jQuery(this).closest("TR.clickable"));
                        else if(jQuery(this).closest("TR.clickable").data("target"))
                            window.open(jQuery(this).closest("TR.clickable").data("url"));
                        else
                            window.location.href = jQuery(this).closest("TR.clickable").data("url");
                    }).css("cursor", "pointer");

                    jQuery("TR .ffField.clickable", $table).bind("click.ff.ffGrid.fullclick", function(e) {
                        var target = e.target || e.srcElement;
                        if ($table.data("isDragging")
                            || jQuery(target).is("a")
                            || jQuery(target).closest("a").length
                            || jQuery(target).is("input")
                            || jQuery(target).is("select")
                            || jQuery(target).is("textarea")
                            || jQuery(target).is("button")
                            || jQuery(target).is("[onclick]")
                            || jQuery(target).parent().is("[onclick]")
                        )
                            return;

                        if(jQuery(this).hasClass("custom"))
                            eval(jQuery(this).data("url"));
                        else if(jQuery(this).hasClass("ajax"))
                            ff.ffGrid.dialogOpen(jQuery(this).closest("DIV.ffGrid").data("record"), jQuery(this).data("url"), undefined, jQuery(this));
                        else if(jQuery(this).data("target"))
                            window.open(jQuery(this).data("url"));
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
