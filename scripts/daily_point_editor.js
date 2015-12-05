$(document).ready(
	function() {
		var lang_tools = ace.require('ace/ext/language_tools');
		lang_tools.addCompleter(
			{
				getCompletions: function(
					editor,
					session,
					position,
					prefix,
					callback
				) {
					callback(null, []);
				}
			}
		);

		var daily_point_editor = ace.edit('daily-point-editor');
		daily_point_editor.$blockScrolling = Infinity;
		daily_point_editor.setTheme('ace/theme/pastel_on_dark');
		daily_point_editor.setShowInvisibles(true);
		daily_point_editor.setShowPrintMargin(false);
		daily_point_editor.setOptions(
			{
				enableBasicAutocompletion: true,
				enableLiveAutocompletion: true
			}
		);
		daily_point_editor.focus();

		var daily_point_mobile_editor = $('#daily-point-mobile-editor');
		var previous_mobile_editor_content = daily_point_mobile_editor.val();

		var FormatPoints = function(points, cursor_position) {
			points = points.map(
				function(point, index) {
					if (
						typeof cursor_position == 'undefined'
						|| cursor_position.row != index
					) {
						return point
							.replace(/((?!\s).)\s{2,}(?=\S)/g, '$1 ')
							.replace(/\s+$/, '')
							.replace(/;$/, '');
					} else {
						return point;
					}
				}
			);

			while (
				points.length
				&& points[0].trim().length == 0
				&& (typeof cursor_position == 'undefined'
				|| cursor_position.row > 0)
			) {
				points.shift();
				if (typeof cursor_position != 'undefined') {
					cursor_position.row--;
				}
			}

			var new_points = [];
			var previous_point_length = 0;
			for (var i = 0; i < points.length; i++) {
				var point = points[i];
				var point_length = point.trim().length;
				if (
					point_length > 0
					|| previous_point_length > 0
					|| (typeof cursor_position != 'undefined'
					&& cursor_position.row == i)
				) {
					new_points.push(point);
				} else if (typeof cursor_position != 'undefined') {
					if (cursor_position.row > i) {
						cursor_position.row--;
					}
				}

				previous_point_length = point_length;
			}
			points = new_points;

			while (
				points.length
				&& points.slice(-1)[0].trim().length == 0
				&& (typeof cursor_position == 'undefined'
				|| cursor_position.row + 1 < points.length)
			) {
				points.pop();
			}
			if (points.length && points.slice(-1)[0].length != 0) {
				points.push('');
			}

			if (typeof cursor_position != 'undefined') {
				return {
					points: points,
					cursor_position: cursor_position
				};
			} else {
				return points;
			}
		};
		var FormatPointsDescription = function(
			points_description,
			cursor_position
		) {
			var points = points_description.split('\n');
			var result = FormatPoints(points, cursor_position);
			if (typeof cursor_position != 'undefined') {
				points_description = result.points.join('\n');
			} else {
				points_description = result.join('\n');
			}

			return {
				points_description: points_description,
				cursor_position: result.cursor_position
			};
		};
		daily_point_editor.formatContent = function() {
			var points_description = this.getValue();
			var cursor_position = this.getCursorPosition();
			var result = FormatPointsDescription(
				points_description,
				cursor_position
			);

			this.setValue(result.points_description, -1);
			if (typeof result.cursor_position != 'undefined') {
				this.moveCursorToPosition(result.cursor_position);
			}
		};
		daily_point_mobile_editor.formatContent = function() {
			var points_description = this.val();
			var result = FormatPointsDescription(points_description);
			this.val(result.points_description);
			previous_mobile_editor_content = result.points_description;
		};

		var saved_flag_container = $('.saved-flag');
		var saved_flag_icon = $('span', saved_flag_container);
		var is_saved = true;
		var SetSavedFlag = function(saved) {
			is_saved = saved;

			if (saved) {
				saved_flag_container
					.addClass('label-success')
					.removeClass('label-danger');
				saved_flag_icon
					.addClass('glyphicon-floppy-saved')
					.removeClass('glyphicon-floppy-remove');

				var last_symbol = document.title.slice(-1);
				if (last_symbol.length && last_symbol == '*') {
					document.title = document.title.slice(0, -1);
				}
			} else {
				saved_flag_container
					.addClass('label-danger')
					.removeClass('label-success');
				saved_flag_icon
					.addClass('glyphicon-floppy-remove')
					.removeClass('glyphicon-floppy-saved');

				var last_symbol = document.title.slice(-1);
				if (last_symbol.length && last_symbol != '*') {
					document.title += '*';
				}
			}
		};

		daily_point_editor.on(
			'change',
			function() {
				SetSavedFlag(false);
			}
		);
		daily_point_editor.on(
			'paste',
			function(event) {
				event.text =
					event.text
					.trim()
					.replace(/;$/, '')
					.replace(/\u00ab([^\u00bb]*)\u00bb/, '"$1"')
					.replace(/\s\u2014\s/, ' - ')
					.replace(/\s\u27a4\s/, ' -> ');
			}
		);

		daily_point_mobile_editor.on(
			'keyup',
			function() {
				var points_description = daily_point_mobile_editor.val();
				if (points_description == previous_mobile_editor_content) {
					return;
				}
				previous_mobile_editor_content = points_description;

				SetSavedFlag(false);
			}
		);
		$('a[data-toggle="tab"]').on(
			'show.bs.tab',
			function(event) {
				var backupped_saved_flag = is_saved;

				var target = $(event.target).attr('href').slice(1);
				switch (target) {
					case 'default':
						var points_description =
							daily_point_mobile_editor.val();
						daily_point_editor.setValue(points_description, -1);

						break;
					case 'mobile':
						var points_description = daily_point_editor.getValue();
						daily_point_mobile_editor.val(points_description);
						previous_mobile_editor_content = points_description;

						break;
				}

				SetSavedFlag(backupped_saved_flag);
			}
		);

		var save_button = $('.save-day-button');
		var save_url = save_button.data('save-url');
		var processing_animation_image = $('img', save_button);
		var save_icon = $('span', save_button);
		var daily_point_editor_container = $(daily_point_editor.container);
		var GetActiveEditor = function() {
			return $('.tab-pane.active').attr('id');
		};
		var FinishAnimation = function() {
			save_button.prop('disabled', false);
			processing_animation_image.hide();
			save_icon.show();

			var active_editor = GetActiveEditor();
			switch (active_editor) {
				case 'default':
					daily_point_editor_container.removeClass('wait');
					break;
				case 'mobile':
					daily_point_mobile_editor.removeClass('wait');
					break;
			}
		};
		var SaveViaAjax = function(callback) {
			save_button.prop('disabled', true);
			processing_animation_image.show();
			save_icon.hide();

			var points_description = '';
			var active_editor = GetActiveEditor();
			switch (active_editor) {
				case 'default':
					daily_point_editor_container.addClass('wait');
					daily_point_editor.formatContent();
					points_description = daily_point_editor.getValue();

					break;
				case 'mobile':
					daily_point_mobile_editor.addClass('wait');
					daily_point_mobile_editor.formatContent();
					points_description = daily_point_mobile_editor.val();

					break;
			}

			var data = $.extend(
				{'points_description': points_description},
				CSRF_TOKEN
			);
			$.post(
				save_url,
				data,
				function() {
					SetSavedFlag(true);
					FinishAnimation();

					if (typeof callback !== 'undefined') {
						callback();
					}
				}
			).fail(
				function(xhr, text_status) {
					FinishAnimation();
					AjaxErrorDialog.handler(xhr, text_status);
				}
			);
		};
		save_button.click(
			function() {
				SaveViaAjax();
			}
		);
		$(window).keydown(
			function(event) {
				if (
					(event.ctrlKey || event.metaKey)
					&& String.fromCharCode(event.which).toLowerCase() == 's'
				) {
					event.preventDefault();
					SaveViaAjax();
				}
			}
		);

		var close_button = $('.close-button');
		var view_url = close_button.data('view-url');
		var CloseDailyPointEditor = function() {
			location.href = view_url;
		};
		close_button.click(
			function() {
				if (!is_saved) {
					DailyPointCloseDialog.show(
						function() {
							DailyPointCloseDialog.hide();
							SaveViaAjax(CloseDailyPointEditor);
						},
						CloseDailyPointEditor
					);
				} else {
					CloseDailyPointEditor();
				}
			}
		);

		$('.daily-point-editor-form').submit(
			function(event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);

		var detector = new MobileDetect(navigator.userAgent);
		if (detector.mobile()) {
			$('a[href="#mobile"]').tab('show');
		}
	}
);
