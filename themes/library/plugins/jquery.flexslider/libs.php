<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"flexslider" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.flexslider",
							"file" => "jquery.flexslider.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.flexslider",
									"file" => "flexslider.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.flexslider"
									, "file" => "jquery.flexslider.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
