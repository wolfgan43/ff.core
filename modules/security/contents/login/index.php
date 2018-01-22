<?php
$options = mod_security_get_settings($cm->path_info);
$cm->oPage->form_method = "post";

if(MOD_SEC_LOGIN_FORCE_LAYER)
    $cm->oPage->layer = MOD_SEC_LOGIN_FORCE_LAYER; 

$mod_sec_login = $cm->router->getRuleById("mod_sec_login");

if(basename($cm->path_info . $cm->real_path_info) != "login") {
    ffRedirect($mod_sec_login->reverse);
}

$ret_url        = ($_REQUEST["ret_url"]
                    ? $_REQUEST["ret_url"]
                    : $_SERVER["HTTP_REFERER"]
                );

$res = $cm->modules["security"]["events"]->doEvent("on_before_login", array($ret_url));
$rc = end($res);
if ($rc !== null) 
{
    $ret_url = $rc;
} else {
    if (!strlen($ret_url) || strpos($ret_url, $mod_sec_login->reverse) !== false)
        $ret_url = rtrim(FF_SITE_PATH, "/") . "/";    
}



if($_SERVER["SERVER_NAME"] == "unastoriachecontinua.paginemediche.it") {
    $ret_url = $_SERVER["HTTP_REFERER"];

}

$cm->oPage->ret_url = $ret_url;

$sError = "";
$frmAction 	= strtolower($_REQUEST["frmAction"]);
$domain 	= $_REQUEST["domain"];
$username 	= $_REQUEST["username"];

$permanent_session = $_REQUEST["stayconnected"];
if($permanent_session == "on") {
    $permanent_session = true;
}

$password 	= $_REQUEST["password"];
if (strlen($_REQUEST["sError"]))
	$sError = strip_tags($_REQUEST["sError"]);

$ID_domain = null;
$logged = mod_security_check_session(false);
if ($logged)
{
	if (get_session("UserID") == MOD_SEC_GUEST_USER_NAME)
		$logged = false;
	else
	{
		if (MOD_SEC_MULTIDOMAIN)
			$ID_domain = mod_security_get_domain();
	}
}
/*
$fixed_ret_url = $_REQUEST["ret_url"];
if (!strlen($fixed_ret_url))
	$fixed_ret_url = $cm->oPage->site_path . "/";
$cm->oPage->ret_url = $fixed_ret_url;*/

$cm->modules["security"]["events"]->doEvent("on_retrieve_params", array(&$sError, &$frmAction, &$logged));



//////////////////////////////////////////////////////////////////////////////////////
// ACTIONS
//////////////////////////////////////////////////////////////////////////////////////
switch($frmAction)
{
	case "login":
		$ret =	mod_sec_check_login($username, $password, $domain, $options, $permanent_session, $logged, $sError, false);
		if($ret["error"])
		{
			$cm->jsonAddResponse(array(
				"success" => false 
				 , "modules" => array(
					"security" => array(
						"action" => "login"
						, "error" => _modsec_process("error", $ret["error"])
					)
				)
			));		
		} elseif ($ret["logged"] === true)
		{
			$cm->jsonAddResponse(array(
				"modules" => array(
					"security" => array(
						"action" => "login"
						, "message" => _modsec_process("logout", true, false)
					)
				)
				, "doredirects" => true
				, "url" => $cm->oPage->ret_url			
			));
		}
		
		return _modsec_login_redirect(null, "login");
		/*$sError			= $ret["error"];
		$logged			= $ret["logged"];
		$userID			= $ret["UserID"];
		$userNID		= $ret["UserNID"];
		$domain			= $ret["domain"];
		$ID_domain		= $ret["ID_domain"];
		$cookiehash		= $ret["cookiehash"];*/
		//break;
	case "logout":
		if(MOD_SEC_ENABLE_TOKEN && $social_enabled)
		{
			// DESTROY TOKENS
			$social_logout_check = $_REQUEST["social_logout_check"];
			if (is_array($social_logout_check) && count($social_logout_check))
			{
				foreach ($social_logout_check as $type => $check)
				{
					if ($check)
						mod_security_accesstoken_revoke($type, null, get_session("UserNID"), $ID_domain, $cm->path_info);
				}
			}
		}
		// DISTRUGGE LA SESSIONE
		mod_security_destroy_session(false);

		$cm->jsonAddResponse(array(
				"modules" => array(
					"security" => array(
						"action" => "logout"
					)
				)
				, "doredirects" => true
				, "url" => $cm->oPage->ret_url		
			));

		return _modsec_login_redirect($cm->oPage->ret_url, "logout");
	
	case "cancellogout":
		return _modsec_login_redirect($cm->oPage->ret_url, "cancellogout");
}


