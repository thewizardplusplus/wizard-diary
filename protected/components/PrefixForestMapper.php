<?php

class PrefixForestMapper {
	public function process($node) {
		$result = array('text' => $node->word);
		if (!$node->isLeaf()) {
			$result['icon'] = 'glyphicon glyphicon-folder-open';

			$result['children'] = array();
			foreach ($node->children as $child) {
				$result['children'][] = $this->process($child);
			}

			usort(
				$result['children'],
				function($a, $b) {
					return strcmp($a['text'], $b['text']);
				}
			);
		} else {
			$result['icon'] = 'glyphicon glyphicon-file';
		}

		return $result;
	}
}
