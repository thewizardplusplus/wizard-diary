var ProcessSelection = function() {};

$(document).ready(
	function() {
		var age_points_button = $('.age-points-button');
		var processing_animation_image = $('img', age_points_button);
		var time_icon = $('span', age_points_button);

		var GetSelectedRows = function() {
			return $('#point-list .point-row.selected');
		};
		var HideAgePointsButton = function() {
			age_points_button.parent().hide();
		};
		var ResetAgePointsButton = function() {
			age_points_button.prop('disabled', false);
			processing_animation_image.hide();
			time_icon.show();
		};

		ProcessSelection = function() {
			var selected = GetSelectedRows();
			if (selected.length) {
				age_points_button.parent().show();
				ResetAgePointsButton();
			} else {
				HideAgePointsButton();
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
							data: $.extend({ ids: ids }, CSRF_TOKEN),
							success: function() {
								HideAgePointsButton();
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
		HideAgePointsButton();
	}
);
