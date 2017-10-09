<?php

$cm->oPage->tplAddJs("aziende", "aziende.js", "/themes/" . $cm->oPage->getTheme() . "/javascript");

$db = ffDB_Sql::factory();

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "RecordClienti";
$oRecord->title = "Cliente";
$oRecord->src_table = CM_TABLE_PREFIX . "mod_clienti_main";
$oRecord->addEvent("on_check_fields", "RecordClienti_on_check_fields");
$oRecord->addEvent("on_do_action", "RecordClienti_on_do_action");
$oRecord->addEvent("on_done_action", "RecordClienti_on_done_action");
$oRecord->insert_additional_fields["created"] = new ffData(time());
$oRecord->additional_fields["last_update"] = new ffData(time());
$oRecord->addContent(null, true, "daticliente");
$oRecord->addContent(null, true, "recapiti");
$oRecord->addContent(null, true, "contatti");
$oRecord->addContent(null, true, "test");
$oRecord->resources[] = "clienti";

$populate_edit_SQL = "SELECT
							" . CM_TABLE_PREFIX . "mod_clienti_main.*
						";

if (isset($cm->modules["clienti"]["fields"]) && count($cm->modules["clienti"]["fields"]))
{
	foreach ($cm->modules["clienti"]["fields"] as $key => $value)
	{
		$populate_edit_SQL .= ", (SELECT
											" . CM_TABLE_PREFIX . "mod_clienti_fields.value
										FROM
											" . CM_TABLE_PREFIX . "mod_clienti_fields
										WHERE
											" . CM_TABLE_PREFIX . "mod_clienti_fields.ID_clienti = " . CM_TABLE_PREFIX . "mod_clienti_main.ID
											AND " . CM_TABLE_PREFIX . "mod_clienti_fields.field = " . $db->toSql($key) . "
								) AS " . $key . "
			";

		$populate_insert_SQL .= ", '' AS " . $key;
	}
	reset($cm->modules["clienti"]["fields"]);
}
$populate_edit_SQL .= "FROM
							" . CM_TABLE_PREFIX . "mod_clienti_main
						WHERE
							" . CM_TABLE_PREFIX . "mod_clienti_main.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number") . "
	";

$oRecord->populate_edit_SQL = $populate_edit_SQL;
$oRecord->auto_populate_edit = true;
$oRecord->del_action = "multi_delete";
$oRecord->del_multi_delete = array(
									"DELETE FROM " . CM_TABLE_PREFIX . "mod_clienti_fields WHERE ID_clienti = [ID_VALUE]" 
								);

$oRecord->addTab("daticliente");
$oRecord->setTabTitle("daticliente", "Dati Cliente");
$oRecord->addContent(null, true, "daticliente");
$oRecord->groups["daticliente"]["tab"] = "Dati Cliente";

$oRecord->addTab("recapiti");
$oRecord->setTabTitle("recapiti", "Recapiti");
$oRecord->addContent(null, true, "recapiti");
$oRecord->groups["recapiti"]["tab"] = "Recapiti";

$oRecord->addTab("contatti");
$oRecord->setTabTitle("contatti", "Contatti");
$oRecord->addContent(null, true, "contatti");
$oRecord->groups["contatti"]["tab"] = "Contatti";

$oRecord->addTab("test");
$oRecord->setTabTitle("test", "Test");
$oRecord->addContent(null, true, "test");
$oRecord->groups["test"]["tab"] = "Test";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "codice";
$oField->label = "Codice";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "referente";
$oField->label = "Referente";
$oField->widget = "activecomboex";
$oField->actex_update_from_db = true;
$oField->source_SQL = "SELECT
							ID
							, CONCAT(nome,' ',cognome) AS nome
						FROM
							" . CM_TABLE_PREFIX . "mod_clienti_contatti
						WHERE
							ID_clienti = " . $db->toSql(new ffData($_REQUEST["keys"]["ID"], "Number", FF_SYSTEM_LOCALE)) . "
						ORDER BY nome ASC
						";
$oField->resources[] = "clienti_contatti";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "ragsoc";
$oField->label = "Ragione Sociale";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "note";
$oField->label = "Note";
$oField->base_type = "Text";
$oField->control_type = "textarea";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_tipo";
$oField->label = "Tipo Cliente";
$oField->source_SQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_clienti_tipo ORDER BY tipo ASC";
$oField->actex_dialog_url = $cm->oPage->site_path . $cm->oPage->page_path . "/tipo/modify";
$oField->resources[] = "tipo_cliente";
$oField->actex_update_from_db = true;
$oField->widget = "activecomboex";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "tipo_azienda";
$oField->label = "Tipo Azienda";
$oField->required = true;
$oField->extended_type = "Selection";
$oField->multi_pairs = array(
								array(new ffData("privato"), new ffData("Privato"))
								,array(new ffData("dittaind"), new ffData("Ditta Individuale"))
								,array(new ffData("societa"), new ffData("Società"))
								,array(new ffData("estero"), new ffData("Estero"))
							);
