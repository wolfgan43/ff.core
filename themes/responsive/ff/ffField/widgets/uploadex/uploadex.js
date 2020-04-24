ff.ffField.uploadex = (function () {

/* inits */
/* inits'end */

/* privates */
var instances = ff.hash();
/* privates' end*/

/* control object */
var control = function(params) {
	/* inits */
	/* inits'end */

	/* privates */
	var privates = { // just to keep all vars together
		"previews" : ff.hash(),
		"sortable_init" : false,
		"queues" : ff.hash(),
	}; 
	/* privates' end*/

	/* publics */
	var publics = {
		__ff : true /* used to recognize ff'objects*/
		, "params" : params
		
		/*, "destroy" : function () {
			if (params.multi) {
				privates.previews.clear();
			}
		}*/
		
		, "delFile" : function(idx) {
                        idx || (idx = 0);
			var obj = privates.previews.get(idx);
			if (obj === undefined) {
				throw "uploadex - delFile prewiews don't matches"
			}

			if (obj.type === "temp") {
				// async delete temp file, just to keep it clean
				jQuery.ajax({
					"type" : "POST",
					"url" : ff.fixPath(params.upload_script),
					"data" : {
						"data_src" : params.dataSrc,
						"delaction" : obj.filename
					},
					// success : function (data, textStatus, jqXHR) {}
					"dataType" : "json",
					"async" : true // non ci importa aspettare il risultato, comunque sono file temporanei
				});
			}

			obj.el.fadeOut(function () {
				jQuery(this).remove();
			});

			privates.previews.unset(idx);
			publics.updateInputs(undefined, [idx]);
			return true;
		}
		
		, showPreview : function(filename, name, type, byteSize, idx, preview_url, view_url) {
			// load saved image preview
			var display_paths = undefined;
			byteSize = byteSize || "";
			name = name || filename;
			
			if (idx === undefined && params.multi && params.multi_allow_duplicates === false) {
				var found = false;
				privates.previews.each(function (k, v, i) {
					if (v.name === name) {
						found = v;
						return true;
					}
				});
				if (found !== false) {
					idx = found.idx;
				}
			}
			
			idx = idx || (params.multi ? ff.getUniqueID() : 0);

			/*if (type == "temp") {
				display_paths = params.display_paths.temp;
			} else {
				display_paths = params.display_paths.saved;
			}*/
			
			var tmp_code_preview = privates.preview_template;
			tmp_code_preview = tmp_code_preview.replaceAll("[[name]]", name);
                        
			var tmp_preview_url = preview_url;// || display_paths.preview.replace("[_FILENAME_]", filename);
			var tmp_view_url = view_url;// || display_paths.view.replace("[_FILENAME_]", filename);
                        /*var filename_name = "";
                        var filename_ext = filename.split('.').pop();
                        if (filename_ext.length === filename.length) {
                            filename_name = filename_ext;
                            filename_ext = "";
                        } else {
                            filename_name = filename.substr(0, filename.length - filename_ext.length - 1);
                        }

			var tmp_preview_url = preview_url || display_paths.preview.replace("[_FILENAME_]", filename);
                        tmp_preview_url = tmp_preview_url.replace("[_FILEONLYNAME_]", filename_name);
                        tmp_preview_url = tmp_preview_url.replace("[_FILEONLYEXT_]", filename_ext);
			tmp_preview_url = ff.urlAddParam(tmp_preview_url, "_", Math.random());*/
			tmp_code_preview = tmp_code_preview.replaceAll("[[preview_url]]", ff.fixPath(tmp_preview_url));
				
			/*var tmp_view_url = view_url || display_paths.view.replace("[_FILENAME_]", filename);
                        tmp_view_url = tmp_view_url.replace("[_FILEONLYNAME_]", filename_name);
                        tmp_view_url = tmp_view_url.replace("[_FILEONLYEXT_]", filename_ext);
			tmp_view_url = ff.urlAddParam(tmp_view_url, "_", Math.random());*/
			tmp_code_preview = tmp_code_preview.replaceAll("[[view_url]]", ff.fixPath(tmp_view_url));
				
			tmp_code_preview = tmp_code_preview.replaceAll("[size]", byteSize);
			tmp_code_preview = tmp_code_preview.replaceAll("[type]", "temp");
			if (params.multi) {
				tmp_code_preview = tmp_code_preview.replaceAll("[el_idx]", ", " + idx);
				tmp_code_preview = tmp_code_preview.replaceAll("[idx]", idx);
			} else {
				tmp_code_preview = tmp_code_preview.replaceAll("[el_idx]", "");
				tmp_code_preview = tmp_code_preview.replaceAll("[idx]", "");
			}

			var tmp_el = jQuery(tmp_code_preview);

			var tmp_replace = false;
			if (privates.previews.isset(idx)) {
				// -- replace preview
				privates.previews.get(idx).el.replaceWith(tmp_el);
				privates.previews.unset(idx);
			} else {
				// -- add preview
				privates.$previews.append(tmp_el);
				if (params.sortable && !privates.sortable_init && privates.previews.length === 1) {
					privates.sortable_init = true;
					privates.$previews.sortable(params.sortable_options);
				}
			}
			
			privates.previews.set(idx, {
				"idx"			: idx,
				"el"			: tmp_el,
				"type"			: type,
				"filename"		: filename,
				"name"			: name,
				"type"			: type,
				"byteSize"		: byteSize,
				"preview_url"	: tmp_preview_url,
				"view_url"		: tmp_view_url
			});
			
			if (params.onclick) {
				jQuery(".view-link", tmp_el).click(function () {
					return params.onclick(params, privates.previews.get(idx));
				});
			} else if (params.showFilePlugin && name.toLowerCase().substr(-4) !== ".pdf") {
				jQuery(".view-link", tmp_el).click(function () {
					return ff.ffField.uploadex.plugins[params.showFilePlugin].showImage(
							params
							, (params.multi ? privates.previews : tmp_el)
							, (params.multi ? idx : name))
				});
			}
			
		}
		
		, updateInputs : function (update_refs, exclude_list) {
			if (params.multi) {
				var files = "";
				if (update_refs)
					var new_refs = ff.hash();

				jQuery(".uplex-preview-item", privates.$previews).each(function(index) {
					  var idx = jQuery(this).data("uplex-idx");
					  if (exclude_list !== undefined && exclude_list.indexOf(idx) !== -1)
						  return;

					  var item = privates.previews.get(idx);
					  if (item === undefined)
						  throw "uplex - corrupted previews";

					  files += (files.length ? "," : "") + item.name + "|" + item.filename + "|" + item.type;

					  if (update_refs)
						  new_refs.set(idx, item);
				});

				privates.$name.val(files);
				if (update_refs)
					privates.previews = new_refs;
			} else {
				var item = privates.previews.get(0);
				if (item === undefined) {
								privates.$name.val("");
								privates.$tmpname.val("");
							} else {
								privates.$name.val(item.name);
								privates.$tmpname.val(item.filename);
							}
			}
		}
		
	}; /* publics' end */
	
	params.upload_script = params.upload_script || ff.site_path + "/themes/responsive/ff/ffField/widgets/uploadex/uploadex.php";
	
	jQuery.extend(true, publics, ff.ffEvents());
	
	privates.$control		= jQuery.fn.escapeGet(params.id + params.control);
	privates.$name			= jQuery.fn.escapeGet(params.id + params.name);
	privates.$tmpname		= jQuery.fn.escapeGet(params.id + params.tmpname);
	privates.$delete		= jQuery.fn.escapeGet(params.id + params.sufdel);

	privates.$container		= jQuery.fn.escapeGet("uplex_" + params.id + "_container");
	privates.$droparea		= jQuery(".uplex-control-droparea", privates.$container);
	privates.$previews		= jQuery.fn.escapeGet("uplex_" + params.id + "_previews");
	privates.$queue			= jQuery.fn.escapeGet("uplex_" + params.id + "_queue");
	
	var $preview_template = jQuery(".uplex-preview-item", privates.$previews);
	privates.preview_template = $preview_template.outerHTML()
			.replaceAll('src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" ', '')
			.replaceAll('shadowhref="', 'href="')
			.replaceAll('shadowsrc="', 'src="');
	$preview_template.remove();
	
	var $queue_template = jQuery(".uplex-queue-item", privates.$queue);
	var $error_template = jQuery(".uplex-queue-error", $queue_template);
	privates.error_template = $error_template.outerHTML();
	$error_template.remove();
	
	privates.queue_template = $queue_template.outerHTML();
	$queue_template.remove();
	
		/*var $queue_item = jQuery(privates.queue_template);
		privates.$queue.append($queue_item);
		var $progressbar = jQuery(".uplex-progressbar", $queue_item);
		var $progresslabel = jQuery(".uplex-progressbar-label", $queue_item);
		
		$progressbar.progressbar({
			value: 20,
			change: function() {
				$progresslabel.text( $progressbar.progressbar( "value" ) + "%" );
			}
		});
				
		var $queue_item = jQuery(privates.queue_template);
		privates.$queue.append($queue_item);
		var $progressbar = jQuery(".uplex-progressbar", $queue_item);
		var $progresslabel = jQuery(".uplex-progressbar-label", $queue_item);
		
		$progressbar.progressbar({
			value: 20,
			change: function() {
				$progresslabel.text( $progressbar.progressbar( "value" ) + "%" );
			}
		});*/
				
	privates.$droparea.html(params.label);
	
	// init sortable functions
	if (params.sortable) {
		params.sortable_options.update = function (event, ui) {
			publics.updateInputs(true);
		}
	}
	
	/*if (params.multi) { // MULTI FILE MODE
		var files = privates.$name.val();
		var hTmpFiles = ff.hash(files.length ? files.split(",") : []);
		hTmpFiles.each(function (key, value, index) {
			if (value.indexOf("|") != -1) {
				var name = value.split("|")[0];
				var tmpname = value.split("|")[1];
				var type = value.split("|")[2];
			} else {
				throw "should it happen?";
			}

			publics.showPreview(tmpname, name, type);
		});
	} else { // SINGLE FILE MODE (normal behaviour)
		if (privates.$tmpname.val().length) {
			publics.showPreview(privates.$tmpname.val(), undefined, "temp");
		} else if (privates.$name.val().length) {
			publics.showPreview(privates.$name.val(), undefined, "saved");
		}
	}*/
	
	if (!window.File || !window.FileList || !window.FileReader) {
		privates.$control.closest(".uplex-control-container").html("Uploadex is unsupported, please update you browser");
		return;
	}
	
	/*
	onCancel : function() {
		if (ff.ffPage && ff.ffPage.activebuttons) {
			ff.ffPage.activebuttons.activateAll();
		}
	},
	onError: function (errorType) {
		if (ff.ffPage && ff.ffPage.activebuttons) {
			ff.ffPage.activebuttons.activateAll();
		}

		alert('The error was: ' + errorType); // TODO
	},*/
	
	function fileSelectHandler (e) {
		e.stopPropagation();
		e.preventDefault();
		
		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;
		if (!files.length)
			return;
		
		var queue_id = ff.getUniqueID();
		privates.queues.set(queue_id, files.length);
		if (privates.queues.length === 1) {
			//privates.$control.attr("disabled", true).css("cursor", "wait");
			if (ff.ffPage && ff.ffPage.activebuttons) {
				ff.ffPage.activebuttons.deactivateAll();
			}
		}
		
		// process all File objects
		(async function(files) {
			files: for (var i = 0, f; f = files[i]; i++) {
				var name_to_check = f.name;

				while (true) {
					var exists = false;
					
					await new Promise((resolve, reject) => {
						var rc = checkFile(f, name_to_check, resolve, reject);
						if (rc !== null) {
							resolve(rc);
						}
					}).then((found) => {
						exists = found;
					}).catch((message) => {
						queueCompletedItem(queue_id);
						throw "Uploadex - checkFile error: " + message
					});
					
					if (exists !== false) {
						var newname = null;
						
						await new Promise((resolve, reject) => {
							// prompt dialog
							var res = ff.ffField.uploadex.doEvent({
								"event_name"	: "fileRenameDialog",
								"event_params"	: [instances.get(params.id), f, name_to_check, exists, resolve, reject]
							});
							var lastres = ff.getLastRes(res);
							if (lastres !== undefined) {
								if (lastres !== null)
									resolve(lastres);
								return;
							}
							if (params.allow_replace) {
								newname = prompt("Un file con lo stesso nome esiste già.\nSpecificare il nome per il nuovo file o lasciarlo invariato per sovrascriverlo:", name_to_check);
							} else {
								newname = prompt("Un file con lo stesso nome esiste già.\nSpecificare un nome differente:", name_to_check);
							}
							resolve(newname);
						}).then((value) => {
							newname = value;
						}).catch((message) => {
							queueCompletedItem(queue_id);
							throw "Uploadex - fileSelectHandler error: " + message
						});
						
						if (newname == null || newname == "") {
							queueCompletedItem(queue_id);
							continue files;
						} else {
							if (newname == name_to_check && params.allow_replace) {
								break;
							} else {
								name_to_check = newname;
							}
						}
					} else {
						break;
					}
				}

				if (name_to_check != f.name) {
					f.newname = name_to_check;
				}
				
				UploadFile(f, params.dataSrc, queue_id);
			}
			
			// reset control for future uploads
			e.target.value = "";
		})(files);
	}
	privates.$control.get(0).addEventListener("change", fileSelectHandler, false);
	
	function queueCompletedItem(queue_id) {
		privates.queues.set(queue_id, privates.queues.get(queue_id) - 1);
		if (!privates.queues.get(queue_id)) {
			privates.queues.unset(queue_id);
			if (!privates.queues.length) {
				privates.$control.attr("disabled", false).css("cursor", "pointer");
				if (ff.ffPage && ff.ffPage.activebuttons) {
					ff.ffPage.activebuttons.activateAll();
				}
			}
		}
	}
	
	function checkFile(file, name, resolve, reject) {
		var found = false;
		
		privates.previews.each(function (k, v, i) {
			if (v.name === name) {
				found = v;
				return true;
			}
		});
		
		var res = ff.ffField.uploadex.doEvent({
			"event_name"	: "checkFile",
			"event_params"	: [instances.get(params.id), file, name, found, resolve, reject]
		});
		var lastres = ff.getLastRes(res);
		if (lastres !== undefined) 
		{
			return lastres; // can be true, false, or null. if null, don't resolve but wait for resolve to be called
		}
		
		return found;
	}
		
	function UploadFile(file, dataSrc, queue_id) {
		var replace_idx = null;
		var xhr = new XMLHttpRequest();
		var filename = file.newname || file.name;
		/*
		if (file.type == "image/jpeg" && file.size <= $id("MAX_FILE_SIZE").value) {
			
		}*/
		
		var url = ff.fixPath(params.upload_script);
		url = ff.urlAddParam(url, "data_src", dataSrc);
		url = ff.urlAddParam(url, "filename", filename);
		
		// delete previous temp file when uploading a newer one
		
		if (params.multi && params.multi_allow_duplicates === false) {
			var found = false;
			privates.previews.each(function (k, v, i) {
				if (v.name === filename) {
					found = v;
					return true;
				}
			});
			if (found !== false && found.type === "temp") {
				url = ff.urlAddParam(url, "delaction", found.filename);
				replace_idx = found.idx;
			}
		} else if (!params.multi && privates.$tmpname.val().length) {
			url = ff.urlAddParam(url, "delaction", privates.$tmpname.val());
		}
		
		xhr.open("POST", url, true);
		
		var tmp_tpl = privates.queue_template;
		tmp_tpl = tmp_tpl.replaceAll("[[name]]", filename);
		var $queue_item = jQuery(tmp_tpl);
		privates.$queue.append($queue_item);
		var $progressbar = jQuery(".uplex-progressbar", $queue_item);
		var $progresslabel = jQuery(".uplex-progressbar-label", $queue_item);
		
		jQuery(".uplex-queue-cancel", $queue_item).bind("click.ffField.uploadex", function () {
			jQuery(this).remove();
			$progresslabel.text( filename + ": interpputed" );
			xhr.abort();
		});
		
		$progressbar.progressbar({
			value: false,
			change: function() {
				$progresslabel.text( /*file.name + ": " + */$progressbar.progressbar( "value" ) + "%" );
			}/*,
			complete: function() {
				$progresslabel.text( "Complete!" );
			}*/
		});
		
		xhr.upload.addEventListener("progress", function(e) {
			var pc = parseInt(e.loaded / e.total * 100);
			jQuery(".uplex-progressbar", $queue_item).progressbar({value: pc});
		}, false);
		
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4) {
				var success = false;
				var error_msg = "Error: unknown";
			
				if (xhr.status === 0) {
					//console.log("aborted");
					success = false;
					error_msg = "no network or interrupted";
				} else if (xhr.status === 200) {
					var res = undefined;
					if (false !== (res = ff.isJsonString(xhr.responseText))) {
						if (res["status"]) {
							// success!
							success = true;
							
							onUploadComplete(res.name, res.tmpname, res.mime, res.size, replace_idx, res.preview, res.view);
						} else {
							// api failure, managed error
							//console.log("xhr.onreadystatechange API FAILURE", res["error"]);
							error_msg = res["error"];
						}
					} else {
						// api error, wrong return format (unmanaged error)
						//console.log("xhr.onreadystatechange API ERROR", xhr.responseText);
						error_msg = "wrong return format";
					}
				} else {
					// http error
					//console.log("xhr.onreadystatechange HTTP ERROR", xhr.status);
					error_msg = "http " + xhr.status;
				}
				
				if (success) {
					jQuery(".uplex-queue-cancel", $queue_item).remove();
					$queue_item.fadeOut({
						"complete" : function () {
							$progressbar.progressbar("destroy");
							$queue_item.remove();
						}
					});
				} else {
					jQuery(".uplex-queue-cancel", $queue_item).unbind("click.ffField.uploadex").bind("click.ffField.uploadex", function () {
						jQuery(this).remove();
						$queue_item.fadeOut({
							"complete" : function () {
								jQuery(this).remove();
							}
						});
					});
					
					var tmp_tpl = privates.error_template;
					tmp_tpl = tmp_tpl.replaceAll("[[name]]", filename);
					tmp_tpl = tmp_tpl.replaceAll("[[error_msg]]", error_msg);
					var $err_el = jQuery(tmp_tpl);
					$progressbar.progressbar("destroy").replaceWith($err_el);
				}
				
				queueCompletedItem(queue_id);
			}
		};
		
		xhr.send(file);
	}
	
	function onUploadComplete(name, tmpname, mime, size, replace_idx, preview, view) {
		// calculate bytesize
		var byteSize = Math.round(size / 1024 * 100) * .01;
		var suffix = 'Kb';
		if (byteSize > 1000) {
			byteSize = Math.round(byteSize *.001 * 100) * .01;
			suffix = 'Mb';
		}

		var sizeParts = byteSize.toString().split('.');
		if (sizeParts.length > 1) {
			byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0, 2) + suffix;
		} else {
			byteSize = sizeParts[0] + suffix;
		}

		/*if (inst.uploadLimit && inst.previews.length >= inst.uploadLimit) {
			//jQuery("#uploadifive-" + inst.component + "_file .uploadifive-input").attr("disabled", true);
			jQuery("#uploadifive-" + inst.component + "_file.uploadifive-button").unbind("click.ffField.uploadifive").bind("click.ffField.uploadifive", function () {alert ("MAX LIMIT REACHED"); return false;});
		}*/
		
		// -----------------------------------------
		//        update inputs and previews
		// -----------------------------------------

		if (tmpname && tmpname.length) {
			publics.showPreview(tmpname, name, "temp", byteSize, replace_idx, preview, view);
		} else {
			publics.showPreview(name, undefined, "saved", byteSize, replace_idx, preview, view);
		}
		
		publics.updateInputs();
		// -----------------------------------------
	}
	
	return publics;
};

