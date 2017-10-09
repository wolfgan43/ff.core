<?php
$options = mod_security_get_settings($cm->path_info);

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ModSecUtenti";
$oRecord->title = "Utenti";
$oRecord->src_table = $options["table_name"];
$oRecord->addEvent("on_done_action", "ModSecUtenti_on_done_action");
$oRecord->addEvent("on_do_action", "ModSecUtenti_on_do_action");
$oRecord->insert_additional_fields["created"] = new ffData(date("d/m/Y H:i:s"), "DateTime", "ITA");
$oRecord->update_additional_fields["modified"] = new ffData(date("d/m/Y H:i:s"), "DateTime", "ITA");

$res = $cm->modules["security"]["events"]->doEvent("get_domain");
$rc_domain = end($res);
if ($rc_domain)
{
	$oRecord->insert_additional_fields["ID_domains"]= new ffData($rc_domain, "Number");
	$oRecord->additional_key_fields["ID_domains"] = new ffData($rc_domain, "Number");
}
else if (MOD_SEC_MULTIDOMAIN && !MOD_SEC_MULTIDOMAIN_EXTERNAL_DB)
{
	$oRecord->insert_additional_fields["ID_domains"]= new ffData(mod_security_get_domain(), "Number");
	$oRecord->additional_key_fields["ID_domains"] = new ffData(mod_security_get_domain(), "Number");
}

if (get_session("UserLevel") == 1)
{
	$oRecord->allow_delete = false;
	$oRecord->allow_insert = false;
}

if (isset($_REQUEST["keys"]["ID"]))
{
	$ret = $oRecord->db[0]->lookup($options["table_name"], "ID", $_REQUEST["keys"]["ID"], null, array(
		"special" => "Text"
		, "level" => "Number"
	), null, true);
	
	if (strlen($ret["special"]))
		$oRecord->allow_delete = false;
	
	if (get_session("UserNID") == intval($_REQUEST["keys"]["ID"]) && !MOD_SECURITY_USERS_DELETE_SELF)
		$oRecord->allow_delete = false;
	else {
		if (get_session("UserLevel") < 3 && !MOD_SECURITY_USERS_SHOW_LEVELS_ALL)
		{
			if (MOD_SECURITY_USERS_SHOW_LEVELS_ACL && strpos(MOD_SECURITY_USERS_SHOW_LEVELS_ACL, $ret["level"]) === false)
			{
				access_denied();
			}
			else if (!MOD_SECURITY_USERS_SHOW_SAME_LEVEL && $ret["level"] >= get_session("UserLevel"))
			{
				access_denied();
			}
		}

		if (get_session("UserLevel") < 3 && (
			($ret["level"] > get_session("UserLevel"))
			|| ($ret["level"] == get_session("UserLevel") && !MOD_SECURITY_USERS_MODIFY_SAME_LEVEL)
		))
		{
			$oRecord->allow_delete = false;
			$oRecord->allow_update = false;
			$oRecord->display_values = true;
		}
	}
}

$populate_edit_SQL = "SELECT
							" . $options["table_name"] . ".*
	";

if (isset($cm->modules["security"]["fields"]) && count($cm->modules["security"]["fields"]))
{
	foreach ($cm->modules["security"]["fields"] as $key => $value)
	{
		if (mod_security_is_default_field($key))
			continue;
		
		$populate_edit_SQL .= ", (SELECT 
											" . $options["table_dett_name"] . ".value
										FROM
											" . $options["table_dett_name"] . "
										WHERE
											" . $options["table_dett_name"] . ".ID_users = " . $options["table_name"] . ".ID
											AND " . $options["table_dett_name"] . ".field = " . $oRecord->db[0]->toSql($key) . "
								) AS " . $key . "
			";
			
		$populate_insert_SQL .= ", '' AS " . $key;
	}
	reset($cm->modules["security"]["fields"]);
}
$populate_edit_SQL .= "FROM
							" . $options["table_name"] . "
						WHERE
							" . $options["table_name"] . ".ID = " . $oRecord->db[0]->toSql($_REQUEST["keys"]["ID"]) . "
	";

$oRecord->populate_edit_SQL = $populate_edit_SQL;
$oRecord->auto_populate_edit = true;
$oRecord->del_action = "multi_delete";
$oRecord->del_multi_delete = array(
									"DELETE FROM " . $options["table_dett_name"] . " WHERE ID_users = [ID_VALUE]"
								);

