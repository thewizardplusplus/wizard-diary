<?php

class PointController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + update', 'ajaxOnly + update');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionUpdate($id) {
		if (!isset($_POST['Point'])) {
			return;
		}

		$model = $this->loadModel($id);
		$model->attributes = $_POST['Point'];
		$model->save();
	}

	public function actionDeleteByQuery() {
		$model = new DeleteByQueryForm();

		if (
			isset($_POST['ajax'])
			and $_POST['ajax'] == 'delete-by-query-form'
		) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['DeleteByQueryForm'])) {
			$model->attributes = $_POST['DeleteByQueryForm'];
			$result = $model->validate();
			if ($result) {
				Yii::log($model->query, 'info');
			}
		}

		$query_container_class =
			count($model->getErrors('query'))
				? ' has-error'
				: '';
		$this->render(
			'delete_by_query',
			array(
				'model' => $model,
				'query_container_class' => $query_container_class
			)
		);
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}
}