/* publics */
var that = {
__ff : true /* used to recognize ff'objects*/
, "plugins" : {}

, "getInstance" : function (id, avoid_undef) {
	var tmp = instances.get(id);
	if (tmp === undefined && !avoid_undef)
		throw "ff.ffField.uploadex - instance does not exists [" + id + "]";

	return tmp;
}

, "exists" : function (id) {
	if (instances.get(id) === undefined)
		return false
	else
		return true;
}

, "factory" : function (params) {
	if (that.exists(params.id))
		return;
		
	var tmp = control(params);
	instances.set(params.id, tmp);

	that.doEvent({
		"event_name"	: "factory",
		"event_params"	: [tmp]
	});

	return tmp;
}

, "delete" : function (id) {
	if (!that.exists(id)) {
		throw ("uploadex: instance doesn't exists");
	}
	//instances.get(id).destroy();
	instances.unset(id);
}

, "delFile" : function(id, name) {
	var inst = instances.get(id);
	if (inst === undefined) {
		throw ("uploadex: instance doesn't exists");
	}
	
	inst.delFile(name);
}

}; /* publics' end */

function onClearField(component, field_id, field_data, inst_id) {
	if (field_data.widget !== "uploadex")
		return;

	if (that.exists(inst_id)) {
		that.delete(inst_id);
	}
}

ff.pluginAddInit("ff.ffField.uploadex", function () {
	ff.addEvent({"id" : "ff.ffField.uploadex.onClearField", "event_name" : "onClearField", "func_name" : onClearField});
});
	
return that;

})();
