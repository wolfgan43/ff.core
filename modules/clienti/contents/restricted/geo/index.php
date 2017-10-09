<?php
//if(get_session("UserLevel") == "4")
//    ffRedirect(FF_SITE_PATH . "/bacheca");
    
$db = ffDB_Sql::factory();
$cm->oPage->widgetLoad("dialog");

$cm->oPage->widgets["dialog"]->process(
							"edit"
							, array(
									"title"			=> ffCommon_specialchars("Modifica")
									, "url"			=> ""
/*									, "tpl_id"		=> $obj->id
									, "params"		=> array(
										"persistent" => true
									)
									, "height"		=> "500"
*/								)
							, $cm->oPage
	);



if (!strlen($_REQUEST["frmAction"])) {
	$sWhere .= "  1 = 0 AND ";
	$sWhere2 .= "  1 = 0 AND ";
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "geo";
$oGrid->title = "Ricerca";
$oGrid->source_SQL = "
					SELECT DISTINCT
						'Cliente' AS type,
						 CONCAT('c', " . CM_TABLE_PREFIX . "mod_clienti_main.ID) AS ID,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lat,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lng,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.ragsoc,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.nome,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.cognome,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.piva,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.cf,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.indirizzo,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.cap,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.citta,
						 support_province.CarAbbreviation AS supportprovincia,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.telefono1,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.cellulare1,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.fax,
						 " . CM_TABLE_PREFIX . "mod_clienti_main.email1
					FROM
						" . CM_TABLE_PREFIX . "mod_clienti_main
					LEFT JOIN support_province
						ON support_province.ID = " . CM_TABLE_PREFIX . "mod_clienti_main.provincia
					WHERE
					" . $sWhere . "
						" . CM_TABLE_PREFIX . "mod_clienti_main.coords_lat <> ''
						AND " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lng <> ''
						[AND]
						[WHERE]
						[HAVING]
					[ORDER]
							";
$oGrid->order_default = "ragsoc";
//$oGrid->display_grid = "search";
$oGrid->display_new = false;
$oGrid->display_edit_bt = false;
$oGrid->display_delete_bt = false;
$oGrid->display_search_simple = false;
$oGrid->bt_edit_url = "javascript:centermap('[ID_VALUE]')";

$oGrid->fixed_pre_content = '<div style="float: left; width: 500px;">';
$oGrid->fixed_post_content = '</div>';

$tmp = ffButton::factory($cm->oPage);
$tmp->id 			= "searched";
$tmp->label 		= ffTemplate::_get_word_by_code("search_bt");
$tmp->aspect 		= "button";
$tmp->action_type 	= "submit";
$tmp->frmAction		= "search";
if  (strlen($tmp->class)) $tmp->class .= " ";
$tmp->class .= "noactivebuttons";
$tmp->jsaction = "ff.ajax.doRequest({'component' : '" . $oGrid->id . "','section' : 'GridData', 'callback' : loadmarkers});";
$oGrid->buttons_options["search"]["obj"] = $tmp;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oGrid->addKeyField($oField);

 
if (MOD_CLIENTI_CODICE)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "codice";
    $oField->label = "Codice";
    $oGrid->addContent($oField);
}
if (MOD_CLIENTI_RAGSOC)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "ragsoc";
    $oField->label = "Ragione Sociale";
    $oGrid->addContent($oField);
}

if (MOD_CLIENTI_NOMECOGNOME)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "nome";
    $oField->label = "Nome";
    $oGrid->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "cognome";
    $oField->label = "Cognome";
    $oGrid->addContent($oField);
}
$oField = ffField::factory($cm->oPage);
$oField->id = "piva";
$oField->label = "P. IVA";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cf";
$oField->label = "Codice Fiscale";
$oGrid->addContent($oField);

    
$oField = ffField::factory($cm->oPage);
$oField->id = "provincia";
$oField->label = "Provincia";
$oField->base_type = "Number";
$oField->extended_type = "Selection";
$oField->multi_select_one_label = "Tutte";
$oField->source_SQL = "SELECT ID, Name FROM support_province ORDER BY Name ASC";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id				= "ragsoc";
$oField->label			= "Rag. Sociale";
$oField->src_operation	= "[NAME] LIKE([VALUE])";
$oField->src_prefix		= "%";
$oField->src_postfix	= "%";
$oGrid->addSearchField($oField);


$cm->oPage->addContent($oGrid);

$tpl = ffTemplate::factory(FF_THEME_DISK_PATH . "/xnmadmin/contents/geo");
$tpl->load_file("index.html", "main");
$tpl->set_var("site_path", FF_SITE_PATH);
$tpl->set_var("theme", $cm->oPage->getTheme());

$cm->oPage->addContent($tpl);
