<?php
  function mod_restricted_get_framework_css() {
  	$framework_css = array(
  		"fullbar" => array(
  			"component" => array(
  				"class" => null
  			)
  			, "inner-wrap" => array(
  				"class" => null
  				, "col" => "row-fluid"
  			)
  			, "nav" => array(
  				"left" => array(
  					"class" => null
  					, "topbar" => "nav-brand"
  				)
  				, "form" => array(
  					"class" => null
  					, "topbar" => "nav-form"
  				)
  				, "right" => array(
  					"class" => "nav-right"
  					, "util" => "align-right"
  				)
			)  		
  			, "action" => array(
  				"toggle" => array(
  					"class" => null
  					, "topbar" => "hamburger"
  				)
  			)
  		)
		, "menu" => array(
			"topbar" => cm_getClassByFrameworkCss("topbar", "bar")
			, "navbar" => cm_getClassByFrameworkCss("navbar", "bar")
		)
		, "dropdown" => array(
			"container" => array(
				"class" => null
				, "panel" => "container"
				, "collapse" => "pane"
			)
			, "header" => array(
				"panel" => "heading"
				, "util" => "clear"
			)
			, "body" => array(
				"def" => array(
					"panel" => "body"
				)
				, "img" => array(
					"col" => array(
						"xs" => 0
						, "sm" => 4
						, "md" => 4
						, "lg" => 4
					)
				)
				, "desc" => array(
					"col" => array(
						"xs" => 12
						, "sm" => 8
						, "md" => 8
						, "lg" => 8
					)
				)
				, "links" => array(
					"class" => "panel-link"
					, "col" => array(
						"xs" => 12
						, "sm" => 12
						, "md" => 12
						, "lg" => 12
					)
					, "util" => "align-right"
				)
			)
			, "footer" => array(
				"panel" => "footer"
				, "util" => "clear"
			)
			, "actions" => array(
                "header" => array(
                    "button" => array("value" => "default", "params" => array("size" => "small"))
                )
			    , "body" => array(
                    "button" => array("value" => "link")
                )

			    , "footer" => array(
                    "button" => array("value" => "default", "params" => array("size" => "small"))
                )

                //todo da togliere e verificare
				, "profile" => array(
					"button" => array("value" => "primary", "params" => array("size" => "small"))
					, "icon" => "pencil"
				)
				, "users" => array(
					"button" => array("value" => "default", "params" => array("size" => "small"))
					, "icon" => "users"
				)
				, "domains" => array(
					"button" => array("value" => "default", "params" => array("size" => "small"))
					, "icon" => "database"
				)
				, "profiling" => array(
					"button" => array("value" => "default", "params" => array("size" => "small"))
					, "icon" => "users"
					, "util" => "right"	
				)
                , "settings" => array(
                    "button" => array("value" => "default", "params" => array("size" => "small"))
                    , "icon" => "cogs"
                    , "util" => "right"
                )
				, "logout" => array(
					"button" => array("value" => "default", "params" => array("size" => "small"))
					, "icon" => "power-off"
					, "util" => "right"
				)
			)
		)
		, "description" => cm_getClassByFrameworkCss("text-muted", "util")
		, "image" => array(
			"util" => array("corner-circle", "corner-thumbnail")
		)
		, "collapse" => array(
			"action" => cm_getClassByFrameworkCss("link", "data", "collapse")
			, "pane" => cm_getClassByFrameworkCss("pane", "collapse")
			, "current" => cm_getClassByFrameworkCss("current", "collapse")
			, "menu" => cm_getClassByFrameworkCss("menu", "collapse")
		)
		, "current" => cm_getClassByFrameworkCss("current", "util")
		, "icons" => array(
			"caret-collapsed" => cm_getClassByFrameworkCss("chevron-right", "icon")
			, "caret" => cm_getClassByFrameworkCss("chevron-down", "icon")
			, "settings" => cm_getClassByFrameworkCss("cog", "icon")
		)
		, "logo" => array(
			"class" => "brand-logo"
		)
	);
	
  	return $framework_css;
  
  }
  
 // if (isset($cm->modules["restricted"]["events"])) 
  	$cm->modules["restricted"]["events"]->addEvent("on_layout_process", "mod_restricted_cm_on_layout_process");
  	$cm->modules["restricted"]["events"]->addEvent("on_select", "mod_restricted_on_select");
  	
