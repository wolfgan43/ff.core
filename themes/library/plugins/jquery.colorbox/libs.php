<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"colorbox" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.colorbox",
							"file" => "jquery.colorbox.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.colorbox",
									"file" => "jquery.colorbox.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.colorbox"
									, "file" => "jquery.colorbox.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
