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

$cm = cm::getInstance();

$cm->addEvent("on_load_module", "mod_security_cm_on_load_module");
//if (isset($cm->modules["restricted"]["events"]))
    $cm->modules["restricted"]["events"]->addEvent("on_layout_process", "mod_security_cm_on_layout_process");

/***
 * Functinons
 */

function mod_security_cm_on_load_module($cm, $mod)
{
    mod_security_load_config(); //load default xml
    $config_file = FF_DISK_PATH . "/conf/modules/auth/config.xml";
    if (is_file($config_file)) {
        mod_security_load_config($config_file);
    }
}


function mod_security_load_config($file = null)
{
    $cm = cm::getInstance();
    if(!$file)
        $file = __DIR__ . "/conf/config.xml";

    $xml = new SimpleXMLElement("file://" . $file, null, true);

    //carica le env relative al modulo
    if (isset($xml->env)) {
        $cm->load_env_by_xml($xml->env);
    }

    //gestisce i percorsi speciali dove non bisogna avere sessione
    if (isset($xml->auth) && count($xml->auth->children()))
    {
        foreach ($xml->auth->children() as $key => $value)
        {
            $attrs = $value->attributes();
            $path = (string)$attrs["path"];
            if (!strlen($path))
                ffErrorHandler::raise("mod_security: malformed xml (missing path parameter on auth section)", E_USER_ERROR, null, get_defined_vars());

            $cm->modules["auth"]["auth_bypath"][$path] = $key;
        }
    }
    if (isset($xml->fields) && count($xml->fields->children()))
    {
        foreach ($xml->fields->children() as $key => $value)
        {
            $key = (string)$key;

            if (!isset($cm->modules["auth"]["fields"][$key]))
            {
                $cm->modules["auth"]["fields"][$key] = array();

                $attrs = $value->attributes();
                foreach ($attrs as $subkey => $subvalue)
                {
                    $subkey = (string)$subkey;
                    $subvalue = (string)$subvalue;

                    $cm->modules["auth"]["fields"][$key][$subkey] = $subvalue;
                }
            }
        }
    }
    if (isset($xml->settings) && count($xml->settings->children()))
    {
        foreach ($xml->settings->children() as $key => $value)
        {
            $attrs = $value->attributes();
            $path = (string)$attrs["path"];

            if (!strlen($path))
                ffErrorHandler::raise("mod_security: malformed xml (missing path parameter on session section)", E_USER_ERROR, null, get_defined_vars());

            $cm->modules["auth"]["settings_bypath"][$path] = $value;
        }
    }
    /*



        if (isset($xml->domains_fields) && count($xml->domains_fields->children()))
        {
            foreach ($xml->domains_fields->children() as $key => $value)
            {
                $key = (string)$key;

                if (!isset($cm->modules["auth"]["domains_fields"][$key]))
                {
                    $cm->modules["auth"]["domains_fields"][$key] = array();

                    $attrs = $value->attributes();
                    foreach ($attrs as $subkey => $subvalue)
                    {
                        $subkey = (string)$subkey;
                        $subvalue = (string)$subvalue;

                        $cm->modules["auth"]["domains_fields"][$key][$subkey] = $subvalue;
                    }
                }
            }
        }

        if (isset($xml->groups) && count($xml->groups->children()))
        {
            foreach ($xml->groups->children() as $key => $value)
            {
                $key = (string)$key;

                if (!isset($cm->modules["auth"]["groups"][$key]))
                {
                    $cm->modules["auth"]["groups"][$key] = array();

                    $attrs = $value->attributes();
                    foreach ($attrs as $subkey => $subvalue)
                    {
                        $subkey = (string)$subkey;
                        $subvalue = (string)$subvalue;

                        $cm->modules["auth"]["groups"][$key][$subkey] = $subvalue;
                    }
                }
            }
        }

        if (isset($xml->domains_groups) && count($xml->domains_groups->children()))
        {
            foreach ($xml->domains_groups->children() as $key => $value)
            {
                $key = (string)$key;

                if (!isset($cm->modules["auth"]["domains_groups"][$key]))
                {
                    $cm->modules["auth"]["domains_groups"][$key] = array();

                    $attrs = $value->attributes();
                    foreach ($attrs as $subkey => $subvalue)
                    {
                        $subkey = (string)$subkey;
                        $subvalue = (string)$subvalue;

                        $cm->modules["auth"]["domains_groups"][$key][$subkey] = $subvalue;
                    }
                }
            }
        }

        if (isset($xml->packages) && count($xml->packages->children()))
        {
            foreach ($xml->packages->children() as $key => $value)
            {
                $key = (string)$key;

                if (!isset($cm->modules["auth"]["packages"][$key]))
                {
                    $cm->modules["auth"]["packages"][$key] = array();

                    $attrs = $value->attributes();
                    foreach ($attrs as $subkey => $subvalue)
                    {
                        $subkey = (string)$subkey;
                        $subvalue = (string)$subvalue;

                        $cm->modules["auth"]["packages"][$key][$subkey] = $subvalue;
                    }
                }
            }
        }

        if (isset($xml->packages_groups) && count($xml->packages_groups->children()))
        {
            foreach ($xml->packages_groups->children() as $key => $value)
            {
                $key = (string)$key;

                if (!isset($cm->modules["auth"]["packages_groups"][$key]))
                {
                    $cm->modules["auth"]["packages_groups"][$key] = array();

                    $attrs = $value->attributes();
                    foreach ($attrs as $subkey => $subvalue)
                    {
                        $subkey = (string)$subkey;
                        $subvalue = (string)$subvalue;

                        $cm->modules["auth"]["packages_groups"][$key][$subkey] = $subvalue;
                    }
                }
            }
        }*/

    // -----------------------
    //  profiling todo: profiling tutta da vedere
    $cm->modules["auth"]["profiling"] = array();

    if (isset($xml->profiling) && count($xml->profiling->children()))
    {
        // PATHS
        if (isset($xml->profiling->paths) && isset($xml->profiling->paths->element))
        {
            $tmp = new ffSerializable($xml->profiling);

            if (!is_array($tmp->paths->element))
                $cm->modules["auth"]["profiling"]["paths"] = mod_sec_profiling_get_paths(array($tmp->paths->element));
            else
                $cm->modules["auth"]["profiling"]["paths"] = mod_sec_profiling_get_paths($tmp->paths->element);
        }

        // STATIC PROFILES
        if (isset($xml->profiling->profiles) && count($xml->profiling->profiles->children()))
        {
            $cm->modules["auth"]["profiling"]["profiles"] = array();
            foreach ($xml->profiling->profiles->children() as $key => $value)
            {
                $attrs = $value->attributes();
                $id = (string)$attrs["id"];
                $label = (string)$attrs["label"];
                if (isset($attrs["acl"]))
                    $acl = (string)$attrs["acl"];
                else
                    $acl = null;
                $cm->modules["auth"]["profiling"]["profiles"][] = array(
                    "id" => $id
                , "label" => $label
                , "acl" => $acl
                );
            }
        }
    }
}
//todo: profiling da valutare
function mod_sec_profiling_get_paths($childrens, &$elements = null, $indent = 0)
{
    if ($elements === null)
        $elements = array();

    foreach ($childrens as $key => $value)
    {
        $path = $value->__attributes["path"];
        $label = $value->__attributes["label"];
        if (isset($value->__attributes["acl"]))
            $acl = $value->__attributes["acl"];
        else
            $acl = null;

        $elements[] = array(
            "path" => $path
        , "label" => $label
        , "acl" => $acl
        , "indent" => $indent
        );

        if (isset($value->element))
        {
            if (!is_array($value->element))
                mod_sec_profiling_get_paths(array($value->element), $elements, $indent + 1);
            else
                mod_sec_profiling_get_paths($value->element, $elements, $indent + 1);
        }
    }

    if ($indent == 0)
        return $elements;
}


