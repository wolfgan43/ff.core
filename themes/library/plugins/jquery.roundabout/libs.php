<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"roundabout" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.roundabout",
							"file" => "jquery.roundabout.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.roundabout"
									, "file" => "jquery.roundabout.observe.js"
								)
							)
							, "js_loads" => array(
								".shapes" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.roundabout",
									"file" => "jquery.roundabout-shapes.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
