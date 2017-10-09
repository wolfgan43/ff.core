<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"liscroll" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.liscroll",
							"file" => "jquery.liscroll.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.liscroll",
									"file" => "liscroll.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.liscroll"
									, "file" => "jquery.liscroll.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
