ff.cms.fn.uploadifyDialogInit = function() {
	jQuery(".uploadify").uploadify({
		'uploader'       : ff.site_path + '/themes/library/plugins/jquery.uploadify/uploadify.swf',
		'script'         : ff.site_path + '/themes/library/plugins/jquery.uploadify/uploadify.php',
		'cancelImg'      : ff.base_path + '/themes/library/plugins/jquery.uploadify/cancel.png',
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
				ff.load("ff.ajax", function() {
					ff.ajax.blockUI();
				});
			}
			top.location.reload(true);
		}
	});
};

ff.pluginAddInit("jquery.uploadify", function() {
    ff.cms.fn.uploadifyDialogInit(); 
});