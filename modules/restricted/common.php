<?php
class cmRestricted {
    private $config = array();
    private $cm = null;

    public function __construct()
    {
        $this->load();
    }

    private function load() {
        $this->cm = &cm::getInstance();

        $this->setConfigPages($this->cm->config["pages"]);

        $this->setLayout();

    }

    private function setConfigMenu ($data, &$config, $father_key = null) {
        if(is_array($data) && count($data)) {
            foreach($data AS $key => $value) {
                if($key === "comment") {
                    continue;
                }

                if(!$value["@attributes"] && is_array($value[0])) {
                    $this->setConfigMenu($value, $this->config["menu"], $key);
                } else {
                    if($father_key) {
                        $key = $father_key . "-" . $key;
                    }

                    $config[$key] = (array)$value["@attributes"];
                    unset($value["@attributes"]);

                    $config[$key]["name"] = ($father_key
                        ? $father_key
                        : $key
                    );

                    $config[$key] = $this->setMenuDefault($config[$key]);

                    if ($config[$key]["favorite"]) {
                        $this->config["sections"]["favorite"]["elements"][$key] = $config[$key];
                    }

                    if ($config[$key]["location"]) {
                        $arrLocation = explode(",", $config[$key]["location"]);
                        //unset($config[$key]["location"]);
                        foreach ($arrLocation AS $location) {
                            if(isset($this->config["sections"][trim($location)])) {
                                $this->config["sections"][trim($location)]["elements"][$key] = $config[$key];
                            }
                        }
                    } else {
                        if ($this->config["menu"][$key]["path"]) {
                            $this->config["menu_bypath"][$this->config["menu"][$key]["path"]][] = &$config[$key];
                        }
                        if (is_array($value) && count($value)) {
                            $this->config["menu"][$key]["elements"] = array();
                            $this->setConfigMenu($value, $this->config["menu"][$key]["elements"]);
                        }
                    }
                }
            }
        }
    }
    private function setConfigSections($data) {
        if(is_array($data) && count($data)) {
            foreach ($data AS $key => $value) {
                $this->config["sections"][$key]["attributes"] = ($value["@attributes"]
                    ? $value["@attributes"]
                    : $value
                );
            }
        }
    }

    private function setConfigPages($data) {
        if(!isset($data["page"][0])) {
            $data["page"] = array($data["page"]);
        }

        foreach($data["page"] AS $page) {
            $this->setConfigPage($page);
        }
     }
    private function setConfigPage($data, $layer = null) {
        $params = $data["@attributes"];

        $path = $params["path"];
        unset($params["path"]);
        unset($data["@attributes"]);

        $params = array_replace($params, $data);

        $this->config["pages"][$path] = array_replace((array) $this->config["pages"][$path], $params);
        if($layer) {
            $this->config["pages"][$path]["layer"] = $layer;
        }


    }

    private function setContextMenu() {
        Filemanager::scan(CM_CONTENT_ROOT . cm::env("MOD_RESTRICTED_PATH"), Filemanager::SCAN_DIR_RECURSIVE, function($dir) {
            $this->addMenu($dir);
        });
    }
    public function checkPermission($acl = null) {
        return ($this->cm->modules["auth"]["obj"]
            ? $this->cm->modules["auth"]["obj"]->checkPermission($acl)
            : true
        );
    }

    public function getLogo($logo = null) {
        if($logo && is_file(FF_DISK_PATH . $logo)) {
            $logo_url = $logo;

        } else {
            $logo_url = $this->cm->oPage->getAsset(array("restricted", "login", "logo", "nobrand"), "images");
        }

        return $logo_url;
    }

    /**
     * @param $data array(
     *      "path" => ""
     *      "label" => ""
     *      "badge" => ""
     *      "icon" => ""
     *      "actions" => ""
     *      "params" => ""
     *      "acl" => ""
     *      "location" => ""
     *      "position" => ""
     *      "settings" => ""
     *      "hide" => ""
     *      "redir" => ""
     *      "jsaction" => ""
     *      "profiling_skip" => ""
     *      "description" => ""
     *      "description_skip" => ""
     *      "readonly" => ""
     *      "readonly_skip" => ""
     *      "dialog" => ""
     *      "class" => ""
     *      "collapse" => ""
     *      "favorite" => ""
     *      "rel" => ""
     * )
     *
     * @param $key
     * @param null $subkey
     */
    public function addMenu($data, $key = null, $subkey = null) {

        $root_path = CM_CONTENT_ROOT;

        if(!is_array($data) && !$key && !$subkey) {
            $data = array(
                "path" => $data
                , "jsaction" => true
            );
        }
        if(strpos($data["path"], $root_path) === 0) {
            $data["path"] = str_replace($root_path, "", $data["path"]);
        }

        if(dirname($data["path"]) == cm::env("MOD_RESTRICTED_PATH")) {
            $key = basename($data["path"]);
        } else {
            $key = basename(dirname($data["path"]));
            $subkey = basename($data["path"]);
        }

        $data["name"] = ($subkey
            ? $subkey
            : $key
        );

        $data = $this->setMenuDefault($data);

        if ($data["favorite"]) {
            $this->config["sections"]["favorite"]["elements"][$key] = $data;
        }

        if ($data["location"]) {
            $arrLocation = explode(",", $data["location"]);
            //unset($config[$key]["location"]);
            foreach ($arrLocation AS $location) {
                if(isset($this->config["sections"][trim($location)])) {
                    $this->config["sections"][trim($location)]["elements"][$key] = $data;
                }
            }
        } elseif($subkey) {
            $this->config["menu"][$key]["elements"][$subkey] = $data;
            if($this->config["menu_bypath"][$key]["elements"][$subkey]["path"]) {
                $this->config["menu_bypath"][$this->config["menu_bypath"][$key]["elements"][$subkey]["path"]][] = &$this->config["menu"][$key]["elements"][$subkey];
            }
        } else {
            $this->config["menu"][$key] = $data;
            if($this->config["menu"][$key]["path"]) {
                $this->config["menu_bypath"][$this->config["menu"][$key]["path"]][] = &$this->config["menu"][$key];
            }
        }


    }

    private function setMenuDefault($data) {
        if (!$data["label"]) {
            $data["label"] = ucwords(str_replace("-", " " , $data["name"]));
        }
        if (strpos($data["label"], "_") === 0) {
            $data["label"] = ffTranslator::get_word_by_code(substr($data["label"], 1));
        }
        if(strpos($data["description"], "_") === 0) {
            $data["description"] = ffTemplate::_get_word_by_code(substr($data["description"], 1));
        }
        if ($data["actions"]) {
            $data["actions"] = explode(",", $data["actions"]);
        }
        if ($data["acl"]) {
            $data["acl"] = explode(",", $data["acl"]);
        }

        return $data;
    }


