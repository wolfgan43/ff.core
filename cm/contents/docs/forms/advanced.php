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

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Select 2 (actex)";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Date Range Picker";
$oRecord->description = "A JavaScript component for choosing date ranges, dates and times.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Switch";
$oRecord->description = "Here are a few types of switches.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);


$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Input Masks";
$oRecord->description = "A jQuery Plugin to make masks on form fields and HTML elements.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);


$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Bootstrap Touchspin";
$oRecord->description = "A mobile and touch friendly input spinner component for Bootstrap. Specify attribute <code>data-toggle=\"touchspin\"</code> and your input would be conveterted into touch friendly spinner.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Bootstrap maxlength";
$oRecord->description = "Uses the HTML5 attribute \"maxlength\" to work. Just specify <code>data-toggle=\"maxlength\"</code> attribute to have maxlength indication on any input.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->title = "Timepicker";
$oRecord->description = "Easily select a time for a text input using your mouse or keyboards arrow keys. Specify attribute <code>data-toggle=\"timepicker\"</code> and you would have nice timepicker input element.";
$oRecord->framework_css["component"]["header_wrap"] = false;

$cm->oPage->addContent($oRecord);

