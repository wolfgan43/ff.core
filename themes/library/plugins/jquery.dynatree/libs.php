<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"dynatree" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.dynatree",
							"file" => "jquery.dynatree.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.dynatree/skin",
									"file" => "ui.dynatree.css"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.cookie" => null
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.dynatree"
									, "file" => "jquery.dynatree.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
