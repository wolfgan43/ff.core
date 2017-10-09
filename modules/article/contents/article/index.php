<?php
$globals = ffGlobals::getIstance("mod_article");

if(strlen($cm->real_path_info)) {

    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/detail.html", $cm->oPage->theme, false);
    if ($filename === null)
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/article/contents/article/detail.html", $cm->oPage->theme, false);
    if ($filename === null)
        $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/article/detail.html", $cm->oPage->theme);

    $tpl = ffTemplate::factory(ffCommon_dirname($filename));
    $tpl->load_file("detail.html", "main");
    $tpl->set_var("theme", $cm->oPage->theme);
    $tpl->set_var("site_path", $cm->oPage->site_path);
    
    if(FF_LOCALE == "ITA") {
        $article_path = (string)$cm->router->getRuleById("articoli")->reverse;
    } else {
        $article_path = (string)$cm->router->getRuleById("articles")->reverse;
    }
    
    $tpl->set_var("article_path", $article_path);
                  
    $cm->preloadApplets(array($tpl));
    $cm->parseApplets(array($tpl));

    $db = ffDb_Sql::factory();
    $db2 = ffDb_Sql::factory();
    $sSQL = "SELECT
                " . CM_TABLE_PREFIX . "mod_article.*, " . CM_TABLE_PREFIX . "mod_article_rel_languages.title AS title, " . CM_TABLE_PREFIX . "mod_article_rel_languages.slug AS slug, " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name AS categories
            FROM
                " . CM_TABLE_PREFIX . "mod_article
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_rel_languages ON " . CM_TABLE_PREFIX . "mod_article.ID = " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_article 
            INNER JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_lang
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article.ID_categories
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories AND " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID
            WHERE " . FF_PREFIX . "languages.code = " . $db->toSql(FF_LOCALE, "Text") . " AND " . CM_TABLE_PREFIX . "mod_article_rel_languages.slug = " . $db->toSql(basename($cm->real_path_info), "Text") . "
            ORDER BY " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name";
    $db->query($sSQL);
    if ($db->nextRecord())
    {
        $global = ffGlobals::getIstance("aurora");
        $global->table = CM_TABLE_PREFIX . "mod_article";
        $global->ID = $db->getField("ID", "Number")->getValue();
        $global->path = ffCommon_dirname($filename);
        $global->field = "slug";
        $global->contest = "article";
        $global->prefix = CM_TABLE_PREFIX . "mod_";
        
//        $cm->oPage->fixed_pre_content = get_article_lang(CM_TABLE_PREFIX . "mod_article", $db->getField("ID", "Number")->getValue(), CM_TABLE_PREFIX . "mod_", ffCommon_dirname($filename));    
        $tpl->set_var("title", htmlentities($db->getField("title")->getValue()));
        $tpl->set_var("slug", htmlentities($db->getField("slug")->getValue()));
        $tpl->set_var("category", htmlentities($db->getField("categories")->getValue()));

        $sSQL = "SELECT 
                    " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.* 
                FROM " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages 
                INNER JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_lang
                WHERE " . FF_PREFIX . "languages.code = " . $db2->toSql(FF_LOCALE, "Text") . " 
                    AND " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID_article = " . $db2->toSql($db->getField("ID")) . "
                ORDER BY " . CM_TABLE_PREFIX . "mod_article_detail_rel_languages.ID";
        $db2->query($sSQL);
        if($db2->nextRecord()) {
            do {
                $tpl->set_var("ID", $db->getField("ID")->getValue());
                $tpl->set_var("ID_dett",  $db2->getField("ID")->getValue());
                if($db2->getField("file")->getValue()) {
                    $tpl->set_var("file", $db2->getField("file")->getValue());
                    $tpl->set_var("position", htmlentities($db2->getField("position")->getValue()));
                    $tpl->parse("SezImage", false);
                } else {
                    $tpl->set_var("SezImage", "");
                }

                $tpl->set_var("description", htmlentities($db2->getField("description")->getValue()));
                $tpl->parse("SezDetail", true);
            } while($db2->nextRecord());
        }
    } else {
        ffRedirect("", FF_SITE_PATH . (string)$cm->router->getRuleById("article")->reverse);
    }
} else {
    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/contents" . $cm->path_info . "/index.html", $cm->oPage->theme, false);
    if ($filename === null)
	    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/article/contents/article/index.html", $cm->oPage->theme, false);
    if ($filename === null)
	    $filename = cm_moduleCascadeFindTemplate($cm->module_path . "/themes", "/contents/article/index.html", $cm->oPage->theme);

    if(FF_LOCALE == "ITA") {
        $article_path = (string)$cm->router->getRuleById("articoli")->reverse;
    } else {
        $article_path = (string)$cm->router->getRuleById("articles")->reverse;
    }
    

    $tpl = ffTemplate::factory(ffCommon_dirname($filename));
    $tpl->load_file("index.html", "main");
    $tpl->set_var("theme", $cm->oPage->theme);
    $tpl->set_var("site_path", $cm->oPage->site_path);
    $tpl->set_var("article_path", $article_path);

    $cm->preloadApplets(array($tpl));
    $cm->parseApplets(array($tpl));

    $db = ffDb_Sql::factory();

    $navigator = ffPageNavigator::factory($cm->oPage);
    $navigator->page = $_REQUEST[$navigator->page_parname];
    if (intval($navigator->page) < 1)
	    $navigator->page = 1;
    if (isset($_REQUEST[$navigator->records_per_page_parname]))
	    $navigator->records_per_page = $_REQUEST[$navigator->records_per_page_parname];

    $sSQL = "SELECT
                " . CM_TABLE_PREFIX . "mod_article.*, " . CM_TABLE_PREFIX . "mod_article_rel_languages.slug AS slug, " . CM_TABLE_PREFIX . "mod_article_rel_languages.title AS title, " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name AS categories
            FROM
                " . CM_TABLE_PREFIX . "mod_article
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_rel_languages ON " . CM_TABLE_PREFIX . "mod_article.ID = " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_article 
            INNER JOIN " . FF_PREFIX . "languages ON " . FF_PREFIX . "languages.ID = " . CM_TABLE_PREFIX . "mod_article_rel_languages.ID_lang
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article.ID_categories
            INNER JOIN " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages ON " . CM_TABLE_PREFIX . "mod_article_categories.ID = " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_categories AND " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.ID_lang = " . FF_PREFIX . "languages.ID
            WHERE " . FF_PREFIX . "languages.code = " . $db->toSql(FF_LOCALE, "Text") . "
            ORDER BY " . CM_TABLE_PREFIX . "mod_article_categories_rel_languages.name";
    $db->query($sSQL);
    if ($db->nextRecord())
    {
        $global = ffGlobals::getIstance("aurora");
        $global->table = CM_TABLE_PREFIX . "mod_article";
        $global->ID = "";
        $global->path = ffCommon_dirname($filename);
        $global->field = "slug";
        $global->contest = "article";
        $global->prefix = CM_TABLE_PREFIX . "mod_";

       // $cm->oPage->fixed_pre_content = get_article_lang(CM_TABLE_PREFIX . "mod_article", "", CM_TABLE_PREFIX . "mod_", ffCommon_dirname($filename));    
	    $navigator->num_rows = $db->numRows();
	    $db->jumpToPage($navigator->page, $navigator->records_per_page);
	    $i = 0;
        $category = $db->getField("categories")->getValue();
	    do
	    {
		    $tpl->set_var("ID", $db->getField("ID")->getValue());

		    //$tpl->set_var("date", htmlentities($db->getField("date", "Date")->getValue(null, FF_LOCALE)));
		    $tpl->set_var("mod_article_title", htmlentities($db->getField("title")->getValue()));
		    $tpl->set_var("mod_article_path", htmlentities("/" . $db->getField("slug")->getValue()));

		    $tpl->parse("SezArticle", true);

            if($category != $db->getField("categories")->getValue()) {
                $tpl->set_var("mod_article_category_name", $category);
                $tpl->parse("SezCategories", true);              
                $tpl->set_var("SezArticle", "");
                $category = $db->getField("categories")->getValue();
            }
		    $i++;
	    } while ($db->nextRecord() && $i < $navigator->records_per_page);
        $tpl->set_var("mod_article_category_name", $category);
        $tpl->parse("SezCategories", true);              
        $tpl->parse("SezArticles", false);
    }
    else
    {
	    $tpl->parse("SezNoArticles", false);
    }

    $tpl->set_var("navigator", $navigator->process());

}



$cm->oPage->fixed_pre_content .= $tpl->rpparse("main", false);