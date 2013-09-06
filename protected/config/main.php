<?php

return array(
	'name' => 'Online-дневник',
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'defaultController' => 'point/list',
	'language' => 'ru',
	'import' => array(
		'application.models.*',
		'application.components.*'
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
				'parameters' => 'parameters/update'
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
		'errorHandler' => array('errorAction' => 'site/error')
	)
);
