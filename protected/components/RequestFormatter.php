<?php

class RequestFormatter {
	public static function formatRequests($number_of_requests) {
		$unit = '';
		$modulo = $number_of_requests % 10;
		if (
			$modulo == 1
			and ($number_of_requests < 10 or $number_of_requests > 20)
		) {
			$unit = 'запрос';
		} else if (
			$modulo > 1 and $modulo < 5
			and ($number_of_requests < 10 or $number_of_requests > 20)
		) {
			$unit = 'запроса';
		} else {
			$unit = 'запросов';
		}

		return sprintf("%d %s", $number_of_requests, $unit);
	}
}
