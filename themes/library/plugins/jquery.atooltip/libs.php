<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"atooltip" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.atooltip",
							"file" => "jquery.atooltip.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.atooltip/css",
									"file" => "atooltip.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.atooltip"
									, "file" => "jquery.atooltip.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
