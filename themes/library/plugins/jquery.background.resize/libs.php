<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"background-resize" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.background.resize",
							"file" => "jquery.background.resize.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.background.resize"
									, "file" => "jquery.background.resize.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
