<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"coverflip" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.coverflip",
							"file" => "jquery.coverflip.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.coverflip"
									, "file" => "jquery.coverflip.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
