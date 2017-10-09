<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"lightbox" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.lightbox",
							"file" => "jquery.lightbox.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.lightbox/css",
									"file" => "jquery.lightbox.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.lightbox"
									, "file" => "jquery.lightbox.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
