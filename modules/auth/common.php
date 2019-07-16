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

class cmAuth {
    private $config = array();
    private $domain = null;

    public function __construct()
    {
        $this->load();
    }

    private function load() {
        $cm = cm::getInstance();

        $this->setConfigAuth($cm->config["auth"]);
        $this->setConfigUserFields($cm->config["fields"]);

        Auth::addEvent("on_get_profiles", function() {
            $cm = cm::getInstance();

            if(is_array($cm->config["profiling"]) && count($cm->config["profiling"])) {
                foreach($cm->config["profiling"] AS $name => $profile) {
                    Auth::loadProfile($profile, $name);
                }
            }
        });

        Auth::addEvent("on_get_packages", function() {
            $cm = cm::getInstance();

            if(is_array($cm->config["packages"]) && count($cm->config["packages"])) {
                foreach($cm->config["packages"] AS $name => $package) {
                    Auth::loadPackage($package, $name);
                }
            }
        });
        Auth::addEvent("on_logged_in", function($user, $opt) {
            Auth::env("MOD_AUTH_NOACCOUNTSCOMBO", (Auth::isAdmin()
                ? false
                : true
            ));
        });

        //$cm->load_env(Auth::getPackage($this->getDomainName()));

        //todo: da sistemare il config con ["config"]
        $cm->modules["auth"] = array_replace($cm->modules["auth"], $this->config);
    }
    private function setConfigAuth($data) {
        if(is_array($data) && count($data)) {
            $this->setConfigNoAuth($data["noauth"]);
        }
    }

    private function setConfigUserFields ($data) {
        if(is_array($data) && count($data)) {
            foreach($data AS $key => $value) {
                $this->config["fields"][$key] = ($value["@attributes"]
                    ? $value["@attributes"]
                    : $value
                );
            }

        }
    }
    private function setConfigNoAuth($data) {
        if(is_array($data) && count($data)) {
            foreach ($data AS $value) {
                $path = ($value["@attributes"]["path"]
                    ? $value["@attributes"]["path"]
                    : $value["path"]
                );

                $this->config["auth_bypath"][$path] = "noauth";
            }
        }
    }
    public function getDomainName() {
        $domain =  Auth::get("domain")->name;
        return ($_COOKIE["domain"]
            ? $_COOKIE["domain"]
            : ($domain
                ? $domain
                : $_SERVER["HTTP_HOST"]
            )
        );
    }
}

$cm = cm::getInstance();

$cm->modules["auth"]["obj"] = new cmAuth();

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
    $anagraph = Auth::get();
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

    $login_path = (string) $cm->router->getRuleById("mod_auth_login")->reverse;
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
    //$cm = cm::getInstance();
    //if (isset($cm->oPage->sections["accountpanel"]))
    //    $cm->oPage->sections["accountpanel"]["events"]->addEvent("on_process", "mod_security_cm_on_load_account");
    //if (isset($cm->oPage->sections["account"]))
    //    $cm->oPage->sections["account"]["events"]->addEvent("on_process", "mod_security_cm_on_load_account");
    //if (isset($cm->oPage->sections["lang"]))
    //    $cm->oPage->sections["lang"]["events"]->addEvent("on_process", "mod_security_cm_on_load_lang");
    //if (isset($cm->oPage->sections["brand"]))
    //    $cm->oPage->sections["brand"]["events"]->addEvent("on_process", "mod_security_cm_on_load_brand");
}

function mod_security_cm_on_load_account($page, $tpl)
{
    if (Auth::isLogged())
    {
        $anagraph = Auth::get();
        if(cm::env("MOD_AUTH_USER_AVATAR")) {
            $tpl->set_var("avatar", Auth::getUserAvatar());
            $tpl->parse("SectUserAvatar", false);
        }
        $tpl->set_var("nomeutente", $anagraph["name"]);
    }
}

