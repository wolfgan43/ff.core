<?php
$db = ffDB_Sql::factory();

$cm->oPage->fixed_post_content .= <<<EOD
<script type="text/javascript">
function gup( name )
{
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( window.location.href );
    if( results == null )
        return "";
    else
        return results[1];
}

$(document).ready(function(){
    var thetabs = $("#tabs").tabs();
    var tab = gup("tab");
    if (tab.length)
    {
        thetabs.tabs('select', parseFloat(tab));
    }

    var thetabsweek = $("#tabsweek").tabs();
    var tabweek = gup("tabweek");
    if (tabweek.length)
    {
        thetabsweek.tabs('select', parseFloat(tabweek));
    }
});
</script>
EOD;

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ArticleModify";
$oRecord->title = ffTemplate::_get_word_by_code("article_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_article";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "date";
$oField->label = "Data";
$oField->base_type = "Date";
$oField->widget = "datepicker";
$oRecord->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_categories";
$oField->label = ffTemplate::_get_word_by_code("mod_article_categories_detail_languages");
$oField->base_type = "Number";
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_article_categories.ID, " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name AS name
                        FROM
                            " . CM_TABLE_PREFIX . "mod_article_categories
                            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories
                            INNER JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_lang
                        WHERE " . FF_PREFIX . "languages.code = " . $db->toSql(FF_LOCALE, "Text");
$oRecord->addFormField($oField);

$cm->oPage->addComponent($oRecord);



$oDetail = ffDetails::factory($cm->oPage, null, null, array("name" => "ffDetails_tabs"));
$oDetail->id = "ArticleDetail";
$oDetail->title = ffTemplate::_get_word_by_code("article_detail_title");
$oDetail->src_table = CM_TABLE_PREFIX . "mod_article_rel_languages";
$oDetail->order_default = "ID";
$oDetail->fields_relationship = array ("ID_article" => "ID");
$oDetail->display_new = false;
$oDetail->display_delete = false;
$oDetail->auto_populate_insert = true;
$oDetail->on_do_action = "ArticleDetail_on_do_action";
$oDetail->populate_insert_SQL = "SELECT " . FF_PREFIX . "languages.ID AS ID_lang, " . FF_PREFIX . "languages.description AS language FROM " . FF_PREFIX . "languages";
$oDetail->auto_populate_edit = true;
$oDetail->populate_edit_SQL = "SELECT 
                                    " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID AS ID
                                    , " . FF_PREFIX . "languages.ID AS ID_lang
                                    , " . FF_PREFIX . "languages.description AS language
                                    , " . CM_TABLE_PREFIX . "mod_article_rel_languages.title AS title
                                    , " . CM_TABLE_PREFIX . "mod_article_rel_languages.slug AS slug
                                FROM " . FF_PREFIX . "languages
                                    LEFT JOIN " . CM_TABLE_PREFIX . "mod_article_rel_languages ON  " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID AND " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_article = [ID_FATHER]
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
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("article_detail_title");
//$oField->required = true;
$oDetail->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "slug";
$oField->label = ffTemplate::_get_word_by_code("article_detail_slug");
$oDetail->addFormField($oField);

$oRecord->addDetail($oDetail);
$cm->oPage->addComponent($oDetail);




$oDetail = ffDetails::factory($cm->oPage);
$oDetail->id = "ArticleDetailRelLangIta";
$oDetail->title = ffTemplate::_get_word_by_code("article_detail_lang_ita_title");
$oDetail->src_table = CM_TABLE_PREFIX . "mod_article_detail_rel_languages";
$oDetail->order_default = "ID";
$oDetail->fields_relationship = array ("ID_article" => "ID");
$oDetail->auto_populate_insert = true;
$oDetail->populate_insert_SQL = "SELECT " . FF_PREFIX . "languages.ID AS ID_lang FROM " . FF_PREFIX . "languages WHERE " . FF_PREFIX . "languages.code = 'ITA'";
$oDetail->auto_populate_edit = true;
$oDetail->populate_edit_SQL = "SELECT 
                                    " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.*
                                FROM " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages
                                    INNER JOIN " . FF_PREFIX . "languages ON " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID 
                                WHERE
                                    " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_article = [ID_FATHER]
                                    AND " . FF_PREFIX . "languages.code = 'ITA'
                                ";
$oDetail->fixed_pre_content .= <<<EOD
<div id="tabs">
    <ul id="tabs-nav">
        <li><a href="#fragment-1"><span>Italiano</span></a></li>
        <li><a href="#fragment-2"><span>English</span></a></li>
    </ul>
    <div id="fragment-1">
EOD;
$oDetail->fixed_post_content .= <<<EOD
    </div>
EOD;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "ID_lang";
$oField->label = ffTemplate::_get_word_by_code("events_detail_ID_languages");
$oField->base_type = "Number";
$oField->required = true;
$oField->default_value = new ffData("1", "Number");
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "file";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_file");
$oField->extended_type = "File";
$oField->file_temp_path = FF_DISK_PATH. "/uploads/mod_article";
$oField->file_storing_path = FF_DISK_PATH. "/uploads/mod_article/[ID_FATHER]/images/[ID_VALUE]";
$oField->file_show_delete = true;
$oField->file_saved_view_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_VALUE]/[_FILENAME_]";
$oField->file_saved_preview_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_VALUE]/thumb/[_FILENAME_]";
$oField->file_temp_view_url            = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/temp/[_FILENAME_]";
$oField->file_temp_preview_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/temp/thumb/[_FILENAME_]";
$oField->file_allowed_mime = array(
                                        "image/png"
                                        , "image/gif"
                                        , "image/jpeg"
                                );