$oField->properties["onchange"] = "jQuery.fn.aziende()";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "nome";
$oField->label = "Nome";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "cognome";
$oField->label = "Cognome";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "piva";
$oField->label = "P. IVA";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "cf";
$oField->label = "Codice Fiscale";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "indirizzo";
$oField->container_class = "indirizzo";
$oField->label = "Indirizzo";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "cap";
$oField->container_class = "cap";
$oField->label = "C.A.P.";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "citta";
$oField->container_class = "citta";
$oField->label = "Città";
$oField->widget = "autocomplete";
$oField->autocomplete_compare = "nome";
$oField->actex_update_from_db = true;
$oField->source_SQL = "SELECT comune.nome
						, comune.nome
					FROM comune
					[WHERE]
					[HAVING]
                    ORDER BY comune.nome
                    LIMIT 20";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "provincia";
$oField->container_class = "provincia";
$oField->label = "Provincia";
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT ID, Name FROM support_province ORDER BY Name";
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "nazione";
$oField->container_class = "nazione";
$oField->label = "Nazione";
$oField->extended_type = "Selection";
$oField->multi_select_one = false;
$oField->source_SQL = "SELECT ID, `desc` FROM support_countries ORDER BY `desc` ASC";
$oField->default_value = new ffData("112");
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono1";
$oField->label = "Telefono";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono2";
$oField->label = "Telefono 2";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "fax";
$oField->label = "Fax";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "fax2";
$oField->label = "Fax 2";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "cellulare1";
$oField->label = "Cellulare";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "cellulare2";
$oField->label = "Cellulare 2";
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "email1";
$oField->label = "E-mail";
$oField->addValidator("email");
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "email2";
$oField->label = "E-mail 2";
$oField->addValidator("email");
$oRecord->addContent($oField, "recapiti");

$oField = ffField::factory($cm->oPage);
$oField->id = "isPotenziale";
$oField->label = "Cliente Potenziale";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "reseller";
$oField->label = "Rivenditore";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "daticliente");

$oField = ffField::factory($cm->oPage);
$oField->id = "disabled";
$oField->label = "Disabilitato";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oRecord->addContent($oField, "daticliente");

mod_clienti_add_custom_fields($oRecord);

$cm->oPage->addContent($oRecord);

