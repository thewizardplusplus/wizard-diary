<?php

class PointFormatter {
	public static function formatNumberOfPoints($number_of_points) {
		$unit = '';
		$modulo = $number_of_points % 10;
		if (
			$modulo == 1
			and ($number_of_points < 10 or $number_of_points > 20)
		) {
			$unit = 'пункт';
		} else if (
			$modulo > 1 and $modulo < 5
			and ($number_of_points < 10 or $number_of_points > 20)
		) {
			$unit = 'пункта';
		} else {
			$unit = 'пунктов';
		}

		return sprintf("%d %s", $number_of_points, $unit);
	}
}
