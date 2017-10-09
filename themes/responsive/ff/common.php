<?php
if (!class_exists("cm"))
	ffErrorHandler::raise("Theme responsive can be used only with CM. Change or Remove FF_DEFAULT_THEME from /config.php", E_USER_ERROR, null, get_defined_vars());

$cm = cm::getInstance();

ffGrid::addEvent			("on_factory_done", "ffTheme_responsive_ffGrid_set_events"		, ffEvent::PRIORITY_HIGH);
ffRecord::addEvent		("on_factory_done", "ffTheme_responsive_ffRecord_set_events"	, ffEvent::PRIORITY_HIGH);
ffDetails::addEvent		("on_factory_done", "ffTheme_responsive_ffDetails_set_events"	, ffEvent::PRIORITY_HIGH);

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
	if (isset($registry->themes[$theme]->default_js) && count($registry->themes[$theme]->default_js->children()))
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


/***
* FRAMEWORK CSS Load JS / CSS Base
*/
$cm->addEvent("on_layout_init", "ffPage_on_layout_init");
function ffPage_on_layout_init($oPage, $layout_vars) {
    $oPage->framework_css = cm_getFrameworkCss($layout_vars["framework_css"]);
    $oPage->font_icon = cm_getFontIcon($layout_vars["font_icon"]);
	
	if(!$oPage->isXHR()) {
		if(is_array($oPage->font_icon)) {
    		if(strlen($oPage->font_icon["css"])) {
    			$oPage->tplAddCss($oPage->font_icon["name"], basename($oPage->font_icon["css"]), ffCommon_dirname($oPage->font_icon["css"]), "stylesheet", "text/css", false, false, null, false, "top");
    		}
		}    
		if(is_array($oPage->framework_css)) {
    		if(strlen($oPage->framework_css["params"]["css"])) {
                if(is_file($oPage->getThemeDir() . "/css/" . $oPage->framework_css["name"] . ".css"))
                    $oPage->tplAddCss($oPage->framework_css["name"], $oPage->framework_css["name"] . ".css", $oPage->getThemePath(false) . "/css", "stylesheet", "text/css", false, false, null, false, "top");
                else
                    $oPage->tplAddCss($oPage->framework_css["name"], basename($oPage->framework_css["params"]["css"]), ffCommon_dirname($oPage->framework_css["params"]["css"]), "stylesheet", "text/css", false, false, null, false, "top");
            } 
            if(strlen($oPage->framework_css["theme"]["css"])) {
                if(is_file($oPage->getThemeDir() . "/css/" . $oPage->framework_css["theme"]["name"] . ".css"))
                    $oPage->tplAddCss($oPage->framework_css["theme"]["name"], $oPage->framework_css["theme"]["name"] . ".css", $oPage->getThemePath(false) . "/css", "stylesheet", "text/css", false, false, null, false, "top");
                else
    				$oPage->tplAddCss($oPage->framework_css["theme"]["name"], basename($oPage->framework_css["theme"]["css"]), ffCommon_dirname($oPage->framework_css["theme"]["css"]), "stylesheet", "text/css", false, false, null, false, "top");
            }
            
            if(strlen($oPage->framework_css["params"]["js"])) {
                if(is_file($oPage->getThemeDir() . "/javascript/" . $oPage->framework_css["name"] . ".js"))
                    $oPage->tplAddJs($oPage->framework_css["name"], $oPage->framework_css["name"] . ".js", $oPage->getThemePath(false) . "/javascript", false, false, null, false, "top");
                else
    				$oPage->tplAddJs($oPage->framework_css["name"], basename($oPage->framework_css["params"]["js"]), ffCommon_dirname($oPage->framework_css["params"]["js"]), false, false, null, false, "top");
            }
            if(strlen($oPage->framework_css["params"]["js_init"])) {
                if(is_file($oPage->getThemeDir() . "/javascript/" . $oPage->framework_css["name"] . "-init.js"))
                    $oPage->tplAddJs($oPage->framework_css["name"], $oPage->framework_css["name"] . "-init.js", $oPage->getThemePath(false) . "/javascript", false, false, null, false, "bottom");
                else
    				$oPage->tplAddJs($oPage->framework_css["name"] . ".init", null, null, false, false, $oPage->framework_css["params"]["js_init"], false, "bottom");
            }
		}				
	}
}
function ffCommon_setClassByFrameworkCss($resolution) 
{
	if($resolution) 
	{
		if(is_array($resolution)) {
			$res = $resolution;
			if(count($resolution) < 4)
	    		$res = array_merge($res, array_fill(count($resolution), 4 - count($resolution), $resolution[count($resolution) - 1]));

	    	$check = array_count_values($res);
	    	if($check[0] == 4)
	    		$res = false;

		} else {
			$res = array_fill(0, 4, $resolution);
		}
	}

	if($res)
		return array(
		"xs" => $res[3]
		, "sm" => $res[2]
		, "md" => $res[1]
		, "lg" => $res[0]
	);		
}	
function cm_getFontIcon($font_icon_name) 
{
    $cm = cm::getInstance();

    if(strlen($font_icon_name)) {
        if($font_icon_name == "no") {
            $font_icon = null;
        } else {
            $font_icon = $cm->oPage->font_icon_setting[$font_icon_name];
            $font_icon["name"] = $font_icon_name;
        }
    }
    
    return $font_icon;
}
function cm_getFrameworkCss($framework_name) 
{
    $cm = cm::getInstance();

    if(strlen($framework_name)) {
        if($framework_name == "no") {
            $framework_css = null;
        } else {
            if(strpos($framework_name, "-fluid") !== false) {
                $arrFrameworkCss = explode("-fluid", $framework_name);    
                $framework_css = array(
                    "name" => $arrFrameworkCss[0]
                    , "is_fluid" => true
                    , "class" => $cm->oPage->framework_css_setting[$arrFrameworkCss[0]]["class-fluid"]
                    , "theme" => array(
                        "name" => $arrFrameworkCss[1]
                        , "css" => $cm->oPage->framework_css_setting[$arrFrameworkCss[0]]["theme"][$arrFrameworkCss[1]]
                    )
                );
				$framework_css = array_replace($cm->oPage->framework_css_setting[$arrFrameworkCss[0]], $framework_css); 
            } elseif(strpos($framework_name, "-") !== false) {
                $arrFrameworkCss = explode("-", $framework_name);    
                $framework_css = array(
                    "name" => $arrFrameworkCss[0]
                    , "is_fluid" => false
                    , "class" => $cm->oPage->framework_css_setting[$arrFrameworkCss[0]]["class"]
                    , "theme" => array(
                        "name" => $arrFrameworkCss[1]
                        , "css" => $cm->oPage->framework_css_setting[$arrFrameworkCss[0]]["theme"][$arrFrameworkCss[1]]
                    )
                );
				$framework_css = array_replace($cm->oPage->framework_css_setting[$arrFrameworkCss[0]], $framework_css); 
            } else {
                $framework_css = array(
                    "name" => $framework_name
                    , "is_fluid" => false
                    , "class" => $cm->oPage->framework_css_setting[$framework_name]["class"]
                    , "theme" => null
                );
				$framework_css = array_replace($cm->oPage->framework_css_setting[$framework_name], $framework_css); 
            }
			unset($framework_css["class-fluid"]);
        }
    }

    return $framework_css;
}
  
