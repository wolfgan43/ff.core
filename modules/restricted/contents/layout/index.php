<?php
if (isset($cm->modules["restricted"]["layout_bypath"]) && count($cm->modules["restricted"]["layout_bypath"]))
{
	foreach ($cm->modules["restricted"]["layout_bypath"] as $key => $value)
	{
		if  (strpos($cm->path_info, $key) === 0)
		{
			if (is_array($value))
			{
				foreach ($value as $section => $option)
				{
					$cm->modules["restricted"]["options"]["layout"][$section] = $option;
				}
			}
			elseif ($value == "nolayout")
			{
				reset($cm->modules["restricted"]["layout_bypath"]);
				return;
			}
		}
	}
	reset($cm->modules["restricted"]["layout_bypath"]);
}

if ($cm->layout_vars["layer"] === null)
{
	if (strlen($cm->modules["restricted"]["options"]["layout"]["layer"]))
		$cm->oPage->layer = $cm->modules["restricted"]["options"]["layout"]["layer"];
	else
		$cm->oPage->layer = "restricted";
}

$cm->oPage->addEvent("getLayerDir", "mod_restricted_getLayerDir", ffEvent::PRIORITY_HIGH, -100, ffEvent::BREAK_NOT_EQUAL, null);
function mod_restricted_getLayerDir(ffPage_base $oPage, $file, $lastres = null)
{
	if ($oPage->layer_dir === null)
		return ffCommon_dirname(cm_moduleCascadeFindTemplateByPath("restricted", "/layouts/" . $file, $oPage->getTheme(), false));
	else
		return null;
}

$cm->oPage->addEvent("getLayoutDir", "mod_restricted_getLayoutDir", ffEvent::PRIORITY_HIGH, -100, ffEvent::BREAK_NOT_EQUAL, null);
function mod_restricted_getLayoutDir(ffPage_base $oPage, $file, $lastres = null)
{
	return ffCommon_dirname(cm_moduleCascadeFindTemplateByPath("restricted", "/layouts/" . $file, $oPage->getTheme(), false));
}

$cm->oPage->addEvent("on_tpl_layer_loaded", "mod_restricted_on_tpl_layer_loaded", ffEvent::PRIORITY_LOW);

if (isset($cm->modules["restricted"]["sections"]["top"]))
{
	foreach ($cm->modules["restricted"]["sections"]["top"] as $key => $value)
	{
		if (strpos($key, "__") === 0)
			continue;
		
		if (!isset($cm->modules["restricted"]["options"]["layout"][$key]) || strlen($cm->modules["restricted"]["options"]["layout"][$key]))
		{
			if (!isset($cm->oPage->sections[$key]))
			{
				$cm->oPage->addSection($key);
				if (!isset($cm->modules["restricted"]["options"]["layout"][$key]))
					$cm->oPage->sections[$key]["name"] = "restricted";
				else
					$cm->oPage->sections[$key]["name"] = $cm->modules["restricted"]["options"]["layout"][$key];
			}

			$default = isset($cm->modules["restricted"]["sections"]["top"]->$key->__attributes["default"]) && $cm->modules["restricted"]["sections"]["top"]->$key->__attributes["default"] == "true";
			$cm->oPage->sections[$key]["events"]->addEvent("on_load_template", "mod_restricted_cm_on_load_topbar", null, 0, null, null, array($key, $default));
		}
	}
	reset($cm->modules["restricted"]["sections"]["top"]);
}

