<?php

require_once(__DIR__ . '/AccessConstants.php');

class Constants extends AccessConstants {
	const DEBUG = false;
	const TRACE_LEVEL = 5;
	const APP_NAME = 'Wizard Diary';
	const APP_VERSION = 'v7.5';
	const DATABASE_TABLE_PREFIX = 'diary_';
	const DEFAULT_PASSWORD = 'admin';
	const COPYRIGHT_START_YEAR = 2013;
	const DAYS_IN_MY_YEAR = 300;
	const DAYS_IN_MY_STREAK = 12;
	// relatively at /protected/controllers
	const BACKUPS_RELATIVE_PATH = '/../../dumps';
	const BACKUPS_CREATE_DURATION_ACCURACY = 2;
	const BACKUPS_CREATE_SOFT_LIMIT = 0.75;
	const BACKUPS_CREATE_HARD_LIMIT = 0.9;
	const BACKUPS_SIZE_ACCURACY = 2;
	const DROPBOX_REDIRECT_URL = '/backup/redirect';
	const SESSION_LIFETIME_IN_MIN_DEFAULT = 12;
	const SESSION_LIFETIME_IN_MIN_MINIMUM = 1;
	// 12 days
	const SESSION_LIFETIME_IN_MIN_MAXIMUM = 17280;
	const ACCESS_CODE_LENGTH = 5;
	const ACCESS_CODE_LIFETIME_IN_S = 60;
	// support 'sms', 'email', 'log' and unions by '|'
	const ACCESS_CODE_TARGETS = 'log';
	// 30 days
	const ACCESS_LOG_LIFETIME_IN_S_DEFAULT = 2592000;
	const ACCESS_LOG_LIFETIME_IN_S_MINIMUM = 0;
	const ACCESS_LOG_LIFETIME_IN_S_MAXIMUM = 2147483647;
	const ACCESS_LOG_UPDATE_PAUSE_IN_S = 30;
	// 1 minute
	const LOGIN_LIMIT_TIME_WINDOW_IN_S = 60;
	const LOGIN_LIMIT_MAXIMAL_COUNT = 12;
	const MINIMAL_ORDER_VALUE = 3;
	const MAXIMAL_ORDER_VALUE = 1000000;
}