function cm_getClassByFrameworkCss($value, $type, $params = array(), $framework_css = null, $font_icon = null) {
	$cm = cm::getInstance();

	$res = "";
	if($value === false)
		return $res;
		
	if($framework_css === null && is_array($cm->oPage->framework_css))
		$framework_css = $cm->oPage->framework_css;

    if($font_icon === null && is_array($cm->oPage->font_icon))
        $font_icon = $cm->oPage->font_icon;

    if(!is_array($font_icon))
        $font_icon = array();
    
    if(!is_array($params) && strlen($params))
        $params = array($params);

    if(!is_array($params))
    	$params = array();
	
	switch($type) {
		case "button":
		case "link":    
			if(is_array($params) && (array_key_exists("strict", $params) || array_search("strict", $params) !== false)) {
				if(array_key_exists("strict", $params))
					$is_strict = true;
				else
					$is_strict = array_search("strict", $params);
				
				if($is_strict !== false ) {
					if($is_strict === true)
						unset($params["strict"]);
					else
						unset($params[$is_strict]);
				}
			}

			$arrFFButton = cm_getFFButtonDefault();
			//$arrDefaultButton = array("default", "primary", "success", "info", "warning", "danger", "link");
			$skip_default = false;
			$res = array();
			if(!$is_strict) {
                $arrButton = $framework_css["button"]["color"];
                $skip_default = $framework_css["button"]["skip-default"];
                if($framework_css["button"]["base"])
                   $res[$framework_css["button"]["base"]] = true;

				/*switch($framework_css["name"]) {
					case "base":  
						$arrButton = array(
							"default"       => ""
							, "primary"     => "primary"
							, "success"     => "success"
							, "info"        => "info"
							, "warning"     => "warning"
							, "danger"      => "danger"
							, "link"        => "link"
						);
						//$res[$type] = true;
						$res["btn"] = true;
						break;
					case "bootstrap":
						$skip_default = true;
						$arrButton = array(
							"default" 		=> "btn-default"
							, "primary" 	=> "btn-primary"
							, "success" 	=> "btn-success"
							, "info" 		=> "btn-info"
							, "warning" 	=> "btn-warning"
							, "danger" 		=> "btn-danger"
							, "link" 		=> "btn-link"
						);

						$res["btn"] = true;
						break;
					case "foundation":
						$skip_default = true;
						$arrButton = array(
							"default" 		=> "secondary"
							, "primary" 	=> ""
							, "success" 	=> "success"
							, "info" 		=> "secondary"
							, "warning" 	=> "alert"
							, "danger" 		=> "alert"
							, "link" 		=> "secondary"
						);

						$res["button"] = true;
						break;
					default:
						$res[$type] = true;
				}  */
			}
			/*if($button_key = array_search($value, $arrDefaultButton) !== false) {  
				$res[$arrButton[$arrDefaultButton[$button_key]]] = true;
			} else*/
			if(array_key_exists($value, $arrFFButton)) {
				if(is_array($arrButton) && strlen($arrButton[$arrFFButton[$value]["default"]]))
					$res[$arrButton[$arrFFButton[$value]["default"]]] = true;
				if(strlen($arrFFButton[$value]["addClass"]))
					$res[$arrFFButton[$value]["addClass"]] = true;
				if(isset($arrFFButton[$value]["params"])) {
					if(is_array($arrFFButton[$value]["params"]))
						$params = array_merge($params, $arrFFButton[$value]["params"]);
					else
						$params = array_merge($params, array($arrFFButton[$value]["params"]));
				}
            } elseif($type == "button" && isset($arrButton[$value])) {
                if($arrButton[$value]) $res[$arrButton[$value]] = true;
			} elseif($type == "button" && isset($arrButton["default"])) {
				if($arrButton["default"]) $res[$arrButton["default"]] = true;
			} elseif($type == "link" && isset($arrButton["link"])) {
				if($arrButton["link"]) $res[$arrButton["link"]] = true; 
			}

            if(is_array($params) && count($params)) {
                foreach($params AS $params_key => $params_value) {
                    if($framework_css["button"][$params_key][$params_value]) {
                        $res[$framework_css["button"][$params_key][$params_value]] = true;
                    } elseif(is_array($font_icon)) {
                        switch($params_value) {
                            case "stack":
                            case "stack-equal":
                                switch($font_icon["name"]) {    
                                    case "base":
                                        $res["icon-stack"] = true;    
                                        break;
                                    case "fontawesome":
                                        $res["fa-stack"] = true;    
                                        break;
                                    case "glyphicons":
                                            $res["icon-stack"] = true;
                                        break;
                                    default:
                                        $res[$params_value] = true;
                                }                            
                                break;
                            default:
                                $res[$params_value] = true;
                        }   
                    } else {
                        $res[$params_value] = true;
                    }
                }
            }
            //print_r($params);
			if(!$skip_default && !$is_strict)
				if(isset($arrFFButton[$value]["class"]))
					$res[$arrFFButton[$value]["class"]] = true;
				else
					$res[$value] = true;

			$res = implode(" ", array_keys($res));
			break;
		case "icon":
		case "icon-default":
		case "icon-tag":
		case "icon-link-tag": 
        case "icon-link-tag-default": 

/* Params:
    stack
    stack-equal
    rotate-90
    rotate-180
    rotate-270
    flip-horizontal
    flip-vertical
    transparent
    inverse
    spin
    pull-left
    pull-right
    border
    lg
    2x
    3x
    4x
    5x
    
*/         

            $addClass = null;   
            if(!is_array($value) && strlen($value)) {
                $arrFFButton = cm_getFFButtonDefault();
                if(isset($arrFFButton[$value])) {
                    if(isset($arrFFButton[$value]["params"])) {
                        if(is_array($arrFFButton[$value]["params"]))
                            $params = array_merge($params, $arrFFButton[$value]["params"]);
                        else
                            $params = array_merge($params, array($arrFFButton[$value]["params"]));
                    }
                                        
                    if($type == "icon" && strlen($arrFFButton[$value]["addClass"])) {
                        $addClass[] = $arrFFButton[$value]["addClass"];  
                    } 
                    
                    if(is_array($arrFFButton[$value]["font_icon"])) {
                        $font_icon = array_replace_recursive($font_icon, $arrFFButton[$value]["font_icon"]);
                    }
                    $value = $arrFFButton[$value]["icon"];
                    $default_loaded = true;
                }                
            } 

            if(is_array($params)) {
                $is_stack = array_search("stack", $params) !== false;
                $is_stack_equal = array_search("stack-equal", $params) !== false;
                if($params["class"]) {
                	$addClass[] = $params["class"];
                	unset($params["class"]);
                }
            }
                 
            if($value === null && is_array($params)) {
                $real_params = $params;
                
                switch($font_icon["name"]) {    
                    case "base":
                        if($is_stack_equal !== false)
                            $real_params[$is_stack_equal] = "stack";
                        break;
                    case "fontawesome":
                        if($is_stack_equal !== false)
                            $real_params[$is_stack_equal] = "stack";
                        break;
                    case "glyphicons":
                        if($is_stack_equal !== false)
                            $real_params[$is_stack_equal] = "stack";
                        break;
                    default:
                }

                $res = cm_getIconByFrameworkCss(null, $type, $real_params, $addClass, $font_icon, $default_loaded);
            } elseif(is_array($value)) {
                foreach($value AS $count_value => $real_value) {
                    $real_params = $params;

                    switch($font_icon["name"]) {    
                        case "base":
                            if($is_stack !== false)
                                $real_params[$is_stack] = "stack-" . ($count_value + 1) . "x";
                            elseif($is_stack_equal !== false)
                                $real_params[$is_stack_equal] = "stack-2x";
                            break;
                        case "fontawesome":
                            if($is_stack !== false)
                                $real_params[$is_stack] = "stack-" . ($count_value + 1) . "x";
                            elseif($is_stack_equal !== false)
                                $real_params[$is_stack_equal] = "stack-2x";
                            break;
                        case "glyphicons":
                            if($is_stack !== false && !$count_value)
                                $real_params[$is_stack] = "icon-stack-base";
                            elseif($is_stack_equal !== false)
                                $real_params[$is_stack_equal] = "icon-stack-base";
                            break;
                        default:
                    }

                    $res[] = cm_getIconByFrameworkCss($real_value, "icon-tag", $real_params, $addClass, $font_icon, $default_loaded);
                }
            } else {
                $res = cm_getIconByFrameworkCss($value, $type, $params, $addClass, $font_icon, $default_loaded);  
            }    
                            
			break;
		case "icon-button-tag": 
        case "icon-button-tag-default": 
			break;
		case "col-default":
		case "col":
		case "col-fluid":
			$is_fluid = null;
			if($type == "col-fluid" && !$framework_css["is_fluid"])
				$is_fluid = true;
			else if($type == "col" && $framework_css["is_fluid"])
				$is_fluid = false;
			
			$res = cm_getClassColByFrameworkCss($value, $is_fluid, $params, $framework_css);
            if(isset($params["class"]) && is_array($params["class"]) && count($params["class"]))
                $res .= " " . implode(" ", $params["class"]);
            if(count($params) && array_key_exists("0", $params) && strlen($params[0]))
                $res .= " " . $params[0];
            
			break;
		case "wrap-default":
        case "wrap": 
        case "wrap-fluid":
            if(is_array($params) && count($params))
                $res = array_fill_keys($params, true);
            else
                $res = array();
			
			if($value)
                $res[$value] = true;

            if(is_array($framework_css)) {
				if($type == "wrap-default")
					$res[$cm->oPage->framework_css_setting[$framework_css["name"]]["class" . ($framework_css["is_fluid"] ? "-fluid" : "")]["wrap"]] = true;
				else if($type == "wrap-fluid")
					$res[$cm->oPage->framework_css_setting[$framework_css["name"]]["class-fluid"]["wrap"]] = true;
				else if($type == "wrap")
					$res[$cm->oPage->framework_css_setting[$framework_css["name"]]["class"]["wrap"]] = true;
            } else {
            	$res["wrap"] = true;
            }
            
            $res = implode(" ", array_keys($res));
            break;
		case "push-default":
		case "push":
		case "push-fluid":
			$is_fluid = null;
			if($type == "push-fluid" && !$framework_css["is_fluid"])
				$is_fluid = true;
			else if($type == "push" && $framework_css["is_fluid"])
				$is_fluid = false;
			
			$res = cm_getClassColByFrameworkCss($value, $is_fluid, $params, $framework_css, "push");
            if(isset($params["class"]) && is_array($params["class"]) && count($params["class"]))
                $res .= " " . implode(" ", $params["class"]);
            if(count($params) && array_key_exists("0", $params) && strlen($params[0]))
                $res .= " " . $params[0];
            
			break;   
		case "pull-default":
		case "pull":
		case "pull-fluid":
			$is_fluid = null;
			if($type == "pull-fluid" && !$framework_css["is_fluid"])
				$is_fluid = true;
			else if($type == "pull" && $framework_css["is_fluid"])
				$is_fluid = false;
			
			$res = cm_getClassColByFrameworkCss($value, $is_fluid, $params, $framework_css, "pull");
            if(isset($params["class"]) && is_array($params["class"]) && count($params["class"]))
                $res .= " " . implode(" ", $params["class"]);
            if(count($params) && array_key_exists("0", $params) && strlen($params[0]))
                $res .= " " . $params[0];
            
			break; 
		case "row-default":
		case "row":
		case "row-fluid":
            if(is_array($params) && count($params))
                $res = array_fill_keys($params, true);
            else
                $res = array();
			
			if($value === true)
				$type = "row-default";
			elseif($value)
                $res[$value] = true;

			if(is_array($framework_css)) {
				if($type == "row-default")
					$framework_css["class"] = $cm->oPage->framework_css_setting[$framework_css["name"]]["class" . ($framework_css["is_fluid"] ? "-fluid" : "")];
				else if($type == "row-fluid")
					$framework_css["class"] = $cm->oPage->framework_css_setting[$framework_css["name"]]["class-fluid"];
				else if($type == "row")
					$framework_css["class"] = $cm->oPage->framework_css_setting[$framework_css["name"]]["class"];

				if(strlen($framework_css["class"]["row-prefix"]))
					$res[$framework_css["class"]["row-prefix"]] = true;
					
				if(strlen($framework_css["class"]["row-postfix"]))
					$res[$framework_css["class"]["row-postfix"]] = true;
			} else {
				$res["line"] = true;
			}	

            $res = implode(" ", array_keys($res));
			break;
		case "form":
		case "callout":
		case "pagination":
        case "bar":
        case "list":
            if(isset($params["exclude"])) {
                $exclude = $params["exclude"];

                if(is_array($framework_css[$type][$value . "-exclude"]) 
                    && count($framework_css[$type][$value . "-exclude"]) 
                    && array_search($exclude, $framework_css[$type][$value . "-exclude"]) !== false
                ) {
                    break;
                }
                
                unset($params["exclude"]);                
            }
                
            if(is_array($params) && count($params))
                $res = array_fill_keys($params, true);
            else
                $res = array();
			
			if(is_array($framework_css)) {
				if(strlen($framework_css[$type][$value]))
					$res[$framework_css[$type][$value]] = true;
			}	

            $res = implode(" ", array_keys($res));
			break;
		case "util":
		case "table":
		case "tab": 
            if(is_array($params) && count($params))
                $res = array_fill_keys($params, true);
            else
                $res = array();
			
			if(is_array($framework_css)) {
				if(is_array($value)) {
					foreach($value AS $subvalue) {
						if(strlen($framework_css[$type][$subvalue]))
							$res[$framework_css[$type][$subvalue]] = true;
					}				
				} else {
					if(strlen($framework_css[$type][$value]))
						$res[$framework_css[$type][$value]] = true;
				}
			}	

            $res = implode(" ", array_keys($res));
			break;
		case "data":
			$subtype = $params[0];
			if(is_array($framework_css)) {
				if(is_array($value)) {
					foreach($value AS $subvalue) {
						if(strlen($framework_css[$type][$subtype][$subvalue]))
							$res[$framework_css[$type][$subtype][$subvalue]] = true;
					}				
				} else {
					if(strlen($framework_css[$type][$subtype][$value]))
						$res[$framework_css[$type][$subtype][$value]] = true;
				}
			}	
			if(is_array($res))
            	$res = " " . implode(" ", array_keys($res));
			break;
		default;
			$res = $value;
            if(is_array($params) && count($params))
                $res .= " " . implode(" ", $params);
	}
	return $res;
}

