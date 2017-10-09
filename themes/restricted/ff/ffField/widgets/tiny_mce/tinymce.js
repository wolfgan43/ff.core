ff.ffField.tinymce = (function () {
	var events = false;

	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
        "init" : function(selectorClass, lang, plugins, buttons1, buttons2, buttons3, buttons4, buttons5, buttons6) {
          	if (!events) {
          		ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearComponent});
          		if(ff.ffPage.dialog)
          			ff.ffPage.dialog.addEvent({"event_name" : "doAction", "func_name" : that.onUpdateDialog});
          		events = true;
			}            
            tinymce.baseURL = ff.site_path + "/themes/library/tiny_mce";

            tinyMCE.init({  
                relative_urls : false,
                language : lang, 
                mode : "specific_textareas",
                editor_selector : selectorClass, 
                theme : "advanced",
                plugins : plugins,
                width: '100%',
                height: $("." + selectorClass).parent().height() + "px",
                theme_advanced_buttons1 : buttons1,
                theme_advanced_buttons2 : buttons2,
                theme_advanced_buttons3 : buttons3,
                theme_advanced_buttons4 : buttons4,
                theme_advanced_buttons5 : buttons5,
                theme_advanced_buttons6 : buttons6,        
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                content_css : "example_data/example_full.css",
                plugin_insertdate_dateFormat : "%Y-%m-%d",
                plugin_insertdate_timeFormat : "%H:%M:%S",
                theme_advanced_resize_horizontal : true,
                theme_advanced_resizing : true,
                apply_source_formatting : true,
                spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",
                filemanager_custom_data : "selectedlang=" + lang,
                font_size_style_values : "8px,10px,12px,14px,18px,24px,36px"
            });
            
            that.doEvent({
				"event_name"	: "onCreate",
				"event_params"	: [elem]
			});
        }, 
        "onClearComponent" : function (component, key, field) {
            if (field.widget == "tiny_mce") {
				var instances = ff.struct.get(component).widgets;
				instances.each(function(key, value) {
					tinyMCE.get(value["id"]).destroy();
				});            
/*
                switch (ff.struct.get(component).type) {
                    case "ffDetails":
                        var rows = parseInt(jQuery("#" + component + "_rows").val());
                        for (var i = 0; i < rows; i++) {
                        	tinyMCE.get(component + "_recordset[" + i + "][" + key + "]").destroy();
                        }
                        break;

                    default:
                    	tinyMCE.get(component + "_" + key).destroy();
                }*/
            }
        },
        "onUpdateDialog" : function (id) {
            ff.struct.each(function (component_id, component) {
                if (component.dialog == id)
                    component.fields.each(function (field_id, field) {
                        if (field.widget == "tiny_mce") {
                            switch (component.type) {
                                case "ffDetails":
                                    var rows = parseInt(jQuery("#" + component_id + "_rows").val());
                                    for (var i = 0; i < rows; i++) {
                                    	tinyMCE.get(component_id + "_recordset[" + i + "][" + field_id + "]").save();
                                    	 
                                        /*if(CKEDITOR.instances[component_id + "_recordset[" + i + "][" + field_id + "]"] !== undefined)
                                            CKEDITOR.instances[component_id + "_recordset[" + i + "][" + field_id + "]"].updateElement();*/
                                    }
                                    break;

                                default:
	                                tinyMCE.get(component_id + "_" + field_id).save();

                                	/*$("#" + component_id + "_" + field_id).tinymce().save();
                                    if(CKEDITOR.instances[component_id + "_" + field_id] !== undefined)
                                        CKEDITOR.instances[component_id + "_" + field_id].updateElement();*/
                            }
                        }
                    });
            });
        }
        
    }; /* publics' end*/

    return that;
})();