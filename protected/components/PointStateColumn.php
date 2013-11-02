<?php
	class PointStateColumn extends CDataColumn {
		public function __construct(CGridView $grid) {
			parent::__construct($grid);

			$this->value = '';
			$this->htmlOptions = array('class' => 'point-state-column');
		}

		protected function renderDataCellContent($row, $data) {
			Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
				'scripts/stateChoising.js'), CClientScript::POS_HEAD);

			if (empty($data->text)) {
				return;
			}

			if ($data->state == 'INITIAL') {
				$icon = 'exclamation-sign';
			} else if ($data->state == 'SATISFIED') {
				$icon = 'ok-sign';
			} else if ($data->state == 'NOT_SATISFIED') {
				$icon = 'remove-sign';
			} else if ($data->state == 'CANCELED') {
				$icon = 'minus-sign';
			}
			$state = strtolower(str_replace('_', '-', $data->state));
			$url = $this->grid->controller->createUrl('point/update', array('id'
				=> $data->id));
?>

<div class = "dropdown">
	<a class = "state-<?php echo $state; ?>" href = "#" data-toggle =
		"dropdown">
		<span class = "glyphicon glyphicon-<?php echo $icon; ?>"></span>
	</a>

	<ul class = "dropdown-menu" role = "menu" aria-labelledby = "point<?php echo
		$data->id; ?>-state-label">
		<li role = "presentation">
			<a class = "state-initial" href = "#" tabindex = "-1" role =
				"menuitem" onclick = "return stateChoising('<?php echo $url;
				?>', 'INITIAL');">
				<span class = "glyphicon glyphicon-exclamation-sign"></span>
				Активный
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-satisfied" href = "#" tabindex = "-1" role =
				"menuitem" onclick = "return stateChoising('<?php echo $url;
				?>', 'SATISFIED');">
				<span class = "glyphicon glyphicon-ok-sign"></span> Выполнен
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-not-satisfied" href = "#" tabindex = "-1" role =
				"menuitem" onclick = "return stateChoising('<?php echo $url;
				?>', 'NOT_SATISFIED');">
				<span class = "glyphicon glyphicon-remove-sign"></span> Не
				выполнен
			</a>
		</li>
		<li role = "presentation">
			<a class = "state-canceled" href = "#" tabindex = "-1" role =
				"menuitem" onclick = "return stateChoising('<?php echo $url;
				?>', 'CANCELED');">
				<span class = "glyphicon glyphicon-minus-sign"></span> Отменён
			</a>
		</li>
	</ul>
</div>

<?php
		}
	}
?>
