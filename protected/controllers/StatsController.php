<?php

class StatsController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionDailyPoints() {
		$data = array();
		$points = Point::model()->findAll(
			array(
				'condition' => 'text != "" AND daily = TRUE',
				'order' => 'date DESC'
			)
		);
		foreach ($points as $point) {
			$date = DateFormatter::formatMyDate($point->date);
			if (!array_key_exists($date, $data)) {
				$data[$date] = array(
					'initial' => false,
					'satisfied' => 0,
					'total' => 0
				);
			}

			$data[$date]['total'] += 1;
			if ($point->state == 'INITIAL') {
				$data[$date]['initial'] = true;
			} else if ($point->state == 'SATISFIED') {
				$data[$date]['satisfied'] += 1;
			} else if ($point->state == 'CANCELED') {
				$data[$date]['total'] -= 1;
			}
		}

		$data = array_filter(
			$data,
			function($item) {
				return $item['initial'] == 0;
			}
		);
		$data = array_slice($data, 0, Constants::STATS_DAYS_LIMIT);
		$data = array_reverse($data);
		$data = array_map(
			function($item) {
				return round(
					100 * $item['satisfied'] / $item['total'],
					2
				);
			},
			$data
		);

		$this->render('daily_points', array('data' => $data));
	}

	public function actionProjects() {
		$this->render('projects');
	}
}
