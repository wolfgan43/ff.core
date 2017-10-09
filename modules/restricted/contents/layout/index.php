<?php
if ($cm->isXHR() || $cm->oPage->getTheme() == "dialog")
	return;

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
	elseif(MOD_RES_FULLBAR || array_key_exists("fullbar", $cm->modules["restricted"]))
		$cm->oPage->layer = "fullbar";
	else
		$cm->oPage->layer = "restricted";
}

$cm->oPage->addEvent("getLayerDir", "mod_restricted_getLayerDir", ffEvent::PRIORITY_HIGH, -100, ffEvent::BREAK_NOT_EQUAL, null);
function mod_restricted_getLayerDir(ffPage_base $oPage, $file, $lastres = null)
{
	if ($oPage->layer_dir === null)
        return ffCommon_dirname(cm_cascadeFindTemplate("/layouts/" . $file, "restricted"));
		//return ffCommon_dirname(cm_moduleCascadeFindTemplateByPath("restricted", "/layouts/" . $file, $oPage->getTheme(), false));
	else
		return null;
}

$cm->oPage->addEvent("getLayoutDir", "mod_restricted_getLayoutDir", ffEvent::PRIORITY_HIGH, -100, ffEvent::BREAK_NOT_EQUAL, null);
function mod_restricted_getLayoutDir(ffPage_base $oPage, $file, $lastres = null)
{
    return ffCommon_dirname(cm_cascadeFindTemplate("/layouts/" . $file, "restricted"));
	//return ffCommon_dirname(cm_moduleCascadeFindTemplateByPath("restricted", "/layouts/" . $file, $oPage->getTheme(), false));
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

			$cm->oPage->sections[$key]["events"]->addEvent(
			    "on_load_template"
                , "mod_restricted_cm_on_load_topbar"
                , null
                , 0
                , null
                , null
                , array(
                    $key
                    , (isset($cm->modules["restricted"]["sections"]["top"]->$key->__attributes)
                        ? $cm->modules["restricted"]["sections"]["top"]->$key->__attributes
                        : null
                    )
                )
            );
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

			if(!(MOD_RES_FULLBAR || array_key_exists("fullbar", $cm->modules["restricted"])) || !$default)
				$cm->oPage->sections[$key]["events"]->addEvent(
				    "on_load_template"
                    , "mod_restricted_cm_on_load_navbar"
                    , null
                    , 0
                    , null
                    , null
                    , array(
                        $key
                        , (isset($cm->modules["restricted"]["sections"]["nav"]->$key->__attributes)
                            ? $cm->modules["restricted"]["sections"]["nav"]->$key->__attributes
                            : null
                        )
                    )
                );
		}
	}
	reset($cm->modules["restricted"]["sections"]["nav"]);
}

$filename = cm_cascadeFindTemplate("/css/ff.modules.restricted.css", "restricted");
//$filename = cm_moduleCascadeFindTemplateByPath("restricted", "/css/ff.modules.restricted.css", $cm->oPage->theme);
$ret = cm_moduleGetCascadeAttrs($filename);
$cm->oPage->tplAddCSS("ff.modules.restricted.css", array(
	"file" => $filename
	, "path" => $ret["path"]
	, "priority" => cm::LAYOUT_PRIORITY_HIGH
	, "index" => 100
));
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

		if (isset($cm->modules["restricted"]["menu_bypath"][$tmp]) && $cm->modules["restricted"]["menu_bypath"][$tmp][0]["name"] !== "default" && $cm->modules["restricted"]["menu_bypath"][$tmp][0]["location"] != "favorite")
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

if ($cm->modules["restricted"]["sel_topbar"] === null )
	ffDialog(false, "okonly", "Access Denied", "Access Denied", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
else if (
			!mod_restricted_checkacl_bylevel($cm->modules["restricted"]["sel_topbar"]["acl"])
			|| (
					!$cm->modules["restricted"]["sel_topbar"]["profiling_skip"]
					&& !mod_sec_checkprofile_bypath($cm->modules["restricted"]["sel_topbar"]["path"])
				)
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
	
	if ($cm->modules["restricted"]["sel_navbar"] !== null && 
		(
			!mod_restricted_checkacl_bylevel($cm->modules["restricted"]["sel_navbar"]["acl"])
			|| (
					(!ffIsset($cm->modules["restricted"]["sel_navbar"], "hide") || !$cm->modules["restricted"]["sel_navbar"]["hide"])
					&& (
							!$cm->modules["restricted"]["sel_navbar"]["profiling_skip"]
							&& !mod_sec_checkprofile_bypath($cm->modules["restricted"]["sel_navbar"]["path"])
					)
				)
		)
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
/*$rc = end($res);
if ($rc !== null)
	return; // TODO: da verificare
*/