/**
 * Session
 */
function session_isset($param_name)
{
    return Auth::getInstance("session")->envIsset($param_name);
}

function get_session($param_name, $bucket = null)
{ //todo: da finire con il bucket ["user"] ecc
    $anagraph = Auth::getUser();
    switch($param_name) {
        case "UserNID":
            $res = $anagraph["user"]["ID"];
            break;
        case "UserID":
            $res = $anagraph["user"]["username"];
            break;
        case "UserLevel":
            $res = $anagraph["user"]["acl"];
            break;
        case "UserEmail":
            $res = $anagraph["user"]["email"];
            break;
        case "Domain":
            $res = $anagraph["domain"]["name"];
            break;
        case "DomainID":
            $res = $anagraph["domain"]["ID"];
            break;
        case "user_permission":
            $res = $anagraph["user"];
            break;
        case "UserLang":
            $res = $anagraph["lang"];
            break;
        case "UserCountry":
            $res = $anagraph["country"];
            break;
        default:
            $res = ($anagraph[$param_name]
                ? $anagraph[$param_name]
                : Auth::getInstance("session")->env($param_name)
            );
    }

    return $res;
}

function set_session($param_name, $param_value)
{
    return Auth::getInstance("session")->env($param_name, $param_value);
}

function unset_session($param_name)
{
    return Auth::getInstance("session")->envUnset($param_name);
}

/**
 * Core
 */
function mod_security_create_session($UserID = null, $UserNID = null, $Domain = "", $DomainID = "", $permanent_session = null)
{
    $cm = cm::getInstance();

    $ID_user = $UserNID;
    $old_session_id = session_id();
    if($permanent_session === null) {
        $permanent_session = $cm::env("MOD_SECURITY_SESSION_PERMANENT");
    }

    $cm->doEvent("mod_security_on_create_session", array($UserID, $UserNID, $Domain, $DomainID, $permanent_session));

    $fields = array();
    if(is_array($cm->modules["auth"]["fields"]) && count($cm->modules["auth"]["fields"])) {
        foreach($cm->modules["auth"]["fields"] AS $name => $params) {
            if($params["db"])
                $fields[] = $params["db"];
        }
    }

    $anagraph = Auth::getInstance("session")->create($ID_user, array(
        "permanent" => $permanent_session
        , "fields" => $fields
    ));

    if(is_array($anagraph)) { //todo:tutto da sistemare
        set_session("__FF_SESSION__", true);

        /**
         * Geolocalization user
         */
        if($cm::env("MOD_SEC_ENABLE_GEOLOCALIZATION"))
            $locale = mod_security_get_locale();

        $anagraph["lang"] = $locale["lang"];
        $anagraph["country"] = $locale["country"];

        set_session("Domain"	        , $anagraph["domain"]["name"]);
        set_session("DomainID"	    , $anagraph["domain"]["ID"]);
        set_session("UserNID"	        , $anagraph["user"]["ID"]);
        set_session("UserID"	        , $anagraph["user"]["username"]);
        set_session("UserLevel"	    , $anagraph["user"]["acl"]);
        set_session("UserEmail"	    , $anagraph["user"]["email"]);
        set_session("UserLang"       , $anagraph["lang"]);
        set_session("UserCountry"    , $anagraph["country"]);
    }
    $cm->doEvent("mod_security_on_created_session", array($anagraph, $old_session_id, $permanent_session));

    return $anagraph;
}
function mod_security_check_session($prompt_login = true)
{
    $cm = cm::getInstance();

    if (isset($cm->modules["auth"]["auth_bypath"]) && count($cm->modules["auth"]["auth_bypath"]))
    {
        foreach ($cm->modules["auth"]["auth_bypath"] as $key => $value)
        {
            if  (strpos($cm->path_info, $key) === 0)
            {
                if ($value == "noauth")
                {
                    reset ($cm->modules["auth"]["auth_bypath"]);
                    return;
                }
            }
        }
        reset ($cm->modules["auth"]["auth_bypath"]);
    }
    $session = Auth::getInstance("session")->check(array("minlevel" => $cm->processed_rule["rule"]->options->minlevel));
    if ($session["status"] === "0")
    {
        //define ("MOD_SECURITY_SESSION_STARTED", true);
        $cm = cm::getInstance();
        $cm->doEvent("mod_security_on_check_session", array($prompt_login));
        return true;
    }
    else
    {
        if ($prompt_login)
        {
            prompt_login();
        }
        else
            return false;
    }
}
function mod_security_destroy_session($promptlogin = false, $ret_url = null)
{
    $cm = cm::getInstance();

    Auth::getInstance("session")->destroy();

    $cm->doEvent("mod_security_on_destroy_session", array());

    if ($promptlogin)
        prompt_login($ret_url);
}

/**
 * Utility: prompt_login
 */

