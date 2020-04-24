<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (uploadex)
//			   by Alessandro Stucchi
// ----------------------------------------

ffRecord::addEvent("retrieve_field_after", function ($oRecord, $fields_array, $id, $mode) {
	if ($mode === "db" && $fields_array[$id]->widget === "uploadex")
	{
		if (strlen($fields_array[$id]->value->getValue()))
		{
			$tmp = explode(",", $fields_array[$id]->value->getValue());
			$final_value = array();
			foreach ($tmp as $key => $value)
			{
				$final_value[] = implode("|", array(
					$value, $value, "saved"
				));
			}
			$final_value = implode(",", $final_value);
			$fields_array[$id]->value->setValue($final_value);
			$fields_array[$id]->value_ori = clone $fields_array[$id]->value;
		}
	}
});

ffGlobals::getInstance("ff")->events->addEvent("files_set_value", function ($comp, $field, $tmpval) {
	if (!($field->widget === "uploadex" && $field->file_multi))
		return null;
	
	$final_value = array();
	
	if (strlen($tmpval->getValue()))
	{
		$value = explode(",", $tmpval->getValue());
		foreach ($value as $k => $v)
		{
			$tmp = explode("|", $v);
			$final_value[] = $tmp[0];
		}

		return implode(",", $final_value);
	}
});

ffGlobals::getInstance("ff")->events->addEvent("files_preprocess", function ($comp, $field) {
	if (!($field->widget === "uploadex" && $field->file_multi))
		return null;
	
	$file_actions = array();
	
	$value = array();
	$value_ori = array();
	
	$tmpval = clone $field->value;
	if (strlen($tmpval->getValue()))
	{
		$tmp_value = explode(",", $tmpval->getValue());
		foreach ($tmp_value as $k => $v)
		{
			$tmp = explode("|", $v);
			$value[$tmp[0]] = array(
				"file" => $tmp[1],
				//"size" => $tmp[2],
				"type" => $tmp[2],
			);
		}
	}
	
	$tmpval = clone $field->value_ori;
	if (strlen($tmpval->getValue()))
	{
		$tmp_value_ori = explode(",", $tmpval->getValue());
		foreach ($tmp_value_ori as $k => $v)
		{
			$tmp = explode("|", $v);
			$value_ori[$tmp[0]] = array(
				"file" => $tmp[1],
				//"size" => $tmp[2],
				"type" => $tmp[2],
			);
		}
	}

	// deletion before
	if (count($value_ori)) foreach ($value_ori as $name => $data)
	{
		if (!isset($value[$name]) || $value[$name]["type"] === "temp")
		{
			$file_actions[] = array(
				"type" => "delete",
				"field" => $field->id,
				"ori" => $data["file"],
				"file" => $field->getFileFullPath($data["file"], false)
			);
		}
	}
	
	// then saving
	if (count($value)) foreach ($value as $name => $data)
	{
		if ($value[$name]["type"] === "temp")
		{
			$file_actions[] = array(
				"type" => "move",
				"field" => $field->id,
				"ori" => $data["file"],
				"src" => $field->getFileFullPath($data["file"]),
				"dest" => $field->getFileFullPath($name, false)
			);
		}
	}
	
	return $file_actions;
});

