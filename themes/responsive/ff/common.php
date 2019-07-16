<?php
//require_once(FF_DISK_PATH . "/library/FF/common/framework-css.php");

$cm = cm::getInstance();

ffGrid::addEvent			("on_factory_done", "ffTheme_responsive_ffGrid_set_events"		, ffEvent::PRIORITY_HIGH);
ffRecord::addEvent		("on_factory_done", "ffTheme_responsive_ffRecord_set_events"	, ffEvent::PRIORITY_HIGH);
ffDetails::addEvent		("on_factory_done", "ffTheme_responsive_ffDetails_set_events"	, ffEvent::PRIORITY_HIGH);
/***
 * FRAMEWORK CSS Load JS / CSS Base
 */
$cm->addEvent("on_layout_init", "ffPage_on_layout_init");


function ffTheme_responsive_ffGrid_set_events(ffGrid_base $grid)
{
	$grid->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffTheme_responsive_ffRecord_set_events(ffRecord_base $record)
{
	$record->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffTheme_responsive_ffDetails_set_events(ffDetails_base $details)
{
	$details->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffComponent_onDialog($oComponent, $returnurl, $type, $title, $message, $cancelurl, $confirmurl, $dialog_path)
{
	return ffDialog($returnurl, $type, $title, $message, $cancelurl, $confirmurl, $oComponent->parent[0]->site_path . $oComponent->parent[0]->page_path . "/dialog?" . $oComponent->parent[0]->get_globals());
}

function ffTheme_html_construct(&$page, $theme)
{
    $registry = ffGlobals::getInstance("_registry_");
    $cm = cm::getInstance();

    $actual_path = $cm->path_info . $cm->real_path_info;

	if (!isset($registry->themes))
	{
		$registry->themes = array();
	}
	if (!isset($registry->themes[$theme]) && is_file(FF_DISK_PATH . "/themes/" . $theme . "/theme_settings.xml"))
	{
		$registry->themes[$theme] = new SimpleXMLElement(FF_DISK_PATH . "/themes/" . $theme . "/theme_settings.xml", null, true);
	}

	if (!($theme == cm_getMainTheme() && ($cm->layout_vars["ignore_defaults_main"] || (isset($registry->ignore_defaults_main) && $registry->ignore_defaults_main)))) {
		if (isset($registry->themes[$theme]->default_css) && count($registry->themes[$theme]->default_css->children()))
		{
			foreach ($registry->themes[$theme]->default_css->children() as $key => $value)
			{
				$path = (string)$value->path;
				$tag = $key;
				$file = (string)$value->file;
				$priority = (string)$value->priority;
				if (!isset($value->exclude_compact) ||
					(isset($value->exclude_compact) && (string)$value->exclude_compact == "false")
				) {
					$exclude_compact = false;
				} else {
					$exclude_compact = true;	
				}

                $allowed_path = $value->allowed_path;
                if (isset($allowed_path) && count($allowed_path)) {
                    $block_item = true;
                    foreach($allowed_path->children() AS $allowed_path_value) {
                        if (strlen($allowed_path_value) && strpos($actual_path, trim($allowed_path_value)) === 0) {
                            $block_item = false;
                            break;
                        }
                    }
                    if ($block_item)
                        continue;
                }

				if (!strlen($path))
					$path = "/themes/" . $theme . "/css";
				if (!strlen($file))
					$file = null;
				if (!strlen($priority))
					$priority = "top";

				$page->tplAddCss($tag, $file, $path, "stylesheet", "text/css", false, false, null, $exclude_compact, $priority);
			}
		}
	}

	if (!$cm->layout_vars["exclude_ff_js"] && isset($registry->themes[$theme]->default_js) && count($registry->themes[$theme]->default_js->children()))
	{
		foreach ($registry->themes[$theme]->default_js->children() as $key => $value)
		{
			$path = (string)$value->path;
			$tag = $key;
			$file = (string)$value->file;
			$priority = (string)$value->priority;
			if (!isset($value->exclude_compact) ||
				(isset($value->exclude_compact) && (string)$value->exclude_compact == "false")
			) {
				$exclude_compact = false;
			} else {
				$exclude_compact = true;	
			}

            $allowed_path = $value->allowed_path;
            if (isset($allowed_path) && count($allowed_path)) {
                $block_item = true;
                foreach($allowed_path->children() AS $allowed_path_value) {
                    if (strlen($allowed_path_value) && strpos($actual_path, trim($allowed_path_value)) === 0) {
                        $block_item = false;
                        break;
                    }
                }
                if ($block_item)
                    continue;
            }
			
			if (!strlen($path))
				$path = "/themes/" . $theme . "/javascript";
	            
			if (!strlen($file))
				$file = null;

			if (!strlen($priority))
				$priority = "top";

			$page->tplAddJs($tag, $file, $path, false, false, null, $exclude_compact, $priority);
		}

		$page->tplAddJs("ff.ffPage", "ffPage.js", FF_THEME_DIR . "/library/ff");
	}
    if (isset($registry->themes[$theme]->default_cdn) && count($registry->themes[$theme]->default_cdn->children()))
    {
        foreach ($registry->themes[$theme]->default_cdn->children() as $key => $value)
        {
            $url = (string)$value->url;
            if (array_key_exists($key, $page->cdn_version))
                $url = str_replace("[VERSION]", implode(".", $page->cdn_version[$key]), $url);

            $name = $key;
            $type = (string)$value->type;

            $allowed_path = $value->allowed_path;
            if (isset($allowed_path) && count($allowed_path)) {
                $block_item = true;
                foreach($allowed_path->children() AS $allowed_path_value) {
                    if (strlen($allowed_path_value) && strpos($actual_path, trim($allowed_path_value)) === 0) {
                        $block_item = false;
                        break;
                    }
                }
                if ($block_item)
                    continue;
            }

            if ($type == "css")
                $page->override_css[$key] = $url;
            elseif ($type == "js")
                $page->override_js[$key] = $url;
        }
    }

    if (isset($registry->themes[$theme]->default_jqueryui_theme) && count($registry->themes[$theme]->default_jqueryui_theme->children()))
    {
        $jqueryui_theme_bypath = "";
        $jqueryui_theme_general = "";

        foreach ($registry->themes[$theme]->default_jqueryui_theme->children() as $key => $value)
        {
            $allowed_path = $value->allowed_path;
            if (isset($allowed_path) && count($allowed_path)) {
                $block_item = true;     
                foreach($allowed_path->children() AS $allowed_path_value) {
                    if (strlen($allowed_path_value) && strpos($actual_path, trim($allowed_path_value)) === 0) {
                        $block_item = false;
                        break;
                    }
                }
                if ($block_item)
                    continue;
            }

            $attrs = $value->attributes();
            if (strlen((string)$attrs["name"])) {
                $theme_ui = (string)$attrs["name"];
            } else {
                $theme_ui = $key;
            }
            
            if (strlen($path)) {
                $jqueryui_theme_bypath = $key;
            } else {
                $jqueryui_theme_general = $key;
            }
        }  
        if (strlen($jqueryui_theme_bypath)) {
            $page->jquery_ui_theme = $jqueryui_theme_bypath;
        } elseif (strlen($jqueryui_theme_general)) {
            $page->jquery_ui_theme = $jqueryui_theme_general;
        }
    }

    if (isset($registry->themes[$theme]->modules) && count($registry->themes[$theme]->modules->children()))
    {
        foreach ($registry->themes[$theme]->modules->children() as $module_key => $module_value)
        {
            if (isset($module_value) && count($module_value->children()))
            {
                foreach ($module_value->children() as $key => $value)
                {
                    $cm->modules[(string) $module_key][(string) $key] = true;
                }
            }
        }
    }
}

function ffPage_on_layout_init($oPage, $layout_vars) {
	$framework_css = Cms::getInstance("frameworkcss")->getFramework(FF_THEME_FRAMEWORK_CSS);
	$font_icon = Cms::getInstance("frameworkcss")->getFontIcon(FF_THEME_FONT_ICON);

	if(!$oPage->isXHR()) {
		if(is_array($font_icon)) {
			if(strlen($font_icon["css"])) {
				$oPage->tplAddCss($font_icon["name"], basename($font_icon["css"]), ffCommon_dirname($font_icon["css"]), "stylesheet", "text/css", false, false, null, false, "top");
			}
		}
		if(is_array($framework_css)) {
			if(strlen($framework_css["params"]["css"])) {
				if(is_file($oPage->getThemeDir() . "/css/" . $framework_css["name"] . ".css"))
					$oPage->tplAddCss($framework_css["name"], $framework_css["name"] . ".css", $oPage->getThemePath(false) . "/css", "stylesheet", "text/css", false, false, null, false, "top");
				else
					$oPage->tplAddCss($framework_css["name"], basename($framework_css["params"]["css"]), ffCommon_dirname($framework_css["params"]["css"]), "stylesheet", "text/css", false, false, null, false, "top");
			}
			if(strlen($framework_css["theme"]["css"])) {
				if(is_file($oPage->getThemeDir() . "/css/" . $framework_css["theme"]["name"] . ".css"))
					$oPage->tplAddCss($framework_css["theme"]["name"], $framework_css["theme"]["name"] . ".css", $oPage->getThemePath(false) . "/css", "stylesheet", "text/css", false, false, null, false, "top");
				else
					$oPage->tplAddCss($framework_css["theme"]["name"], basename($framework_css["theme"]["css"]), ffCommon_dirname($framework_css["theme"]["css"]), "stylesheet", "text/css", false, false, null, false, "top");
			}

			if(strlen($framework_css["params"]["js"])) {
				if(is_file($oPage->getThemeDir() . "/javascript/" . $framework_css["name"] . ".js"))
					$oPage->tplAddJs($framework_css["name"], $framework_css["name"] . ".js", $oPage->getThemePath(false) . "/javascript", false, false, null, false, "top");
				else
					$oPage->tplAddJs($framework_css["name"], basename($framework_css["params"]["js"]), ffCommon_dirname($framework_css["params"]["js"]), false, false, null, false, "top");
			}
			if(strlen($framework_css["params"]["js_init"])) {
				if(is_file($oPage->getThemeDir() . "/javascript/" . $framework_css["name"] . "-init.js"))
					$oPage->tplAddJs($framework_css["name"], $framework_css["name"] . "-init.js", $oPage->getThemePath(false) . "/javascript", false, false, null, false, "bottom");
				else
					$oPage->tplAddJs($framework_css["name"] . ".init", null, null, false, false, $framework_css["params"]["js_init"], false, "bottom");
			}
		}
	}
}

// -------------------------------------------------------------
//  parti di codice da aggiungere solo in presenza di un dialog
// -------------------------------------------------------------
if ($cm->isXHR())
{
	if (!isset($_REQUEST["XHR_DIALOG_ID"]))
	{
		$cm->layout_vars["page"] = "XHR";
		return;
	}
}
else
	return;

// -------------------------------------------------------------------------------
//  cambia i button per agire all'interno dei contesti ajax, qualsiasi tipo siano
// -------------------------------------------------------------------------------

global $ff_global_setting;
$ff_global_setting["ffButton_html"]["jsaction"] = "ff.ffPage.dialog.doAction('[[XHR_DIALOG_ID]]', '[[frmAction]]', '[[component_action]]');";

// -------------------------------------------------------------
//  parti di codice da aggiungere solo in presenza di un dialog
// -------------------------------------------------------------
ffRecord::addEvent ("on_factory", "ffTheme_responsive_ffRecord_on_factory", ffEvent::PRIORITY_HIGH, 100, ffEvent::BREAK_NOT_EQUAL, null);
function ffTheme_responsive_ffRecord_on_factory($page, $disk_path, $theme, $variant)
{
	if (is_null($variant))
	{
		return cm_findCascadeClass("ffRecord", $theme, null, "ffRecord_dialog", false);
	}
	else
		return null;
}

ffRecord::addEvent("on_factory_done", "ffRecord_set_events_dialog", ffEvent::PRIORITY_HIGH);
function ffRecord_set_events_dialog(ffRecord_base $record)
{
	$record->addEvent("on_done_action", "ffRecord_dialog_on_done_action_prepare_results", ffEvent::PRIORITY_HIGH);
	$record->addEvent("on_done_action", "ffRecord_dialog_on_done_action_output_results", ffEvent::PRIORITY_FINAL);
	//$record->skip_events_on_error = false; //non e applicabile. Skippa tutti i campi required e se usi un on_do_action non ce la garanzia che i campi required siano rispettati o i validatori
}

function ffRecord_dialog_on_done_action_prepare_results(ffRecord_base $record, $frmAction)
{
	switch (isset($record->default_actions[$record->frmAction]) ? $record->default_actions[$record->frmAction] : $record->frmAction)
	{
		case "confirmdelete":
			$record->json_result["close"] = true;
			$record->json_result["refresh"] = true;
			$record->json_result["resources"] = $record->resources;
			break;

		case "insert":
			$record->json_result["close"] = true;
			$record->json_result["refresh"] = true;
			if (is_array($record->key_fields) && count($record->key_fields))
				$record->json_result["insert_id"] = end($record->key_fields)->value->getValue();
			$record->json_result["resources"] = $record->resources;
			break;

		case "update":
			$record->json_result["close"] = true;
			$record->json_result["refresh"] = true;
			if ($record->db[0]->affectedRows())
				$record->json_result["insert_id"] = end($record->key_fields)->value->getValue();
			$record->json_result["resources"] = $record->resources;
			break;
	}
	
	return false;
}
function ffRecord_dialog_on_done_action_output_results(ffRecord_base $record, $frmAction)
{
	cm::jsonParse($record->json_result);
	exit;
}

function ffTheme_restricted_icon($class) {
	$arrClass = explode("_", $class);

	return Cms::getInstance("frameworkcss")->get($arrClass[1], "icon-tag");
}
/*
function ffTheme_responsive_oPage_getTemplateDir(ffPage_base $oPage)
{
	if ($oPage->template_dir !== null)
		return $oPage->template_dir;
	else
	{
		die($oPage->template_file);
		return cm_findCascadeTemplate("ffPage", $oPage->getTheme(), $oPage->template_file);
	}
}*/
