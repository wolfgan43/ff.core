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

$cm->oPage->layer = "empty";

$framework_css = mod_auth_get_framework_css();
$mod_auth_login= $cm->router->getRuleById("mod_auth_login");
$mod_auth_dashboard = $cm->router->getRuleById("mod_auth_dashboard");
if($mod_auth_dashboard)
	$dashboard_ret_url = $mod_auth_dashboard->reverse;

if (Auth::isLogged()) {
	if ($filename === null) {
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/social/logged.html", $cm->oPage->theme, false);
    }
	if ($filename === null) {
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/social/logged.html", $cm->oPage->theme, false);
    }
	if ($filename === null) {
        $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/social/logged.html", $cm->oPage->theme);
    }

	$tpl = ffTemplate::factory(ffCommon_dirname($filename));
	$tpl->load_file(basename($filename), "main");

	$tpl->set_var("site_path", FF_SITE_PATH);
	$tpl->set_var("theme", $cm->oPage->theme);
	$tpl->set_var("domain", $_SERVER["HTTP_HOST"]);

	/**
	* 	Responsive Parsing
	*/
   	$component_class["base"] = $framework_css["component"]["class"];
   	$component_class["popup"] = "social-popup";
    if($framework_css["component"]["grid"]) {
        if(is_array($framework_css["component"]["grid"]))
            $component_class["grid"] = Cms::getInstance("frameworkcss")->get($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = Cms::getInstance("frameworkcss")->get("", $framework_css["component"]["grid"]);      
        }
    }   
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", Cms::getInstance("frameworkcss")->getClass($framework_css["inner-wrap"]));

	if(cm::env("MOD_AUTH_USER_AVATAR")) {
		$tpl->set_var("avatar_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]["avatar"]));
		$tpl->set_var("avatar", Auth::getUserAvatar(cm::env("MOD_AUTH_USER_AVATAR")));
		$tpl->parse("SectAvatar", false);
	}

    $user = Auth::get("user");

    if($user->username) {
        $tpl->set_var("username", $user->username);
        $tpl->parse("SectUsername", false);
    }
    if($user->email) {
        $tpl->set_var("email", $user->email);
        $tpl->parse("SectEmail", false);
    }

    $tpl->set_var("logout_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]));
    $tpl->set_var("login_button_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["login"]));
    $tpl->set_var("login_url", $dashboard_ret_url);
    $tpl->set_var("error_class", Cms::getInstance("frameworkcss")->getClass($framework_css["error"]));

	$cm->oPage->addContent($tpl);
	exit;
}

if (isset($_GET['code'])) 
{
	$client = mod_auth_social_get_google_client();

	$client->authenticate($_GET['code']);
	$access_token = $client->getAccessToken();

	$oauth2 = new Google_Service_Oauth2($client);
	
	$ret = $oauth2->userinfo->get();
	
	$arrDefaultFields = explode(",", MOD_SEC_DEFAULT_FIELDS);

    if(!$username) {
        $username = $ret["name"];
    }
    if(!$username) {
        $arrUsername = explode("@", $ret["email"]);

        $username = $arrUsername[0] . " " . substr($arrUsername[1], 0, strpos($arrUsername[1], "."));
        $username = ucwords(str_replace(array(".", "-", "_"), array(" "), $username));
    }


    $arrUserParams["username"] = $username;
    
	if(MOD_SEC_STRICT_FIELDS)
	{
		if(strlen(MOD_SEC_USER_FIRSTNAME)) 
		{
			if(array_search(MOD_SEC_USER_FIRSTNAME, $arrDefaultFields) === false)
				$arrUserField[MOD_SEC_USER_FIRSTNAME] = $ret["familyName"];	
			else
				$arrUserParams[MOD_SEC_USER_FIRSTNAME] = $ret["familyName"];	
		}
		
		if(strlen(MOD_SEC_USER_LASTNAME)) 
		{
			if(array_search(MOD_SEC_USER_LASTNAME, $arrDefaultFields) === false)
				$arrUserField[MOD_SEC_USER_LASTNAME] = $ret["givenName"];
			else
				$arrUserParams[MOD_SEC_USER_LASTNAME] = $ret["givenName"];
		}
	}
	else 
	{
		if(array_search("name", $arrDefaultFields) === false)
			$arrUserField["name"] = $ret["givenName"];	
		else
			$arrUserParams["name"] = $ret["givenName"];	

		if(array_search("surname", $arrDefaultFields) === false)
			$arrUserField["surname"] = $ret["familyName"];
		else
			$arrUserParams["surname"] = $ret["familyName"];

	}
	
	//email
	
	if($ret["verifiedEmail"] == "1") 
		$arrUserParams["status"] = true; 
	else
		$arrUserParams["status"] = false; 

	//social profile url
	/*if(strlen($ret["profile"]["providerSpecifier"])) 
	{
		$arrUserField[$ret["profile"]["providerSpecifier"]] = $ret["profile"]["url"];
	}*/

	// token
	$UserToken["token"] = $access_token;
	$UserToken["type"] = "google";
		
	$res = mod_security_set_user_by_social("google", $arrUserParams, $arrUserField, $UserToken, null, false, true);
	$sError = $res["error"];
	
	if (strlen($sError))
    {
        $cm->modules["auth"]["events"]->doEvent("google_error", array(&$sError, &$ret_url, &$err_url));
        
        if ($filename === null)
            $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/social/error.html", $cm->oPage->theme, false);
        if ($filename === null)
            $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/social/error.html", $cm->oPage->theme, false);
        if ($filename === null)
            $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/social/error.html", $cm->oPage->theme);

        $tpl = ffTemplate::factory(ffCommon_dirname($filename));
        $tpl->load_file(basename($filename), "main");

        $tpl->set_var("site_path", FF_SITE_PATH);
        $tpl->set_var("theme", $cm->oPage->theme);
        $tpl->set_var("domain", $_SERVER["HTTP_HOST"]);
        
        $tpl->set_var("sError", ffCommon_specialchars($sError));

		/**
		* 	Responsive Parsing
		*/
		$component_class["base"] = $framework_css["component"]["class"];
   		$component_class["popup"] = "social-popup";
	    if($framework_css["component"]["grid"]) {
	        if(is_array($framework_css["component"]["grid"]))
	            $component_class["grid"] = Cms::getInstance("frameworkcss")->get($framework_css["component"]["grid"], "col");
	        else {
	            $component_class["grid"] = Cms::getInstance("frameworkcss")->get("", $framework_css["component"]["grid"]);      
	        }
	    }   
	    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
	    $tpl->set_var("inner_wrap_class", Cms::getInstance("frameworkcss")->getClass($framework_css["inner-wrap"]));		
	    
	    $tpl->set_var("login_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["def"]));
	    $tpl->set_var("actions_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["def"]));
	    $tpl->set_var("account_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]));
	    $tpl->set_var("logout_button_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["logout"]));
	    $tpl->set_var("logout_url", $mod_auth_login->reverse);
	    $tpl->set_var("error_class", Cms::getInstance("frameworkcss")->getClass($framework_css["error"]));
        
        $cm->oPage->addContent($tpl);
        return;
    }

	if (strlen($sError))
		die($sError);
	
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/social/success.html", $cm->oPage->theme, false);
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/social/success.html", $cm->oPage->theme, false);
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/social/success.html", $cm->oPage->theme);

	$tpl = ffTemplate::factory(ffCommon_dirname($filename));
	$tpl->load_file(basename($filename), "main");

	$tpl->set_var("site_path", FF_SITE_PATH);
	$tpl->set_var("theme", $cm->oPage->theme);
	$tpl->set_var("domain", $_SERVER["HTTP_HOST"]);

	/**
	* 	Responsive Parsing
	*/
   	$component_class["base"] = $framework_css["component"]["class"];
    if($framework_css["component"]["grid"]) {
        if(is_array($framework_css["component"]["grid"]))
            $component_class["grid"] = Cms::getInstance("frameworkcss")->get($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = Cms::getInstance("frameworkcss")->get("", $framework_css["component"]["grid"]);      
        }
    }   
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", Cms::getInstance("frameworkcss")->getClass($framework_css["inner-wrap"]));

	if(cm::env("MOD_AUTH_USER_AVATAR")) {
		$tpl->set_var("avatar_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]["avatar"]));
		$tpl->set_var("avatar", Auth::getUserAvatar(cm::env("MOD_AUTH_USER_AVATAR")));
		$tpl->parse("SectAvatar", false);
	}

    $user = Auth::get("user");

    if($user->username) {
        $tpl->set_var("username", $user->username);
        $tpl->parse("SectUsername", false);
    }
    if($user->email) {
        $tpl->set_var("email", $user->email);
        $tpl->parse("SectEmail", false);
    }

    $tpl->set_var("logout_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]));
    $tpl->set_var("login_button_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["login"])); 
    $tpl->set_var("login_url", $dashboard_ret_url);
    $tpl->set_var("error_class", Cms::getInstance("frameworkcss")->getClass($framework_css["error"]));	
	
	$cm->oPage->addContent($tpl);
}