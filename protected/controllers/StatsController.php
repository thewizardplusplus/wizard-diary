<?php

class StatsController extends CController {
	public static function collectDailyStats() {
		$points = Point::model()->findAll(
			array(
				'condition' => 'text != "" AND daily = TRUE',
				'order' => 'date'
			)
		);

		$data = array();
		foreach ($points as $point) {
			if (!array_key_exists($point->date, $data)) {
				$data[$point->date] = array(
					'initial' => false,
					'satisfied' => 0,
					'canceled' => 0,
					'total' => 0
				);
			}

			$data[$point->date]['total'] += 1;
			switch ($point->state) {
				case 'INITIAL':
					$data[$point->date]['initial'] = true;
					break;
				case 'SATISFIED':
					$data[$point->date]['satisfied'] += 1;
					break;
				case 'CANCELED':
					$data[$point->date]['canceled'] += 1;
					break;
			}
		}

		$data = array_filter(
			$data,
			function($item) {
				return !$item['initial'];
			}
		);
		$data = array_map(
			function($item) {
				$not_canceled = $item['total'] - $item['canceled'];
				return array(
					'satisfied' =>
						$not_canceled != 0
							? round(
								100 * $item['satisfied'] / $not_canceled,
								2
							)
							: 100,
					'total' => 10 * $item['total'],
					'not_canceled' => 10 * $not_canceled
				);
			},
			$data
		);

		$data = DateCompleter::complete(
			$data,
			function($key, $value) {
				return $key;
			},
			function(&$dates, $key, $value, $date) {
				if (!is_null($value)) {
					$dates[$date] = $value;
				} else {
					$dates[$date] = array(
						'satisfied' => 100,
						'total' => 0,
						'not_canceled' => 0
					);
				}
			}
		);

		return $data;
	}

	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionDailyPoints() {
		$data = self::collectDailyStats();

		$mean = 0;
		if (!empty($data)) {
			foreach ($data as $item) {
				$mean += $item['satisfied'];
			}
			$mean /= count($data);
		}

		$this->render('daily_points', array('data' => $data, 'mean' => $mean));
	}

	public function actionPoints() {
		$data = Yii::app()
			->db
			->createCommand()
			->select(
				array(
					'date',
					'SUM('
						. 'CASE '
							. 'WHEN `daily` = FALSE AND LENGTH(`text`) > 0 '
								. 'THEN 1 '
							. 'ELSE 0 '
						. 'END'
					. ') AS \'number\''
				)
			)
			->from('{{points}}')
			->group('date')
			->order('date')
			->queryAll();
		$data = DateCompleter::complete(
			$data,
			function($key, $value) {
				return $value['date'];
			},
			function(&$dates, $key, $value, $date) {
				$dates[] =
					!is_null($value)
						? $value
						: array('date' => $date, 'number' => 0);
			}
		);

		$mean = 0;
		if (!empty($data)) {
			foreach ($data as $item) {
				$mean += $item['number'];
			}
			$mean /= count($data);
		}

		$this->render('points', array('data' => $data, 'mean' => $mean));
	}

	public function actionAchievements() {
		$data = $this->getAchievementsData();

		$achievements = array();
		foreach ($data as $text => $subdata) {
			foreach ($subdata['achievements'] as $level => $date) {
				$achievements[] = array(
					'point' => $text,
					'level' => $level,
					'days' => $this->formatDays($level),
					'name' => $this->achievements_names[$level],
					'date' => $date
				);
			}
		}

		$achievements_provider = new CArrayDataProvider(
			$achievements,
			array(
				'keyField' => 'date',
				'sort' => array(
					'attributes' => array('date'),
					'defaultOrder' => array('date' => CSort::SORT_DESC)
				)
			)
		);

		$this->render(
			'achievements',
			array('achievements_provider' => $achievements_provider)
		);
	}

