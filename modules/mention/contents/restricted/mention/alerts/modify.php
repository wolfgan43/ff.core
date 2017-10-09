<?php
$permission = check_mention_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_MENTION_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

$oRecord = ffRecord::factory($cm->oPage);
/*
if(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path) . "/ffRecord.html")) {
	$oRecord->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path);
} elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/ffRecord.html")) {
	$oRecord->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm";
}*/
$oRecord->id = "AlertsModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("alerts_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_mention_alerts";
$oRecord->addEvent("on_done_action", "AlertsModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_name");
$oField->required = true;
$oRecord->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "path";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_path");
$oField->required = true;
$oRecord->addContent($oField);


$settings_field = null;
if(isset($_REQUEST["keys"]["ID"])) {
	$oRecord->addTab("stats");
	$oRecord->setTabTitle("stats", ffTemplate::_get_word_by_code("alerts_modify_stats"));

	$oRecord->addContent(null, true, "stats"); 
	$oRecord->groups["stats"] = array(
		                             "title" => ffTemplate::_get_word_by_code("alerts_modify_stats")
		                             , "cols" => 1
		                             , "tab" => "stats"
		                          );

		                          
	$oField = ffField::factory($cm->oPage);
	$oField->id = "ID_source";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_ID_source");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  

	$oField = ffField::factory($cm->oPage);
	$oField->id = "read_access_secret";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_read_access_secret");
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  

	$oField = ffField::factory($cm->oPage);
	$oField->id = "stats_mentions_total";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_stats_mentions_total");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  
		                          
	$oField = ffField::factory($cm->oPage);
	$oField->id = "stats_unread_mentions_total";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_stats_unread_mentions_total");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  

	$oField = ffField::factory($cm->oPage);
	$oField->id = "stats_favorite_mentions_total";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_stats_favorite_mentions_total");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  

	$oField = ffField::factory($cm->oPage);
	$oField->id = "stats_trashed_mentions_total";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_stats_trashed_mentions_total");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  
	
	$oField = ffField::factory($cm->oPage);
	$oField->id = "stats_tasks_total";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_stats_tasks_total");
	$oField->base_type = "Number";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  
	
	$oField = ffField::factory($cm->oPage);
	$oField->id = "created_at";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_created_at");
	$oField->base_type = "Timestamp";
	$oField->extended_type = "DateTime";
	$oField->app_type = "DateTime";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  
		                          
	$oField = ffField::factory($cm->oPage);
	$oField->id = "updated_at";
	$oField->label = ffTemplate::_get_word_by_code("alerts_modify_updated_at");
	$oField->base_type = "Timestamp";
	$oField->extended_type = "DateTime";
	$oField->app_type = "DateTime";
	$oField->control_type = "label";
	$oRecord->addContent($oField, "stats");  


	$oRecord->addTab("settings");
	$oRecord->setTabTitle("settings", ffTemplate::_get_word_by_code("alerts_modify_settings"));

	$oRecord->addContent(null, true, "settings"); 
	$oRecord->groups["settings"] = array(
		                             "title" => ffTemplate::_get_word_by_code("alerts_modify_settings")
		                             , "cols" => 1
		                             , "tab" => "settings"
		                          );
	$settings_field = "settings";
	
}

$primary_keyword = false;
if(strlen(global_settings("MOD_MENTION_KEYWORDS_BY_DB"))) {
	$arrKeywordsTableField = explode(".", global_settings("MOD_MENTION_KEYWORDS_BY_DB"));
	$keywords_table = $arrKeywordsTableField[0];
	$keywords_field = $arrKeywordsTableField[1];
	
	$keywords_field_prefix = global_settings("MOD_MENTION_KEYWORDS_BY_DB_PREFIX");
	if($keywords_field_prefix)
		$keywords_field_prefix = $keywords_field_prefix . " ";
	$keywords_field_postfix = global_settings("MOD_MENTION_KEYWORDS_BY_DB_POSTFIX"); 
	if($keywords_field_postfix)
		$keywords_field_postfix = " " . $keywords_field_postfix;
	
	if(strlen($keywords_table) && strlen($keywords_field)) {
		$primary_keyword = true;

		$oField = ffField::factory($cm->oPage);
		$oField->id = "primary_keyword";
		$oField->label = ffTemplate::_get_word_by_code("alerts_modify_primary_keyword");
		
		$oField->extended_type = "Selection";
		$oField->source_SQL = "SELECT CONCAT(" . $db->toSql($keywords_field_prefix) . ", " . $keywords_table . "." . $keywords_field . ", " . $db->toSql($keywords_field_postfix) . ") AS ID
					, CONCAT(" . $db->toSql($keywords_field_prefix) . ", " . $keywords_table . "." . $keywords_field . ", " . $db->toSql($keywords_field_postfix) . ") AS name
				FROM " . $keywords_table . "
				WHERE " . $keywords_table . "." . $keywords_field . " != ''
				[AND] [WHERE]
				[ORDER] [COLON] " . $keywords_table . "." . $keywords_field . "
				[LIMIT]";

		$oField->autocompletetoken_minLength = 0;
		//$oField->autocompletetoken_delimiter = ",";
		$oField->autocompletetoken_combo = true;
		$oField->autocompletetoken_limit = 1;
		$oField->autocompletetoken_compare = "name";
		$oField->widget = "autocompletetoken";
		$oField->required = true;
		$oRecord->addContent($oField, $settings_field);  		
	}
}


      
$oField = ffField::factory($cm->oPage);
$oField->id = "included_keywords";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_included_keywords");
$oField->widget = "listgroup";
$oField->grouping_separator = ",";
$oField->required = !$primary_keyword;
$oRecord->addContent($oField, $settings_field);  

