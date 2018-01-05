<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"calendario" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.calendario",
							"file" => "jquery.calendario.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.calendario",
									"file" => "calendar.css",
								)
								, ".custom" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.calendario",
									"file" => "custom_2.css",
								)
							)
						)
					)
				)
			)
		)
	)
);
