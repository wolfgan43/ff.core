ff.cms.fn.uploadifyDialogInit = function() {
	jQuery(".uploadify").uploadify({
		'uploader'       : ff.site_path + '/themes/library/plugins/jquery.uploadify/uploadify.swf',
		'script'         : ff.site_path + '/themes/library/plugins/jquery.uploadify/uploadify.php',
		'cancelImg'      : ff.site_path + '/themes/library/plugins/jquery.uploadify/cancel.png',
		'folder'         : jQuery("#path_upload").attr("value"),
		'buttonText'	 : 'Sfoglia', 
		/*'buttonImg'      : ff.site_path + '/themes/library/plugins/jquery.uploadify/browse.png',*/
		'auto'           : false,
		'multi'          : true, 
		'sizeLimit'      : jQuery("#max_upload").attr("value"),
		'fileExt'		 : (jQuery("#file_ext").attr("value") == '' ? null : jQuery("#file_ext").attr("value")),
		'fileDesc'    	 : (jQuery("#file_ext").attr("value") == '' ? null : 'File: (' + jQuery("#file_ext").attr("value") + ')'), 
		'queueSizeLimit' : (jQuery("#file_limit").attr("value") == '' ? 999 : jQuery("#file_limit").attr("value")), 
		'onAllComplete' : function(event,data) {
			if(jQuery("#upload_ret_url").attr("value") != '') {
				ff.ffPage.dialog.close(jQuery("#upload_ret_url").attr("value"));
				ff.pluginLoad("ff.ajax", "/themes/library/ff/ajax.js", function() {
					ff.ajax.blockUI();
				});
			}
			top.location.reload(true);
		}
	});
};

ff.pluginLoad("swfobject", "/themes/library/swfobject/swfobject.js", function() {
    ff.pluginLoad("jquery.fn.uploadify", "/themes/library/plugins/jquery.uploadify/jquery.uploadify.js", function() {
        ff.cms.fn.uploadifyDialogInit(); 
    });
});