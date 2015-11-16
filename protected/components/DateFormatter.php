<?php

class DateFormatter {
	public static function formatDate($date) {
		return implode('.', array_reverse(explode('-', $date)));
	}

	public static function getStartDate() {
		return
			Yii::app()
			->db
			->createCommand('SELECT MIN(date) FROM {{points}}')
			->queryScalar();
	}

	public static function formatMyDate($my_date, $start_date = NULL) {
		if (is_null($start_date)) {
			$start_date = self::getStartDate();
		}

		$difference = date_diff(
			date_create($start_date),
			date_create($my_date)
		);
		$days = $difference->days;

		$my_day = $days % Constants::DAYS_IN_MY_YEAR + 1;
		if ($my_day < 10) {
			$my_day = '0' . $my_day;
		}
		$my_year = round($days / Constants::DAYS_IN_MY_YEAR) + 1;
		if ($my_year < 10) {
			$my_year = '0' . $my_year;
		}

		return $my_day . '.' . $my_year;
	}

	public static function getDatePartsInMyFormat($date) {
		$my_date = self::formatMyDate($date);
		$my_date_parts = array_map('intval', explode('.', $my_date));

		$result = new stdClass;
		$result->day = $my_date_parts[0];
		$result->year = $my_date_parts[1];

		return $result;
	}
}
