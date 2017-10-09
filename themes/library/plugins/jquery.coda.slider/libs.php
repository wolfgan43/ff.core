<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"coda-slider" => array(
							"type" => "slider",
							"path" => FF_THEME_DIR . "/library/plugins/jquery.coda.slider",
							"file" => "jquery.coda.slider.js",
							"css_loads" => array(
								".style" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.coda.slider",
									"file" => "coda.slider.css"
								)
							)
						)
					)
				)
			)
		)
	)
);
