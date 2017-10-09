<?php

$globals = ffGlobals::getIstance("mod_article");

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "GridArticle";
$oGrid->title = "Article";
$oGrid->source_SQL = "
						SELECT
								*
							FROM
								`" . CM_TABLE_PREFIX . "mod_article`
							[WHERE]
							[ORDER]
					";

if (isset($applet_params["limit"]) && intval($applet_params["limit"]) > 0)
{
  $oGrid->source_SQL .= " LIMIT 0," . intval($applet_params["limit"]);
}

$oGrid->order_default = "date";
$oGrid->use_search = false;
$oGrid->record_id = "MainRecord";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "date";
$oField->label = "Data";
$oField->order_dir = "DESC";
$oField->base_type = "Date";
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "id_categories";
$oField->label = "Categoria";
$oGrid->addDisplayField($oField);

$oGrid->use_own_location = true;

$cm->applets_components[$appletid] = array("GridArticle"=>true);

$cm->oPage->addComponent($oGrid);


























