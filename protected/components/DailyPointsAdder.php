<?php

class DailyPointsAdder {
	public static function addDailyPoints() {
		Yii::app()
			->db
			->createCommand(
				'INSERT INTO {{points}} (date, text, daily) '
				. 'SELECT CURDATE(), text, TRUE '
				. 'FROM {{daily_points}} '
				. 'ORDER BY `order`'
			)
			->execute();

		$date = date('Y-m-d');
		Point::renumberOrderFieldsForDate($date);
	}
}
