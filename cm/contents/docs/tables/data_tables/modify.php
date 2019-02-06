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
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = true;
//$oRecord->framework_css["component"]["type"] = null;
$oRecord->setWidthComponent(array(6));
//$oRecord->tab = "right";
/**
 * ID dell'oggetto.
 * Se questo oggetto è in relazione con un altro (modifica) è importante che questo campo coincida
 * con il record_ID del campo originale
 */
//$oRecord->id = "UsersModify";
/**
 * resources è un array che viene popolato con gli ID degli oggetti su cui si sta lavorando
 */
$oRecord->resources[] = $oRecord->id;
/**
 * Titolo dell'oggetto record
 */
$oRecord->title = ffTemplate::_get_word_by_code("user_modify");
$oRecord->description = ffTemplate::_get_word_by_code("user_modify");
/**
 * Tabella da cui vengono prese e/o salvate le informazioni.
 * Può essere dichiarata una sola tabella, se i dati arrivano da più fonti la gestione
 * verrà delegata negli eventi
 */
$oRecord->src_table = "access_users";
$oRecord->insert_additional_fields = array(
    "created" => new ffData(time(), "Number")
);
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

$oField = ffField::factory($cm->oPage);
$oField->id = "avatar";
/**
 * Dichiaro la classe che conterrà il campo (è opzionale)
 */
$oField->container_class = "avatar";
$oField->label = ffTemplate::_get_word_by_code("avatar");
$oField->extended_type = "File";
$oField->widget = "uploadifive";
$oRecord->addContent($oField, "Avatar");

$oField = ffField::factory($cm->oPage);
$oField->id = "username";
$oField->label = ffTemplate::_get_word_by_code("username");
/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->required = true;
$oRecord->addContent($oField, "Altro");

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = ffTemplate::_get_word_by_code("email");
$oField->addValidator("email");
/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->required = true;
$oField->setWidthComponent(array(9,8,7,6));
$oRecord->addContent($oField, "Altro");

$oField = ffField::factory($cm->oPage);
$oField->id = "tel";
$oField->label = ffTemplate::_get_word_by_code("tel");

/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->setWidthComponent(array(9,8,7,6));
$oRecord->addContent($oField, "Altro");


$oField = ffField::factory($cm->oPage);
$oField->id = "username2";
$oField->label = ffTemplate::_get_word_by_code("username");
/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->required = true;
$oRecord->addContent($oField, "Altro");

$oField = ffField::factory($cm->oPage);
$oField->id = "username3";
$oField->label = ffTemplate::_get_word_by_code("username");
/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->required = true;
$oRecord->addContent($oField, "Altro2");



$oField = ffField::factory($cm->oPage);
$oField->id = "username3";
$oField->label = ffTemplate::_get_word_by_code("username");
/**
 * Indica l'obbligatorietà  del campo in questione,
 * se non compilato restituirà  un errore
 */
$oField->required = true;
$oRecord->addContent($oField);


$oRecord->addGroup(array(
    "title" => "Altro"
    , "width" => array(6)
));


$oRecord->addGroup("Altro2", array(
    "title" => "cia"
    , "tab" => "Altro"
    , "width" => array(6)
    //, "fixed_pre_content" => "asdasd"
    //, "fixed_post_content" => "asdasd"
    , "description" => "adpoasdmiopasdmiop op "
));

/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord);