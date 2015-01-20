<?php

class PointController extends CController {
	const MAXIMAL_ORDER_VALUE = 1000000;

	public function filters() {
		return array(
			'accessControl',
			'postOnly + create, update, age, delete',
			'ajaxOnly + autocomplete, create, update, age, delete'
		);
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Point',
			array(
				'criteria' => array('order' => 'date, `order`'),
				'pagination' => array(
					'pagesize' => Parameters::getModel()->points_on_page
				)
			)
		);

		$pagination = $data_provider->pagination;
		$pagination->setItemCount($data_provider->getTotalItemCount());
		$number_of_pages = $pagination->pageCount;
		if (!isset($_GET['ajax']) or $_GET['ajax'] != 'point-list') {
			$current_page = $number_of_pages - 1;
			if (
				isset($_GET['Point_page'])
				and is_numeric($_GET['Point_page'])
			) {
				$point_page = intval($_GET['Point_page']);
				if ($point_page >= 1 and $point_page <= $number_of_pages) {
					$current_page = $point_page - 1;
				}
			}

			$pagination->currentPage = $current_page;
		}

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'number_of_pages' => $number_of_pages
			)
		);
	}

	public function actionAutocomplete() {
		if (empty($_GET['term'])) {
			echo '[]';
			return;
		}

		$database = Yii::app()->db;
		$sample = $database->quoteValue($_GET['term']);
		$separators_number_in_sample = substr_count($sample, ',');

		$points =
			$database
			->createCommand(
				'SELECT text '
				. 'FROM {{points}} '
				. "WHERE LEFT(text, CHAR_LENGTH($sample)) = $sample"
			)
			->queryAll();
		$points = array_map(
			function($row) use($separators_number_in_sample) {
				$point = str_replace('"', '\"', $row['text']);
				if (substr($point, -1) == ';') {
					$point = substr($point, 0, -1);
				}

				$point_parts = explode(',', $point);
				$point_parts = array_map('trim', $point_parts);
				$point_parts = array_slice(
					$point_parts,
					0,
					$separators_number_in_sample + 1
				);

				return implode(', ', $point_parts);
			},
			$points
		);
		$points = array_unique($points);
		sort($points, SORT_STRING);
		$points = array_map(
			function($point) {
				$point_parts = explode(',', $point);
				$label = end($point_parts);
				$label = trim($label);

				return "{ \"label\": \"$label\", \"value\": \"$point\" }";
			},
			$points
		);

		echo '[ ' . implode(', ', $points) . ' ]';
	}

	public function actionCreate() {
		if (isset($_POST['Point'])) {
			$model = new Point();
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				$date = date('Y-m-d');
				Point::renumberOrderFieldsForDate($date);
			}
		}
	}

	public function actionUpdate($id) {
		if (isset($_POST['Point'])) {
			$model = $this->loadModel($id);
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				if (isset($_POST['Point']['text'])) {
					echo $model->text;
				}
				if (isset($_POST['Point']['order'])) {
					Point::renumberOrderFieldsForDate($model->date);
				}
			}
		}
	}

	public function actionAge() {
		if (!isset($_POST['ids']) or !is_array($_POST['ids'])) {
			throw new CHttpException(500, 'Неверные параметры запроса.');
		}
		foreach ($_POST['ids'] as $id) {
			if (!is_numeric($id)) {
				throw new CHttpException(500, 'Неверные параметры запроса.');
			}
		}

		$ids = array_map('intval', $_POST['ids']);
		$ids_in_string = implode(', ', $ids);

		$start_order_value = self::MAXIMAL_ORDER_VALUE - 2 * count($ids);
		Yii::app()
			->db
			->createCommand('SET @order = ' . $start_order_value)
			->execute();
		Point::model()->updateAll(
			array(
				'date' => new CDbExpression('DATE_SUB(date, INTERVAL 1 DAY)'),
				'order' => new CDbExpression('(@order := @order + 2)')
			),
			array(
				'condition' => 'id IN (' . $ids_in_string . ')',
				'order' => '`order`, id'
			)
		);

		$dates = Yii::app()
			->db
			->createCommand(
				'SELECT date '
				. 'FROM {{points}} '
				. 'WHERE id IN (' . $ids_in_string . ')'
				. 'GROUP BY date'
			)
			->queryAll();
		foreach ($dates as $date) {
			Point::renumberOrderFieldsForDate($date['date']);
		}
	}

	public function actionDelete($id) {
		$model = $this->loadModel($id);
		$date = $model->date;
		$model->delete();

		Point::renumberOrderFieldsForDate($date);
	}

	public function actionImport() {
		if (
			isset($_POST['target-date'])
			and isset($_POST['points-description'])
		) {
			$date = $_POST['target-date'];
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
				throw new CHttpException(500, 'Неверные параметры запроса.');
			}

			$lines = $this->extendImport($_POST['points-description']);
			$order = self::MAXIMAL_ORDER_VALUE - 2 * count($lines);

			$sql_lines = array_map(
				function($line) use ($date, &$order) {
					$sql_line = sprintf(
						'("%s", %s, "%s", FALSE, FALSE, %d)',
						$date,
						Yii::app()->db->quoteValue($line),
						!empty($line) ? 'SATISFIED' : 'INITIAL',
						$order
					);
					$order += 2;

					return $sql_line;
				},
				$lines
			);
			if (!empty($sql_lines)) {
				$sql = sprintf(
					'INSERT INTO `{{points}}`'
						. '(`date`, `text`, `state`, `check`, `daily`, `order`)'
					. 'VALUES %s',
					implode(',', $sql_lines)
				);
				Yii::app()->db->createCommand($sql)->execute();

				Point::renumberOrderFieldsForDate($date);

				$this->redirect($this->createUrl('point/list'));
			}
		}

		$this->render('import_editor');
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function extendImport($text) {
		$lines = explode("\n", $text);

		$last_line_blocks = array();
		$extended_lines = array_map(
			function($line) use (&$last_line_blocks) {
				$line = rtrim($line);

				$extended_line = '';
				while (
					substr($line, 0, 1) == "\t" || substr($line, 0, 4) == '    '
				) {
					if (!empty($last_line_blocks)) {
						$extended_line .= array_shift($last_line_blocks) . ', ';
					}

					$shift_size = substr($line, 0, 1) == "\t" ? 1 : 4;
					$line = substr($line, $shift_size);
				}
				$extended_line .= $line;

				if (!empty($extended_line)) {
					if (substr($extended_line, -1) != ';') {
						$extended_line .= ';';
					}

					$last_line_blocks = array_map(
						'trim',
						explode(',', $extended_line)
					);
				}

				return $extended_line;
			},
			$lines
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
}
