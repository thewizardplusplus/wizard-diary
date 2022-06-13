<?php

class MistakeController extends CController {
	public function filters() {
		return array('accessControl', 'postOnly + check', 'ajaxOnly + check');
	}

	public function accessRules() {
		return array(array('allow', 'users' => array('admin')), array('deny'));
	}

	public function actionList() {
		$pspells = $this->initPspells(self::$pspell_languages);
		$points = $this->collectPointList($pspells);
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

	public function actionCheck() {
		if (!isset($_POST['text'])) {
			throw new CHttpException(400, 'Некорректный запрос.');
		}

		$lines = explode("\n", $_POST['text']);
		$word_lines = array_map(
			function($line) {
				if (false === preg_match_all(
					Spelling::WORD_PATTERN,
					$line,
					$matches,
					PREG_OFFSET_CAPTURE
				)) {
					throw new CHttpException(500, 'Ошибка парсинга строки текста.');
				}

				return $matches;
			},
			$lines
		);

		$pspells = $this->initPspells(self::$pspell_languages);
		$spellings = $this->getSpellings();
		$mistake_lines = array_map(
			function($words) use ($pspells, $spellings) {
				return array_filter(
					$words[0],
					function($word) use ($pspells, $spellings) {
						$word = $word[0];
						return
							(!in_array($word, $spellings)
							and !$this->checkWord($pspells, $word));
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
							substr($lines[$line_counter], 0, $word[1]),
							'utf-8'
						);
						$mistakes[] = array(
							'start' => array(
								'line' => $line_counter,
								'offset' => $offset
							),
							'end' => array(
								'line' => $line_counter,
								'offset' => $offset
									+ mb_strlen($word[0], 'utf-8')
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

	private static $pspell_languages = array('ru', 'en_US');

	private function collectPointList($pspells) {
		$points = Yii::app()
			->db
			->createCommand()
			->from('{{points}}')
			->where('text != ""')
			->queryAll();
		$spellings = $this->getSpellings();

		$points = array_map(
			function($point) use($pspells, $spellings) {
				$counter = 0;
				$point['text'] = preg_replace_callback(
					Spelling::WORD_PATTERN,
					function($matches) use ($pspells, $spellings, &$counter) {
						$result = '';
						$word = $matches[0];
						if (
							in_array($word, $spellings)
							or $this->checkWord($pspells, $word)
						) {
							$result = $word;
						} else {
							$result =
								'<mark>' . $word . '</mark>'
								. '<button '
									. 'class = "'
										. 'btn '
										. 'btn-default '
										. 'btn-xs '
										. 'blue-button '
										. 'add-word-button'
									.'" '
									. 'data-word = "'
										. CHtml::encode($word)
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

	private function initPspells($languages) {
		$pspells = array();
		foreach ($languages as $language) {
			$pspell = pspell_new($language, '', '', 'utf-8', PSPELL_FAST);
			if ($pspell === false) {
				throw new CException('Не удалось инициализировать Pspell.');
			}

			$pspells[] = $pspell;
		}

		return $pspells;
	}

	private function checkWord($pspells, $word) {
		foreach ($pspells as $pspell) {
			if (pspell_check($pspell, $word)) {
				return true;
			}
		}

		return false;
	}

	private function getSpellings() {
		$spellings = array();
		$spellings_objects =
			Spelling::model()
			->findAll(array('select' => array('word')));
		foreach ($spellings_objects as $spelling_object) {
			$spellings[] = $spelling_object->word;
		}

		return $spellings;
	}
}
