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

        require("ui.php");

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
                : CM_LOCAL_APP_NAME
            )
        );
    }

    public function checkPermission($acl = null) {
        $res = $this->permissionAcl($acl);
        if($res) {
            $res = $this->permissionProfile();
        }

        return $res;
    }
    private function permissionAcl($acl = null) {
        $user_acl = Auth::get("user")->acl;

        return ((Auth::DEBUG && $user_acl == "-1")
            || $acl === null
            || in_array($user_acl, (array) $acl)
        );
    }

    private function permissionProfile() {
        return true;

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

function mod_auth_get_domain() {
    if(Auth::isLogged()) {
        $domain = Auth::get("domain", array("toArray" => true));
    }

    return ($_COOKIE["domain"]
        ? $_COOKIE["domain"]
        : ($domain["name"]
            ? $domain["name"]
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
        , "image" => array(
			"util" => array("corner-circle", "corner-thumbnail")
		)
		, "collapse" => array(
			"action" => Cms::getInstance("frameworkcss")->get("link", "data", "collapse")
			, "pane" => Cms::getInstance("frameworkcss")->get("pane", "collapse")
			, "current" => Cms::getInstance("frameworkcss")->get("current", "collapse")
			, "menu" => Cms::getInstance("frameworkcss")->get("menu", "collapse")
		)
        , "icons" => array(
            "caret-collapsed" => "menu-caret " . Cms::getInstance("frameworkcss")->get("chevron-right", "icon")
            , "caret" => "menu-caret " . Cms::getInstance("frameworkcss")->get("chevron-right", "icon", array("rotate-90"))
            , "settings" => Cms::getInstance("frameworkcss")->get("cog", "icon")
        )
    );

	return $framework_css;
}