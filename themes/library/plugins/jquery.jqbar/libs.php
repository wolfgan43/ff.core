<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"jqbar" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.jqbar",
							"file" => "jquery.jqbar.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.jqbar",
									"file" => "jquery.jqbar.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.jqbar"
									, "file" => "jquery.jqbar.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
