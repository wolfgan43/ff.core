<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"prettyphoto" => array(
							"type" => "viewer",
							"path" => FF_THEME_DIR . "/library/plugins/jquery.prettyphoto",
							"file" => "jquery.prettyphoto.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.prettyphoto",
									"file" => "jquery.prettyphoto.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.prettyphoto"
									, "file" => "jquery.prettyphoto.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
