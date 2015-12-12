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
					$a_size =
						array_key_exists('children', $a)
							? count($a['children'])
							: 0;
					$b_size =
						array_key_exists('children', $b)
							? count($b['children'])
							: 0;
					return $b_size - $a_size;
				}
			);
		} else {
			$result['icon'] = 'glyphicon glyphicon-file';
		}

		return $result;
	}
}
