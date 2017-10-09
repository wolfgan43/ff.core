<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"checkbox" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.checkbox",
							"file" => "jquery.checkbox.js",
							"js_loads" => array(
								".observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.checkbox",
									"file" => "jquery.checkbox.observe.js",
								)
							)
						)
					)
				)
			)
		)
	)
);
