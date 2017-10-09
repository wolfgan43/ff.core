<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"isotope" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.isotope",
							"file" => "jquery.isotope.js"
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.isotope"
									, "file" => "jquery.isotope.observe.js"
								)
							)
						)
					)
				)
			)
		)
	)
);
