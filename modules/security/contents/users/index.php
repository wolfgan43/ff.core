<?php
$db = ffDB_Sql::factory();

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "MainGrid";
$oGrid->title = "Utenti";
$oGrid->source_DS = "mod_sec_users_index";
		
if (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username")
	$oGrid->order_default = "username";
else
	$oGrid->order_default = "email";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "ModSecUtenti";
$oGrid->addEvent("on_before_parse_row", "MainGrid_on_before_parse_row");
if (get_session("UserLevel") == 1)
{
	$oGrid->display_delete_bt = false;
	$oGrid->display_new = false;
}

$oGrid->force_no_field_params = true;
if(MOD_SEC_MAXUSERS){
	$db_countitems = ffDB_Sql::factory();
	$oGridcount_SQL = "SELECT COUNT(*) AS count
                        FROM 
                            " . CM_TABLE_PREFIX . "mod_security_users
						WHERE " . CM_TABLE_PREFIX . "mod_security_users.status = 1 OR " . CM_TABLE_PREFIX . "mod_security_users.expiration > " . $db->toSql(new ffData(date("d/m/Y H:i:s"), "DateTime", "ITA"));
	$db_countitems->query($oGridcount_SQL);
	if($db_countitems->nextRecord()){
		$users_mancanti = MOD_SEC_MAXUSERS - $db_countitems->getField("count")->getValue();
		if($users_mancanti >= 1){
			$oGrid->description = ffTemplate::_get_word_by_code("no_limit_users_begin") . "&nbsp;" . $users_mancanti ."&nbsp;" . ffTemplate::_get_word_by_code("no_limit_users_end");
		}else{
			$oGrid->display_new = false;
			$oGrid->description = ffTemplate::_get_word_by_code("limit_users_begin") . "&nbsp;" . MOD_SEC_MAXUSERS ."&nbsp;" . ffTemplate::_get_word_by_code("limit_users_end");
		}	
	}
}

if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MAXUSERS && mod_security_get_domain())
{
	
	$options = mod_security_get_settings($cm->path_info);
	
	$db = ffDB_Sql::factory();
	$sSQL = "SELECT * FROM cm_mod_security_domains WHERE ID = " . $db->toSql(mod_security_get_domain());
	$db->query($sSQL);
	$db->nextRecord();

	$max_users = $db->getField("max_users")->getValue();

	$sSQL = "SELECT `ID` FROM `cm_mod_security_users` WHERE `" . $options["table_name"] . "`.`ID_domains` = ";
	if ($rc_domain)
		$sSQL .= $oGrid->db[0]->toSql($rc_domain);
	else
		$sSQL .= $oGrid->db[0]->toSql(mod_security_get_domain());
	
	if (MOD_SEC_EXCLUDE_SQL)
		$sSQL .= " AND `" . $options["table_name"] . "`.ID " . MOD_SEC_EXCLUDE_SQL;

	$db->query($sSQL);
	if ($db->numRows() >= $max_users)
	{
		$oGrid->display_new = false;
	}
}


$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi visualizzati
if (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username")
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "username";
	$oField->label = "Username";
	$oGrid->addContent($oField);
}

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = "E-Mail";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "status";
$oField->label = "Status";
$oField->extended_type = "Selection";
$oField->multi_select_one = false;
$oField->multi_pairs = array( 
								array( new ffData("0"),  new ffData("Disabled")),
								array( new ffData("1"),  new ffData("Enabled"))
							);
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "level";
$oField->label = "Livello";
$oField->extended_type = "Selection";
$oField->multi_pairs[] = array( new ffData("1"),  new ffData("Utente"));
$oField->multi_pairs[] = array( new ffData("2"),  new ffData("Admin"));
$oField->multi_pairs[] = array( new ffData("3"),  new ffData("Super Admin"));
$oGrid->addContent($oField);
	
if (MOD_SEC_PROFILING && get_session("UserLevel") == 3)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "profile";
	$oField->label = "Profilo";
	$oField->extended_type = "Selection";
	$oField->source_SQL = "SELECT ID, nome FROM cm_mod_security_profiles WHERE enabled = '1' ORDER BY (`order`)";
	$oField->multi_select_one_label = "";
	$oGrid->addContent($oField);
}

$oField = ffField::factory($cm->oPage);
$oField->id = "descrizione";
$oField->label = "Nome Cognome / Ragione Sociale";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "telcell";
$oField->label = "tel / cellulare";
$oGrid->addContent($oField);

// Campi di ricerca
if (MOD_SECURITY_LOGON_USERID == "both" || MOD_SECURITY_LOGON_USERID == "username")
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "username";
	$oField->label = "Username";
	$oField->src_operation 	= "[NAME] LIKE [VALUE]";
	$oField->src_prefix 	= "%";
	$oField->src_postfix 	= "%";
	$oGrid->addSearchField($oField);
}

$oField = ffField::factory($cm->oPage);
$oField->id = "status";
$oField->label = "Status";
$oField->extended_type = "Selection";
$oField->multi_select_one_label = "All";
$oField->multi_pairs = array( 
								array( new ffData("0"),  new ffData("Disabled")),
								array( new ffData("1"),  new ffData("Enabled"))
							);
$oGrid->addSearchField($oField);

if(strlen(MOD_SEC_USER_FIRSTNAME))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "firstname";
	$oField->label = "Nome";
	$oField->src_operation 	= "[NAME] LIKE [VALUE]";
	$oField->src_prefix 	= "%";
	$oField->src_postfix 	= "%";
	$oField->src_having = true;
	$oGrid->addSearchField($oField);
}

if(strlen(MOD_SEC_USER_LASTNAME))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "lastname";
	$oField->label = "Cognome";
	$oField->src_operation 	= "[NAME] LIKE [VALUE]";
	$oField->src_prefix 	= "%";
	$oField->src_postfix 	= "%";
	$oField->src_having = true;
	$oGrid->addSearchField($oField);
}

if(strlen(MOD_SEC_USER_COMPANY))
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "company";
	$oField->label = "Ragione Sociale";
	$oField->src_operation 	= "[NAME] LIKE [VALUE]";
	$oField->src_prefix 	= "%";
	$oField->src_postfix 	= "%";
	$oField->src_having = true;
	$oGrid->addSearchField($oField);
}

$cm->oPage->addContent($oGrid);

function MainGrid_on_before_parse_row($oComponent)
{
	$db = $oComponent->db[0];

	if (
			get_session("UserLevel") < 3
			&& (
				($db->getField("ID", "Number", true) == get_session("UserNID"))
				|| strlen($db->getField("special", "Text", true))
				|| ($db->getField("level", "Number", true) > get_session("UserLevel"))
				|| (($db->getField("level", "Number", true) == get_session("UserLevel")) && !MOD_SECURITY_USERS_MODIFY_SAME_LEVEL)
			)
		)
		$oComponent->visible_delete_bt = false;
	else
		$oComponent->visible_delete_bt = true;
}
