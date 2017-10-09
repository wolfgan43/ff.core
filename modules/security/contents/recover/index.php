<?php
$options = mod_security_get_settings($cm->path_info);

if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && $ID_domain)
	$db = mod_security_get_db_by_domain($ID_domain);
else
	$db = mod_security_get_main_db();

$cm->oPage->form_method = "post";

$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/recover.html", $cm->oPage->theme, false);
if ($filename === null)
	$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/contents/recover/recover.html", $cm->oPage->theme, false);
if ($filename === null)
	$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/recover/recover.html", $cm->oPage->theme);

$tpl = ffTemplate::factory(ffCommon_dirname($filename));
$tpl->load_file("recover.html", "main");
$tpl->set_var("site_path", FF_SITE_PATH);
$tpl->set_var("theme", $cm->oPage->theme);

$cm->preloadApplets($tpl);
$cm->parseApplets($tpl);

$frmAction 	= strtolower($_REQUEST["frmAction"]);
$username_post = $_REQUEST["username"];
$email_post = $_REQUEST["email"];

$ret_url 	= $_REQUEST["ret_url"];
if (!strlen($ret_url))
	$ret_url = $cm->oPage->site_path . "/";

$cm->oPage->ret_url = $ret_url;
$tpl->set_var("ret_url", $ret_url);
$tpl->set_var("encoded_ret_url", rawurlencode($ret_url));
$tpl->set_var("encoded_this_url", rawurlencode($_SERVER["REQUEST_URI"]));
$tpl->set_var("query_string", $_SERVER["QUERY_STRING"]);

$tpl->set_var("username", $username_post);

$cm->oPage->addContent($tpl, null, "recover");

$frmAction = $_REQUEST["frmAction"];

