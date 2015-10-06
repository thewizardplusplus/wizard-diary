<?php

class DayController extends CController {
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

		$this->render('list', array('data_provider' => $data_provider));
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

	private function getStats($date) {
		return Yii::app()
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

				$line .= str_repeat(' ', 4);
			}
			$last_parts = $parts;

			for ($j = $last_index; $j < $number_of_parts; $j++) {
				if (strlen(trim($line)) != 0) {
					$line .= ', ';
				}

				$line .= $parts[$j];
			}

			$points_description .= $line . "\n";
		}
		$points_description = trim($points_description) . "\n";

		return $points_description;
	}
}