$oDetail->addFormField($oField);
$oField = ffField::factory($cm->oPage);

$oField->id = "position";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_position");
$oField->extended_type = "Selection";
//$oField->widget = "activecomboex";
$oField->multi_pairs = array (
                            array(new ffData("left"), new ffData(ffTemplate::_get_word_by_code("left"))),
                            array(new ffData("right"), new ffData(ffTemplate::_get_word_by_code("right")))
                       );      
//$oField->required = true;
$oDetail->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_description");
$oField->control_type = "textarea";
//$oField->widget = "tiny_mce";
$oField->extended_type = "Text";
$oField->base_type = "Text";
$oDetail->addFormField($oField);

$oRecord->addDetail($oDetail);
$cm->oPage->addComponent($oDetail);

$oDetail = ffDetails::factory($cm->oPage);
$oDetail->id = "ArticleDetailRelLangEng";
$oDetail->title = ffTemplate::_get_word_by_code("article_detail_ita_lang_title");
$oDetail->src_table = CM_TABLE_PREFIX . "mod_article_detail_rel_languages";
$oDetail->order_default = "ID";
$oDetail->fields_relationship = array ("ID_article" => "ID");
$oDetail->auto_populate_insert = true;
$oDetail->populate_insert_SQL = "SELECT " . FF_PREFIX . "languages.ID AS ID_lang FROM " . FF_PREFIX . "languages WHERE " . FF_PREFIX . "languages.code = 'ENG'";
$oDetail->auto_populate_edit = true;
$oDetail->populate_edit_SQL = "SELECT 
                                    " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.*
                                FROM " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages
                                    INNER JOIN " . FF_PREFIX . "languages ON " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID 
                                WHERE
                                    " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_article = [ID_FATHER]
                                    AND " . FF_PREFIX . "languages.code = 'ENG'
                                ";
$oDetail->fixed_pre_content .= <<<EOD
    <div id="fragment-2">
EOD;
$oDetail->fixed_post_content .= <<<EOD
    </div>
EOD;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "ID_lang";
$oField->label = ffTemplate::_get_word_by_code("events_detail_ID_languages");
$oField->base_type = "Number";
$oField->required = true;
$oField->default_value = new ffData("2", "Number");
$oDetail->addHiddenField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "file";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_file");
$oField->extended_type = "File";
$oField->file_temp_path = FF_DISK_PATH. "/uploads/mod_article";
$oField->file_storing_path = FF_DISK_PATH. "/uploads/mod_article/[ID_FATHER]/images/[ID_VALUE]";
$oField->file_show_delete = true;
$oField->file_saved_view_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_VALUE]/[_FILENAME_]";
$oField->file_saved_preview_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_VALUE]/thumb/[_FILENAME_]";
$oField->file_temp_view_url            = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/temp/[_FILENAME_]";
$oField->file_temp_preview_url        = FF_SITE_PATH . "/cm/showfiles.php/mod-article-images/temp/thumb/[_FILENAME_]";
$oField->file_allowed_mime = array(
                                        "image/png"
                                        , "image/gif"
                                        , "image/jpeg"
                                );
$oDetail->addFormField($oField);
$oField = ffField::factory($cm->oPage);

$oField->id = "position";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_position");
$oField->extended_type = "Selection";
//$oField->widget = "activecomboex";
$oField->multi_pairs = array (
                            array(new ffData("left"), new ffData(ffTemplate::_get_word_by_code("left"))),
                            array(new ffData("right"), new ffData(ffTemplate::_get_word_by_code("right")))
                       );      
$oDetail->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = ffTemplate::_get_word_by_code("article_detail_lang_description");
$oField->control_type = "textarea";
//$oField->widget = "tiny_mce";
$oField->extended_type = "Text";
$oField->base_type = "Text";
$oDetail->addFormField($oField);

$oRecord->addDetail($oDetail);
$cm->oPage->addComponent($oDetail);

