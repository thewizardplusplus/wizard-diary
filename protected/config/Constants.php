<?php

class Constants {
	const DEBUG = false;
	const TRACE_LEVEL = 5;
	const APP_NAME = 'Online-дневник';
	const DATABASE_HOST = 'localhost';
	const DATABASE_NAME = 'diary';
	const DATABASE_USER = 'root';
	const DATABASE_PASSWORD = '';
	const COPYRIGHT_START_YEAR = 2014;
	// 30 days
	const REMEMBER_DURATION_IN_S = 2592000;
	const POINTS_ON_PAGE = 3;
	const DAYS_IN_MY_YEAR = 300;
	const DEFAULT_PASSWORD = 'admin';
	// relatively at /protected/controllers
	const BACKUPS_RELATIVE_PATH = '/../../backups';
}
