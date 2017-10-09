<?php
$db = ffDB_Sql::factory();

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "ArticleCategories";
$oGrid->title = ffTemplate::_get_word_by_code("article_categories_title");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_article_categories.*
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name FROM " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages WHERE ID_lang = '1' AND " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories = " . CM_TABLE_PREFIX . "mod_article_categories.ID) AS name_ita
                            , (SELECT " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name FROM " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages WHERE ID_lang = '2' AND " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories = " . CM_TABLE_PREFIX . "mod_article_categories.ID) AS name_eng
                        FROM
                            " . CM_TABLE_PREFIX . "mod_article_categories
                INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories
                   INNER JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_lang
                   WHERE " . FF_PREFIX . "languages.code = " . $db->toSql(FF_LOCALE, "Text") . "
                        [AND] [WHERE] 
                        [ORDER]";

$oGrid->order_default = "ID";
$oGrid->use_search = false;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "ArticleCategoriesModify";

// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "name_ita";
$oField->label = ffTemplate::_get_word_by_code("article_categories_name");
$oGrid->addDisplayField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name_eng";
$oField->label = ffTemplate::_get_word_by_code("article_categories_name");
$oGrid->addDisplayField($oField);


// Campi di ricerca

// Campi visualizzati

$cm->oPage->addComponent($oGrid);


?>