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

	public function actionUpdate($id) {
		if (isset($_POST['Point'])) {
			$model = $this->loadModel($id);
			$model->attributes = $_POST['Point'];
			$result = $model->save();

			if ($result) {
				if (isset($_POST['Point']['text'])) {
					echo $model->text;
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

		$start_order_value = Constants::MAXIMAL_ORDER_VALUE - 2 * count($ids);
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

	public function actionAddDailyPoints() {
		DailyPointsAdder::addDailyPoints();
		$this->redirect($this->createUrl('point/list'));
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
