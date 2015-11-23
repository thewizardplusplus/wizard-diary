<?php

class PrefixForestCollector {
	public function collect($node, $prefix = '') {
		foreach ($node->children as $child) {
			if ($child->isLeaf()) {
				$this->lines[] = !empty($prefix) ? $prefix : $child->word;
				continue;
			}

			$this->collect($child, trim($prefix . ' ' . $child->word));
		}
	}

	public function getLines() {
		$lines = array_count_values($this->lines);
		$lines = array_filter(
			$lines,
			function($number) {
				return $number > 1;
			}
		);
		arsort($lines, SORT_NUMERIC);

		return $lines;
	}

	private $lines = array();
}
