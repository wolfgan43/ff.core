ff.ffField.aviary = (function () {
	var aviaryUrl = "http://feather.aviary.com/js/feather.js";
	var defaultTools = "all";
	var defaultTheme = "light";
	var defaultVersion = 3;
	var defaultPostUrl = "/themes/restricted/ff/ffField/widgets/aviary/save.php";
	var featherEditor = null;

	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
		"init" : function (key, tools, theme, version, postUrl, imgHash, imgElem, imgPath, newImg) {
			var tools = (tools ? tools : defaultTools);
			var theme = (theme ? theme : defaultTheme);
			var version = (version ? version : defaultVersion);
			var postUrl = (postUrl ? postUrl : defaultPostUrl);
			
			ff.pluginLoad('jquery.cookie', '/themes/library/plugins/jquery.cookie/jquery.cookie.js', function() {
				ff.pluginLoad('Aviary', 'http://feather.aviary.com/js/feather.js', function() {
					featherEditor = new Aviary.Feather({
						apiKey: key, /* {aviary_api_key}',*/
	       				apiVersion: version,
	       				theme: theme, /* Check out our new 'light' and 'dark' themes!*/
	       				tools: tools, /*['draw', 'stickers'],*/
	       				appendTo: '',
	       				onLoad : function() {
	       					var imgName = '';
	       					if(newImg) {
								imgName = "&img=" + newImg;
	       					}

    						featherEditor.launch({
								image: imgElem,
								url: window.location.protocol + '//' + window.location.hostname + imgPath,
								postUrl: window.location.protocol + '//' + window.location.hostname + postUrl,
								postData: ff.modules.security.session.session_name + "=" + ff.modules.security.session_id() + "&ref=" + imgHash + imgName
							}); 
	       				},
						onSave: function(imageID, newURL) {
							ff.ffField.aviary.reloadImg(imageID);
							featherEditor.close();
						},
	       				onError: function(errorObj) {
	           				alert(errorObj.message);
	       				}
					});
				});
			});
		},
		"launch" : function(key, tools, theme, version, postUrl, imgHash, imgElem, imgPath, newImg) {
			if(featherEditor === null) {
				ff.ffField.aviary.init(key, tools, theme, version, postUrl, imgHash, imgElem, imgPath, newImg);
			} else {
	       		var imgName = '';
	       		if(newImg) {
					imgName = "&img=" + newImg;
	       		}				

				featherEditor.launch({
					image: imgElem,
					url: window.location.protocol + '//' + window.location.hostname + imgPath,
					postData: ff.modules.security.session.session_name + "=" + ff.modules.security.session_id() + "&ref=" + imgHash + imgName,
					onReady : function() {
						featherEditor.showWaitIndicator();
					}
				}); 
			}

			return false; 
		},
		"reloadImg" : function (id) {
		   var obj = document.getElementById(id);
		   var src = obj.src;
		   var pos = src.indexOf('?');
		   if (pos >= 0) {
		      src = src.substr(0, pos);
		   }
		   var date = new Date();
		   obj.src = src + '?v=' + date.getTime() + '&__nocache__';
		   return false;
		}		
	};
	return that;
	
})();		
