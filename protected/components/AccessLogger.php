<?php

class AccessLogger {
	public static function log() {
		$access_log_record = new AccessLogRecord();
		$access_log_record->save();
	}
}