ffGlobals::getInstance("ff")->events->addEvent("files_purge", function ($comp, $field) {
	if (!($field->widget === "uploadex" && $field->file_multi))
		return null;
	
	$file_actions = array();
	
	$value = array();
	$value_ori = array();
	
	$tmpval = clone $field->value;
	if (strlen($tmpval->getValue()))
	{
		$tmp_value = explode(",", $tmpval->getValue());
		foreach ($tmp_value as $k => $v)
		{
			$tmp = explode("|", $v);
			$value[$tmp[0]] = array(
				"file" => $tmp[1],
				//"size" => $tmp[2],
				"type" => $tmp[2],
			);
		}
	}
	
	$tmpval = clone $field->value_ori;
	if (strlen($tmpval->getValue()))
	{
		$tmp_value_ori = explode(",", $tmpval->getValue());
		foreach ($tmp_value_ori as $k => $v)
		{
			$tmp = explode("|", $v);
			$value_ori[$tmp[0]] = array(
				"file" => $tmp[1],
				//"size" => $tmp[2],
				"type" => $tmp[2],
			);
		}
	}

	// ori before
	if (count($value_ori)) foreach ($value_ori as $name => $data)
	{
		$file_actions[] = array(
			"type" => "delete",
			"field" => $field->id,
			"file" => $field->getFileFullPath($data["file"], false)
		);
	}
	
	// then current
	if (count($value)) foreach ($value as $name => $data)
	{
		$file_actions[] = array(
			"type" => "delete",
			"field" => $field->id,
			"file" => $field->getFileFullPath($data["file"], false)
		);
	}
	
	return $file_actions;
});