if (isset($cm->modules["restricted"]["sections"]["nav"]))
{
	foreach ($cm->modules["restricted"]["sections"]["nav"] as $key => $value)
	{
		if (strpos($key, "__") === 0)
			continue;
		
		if (!isset($cm->modules["restricted"]["options"]["layout"][$key]) || strlen($cm->modules["restricted"]["options"]["layout"][$key]))
		{
			if (!isset($cm->oPage->sections[$key]))
			{
				$cm->oPage->addSection($key);
				if (!isset($cm->modules["restricted"]["options"]["layout"][$key]))
					$cm->oPage->sections[$key]["name"] = "restricted";
				else
					$cm->oPage->sections[$key]["name"] = $cm->modules["restricted"]["options"]["layout"][$key];
			}

			$default = isset($cm->modules["restricted"]["sections"]["nav"]->$key->__attributes["default"]) && $cm->modules["restricted"]["sections"]["nav"]->$key->__attributes["default"] == "true";
			if(!(MOD_RES_FULLBAR || array_key_exists("fullbar", $cm->modules["restricted"])))
				$cm->oPage->sections[$key]["events"]->addEvent("on_load_template", "mod_restricted_cm_on_load_navbar", null, 0, null, null, array($key, $default));
		}
	}
	reset($cm->modules["restricted"]["sections"]["nav"]);
}

//ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());

$globals = ffGlobals::getInstance("__mod_restricted__");
$globals->access = false;
$cm->modules["restricted"]["sel_topbar"] = null;
$cm->modules["restricted"]["sel_navbar"] = null;

$res = $cm->modules["restricted"]["events"]->doEvent("on_select", array(&$cm->modules["restricted"]));

//------------------------------------------------------------------------------------------------------------------------------------------------
// Rileva la topbar e la navbar selezionata

if ($cm->modules["restricted"]["sel_topbar"] === null)
{
	$path_parts = explode("/", $cm->path_info);
	for ($i = 1; $i < count($path_parts); $i++)
	{
		$tmp .= "/" . $path_parts[$i];

		if (isset($cm->modules["restricted"]["menu_bypath"][$tmp]) && $cm->modules["restricted"]["menu_bypath"][$tmp][0]["name"] !== "default")
		{
			$cm->modules["restricted"]["sel_topbar"] =& $cm->modules["restricted"]["menu_bypath"][$tmp][0];
			$cm->modules["restricted"]["sel_topbar"]["selected"] = true;
			$cm->modules["restricted"]["sel_navbar"] = null;
			break;
		}
	}
}

if ($cm->modules["restricted"]["sel_topbar"] === null)
{
	if (isset($cm->modules["restricted"]["menu"]["default"]))
	{
		$cm->modules["restricted"]["sel_topbar"] =& $cm->modules["restricted"]["menu"]["default"];
		$cm->modules["restricted"]["sel_topbar"]["selected"] = true;
		$cm->modules["restricted"]["sel_navbar"] = null;
	}
	elseif (isset($cm->modules["restricted"]["menu_bypath"]["/"]))
	{
		$cm->modules["restricted"]["sel_topbar"] =& $cm->modules["restricted"]["menu_bypath"]["/"][0];
		$cm->modules["restricted"]["sel_topbar"]["selected"] = true;
		$cm->modules["restricted"]["sel_navbar"] = null;
	}
}

