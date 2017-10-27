<?php
if (!class_exists("cm"))
	ffErrorHandler::raise("Theme restricted can be used only with CM. Change or Remove FF_DEFAULT_THEME from /config.php", E_USER_ERROR, null, get_defined_vars());

// --------------------------------------
//        libraries cache
$glob_libs = ffGlobals::getInstance("__ffTheme_libs__");
$glob_libs->libs = array();	
	
$ffcache_libs_success = false;
if (FF_THEME_RESTRICTED_LIBS_MEMCACHE)
{
	$cache = ffCache::getInstance(CM_CACHE_ADAPTER);
	$libs = $cache->get("__ffTheme_libs_restricted__", $ffcache_libs_success);
	if ($ffcache_router_success)
		$glob_libs->libs = unserialize($libs);
}

if (!$ffcache_libs_success)
{
	// PHP VERSION
	$cache_file = CM_CACHE_PATH . "/libs.php";
	if (FF_THEME_RESTRICTED_LIBS_CACHE && file_exists($cache_file))
	{
		$glob_libs->libs = include($cache_file);
	}
	else
	{
		cm_loadlibs($glob_libs->libs, __DIR__ . "/ffPage", "ffPage", "theme/ff");
		cm_loadlibs($glob_libs->libs, __DIR__, "ff", "theme");

		if (FF_THEME_RESTRICTED_LIBS_CACHE && !file_exists($cache_file))
		{
			@mkdir(CM_CACHE_PATH, 0777, true);
			$tmp_libs_var = var_export($glob_libs->libs, true);
			file_put_contents($cache_file, "<?php\n\nreturn $tmp_libs_var;\n\n", LOCK_EX);
		}
		
		if (FF_THEME_RESTRICTED_LIBS_MEMCACHE)
		{
			$this->cache->set("__ffTheme_libs_restricted__", null, serialize($glob_libs->libs));
		}
	}
	
	// JSON VERSION
	/*
	$cache_file = CM_CACHE_PATH . "/libs.cache";
	if (FF_THEME_RESTRICTED_LIBS_CACHE && file_exists($cache_file))
	{
		$glob_libs->libs = json_decode(file_get_contents(CM_CACHE_PATH . "/libs.cache"), true);
	}
	else
	{
		cm_loadlibs($glob_libs->libs, __DIR__ . "/ffPage", "ffPage", "theme/ff");
		cm_loadlibs($glob_libs->libs, __DIR__, "ff", "theme");


		if (FF_THEME_RESTRICTED_LIBS_CACHE && !file_exists($cache_file))
		{
			@mkdir(CM_CACHE_PATH, 0777, true);
			file_put_contents($cache_file, json_encode($glob_libs->libs));
		}
		
		if (FF_THEME_RESTRICTED_LIBS_MEMCACHE)
		{
			$this->cache->set("__ffTheme_libs_restricted__", null, serialize($glob_libs->libs));
		}
	}
	*/
}

/*echo "<pre>";
print_r($glob_libs->libs["theme/ff/ffPage"]);
exit;*/

/*
trait ffTheme_restricted_libs
{
    function loadlibs($obj_path) 
	{
	}
}

da vedere: decorator
*/

if (ffIsset($_REQUEST, "XHR_FORMAT") && $_REQUEST["XHR_FORMAT"] === "json")
{
	ffGrid::addEvent ("on_factory", "ffTheme_restricted_ffGrid_on_factory", ffEvent::PRIORITY_HIGH, 100, ffEvent::BREAK_NOT_EQUAL, null);
	function ffTheme_restricted_ffGrid_on_factory($page, $disk_path, $theme, $variant)
	{
		if (is_null($variant))
		{
			return cm_findCascadeClass("ffGrid", $theme, null, "ffGrid_json", false);
		}
		else
			return null;
	}
}

function ffTheme_restricted_get_libs($obj, $obj_path)
{
	$glob_libs = ffGlobals::getInstance("__ffTheme_libs__");
	if (ffIsset($glob_libs->libs, $obj_path))
		return $glob_libs->libs[$obj_path];
	else
		return null;
}

// --------------------------------------	

$cm = cm::getInstance();

ffPage::addEvent			("on_factory_done", "ffTheme_restricted_ffPage_set_events"		, ffEvent::PRIORITY_HIGH);
ffGrid::addEvent			("on_factory_done", "ffTheme_restricted_ffGrid_set_events"		, ffEvent::PRIORITY_HIGH);
ffRecord::addEvent		("on_factory_done", "ffTheme_restricted_ffRecord_set_events"	, ffEvent::PRIORITY_HIGH);
ffDetails::addEvent		("on_factory_done", "ffTheme_restricted_ffDetails_set_events"	, ffEvent::PRIORITY_HIGH);

