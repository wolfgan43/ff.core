<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"mediagallery" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.mediagallery",
							"file" => "jquery.mediagallery.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.mediagallery",
									"file" => "mediagallery-normal.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.mediagallery"
									, "file" => "jquery.mediagallery.observe.js"
								)
							)
							, "js_deps" => array(
								"jquery-ui" => null
								, "swfobject" => null
								, "jquery.plugins.mousewheel" => null
							
							)
						)
					)
				)
			)
		)
	)
);
