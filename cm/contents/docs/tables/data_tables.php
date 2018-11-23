<?php

/**
 * Inizializzazione dell'oggetto ffGrid,
 * $oGrid è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oGrid = ffGrid::factory($cm->oPage);
/**
 * ID della griglia, deve essere univoco nella pagina
 */
$oGrid->id = "utenti_semplice";
/**
 * La query utilizzata per recuperare i dati da visualizzare.
 * Possono essere anche più tabelle in join.
 * [AND] [WHERE] [HAVING] [ORDER]) devono essere lasciati,
 * perche vengono automaticamente popolati dai parametri della grid
 */
$oGrid->source_SQL = "SELECT *
						FROM access_users
						[WHERE] [HAVING] [ORDER]";
/**
 * Il campo rispetto al quale ordinare i dati estratti della tabella.
 * Deve essere uno dei campi dichiarati in tabella.
 */
$oGrid->order_default = "username";
/**
 * Nasconde il tasto add_new
 */
$oGrid->display_new = true;
/**
 * Nasconde il campo di ricerca
 */
$oGrid->use_search = true;
/**
 * Nasconde il campo "esporta"
 */
$oGrid->use_paging = true;

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
 * Serve per la visualizzazione e per la modifica dei dati
 */
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "username";
/**
 * Il nome con cui si vuole che il campo sia visualizzato
 */
$oField->label = ffTemplate::_get_word_by_code("utenti_username");
$oField->base_type = "Text";
/**
 * Viene aggiunto il campo(visibile) alla tabella
 */
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = ffTemplate::_get_word_by_code("utenti_email");
$oField->base_type = "Text";
$oGrid->addContent($oField);

/**
 * Inserisce la struttura all'interno di un tab, dichiarandone la label
 */
$cm->oPage->addContent($oGrid);

