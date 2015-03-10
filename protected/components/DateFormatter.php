<?php

class DateFormatter {
	public static function formatMyDate($my_date) {
		$start_date =
			Yii::app()->db
			->createCommand('SELECT MIN(date) FROM {{points}}')
			->queryScalar();

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
}
