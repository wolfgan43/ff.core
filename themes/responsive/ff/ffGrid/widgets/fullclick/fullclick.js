window.addEventListener('load', function () {
    (function (jQuery) {
        jQuery.extend({
            jTableFullClick: new function () {
                /* public methods */
                this.construct = function () {
                    return this.each(function () {
                        jQuery("TR.clickable").css("cursor", "pointer");

                        jQuery(document).on("click.ff.ffGrid.fullclick", "TR.clickable > TD:not(.clickable)", function (e) {
                            var $table = jQuery(this).closest("TABLE");
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
                                || jQuery(this).hasClass("disableClick")
                            )
                                return;

                            if (jQuery(this).hasClass("custom")) {
                                eval(jQuery(this).closest("TR.clickable").data("url"));
                            } else if (jQuery(this).closest("TR.clickable").hasClass("ajax")) {
                                jQuery(this).closest("TR.clickable").addClass("ff-modal-highlight");
                                ff.ffGrid.dialogOpen(jQuery(this).closest(".ffGrid").data("record"), jQuery(this).closest("TR.clickable").data("url"), undefined, jQuery(this).closest("TR.clickable"));
                            } else if (jQuery(this).closest("TR.clickable").data("target")) {
                                window.open(ff.ffPage.goToWithRetUrl(jQuery(this).closest("TR.clickable").data("url"), true));
                            } else {
                                ff.ffPage.goToWithRetUrl(jQuery(this).closest("TR.clickable").data("url"));
                            }
                        });

                        jQuery(document).on("click.ff.ffGrid.fullclick", "TR > TD.clickable", function (e) {
                            var $table = jQuery(this).closest("TABLE");
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
                                || jQuery(this).hasClass("disableClick")
                            )
                                return;

                            if (jQuery(this).hasClass("custom")) {
                                eval(jQuery(this).data("url"));
                            } else if (jQuery(this).hasClass("ajax")) {
                                jQuery(this).closest("TR.clickable").addClass("ff-modal-highlight");
                                ff.ffGrid.dialogOpen(jQuery(this).closest(".ffGrid").data("record"), jQuery(this).data("url"), undefined, jQuery(this));
                            } else if (jQuery(this).data("target")) {
                                window.open(ff.ffPage.goToWithRetUrl(jQuery(this).data("url"), true));
                            } else {
                                ff.ffPage.goToWithRetUrl(jQuery(this).closest("TR.clickable").data("url"));
                            }
                        }).css("cursor", "pointer");
                    });
                }
            }
        });

        jQuery.fn.extend({
            jTableFullClick: jQuery.jTableFullClick.construct
        });
    })(jQuery);

    ff.initExt("ff.ffGrid.fullclick");
});