function cm_getIconByFrameworkCss($value, $type, $params, $addClass, $font_icon, $skip_default = false)
{          
    if(is_array($font_icon)) {
        $res = array();

        if(!$skip_default)
            $arrFFButton = cm_getFFButtonDefault();
        
        if(!strlen($value)) {
            $arrName = $params;
            $skip_fix = true;
        } elseif(is_array($params)) {
            $arrName = array_merge(explode(" ", $value), $params);
        } else {
            $arrName = explode(" ", $value);
        }
        
        foreach($arrName AS $single_value) {
            if(strlen($single_value)) {
                if(!$skip_default && isset($arrFFButton[$single_value])) {
                    if(strpos($type, "-default") !== false)
                        continue;
                    
                    $res[$font_icon["prepend"] . $arrFFButton[$single_value]["icon"] . $font_icon["append"]] = true;

                    if($type == "icon" && strlen($arrFFButton[$single_value]["addClass"])) {
                        $res[$arrFFButton[$single_value]["addClass"]] = true;
                    }
                } else {
                    $res[$font_icon["prepend"] . $single_value . $font_icon["append"]] = true;
                }
                
            }
        }
        if(is_array($addClass) && count($addClass)) {
            foreach($addClass AS $addClass_value) {
                $res[$addClass_value] = true;
            }
        }
        if(!$skip_fix && count($res)) {
            if(strlen($font_icon["prefix"]))
                $res[$font_icon["prefix"]] = true;
            if(strlen($font_icon["postfix"]))
                $res[$font_icon["postfix"]] = true;
        }

        $res = implode(" ", array_keys($res));
    } else {
        $res = $value;
    }
            
    if(strlen($res) && strpos($type, "-tag") !== false)
        $res = '<i class="' . $res . '"></i>';

    return $res;
}