// redirect to prompt login with proper vars selected
function prompt_login($ret_url = null)
{
    $cm = cm::getInstance();

    if ($ret_url === null)
        $ret_url = $_SERVER["REQUEST_URI"];

    if ($cm->isXHR())
    {
        $cm->jsonAddResponse(array(
            "modules" => array(
                "security" => array(
                    "prompt_login" => true
                )
            )
        ));
        $url = mod_security_get_login_path();
    }
    else
    {
        $url = mod_security_get_login_path() . ($ret_url ? "/?ret_url=" . rawurlencode($ret_url) : "");
    }

    ffRedirect ($url, "302");
}

function mod_security_get_login_path()
{
    $cm = cm::getInstance();

    $login_path = (string) $cm->router->getRuleById("mod_sec_login")->reverse;
    if (!$login_path)
        $login_path = FF_SITE_PATH . "/login";

    return $login_path;
}

function mod_security_get_settings($path_info = null)
{
    $cm = cm::getInstance();

    if (!$path_info)
        $path_info = $cm->path_info;

    $options["table_name"] = CM_TABLE_PREFIX . "mod_security_users";
    $options["table_dett_name"] = CM_TABLE_PREFIX . "mod_security_users_fields";
    $options["table_groups_name"] = CM_TABLE_PREFIX . "mod_security_groups";
    $options["table_groups_rel_user"] = CM_TABLE_PREFIX . "mod_security_users_rel_groups";
    $options["table_groups_dett_name"] = CM_TABLE_PREFIX . "mod_security_groups_fields";
    $options["table_token"] = CM_TABLE_PREFIX . "mod_security_token";
    $options["table_domains_fields"] = CM_TABLE_PREFIX . "mod_security_domains_fields";

    $options["session_name"] = session_name();
    $options["session_path"] = session_save_path();

    if (!isset($cm->modules["auth"]["settings_bypath"]) || !count($cm->modules["auth"]["settings_bypath"]))
        return $options;

    ksort($cm->modules["auth"]["settings_bypath"], SORT_STRING);
    foreach ($cm->modules["auth"]["settings_bypath"] as $key => $value)
    {
        $match = false;
        $attrs = $value->attributes();

        $path = rtrim($key, "/");
        $propagate = (string)$attrs["propagate"];
        if ($propagate == "false")
            $propagate = false;
        else
            $propagate = true;

        if ($path == $path_info)
            $match = true;
        elseif ($propagate && strpos($path_info, $path . "/") === 0)
            $match = true;

        if (!$match)
            continue;

        if (isset($value->table) && strlen((string)$value->table))
            $options["table_name"] = (string)$value->table;

        if (isset($value->table_dett) && strlen((string)$value->table_dett))
            $options["table_dett_name"] = (string)$value->table_dett;

        if (isset($value->table_groups) && strlen((string)$value->table_groups))
            $options["table_groups_name"] = (string)$value->table_groups;

        if (isset($value->table_groups_rel_user) && strlen((string)$value->table_groups_rel_user))
            $options["table_groups_rel_user"] = (string)$value->table_groups_rel_user;

        if (isset($value->table_groups_dett_name) && strlen((string)$value->table_groups_dett_name))
            $options["table_groups_dett_name"] = (string)$value->table_groups_dett_name;

        if (isset($value->table_token) && strlen((string)$value->table_token))
            $options["table_token"] = (string)$value->table_token;

        if (isset($value->table_domains_fields) && strlen((string)$value->table_domains_fields))
            $options["table_domains_fields"] = (string)$value->table_domains_fields;

        if (isset($value->login_path) && strlen((string)$value->login_path))
            $options["login_path"] = (string)$value->login_path;

        if (isset($value->session_name) && strlen((string)$value->session_name))
            $options["session_name"] = (string)$value->session_name;

        if (isset($value->session_path) && strlen((string)$value->session_path))
            $options["session_path"] = (string)$value->session_path;
    }
    reset($cm->modules["auth"]["settings_bypath"]);

    return $options;
}


/***
 * Area Restricted Blocchi di layout aggiuntivi
 */
function mod_security_cm_on_layout_process()
{
    $cm = cm::getInstance();
    //if (isset($cm->oPage->sections["accountpanel"]))
    //    $cm->oPage->sections["accountpanel"]["events"]->addEvent("on_process", "mod_security_cm_on_load_account");
    if (isset($cm->oPage->sections["account"]))
        $cm->oPage->sections["account"]["events"]->addEvent("on_process", "mod_security_cm_on_load_account");
    //if (isset($cm->oPage->sections["lang"]))
    //    $cm->oPage->sections["lang"]["events"]->addEvent("on_process", "mod_security_cm_on_load_lang");
    //if (isset($cm->oPage->sections["brand"]))
    //    $cm->oPage->sections["brand"]["events"]->addEvent("on_process", "mod_security_cm_on_load_brand");
}

function mod_security_cm_on_load_account($page, $tpl)
{
    if (mod_security_check_session(false))
    {
        if (!MOD_SEC_MULTIDOMAIN_EXTERNAL_DB || mod_security_is_admin())
            $db = mod_security_get_main_db();
        else
            $db = mod_security_get_db_by_domain(null);

        $username = "";

        $cm = cm::getInstance();

        if ($cm->modules["auth"]["fields"]["firstname"])
            $username .= mod_security_getUserInfo("firstname", null, $db)->getValue();
        if ($cm->modules["auth"]["fields"]["lastname"])
        {
            if (strlen($username))
                $username .= " ";
            $username .= mod_security_getUserInfo("lastname", null, $db)->getValue();
        }

        if (!strlen($username))
        {
            if ($cm->modules["auth"]["fields"]["nickname"])
                $username = mod_security_getUserInfo("nickname", null, $db)->getValue();
            else if (!strlen($username) && $cm->modules["auth"]["fields"]["nominativo"])
                $username = mod_security_getUserInfo("nominativo", null, $db)->getValue();
            else if (!strlen($username) && $cm->modules["auth"]["fields"]["company_name"])
                $username = mod_security_getUserInfo("company_name", null, $db)->getValue();
            else if (!strlen($username))
                $username = get_session("UserID");
        }
        if(MOD_SEC_GROUPS) {
            $user_permission = get_session("user_permission");

            if(is_array($user_permission) && array_key_exists("avatar", $user_permission) && strlen($user_permission["avatar"])) {
                $tpl->set_var("avatar", Auth::getUserAvatar(null, $user_permission["avatar"]));
                $tpl->parse("SectUserAvatar", false);
            }
        }

        $tpl->set_var("nomeutente", $username);
    }
}

