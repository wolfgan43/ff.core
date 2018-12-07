<?php
/**
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

/**
 * Inizializzazione dell'oggetto ffGrid,
 * $oGrid è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */

$oGrid = ffGrid::factory($cm->oPage);
/**
 * ID della griglia, deve essere univoco nella pagina
 */
$oGrid->id = "Users";
$oGrid->full_ajax = true;
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
//$oGrid->records_per_page = 10;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "UsersModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->use_alpha = true;
$oGrid->records_per_page = 10;
$oGrid->display_edit_bt = true;
$oGrid->setWidthComponent(6);
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
$oField->label = ffTranslator::get_word_by_code("username");
$oGrid->addSearchField($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "avatar";
$oField->label = ffTemplate::_get_word_by_code("avatar");
$oField->extended_type = "File";
/**
 * Percorso in cui verrà salvato il file
 */
//$oField->file_storing_path = FF_DISK_UPDIR . "/anagraph/[ID_VALUE]";
/**
 * Percorso in cui verrà salvato il file temporaneo
 */
//$oField->file_temp_path = FF_DISK_UPDIR . "/anagraph";
//$oField->file_thumb = "100x100";
$oField->control_type = "picture";
$oGrid->addContent($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "username";
/**
 * Il nome con cui si vuole che il campo sia visualizzato
 */
$oField->label = ffTemplate::_get_word_by_code("username");
$oField->base_type = "Text";
/**
 * Viene aggiunto il campo(visibile) alla tabella
 */
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = ffTemplate::_get_word_by_code("email");
$oField->base_type = "Text";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "created";
$oField->label = ffTemplate::_get_word_by_code("created");
$oField->base_type = "Timestamp";
$oField->extended_type = "DateTime";
$oField->app_type = "DateTime";
$oGrid->addContent($oField);

/**
 * Inserisce la struttura all'interno di un tab, dichiarandone la label
 */
$cm->oPage->addContent($oGrid);

