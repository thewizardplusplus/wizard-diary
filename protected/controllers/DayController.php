<?php

class DayController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$days = Yii::app()
			->db
			->createCommand(
				'SELECT'
					. '`date`,'
					. 'NOT MAX('
						. '`state` = \'INITIAL\' AND LENGTH(`text`) > 0'
					. ') AS \'completed\','
					. 'SUM('
						. 'CASE '
							. 'WHEN `daily` = FALSE AND LENGTH(`text`) > 0 '
								. 'THEN 1 '
							. 'ELSE 0 '
						. 'END'
					. ') AS \'projects\''
				. 'FROM `{{points}}`'
				. 'GROUP BY `date`'
			)
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

		$this->render(
			'view',
			array(
				'data_provider' => $data_provider,
				'my_date' => DateFormatter::formatMyDate($date),
				'date' => DateFormatter::formatDate(CHtml::encode($date))
			)
		);
	}
}
