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

$framework_css = mod_sec_get_framework_css();
$mod_sec_login = $cm->router->getRuleById("mod_sec_login");
$mod_sec_dashboard = $cm->router->getRuleById("mod_sec_dashboard");
if($mod_sec_dashboard)
	$dashboard_ret_url = $mod_sec_dashboard->reverse;

if (mod_security_check_session(false) && get_session("UserNID") != MOD_SEC_GUEST_USER_ID)
{
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/social/logged.html", $cm->oPage->theme, false);
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/social/logged.html", $cm->oPage->theme, false);
	if ($filename === null)
		$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/social/logged.html", $cm->oPage->theme);

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
            $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
        }
    }   
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));

	if(MOD_SEC_USER_AVATAR) {
		if(MOD_SEC_GROUPS) {
		    $user_permission = get_session("user_permission");
			$avatar = $user_permission["avatar"];
		} else {
		    $avatar = mod_security_getUserInfo(MOD_SEC_USER_AVATAR, null, $db)->getValue();
		}
		
		$tpl->set_var("avatar_class", cm_getClassByDef($framework_css["logout"]["account"]["avatar"]));
		$tpl->set_var("avatar", Auth::getUserAvatar(cm::env("MOD_SEC_USER_AVATAR_MODE"), $avatar));
		$tpl->parse("SectAvatar", false);
	}
		
	$email = ffCommon_specialchars(mod_security_getUserInfo("email", null, $db)->getValue());
	
	$username = ffCommon_specialchars(mod_security_getUserInfo(MOD_SEC_USER_FIRSTNAME, null, $db)->getValue() . " " . mod_security_getUserInfo(MOD_SEC_USER_LASTNAME, null, $db)->getValue());
	if(!$username)
		$username = ffCommon_specialchars(mod_security_getUserInfo(MOD_SEC_USER_FIRSTNAME, null, $db)->getValue());
	if(!$username) {
		if((MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username"))
			$username = ffCommon_specialchars(mod_security_getUserInfo("username", null, $db)->getValue());
		elseif((MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "email")) {
			$username = $email;
			$email = "";
		}
	}

	if($username) {
		//$tpl->set_var("username_class", cm_getClassByDef($framework_css["logout"]["account"]["username"]));
		$tpl->set_var("username", $username);
		$tpl->parse("SectUsername", false);
	}
	if($email) {
		//$tpl->set_var("email_class", cm_getClassByDef($framework_css["logout"]["account"]["email"]));
		$tpl->set_var("email", $email);
		$tpl->parse("SectEmail", false);
	}

    $tpl->set_var("logout_class", cm_getClassByDef($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("login_button_class", cm_getClassByDef($framework_css["actions"]["login"]));
    $tpl->set_var("login_url", $dashboard_ret_url);
    $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));

	$cm->oPage->addContent($tpl);
	return;	
}

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication(MOD_SEC_SOCIAL_FACEBOOK_APPID, MOD_SEC_SOCIAL_FACEBOOK_SECRET);
$helper = new FacebookRedirectLoginHelper(MOD_SEC_SOCIAL_FACEBOOK_CLIENT_REDIR_URI);
$helper->disableSessionStatusCheck();

$_SESSION['FBRLH_' . 'state'] = $_GET["state"];

try {
	$session = $helper->getSessionFromRedirect();

	if ($session) 
	{
		try {
            $user_profile = (new FacebookRequest(
                $session, 'GET', '/me?fields=id,email,first_name,last_name,is_verified,birthday,cover,name,gender'
            ))->execute()->getGraphObject(GraphUser::className());


            $arrDefaultFields = explode(",", MOD_SEC_DEFAULT_FIELDS);
            $arrUserParams["email"] = $user_profile->getEmail();

            if(!strlen($username)) {
                $username = $user_profile->getName();
            }
            if(!strlen($username)) {
                $arrUsername = explode("@", $arrUserParams["email"]);

                $username = $arrUsername[0] . " " . substr($arrUsername[1], 0, strpos($arrUsername[1], "."));
                $username = ucwords(str_replace(array(".", "-", "_"), array(" "), $username));
            }

            $arrUserParams["username"] = $username;
            if(MOD_SEC_STRICT_FIELDS)
            {
                if(strlen(MOD_SEC_USER_FIRSTNAME))
                {
                    if(array_search(MOD_SEC_USER_FIRSTNAME, $arrDefaultFields) === false)
                        $arrUserField[MOD_SEC_USER_FIRSTNAME] = $user_profile->getFirstName();
                    else
                        $arrUserParams[MOD_SEC_USER_FIRSTNAME] = $user_profile->getFirstName();
                }

                if(strlen(MOD_SEC_USER_LASTNAME))
                {
                    if(array_search(MOD_SEC_USER_LASTNAME, $arrDefaultFields) === false)
                        $arrUserField[MOD_SEC_USER_LASTNAME] = $user_profile->getLastName();
                    else
                        $arrUserParams[MOD_SEC_USER_LASTNAME] = $user_profile->getLastName();
                }

                $cover = $user_profile->getProperty("cover");
                if($cover) {
                    if(array_search(MOD_SEC_USER_AVATAR, $arrDefaultFields) === false)
                        $arrUserField[MOD_SEC_USER_AVATAR] = $cover->getProperty("source");
                    else
                        $arrUserParams[MOD_SEC_USER_AVATAR] = $cover->getProperty("source");
                }
            }
            else
            {
                if(array_search("name", $arrDefaultFields) === false)
                    $arrUserField["name"] = $user_profile->getFirstName();
                else
                    $arrUserParams["name"] = $user_profile->getFirstName();

                if(array_search("surname", $arrDefaultFields) === false)
                    $arrUserField["surname"] = $user_profile->getLastName();
                else
                    $arrUserParams["surname"] = $user_profile->getLastName();


                $cover = $user_profile->getProperty("cover");
                if($cover) {
                    if (array_search("avatar", $arrDefaultFields) === false)
                        $arrUserField["avatar"] = $cover->getProperty("source");
                    else
                        $arrUserParams["avatar"] = $cover->getProperty("source");
                }

                if(array_search("gender", $arrDefaultFields) === false)
                    $arrUserField["gender"] = $user_profile->getGender();
                else
                    $arrUserParams["gender"] = $user_profile->getGender();

                if(array_search("birthday", $arrDefaultFields) === false)
                    $arrUserField["birthday"] = $user_profile->getBirthday();
                else
                    $arrUserParams["birthday"] = $user_profile->getBirthday();
            }

            if($arrUserParams["email"])
                $arrUserParams["status"] = true;

            // token
            $UserToken["token"] = $session->getToken();
            $UserToken["type"] = "facebook";

            $res = mod_security_set_user_by_social("facebook", $arrUserParams, $arrUserField, $UserToken, null, false, true);
            $sError = $res["error"];

            if (strlen($sError))
			{
				$cm->modules["auth"]["events"]->doEvent("facebook_error", array(&$sError, &$ret_url, &$err_url));

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
                        $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
                    else {
                        $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
                    }
                }  

                $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
                $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
			    $tpl->set_var("login_class", cm_getClassByDef($framework_css["login"]["def"]));
			    $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
			    $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
			    $tpl->set_var("logout_button_class", cm_getClassByDef($framework_css["actions"]["logout"]));
			    $tpl->set_var("logout_url", $mod_sec_login->reverse);
			    $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));
                
				$cm->oPage->addContent($tpl);
				return;
			}

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
   			$component_class["popup"] = "social-popup";
            if($framework_css["component"]["grid"]) {
                if(is_array($framework_css["component"]["grid"]))
                    $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
                else {
                    $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
                }
            }   
            $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
            $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));

			if(MOD_SEC_USER_AVATAR) {
				if(MOD_SEC_GROUPS) {
		            $user_permission = get_session("user_permission");
					$avatar = $user_permission["avatar"];
		        } else {
		        	$avatar = mod_security_getUserInfo(MOD_SEC_USER_AVATAR, null, $db)->getValue();
		        }

				$tpl->set_var("avatar_class", cm_getClassByDef($framework_css["logout"]["account"]["avatar"]));
				$tpl->set_var("avatar", Auth::getUserAvatar(cm::env("MOD_SEC_USER_AVATAR_MODE"), $avatar));
				$tpl->parse("SectAvatar", false);
			}
				
			$email = ffCommon_specialchars(mod_security_getUserInfo("email", null, $db)->getValue());
			
			$username = ffCommon_specialchars(mod_security_getUserInfo(MOD_SEC_USER_FIRSTNAME, null, $db)->getValue() . " " . mod_security_getUserInfo(MOD_SEC_USER_LASTNAME, null, $db)->getValue());
			if(!$username)
				$username = ffCommon_specialchars(mod_security_getUserInfo(MOD_SEC_USER_FIRSTNAME, null, $db)->getValue());
			if(!$username) {
				if((MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username"))
					$username = ffCommon_specialchars(mod_security_getUserInfo("username", null, $db)->getValue());
				elseif((MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "email")) {
					$username = $email;
					$email = "";
				}
			}

			if($username) {
				//$tpl->set_var("username_class", cm_getClassByDef($framework_css["logout"]["account"]["username"]));
				$tpl->set_var("username", $username);
				$tpl->parse("SectUsername", false);
			}
			if($email) {
				//$tpl->set_var("email_class", cm_getClassByDef($framework_css["logout"]["account"]["email"]));
				$tpl->set_var("email", $email);
				$tpl->parse("SectEmail", false);
			}

			/**
			* 	Responsive Parsing
			*/
			$component_class["base"] = $framework_css["component"]["class"];
   			$component_class["popup"] = "social-popup";
            if($framework_css["component"]["grid"]) {
                if(is_array($framework_css["component"]["grid"]))
                    $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
                else {
                    $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
                }
            }   
            $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
            $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
            
            $tpl->set_var("logout_class", cm_getClassByDef($framework_css["logout"]["def"]));
            $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
            $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
            $tpl->set_var("login_button_class", cm_getClassByDef($framework_css["actions"]["login"]));
            $tpl->set_var("login_url", $dashboard_ret_url);
            $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));
            
			$cm->oPage->addContent($tpl);
			
		} catch (FacebookRequestException $e) {
			
			echo "Exception occured, code: " . $e->getCode();
			echo " with message: " . $e->getMessage();
			exit;
			
		}
	} else {
		$cm->modules["auth"]["events"]->doEvent("facebook_error", array(&$sError, &$ret_url, &$err_url));

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
	    
	    $tpl->set_var("sError", $_REQUEST["error_description"]);

		/**
		* 	Responsive Parsing
		*/                
	    $component_class["base"] = $framework_css["component"]["class"];
   		$component_class["popup"] = "social-popup";
	    if($framework_css["component"]["grid"]) {
	        if(is_array($framework_css["component"]["grid"]))
	            $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
	        else {
	            $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
	        }
	    }   
	    
	    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
	    $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
	    
	    $tpl->set_var("login_class", cm_getClassByDef($framework_css["logout"]["def"]));
	    $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
	    $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
	    $tpl->set_var("logout_button_class", cm_getClassByDef($framework_css["actions"]["logout"]));
	    $tpl->set_var("logout_url", $mod_sec_login->reverse);
	    $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));

		$cm->oPage->addContent($tpl);
	}
} catch(FacebookRequestException $ex) {
  // When Facebook returns an error
	$cm->modules["auth"]["events"]->doEvent("facebook_error", array(&$sError, &$ret_url, &$err_url));

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
    
    $tpl->set_var("sError", ffCommon_specialchars($ex->getErrorType() . " - (" . $ex->getCode() . "/" . $ex->getSubErrorCode() . ") " . $ex->getMessage()));

	/**
	* 	Responsive Parsing
	*/                
    $component_class["base"] = $framework_css["component"]["class"];
   	$component_class["popup"] = "social-popup";
    if($framework_css["component"]["grid"]) {
        if(is_array($framework_css["component"]["grid"]))
            $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
        }
    }   
    
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
    
    $tpl->set_var("login_class", cm_getClassByDef($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("logout_button_class", cm_getClassByDef($framework_css["actions"]["logout"]));
    $tpl->set_var("logout_url", $mod_sec_login->reverse);
    $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));

	$cm->oPage->addContent($tpl);
	return;
} catch(\Exception $ex) {
  // When validation fails or other local issues
	$cm->modules["auth"]["events"]->doEvent("facebook_error", array(&$sError, &$ret_url, &$err_url));

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

	$tpl->set_var("sError", ffCommon_specialchars("VALIDATION FAILED"));

	/**
	* 	Responsive Parsing
	*/                
    $component_class["base"] = $framework_css["component"]["class"];
   	$component_class["popup"] = "social-popup";
    if($framework_css["component"]["grid"]) {
        if(is_array($framework_css["component"]["grid"]))
            $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
        }
    }   
    
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
    
    $tpl->set_var("login_class", cm_getClassByDef($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("logout_button_class", cm_getClassByDef($framework_css["actions"]["logout"])); 
    $tpl->set_var("logout_url", $mod_sec_login->reverse);
    $tpl->set_var("error_class", cm_getClassByDef($framework_css["error"]));
    
	$cm->oPage->addContent($tpl);
	return;
}
