<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"socialcount" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.socialcount",
							"file" => "jquery.socialcount.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.socialcount",
									"file" => "jquery.socialcount.icons.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.socialcount"
									, "file" => "jquery.socialcount.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
