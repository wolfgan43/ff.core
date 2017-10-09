<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"pnotify" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.pnotify",
							"file" => "jquery.pnotify.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.pnotify",
									"file" => "jquery.pnotify.default.css"
								)
							)
						)
					)
				)
			)
		)
	)
);
