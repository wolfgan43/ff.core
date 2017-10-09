<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"crosslide" => array(
							"type" => "slider",
							"path" => FF_THEME_DIR . "/library/plugins/jquery.crosslide",
							"file" => "jquery.crosslide.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.crosslide",
									"file" => "jquery.crosslide.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.crosslide"
									, "file" => "jquery.crosslide.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
