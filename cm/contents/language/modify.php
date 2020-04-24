<?php
$db = ffDB_Sql::factory();

// Quando segni un task come aperto o chiuso
if(isset($_REQUEST["frmAction"]) && isset($_REQUEST["setstatus"])) {
    $sSQL = "UPDATE ff_languages
                    SET status = " . $db->toSql($_REQUEST["setstatus"]) . "
                    WHERE 
                    	ff_languages.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
    $db->execute($sSQL);
    if($_REQUEST["XHR_DIALOG_ID"]) {
        die(ffCommon_jsonenc(array("url" => $_REQUEST["ret_url"], "close" => false, "refresh" => true), true));
    } else {
        ffRedirect($_REQUEST["ret_url"]);
    }
}

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "LanguageModify";
$oRecord->resources[] = "LanguageModify";
$cm->oPage->title = "Lingua";
$oRecord->src_table = "ff_languages";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = "Lingua";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "code";
$oField->label = "code";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tiny_code";
$oField->label = "Tiny code";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "iso6391";
$oField->label = "ISO6391";
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);