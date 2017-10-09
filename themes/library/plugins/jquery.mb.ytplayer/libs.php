<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"mb-ytplayer" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.mb.ytplayer",
							"file" => "jquery.mb.ytplayer.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.mb.ytplayer",
									"file" => "mb.ytplayer.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.mb.ytplayer"
									, "file" => "jquery.mb.ytplayer.observe.js"
								)
							)
							, "js_deps" => array(
								"jquery.plugins.metadata" => null
							)
						)
					)
				)
			)
		)
	)
);
