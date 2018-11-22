<?php

  $cm->oPage->tplAddJs("ff.cms.doc", "ff.cms.doc.js", FF_THEME_DIR . "/" . THEME_INSET . "/javascript/tools");

  $tpl = ffTemplate::factory(get_template_cascading($globals->user_path, "api.html", "/doc"));
  $tpl->load_file("api.html", "main");

  $buffer = $tpl->rpparse("main", false);
  
  if(strlen($buffer))
  	$cm->oPage->addContent($buffer);
