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
$mod_auth_login = $cm->router->getRuleById("mod_auth_login");
$mod_auth_dashboard = $cm->router->getRuleById("mod_auth_dashboard");
if($mod_auth_dashboard) {
    $dashboard_ret_url = $mod_auth_dashboard->reverse;
}

if (Auth::check() && !Auth::isGuest())
{
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
        if(is_array($framework_css["component"]["grid"])) {
            $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
        } else {
            $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
        }
    }   
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));

	if(cm::env("MOD_AUTH_USER_AVATAR")) {
		$tpl->set_var("avatar_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["avatar"]));
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

    $tpl->set_var("logout_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("login_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["login"]));
    $tpl->set_var("login_url", $dashboard_ret_url);
    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));

	$cm->oPage->addContent($tpl);
	return;	
}

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

FacebookSession::setDefaultApplication(cm::env("MOD_AUTH_SOCIAL_FACEBOOK_CLIENT_ID"), cm::env("MOD_AUTH_SOCIAL_FACEBOOK_CLIENT_SECRET"));
$helper = new FacebookRedirectLoginHelper(cm::env("MOD_AUTH_SOCIAL_FACEBOOK_CLIENT_REDIRECT"));
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

            $UserToken["token"] = $session->getToken();
            $UserToken["type"] = "facebook";

            if ($ID_domain === null) {
                $ID_domain = mod_auth_get_domain();
            }

            $permanent_session = cm::env("MOD_AUTH_SESSION_PERMANENT");
            $disable_events = false;
            $skip_redirect = true;
            $social = "facebook";

            $email = $user_profile->getEmail();

            if(1) {
                $anagraphObject = Anagraph::getInstance();
                $arrAnagraphList = $anagraphObject->read(
                    array(
                        "anagraph.ID"
                        , "access.users.status"
                        , "access.users.ID"
                        , "access.users.username"
                        , "access.users.avatar"
                    ), array(
                        "access.users.email" => $email
                    )
                );

                if(is_array($arrAnagraphList) && count($arrAnagraphList)) {
                    if($arrAnagraphList[0]["user"]["status"]) {
                        $ID_user = $arrAnagraphList[0]["user"]["ID"];

                        if($UserToken["token"]) {
                            Anagraph::getInstance("access")->write(array(
                                    "tokens.ID_user" => $ID_user
                                    , "tokens.type" => $UserToken["type"]
                                )
                                , array(
                                    "tokens.token" => $UserToken["token"]
                                )
                                , array(
                                    "tokens.ID_user" => $ID_user
                                    , "tokens.type" => $UserToken["type"]
                                    , "tokens.token" => $UserToken["token"]
                                )
                            );
                        }

                        Auth::getInstance("session")->create($ID_user);
                        $sError = false;
                    } else {
                        $sError = ffTemplate::_get_word_by_code($social . "_login_user_not_active");
                    }
                } else {
                    $arrUserInfo["user"]["access.users.email"] = $email;
                    $arrUserInfo["anagraph"]["anagraph.email"] = $email;

                    if (!strlen($username)) {
                        $username = $user_profile->getName();
                    }
                    if (!strlen($username)) {
                        $arrUsername = explode("@", $arrUserParams["email"]);

                        $username = $arrUsername[0] . " " . substr($arrUsername[1], 0, strpos($arrUsername[1], "."));
                        $username = ucwords(str_replace(array(".", "-", "_"), array(" "), $username));
                    }

                    $arrUserInfo["user"]["access.users.username"] = $username;
                    $arrUserInfo["user"]["access.users.username_slug"] = ffcommon_url_rewrite($username);

                    $arrUserInfo["anagraph"]["anagraph_person.name"] = $user_profile->getFirstName();
                    $arrUserInfo["anagraph"]["anagraph_person.surname"] = $user_profile->getLastName();


                    $cover = $user_profile->getProperty("cover");
                    if ($cover) {
                        $arrUserField["anagraph"]["anagraph.avatar"] = $cover->getProperty("source");
                        $arrUserField["user"]["access.users.avatar"] = $cover->getProperty("source");
                    }

                    $arrUserInfo["anagraph"]["anagraph_person.gender"] = $user_profile->getGender();
                    $arrUserInfo["anagraph"]["anagraph_person.birthday"] = $user_profile->getBirthday();

                    if ($arrUserInfo["anagraph"]["anagraph.email"]) {
                        $arrUserInfo["user"]["status"] = true;
                    }
                    $arrUserInfo["tokens"] = $UserToken;
                    create_new_user_social($arrUserInfo, $ID_domain);
                }
            }
            // token



            if (strlen($sError))
			{
				if ($filename === null) {
                    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/social/error.html", $cm->oPage->theme, false);
                }
                if ($filename === null) {
                    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/social/error.html", $cm->oPage->theme, false);
                }
				if ($filename === null) {
                    $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/social/error.html", $cm->oPage->theme);
                }

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
                    if(is_array($framework_css["component"]["grid"])) {
                        $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
                    } else {
                        $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
                    }
                }  

                $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
                $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));
			    $tpl->set_var("login_class", $cm->oPage->frameworkCSS->getClass($framework_css["login"]["def"]));
			    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
			    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
			    $tpl->set_var("logout_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["logout"]));
			    $tpl->set_var("logout_url", $mod_auth_login->reverse);
			    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));
                
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
                    $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
                else {
                    $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
                }
            }   
            $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
            $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));

			if(cm::env("MOD_AUTH_USER_AVATAR")) {
				$tpl->set_var("avatar_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["avatar"]));
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

			/**
			* 	Responsive Parsing
			*/
			$component_class["base"] = $framework_css["component"]["class"];
   			$component_class["popup"] = "social-popup";
            if($framework_css["component"]["grid"]) {
                if(is_array($framework_css["component"]["grid"]))
                    $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
                else {
                    $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
                }
            }   
            $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
            $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));
            
            $tpl->set_var("logout_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
            $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
            $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
            $tpl->set_var("login_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["login"]));
            $tpl->set_var("login_url", $dashboard_ret_url);
            $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));
            
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
	            $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
	        else {
	            $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
	        }
	    }   
	    
	    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
	    $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));
	    
	    $tpl->set_var("login_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
	    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
	    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
	    $tpl->set_var("logout_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["logout"]));
	    $tpl->set_var("logout_url", $mod_auth_login->reverse);
	    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));

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
            $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
        }
    }   
    
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));
    
    $tpl->set_var("login_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("logout_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["logout"]));
    $tpl->set_var("logout_url", $mod_auth_login->reverse);
    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));

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
            $component_class["grid"] = $cm->oPage->frameworkCSS->get($framework_css["component"]["grid"], "col");
        else {
            $component_class["grid"] = $cm->oPage->frameworkCSS->get("", $framework_css["component"]["grid"]);
        }
    }   
    
    $tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
    $tpl->set_var("inner_wrap_class", $cm->oPage->frameworkCSS->getClass($framework_css["inner-wrap"]));
    
    $tpl->set_var("login_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]["def"]));
    $tpl->set_var("logout_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["logout"]));
    $tpl->set_var("logout_url", $mod_auth_login->reverse);
    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));
    
	$cm->oPage->addContent($tpl);
	return;
}
