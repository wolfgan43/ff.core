<?php

function get_article_lang($table, $ID, $prefix = null, $path, $field = "slug") {
    $db = ffDB_Sql::factory();

    $dir_int = array("article" => array("ITA" => "articoli", "ENG" => "article"));

    $sSQL = "SELECT " . $table . "_rel_languages." . $field . ", " . FF_PREFIX . "languages.code AS lang
            FROM " . FF_PREFIX . "languages
            LEFT JOIN " . $table . "_rel_languages  ON " . FF_PREFIX . "languages.ID = " . $table . "_rel_languages.ID_lang AND " . $table . "_rel_languages.ID_" . ($prefix === NULL ? $table : str_replace($prefix, "", $table)) . " = " . $db->toSql($ID, "Number") . "
            WHERE 1";
    $db->query($sSQL);
    if($db->nextRecord()) {
        $tpl = new ffTemplate($path);
        $tpl->load_file("language.html", "main");
        do {
            $tpl->set_var("lang_path", FF_SITE_PATH . "/" . ($prefix === NULL ? $dir_int[$table][$db->getField("lang", "Text")->getValue()] : $dir_int[str_replace($prefix, "", $table)][$db->getField("lang", "Text")->getValue()]) . (strlen($db->getField($field, "Text")->getValue())
                                                        ? "/" . $db->getField($field, "Text")->getValue()
                                                        : "")
                                                        . "?lang=" . $db->getField("lang", "Text")->getValue());
            $tpl->set_var("lang_name", $db->getField("lang", "Text")->getValue());
            $tpl->parse("SezLang", true);
        } while($db->nextRecord());
        return $tpl->rpparse("main", false);
    }
}