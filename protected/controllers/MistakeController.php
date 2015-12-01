<?php

class MistakeController extends CController {
	public function filters() {
		return array('accessControl');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$points = $this->collectPointList();
		$data_provider = new CArrayDataProvider(
			$points,
			array(
				'keyField' => 'date',
				'sort' => array(
					'attributes' => array('date', '`order`'),
					'defaultOrder' => array(
						'date' => CSort::SORT_DESC,
						'`order`' => CSort::SORT_ASC,
					)
				)
			)
		);

		$daily_stats = $this->collectDailyStats();

		$this->render(
			'list',
			array(
				'data_provider' => $data_provider,
				'daily_stats' => $daily_stats
			)
		);
	}

	private function collectPointList() {
		$pspell = pspell_new('ru', '', '', 'utf-8', PSPELL_FAST);
		if ($pspell === false) {
			throw new CException('Не удалось инициализировать Pspell.');
		}

		$points = Yii::app()
			->db
			->createCommand()
			->from('{{points}}')
			->where('text != ""')
			->queryAll();

		$points = array_map(
			function($point) use(&$pspell) {
				$counter = 0;
				$point['text'] = preg_replace_callback(
					'/\b[а-яё]+\b/iu',
					function($matches) use (&$pspell, &$counter) {
						$result = '';
						if (pspell_check($pspell, $matches[0])) {
							$result = $matches[0];
						} else {
							$result = '<mark>' . $matches[0] . '</mark>';
							$counter++;
						}

						return $result;
					},
					$point['text']
				);
				$point['counter'] = $counter;

				return $point;
			},
			$points
		);
		$points = array_filter(
			$points,
			function($point) {
				return $point['counter'] > 0;
			}
		);

		return $points;
	}

	private function collectDailyStats() {
		$result = array();
		$daily_stats = Yii::app()
			->db
			->createCommand()
			->select(
				array(
					'date',
					'SUM('
							. 'CASE '
								. 'WHEN `daily` = TRUE THEN 1 '
								. 'ELSE 0 '
							. 'END'
						. ') AS \'daily\''
				)
			)
			->from('{{points}}')
			->group('date')
			->queryAll();
		foreach ($daily_stats as $row) {
			$result[$row['date']] = $row['daily'];
		}

		return $result;
	}

	public function calculateLine($point, $daily_stats) {
		$line = (intval($point['order']) - 1) / 2;
		if (
			array_key_exists($point['date'], $daily_stats)
			and $daily_stats[$point['date']] > 0
		) {
			$line -= $daily_stats[$point['date']] + 1;
		}

		return $line;
	}
}
