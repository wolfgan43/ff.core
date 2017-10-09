<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"easyslider" => array(
							"type" => "slider",
							"path" => FF_THEME_DIR . "/library/plugins/jquery.easyslider",
							"file" => "jquery.easyslider.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.easyslider/css",
									"file" => "jquery.easyslider.css"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.cookie" => null
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.easyslider"
									, "file" => "jquery.easyslider.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
