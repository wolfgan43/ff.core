<?php

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "MainGrid";
$oGrid->title = "Grafici";
$oGrid->source_SQL = "SELECT
							 " . CM_TABLE_PREFIX . "mod_graph_type.ID AS idtype
							," . CM_TABLE_PREFIX . "mod_graph_type.name AS typename							
							," . CM_TABLE_PREFIX . "mod_graph_chart.ID
							," . CM_TABLE_PREFIX . "mod_graph_chart.name AS graph_name
					FROM
							" . CM_TABLE_PREFIX . "mod_graph_chart
					LEFT JOIN
						" . CM_TABLE_PREFIX . "mod_graph_type
							ON " . CM_TABLE_PREFIX . "mod_graph_chart.ID_type = " . CM_TABLE_PREFIX . "mod_graph_type.ID
					[WHERE]
					[ORDER]
							 ";

$oGrid->order_default = "graph_name";
$oGrid->use_search = false;
$oGrid->record_id = "MainRecord";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "graph_name";
$oField->label = "Nome";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "typename";
$oField->label = "Tipo Grafico";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);