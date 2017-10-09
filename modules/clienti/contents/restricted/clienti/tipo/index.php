<?php

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "GridTipo";
$oGrid->title = "Tipo Cliente";
$oGrid->full_ajax = true;
$oGrid->record_id = "RecordTipo";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->resources[] = "tipo_cliente";
$oGrid->source_SQL = "SELECT
						*
					FROM
						" . CM_TABLE_PREFIX . "mod_clienti_tipo
					[WHERE]
					[ORDER]
					";
$oGrid->use_search = false;
$oGrid->order_default = "ID";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tipo";
$oField->label = "Tipo Cliente";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);