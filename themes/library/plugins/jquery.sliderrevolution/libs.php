<?php
return array(
	"jquery" => array(
		"all" => array(
			"js_defs" => array(
				"plugins" => array(
					"empty" => true,
					"js_defs" => array(
						"sliderrevolution" => array(
							"path" => FF_THEME_DIR . "/library/plugins/jquery.sliderrevolution",
							"file" => "jquery.themepunch.revolution.min.js",
							"tpl" => "ulli", //divimg ulli listdiv thumb
							"css_loads" => array(
								".settings" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.sliderrevolution/css",
									"file" => "settings.css"
								),
								".layers" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.sliderrevolution/css",
									"file" => "layers.css"
								),
								".navigation" => array(
									"path" => FF_THEME_DIR . "/library/plugins/jquery.sliderrevolution/css",
									"file" => "navigation.css"
								)
							)
							, 'js_defs' => array(
								'tools' => 
								array(
									'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution',
									'file' => 'jquery.themepunch.tools.min.js'
								),
								"addons" => array(
									"empty" => true
									, "js_defs" => array(
										'actions' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.actions.min.js'
										),
										'carousel' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.carousel.min.js'
										),
										'kenburn' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.kenburn.min.js'
										),
										'layeranimation' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.layeranimation.min.js'
										),
										'migration' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.migration.min.js'
										),
										'navigation' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.navigation.min.js'
										),
										'parallax' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.parallax.min.js'
										),
										'slideanims' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.slideanims.min.js'
										),
										'video' => 
										array(
											'path' => FF_THEME_DIR . '/library/plugins/jquery.sliderrevolution/extensions',
											'file' => 'revolution.extension.video.min.js'
										)
									)
								)
							),
							'js_loads' => 
							array(
								"jquery.sliderrevolution.tools" => null
							)
						)
					)
				)
			)
		)
	)
);
