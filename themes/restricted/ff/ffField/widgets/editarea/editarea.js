ff.ffField.editarea = (function () {
	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
		"init" : function(id, syntax, lang, writable) {

            ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearComponent});
            ff.addEvent({"event_name" : "getFields", "func_name" : that.onUpdateDialog});

			editAreaLoader.init({
						id: id	/* id of the textarea to transform	*/
						,start_highlight: true	
						,font_size: "8"
						,font_family: "verdana, monospace"
						,allow_resize: "both"
						,allow_toggle: false
						,is_editable: writable
						,language: lang
						,syntax: syntax
						,toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, word_wrap, charmap, |, help"
						,plugins: "charmap"
						,charmap_default: "arrows"
						, change_callback : "ff.ffField.editarea.updateEditArea"
					});

			that.doEvent({
				"event_name"	: "onCreate",
				"event_params"	: [document.getElementById(id)]
			});

		}, 
		"onClearComponent" : function (component, key, field) {
			if (field.widget == "editarea") {
				switch (ff.struct.get(component).type) {
					case "ffDetails":
						var rows = parseInt(jQuery("#" + component + "_rows").val());
						for (var i = 0; i < rows; i++) {
							/*jQuery("#frame_" + component + "_recordset[" + i + "][" + key + "]").remove(); */
						}
						break;

					default:
						
				}
				editAreaLoader.delete_instance(component + "_" + key);
			}
		},
		"onUpdateDialog" : function (id, dialog) {
			ff.struct.each(function (component_id, component) {
				if ((dialog !== undefined && component.dialog === dialog) || (dialog === undefined && component.dialog === undefined)) {
					component.fields.each(function (field_id, field) {
						if (field.widget == "editarea") {
							switch (component.type) {
								case "ffDetails":
									var rows = parseInt(jQuery("#" + component_id + "_rows").val());
									for (var i = 0; i < rows; i++) {
										editAreaLoader.setValue(component_id + "_recordset[" + i + "][" + field_id + "]", editAreaLoader.getValue(component_id + "_recordset[" + i + "][" + field_id + "]")); 
									}
									break;

								default:
									editAreaLoader.setValue(component_id + "_" + field_id, editAreaLoader.getValue(component_id + "_" + field_id));
							}
						}
					});
				}
			});
		},
		"updateEditArea" : function(EditArea) {
			jQuery("#" + EditArea).val(editAreaLoader.getValue(EditArea));
		}
		
	}; /* publics' end*/
	
	return that;
})();