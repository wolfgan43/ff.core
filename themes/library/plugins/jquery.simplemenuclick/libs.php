<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"simplemenuclick" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenuclick",
							"file" => "jquery.simplemenuclick.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenuclick",
									"file" => "jquery.simplemenuclick.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.simplemenuclick"
									, "file" => "jquery.simplemenuclick.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
