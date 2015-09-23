<?php

class StatsController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionDailyPoints() {
		$points = Point::model()->findAll(
			array(
				'condition' => 'text != "" AND daily = TRUE',
				'order' => 'date DESC'
			)
		);

		$data = array();
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
				return !$item['initial'];
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
		foreach ($data as $first_key => $second_keys) {
			$first_first_start = null;
			$first_last_end = null;
			$new_second_keys = array();
			foreach ($second_keys as $second_key => $dates) {
				$dates = array_unique($dates);
				sort($dates, SORT_STRING);

				$intervals = array();
				foreach ($dates as $date) {
					$start_date = date_create($date);
					$end_date = date_create($date);
					if (empty($intervals)) {
						$intervals[] = array(
							'start' => $start_date,
							'end' => $end_date
						);
					} else {
						$last_index = count($intervals) - 1;
						$difference = date_diff(
							$intervals[$last_index]['end'],
							$end_date
						);
						if ($difference->days == 0) {
							continue;
						} else if ($difference->days == 1) {
							$intervals[$last_index]['end'] = $end_date;
						} else if ($difference->days > 1) {
							$intervals[] = array(
								'start' => $start_date,
								'end' => $end_date
							);
						}
					}
				}

				$second_first_start = null;
				$second_last_end = null;
				$new_intervals = array();
				foreach ($intervals as $interval) {
					$start_date = $interval['start'];
					$formatted_start_date = date_format($start_date, 'c');

					$end_date = $interval['end'];
					$shifted_end_date = date_add(
						$end_date,
						new DateInterval('PT23H59M59S')
					);
					$formatted_shifted_end_date = date_format(
						$shifted_end_date,
						'c'
					);

					if (
						is_null($first_first_start)
						|| date_diff(
							$first_first_start,
							$start_date
						)->invert === 1
					) {
						$first_first_start = $start_date;
					}
					if (
						is_null($first_last_end)
						|| date_diff(
							$first_last_end,
							$shifted_end_date
						)->invert === 0
					) {
						$first_last_end = $shifted_end_date;
					}

					if (is_null($second_first_start)) {
						$second_first_start = $formatted_start_date;
					}
					$second_last_end = $formatted_shifted_end_date;

					$new_intervals[] = array(
						'start' => $formatted_start_date,
						'end' => $formatted_shifted_end_date
					);
				}
				$intervals = $new_intervals;

				$new_second_keys[$second_key] = array(
					'start' => $second_first_start,
					'end' => $second_last_end,
					'intervals' => $intervals
				);
			}

			uasort(
				$new_second_keys,
				function($second_key_1, $second_key_2) {
					return strcmp($second_key_2['end'], $second_key_1['end']);
				}
			);
			$new_data[$first_key] = array(
				'start' => date_format($first_first_start, 'c'),
				'end' => date_format($first_last_end, 'c'),
				'tasks' => $new_second_keys
			);
		}
		uasort(
			$new_data,
			function($data_1, $data_2) {
				return strcmp($data_2['end'], $data_1['end']);
			}
		);
		$data = $new_data;

		$this->render('projects', array('data' => $data));
	}

	public function actionProjectList() {
		$points = Point::model()->findAll(
			array('condition' => 'text != "" AND daily = FALSE')
		);

		$data = array();
		foreach ($points as $point) {
			$text_parts = array_map('trim', explode(',', $point->text));
			$first_key = $text_parts[0];

			$second_key = '&mdash;';
			if (count($text_parts) > 1) {
				$second_key = $text_parts[1];
			}

			$rest_text = '&mdash;';
			if (count($text_parts) > 2) {
				$rest_text = implode(', ', array_slice($text_parts, 2));
				if (substr($rest_text, -1) == ';') {
					$rest_text = substr($rest_text, 0, -1);
				}
			}

			$data[$first_key][$second_key][$point->date][] = $rest_text;
		}

		$new_data = array();
		$start_date = DateFormatter::getStartDate();
		foreach ($data as $first_key => $second_keys) {
			$new_second_keys = array();
			foreach ($second_keys as $second_key => $dates) {
				$new_dates = array();
				foreach ($dates as $date => $points) {
					$points = array_unique($points);
					sort($points, SORT_STRING);
					$new_dates[$date] = $points;
				}
				ksort($new_dates, SORT_STRING);

				$formatted_dates = array();
				foreach ($new_dates as $date => $points) {
					$date = DateFormatter::formatMyDate($date, $start_date);
					$formatted_dates[$date] = $points;
				}

				$new_second_keys[$second_key] = $formatted_dates;
			}

			ksort($new_second_keys, SORT_STRING);
			$new_data[$first_key] = $new_second_keys;
		}
		ksort($new_data, SORT_STRING);
		$data = $new_data;

		$new_data = array();
		foreach ($data as $first_key => $second_keys) {
			$new_second_keys = array();
			foreach ($second_keys as $second_key => $dates) {
				$new_dates = array();
				foreach ($dates as $date => $points) {
					$new_points = array();
					foreach ($points as $point) {
						$new_points[] = array(
							'text' => $point,
							'icon' => 'glyphicon glyphicon-file'
						);
					}

					$new_dates[] = array(
						'text' => $date,
						'icon' => 'glyphicon glyphicon-folder-open',
						'children' => $new_points
					);
				}

				$new_second_keys[] = array(
					'text' => $second_key,
					'icon' => 'glyphicon glyphicon-folder-open',
					'children' => $new_dates
				);
			}

			$new_data[] = array(
				'text' => $first_key,
				'icon' => 'glyphicon glyphicon-folder-open',
				'children' => $new_second_keys
			);
		}
		$data = $new_data;

		$this->render('project_list', array('data' => $data));
	}
}
