<?php

$cm->oPage->tplAddJs("tipsy", "tipsy/jquery.tipsy.js", "/themes/" . $cm->oPage->getTheme() . "/javascript");
$cm->oPage->tplAddCss("tipsy", "tipsy/stylesheets/tipsy.css", "/themes/" . $cm->oPage->getTheme() . "/javascript");

$db_number = ffDB_Sql::factory();

$sSQL_number = "SELECT
	   cm_mod_clienti_main.ID
	   , (SELECT COUNT(*)
		FROM
		 cm_mod_clienti_main
		WHERE 
		 disabled = 0
		 AND isPotenziale != 1
	   ) AS clienti
	   , (SELECT COUNT(*)
		FROM
		 cm_mod_clienti_main
		WHERE 
		 isPotenziale = 1
		 AND
		 disabled != 1
	   ) AS potenziali
	   , (SELECT COUNT(*)
		FROM
		 cm_mod_clienti_main
		WHERE 
		 disabled = 1
	   ) AS disabilitati
	   , (SELECT COUNT(*)
		FROM
		 cm_mod_clienti_main
		WHERE 
		 reseller = 1
		 AND
		 disabled != 1
	   ) AS rivenditori
	   FROM
		cm_mod_clienti_main
	   ORDER BY ID DESC
	   LIMIT 0, 1
			";
					
$db_number->query($sSQL_number);
if($db_number->nextRecord())
{
	$clienti = $db_number->getField("clienti")->getValue();
	$potenziali = $db_number->getField("potenziali")->getValue();
	$disabilitati = $db_number->getField("disabilitati")->getValue();
	$rivenditori = $db_number->getField("rivenditori")->getValue();
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "GridClienti";
$oGrid->title = "Clienti";
$oGrid->description = "Clienti (".$clienti."), potenziali (".$potenziali."), disabilitati (".$disabilitati."), rivenditori (".$rivenditori.")";
$oGrid->source_SQL = "SELECT
						" . CM_TABLE_PREFIX . "mod_clienti_main.*
					FROM
						" . CM_TABLE_PREFIX . "mod_clienti_main
					[WHERE]
					[HAVING]
					[ORDER]
					";
//$oGrid->full_ajax = TRUE;
$oGrid->record_id = "RecordClienti";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->order_default = "ID";
$oGrid->addEvent("on_before_parse_row", "GridClienti_on_before_parse_row");
$oGrid->addEvent("on_do_action", "GridClienti_on_do_action");
$oGrid->resources[] = "cm_mod_clienti_main";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oField->order_SQL = " ragsoc ASC";
$oGrid->addKeyField($oField);

if (MOD_CLIENTI_CODICE)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "codice";
    $oField->label = "Codice";
    $oGrid->addContent($oField);
}
if (MOD_CLIENTI_RAGSOC)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "ragsoc";
	$oField->label = "Ragione Sociale";
	$oField->encode_entities = false;
	$oGrid->addContent($oField);
}

if (MOD_CLIENTI_NOMECOGNOME)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "nome";
	$oField->label = "Nome";
	$oGrid->addContent($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "cognome";
	$oField->label = "Cognome";
	$oGrid->addContent($oField);
}

$oField = ffField::factory($cm->oPage);
$oField->id = "note";
$oField->label = "Note";
$oField->display = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "piva";
$oField->label = "P. IVA";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cf";
$oField->label = "Codice Fiscale";
$oGrid->addContent($oField);

if (MOD_CLIENTI_INDIRIZZO)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "indirizzo";
	$oField->label = "Indirizzo";
	$oGrid->addContent($oField);
}

if (MOD_CLIENTI_CAP)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "cap";
	$oField->label = "C.A.P.";
	$oGrid->addContent($oField);
}

if (MOD_CLIENTI_CITTA)
{
$oField = ffField::factory($cm->oPage);
$oField->id = "citta";
$oField->label = "Città";
$oGrid->addContent($oField);
}

