<?php

/* Based on the proof of concept by Jason Johnson.
 * https://gist.github.com/jajohnson/2836850
 */
class PrefixForestNode {
	public $word;
	public $children;

	public function __construct($word = null, $children = array()) {
		$this->word = $word;
		$this->children = $children;
	}

	public function isNeedRemove($number_of_parent_children) {
		$number_of_children = count($this->children);
		return
			$number_of_parent_children == 1
			&& ($number_of_children == 0
			|| $number_of_children == 1);
	}

	public function cleanChildren() {
		$new_children = array();
		$number_of_children = count($this->children);
		foreach ($this->children as $child) {
			$child->cleanChildren();
			if (!$child->isNeedRemove($number_of_children)) {
				$new_children[] = $child;
			}
		}

		$this->children = $new_children;
	}
}
