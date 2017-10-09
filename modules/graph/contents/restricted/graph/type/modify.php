<?php
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "MainRecord";
$oRecord->title = "Tipo";
$oRecord->src_table =  CM_TABLE_PREFIX . "mod_graph_type";
$oRecord->addEvent("on_done_action", "MainRecord_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = "Nome";
$oField->required = true;
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "template_path";
$oField->label = "Percorso template";
$oField->control_type = "label";
$oField->data_type = "";
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);

function MainRecord_on_done_action ($oRecord)
{
	$cm = cm::getInstance();
	$db = ffDB_Sql::factory();

	$name = $oRecord->form_fields["name"]->getValue();
	$template_path = strtolower($name) . ".html";
	$oRecord->form_fields["template_path"]->setValue($template_path);

	$sSQL = "UPDATE
				" . CM_TABLE_PREFIX . "mod_graph_type
			SET template_path = " . $db->toSql($template_path)
			. " WHERE ID = " . $db->toSql($_REQUEST["keys"]["ID"]) . "
				";

	$db->execute($sSQL);
}