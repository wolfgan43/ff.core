<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"owlcarousel2" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel2",
							"file" => "jquery.owlcarousel.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel2",
									"file" => "jquery.owlcarousel.css"
								)
								, ".theme" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.owlcarousel2",
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
