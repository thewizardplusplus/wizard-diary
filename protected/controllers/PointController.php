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
		if (!isset($_POST['Point'])) {
			return;
		}

		$model = $this->loadModel($id);
		$model->attributes = $_POST['Point'];
		$model->save();
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
