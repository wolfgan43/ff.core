<?php
	return array(
		"jquery" => array(
			"all" => array(
				"js_defs" => array(
					"plugins" => array(
						"empty" => true,
						"js_defs" => array(
							'uploadify' => 
							array (
								'path' => FF_THEME_DIR . '/library/plugins/jquery.uploadify',
								'file' => 'jquery.uploadify.js',
								'index' => 200,
								"js_deps" => array(
									"library.swfobject" => null
								),
								'css_deps' => 
								array (
									'.style' => 
									array (
										'path' => FF_THEME_DIR . '/library/plugins/jquery.uploadify',
										'file' => 'uploadify.css'
									)
								)
								, "js_defs" => array(
									"observe" => array(
										"path" => FF_THEME_DIR . "/library/plugins/jquery.uploadify"
										, "file" => "jquery.uploadify.observe.js"
									)
									, "dialog" => array(
										"path" => FF_THEME_DIR . "/library/plugins/jquery.uploadify"
										, "file" => "jquery.uploadify.observe.dialog.js"
									) 
								)
							)
						)
					)
				)
			)
		)
	);