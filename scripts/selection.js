var Selection = {};

$(document).ready(
	function() {
		var body = $('body');
		var age_points_button_container = $('.age-points-button-container');
		var age_points_button = $(
			'.age-points-button',
			age_points_button_container
		);
		var processing_animation_image = $('img', age_points_button);
		var time_icon = $('span', age_points_button);

		var GetSelectedRows = function() {
			return $('#point-list .point-row.selected');
		};
		var ShowAgePointsButton = function() {
			age_points_button_container.show();
			body.css('padding-bottom', 50);
		};
		var ResetAgePointsButton = function() {
			age_points_button.prop('disabled', false);
			processing_animation_image.hide();
			time_icon.show();
		};

		Selection.hideAgePointsButton = function() {
			age_points_button_container.hide();
			body.css('padding-bottom', '');
		};
		Selection.process = function() {
			if (GetSelectedRows().length) {
				ShowAgePointsButton();
				ResetAgePointsButton();
			} else {
				Selection.hideAgePointsButton();
			}
		};

		age_points_button.click(
			function() {
				var ids =
					GetSelectedRows()
					.map(
						function() {
							var classes = this.className.split(/\s/);
							for (var i = 0; i < classes.length; ++i) {
								var matches = /^point-(\d+)/.exec(classes[i]);
								if (matches) {
									return matches[1];
								}
							}

							return undefined;
						}
					)
					.get();
				if (ids.length) {
					age_points_button.prop('disabled', true);
					processing_animation_image.show();
					time_icon.hide();

					var point_list = $('#point-list');
					point_list.yiiGridView(
						'update',
						{
							type: 'POST',
							url: age_points_button.data('age-points-url'),
							data: $.extend({ids: ids}, CSRF_TOKEN),
							success: function() {
								Selection.hideAgePointsButton();
								point_list.yiiGridView(
									'update',
									{
										url:
											location.pathname
												+ location.search
												+ location.hash
									}
								);
							}
						}
					);
				}
			}
		);
		Selection.hideAgePointsButton();
	}
);
