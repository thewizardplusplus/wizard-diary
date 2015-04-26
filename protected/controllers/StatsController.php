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
			if (!array_key_exists($point->date, $data)) {
				$data[$point->date] = array(
					'initial' => false,
					'satisfied' => 0,
					'total' => 0
				);
			}

			$data[$point->date]['total'] += 1;
			if ($point->state == 'INITIAL') {
				$data[$point->date]['initial'] = true;
			} else if ($point->state == 'SATISFIED') {
				$data[$point->date]['satisfied'] += 1;
			} else if ($point->state == 'CANCELED') {
				$data[$point->date]['total'] -= 1;
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
		$points = Point::model()->findAll(
			array('condition' => 'text != "" AND daily = FALSE')
		);

		$data = array();
		foreach ($points as $point) {
			$text_parts = explode(',', $point->text);
			$first_key = trim($text_parts[0]);
			$second_key = count($text_parts) > 1 ? trim($text_parts[1]) : '';
			$data[$first_key][$second_key][] = $point->date;
		}

		$new_data = array();
		foreach ($data as $first_key => $subdata) {
			$new_subdata = array();
			foreach ($subdata as $second_key => $dates) {
				$dates = array_unique($dates);
				asort($dates, SORT_STRING);
				$new_subdata[$second_key] = $dates;
			}

			ksort($new_subdata, SORT_STRING);
			$new_data[$first_key] = $new_subdata;
		}
		ksort($new_data, SORT_STRING);

		$new_new_data = array();
		foreach ($new_data as $first_key => $subdata) {
			$new_subdata = array();
			foreach ($subdata as $second_key => $dates) {
				$new_dates = array();
				foreach ($dates as $date) {
					$start_date = date_create($date);
					$end_date = date_create($date);
					if (empty($new_dates)) {
						$new_dates[] = array(
							'start' => $start_date,
							'end' => $end_date
						);
					} else {
						$last_index = count($new_dates) - 1;
						$difference = date_diff(
							$new_dates[$last_index]['end'],
							$end_date
						);
						if ($difference->days == 0) {
							continue;
						} else if ($difference->days == 1) {
							$new_dates[$last_index]['end'] = $end_date;
						} else if ($difference->days > 1) {
							$new_dates[] = array(
								'start' => $start_date,
								'end' => $end_date
							);
						}
					}
				}

				$new_subdata[$second_key] = $new_dates;
			}

			$new_new_data[$first_key] = $new_subdata;
		}

		$new_new_new_data = array();
		foreach ($new_new_data as $first_key => $subdata) {
			$new_subdata = array();
			foreach ($subdata as $second_key => $intervals) {
				$new_intervals = array();
				foreach ($intervals as $interval) {
					$interval['end'] = date_add(
						$interval['end'],
						new DateInterval('PT23H59M59S')
					);

					$new_intervals[] = $interval;
				}

				$new_subdata[$second_key] = $new_intervals;
			}

			$new_new_new_data[$first_key] = $new_subdata;
		}

		$new_new_new_new_data = array();
		foreach ($new_new_new_data as $first_key => $subdata) {
			$new_subdata = array();
			foreach ($subdata as $second_key => $intervals) {
				$first_start = null;
				$last_end = null;
				$new_intervals = array();
				foreach ($intervals as $interval) {
					$interval = array(
						'start' => date_format($interval['start'], 'c'),
						'end' => date_format($interval['end'], 'c')
					);

					if (is_null($first_start)) {
						$first_start = $interval['start'];
					}
					$last_end = $interval['end'];

					$new_intervals[] = $interval;
				}

				$new_subdata[$second_key] = array(
					'start' => $first_start,
					'end' => $last_end,
					'intervals' => $new_intervals
				);
			}

			$new_new_new_new_data[$first_key] = $new_subdata;
		}

		$this->render('projects', array('data' => $new_new_new_new_data));
	}

	public function actionProjectList() {
		$this->render('project_list');
	}
}
