<?php

class DayStateWidget extends CWidget {
	public function setStats($stats) {
		$this->stats = $stats;
	}

	public function run() {
		$is_completed = $this->stats['completed'];
		$is_skipped = $is_completed
			&& $this->stats['skipped']
			&& $this->stats['daily'] > 0;

		$label_class = $is_skipped
			? 'label-default'
			: ($is_completed
				? 'label-success'
				: 'label-primary');
		$label_title = $is_skipped
			? 'Пропущен'
			: ($is_completed
				? 'Завершён'
				: 'Не завершён');
		$label_glyphicon = $is_skipped
			? 'glyphicon-modal-window'
			: ($is_completed
				? 'glyphicon-check'
				: 'glyphicon-unchecked');

		$this->render(
			'day_state_widget',
			array(
				'date' => CHtml::encode($this->stats['date']),
				'label_class' => $label_class,
				'label_title' => $label_title,
				'label_glyphicon' => $label_glyphicon
			)
		);
	}

	private $stats = null;
}
