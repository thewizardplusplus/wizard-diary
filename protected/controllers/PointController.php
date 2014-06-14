<?php

class PointController extends CController {
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

		if (!isset($_GET['ajax']) or $_GET['ajax'] != 'point-list') {
			$pagination = $data_provider->pagination;
			$pagination->setItemCount($data_provider->getTotalItemCount());
			$pagination->currentPage = $pagination->pageCount - 1;
		}

		$this->render(
			'list',
			array('data_provider' => $data_provider)
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
		$ids = implode(', ', $ids);

		Yii::app()
			->db
			->createCommand(
				'UPDATE {{points}} '
				. 'SET date = DATE_SUB(date, INTERVAL 1 DAY),'
					. '`order` = 18446744073709551615 '
				. 'WHERE id IN ('
					. $ids
				. ')'
			)
			->execute();

		$dates = Yii::app()
			->db
			->createCommand(
				'SELECT date '
				. 'FROM {{points}} '
				. 'WHERE id IN ('
					. $ids
				. ')'
			)
			->queryAll();
		$dates = array_map(
			function($row) {
				return $row['date'];
			},
			$dates
		);
		$dates = array_unique($dates);

		foreach ($dates as $date) {
			Point::renumberOrderFieldsForDate($date);
		}
	}

	public function actionDelete($id) {
		$model = $this->loadModel($id);
		$date = $model->date;
		$model->delete();

		Point::renumberOrderFieldsForDate($date);
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