if (cm_getMainTheme() == "restricted" || $cm->oPage->getTheme() == "restricted")
{
	$oRecord->addContent(null, true, "account");
	$oRecord->addContent(null, true, "userinfo");
	$oRecord->addContent(null, true, "preferences");

	$account = "account";
	$userinfo = "userinfo";
	$preferences = "preferences";

	$oRecord->groups["account"]["title"] = "Account";
	$oRecord->groups["userinfo"]["title"] = "Dati Personali";
	$oRecord->groups["preferences"]["title"] = "Preferenze";
}
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

//if (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username")
if(strlen(MOD_SEC_USER_AVATAR))
{
	if (!mod_security_is_defined_field("avatar"))
	{
	    $uid = $_REQUEST["keys"]["ID"];

	    $oField = ffField::factory($cm->oPage);
	    $oField->id = "avatar";
	    $oField->label = "Avatar";
	    $oField->base_type = "Text";
	    $oField->extended_type = "File";
	    $oField->file_storing_path = FF_DISK_PATH . FF_UPDIR . "/users/" . $uid;
	    $oField->file_temp_path = FF_DISK_PATH . FF_UPDIR . "/users";
	    $oField->file_max_size = 5000000;
	    $oField->file_show_filename = true; 
	    $oField->file_full_path = true;
	    $oField->file_check_exist = false;
	    $oField->file_normalize = true;
	    $oField->file_show_preview = true;
	    $oField->uploadify_model_thumb = "thumb";
	    $oField->file_saved_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/[_FILENAME_]";
	    $oField->file_saved_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/" . $oField->uploadify_model_thumb . "/[_FILENAME_]";
	    $oField->control_type = "file";
	    $oField->file_show_delete = true;
	    $oField->widget = "uploadify"; 
	    $oRecord->addContent($oField, $account);
	}
}
if (!mod_security_is_defined_field("username"))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "username";
	$oField->label = "Username";
	$oField->required = true;
	$oRecord->addContent($oField, $account);
}

if (!mod_security_is_defined_field("email"))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "email";
	$oField->label = "E-Mail";
	$oField->required = true;
	$oField->addValidator("email");
	$oRecord->addContent($oField, $account);
}

if (!mod_security_is_defined_field("password") && !$oRecord->display_values)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "password";
	$oField->label = "Password";
	$oField->extended_type = "Password";
	switch (MOD_SEC_PASS_FUNC)
	{
		case "MD5":
			$oField->crypt_method = "MD5";
			break;

		default:
			$oField->crypt_method = "mysql_password";
			break;
	}
	$oRecord->addContent($oField, $account);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "confpassword";
	$oField->label = "Conferma Password";
	$oField->extended_type = "Password";
	$oField->compare = "password";
	$oRecord->addContent($oField, $account);
}

if (!mod_security_is_defined_field("status") && mod_sec_check_acl(MOD_SEC_ACL_STATUS))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "status";
	$oField->label = "Stato";
	$oField->extended_type = "Selection";
	$oField->multi_pairs = array( 
									  array( new ffData("1"),  new ffData("Attivo"))
									, array( new ffData("0"),  new ffData("Sospeso"))
								);
	$oField->default_value = new ffData("1");
	if (get_session("UserLevel") == 1)
	{
		$oField->store_in_db = false;
		$oField->control_type = "label";
	}
	else
		$oField->required = true;
	$oRecord->addContent($oField, $account);
}

if (!mod_security_is_defined_field("level") && mod_sec_check_acl(MOD_SEC_ACL_LEVEL))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "level";
	$oField->label = "Livello";
	$oField->extended_type = "Selection";
	if (get_session("UserLevel") == 1)
	{
		$oField->multi_pairs[] = array( new ffData("1"),  new ffData("Utente"));
		$oField->multi_pairs[] = array( new ffData("2"),  new ffData("Admin"));
		$oField->multi_pairs[] = array( new ffData("3"),  new ffData("Super Admin"));
		$oField->store_in_db = false;
		$oField->control_type = "label";
	}
	else
	{
		$oField->required = true;
		$oField->multi_pairs[] = array( new ffData("1"),  new ffData("Utente"));
		$oField->multi_pairs[] = array( new ffData("2"),  new ffData("Admin"));
		if (get_session("UserLevel") == 3)
			$oField->multi_pairs[] = array( new ffData("3"),  new ffData("Super Admin"));
	}
	$oRecord->addContent($oField, $account);
}

