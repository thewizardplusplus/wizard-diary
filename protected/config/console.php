<?php

return array(
	'name' => Constants::APP_NAME,
	'basePath' => __DIR__ . '/..',
	'language' => 'ru',
	'preload' => array('log'),
	'import' => array(
		'application.components.*',
		'application.config.AccessConstants',
		'application.config.Constants',
		'application.models.*'
	),
	'components' => array(
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
		'log' => array(
			'class'=>'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace, info, warning, error'
				)
			)
		)
	)
);
