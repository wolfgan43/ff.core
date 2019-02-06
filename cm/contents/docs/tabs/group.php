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

$content1 = nl2br("Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.

Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.");

$content2 = nl2br("Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.

Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.");

$content3 = nl2br("Culpa dolor voluptate do laboris laboris irure reprehenderit id incididunt duis pariatur mollit aute magna pariatur consectetur. Eu veniam duis non ut dolor deserunt commodo et minim in quis laboris ipsum velit id veniam. Quis ut consectetur adipisicing officia excepteur non sit. Ut et elit aliquip labore Leggings enim eu. Ullamco mollit occaecat dolore ipsum id officia mollit qui esse anim eiusmod do sint minim consectetur qui.");

/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = true;
//$oRecord->framework_css["component"]["type"] = null;
//$oRecord->setWidthComponent(array(6));
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
$oRecord->title = "DEFAULT TABS";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");


/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 1");

/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "pills";
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
$oRecord->title = "TABS FILLED";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 2");


/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "pills-justified";
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
$oRecord->title = "TABS JUSTIFIED";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 2");



/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "left";
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
$oRecord->title = "TABS VERTICAL LEFT";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 3");




/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "right";
//$oRecord->framework_css["component"]["type"] = null;
$oRecord->setWidthComponent(array(6));
//$oRecord->tab = "right";
/**
 * ID dell'oggetto.
 * Se questo oggetto è in relazione con un altro (modifica) è importante che questo campo coincida
 * con il record_ID del campo originale
 */
//$oRecord->id = "UsersModify2";
/**
 * resources è un array che viene popolato con gli ID degli oggetti su cui si sta lavorando
 */
$oRecord->resources[] = $oRecord->id;
/**
 * Titolo dell'oggetto record
 */
$oRecord->title = "TABS VERTICAL RIGHT";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 3");


/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "bordered";
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
$oRecord->title = "TABS BORDERED";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 3");



/**
 * Inizializzazione dell'oggetto ffRecord,
 * $oRecord è lo standard, ma può essere usato qualsiasi nome,
 * purchè sia rispettata la coerenza in seguito
 */
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->tab = "bordered-justified";
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
$oRecord->title = "TABS BORDERED JUSTIFIED";

$oRecord->addContent($content1, "Tab1");
$oRecord->addContent($content2, "Tab2");
$oRecord->addContent($content3, "Tab3");



/**
 * Viene innestato l'oggetto $oRecord all'interno della pagina
 */
$cm->oPage->addContent($oRecord, "Group 3");

$cm->oPage->addGroup("Group 3", array(
    "title" => false
));