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

	public function getNumberOfChildren() {
		return count($this->children);
	}

	public function isLeaf() {
		return $this->getNumberOfChildren() == 0;
	}

	public function isNeedRemove($number_of_parent_children) {
		return $number_of_parent_children == 1 && $this->isLeaf();
	}

	public function cleanChildren() {
		$new_children = array();
		$number_of_children = $this->getNumberOfChildren();
		foreach ($this->children as $child) {
			$child->cleanChildren();
			if (!$child->isNeedRemove($number_of_children)) {
				$new_children[] = $child;
			}
		}

		$this->children = $new_children;
	}
}
