<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"datepair" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.datepair",
							"file" => "jquery.datepair.js"
							, "js_deps" => array(
								"jquery.plugins.timepicker" => null
							)
						)
					)
				)
			)
		)
	)
);