$oField = ffField::factory($cm->oPage);
$oField->id = "excluded_keywords";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_excluded_keywords");
$oField->widget = "listgroup";
$oField->grouping_separator = ",";
$oRecord->addContent($oField, $settings_field);  

$oField = ffField::factory($cm->oPage);
$oField->id = "languages";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_languages");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT
                           " . FF_PREFIX . "languages.tiny_code,
                           " . FF_PREFIX . "languages.description
                       FROM
                           " . FF_PREFIX . "languages
                       [WHERE]
                       [ORDER] [COLON] " . FF_PREFIX . "languages.description
                       [LIMIT]";
//$oField->autocompletetoken_compare_having = "Fname";
$oField->autocompletetoken_minLength = 0;
$oField->autocompletetoken_delimiter = ",";
$oField->autocompletetoken_combo = true;
$oField->autocompletetoken_compare = "description";
$oField->widget = "autocompletetoken";
$oField->required = true;
$oRecord->addContent($oField, $settings_field);  

$oField = ffField::factory($cm->oPage);
$oField->id = "sources";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_sources");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT *
                        FROM (
                            ( 
                            SELECT
                              'web' AS `ID`
                              , 'Web' AS name
                            )
                            UNION  
                            (      
                            SELECT
                              'facebook' AS ID
                              , 'Facebook' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'twitter' AS ID
                              , 'Twitter' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'news' AS ID
                              , 'News' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'blogs' AS ID
                              , 'Blogs' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'videos' AS ID
                              , 'Videos' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'forums' AS ID
                              , 'Forums' AS name
                            )
                            UNION        
                            (
                            SELECT
                              'images' AS ID
                              , 'Images' AS name
                            )
                        ) AS tbl_src
                       [WHERE]
                       [ORDER] [COLON] tbl_src.name
                       [LIMIT]";
//$oField->autocompletetoken_compare_having = "Fname";
$oField->autocompletetoken_minLength = 0;
$oField->autocompletetoken_delimiter = ",";
$oField->autocompletetoken_combo = true;
$oField->autocompletetoken_compare = "name";
$oField->widget = "autocompletetoken";
$oField->required = true;
$oRecord->addContent($oField, $settings_field);  

$oField = ffField::factory($cm->oPage);
$oField->id = "blocked_sites";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_blocked_sites");
$oField->widget = "listgroup";
$oField->grouping_separator = ",";
$oRecord->addContent($oField, $settings_field);  

$oField = ffField::factory($cm->oPage);
$oField->id = "noise_detection";
$oField->label = ffTemplate::_get_word_by_code("alerts_modify_noise_detection");
$oField->base_type = "Number";
$oField->extended_type = "Boolean";
$oField->control_type = "checkbox";
$oField->checked_value = new ffData("1", "Number");
$oField->unchecked_value = new ffData("0", "Number");
$oField->default_value = new ffData("1", "Number");
$oRecord->addContent($oField, $settings_field); 

$cm->oPage->addContent($oRecord);   

function AlertsModify_on_done_action($component, $action) {
	$db = ffDB_Sql::factory();

	switch($action) {
		case "insert":
		case "update":
			$arrIncludedKeywords = explode(",", $component->form_fields["included_keywords"]->getValue());
			if(isset($component->form_fields["primary_keyword"])) {
				if(array_search($component->form_fields["primary_keyword"]->getValue(), $arrIncludedKeywords) !== false) {
					unset($arrIncludedKeywords[array_search($component->form_fields["primary_keyword"]->getValue(), $arrIncludedKeywords)]);
					$sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_mention_alerts SET 
								included_keywords = " . $db->toSql(implode(",", $arrIncludedKeywords)) . "
							WHERE " . CM_TABLE_PREFIX . "mod_mention_alerts.ID = " . $db->toSql($component->key_fields["ID"]->value);
					$db->execute($sSQL);
				}
			}

			$sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_mention_alerts SET 
						smart_url = " . $db->toSql(ffCommon_url_rewrite($component->form_fields["name"]->getValue())) . "
					WHERE " . CM_TABLE_PREFIX . "mod_mention_alerts.ID = " . $db->toSql($component->key_fields["ID"]->value);
			$db->execute($sSQL);

			break;
		default:	
	}
}
?>
