<?php
/**
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

 // if (isset($cm->modules["restricted"]["events"]))
/*$cm->modules["restricted"]["events"]->addEvent("on_layout_process", function() {

});*/
/*
function mod_restricted_get_logo($logo = null, $restricted = false)
{

}*/

function on_load_section_admin($page, $tpl, $attr) {
    $cm = cm::getInstance();

    $attr["location_default"] = "admin";
    $attr["label"] = false;
    $attr["description_skip"] = true;
    $attr["readonly_skip"] = true;

    $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["sections"]["admin"], $attr);
    if($res_navbar["count"])
    {
        $tpl->parse("SectMenu", false);
    }
}
function on_load_section_brand($page, $tpl)
{
    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();
    $attr["location_default"] = "brand";

    $tpl->set_var("logo_class", $cm->oPage->frameworkCSS->getClass($framework_css["logo"]));

    $logo_url = $cm->modules["restricted"]["obj"]->getLogo($cm::env("MOD_RESTRICTED_LOGO_PATH"));

    if(Auth::isAdmin()) {
        if($logo_url) {
            $tpl->set_var("logo_url", $logo_url);
            $tpl->set_var("logo_name", $cm->modules["restricted"]["obj"]->getDomainName());
            $tpl->parse("SectLogo", false);
        } else {
            $tpl->set_var("host_name", $cm->modules["restricted"]["obj"]->getDomainName());
        }

        $tpl->set_var("nav_left_class", "domain");//$cm->oPage->frameworkCSS->getClass($framework_css["fullbar"]["nav"]["left"]));
        $tpl->set_var("more_icon", '<i class="' . $framework_css["icons"]["settings"] . '"></i>');
        $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
        $tpl->set_var("panel_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_header_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["header"]));
        $tpl->set_var("panel_body_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["def"]));
        $tpl->set_var("panel_links_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["links"]));
        $tpl->set_var("panel_footer_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["footer"]));

        $tpl->set_var("list_group_class", $framework_css["list"]["container"]);
        $tpl->set_var("list_group_horizontal_class", $framework_css["list"]["horizontal"]);
        $tpl->set_var("list_group_item_class", $framework_css["list"]["item"]);


        if($page->sections["admin"]["elements"] && $page->tpl_layer[0]->isset_var("brand") && !$page->tpl_layer[0]->isset_var("admin")) {
            $tpl->set_var("admin", $page->sections["admin"]["tpl"]->rpparse("SectMenu", false));
            $tpl->parse("SectBody", false);
        }

        $tpl->parse("SectBrandName", false);



        if(is_array($cm->modules["restricted"]["sections"]["brand"]["elements"])) {
            $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["sections"]["brand"]);
            if(is_array($res_navbar["count_position"]) && count($res_navbar["count_position"])) {
                foreach($res_navbar["count_position"] AS $position_name => $position_count) {
                    $tpl->parse("Sect" . $position_name, false);
                }
            }
        }

        $tpl->parse("SectBrandInfo", false);
        $tpl->parse("SectBrandPanel", false);
    } elseif($logo_url) {
        $tpl->set_var("logo_url", $logo_url);
        $tpl->parse("SectBrandNoPanel", false);
    }
}
function on_load_section_lang($page, $tpl, $attr)
{
    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();
    $attr["location_default"] = "lang";

    $locale = $cm->get_locale();

    if(is_array($locale["lang"]) && count($locale["lang"])) {

        $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
        $tpl->set_var("panel_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_body_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["def"]));

        $tpl->set_var("list_group_class", $framework_css["list"]["container"]);
        $tpl->set_var("list_group_horizontal_class", $framework_css["list"]["horizontal"]);
        $tpl->set_var("list_group_item_class", $framework_css["list"]["item"]);

        foreach($locale["lang"] AS $code => $params) {
            if($code == "current")
                continue;

            $tpl->set_var("code", $code);
            $tpl->set_var("code_lang", strtolower($code));
            $tpl->set_var("tiny_code", $params["tiny_code"]);
            $tpl->set_var("description", $params["description"]);
            $tpl->set_var("flag_lang", "flag " . $params["tiny_code"]);

            if($code == $locale["lang"]["current"]["code"]) {
                $tpl->set_var("current_class", $framework_css["current"]);
                $tpl->set_var("flag_lang_active", "flag " . $params["tiny_code"]);
                $tpl->set_var("code_active", $code);
                $tpl->parse("SectCurrentLang", false);
            } else {
                $mod_sec_setparams = $cm->router->getRuleById("mod_sec_setparams");
                if($mod_sec_setparams->reverse) {
                    $tpl->set_var("lang_url", "
						var that = this;
						jQuery.get('" . FF_SITE_PATH . $mod_sec_setparams->reverse . "?lang=" . $code . "', function(data) {
						jQuery('body').addClass('loading');
						window.location.reload();	
						});");
                } else {
                    $tpl->set_var("lang_url", "ff.urlAddParam(window.location.href, 'lang', " . $code . ")");
                }

                $tpl->set_var("show_files", "?lang=" . $code);
                $tpl->parse("SectLang", true);
            }
        }
    }
}
function on_load_section_breadcrumb($page, $tpl, $attr)
{
	$cm = cm::getInstance();

    $attr["location_default"] = "breadcrumb";

	if($page->sections["brand"]) {
		if(!$page->tpl_layer[0]->isset_var("brand")) {
            $framework_css = mod_restricted_get_framework_css();

            $page->sections["brand"]["tpl"]->set_var("SectLogo", "");

            if (MOD_SEC_MULTIDOMAIN &&  is_callable("mod_auth_get_domain"))
            {
                $ID_domain = mod_auth_get_domain();
            }

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

	$tpl->set_var("page-title", $page->getTitle());

}
function on_load_section_favorite($page, $tpl, $attr)
{
	$cm = cm::getInstance();

    $attr["location_default"] = "favorite";

	if(isset($cm->modules["restricted"]["sections"]["favorite"]) && is_array($cm->modules["restricted"]["sections"]["favorite"]["elements"]))
	{
		foreach ($cm->modules["restricted"]["sections"]["favorite"]["elements"] as $key => $value)
		{
			if($value["path"] != "/" && strpos($cm->path_info, $value["path"]) === 0)
				continue;

            if (!$cm->modules["restricted"]["obj"]->checkPermission($value["acl"])) {
                continue;
            }

			if(strpos($value["label"], "_") === 0) {
                $value["label"] = ffTemplate::_get_word_by_code(substr($value["label"], 1));
			}
			if($value["icon"]) {
                $tpl->set_var("icon", $cm->oPage->frameworkCSS->get($value["icon"], "icon-tag", "lg"));
            }

            if($attr["label"] === false) {
                $item_properties["title"] = 'title="' . $value["label"] . '"';
                if($value["icon"]) {
                    $tpl->set_var("label", "");
                } else {
                    $tpl->set_var("label", '<span>' . ucfirst(substr($value["label"], 0, 1)) . '</span>');
                }
            } else {
                if(strpos($value["label"], "_") === 0) {
                    $tpl->set_var("label", '<span>' . ffTemplate::_get_word_by_code(substr($value["label"], 1)) . '</span>');
                } else {
                    $tpl->set_var("label", '<span>' . $value["label"] . '</span>');
                }
            }

            if($item_properties) {
                $item_properties = implode(" ", $item_properties);
            }

            $tpl->set_var("item_properties", $item_properties);
            $tpl->set_var("path", $cm->oPage->site_path . $value["path"]);
			$tpl->parse("SectFavorite", true);
		}
	}
}
function on_load_section_topbar($page, $tpl, $attr)
{
	$cm = cm::getInstance();

	$globals_mod = ffGlobals::getInstance("__mod_restricted__");
    $attr["location_default"] = "topbar";

	if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
	{
        $cache_key = "/" . $attr["location_default"] . (MOD_RES_MEM_CACHING_BYPATH
            ? $cm->path_info
            : "default"
        );

        $cache = $cm->cache->get($cache_key, "/cm/mod/restricted/template/" . $attr["location_default"]);
	}
	if ($cache)
	{
        $globals_mod->access    = $cache["access"];
		$tpl->ParsedBlocks      = $cache["ParsedBlock"];
	}
	else
	{
		$framework_css = mod_restricted_get_framework_css();

		$res = array(
		    "count" => 0
            , "count_icon" => 0
            , "count_position" => null
        );

		foreach ($cm->modules["restricted"]["menu"] as $key => $value)
		{
            if (!$cm->modules["restricted"]["obj"]->checkPermission($value["acl"])) {
                continue;
            }
			if($attr["readonly_skip"] && $value["readonly"]) {
                continue;
            }
            if($value["hide"]) {
                continue;
            }
			$location = ($attr["default"] && !$value["location"]
                ? $attr["location_default"]
                : $value["location"]
            );

            if($location != $attr["location_default"]) {
                continue;
            }

			$globals_mod->access |= true;
//			ffErrorHandler::raise("ASD", E_USER_ERROR, null, get_defined_vars());

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

            if ($value["description"] && !$attr["description_skip"])
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
                                $action_icon = $cm->oPage->frameworkCSS->get($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

                            $action_label = $action_data["label"];
                        } elseif($cm->modules["restricted"]["menu_bypath"][$action_data]) {
                            $action_path = $action_data;
                            if($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
                                $action_icon = $cm->oPage->frameworkCSS->get($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");

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

            if($attr["submenu"]) {
                $child_class = null;
                $params = $attr;
                $params["prefix"] = "Child";

                $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["menu"][$key], $params);
                if($res_navbar["count"])
                {
                    if($value["collapse"] !== false)
                    {
                        $aria = ' aria-expanded="false"';
                        if($res_navbar["is_opened"] || $value["collapse"]) {
                            $item_class["current"] = $framework_css["current"];
                            $aria = ' aria-expanded="true"';
                            //      $child_class["current"] = $framework_css["collapse"]["current"];
                        }
                        $collapse = $framework_css["collapse"]["action"] . $aria;

                        if(!$value["readonly"] && !$value["jsaction"]) {
                            $item_properties["url"] = 'href="#topnav-' . $key . '"';
                            $item_properties["collapse"] = $collapse;
                        }
                        $child_class["collapse"] = $framework_css["collapse"]["pane"];
                        //$item_actions["dropdown"] = '<a href="#topnav-' . $key . '" class="' . ($res_navbar["is_opened"] ? $framework_css["icons"]["caret"] : $framework_css["icons"]["caret-collapsed"] . " " . $framework_css["collapse"]["menu"]) . '" ' . $framework_css["collapse"]["action"] . '></a>';
                        $item_actions["dropdown"] = '<a href="#topnav-' . $key . '" class="' . $framework_css["icons"]["caret-collapsed"] . " " . $framework_css["collapse"]["menu"] . '" ' . $collapse . '></a>';

                    }

                    $tpl->set_var("child_id", "topnav-" . $key);
                    if($child_class)
                        $tpl->set_var("child_class", implode(" ", $child_class));

                    $tpl->set_var("menu_class", $framework_css["menu"]["navbar"]);

                    if(is_array($res_navbar["count_position"]) && count($res_navbar["count_position"])) {
                        foreach($res_navbar["count_position"] AS $position_name => $position_count) {
                            $tpl->parse("SectChild" . $position_name, false);
                        }
                    }

                    $tpl->parse("SectChild", false);
                }
            }

            if(!$value["path"] && !$value["label"]) {
                continue;
            }

            if(!$item_properties["url"]) {
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

                if ($value["jsaction"] && $value["jsaction"] !== true) {
                    $path = $value["jsaction"];
                } elseif($value["redir"]) {
                    $path = $cm->oPage->site_path . $value["redir"];
                } else {
                    $path = $cm->oPage->site_path . $value["path"] . ($globals . $params ? "?" . $globals . $params : "");
                }

                if($value["readonly"]) {
                    $item_properties["url"] = 'data-url="' . $path . '"';
                } else {
                    if($value["dialog"])
                        $item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');"  . '"';
                    else
                        $item_properties["url"] = 'href="' . $path . '"';

                    if($value["rel"])
                        $item_properties["rel"] = 'rel="' . $value["rel"] . '"';
                }

            }

            if(($attr["icons"] === true || $attr["icons"] == "all" || $attr["icons"] == "mainmenu") && $value["icon"])
                $item_icon = $cm->oPage->frameworkCSS->get($value["icon"], "icon-tag", "lg");

            if($attr["label"] === false) {
                $item_properties["title"] = 'title="' . $value["label"] . '"';
                if($item_icon) {
                    $tpl->set_var("label", "");
                } else {
                    $tpl->set_var("label", '<span>' . ucfirst(substr($value["label"], 0, 1)) . '</span>');
                }
            } else {
                if(strpos($value["label"], "_") === 0) {
                    $tpl->set_var("label", '<span>' . ffTemplate::_get_word_by_code(substr($value["label"], 1)) . '</span>');
                } else {
                    $tpl->set_var("label", '<span>' . $value["label"] . '</span>');
                }
            }

            if($value["position"]) {
                $item_class["grid"] = $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["actions"][$value["position"]]);
            }
            if($value["class"]) {
                $item_class["custom"] = $value["class"];
            }
            if($value["badge"]) {
                $item_actions[] = '<span class="' . $cm->oPage->frameworkCSS->get("default", "badge") . '">' . $value["badge"] . '</span>';
            }

            /*
            if($item_class) {
                $item_properties["class"] = 'class="' . implode(" ", $item_class) . '"';
            }
            if($item_properties) {
                $item_properties = implode(" ", $item_properties);
            }

            if($item_actions) {
                $item_actions = '<span class="nav-controls">' . implode(" ", $item_actions) . '</span>';
            }
            $tpl->set_var("actions", $item_actions);
            $tpl->set_var("item_properties", $item_properties);
            $tpl->set_var("item_icon", $item_icon);
            $tpl->set_var("item_tag", $item_tag);
            */

            $cm->modules["restricted"]["obj"]->parseMenuItem($tpl, $item_properties, $item_class, $item_tag, $item_icon, $item_actions);

            /*if($res["count"]) {
                $tpl->parse("SectSeparator", false);
            }*/

            $parse_key = "Sect" . $attr["prefix"] . "Element";
            if($value["position"]) {
                $position = ucfirst($value["position"]);
                $parse_key .= $position;
                $res["count_position"][$position]++;
            }
            $tpl->parse($parse_key, true);
            $tpl->set_var("SectChild", "");

            if($item_icon) {
                $res["count_icon"]++;
            }
            $res["count"]++;
        }

		//reset($cm->modules["restricted"]["menu"]);

        if($res["count"]) {
			$tpl->set_var("menu_class", $framework_css["menu"][$attr["location_default"]] . ($res["count_icon"] ? " -withicons" : ""));

            if(is_array($res["count_position"]) && count($res["count_position"])) {
                foreach($res["count_position"] AS $position_name => $position_count) {
                    $tpl->parse("Sect" . $position_name, false);
                }
            }
            $tpl->parse("SectMenu", false);
        }

		if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
		{
            $cache_key = "/" . $attr["location_default"] . (MOD_RES_MEM_CACHING_BYPATH
                ? $cm->path_info
                : "default"
            );

            /** @var reference $access */
            $tmp = array(
                "ParsedBlock" => $tpl->ParsedBlocks
                , "access" => $globals_mod->access
            );

            $res = $cm->cache->set($cache_key, $tmp, "/cm/mod/restricted/template/" . $attr["location_default"]);
		}
	}
}
function on_load_section_navbar($page, $tpl, $attr)
{
	$cm = cm::getInstance();

    $globals_mod = ffGlobals::getInstance("__mod_restricted__");
    $attr["location_default"] = "navbar";
    if(!isset($attr["readonly_skip"])) {
        $attr["readonly_skip"] = true;
    }

    if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
    {
        $cache_key = "/navbar" . (MOD_RES_MEM_CACHING_BYPATH
                ? $cm->path_info
                : "default"
            );

        $cache = $cm->cache->get($cache_key, "/cm/mod/restricted/template/navbar");
    }
    if ($cache)
    {
        $globals_mod->access    = $cache["access"];
        $tpl->ParsedBlocks      = $cache["ParsedBlock"];
    }
    else
    {
        $framework_css = mod_restricted_get_framework_css();

        $tpl->set_var("navbar_class", $cm->modules["restricted"]["sel_topbar"]["name"]);

        $globals_mod->access |= true;

        $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["sel_topbar"], $attr);
        if($res_navbar["count"])
        {

            $tpl->set_var("menu_class", $framework_css["menu"]["topbar"]);

            $tpl->parse("SectMenu", false);
        }

        if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
        {
            $cache_key = "/navbar" . (MOD_RES_MEM_CACHING_BYPATH
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
function on_load_section_sidebar($page, $tpl, $attr)
{
    $cm = cm::getInstance();

    $globals_mod = ffGlobals::getInstance("__mod_restricted__");
    $attr["location_default"] = "sidebar";

    if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
    {
        $cache_key = "/" . $attr["location_default"] . (MOD_RES_MEM_CACHING_BYPATH
                ? $cm->path_info
                : "default"
            );

        $cache = $cm->cache->get($cache_key, "/cm/mod/restricted/template/" . $attr["location_default"]);
    }
    if ($cache)
    {
        $globals_mod->access    = $cache["access"];
        $tpl->ParsedBlocks      = $cache["ParsedBlock"];
    }
    else
    {
        $framework_css = mod_restricted_get_framework_css();

        $res = array(
            "count" => 0
            , "count_icon" => 0
            , "count_position" => null
        );

        foreach ($cm->modules["restricted"]["menu"] as $key => $value)
        {
            if (!$cm->modules["restricted"]["obj"]->checkPermission($value["acl"])) {
                continue;
            }
            if($attr["readonly_skip"] && $value["readonly"]) {
                continue;
            }
            if($value["hide"]) {
                continue;
            }

            $location = ($attr["default"] && !$value["location"]
                ? $attr["location_default"]
                : $value["location"]
            );

            if($location != $attr["location_default"]) {
                continue;
            }

            $globals_mod->access |= true;
//			ffErrorHandler::raise("ASD", E_USER_ERROR, null, get_defined_vars());

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

            if ($value["description"] && !$attr["description_skip"])
            {
                if(strpos($value["description"], "_") === 0) {
                    $description = ffTemplate::_get_word_by_code(substr($value["description"], 1));
                } else {
                    $description = $value["description"];
                }
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
                            if($action_data["icon"]) {
                                $action_icon = $cm->oPage->frameworkCSS->get($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");
                            }
                            $action_label = $action_data["label"];
                        } elseif($cm->modules["restricted"]["menu_bypath"][$action_data]) {
                            $action_path = $action_data;
                            if($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"]) {
                                $action_icon = $cm->oPage->frameworkCSS->get($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");
                            }
                            $action_label = $cm->modules["restricted"]["menu_bypath"][$action_data][0]["label"];
                        }

                        if(strpos($action_label, "_") === 0) {
                            $action_label = ffTemplate::_get_word_by_code(substr($action_label, 1));
                        }
                        $action_path = str_replace(array("[rel]", "[key]"), array($value["rel"], $key), $action_path);
                        if($action_data["dialog"] !== false) {
                            $action_path = 'javascript:ff.ffPage.dialog.doOpen(\'dialogResponsive\',\'' . $action_path . '\');';
                        }
                        $item_actions[] = '<a href="' . $action_path . '" class="' . $action_icon . '" title="' . $action_label . '"></a>';
                    }
                }
            }

            if($attr["submenu"] !== false) {
                $child_class = null;

                $params = $attr;
                $params["prefix"] = "Child";
                $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["menu"][$key], $params);
                if($res_navbar["count"])
                {
                    if($value["collapse"] !== false)
                    {
                        if($value["selected"]) {
                            $res_navbar["is_opened"] = true;
                        }
                        $aria = ' aria-expanded="false"';
                        if($res_navbar["is_opened"] || $value["collapse"]) {
                            $item_class["current"] = $framework_css["current"];
                            $child_class["current"] = $framework_css["collapse"]["current"];

                            $aria = ' aria-expanded="true"';
                        }
                        $collapse = $framework_css["collapse"]["action"] . $aria;

                        if(!$value["readonly"] && !$value["jsaction"]) {
                            $item_properties["url"] = 'href="#sidenav-' . $key . '"';
                            $item_properties["collapse"] = $collapse;
                        }

                        $child_class["collapse"] = $framework_css["collapse"]["pane"];
                        $item_actions["dropdown"] = '<a href="#sidenav-' . $key . '" class="' . ($res_navbar["is_opened"] ? $framework_css["icons"]["caret"] : $framework_css["icons"]["caret-collapsed"] . " " . $framework_css["collapse"]["menu"]) . '" ' . $collapse . '></a>';

                    }

                    $tpl->set_var("child_id", "sidenav-" . $key);
                    if($child_class) {
                        $tpl->set_var("child_class", implode(" ", $child_class));
                    }
                    $tpl->set_var("menu_class", $framework_css["menu"]["navbar"]);

                    $tpl->parse("SectChild", false);
                }
            }

            if($value["selected"]) {
                $item_class["current"] = $framework_css["current"];
            }

            if(!$value["path"] && !$value["label"]) {
                continue;
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

                if ($value["jsaction"] && $value["jsaction"] !== true) {
                    $path = $value["jsaction"];
                } elseif($value["redir"]) {
                    $path = $cm->oPage->site_path . $value["redir"];
                } else {
                    $path = $cm->oPage->site_path . $value["path"] . ($globals . $params ? "?" . $globals . $params : "");
                }
                if($value["readonly"]) {
                    $item_properties["url"] = 'data-url="' . $path . '"';
                } else {
                    if($value["dialog"]) {
                        $item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');" . '"';
                    } else {
                        $item_properties["url"] = 'href="' . $path . '"';
                    }
                    if($value["rel"]) {
                        $item_properties["rel"] = 'rel="' . $value["rel"] . '"';
                    }
                }

            }

            if(($attr["icons"] === true || $attr["icons"] == "all" || $attr["icons"] == "mainmenu") && $value["icon"]) {
                $item_icon = $cm->oPage->frameworkCSS->get($value["icon"], "icon-tag", "lg");
            }

            if($attr["label"] === false) {
                $item_properties["title"] = 'title="' . $value["label"] . '"';
                if($item_icon) {
                    $tpl->set_var("label", "");
                } else {
                    $tpl->set_var("label", '<span>' . ucfirst(substr($value["label"], 0, 1)) . '</span>');
                }
            } else {
                if(strpos($value["label"], "_") === 0) {
                    $tpl->set_var("label", '<span>' . ffTemplate::_get_word_by_code(substr($value["label"], 1)) . '</span>');
                } else {
                    $tpl->set_var("label", '<span>' . $value["label"] . '</span>');
                }
            }

            if($value["position"]) {
                $item_class["grid"] = $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["actions"][$value["position"]]);
            }
            if($value["class"]) {
                $item_class["custom"] = $value["class"];
            }

            if($value["badge"]) {
                $item_actions[] = '<span class="' . $cm->oPage->frameworkCSS->get("default", "badge") . '">' . $value["badge"] . '</span>';
            }

            /*
            if($item_class) {
                $item_properties["class"] = 'class="' . implode(" ", $item_class) . '"';
            }
            if($item_properties) {
                $item_properties = implode(" ", $item_properties);
            }

            if($item_actions)
                $item_actions = '<span class="nav-controls">' . implode(" " , $item_actions) . '</span>';

            $tpl->set_var("actions", $item_actions);
            $tpl->set_var("item_properties", $item_properties);
            $tpl->set_var("item_icon", $item_icon);
            $tpl->set_var("item_tag", $item_tag);
            */

            $cm->modules["restricted"]["obj"]->parseMenuItem($tpl, $item_properties, $item_class, $item_tag, $item_icon, $item_actions);

            //if($res["count"])
            //    $tpl->parse("SectSeparator", false);

            $parse_key = "Sect" . $attr["prefix"] . "Element";
            if($value["position"]) {
                $position = ucfirst($value["position"]);
                $parse_key .= $position;
                $res["count_position"][$position]++;
            }
            $tpl->parse($parse_key, true);
            $tpl->set_var("SectChild", "");

            if($item_icon) {
                $res["count_icon"]++;
            }
            $res["count"]++;
        }
        reset($cm->modules["restricted"]["menu"]);

        if($res["count"]) {
            $tpl->set_var("menu_class", $framework_css["menu"]["topbar"] . ($res["count_icon"] ? " -withicons" : ""));

            if(is_array($res["count_position"]) && count($res["count_position"])) {
                foreach($res["count_position"] AS $position_name => $position_count) {
                    $tpl->parse("Sect" . $position_name, false);
                }
            }
            $tpl->parse("SectMenu", false);
        }

        if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
        {
            $cache_key = "/" . $attr["location_default"] . (MOD_RES_MEM_CACHING_BYPATH
                    ? $cm->path_info
                    : "default"
                );

            /** @var reference $access */
            $tmp = array(
                "ParsedBlock" => $tpl->ParsedBlocks
            , "access" => $globals_mod->access
            );

            $res = $cm->cache->set($cache_key, $tmp, "/cm/mod/restricted/template/" . $attr["location_default"]);
        }
    }

    return $res;
}
function on_load_section_rightcol($page, $tpl, $attr) {
    $cm = cm::getInstance();

    $attr["location_default"] = "admin";

    $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu($tpl, $cm->modules["restricted"]["sections"]["rightcol"], $attr);
    if($res_navbar["count"])
    {
        $tpl->parse("SectMenu", false);
    }
}


function section_controller_title($page) {
    return $page->getTitle();
}