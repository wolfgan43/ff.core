<?php
if(!$cm->isXHR()) {
	//$cm->oPage->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");

	$filename = cm_moduleCascadeFindTemplateByPath("security", "/javascript/ff.modules.security.js", $cm->oPage->theme);
	$ret = cm_moduleGetCascadeAttrs($filename);
	$cm->oPage->tplAddJs("ff.modules.security", $filename, $ret["path"]);
	//$cm->oPage->tplAddJs("ff.modules.security", "", cm_getModulesExternalPath() . "/security/restricted/javascript/ff.modules.security.js"); // useful for caching purpose

	if (MOD_SEC_CSS_PATH !== false && isset($cm->router->matched_rules["mod_sec_login"])) {
		$css_name = "ff.modules.security.css";
		if (MOD_SEC_CSS_PATH)
			$filename = MOD_SEC_CSS_PATH;
		else
			$filename = cm_moduleCascadeFindTemplateByPath("security", "/css/" . $css_name, $cm->oPage->theme);

		$ret = cm_moduleGetCascadeAttrs($filename);
		$cm->oPage->tplAddCSS($css_name, $filename, $ret["path"]);
		//$cm->oPage->tplAddCSS("modules.security", "", cm_getModulesExternalPath() . "/security/restricted/css/ff.modules.security.css"); // useful for caching purpose
	}

	$options = mod_security_get_settings($cm->path_info);

	if (isset($options["session_name"]))
		session_name($options["session_name"]);

	$filename = cm_moduleCascadeFindTemplateByPath("security", "/javascript/init.js", $cm->oPage->theme);
	$tpl = ffTemplate::factory(ffCommon_dirname($filename));
	$tpl->load_file(basename($filename), "main");

	$mod_sec_login = $cm->router->getRuleById("mod_sec_login");
	if ($mod_sec_login) {
		$tpl->set_var("login_path", (string)$mod_sec_login->reverse);
		$tpl->parse("Sect_service_login", false);
	}

	$mod_sec_check_session = $cm->router->getRuleById("mod_sec_check_session");
	if ($mod_sec_check_session) {
		$tpl->set_var("check_session_path", (string)$mod_sec_check_session->reverse);
		$tpl->parse("Sect_service_check_session", false);
	}

	if (MOD_SEC_OAUTH2_SERVER) {
		$filename = cm_moduleCascadeFindTemplateByPath("security", "/javascript/oauth2.js", $cm->oPage->theme);
		$ret = cm_moduleGetCascadeAttrs($filename);
		$cm->oPage->tplAddJs("ff.modules.security.oauth2", $filename, $ret["path"]);

		$mod_sec_oauth2_service = $cm->router->getRuleById("mod_sec_oauth2_service");
		if ($mod_sec_oauth2_service) {
			$tpl->set_var("oauth2_path", (string)$mod_sec_oauth2_service->reverse);
			$tpl->parse("Sect_service_oauth2", false);
		}
	}

	$tpl->set_var("session_name", session_name());

	$cm->oPage->tplAddJs("ff.modules.security.init", null, null, false, false, $tpl->rpparse("main", false));
}