function mod_security_cm_on_load_brand($page, $tpl)
{
    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();
    $domain_name = mod_security_get_domain();

    $tpl->set_var("logo_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logo"]));


    $logo = cm::env("MOD_AUTH_BRAND_LOGO");
    if($logo && is_file(FF_DISK_PATH . $logo))
        $logo_url = $logo;
    elseif($restricted && is_file(FF_THEME_DISK_PATH . "/" . $cm->oPage->getTheme() . "/images/logo/restricted.png"))
        $logo_url = ff_getThemePath($cm->oPage->getTheme()) . "/" . $cm->oPage->getTheme() . "/images/logo/restricted.png";
    elseif(is_file(FF_THEME_DISK_PATH . "/" . $cm->oPage->getTheme() . "/images/logo/login.svg"))
        $logo_url = ff_getThemePath($cm->oPage->getTheme()) . "/" . $cm->oPage->getTheme() . "/images/logo/login.svg";
    elseif(!$restricted &&  is_file(FF_THEME_DISK_PATH . "/" . $cm->oPage->getTheme() . "/images/logo/login.png"))
        $logo_url = ff_getThemePath($cm->oPage->getTheme()) . "/" . $cm->oPage->getTheme() . "/images/logo/login.png";
    elseif(is_file(FF_THEME_DISK_PATH . "/" . cm_getMainTheme() . "/images/nobrand.svg"))
        $logo_url = ff_getThemePath(cm_getMainTheme()) . "/" . cm_getMainTheme() . "/images/nobrand.svg";


    if(Auth::get("user")->acl >= cm::env("MOD_AUTH_BRAND_ACL")) {
        if($logo_url) {
            $tpl->set_var("logo_url", $logo_url);
            $tpl->set_var("logo_name", $domain_name);
            $tpl->parse("SectLogo", false);
        } else {
            $tpl->set_var("host_name", $domain_name);
        }

        $tpl->set_var("nav_left_class", "domain");//Cms::getInstance("frameworkcss")->getClass($framework_css["fullbar"]["nav"]["left"]));
        $tpl->set_var("more_icon", '<i class="' . $framework_css["icons"]["settings"] . '"></i>');
        $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
        $tpl->set_var("panel_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_header_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["header"]));
        $tpl->set_var("panel_body_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["body"]["def"]));
        $tpl->set_var("panel_links_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["body"]["links"]));
        $tpl->set_var("panel_footer_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["footer"]));

        /*$mod_sec_domains = $cm->router->getRuleById("mod_sec_domains");
        if($mod_sec_domains->reverse) {
            $tpl->set_var("manage_domains", FF_SITE_PATH . $mod_sec_domains->reverse);
            $tpl->set_var("domains_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["actions"]["domains"]));
            $tpl->parse("SectDomains", false);
        }
        $mod_sec_profiling = $cm->router->getRuleById("mod_sec_profiling");
        if($mod_sec_profiling->reverse) {
            $tpl->set_var("manage_profiling", FF_SITE_PATH . $mod_sec_profiling->reverse);
            $tpl->set_var("profiling_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["actions"]["profiling"]));
            $tpl->parse("SectProfiling", false);
        }
        $mod_restricted_settings = $cm->router->getRuleById("mod_restricted_settings");
        if($mod_restricted_settings->reverse) {
            $tpl->set_var("manage_settings", FF_SITE_PATH . $mod_restricted_settings->reverse);
            $tpl->set_var("settings_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["actions"]["settings"]));
            $tpl->parse("SectSettings", false);
        }*/

        if($page->sections["admin"]) {
            if($page->tpl_layer[0]->isset_var("brand") && !$page->tpl_layer[0]->isset_var("admin")) {

                $tpl->set_var("admin", $page->sections["admin"]["tpl"]->rpparse("SectMenu", false));
            }
        }

        $tpl->parse("SectBrandName", false);

        if(cm::env("MOD_AUTH_MULTIDOMAIN") && !Auth::env("MOD_AUTH_NOACCOUNTSCOMBO"))
        {
            //if(!$ID_domain)
            //	$host_class = " hidden";

            //$tpl->set_var("host_class", Cms::getInstance("frameworkcss")->getClass($framework_css["fullbar"]["nav"]["left"]) . $host_class);
            //$tpl->set_var("host_name", get_session("Domain"));
            //$tpl->set_var("host_icon", Cms::getInstance("frameworkcss")->get("external-link", "icon-tag"));

            $field = ffField::factory($page);
            $field->id = "accounts";
            $field->base_type = "Number";
            $field->widget = "actex";
            $field->actex_update_from_db = true;
            $field->multi_select_one_label = ffTemplate::_get_word_by_code("master_domain");
            $field->source_SQL = "SELECT name, name FROM " . CM_TABLE_PREFIX . "mod_security_domains ORDER BY name";
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
            $field->value = new ffData($domain_name);
            $field->parent_page = array(&$page);
            $tpl->set_var("domain_switch", $field->process());

            $tpl->parse("SectMultiDomain", false);
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
    $cm = cm::getInstance();

    $framework_css = mod_restricted_get_framework_css();

    $flag_dim = "16";

    $user = Auth::get();

    if($user["locale"]["lang"]) {
        $locale["lang"] = $user["locale"]["lang"];
    } else {
        $locale = mod_security_get_locale();
    }
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
        $tpl->set_var("panel_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["container"]));
        $tpl->set_var("panel_body_class", Cms::getInstance("frameworkcss")->getClass($framework_css["dropdown"]["body"]["def"]));
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

function mod_auth_get_locale($lang_default = null, $nocurrent = false) { //cache, security
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

function mod_auth_get_domain() {
    return ($_COOKIE["domain"]
        ? $_COOKIE["domain"]
        : (Auth::isLogged()
            ? Auth::get("domain")->name
            : $_SERVER["HTTP_HOST"]
        )
    );
}

function create_new_user_social($arrUserInfo, $ID_domain, $public_user = false) {
    $anagraphObject = Anagraph::getInstance();
    $arrUser = $anagraphObject->insert(
        array(
            "access.users.acl" => 3  /* DA CONTROLLARE E SOSTITUIRE CON COSTANTE */
            , "access.users.ID_domain" => $ID_domain
            , "access.users.acl_primary" => "Utente"  /* DA CONTROLLARE E SOSTITUIRE CON COSTANTE */
            , "access.users.expire" => 0
            , "access.users.status" => 0
            , "access.users.username" => $arrUserInfo["user"]["access.users.username"]
            , "access.users.username_slug" => $arrUserInfo["user"]["access.users.username_slug"]
            , "access.users.email" => $arrUserInfo["user"]["access.users.email"]
            , "access.users.tel" => ""
            , "access.users.password" => ""
            , "access.users.avatar" => $arrUserInfo["user"]["access.users.avatar"]
            , "access.users.created" => time()
            , "access.users.last_update" => time()
            , "access.users.last_login" => time()
            , "access.users.ID_lang" => LANGUAGE_INSET_ID
            , "access.users.SID" => ""
            , "access.users.SID_expire" => 0
            , "access.users.SID_device" => 0
            , "access.users.SID_ip" => ""
            , "access.users.SID_question" => ""
            , "access.users.SID_answer" => ""
            , "access.users.verified_email" => 0
            , "access.users.verified_tel" => 0
        )
    );
    $arrUserInfo["anagraph"]["anagraph.ID_user"] = $arrUser[0]["user"];

    $ID_user = $arrUserInfo["anagraph"]["anagraph.ID_user"];

    if($arrUserInfo["anagraph"]) {
        if($arrUserInfo["anagraph"]["anagraph_person.name"]) {
            $anagraph_name = $arrUserInfo["anagraph"]["anagraph_person.name"];
        }
        if($arrUserInfo["anagraph"]["anagraph_person.surname"]) {
            if(strlen($anagraph_name)) {
                $anagraph_name .= " ";
            }
            $anagraph_name .= $arrUserInfo["anagraph"]["anagraph_person.surname"];
        }

        if($public_user) { /* DA CONTROLLARE E SOSTITUIRE CON COSTANTE */
            $arrUserInfo["anagraph"]["anagraph_seo.ID_lang"] = LANGUAGE_INSET_ID;
            $arrUserInfo["anagraph"]["anagraph_seo.h1"] = $arrUserInfo["user"]["access.users.username"];
            $arrUserInfo["anagraph"]["anagraph_seo.meta_description"] = $arrUserInfo["user"]["access.users.username"];
            $arrUserInfo["anagraph"]["anagraph_seo.meta_title"] = $arrUserInfo["user"]["access.users.username"];
            $arrUserInfo["anagraph"]["anagraph_seo.smart_url"] = $arrUserInfo["user"]["access.users.username_slug"];
            $arrUserInfo["anagraph"]["anagraph_seo.parent"] = '/';
            $arrUserInfo["anagraph"]["anagraph_seo.permalink"] = "/" . $arrUserInfo["user"]["access.users.username_slug"];
        }

        $arrUserInfo["anagraph"]["anagraph.password_alg"] = "";
        $arrUserInfo["anagraph"]["anagraph.valid_tel"] = 0;
        $arrUserInfo["anagraph"]["anagraph.valid_email"] = 0;

        $arrUserInfo["anagraph"]["anagraph.created"] = time();
        $arrUserInfo["anagraph"]["anagraph.last_update"] = time();

        $arrUserInfo["anagraph"]["anagraph.name"] = $anagraph_name;
        $arrUserInfo["anagraph"]["anagraph.ID_role"] = null;
        $arrUserInfo["anagraph"]["anagraph.ID_lang"] = LANGUAGE_INSET_ID;
        $arrUserInfo["anagraph"]["anagraph.ID_domain"] = $ID_domain;
        $arrUserInfo["anagraph"]["anagraph.ID_type"] = 1;  /* DA CONTROLLARE E SOSTITUIRE CON COSTANTE */

        foreach($arrUserInfo["anagraph"] AS $key => $register_value) {
            $arrInsert[$key] = $register_value;
        }

        if(is_array($arrInsert) && count($arrInsert)) {
            $anagraphObject->insert(
                $arrInsert
            );
        }
    }

    if($arrUserInfo["tokens"]) {
        Anagraph::getInstance("access")->insert(array(
                "tokens.ID_user" => $ID_user
                , "tokens.type" => $arrUserInfo["tokens"]["type"]
                , "tokens.token" => $arrUserInfo["tokens"]["token"]
            )
        );
    }
    Auth::getInstance("session")->create($ID_user);
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

    if (Auth::isAdmin())
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
    $db = ffDB_Sql::factory();
    $db2 = ffDB_Sql::factory();

    if ($usernid === null && Auth::isLogged())
        $usernid = Auth::get("user")->id;

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
    return null;

    if ($UserNID === null)
        if (Auth::isLogged())
            $UserNID = Auth::get("user")->id;
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
function mod_auth_getOauth2Server()
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

    //$cm->preloadApplets($tpl);
    //s$cm->parseApplets($tpl);

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
    $server = mod_auth_getOauth2Server();

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
    $server = mod_auth_getOauth2Server();

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

function mod_auth_social_get_google_client()
{
    $client = new Google_Client();

    $client->setApplicationName(cm::env("MOD_AUTH_SOCIAL_GPLUS_APP_NAME"));
    $client->setClientId(cm::env("MOD_AUTH_SOCIAL_GPLUS_CLIENT_ID"));
    $client->setClientSecret(cm::env("MOD_AUTH_SOCIAL_GPLUS_CLIENT_SECRET"));
    $client->setRedirectUri(cm::env("MOD_AUTH_SOCIAL_GPLUS_REDIRECT"));

    $arrScope = explode(",", cm::env("MOD_AUTH_SOCIAL_GPLUS_SCOPE"));
    if(is_array($arrScope) && count($arrScope)) {
        $googleScope = array();
        foreach($arrScope AS $scope) {
            switch($scope) {
                case "PLUS_LOGIN":
                    $googleScope[] = Google_Service_Oauth2::PLUS_LOGIN;
                    break;
                case "PLUS_ME":
                    $googleScope[] = Google_Service_Oauth2::PLUS_ME;
                    break;
                case "USERINFO_EMAIL":
                    $googleScope[] = Google_Service_Oauth2::USERINFO_EMAIL;
                    break;
                case "USERINFO_PROFILE":
                    $googleScope[] = Google_Service_Oauth2::USERINFO_PROFILE;
                    break;
                default:
            }
        }
        $client->setScopes($googleScope);
    }

    return $client;
}

/***
 * Framework css
 */

function mod_auth_get_framework_css() {
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