if ($cm->modules["restricted"]["sel_topbar"] === null)
	ffDialog(false, "okonly", "Access Denied", "Access Denied", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
else if (
			!mod_restricted_checkacl_bylevel($cm->modules["restricted"]["sel_topbar"]["acl"])
			|| !mod_sec_checkprofile_bypath($cm->modules["restricted"]["sel_topbar"]["path"])
		)
{
	foreach ($cm->modules["restricted"]["menu"] as $key => $value)
	{
		if (
				mod_restricted_checkacl_bylevel($value["acl"])
				&& mod_sec_checkprofile_bypath($value["path"])
			)
			ffRedirect(FF_SITE_PATH . $value["path"] . "?" . $cm->oPage->get_globals());
	}	
	ffDialog(false, "okonly", "Access Denied", "Access Denied", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
}
else
{
	$tmp = $cm->path_info;
	do
	{
		if (isset($cm->modules["restricted"]["menu_bypath"][$tmp]))
		{
			for ($i = 0; $i < count($cm->modules["restricted"]["menu_bypath"][$tmp]); $i++)
			{
				if ( (count($cm->modules["restricted"]["menu_bypath"][$tmp]) == 1 || $i > 0) && $cm->modules["restricted"]["menu_bypath"][$tmp][$i] !== $cm->modules["restricted"]["sel_topbar"])
				{
					$cm->modules["restricted"]["sel_navbar"] =& $cm->modules["restricted"]["menu_bypath"][$tmp][$i];
					$cm->modules["restricted"]["sel_navbar"]["selected"] = true;
				}
			}
			break;
		}
	} while(($tmp = ffCommon_dirname($tmp)) && $tmp != "/");

	if ($cm->modules["restricted"]["sel_navbar"] === null && strlen($cm->modules["restricted"]["sel_topbar"]["redir"]))
		ffRedirect(FF_SITE_PATH . $cm->modules["restricted"]["sel_topbar"]["redir"] . "?" . $cm->oPage->get_globals());
	
		if (
			!mod_restricted_checkacl_bylevel($cm->modules["restricted"]["sel_navbar"]["acl"])
			|| !mod_sec_checkprofile_bypath($cm->modules["restricted"]["sel_navbar"]["path"])
		)
		{
			if (count($cm->modules["restricted"]["sel_topbar"]["elements"]))
			{
				foreach ($cm->modules["restricted"]["sel_topbar"]["elements"] as $key => $value)
				{
					if (
							mod_restricted_checkacl_bylevel($value["acl"])
							&& mod_sec_checkprofile_bypath($value["path"])
						)
							ffRedirect(FF_SITE_PATH . $value["path"] . "?" . $cm->oPage->get_globals());
				}
			}
			ffDialog(false, "okonly", "Access Denied", "Access Denied", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
		}
}

$res = $cm->modules["restricted"]["events"]->doEvent("on_layout_process", array(&$cm->modules["restricted"]));
$rc = end($res);
if ($rc !== null)
	return; // TODO: da verificare

function mod_restricted_on_tpl_layer_loaded($page, $tpl)
{
    $cm = cm::getInstance();
  //AVATAR USERNAME
    $tpl->set_var("CM_LOCAL_APP_NAME", ffCommon_specialchars(cm_getAppName()));
}	

function mod_restricted_cm_on_load_topbar($page, $tpl, $location, $default)
{
	$cm = cm::getInstance();
	$globals_mod = ffGlobals::getInstance("__mod_restricted__");

    if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
    {
        $cache_key = "/" . $location . (MOD_RES_MEM_CACHING_BYPATH
                ? $cm->path_info
                : "default"
            );

        $res = $cm->cache->get($cache_key, "/cm/mod/restricted/template/topbar");
    }
    if ($res)
    {
        $globals_mod->access    = $res["access"];
        $tpl->ParsedBlocks      = $res["ParsedBlock"];
    }
	else
	{
        $count = 0;
		foreach ($cm->modules["restricted"]["menu"] as $key => $value)
		{
			if (
					!mod_restricted_checkacl_bylevel($value["acl"]) 
					|| !mod_sec_checkprofile_bypath($value["path"]) 
					|| ($default && isset($value["location"]) && $value["location"] != $location)
					|| (!$default && (!isset($value["location"]) || $value["location"] != $location))
				)
				continue;

			$globals_mod->access |= true;

			// codice ridondante, da eliminare
			if(!isset($value["visible"]))
				$visible = true;
			else
				$visible = $value["visible"];
			
			if(!$value["hide"] && $visible)
			{
				$add_class = "";
				// modifica di ALEX
                if(MOD_RES_FULLBAR || array_key_exists("fullbar", $cm->modules["restricted"]))
				{
					$tpl->set_var("SectChild", "");
					$res_navbar = mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["menu"][$key], "Child");
                    if($res_navbar["count"]) 
                    {
                        $tpl->parse("SectChild", false);
                        if($res_navbar["opened"])
                        	$add_class = " active opened";
					}
                }
				
				$globals = "";
				$params = "";
				
				if ($value["globals_exclude"])
				{
					$globals =  $cm->oPage->get_globals($value["globals_exclude"]);
					$params = ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals($value["globals_exclude"]));
				}
				else
				{
					$globals = $cm->oPage->get_globals();
					$params = ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals());
				}
								
				$tpl->set_var("name", $key . $add_class);
				$tpl->set_var("path", $cm->oPage->site_path . $value["path"] . "?" . $globals . $params);
				if(strpos($value["label"], "_") === 0) {
					$tpl->set_var("label", ffTemplate::_get_word_by_code(substr($value["label"], 1)));
				} else {
					$tpl->set_var("label", $value["label"]);
				}
				
				//$tpl->set_var("label", ffCommon_specialchars($value["label"], ENT_QUOTES, "UTF-8"));
				if ($value["globals_exclude"])
					$tpl->set_var("params", ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals($value["globals_exclude"])));
				else
					$tpl->set_var("params", ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals()));

                if ($value["selected"])
				{
                    $tpl->set_var("selected", "class=\"selected\"");
                    $tpl->set_var("class_selected", "selected");
				}
                else
				{
                    $tpl->set_var("selected", "");
                    $tpl->set_var("class_selected", "");
				}
                    
				if ($count)
					$tpl->parse("SectSeparator", false);

				$tpl->parse("SectElement", true);
                $count++;
			}
		}
		reset($cm->modules["restricted"]["menu"]);

        if($count) {
            $tpl->parse("SectMenu", false);
        } else {
            $tpl->set_var("SectMenu", "");
        }

        if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
        {
            $cache_key = "/" . $location . (MOD_RES_MEM_CACHING_BYPATH
                    ? $cm->path_info
                    : "default"
                );

            /** @var reference $access */
            $tmp = array(
                "ParsedBlock" => $tpl->ParsedBlocks
            , "access" => $globals_mod->access
            );

            $res = $cm->cache->set($cache_key, $tmp, "/cm/mod/restricted/template/topbar");
        }
	}
}

