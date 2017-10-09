<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"qtip2" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.qtip2",
							"file" => "jquery.qtip.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.qtip2",
									"file" => "jquery.qtip.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.qtip2"
									, "file" => "jquery.qtip.observe.js"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.imagesloaded" => null
							)
						)
					)
				)
			)
		)
	)
);