function mod_security_cm_on_load_brand($page, $tpl)
{
    if (!mod_security_check_session(false))
        return;

    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();
    $ID_domain = mod_security_get_domain();

    $tpl->set_var("logo_class", cm_getClassByDef($framework_css["logo"]));
    if($ID_domain)
        $host_name = get_session("Domain");
    else
        $host_name = CM_LOCAL_APP_NAME;

    $logo_url = mod_srcurity_get_logo(MOD_RESTRICTED_LOGO_PATH, true);

    if(get_session("UserLevel") >= MOD_SEC_BRAND_ACL) {
        if($logo_url) {
            $tpl->set_var("logo_url", $logo_url);
            $tpl->set_var("logo_name", $host_name);
            $tpl->parse("SectLogo", false);
        } else {
            if($ID_domain)
                $tpl->set_var("host_name", get_session("Domain"));
            else
                $tpl->set_var("host_name", CM_LOCAL_APP_NAME);
        }

        $tpl->set_var("nav_left_class", "domain");//cm_getClassByDef($framework_css["fullbar"]["nav"]["left"]));
        $tpl->set_var("more_icon", '<i class="' . $framework_css["icons"]["settings"] . '"></i>');
        $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
        $tpl->set_var("panel_class", cm_getClassByDef($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_header_class", cm_getClassByDef($framework_css["dropdown"]["header"]));
        $tpl->set_var("panel_body_class", cm_getClassByDef($framework_css["dropdown"]["body"]["def"]));
        $tpl->set_var("panel_links_class", cm_getClassByDef($framework_css["dropdown"]["body"]["links"]));
        $tpl->set_var("panel_footer_class", cm_getClassByDef($framework_css["dropdown"]["footer"]));

        /*$mod_sec_domains = $cm->router->getRuleById("mod_sec_domains");
        if($mod_sec_domains->reverse) {
            $tpl->set_var("manage_domains", FF_SITE_PATH . $mod_sec_domains->reverse);
            $tpl->set_var("domains_class", cm_getClassByDef($framework_css["dropdown"]["actions"]["domains"]));
            $tpl->parse("SectDomains", false);
        }
        $mod_sec_profiling = $cm->router->getRuleById("mod_sec_profiling");
        if($mod_sec_profiling->reverse) {
            $tpl->set_var("manage_profiling", FF_SITE_PATH . $mod_sec_profiling->reverse);
            $tpl->set_var("profiling_class", cm_getClassByDef($framework_css["dropdown"]["actions"]["profiling"]));
            $tpl->parse("SectProfiling", false);
        }
        $mod_restricted_settings = $cm->router->getRuleById("mod_restricted_settings");
        if($mod_restricted_settings->reverse) {
            $tpl->set_var("manage_settings", FF_SITE_PATH . $mod_restricted_settings->reverse);
            $tpl->set_var("settings_class", cm_getClassByDef($framework_css["dropdown"]["actions"]["settings"]));
            $tpl->parse("SectSettings", false);
        }*/

        if($page->sections["admin"]) {
            if($page->tpl_layer[0]->isset_var("brand") && !$page->tpl_layer[0]->isset_var("admin")) {

                $tpl->set_var("admin", $page->sections["admin"]["tpl"]->rpparse("SectMenu", false));
            }
        }

        $tpl->parse("SectBrandName", false);

        if(MOD_SEC_MULTIDOMAIN && !defined("MOD_SEC_NOACCOUNTSCOMBO"))
        {
            if (mod_security_is_admin())
            {
                //if(!$ID_domain)
                //	$host_class = " hidden";

                //$tpl->set_var("host_class", cm_getClassByDef($framework_css["fullbar"]["nav"]["left"]) . $host_class);
                //$tpl->set_var("host_name", get_session("Domain"));
                //$tpl->set_var("host_icon", cm_getClassByFrameworkCss("external-link", "icon-tag"));

                $field = ffField::factory($page);
                $field->id = "accounts";
                $field->base_type = "Number";
                $field->widget = "actex";
                $field->actex_update_from_db = true;
                $field->multi_select_one_label = ffTemplate::_get_word_by_code("master_domain");
                $field->source_SQL = "SELECT ID, nome FROM " . CM_TABLE_PREFIX . "mod_security_domains ORDER BY nome";
                $mod_sec_setparams = $cm->router->getRuleById("mod_sec_setparams");
                if($mod_sec_setparams->reverse) {
                    $field->actex_on_change  = "function(obj, old_value, action) {
						if(action == 'change') {
							jQuery.get('" . $mod_sec_setparams->reverse . "?accounts=' + obj.value, function(data) {
								if(data['id'] > 0) {
									jQuery('#domain-title').text(data['name']);
									jQuery('#domain-title').attr('href', 'http://' + data['name']);
									jQuery('#domain-title').parent().removeClass('hidden');
								} else {
									jQuery('#domain-title').parent().addClass('hidden');
								}
								jQuery('body').addClass('loading');
								window.location.reload();
							});
						}
					}";
                } else {
                    $field->actex_on_change  = "function(obj, old_value, action) {
						if(action == 'change') {
							if(obj.value > 0) {
								window.location.href = ff.urlAddParam(window.location.href, 'accounts', obj.value);
							} else {
								window.location.href = ff.urlAddParam(window.location.href, 'accounts').replace('accounts&', '');
							}
						}					
					}";
                }
                $field->value = new ffData($ID_domain, "Number");
                $field->parent_page = array(&$page);
                $field->db = array(mod_security_get_main_db());
                $tpl->set_var("domain_switch", $field->process());

                $tpl->parse("SectMultiDomain", false);
            }

        }
        $tpl->parse("SectBrandInfo", false);
        $tpl->parse("SectBrandPanel", false);
    } elseif($logo_url) {
        $tpl->set_var("logo_url", $logo_url);
        $tpl->parse("SectBrandNoPanel", false);
    }
}

