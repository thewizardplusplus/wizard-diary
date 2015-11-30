$(document).ready(
	function() {
		var UPDATE_DELAY = 500;

		var levels_picker = $('#achievements_levels_select').selectpicker();
		var texts_picker = $('#achievements_texts_select').selectpicker();
		var detector = new MobileDetect(navigator.userAgent);
		var ResetAchievementSelects = function() {
			levels_picker.selectpicker('deselectAll');
			texts_picker.selectpicker('deselectAll');
		};
		var UpdateAchievementList = function() {
			var levels = levels_picker.val();
			var texts = texts_picker.val();
			$.fn.yiiListView.update(
				'achievements-list',
				{data: {search: {levels: levels, texts: texts}}}
			);
		};

		var update_timer = null;
		var UpdateWithDelay = function() {
			clearTimeout(update_timer);
			update_timer = setTimeout(UpdateAchievementList, UPDATE_DELAY);
		};

		ResetAchievementSelects();
		levels_picker.change(UpdateWithDelay);
		texts_picker.change(UpdateWithDelay);
		if (detector.mobile()) {
			levels_picker.selectpicker('mobile');
			texts_picker.selectpicker('mobile');
		}

		$('.achievement-list-reset-button').click(
			function() {
				ResetAchievementSelects();
				UpdateAchievementList();
			}
		);

		$('.achievements-selects-form').submit(
			function (event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);
	}
);
