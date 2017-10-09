<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"carousel" => array(
							"type" => "carousel",
							"path" => FF_THEME_DIR . "/library/plugins/jquery.carousel",
							"file" => "jquery.carousel.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.carousel/css",
									"file" => "carousel.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.carousel"
									, "file" => "jquery.carousel.observe.js"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.mousewheel" => null
							
							)
						)
					)
				)
			)
		)
	)
);
