<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"simplemenu" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenu",
							"file" => "jquery.simplemenu.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenu",
									"file" => "jquery.simplemenu.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenu"
									, "file" => "jquery.simplemenu.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
