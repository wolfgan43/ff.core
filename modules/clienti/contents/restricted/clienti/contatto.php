<?php
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "RecordContatti";
$oRecord->title = "Aggiungi/Modifica Contatto";
$oRecord->src_table = CM_TABLE_PREFIX . "mod_clienti_contatti";
$oRecord->resources[] = "clienti_contatti";
$oRecord->resources[] = "contatti";
$oRecord->insert_additional_fields["ID_clienti"] = new ffData($_REQUEST["keys"]["ID"], "Number", FF_SYSTEM_LOCALE);
$oRecord->insert_additional_fields["created"] = new ffData(time());
$oRecord->additional_fields["last_update"] = new ffData(time());


$oRecord->addContent(null, true, "personal");

$oRecord->groups["personal"] = array(
		"title" => "Informazioni Personali"
	);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_contact";
$oField->data_source = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "nome";
$oField->label = "Nome";
$oField->required = true;
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cognome";
$oField->label = "Cognome";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "nascita";
$oField->base_type = "Date";
$oField->label = "Data di Nascita";
$oField->widget = "datechooser";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono";
$oField->label = "Telefono";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cellulare";
$oField->label = "Cellulare";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "fax";
$oField->label = "FAX";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = "E-Mail";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "isReferente";
$oField->label = "Referente";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "hobby";
$oField->label = "Hobby - Sport";
$oRecord->addContent($oField, "personal");

$oField = ffField::factory($cm->oPage);
$oField->id = "squadra";
$oField->label = "Squadra del cuore";
$oRecord->addContent($oField, "personal");

$oField = ffField::factory($cm->oPage);
$oField->id = "associazione";
$oField->label = "Ass. di appartenenza";
$oRecord->addContent($oField, "personal");

$cm->oPage->addContent($oRecord);