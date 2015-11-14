<?php

class DateCompleter {
	public static function complete(
		$dates,
		$date_getter,
		$date_setter
	) {
		$completted_dates = array();
		if (empty($dates)) {
			return $completted_dates;
		}

		$completted_date = null;
		foreach ($dates as $key => $value) {
			$current_date = date_create($date_getter($key, $value));
			if (is_null($completted_date)) {
				$completted_date = clone $current_date;
			}

			while ($completted_date->diff($current_date)->days > 0) {
				self::addComplettedDate(
					$completted_dates,
					$date_setter,
					null,
					null,
					$completted_date
				);
			}

			self::addComplettedDate(
				$completted_dates,
				$date_setter,
				$key,
				$value,
				$completted_date
			);
		}

		$maximal_date = self::incrementDate(date_create());
		while ($completted_date->diff($maximal_date)->days > 0) {
			self::addComplettedDate(
				$completted_dates,
				$date_setter,
				null,
				null,
				$completted_date
			);
		}

		return $completted_dates;
	}

	private static function addComplettedDate(
		&$completted_dates,
		$date_setter,
		$key,
		$value,
		$completted_date
	) {
		$date_setter(
			$completted_dates,
			$key,
			$value,
			$completted_date->format('Y-m-d')
		);
		self::incrementDate($completted_date);
	}

	private static function incrementDate($date) {
		$date->add(DateInterval::createFromDateString('1 day'));
		return $date;
	}
}
