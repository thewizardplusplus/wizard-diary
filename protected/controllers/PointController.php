<?php

class PointController extends CController {
	public function filters() {
		return array(
			'accessControl',
			'postOnly + update',
			'ajaxOnly + find, update'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionFind($query) {
		$points = Point::model()->findAll(
			array(
				'select' => array('id', 'date', 'text', 'daily'),
				'condition' => 'LEFT(text, CHAR_LENGTH(:query)) = :query',
				'params' => array('query' => $query),
				'order' => 'date DESC, `order`'
			)
		);

		$new_points = array();
		$start_date = DateFormatter::getStartDate();
		foreach ($points as $point) {
			$new_points[] = array(
				'id' => $point->id,
				'date' => DateFormatter::formatMyDate(
					$point->date,
					$start_date
				),
				'text' => $point->text,
				'daily' => !!$point->daily
			);
		}
		$points = $new_points;

		echo json_encode($points);
	}

	public function actionUpdate($id) {
		if (!isset($_POST['Point'])) {
			return;
		}

		$model = $this->loadModel($id);
		$model->attributes = $_POST['Point'];
		$model->save();
	}

	public function actionDelete() {
		if (isset($_POST['points_ids'])) {
			$points_ids = $_POST['points_ids'];
			if (!is_array($points_ids)) {
				throw new CHttpException(400, 'Некорректный запрос.');
			}
			foreach ($points_ids as $point_id) {
				if (!is_numeric($point_id)) {
					throw new CHttpException(400, 'Некорректный запрос.');
				}
			}

			$this->deletePosts($points_ids);
			$this->redirect($this->createUrl('day/list'));
		}

		$this->render('delete');
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function deletePosts($points_ids) {
		Point::model()->deleteAllByAttributes(array('id' => $points_ids));
	}
}
