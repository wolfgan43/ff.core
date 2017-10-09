<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"cluetip" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.cluetip",
							"file" => "jquery.cluetip.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.cluetip",
									"file" => "jquery.cluetip.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.cluetip"
									, "file" => "jquery.cluetip.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
