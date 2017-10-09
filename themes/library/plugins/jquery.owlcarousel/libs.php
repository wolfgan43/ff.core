<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"owlcarousel" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel",
							"file" => "jquery.owlcarousel.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel",
									"file" => "jquery.owlcarousel.css"
								)
								, ".theme" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel",
									"file" => "jquery.owltheme.css"
								)
							)
						)
					)
				)
			)
		)
	)
);
