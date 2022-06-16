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
		'user' => array('allowAutoLogin' => true, 'autoRenewCookie' => true),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'login' => 'site/login',
				'access_code' => 'site/accessCode',
				'days' => 'day/list',
				'day/<date:\d{4}(?:-\d{2}){2}>' => 'day/view',
				'day/<date:\d{4}(?:-\d{2}){2}>/update/line:<line:\d+>' =>
					'day/update',
				'day/<date:\d{4}(?:-\d{2}){2}>/update' => 'day/update',
				'daily_points' => 'dailyPoint/list',
				'daily_points/update' => 'dailyPoint/update',
				'import_points' => 'day/import',
				'update_points' => 'point/updateBatch',
				'delete_points' => 'point/deleteBatch',
				'stats/daily_points' => 'stats/dailyPoints',
				'stats/future_achievements' => 'stats/futureAchievements',
				'stats/daily_point_list' => 'stats/dailyPointList',
				'stats/project_list' => 'stats/projectList',
				'stats/project_actions' => 'stats/projectActions',
				'stats/projects/short' => array(
					'stats/projects',
					'defaultParams'=>array('tasks_required' => false)
				),
				'stats/projects/full' => array(
					'stats/projects',
					'defaultParams'=>array('tasks_required' => true)
				),
				'mistakes' => 'mistake/list',
				'spellings' => 'spelling/list',
				'spelling/delete' => 'spelling/delete',
				'backups' => 'backup/list',
				'backup/current_diff/<file:\d{4}(?:-\d{2}){5}>' =>
					'backup/currentDiff',
				'backup/diff'
					. '/<file:\d{4}(?:-\d{2}){5}>'
					. '/<previous_file:\d{4}(?:-\d{2}){5}>' =>
					'backup/diff',
				'parameters' => 'parameters/update',
				'accesses' => 'access/list',
				'whitelist' => 'access/whitelist'
			)
		),
		'db' => array(
			'connectionString' =>
				'mysql:host='
				. Constants::DATABASE_HOST
				. ';dbname='
				. Constants::DATABASE_NAME,
			'emulatePrepare' => true,
			'enableProfiling' => true,
			'username' => Constants::DATABASE_USER,
			'password' => Constants::DATABASE_PASSWORD,
			'charset' => 'utf8',
			'tablePrefix' => Constants::DATABASE_TABLE_PREFIX
		),
		'session' => array(
			'class' => 'CDbHttpSession',
			'connectionID' => 'db',
			'sessionTableName' => Constants::DATABASE_TABLE_PREFIX . 'sessions',
			'timeout' => Constants::SESSION_LIFETIME_IN_MIN_MINIMUM * 60
		),
		'clientScript' => array(
			'packages' => array(
				'jquery' => array(
					'baseUrl' => 'https://code.jquery.com/',
					'js' => array('jquery-2.1.4.min.js')
				),
				'bootstrap' => array(
					'baseUrl' =>
						'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/',
					'js' => array('js/bootstrap.min.js'),
					'css' => array('css/bootstrap.min.css'),
					'depends' => array('jquery')
				),
				'jquery.ui' => array(
					'baseUrl' => 'https://code.jquery.com/ui/1.11.4/',
					'js' => array('jquery-ui.min.js'),
					'css' => array('themes/start/jquery-ui.css'),
					'depends' => array(
						'jquery',
						// doesn't depend, but should override
						'bootstrap'
					)
				),
				'moment' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/'
							. 'moment.js/2.10.6/',
					'js' => array('moment-with-locales.min.js')
				),
				'jstree' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/',
					'js' => array('jstree.min.js'),
					'css' => array('themes/default/style.min.css'),
					'depends' => array('jquery')
				),
				'ace' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.2/',
					'js' => array('ace.js')
				),
				'ace-language-tools' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.2/',
					'js' => array('ext-language_tools.js')
				),
				// need only for 'ace/mode/behaviour/cstyle' module
				'ace-mode-javascript' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.2/',
					'js' => array('mode-javascript.js')
				),
				'mobile-detect' => array(
					'baseUrl' =>
						'https://cdnjs.cloudflare.com/ajax/libs/'
							. 'mobile-detect/1.3.0/',
					'js' => array('mobile-detect.min.js')
				),
				'jdenticon' => array(
					'baseUrl' => 'https://cdn.jsdelivr.net/jdenticon/1.3.2/',
					'js' => array('jdenticon.min.js')
				),
				'awesome-bootstrap-checkbox' => array(
					'baseUrl' => 'https://cdnjs.cloudflare.com/ajax/libs/'
						. 'awesome-bootstrap-checkbox/0.3.5/',
					'css' => array('awesome-bootstrap-checkbox.min.css'),
					'depends' => array('bootstrap')
				),
				'bootstrap-select' => array(
					'baseUrl' => 'https://cdnjs.cloudflare.com/ajax/libs/'
						. 'bootstrap-select/1.7.5/',
					'js' => array('js/bootstrap-select.min.js'),
					'css' => array('css/bootstrap-select.min.css'),
					'depends' => array('jquery', 'bootstrap')
				),
				'bootstrap-select-i18n' => array(
					'baseUrl' => 'https://cdnjs.cloudflare.com/ajax/libs/'
						. 'bootstrap-select/1.7.5/',
					'js' => array('js/i18n/defaults-ru_RU.min.js'),
					'depends' => array('bootstrap-select')
				),
				'URI' => array(
					'baseUrl' => 'https://cdnjs.cloudflare.com/ajax/libs/'
						. 'URI.js/1.17.0/',
					'js' => array('URI.min.js')
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
