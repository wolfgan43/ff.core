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
$oRecord->addContent('<hr /><h2 class="' . cm_getClassByFrameworkCss(array(12), "col") . '">Campo di selezione (Selection)</h2>', $group_field);

$oField = ffField::factory($cm->oPage);
$oField->id="utenti";

$oField->extended_type = "Selection"; //Questa istruzione dirà che questo è un campo speciale, che nel nostro caso si chiamerà "Selection"
/*
 * Nella source_SQL va inserita una SELECT che selezionerà solamente due campi
 * Il primo sarà quello salvato nel database
 * Il secondo sarà quello visualizzato dall'utente
 */
$oField->source_SQL = "  SELECT ID, username FROM cm_mod_security_users
                          WHERE 1 
                          [AND] [WHERE] [HAVING] [ORDER]";

/*
 * Questa istruzione imposta un campo extra che avrà, di default, valore nullo.
 * Di base è impostata a true
 */
$oField->multi_select_one = true;

/*
 * Questa istruzione imposta la scritta che vogliamo sul campo extra.
 * Di base è "Selezionare Un Elemento.."
 */
$oField->multi_select_one_label = "Selezionare Un Elemento..";

/*
 * Questa istruzione istruzione imposta un valore per il campo extra
 * N.B. Il valore deve essere di tipo ffData!
 * Il valore di defaul è null
 */
$oField->multi_select_one_val = new ffData(0);

$oField->base_type = "Text";
$oField->label = "Seleziona utente:";
$oRecord->addContent($oField, $group_field);

$code = '<div class="' . cm_getClassByFrameworkCss(array(6), "col") . '">
	<code>
		$oField = ffField::factory($cm->oPage); <br />
        $oField->id="utenti"; <br />
        <br />
        /*Questa istruzione dirà che questo è un campo più complesso, che nel nostro caso si chiamerà "Selection"*/ <br />
        $oField->extended_type = "Selection"; <br /> 
        <br />
        /* <br />
         * Nella source_SQL va inserita una SELECT che selezionerà solamente due campi <br />
         * Il primo sarà quello salvato nel database <br />
         * Il secondo sarà quello visualizzato dall\'utente <br />
         */ <br />
        $oField->source_SQL = "  SELECT ID, username FROM cm_mod_security_users <br />
                                  WHERE 1 <br />
                                  [AND] [WHERE] [HAVING] [ORDER]"; <br />
        <br />
        /* <br />
         * Questa istruzione imposta un campo extra che avrà, di default, valore nullo. <br />
         * Di base è impostata a true <br />
         */ <br />
        $oField->multi_select_one = true; <br />
        <br />
        /* <br />
         * Questa istruzione imposta la scritta che vogliamo sul campo extra. <br />
         * Di base è "Selezionare Un Elemento.." <br />
         */ <br />
        $oField->multi_select_one_label = "Selezionare Un Elemento.."; <br />
        
        /* <br />
         * Questa istruzione istruzione imposta un valore per il campo extra <br />
         * N.B. Il valore deve essere di tipo ffData! <br />
         * Il valore di defaul è null <br />
         */ <br />
        $oField->multi_select_one_val = new ffData(0); <br />
        <br />
        $oField->base_type = "Text"; <br />
        $oField->label = "Seleziona utente:"; <br />
        $oRecord->addContent($oField, $group_field); <br />
	</code>
</div>';
$oRecord->addContent($code, $group_field);

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