function mod_security_cm_on_load_lang($page, $tpl)
{
    if (!mod_security_check_session(false))
        return;

    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();

    $flag_dim = "16";
    if(MOD_SEC_GROUPS) {
        $user_permission = get_session("user_permission");
        $locale["lang"] = $user_permission["lang"];
    }
    if(!$locale["lang"])
        $locale = mod_security_get_locale();

    if(is_array($locale["lang"]) && count($locale["lang"])) {
        $filename = cm_cascadeFindTemplate("/css/lang-flags" . $flag_dim . ".css", "security");
        //$filename = cm_moduleCascadeFindTemplateByPath("restricted", "/css/lang-flags" . $flag_dim . ".css", $cm->oPage->theme);
        $ret = cm_moduleGetCascadeAttrs($filename);
        $cm->oPage->tplAddCSS("lang-flags" . $flag_dim . ".css", array(
            "file" => $filename
        , "path" => $ret["path"]
        ));

        $tpl->set_var("flag_dim", "f" . $flag_dim);
        $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
        $tpl->set_var("panel_class", cm_getClassByDef($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_body_class", cm_getClassByDef($framework_css["dropdown"]["body"]["def"]));
        foreach($locale["lang"] AS $code => $params) {
            if($code == "current")
                continue;

            $tpl->set_var("code", $code);
            $tpl->set_var("description", $params["description"]);
            $tpl->set_var("flag_lang", "flag " . $params["tiny_code"]);

            if($code == $locale["lang"]["current"]["code"]) {
                $tpl->set_var("current_class", $framework_css["current"]);
                $tpl->set_var("flag_lang_active", "flag " . $params["tiny_code"]);
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



/***
 * Da controllare ma usati nel codice
 */
function mod_security_getUserInfo($field, $ID_user = null, $db = null, $destroy_session = true)
{
    if ($ID_user === null)
        $ID_user = get_session("UserNID");
    return getUserInfo($ID_user, $field, $db, $destroy_session);
}

function getUserInfo($ID_user, $field, $db = null, $destroy_session = true) //security
{
    $cm = cm::getInstance();
    if ($db === null)
        $db = ffDb_Sql::factory();

    $options = mod_security_get_settings($cm->path_info);
    $sSQL = "SELECT 1 ";
    if (mod_security_is_default_field($field))
    {
        $sSQL .= ", " . $options["table_name"] . ".`" . $field . "`";
    }
    else if (isset($cm->modules["auth"]["fields"]) && count($cm->modules["auth"]["fields"]))
    {
        foreach ($cm->modules["auth"]["fields"] as $key => $value)
        {
            $sSQL .= ", (SELECT 
												" . $options["table_dett_name"] . ".value
											FROM
												" . $options["table_dett_name"] . "
											WHERE
												" . $options["table_dett_name"] . ".ID_users = " . $options["table_name"] . ".ID
												AND " . $options["table_dett_name"] . ".field = " . $db->toSql($key) . "
									) AS `" . $key . "`
				";
        }
        reset($cm->modules["auth"]["fields"]);
    }
    $sSQL .= "FROM
								" . $options["table_name"] . "
							WHERE
								" . $options["table_name"] . ".ID = " . $db->toSql($ID_user) . "
		";

    $db->query($sSQL);
    if ($db->nextRecord())
    {
        return $db->getField($field);
    }
    else if ($destroy_session)
    {
        mod_security_destroy_session(false);
        unset($_GET[session_name()], $_POST[session_name()], $_COOKIE[session_name()], $_REQUEST[session_name()]);
        //ffErrorHandler::raise("mod_security: User Not Found!!!", E_USER_ERROR, null, get_defined_vars());
    }
    return new ffData("");
}

function mod_security_setUserInfo($field, $value, $ID_user = null, $db = null) //unused
{
    if ($ID_user === null)
        $ID_user = get_session("UserNID");
    return setUserInfo($ID_user, $field, $value, $db);
}

function setUserInfo($ID_user, $field, $value, $db = null) //unused
{
    $cm = cm::getInstance();
    if ($db === null)
        $db = ffDb_Sql::factory();

    if (!isset($cm->modules["auth"]["fields"]) || !count($cm->modules["auth"]["fields"]) || !isset($cm->modules["auth"]["fields"][$field]))
        ffErrorHandler::raise("mod_security: Field don't exists", E_USER_ERROR, null, get_defined_vars());

    $options = mod_security_get_settings($cm->path_info);

    if (mod_security_is_default_field($field))
    {
        $sSQL = "UPDATE 
						" . $options["table_name"] . "
					SET
						" . $options["table_name"] . ".`" . $field . "` = " . $db->toSql($value) . "
					WHERE
						" . $options["table_name"] . ".ID = " . $db->toSql($ID_user) . "
			";
        $db->execute($sSQL);
        if (!$db->affectedRows())
        {
            mod_security_destroy_session(true, $_SERVER["REQUEST_URI"]);
        }
    }
    else
    {
        $sSQL = "SELECT ID
                        FROM " . $options["table_dett_name"] . " 
                        WHERE " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user) . "
                            AND " . $options["table_dett_name"] . ".field = " . $db->toSql($field);
        $db->query($sSQL);
        if($db->nextRecord()) {
            $sSQL = "UPDATE " . $options["table_dett_name"] . " SET
                        " . $options["table_dett_name"] . ".value = " . $db->toSql($value) . "
                    WHERE " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user) . "
                        AND " . $options["table_dett_name"] . ".field = " . $db->toSql($field);
            $db->execute($sSQL);
        } else {
            $sSQL = "INSERT INTO " . $options["table_dett_name"] . " (ID_users, field, value) VALUES (" . $db->toSql($ID_user) . ", " . $db->toSql($field) . ", " . $db->toSql($value) . ")";
            $db->execute($sSQL);
        }
    }
}

function access_denied($confirmurl = "", $dlg_site_path = "")
{
    //ffErrorHandler::raise("access_denied", E_USER_ERROR, null, get_defined_vars());
    $cm = cm::getInstance();

    if (!strlen($confirmurl))
        $confirmurl = $_REQUEST["ret_url"];
    if (!strlen($confirmurl))
        $confirmurl = $_SERVER["HTTP_REFERER"];
    if (!strlen($confirmurl))
        $confirmurl = FF_SITE_PATH . "/" . ($cm->oPage ? $cm->oPage->get_globals() : "");

    if (!strlen($dlg_site_path))
        $dlg_site_path = FF_SITE_PATH . "/dialog";

    ffDialog(false, "okonly", "_dialog_title_accessdenied", "_dialog_accessdenied", null, $confirmurl, $dlg_site_path);
}

function mod_security_get_locale($lang_default = null, $nocurrent = false) { //cache, security
    $db = ffDB_Sql::factory();

    $locale = array();
    $locale["lang"] = array();

    $sSQL = "SELECT " . FF_PREFIX . "languages.* 
			FROM " . FF_PREFIX . "languages 
			WHERE " . FF_PREFIX . "languages.status > 0
			ORDER BY " . FF_PREFIX . "languages.description";
    $db->query($sSQL);
    if($db->nextRecord())
    {
        $arrLangKey = array();
        if($lang_default === null)
            $lang_default = $db->getField("code", "Text", true);

        do
        {
            $ID_lang = $db->getField("ID", "Number", true);
            $lang_code = $db->getField("code", "Text", true);

            $locale["lang"][$lang_code]["ID"] 										= $ID_lang;
            $locale["lang"][$lang_code]["tiny_code"] 								= $db->getField("tiny_code", "Text", true);
            $locale["lang"][$lang_code]["description"] 								= $db->getField("description", "Text", true);
            $locale["lang"][$lang_code]["stopwords"] 								= $db->getField("stopwords", "Text", true);
            $locale["lang"][$lang_code]["prefix"] 									= ($lang_code == $lang_default
                ? ""
                : "/" . $locale["lang"][$lang_code]["tiny_code"]
            );

            $locale["rev"]["lang"][$locale["lang"][$lang_code]["tiny_code"]] 		= $lang_code;

            if(!$nocurrent && $locale["ID_languages"] == $ID_lang)
            {
                $locale["lang"]["current"] 											= $locale["lang"][$lang_code];
                $locale["lang"]["current"]["code"] 									= $lang_code;
            }
            $arrLangKey[$ID_lang] 													= $lang_code;
        } while($db->nextRecord());

        if(count($arrLangKey)) {
            $locale["rev"]["key"] 													= $arrLangKey;

            $sSQL = "SELECT " . FF_SUPPORT_PREFIX . "state.*
						, " . FF_PREFIX . "ip2nationCountries.country 		AS country
						, " . FF_PREFIX . "ip2nationCountries.iso_country 	AS country_iso
						, " . FF_PREFIX . "ip2nationCountries.code 			AS country_code
					FROM " . FF_SUPPORT_PREFIX . "state
						INNER JOIN " . FF_PREFIX . "ip2nationCountries ON " . FF_PREFIX . "ip2nationCountries.iso_country = " . FF_SUPPORT_PREFIX . "state.name 
					WHERE " . FF_SUPPORT_PREFIX . "state.ID_lang IN(" . $db->toSql(implode(",", array_keys($arrLangKey)), "Number") . ")";
            $db->query($sSQL);
            if($db->nextRecord()) {
                do {
                    $country_code = $db->getField("country_code", "Text", true);

                    $locale["country"][$country_code]["ID"]													= $db->getField("ID", "Number", true);
                    $locale["country"][$country_code]["name"]												= $db->getField("country", "Text", true);
                    $locale["country"][$country_code]["iso"]												= $db->getField("country_iso", "Text", true);
                    $locale["country"][$country_code]["ID_lang"]											= $db->getField("ID_lang", "Number", true);

                    $locale["rev"]["country"][$country_code] 												= $arrLangKey[$locale["country"][$country_code]["ID_lang"]];
                    $locale["lang"][$arrLangKey[$locale["country"][$country_code]["ID_lang"]]]["country"] 	= $country_code;

                } while($db->nextRecord());
            }
        }
    }

    if(!$nocurrent) {
        $sSQL = "SELECT " . FF_PREFIX . "ip2nation.country AS country_code
				FROM " . FF_PREFIX . "ip2nation
				WHERE " . FF_PREFIX . "ip2nation.ip < INET_ATON(" . $db->toSql($_SERVER["REMOTE_ADDR"]) . ")
				ORDER BY " . FF_PREFIX . "ip2nation.ip DESC
				LIMIT 0, 1";
        $db->query($sSQL);
        if($db->nextRecord())
        {
            $country_code = $db->getField("country_code", "Text", true);

            $locale["country"]["current"]												= $locale["country"][$country_code];
            $locale["country"]["current"]["code"]										= $country_code;

            if(isset($arrLangKey[$locale["country"]["current"]["ID_lang"]])) {
                $locale["lang"]["current"] 												= $locale["lang"][$arrLangKey[$locale["country"]["current"]["ID_lang"]]];
                $locale["lang"]["current"]["code"] 										= $arrLangKey[$locale["country"]["current"]["ID_lang"]];
            }
        }

        if(!array_key_exists("current", $locale["lang"]) && strlen($lang_default))
        {
            $locale["lang"]["current"] 													= $locale["lang"][$lang_default];
            $locale["lang"]["current"]["code"] 											= $lang_default;
        }
    }
    return $locale;
}

function mod_security_get_domain()
{
    if (mod_security_check_session(false))
    {
        $res = cm::getInstance()->modules["auth"]["events"]->doEvent("get_domain");
        $rc = end($res);
        if ($rc !== null)
            return $rc;

        if ($rc = get_session("DomainID"))
            return $rc;
        else
            return intval($_REQUEST["accounts"]);
    }
    else
        return null;
}
function mod_sec_createRandomPassword($length = 8, $strength = 7)
{
    srand((double)microtime()*1000000);

    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';

    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }

    return $password;
}

/**
 *
 * @param type $path
 * @param type $others
 * @param type $modify can be false (view), true (modify), "insert" and "delete" (last two with MOD_SEC_PROFILING_ADDITIONAL_PRIVS)
 * @param type $strict
 * @param type $profile
 * @param type $usernid
 * @param type $path_info
 * @return boolean
 */
function mod_sec_checkprofile_bypath($path, $others = false, $modify = false, $strict = true, $profile = null, $usernid = null, $path_info = null) //restricted
{
    if (!MOD_SEC_PROFILING)
        return true;

    if (defined("MOD_SECURITY_SESSION_STARTED") && get_session("UserLevel") == 3)
        return true;

    $permissions = mod_sec_getprofile_bypath($path, $profile, $usernid, $path_info);

    if (!$permissions)
        return true;

    if (MOD_SEC_PROFILING_MULTI)
    {
        $rc = false;
        foreach ($permissions as $value)
        {
            $rc |= mod_sec_checkperssion($value, $others, $modify, $strict);
        }
        return $rc;
    }
    else
    {
        return mod_sec_checkperssion($permissions, $others, $modify, $strict);
    }

}

function mod_sec_checkperssion($permissions, $others = false, $modify = false, $strict = true) //security
{
    if (MOD_SEC_PROFILING_EXTENDED)
    {
        if (!$others)
        {
            if ($modify === true)
            {
                if ($strict)
                    return $permissions["modify_own"];
                else
                    return $permissions["modify_own"] | $permissions["modify_others"];
            }
            else if ($modify === false)
            {
                if ($strict)
                    return $permissions["view_own"];
                else
                    return $permissions["modify_others"] | $permissions["view_others"] | $permissions["modify_own"] | $permissions["view_own"];
            }
            else if ($modify === "insert")
            {
                if ($strict)
                    return $permissions["insert_own"];
                else
                    return $permissions["insert_own"] | $permissions["insert_others"];
            }
            else if ($modify === "delete")
            {
                if ($strict)
                    return $permissions["delete_own"];
                else
                    return $permissions["delete_own"] | $permissions["delete_others"];
            }
        }
        else
        {
            if ($modify === true)
                return $permissions["modify_others"];
            else if ($modify === false)
            {
                if ($strict)
                    return $permissions["view_others"];
                else
                    return $permissions["modify_others"] | $permissions["view_others"];
            }
            else if ($modify === "insert")
            {
                if ($strict)
                    return $permissions["insert_others"];
                else
                    return $permissions["insert_others"] | $permissions["insert_others"];
            }
            else if ($modify === "delete")
            {
                if ($strict)
                    return $permissions["delete_others"];
                else
                    return $permissions["delete_others"] | $permissions["delete_others"];
            }
        }
    }
    else
    {
        if ($strict)
            return $permissions["view_own"];
        else
            return ($permissions["view_own"] | $permissions["modify_own"] | $permissions["view_others"] | $permissions["modify_others"]);
    }

    return false; // to catch errors
}

function mod_sec_getprofile_bypath($path, $profile = null, $usernid = null, $path_info = null) //security
{
    if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && MOD_SEC_PROFILING_MAINDB)
    {
        $db = mod_security_get_main_db();
        if (mod_security_get_domain())
            $db2 = mod_security_get_db_by_domain();
        else
            $db2 = mod_security_get_main_db();
    }
    else
    {
        $db = ffDB_Sql::factory();
        $db2 = ffDB_Sql::factory();
    }

    if ($usernid === null && defined("MOD_SECURITY_SESSION_STARTED"))
        $usernid = get_session("UserNID");

    if ($usernid === null && $profile === null)
        return null;
//		ffErrorHandler::raise("wrong mod_sec_checkprofile_bypath use, cannot determine profile", E_USER_ERROR, null, get_defined_vars());

    if ($profile === null)
        $profile = mod_sec_getprofile_byuser($usernid, $path_info);

    if (!$profile)
        return null;

    if (MOD_SEC_PROFILING_MULTI)
    {
        foreach ($profile as $value)
        {
            $permissions[] = $db2->lookup(
                "SELECT 
							* 
						FROM 
							cm_mod_security_profiles_pairs 
						WHERE 
							ID_profile = " . $db->toSql($value) . "
							AND path = " . $db->toSql($path) . "
						"
                , null
                , null
                , null
                , array(
                    "view_own"			=> "Text"
                , "view_others"		=> "Text"
                , "modify_own"		=> "Text"
                , "modify_others"	=> "Text"
                , "insert_own"		=> "Text"
                , "insert_others"	=> "Text"
                , "delete_own"		=> "Text"
                , "delete_others"	=> "Text"
                )
                , null
                , true
            );
        }
    }
    else
    {
        $permissions = $db2->lookup(
            "SELECT 
						* 
					FROM 
						cm_mod_security_profiles_pairs 
					WHERE 
						ID_profile = " . $db->toSql($profile) . "
						AND path = " . $db->toSql($path) . "
					"
            , null
            , null
            , null
            , array(
                "view_own"			=> "Text"
            , "view_others"		=> "Text"
            , "modify_own"		=> "Text"
            , "modify_others"	=> "Text"
            , "insert_own"		=> "Text"
            , "insert_others"	=> "Text"
            , "delete_own"		=> "Text"
            , "delete_others"	=> "Text"
            )
            , null
            , true
        );
    }
    return $permissions;
}

function mod_sec_getprofile_byuser($UserNID = null, $path_info = null) //security
{
    if ($UserNID === null)
        if (defined("MOD_SECURITY_SESSION_STARTED"))
            $UserNID = get_session("UserNID");
        else
            return null;

    if ($path_info === null)
        $path_info = cm::getInstance()->path_info;

    $options = mod_security_get_settings($path_info);

    $db = ffDB_Sql::factory();
    $profile = null;

    if (!MOD_SEC_PROFILING_MULTI)
        $profile = $db->lookup("SELECT profile FROM " . $options["table_name"] . " WHERE ID = " . $db->toSql($UserNID), null, null, null, null, null, true);
    else
    {
        $sSQL = "SELECT 
						`ID_profile`
					FROM 
						`cm_mod_security_rel_profiles_users`
					WHERE 
						`ID_user` = " . $db->toSql($UserNID) . "
						AND `enabled` = '1'
			";
        $db->query($sSQL);
        if ($db->nextRecord())
        {
            $profile = array();
            do
            {
                $profile[] = $db->getField("ID_profile")->getValue();
            } while ($db->nextRecord());
        }
    }

    if (!$profile)
        return null;
    else
        return $profile;

}



/***
 * OAUTH2
 */
function modsec_getOauth2Server()
{
    if (ffIsset($_REQUEST, "__OAUTH2DEBUG__"))
    {
        $parts = explode("/", $_SERVER["REQUEST_URI"]);
        @mkdir(CM_CACHE_DISK_PATH . "/oauth2", 0777, true);
        $fp = fopen(CM_CACHE_DISK_PATH . "/oauth2/" . end($parts) . "_" . uniqid(), "w+");
        fwrite($fp, print_r($_REQUEST, true));
        fclose($fp);
    }

    static $server = null;

    if ($server !== null)
        return $server;

    $storage = new OAuth2\Storage\FF();

    $server = new OAuth2\Server($storage);

    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
    $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));

    return $server;
}

function modsec_OAuth2Error($response)
{
    $cm = cm::getInstance();

    $template_file = "error.html";
    $filename = null;
    if ($filename === null)
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . rtrim($cm->path_info, "/") . "/" . $template_file, $cm->oPage->theme, false);
    if ($filename === null)
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/oauth2/" . $template_file, $cm->oPage->theme, false);
    if ($filename === null)
        $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/oauth2/" . $template_file, $cm->oPage->theme);

    $tpl = ffTemplate::factory(ffCommon_dirname($filename));
    $tpl->load_file(basename($filename), "main");

    $tpl->set_var("site_path", FF_SITE_PATH);
    $tpl->set_var("theme", $cm->oPage->theme);
    $tpl->set_var("http_domain", $_SERVER["HTTP_HOST"]);

    $cm->preloadApplets($tpl);
    $cm->parseApplets($tpl);

    $tpl->set_var("ret_url",			$_REQUEST["ret_url"]);
    $tpl->set_var("encoded_ret_url",	rawurlencode($_REQUEST["ret_url"]));
    $tpl->set_var("encoded_this_url",	rawurlencode($cm->oPage->getRequestUri()));
    $tpl->set_var("query_string",		$_SERVER["QUERY_STRING"]);
    $tpl->set_var("path_info",			$_SERVER["PATH_INFO"]);
    $tpl->set_var("app_title",			ffCommon_specialchars(CM_LOCAL_APP_NAME));

    $parameters = $response->getParameters();

    $tpl->set_var("error", ffCommon_specialchars($parameters["error"]));
    $tpl->set_var("error_description", ffCommon_specialchars($parameters["error_description"]));
    //$tpl->set_var("error_uri", $parameters["error_uri"]);

    if (isset($_REQUEST["ret_url"]) && strlen($_REQUEST["ret_url"]))
        $tpl->parse("SectRetUrl", false);
    else
        $tpl->parse("SectPopup", false);

    $cm->oPage->layer = "empty";
    $cm->oPage->form_method = "POST";
    $cm->oPage->use_own_form = true;
    $cm->oPage->addContent($tpl);
}

