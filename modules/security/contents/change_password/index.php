<?php
mod_security_check_session(false);

$_REQUEST["keys"]["ID"] = get_session("UserNID");

$globals = ffGlobals::getInstance("mod_security");
$globals->options = mod_security_get_settings($cm->path_info);

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ModSecChangePassword";
$oRecord->title = "Resetta Password";
$oRecord->src_table = $globals->options["table_name"];
$oRecord->addEvent("on_done_action", "ModSecChangePassword_on_done_action");
$oRecord->allow_delete = false;
$oRecord->allow_insert = false;
$oRecord->widget_discl_enable = false;
$oRecord->buttons_options["cancel"]["display"] = false;

if (MOD_SEC_MULTIDOMAIN && !MOD_SEC_MULTIDOMAIN_EXTERNAL_DB)
{
	$oRecord->insert_additional_fields["ID_domains"]= new ffData(mod_security_get_domain(), "Number");
	$oRecord->additional_key_fields["ID_domains"] = new ffData(mod_security_get_domain(), "Number");
}

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "password";
$oField->label = "Nuova Password";
$oField->required = true;
$oField->extended_type = "Password";
$oRecord->addContent($oField);

switch (MOD_SEC_PASS_FUNC)
{
	case "MD5":
		$oField->crypt_method = "MD5";
		break;

	default:
		$oField->crypt_method = "mysql_password";
		break;
}

$oField = ffField::factory($cm->oPage);
$oField->id = "confirmnewpassword";
$oField->label = "Conferma Password";
$oField->required = true;
$oField->extended_type = "Password";
$oField->compare = "password";
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);

function ModSecChangePassword_on_done_action($oRecord, $frmAction)
{
	$globals = ffGlobals::getInstance("mod_security");

	switch ($frmAction)
	{
		case "update":
			$db = ffDB_Sql::factory();
			$sSQL = "UPDATE
						" . $globals->options["table_name"] . "
					SET
						" . $globals->options["table_name"] . ".password_used = ''
						, " . $globals->options["table_name"] . ".temp_password = ''
						, " . $globals->options["table_name"] . ".password_generated_at = ''
					WHERE " . $globals->options["table_name"] . ".ID = " . $db->toSql(get_session("UserNID"), "Number") . "
					";
			$db->execute($sSQL);
		break;
	}
}