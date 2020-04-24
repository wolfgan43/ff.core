<?php
return array(
	"ff" => array(
		"latest" => array(
			"js_defs" => array(
				"ffField" => array(
					"js_defs" => array(
						"uploadex" => array(
							"path" => FF_THEME_DIR . "/responsive/ff/ffField/widgets/uploadex",
							"file" => "uploadex.js",
							'js_deps' => array(
								'ff.ajax' => NULL
								, "jquery-ui" => NULL
							),
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/responsive/ff/ffField/widgets/uploadex",
									"file" => "uploadex.css",
								),
								'jquery-ui.core' => NULL,
							),
							"js_defs" => array(
								"plugins" => array(
									"empty" => true,
									"js_defs" => array(
										"fancybox" => array(
											"path" => FF_THEME_DIR . "/responsive/ff/ffField/widgets/uploadex",
											"file" => "plugin.fancybox.js",
											"js_deps" => array(
												"jquery.plugins.fancybox" => null
											)
										)
									)
								)
							)
						)
					)
				)
			)
		)
	)
);
