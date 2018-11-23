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
$db = ffDB_Sql::factory();

$ID = $_REQUEST["keys"]["ID"];
if($ID) {
    $sSQL = "SELECT DISTINCT
            " . CM_TABLE_PREFIX . "media.`path` 
            , " . CM_TABLE_PREFIX . "media.`key`
        FROM " . CM_TABLE_PREFIX . "media 
        WHERE " . CM_TABLE_PREFIX . "media.ID = " . $db->toSql($ID, "Number") . "
        ORDER BY `path`";
    $db->query($sSQL);
    if($db->nextRecord()) {
        $key = $db->getField("key", "Text", true);
        $path = $db->getField("path", "Text", true);
    }
} else {
    $key = $_REQUEST["key"];
    $path = ($_REQUEST["path"]
        ? $_REQUEST["path"]
        : $cm->real_path_info
    );
}
$real_file = FF_DISK_UPDIR . $path;


$cm->oPage->title = ffTemplate::_get_word_by_code("media_modify") . ": " . basename($path) . ($key ? " " . ffTemplate::_get_word_by_code("in") . " " . $key : "");
		
if(!is_file($real_file) ) //da fare l'addnew
    ffErrorHandler::raise("Resouce Missing", E_USER_ERROR, null, get_defined_vars());

$media_plugin = "origin-file";
if(is_file(FF_THEME_DISK_PATH . "/library/plugins/jquery.fancybox/jquery.fancybox.js")) {
    $addon = array(
        "jquery" => array(
            "all" => array(
                "js_defs" => array(
                    "fancybox" => array(
                        "path" => FF_THEME_DIR . "/library/plugins/jquery.fancybox"
                        , "file" => "jquery.fancybox.js"
                        , "index" => 200
                        , "js_loads" => array(
                            ".observe" => array(
                                "embed" => 'ff.pluginAddInit("jquery.fancybox", function() { $("a.fancybox").fancybox(); });'
                            )
                        )
                    )
                )
            )
        )
    );
    $addon["jquery"]["all"]["js_defs"]["fancybox"]["css_deps"]["fancybox"] = array(
        "path" => "/themes/library/plugins/jquery.fancybox"
        , "file" => "jquery.fancybox.css"

    );
    $cm->oPage->libsExtend($addon); // carica le aggiunte
    $cm->oPage->tplAddJs("jquery.fancybox");
    $media_plugin = "fancybox";
}


$sSQL = "SELECT DISTINCT
            " . CM_TABLE_PREFIX . "media.`key` 
        FROM " . CM_TABLE_PREFIX . "media 
        WHERE " . CM_TABLE_PREFIX . "media.path = " . $db->toSql($path) . "
        ORDER BY `key`";
$db->query($sSQL);
if($db->nextRecord()) {
    do {
        $referer[$db->record["key"]] = ($key == $db->record["key"]
            ? true
            : false
        );
    } while($db->nextRecord());
}

$sSQL = "SELECT COUNT(" . FF_PREFIX . "languages.ID) AS count_lang FROM " . FF_PREFIX . "languages WHERE " . FF_PREFIX . "languages.status = '1'";
$db->query($sSQL);
if ($db->nextRecord()) {
    $count_lang = $db->getField("count_lang", "Number", true);
}

$tpl = ffTemplate::factory(__DIR__);
$tpl->load_file("preview.html", "main");
$tpl->set_var("theme_path", ff_getThemePath(cm_getMainTheme()) . "/" . cm_getMainTheme());
$tpl->set_var("container_class", Cms::getInstance("frameworkcss")->get(array(4), "col", "preview"));
$tpl->set_var("download_class", Cms::getInstance("frameworkcss")->get("download", "icon")); 
$tpl->set_var("modify_class", Cms::getInstance("frameworkcss")->get("crop", "icon"));

$cache = ffCache::getInstance();
if($cache->get("aviary/key")) {
    $tpl->set_var("aviary_key", $cache->get("aviary/key"));
    $tpl->parse("SezAviary", false);
}

$tpl->set_var("media_class", $media_plugin);
$tpl->set_var("media_thumb_class", Cms::getInstance("frameworkcss")->get("corner-thumbnail", "util"));
$tpl->set_var("media_width", "600");
$tpl->set_var("media_height", "400");

