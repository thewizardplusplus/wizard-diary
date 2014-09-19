<?php

class Constants {
	const DEBUG = false;
	const TRACE_LEVEL = 5;
	const APP_NAME = 'Wizard Diary';
	const DATABASE_HOST = 'localhost';
	const DATABASE_NAME = 'diary';
	const DATABASE_USER = 'root';
	const DATABASE_PASSWORD = '';
	const DATABASE_TABLE_PREFIX = 'diary_';
	const DEFAULT_PASSWORD = 'admin';
	const RECAPTCHA_PUBLIC_KEY = '6Lcm7_kSAAAAABWsfXQjmzXIc867ukoIpLi5m9X2';
	const RECAPTCHA_PRIVATE_KEY = '6Lcm7_kSAAAAAFpu9z5F2uEbbldK--dNXSZ-ZFq1';
	const COPYRIGHT_START_YEAR = 2014;
	const DAYS_IN_MY_YEAR = 300;
	const POINTS_ON_PAGE_DEFAULT = 24;
	// relatively at /protected/controllers
	const BACKUPS_RELATIVE_PATH = '/../../backups';
	const BACKUPS_CREATE_DURATION_ACCURACY = 2;
	const BACKUPS_CREATE_SOFT_LIMIT = 0.75;
	const BACKUPS_CREATE_HARD_LIMIT = 0.9;
	const BACKUPS_SIZE_ACCURACY = 2;
	const DROPBOX_APP_NAME = 'wizard-diary-debug';
	const DROPBOX_APP_KEY = 'd4m4a42cz57tuh0';
	const DROPBOX_APP_SECRET = 'jpblp3axnke90hx';
	const DROPBOX_REDIRECT_URL = '/wizard-diary/backup/redirect';
	const SMS_RU_LOGIN = '79307808612';
	const SMS_RU_PASSWORD = '';
	const ACCESS_CODE_LENGTH = 5;
	// 5 minute
	const ACCESS_LOG_LIFETIME_IN_S = 300;
}
