<?php

class AddDailyPointsCommand extends CConsoleCommand {
	public function run() {
		Yii::app()
			->db
			->createCommand(
				'INSERT INTO {{points}} (date, text, `check`) '
				. 'SELECT CURDATE(), text, `check` '
				. 'FROM {{daily_points}} '
				. 'ORDER BY `order`'
			)
			->execute();

		$date = date('Y-m-d');
		Point::renumberOrderFieldsForDate($date);
	}
}