$tpl->set_var("view_path", FF_SITE_UPDIR);
$tpl->set_var("preview_path", ffMedia::SHOWFILES);
$tpl->set_var("media_time", time());

$tpl->set_var("row_class", Cms::getInstance("frameworkcss")->get("row", "form"));
$tpl->set_var("control_class", Cms::getInstance("frameworkcss")->get("control", "form")); 
$tpl->set_var("info_class", Cms::getInstance("frameworkcss")->get("info", "callout") . " " . Cms::getInstance("frameworkcss")->get("text-overflow", "util"));

$original_size = formatSizeUnits(filesize($real_file));
//Original image
$tpl->set_var("media_path", $path);
$tpl->set_var("media_name_normalized", ffCommon_url_rewrite(basename($path)));
$tpl->set_var("media_title", "Original - Size: " . $original_size);
$tpl->parse("SezThumb", true);

//Original image info
$parent_path = FF_UPDIR . ffCommon_dirname($path);
$arrParentPath = explode("/", trim($parent_path, "/"));
if(is_array($arrParentPath) && count($arrParentPath)) {
    foreach($arrParentPath AS $arrParentPath_value) { 
        $parent_title = ucwords(str_replace("-", " " , $arrParentPath_value));
        $str_menu_parent_path .= ($str_menu_parent_path ? '<ul class="list-group">' : '') . '<li class="list-group-item ' . Cms::getInstance("frameworkcss")->get(array("text-nowrap", "text-overflow"), "util") . '" title="' . $parent_title . '"><a href="javascript:void(0);"' . Cms::getInstance("frameworkcss")->get("folder-open", "icon-tag") . " " . $parent_title . '</a>';
    }
}
$tpl->set_var("media_tree_path", '<ul class="nopadding">' . $str_menu_parent_path . str_repeat("</li></ul>", substr_count($parent_path, "/")));
$tpl->set_var("media_name", basename($path));
$tpl->set_var("media_size", $original_size);


//thumbs generated in cache
$cacheThumbs = glob(ffMedia::STORING_BASE_PATH . ffCommon_dirname($path) . "/*");
if (is_array($cacheThumbs) && count($cacheThumbs)) {
    $filename = pathinfo($path, PATHINFO_FILENAME);
    $tpl->set_var("media_width", "100");
    $tpl->set_var("media_height", "100");
    $tpl->set_var("view_path", ffMedia::SHOWFILES);

    foreach ($cacheThumbs AS $cache_thumb_path) {
        $thumb_filename = pathinfo($cache_thumb_path, PATHINFO_FILENAME);
        $thumb_info = ffMedia::getInfo($cache_thumb_path);

        if($thumb_filename != $filename && strpos($thumb_filename, $filename) === 0) {
            $tpl->set_var("preview_path", ffMedia::SHOWFILES);
            $tpl->set_var("media_path", str_replace(ffMedia::STORING_BASE_PATH, "", $cache_thumb_path));
            $tpl->set_var("media_name_normalized", ffCommon_url_rewrite(basename($cache_thumb_path)));
            $tpl->set_var("media_title", "Thumb: " . $thumb_info["mode"] . " - Size: " . formatSizeUnits(filesize($cache_thumb_path)) . " (Optimized)");
            $tpl->parse("SezThumb", true);
        }
    }
}

//referer
if(is_array($referer) && count($referer)) {
    $tpl->set_var("referer_container_class", Cms::getInstance("frameworkcss")->get("group", "list"));
    foreach ($referer AS $ref_key => $ref_value) {
        $arrItemClass = array("item");
        if($ref_value) {
            $arrItemClass[] = "current";
        }

        $tpl->set_var("referer_class", Cms::getInstance("frameworkcss")->get($arrItemClass, "list"));
        $tpl->set_var("referer_name", $ref_key);
        if($cm->isXHR()) {
            $tpl->set_var("referer_url", "javascript:ff.ffPage.dialog.goToUrl('" . $_REQUEST["XHR_CTX_ID"] . "', '" . $cm->path_info . "?path=" . rawurlencode($path) . "&key=" . rawurlencode($ref_key) . "');");
        } else {
            ffRedirect($cm->path_info . "?path=" . rawurlencode($path) . "&key=" . rawurlencode($ref_key));
        }
        $tpl->parse("SezRefererItem", true);

    }
}