function mod_restricted_cm_on_load_navbar($page, $tpl, $location, $default)
{      
	$cm = cm::getInstance();
    $globals_mod = ffGlobals::getInstance("__mod_restricted__");
    if ($cm->modules["restricted"]["sel_topbar"] === null)
		return;

	// --- codice di ALEX
	if(strlen($cm->modules["restricted"]["sel_topbar"]["label"]))
	{
		if(strpos($cm->modules["restricted"]["sel_topbar"]["label"], "_") === 0)
		{
			$tpl->set_var("navbar_label", ffTemplate::_get_word_by_code(substr($cm->modules["restricted"]["sel_topbar"]["label"], 1)));
		} 
		else 
		{
			$tpl->set_var("navbar_label", $cm->modules["restricted"]["sel_topbar"]["label"]);
		}
		$tpl->parse("SectNavbarTitle", false);
	} 
	else 
	{
		$tpl->set_var("SectNavbarTitle", "");
	}
	
	$tpl->set_var("navbar_class", preg_replace("/[^[:alnum:]]+/", "", $cm->modules["restricted"]["sel_topbar"]["label"]));
	// ---
		
	if (!is_array($cm->modules["restricted"]["sel_topbar"]["elements"]) || !count($cm->modules["restricted"]["sel_topbar"]["elements"]))
		return;

    if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
    {
        $cache_key = "/" . $location . (MOD_RES_MEM_CACHING_BYPATH
                ? $cm->path_info
                : "default"
            );

        $res = $cm->cache->get($cache_key, "/cm/mod/restricted/template/navbar");
    }
    if ($res)
    {
        $globals_mod->access    = $res["access"];
        $tpl->ParsedBlocks      = $res["ParsedBlock"];
	}
	else
	{
		// modifica di ALEX
		$res_navbar = mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["sel_topbar"], "", $location, $default);
        if($res_navbar["count"]) 
            $tpl->parse("SectMenu", false);
		else 
            $tpl->set_var("SectMenu", "");

        if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
        {
            $cache_key = "/" . $location . (MOD_RES_MEM_CACHING_BYPATH
                    ? $cm->path_info
                    : "default"
                );

            /** @var reference $access */
            $tmp = array(
                "ParsedBlock" => $tpl->ParsedBlocks
            , "access" => $globals_mod->access
            );

            $res = $cm->cache->set($cache_key, $tmp, "/cm/mod/restricted/template/navbar");
        }
	}
}

