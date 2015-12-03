<?php

class DailyPointsAdder {
	public static function addDailyPoints($date) {
		$new_daily_points = self::getNewDailyPoints();
		$existing_daily_points = self::getExistingDailyPoints($date);
		$daily_points_queries = self::processDailyPoints(
			$date,
			$new_daily_points,
			$existing_daily_points
		);
		$sql = self::generateSql($date, $daily_points_queries);
		Yii::app()->db->createCommand($sql)->execute();
	}

	private static function getExistingDailyPoints($date) {
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

		$transformed_existing_daily_points = array();
		foreach ($existing_daily_points as $daily_point) {
			$transformed_existing_daily_points[$daily_point->text] =
				$daily_point->state;
		}

		return $transformed_existing_daily_points;
	}

	private static function getNewDailyPoints() {
		return DailyPoint::model()->findAll(
			array(
				'select' => array('text'),
				'order' => '`order`'
			)
		);
	}

	private static function processDailyPoints(
		$date,
		$new_daily_points,
		$existing_daily_points
	) {
		$daily_points_queries = array();
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

		return $daily_points_queries;
	}

	private static function generateSql($date, $daily_points_queries) {
		$escaped_date = Yii::app()->db->quoteValue($date);
		$delete_sql = sprintf(
			"DELETE FROM {{points}}\n"
				. "WHERE `date` = %s AND `daily` = TRUE;",
			$escaped_date
		);

		$general_daily_points_query = implode(",\n\t", $daily_points_queries);
		$add_daily_points_sql = sprintf(
			"INSERT INTO {{points}} (`date`, `text`, `state`, `daily`)\n"
				. "VALUES\n"
				. "\t%s;",
			$general_daily_points_query
		);

		$renumber_order_sql = Point::getRenumberOrderSql($date);
		return
			"START TRANSACTION;\n\n"
			. $delete_sql . "\n\n"
			. $add_daily_points_sql . "\n\n"
			. $renumber_order_sql . "\n\n"
			. 'COMMIT;';
	}
}