if($frmAction == "recover" && (strlen($username_post) || strlen($email_post)))
{
	if (!strlen($username_post) && !strlen($email_post))
	{
		$sError = "Immettere almeno uno dei due campi";
		$tpl->set_var("sError", $sError);
		$tpl->parse("SectError", false);
		return;
	}
	
	$db2 = ffDB_Sql::factory();
	$sSQL2 = "SELECT
				" . $options["table_name"] . ".ID
				, " . $options["table_name"] . ".username
				, " . $options["table_name"] . ".email
				, " . $options["table_name"] . ".password_generated_at AS time
			FROM
				" . $options["table_name"] . "
			WHERE " . $options["table_name"] . ".status = '1'
								AND (";
				if (strlen($username_post) && (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username"))
					 $sSQL2 .= $options["table_name"] . ".username = " . $db2->toSql($username_post, "Text");
				if (strlen($username_post) && MOD_SECURITY_LOGON_USERID == "both" && strlen($email_post))
					 $sSQL2 .= " OR ";
				if (strlen($email_post)  && (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "email"))
					 $sSQL2 .= $options["table_name"] . ".email = " . $db2->toSql($email_post, "Text");
				$sSQL2 .= ")";
	if (MOD_SEC_EXCLUDE_SQL)
		$sSQL2 .= " AND " . $options["table_name"] . ".ID " . MOD_SEC_EXCLUDE_SQL;
		
	$sSQL2 .= " ORDER BY ID DESC";

	$db2->query($sSQL2);

	if (!$db2->nextRecord())
		return;
	
	if ($db2->numRows() > 1)
	{
		$sError = "&Egrave; stato riscontrato un problema con i dati dell'utenza selezionata, contattare l'amministratore del sito";
		$tpl->set_var("sError", $sError);
		$tpl->parse("SectError", false);
		return;
	}

	$username = $db2->getField("username")->getValue();
	$email = $db2->getField("email")->getValue();
	$time_elapsed = time() - $db2->getField("time", "Date")->getValue("Timestamp");
	
	if ($time_elapsed > MOD_SEC_PASSWORD_RECOVER_INTERVAL)
	{
		$password = mod_sec_createRandomPassword();
		
		$sSQL = "UPDATE
						" . $options["table_name"] . "
					SET
						temp_password = PASSWORD(" . $db->toSql($password) . ")
						, password_generated_at = " . $db->toSql(date("Y-m-d H:i:s")) . "
						, password_used = 0
					WHERE
						ID = " . $db->toSql($db2->getField("ID"));
		$db->execute($sSQL);

		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/mail_recover.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/email/mail_recover.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/email/mail_recover.html", $cm->oPage->theme);

		$tpl_html = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl_html->load_file("mail_recover.html", "main");
		$tpl_html->set_var("site_path", FF_SITE_PATH);
		$tpl_html->set_var("theme", $cm->oPage->theme);

		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/mail_recover.txt", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/security/email/mail_recover.txt", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/email/mail_recover.txt", $cm->oPage->theme);

		$tpl_txt = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl_txt->load_file("mail_recover.txt", "main");
		$tpl_txt->set_var("site_path", FF_SITE_PATH);
		$tpl_txt->set_var("theme", $cm->oPage->theme);

		// ELEMENTI DEI TEMPLATE
		if(strlen($_REQUEST["ret_url"]))
			$url = $_REQUEST["ret_url"];
		else
			$url = mod_security_get_login_path();
		
		$tpl_html->set_var("username", ffCommon_specialchars($username));
		$tpl_txt->set_var("username", $username);
		$tpl_html->set_var("password", ffCommon_specialchars($password));
		$tpl_txt->set_var("password", $password);
		$tpl_html->set_var("minuti", ffCommon_specialchars(MOD_SEC_PASSWORD_RECOVER_INTERVAL / 60));
		$tpl_txt->set_var("minuti", MOD_SEC_PASSWORD_RECOVER_INTERVAL / 60);
		$tpl_html->set_var("login_url", "http" . ($_SERVER["HTTPS"] ? "s": "") . "://" . $_SERVER["HTTP_HOST"] . $url);
		$tpl_txt->set_var("login_url", "http" . ($_SERVER["HTTPS"] ? "s": "") . "://" . $_SERVER["HTTP_HOST"] . $url);
		
		// INVIO MAIL
        $enable_sendmail = true;
        if(function_exists("check_function")) {
            if(!check_function("class.phpmailer"))
                $enable_sendmail = false;
        }
        if($enable_sendmail) {
		    $mail = new phpMailer();
		    $mail->SetLanguage("it", FF_DISK_PATH . "/library/phpmailer/language/");
		    $mail->CharSet = "utf-8";
		    
		    if (SECURITY_REGISTER_EMAIL_ENABLE_SMTP)
		    {
			    $mail->IsSMTP();
			    $mail->SMTPSecure	= SECURITY_REGISTER_EMAIL_SMTP_SECURITY;
			    $mail->SMTPAuth     = true;
			    $mail->Host 		= SECURITY_REGISTER_EMAIL_HOST;
			    $mail->Username     = SECURITY_REGISTER_EMAIL_USER;
			    $mail->Password     = SECURITY_REGISTER_EMAIL_PASS;
		    }
		    else
			    $mail->IsMail();

		    $mail->IsHTML(true);
			    
		    $res = $cm->modules["security"]["events"]->doEvent("on_mailrecover_tpl_processed", array(&$cm->modules["security"], &$tpl_txt, &$tpl_html));
		    $mail->Body    = $tpl_html->rpparse("main", false);
		    $mail->AltBody = $tpl_txt->rpparse("main", false);

		    $mail->From     = SECURITY_REGISTER_EMAIL_FROM;
		    $mail->FromName = "Password Reminder";

		    $mail->Subject = "Nuova Password";

		    $mail->AddAddress($email);
		    $mail->Send();
        }
		ffRedirect($url);
	}
	else
	{
		$remaining_time = floor((MOD_SEC_PASSWORD_RECOVER_INTERVAL - $time_elapsed) / 60);

		$sError = "Devi attendere " . $remaining_time . " minuti prima di chiedere una nuova password";
		$tpl->set_var("sError", $sError);
		$tpl->parse("SectError", false);
	}
}	