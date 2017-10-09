<?php

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "MainRecord";
$oRecord->title = "Dati";
$oRecord->src_table = CM_TABLE_PREFIX . "mod_graph_data";

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
$oField->id = "sql";
$oField->label = "SQL";
$oField->extended_type = "Text";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "column";
$oField->label = "Colonne";
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);

function MainRecord_on_do_action(ffRecord_base $oRecord, $frmAction)
{
	if($frmAction)
	{
		$error = check_query(array(&$oRecord));
		if(strlen($error))
		{	
			$oRecord->strError = $error;
			return true;
		}			
	}
	switch ($frmAction)
	{
		case "insert":

			$db = ffDB_Sql::factory();
			$sSQL = $oRecord->form_fields["sql"]->value->getValue();
			$db->query($sSQL);
			$db->nextRecord();

			foreach ($db->fields as $key => $value)
			{
				$fields .= $key . ", ";
			}

			$num_col = count($db->fields);
			$oRecord->form_fields["column"]->setValue($fields);
			$oRecord->form_fields["column_count"]->setValue($num_col);
			break;

		case "update":

			$db = ffDB_Sql::factory();
			$sSQL = $oRecord->form_fields["sql"]->value->getValue();
			$db->query($sSQL);
			$db->nextRecord();

			foreach ($db->fields as $key => $value)
			{
				$fields .= $key . ", ";
			}

			$num_col = count($db->fields);
			$oRecord->form_fields["column"]->setValue($fields);
			$oRecord->form_fields["column_count"]->setValue($num_col);
			break;
	}
}

function check_query($oRecord)
{
	$db2 = ffDB_Sql::factory();
	$db2->on_error = "ignore";
	$db2->query($oRecord[0]->form_fields["sql"]->value->getValue());

	return $db2->error;
}

