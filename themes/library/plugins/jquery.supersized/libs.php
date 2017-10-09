<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"supersized" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.supersized",
							"file" => "jquery.supersized.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.supersized/css",
									"file" => "supersized.css"
								)
								, ".shutter" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.supersized/theme",
									"file" => "supersized.shutter.css"
								)
							)
							, "js_loads" => array(
								".shutter" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.supersized/theme",
									"file" => "supersized.shutter.js"
								)							
							)
						)
					)
				)
			)
		)
	)
);
