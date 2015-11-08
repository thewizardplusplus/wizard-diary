<?php

return array(
	'name' => Constants::APP_NAME,
	'basePath' => __DIR__ . '/..',
	'defaultController' => 'day/list',
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
				'days' => 'day/list',
				'day/<date:\d\d\d\d-\d\d-\d\d>' => 'day/view',
				'day/<date:\d\d\d\d-\d\d-\d\d>/update' => 'day/update',
				'daily_points' => 'dailyPoint/list',
				'daily_point/<id:\d+>/update' => 'dailyPoint/update',
				'daily_point/<id:\d+>/delete' => 'dailyPoint/delete',
				'stats/daily_points' => 'stats/dailyPoints',
				'stats/project_list' => 'stats/projectList',
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
					'js' => array('jquery-2.1.4.min.js')
				),
				'jquery.ui' => array(
					'baseUrl' => 'https://code.jquery.com/ui/1.11.4/',
					'js' => array('jquery-ui.min.js'),
					'css' => array('themes/start/jquery-ui.css'),
					'depends' => array('jquery')
				),
				'bootstrap' => array(
					'baseUrl' =>
						'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/',
					'js' => array('js/bootstrap.min.js'),
					'css' => array('css/bootstrap.min.css'),
					'depends' => array('jquery')
				),
				'purl' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/purl/2.3.1/',
					'js' => array('purl.min.js'),
					'depends' => array('jquery')
				),
				'jeditable' => array(
					'baseUrl' =>
						'https://cdn.jsdelivr.net/jquery.jeditable/1.7.3/',
					'js' => array('jquery.jeditable.js'),
					'depends' => array('jquery')
				),
				'moment' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/',
					'js' => array('moment-with-locales.min.js')
				),
				'sortable' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/',
					'js' => array('jquery-sortable-min.js'),
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