function ArticleDetail_on_do_action($component, $action) {
    $db = ffDB_Sql::factory();
    if($action == "insert" || $action == "update") {
    //ffErrorHandler::raise("aad", E_USER_ERROR, null, get_defined_vars());
        if(is_array($component[0]->recordset) && count($component[0]->recordset)) {
            foreach($component[0]->recordset AS $rst_key => $rst_value) {
                if(!$rst_value["slug"]->getValue()) 
                    $component[0]->recordset[$rst_key]["slug"]->setValue(ffCommon_url_rewrite($rst_value["title"]->getValue()));
                else
                    $component[0]->recordset[$rst_key]["slug"]->setValue(ffCommon_url_rewrite($rst_value["slug"]->getValue()));
                    
                if($rst_value["title"]->getValue()) {
                    $sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_article_rel_languages WHERE ID_lang = " . $db->toSql($rst_value["ID_lang"], "Number") . " AND title = " . $db->toSql($rst_value["title"], "Text") . " AND ID <> " . $db->toSql($rst_value["ID"], "Number");
                    $db->query($sSQL);
                    if($db->nextRecord()) {
                        $component[0]->displayError(ffTemplate::_get_word_by_code("title_not_unic"));
                        break;
                    }
                }
            }
            reset($component[0]->recordset);
        }
    }
    return false;
    
    
}










 /*
$oDetail = ffDetails::factory($cm->oPage);
$oDetail->id = "ImagesDetail";
$oDetail->src_table = CM_TABLE_PREFIX . "mod_news_images";
$oDetail->title = "Galleria";
$oDetail->fields_relationship = array(
									"ID_news" => "ID"
								);
$oDetail->order_default = "title";

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_dett_images";
$oField->data_source = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = "Titolo";
$oDetail->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "file";
$oField->label = "File";
$oField->extended_type = "File";
$oField->file_temp_path = FF_DISK_PATH. "/uploads/mod_news";
$oField->file_storing_path = FF_DISK_PATH. "/uploads/mod_news/[ID_FATHER]/images/[ID_dett_images_VALUE]";
$oField->file_show_delete = true;
$oField->file_saved_view_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_dett_images_VALUE]/[_FILENAME_]";
$oField->file_saved_preview_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-images/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_dett_images_VALUE]/thumb/[_FILENAME_]";
$oField->file_temp_view_url			= FF_SITE_PATH . "/cm/showfiles.php/mod-news-images/temp/[_FILENAME_]";
$oField->file_temp_preview_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-images/temp/thumb/[_FILENAME_]";
$oField->file_allowed_mime = array(
										"image/png"
										, "image/gif"
										, "image/jpeg"
								);
$oDetail->addFormField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "comment";
$oField->label = "Commento";
$oField->extended_type = "Text";
$oField->properties["style"]["height"] = "200px";
$oDetail->addFormField($oField);

$oRecord->addDetail($oDetail);
$cm->oPage->addComponent($oDetail);

if (MOD_NEWS_ATTACHS)
{
	$oDetail2 = ffDetails::factory($cm->oPage);
	$oDetail2->id = "AttachsDetail";
	$oDetail2->src_table = CM_TABLE_PREFIX . "mod_news_attachs";
	$oDetail2->title = "Allegati";
	$oDetail2->fields_relationship = array(
										"ID_news" => "ID"
									);
	$oDetail2->order_default = "title";

	$oField = ffField::factory($cm->oPage);
	$oField->id = "ID_dett_attachs";
	$oField->data_source = "ID";
	$oField->base_type = "Number";
	$oDetail2->addKeyField($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "title";
	$oField->label = "Titolo";
	$oDetail2->addFormField($oField);

	$oField = ffField::factory($cm->oPage);
	$oField->id = "file";
	$oField->label = "File";
	$oField->extended_type = "File";
	$oField->file_temp_path = FF_DISK_PATH. "/uploads/mod_news";
	$oField->file_storing_path = FF_DISK_PATH. "/uploads/mod_news/[ID_FATHER]/attachs/[ID_dett_attachs_VALUE]";
	$oField->file_show_delete = true;
	$oField->file_saved_view_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-attachs/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_dett_attachs_VALUE]/[_FILENAME_]";
	$oField->file_saved_preview_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-attachs/saved/" . $_REQUEST["keys"]["ID"] . "/[ID_dett_attachs_VALUE]/thumb/[_FILENAME_]";
	$oField->file_temp_view_url			= FF_SITE_PATH . "/cm/showfiles.php/mod-news-attachs/temp/[_FILENAME_]";
	$oField->file_temp_preview_url		= FF_SITE_PATH . "/cm/showfiles.php/mod-news-attachs/temp/thumb/[_FILENAME_]";
	$oField->file_allowed_mime = array(
											"image/png"
											, "image/gif"
											, "image/jpeg"
									);
	$oDetail2->addFormField($oField);

	$oRecord->addDetail($oDetail2);
	$cm->oPage->addComponent($oDetail2);
}*/