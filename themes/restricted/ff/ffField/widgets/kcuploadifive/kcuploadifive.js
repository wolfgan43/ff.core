ff.ffField.kcuploadifive = (function () {
	var basePathUF = "/themes/library/plugins/jquery.uploadifive";
	var baseUrlUF = "/";
	var previewJs = true;
	var viewUrl = "";
	var previewUrl = "";
	var previewPath = "/cm/showfiles.php";
	/*var relativePath = '';*/
	var idComponent = undefined;

	var basePathKC = "/themes/library/kcfinder";
	var baseUrlKC = "";
	
    var start = "";
    var target = "";
    var tmpname = "";
    var sufdel = "";
	var dataSrc = "";
	
	var aviary = null;
	
	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
		"init" : function (params) {
			this.component			= params.component;
            this.start              = params.start;
            this.target             = params.target;
            this.tmpname            = params.tmpname;
            this.sufdel            	= params.sufdel;
			this.basePathUF			= ff.site_path + basePathUF;
			this.previewJs			= params.previewJs;
			this.viewUrl			= params.viewUrl;
			this.previewUrl			= params.previewUrl;
            this.writable           = params.writable;
			this.sizeLimit			= params.sizeLimit;
			this.fileExt			= params.fileExt;
			this.model				= params.model;
			this.modelThumb			= params.modelThumb;
			this.showFile			= params.showFile;
            this.fullPath           = params.fullPath;
            this.dataSrc			= params.dataSrc;

            this.width           	= params.width;
            this.height           	= params.height;
            
            this.aviary				= params.aviary;

			this.previewPath		= ff.site_path + "/cm/showfiles.php";
			/*this.relativePath		= params.relativePath;*/

			this.basePathKC			= ff.base_path + basePathKC;
			this.baseUrlKC			= params.baseUrlKC;
			
            var startComponent = document.getElementById(this.component + this.start);
            var targetComponent = document.getElementById(this.component + this.target);
            var tmpnameComponent = document.getElementById(this.component + this.tmpname);
            var deleteComponent = document.getElementById(this.component + this.sufdel);

            var idComponent = this.component.replace(/[^a-zA-Z0-9]+/g,'');
            
            this.idComponent = idComponent;
            
            jQuery(startComponent).attr("id", idComponent);
            
            /*jQuery(startComponent).parent().find(".preview").prepend('<div class="uploadifiveQueue" id="' + idComponent + 'Queue"></div>');*/
            var component = this.component; 
            var previewJs = this.previewJs;
            var viewUrl = this.viewUrl;
            var previewUrl = this.previewUrl;
            var writable = this.writable;
            var previewPath = this.previewPath;
            /*var baseUrlUF = this.baseUrlUF;*/
            var basePathUF = this.basePathUF;
            var target = this.target;
			var model = this.model;
			var modelThumb = this.modelThumb;
			var showFile = this.showFile;
            var fullPath = this.fullPath;
            var dataSrc = this.dataSrc;
            var aviary = this.aviary;
           /* var relativePath = this.relativePath;*/

            if(model == 'default' && modelThumb == '')
                modelThumb = "thumb";
            
            jQuery("#" + idComponent).parent().find(".preview").attr("id", "uploadifive_" + idComponent);
            jQuery("#" + idComponent).parent().find(".preview").addClass("uploadifiveQueueItem");
            jQuery("#" + idComponent).parent().find(".preview").addClass(model);
            jQuery("#" + idComponent).parent().find(".preview").find(".cancel a img").attr('src', (this.basePathUF + "/uploadifive-cancel.png"));
            
			if(jQuery("#uploadifive_" + idComponent).attr("id") === undefined) {
				if(model == "vertical") {
					jQuery("#" + idComponent).parent().prepend('<div class="uploadifiveQueueItem ' + model + '" id="uploadifive_' + idComponent + '"></div>');
				} else if(model == "horizzontal") {
					jQuery("#" + idComponent).parent().prepend('<div class="uploadifiveQueueItem ' + model + '" id="uploadifive_' + idComponent + '"></div>');				
				} else {
					jQuery("#" + idComponent).parent().append('<div class="uploadifiveQueueItem ' + model + '" id="uploadifive_' + idComponent + '"></div>');
				}
				jQuery("#uploadifive_" + idComponent).hide(); 
			} else {
                if(previewUrl == '') {
	                if(modelThumb == '') {
	                    previewUrl = previewPath /*+ relativePath*/;
					} else {
						previewUrl = previewPath + "/" + modelThumb /*+ relativePath*/;
					}
				}

				if(modelThumb != '' && jQuery("#uploadifive_" + idComponent + " IMG.image").attr("src") !== undefined) {
					jQuery("#uploadifive_" + idComponent + " IMG.image").attr("src", jQuery("#uploadifive_" + idComponent + " IMG.image").attr("src").replace(previewPath + "/thumb" /*+ relativePath*/, previewUrl));
				}

				jQuery("#uploadifive_" + idComponent + " IMG.image").parent().attr("rel", previewUrl);

				if(model == "vertical") {
					/*jQuery("#" + idComponent).insertAfter(jQuery("#uploadifive_" + idComponent).append());*/
					jQuery("#KcFinder_" + component).insertAfter(jQuery("#uploadifive_" + idComponent).append());
				} else if(model == "horizzontal") {
					/*jQuery("#" + idComponent).insertAfter(jQuery("#uploadifive_" + idComponent).append());*/
					jQuery("#KcFinder_" + component).insertAfter(jQuery("#uploadifive_" + idComponent).append());
				}
			}

			var scriptData = {};
			scriptData[ff.modules.security.session.session_name] = ff.modules.security.session_id();
			scriptData['folder'] = this.dataSrc;
			scriptData['fileExt'] = this.fileExt;
			
            jQuery("#" + idComponent).uploadifive({
		        'uploadScript'      : this.basePathUF + '/uploadifive.php',
				'formData'			: scriptData,
                'buttonText'     	: '', 
                'buttonClass'     	: 'uploadifive', 
		        'auto'           	: true,
		        'multi'          	: false, 
		        'fileSizeLimit'     : this.sizeLimit,
		        'fileType'		 	: false,
		        'removeCompleted' 	: true,
	            'width'				: this.width, 
	            'height'			: this.height, 
                'queueID'        	: false, /* The optional ID of the queue container*/
                
		        onSelect		 	: function(queue) {
		        	ff.ffField.kcuploadifive.del(component);
		        	jQuery("DIV.actions INPUT[type=button]").attr("disabled", "disabled").css({ "opacity" : "0.6" });
				},
				onCancel : function() {
		        	jQuery("DIV.actions INPUT[type=button]").removeAttr("disabled").css({ "opacity" : "1" });
				},
		        onUploadComplete : function(fileObj, response) {
		        	jQuery("DIV.actions INPUT[type=button]").removeAttr("disabled").css({ "opacity" : "1" });
		        	
		        	var previewBlock = '';
		        	var descBlock = '';
		        	var editBlock = '';
		        	var cancelBlock = '';

		        	var response = jQuery.parseJSON(response);
		        	
					jQuery("#" + idComponent).uploadifive('clearQueue');
					if(response["status"]) {
						/*
	                    if(baseUrlUF == '/') {
	                        fileValue = '/' + response;
	                    } else {
	                        fileValue = fileObj.filePath.replace(fileObj.name, '') + response;
	                    }

	                    if(fullPath) {
	                        fileValueOut = fileValue;
	                    } else {
	                        fileValueOut = response;
	                    }

	                    if(fileValue.indexOf("/uploads") == 0) {
	                        fileValue = fileValue.substr(8);
	                    }
	                    if(fileValue.indexOf("/themes") == 0) {
	                        fileValue = fileValue.substr(7);
	                    }*/
	                    
	                    if(fullPath) {
	                        fileValue = response["fullpath"];
	                    } else {
	                        fileValue = response["name"];
	                    }
	                    
						fileValueOut = response["name"];                

	                    var byteSize = Math.round(fileObj.size / 1024 * 100) * .01;
	                    var suffix = 'KB';
	                    if (byteSize > 1000) {
	                        byteSize = Math.round(byteSize *.001 * 100) * .01;
	                        suffix = 'MB';
	                    }
	                    var sizeParts = byteSize.toString().split('.');
	                    if (sizeParts.length > 1) {
	                        byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0,2);
	                    } else {
	                        byteSize = sizeParts[0];
	                    }

	                    if(writable) {
	                        var strResponse = '<input type="text" id="' + component + target + '" name="' + component + target + '" value="' + fileValueOut + '" />';
	                    } else {
	                        /*var strResponse = '<input type="hidden" id="' + component + target + '" name="' + component + target + '" value="' + fileValueOut + '" />' + response["name"];*/
	                        var strResponse = response["name"];
	                    }

			            if(previewUrl == '') {
				            if(modelThumb == '') {
				                previewUrl = previewPath /*+ relativePath*/;
							} else {
								previewUrl = previewPath + "/" + modelThumb /*+ relativePath*/;
							}
						}
						
						if(viewUrl == '') {
							viewUrl = previewPath /*+ relativePath*/;
						}

						if(previewJs)
							previewBlock = '<a href="' + viewUrl + fileValue + '" target="_blank" rel="' + previewUrl + '"><img id="' + component + target + '_img" class="image" src="' + previewUrl + fileValue + '" /></a>';

						if(showFile)
							descBlock = '<span class="file_name">' + strResponse + ' (' + byteSize + suffix + ')' + '</span>';

						if(aviary)
							editBlock = '<div class="edit"><a href="javascript:void(0);" alt="modify" class="edit-file" onclick="ff.load(\'ff.ffField.aviary\', function() { ff.ffField.aviary.launch(\'' + aviary.key + '\', \'' + aviary.tools + '\', \'' + aviary.theme + '\', \'' + aviary.version + '\', \'' + aviary.post_url + '\', \'' + aviary.img_hash + '\', \'' + component + target + '_img' + '\', \'' + viewUrl + fileValue + '\', \'' + response["name"] + '\'); });"></a></div>';

						cancelBlock = '<div class="cancel"><a href="javascript:ff.ffField.uploadifive.del(\'' + component + '\');" alt="delete" class="del-file"></a></div>';

	                    if(model == "vertical") {
                        	jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
						} else if(model == "horizzontal") {
							jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
						} else {
							jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
						}

	                    jQuery("#uploadifive_" + idComponent).show();

	                    jQuery(targetComponent).val( response["name"] );
	                    jQuery(tmpnameComponent).val( response["name"] );
	                    jQuery(deleteComponent).val("");
					} else {
						alert(response["error"]);
					}
                }, 
                onError: function (errorType) {
                	jQuery("DIV.actions INPUT[type=button]").removeAttr("disabled").css({ "opacity" : "1" });
                	
                    alert('The error was: ' + errorType);
                }
		    });			
		},
		"del" : function(component, remove) {
            var targetComponent = document.getElementById(component + this.target);
            var tmpnameComponent = document.getElementById(component + this.tmpname);
            var idComponent = component.replace(/[^a-zA-Z0-9]+/g,'');

			if(jQuery(tmpnameComponent).val()) {
				fileDelete = jQuery(tmpnameComponent).val();
				
				jQuery.post(this.basePathUF + '/uploadifive.php', 'delaction=' + fileDelete + "&" + ff.modules.security.session.session_name + "=" + ff.modules.security.session_id() + "&folder=" + this.dataSrc, function(data) {
				}, "json");
				
				/*alert(jQuery(targetComponent).parent().text());*/
				
			} else if(jQuery(targetComponent).val() && remove) {
				fileDelete = jQuery(targetComponent).val();

				jQuery.post(this.basePathUF + '/uploadifive.php', 'delaction=' + fileDelete + "&" + ff.modules.security.session.session_name + "=" + ff.modules.security.session_id() + "&folder=" + this.dataSrc, function(data) {
				}, "json");
			}

			jQuery(tmpnameComponent).val("");
			jQuery(targetComponent).val("");

			jQuery("#uploadifive_" + idComponent).hide();
            
            return ;
		},
		"openKCFinder" : function (urlSelect, field) {
			var field = document.getElementById(field);
			var previewJs = this.previewJs;
		    window.KCFinder = {
		        callBack: function(url) {
		            window.KCFinder = null;
		            field.value = url;

		            kcUploadifiveSetFileField(url, field);
		        }
		    };

		    window.open(this.basePathKC + '/browse.php?type=&dir=' + urlSelect, 'kcfinder_textbox',
		        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
		        'resizable=1, scrollbars=0, width=800, height=600'
		    );
		},
		"setFileField" : function ( fileUrl, field ) {
			var showFile = this.showFile;
			var aviary = this.aviary;
			var previewJs = this.previewJs;

			var previewBlock = '';
		    var descBlock = '';
		    var editBlock = '';
		    var cancelBlock = '';			

			document.getElementById(field.id ).value = fileUrl.replace(ff.site_path + this.baseUrlKC, "") ;

			var component = field.id.replace(this.target, '');
			var idComponent = field.id.replace(this.target, '').replace(/[^A-Za-z0-9 ]/g, '');
			
	        var startComponent = document.getElementById(component + this.start);
	        var targetComponent = document.getElementById(component + this.target);
	        var tmpnameComponent = document.getElementById(component + this.tmpname);
	        var deleteComponent = document.getElementById(component + this.sufdel);

			var sFileName = decodeURIComponent( fileUrl.replace( /^.*[\/\\]/g, '' ) );
			var viewUrl = this.viewUrl;
			var previewUrl = this.previewUrl;
			var writable = this.writable;
		   /* var relativePath = this.relativePath;*/
			var model = this.model;
			var modelThumb = this.modelThumb;

	        var byteSize = '';
	        var suffix = '';
		
			
		    if(model == 'default' && modelThumb == '')
		        modelThumb = "thumb";
			
		    if(previewUrl == '') {
			    if(modelThumb == '') {
			        previewUrl = previewPath /*+ relativePath*/;
				} else {
					previewUrl = previewPath + "/" + modelThumb /*+ relativePath*/;
				}
			}
			
			if(viewUrl == '') {
				viewUrl = previewPath /*+ relativePath*/;
			}

			var viewFullUrl = previewUrl.substring(0, previewUrl.lastIndexOf("/")) + fileUrl.replace(ff.site_path + this.baseUrlKC, "");
			var previewFullUrl = previewUrl + fileUrl.replace(ff.site_path + this.baseUrlKC, "");
			var viewName = viewFullUrl.substring(viewFullUrl.lastIndexOf("/") + 1);


	        if(writable) {
	            var strResponse = '<input type="text" id="' + field.id + '" name="' + field.id + '" value="' + fileUrl.replace(ff.site_path + this.baseUrlKC, "") + '" />';
	        } else {
	            /*var strResponse = '<input type="hidden" id="' + component + target + '" name="' + component + target + '" value="' + fileValueOut + '" />' + response["name"];*/
	            var strResponse = viewName;
	        }	

			if(previewJs) {
				/*if(document.getElementById(field.id + "_img") == null) {*/
				previewBlock = '<a href="javascript:void(0);" rel="' + previewUrl + '" target="_blank"><img id="' + field.id + '_img' + '" class="image" /></a>';

				if(showFile)
					descBlock = '<span class="file_name">' + strResponse + ' ' + byteSize + suffix + '' + '</span>';

				if(aviary)
					editBlock = '<div class="edit"><a href="javascript:void(0);" alt="modify" class="edit-file" onclick="ff.load(\'ff.ffField.aviary\', function() { ff.ffField.aviary.launch(\'' + aviary.key + '\', \'' + aviary.tools + '\', \'' + aviary.theme + '\', \'' + aviary.version + '\', \'' + aviary.post_url + '\', \'' + aviary.img_hash + '\', \'' + field.id + '_img' + '\', \'' + viewFullUrl + '\', \'' + fileUrl.replace(ff.site_path + this.baseUrlKC, "") + '\'); });"></a></div>';

				cancelBlock = '<div class="cancel"><a href="javascript:ff.ffField.uploadifive.del(\'' + component + '\');" alt="delete" class="del-file"></a></div>';

		        if(model == "vertical") {
	                jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
				} else if(model == "horizzontal") {
					jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
				} else {
					jQuery("#uploadifive_" + idComponent).html('<span class="top">' + previewBlock + editBlock + cancelBlock + '</span>' + descBlock);
				}
				/*} */


				
				var parentA = document.getElementById(field.id + "_img").parentNode;
				if(parentA.rel) {
					document.getElementById(field.id + "_img").parentNode.href = viewFullUrl;
					document.getElementById(field.id + "_img").src = previewFullUrl;
					document.getElementById(field.id + "_img").title = sFileName;
				} else {
					document.getElementById(field.id + "_img").parentNode.href = "#";
					document.getElementById(field.id + "_img").src = "#";
					document.getElementById(field.id + "_img").title = "";
					
					/*jQuery(parentA).parent().find('.cancel').children('a').click();	*/
				}

				jQuery(tmpnameComponent).val("");

				jQuery("#uploadifive_" + idComponent).show();
			}
		}
	};
	return that;
	
})();

function kcUploadifiveSetFileField( fileUrl, field ) {
	return ff.ffField.kcuploadifive.setFileField(fileUrl, field);	
}
