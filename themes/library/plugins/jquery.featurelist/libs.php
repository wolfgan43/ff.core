<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"featurelist" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.featurelist",
							"file" => "jquery.featurelist.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.featurelist"
									, "file" => "jquery.featurelist.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
