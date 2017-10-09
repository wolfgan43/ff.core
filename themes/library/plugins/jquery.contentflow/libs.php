<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"contentflow" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow",
							"file" => "jquery.contentflow.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow",
									"file" => "contentflow.css"
								)
								, ".addon-white" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow",
									"file" => "ContentFlowAddOn_white.css"
								)
							)
							, "js_defs" => array(
								"observe" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow"
									, "file" => "jquery.contentflow.observe.js"
								)
							)
							, "js_deps" => array(
								".config" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow",
									"file" => "jquery.contentflow.config.js"
								)
							)
							, "js_loads" => array(
								".addon-white" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.contentflow",
									"file" => "ContentFlowAddOn_white.js"
								)							
							)
						)
					)
				)
			)
		)
	)
);
