$(document).ready(
	function() {
		var day_editor = ace.edit('day-editor');
		day_editor.setTheme('ace/theme/twilight');
		day_editor.setShowInvisibles(true);
		day_editor.setShowPrintMargin(false);

		var number_of_points_view = $('.number-of-points-view');
		var SetNumberOfPoints = function() {
			var points_description = day_editor.getValue();
			var points =
				points_description
				.split('\n')
				.filter(
					function(line) {
						return line.trim().length != 0;
					}
				);

			var number_of_points = points.length;
			number_of_points_view.text(
				number_of_points.toString()
				+ ' '
				+ GetPointUnit(number_of_points)
			);
		};
		day_editor.on(
			'change',
			function() {
				SetNumberOfPoints();
			}
		);
	}
);
