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

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'daily_stats' => $daily_stats
			)
		);
	}

	public function actionView($date) {
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
		$stats = $this->getStats($date);
		echo json_encode($stats);
	}

	public function actionUpdate($date) {
		if (isset($_POST['points_description'])) {
			$points_description = $this->extendImport(
				$_POST['points_description']
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
		Yii::log(print_r($point_hierarchy, true), 'info');

		$this->render(
			'update',
			array(
				'points_description' => $points_description,
				'my_date' => DateFormatter::formatMyDate($date),
				'date' => DateFormatter::formatDate($encoded_date),
				'raw_date' => CHtml::encode($encoded_date),
				'stats' => $stats
			)
		);
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
			return array('date' => $date, 'completed' => true, 'projects' => 0);
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
				$tail = implode(', ', array_slice($parts, 2));
				$result = preg_match(
					'/[A-Z]/i',
					$tail,
					$matches,
					PREG_OFFSET_CAPTURE
				);
				if ($result == 1) {
					$tail = trim(substr($tail, 0, $matches[0][1]));
				}

				$tails[] = $tail;
			}
		}

		$new_hierarchy = array();
		foreach ($hierarchy as $level_1 => $level_2_list) {
			sort($level_2_list, SORT_STRING);
			$new_hierarchy[$level_1] = $level_2_list;
		}
		ksort($new_hierarchy, SORT_STRING);
		$hierarchy = $new_hierarchy;

		$tails = array_unique($tails);
		sort($tails);

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

	private function extendImport($points_description) {
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
		if (!empty($extended_lines)) {
			array_unshift($extended_lines, '');
		}

		return $extended_lines;
	}

	private function importToSql($date, $extended_points) {
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

		return
			"START TRANSACTION;\n\n"
			. "$deleting_sql\n\n"
			. "$points_sql\n\n"
			. "$renumber_sql\n\n"
			. "COMMIT;";
	}
}