function mod_restricted_on_select($mod_restricted) {
	$cm = cm::getInstance();

    $filename = cm_cascadeFindTemplate("/javascript/ff.modules.restricted.js", "restricted");
	//$filename = cm_moduleCascadeFindTemplateByPath("restricted", "/javascript/ff.modules.restricted.js", $cm->oPage->theme);
	$ret = cm_moduleGetCascadeAttrs($filename);
	$cm->oPage->tplAddJS("ff.modules.restricted.js", array(
		"file" => $filename
		, "path" => $ret["path"]
	));
}

  	
function mod_restricted_cm_on_layout_process()
{
	$cm = cm::getInstance();
	if (isset($cm->oPage->sections["favorite"]))
		$cm->oPage->sections["favorite"]["events"]->addEvent("on_process", "mod_restricted_cm_on_load_favorite");

	if (isset($cm->oPage->sections["breadcrumb"]))
		$cm->oPage->sections["breadcrumb"]["events"]->addEvent("on_process", "mod_restricted_cm_on_load_breadcrumb");

	$cm->oPage->widgetLoad("dialog");
    $cm->oPage->widgets["dialog"]->process(
         "dialogResponsive"
         , array(
            "tpl_id" => null
            //"name" => "myTitle"
            , "url" => ""
            , "title" => ""
            , "callback" => ""
            , "class" => ""
            , "params" => array(
            )
            , "resizable" => true
            , "position" => "center"
            , "draggable" => true
            , "doredirects" => false
            , "responsive" => true
            , "unic" => true
            , "dialogClass" => "modal-lg"
        )
        , $cm->oPage
    );
}
  

function mod_restricted_cm_on_load_breadcrumb($page, $tpl)
{
	if (!mod_security_check_session(false))
		return;

	$cm = cm::getInstance();
	if($page->sections["brand"]) {
		if(!$page->tpl_layer[0]->isset_var("brand")) {
            $framework_css = mod_restricted_get_framework_css();

            $page->sections["brand"]["tpl"]->set_var("SectLogo", "");

            $ID_domain = mod_security_get_domain();

            $page->sections["brand"]["tpl"]->set_var("host_name", ($ID_domain 
                ? get_session("Domain")
                : CM_LOCAL_APP_NAME
            ));
            $page->sections["brand"]["tpl"]->set_var("more_icon", '<i class="' . $framework_css["icons"]["caret"] . '"></i>');
			$home = $page->sections["brand"]["tpl"]->rpparse("SectBrandName", false);
			$tpl->set_var("fixed_post_content", $page->sections["brand"]["tpl"]->rpparse("SectBrandInfo", false));
		}
	} else {
		$home = "Home";
	}
	
	$arrPath = explode("/", trim(ffCommon_dirname($cm->path_info), "/"));
	if(is_array($arrPath) && count($arrPath)) {
        $actual_path = "";
		foreach($arrPath AS $key => $path_name) {
			if($actual_path) {
				$tpl->set_var("crumb_label", ($cm->modules["restricted"]["menu_bypath"][$actual_path]["label"]
						? $cm->modules["restricted"]["menu_bypath"][$actual_path]["label"]
						: ucfirst($path_name)
					)
				);		

			} elseif($home) {
				$tpl->set_var("crumb_label", $home);
			} else {
				$actual_path .= "/" . $path_name;
				continue;
			}
			
			$actual_path .= "/" . $path_name;
			
			$tpl->set_var("crumb_url", FF_SITE_PATH . $actual_path);
			$tpl->parse("SectCrumb", true);
		}
	}
	
	$tpl->set_var("page-title", ($page->title == cm_getAppName()
		? ucwords(str_replace("-", " ", basename($cm->path_info)))
		: str_replace(" - " . cm_getAppName(), "", $page->title)
	));
	
}
  
