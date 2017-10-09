<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"countdown" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.countdown",
							"file" => "jquery.countdown.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.countdown",
									"file" => "jquery.countdown.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.countdown"
									, "file" => "jquery.countdown.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