//////////////////////////////////////////////////////////////////////////////////////
// OUTPUT
//////////////////////////////////////////////////////////////////////////////////////
/*$user_state = ($logged ? "logout" : "login");
if ($cm->isXHR() && $frmAction)
{
	$cm->jsonAddResponse(array(
			"modules" => array(
				"security" => array(
					"action" => $user_state
				)
			)
		));

	if($sError)
		$cm->jsonAddResponse(array(
			"success" => false 
			, "modules" => array(
				"security" => array(
					"error" => _modsec_process("error", $sError)
				)
			)
		));
	else if($logged) 
		$cm->jsonAddResponse(array(
			"modules" => array(
				"security" => array(
					"message" => _modsec_process("logout", true, false)
				)
			)
			, "doredirects" => true
			, "url" => $ret_url			
		));
	
	_modsec_login_redirect($ret_url); 
} else {
	$cm->oPage->addContent(_modsec_process($user_state, $sError), null, $user_state);
}*/

$user_state = ($logged ? "logout" : "login");
$cm->oPage->addContent(_modsec_process($user_state, $sError), null, $user_state);
	
		
function _modsec_process($action, $sError = null, $logo = MOD_SEC_LOGO) {
	$cm = cm::getInstance();

	switch($action) {
		case "login":
			$template_file = "login.html";
			break;
		case "logout":
			$template_file = "logout.html";
			break;
		case "error":
			$res = _modsec_process_error($sError);
			break;
		default:
	
	}
	
	if($template_file) 
	{
		$framework_css = mod_sec_get_framework_css();
	
		$filename = null;
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/" . $template_file, $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/login/" . $template_file, $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/login/" . $template_file, $cm->oPage->theme);

		$tpl = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl->load_file(basename($filename), "main");

		$tpl->set_var("site_path", FF_SITE_PATH);
		$tpl->set_var("theme", $cm->oPage->theme);
		$tpl->set_var("domain", $_SERVER["HTTP_HOST"]);

		$cm->preloadApplets($tpl);
		$cm->parseApplets($tpl);

		$cm->modules["security"]["events"]->doEvent("onTplLoad", array(&$tpl));

		$tpl->set_var("ret_url",			$cm->oPage->ret_url);
		$tpl->set_var("encoded_ret_url",	rawurlencode($cm->oPage->ret_url));
		$tpl->set_var("encoded_this_url",	rawurlencode($cm->oPage->getRequestUri()));
		$tpl->set_var("query_string",		$_SERVER["QUERY_STRING"]);
		$tpl->set_var("path_info",			$_SERVER["PATH_INFO"]);
		$tpl->set_var("app_title",			ffCommon_specialchars(CM_LOCAL_APP_NAME));

		if($cm->oPage->layer == "empty" && ! $cm->isXHR()) {
			$framework_css["component"]["class"] = trim(str_replace("nopadding", "", $framework_css["component"]["class"]));
			$framework_css["component"]["grid"] = "row";
		}

		if(!$logo) {  
			$framework_css["login"]["def"]["col"] = array( 
								                    "xs" => 12
								                    , "sm" => 12
								                    , "md" => 12
								                    , "lg" => 12
								                ) ;
			unset($framework_css["login"]["def"]["push"]);
			$framework_css["logout"]["def"]["col"] = array( 
														"xs" => 12
														, "sm" => 12
														, "md" => 12
														, "lg" => 12
													) ;
			unset($framework_css["logout"]["def"]["push"]);
			$framework_css["inner-wrap"]["col"] = array( 
													"xs" => 12
													, "sm" => 12
													, "md" => 6
													, "lg" => 6 
												);
			$framework_css["inner-wrap"]["push"] = array( 
													"xs" => 0
													, "sm" => 0
													, "md" => 3
													, "lg" => 3 
												);
		}

		/**
		* Container Class
		*/
		
		$component_class["base"] = $framework_css["component"]["class"];
		if($framework_css["component"]["grid"]) {
		    if(is_array($framework_css["component"]["grid"]))
		        $component_class["grid"] = cm_getClassByFrameworkCss($framework_css["component"]["grid"], "col");
		    else {
		        $component_class["grid"] = cm_getClassByFrameworkCss("", $framework_css["component"]["grid"]);      
		    }
		}   

		
		if($action == "login")
			$component_class = _modsec_process_login($tpl, $sError, $component_class, $framework_css);
		elseif($action == "logout")
			$component_class = _modsec_process_logout($tpl, $sError, $component_class, $framework_css);

		if($logo) {
		    if(MOD_SEC_LOGO_PATH === false) {
		        $tpl->set_var("SectLogoImg" . $logo, "");
		    } else {
		        if(is_file(FF_DISK_PATH . MOD_SEC_LOGO_PATH))
		            $logo_url = MOD_SEC_LOGO_PATH;
		        elseif(is_file(FF_THEME_DISK_PATH . "/" . $cm->oPage->getTheme() . "/images/logo-login.png")) 
		            $logo_url = FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/images/logo-login.png";
		        elseif(is_file(FF_THEME_DISK_PATH . "/" . cm_getMainTheme() . "/images/logo-login.gif"))
		            $logo_url = FF_THEME_DIR . "/" . cm_getMainTheme() . "/images/logo-login.gif";

		        $tpl->set_var("logo_login", FF_SITE_PATH . $logo_url);
		        $tpl->parse("SectLogoImg" . $logo, false);
		    }
		    $tpl->set_var("logo_class", cm_getClassByDef($framework_css["logo"]));
		    $tpl->parse("SectLogo" . $logo, false);
		}

		$tpl->set_var("container_class", implode(" ", array_filter($component_class))); 
		$tpl->set_var("inner_wrap_class", cm_getClassByDef($framework_css["inner-wrap"]));
		
		$res =  $tpl->rpparse("main", false);
	}
	return $res;
}

