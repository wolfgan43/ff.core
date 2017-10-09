<?php
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ArticleCategoriesModify";
$oRecord->title = ffTemplate::_get_word_by_code("article_categories_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_article_categories";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$cm->oPage->addComponent($oRecord);   

$oDetail = ffDetails::factory($cm->oPage, null, null, array("name" => "ffDetails_tabs"));
$oDetail->id = "ArticleCategoriesDetail";
$oDetail->title = ffTemplate::_get_word_by_code("article_categories_detail_title");
$oDetail->src_table = CM_TABLE_PREFIX . "mod_article_categories_rel_languages";
$oDetail->order_default = "ID";
$oDetail->fields_relationship = array ("ID_categories" => "ID");
$oDetail->display_new = false;
$oDetail->display_delete = false;
$oDetail->auto_populate_insert = true;
$oDetail->populate_insert_SQL = "SELECT " . FF_PREFIX . "languages.ID AS ID_lang, " . FF_PREFIX . "languages.description AS language FROM " . FF_PREFIX . "languages";
$oDetail->auto_populate_edit = true;
$oDetail->populate_edit_SQL = "SELECT 
                                    " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID AS ID
                                    , " . FF_PREFIX . "languages.ID AS ID_lang
                                    , " . FF_PREFIX . "languages.description AS language
                                    , " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name AS name
                                FROM " . FF_PREFIX . "languages
                                    LEFT JOIN " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages ON  " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID AND " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories = [ID_FATHER]
                                ";
$oDetail->tab_label = "language";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "language";
$oField->label = ffTemplate::_get_word_by_code("events_detail_languages");
$oField->store_in_db = false;
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_lang";
$oField->label = ffTemplate::_get_word_by_code("events_detail_ID_languages");
$oField->base_type = "Number";
$oField->required = true;
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("mod_article_categories_detail_name");
$oField->required = true;
$oDetail->addFormField($oField);

$oRecord->addDetail($oDetail);
$cm->oPage->addComponent($oDetail);


?>