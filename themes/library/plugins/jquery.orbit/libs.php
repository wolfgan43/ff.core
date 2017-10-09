<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"orbit" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.orbit",
							"file" => "jquery.orbit.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.orbit",
									"file" => "orbit.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.orbit"
									, "file" => "jquery.orbit.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
