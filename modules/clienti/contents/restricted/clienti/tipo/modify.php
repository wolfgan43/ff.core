<?php

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "RecordTipo";
$oRecord->title = "Tipo Cliente";
$oRecord->src_table = CM_TABLE_PREFIX . "mod_clienti_tipo";
$oRecord->resources[] = "tipo_cliente";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tipo";
$oField->label = "Tipo Cliente";
$oField->required = true;
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);