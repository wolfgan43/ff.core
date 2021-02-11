<?php

ffGlobals::getInstance("ff")->events->addEvent("files_preprocess", function ($comp, $field, $previous_ret) {
	if ($field->widget !== "uploadex")
		return $previous_ret; // preserve original return value (if one), usually null (normal processing)
	
	if (strlen($field->file_tmpname) && strpos($field->file_tmpname, "/") === 0)
	{
		return false; // skip this file
	}
	else
	{
		return $previous_ret; // preserve original return value (if one), usually null (normal processing)
	}
});//, ffEvent::PRIORITY_HIGH);

ffPage::addEvent("on_factory_done", function($page) {
	$page->addEvent("on_widget_loaded", function ($page_ref, $name, $path, $ref) {
		if ($name === "uploadex")
		{
			ffWidget_uploadex::_addEvent("onSettings", "uploadex_automedia_paths");
			ffWidget_uploadex::_addEvent("onShowFile", "uploadex_automedia_show");
		}
	});
});

function uploadex_automedia_show(&$widget, &$Field, $settings, $filename, $name, $type) {
	if (!$Field->widget_options["automedia_show"] && !$Field->widget_options["automedia_browse"])
		return;
	
	if (strpos($filename, "/") === 0)
	{
		return array(
			"filename" => $filename,
			"name" => $name,
			"type" => "saved",
			"view" => "/media" . $filename,
			"preview" => "/media/100x100" . $filename
			//"preview" => preg_replace("/\/media\//", "/media/100x100/", $filename, 1)
		);
	}
}

function uploadex_automedia_paths (&$widget, &$Field, $tpl_id, &$settings) {
	if (!$Field->widget_options["automedia_show"] && !$Field->widget_options["automedia_browse"])
		return;
	
	if (
			   strlen($settings["display_paths"]["temp"]["view"])
			|| strlen($settings["display_paths"]["temp"]["preview"])
			|| strlen($settings["display_paths"]["saved"]["view"])
			|| strlen($settings["display_paths"]["saved"]["preview"])
		)
		return;

	$thumb = null;
	if(is_array($Field->file_thumb))
		$thumb = $Field->file_thumb["width"] . "x" . $Field->file_thumb["height"];
	else if (strlen($Field->file_thumb))
		$thumb = $Field->file_thumb;

	$base_path = $Field->getFileBasePath();
	
	// temp
	$storing_path = $Field->getFilePath();
	$folder_tmp = str_replace($base_path, "", $storing_path);

	if(!strlen($folder_tmp))
		$folder_tmp = "/";

	$settings["display_paths"]["temp"]["preview"] = "/media" . $folder_tmp . "/" . ($thumb ? "[_FILEONLYNAME_]-" . $thumb . "[_FILEONLYEXT_]" : "[_FILENAME_]");
	$settings["display_paths"]["temp"]["view"] = "/media" . $folder_tmp . "/[_FILENAME_]";

	// saved
	$storing_path = $Field->getFilePath(false);
	$folder_saved = str_replace($base_path, "", $storing_path);

	if(!strlen($folder_saved))
		$folder_saved = "/";

	$settings["display_paths"]["saved"]["preview"] = "/media" . $folder_saved . "/" . ($thumb ? "[_FILEONLYNAME_]-" . $thumb . "[_FILEONLYEXT_]" : "[_FILENAME_]");
	$settings["display_paths"]["saved"]["view"] = "/media" . $folder_saved . "/[_FILENAME_]";
	
	// dialog
	if($Field->file_show_edit) 
	{
	    static $loaded_dialog_fields = null;

		$file_modify_path = FF_SITE_PATH . ffMedia::MODIFY_PATH . "?key=" . $Field->file_modify_referer . "&path=";
		
		$dialog_id = $Field->id . "_media";
		if($Field->file_modify_dialog === true && !$loaded_dialog_fields[$dialog_id])
		{
			$loaded_dialog_fields[$dialog_id] = true;
			if (count($loaded_dialog_fields) === 1)
			{
				$Field->getParentPage()->tplAddJs("uploadexdialog", array(
					"embed" => <<<EOD
					ff.fn.uploadexDialogOpen = function (params, el) {
						if (el.type === "saved") {
							ff.ffPage.dialog.doOpen('$dialog_id', '$file_modify_path' + (el.name.indexOf('/') !== 0 ? encodeURIComponent('$folder_saved' + '/' + el.name) : el.name));
						} else {
							//ff.ffPage.dialog.doOpen('$dialog_id', '$file_modify_path' + (el.filename.indexOf('/') !== 0 ? encodeURIComponent('$folder_tmp' + '/' + el.filename) : el.filename));
							alert("Sarà possibile personalizzare l'immagine dopo il salvataggio");
						}
						return false; // cancel default event
					}
EOD
				));
			}
			if ($Field->uploadex_onclick === null)
				$Field->uploadex_onclick = "ff.fn.uploadexDialogOpen";
			
			$widget->tpl[$tpl_id]->set_var("param_name", "file_modify_referer");
			$widget->tpl[$tpl_id]->set_var("param_value", '"' . $Field->file_modify_referer . '"');
			$widget->tpl[$tpl_id]->parse("SectParam", true);
			
			
			//$Field->file_modify_dialog = $dialog_id;

			$params = array(
				"title" 				=> ffTemplate::_get_word_by_code("ffField_modify") . " " . $Field->label
				, "class" 				=> null
				, "width" 				=> null
				, "height" 				=> null
				, "type" 				=> null
			);


			$Field->parent_page[0]->widgetLoad("dialog");
			$Field->parent_page[0]->widgets["dialog"]->process(
				$dialog_id
				, array(
					"title"          	=> $params["title"]
					, "tpl_id"        	=> null
					, "width"        	=> $params["width"]
					, "height"        	=> $params["height"]
					, "dialogClass"     => $params["class"]
					, "type"			=> $params["type"]
				)
				, $Field->parent_page[0]
			);
		}
	}
}

