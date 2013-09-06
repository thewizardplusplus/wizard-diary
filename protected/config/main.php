<?php

return array(
	'language' => 'ru',
	'name' => 'Online-дневник',
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'defaultController' => 'point/list',
	'import' => array(
		'application.models.*',
		'application.components.*'
	),
	//TODO: remove it.
	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'admin'
		)
	),
	'components' => array(
		'user' => array(
			'allowAutoLogin' => true
		),
		//TODO: setting it.
		/*'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),*/
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=diary',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => 'diary_'
		),
		'errorHandler' => array(
			'errorAction' => 'site/error',
		)
	)
);
