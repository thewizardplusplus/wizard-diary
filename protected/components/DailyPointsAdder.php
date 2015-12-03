<?php

class DailyPointsAdder {
	public static function addDailyPoints($date) {
		$existing_daily_points = Point::model()->findAll(
			array(
				'select' => array('state', 'text'),
				'condition' =>
					'`date` = :date '
					. 'AND `daily` = TRUE '
					. 'AND LENGTH(TRIM(`text`)) > 0 '
					. 'AND `state` != "INITIAL"',
				'params' => array('date' => $date)
			)
		);
		$new_existing_daily_points = array();
		foreach ($existing_daily_points as $daily_point) {
			$new_existing_daily_points[$daily_point->text] =
				$daily_point->state;
		}
		$existing_daily_points = $new_existing_daily_points;

		$daily_points_queries = array();
		$new_daily_points = DailyPoint::model()->findAll(
			array(
				'select' => array('text'),
				'order' => '`order`'
			)
		);
		$escaped_date = Yii::app()->db->quoteValue($date);
		foreach ($new_daily_points as $daily_point) {
			$daily_point_data = array(
				$escaped_date,
				Yii::app()->db->quoteValue($daily_point->text),
				Yii::app()->db->quoteValue(
					array_key_exists($daily_point->text, $existing_daily_points)
						? $existing_daily_points[$daily_point->text]
						: 'INITIAL'
				),
				'TRUE'
			);
			$daily_points_query = sprintf(
				'(%s)',
				implode(', ', $daily_point_data)
			);
			$daily_points_queries[] = $daily_points_query;
		}

		$sql = "START TRANSACTION;\n\n";
		$sql .= sprintf(
			"DELETE FROM {{points}}\n"
				. "WHERE `date` = %s AND `daily` = TRUE;\n\n",
			$escaped_date
		);
		$sql .= sprintf(
			"INSERT INTO {{points}} (`date`, `text`, `state`, `daily`)\n"
				. "VALUES\n"
				. "\t%s;\n\n",
			implode(",\n\t", $daily_points_queries)
		);
		$sql .= Point::getRenumberOrderSql($date) . "\n\n";
		$sql .= 'COMMIT;';

		Yii::app()->db->createCommand($sql)->execute();
	}
}
