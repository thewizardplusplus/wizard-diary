<?php

$time_zone = getenv('TZ');
if (!empty($time_zone)) {
	ini_set('date.timezone', $time_zone);
}

define('YII_BEGIN_TIME', microtime(true));

require_once(__DIR__ . '/protected/config/Constants.php');

define('YII_DEBUG', Constants::DEBUG);
if (Constants::DEBUG) {
	define('YII_TRACE_LEVEL', Constants::TRACE_LEVEL);
}

require_once(__DIR__ . '/yii/framework/yii.php');
Yii::createWebApplication(__DIR__ . '/protected/config/main.php')->run();
