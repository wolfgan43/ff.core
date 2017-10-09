<?php             
  $db = ffDB_Sql::factory();
  
  $task_smart_url = basename($cm->real_path_info);

  if(strlen($task_smart_url)) {
      $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_task.* 
                FROM " . CM_TABLE_PREFIX . "mod_task
                WHERE " . CM_TABLE_PREFIX . "mod_task.smart_url = " . $db->toSql($task_smart_url);
      $db->query($sSQL);
      if($db->nextRecord()) {
          $tpl = ffTemplate::factory(FF_DISK_PATH . "/modules/task/themes/restricted/task");
          $tpl->load_file("index.html", "main");
          
          $tpl->set_var("smart_url", $db->getField("smart_url", "Text", true));
          $tpl->set_var("title", $db->getField("description", "Text", true));
          if($db->getField("full_description", "Text", true)) {
            $tpl->set_var("full_description", $db->getField("full_description", "Text", true));
            $tpl->parse("SezFulldescription", false);
          } else {
              $tpl->set_var("SezFulldescription", "");
          }

          echo $tpl->rpparse("main", false);
      }
  }

  exit;
