<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"kwicks" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.kwicks",
							"file" => "jquery.kwicks.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.kwicks"
									, "file" => "jquery.kwicks.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
