<?php

require_once(__DIR__ . '/StatsController.php');

class DayController extends CController {
	const ONE_LEVEL_EDITOR_INDENT = '    ';

	public function filters() {
		return array('accessControl', 'ajaxOnly + stats');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$days = Yii::app()
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
						. ') AS \'daily\'',
					'SUM('
							. 'CASE '
								. 'WHEN `daily` = FALSE AND LENGTH(`text`) > 0 '
									. 'THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'projects\''
				)
			)
			->from('{{points}}')
			->group('date')
			->queryAll();
		$days = DateCompleter::complete(
			$days,
			function($key, $value) {
				return $value['date'];
			},
			function(&$dates, $key, $value, $date) {
				$dates[] =
					!is_null($value)
						? $value
						: array(
							'date' => $date,
							'completed' => true,
							'daily' => 0,
							'projects' => 0
						);
			}
		);

		$data_provider = new CArrayDataProvider(
			$days,
			array(
				'keyField' => 'date',
				'sort' => array(
					'attributes' => array('date'),
					'defaultOrder' => array('date' => CSort::SORT_DESC)
				)
			)
		);

		$daily_stats = StatsController::collectDailyStats();

		$current_date = date('Y-m-d');
		$day = $this->getMyDay($current_date);

		$rest_days = $day % Constants::DAYS_IN_MY_STREAK;
		$rest_days =
			$rest_days != 0
				? $rest_days
				: Constants::DAYS_IN_MY_STREAK;
		$rest_days = Constants::DAYS_IN_MY_STREAK - $rest_days + 1;

		$target_date = date_add(
			date_create($current_date),
			DateInterval::createFromDateString(
				sprintf('%d day', $rest_days - 1)
			)
		);
		$target_date = $target_date->format('Y-m-d');

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'daily_stats' => $daily_stats,
				'rest_days_prefix' => DayFormatter::formatRestDaysPrefix(
					$rest_days
				),
				'rest_days' => DayFormatter::formatCompletedDays($rest_days),
				'target_date' => DateFormatter::formatDate($target_date),
				'target_my_date' => DateFormatter::formatMyDate($target_date)
			)
		);
	}

	public function actionView($date) {
		$this->testDate($date);

		$data_provider = new CActiveDataProvider(
			'Point',
			array(
				'criteria' => array(
					'condition' => 'date = :date',
					'params' => array('date' => $date),
					'order' => '`order`'
				),
				'pagination' => false
			)
		);
		$encoded_date = CHtml::encode($date);
		$stats = $this->getStats($date);

		$this->render(
			'view',
			array(
				'data_provider' => $data_provider,
				'my_date' => DateFormatter::formatMyDate($date),
				'date' => DateFormatter::formatDate($encoded_date),
				'raw_date' => CHtml::encode($encoded_date),
				'stats' => $stats
			)
		);
	}

	public function actionStats($date) {
		$this->testDate($date);

		$stats = $this->getStats($date);
		echo json_encode($stats);
	}

	public function actionUpdate($date, $line = 0) {
		$this->testDate($date);
		$this->testLineNumber($line);

		if (isset($_POST['points_description'])) {
			$number_of_daily_points = Point::model()->count(
				array(
					'condition' => 'date = :date AND `daily` = TRUE',
					'params' => array('date' => $date)
				)
			);

			$points_description = $this->extendImport(
				$_POST['points_description'],
				$number_of_daily_points
			);
			$sql = $this->importToSql($date, $points_description);
			Yii::app()->db->createCommand($sql)->execute();

			return;
		}

		$points = Point::model()->findAll(
			array(
				'select' => array('text'),
				'condition' => 'date = :date AND `daily` = FALSE',
				'params' => array('date' => $date),
				'order' => '`order`'
			)
		);

		$points_description = $this->prepareImport($points);
		$encoded_date = CHtml::encode($date);
		$stats = $this->getStats($date);
		$point_hierarchy = $this->getPointHierarchy();

		$this->render(
			'update',
			array(
				'points_description' => $points_description,
				'my_date' => DateFormatter::formatMyDate($date),
				'date' => DateFormatter::formatDate($encoded_date),
				'raw_date' => CHtml::encode($encoded_date),
				'stats' => $stats,
				'point_hierarchy' => $point_hierarchy,
				'line' => $line
			)
		);
	}

	public function actionImport() {
		if (isset($_POST['points-description'])) {
			$import = $this->parseImport($_POST['points-description']);
			$points_numbers = $this->getPointsNumbers(array_keys($import));
			$sql = $this->globalImportToSql($import, $points_numbers);
			Yii::app()->db->createCommand($sql)->execute();
		}

		$this->render('import');
	}

	public function getRowClass($date) {
		$row_class = '';
		$day = $this->getMyDay($date);
		if (($day % Constants::DAYS_IN_MY_STREAK) == 0) {
			$row_class = 'danger';
		} else if (($day % (Constants::DAYS_IN_MY_STREAK / 2)) == 0) {
			$row_class = 'warning';
		} else if ((($day - 1) % Constants::DAYS_IN_MY_STREAK) == 0) {
			$row_class = 'success';
		}

		return $row_class;
	}

	public function findSatisfiedCounter($daily_stats, $data) {
		$date = $data['date'];
		if (array_key_exists($date, $daily_stats)) {
			return $daily_stats[$date]['satisfied'];
		} else if ($data['daily'] == 0) {
			return 100;
		} else {
			return -1;
		}
	}

	public function formatSatisfiedCounter($satisfied_counter) {
		if ($satisfied_counter != -1) {
			return $satisfied_counter . '%';
		} else {
			return '&mdash;';
		}
	}

	private function getMyDay($date) {
		$my_date = DateFormatter::formatMyDate($date);
		return intval(explode('.', $my_date)[0]);
	}

	private function testDate($date) {
		if (!preg_match('/\d{4}(?:-\d{2}){2}/', $date)) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}
	}

	private function testLineNumber($number) {
		if (!preg_match('/\d+/', $number)) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}
	}

	private function getStats($date) {
		$row = Yii::app()
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
						. ') AS \'daily\'',
					'SUM('
							. 'CASE '
								. 'WHEN '
									. '`daily` = TRUE '
									. 'AND `state` = \'SATISFIED\''
									. 'AND LENGTH(`text`) > 0 '
									. 'THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'satisfied\'',
					'SUM('
							. 'CASE '
								. 'WHEN '
									. '`daily` = TRUE '
									. 'AND (`state` = \'SATISFIED\''
									. 'OR `state` = \'NOT_SATISFIED\')'
									. 'AND LENGTH(`text`) > 0 '
									. 'THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'not_canceled\'',
					'SUM('
							. 'CASE '
								. 'WHEN `daily` = FALSE AND LENGTH(`text`) > 0 '
									. 'THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'projects\''
				)
			)
			->from('{{points}}')
			->where('date = :date', array('date' => $date))
			->group('date')
			->queryRow();
		if ($row === false) {
			$row = array(
				'date' => $date,
				'completed' => true,
				'daily' => 0,
				'satisfied' => 100,
				'not_canceled' => 0,
				'projects' => 0
			);
		} else if (!$row['completed']) {
			$row['satisfied'] = -1;
		} else if ($row['not_canceled'] == 0) {
			$row['satisfied'] = 100;
		} else {
			$row['satisfied'] = round(
				100 * $row['satisfied'] / $row['not_canceled'],
				2
			);
		}

		return $row;
	}

	private function getPointHierarchy() {
		$points = Point::model()->findAll(
			array(
				'select' => array('text'),
				'condition' => '`daily` = FALSE AND LENGTH(TRIM(`text`)) > 0'
			)
		);

		$hierarchy = array();
		$tails = array();
		foreach ($points as $point) {
			$parts = array_map('trim', explode(',', $point->text));
			$number_of_parts = count($parts);
			if ($number_of_parts > 0) {
				if (!array_key_exists($parts[0], $hierarchy)) {
					$hierarchy[$parts[0]] = array();
				}
			}
			if ($number_of_parts > 1) {
				if (!in_array($parts[1], $hierarchy[$parts[0]])) {
					$hierarchy[$parts[0]][] = $parts[1];
				}
			}
			if ($number_of_parts > 2) {
				$tails[] = implode(', ', array_slice($parts, 2));
			}
		}

		$new_hierarchy = array();
		foreach ($hierarchy as $level_1 => $level_2_list) {
			sort($level_2_list, SORT_STRING);
			$new_hierarchy[$level_1] = $level_2_list;
		}
		ksort($new_hierarchy, SORT_STRING);
		$hierarchy = $new_hierarchy;

		$prefix_forest = new PrefixForest();
		foreach ($tails as $tail) {
			$prefix_forest->add($tail);
		}
		$prefix_forest->clean();

		$collector = new PrefixForestCollector();
		$collector->collect($prefix_forest->root);
		$tails = $collector->getLines();

		return array('hierarchy' => $hierarchy, 'tails' => $tails);
	}

	private function prepareImport($points) {
		$points_description = '';
		$last_parts = array();
		foreach ($points as $point) {
			$text = trim($point->text);
			if (empty($text)) {
				$points_description .= "\n";
				continue;
			}

			$parts = explode(',', $text);
			$parts = array_map('trim', $parts);

			$number_of_parts = count($parts);
			$minimal_number = min(count($last_parts), $number_of_parts);

			$line = '';
			$last_index = 0;
			for ($i = 0; $i < $minimal_number; $i++) {
				if ($parts[$i] != $last_parts[$i]) {
					$last_index = $i;
					break;
				}

				$line .= self::ONE_LEVEL_EDITOR_INDENT;
			}
			$last_parts = $parts;

			for ($j = $last_index; $j < $number_of_parts; $j++) {
				if (strlen(trim($line)) != 0) {
					$line .= ', ';
				}

				$line .= $parts[$j];
			}

			if (!empty($line) and substr($line, -1) == ';') {
				$line = substr($line, 0, -1);
			}

			$points_description .= $line . "\n";
		}

		$points_description = trim($points_description);
		if (!empty($points_description)) {
			$points_description .= "\n";
		}

		return $points_description;
	}

	private function extendImport(
		$points_description,
		$number_of_daily_points
	) {
		$lines = explode("\n", $points_description);

		$last_line_blocks = array();
		$extended_lines = array_map(
			function($line) use (&$last_line_blocks) {
				$line = rtrim($line);

				$extended_line = '';
				while (substr($line, 0, 4) == self::ONE_LEVEL_EDITOR_INDENT) {
					if (!empty($last_line_blocks)) {
						$extended_line .= array_shift($last_line_blocks) . ', ';
					}

					$line = substr($line, 4);
				}
				$extended_line .= $line;

				if (!empty($extended_line)) {
					$last_line_blocks = array_map(
						'trim',
						explode(',', $extended_line)
					);
				}

				return $extended_line;
			},
			$lines
		);
		$extended_lines = array_map(
			function($extended_line) {
				if (
					!empty($extended_line)
					and substr($extended_line, -1) == ';'
				) {
					$extended_line = substr($extended_line, 0, -1);
				}

				return $extended_line;
			},
			$extended_lines
		);

		if (
			!empty($extended_lines)
			&& empty($extended_lines[count($extended_lines) - 1])
		) {
			$extended_lines = array_slice(
				$extended_lines,
				0,
				count($extended_lines) - 1
			);
		}
		if (!empty($extended_lines) and $number_of_daily_points > 0) {
			array_unshift($extended_lines, '');
		}

		return $extended_lines;
	}

	private function importToSql($date, $extended_points, $append=false) {
		$escaped_date = Yii::app()->db->quoteValue($date);
		$deleting_sql = sprintf(
			'DELETE FROM {{points}} WHERE `date` = %s AND `daily` = FALSE;',
			$escaped_date
		);

		$order = Constants::MAXIMAL_ORDER_VALUE - 2 * count($extended_points);
		$points_sql_lines = array_map(
			function($extended_point) use ($escaped_date, &$order) {
				$sql_line = sprintf(
					'(%s, %s, "%s", %d)',
					$escaped_date,
					Yii::app()->db->quoteValue($extended_point),
					!empty($extended_point) ? 'SATISFIED' : 'INITIAL',
					$order
				);
				$order += 2;

				return $sql_line;
			},
			$extended_points
		);

		$points_sql = '';
		if (!empty($points_sql_lines)) {
			$points_sql = sprintf(
				"INSERT INTO `{{points}}` (`date`, `text`, `state`, `order`)\n"
					. "VALUES\n\t%s;",
				implode(",\n\t", $points_sql_lines)
			);
		}

		$renumber_sql = Point::getRenumberOrderSql($date);

		$sql = '';
		if (!$append) {
			$sql = "START TRANSACTION;\n\n"
				. "$deleting_sql\n\n"
				. "$points_sql\n\n"
				. "$renumber_sql\n\n"
				. "COMMIT;";
		} else {
			$sql = "$points_sql\n\n"
				. "$renumber_sql";
		}

		return $sql;
	}

	private function parseImport($points_description) {
		if (false === preg_match_all(
			'/
				\#\#\s (\d{4}-\d{2}-\d{2})\r?\n
				\r?\n
				```\r?\n
				((?:.(?!```))*)\r?\n
				```
			/xsu',
			$points_description,
			$matches,
			PREG_SET_ORDER
		)) {
			throw new CHttpException(500, 'Ошибка парсинга импорта.');
		}

		$import = array();
		foreach ($matches as $match) {
			$import[$match[1]] = $match[2];
		}

		return $import;
	}

	private function getPointsNumbers($dates) {
		$criteria = new CDbCriteria();
		$criteria->addInCondition('date', $dates);

		$nonzero_points_numbers = Yii::app()->db->createCommand()
			->select(array('date', 'COUNT(*) AS number'))
			->from('{{points}}')
			->where($criteria->condition)
			->group(array('date'))
			->queryAll(true, $criteria->params);

		$points_numbers = array_fill_keys($dates, 0);
		foreach ($nonzero_points_numbers as $number) {
			$points_numbers[$number['date']] = $number['number'];
		}

		return $points_numbers;
	}

	private function globalImportToSql($global_import, $points_numbers) {
		$sql = '';
		foreach ($global_import as $date => $points_description) {
			$extended_points_description = $this->extendImport(
				$points_description,
				$points_numbers[$date]
			);
			$sql .= $this->importToSql(
				$date,
				$extended_points_description,
				true
			) . "\n\n";
		}

		return "START TRANSACTION;\n\n"
			. "$sql"
			. "COMMIT;";
	}
}
