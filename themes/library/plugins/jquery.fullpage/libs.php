<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"fullpage" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.fullpage",
							"file" => "jquery.fullPage.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.fullpage",
									"file" => "jquery.fullPage.css"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.slimscroll" => null
							)
						)
					)
				)
			)
		)
	)
);
