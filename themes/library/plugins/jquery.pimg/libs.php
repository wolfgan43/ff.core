<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"pimg" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.pimg",
							"file" => "jquery.pimg.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.pimg"
									, "file" => "jquery.pimg.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