function mod_keeng_enforce_config ($params) {
	if (!$params["dim_x"] && !$params["dim_y"])
		return;
	
	$CM_CACHE_DISK_PATH = __PRJ_DIR__ . "/cache/keeng-dimensions.php";
	
	if (file_exists($CM_CACHE_DISK_PATH))
	{
		$allowed_res = require($CM_CACHE_DISK_PATH);
	}
	else
	{
		if (!defined("BLOCK_COLUMN"))
			require(__PRJ_DIR__ . "/conf/setting_blockcomposer.php");

		// default dimensions
		$allowed_res = array(
			//x
            "1080" => array(
                //y
                "0" => true
            ),
            "480" => array(
                //y
                "0" => true
            ),
			"100" => array(
				//y
				"100" => true
			),
			"48" => array(
				//y
				"48" => true
			),
		);

		$tmp = explode(",", BLOCK_COLUMN);
		foreach ($tmp as $row)
		{
			$cols = explode("-", $row);

			mod_keeng_get_allowed($cols, IMG_XL_21_9, $allowed_res);

			// Dimensioni Immagini Grandi FULL
			mod_keeng_get_allowed($cols, IMG_XL_21_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_16_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_4_3, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_1_1, $allowed_res);

			// Dimensioni Immagini Grandi FULL
			mod_keeng_get_allowed($cols, IMG_LG_21_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_16_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_4_3, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_1_1, $allowed_res);

			// Dimensioni Immagini Medie FULL
			mod_keeng_get_allowed($cols, IMG_MD_21_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_16_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_4_3, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_1_1, $allowed_res);

			// Dimensioni Immagini Piccole FULL
			mod_keeng_get_allowed($cols, IMG_SM_21_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_16_9, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_4_3, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_1_1, $allowed_res);

			// Dimensioni Immagini Grandi CONTAINER
			mod_keeng_get_allowed($cols, IMG_XL_21_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_16_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_4_3_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_XL_1_1_c, $allowed_res);

			// Dimensioni Immagini Grandi CONTAINER
			mod_keeng_get_allowed($cols, IMG_LG_21_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_16_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_4_3_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_LG_1_1_c, $allowed_res);

			// Dimensioni Immagini Medie CONTAINER
			mod_keeng_get_allowed($cols, IMG_MD_21_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_16_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_4_3_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_MD_1_1_c, $allowed_res);

			// Dimensioni Immagini Piccole CONTAINER
			mod_keeng_get_allowed($cols, IMG_SM_21_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_16_9_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_4_3_c, $allowed_res);
			mod_keeng_get_allowed($cols, IMG_SM_1_1_c, $allowed_res);		
		}
		
		$db = ffDB_Sql::factory();
		$sSQL = "SELECT * FROM `cm_showfiles_modes`";
		$db->query($sSQL);
		if ($db->nextRecord())
		{
			do
			{
				if (!$db->record["dim_x"] || !$db->record["dim_y"])
					continue;
				
				$allowed_res[$db->record["dim_x"]][$db->record["dim_y"]] = true;
			} while ($db->nextRecord());
		}
		
		@mkdir(basename($CM_CACHE_DISK_PATH), 0777, true);
		$tmp_var = var_export($allowed_res, true);
		file_put_contents($CM_CACHE_DISK_PATH, "<?php\n\nreturn $tmp_var;\n\n", LOCK_EX);
	}
	
	if (!$params["dim_y"])
	{
		$allowed = isset($allowed_res[$params["dim_x"]]);
	}
	else if (!$params["dim_x"])
	{
		$allowed = false;
		foreach ($allowed_res as $row)
		{
			if (isset($row[$params["dim_y"]]))
				$allowed = true;
		}
	}
	else
	{
		$allowed = isset($allowed_res[$params["dim_x"]]) && isset($allowed_res[$params["dim_x"]][$params["dim_y"]]);
	}
	
	if (!$allowed)
	{
		http_response_code(404);
		exit;
	}
	/*var_dump($allowed, $params, $allowed_res);
	exit;*/
}

function mod_keeng_get_allowed ($cols, $dim_const, &$allowed_res) {
		$dims = mod_keeng_get_dimension($dim_const);
		
		$processed = array();
		
		foreach ($cols as $col)
		{
			if (isset($processed[$col]))
				continue;
			
			$newx = round($dims["x"] / 12 * intval($col), 0);
			$newy = round($dims["y"] / 12 * intval($col), 0);
			
			$allowed_res[$newx][$newy] = true;
			
			$processed[$col] = true;
		}
}

function mod_keeng_get_dimension($dim)
{
	$tmp = explode("-", $dim);
	return array("x" => intval($tmp[0]), "y" => intval($tmp[1]));
}