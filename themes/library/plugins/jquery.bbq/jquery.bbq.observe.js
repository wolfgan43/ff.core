jQuery(function(){
  
  // Enable tabs on all tab widgets. If you define a callback for the 'select'
  // event, it will be executed for the selected tab whenever the hash changes.
  
  // Define our own click handler for the tabs, overriding the default.
  if(window.location.href.indexOf("#") >= 0) {
    var urlPre = window.location.href;
    
    urlPre = urlPre.replace("http://" + ff.domain, "");
    urlPre = urlPre.substring(0, urlPre.indexOf("#"));

    jQuery("a").each(function() {
        actualUrl = jQuery(this).attr("href");
        replaceUrl = window.location.href;
        replaceUrl = replaceUrl.replace("http://" + ff.domain, "");

        jQuery(this).attr("href",
            actualUrl.replace("ret_url=" + encodeURIComponent(urlPre)
                , "ret_url=" + encodeURIComponent(replaceUrl) 
            )
        );
        

    });
  
  }

  jQuery('a').click(function() { 
    if(jQuery(this).attr("href").substring(0,1) == "#") {
        var urlFrag = jQuery(this).attr("href");
        var urlPre = window.location.href;
        
        urlPre = urlPre.replace("http://" + ff.domain, "");
        
        jQuery.bbq.pushState(jQuery(this).attr("href"));
        

        jQuery("a").each(function() {
            actualUrl = jQuery(this).attr("href");
            replaceUrl = window.location.href;
            replaceUrl = replaceUrl.replace("http://" + ff.domain, "");

            jQuery(this).attr("href",
                actualUrl.replace("ret_url=" + encodeURIComponent(urlPre)
                    , "ret_url=" + encodeURIComponent(replaceUrl) 
                )
            );
            
        
        });
    }
  });
});