class ffWidget_uploadex extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_uploadex";

	var $widget_deps	= array();
	
	var $libraries		= array();
	
    var $js_deps = array(
							"ff.ffField.uploadex"	=> null
						);
    var $css_deps 		= array(
    					);

	// PRIVATE VARS
	
	var $tpl 			= null;
	var $db				= null;

	var $oPage = null;
	var $source_path	= null;
	var $style_path = null;
	
	
	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		//$this->get_defaults();

		$this->oPage = array(&$oPage);
		
		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
		
		$this->db[0] = ffDb_Sql::factory();

	}

	function prepare_template($id)
	{
		$this->tpl[$id] = ffTemplate::factory(ffCommon_dirname(__FILE__));
		$this->tpl[$id]->load_file($this->template_file, "main");

		$this->tpl[$id]->set_var("source_path", $this->source_path);

        if ($this->style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());

	}
	
	function pre_process($obj, $options = null)
	{
		if (ffArrIsset($options, "events"))
		{
			foreach ($options["events"] as $key => $event) {
				call_user_func_array(array(&$this, "addEvent"), array_merge_recursive(array($key), $event));
			}
		}
	}

	function process($id, &$fld_value, ffField_base &$Field)
	{
		global $plgCfg_uploadex_UseOwnSession;
		
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", "\"" . $tpl_id . "\"");
			$this->tpl[$tpl_id]->set_var("container", $prefix);
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", "undefined");
		}
		
		$this->doEvent("onProcess", array(&$this, &$Field, $tpl_id));
		
		if ($this->oPage[0]->getXHRCtx())
			$this->tpl[$tpl_id]->set_var("ctx", '"' . $this->oPage[0]->getXHRCtx() . '"');
		else
			$this->tpl[$tpl_id]->set_var("ctx", "undefined");
		
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());

		$fixed_pre_content = $Field->fixed_pre_content;
		$fixed_post_content = $Field->fixed_post_content;
		$fixed_pre_content = str_replace("[FIELD_ID]", $id, $fixed_pre_content);
		$fixed_post_content = str_replace("[FIELD_ID]", $id, $fixed_post_content);
		
		$this->tpl[$tpl_id]->set_var("fixed_pre_content", $fixed_pre_content);
		$this->tpl[$tpl_id]->set_var("fixed_post_content", $fixed_post_content);
        
        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/responsive/ff/ffField/widgets/uploadex");
		
		if ($Field->uploadex_showfile_plugin /*&& is_file(FF_DISK_PATH . FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadex_showfile_plugin . "/jquery." . $Field->uploadex_showfile_plugin . ".js")*/)
		{
			$Field->parent_page[0]->tplAddJs("ff.ffField.uploadex.plugins." . $Field->uploadex_showfile_plugin);
			$this->tpl[$tpl_id]->set_var("showfile_plugin", "'" . $Field->uploadex_showfile_plugin . "'");
		} 
		else 
		{
			$this->tpl[$tpl_id]->set_var("showfile_plugin", "undefined");
		}
		
		$this->tpl[$tpl_id]->set_var("SectParam", "");
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// session manage
		
		$session = false;
		$ff_data = array();
		
		if (!$Field->uploadex_force_no_session && mod_security_check_session(false))
		{
			if (get_session("UserNID") != MOD_SEC_GUEST_USER_ID) // qui e non sopra per evitare la doppia sessione
			{
				$ff_data = get_session("ff");
				$session = true;
			}
		}
		else if (($plgCfg_uploadex_UseOwnSession || $Field->uploadex_use_own_session) && !$Field->uploadex_force_no_session)
		{
			/*if (strlen($Field->uploadex_own_sess_name))
				session_name($Field->uploadex_own_sess_name);*/
			
			if (isset($_POST[session_name()]))
				session_id($_POST[session_name()]);
			elseif (isset($_GET[session_name()]))
				session_id($_GET[session_name()]);
			elseif (isset($_COOKIE[session_name()]))
				session_id($_COOKIE[session_name()]);
			
			if (!defined("FF_UPLOADEX_SESSION_STARTED"))
			{
				session_start();
				define("FF_UPLOADEX_SESSION_STARTED", true);
			}			
			
			$ff_data = get_session("ff");
			$session = true;
		}
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// process paths
		
		$display_paths = $Field->fileGetPaths();
		
		$storing_paths["temp"] = $Field->getFilePath(true, false);
		$storing_paths["saved"] = $Field->getFilePath(false, false);
		
		$filename["temp"] = $Field->file_tmpname;
		$filename["saved"] = $Field->getValue();
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// settings manage
		
		$settings = array();
		$settings["display_paths"] = $display_paths;
		$settings["storing_paths"] = $storing_paths;
		$settings["max_size"] = $Field->file_max_size;
		$settings["allowed_mime"] = $Field->file_allowed_mime;
		$settings["avoid_temporary"] = $Field->file_avoid_temporary;
		
		$this->doEvent("onSettings", array(&$this, $Field, $tpl_id, &$settings));
		//var_dump("post", $settings); exit;
		
		// generate unique id
		$unique_id = serialize($settings);
        $inst = MD5($unique_id);
		
		/////////////////////////////////////////////////////////////////////////////////////////
		
		if (count($Field->parent) && is_subclass_of($Field->parent[0], "ffDetails_base"))
		{
			$suffix_factory_id = "[factory_id]";
			$suffix_control = "[file]";
			$suffix_name = "[name]";
			$suffix_tmpname = "[tmpname]";
			$suffix_delete = "[delete]";
		} 
		else 
		{
			$suffix_factory_id = "_factory_id";
			$suffix_control = "_file";
			$suffix_name = "_name";
			$suffix_tmpname = "_tmpname";
			$suffix_delete = "_delete";
		}

		$this->tpl[$tpl_id]->set_var("id", $id);
		
		$this->tpl[$tpl_id]->set_var("suffix_control", $suffix_control);
		$this->tpl[$tpl_id]->set_var("suffix_name", $suffix_name);
		$this->tpl[$tpl_id]->set_var("suffix_tmpname", $suffix_tmpname);
		$this->tpl[$tpl_id]->set_var("suffix_delete", $suffix_delete);
		$this->tpl[$tpl_id]->set_var("suffix_factory_id", $suffix_factory_id);

		$this->tpl[$tpl_id]->set_var("display_temp_view", $display_paths["temp"]["view"]);
		$this->tpl[$tpl_id]->set_var("display_temp_preview", $display_paths["temp"]["preview"]);
		$this->tpl[$tpl_id]->set_var("display_saved_view", $display_paths["saved"]["view"]);
		$this->tpl[$tpl_id]->set_var("display_saved_preview", $display_paths["saved"]["preview"]);
		
		if ($Field->uploadex_upload_script)
			$this->tpl[$tpl_id]->set_var("upload_script", '"' . $Field->uploadex_upload_script . '"');
		else
			$this->tpl[$tpl_id]->set_var("upload_script", "undefined");
		
		$this->tpl[$tpl_id]->set_var("data_src", $inst);
		
		$this->tpl[$tpl_id]->set_var("size_limit", $Field->file_max_size);

		$this->tpl[$tpl_id]->set_var("allow_replace", $Field->uploadex_allow_replace ? "true" : "false");
		$this->tpl[$tpl_id]->set_var("multi_allow_duplicates", $Field->uploadex_multi_allow_duplicates ? "true" : "false");
		$this->tpl[$tpl_id]->set_var("sortable", $Field->uploadex_sortable ? "true" : "false");
		if ($Field->uploadex_sortable)
		{
			$this->tpl[$tpl_id]->set_var("sortable_options", json_encode($Field->uploadex_sortable_options));
			$this->tpl[$tpl_id]->parse("Sect_sortable_options", false);
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("Sect_sortable_options", "");
		}
		
		$this->tpl[$tpl_id]->set_var("file_multi", $Field->file_multi ? "true" : "false");
		if ($Field->file_multi)
			$this->tpl[$tpl_id]->parse("SectMulti", false);
		else
			$this->tpl[$tpl_id]->set_var("SectMulti", "");
		
		$this->tpl[$tpl_id]->set_var("label", ffCommon_specialchars($Field->uploadex_label));
		
		if (is_array($Field->uploadex_icons_class) && count($Field->uploadex_icons_class)) 
		{
			foreach ($Field->uploadex_icons_class as $key => $value)
			{
				$this->tpl[$tpl_id]->set_var("uplex-class-" . $key, $value);
			}
		}
		
		if (is_array($Field->uploadex_properties) && count($Field->uploadex_properties)) 
		{
			foreach ($Field->uploadex_properties as $key => $value)
			{
				$this->tpl[$tpl_id]->set_var("uplex-properties-" . $key, $Field->getProperties($value));
			}
		}
		
		if ($Field->file_show_filename) {
			$this->tpl[$tpl_id]->parse("SectShowFileCaption", FALSE);
		} else {
			$this->tpl[$tpl_id]->set_var("SectShowFileCaption", "");
		}

		if ($Field->file_show_preview) {
			$this->tpl[$tpl_id]->parse("SectShowFileImage", FALSE);
		} else {
			$this->tpl[$tpl_id]->set_var("SectShowFileImage", "");
		}

		if ($Field->uploadex_onclick) {
			$this->tpl[$tpl_id]->set_var("onclick", $Field->uploadex_onclick);
		} else {
			$this->tpl[$tpl_id]->set_var("onclick", "undefined");
		}

        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($fld_value->ori_value));
        else
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($fld_value->getValue($Field->get_app_type(), $Field->get_locale())));

		/////////////////////////////////////////////////////////////////////////////////////////
		// save to session
		
		if ($session)
		{
			$ff_data["uploadex"][$inst] = $settings;
			set_session("ff", $ff_data);
		}
		else if (!is_file(FF_DISK_PATH . "/cache/uploadex/" . $inst))
		{
			@mkdir(FF_DISK_PATH . "/cache/uploadex", 0777, true);
			$tmp_buffer = "<?php\n\n";
			$tmp_buffer .= "\$ff_data['uploadex']['" . $inst . "'] = " . var_export($settings, true) . ";\n\n";
			file_put_contents(FF_DISK_PATH . "/cache/uploadex/" . $inst, $tmp_buffer, LOCK_EX);
		}

 		
		$this->tpl[$tpl_id]->set_var("SectImg", "");
		if ($Field->file_multi)
		{
			if (strlen($Field->value->getValue())) foreach (explode(",", $Field->value->getValue()) as $item)
			{
				$item_parts = explode("|", $item);
				
				$filename = $item_parts[0];
				$name = $item_parts[1];
				$type = $item_parts[2];
				
				$res = $this->doEvent("onShowFile", array($settings, $filename, $name, $type));
				$rc = end($res);
				if ($rc !== null)
				{
					$filename = $rc["filename"];
					$name = $rc["name"];
					$type = $rc["type"];
					$tmp_view = $rc["view"];
					$tmp_preview = $rc["preview"];
				}
				else
				{
					$tmp_fileinfo = pathinfo($filename);
					$tmp_view = $settings["display_paths"][$type]["view"];
					$tmp_view = str_replace("[_FILENAME_]", $filename, $tmp_view);
					$tmp_view = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_view);
					$tmp_view = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_view);
					$tmp_preview = $settings["display_paths"][$type]["preview"];
					$tmp_preview = str_replace("[_FILENAME_]", $filename, $tmp_preview);
					$tmp_preview = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_preview);
					$tmp_preview = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_preview);
				}
				
				$this->tpl[$tpl_id]->set_var("filename", $filename);
				$this->tpl[$tpl_id]->set_var("name", $name === null ? "undefined" : '"' . $name . '"');
				$this->tpl[$tpl_id]->set_var("type", $type);
				$this->tpl[$tpl_id]->set_var("view_url", $tmp_view);
				$this->tpl[$tpl_id]->set_var("preview_url", $tmp_preview);
			
				$this->tpl[$tpl_id]->parse("SectImg", true);
			}
		}
		else
		{
			if (strlen($Field->file_tmpname))
			{
				$filename = $Field->file_tmpname;
				$name = $Field->value->getValue();
				$type = "temp";
			} 
			else 
			{
				$filename = $Field->value->getValue();
				$name = null;
				$type = "saved";
			}
			
			$res = $this->doEvent("onShowFile", array($settings, $filename, $name, $type));
			$rc = end($res);
			if ($rc !== null)
			{
				$filename = $rc["filename"];
				$name = $rc["name"];
				$type = $rc["type"];
				$tmp_view = $rc["view"];
				$tmp_preview = $rc["preview"];
			}
			else
			{
				$tmp_fileinfo = pathinfo($filename);
				$tmp_view = $settings["display_paths"][$type]["view"];
				$tmp_view = str_replace("[_FILENAME_]", $filename, $tmp_view);
				$tmp_view = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_view);
				$tmp_view = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_view);
				$tmp_preview = $settings["display_paths"][$type]["preview"];
				$tmp_preview = str_replace("[_FILENAME_]", $filename, $tmp_preview);
				$tmp_preview = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_preview);
				$tmp_preview = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_preview);
			}
			
			$this->tpl[$tpl_id]->set_var("filename", $filename);
			$this->tpl[$tpl_id]->set_var("name", $name === null ? "undefined" : '"' . $name . '"');
			$this->tpl[$tpl_id]->set_var("type", $type);
			$this->tpl[$tpl_id]->set_var("view_url", $tmp_view);
			$this->tpl[$tpl_id]->set_var("preview_url", $tmp_preview);
			
			$this->tpl[$tpl_id]->parse("SectImg", true);
		}

       $this->tpl[$tpl_id]->parse("SectBinding", true);

		$this->tpl[$tpl_id]->set_var("filename", $Field->value->getValue());
		$this->tpl[$tpl_id]->set_var("tmpname", $Field->file_tmpname);
		
		$this->tpl[$tpl_id]->set_var("control", $Field->tpl[0]->rpparse("main", false));
        return $this->tpl[$tpl_id]->rpparse("SectControl", FALSE);
    }
	
	function get_component_headers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}
	
	function get_component_footers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectFooters", false);
	}
	
	function process_headers()
	{
		if (!isset($this->tpl["main"]))
			return;

		return $this->tpl["main"]->rpparse("SectHeaders", false);
	}
	
	function process_footers()
	{
		if (!isset($this->tpl["main"]))
			return;

		return $this->tpl["main"]->rpparse("SectFooters", false);
	}
}
