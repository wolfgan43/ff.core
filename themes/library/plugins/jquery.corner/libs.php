<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"corner" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.corner",
							"file" => "jquery.corner.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.corner"
									, "file" => "jquery.corner.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
