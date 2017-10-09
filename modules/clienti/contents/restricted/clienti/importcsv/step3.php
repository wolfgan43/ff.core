<?php
require_once(ffCommon_dirname(__FILE__) . "/common." . FF_PHP_EXT);
// -------------------------
// check key & form url
$globals = ffGlobals::getInstance("wizard");
$globals->var_prefix = "wizcsv_";

$globals->transit_params = $cm->oPage->get_globals() . "ret_url=" . rawurlencode($_REQUEST["ret_url"]);
// -------------------------

$cm->oPage->form_method = "POST";
$db = ffDB_Sql::factory();

// ----------------------------------
//  BREADCRUMB
if(is_file(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv/breadcrumb.html")) {
	$tpl_bread = ffTemplate::factory(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv");
	$tpl_bread->load_file("breadcrumb.html", "main");
	$tpl_bread->set_var("site_path", FF_SITE_PATH);
	$tpl_bread->set_var("theme", $cm->oPage->getTheme());
	$tpl_bread->set_var("query_string", $_SERVER["QUERY_STRING"]);

	$tpl_bread->set_var("selected_3", "wizbread_selected");

	$cm->oPage->addContent($tpl_bread);
}
// ----------------------------------
$arrData = get_importcsv_fields($globals->import_fields[0]);


//creo componente - grid
$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "ImportCSV";
$oGrid->title = ffTemplate::_get_word_by_code("fix_members");
$oGrid->description = ffTemplate::_get_word_by_code("wizcsv_step1_format_description");
$oGrid->source_SQL = "SELECT
							" . $arrData["table"] . ".*
						FROM
							" . $arrData["table"] . "
						WHERE
							" . $arrData["table"] . ".tofix <> ''
							AND " . $arrData["table"] . ".import = " . $db->toSql(get_session("importcsvref")) ."
						[AND] [WHERE]
						[ORDER]";

$oGrid->order_default = $arrData["record"]["key"];
$oGrid->record_url = $arrData["record"]["url"];
$oGrid->resources[] = $arrData["record"]["resources"];
$oGrid->record_id = "IscrittoRecord";
$oGrid->addEvent("on_before_parse_row", "ImportCSV_on_before_parse_row");

/*if ($tot_iscritti > NEWSLETTER_TEST_GROUP_LIMIT)
	$oGrid->display_new = false;*/
//$oGrid->full_ajax = true;
$oGrid->open_adv_search = true;
$oGrid->display_new = false;

$bt = ffButton::factory($cm->oPage);
$bt->id = "step2";
$bt->label = ffTemplate::_get_word_by_code("wizcsv_step2");
$bt->class = "prev-last ";
$bt->action_type = "gotourl";
$bt->url = FF_SITE_PATH . $cm->oPage->page_path . "/step2?" . $_SERVER["QUERY_STRING"];
$oGrid->addActionButton($bt);

$bt = ffButton::factory($cm->oPage);
$bt->id = "done";
$bt->label = ffTemplate::_get_word_by_code("wizcsv_done");
$bt->class = "next-save ";
$bt->action_type = "gotourl";
$bt->url = FF_SITE_PATH . $cm->oPage->page_path . "/step1?" . $_SERVER["QUERY_STRING"];
$oGrid->addActionButton($bt);


//campo chiave
$oField = ffField::factory($cm->oPage);
$oField->id = $arrData["record"]["key"];
$oField->data_source = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$csv_rel_field = get_session("importcsv_rel_field");

if(is_array($csv_rel_field) && count($csv_rel_field)) {
	foreach($csv_rel_field AS $csv_rel_field_key => $csv_rel_field_value) {
		if(array_key_exists($csv_rel_field_value, $arrData["structure"])
			&& array_key_exists("key", $arrData["structure"][$csv_rel_field_value])
		) {
			$real_field = $arrData["structure"][$csv_rel_field_value]["key"];
		} else {
			$real_field = $csv_rel_field_value;
		}
        if(strlen($real_field)) {
		    $oField = ffField::factory($cm->oPage);
		    $oField->id = $real_field;
		    $oField->label = $csv_rel_field_value;
		    $oGrid->addContent($oField);
        }
	}
}

$cm->oPage->addContent($oGrid);

function ImportCSV_on_before_parse_row($component) {
    $arrToFix = explode(",", $component->db[0]->getField("tofix", "Text", true));
    
    if(is_array($arrToFix) && count($arrToFix)) {
        foreach($component->grid_fields AS $key => $value) {
            if(array_search($key, $arrToFix) !== false) {
                $component->grid_fields[$key]->container_class = "red";
            } else {
                $component->grid_fields[$key]->container_class = "green";
            }
        }
    }
}