    public function process() {
        require("ui.php");

        $this->setConfigSections($this->cm->config["sections"]);
        $this->setConfigMenu($this->cm->config["menu"], $this->config["menu"]);

        $this->setContextMenu();

        if(cm::env("MOD_RESTRICTED_DYNAMIC_TABS") && !defined("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID"))
            define("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID", true);

        //todo: da sistemare il config con ["config"]
        $this->cm->modules["restricted"] = array_replace($this->cm->modules["restricted"], $this->config);

        $this->loadDialog();
        $this->processMenu();

        $this->cm->oPage->addEvent("on_tpl_layer_loaded", function($page, $tpl) {
            $cm = cm::getInstance();

            if (isset($cm->modules["restricted"]["sections"]))
            {
                foreach ($cm->modules["restricted"]["sections"] as $key => $value)
                {
                    if (!isset($cm->modules["restricted"]["options"]["layout"][$key]) || strlen($cm->modules["restricted"]["options"]["layout"][$key]))
                    {
                        if(is_callable("on_load_section_" . $key)) {
                            $value["events"]["on_load_template"] = "on_load_section_" . $key;
                        }
                        $cm->oPage->addSection($key, $value);
                    }
                }
                reset($cm->modules["restricted"]["sections"]);
            }

            $framework_css = mod_restricted_get_framework_css();

            $tpl->set_var("toggle_class", Cms::getInstance("frameworkcss")->getClass($framework_css["layer"]["action"]["toggle"]));
            $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
            $tpl->set_var("nav_left_class", Cms::getInstance("frameworkcss")->getClass($framework_css["layer"]["nav"]["left"]));
            $tpl->set_var("nav_right_class", Cms::getInstance("frameworkcss")->getClass($framework_css["layer"]["nav"]["right"]));

            $tpl->set_var("page-title", ($page->title == cm_getAppName()
                ? ucwords(str_replace("-", " ", basename($this->cm->path_info)))
                : str_replace(" - " . cm_getAppName(), "", $page->title)
            ));
            $tpl->set_var("CM_LOCAL_APP_NAME", ffCommon_specialchars(cm_getAppName()));
            if (cm::env("MOD_RESTRICTED_DEVELOPER")) {
                $tpl->parse("SectFooter", false);
            }
        });
    }
    /*public function getTemplatePath($filename, $base_path = __DIR__) {
        return ($this->cm->oPage->getTheme()
        && $this->cm->oPage->layer
        && is_file(ff_getThemeDir($this->cm->oPage->getTheme()) . FF_THEME_DIR . "/" . $this->cm->oPage->getTheme(). "/layouts/" . $filename)
            ? FF_THEME_DIR . "/" . $this->cm->oPage->getTheme(). "/layouts"
            : __DIR__ . FF_THEME_DIR . "/" . cm_getMainTheme() . "/layouts"
        );
    }*/
    private function setLayout() {
        $pathinfo = $this->cm->path_info;
        do {
            if($this->config["pages"][$pathinfo]) {
                $this->cm->layout_vars = $this->config["pages"][$pathinfo];
                break;
            }

        } while($pathinfo != DIRECTORY_SEPARATOR && $pathinfo = dirname($pathinfo));

        if(!$this->cm->layout_vars["layout"] && strpos($this->cm->path_info, cm::env("MOD_RESTRICTED_PATH")) === 0) {
            $this->cm->layout_vars["layout"] = cm::env("MOD_RESTRICTED_LAYOUT");
        }
    }



    private function loadDialog() {

        //if (isset($cm->oPage->sections["favorite"]))
        //	$cm->oPage->sections["favorite"]["events"]->addEvent("on_process", "mod_restricted_cm_on_load_favorite");

        //if (isset($cm->oPage->sections["breadcrumb"]))
        //	$cm->oPage->sections["breadcrumb"]["events"]->addEvent("on_process", "mod_restricted_cm_on_load_breadcrumb");

        $this->cm->oPage->widgetLoad("dialog");
        $this->cm->oPage->widgets["dialog"]->process(
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
            , $this->cm->oPage
        );
    }

