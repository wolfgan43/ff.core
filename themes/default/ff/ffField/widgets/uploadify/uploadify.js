ff.ffField.uploadify = (function () {
	var basePath = ff.site_path + "/themes/library/plugins/jquery.uploadify";
	var baseUrl = "/uploads";
	var previewJs = true;
	var previewPath = ff.site_path + "/cm/showfiles";
	var component = "";

	var that = { // publics
		"init" : function (params) {
			this.component			= params.component;
			this.suffix				= params.suffix;
			this.basePath			= params.basePath;
			this.baseUrl			= params.baseUrl;
			this.previewJs			= params.previewJs;
			this.sizeLimit			= params.sizeLimit;
			this.previewPath		= ff.site_path + "/cm/showfiles";
			
			//jQuery("#" + this.component + this.suffix).parent().has(".preview").attr("id", "uploadify_" + this.component);
			
			//alert(jQuery("#uploadify_" + this.component));
			
			//if(jQuery("#uploadify_" + this.component) === undefined)
			jQuery("#" + this.component + this.suffix).parent().find(".preview").attr("id", "uploadify_" + this.component);
			jQuery("#" + this.component + this.suffix).parent().find(".preview").addClass("uploadifyQueueItem");
			jQuery("#" + this.component + this.suffix).parent().find(".preview").find(".cancel a img").attr('src', (this.basePath + "/cancel.png"));
			
			
			if(jQuery("#uploadify_" + this.component).attr("id") === undefined) {
				jQuery("#" + this.component + this.suffix).parent().append('<div class="uploadifyQueueItem" id="uploadify_' + this.component + '"></div>');			
				jQuery("#uploadify_" + this.component).hide();
			}
			
			
			var component = this.component;
			var previewJs = this.previewJs;
			var previewPath = this.previewPath;
			var basePath = this.basePath;
			
		    jQuery("#" + this.component + this.suffix).uploadify({
		        'uploader'       : this.basePath + '/uploadify.swf',
		        'script'         : this.basePath + '/uploadify.php',
		        'cancelImg'      : this.basePath + '/cancel.png',
		        'folder'         : this.baseUrl,
		        'buttonImg'      : this.basePath + '/browse.png',
		        'auto'           : true,
		        'multi'          : false, 
		        'sizeLimit'      : this.sizeLimit,
		        onSelect		 : function() {
		        	ff.ffField.uploadify.del(component);
				},
		        onComplete		 : function(event, queueID, fileObj, response, data) {
		        	var previewBlock = '';

					if(response) {		        	
					if(previewJs)
						previewBlock = '<a href="' + previewPath + fileObj.filePath.replace(fileObj.name, '') + response + '" target="_blank"><img src="' + previewPath + '/thumb' + fileObj.filePath.replace(fileObj.name, '') + response + '" /></a>';
			
					jQuery("#uploadify_" + component).html(previewBlock + '<span class="fileName">' + response + ' (' + fileObj.size + ')' + '</span><div class="cancel"><a href="javascript:ff.ffField.uploadify.del(\'' + component + '\');"><img src="' + basePath + '/cancel.png" /></a></div>');
					jQuery("#uploadify_" + component).show();
					
					jQuery("#" + component).val(fileObj.filePath.replace(fileObj.name, '') + response );
					jQuery("#" + component + "_tmpname").val( response );

		        	//alert(	event + "  " + queueID + "  " + fileObj + "  " + response + "  " + data);
					} else {
						return false;
					}
				}
		    });			
		},
		"del" : function(component) {
			jQuery.post(this.basePath + '/uploadify.php', 'delaction=' + jQuery("#" + component).val(), function(data) {
			
			}, "json");

			jQuery("#" + component).val("");
			jQuery("#" + component + "_tmpname").val("");
			jQuery("#uploadify_" + component).hide();
			return ;
		}
	};
	return that;
	
})();