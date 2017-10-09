<?php
$db = ffDB_Sql::factory();

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "ArticleCategories";
$oGrid->title = ffTemplate::_get_word_by_code("article_title");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_article.*
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_rel_languages.title FROM " . CM_TABLE_PREFIX . "mod_article_rel_languages WHERE ID_lang = '1' AND ID_article = " . CM_TABLE_PREFIX . "mod_article.ID) AS title_ita
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_rel_languages.title FROM " . CM_TABLE_PREFIX . "mod_article_rel_languages WHERE ID_lang = '2' AND ID_article = " . CM_TABLE_PREFIX . "mod_article.ID) AS title_eng
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name FROM " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages WHERE ID_lang = '1' AND ID_categories = " . CM_TABLE_PREFIX . "mod_article.ID_categories) AS categories_ita
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name FROM " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages WHERE ID_lang = '2' AND ID_categories = " . CM_TABLE_PREFIX . "mod_article.ID_categories) AS categories_eng
                        FROM
                            " . CM_TABLE_PREFIX . "mod_article
                        [WHERE] 
                        [ORDER]";

$oGrid->order_default = "ID";
$oGrid->use_search = false;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "ArticleModify";

// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "date";
$oField->base_type = "Date";
$oField->label = ffTemplate::_get_word_by_code("events_data");
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "categories_ita";
$oField->label = ffTemplate::_get_word_by_code("article_categories_ita");
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "categories_eng";
$oField->label = ffTemplate::_get_word_by_code("article_categories_eng");
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title_ita";
$oField->label = ffTemplate::_get_word_by_code("article_title_ita");
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title_eng";
$oField->label = ffTemplate::_get_word_by_code("article_title_eng");
$oGrid->addDisplayField($oField);


// Campi di ricerca

// Campi visualizzati

$cm->oPage->addComponent($oGrid);


?>