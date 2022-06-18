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

	public function actionFind($query, $search_from_beginning) {
		$points = Point::model()->findAll(
			array(
				'select' => array('id', 'date', 'text', 'daily'),
				'condition' => $search_from_beginning == 'true'
					? 'LEFT(text, CHAR_LENGTH(:query)) = :query'
					: 'INSTR(CAST(text AS BINARY), CAST(:query AS BINARY)) != 0',
				'params' => array('query' => $query),
				'order' => 'date DESC, `order`'
			)
		);

		$new_points = array();
		$start_date = DateFormatter::getStartDate();
		foreach ($points as $point) {
			$new_points[] = array(
				'id' => $point->id,
				'date' => $point->date,
				'my_date' => DateFormatter::formatMyDate(
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
		$result = $model->validate();
		if (!$result) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}

		$model->save();
	}

	public function actionUpdateBatch() {
		if (
			isset($_POST['points_ids'])
			and isset($_POST['query'])
			and isset($_POST['replacement'])
			and isset($_POST['search_from_beginning'])
		) {
			$this->testPointsIds($_POST['points_ids']);

			throw new CHttpException(500, 'Not yet implemented.');
		}

		$this->render('update_batch');
	}

	public function actionDeleteBatch() {
		if (isset($_POST['points_ids']) and isset($_POST['points_dates'])) {
			$this->testPointsIds($_POST['points_ids']);
			$this->testPointsDates($_POST['points_dates']);

			$this->deletePosts($_POST['points_ids']);
			$this->deleteSeparatorsDuplicates($_POST['points_dates']);

			$this->redirect($this->createUrl('day/list'));
		}

		$this->render('delete_batch');
	}

	private function loadModel($id) {
		$model = Point::model()->findByPk($id);
		if (is_null($model)) {
			throw new CHttpException(404, 'Запрашиваемая страница не найдена.');
		}

		return $model;
	}

	private function testPointsIds($points_ids) {
		if (!is_array($points_ids)) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}
		foreach ($points_ids as $point_id) {
			if (!is_numeric($point_id)) {
				throw new CHttpException(400, 'Некорректный запрос.');
			}
		}
	}

	private function testPointsDates($points_dates) {
		if (!is_array($points_dates)) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}
		foreach ($points_dates as $point_date) {
			if (!preg_match('/\d{4}(?:-\d{2}){2}/', $point_date)) {
				throw new CHttpException(400, 'Некорректный запрос.');
			}
		}
	}

	private function deletePosts($points_ids) {
		Point::model()->deleteAllByAttributes(array('id' => $points_ids));
	}

	private function deleteSeparatorsDuplicates($points_dates) {
		$points = Point::model()->findAllByAttributes(
			array('date' => $points_dates),
			array(
				'select' => array('id', 'date', 'text'),
				'order' => 'date, `order`'
			)
		);

		$new_points = [];
		foreach ($points as $point) {
			if (!array_key_exists($point->date, $new_points)) {
				$new_points[$point->date] = array();
			}

			$new_points[$point->date][] = array(
				'id' => $point->id,
				'text' => $point->text
			);
		}
		$points = $new_points;

		$points_for_deletion = [];
		foreach ($points as $days_points) {
			$i = 0;
			while (
				$i < count($days_points)
				and empty($days_points[$i]['text'])
			) {
				$points_for_deletion[] = $days_points[$i]['id'];
				$i++;
			}

			for ($i = 0; $i < count($days_points); $i++) {
				if (
					$i > 0
					and empty($days_points[$i - 1]['text'])
					and empty($days_points[$i]['text'])
				) {
					$points_for_deletion[] = $days_points[$i]['id'];
				}
			}

			$i = count($days_points) - 1;
			while ($i >= 0 and empty($days_points[$i]['text'])) {
				$points_for_deletion[] = $days_points[$i]['id'];
				$i--;
			}
		}
		$points_for_deletion = array_unique($points_for_deletion, SORT_NUMERIC);

		Point::model()->deleteAllByAttributes(
			array('id' => $points_for_deletion)
		);
	}
}