if (MOD_SEC_PROFILING && !mod_security_is_defined_field("profile") && mod_sec_check_acl(MOD_SEC_ACL_PROFILE) && !MOD_SEC_PROFILING_MULTI)
{
	mod_sec_profiling_update_profiles();
	
	$oField = ffField::factory($cm->oPage);
	$oField->id = "profile";
	$oField->label = "Profilo";
	$oField->extended_type = "Selection";
	$oField->source_DS = "mod_sec_user_profile";
	$oField->multi_select_one_label = "Nessuno";
	$oRecord->addContent($oField, $account);
}

if (!mod_security_is_defined_field("expiration") && mod_sec_check_acl(MOD_SEC_ACL_EXPIRATION))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "expiration";
	$oField->label = "Scadenza";
	$oField->base_type = "Date";
	if (get_session("UserLevel") == 1)
	{
		$oField->store_in_db = false;
		$oField->control_type = "label";
	}
	else
		$oField->widget = "datepicker";
	$oRecord->addContent($oField, $account);
}

mod_security_add_custom_fields($oRecord);

$cm->oPage->addContent($oRecord);

if (MOD_SEC_PROFILING && !mod_security_is_defined_field("profile") && mod_sec_check_acl(MOD_SEC_ACL_PROFILE) && MOD_SEC_PROFILING_MULTI)
{
	$oRecord->addContent(null, true, "profiles");
	$oRecord->groups["profiles"]["title"] = "Profilazione";

	$detail = ffDetails::factory($cm->oPage, null, null, array("name" => "ffDetails_horiz"));
	$detail->id = "rel-profiles";
	$detail->src_table = "cm_mod_security_rel_profiles_users";
	$detail->order_default = "order";
	$detail->fields_relationship = array(
		"ID_user" => "ID"
	);
	$detail->display_new = false;
	$detail->display_delete = false;
	$detail->populate_edit_SQL = "SELECT
										cm_mod_security_rel_profiles_users.ID AS ID
										, cm_mod_security_profiles.ID AS ID_profile
										, cm_mod_security_rel_profiles_users.enabled AS enabled
										, cm_mod_security_profiles.`order` AS `order`
									FROM
										cm_mod_security_profiles
										LEFT JOIN cm_mod_security_rel_profiles_users ON
											cm_mod_security_rel_profiles_users.ID_profile = cm_mod_security_profiles.ID
											AND cm_mod_security_rel_profiles_users.ID_user = [ID_FATHER]
									WHERE
										cm_mod_security_profiles.enabled = '1' 
										AND ( cm_mod_security_profiles.acl = '' OR " . get_session("UserLevel") . " IN(cm_mod_security_profiles.acl) )
									ORDER BY
										cm_mod_security_profiles.`order`
		";
	$detail->auto_populate_edit = true;
	
	$detail->populate_insert_SQL = "SELECT
										cm_mod_security_profiles.ID AS ID_profile
										, cm_mod_security_profiles.`order`
									FROM
										cm_mod_security_profiles
									WHERE
										cm_mod_security_profiles.enabled = '1' 
										AND ( cm_mod_security_profiles.acl = '' OR " . get_session("UserLevel") . " IN(cm_mod_security_profiles.acl) )
									ORDER BY
										cm_mod_security_profiles.`order`
		";
	$detail->auto_populate_insert = true;
	
	$field = ffField::factory($cm->oPage);
	$field->id = "ID_detail";
	$field->data_source = "ID";
	$field->base_type = "Number";
	$detail->addKeyField($field);
	
	$field = ffField::factory($cm->oPage);
	$field->id = "ID_profile";
	$field->base_type = "Number";
	$field->label = "Profilo";
	$field->extended_type = "Selection";
	$field->source_SQL = "SELECT ID, nome FROM `cm_mod_security_profiles`";
	$field->control_type = "label";
	$detail->addContent($field);
	
	$field = ffField::factory($cm->oPage);
	$field->id = "enabled";
	$field->label = "Abilitato";
	$field->extended_type = "Boolean";
	$field->checked_value = new ffData('1');
	$field->unchecked_value = new ffData('');
	$detail->addContent($field);
	
	$cm->oPage->addContent($detail);
	$oRecord->addContent($detail, "profiles");
}

