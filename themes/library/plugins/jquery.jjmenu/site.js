function setTab(tab) {

    jQuery("a.h3").parent().hide();
    jQuery("a[name='"+tab+"']").parent().show();
}



jQuery(document).ready(function() {

    var active = jQuery("ul.menu li.active a").attr("href").replace("#","");
    setTab(active);
    
    jQuery("ul.menu li").click( function() {
        jQuery("ul.menu li").removeClass("active");
        jQuery(this).addClass("active");
        jQuery(this).children("a").blur();
        var tab = jQuery(this).children("a").attr("href").replace("#","");
        setTab(tab);
                
        return false;
        
    });

jQuery("#show_sources").click ( function () { jQuery('textarea').fadeIn(); } );

});