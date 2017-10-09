<?php
$globals = ffGlobals::getInstance("mod_mention");

$db = ffDB_Sql::factory();

if(!strlen($user_path)) {
    if(strlen($cm->real_path_info))
        $user_path = $cm->real_path_info;
    else
        $user_path = $cm->path_info;
}

if(strlen($user_path)) {
    $arrPathInfo = array();
    if(strlen(global_settings("MOD_MENTION_CLASS_BASE_PATH"))) {
        $path_info = $cm->path_info . $cm->real_path_info;
        
        $arrPathInfo = explode(global_settings("MOD_MENTION_CLASS_BASE_PATH"), $path_info);
        $smart_url = basename($arrPathInfo[1]);
    }

    if(strlen($smart_url)) {
        $arrPpublishedAt = explode("-", substr($smart_url, 0, 19));
        $published_at = new ffData($arrPpublishedAt[0] . "-" . $arrPpublishedAt[1] . "-" . $arrPpublishedAt[2] . " " . $arrPpublishedAt[3] . ":" . $arrPpublishedAt[4] . ":" . $arrPpublishedAt[5], "DateTime", FF_SYSTEM_LOCALE);
        $smart_url = substr($smart_url, 20);

        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_mentions.*
                    , IF(" . CM_TABLE_PREFIX . "mod_mention_mentions.unique_id
                        , " . CM_TABLE_PREFIX . "mod_mention_mentions.unique_id
                        , " . CM_TABLE_PREFIX . "mod_mention_mentions.url
                    ) AS url
                FROM " . CM_TABLE_PREFIX . "mod_mention_mentions
                    INNER JOIN " . CM_TABLE_PREFIX . "mod_mention_alerts ON " . CM_TABLE_PREFIX . "mod_mention_alerts.ID = " . CM_TABLE_PREFIX . "mod_mention_mentions.ID_alert
                WHERE " . CM_TABLE_PREFIX . "mod_mention_alerts.path = " . $db->toSql($user_path) . "
                    AND " . CM_TABLE_PREFIX . "mod_mention_mentions.smart_url = " . $db->toSql($smart_url) . "
                    AND " . CM_TABLE_PREFIX . "mod_mention_mentions.published_at = " . $db->toSql($published_at->getValue("Timestamp", FF_SYSTEM_LOCALE)) . "
                    AND " . CM_TABLE_PREFIX . "mod_mention_mentions.status > 0";
        $db->query($sSQL);
        if($db->nextRecord()) {
            $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/mention/applets/list/detail.html", $cm->oPage->theme, false);
            if ($filename === null)
                $filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/mention/themes", "/applets/list/detail.html", $cm->oPage->theme);
                
            $tpl = ffTemplate::factory(ffCommon_dirname($filename));
            $tpl->load_file("detail.html", "main");

            $tpl->set_var("theme", $cm->oPage->theme);
            $tpl->set_var("site_path", $cm->oPage->site_path);

            $tpl->set_var("title", $db->getField("title", "Text", true));
            $tpl->set_var("description", $db->getField("description", "Text", true));
            
            $cm->oPage->title = $db->getField("title", "Text", true);
            $cm->oPage->tplAddMeta("description", trim(preg_replace('/(\r|\n|\")/', " ", strip_tags($db->getField("description", "Text", true)))));


            if(strlen( $db->getField("url", "Text", true))) {
                $tpl->set_var("url", $db->getField("url", "Text", true));
                if(strlen(global_settings("MOD_MENTION_CLASS_URL"))) {
                    $tpl->set_var("url_class", 'class="back ' . global_settings("MOD_MENTION_CLASS_URL") . '"');
                }
                $tpl->parse("SectMentionUrl", false);
            } else {
                $tpl->set_var("SectMentionUrl", "");
            }
            $tpl->set_var("published_at", $db->getField("published_at", "Timestamp")->getValue("DateTime", FF_LOCALE));
            if(strlen( $db->getField("source_name", "Text", true))) {
                $tpl->set_var("source_type", $db->getField("source_type", "Text", true));
                $tpl->set_var("source_url", $db->getField("source_url", "Text", true));
                $tpl->set_var("source_name", $db->getField("source_name", "Text", true));
                if(strlen(global_settings("MOD_MENTION_CLASS_SOURCE"))) {
                    $tpl->set_var("source_class", "class=" . global_settings("MOD_MENTION_CLASS_SOURCE"));
                }
                $tpl->parse("SectMentionSourceUrl", false);
                $tpl->set_var("SectMentionSourceNoUrl", "");
            } elseif(strlen($db->getField("source_name", "Text", true))) {
                $tpl->set_var("source_type", $db->getField("source_type", "Text", true));
                $tpl->set_var("source_name", $db->getField("source_name", "Text", true));
                $tpl->set_var("SectMentionSourceUrl", "");
                $tpl->parse("SectMentionSourceNoUrl", false);
            } else {
                $tpl->set_var("SectMentionSourceUrl", "");
                $tpl->set_var("SectMentionSourceNoUrl", "");
            }
            
            $tpl->set_var("mention_detail_back", $arrPathInfo[0] . global_settings("MOD_MENTION_CLASS_BASE_PATH"));
            
            $buffer = $tpl->rpparse("main", false);
        } else {
            $buffer = "";
        }
        
    } else {
        $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/mention/applets/list/index.html", $cm->oPage->theme, false);
        if ($filename === null)
            $filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/mention/themes", "/applets/list/index.html", $cm->oPage->theme);
        
        $tpl = ffTemplate::factory(ffCommon_dirname($filename));
        $tpl->load_file("index.html", "main");

        $tpl->set_var("theme", $cm->oPage->theme);
        $tpl->set_var("site_path", $cm->oPage->site_path);

        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_mentions.*
                    , IF(" . CM_TABLE_PREFIX . "mod_mention_mentions.unique_id
                        , " . CM_TABLE_PREFIX . "mod_mention_mentions.unique_id
                        , " . CM_TABLE_PREFIX . "mod_mention_mentions.url
                    ) AS url
                FROM " . CM_TABLE_PREFIX . "mod_mention_mentions
                    INNER JOIN " . CM_TABLE_PREFIX . "mod_mention_alerts ON " . CM_TABLE_PREFIX . "mod_mention_alerts.ID = " . CM_TABLE_PREFIX . "mod_mention_mentions.ID_alert
                WHERE " . CM_TABLE_PREFIX . "mod_mention_alerts.path = " . $db->toSql($user_path) . "
                    AND " . CM_TABLE_PREFIX . "mod_mention_mentions.status > 0";
        $db->query($sSQL);
        if($db->nextRecord()) {
            do {
                if(strlen($db->getField("smart_url", "Text", true))) {
                    $smart_url = $db->getField("smart_url", "Text", true);
                } else {
                    $smart_url = ffCommon_url_rewrite($db->getField("title", "Text", true));
                }
                
                $tpl->set_var("title", $db->getField("title", "Text", true));
                if(global_settings("MOD_MENTION_CLASS_BASE_PATH") && $smart_url) {
                    $tpl->set_var("smart_url",  $cm->oPage->site_path .  $cm->oPage->page_path . "/" . ffCommon_url_rewrite($db->getField("published_at", "Timestamp")->getValue("DateTime", FF_SYSTEM_LOCALE)) . "-" . $smart_url);    
                    $tpl->parse("SectMentionTitleUrl", false);
                    $tpl->set_var("SectMentionTitleNoUrl", "");
                } else {
                    $tpl->set_var("SectMentionTitleUrl", "");
                    $tpl->parse("SectMentionTitleNoUrl", false);
                }
                
                $tpl->set_var("description", $db->getField("description", "Text", true));

                if(strlen( $db->getField("url", "Text", true))) {
                    $tpl->set_var("url", $db->getField("url", "Text", true));
                    if(strlen(global_settings("MOD_MENTION_CLASS_URL"))) {
                        $tpl->set_var("url_class", "class=" . global_settings("MOD_MENTION_CLASS_URL"));
                    }
                    $tpl->parse("SectMentionUrl", false);
                } else {
                    $tpl->set_var("SectMentionUrl", "");
                }
                $tpl->set_var("published_at", $db->getField("published_at", "Timestamp")->getValue("DateTime", FF_LOCALE));
                if(strlen( $db->getField("source_name", "Text", true))) {
                    $tpl->set_var("source_type", $db->getField("source_type", "Text", true));
                    $tpl->set_var("source_url", $db->getField("source_url", "Text", true));
                    $tpl->set_var("source_name", $db->getField("source_name", "Text", true));
                    if(strlen(global_settings("MOD_MENTION_CLASS_SOURCE"))) {
                        $tpl->set_var("source_class", "class=" . global_settings("MOD_MENTION_CLASS_SOURCE"));
                    }
                    $tpl->parse("SectMentionSourceUrl", false);
                    $tpl->set_var("SectMentionSourceNoUrl", "");
                } elseif(strlen($db->getField("source_name", "Text", true))) {
                    $tpl->set_var("source_type", $db->getField("source_type", "Text", true));
                    $tpl->set_var("source_name", $db->getField("source_name", "Text", true));
                    $tpl->set_var("SectMentionSourceUrl", "");
                    $tpl->parse("SectMentionSourceNoUrl", false);
                } else {
                    $tpl->set_var("SectMentionSourceUrl", "");
                    $tpl->set_var("SectMentionSourceNoUrl", "");
                }
                $tpl->parse("SectMention", true);
            } while($db->nextRecord());
        }
        $buffer = $tpl->rpparse("main", false);
    }
}

$out_buffer = $buffer;