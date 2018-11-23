<?php
if ($cm->isXHR())
	return;



$cm->modules["restricted"]["obj"]->process();

/*
if(defined("FF_THEME_ADMIN") && FF_THEME_ADMIN)
    $cm->oPage->theme = FF_THEME_ADMIN;


if ($cm->layout_vars["layer"] === null)
{
    if (strlen($cm->modules["restricted"]["options"]["layout"]["layer"]))
        $cm->oPage->layer = $cm->modules["restricted"]["options"]["layout"]["layer"];
    else
        $cm->oPage->layer = "restricted";
}
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
}*/
/*
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
*/


/*
if (isset($cm->modules["restricted"]["sections"]))
{
	foreach ($cm->modules["restricted"]["sections"] as $key => $value)
	{
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
                    , (isset($cm->modules["restricted"]["sections"][$key]["attributes"])
                        ? $cm->modules["restricted"]["sections"][$key]["attributes"]
                        : null
                    )
                )
            );
		}
	}
	reset($cm->modules["restricted"]["sections"]);
}*/

/*
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
            //fullbar
			if(!$default)
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
}*/
/*
$filename = cm_cascadeFindTemplate("/css/ff.modules.restricted.css", "restricted");
//$filename = cm_moduleCascadeFindTemplateByPath("restricted", "/css/ff.modules.restricted.css", $cm->oPage->theme);
$ret = cm_moduleGetCascadeAttrs($filename);
$cm->oPage->tplAddCSS("ff.modules.restricted.css", array(
	"file" => $filename
	, "path" => $ret["path"]
	, "priority" => cm::LAYOUT_PRIORITY_HIGH
	, "index" => 100
));*/
//ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());

//$globals = ffGlobals::getInstance("__mod_restricted__");
//$globals->access = false;











//$res = $cm->modules["restricted"]["events"]->doEvent("on_layout_process", array(&$cm->modules["restricted"]));
/*$rc = end($res);
if ($rc !== null)
	return; // TODO: da verificare
*/
