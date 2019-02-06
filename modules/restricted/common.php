<?php
class cmRestricted {
    private $config = array();
    private $cm = null;

    public function __construct()
    {
        $this->load();
    }

    private function load() {
        $this->cm = cm::getInstance();

        $this->setConfigPages($this->cm->config["pages"]);

        $this->setLayout();

    }

    private function setConfigMenu ($data, &$config, $father = null) {
        if(is_array($data) && count($data)) {
            if(is_array($father)) {
                $parent = $father["path"];
            } else {
                $father_key = $father;
            }
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
                        if ($this->config["menu"][$key]["path"] && !$parent) {
                            $this->config["menu_bypath"][$this->config["menu"][$key]["path"]][] = &$config[$key];
                        } elseif($config[$key]["path"] && $parent) {
                            $this->config["menu_bysubpath"][$config[$key]["path"]] = array("parent" => $parent, "key" => $key);
                        }
                        if (is_array($value) && count($value)) {
                            $this->config["menu"][$key]["elements"] = array();
                            $this->setConfigMenu($value, $this->config["menu"][$key]["elements"], $this->config["menu"][$key]);
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
        if (!isset($data["page"][0])) {
            $data["page"] = array($data["page"]);
        }

        foreach ($data["page"] AS $page) {
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

        return FF_SITE_PATH . $logo_url;
    }

    public function getDomainName() {
        $domain =  Auth::get("domain")->name;
        return ($_COOKIE["domain"]
            ? $_COOKIE["domain"]
            : ($domain
                ? $domain
                : CM_LOCAL_APP_NAME
            )
        );
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
            $skip_if_exist = true;
            $data = array(
                "path" => $data
                , "jsaction" => true
                , "source" => "context"
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

        if($skip_if_exist && (
            ($this->config["menu"][$key] && !$this->config["menu"][$key]["source"])
            || ($this->config["menu"][$key]["elements"][$subkey] && !$this->config["menu"][$key]["elements"][$subkey]["source"])
        )) {
            return false;
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
            $this->config["menu_bysubpath"][$data["path"]] = array("parent" => $this->config["menu"][$key]["path"], "key" => $subkey);

           // if($this->config["menu_bypath"][$key]["elements"][$subkey]["path"]) {
            //    $this->config["menu_bypath"][$this->config["menu_bypath"][$key]["elements"][$subkey]["path"]][] = &$this->config["menu"][$key]["elements"][$subkey];
            //}
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

        //if(cm::env("MOD_RESTRICTED_DYNAMIC_TABS") && !defined("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID"))
        //    define("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID", true);

        //todo: da sistemare il config con ["config"]
        $this->cm->modules["restricted"] = array_replace($this->cm->modules["restricted"], $this->config);

        $this->loadDialog();
        $this->processMenu();

        $this->cm->oPage->addEvent("on_tpl_layer_loaded", function($page, $tpl) {
            $cm = cm::getInstance();

            /* Override Part @carmine */
            $theme = $this->cm->oPage->getTheme();
            $class_name = null;
            $themeUIObject = null;
            if (file_exists(FF_THEME_DISK_PATH . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $theme . "UI.php")) {
                require_once (FF_THEME_DISK_PATH . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $theme . "UI.php");
                $class_name = $theme . "UI";
                $themeUIObject = new $class_name();
                //ddd($themeUIObject);
            }

            if (isset($cm->modules["restricted"]["sections"]))
            {
                foreach ($cm->modules["restricted"]["sections"] as $key => $value)
                {
                    if (!isset($cm->modules["restricted"]["options"]["layout"][$key]) || strlen($cm->modules["restricted"]["options"]["layout"][$key]))
                    {

                        $method = "on_load_section_" . $key;

                        if(is_callable($method)) {
                            $value["events"]["on_load_template"] = $method;
                        }

                        if ($class_name !== null) {
                            if (method_exists($themeUIObject, $method)) {
                                //$value["events"]["on_load_template"] = $themeUIObject::$method();


                                $value["override"] = $themeUIObject;
                                $value["override_method"] = $method;
                            }
                        }

                        $cm->oPage->addSection($key, $value);
                    }
                }
                reset($cm->modules["restricted"]["sections"]);
            }
            /*$cm = cm::getInstance();

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
            }*/

            $framework_css = mod_restricted_get_framework_css();

            $tpl->set_var("toggle_class", $this->cm->oPage->frameworkCSS->getClass($framework_css["layer"]["action"]["toggle"]));
            $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
            $tpl->set_var("nav_left_class", $this->cm->oPage->frameworkCSS->getClass($framework_css["layer"]["nav"]["left"]));
            $tpl->set_var("nav_right_class", $this->cm->oPage->frameworkCSS->getClass($framework_css["layer"]["nav"]["right"]));


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
        $layout_vars = array();
        $pathinfo = $this->cm->path_info;
        if($pathinfo) {
            $pathinfo_dirname = "";
            $arrPathinfo = explode("/", ltrim($pathinfo, "/"));
            foreach ($arrPathinfo AS $pathinfo_name) {
                $pathinfo_dirname .= "/" . $pathinfo_name;

                $layout_vars = array_replace($layout_vars, (array) $this->config["pages"][$pathinfo_dirname]);
            }
        }
        $this->cm->layout_vars = $layout_vars;

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

            if($this->cm->real_path_info) {
                $real_path_info = cm::env("MOD_RESTRICTED_PATH"). $this->cm->real_path_info;
                do {
                    if($this->cm->modules["restricted"]["menu_bysubpath"][$real_path_info]) {
                        $sel_topbar_path = $this->cm->modules["restricted"]["menu_bysubpath"][$real_path_info]["parent"];
                        $nav_key =  $this->cm->modules["restricted"]["menu_bysubpath"][$real_path_info]["key"];
                        break;
                    }

                    $real_path_info = dirname($real_path_info);
                } while ($real_path_info != DIRECTORY_SEPARATOR);
            }
/*
            $restricted_path = cm::env("MOD_RESTRICTED_PATH");
            if($this->cm->real_path_info && $this->cm->real_path_info != "/") {
                $path_parts = explode("/", ltrim($this->cm->real_path_info, "/"), 2);
                $restricted_path .= "/" . $path_parts[0];
            }

            $sel_topbar_path = ($this->cm->modules["restricted"]["menu_bysubpath"][$restricted_path]
                ? $this->cm->modules["restricted"]["menu_bysubpath"][$restricted_path]["parent"]
                : $restricted_path
            );*/


            if($this->cm->modules["restricted"]["menu_bypath"][$sel_topbar_path]) {
                $this->cm->modules["restricted"]["sel_topbar"] =& $this->cm->modules["restricted"]["menu_bypath"][$sel_topbar_path][0];
                $this->cm->modules["restricted"]["sel_topbar"]["selected"] = true;

                //$nav_key = str_replace("/", "_", $path_parts[1]);
               // $nav_key = $this->cm->modules["restricted"]["menu_bysubpath"][$restricted_path . ($path_parts[1] ? "/" . $path_parts[1] : "")]["key"];
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
                                    $action_icon = $this->cm->oPage->frameworkCSS->get($action_data["icon"], "icon") . ($action_data["class"] ? " " . $action_data["class"] : "");

                                $action_label = $action_data["label"];
                                if($action_data["dialog"] !== false)
                                    $action_data_dialog = false;
                            } elseif($this->cm->modules["restricted"]["menu_bypath"][$action_data]) {
                                $action_path = $action_data;
                                if($this->cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"])
                                    $action_icon = $this->cm->oPage->frameworkCSS->get($this->cm->modules["restricted"]["menu_bypath"][$action_data][0]["icon"], "icon");

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
                    $item_icon = $this->cm->oPage->frameworkCSS->get($value["icon"], "icon-tag", "lg");

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
                    $item_class["default"] = $this->cm->oPage->frameworkCSS->getClass($framework_css["actions"][$value["name"]]);
                } elseif($framework_css["actions"][$value["position"]]) {
                    $item_class["default"] = $this->cm->oPage->frameworkCSS->getClass($framework_css["actions"][$value["position"]]);
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
                    $item_actions[] = '<span class="' . $this->cm->oPage->frameworkCSS->get("default", "badge") . '">' . $value["badge"] . '</span>';
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

/***
 * Framework css
 */

function mod_restricted_get_framework_css() {
    $cm = cm::getInstance();
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
            "topbar" => $cm->oPage->frameworkCSS->get("topbar", "bar")
        , "navbar" => $cm->oPage->frameworkCSS->get("navbar", "bar")
            //todo: da inserire la sidenav
        )
    , "list" => array(
            "container" => $cm->oPage->frameworkCSS->get("group", "list")
        , "horizontal" => $cm->oPage->frameworkCSS->get("group-horizontal", "list")
        , "item" => $cm->oPage->frameworkCSS->get("item", "list")
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
    , "description" => $cm->oPage->frameworkCSS->get("text-muted", "util")
    , "image" => array(
            "util" => array("corner-circle", "corner-thumbnail")
        )
    , "collapse" => array(
            "action" => $cm->oPage->frameworkCSS->get("link", "data", "collapse")
        , "pane" => $cm->oPage->frameworkCSS->get("pane", "collapse")
        , "current" => $cm->oPage->frameworkCSS->get("current", "collapse")
        , "menu" => $cm->oPage->frameworkCSS->get("menu", "collapse")
        )
    , "current" => $cm->oPage->frameworkCSS->get("current", "util")
    , "icons" => array(
            "caret-collapsed" => "menu-caret " . $cm->oPage->frameworkCSS->get("chevron-right", "icon")
        , "caret" => "menu-caret " . $cm->oPage->frameworkCSS->get("chevron-right", "icon", array("rotate-90"))
        , "settings" => $cm->oPage->frameworkCSS->get("cog", "icon")
        )
    , "logo" => array(
            "class" => "brand-logo"
        )
    );

    return $framework_css;

}