function ffTheme_restricted_ffPage_set_events(ffPage_base $page)
{
	$page->addEvent("tplAddJs_not_found", function ($page, $tag, $params) {
		static $last_call;
		if ($tag === $last_call)
			ffErrorHandler::raise("Autoloader recursive inclusion", E_USER_ERROR, $page, get_defined_vars());
		$last_call = $tag;

		$glob_libs = ffGlobals::getInstance("__ffTheme_libs__");
		$tag_parts = explode(".", $tag);
				
		if (strpos($tag, "jquery.plugins.") === 0)
		{
			cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/library/plugins/jquery." . $tag_parts[2], "plugins/" . $tag, "jquery", false, true, true);
			$page->libsExtend($glob_libs->libs["jquery/plugins/" . $tag]);
			unset($page->js_loaded[$tag]);
			$page->tplAddJs($tag, $params);
			return true;
		} 
		elseif (strpos($tag, $tag_parts[0] . ".jquery.plugins.") === 0)
		{
			if (!ffIsset($glob_libs->libs, "theme/" . $tag_parts[0]))
			{
				cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/" . $tag_parts[0], $tag_parts[0], "theme", false, false);
				$page->libsExtend($glob_libs->libs["theme/" . $tag_parts[0]]);
			}
			
			cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/" . $tag_parts[0] . "/javascript/plugins/jquery." . $tag_parts[3], "plugins/" . $tag, $tag_parts[0] . "/jquery", false, true, true);
			$page->libsExtend($glob_libs->libs[$tag_parts[0] . "/jquery/plugins/" . $tag]);
			unset($page->js_loaded[$tag]);
			$page->tplAddJs($tag, $params);
			return true;
		} 
		elseif (strpos($tag, "library.") === 0)
		{
			cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/library/" . $tag_parts[1], $tag, "library", false, true, true);
			$page->libsExtend($glob_libs->libs["library/" . $tag]);
			unset($page->js_loaded[$tag]);
			$page->tplAddJs($tag, $params);
			return true;
		}
		/*elseif (strpos($tag, $tag_parts[0] . ".jquery.") === 0)
		{
			if (!ffIsset($glob_libs->libs, "theme/" . $tag_parts[0]))
			{
				cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/" . $tag_parts[0], $tag_parts[0], "theme", false, false);
				$page->libsExtend($glob_libs->libs["theme/" . $tag_parts[0]]);
			}
			
			cm_loadlibs($glob_libs->libs, FF_THEME_DISK_PATH . "/" . $tag_parts[0] . "/javascript/plugins/jquery." . $tag_parts[2], "plugins/" . $tag_parts[0] . ".jquery.plugins." . $tag_parts[2], $tag_parts[0] . "/jquery", false, true, true);
			$page->libsExtend($glob_libs->libs[$tag_parts[0] . "/jquery/plugins/" . $tag_parts[0] . ".jquery.plugins." . $tag_parts[2]]);
			unset($page->js_loaded[$tag_parts[0] . ".jquery.plugins." . $tag_parts[2]]);
			$page->tplAddJs($tag_parts[0] . ".jquery.plugins." . $tag_parts[2], $params);
			return true;
		} */
	});
}