$cm->oPage->addContent($tpl);


if($referer[$key]) {
    $_REQUEST["keys"]["ID"] = $key;
    $_REQUEST["keys"]["path"] = $path;
}



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "MediaModify";
$oRecord->resources[] = $oRecord->id;
//$oRecord->title = "";
//$oRecord->src_table = false;
//$oRecord->user_vars["path"] = ffCommon_dirname($path);
$oRecord->buttons_options["delete"]["display"] = false;
/* Title Block */
//$oRecord->fixed_pre_content = '<h1 class="dialogTitle admin-title vg-content">' . Cms::getInstance("frameworkcss")->get("vg-gallery", "icon-tag", array("2x", "content")) . $gallery_title . '</h1>';
$oRecord->setWidthComponent(8);
//  $oRecord->class = "nopadding";

//$oRecord->addEvent("on_do_action", "GalleryModify_on_do_action");
//$oRecord->addEvent("on_done_action", "GalleryModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->default_value = new ffData($key);
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "path";
$oField->default_value = new ffData($path);
$oRecord->addKeyField($oField);

//Detail with Lang Support
$oDetail = ffDetails::factory($cm->oPage);
if ($count_lang > 1) {
    $oDetail->tab = "top";
    $oDetail->tab_label = "language";
}
$oDetail->id = "MediaDetail";
//$oDetail->title = ffTemplate::_get_word_by_code("gallery_field_title");
$oDetail->src_table = CM_TABLE_PREFIX . "media";
$oDetail->order_default = "ID";
$oDetail->fields_relationship = array("key" => "ID", "path" => "path");
$oDetail->display_new = false;
$oDetail->display_delete = false;
$oDetail->auto_populate_insert = true;
$oDetail->populate_insert_SQL = "SELECT 
                                " . FF_PREFIX . "languages.ID AS ID_lang
                                , " . FF_PREFIX . "languages.description AS language
                                , " . FF_PREFIX . "languages.code AS code_lang
                                FROM " . FF_PREFIX . "languages
                                WHERE
                                    " . FF_PREFIX . "languages.status = '1'
                                ORDER BY " . FF_PREFIX . "languages.tiny_code";
$oDetail->auto_populate_edit = true;
// $oDetail->addEvent("on_do_action", "MediaModifyDetail_on_do_action");
$oDetail->populate_edit_SQL = "SELECT 
                                    " . CM_TABLE_PREFIX . "media.ID AS ID
                                    , " . FF_PREFIX . "languages.ID AS ID_lang
                                    , " . FF_PREFIX . "languages.description AS language
                                    , " . FF_PREFIX . "languages.code AS code_lang
                                    , " . CM_TABLE_PREFIX . "media.title AS title
                                    , " . CM_TABLE_PREFIX . "media.description AS description
                                FROM " . FF_PREFIX . "languages
                                    LEFT JOIN " . CM_TABLE_PREFIX . "media ON  " . CM_TABLE_PREFIX . "media.ID_lang = " . FF_PREFIX . "languages.ID 
                                        AND " . CM_TABLE_PREFIX . "media.`path` = " . $db->toSql($path) . "
                                        AND " . CM_TABLE_PREFIX . "media.`key` = " . $db->toSql($key)   . "
                                WHERE
                                    " . FF_PREFIX . "languages.status = '1'
                                ORDER BY " . FF_PREFIX . "languages.tiny_code
                                ";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "language";
$oField->store_in_db = false;
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_lang";
$oField->base_type = "Number";
$oField->required = true;
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "code_lang";
$oField->store_in_db = false;
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("media_title");
$oDetail->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = ffTemplate::_get_word_by_code("media_description");
$oField->extended_type = "Text";
$oDetail->addContent($oField);

$oRecord->addContent($oDetail);
$cm->oPage->addContent($oDetail);


$oField = ffField::factory($cm->oPage);
$oField->id = "permalink";
$oField->label = ffTemplate::_get_word_by_code("media_permalink");
$oField->default_value = new ffData($path);
$oField->data_type = "";
$oField->store_in_db = false;
$oField->fixed_pre_content = CM_SHOWFILES;
$oRecord->addContent($oField);


$cm->oPage->addContent($oRecord);



    

// -------------------------
//          EVENTI
// -------------------------


function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}