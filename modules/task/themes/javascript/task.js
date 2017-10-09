
jQuery(function() {
    if(window.location.hash) {
        if(jQuery(window.location.hash).length) {
            jQuery(".task-container .task-detail").hide();
            jQuery(window.location.hash).fadeIn();
        } else {
            $.ajax({
              url: ff.site_path + "/services/task/" + window.location.hash.substring(1),
              context: jQuery(".task-container")
            }).done(function(data) { 
                $( this ).append( data );
                jQuery(".task-container .task-detail").hide();
                jQuery(window.location.hash).fadeIn();
            });
            
        }
    }
});