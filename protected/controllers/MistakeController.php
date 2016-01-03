<?php

class MistakeController extends CController {
	public function filters() {
		return array(
			'accessControl',
			'postOnly + addWord',
			'ajaxOnly + check, addWord'
		);
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$pspell = $this->initPspell();

		if (isset($_POST['word'])) {
			$this->addWord($pspell, $_POST['word']);
		}

		$points = $this->collectPointList($pspell);
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

	public function actionCheck($text) {
		$lines = explode("\n", $text);
		$word_lines = array_map(
			function($line) {
				preg_match_all(
					'/\b[а-яё]+\b/iu',
					$line,
					$matches,
					PREG_OFFSET_CAPTURE
				);

				return $matches;
			},
			$lines
		);

		$pspell = $this->initPspell();
		$mistake_lines = array_map(
			function($words) use ($pspell) {
				return array_filter(
					$words[0],
					function($word) use ($pspell) {
						return !pspell_check($pspell, $word[0]);
					}
				);
			},
			$word_lines
		);

		$line_counter = 0;
		$mistakes = array();
		array_map(
			function($words) use (&$mistakes, $lines, &$line_counter) {
				array_map(
					function($word) use (&$mistakes, $lines, $line_counter) {
						$offset = mb_strlen(
							substr($lines[$line_counter], 0, $word[1])
						);
						$mistakes[] = array(
							'start' => array(
								'line' => $line_counter,
								'offset' => $offset
							),
							'end' => array(
								'line' => $line_counter,
								'offset' => $offset + mb_strlen($word[0])
							)
						);
					},
					$words
				);

				$line_counter++;
			},
			$mistake_lines
		);

		echo json_encode($mistakes);
	}

	public function actionAddWord() {
		if (!isset($_POST['word'])) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}

		$pspell = $this->initPspell();
		$this->addWord($pspell, $_POST['word']);
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

	public function formatMistakes($number) {
		$modulo = $number % 10;
		$unit =
			($modulo == 1 and ($number < 10 or $number > 20))
				? 'пункте'
				: ($number != 0
					? 'пунктах'
					: 'пунктов');

		return sprintf("%d %s", $number, $unit);
	}

	private $custom_spellings_path = '/../../dictionaries/custom_spellings.pws';

	private function collectPointList($pspell) {
		$points = Yii::app()
			->db
			->createCommand()
			->from('{{points}}')
			->where('text != ""')
			->queryAll();

		$points = array_map(
			function($point) use($pspell) {
				$counter = 0;
				$point['text'] = preg_replace_callback(
					'/\b[а-яё]+\b/iu',
					function($matches) use ($pspell, &$counter) {
						$result = '';
						if (pspell_check($pspell, $matches[0])) {
							$result = $matches[0];
						} else {
							$result =
								'<mark>' . $matches[0] . '</mark>'
								. '<button '
									. 'class = "'
										. 'btn '
										. 'btn-default '
										. 'btn-xs '
										. 'blue-button '
										. 'add-word-button'
									.'" '
									. 'data-word = "'
										. CHtml::encode($matches[0])
									. '" '
									. 'title = "Добавить в словарь">'
									. '<span '
										. 'class = "glyphicon glyphicon-plus">'
									. '</span>'
								. '</button>';
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

	private function initPspell() {
		$pspell = pspell_new_personal(
			__DIR__ . $this->custom_spellings_path,
			'ru',
			'',
			'',
			'utf-8',
			PSPELL_FAST
		);
		if ($pspell === false) {
			throw new CException('Не удалось инициализировать Pspell.');
		}

		return $pspell;
	}

	private function addWord($pspell, $word) {
		$result = pspell_add_to_personal($pspell, $word);
		if ($result === false) {
			throw new CException(
				'Не удалось добавить слово в пользовательский словарь Pspell.'
			);
		}

		$result = pspell_save_wordlist($pspell);
		if ($result === false) {
			throw new CException(
				'Не удалось сохранить пользовательский словарь Pspell.'
			);
		}
	}
}
