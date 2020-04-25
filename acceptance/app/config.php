<?php
return [
	'providers' => [
		// \App\View\ViewServiceProvider::class,
	],

	'routes' => [
		'web'   => [
			'definitions' => __DIR__ . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php',
		],
		'admin' => [
			'definitions' => __DIR__ . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'admin.php',
		],
		'ajax'  => [
			'definitions' => __DIR__ . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'ajax.php',
		],
	],

	'middleware' => [
		// 'mymiddleware' => \App\Middleware\MyMiddleware::class,
	],

	'middleware_groups' => [
		'global' => [],
		'web'    => [],
		'ajax'   => [],
		'admin'  => [],
	],
];
