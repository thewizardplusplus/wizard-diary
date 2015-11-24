<?php

class DayFormatter {
	public static function formatDays($level) {
		$days = intval(substr($level, 1));
		$modulo = $days % 10;
		$unit =
			($modulo == 1 and ($days < 10 or $days > 20))
				? 'дня'
				: 'дней';

		return sprintf("%d %s", $days, $unit);
	}

	public static function formatCompletedDays($days) {
		$modulo = $days % 10;
		if ($modulo == 1 and ($days < 10 or $days > 20)) {
			$unit = 'день';
		} else if ($modulo > 1 and $modulo < 5 and ($days < 10 or $days > 20)) {
			$unit = 'дня';
		} else {
			$unit = 'дней';
		}

		return sprintf("%d %s", $days, $unit);
	}
}