if (MOD_CLIENTI_PROVINCIA)
{
	$oField = ffField::factory($cm->oPage);
	$oField->id = "provincia";
	$oField->label = "Provincia";
	$oField->extended_type = "Selection";
	$oField->multi_select_one = false;
	$oField->source_SQL = "SELECT ID, Name FROM support_province ORDER BY Name";
	$oGrid->addContent($oField);
}

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono1";
$oField->label = "Telefono 1";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "fax";
$oField->label = "FAX";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cellulare1";
$oField->label = "Cellulare 1";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email1";
$oField->label = "E-mail 1";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ragsoc";
$oField->label = "Cliente";
$oField->src_operation = "[NAME] LIKE [VALUE]";
$oField->src_prefix = "%";
$oField->src_postfix = "%";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "provincia";
$oField->label = "Provincia";
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT ID, Name FROM support_province ORDER BY Name";
$oField->multi_select_one_label = "Tutte";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "citta";
$oField->label = "Città";
$oField->src_operation = "[NAME] LIKE [VALUE]";
$oField->src_prefix = "%";
$oField->src_postfix = "%";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono1";
$oField->label = "Telefono";
$oField->src_operation = "[NAME] LIKE [VALUE]";
$oField->src_fields = array("telefono1", "cellulare1");
$oField->src_prefix = "%";
$oField->src_postfix = "%";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "disabled";
$oField->label = "Disabilitato";
$oField->control_type = "checkbox";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("0");
$oField->checked_value = new ffData("1");
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "isPotenziale";
$oField->label = "Potenziale";
$oField->control_type = "checkbox";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("");
$oField->checked_value = new ffData("1");
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "reseller";
$oField->label = "Rivenditore";
$oField->control_type = "checkbox";
$oField->extended_type = "Boolean";
$oField->unchecked_value = new ffData("");
$oField->checked_value = new ffData("1");
$oGrid->addSearchField($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "cliente";
$oButton->label = "Cliente";
$oButton->action_type = "submit";
$oButton->frmAction = "cliente@[ID_VALUE]";
$oGrid->addGridButton($oButton);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "exports";
$oButton->class = "noactionbuttons";
$oButton->label = "Esporta";
$oButton->frmAction = "export";
$oGrid->addActionButton($oButton);

$cm->oPage->addContent($oGrid);

$js = <<<EOD
	<script type="text/javascript">
		jQuery(function() {
			jQuery(".tipsyb").tipsy({gravity: 'w'});
		});
		function applytipsy() {
			jQuery(".tipsyb").tipsy({gravity: 'w'});
		}
		ff.ajax.addEvent({
			"event_name" : "onEmptyQueue"
			, "func_name" : applytipsy
		});
		

		
	</script>
EOD;
$cm->oPage->addContent($js);

function GridClienti_on_before_parse_row($oGrid)
{
	if ($oGrid->db[0]->getField("isPotenziale")->getValue() == "0" || $oGrid->db[0]->getField("isPotenziale")->getValue() == "")
	{
		$oGrid->grid_buttons["cliente"]->visible = false;
	}
	else if($oGrid->db[0]->getField("isPotenziale")->getValue() == "1")
	{
		$oGrid->grid_buttons["cliente"]->visible = true;
	}
    $nota = $oGrid->grid_fields["note"]->getValue();
	$name = $oGrid->grid_fields["ragsoc"]->getValue();

	$img_enabled = '<img src="' . FF_SITE_PATH . '/themes/xnmadmin/javascript/tipsy/images/note-on.png" />';
	$img_disabled = '<img src="' . FF_SITE_PATH . '/themes/xnmadmin/javascript/tipsy/images/note-off.png" />';

	if (strlen($nota))
		$oGrid->grid_fields["ragsoc"]->setValue('&nbsp;<a class="tipsyb" href="#" original-title="' . $nota . '">' . $img_enabled . '</a>' . $name);
}

function GridClienti_on_do_action($oGrid, $frmAction)
{
	$db = ffDB_Sql::factory();

	if($frmAction == "export")
	{
		$db = ffDB_Sql::factory();
		
		$sSQL = $oGrid->processed_SQL;
		$db->query($sSQL);
		if($db->nextRecord())
		{
			$res = true;
			if(!is_dir(FF_DISK_PATH . "/uploads/mod_clienti/clienti"))
				$res = @mkdir(FF_DISK_PATH . "/uploads/mod_clienti/clienti", 0777, true);

			if($res) {
				$filename = FF_DISK_PATH . "/uploads/mod_clienti/clienti/export.csv";
				$handle = fopen($filename,"w+");

				$export = "";

				if(is_array($db->fields) && count($db->fields)) {
					foreach($db->fields AS $key => $value) {
						if(strlen($key)) {
							if(strlen($export))
								$export .= "\t";
							
							$sub_export .= '"' . str_replace("\t", "\\t", $key) . '"';	
						}
					}
				}

				do
				{
					$sub_export = "";

					if(is_array($db->fields) && count($db->fields)) {
						foreach($db->fields AS $key => $value) {
							if(strlen($key)) {
								if(strlen($sub_export))
									$sub_export .= "\t";
								
								$sub_export .= '"' . str_replace("\t", "\\t", $db->getField($key, "Text", true)) . '"';	
							}
						}
					}

					$sub_export = str_replace("\n", "\\n", $sub_export);
					$sub_export = str_replace("\r", "", $sub_export);
					
					$export .= $sub_export . "\n";

				} while ($db->nextRecord());

				fwrite($handle, $export);
				fclose($handle);

				output_header(FF_DISK_PATH . "/uploads/mod_clienti/clienti/export.csv", "attachment");
				readfile(FF_DISK_PATH . "/uploads/mod_clienti/clienti/export.csv");
				die();

			}
		}
	} else {
		$action = explode("@", $frmAction);

		if ($action[0] == "cliente")
		{
			$sSQL = "UPDATE
						" . CM_TABLE_PREFIX . "mod_clienti_main
					SET isPotenziale = 0
					WHERE ID = " . $db->toSql($action[1]) . "
					";
			$db->execute($sSQL);
		}
	}
}
