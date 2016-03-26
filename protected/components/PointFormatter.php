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

	public static function encodePointText($text) {
		if (!empty($text) and substr($text, -1) == ';') {
			$text = substr($text, 0, -1);
		}

		return CHtml::encode($text);
	}

	public static function formatPointText($text) {
		$text = self::encodePointText($text);

		$text = preg_replace(
			'/^([^,]+,)\s*(.+)$/',
			'<strong>$1</strong><br />$2',
			$text
		);

		$text = str_replace('&quot;', '"', $text);
		$text = preg_replace(
			'/"([^"]*)"/',
			'&laquo;$1&raquo;',
			$text
		);
		$text = str_replace('"', '&quot;', $text);

		$text = preg_replace('/\s-(?:>|&gt;)\s/', ' &#10148; ', $text);
		$text = preg_replace('/\s-\s/', ' &mdash; ', $text);
		$text = preg_replace_callback(
			'/\(key\s([^\)]+)\)/',
			function($matches) {
				return sprintf('<kbd>%s</kbd>', trim($matches[1]));
			},
			$text
		);

		if (substr_count($text, ',') >= 2) {
			$levels = array_map('trim', explode(',', $text));
			$point_tail = implode(', ', array_slice($levels, 2));
			$point_tail = preg_replace(
				'/(\.?[a-z0-9_]+(?:(?:\.|::)[a-z0-9_]+)*(?:\(\)|\+\+|#)?)/i',
				'<code>$1</code>',
				$point_tail
			);
			$text = sprintf('%s, %s, %s', $levels[0], $levels[1], $point_tail);
		}

		if (!empty($text)) {
			$text .= ';';
		} else {
			$text = '<span class = "not-selectable">&nbsp;</span>';
		}

		return $text;
	}
}