if(isset($_REQUEST["keys"]["ID"]))
{
	$oGrid = ffGrid::factory($cm->oPage);
	$oGrid->id = "GridContatti";
	$oGrid->title = "Contatti";
	$oGrid->source_SQL = "SELECT
							" . CM_TABLE_PREFIX . "mod_clienti_contatti.*
						FROM
							" . CM_TABLE_PREFIX . "mod_clienti_contatti
						WHERE ID_clienti = " . $db->toSql(new ffData($_REQUEST["keys"]["ID"], "Number", FF_SYSTEM_LOCALE)) . "
						[AND] [WHERE] [HAVING] [ORDER]
							";
	$oGrid->order_default = "nome";
	//$oGrid->full_ajax = true;
	$oGrid->record_id = "RecordContatti";
	$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/contatto";

	$oField = ffField::factory($cm->oPage);
	$oField->id = "ID_contact";
	$oField->data_source = "ID";
	$oField->base_type = "Number";
	$oGrid->addKeyField($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "nome";
	$oField->label = "Nome";
	$oGrid->addContent($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "cognome";
	$oField->label = "Cognome";
	$oGrid->addContent($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "telefono";
	$oField->label = "Telefono";
	$oGrid->addContent($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "cellulare";
	$oField->label = "Cellulare";
	$oGrid->addContent($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "email";
	$oField->label = "E-mail";
	$oGrid->addContent($oField);

	$oRecord->addContent($oGrid, "contatti");
	$cm->oPage->addContent($oGrid);
}


    $oRecord->addTab("geo");
    $oRecord->setTabTitle("geo", "Geo");
    $oRecord->addContent(null, true, "geo");
    $oRecord->groups["geo"] = array(
            "showtitle" => false
            , "title" => "Geo"
            , "tab" => "geo"
        );
		/*
	$oField = ffField::factory($cm->oPage);
	$oField->id = "coords_title";
	$oField->label = "Coordinate";
	$oRecord->addContent($oField, "geo");*/
	
	$db_coord = ffDB_Sql::factory();
		$sSQL_coord = "SELECT " . CM_TABLE_PREFIX . "mod_clienti_main.* 
			FROM " . CM_TABLE_PREFIX . "mod_clienti_main
			WHERE " . CM_TABLE_PREFIX . "mod_clienti_main.ID = " . $oRecord->db[0]->toSql($_REQUEST["keys"]["ID"]);
	$db_coord->query($sSQL_coord);
	
	if($db_coord->nextRecord()) {
			$coords = $db_coord->getField("coords_title", "Text", true);
		}

    $oField = ffField::factory($cm->oPage);
    $oField->id = "coords";
    $oField->label = "Geo Localization";
    $oField->container_class = "gmap-address";
    $oField->properties["style"]["width"] = "400px";
    $oField->properties["style"]["height"] = "200px";
    $oField->gmap_draggable = true;
    $oField->gmap_start_zoom = 10;
    $oField->widget = "gmap";
    $oField->gmap_key = "AIzaSyDkSo_W6j1cWYwKIpZhAxl5vqn9grXj7aA";
    $oField->gmap_force_search = true;
	
    	$oField->gmap_update_class = "indirizzo,citta,procincia,nazione";
   // $oField->gmap_update_class_prefix = "bill";
    $oRecord->addContent($oField, "geo"); 
    
    
function RecordClienti_on_do_action($oRecord, $action)
{
	$db = ffDB_Sql::factory();
	if($action == "insert" && strlen($oRecord->form_fields["piva"]->value->getValue())) {
		$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_clienti_main.* 
			FROM " . CM_TABLE_PREFIX . "mod_clienti_main
			WHERE " . CM_TABLE_PREFIX . "mod_clienti_main.piva = " . $db->toSql($oRecord->form_fields["piva"]->value->getValue());
		$db->query($sSQL);
		if($db->nextRecord()) {
			$oRecord->tplDisplayError("partita iva gia inserita");
			return true;
		}
	
	}


}

function RecordClienti_on_done_action($oRecord, $frmAction)
{
	$cm = cm::getInstance();

	$ID = $oRecord->key_fields["ID"]->value;
	$db = ffDb_Sql::factory();

	if (isset($cm->modules["clienti"]["fields"]) && count($cm->modules["clienti"]["fields"]))
	{
		switch ($frmAction)
		{
			case "insert":
				foreach ($cm->modules["clienti"]["fields"] as $key => $value)
				{
					$sSQL = "INSERT INTO
									" . CM_TABLE_PREFIX . "mod_clienti_fields (ID_clienti, field, value)
								VALUES
									(
									  " . $db->toSql($ID) . "
									, " . $db->toSql($key) . "
									, " . $db->toSql($oRecord->form_fields[$key]->value) . "
									)
							";
					$db->execute($sSQL);
				}
				break;

			case "update":
				foreach ($cm->modules["clienti"]["fields"] as $key => $value)
				{
					$sSQL = "UPDATE
									" . CM_TABLE_PREFIX . "mod_clienti_fields
								SET
									value = " . $db->toSql($oRecord->form_fields[$key]->value) . "
								WHERE
									ID_clienti = " . $db->toSql($ID) . "
									AND field = " . $db->toSql($key) . "
							";
					$db->execute($sSQL);

					if ($db->affectedRows() == 0)
					{
						$sSQL = "INSERT INTO
										" . CM_TABLE_PREFIX . "mod_clienti_fields (ID_clienti, field, value)
									VALUES
										(
										  " . $db->toSql($ID) . "
										, " . $db->toSql($key) . "
										, " . $db->toSql($oRecord->form_fields[$key]->value) . "
										)
								";
						$db->execute($sSQL);
					}
				}
				break;
		}
	}

	return FALSE;
}

function RecordClienti_on_check_fields($oRecord, $field, $frmAction)
{
	$tipo_azienda = $oRecord->form_fields["tipo_azienda"]->value->getValue();
	
	if($field->id == "piva" && $field->value->getValue() != "" && $tipo_azienda == "societa")
	{
		$validator = ffValidator::getInstance("piva");
		$rc = $validator->checkValue($field->value, $field->label, null);

		if ($rc === false)
			return null;
		else
			return $rc;
	}

	if($field->id == "cf" && $field->value->getValue() != "" && $tipo_azienda == "societa")
	{
		$validator = ffValidator::getInstance("piva");
		$rc = $validator->checkValue($field->value, $field->label, null);

		if ($rc === false)
			return null;
		else
			return $rc;
	}

	if($field->id == "cf" && $field->value->getValue() != "" && $tipo_azienda = "privato")
	{
		$validator = ffValidator::getInstance("cf");
		$rc = $validator->checkValue($field->value, $field->label, null);

		if ($rc === false)
			return null;
		else
			return $rc;
	}

	if($field->id == "cf" && $field->value->getValue() != "" && $tipo_azienda == "dittaind")
	{
		$validator = ffValidator::getInstance("cf");
		$rc = $validator->checkValue($field->value, $field->label, null);

		if ($rc === false)
			return null;
		else
			return $rc;
	}

	if($field->id == "piva" && $field->value->getValue() != "" && $tipo_azienda == "dittaind")
	{
		$validator = ffValidator::getInstance("piva");
		$rc = $validator->checkValue($field->value, $field->label, null);

		if ($rc === false)
			return null;
		else
			return $rc;
	}
}
   