function _modsec_process_login(&$tpl, $sError = null, $component_class = null, $framework_css = null) 
{
	$cm = cm::getInstance();
	$count_links = 0;
	if(!$framework_css)
		$framework_css = mod_sec_get_framework_css();

	$tiny_lang_code = strtolower(substr(FF_LOCALE, 0, 2));
	$mod_sec_recover = ($cm->router->getRuleById("mod_sec_recover_" . $tiny_lang_code)
	                        ? $cm->router->getRuleById("mod_sec_recover_" . $tiny_lang_code)
	                        : $cm->router->getRuleById("mod_sec_recover")
	                    );
	$mod_sec_recover_username = ($cm->router->getRuleById("mod_sec_recover_username_" . $tiny_lang_code) 
	                                ? $cm->router->getRuleById("mod_sec_recover_username_" . $tiny_lang_code)
	                                : $cm->router->getRuleById("mod_sec_recover_username")
	                            );
	$mod_sec_register = ($cm->router->getRuleById("mod_sec_register_" . $tiny_lang_code) 
	                        ? $cm->router->getRuleById("mod_sec_register_" . $tiny_lang_code)
	                        : $cm->router->getRuleById("mod_sec_register")
	                    );
	$mod_sec_social_url = (string)$cm->router->getRuleById("mod_sec_social")->reverse;	                    

	if(MOD_SEC_LOGIN_TITLE)
		$tpl->parse("SectLoginTitle", false);

	$tpl->set_var("sError", _modsec_process_error($sError, $framework_css));

	/**
	* Standard Login Parsing
	*/ 
	if(MOD_SEC_LOGIN_STANDARD) {
		/**
		* Login Actions
		*/
		$tpl->set_var("field_class", cm_getClassByDef($framework_css["login"]["standard"]["field"]));

		if(MOD_SEC_ENABLE_TOKEN)
		{
		    $tpl->set_var("stayconnect_class", cm_getClassByDef($framework_css["login"]["standard"]["stayconnect"]));
		    $tpl->parse("SectStayConnected", false);
		}

		if(MOD_SEC_USERNAME_RECOVER_USERNAME && $mod_sec_recover_username)
		{     
		    $tpl->set_var("recover_class", cm_getClassByDef($framework_css["login"]["standard"]["recover"]));
		    $tpl->set_var("recover", (string)$mod_sec_recover_username->reverse);
		    $tpl->parse("SectRecoverUsername", false);
		} 

		if(MOD_SEC_PASSWORD_RECOVER && $mod_sec_recover)
		{     
			$tpl->set_var("recover_class", cm_getClassByDef($framework_css["login"]["standard"]["recover"]));
		    $tpl->set_var("recover", (string)$mod_sec_recover->reverse);
			$tpl->parse("SectRecoverPassword", false);
		} 

		if(MOD_SEC_LOGIN_REGISTER_URL && $mod_sec_register)
		{
			$count_links++;
		    if(is_string(MOD_SEC_LOGIN_REGISTER_URL))
		        $register_link = MOD_SEC_LOGIN_REGISTER_URL;
		    elseif($mod_sec_register)
		        $register_link = (string)$mod_sec_register->reverse;

		    $tpl->set_var("register_class", cm_getClassByDef($framework_css["links"]["register"]));
		    $tpl->set_var("register", $register_link);
		    $tpl->parse("SectRegister", false);
		} 	

		/**
		* Login Label
		*/
		if(MOD_SEC_LOGIN_LABEL) {
		    $tpl->parse("SectDomainLabel", false);
		    $tpl->parse("SectUsernameLabel", false);
		    $tpl->parse("SectPasswordLabel", false);
		}

		/**
		* Login Field
		*/

		if (MOD_SEC_MULTIDOMAIN && MOD_SEC_LOGIN_DOMAIN)
		{
			$domain = $_POST["domain"];
			if ($_REQUEST["frmAction"] == "" && $domain == "")
				$domain = $_COOKIE["domain"];
			$tpl->set_var("domain", $domain);
			$tpl->parse("SectDomain", false);
			$tpl->set_var("focus_target", "domain");
		}
		else
		{
			$domain = null;
			$tpl->set_var("SectDomain", "");
			$tpl->set_var("focus_target", "username");
		}
	
		$tpl->set_var("username", ffCommon_specialchars($_POST["username"]));
		
		$tpl->set_var("row_class", cm_getClassByDef($framework_css["login"]["standard"]["record"]));
		$tpl->set_var("login_button_class", cm_getClassByDef($framework_css["actions"]["login"]));	
		$tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));
		$tpl->set_var("login_standard_class", cm_getClassByDef($framework_css["login"]["standard"]["def"]));
		$tpl->parse("SectStandardLogin", false);
	}

	/**
	* Social Login Parsing
	*/ 
	if (MOD_SEC_SOCIAL_GOOGLE)
	{
	    $tpl->set_var("social_class", cm_getClassByDef($framework_css["login"]["social"]["google"]));
	    $tpl->set_var("social_icon", cm_getClassByFrameworkCss("google", "icon-tag"));
	    $tpl->set_var("social_url_google", FF_SITE_PATH . $mod_sec_social_url . "/google");
	    $tpl->parse("SectSocialLoginGoogle" . ucfirst(MOD_SEC_SOCIAL_POS), false);
	    $tpl->parse("SectSocialLogoutGoogle", false);
	}

	if (MOD_SEC_SOCIAL_FACEBOOK)
	{
	    $tpl->set_var("social_class", cm_getClassByDef($framework_css["login"]["social"]["facebook"]));
	    $tpl->set_var("social_icon", cm_getClassByFrameworkCss("facebook", "icon-tag"));
	    $tpl->set_var("social_url_facebook", FF_SITE_PATH . $mod_sec_social_url . "/facebook");
	    $tpl->parse("SectSocialLoginFacebook" . ucfirst(MOD_SEC_SOCIAL_POS), false);
	    $tpl->parse("SectSocialLogoutFacebook", false);
	}

	if(MOD_SEC_SOCIAL_JANRAIN)
	{
	    $tpl->set_var("social_class", cm_getClassByDef($framework_css["login"]["social"]["janrain"]));
	    $tpl->set_var("janrain_appname", ffCommon_url_rewrite(MOD_SEC_SOCIAL_JANRAIN_APPNAME));
	   
	    $tpl->parse("SectJanRainLogin", false);
	    $tpl->parse("SectJanrainJS", false);
	} 

	if(MOD_SEC_SOCIAL_GOOGLE || MOD_SEC_SOCIAL_FACEBOOK || MOD_SEC_SOCIAL_JANRAIN) { 
		$component_class["social"] = "social";

		if(MOD_SEC_LOGIN_STANDARD)
			$framework_css["login"]["social"]["def"]["class"] .= " " . MOD_SEC_SOCIAL_POS . "-standard-login"; 
			
	    $tpl->set_var("login_social_class", cm_getClassByDef($framework_css["login"]["social"]["def"]));
		$tpl->parse("SectSocialLogin" . ucfirst(MOD_SEC_SOCIAL_POS), false);
	}
	
	if (MOD_SEC_LOGIN_BACK_URL)
	{
	    $count_links++;
	    $tpl->set_var("back_class", cm_getClassByDef($framework_css["links"]["back"]));
	    $tpl->set_var("back_url", FF_SITE_PATH . "/"); 
	    $tpl->parse("SectLoginBack", false);
	}    

	if($count_links) {
		$tpl->set_var("link_class", cm_getClassByDef($framework_css["links"]["def"]));
		$tpl->parse("SectLoginLinks", false);
	}		
	
	$tpl->set_var("login_class", cm_getClassByDef($framework_css["login"]["def"]));  
	$tpl->parse("SectLogin", false);

	return $component_class;
}


