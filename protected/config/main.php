<?php

return array(
	'name' => 'Online-дневник',
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'defaultController' => 'point/list',
	'language' => 'ru',
	'preload' => array('log'),
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.config.Constants'
	),
	'components' => array(
		'user' => array('allowAutoLogin' => true),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => FALSE,
			'rules' => array(
				'login' => 'site/login',
				'logout' => 'site/logout',
				'points' => 'point/list',
				'point/<id:\d+>/update' => 'point/update',
				'point/<id:\d+>/delete' => 'point/delete',
				'parameters' => 'parameters/update',
				'backups' => 'backup/list',
				'backups/new' => 'backup/new'
			)
		),
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=diary',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'diary_'
		),
		'log' => array(
			'class'=>'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'logFile' => 'backups.log',
					'levels' => 'info',
					'categories' => 'backups'
				),
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error'
				),
				array(
					'class' => 'CWebLogRoute',
					'levels' => 'trace, info, warning, error'
				)
			),
		),
		'errorHandler' => array('errorAction' => 'site/error')
	)
);
