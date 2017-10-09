<?php
$options = mod_security_get_settings($cm->path_info);

if (isset($cm->modules["security"]["auth_bypath"]) && count($cm->modules["security"]["auth_bypath"]))
{
	foreach ($cm->modules["security"]["auth_bypath"] as $key => $value)
	{
		if  (strpos($cm->path_info, $key) === 0)
		{
			if ($value == "noauth")
			{
				reset ($cm->modules["security"]["auth_bypath"]);
				return;
			}
		}
	}
	reset ($cm->modules["security"]["auth_bypath"]);
}

/*if (isset($cm->modules["security"]["auth_bypath"][$cm->path_info]) && $cm->modules["security"]["auth_bypath"][$cm->path_info] == "noauth")
	return;
*/
mod_security_check_session();

if (isset($cm->processed_rule["rule"]->options->minlevel))
{
	if (get_session("UserLevel") < (string)$cm->processed_rule["rule"]->options->minlevel)
		mod_security_destroy_session(true);
}


if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && $ID_domain)
	$db = mod_security_get_db_by_domain($ID_domain);
else
	$db = mod_security_get_main_db();

$sSQL = "SELECT
			" . $options["table_name"] . ".*
		FROM
			" . $options["table_name"] . "
		WHERE
			" . $options["table_name"] . ".status = '1'
			AND " . $options["table_name"] . ".ID = " . $db->toSql(get_session("UserNID"), "Number");
if (MOD_SEC_EXCLUDE_SQL)
	$sSQL .= " AND " . $options["table_name"] . ".ID " . MOD_SEC_EXCLUDE_SQL;

$sSQL .= " ORDER BY ID DESC";	

$db->query($sSQL);
$db->nextRecord();

$url = FF_SITE_PATH . (string)$cm->router->getRuleById("mod_sec_change_password")->reverse;

if($db->getField("password_used")->getValue() == "1" && $_SERVER["REDIRECT_URL"] != $url)
{
	ffRedirect($url . "?ret_url=" . rawurlencode($_SERVER["REQUEST_URI"]));
}
