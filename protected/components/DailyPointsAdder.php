<?php

class DailyPointsAdder {
	public static function addDailyPoints() {
		$date = date('Y-m-d');
		Yii::app()
			->db
			->createCommand(
				'INSERT INTO {{points}} (date, text, daily) '
				. 'SELECT \'' . $date . '\', text, TRUE '
				. 'FROM {{daily_points}} '
				. 'ORDER BY `order`'
			)
			->execute();
		Point::renumberOrderFieldsForDate($date);

		return $date;
	}
}
