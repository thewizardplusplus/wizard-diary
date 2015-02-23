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
		$points = explode("\n", $this->points_description);
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

				$new_point = $prefix . ' | ' . $point;

				$line_number++;
				return $new_point;
			},
			$points
		);

		return implode("\n", $points);
	}
}
