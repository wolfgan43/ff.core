<?php
$cm->oPage->layer = "empty";

$framework_css = mod_auth_get_framework_css();
$mod_auth_login = $cm->router->getRuleById("mod_auth_login");

if (Auth::isLogged()) {
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
		//$tpl->set_var("username_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]["username"]));
		$tpl->set_var("username", $username);
		$tpl->parse("SectUsername", false);
	}
	if($email) {
		//$tpl->set_var("email_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]["email"]));
		$tpl->set_var("email", $email);
		$tpl->parse("SectEmail", false);
	}

    $tpl->set_var("logout_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", Cms::getInstance("frameworkcss")->getClass($framework_css["logout"]["account"]));
    $tpl->set_var("login_button_class", Cms::getInstance("frameworkcss")->getClass($framework_css["actions"]["login"]["def"])); 
    $tpl->set_var("login_url", $mod_auth_login->reverse);
    $tpl->set_var("error_class", Cms::getInstance("frameworkcss")->getClass($framework_css["error"]));

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

$loginUrl = $helper->getLoginUrl(explode(",", cm::env("MOD_AUTH_SOCIAL_FACEBOOK_CLIENT_SCOPE")), null, true, "https");

ffRedirect($loginUrl);
