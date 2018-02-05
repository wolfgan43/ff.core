<?php
$db = ffDB_Sql::factory(); 
/** 
 * Inizializzazione dell'oggetto ffRecord, 
 * $oRecord è lo standard, ma può essere usato qualsiasi nome, 
 * purchè sia rispettata la coerenza in seguito 
*/
$oRecord = ffRecord::factory($cm->oPage);
/** 
 * ID dell'oggetto. 
 * Se questo oggetto è in relazione con un altro (modifica) è importante che questo campo coincida 
 * con il record_ID del campo originale 
 */
$oRecord->id = "UtentiModify";
/** 
 * resources è un array che viene popolato con gli ID degli oggetti su cui si sta lavorando
 */
$oRecord->resources[] = $oRecord->id;
/**
 * Impedisce che le modifiche effettuate in questa zona di test diventino effettive
 */
$oRecord->skip_action = true;
$oRecord->hide_all_controls = true;
/**
 * Titolo dell'oggetto record 
 */
//$oRecord->title = ffTemplate::_get_word_by_code("utenti_modify");
/**
 * Tabella da cui vengono prese e/o salvate le informazioni.
 * Può essere dichiarata una sola tabella, se i dati arrivano da più fonti la gestione
 * verrà delegata negli eventi
 */
$oRecord->src_table = "cm_mod_security_users";

/**
 * Aggiunta dei tab
 */
$oRecord->tab = true;
/** 
 * Inizializzazione dell'oggetto ffField, 
 * elemento base di tutte le sovrastrutture del framework (grid, record e detail) 
 */
$oField = ffField::factory($cm->oPage);
/** 
 * ID del field, deve essere univoco all'interno di un oggetto 
 */
$oField->id = "ID";
/** 
 * Tipo del dato, 
 * se non espresso si sottointende Text 
 */
$oField->base_type = "Number";
/** 
 * Viene dichiarato il campo chiave (possono essere più di uno), 
 * non sono visibili all'interno della tabella (se si vuole vedere questo dato bisogna ridichiararlo sotto). 
 * Nel caso sia un record in modifica è importante abbia un nome coerente con quello del campo chiave in visualizzazione. 
*/
$oRecord->addKeyField($oField);


/**
 * Campi di base
 */
$group_field = "base";
$oRecord->addContent(null, true, $group_field);
$oRecord->groups[$group_field] = array(
	"title" => ucfirst($group_field)
);

$oRecord->addContent('<hr /><h2 class="' . cm_getClassByFrameworkCss(array(12), "col") . '">Campo Semplice</h2>', $group_field);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = "utenti name"; 	//ffTemplate::_get_word_by_code("name");
$oField->base_type = "Text";
$oField->store_in_db = false;
$oField->setWidthComponent(6);
$oRecord->addContent($oField, $group_field);

$code = '<div class="' . cm_getClassByFrameworkCss(array(6), "col") . '">
	<code>
		$oField = ffField::factory($cm->oPage);  													<br />
		<br />/* Required */																		<br />
		$oField->id = "name"; 																		<br />
		<br />/* Optional */																		<br />
		$oField->label = "Nome"; 						// ffTemplate::_get_word_by_code("name"); 	<br />
		$oField->base_type = "Text"; 																<br />
		<br />
		$oRecord->addContent($oField);
	</code>
</div>';
$oRecord->addContent($code, $group_field);

$oRecord->addContent('<hr /><h2 class="' . cm_getClassByFrameworkCss(array(12), "col") . '">Campo Semplice</h2>', $group_field);

$oField = ffField::factory($cm->oPage);
$oField->id = "name2";
$oField->label = "Nome"; 	//ffTemplate::_get_word_by_code("name");
$oField->base_type = "Text";
$oField->setWidthLabel(6);
$oField->store_in_db = false;
$oField->setWidthComponent(6);
$oRecord->addContent($oField, $group_field);

$code = '<div class="' . cm_getClassByFrameworkCss(array(6), "col") . '">
	<code>
		$oField = ffField::factory($cm->oPage);  													<br />
		<br />/* Required */																		<br />
		$oField->id = "name"; 																		<br />
		<br />/* Optional */																		<br />
		$oField->label = "Nome"; 				// ffTemplate::_get_word_by_code("name"); 			<br />
		$oField->base_type = "Text"; 																<br />
		<br />
		$oRecord->addContent($oField);
	</code>
</div>';
$oRecord->addContent($code, $group_field);

/**
 * Campi upload
 */
$group_field = "upload";
$oRecord->addContent(null, true, $group_field);
$oRecord->groups[$group_field] = array(
	"title" => ucfirst($group_field)
);

$oRecord->addContent('<hr /><h2 class="' . cm_getClassByFrameworkCss(array(12), "col") . '">Upload Semplice</h2>', $group_field);

$oField = ffField::factory($cm->oPage);
$oField->id = "avatar";
$oField->container_class = "avatar_uploadifive";
$oField->label = ffTemplate::_get_word_by_code("uploadifive");
$oField->base_type = "Text";
$oField->extended_type = "File";
$oField->file_storing_path = DISK_UPDIR . "/docs/record";
$oField->file_temp_path = DISK_UPDIR . "/tmp/docs";
$oField->file_max_size = 1000000;
$oField->file_show_filename = true;
$oField->file_full_path = true;
$oField->file_check_exist = true;
$oField->file_normalize = true;
$oField->file_show_preview = true;
$oField->file_saved_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/[_FILENAME_]";
$oField->file_saved_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/avatar/[_FILENAME_]";
$oField->control_type = "file";
$oField->file_show_delete = true;
$oField->widget = "uploadifive";
$oField->store_in_db = false;
$oField->setWidthComponent(6);
$oRecord->addContent($oField, $group_field);

$code = '<div class="' . cm_getClassByFrameworkCss(array(6), "col") . '">
	<code>
		
	</code>
</div>';
$oRecord->addContent($code, $group_field);


/**
 * Campi combo
 */

$group_field = "combo";
$oRecord->addContent(null, true, $group_field);
$oRecord->groups[$group_field] = array(
	"title" => ucfirst($group_field)
);

/**
 * Campi con widget
 */
$group_field = "widgets";
$oRecord->addContent(null, true, $group_field);
$oRecord->groups[$group_field] = array(
	"title" => ucfirst($group_field)
);


/** 
 * Viene innestato l'oggetto $oRecord all'interno della pagina 
 */
$cm->oPage->addContent($oRecord);