	public function actionFutureAchievements() {
		$points = Yii::app()
			->db
			->createCommand()
			->select(
				array(
					'date',
					'NOT MAX('
							. '`state` = \'INITIAL\' AND LENGTH(`text`) > 0'
						. ') AS \'completed\'',
					'SUM('
							. 'CASE '
								. 'WHEN `daily` = TRUE AND LENGTH(`text`) > 0 '
									. 'THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'daily\''
				)
			)
			->from('{{points}}')
			->group('date')
			->order('date DESC')
			->queryAll();

		$leading_uncompleted_days = 0;
		$last_date = date_create();
		foreach ($points as $point) {
			if (intval($point['daily']) == 0) {
				break;
			}
			if ($point['completed']) {
				break;
			}

			$date = date_create($point['date']);
			if ($date->diff($last_date)->days > 1) {
				break;
			}
			$last_date = $date;

			$leading_uncompleted_days++;
		}

		$data = $this->getAchievementsData();

		$future_achievements = array();
		$current_date = date_create('2015-03-17');
		foreach ($data as $text => $subdata) {
			foreach ($subdata['achievements'] as $level => $date) {
				if (
					date_create($date)->diff($current_date)->days
					<= $leading_uncompleted_days
				) {
					$next_level = $this->next_achievements_levels[$level];
					if (!is_null($next_level)) {
						$future_achievements[] = array(
							'point' => $text,
							'level' => $next_level,
							'days' => $this->formatDays($level),
							'name' => $this->achievements_names[$next_level]
						);
					}
				}
			}
		}

		$future_achievements_provider = new CArrayDataProvider(
			$future_achievements,
			array(
				'keyField' => 'point',
				'sort' => array(
					'attributes' => array('point'),
					'defaultOrder' => array('point' => CSort::SORT_ASC)
				)
			)
		);

		$this->render(
			'future_achievements',
			array(
				'future_achievements_provider' => $future_achievements_provider
			)
		);
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

	private $achievements_levels = array(1, 6, 12, 24, 48);
	private $next_achievements_levels = array(
		'#1' => '#6',
		'#6' => '#12',
		'#12' => '#24',
		'#24' => '#48',
		'#48' => null
	);
	private $achievements_names = array(
		'#1' => 'Первая попытка',
		'#6' => 'Выдержка',
		'#12' => 'Работа над собой',
		'#24' => 'Сила воли',
		'#48' => 'Привычка'
	);

	private function getAchievementsData() {
		$points = Point::model()->findAll(
			array(
				'select' => array('text', 'date'),
				'condition' =>
					'daily = TRUE AND text != "" AND state = "SATISFIED"',
				'order' => 'date'
			)
		);

		$data = array();
		foreach ($points as $point) {
			$date = date_create($point->date);
			if (!array_key_exists($point->text, $data)) {
				$data[$point->text] = array(
					'dates' => array($point->date),
					'last_date' => $date,
					'achievements' => array('#1' => $point->date)
				);

				continue;
			}

			if ($data[$point->text]['last_date']->diff($date)->days <= 1) {
				$data[$point->text]['dates'][] = $point->date;

				$number_of_dates = count($data[$point->text]['dates']);
				foreach ($this->achievements_levels as $level) {
					if ($level > $number_of_dates) {
						break;
					}

					$level_key = sprintf('#%d', $level);
					if (
						array_key_exists(
							$level_key,
							$data[$point->text]['achievements']
						)
					) {
						continue;
					}

					$data[$point->text]['achievements'][$level_key] =
						$point->date;
				}
			} else {
				$data[$point->text]['dates'] = array($point->date);
			}

			$data[$point->text]['last_date'] = $date;
		}

		return $data;
	}

	private function formatDays($level) {
		$days = intval(substr($level, 1));
		$modulo = $days % 10;
		$unit =
			($modulo == 1 and ($days < 10 or $days > 20))
				? 'дня'
				: 'дней';

		return sprintf("%d %s", $days, $unit);
	}
}
