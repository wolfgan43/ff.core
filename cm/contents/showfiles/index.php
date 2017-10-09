<?php
/**
 * @ignore
 * @package ContentManager
 * @subpackage contents
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->full_ajax = true;
$oGrid->id = "MainGrid";
$oGrid->title = "ShowFiles - Items";
$oGrid->source_SQL = "
						SELECT
								`" . CM_TABLE_PREFIX . "showfiles`.*
							FROM
								`" . CM_TABLE_PREFIX . "showfiles`
							[WHERE]
							[ORDER]
					";
$oGrid->order_default = "name";
$oGrid->use_search = false;
$oGrid->record_id = "MainRecord";
$oGrid->resources[] = "cmSettings";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
if (cm_getMainTheme() == "restricted" || $cm->oPage->getTheme() == "restricted")
	$oGrid->full_ajax = true;
else
	$oGrid->full_ajax = false;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = "Name";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);