function ModSecUtenti_on_done_action($oRecord, $frmAction)
{
	$cm = cm::getInstance();

	$options = mod_security_get_settings($cm->path_info);
	$ID = $oRecord->key_fields["ID"]->value;
	$db = ffDb_Sql::factory();
	
	if (isset($cm->modules["security"]["fields"]) && count($cm->modules["security"]["fields"]))
	{
		switch ($frmAction)
		{
			case "insert":
				foreach ($cm->modules["security"]["fields"] as $key => $value)
				{
					if (mod_security_is_default_field($key))
						continue;
					
					$sSQL = "INSERT INTO
									" . $options["table_dett_name"] . " (ID_users, field, value)
								VALUES
									(
									  " . $db->toSql($ID) . "
									, " . $db->toSql($key) . "
									, " . $db->toSql($oRecord->form_fields[$key]->value) . "
									)
							";
					$db->execute($sSQL);
				}
				break;
				
			case "update":
				foreach ($cm->modules["security"]["fields"] as $key => $value)
				{
					if (mod_security_is_default_field($key))
						continue;
					
                                        $sSQL = "SELECT ID
                                                    FROM " . $options["table_dett_name"] . "
                                                    WHERE ID_users = " . $db->toSql($ID) . "
                                                        AND field = " . $db->toSql($key);
                                        $db->query($sSQL);
                                        if($db->nextRecord()) {
                                            $sSQL = "UPDATE 
                                                                            " . $options["table_dett_name"] . "
                                                                    SET
                                                                            value = " . $db->toSql($oRecord->form_fields[$key]->value) . "
                                                                    WHERE
                                                                            ID_users = " . $db->toSql($ID) . "
                                                                            AND field = " . $db->toSql($key) . "
                                                            ";
                                            $db->execute($sSQL);
                                        } else {
                                            $sSQL = "INSERT INTO
                                                                            " . $options["table_dett_name"] . " (ID_users, field, value)
                                                                    VALUES
                                                                            (
                                                                              " . $db->toSql($ID) . "
                                                                            , " . $db->toSql($key) . "
                                                                            , " . $db->toSql($oRecord->form_fields[$key]->value) . "
                                                                            )
                                                            ";
                                            $db->execute($sSQL);
					}
				}
				break;
			case "confirmdelete":
				cm_purge_dir(FF_DISK_PATH . "/uploads/users/" . $component->key_fields["ID"]->getValue(), "/users/" . $component->key_fields["ID"]->getValue());
				break;
		}
	}
	
	return FALSE;
}

function ModSecUtenti_on_do_action($oRecord, $frmAction)
{
	$cm = cm::getInstance();

	switch($frmAction)
	{
		case "insert":
			$db = ffDB_Sql::factory();
			$sSQL = "SELECT
							*
						FROM
							`" . $oRecord->src_table . "`
						WHERE
							1
				";
			if (MOD_SEC_EXCLUDE_SQL)
				$sSQL .= " AND `" . $oRecord->src_table . "`.ID " . MOD_SEC_EXCLUDE_SQL;

			if (
					(MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username")
					&&
					(MOD_SECURITY_REGISTER_SHOWUSERID == "both" || MOD_SECURITY_REGISTER_SHOWUSERID == "username")
				)
			{
				$tmp_SQL = $sSQL . " AND `username` = " . $db->toSql($oRecord->form_fields["username"]->value);
				$db->query($tmp_SQL);
				if ($db->nextRecord())
				{
					$oRecord->strError = "L'username desiderato Ã¨ giÃ  in utilizzo";
					return true;
				}
			}

			if (MOD_SECURITY_LOGON_USERID == "email" || MOD_SECURITY_REGISTER_SHOWUSERID == "both" || MOD_SECURITY_REGISTER_SHOWUSERID == "email")
			{
				$tmp_SQL = $sSQL . " AND `email` = " . $db->toSql($oRecord->form_fields["email"]->value);
				$db->query($tmp_SQL);
				if ($db->nextRecord())
				{
					$oRecord->strError = "L'E-Mail inserita Ã¨ giÃ  in utilizzo";
					return true;
				}
			}

/*			if (MOD_SECURITY_LOGON_USERID == "email" || MOD_SECURITY_REGISTER_SHOWUSERID == "email")
				$oRecord->additional_fields["username"] = $oRecord->form_fields["email"]->value;
*/
			break;
	}
}

//Procedura per cancellare i file/cartelle
function cm_purge_dir($absolute_path, $relative_path, $exclude_dir = false) {
	if (file_exists($absolute_path) && is_dir($absolute_path)) {
		if ($handle = opendir($absolute_path)) {
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					if (is_dir($absolute_path . "/" . $file)) {
						cm_purge_dir($absolute_path . "/" . $file, $relative_path . "/" . $file);
					} else {
                        if(is_file($absolute_path . "/" . $file))
						    unlink($absolute_path . "/" . $file);
					}
				}
			}
			if(!$exclude_dir)
				rmdir ($absolute_path);
		}
	} else {
        if(file_exists($absolute_path) && is_file($absolute_path))
		    @unlink($absolute_path);

	}
}
