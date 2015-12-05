<?php

class DailyPointController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$current_date = date('Y-m-d');
		$my_current_date = DateFormatter::getDatePartsInMyFormat($current_date);
		$model = new DailyPointForm(
			$my_current_date->day,
			$my_current_date->year
		);

		if (isset($_POST['ajax']) and $_POST['ajax'] == 'daily-point-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['DailyPointForm'])) {
			$model->attributes = $_POST['DailyPointForm'];
			$result = $model->validate();
			if ($result) {
				$target_date = DateFormatter::getDateFromMyDateParts(
					$model->day,
					$model->year
				);

				DailyPointsAdder::addDailyPoints($target_date);
				$this->redirect(
					$this->createUrl('day/view', array('date' => $target_date))
				);
			}
		}

		$day_container_class =
			count($model->getErrors('day'))
				? ' has-error'
				: '';
		$year_container_class =
			count($model->getErrors('year'))
				? ' has-error'
				: '';

		$data_provider = new CActiveDataProvider(
			'DailyPoint',
			array(
				'criteria' => array('order' => '`order`'),
				'pagination' => false
			)
		);
		$number_of_daily_points = DailyPoint::model()->count(
			array('condition' => 'LENGTH(TRIM(`text`)) > 0')
		);

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'model' => $model,
				'day_container_class' => $day_container_class,
				'year_container_class' => $year_container_class,
				'date' => $current_date,
				'my_date' => $my_current_date,
				'number_of_daily_points' => $number_of_daily_points
			)
		);
	}

	public function actionUpdate() {
		if (isset($_POST['points_description'])) {
			$sql = $this->dailyPointsToSql($_POST['points_description']);
			Yii::app()->db->createCommand($sql)->execute();

			return;
		}

		$points_description = $this->getDailyPoints();
		$this->render(
			'update',
			array('points_description' => $points_description)
		);
	}

	private function getDailyPoints() {
		$daily_points = DailyPoint::model()->findAll(
			array(
				'select' => array('text'),
				'order' => '`order`'
			)
		);

		$points_description = '';
		foreach ($daily_points as $daily_point) {
			$text = trim($daily_point->text);
			if (!empty($text) and substr($text, -1) == ';') {
				$text = substr($text, 0, -1);
			}

			$points_description .= $text . "\n";
		}

		return $points_description;
	}

	private function dailyPointsToSql($points_description) {
		$points = explode("\n", $points_description);
		$points = array_map(
			function($point) {
				if (!empty($point) and substr($point, -1) == ';') {
					$point = substr($point, 0, -1);
				}

				return $point;
			},
			$points
		);
		if (
			!empty($points)
			&& empty($points[count($points) - 1])
		) {
			$points = array_slice(
				$points,
				0,
				count($points) - 1
			);
		}

		$order = Constants::MINIMAL_ORDER_VALUE;
		$points_sql_lines = array_map(
			function($point) use (&$order) {
				$sql_line = sprintf(
					'(%s, %d)',
					Yii::app()->db->quoteValue($point),
					$order
				);
				$order += 2;

				return $sql_line;
			},
			$points
		);

		$points_sql = '';
		if (!empty($points_sql_lines)) {
			$points_sql = sprintf(
				"INSERT INTO `{{daily_points}}` (`text`, `order`)\n"
					. "VALUES\n\t%s;",
				implode(",\n\t", $points_sql_lines)
			);
		}

		return
			"START TRANSACTION;\n\n"
			. "DELETE FROM `{{daily_points}}`;\n\n"
			. "$points_sql\n\n"
			. "COMMIT;";
	}
}
