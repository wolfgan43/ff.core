<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"cloudzoom" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.cloudzoom",
							"file" => "jquery.cloudzoom.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.cloudzoom",
									"file" => "cloudzoom.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.cloudzoom"
									, "file" => "jquery.cloudzoom.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
