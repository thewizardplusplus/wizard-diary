<?php

class DailyPointsAdder {
	public static function addDailyPoints() {
		$date = date('Y-m-d');
		$escaped_date = Yii::app()->db->quoteValue($date);

		$sql = "START TRANSACTION;\n\n";
		$sql .= sprintf(
			"DELETE FROM {{points}}\n"
				. "WHERE `date` = %s AND `daily` = TRUE;\n\n",
			$escaped_date
		);
		$sql .= sprintf(
			"INSERT INTO {{points}} (date, text, daily)\n"
				. "SELECT %s, text, TRUE\n"
				. "FROM {{daily_points}}\n"
				. "ORDER BY `order`;\n\n",
			$escaped_date
		);
		$sql .= Point::getRenumberOrderSql($date) . "\n\n";
		$sql .= 'COMMIT;';

		Yii::app()->db->createCommand($sql)->execute();
		return $date;
	}
}
