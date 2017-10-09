<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"fbalbum" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.fbalbum",
							"file" => "jquery.fbalbum.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.fbalbum/css",
									"file" => "jquery.fbalbum.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.fbalbum"
									, "file" => "jquery.fbalbum.observe.js"
								)
							)
							, "js_loads" => array(
								".lang" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.fbalbum/Language",
									"file" => strtolower(LANGUAGE_INSET_TINY) . ".js"
								)							
							)
							, "js_deps" => array(
								"facebook" => null
								, "jquery.plugins.isotope" => null
							)
						)
					)
				)
			)
		)
	)
);