function cm_getClassColByFrameworkCss($resolution = array(), $is_fluid = null, $params = array(), $framework_css = null, $prefix = "col")
{
	$cm = cm::getInstance();
 	$res = "";

	if($framework_css === null && is_array($cm->oPage->framework_css))
		$framework_css = $cm->oPage->framework_css;

	if(is_array($framework_css))
	{	
		if($is_fluid === true) {
			$framework_css["class"] = $cm->oPage->framework_css_setting[$framework_css["name"]]["class-fluid"];
		} elseif($is_fluid === false) {
			$framework_css["class"] = $cm->oPage->framework_css_setting[$framework_css["name"]]["class"];	
		}

		$skip_full = (isset($params["skip-full"])
			? $params["skip-full"]
			: $framework_css["class"]["skip-full"]
		);
		$skip_resolution = (isset($params["skip-resolution"])
			? $params["skip-resolution"]
			: $framework_css["class"]["skip-resolution"]
		);

		$skip_prepost = (isset($params["skip-prepost"])
			? $params["skip-prepost"]
			: false
		);
		
		$arrRes = array();
		if(is_array($resolution) && count($resolution))
		{
			if(count($framework_css["resolution"]))
			{
				$diff_resolution = count($resolution) - count($framework_css["resolution"]);
				if($diff_resolution > 0)
				{
					$resolution = array_slice($resolution, $diff_resolution, count($framework_css["resolution"]));
				}
			}
			
			$count_res_value = array_count_values($resolution);
			if($count_res_value[0] == count($resolution)) {
				if($skip_full)
					$resolution = array();
				else
					$resolution = array_fill(0, count($resolution), 12);
			} elseif($count_res_value[12] == count($resolution)) {
				if($skip_full)
					$resolution = array();
			}
			
			if(count($resolution))
			{
	

				if($framework_css["class"]["skip-resolution"]) 
					$resolution = array_reverse($resolution);

				$i = 0;
				foreach($resolution AS $res_num) 
				{
					if($res_num !== $prev_num || $res_num == 0) {
						$real_prefix = ($res_num ? $prefix . "-append" : $prefix . "-hidden");
						if(array_key_exists($real_prefix, $framework_css["class"]) 
							&& strlen($framework_css["class"][$real_prefix])
						) {
							$arrRes[$i] .= $framework_css["class"][$real_prefix];
							if($i == 0)
								$arrRes[$i] .= $framework_css["class"][$real_prefix . "-smallest"];
						} 
						
						if($res_num || array_key_exists($real_prefix, $framework_css["class"])) {
							if(!$skip_resolution) {
								if(array_key_exists("resolution", $framework_css) 
									&& is_array($framework_css["resolution"]) && count($framework_css["resolution"])
									&& array_key_exists($i, $framework_css["resolution"])
								) {
									$arrRes[$i] .= $framework_css["resolution"][$i] . ($res_num ? "-" : "");
								}
							}
						
							$arrRes[$i] .= $framework_css["class"][$prefix . "-prepend"];
						
							if($res_num)
								$arrRes[$i] .= $res_num;
						}

						$prev_num = $res_num;
					}						

					if($skip_resolution) 
						break;

					$i++;
				}

				if(array_key_exists(count($resolution) - 1, $arrRes))
					$arrRes[count($resolution) - 1] .= $framework_css["class"][$real_prefix . "-largest"];

			}
		}
		
		if(strlen($framework_css["class"][$prefix . "-prefix"])) {
			array_unshift($arrRes, $framework_css["class"][$prefix . "-prefix"]);
		}

		if(strlen($framework_css["class"][$prefix . "-postfix"])) {
			$arrRes[] = $framework_css["class"][$prefix . "-postfix"];
		}

		if($skip_prepost) {
			$arrRes[] = $framework_css["class"]["skip-prepost"];
		}
		
		$res = implode(" ", $arrRes);
	}

	return $res;
}
function cm_getResolution($type = null, $skip_default = true, $large_to_small = true, $framework_css = null) {
	$cm = cm::getInstance();
 	$res = null;

	if($framework_css === null && is_array($cm->oPage->framework_css))
		$framework_css = $cm->oPage->framework_css;

	if(is_array($framework_css)) {
		$res = $framework_css["resolution" . ($type ? "-" . $type : "")];
		
		$default = array_pop($res);
		if(!$skip_default && $type)
			$res["default"] = $default;
			
		if($large_to_small)
			$res = array_reverse($res, ($type ? true : false));
	}

	return $res;
}
function cm_getFFButtonDefault() {
    $arrFFButton = array(
        //ffRecord
        "ActionButtonInsert"     => array(
                                        "default" => "success"
                                        , "addClass" => ""
                                        , "icon" => "check"
                                        , "class" => "insert"
                                    )
        , "ActionButtonUpdate"    => array(
                                        "default" => "success"
                                        , "addClass" => ""
                                        , "icon" => "check"
                                        , "class" => "update"
                                    )
        , "ActionButtonDelete"    => array(
                                        "default" => "danger"
                                        , "addClass" => ""
                                        , "icon" => "trash-o"
                                        , "class" => "delete"
                                    )
        , "ActionButtonCancel"    => array(
                                        "default" => "link"
                                        , "addClass" => ""
                                        , "icon" => ""
                                        , "class" => "cancel"
                                    )
        , "insert"         => array(
                                "default" => "success"
                                , "addClass" => "activebuttons"
                                , "icon" => "check"
                            )
        , "update"         => array(
                                "default" => "success"
                                , "addClass" => "activebuttons"
                                , "icon" => "check"
                            )
        , "delete"         => array(
                                "default" => "danger"
                                , "addClass" => "activebuttons"
                                , "icon" => "trash-o"
                            )
        , "cancel"         => array(
                                "default" => "link"
                                , "addClass" => ""
                                , "icon" => "times"
                            )
        , "print"         => array(
                                "default" => "default"
                                , "addClass" => "print"
                                , "icon" => "print"
                            ) 
        //ffGrid
        , "search"         => array(
                                "default" => "primary"
                                , "addClass" => "search"
                                , "icon" => "search"
                            )
        , "searchadv"         => array(
                                "default" => "primary"
                                , "addClass" => "search"
                                , "icon" => "search"
                            )                            
        , "more"         => array(
                                "default" => "link"
                                , "addClass" => "more"
                                , "icon" => "caret-down"
                            )
        , "export"         => array(
                                "default" => "default"
                                , "addClass" => "export"
                                , "icon" => "download"
                            )
        , "sort"    => array(
                                "default" => "link"
                                , "addClass" => "sort"
                                , "icon" => "sort"
                            )
        , "sort-asc"    => array(
                                "default" => "link"
                                , "addClass" => "sort asc"
                                , "icon" => "sort-asc"
                            )
        , "sort-desc"   => array(
                                "default" => "link"
                                , "addClass" => "sort desc"
                                , "icon" => "sort-desc"
                            )    
        , "addnew"        => array(
                                "default" => "primary"
                                , "addClass" => "addnew"
                                , "icon" => "plus"
                            )
        , "editrow"     => array(
                                "default" => "link"
                                , "addClass" => "edit"
                                , "icon" => "pencil"
                            )
        , "deleterow"    => array(
                                "default" => "danger"
                                , "addClass" => "delete"
                                , "icon" => "trash-o"
                            )
        , "deletetabrow"    => array(
                                "default" => null
                                , "addClass" => "delete close"
                                , "icon" => "trash-o"
                            )                            
        //ffDetail    
        , "addrow"         => array(
                                "default" => "primary"
                                , "addClass" => ""
                                , "icon" => "plus"
                            )
        //ffPageNavigator
        , "first"         => array(
                                "default" => "link"
                                , "addClass" => "first"
                                , "icon" => "step-backward" 
                            )
        , "last"         => array(
                                "default" => "link"
                                , "addClass" => "last"
                                , "icon" => "step-forward" 
                            )
        , "prev"         => array(
                                "default" => "link"
                                , "addClass" => "prev"
                                , "icon" => "play" 
                                , "params" => "rotate-180"
                            )
        , "next"         => array(
                                "default" => "link"
                                , "addClass" => "next"
                                , "icon" => "play"
                            )
        , "prev-frame"   => array(
                                "default" => "link"
                                , "addClass" => "prev-frame"
                                , "icon" => "backward"
                            )
        , "next-frame"   => array(
                                "default" => "link"
                                , "addClass" => "next-frame"
                                , "icon" => "forward"
                            )

       //other
        , "pdf"          => array(
                                "default" => "link"
                                , "addClass" => "pdf"
                                , "icon" => "file-pdf-o"
                            )
        , "email"        => array(
                                "default" => "link"
                                , "addClass" => "email"
                                , "icon" => "envelope-o"
                            )    
        , "preview"      => array(
                                "default" => "link"
                                , "addClass" => "preview"
                                , "icon" => "search"
                            )
        , "preview-email"=> array(
                                "default" => "link"
                                , "addClass" => "email"
                                , "icon" => "envelope-o"
                                , "params" => ""
                            )
        , "refresh"		=> array(
                                "default" => "link"
                                , "addClass" => "refresh"
                                , "icon" => "refresh"
                            )
        , "clone"        => array(
                                "default" => "link"
                                , "addClass" => "clone"
                                , "icon" => "copy"
                            )
        , "permissions"        => array(
                                "default" => "link"
                                , "addClass" => "permissions"
                                , "icon" => "lock"
                            )
        , "relationships"        => array(
                                "default" => "link"
                                , "addClass" => "relationships"
                                , "icon" => "share-alt"
                            )
        , "settings"        => array(
                                "default" => "link"
                                , "addClass" => "settings"
                                , "icon" => "cog"
                            )
        , "properties"      => array(
                                "default" => "link"
                                , "addClass" => "properties"
                                , "icon" => "wrench"
                            )
        , "help"            => array(
                                "default" => "link"
                                , "addClass" => "helper"
                                , "icon" => "question-circle"
                            )
        , "noimg"           => array(
                                "default" => "link"
                                , "addClass" => "noimg"
                                , "icon" => "picture-o"
                            )
        , "checked"           => array(
                                "default" => "link"
                                , "addClass" => "checked"
                                , "icon" => "check-circle-o"
                            )
        , "unchecked"           => array(
                                "default" => "link"
                                , "addClass" => "unchecked"
                                , "icon" => "circle-o"
                            )
        , "exanded"           => array(
                                "default" => "link"
                                , "addClass" => "exanded"
                                , "icon" => "minus-square-o"
                            )
        , "retracted"           => array(
                                "default" => "link"
                                , "addClass" => "retracted"
                                , "icon" => "plus-square-o"
                            )


        //CMS Ecommerce
        , "history"      => array(
                                "default" => "link"
                                , "addClass" => "history"
                                , "icon" => "history"
                            )
        , "payments"     => array(
                                "default" => "link"
                                , "addClass" => "payments"
                                , "icon" => "credit-card"
                            )
        //CMS
        , "vg-admin"     => array(
                                "default" => "link"
                                , "addClass" => "admin"
                                , "icon" => "cog"
                                , "params" => "2x"
                            )
        , "vg-restricted"=> array(
                                "default" => "link"
                                , "addClass" => "restricted"
                                , "icon" => "unlock-alt"
                                , "params" => "2x"
                            )
        , "vg-manage"    => array(
                                "default" => "link"
                                , "addClass" => "manage"
                                , "icon" => "shopping-cart"
                                , "params" => "2x"
                            )
        , "vg-fontend"   => array(
                                "default" => "link"
                                , "addClass" => "fontend"
                                , "icon" => "desktop"
                                , "params" => "2x"
                            )
        , "vg-static-menu"    => array(
                                "default" => "link"
                                , "addClass" => "static-menu"
                                , "icon" => "static-menu"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-gallery-menu"    => array(
                                "default" => "link"
                                , "addClass" => "gallery-menu"
                                , "icon" => "gallery-menu"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-vgallery-menu"    => array(
                                "default" => "link"
                                , "addClass" => "vgallery-menu"
                                , "icon" => "vgallery-menu"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-vgallery-group"    => array(
                                "default" => "link"
                                , "addClass" => "vgallery-group"
                                , "icon" => "vgallery-group"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-gallery"    => array(
                                "default" => "link"
                                , "addClass" => "gallery"
                                , "icon" => "gallery"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-draft"    => array(
                                "default" => "link"
                                , "addClass" => "draft"
                                , "icon" => "draft"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-file"    => array(
                                "default" => "link"
                                , "addClass" => "file"
                                , "icon" => "file"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-virtual-gallery"    => array(
                                "default" => "link"
                                , "addClass" => "virtual-gallery"
                                , "icon" => "virtual-gallery"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-publishing"    => array(
                                "default" => "link"
                                , "addClass" => "publishing"
                                , "icon" => "publishing"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-vgallery-rel"    => array(
                                "default" => "link"
                                , "addClass" => "vgallery-rel"
                                , "icon" => "vgallery-rel"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-comment"    => array(
                                "default" => "link"
                                , "addClass" => "comment"
                                , "icon" => "comment"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-cart"    => array(
                                "default" => "link"
                                , "addClass" => "cart"
                                , "icon" => "cart"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-lang"    => array(
                                "default" => "link"
                                , "addClass" => "lang"
                                , "icon" => "lang"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-search"    => array(
                                "default" => "link"
                                , "addClass" => "search"
                                , "icon" => "search"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-login"    => array(
                                "default" => "link"
                                , "addClass" => "login"
                                , "icon" => "login"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-breadcrumb"    => array(
                                "default" => "link"
                                , "addClass" => "breadcrumb"
                                , "icon" => "breadcrumb"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-profile"    => array(
                                "default" => "link"
                                , "addClass" => "profile"
                                , "icon" => "profile"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-modules"    => array(
                                "default" => "link"
                                , "addClass" => "module"
                                , "icon" => "module"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "vg-applets"    => array(
                                "default" => "link"
                                , "addClass" => "applets"
                                , "icon" => "applets"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-addnew"    => array(
                                "default" => "link"
                                , "addClass" => "lay-addnew"
                                , "icon" => "lay-addnew"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-addnew"    => array(
                                "default" => "link"
                                , "addClass" => "lay-addnew"
                                , "icon" => "lay-addnew"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay"    => array(
                                "default" => "link"
                                , "addClass" => "lay-unknown"
                                , "icon" => "lay"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-31"    => array(
                                "default" => "link"
                                , "addClass" => "lay-top"
                                , "icon" => "lay-31"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-13"    => array(
                                "default" => "link"
                                , "addClass" => "lay-left"
                                , "icon" => "lay-13"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-3133"    => array(
                                "default" => "link"
                                , "addClass" => "lay-right"
                                , "icon" => "lay-3133"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-1333"    => array(
                                "default" => "link"
                                , "addClass" => "lay-right"
                                , "icon" => "lay-1333"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "lay-2233"    => array(
                                "default" => "link"
                                , "addClass" => "lay-content"
                                , "icon" => "lay-2233"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "js"    => array(
                                "default" => "link"
                                , "icon" => "js"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "css"    => array(
                                "default" => "link"
                                 , "icon" => "css"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
        , "seo"    => array(
                                "default" => "link"
                                 , "icon" => "seo"
                                , "font_icon" => array("prepend" => "vg-", "prefix" => "vg")
                            )
    );
    
    return $arrFFButton;
}


function cm_getClassByDef($def, $custom = array(), $out_tag = false) {
    if(!is_array($def))
        return "";

	$res["class"] = $def["class"];
	unset($def["class"]);

	foreach($def AS $type => $data) {
        if(is_array($data) && isset($data["params"])) {
            $value = $data["value"];
            $params = $data["params"];
        } else {
            $value = $data;
            $params = array();
        }

		$res[$type] = cm_getClassByFrameworkCss($value, $type, $params);
	}
	if(is_array($custom) && count($custom))
		$res = array_replace($res, $custom);
	
	$res = implode(" ", array_filter($res));
	if($out_tag && $res) {
		$res =  'class="' . $res . '"';
	}

	return $res;
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

function ffTheme_responsive_oPage_getTemplateDir(ffPage_base $oPage)
{
	if ($oPage->template_dir !== null)
		return $oPage->template_dir;
	else
	{
		die($oPage->template_file);
		return cm_findCascadeTemplate("ffPage", $oPage->getTheme(), $oPage->template_file);
	}
}

ffRecord::addEvent("on_factory_done", "ffRecord_set_events_dialog", ffEvent::PRIORITY_HIGH);

global $ff_global_setting;
$ff_global_setting["ffButton_html"]["jsaction"] = "ff.ffPage.dialog.doAction('[[XHR_DIALOG_ID]]', '[[frmAction]]', '[[component_action]]');";

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

	return cm_getClassByFrameworkCss($arrClass[1], "icon-tag");
}