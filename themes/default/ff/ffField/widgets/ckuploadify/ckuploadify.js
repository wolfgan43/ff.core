ff.ffField.ckuploadify = (function () {
	var basePathUF = ff.site_path + "/themes/library/plugins/jquery.uploadify";
	var baseUrlUF = "/uploads";
	var previewJs = true;
	var previewPath = ff.site_path + "/cm/showfiles";
	var component = "";

	var basePathCK = "/ckfinder/";
	var baseUrlCK = "";
	

	var that = { // publics
		"init" : function (params) {
			this.component			= params.component;
			this.suffix				= params.suffix;
			this.basePathUF			= params.basePathUF;
			this.baseUrlUF			= params.baseUrlUF;
			this.previewJs			= params.previewJs;
			this.sizeLimit			= params.sizeLimit;
			this.previewPath		= ff.site_path + "/cm/showfiles";

			this.basePathCK			= params.basePathCK;
			this.baseUrlCK			= params.baseUrlCK;
			

			jQuery("#" + this.component + this.suffix).parent().find(".preview").attr("id", "uploadify_" + this.component);
			jQuery("#" + this.component + this.suffix).parent().find(".preview").addClass("uploadifyQueueItem");
			jQuery("#" + this.component + this.suffix).parent().find(".preview").find(".cancel a img").attr('src', (this.basePathUF + "/cancel.png"));
			
			
			if(jQuery("#uploadify_" + this.component).attr("id") === undefined) {
				jQuery("#" + this.component + this.suffix).parent().append('<div class="uploadifyQueueItem" id="uploadify_' + this.component + '"></div>');			
				jQuery("#uploadify_" + this.component).hide();
			}
			
			
			var component = this.component;
			var previewJs = this.previewJs;
			var previewPath = this.previewPath;
			var basePathUF = this.basePathUF;
			
		    jQuery("#" + this.component + this.suffix).uploadify({
		        'uploader'       : this.basePathUF + '/uploadify.swf',
		        'script'         : this.basePathUF + '/uploadify.php',
		        'cancelImg'      : this.basePathUF + '/cancel.png',
		        'folder'         : this.baseUrlUF,
		        'buttonImg'      : this.basePathUF + '/browse.png',
		        'auto'           : true,
		        'multi'          : false, 
		        'sizeLimit'      : this.sizeLimit,
		        onSelect		 : function() {
		        	ff.ffField.ckuploadify.delete(component);
				},
		        onComplete		 : function(event, queueID, fileObj, response, data) {
		        	var previewBlock = '';

					if(response) {		        	
					if(previewJs)
						previewBlock = '<a href="' + previewPath + fileObj.filePath.replace(fileObj.name, '') + response + '" target="_blank"><img src="' + previewPath + '/thumb' + fileObj.filePath.replace(fileObj.name, '') + response + '" /></a>';
			
					jQuery("#uploadify_" + component).html(previewBlock + '<span class="fileName">' + response + ' (' + fileObj.size + ')' + '</span><div class="cancel"><a href="javascript:ff.ffField.ckuploadify.delete(\'' + component + '\');"><img src="' + basePathUF + '/cancel.png" /></a></div>');
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
		"delete" : function(component) {
			jQuery.post(this.basePathUF + '/uploadify.php', 'delaction=' + jQuery("#" + component).val(), function(data) {
			
			}, "json");

			jQuery("#" + component).val("");
			jQuery("#" + component + "_tmpname").val("");
			jQuery("#uploadify_" + component).hide();
			return ;
		},
		"browseServer" : function ( startupPath, functionData ) {
			finder = new CKFinder();
			// You can use the "CKFinder" class to render CKFinder in a page:
			
			
			// The path for the installation of CKFinder (default = "/ckfinder/").
			finder.BasePath = this.basePathCK ;
			
			//Startup path in a form: "Type:/path/to/directory/"
			finder.StartupPath = startupPath;
			finder.StartupFolderExpanded = true;
			
			// Name of a function which is called when a file is selected in CKFinder.
			finder.SelectFunction = ckFinderSetFileField ;
			
			// Additional data to be passed to the SelectFunction in a second argument.
			// We'll use this feature to pass the Id of a field that will be updated.
			finder.SelectFunctionData = functionData ;
			
			
			// Name of a function which is called when a thumbnail is selected in CKFinder.
			finder.SelectThumbnailFunction = ckFinderShowThumbnails ;

			// Launch CKFinder
			finder.Popup() ;
		}, 
		"setFileField" : function ( fileUrl, data ) {
			document.getElementById(data["selectFunctionData"] ).value = fileUrl.replace(this.baseUrlCK, "") ;
			if(this.previewJs) {
				var sFileName = decodeURIComponent( fileUrl.replace( /^.*[\/\\]/g, '' ) ) ;

				parentA = document.getElementById(data["selectFunctionData"] + "_img").parentNode;
				if(parentA.rel) {
					document.getElementById(data["selectFunctionData"] + "_img").parentNode.href = parentA.rel.substring(0, parentA.rel.lastIndexOf("/")) + fileUrl.replace(this.baseUrlCK, "");
					document.getElementById(data["selectFunctionData"] + "_img").src = parentA.rel + fileUrl.replace(this.baseUrlCK, "");
					document.getElementById(data["selectFunctionData"] + "_img").title = sFileName + '(' + data["fileSize"] + 'KB)';
				} else {
					document.getElementById(data["selectFunctionData"] + "_img").parentNode.href = "#";
					document.getElementById(data["selectFunctionData"] + "_img").src = "#";
					document.getElementById(data["selectFunctionData"] + "_img").title = "";
				}
			}
		}, 
		// This is a sample function which is called when a thumbnail is selected in CKFinder.
		"showThumbnails": function ( fileUrl, data ) {
			var sFileName = decodeURIComponent( fileUrl.replace( /^.*[\/\\]/g, '' ) ) ;
			document.getElementById('preview').innerHTML = 
					'<div class="thumb">' +
						'<img src="' + fileUrl + '" />' +
						'<div class="caption">' +
							'<a href="' + data["fileUrl"] + '" target="_blank">' + sFileName + '</a> (' + data["fileSize"] + 'KB)' +
						'</div>' +
					'</div>' ;

			document.getElementById( 'preview' ).style.display = "";
			// It is not required to return any value.
			// When false is returned, CKFinder will not close automatically.
			return false;
		}
	};
	return that;
	
})();

function ckFinderSetFileField( fileUrl, data ) {
	return ff.ffField.ckfinder.setFileField(fileUrl, data);	
}

		
function ckFinderShowThumbnails( fileUrl, data ) {
	return ff.ffField.ckfinder.showThumbnails(fileUrl, data);	
}
