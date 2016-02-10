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

		$number_of_deleted_points = null;
		$data_provider = null;
		if (isset($_POST['DeleteByQueryForm'])) {
			$model->attributes = $_POST['DeleteByQueryForm'];
			$result = $model->validate();
			if ($result) {
				$dates = $this->findPosts($model->query);
				if (!empty($dates)) {
					$number_of_deleted_points = $this->deletePosts(
						$model->query
					);
					$this->debugOutput($dates, $number_of_deleted_points);

					$data_provider = $this->findDays($dates);
				} else {
					$number_of_deleted_points = 0;
				}
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
				'query_container_class' => $query_container_class,
				'number_of_deleted_points' => $number_of_deleted_points,
				'data_provider' => $data_provider
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

	private function findPosts($query) {
		$points = Point::model()->findAll(
			array(
				'select' => array('date'),
				'condition' => 'LEFT(text, CHAR_LENGTH(:query)) = :query',
				'params' => array('query' => $query),
				'group' => 'date'
			)
		);

		$dates = array();
		foreach ($points as $point) {
			$dates[] = array('date' => $point->date);
		}

		return $dates;
	}

	private function findDays($dates) {
		return new CArrayDataProvider(
			$dates,
			array(
				'keyField' => 'date',
				'sort' => array(
					'attributes' => array('date'),
					'defaultOrder' => array('date' => CSort::SORT_DESC)
				)
			)
		);
	}

	private function deletePosts($query) {
		return Point::model()->deleteAll(
			'LEFT(text, CHAR_LENGTH(:query)) = :query',
			array('query' => $query)
		);
	}

	private function debugOutput($dates, $number_of_deleted_points) {
		$dates = array_map(
			function($date) {
				return sprintf('"%s"', $date['date']);
			},
			$dates
		);

		Yii::log(
			sprintf(
				'%d item%s have been removed '
					. 'from the following day%s: %s.',
				$number_of_deleted_points,
				DeleteByQueryFormatter::formatPlural($number_of_deleted_points),
				DeleteByQueryFormatter::formatPlural(count($dates)),
				implode(', ', $dates)
			),
			'info'
		);
	}
}
