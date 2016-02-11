<?php

class DeleteByQueryFormatter {
	public static function formatPlural($number) {
		return $number > 1 ? 's' : '';
	}

	public static function formatLabelStart($number) {
		$text = '';
		if ($number % 10 == 1 and $number != 11) {
			$text = 'Был удален';
		} else {
			$text = 'Было удалено';
		}

		return $text;
	}

	public static function formatLabelEnd($number) {
		return $number > 1 ? 'следующих дней' : 'следующего дня';
	}
}
