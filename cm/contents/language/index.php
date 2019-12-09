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
$oGrid->id = "Language";
$oGrid->title = "Lingue";
$oGrid->source_SQL = "SELECT ff_languages.*
                      FROM ff_languages  
                      [WHERE]
                       [ORDER]";
$oGrid->order_default = "description";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "LanguageModify";
$oGrid->addEvent("on_before_parse_row", "Lang_on_before_parse_row");
$oGrid->resources[] = $oGrid->record_id;

// Campo chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi visualizzazione
$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->order_dir = "DESC";
$oField->label = "Lingua";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "code";
$oField->label = "code";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tiny_code";
$oField->label = "Tiny code";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "iso6391";
$oField->label = "ISO6391";
$oGrid->addContent($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "status";
$oButton->action_type = "gotourl";
$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";
$oGrid->addGridButton($oButton);

$cm->oPage->addContent($oGrid);

function Lang_on_before_parse_row($component){
    if(isset($component->grid_buttons["status"])) {
        if($component->db[0]->getField("status", "Number", true)==1) {
            $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("remove_to_status");
            $component->grid_buttons["status"]->class = "far fa-check-square";
            $component->grid_buttons["status"]->action_type = "submit";
            $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&ret_url=" . urlencode($component->parent[0]->getRequestUri());
            if($_REQUEST["XHR_DIALOG_ID"]) {
                $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
            } else {
                $component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', 'injectid' : '#pagecontent', fields: [], 'url' : '[[frmAction_url]]'});";
            }
        } else {
            $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("add_to_status");
            $component->grid_buttons["status"]->class = "far fa-square";
            $component->grid_buttons["status"]->action_type = "submit";
            $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&ret_url=" . urlencode($component->parent[0]->getRequestUri());
            if($_REQUEST["XHR_DIALOG_ID"]) {
                $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
            } else {
                $component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', 'injectid' : '#pagecontent', fields: [], 'url' : '[[frmAction_url]]'});";
            }
        }
    }
}