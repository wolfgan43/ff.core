<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"bbq" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.bbq",
							"file" => "jquery.bbq.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.bbq"
									, "file" => "jquery.bbq.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
