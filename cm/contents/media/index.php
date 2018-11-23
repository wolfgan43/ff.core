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

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->full_ajax = true;
$oGrid->id = "Media";
$oGrid->title = ffTemplate::_get_word_by_code("media");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "media.`ID`
                        , " . CM_TABLE_PREFIX . "media.`path` AS real_path
                        , CONCAT(" . CM_TABLE_PREFIX . "media.`path`, '<br />', IF(" . CM_TABLE_PREFIX . "media.`permalink`, CONCAT('<b>Permalink</b>: ', '" . CM_SHOWFILES . "', " . CM_TABLE_PREFIX . "media.`permalink`), '')) AS path
                        , " . CM_TABLE_PREFIX . "media.`key`
                        , GROUP_CONCAT(CONCAT('<b>', " . FF_PREFIX . "languages.description, '</b>: ', " . CM_TABLE_PREFIX . "media.`title`) SEPARATOR '<br />') AS title 
                        , GROUP_CONCAT(CONCAT('<b>', " . FF_PREFIX . "languages.description, '</b>: ', " . CM_TABLE_PREFIX . "media.`description`) SEPARATOR '<br />') AS description
                      FROM " . CM_TABLE_PREFIX . "media
                        LEFT JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "media.ID_lang  
                      [WHERE]
                       GROUP BY " . CM_TABLE_PREFIX . "media.`path`, " . CM_TABLE_PREFIX . "media.`key`
                       [ORDER]";
$oGrid->order_default = "path";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "MediaModify";
$oGrid->resources[] = $oGrid->record_id;

// Campo chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi visualizzazione
$oField = ffField::factory($cm->oPage);
$oField->id = "cover";
$oField->label = ffTemplate::_get_word_by_code("media_cover");
$oField->data_source = "real_path";
$oField->control_type = "picture";
//$oField->file_storing_path = FF_DISK_UPDIR;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "path";
$oField->label = ffTemplate::_get_word_by_code("media_path");
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "key";
$oField->label = ffTemplate::_get_word_by_code("media_key");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("media_title");
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = ffTemplate::_get_word_by_code("media_description");
$oField->encode_entities = false;
$oGrid->addContent($oField);


$cm->oPage->addContent($oGrid);