function modsec_OAuth2_UserResourceController($scopeRequired, $callback)
{
    $server = modsec_getOauth2Server();

    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();

    if (!$server->verifyResourceRequest($request, $response, $scopeRequired))
    {
        $response->send();
        exit;
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $UserNID = $token["user_id"];
    if (!intval($UserNID))
    {
        $response->setError(401, "wrong_token_type", "The token spupplied is not linked with any user");
        $response->send();
        exit;
    }

    $scopes = array_flip(explode(" ", $token["scope"]));

    $ret = call_user_func_array($callback, array($UserNID, $scopes, $request, $response, $server));
}

function modsec_OAuth2_ResourceController($scopeRequired, $callback)
{
    $server = modsec_getOauth2Server();

    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();

    if (!$server->verifyResourceRequest($request, $response, $scopeRequired))
    {
        $response->send();
        exit;
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $scopes = array_flip(explode(" ", $token["scope"]));
    $ret = call_user_func_array($callback, array($scopes, $request, $response, $server));
}


/***
 * Framework css
 */

function mod_sec_get_framework_css() {
	$framework_css = array(
        "component" => array(
            "class" => "loginBox security nopadding"
            , "type" => null        //null OR '' OR "-inline"
            , "grid" => "row-fluid"  //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
        )
        , "inner-wrap" => array(
            "col" => array(
                            "xs" => 12
                            , "sm" => 12
                            , "md" => 12
                            , "lg" => 12
                        )
        )
        , "logo" => array(
            "class" => "logo-login"
            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
                            "xs" => 0
                            , "sm" => 0
                            , "md" => 6
                            , "lg" => 7
                        )
        )
        , "login" => array(
        	"def" => array(
	            "class" => "login"
	            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
	                            "xs" => 12
	                            , "sm" => 12
	                            , "md" => 6
	                            , "lg" => 5
	                        )
	        )
	        , "standard" => array(
	        	"def" => array(
		            "class" => "standard-login"
		            , "col" => false
		        )
		        , "record" => array(
			        "class" => "login-field"
			        , "form" => null
		        )
		        , "field" => array(
	        		"form" => "control"
		        )
		        , "recover" => array(
		            "class" => "recover"
		            , "util" => "align-right"
		        )
	        )
	        , "social" => array(
	        	"def" => array(
		            "class" => "social-login"
		            , "col" => false
		        )
				, "google" => array(
	                "class" => "google"
	                , "button" => array(
	                    "value" => "primary"
	                    , "params" => array(
	                        "width" => "full"
	                    )
	                )
	            )
	            , "facebook" => array(
	                "class" => "facebook"
	                , "button" => array(
	                    "value" => "primary"
	                    , "params" => array(
	                        "width" => "full"
	                    )
	                )
	            )
	            , "janrain" => array(
	                "class" => "janrain"
	            )
	        )
        )
        , "logout" => array(
        	"def" => array(
	            "class" => "logout"
	            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
	                            "xs" => 12
	                            , "sm" => 12
	                            , "md" => 6
	                            , "lg" => 5
	                        )

	            , "util" => array(
	            	"align-center"
	            )
	        )
	        , "account" => array(
	        	"def" => array(
		            "class" => "account"
		            , "col" => false
		            , "util" => "align-center"
		        )
		        , "avatar" => array(
					"class" => "avatar"
		            , "util" => "corner-circle"
		        )
		        , "username" => array(
		        )
		        , "email" => array(
		        )
	        )
        )
 		, "actions" => array(
		    "def" => array(
			    "class" => "actions"
			    , "form" => null
		    )
			, "login" => array(
                "class" => null
                , "button" => array(
                    "value" => "primary"
                    , "params" => array(
                        "width" => "full"
                    )
                )
            )
        	, "logout" => array(
                "class" => null
                , "button" => array(
                    "value" => "primary"
                    , "params" => array(
                        "width" => "full"
                    )
                )
            )
			, "activation" => array(
	            "class" => null
	            , "button" => array(
	                "value" => "primary"
	                , "params" => array(
	                    "width" => "full"
	                )
	            )
	        )
	        , "recover" => array(
	            "class" => null
	            , "button" => array(
	                "value" => "primary"
	                , "params" => array(
	                    "width" => "full"
	                )
	            )
	        )
		)
        , "links" => array(
        	"def" => array(
            	"class" => "link-login"
            )
	        , "register" => array(
	            "class" => "register"
	            , "util" => "left"
	        )
	        , "back" => array(
	            "class" => "back"
	            , "util" => "right"
	        )
        )
        , "error" => array(
            "class" => "error"
            , "callout" => "danger"
        )
    );

	return $framework_css;
}