function mod_restricted_process_navbar(&$tpl, $sel_topbar, $prefix = "", $location = "navbar", $default = true)
{
    $cm = cm::getInstance();
    $count = 0;
    $is_opened = false;
    
    if(strlen($prefix))
        $tpl->set_var("Sect" . $prefix . "Element", "");

	$tpl->set_var("navbar_class", preg_replace("/[^[:alnum:]]+/", "", $sel_topbar["label"]));
    
    if(is_array($sel_topbar) && array_key_exists("elements", $sel_topbar) && count($sel_topbar["elements"]))
	{
        foreach ($sel_topbar["elements"] as $key => $value)
        {
            if (
                    !mod_restricted_checkacl_bylevel($value["acl"]) || !mod_sec_checkprofile_bypath($value["path"])
                    || (isset($value["hide"]) && $value["hide"])
                    || ($default && isset($value["location"]) && $value["location"] != $location)
                    || (!$default && (!isset($value["location"]) || $value["location"] != $location))
                )
                continue;
            
            $tpl->set_var("Sect" . $prefix . "Link", "");
            $tpl->set_var("Sect" . $prefix . "Heading", "");

            if(strpos($value["label"], "_") === 0)
                $tpl->set_var("label", ffTemplate::_get_word_by_code(substr($value["label"], 1)));
            else
                $tpl->set_var("label", $value["label"]);
            
            $globals = "";
            $params = "";

            if ($value["globals_exclude"])
            {
                    $globals =  $cm->oPage->get_globals($value["globals_exclude"]);
                    $params = ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals($value["globals_exclude"]));
            }
            else
            {
                    $globals = $cm->oPage->get_globals();
                    $params = ffProcessTags($value["params"], null, null, "normal", $cm->oPage->get_params(), $cm->oPage->ret_url, $cm->oPage->get_globals());
            }

            $tpl->set_var("globals", $globals);
            $tpl->set_var("params", $params);

            if ($value["description"]) {
                if(strpos($value["description"], "_") === 0) {
                    $tpl->set_var("description", ffTemplate::_get_word_by_code(substr($value["description"], 1)));
                } else {
                    $tpl->set_var("description", $value["description"]);
                }
                $tpl->parse("SectDescription", false);
            }
            else {
                $tpl->set_var("SectDescription", "");
            }
            
            if($value["class"]) {
                $tpl->set_var("class", $value["class"]);
            } else {
                $tpl->set_var("class", "");
            }
            
            if ($value["is_heading"])
                $tpl->parse("Sect" . $prefix . "Heading", false);
            else
            {
                $tpl->set_var("name", $key);
				if ($value["jsaction"])
					$tpl->set_var("path", $value["jsaction"]);
				else
					$tpl->set_var("path", $cm->oPage->site_path . $value["path"] . "?" . $globals . $params);

                if ($value["selected"])
				{
                    $tpl->set_var("selected", "class=\"selected\"");
                    $tpl->set_var("class_selected", "selected");
                    
                    $is_opened = true;
				}
                else
				{
                    $tpl->set_var("selected", "");
                    $tpl->set_var("class_selected", "");
				}

                $tpl->parse("Sect" . $prefix . "Link", false);
            }
            $tpl->parse("Sect" . $prefix . "Element", true);
            $count++;
        }
    }
    reset($sel_topbar);

    return array("count" => $count, "opened" => $is_opened);
}