<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"photowall" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.photowall",
							"file" => "jquery.photowall.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.photowall",
									"file" => "jquery.photowall.css"
								)
							)
						)
					)
				)
			)
		)
	)
);