function _modsec_process_logout(&$tpl, $skip_action = false, $component_class = null, $framework_css = null) 
{
	$cm = cm::getInstance();
	$count_links = 0;	
	if(!$framework_css)
		$framework_css = mod_sec_get_framework_css();

	if (MOD_SEC_MULTIDOMAIN)
		$ID_domain = mod_security_get_domain();	

	/**
	* Logout Title
	*/
	if(MOD_SEC_LOGOUT_TITLE) {
		if($skip_action)
			$tpl->set_var("logout_title", ffTemplate::_get_word_by_code("logout_noaction_title"));
		else
			$tpl->set_var("logout_title", ffTemplate::_get_word_by_code("logout_title"));
	    $tpl->parse("SectLogoutTitle", false);
	}

	if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && $ID_domain)
		$db = mod_security_get_db_by_domain($ID_domain);
	else
		$db = mod_security_get_main_db();

	    
	if(MOD_SEC_ENABLE_TOKEN && !$skip_action && (MOD_SEC_SOCIAL_GOOGLE || MOD_SEC_SOCIAL_FACEBOOK || MOD_SEC_SOCIAL_JANRAIN))
	{
		$options = mod_security_get_settings($cm->path_info);
		// CHECK VALID TOKENS
		$valid_token = false;
		$sSQL = "SELECT
					*
				FROM
					`" . $options["table_token"] . "`
				WHERE `" . $options["table_token"] . "`.`type` = 'live'
					AND `" . $options["table_token"] . "`.`ID_user` = " . $db->toSql(get_session("UserNID"), "Number");
		if (MOD_SEC_MULTIDOMAIN && !MOD_SEC_MULTIDOMAIN_EXTERNAL_DB)
			$sSQL .= " AND `ID_domain` = " . $db->toSql($ID_domain);
		$db->query($sSQL);
		if ($db->nextRecord())
		{
			do
			{
				$at_type = $db->getField("type", "Text", true);
				$rc = mod_security_accesstoken_check($at_type, $db->getField("token", "Text", true), get_session("UserNID"), $ID_domain, $cm->path_info);
				if ($rc)
					$tpl->parse("SectSocialLogout_" . $at_type, true);
				$valid_token |= $rc;
			} while ($db->nextRecord());
		}
		
		if ($valid_token)
			$tpl->parse("SectSocialLogout", false);
	}

	if(MOD_SEC_USER_AVATAR) {
		if(MOD_SEC_GROUPS) {
		    $user_permission = get_session("user_permission");
			$avatar = $user_permission["avatar"];
		} else {
		    $avatar = mod_security_getUserInfo(MOD_SEC_USER_AVATAR, null, $db)->getValue();
		}	

		//die(mod_sec_get_avatar($avatar, MOD_SEC_USER_AVATAR_MODE));
		$tpl->set_var("avatar_class", cm_getClassByDef($framework_css["logout"]["account"]["avatar"]));
		$tpl->set_var("avatar", mod_sec_get_avatar($avatar, MOD_SEC_USER_AVATAR_MODE));
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
		$tpl->set_var("username", $username);
		$tpl->parse("SectUsername", false);
	}
	if($email) {
		$tpl->set_var("email", $email);
		$tpl->parse("SectEmail", false);
	}
	
	$tpl->set_var("account_class", cm_getClassByDef($framework_css["logout"]["account"]["def"]));
	if(!$skip_action)
	{
		$tpl->set_var("logout_button_class", cm_getClassByDef($framework_css["actions"]["logout"]));	
		$tpl->set_var("actions_class", cm_getClassByDef($framework_css["actions"]["def"]));	
		$tpl->parse("SectStandardLogout", false); 
	}
	
	if (MOD_SEC_LOGIN_BACK_URL)
	{
	    $count_links++;
	    $tpl->set_var("back_class", cm_getClassByDef($framework_css["links"]["back"]));
	    $tpl->set_var("back_url", $cm->oPage->ret_url); 
	    $tpl->parse("SectLogoutBack", false);
	}    

	if($count_links) {
		$tpl->set_var("link_class", cm_getClassByDef($framework_css["links"]["def"]));
		$tpl->parse("SectLogoutLinks", false);
	}

	$tpl->set_var("logout_class", cm_getClassByDef($framework_css["logout"]["def"]));
	$tpl->parse("SectLogout", false); 	
	
	return $component_class;
}

function _modsec_process_error($sError, $framework_css = null) {
	if($sError) {
		if(!$framework_css)
			$framework_css = mod_sec_get_framework_css();
	
		$strError = '<div class="' . cm_getClassByDef($framework_css["error"]) . '">' . $sError . '</div>';
	}
	return $strError;
}