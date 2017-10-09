<?php

$cm->oPage->tplAddJs("tipsy", "tipsy/jquery.tipsy.js", "/themes/" . $cm->oPage->getTheme() . "/javascript");
$cm->oPage->tplAddCss("tipsy", "tipsy/stylesheets/tipsy.css", "/themes/" . $cm->oPage->getTheme() . "/javascript");

$db_number = ffDB_Sql::factory();

$sSQL_number = "SELECT
	   cm_mod_clienti_contatti.ID
	   , (SELECT COUNT(*)
		FROM
		 cm_mod_clienti_contatti
	   ) AS clienti
	   FROM
		cm_mod_clienti_contatti
	   ORDER BY ID DESC
	   LIMIT 0, 1
			";
					
$db_number->query($sSQL_number);
if($db_number->nextRecord())
{
	$clienti = $db_number->getField("clienti")->getValue();
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "GridContatti";
$oGrid->title = "Contatti (".$clienti.")";
$oGrid->source_SQL = "SELECT
						" . CM_TABLE_PREFIX . "mod_clienti_contatti.*
						,CONCAT(" . CM_TABLE_PREFIX . "mod_clienti_contatti.nome, ' '," . CM_TABLE_PREFIX . "mod_clienti_contatti.cognome) AS contatto
						," . CM_TABLE_PREFIX . "mod_clienti_main.ragsoc AS cliente
					FROM " . CM_TABLE_PREFIX . "mod_clienti_contatti
					LEFT JOIN " . CM_TABLE_PREFIX . "mod_clienti_main
						ON " . CM_TABLE_PREFIX . "mod_clienti_contatti.ID_clienti = " . CM_TABLE_PREFIX . "mod_clienti_main.ID
					[WHERE]
					[HAVING]
					ORDER BY contatto ASC
					[COLON]
					[ORDER]
					";
$oGrid->record_id = "RecordContatti";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->order_default = "ID";
$oGrid->resources[] = "clienti_contatti";
$oGrid->resources[] = "contatti";
$oGrid->addEvent("on_before_parse_row", "HostingGrid_on_before_parse_row");
//$oGrid->full_ajax = true;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "contatto";
$oField->label = "Nome e Cognome";
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "note";
$oField->label = "Note";
$oField->display = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cliente";
$oField->label = "Cliente";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "cellulare";
$oField->label = "Cellulare";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = "E-mail";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_clienti";
$oField->label = "Cliente";
$oField->extended_type = "Selection";
$oField->multi_select_one_label = "Tutti";
$oField->source_SQL = "SELECT ID, ragsoc FROM " . CM_TABLE_PREFIX . "mod_clienti_main ORDER BY ragsoc ASC";
$oGrid->addSearchField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "contatto";
$oField->label = "Contatto";
//$oField->extended_type = "Selection";
$oField->src_having = true;
//$oField->src_table = CM_TABLE_PREFIX . "mod_clienti_contatti";
//$oField->base_type = "Number";
//$oField->multi_select_one_label = "Tutti";
//$oField->source_SQL = "SELECT ID, CONCAT(nome, ' ', cognome) AS contatto FROM " . CM_TABLE_PREFIX . "mod_clienti_contatti ORDER BY contatto ASC";
$oField->src_prefix = "%";
$oField->src_postfix = "%";
$oField->src_operation = "[NAME] LIKE [VALUE]";
$oGrid->addSearchField($oField);


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

function HostingGrid_on_before_parse_row($oGrid)
{	
	$cm = cm::getInstance();	
	$nota = $oGrid->grid_fields["note"]->getValue();
	$name = $oGrid->grid_fields["contatto"]->getValue();

	$img_enabled = '<img src="' . FF_SITE_PATH . '/themes/xnmadmin/javascript/tipsy/images/note-on.png" />';
	$img_disabled = '<img src="' . FF_SITE_PATH . '/themes/xnmadmin/javascript/tipsy/images/note-off.png" />';

	if (strlen($nota))
		$oGrid->grid_fields["contatto"]->setValue('&nbsp;<a class="tipsyb" href="#" original-title="' . $nota . '">' . $img_enabled . '</a>' . $name);
}