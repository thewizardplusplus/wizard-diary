<?php

/* Based on the proof of concept by Jason Johnson.
 * https://gist.github.com/jajohnson/2836850
 */
class PrefixForest {
	public $root;

	public function __construct() {
		$this->root = new PrefixForestNode();
	}

	public function add($text) {
		$words = preg_split('/\s+/', $text, null, PREG_SPLIT_NO_EMPTY);
		$current = $this->root;
		while (true) {
			$word = array_shift($words);
			if (is_null($word)) {
				break;
			}

			$found = false;
			foreach ($current->children as $child) {
				if ($child->word == $word) {
					$current = $child;
					$found = true;

					break;
				}
			}

			if (!$found) {
				$node = new PrefixForestNode($word);
				$current->children[] = $node;
				$current = $node;
			}
		}
	}

	public function clean() {
		$this->root->cleanChildren();
	}
}
