<?php
$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "MainGrid";
$oGrid->title = "Tipo";
$oGrid->source_SQL = "SELECT
                            *
					FROM
							`" . CM_TABLE_PREFIX . "mod_graph_type`
					[WHERE]
					[ORDER]
							 ";

$oGrid->order_default = "name";
$oGrid->use_search = false;
$oGrid->record_id = "MainRecord";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = "Nome";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);