function mod_restricted_cm_on_load_favorite($page, $tpl)
{
	if (!mod_security_check_session(false))
		return;

	$cm = cm::getInstance();
	if(isset($cm->modules["restricted"]["sections"]["favorite"]))
	{
		foreach ($cm->modules["restricted"]["sections"]["favorite"] as $key => $value)
		{
			if (strpos($key, "__") === 0)
				continue;

			if(is_array($value)) {
			    $path = $value["path"];
				$label = $value["label"];
				$icon = $value["icon"];
			} else {
				$path = (string) $value->path;
				$label = (string) $value->label;
				$icon = (string) $value->icon;
			}
			if($path != "/" && strpos($cm->path_info, $path) === 0)
				continue;

			if(strpos($label, "_") === 0) {
				$label = ffTemplate::_get_word_by_code(substr($label, 1));
			}

			if($icon)
				$tpl->set_var("icon", cm_getClassByFrameworkCss($icon, "icon-tag", "lg")); 


			$tpl->set_var("label", $label);
			$tpl->set_var("path", $path);
			$tpl->parse("SectFavorite", true);
		}
	}
}

  
function mod_restricted_on_tpl_layer_loaded($page, $tpl)
{
    $cm = cm::getInstance();

  	$framework_css = mod_restricted_get_framework_css();

  	$tpl->set_var("fullbar_class", cm_getClassByDef($framework_css["fullbar"]["component"]));
  	$tpl->set_var("toggle_class", cm_getClassByDef($framework_css["fullbar"]["action"]["toggle"]));
  	$tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
  	$tpl->set_var("nav_left_class", cm_getClassByDef($framework_css["fullbar"]["nav"]["left"]));
  	$tpl->set_var("nav_right_class", cm_getClassByDef($framework_css["fullbar"]["nav"]["right"]));
  	
  	$tpl->set_var("page-title", ($page->title == cm_getAppName()
		? ucwords(str_replace("-", " ", basename($cm->path_info)))
		: str_replace(" - " . cm_getAppName(), "", $page->title)
	));
    $tpl->set_var("CM_LOCAL_APP_NAME", ffCommon_specialchars(cm_getAppName()));
	if (MOD_RES_DEVELOPER)
		$tpl->parse("SectFooter", false);
}	

