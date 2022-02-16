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
						'initial' => false,
						'satisfied' => 0,
						'canceled' => 0,
						'total' => 0
					);
				}
			}
		);
		$data = array_filter(
			$data,
			function($item) {
				return !$item['initial'];
			}
		);
		$data = array_map(
			function($item) {
				$not_canceled = $item['total'] - $item['canceled'];
				$satisfied =
					$not_canceled != 0
						? round(
							100 * $item['satisfied'] / $not_canceled,
							2
						)
						: 100;
				return array(
					'satisfied' => $satisfied,
					'total' => 10 * $item['total'],
					'not_canceled' => 10 * $not_canceled,
					'quality' =>
						0.1 * ($satisfied - $not_canceled) * $not_canceled
				);
			},
			$data
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

	public function actionProjects($tasks_required = true) {
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
		$global_start = null;
		$global_end = null;
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

						if (
							is_null($global_start)
							|| date_diff(
								$global_start,
								$start_date
							)->invert === 1
						) {
							$global_start = $start_date;
						}
					}
					if (
						is_null($first_last_end)
						|| date_diff(
							$first_last_end,
							$shifted_end_date
						)->invert === 0
					) {
						$first_last_end = $shifted_end_date;

						if (
							is_null($global_end)
							|| date_diff(
								$global_end,
								$shifted_end_date
							)->invert === 0
						) {
							$global_end = $shifted_end_date;
						}
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
					return strcmp($second_key_1['end'], $second_key_2['end']);
				}
			);
			$new_data[$first_key] = array(
				'start' => date_format($first_first_start, 'c'),
				'end' => date_format($first_last_end, 'c'),
				'tasks' => $tasks_required ? $new_second_keys : array()
			);
		}
		uasort(
			$new_data,
			function($data_1, $data_2) {
				return strcmp($data_1['end'], $data_2['end']);
			}
		);
		$data = array(
			'start' => !is_null($global_start) ? date_format($global_start, 'c') : null,
			'end' => !is_null($global_end) ? date_format($global_end, 'c') : null,
			'data' => $new_data
		);

		$this->render('projects', array('data' => $data));
	}

	public function actionDailyPointList() {
		$points = Point::model()->findAll(
			array(
				'select' => array('text'),
				'condition' => 'text != "" AND daily = TRUE',
				'group' => 'text'
			)
		);

		$new_points = array();
		foreach ($points as $point) {
			$new_points[] = array('text' => $point->text);
		}
		$points = $new_points;

		$data_provider = new CArrayDataProvider(
			$points,
			array(
				'keyField' => 'text',
				'sort' => array(
					'attributes' => array('text'),
					'defaultOrder' => array('text' => CSort::SORT_ASC)
				)
			)
		);

		$this->render(
			'daily_point_list',
			array('data_provider' => $data_provider)
		);
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

	public function actionProjectActions() {
		$tails = $this->getPointTails();
		$this->render('project_actions', array('data' => $tails));
	}

	public function actionAchievements() {
		$data = $this->getAchievementsData();

		$achievements = array();
		$achievements_texts = array();
		foreach ($data as $text => $subdata) {
			foreach ($subdata->achievements as $level => $date) {
				$name = $this->achievements_names[$level];
				$achievements[] = array(
					'point' => $text,
					'level' => $level,
					'days' => DayFormatter::formatDays($level),
					'name' => $name,
					'hash' => $this->hashAchievement($name, $text),
					'date' => $date
				);
				$achievements_texts[] = $text;
			}
		}

		if (isset($_GET['search'])) {
			$levels = array();
			if (
				isset($_GET['search']['levels'])
				and is_array($_GET['search']['levels'])
			) {
				$levels = $_GET['search']['levels'];
			}

			$texts = array();
			if (
				isset($_GET['search']['texts'])
				and is_array($_GET['search']['texts'])
			) {
				$texts = $_GET['search']['texts'];
			}

			$achievements = array_filter(
				$achievements,
				function($achievement) use ($levels, $texts) {
					$right_level =
						count($levels) == 0
						|| in_array(
							$this->unformatLevel($achievement['level']),
							$levels,
							true
						);
					$right_text =
						count($texts) == 0
						|| in_array($achievement['point'], $texts, true);
					return $right_level && $right_text;
				}
			);
		}

		$achievements_levels = array();
		foreach ($this->achievements_names as $level => $name) {
			$level = $this->unformatLevel($level);
			$achievements_levels[$level] = $name;
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

		$new_achievements_texts = array();
		$achievements_texts = array_unique($achievements_texts);
		foreach ($achievements_texts as $achievement_text) {
			$new_achievements_texts[$achievement_text] = $achievement_text;
		}
		asort($new_achievements_texts);
		$achievements_texts = $new_achievements_texts;

		$this->render(
			'achievements',
			array(
				'achievements_provider' => $achievements_provider,
				'achievements_levels' => $achievements_levels,
				'achievements_texts' => $achievements_texts
			)
		);
	}

	public function actionFutureAchievements() {
		$data = $this->getAchievementsData();
		$leading_uncompleted_days = $this->getLeadingUncompletedDays();

		$future_achievements = array();
		$current_date = date_create();
		foreach ($data as $text => $subdata) {
			$streak_length = count($subdata->last_streak);
			$last_streak_date = date_create(
				$subdata->last_streak[$streak_length - 1]
			);
			if (
				$last_streak_date->diff($current_date)->days
				> $leading_uncompleted_days
			) {
				continue;
			}

			$next_level = null;
			foreach ($this->achievements_levels as $level) {
				$formatted_level = $this->formatLevel($level);
				if (
					$level > $streak_length
					and !array_key_exists(
						$formatted_level,
						$subdata->achievements
					)
				) {
					$next_level = $level;
					break;
				}
			}
			if (is_null($next_level)) {
				continue;
			}

			$rest_days = $next_level - $streak_length;
			$next_level = $this->formatLevel($next_level);

			$last_streak_date->add(
				DateInterval::createFromDateString(
					sprintf('%d day', $rest_days)
				)
			);
			$date = $last_streak_date->format('Y-m-d');

			$name = $this->achievements_names[$next_level];
			$future_achievements[] = array(
				'point' => $text,
				'level' => $next_level,
				'days' => DayFormatter::formatDays($next_level),
				'completed_days' => DayFormatter::formatCompletedDays(
					$streak_length
				),
				'rest_days' => DayFormatter::formatCompletedDays($rest_days),
				'name' => $name,
				'hash' => $this->hashAchievement($name, $text),
				'date' => DateFormatter::formatDate($date),
				'my_date' => DateFormatter::formatMyDate($date)
			);
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

	private $achievements_levels = array(1, 6, 12, 24, 48, 96);
	private $achievements_names = array(
		'#1' => 'Первая попытка',
		'#6' => 'Выдержка',
		'#12' => 'Работа над собой',
		'#24' => 'Сила воли',
		'#48' => 'Привычка',
		'#96' => 'Новое лицо'
	);

	private function getAchievementsData() {
		$points = Point::model()->findAll(
			array(
				'select' => array('state', 'text', 'date'),
				'condition' =>
					'daily = TRUE '
					. 'AND text != "" '
					. 'AND (state = "SATISFIED" OR state = "CANCELED")',
				'order' => 'date'
			)
		);

		$data = array();
		foreach ($points as $point) {
			$date = date_create($point->date);
			if (!array_key_exists($point->text, $data)) {
				if ($point->state == 'SATISFIED') {
					$data_item = new stdClass;
					$data_item->dates = array($point->date);
					$data_item->last_streak = array();
					$data_item->last_date = $date;
					$data_item->achievements = array('#1' => $point->date);

					$data[$point->text] = $data_item;
				}

				continue;
			}

			if (
				!is_null($data[$point->text]->last_date)
				and $data[$point->text]->last_date->diff($date)->days <= 1
			) {
				$data[$point->text]->dates[] = $point->date;
				$data[$point->text]->last_date = $date;

				$number_of_dates = count($data[$point->text]->dates);
				foreach ($this->achievements_levels as $level) {
					if ($level > $number_of_dates) {
						break;
					}

					$level_key = $this->formatLevel($level);
					if (
						array_key_exists(
							$level_key,
							$data[$point->text]->achievements
						)
					) {
						continue;
					}

					$data[$point->text]->achievements[$level_key] =
						$point->date;
				}
			} else {
				if (!empty($data[$point->text]->dates)) {
					$data[$point->text]->last_streak =
						$data[$point->text]->dates;
				}

				if ($point->state == 'SATISFIED') {
					$data[$point->text]->dates = array($point->date);
					$data[$point->text]->last_date = $date;
				} else {
					$data[$point->text]->dates = array();
					$data[$point->text]->last_date = null;
				}
			}
		}

		foreach ($data as $data_item) {
			if (!empty($data_item->dates)) {
				$data_item->last_streak = $data_item->dates;
			}
		}

		return $data;
	}

	private function getLeadingUncompletedDays() {
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

		return $leading_uncompleted_days;
	}

	private function formatLevel($level) {
		return sprintf('#%d', $level);
	}

	private function unformatLevel($level) {
		return substr($level, 1);
	}

	private function hashAchievement($name, $point) {
		return md5(sprintf('%s:%s', $name, $point));
	}

	public function formatAchievements($number) {
		$unit = '';
		$modulo = $number % 10;
		if ($modulo == 1 and ($number < 10 or $number > 20)) {
			$unit = 'достижение';
		} else if (
			$modulo > 1 and $modulo < 5
			and ($number < 10 or $number > 20)
		) {
			$unit = 'достижения';
		} else {
			$unit = 'достижений';
		}

		return sprintf("%d %s", $number, $unit);
	}

	private function getPointTails() {
		$points = Point::model()->findAll(
			array(
				'select' => array('text'),
				'condition' => '`daily` = FALSE AND LENGTH(TRIM(`text`)) > 0'
			)
		);

		$tails = array();
		foreach ($points as $point) {
			$parts = array_map('trim', explode(',', $point->text));
			if (count($parts) > 2) {
				$tails[] = implode(', ', array_slice($parts, 2));
			}
		}

		$prefix_forest = new PrefixForest();
		foreach ($tails as $tail) {
			$prefix_forest->add($tail);
		}

		$collector = new PrefixForestMapper();
		$tails = $collector->process($prefix_forest->root);

		return isset($tails['children']) ? $tails['children'] : array();
	}
}
