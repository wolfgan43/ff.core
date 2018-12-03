<?php
$cm->oPage->layer = "empty";

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

    $tpl->set_var("logout_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["def"]));
    $tpl->set_var("actions_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["def"]));
    $tpl->set_var("account_class", $cm->oPage->frameworkCSS->getClass($framework_css["logout"]["account"]));
	$tpl->set_var("login_button_class", $cm->oPage->frameworkCSS->getClass($framework_css["actions"]["login"]));
    $tpl->set_var("login_url", $mod_auth_login->reverse);
    $tpl->set_var("error_class", $cm->oPage->frameworkCSS->getClass($framework_css["error"]));
    
    
	$cm->oPage->addContent($tpl);
}

$client = mod_auth_social_get_google_client();
                                        
$authUrl = $client->createAuthUrl();
ffRedirect($authUrl);
