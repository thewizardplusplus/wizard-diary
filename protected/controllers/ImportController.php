<?php

class ImportController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(
			array('allow', 'users' => array('admin')),
			array('deny')
		);
	}

	public function actionList() {
		$data_provider = new CActiveDataProvider(
			'Import',
			array('criteria' => array('order' => 'date DESC'))
		);

		$this->render('list', array('data_provider' => $data_provider));
	}

	public function actionView($id) {
		$model = $this->loadModel($id);
		$this->render('view', array('model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'import-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['Import'])) {
			$model->attributes = $_POST['Import'];
			$result = $model->save();
			if ($result) {
				if (
					isset($_POST['Import']['import'])
					&& $_POST['Import']['import'] == 'true'
				) {
					$this->importModel($model);
				}
			}
		}

		$this->render('update', array('model' => $model));
	}

	public function actionImport($id) {
		$model = $this->loadModel($id);
		$this->importModel($model);
	}

	private function loadModel($id) {
		$model = Import::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function importModel($model) {
		$extended_points = $this->extendImport($model->points_description);
		$this->importPoints($model->date, $extended_points);

		if (!empty($extended_points)) {
			$model->imported = true;
			$model->save();
		}

		$this->redirect($this->createUrl('point/list'));
	}

	private function importPoints($date, $extended_points) {
		$order = Constants::MAXIMAL_ORDER_VALUE - 2 * count($extended_points);
		$sql_lines = array_map(
			function($extended_point) use ($date, &$order) {
				$sql_line = sprintf(
					'("%s", %s, "%s", FALSE, FALSE, %d)',
					$date,
					Yii::app()->db->quoteValue($extended_point),
					!empty($extended_point) ? 'SATISFIED' : 'INITIAL',
					$order
				);
				$order += 2;

				return $sql_line;
			},
			$extended_points
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
		}
	}

	private function extendImport($points_description) {
		$lines = explode("\n", $points_description);

		$last_line_blocks = array();
		$extended_lines = array_map(
			function($line) use (&$last_line_blocks) {
				$line = rtrim($line);

				$extended_line = '';
				while (
					substr($line, 0, 1) == "\t"
					|| substr($line, 0, 4) == '    '
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
