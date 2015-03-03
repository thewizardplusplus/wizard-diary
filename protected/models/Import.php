<?php

class Import extends CActiveRecord {
	public static function model($class_name = __CLASS__) {
		return parent::model($class_name);
	}

	public function tableName() {
		return '{{imports}}';
	}

	public function rules() {
		return array(array('points_description', 'safe'));
	}

	public function attributeLabels() {
		return array('points_description' => 'Описание пунктов');
	}

	public function getFormattedDate() {
		return implode('.', array_reverse(explode('-', $this->date)));
	}

	public function getFormattedPointsDescription() {
		$points = $this->getPoints();
		$number_of_points = count($points);
		$maximal_prefix_length = strlen(
			number_format($number_of_points, 0, '', '')
		);

		$line_number = 1;
		$points = array_map(
			function($point) use ($maximal_prefix_length, &$line_number) {
				$prefix = number_format($line_number, 0, '', '');
				while (strlen($prefix) < $maximal_prefix_length) {
					$prefix = ' ' . $prefix;
				}

				$point = rtrim($point);
				$point = preg_replace(
					'/(\t|    )/',
					$this->getUnicodeString('\u00a0\u00a0\u00a0\u21e5'),
					$point
				);
				$point = str_replace(
					' ',
					$this->getUnicodeString('\u00b7'),
					$point
				);
				$point =
					$prefix . ' | '
					. $point
					. $this->getUnicodeString('\u00b6');

				$line_number++;
				return $point;
			},
			$points
		);

		return implode("\n", $points);
	}

	private function getPoints() {
		$points = explode("\n", $this->points_description);
		while (!empty($points) and strlen(trim(reset($points))) == 0) {
			array_shift($points);
		}
		while (!empty($points) and strlen(trim(end($points))) == 0) {
			array_pop($points);
		}

		return $points;
	}

	private function getUnicodeString($escaped_string) {
		return json_decode('"' . $escaped_string . '"');
	}

	public function getNumberOfPoints() {
		$points = $this->getPoints();
		$number_of_points = count($points);

		$unit = '';
		$modulo = $number_of_points % 10;
		if ($modulo == 1) {
			$unit = 'пункт';
		} else if (
			$modulo > 1 and $modulo < 5
			and ($number_of_points < 10 or $number_of_points > 20)
		) {
			$unit = 'пункта';
		} else {
			$unit = 'пунктов';
		}

		return number_format($number_of_points, 0, '', '') . ' ' . $unit;
	}
}