function mod_restricted_cm_on_load_topbar($page, $tpl, $location, $attr)
{
	$cm = cm::getInstance();
	$globals_mod = ffGlobals::getInstance("__mod_restricted__");
    $is_default = ($attr["default"] == "true" ? true : false);
    $hide_label = ($attr["label"] == "false" ? true : false);

    $success = false;
	if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
	{
		$hash = "__mod_restricted_" . $location . "_" . mod_res_get_hash();
        /** @var reference $success */
        $res = $cm->cache->get($hash, $success);
	}
	if ($success)
	{
		$tmp = unserialize($res);
		$access = $tmp["access"];
		$tpl->ParsedBlocks = $tmp["ParsedBlock"];
	}
	else
	{
		$framework_css = mod_restricted_get_framework_css();
		
        $count = 0;
        $count_icon = 0;

		$toskip = explode(",", MOD_SEC_PROFILING_SKIPSYSTEM);
		foreach ($cm->modules["restricted"]["menu"] as $key => $value)
		{
			$profile_check = MOD_SEC_PROFILING && (MOD_SEC_PROFILING_SKIPSYSTEM !== "*") && !in_array($key, $toskip) && !$value["profiling_skip"];
			
			if (
					!mod_restricted_checkacl_bylevel($value["acl"]) 
					|| ($profile_check && !mod_sec_checkprofile_bypath($value["path"]))
					|| ($is_default && isset($value["location"]) && $value["location"] != $location)
					|| (!$is_default && (!isset($value["location"]) || $value["location"] != $location))
				)
				continue;

			$globals_mod->access |= true;
//			ffErrorHandler::raise("ASD", E_USER_ERROR, null, get_defined_vars());

            if($value["settings"] && defined($value["settings"]))
                $hide = !constant($value["settings"]);

			if(!$value["hide"] && !$hide)
			{
				$item_tag = ($value["readonly"] 
					? ($value["readonly"] === true
						? "div"
						: $value["readonly"] 
					)
					: "a"
				);
				$item_class = array("key" => $key);
				$item_icon = null;
				$item_properties = null;
				$item_actions = null;
				$description = "";
				
				$tpl->set_var("name", $key);

				if ($value["description"])
	            {
	                if(strpos($value["description"], "_") === 0)
                		$description = ffTemplate::_get_word_by_code(substr($value["description"], 1));
	                else
                		$description = $value["description"];

					$description =  '<p class="' . $framework_css["description"] . '">' . $description . '</p>';	                 
				} 				
				
				$tpl->set_var("item_description", $description);

				if($value["actions"]) {
					if(is_array($value["actions"]) && count($value["actions"])) {
						foreach($value["actions"] AS $action_data) {
							$action_path = "";
							$action_label = "";
							$action_icon = $framework_css["icons"]["settings"];
							if(is_array($action_data)) {
								$action_path = $action_data["path"];
								if($action_data["icon"])
									$action_icon = cm_getClassByFrameworkCss($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

								$action_label = $action_data["label"];
							} elseif($cm->modules["restricted"]["menu_bypath"][$action_data]) {
								$action_path = $action_data;
								if($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
									$action_icon = cm_getClassByFrameworkCss($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");
									
								$action_label = $cm->modules["restricted"]["menu_bypath"][$action_data][0]["label"];
							}

					        if(strpos($action_label, "_") === 0)
					            $action_label = ffTemplate::_get_word_by_code(substr($action_label, 1));
							
							$action_path = str_replace(array("[rel]", "[key]"), array($value["rel"], $key), $action_path);
							if($action_data["dialog"] !== false)
								$action_path = 'javascript:ff.ffPage.dialog.doOpen(\'dialogResponsive\',\'' . $action_path . '\');';
								
							$item_actions[] = '<a href="' . $action_path . '" class="' . $action_icon . '" title="' . $action_label . '"></a>';						
						}
					}
				}

                if(MOD_RES_FULLBAR || array_key_exists("fullbar", $cm->modules["restricted"]))
				{
					if($location == "topbar") {
						$child_class = null;
						
						$res_navbar = mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["menu"][$key], "Child", null, true, $framework_css);
	                    if($res_navbar["count"]) 
	                    {	
	                    	if($value["collapse"] !== false) 
	                    	{
								if(!$value["readonly"]) {
									$item_properties["url"] = 'href="#nav-' . $key . '"';
                    				$item_properties["collapse"] = $framework_css["collapse"]["action"];
								}
                    			$child_class["collapse"] = $framework_css["collapse"]["pane"];
                    			$item_actions["dropdown"] = '<a href="#nav-' . $key . '" class="' . ($res_navbar["opened"] ? $framework_css["icons"]["caret"] : $framework_css["icons"]["caret-collapsed"] . " " . $framework_css["collapse"]["menu"]) . '" ' . $framework_css["collapse"]["action"] . '></a>';
		                        if($res_navbar["opened"] || $value["collapse"]) {
                        			$item_class["current"] = $framework_css["current"];
                        			$child_class["current"] = $framework_css["collapse"]["current"];
								}
							}

							$tpl->set_var("child_id", "nav-" . $key);
							if($child_class)
								$tpl->set_var("child_class", implode(" ", $child_class));
	                        $tpl->parse("SectChild", false);
						}
					} else {
						//mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["menu"][$key], "", $location, $is_default, $framework_css);
					}
                }
				
				if(!$value["path"] && !$value["label"])
					continue;
				
				if(strpos($value["label"], "_") === 0) {
					$label = ffTemplate::_get_word_by_code(substr($value["label"], 1));
				} else {
					$label = $value["label"];
				}	

				
				if(!$item_properties["url"]) {
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

					if ($value["jsaction"])
						$path = $value["jsaction"];
					elseif($value["redir"])
                        $path = $cm->oPage->site_path . $value["redir"];
					else
						$path = $cm->oPage->site_path . $value["path"] . ($globals . $params ? "?" . $globals . $params : "");

					if($value["dialog"])
						$item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');"  . '"';
					else
						$item_properties["url"] = 'href="' . $path . '"';
						
					if($value["rel"])
						$item_properties["rel"] = 'rel="' . $value["rel"] . '"';
				}

                if($value["icon"])
                	$item_icon = cm_getClassByFrameworkCss($value["icon"], "icon-tag", "lg");



				if($hide_label && $item_icon) {
					$item_properties["title"] = 'title="' . $label . '"';
					$tpl->set_var("label", "");
				} else {
					$tpl->set_var("label", '<span>' . $label . '</span>');
				}
				if($value["position"])
                    $item_class["grid"] = cm_getClassByDef($framework_css["dropdown"]["actions"][$value["position"]]);

	            if($value["class"])
	               $item_class["custom"] = $value["class"];

                if($item_class)
                    $item_properties["class"] = 'class="' . implode(" ", $item_class) . '"';

                if($item_properties)
                	$item_properties = implode(" ", $item_properties);
                	
                if($item_actions)
                	$item_actions = '<span class="nav-controls">' . implode(" " , $item_actions) . '</span>';

                $tpl->set_var("actions", $item_actions);
                $tpl->set_var("item_properties", $item_properties);
				$tpl->set_var("item_icon", $item_icon);
                $tpl->set_var("item_tag", $item_tag);

                if($count)
                	$tpl->parse("SectSeparator", false);
                
				$tpl->parse("SectElement" . ($value["position"] ? ucfirst($value["position"]) : ""), true);
				$tpl->set_var("SectChild", "");

                if($location == "topbar" && $item_icon)
                    $count_icon++;

                $count++;
			}
		}
		reset($cm->modules["restricted"]["menu"]);

        if($count) {
			$tpl->set_var("menu_class", $framework_css["menu"]["topbar"] . ($count_icon ? " -withicons" : ""));
            $tpl->parse("SectMenu", false);
        }

		if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
		{
            /** @var reference $access */
            $tmp = array(
				"ParsedBlock" => $tpl->ParsedBlocks
				, "access" => $globals_mod->access
			);
			$res = $cm->cache->set($hash, null, serialize($tmp), "__mod_restricted__");
		}
	}
}

function mod_restricted_cm_on_load_navbar($page, $tpl, $location, $attr)
{      
	$cm = cm::getInstance();
    $globals_mod = ffGlobals::getInstance("__mod_restricted__");
    $is_default = ($attr["default"] == "true" ? true : false);

    //if ($cm->modules["restricted"]["sel_topbar"] === null)
		//return;

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
	
	$tpl->set_var("navbar_class", $cm->modules["restricted"]["sel_topbar"]["name"]);
	// ---
	//if (!is_array($cm->modules["restricted"]["sel_topbar"]["elements"]) || !count($cm->modules["restricted"]["sel_topbar"]["elements"]))
	//	return;

    $success = false;
	if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
	{
		$hash = "__mod_restricted_" . $location . "_" . mod_res_get_hash();
        /** @var reference $success */
        $res = $cm->cache->get($hash, $success);
	}
	
	if ($success)
	{
		$tmp = unserialize($res);
		$access = $tmp["access"];
		$tpl->ParsedBlocks = $tmp["ParsedBlock"];
	}
	else
	{

	//die($location);
		// modifica di ALEX
		if($is_default) {
			$res_navbar = mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["sel_topbar"], "", $location, $is_default);
		} elseif($location && $cm->modules["restricted"]["sections"][$location]) {
			$res_navbar = mod_restricted_process_navbar($tpl, $cm->modules["restricted"]["sections"][$location], "", $location, $is_default);
		}

        $globals_mod->access |= true;

        if($res_navbar["count"]) 
            $tpl->parse("SectMenu", false);

		if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
		{
            /** @var reference $access */
            $tmp = array(
				"ParsedBlock" => $tpl->ParsedBlocks
				, "access" => $globals_mod->access
			);
			$res = $cm->cache->set($hash, null, serialize($tmp), "__mod_restricted__");
		}
	}
}

// funzione di ALEX
function mod_restricted_process_navbar(&$tpl, $sel_topbar, $prefix = "", $location = "navbar", $default = true, $framework_css = null)
{
    $cm = cm::getInstance();
    $count = 0;
    $is_opened = false;

    if(strlen($prefix))
        $tpl->set_var("Sect" . $prefix . "Element", "");

	//$tpl->set_var("navbar_class", preg_replace("/[^[:alnum:]]+/", "", $sel_topbar["label"]));
	if(!$framework_css)
    	$framework_css = mod_restricted_get_framework_css();

    if(is_array($sel_topbar) && array_key_exists("elements", $sel_topbar) && count($sel_topbar["elements"]))
	{
        foreach ($sel_topbar["elements"] as $key => $value)
        {
            if (
                    !mod_restricted_checkacl_bylevel($value["acl"]) 
					|| (
							!$value["profiling_skip"]
							&& !mod_sec_checkprofile_bypath($value["path"])
						)
                    || (isset($value["hide"]) && $value["hide"])
					|| ($default && isset($value["location"]) && $value["location"] != $location)
					|| (!$default && (!isset($value["location"]) || $value["location"] != $location))
                )
                continue;

           // $tpl->set_var("Sect" . $prefix . "Link", "");
           // $tpl->set_var("Sect" . $prefix . "Heading", "");
			$item_tag = ($value["readonly"] 
				? ($value["readonly"] === true
					? "div"
					: $value["readonly"] 
				)
				: "a"
			);
			$item_class = null;
			$item_icon = null;
			$item_properties = null;
			$item_actions = null;
			$description = "";

            if(strpos($value["label"], "_") === 0)
                $tpl->set_var("label", '<span>' . ffTemplate::_get_word_by_code(substr($value["label"], 1)) . '</span>');
            else
                $tpl->set_var("label", '<span>' . $value["label"] . '</span>');

            if ($value["description"])
            {
                if(strpos($value["description"], "_") === 0)
                	$description = ffTemplate::_get_word_by_code(substr($value["description"], 1));
                else
                	$description = $value["description"];

                 $description = '<p class="' . $framework_css["description"] . '">' . $description . '</p>';
			} 
			                    
            $tpl->set_var("description", $description);

 			if($value["actions"]) {
				if(is_array($value["actions"]) && count($value["actions"])) {
					foreach($value["actions"] AS $action_data) {
						$action_path = "";
						$action_label = "";
						$action_icon = $framework_css["icons"]["settings"];
						if(is_array($action_data)) {
							$action_path = $action_data["path"];
							if($action_data["icon"])
								$action_icon = cm_getClassByFrameworkCss($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

							$action_label = $action_data["label"];
						} elseif($cm->modules["restricted"]["menu_bypath"][$action_data]) {
							$action_path = $action_data;
							if($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
								$action_icon = cm_getClassByFrameworkCss($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");
								
							$action_label = $cm->modules["restricted"]["menu_bypath"][$action_data][0]["label"];
						}

				        if(strpos($action_label, "_") === 0)
				            $action_label = ffTemplate::_get_word_by_code(substr($action_label, 1));
						
						$action_path = str_replace(array("[rel]", "[key]"), array($value["rel"], $key), $action_path);
						if($action_data["dialog"] !== false)
							$action_path = 'javascript:ff.ffPage.dialog.doOpen(\'dialogResponsive\',\'' . $action_path . '\');';
						
						$item_actions[] = '<a href="' . $action_path . '" class="' . $action_icon . '" title="' . $action_label . '"></a>';
					}
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

			if ($value["jsaction"])
				$path = $value["jsaction"];
            elseif($value["redir"])
                $path = $cm->oPage->site_path . $value["redir"];
			else
				$path = $cm->oPage->site_path . $value["path"] . ($globals . $params ? "?" . $globals . $params : "");

			if($value["dialog"])
				$item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');"  . '"';
			else
				$item_properties["url"] = 'href="' . $path . '"';

			if($value["rel"])
				$item_properties["rel"] = 'rel="' . $value["rel"] . '"';
				
			if($value["icon"])
				$item_icon = cm_getClassByFrameworkCss($value["icon"], "icon-tag", "lg");
            
            if($value["class"])
               $item_class["custom"] = $value["class"];

            if ($value["selected"])
			{
				$item_class["current"] = $framework_css["current"];
                $is_opened = true;
			}

			if($item_class)
				$item_properties["class"] = 'class="' . implode(" ", $item_class). '"';

            if($item_properties)
                $item_properties = implode(" ", $item_properties);

            if($item_actions)
                $item_actions = '<span class="nav-controls">' . implode(" " , $item_actions) . '</span>';
			
			$tpl->set_var("actions", $item_actions);
			$tpl->set_var("item_properties", $item_properties);
			$tpl->set_var("item_icon", $item_icon);
			$tpl->set_var("item_tag", $item_tag);

            $tpl->parse("Sect" . $prefix . "Element", true);
            $count++;
        }
    }
    
    if($count) {
    	if($location == "navbar")
        	$tpl->set_var("menu_class", $framework_css["menu"]["topbar"]);
        else 
        	$tpl->set_var("menu_class", $framework_css["menu"]["navbar"]);
        $tpl->parse("SectMenu", false);
    }
    
    reset($sel_topbar);

    return array("count" => $count, "opened" => $is_opened);
}