    private function processMenu() {
        $this->cm->modules["restricted"]["sel_topbar"] = null;
        $this->cm->modules["restricted"]["sel_navbar"] = null;

        //------------------------------------------------------------------------------------------------------------------------------------------------
        // Rileva la topbar e la navbar selezionata
        if ($this->cm->modules["restricted"]["sel_topbar"] === null)
        {
            $restricted_path = cm::env("MOD_RESTRICTED_PATH");
            if($this->cm->real_path_info && $this->cm->real_path_info != "/") {
                $path_parts = explode("/", ltrim($this->cm->real_path_info, "/"), 2);
                $restricted_path .= "/" . $path_parts[0];
            }

            if($this->cm->modules["restricted"]["menu_bypath"][$restricted_path]) {
                $this->cm->modules["restricted"]["sel_topbar"] =& $this->cm->modules["restricted"]["menu_bypath"][$restricted_path][0];
                $this->cm->modules["restricted"]["sel_topbar"]["selected"] = true;


                $nav_key = str_replace("/", "_", $path_parts[1]);
                $this->cm->modules["restricted"]["sel_navbar"] = null;
                if($this->cm->modules["restricted"]["sel_topbar"]["elements"][$nav_key]) {
                    $this->cm->modules["restricted"]["sel_navbar"] =& $this->cm->modules["restricted"]["sel_topbar"]["elements"][$nav_key];
                    $this->cm->modules["restricted"]["sel_navbar"]["selected"] = true;

                }
            }
        }

        //------------------------------------------------------------------------------------------------------------------------------------------------
        // Redirect
        if ($this->cm->modules["restricted"]["sel_topbar"]["redir"]) {
            ffRedirect(FF_SITE_PATH . $this->cm->modules["restricted"]["sel_topbar"]["redir"] . "?" . $this->cm->oPage->get_globals());
        }

        //------------------------------------------------------------------------------------------------------------------------------------------------
        // set default if not found topbar
        if ($this->cm->modules["restricted"]["sel_topbar"] === null)
        {
            if (isset($this->cm->modules["restricted"]["menu"]["default"]))
            {
                $this->cm->modules["restricted"]["sel_topbar"] =& $this->cm->modules["restricted"]["menu"]["default"];
                $this->cm->modules["restricted"]["sel_topbar"]["selected"] = true;
            }
            elseif (isset($this->cm->modules["restricted"]["menu_bypath"]["/"]))
            {
                $this->cm->modules["restricted"]["sel_topbar"] =& $this->cm->modules["restricted"]["menu_bypath"]["/"][0];
                $this->cm->modules["restricted"]["sel_topbar"]["selected"] = true;
            }
        }

        //------------------------------------------------------------------------------------------------------------------------------------------------
        // check permission
        if ($this->cm->modules["restricted"]["sel_topbar"] === null ) {
            ffDialog(false, "okonly", "Access Denied", "Restricted Area not Set", FF_SITE_PATH . "/", FF_SITE_PATH . "/", FF_SITE_PATH . "/dialog");
        } else if (!$this->checkPermission($this->cm->modules["restricted"]["sel_topbar"]["acl"])) {
            foreach ($this->cm->modules["restricted"]["menu"] as $key => $value)
            {
                if ($this->checkPermission($value)) {
                    ffRedirect(FF_SITE_PATH . $value["path"] . "?" . $this->cm->oPage->get_globals());
                }
            }
            ffDialog(false, "okonly", "Access Denied", "Access Denied", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
        }
        else
        {
            if ($this->cm->modules["restricted"]["sel_navbar"] === null && strlen($this->cm->modules["restricted"]["sel_topbar"]["redir"])) {
                ffRedirect(FF_SITE_PATH . $this->cm->modules["restricted"]["sel_topbar"]["redir"] . "?" . $this->cm->oPage->get_globals());
            }
            if ($this->cm->modules["restricted"]["sel_navbar"] !== null
                && !$this->checkPermission($this->cm->modules["restricted"]["sel_navbar"]["acl"])
            )
            {
                if (count($this->cm->modules["restricted"]["sel_topbar"]["elements"]))
                {
                    foreach ($this->cm->modules["restricted"]["sel_topbar"]["elements"] as $key => $value)
                    {
                        if ($this->checkPermission($value["acl"])) {
                            ffRedirect(FF_SITE_PATH . $value["path"] . "?" . $this->cm->oPage->get_globals());
                        }
                    }
                }
                ffDialog(false, "okonly", "Access Denied", "Insufficent Permission", FF_SITE_PATH . "/", FF_SITE_PATH . "/",  FF_SITE_PATH . "/dialog");
            }
        }
    }

    public function parseMenu(&$tpl, $sel_topbar, $attr = array()) {
        $res = array(
            "count" => 0
        , "is_opened" => false
        , "count_position" => null
        );

        $framework_css = (is_array($attr["framework_css"])
            ? $attr["framework_css"]
            : mod_restricted_get_framework_css()
        );

        if($attr["prefix"]) {
            $tpl->set_var("Sect" . $attr["prefix"] . "Element", "");
        }
        //$tpl->set_var("navbar_class", preg_replace("/[^[:alnum:]]+/", "", $sel_topbar["label"]));
        //var_dump(count($sel_topbar["elements"]));
//print_r(count($sel_topbar["elements"]));
        if(is_array($sel_topbar) && array_key_exists("elements", $sel_topbar) && count($sel_topbar["elements"]))
        {
            // echo "CCCC  ";

            foreach ($sel_topbar["elements"] as $key => $value)
            {
                if (!$this->checkPermission($value["acl"])) {
                    continue;
                }
                if($attr["readonly_skip"] && $value["readonly"]) {
                    continue;
                }
                if($value["hide"]) {
                    continue;
                }
                // $tpl->set_var("Sect" . $attr["prefix"] . "Link", "");
                // $tpl->set_var("Sect" . $attr["prefix"] . "Heading", "");
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

                if ($value["description"] && !$attr["description_skip"]) {
                    $description = '<p class="' . $framework_css["description"] . '">' . $value["description"] . '</p>';
                }

                $tpl->set_var("description", $description);

                if($value["actions"]) {
                    if(is_array($value["actions"]) && count($value["actions"])) {
                        foreach($value["actions"] AS $action_data) {
                            $action_path = "";
                            $action_label = "";
                            $action_icon = $framework_css["icons"]["settings"];
                            $action_data_dialog = true;
                            if(is_array($action_data)) {
                                $action_path = $action_data["path"];
                                if($action_data["icon"])
                                    $action_icon = Cms::getInstance("frameworkcss")->get($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

                                $action_label = $action_data["label"];
                                if($action_data["dialog"] !== false)
                                    $action_data_dialog = false;
                            } elseif($this->cm->modules["restricted"]["menu_bypath"][$action_data]) {
                                $action_path = $action_data;
                                if($this->cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
                                    $action_icon = Cms::getInstance("frameworkcss")->get($this->cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");

                                $action_label = $this->cm->modules["restricted"]["menu_bypath"][$action_data][0]["label"];
                            }

                            if(strpos($action_label, "_") === 0)
                                $action_label = ffTemplate::_get_word_by_code(substr($action_label, 1));

                            $action_path = str_replace(array("[rel]", "[key]"), array($value["rel"], $key), $action_path);
                            if($action_data_dialog)
                                $action_path = 'javascript:ff.ffPage.dialog.doOpen(\'dialogResponsive\',\'' . $action_path . '\');';

                            $item_actions[] = '<a href="' . $action_path . '" class="' . $action_icon . '" title="' . $action_label . '"></a>';
                        }
                    }
                }

                /*
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
                */
                if ($value["jsaction"] && $value["jsaction"] !== true) {
                    $path = $value["jsaction"];
                } elseif($value["redir"]) {
                    $path = $this->cm->oPage->site_path . $value["redir"];
                } else {
                    $path = $this->cm->oPage->site_path . $value["path"]; /*. ($globals . $params ? "?" . $globals . $params : "")*/
                }
                if($value["readonly"]) {
                    $item_properties["url"] = 'data-url="' . $path . '"';
                } else {
                    if ($value["dialog"])
                        $item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');" . '"';
                    else
                        $item_properties["url"] = 'href="' . $path . '"';
                }
                if($value["rel"])
                    $item_properties["rel"] = 'rel="' . $value["rel"] . '"';

                if(($attr["icons"] == "all" || $attr["icons"] == "submenu") && $value["icon"])
                    $item_icon = Cms::getInstance("frameworkcss")->get($value["icon"], "icon-tag", "lg");

                $label = "";
                if($attr["label"] === false) {
                    $item_properties["title"] = 'title="' . $value["label"] . '"';
                    if(!$item_icon) {
                        $label = ucfirst(substr($value["label"], 0, 1));
                    }
                } else {
                    $label = $value["label"];
                }
                if($label) {
                    if($value["actions"] || $value["description"]) {
                        $tpl->set_var("label", '<span>' . $label . '</span>');
                    } else {
                        $tpl->set_var("label", $label);
                    }
                }


                if($framework_css["actions"][$value["name"]]) {
                    $item_class["default"] = Cms::getInstance("frameworkcss")->getClass($framework_css["actions"][$value["name"]]);
                } elseif($framework_css["actions"][$value["position"]]) {
                    $item_class["default"] = Cms::getInstance("frameworkcss")->getClass($framework_css["actions"][$value["position"]]);
                }

                if($value["class"]) {
                    $item_class["custom"] = $value["class"];
                }
                if ($value["selected"])
                {
                    $item_class["current"] = $framework_css["current"];
                    $res["is_opened"] = true;
                }

                if($value["badge"]) {
                    $item_actions[] = '<span class="' . Cms::getInstance("frameworkcss")->get("default", "badge") . '">' . $value["badge"] . '</span>';
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
                $tpl->set_var("item_tag", $item_tag);*/

                $this->parseMenuItem($tpl, $item_properties, $item_class, $item_tag, $item_icon, $item_actions);
//echo "Sect" . $attr["prefix"] . "Element";

                $parse_key = "Sect" . $attr["prefix"] . "Element";
                if($value["position"]) {
                    $position = ucfirst($value["position"]);
                    $parse_key .= $position;
                    $res["count_position"][$position]++;
                }
                $tpl->parse($parse_key, true);

                $res["count"]++;
            }
        }

        return $res;
    }

    public function parseMenuItem($tpl, $item_properties, $item_class, $item_tag, $item_icon, $item_actions) {
        if($item_class) {
            $item_properties["class"] = 'class="' . implode(" ", $item_class) . '"';
        }
        foreach ($item_class AS $key => $class) {
            $tpl->set_var("class:" . $key, $class);
        }
        foreach ($item_properties AS $key => $property) {
            $tpl->set_var($key, $property);
        }
        foreach ($item_actions AS $key => $action) {
            $tpl->set_var($key, $action);
        }

        $tpl->set_var("actions", '<span class="nav-controls">' . implode(" ", $item_actions) . '</span>');
        $tpl->set_var("item_properties", implode(" ", $item_properties));
        $tpl->set_var("item_icon", $item_icon);
        $tpl->set_var("item_tag", $item_tag);
    }
}



$cm = cm::getInstance();

$cm->modules["restricted"]["obj"] = new cmRestricted();
/*
$cm->modules["restricted"]["options"] = array();
$cm->modules["restricted"]["menu"] = array();
$cm->modules["restricted"]["menu_bypath"] = array();
$cm->modules["restricted"]["layout_bypath"] = array();

if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
{
    $cache = ffCache::getInstance();
	// calculate hash
    $cache_key = (MOD_RES_MEM_CACHING_BYPATH
                    ? $cm->path_info
                    : "default"
                );


    $cm->modules["restricted"]["options"]           = $cache->get($cache_key, "/cm/mod/restricted/options");
    $cm->modules["restricted"]["menu"]              = $cache->get($cache_key, "/cm/mod/restricted/menu");
    $cm->modules["restricted"]["layout_bypath"]     = $cache->get($cache_key, "/cm/mod/restricted/layout_bypath");
    $cm->modules["restricted"]["settings"]          = $cache->get($cache_key, "/cm/mod/restricted/settings");

	if (is_array($cm->modules["restricted"]["menu"]) && count($cm->modules["restricted"]["menu"]))
	{
		foreach ($cm->modules["restricted"]["menu"] as $key => $value)
		{
			$cm->modules["restricted"]["menu_bypath"][$value["path"]][] =& $cm->modules["restricted"]["menu"][$key];
			if (is_array($value["elements"]) && count($value["elements"]))
			{
				foreach ($value["elements"] as $subkey => $subvalue)
				{
					if (strlen($subvalue["path"]))
						$cm->modules["restricted"]["menu_bypath"][$subvalue["path"]][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
				}
				reset($value["elements"]);
			}
		}
		reset($cm->modules["restricted"]["menu"]);
	}
}

if (!$cm->modules["restricted"]["menu"])
{
	$cm->addEvent("on_load_module", "mod_restricted_cm_on_load_module");
	if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING) {
        $cache = ffCache::getInstance();
        // calculate hash
        $cache_key = (MOD_RES_MEM_CACHING_BYPATH
                    ? $cm->path_info
                    : "default"
                );

        $cache->set($cache_key, $cm->modules["restricted"]["options"], "/cm/mod/restricted/options");
        $cache->set($cache_key, $cm->modules["restricted"]["menu"], "/cm/mod/restricted/menu");
        $cache->set($cache_key, $cm->modules["restricted"]["layout_bypath"], "/cm/mod/restricted/layout_bypath");
        $cache->set($cache_key, $cm->modules["restricted"]["settings"], "/cm/mod/restricted/settings");
    }

	$tmp = cm_confCascadeFind(FF_DISK_PATH, "", "mod_restricted.xml");
	if (is_file($tmp))
		mod_restricted_load_config_file($tmp);

	mod_restricted_load_config_file(cm_confCascadeFind(CM_ROOT . "/conf", "/cm", "mod_restricted.xml"));

	mod_restricted_load_by_path();
}*/




/*function mod_restricted_load_by_path()
{
	$cm = cm::getInstance();

	$script_path_parts = explode("/", $cm->path_info);
	$script_path_tmp = FF_DISK_PATH . "/conf/contents";
	$script_path_count = 0;
	while ($script_path_count < count($script_path_parts) && $script_path_tmp .= $script_path_parts[$script_path_count] . "/")
	{
		if (is_file($script_path_tmp . "mod_restricted.xml"))
		{
			mod_restricted_load_config_file($script_path_tmp . "mod_restricted.xml");
		}
		$script_path_count++;
	}
}*/

/*function mod_restricted_cm_on_load_module($cm, $mod)
{
	$tmp = cm_confCascadeFind(CM_MODULES_ROOT . "/" . $mod . "/conf", "/modules/" . $mod, "mod_restricted.xml");
	if (is_file($tmp))
		mod_restricted_load_config_file($tmp);
}*/
/*
function mod_restricted_get_setting($name, $DomainID = null, $db = null)
{
	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_auth_get_domain"))
		{
			$DomainID = mod_auth_get_domain();
		}
	}	
	
	if ($db === null) {
        $db = ffDB_Sql::factory();
	}

	$sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_restricted_settings WHERE name = " . $db->toSql(new ffData($name));

		$sSQL .= " AND ID_domains = " . $db->toSql($DomainID);


	$db->query($sSQL);
	if ($db->nextRecord())
	{
		return $db->getField("value")->getValue();
	}
	else
	{
		return "";
	}
}

function mod_restricted_get_all_setting($DomainID = null, $db = null)
{
    if ($db === null) {
        $db = ffDB_Sql::factory();
    }

    $sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_restricted_settings WHERE 1 ";

	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_auth_get_domain"))
		{
			$DomainID = mod_auth_get_domain();
		}
	}	
	

        $sSQL .= " AND ID_domains = " . $db->toSql($DomainID);


    $db->query($sSQL);
    if ($db->nextRecord())
    {
        $res = array();
        do {
            $res[$db->getField("name", "Text", true)] = $db->getField("value", "Text", true);
        } while($db->nextRecord());
        
        return $res;
    }
    else
    {
        return null;
    }
}

function mod_restricted_set_setting($name, $value, $DomainID = null, $db = null)
{
    if ($db === null) {
        $db = ffDB_Sql::factory();
    }

    $sSQL = "UPDATE
					" . CM_TABLE_PREFIX . "mod_restricted_settings
				SET
					value = " . $db->toSql($value) . "
				WHERE
					name = " . $db->toSql($name) . "
		";

	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_auth_get_domain") && MOD_AUTH_MULTIDOMAIN)
		{
			$DomainID = mod_auth_get_domain();
		}
	}	
	
	if ($DomainID !== null)
	{
		$sSQL .= " AND ID_domains = " . $db->toSql($DomainID);
	}

	$db->execute($sSQL);
	if (!$db->affectedRows())
	{
		if ($DomainID !== null)
		{
			$fields = ", ID_domains";
			$values = ", " . $db->toSql($DomainID);
		}
		$sSQL = "INSERT INTO
					" . CM_TABLE_PREFIX . "mod_restricted_settings (
						value
						, name
						" . $fields . "
					) VALUES (
						" . $db->toSql($value) . "
						, " . $db->toSql($name) . "
						" . $values . "
					)
			";
		$db->execute($sSQL);
	}
}
*/
/*function mod_restricted_load_config_file($file)
{
	$xml = new SimpleXMLElement("file://" . $file, null, true);
	mod_restricted_load_config($xml, $file);
}*/
/*
function mod_restricted_load_config($xml, $file = null)
{
	$cm = cm::getInstance();
	
    static $sect_compare;
    if($file !== null && $sect_compare == "" && strpos($file, FF_DISK_PATH . "/conf/contents") === 0) {
        $sect_compare = ffCommon_dirname(substr($file, strlen(FF_DISK_PATH . "/conf/contents"))); 
    }

    //carica le env relative al modulo
    if (isset($xml->env)) {
        $cm->load_env_by_xml($xml->env);
    }

    if (isset($xml->sections) && count($xml->sections->children()))
    {
        foreach ($xml->sections->children() as $key => $value)
        {
            if ($key == "comment")
                continue;

            if(!$cm->modules["restricted"]["sections"][$key]) {
                $cm->modules["restricted"]["sections"][$key] = array();
            }
            foreach($value->attributes() AS $attr_key => $attr_value) {
                if(strpos($attr_key, "__") === 0)
                    continue;

                switch($attr_value) {
                    case "true":
                        $cm->modules["restricted"]["sections"][$key]["attributes"][$attr_key] = true;
                        break;
                    case "false":
                        $cm->modules["restricted"]["sections"][$key]["attributes"][$attr_key] = false;
                        break;
                    default:
                        $cm->modules["restricted"]["sections"][$key]["attributes"][$attr_key] = (string) $attr_value;
                }
            }
        }

        //ffErrorHandler::raise ("gotcha2!", E_USER_ERROR, $this, get_defined_vars ());
    }

	if (isset($xml->menu) && count($xml->menu->children()))
	{
		foreach ($xml->menu->children() as $key => $value)
		{
			if ($key == "comment")
				continue;

			if (!isset($cm->modules["restricted"]["menu"][$key]))
			{
                $attrs = array();
                foreach($value->attributes() AS $attr_key => $attr_value) {
                    if(strpos($attr_key, "__") === 0)
                        continue;

                    switch($attr_value) {
                        case "true":
                            $attrs[$attr_key] = true;
                            break;
                        case "false":
                            $attrs[$attr_key] = false;
                            break;
                        default:
                            $attrs[$attr_key] = (string) $attr_value;
                    }
                }

				if($attrs["path"] != "/" && strlen($sect_compare) && strpos($attrs["path"], $sect_compare) !== 0 && strpos($cm->path_info, $sect_compare) === 0) {
                    continue;
                }

				$cm->modules["restricted"]["menu"][$key] = array();

				if (!strlen($attrs["path"]))
                    $attrs["path"] = strtolower("/" . $key);

				if (!strlen($attrs["label"]))
                    $attrs["label"] = $key;
				
				$cm->modules["restricted"]["menu"][$key]["name"] = $key;
				$cm->modules["restricted"]["menu"][$key]["path"] = $attrs["path"];
				$cm->modules["restricted"]["menu"][$key]["label"] = $attrs["label"];
				$cm->modules["restricted"]["menu"][$key]["badge"] = $attrs["badge"];
				//$cm->modules["restricted"]["menu"][$key]["is_heading"] = $is_heading;
				$cm->modules["restricted"]["menu"][$key]["icon"] = $attrs["icon"];
				if($attrs["actions"])
					$cm->modules["restricted"]["menu"][$key]["actions"] = explode(",", $attrs["actions"]);
				
				$cm->modules["restricted"]["menu"][$key]["dialog"] = $attrs["dialog"];
				$cm->modules["restricted"]["menu"][$key]["readonly"] = $attrs["readonly"];
                $cm->modules["restricted"]["menu"][$key]["readonly_skip"] = $attrs["readonly_skip"];

				if ($attrs["description"])
					$cm->modules["restricted"]["menu"][$key]["description"] = $attrs["description"];
                $cm->modules["restricted"]["menu"][$key]["description_skip"] = $attrs["description_skip"];

				$cm->modules["restricted"]["menu"][$key]["class"] = $attrs["class"];
				$cm->modules["restricted"]["menu"][$key]["hide"] = $attrs["hide"];
				$cm->modules["restricted"]["menu"][$key]["profiling_skip"] = $attrs["profiling_skip"];
				if ($attrs["profiling_default"] !== null)
				    $cm->modules["restricted"]["menu"][$key]["profiling_default"] = $attrs["profiling_default"];
				if (strlen($attrs["profiling_acl"]))
				    $cm->modules["restricted"]["menu"][$key]["profiling_acl"] = $attrs["profiling_acl"];

				$cm->modules["restricted"]["menu"][$key]["params"] = $attrs["params"];
				$cm->modules["restricted"]["menu"][$key]["globals_exclude"] = $attrs["globals_exclude"];
				if (strlen($attrs["acl"]))
					$cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $attrs["acl"]);
				if (strlen($attrs["redir"]))
					$cm->modules["restricted"]["menu"][$key]["redir"] = $attrs["redir"];

                if (strlen($attrs["position"]))
                    $cm->modules["restricted"]["menu"][$key]["position"] = $attrs["position"];
                if (strlen($attrs["settings"]))
                    $cm->modules["restricted"]["menu"][$key]["settings"] = $attrs["settings"];

                $cm->modules["restricted"]["menu"][$key]["collapse"] = $attrs["collapse"];
				
				$cm->modules["restricted"]["menu_bypath"][$attrs["path"]][] =& $cm->modules["restricted"]["menu"][$key];

				if($attrs["location"]) {
                    $cm->modules["restricted"]["menu"][$key]["location"] = $attrs["location"];

                    $cm->modules["restricted"]["sections"][$attrs["location"]]["elements"][$key] = $cm->modules["restricted"]["menu"][$key];
                    unset($cm->modules["restricted"]["sections"][$attrs["location"]]["elements"][$key]["elements"]);
				}
				if($attrs["favorite"]) {
                    $cm->modules["restricted"]["sections"]["favorite"]["elements"][$key] = $attrs;
				}
			}

			if (count($value))
			{
				foreach ($value as $subkey => $subvalue)
				{
					if (!isset($cm->modules["restricted"]["menu"][$key]["elements"][$subkey]))
					{
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey] = array();

                        $attrs = array();
                        foreach($subvalue->attributes() AS $attr_key => $attr_value) {
                            if(strpos($attr_key, "__") === 0)
                                continue;

                            switch($attr_value) {
                                case "true":
                                    $attrs[$attr_key] = true;
                                    break;
                                case "false":
                                    $attrs[$attr_key] = false;
                                    break;
                                default:
                                    $attrs[$attr_key] = (string) $attr_value;
                            }
                        }

						if (strlen($attrs["acl"])) {
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["acl"] = explode(",", $attrs["acl"]);
                        }
						if ($attrs["description"]) {
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["description"] = $attrs["description"];
                        }
						if ($attrs["jsaction"]) {
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["jsaction"] = $attrs["jsaction"];
                        }
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = $attrs["hide"];
                        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = $attrs["profiling_skip"];

                        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $attrs["params"];

                        if (!strlen($attrs["path"])) {
                            $attrs["path"] = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
                        }
                        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $attrs["path"];

						if (!strlen($attrs["label"])) {
                            $attrs["label"] = $subkey;
                        }

						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $attrs["label"];
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["badge"] = $attrs["badge"];
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["icon"] = $attrs["icon"];
						if($attrs["actions"]) {
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["actions"] = explode(",", $attrs["actions"]);
                        }
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["dialog"] = $attrs["dialog"];
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["readonly"] = $attrs["readonly"];

						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["class"] = $attrs["class"];

						$cm->modules["restricted"]["menu_bypath"][$attrs["path"]][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];

						if($attrs["location"] && isset($cm->modules["restricted"]["sections"][$attrs["location"]])) {
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["location"] = $attrs["location"];

						    $cm->modules["restricted"]["sections"][$attrs["location"]]["elements"][$subkey] = $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
                        }
                        if($attrs["favorite"]) {
                            $cm->modules["restricted"]["sections"]["favorite"]["elements"][$key. "-" . $subkey] = $attrs;
                        }
					}
				}
			}
		}
	}

	if (isset($xml->layout) && count($xml->layout->children()))
	{
		foreach ($xml->layout->children() as $key => $value)
		{
			if ($key == "comment")
				continue;

			$attrs = $value->attributes();
			if ($key == "nolayout")
			{
				$path = (string)$attrs["path"];
				if (!strlen($path))
					ffErrorHandler::raise("mod_restricted: malformed xml (missing path parameter on layout/nolayout section)", E_USER_ERROR, null, get_defined_vars());

				$cm->modules["restricted"]["layout_bypath"][$path] = $key;
			}
			else
			{
				$path = (string)$attrs["path"];
				if (!strlen($path))
					$cm->modules["restricted"]["options"]["layout"][$key] = (string)$value;
				else
				{
					$name = (string)$attrs["name"];
					$cm->modules["restricted"]["layout_bypath"][$path][$key] = $name;
				}
			}
		}
	}

	if (isset($xml->settings) && count($xml->settings->children()))
	{
		foreach ($xml->settings->children() as $key => $value)
		{
			if ($key == "comment")
				continue;

			if (!isset($cm->modules["restricted"]["settings"][$key]))
			{
				$cm->modules["restricted"]["settings"][$key] = new ffSerializable($value);
			}
			else
			{
				foreach ($value->children() as $subkey => $subvalue)
				{
					if ($subkey == "comment")
						continue;

					if (isset($cm->modules["restricted"]["settings"][$key]->$subkey))
					{
						if (count($attrs = $subvalue->attributes()))
						{
							foreach ($attrs as $attr_key => $attr_value)
							{
								$cm->modules["restricted"]["settings"][$key]->$subkey->$attr_key = (string)$attr_value;
							}
						}
					}
					else
						$cm->modules["restricted"]["settings"][$key]->$subkey = new ffSerializable($subvalue);
				}
			}
		}
	}
}
*/



/*
function mod_restricted_add_menu_child($data) {
    $cm = cm::getInstance();
    
    if (ffIsset($data, "key"))					$key 		= $data["key"];
    if (ffIsset($data, "path"))					$path 		= $data["path"];
    if (ffIsset($data, "label"))				$label 		= $data["label"];
    if (ffIsset($data, "badge"))				$badge 		= $data["badge"];
    if (ffIsset($data, "icon"))					$icon 		= $data["icon"];
    if (ffIsset($data, "actions"))				$actions 	= $data["actions"];
    if (ffIsset($data, "params"))				$params 	= $data["params"];
    if (ffIsset($data, "visible"))				$visible 	= $data["visible"];
    if (ffIsset($data, "acl"))					$acl 		= $data["acl"];
    if (ffIsset($data, "location"))				$location 	= $data["location"];
    if (ffIsset($data, "position"))				$position 	= $data["position"];
    if (ffIsset($data, "settings"))				$settings 	= $data["settings"];
    if (ffIsset($data, "redir"))				$redir 		= $data["redir"];
    if (ffIsset($data, "class"))				$class 		= $data["class"];
    if (ffIsset($data, "readonly"))				$readonly 	        = $data["readonly"];
    if (ffIsset($data, "readonly_skip"))		$readonly_skip	    = $data["readonly_skip"];
    if (ffIsset($data, "description"))			$description 	    = $data["description"];
    if (ffIsset($data, "description_skip"))		$description_skip	= $data["description_skip"];
    if (ffIsset($data, "dialog"))				$dialog 	= $data["dialog"];
    if (ffIsset($data, "favorite"))				$favorite 	= $data["favorite"];
    if (ffIsset($data, "collapse"))				$collapse 	= $data["collapse"];
    if (ffIsset($data, "rel"))					$rel 		= $data["rel"];
    if (ffIsset($data, "profiling_skip"))		$profiling_skip 	= $data["profiling_skip"];

    if (!isset($cm->modules["restricted"]["menu"][$key]))
    {
        $cm->modules["restricted"]["menu"][$key] = array();

        //$attrs = $value->attributes();


        if (!strlen($path))
            $path = strtolower("/" . $key);

        if (!strlen($label))
            $label = $key;

        $cm->modules["restricted"]["menu"][$key]["path"] = $path;
        $cm->modules["restricted"]["menu"][$key]["label"] = $label;
        $cm->modules["restricted"]["menu"][$key]["badge"] = $badge;
        $cm->modules["restricted"]["menu"][$key]["icon"] = $icon;
        $cm->modules["restricted"]["menu"][$key]["actions"] = $actions;
        $cm->modules["restricted"]["menu"][$key]["dialog"] = $dialog;
        $cm->modules["restricted"]["menu"][$key]["readonly"] = $readonly;
        $cm->modules["restricted"]["menu"][$key]["readonly_skip"] = $readonly_skip;
        $cm->modules["restricted"]["menu"][$key]["description"] = $description;
        $cm->modules["restricted"]["menu"][$key]["description_skip"] = $description_skip;
        $cm->modules["restricted"]["menu"][$key]["class"] = $class;
        $cm->modules["restricted"]["menu"][$key]["params"] = $params;
        $cm->modules["restricted"]["menu"][$key]["visible"] = $visible;
        if (strlen($acl))
            $cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $acl);

        if ($location)
            $cm->modules["restricted"]["menu"][$key]["location"] = $location;
        if ($position)
            $cm->modules["restricted"]["menu"][$key]["position"] = $position;
        if ($settings)
            $cm->modules["restricted"]["menu"][$key]["settings"] = $settings;

        if (strlen($redir))
            $cm->modules["restricted"]["menu"][$key]["redir"] = $redir;

		if($collapse !== null)
			$cm->modules["restricted"]["menu"][$key]["collapse"] = $collapse;
        
        $cm->modules["restricted"]["menu"][$key]["profiling_skip"] = $profiling_skip;

        $cm->modules["restricted"]["menu"][$key]["rel"] = $rel;
        
        $cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key];
        
        if($favorite)
			$cm->modules["restricted"]["sections"]["favorite"]["elements"][$key] = $cm->modules["restricted"]["menu"][$key];

    }
    
}
*/
/*
function mod_restricted_add_menu_sub_element($data) {
    $cm = cm::getInstance();

    if (ffIsset($data, "key"))					$key 				= $data["key"];
    if (ffIsset($data, "subkey"))				$subkey 			= $data["subkey"];
    if (ffIsset($data, "path"))					$path 				= $data["path"];
    if (ffIsset($data, "label"))				$label 				= $data["label"];
    if (ffIsset($data, "badge"))				$badge 				= $data["badge"];
    if (ffIsset($data, "icon"))					$icon 				= $data["icon"];
    if (ffIsset($data, "actions"))				$actions 			= $data["actions"];
    if (ffIsset($data, "params"))				$params 			= $data["params"];
    if (ffIsset($data, "acl"))					$acl 				= $data["acl"];
    if (ffIsset($data, "location"))				$location 			= $data["location"];
    if (ffIsset($data, "position"))				$position 			= $data["position"];
    if (ffIsset($data, "settings"))				$settings 			= $data["settings"];
    if (ffIsset($data, "hide"))					$hide 				= $data["hide"];
    if (ffIsset($data, "description"))			$description		= $data["description"];
    if (ffIsset($data, "profiling_skip"))		$profiling_skip 	= $data["profiling_skip"];
    if (ffIsset($data, "readonly"))				$readonly 			= $data["readonly"];
    if (ffIsset($data, "dialog"))				$dialog 			= $data["dialog"];
    if (ffIsset($data, "class"))				$class 				= $data["class"];
    if (ffIsset($data, "favorite"))				$favorite 			= $data["favorite"];
    if (ffIsset($data, "rel"))					$rel 				= $data["rel"];

	if ($subkey && !isset($cm->modules["restricted"]["menu"][$key]["elements"][$subkey]))
	{
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey] = array();

		if (strlen($acl))
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["acl"] = explode(",", $acl);

		if ($description)
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["description"] = $description;

        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = $hide;

        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = $profiling_skip;

		if (strlen($location))
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["location"] = $location;
        if (strlen($position))
            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["position"] = $position;
        if (strlen($settings))
            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["settings"] = $settings;
		//if (!$is_heading)
		//{
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $params;

			if (!strlen($path))
				$path = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $path;
			$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
			
		//}

		if (!strlen($label))
			$label = $subkey;

		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $label;
        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["badge"] = $badge;
        $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["icon"] = $icon;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["actions"] = $actions;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["dialog"] = $dialog;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["readonly"] = $readonly;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["class"] = $class;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["rel"] = $rel;
		//$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["is_heading"] = $is_heading;

		if($location && isset($cm->modules["restricted"]["sections"][$location])) {
			$cm->modules["restricted"]["sections"][$location]["elements"][$subkey] = $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
		}		
		
		if($favorite)
            $cm->modules["restricted"]["sections"]["favorite"]["elements"][$key . "-" . $subkey] = $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];

	}
}
*/
/*function mod_restricted_process_navbar(&$tpl, $sel_topbar, $attr = array())
{
    $cm = cm::getInstance();
    $res = array(
        "count" => 0
            , "is_opened" => false
            , "count_position" => null
    );

    $framework_css = (is_array($attr["framework_css"])
        ? $attr["framework_css"]
        : mod_restricted_get_framework_css()
    );

    if($attr["prefix"]) {
        $tpl->set_var("Sect" . $attr["prefix"] . "Element", "");
    }
    //$tpl->set_var("navbar_class", preg_replace("/[^[:alnum:]]+/", "", $sel_topbar["label"]));
    //var_dump(count($sel_topbar["elements"]));
//print_r(count($sel_topbar["elements"]));
    if(is_array($sel_topbar) && array_key_exists("elements", $sel_topbar) && count($sel_topbar["elements"]))
    {
        // echo "CCCC  ";

        foreach ($sel_topbar["elements"] as $key => $value)
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
            // $tpl->set_var("Sect" . $attr["prefix"] . "Link", "");
            // $tpl->set_var("Sect" . $attr["prefix"] . "Heading", "");
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

            if ($value["description"] && !$attr["description_skip"]) {
                $description = '<p class="' . $framework_css["description"] . '">' . $value["description"] . '</p>';
            }

            $tpl->set_var("description", $description);

            if($value["actions"]) {
                if(is_array($value["actions"]) && count($value["actions"])) {
                    foreach($value["actions"] AS $action_data) {
                        $action_path = "";
                        $action_label = "";
                        $action_icon = $framework_css["icons"]["settings"];
                        $action_data_dialog = true;
                        if(is_array($action_data)) {
                            $action_path = $action_data["path"];
                            if($action_data["icon"])
                                $action_icon = Cms::getInstance("frameworkcss")->get($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

                            $action_label = $action_data["label"];
                            if($action_data["dialog"] !== false)
                                $action_data_dialog = false;
                        } elseif($cm->modules["restricted"]["menu_bypath"][$action_data]) {
                            $action_path = $action_data;
                            if($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
                                $action_icon = Cms::getInstance("frameworkcss")->get($cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");

                            $action_label = $cm->modules["restricted"]["menu_bypath"][$action_data][0]["label"];
                        }

                        if(strpos($action_label, "_") === 0)
                            $action_label = ffTemplate::_get_word_by_code(substr($action_label, 1));

                        $action_path = str_replace(array("[rel]", "[key]"), array($value["rel"], $key), $action_path);
                        if($action_data_dialog)
                            $action_path = 'javascript:ff.ffPage.dialog.doOpen(\'dialogResponsive\',\'' . $action_path . '\');';

                        $item_actions[] = '<a href="' . $action_path . '" class="' . $action_icon . '" title="' . $action_label . '"></a>';
                    }
                }
            }

            if ($value["jsaction"])
                $path = $value["jsaction"];
            elseif($value["redir"])
                $path = $cm->oPage->site_path . $value["redir"];
            else
                $path = $cm->oPage->site_path . $value["path"] ;

            if($value["readonly"]) {
                $item_properties["url"] = 'data-url="' . $path . '"';
            } else {
                if ($value["dialog"])
                    $item_properties["url"] = 'href="' . "javascript:ff.ffPage.dialog.doOpen('dialogResponsive','" . $path . "');" . '"';
                else
                    $item_properties["url"] = 'href="' . $path . '"';
            }
            if($value["rel"])
                $item_properties["rel"] = 'rel="' . $value["rel"] . '"';

            if(($attr["icons"] == "all" || $attr["icons"] == "submenu") && $value["icon"])
                $item_icon = Cms::getInstance("frameworkcss")->get($value["icon"], "icon-tag", "lg");

            $label = "";
            if($attr["label"] === false) {
                $item_properties["title"] = 'title="' . $value["label"] . '"';
                if(!$item_icon) {
                    $label = ucfirst(substr($value["label"], 0, 1));
                }
            } else {
                $label = $value["label"];
            }
            if($label) {
                if($value["actions"] || $value["description"]) {
                    $tpl->set_var("label", '<span>' . $label . '</span>');
                } else {
                    $tpl->set_var("label", $label);
                }
            }


            if($framework_css["actions"][$value["name"]]) {
                $item_class["default"] = Cms::getInstance("frameworkcss")->getClass($framework_css["actions"][$value["name"]]);
            } elseif($framework_css["actions"][$value["position"]]) {
                $item_class["default"] = Cms::getInstance("frameworkcss")->getClass($framework_css["actions"][$value["position"]]);
            }

            if($value["class"]) {
                $item_class["custom"] = $value["class"];
            }
            if ($value["selected"])
            {
                $item_class["current"] = $framework_css["current"];
                $res["is_opened"] = true;
            }

            if($item_class) {
                $item_properties["class"] = 'class="' . implode(" ", $item_class) . '"';
            }
            if($item_properties) {
                $item_properties = implode(" ", $item_properties);
            }
            if($value["badge"]) {
                $item_actions[] = '<span class="' . Cms::getInstance("frameworkcss")->get("default", "badge") . '">' . $value["badge"] . '</span>';
            }

            if($item_actions) {
                $item_actions = '<span class="nav-controls">' . implode(" ", $item_actions) . '</span>';
            }
            $tpl->set_var("actions", $item_actions);
            $tpl->set_var("item_properties", $item_properties);
            $tpl->set_var("item_icon", $item_icon);
            $tpl->set_var("item_tag", $item_tag);
//echo "Sect" . $attr["prefix"] . "Element";

            $parse_key = "Sect" . $attr["prefix"] . "Element";
            if($value["position"]) {
                $position = ucfirst($value["position"]);
                $parse_key .= $position;
                $res["count_position"][$position]++;
            }
            $tpl->parse($parse_key, true);

            $res["count"]++;
        }
    }



    // reset($sel_topbar);

    return $res;
}*/
/*
function mod_restricted_check_no_permission($params, $profile_check = null) {
//    $toskip = explode(",", MOD_SEC_PROFILING_SKIPSYSTEM);
//    $profile_check = MOD_SEC_PROFILING && (MOD_SEC_PROFILING_SKIPSYSTEM !== "*") && !in_array($key, $toskip) && !$value["profiling_skip"];

    if($profile_check === null) {
        $profile_check = !$params["profiling_skip"];
    }
    if($params["settings"] && defined($params["settings"])) {
        $user_setting = !constant($params["settings"]);
    }

    return (
        !mod_restricted_checkacl_bylevel($params["acl"])
        || ($profile_check && !mod_sec_checkprofile_bypath($params["path"]))
        || $user_setting
        //|| $params["hide"]
    );
}*/
/*
function mod_restricted_checkacl_bylevel($acl, $level = null, $usernid = null, $path_info = null)
{
	if ($acl === null)
		return true;
	
	if ($level === null)
	{
		if ($usernid === null)
		{
            $level = get_session("UserLevel");
        }
    else
		{
			if ($path_info === null)
				$path_info = cm::getInstance()->path_info;
			
			$options = mod_security_get_settings($path_info);

			$db = ffDB_Sql::factory();
			$level = $db->lookup("SELECT level FROM " . $options["table_name"] . " WHERE ID = " . $db->toSql($usernid));
			if (!$level)
				ffErrorHandler::raise("wrong mod_restricted_checkacl_bylevel use, cannot determine level", E_USER_ERROR, null, get_defined_vars());
		}
	}
	

	if (in_array(get_session("UserLevel"), $acl))
		return true;
}*/
/*
function mod_res_removelabel($topbar, $label)
{
	$mod_data =& cm::getInstance()->modules["restricted"];
	
	foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
	{
		if ($value["is_heading"] && $value["label"] == $label)
		{
			unset($mod_data["menu"][$topbar]["elements"][$key]);
			break;
		}
	}
	reset($mod_data["menu"][$topbar]["elements"]);
}*/
/*
function mod_res_remove_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null)
	{
		$path = $mod_data["menu"][$topbar]["elements"][$navbar]["path"];
		unset($mod_data["menu_bypath"][$path]);
		unset($mod_data["menu"][$topbar]["elements"][$navbar]);
	}
	else
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				$path = $mod_data["menu"][$topbar]["elements"][$key]["path"];
				if (strlen($path))
				{
					//echo $key . " " . $path . "<br />";
					unset($mod_data["menu_bypath"][$path]);
				}
			}
		}
		$path = $mod_data["menu"][$topbar]["path"];
		unset($mod_data["menu_bypath"][$path]);
		unset($mod_data["menu"][$topbar]);
	}
}
*/
/*
function mod_res_disable_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null && ffArrIsset($mod_data, "menu", $topbar, "elements", $navbar))
	{
		if (!isset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"] = $mod_data["menu"][$topbar]["elements"][$navbar]["acl"];
			$mod_data["menu"][$topbar]["elements"][$navbar]["acl"] = array(0);
		}
	}
	else if (ffArrIsset($mod_data, "menu", $topbar))
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				if (!isset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]))
				{
					$mod_data["menu"][$topbar]["elements"][$key]["old_acl"] = $mod_data["menu"][$topbar]["elements"][$key]["acl"];
					$mod_data["menu"][$topbar]["elements"][$key]["acl"] = array(0);
				}
			}
			reset($mod_data["menu"][$topbar]["elements"]);
		}
		if (!isset($mod_data["menu"][$topbar]["acl"]))
		{
			$mod_data["menu"][$topbar]["old_acl"] = $mod_data["menu"][$topbar]["acl"];
			$mod_data["menu"][$topbar]["acl"] = array(0);
		}
	}
}
*/
/*
function mod_res_enable_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null)
	{
		if (isset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["elements"][$navbar]["acl"] = $mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"];
			unset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]);
		}
	}
	else
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				if (isset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]))
				{
					$mod_data["menu"][$topbar]["elements"][$key]["acl"] = $mod_data["menu"][$topbar]["elements"][$key]["old_acl"];
					unset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]);
				}
			}
			reset($mod_data["menu"][$topbar]["elements"]);
		}
		if (isset($mod_data["menu"][$topbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["acl"] = $mod_data["menu"][$topbar]["old_acl"];
			unset($mod_data["menu"][$topbar]["old_acl"]);
		}
	}
}
*/
/*
function mod_res_access_denied($confirm_url = null)
{
	$cm = cm::getInstance();

	$res_path = (string)$cm->router->getRuleById("restricted")->reverse;
	
	if ($confirm_url === null)
	{
		if (isset($_REQUEST["ret_url"]))
			$confirm_url = $_REQUEST["ret_url"];
		else
			$confirm_url = FF_SITE_PATH . $res_path . "?" . $cm->oPage->get_globals();
	}
	
	access_denied($confirm_url, FF_SITE_PATH . $res_path . "/dialog");
}
*/

/***
 * Framework css
 */

function mod_restricted_get_framework_css() {
    $framework_css = array(
        "layer" => array(
            "component" => array(
                "class" => null
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
    , "variant" => array(
            "-inverse" => array(
                "sidemenu"
            , "rightmenu"
            , "header__top"
            , "header__bar"
            , "pagecontent"
            , "footer"
            )
        , "-fixed" => array(
                "sidemenu"
            , "rightcol"
            , "header__top"
            , "header__bar"
            , "footer"
            )
        , "-noicon" => array(
                "sidemenu"
            , "header__top"
            , "header__bar"
            , "pagecontent"
            , "footer"
            )
        , "-closed" => array(
                "sidemenu"
            , "rightcol"
            )
        , "-rightview" => array(
                "rightcol"
            )
        , "-notab" => array(
                "header__bar"
            )
        , "-floating" => array(
                "sidemenu"
            , "rightcol"
            , "button"
            )
        , "-sortable" => array(

            )
        , "-draggable" => array(

            )
        , "-dragging" => array(

            )
        , "-dragover" => array(

            )
        , "-active" => array(

            )
        , "-pad1" => array(

            )
        , "-pad2" => array(

            )
        , "-pad3" => array(

            )
        , "-pad4" => array(

            )
        , "-pad5" => array(

            )
        , "-pad6" => array(

            )
        , "-pad7" => array(

            )
        , "-pad8" => array(

            )
        , "-pad9" => array(

            )


        )
    , "menu" => array(
            "topbar" => Cms::getInstance("frameworkcss")->get("topbar", "bar")
        , "navbar" => Cms::getInstance("frameworkcss")->get("navbar", "bar")
            //todo: da inserire la sidenav
        )
    , "list" => array(
            "container" => Cms::getInstance("frameworkcss")->get("group", "list")
        , "horizontal" => Cms::getInstance("frameworkcss")->get("group-horizontal", "list")
        , "item" => Cms::getInstance("frameworkcss")->get("item", "list")
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
            )
        )
    , "description" => Cms::getInstance("frameworkcss")->get("text-muted", "util")
    , "image" => array(
            "util" => array("corner-circle", "corner-thumbnail")
        )
    , "collapse" => array(
            "action" => Cms::getInstance("frameworkcss")->get("link", "data", "collapse")
        , "pane" => Cms::getInstance("frameworkcss")->get("pane", "collapse")
        , "current" => Cms::getInstance("frameworkcss")->get("current", "collapse")
        , "menu" => Cms::getInstance("frameworkcss")->get("menu", "collapse")
        )
    , "current" => Cms::getInstance("frameworkcss")->get("current", "util")
    , "icons" => array(
            "caret-collapsed" => "menu-caret " . Cms::getInstance("frameworkcss")->get("chevron-right", "icon")
        , "caret" => "menu-caret " . Cms::getInstance("frameworkcss")->get("chevron-right", "icon", array("rotate-90"))
        , "settings" => Cms::getInstance("frameworkcss")->get("cog", "icon")
        )
    , "logo" => array(
            "class" => "brand-logo"
        )
    );

    return $framework_css;

}























