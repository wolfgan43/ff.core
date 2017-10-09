<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"jscrollpane" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.jscrollpane",
							"file" => "jquery.jscrollpane.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.jscrollpane",
									"file" => "jquery.jscrollpane.css"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.mwint" => null
							)
						)
					)
				)
			)
		)
	)
);
