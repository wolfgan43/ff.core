<?php

return array(
	'ff' => array(
		'latest' => array(
			'js_defs' => array(
				'ffPage' => array(
					'js_defs' => array(
						'dialog' => array(
							'path' => '/themes/restricted/ff/ffPage/widgets/dialog',
							'file' => 'dialog.js',
							'index' => 100,
							'js_deps' => array(
								'ff.ajax' => NULL
								, "jquery-ui" => NULL
							),
							'css_deps' => array(
								'jquery-ui.core' => NULL,
								'jquery-ui.theme' => NULL,
								'jquery-ui.button' => NULL,
								'jquery-ui.dialog' => NULL,
								'jquery-ui.resizable' => NULL
							)
						)
					)
				)
			)
		)
	)
);
