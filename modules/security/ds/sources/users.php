<?php
$options = mod_security_get_settings(cm::getInstance()->path_info);

$db = ffDBConnection::factory("db_sql_mysqli", "main");

$strAddSearchFields = "";
$strKeyDescrizione = "";
if(strlen(MOD_SEC_USER_FIRSTNAME)) {
	if(strlen($strKeyDescrizione))
		$strKeyDescrizione .= ",";

	$strKeyDescrizione .= $db->toSql(MOD_SEC_USER_FIRSTNAME);
	
	$strAddSearchFields .= ", (SELECT 
									" . $options["table_dett_name"] . ".value
								FROM
									" . $options["table_dett_name"] . "
								WHERE
									" . $options["table_dett_name"] . ".ID_users = " . $options["table_name"] . ".ID
									AND " . $options["table_dett_name"] . ".field = " . $db->toSql(MOD_SEC_USER_FIRSTNAME) . "
							) AS firstname
				";
}
if(strlen(MOD_SEC_USER_LASTNAME)) {
	if(strlen($strKeyDescrizione))
		$strKeyDescrizione .= ",";

	$strKeyDescrizione .= $db->toSql(MOD_SEC_USER_LASTNAME);
	
	$strAddSearchFields .= ", (SELECT 
									" . $options["table_dett_name"] . ".value
								FROM
									" . $options["table_dett_name"] . "
								WHERE
									" . $options["table_dett_name"] . ".ID_users = " . $options["table_name"] . ".ID
									AND " . $options["table_dett_name"] . ".field = " . $db->toSql(MOD_SEC_USER_LASTNAME) . "
							) AS lastname
				";
}
if(strlen(MOD_SEC_USER_COMPANY)) {
	if(strlen($strKeyDescrizione))
		$strKeyDescrizione .= ",";

	$strKeyDescrizione .= $db->toSql(MOD_SEC_USER_COMPANY);
	
	$strAddSearchFields .= ", (SELECT 
									" . $options["table_dett_name"] . ".value
								FROM
									" . $options["table_dett_name"] . "
								WHERE
									" . $options["table_dett_name"] . ".ID_users = " . $options["table_name"] . ".ID
									AND " . $options["table_dett_name"] . ".field = " . $db->toSql(MOD_SEC_USER_COMPANY) . "
							) AS company
				";
}

$strKeyTelCell = "";
$key_tel = "tel";
$key_cell = "cell";
if(strlen($key_tel)) {
	if(strlen($strKeyTelCell))
		$strKeyTelCell .= ",";

	$strKeyTelCell .= $db->toSql($key_tel);
}
if(strlen($key_cell)) {
	if(strlen($strKeyTelCell))
		$strKeyTelCell .= ",";

	$strKeyTelCell .= $db->toSql($key_cell);
}

$tmp = new ffDBQuery(
	"mod_sec_users_index"
	, $db
	, "SELECT 
			`" . $options["table_name"] . "`.*
			, (SELECT 
					GROUP_CONCAT(IF(`" . $options["table_dett_name"] . "`.value = " . $db->toSql(MOD_SEC_USER_COMPANY) . "
									, CONCAT(' / ', `" . $options["table_dett_name"] . "`.value)
									, `" . $options["table_dett_name"] . "`.value
								) 
						SEPARATOR ' '
					)
				FROM
					`" . $options["table_dett_name"] . "`
				WHERE
					`" . $options["table_dett_name"] . "`.ID_users = `" . $options["table_name"] . "`.ID
					AND `" . $options["table_dett_name"] . "`.field IN (" . $strKeyDescrizione . ")
			) AS descrizione
			, (SELECT 
					GROUP_CONCAT(`" . $options["table_dett_name"] . "`.value SEPARATOR ' / ')
				FROM
					`" . $options["table_dett_name"] . "`
				WHERE
					`" . $options["table_dett_name"] . "`.ID_users = " . $options["table_name"] . ".ID
					AND `" . $options["table_dett_name"] . "`.field IN (" . $strKeyTelCell . ")
			) AS telcell
			$strAddSearchFields
		FROM 
			`" . $options["table_name"] . "`
		WHERE
			1
			[AND] [WHERE] 
		[HAVING] 
		[ORDER]
	"
);

$tmp->addEvent("on_getSql", function (ffDBQuery $source, $sql, $context, $last_event_res) {
	if ($last_event_res)
		$sql = $last_event_res;
	
	$db = $context->db[0];
	
	$addSql = "";

	if (get_session("UserLevel") < 3 && !MOD_SECURITY_USERS_SHOW_LEVELS_ALL)
	{
		if (MOD_SECURITY_USERS_SHOW_LEVELS_ACL)
			$addSql .= "
										AND (`" . $options["table_name"] . "`.level IN (" . MOD_SECURITY_USERS_SHOW_LEVELS_ACL . ")
								";
		else if (MOD_SECURITY_USERS_SHOW_SAME_LEVEL)
			$addSql .= "
										AND (`" . $options["table_name"] . "`.level <= " . $db->toSql(get_session("UserLevel")) . "
				";
		else
			$addSql .= "
										AND (`" . $options["table_name"] . "`.level < " . $db->toSql(get_session("UserLevel")) . "
				";
		$addSql .= "
									OR `" . $options["table_name"] . "`.ID = " . $db->toSql(get_session("UserNID"), "Number") . "
								)
			";
	}

	$res = cm::getInstance()->modules["security"]["events"]->doEvent("get_domain");
	$rc_domain = end($res);
	if ($rc_domain)
		$addSql .= " AND `" . $options["table_name"] . "`.ID_domains = " . $db->toSql($rc_domain, "Number") . "";
	else if (MOD_SEC_MULTIDOMAIN && !MOD_SEC_MULTIDOMAIN_EXTERNAL_DB)
		$addSql .= " AND `" . $options["table_name"] . "`.ID_domains = " . $db->toSql(mod_security_get_domain(), "Number") . "";

	if (MOD_SEC_EXCLUDE_SQL)
		$addSql .= " AND `" . $options["table_name"] . "`.ID " . MOD_SEC_EXCLUDE_SQL;
	
	$sql = str_replace("[AND]", $addSql . " [AND]", $sql);
	
	return $sql;
});