function ffTheme_restricted_ffGrid_set_events(ffGrid_base $grid)
{
	$grid->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffTheme_restricted_ffRecord_set_events(ffRecord_base $record)
{
	$record->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffTheme_restricted_ffDetails_set_events(ffDetails_base $details)
{
	$details->addEvent("onDialog", "ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
}

function ffComponent_onDialog($oComponent, $returnurl, $type, $title, $message, $cancelurl, $confirmurl, $dialog_path)
{
	return ffDialog($returnurl, $type, $title, $message, $cancelurl, $confirmurl, $oComponent->parent[0]->site_path . $oComponent->parent[0]->page_path . "/dialog?" . $oComponent->parent[0]->get_globals());
}

function ffTheme_html_construct(ffPage_html &$page, $theme)
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
				if (strlen($priority) && !is_numeric($priority))
					$priority = constant("cm::LAYOUT_PRIORITY_" . strtoupper($priority));
				elseif (!is_numeric($priority) || $priority > cm::LAYOUT_PRIORITY_FINAL || $priority > cm::LAYOUT_PRIORITY_TOPLEVEL)
					$priority = cm::LAYOUT_PRIORITY_HIGH;
					
				$index = (int)$value->index;
			
				$page->tplAddCss($tag, array(
					"file" => $file
					, "path" => $path
					, "exclude_compact" => $exclude_compact
					, "priority" => $priority
					, "index" => $index
				));
			}
		}
	}
	
	if (isset($registry->themes[$theme]->default_js) && count($registry->themes[$theme]->default_js->children()))
	{
		foreach ($registry->themes[$theme]->default_js->children() as $key => $value)
		{
			$path = (string)$value->path;
			$tag = $key;
			$file = (string)$value->file;
			if (
					!isset($value->exclude_compact) ||
					(isset($value->exclude_compact) && (string)$value->exclude_compact == "false")
			)
				$exclude_compact = false;
			else 
				$exclude_compact = true;	

            $allowed_path = $value->allowed_path;
            if (isset($allowed_path) && count($allowed_path))
			{
                $block_item = true;
                foreach($allowed_path->children() AS $allowed_path_value) 
				{
                    if (strlen($allowed_path_value) && strpos($actual_path, trim($allowed_path_value)) === 0)
					{
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

			$priority = (string)$value->priority;
			if ($priority === "")
				$priority = cm::LAYOUT_PRIORITY_HIGH;
			else if (!is_numeric($priority))
				$priority = constant("cm::LAYOUT_PRIORITY_" . $priority);
			
			$index = (int)$value->index;
			
			$page->tplAddJs($tag, array(
				"file" => $file
				, "path" => $path
				, "exclude_compact" => $exclude_compact
				, "priority" => $priority
				, "index" => $index
			));
		}
	}

    if (isset($registry->themes[$theme]->default_cdn) && count($registry->themes[$theme]->default_cdn->children()))
    {
        foreach ($registry->themes[$theme]->default_cdn->children() as $key => $value)
        {
            $url = (string)$value->url;
            if (ffIsset($page->libraries, $key) && ffIsset($page->libraries[$key], "version"))
                $url = str_replace("[VERSION]", $page->libraries[$key]["version"], $url);

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
        	switch ($module_key)
        	{
        		case "restricted":
        			mod_restricted_load_config($module_value);
        			break;
        			
        		default:
		            if (isset($module_value) && count($module_value->children()))
		            {
		                foreach ($module_value->children() as $key => $value)
		                {
                			if (!count($value))
                    			$cm->modules[(string) $module_key][(string) $key] = true;
		                }
		            }
        	}
        }
    }
}

if ($cm->isXHR())
{
	if (!isset($_REQUEST["XHR_CTX_ID"]))
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
$ff_global_setting["ffButton_html"]["jsaction"] = "ff.ajax.ctxDoAction('[[XHR_CTX_ID]]', '[[frmAction]]', '[[component_action]]');";

// -------------------------------------------------------------
//  parti di codice da aggiungere solo in presenza di un dialog
// -------------------------------------------------------------
if (!ffIsset($_REQUEST, "XHR_CTX_TYPE") || $_REQUEST["XHR_CTX_TYPE"] !== "dialog")
	return;

ffRecord::addEvent ("on_factory", "ffTheme_restricted_ffRecord_on_factory", ffEvent::PRIORITY_HIGH, 100, ffEvent::BREAK_NOT_EQUAL, null);
function ffTheme_restricted_ffRecord_on_factory($page, $disk_path, $theme, $variant)
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
	$record->skip_events_on_error = false;
}

function ffRecord_dialog_on_done_action_prepare_results(ffRecord_base $record, $frmAction)
{
	$cursor = $record->cursor_dialog && ffIsset($_REQUEST, "cursor[id]");
	switch (isset($record->default_actions[$record->frmAction]) ? $record->default_actions[$record->frmAction] : $record->frmAction)
	{
		case "confirmdelete":
			$record->json_result["close"] = !$cursor;
			$record->json_result["refresh"] = true;
			$record->json_result["resources"] = $record->resources;
			if ($cursor)
				$record->json_result["cursor_reload"] = $_REQUEST["XHR_CTX_ID"];
			break;

		case "insert":
			$record->json_result["close"] = true;
			$record->json_result["refresh"] = true;
			if (is_array($record->key_fields) && count($record->key_fields))
				$record->json_result["insert_id"] = end($record->key_fields)->value->getValue();
			$record->json_result["resources"] = $record->resources;
			break;

		case "update":
			$record->json_result["close"] = !$cursor;
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

function ffTheme_restricted_icon($class)
{
	if($class == "UserRand")
		$class = "NewIcon_user". rand(1, 9);
	else if($class == "AdminRand")
		$class = "NewIcon_administrator". rand(1, 3);
	
	$icon = "<hr class=\"NewIcon " . $class . "\" />";
        
    return $icon;
}
