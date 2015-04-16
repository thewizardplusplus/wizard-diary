<?php

return array(
	'name' => Constants::APP_NAME,
	'basePath' => __DIR__ . '/..',
	'defaultController' => 'point/list',
	'language' => 'ru',
	'preload' => array('log'),
	'import' => array(
		'application.components.*',
		'application.config.AccessConstants',
		'application.config.Constants',
		'application.controllers.AccessController',
		'application.models.*'
	),
	'components' => array(
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'login' => 'site/login',
				'access-code' => 'site/accessCode',
				'points' => 'point/list',
				'point/<id:\d+>/update' => 'point/update',
				'point/<id:\d+>/delete' => 'point/delete',
				'daily_points' => 'dailyPoint/list',
				'daily_point/<id:\d+>/update' => 'dailyPoint/update',
				'daily_point/<id:\d+>/delete' => 'dailyPoint/delete',
				'imports' => 'import/list',
				'import/<id:\d+>/view' => 'import/view',
				'import/<id:\d+>/update' => 'import/update',
				'import/<id:\d+>/import' => 'import/import',
				'stats/daily_points' => 'stats/dailyPoints',
				'backups' => 'backup/list',
				'parameters' => 'parameters/update',
				'accesses' => 'access/list'
			)
		),
		'db' => array(
			'connectionString' =>
				'mysql:host='
				. Constants::DATABASE_HOST
				. ';dbname='
				. Constants::DATABASE_NAME,
			'emulatePrepare' => true,
			'username' => Constants::DATABASE_USER,
			'password' => Constants::DATABASE_PASSWORD,
			'charset' => 'utf8',
			'tablePrefix' => Constants::DATABASE_TABLE_PREFIX
		),
		'session' => array(
			'class' => 'CDbHttpSession',
			'connectionID' => 'db',
			'sessionTableName' => Constants::DATABASE_TABLE_PREFIX . 'sessions'
		),
		'clientScript' => array(
			'packages' => array(
				'jquery' => array(
					'baseUrl' => 'https://code.jquery.com/',
					'js' => array('jquery-2.1.1.min.js')
				),
				'jquery.ui' => array(
					'baseUrl' => 'https://code.jquery.com/ui/1.10.4/',
					'js' => array('jquery-ui.min.js'),
					'css' => array('themes/start/jquery-ui.css'),
					'depends' => array('jquery')
				),
				'bootstrap' => array(
					'baseUrl' =>
						'https://netdna.bootstrapcdn.com/bootstrap/3.1.1/',
					'js' => array('js/bootstrap.min.js'),
					'css' => array('css/bootstrap.min.css'),
					'depends' => array('jquery')
				)
 			)
		),
		'widgetFactory' => array(
			'widgets' => array(
				'CJuiAutoComplete' => array(
					'scriptUrl' => 'https://code.jquery.com/ui/1.10.4',
					'themeUrl' => 'https://code.jquery.com/ui/1.10.4/themes',
					'theme' => 'start'
				),
				'CJuiTabs' => array(
					'scriptUrl' => 'https://code.jquery.com/ui/1.10.4',
					'themeUrl' => 'https://code.jquery.com/ui/1.10.4/themes',
					'theme' => 'start'
				)
			)
		),
		'request' => array(
			'enableCsrfValidation' => true,
			'enableCookieValidation' => true
		),
		'log' => array(
			'class'=>'CLogRouter',
			'routes' => array_merge(
				array(array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error'
				)),
				Constants::DEBUG
					? array(array(
						'class' => 'CWebLogRoute',
						'levels' => 'trace, info, warning, error'
					))
					: array()
			)
		),
		'errorHandler' => array('errorAction' => 'site/error')
	),
	'onBeginRequest' => array('BeginRequestHandler', 'handle')
);
