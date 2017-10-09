<?php
$tmp = new ffDBQuery(
	"mod_sec_profiles_index"
	, ffDBConnection::factory("db_sql_mysqli", "main")
	, "SELECT
				`cm_mod_security_profiles`.*
				[addSQLDomainField]
			FROM
				`cm_mod_security_profiles`
				[addSQLDomainJoin]
			WHERE
				( `cm_mod_security_profiles`.`special` = '' [addSqlSpecial] )
				AND ( `cm_mod_security_profiles`.`acl` = '' OR [UserLevel] IN(`cm_mod_security_profiles`.`acl`) )
				[addSQLDomainWhere]
				[AND] [WHERE]
			[HAVING]
			ORDER BY 
				`cm_mod_security_profiles`.`order` 
				[COLON] [ORDER]
		"
);

$tmp->addEvent("on_getSql", function (ffDBQuery $source, $sql, $context, $last_event_res) {
	if ($last_event_res)
		$sql = $last_event_res;
	
	$db = $oComponent->db[0];

	$ids = mod_sec_profiling_update_profiles();
	if (strlen($ids))
		$addSqlSpecial = " OR (`cm_mod_security_profiles`.`special` <> '' AND `cm_mod_security_profiles`.`ID` IN($ids)) ";

	if (MOD_SEC_MULTIDOMAIN && !MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && mod_security_get_domain())
	{
		$addSQLDomainField = ", `cm_mod_security_domains`.`nome` AS `domainname`";
		$addSQLDomainJoin = "							LEFT JOIN `cm_mod_security_domains` ON
									`cm_mod_security_domains`.`ID` = `cm_mod_security_profiles`.`ID_domains`
							";
		$addSQLDomainWhere = " AND (`cm_mod_security_profiles`.`ID_domains` = 0 OR `cm_mod_security_profiles`.`ID_domains` = " . $db->toSql(mod_security_get_domain()) . ")";
	}
	
	$sql = str_replace("[addSqlSpecial]", $addSqlSpecial, $sql);
	$sql = str_replace("[addSQLDomainField]", $addSQLDomainField, $sql);
	$sql = str_replace("[addSQLDomainJoin]", $addSQLDomainJoin, $sql);
	$sql = str_replace("[addSQLDomainWhere]", $addSQLDomainWhere, $sql);
	$sql = str_replace("[UserLevel]", get_session("UserLevel"), $sql);
	
	return $sql;
});