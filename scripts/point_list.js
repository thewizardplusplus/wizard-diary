var PointList = {};

$(document).ready(
	function() {
		var day_completed_flag = $('.day-completed-flag');
		var stats_url = day_completed_flag.data('stats-url');
		var UpdateDayCompletedFlag = function() {
			$.get(
				stats_url,
				function(data) {
					if (data.completed == "1") {
						day_completed_flag
							.text('Завершён')
							.removeClass('label-primary')
							.addClass('label-success');
					} else {
						day_completed_flag
							.text('Не завершён')
							.removeClass('label-success')
							.addClass('label-primary');
					}
				},
				'json'
			).fail(
				function(xhr, text_status) {
					AjaxErrorDialog.handler(xhr, text_status);
				}
			);
		};

		var point_list = $('#point-list');
		var UpdatePointList = function() {
			point_list.yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToPointList = function(url, data) {
			$.extend(data, CSRF_TOKEN);
			point_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: url,
					data: data,
					success: function() {
						UpdatePointList();
						UpdateDayCompletedFlag();
					}
				}
			);
		};

		PointList = {
			initialize: function() {
				$('.dropdown-menu a[class^=state]').click(
					function() {
						var link = $(this);
						RequestToPointList(
							link.parent().parent().data('update-url'),
							{ 'Point[state]': link.data('state') }
						);

						return false;
					}
				);
			},
			afterUpdate: function() {
				PointList.initialize();
			}
		};

		PointList.initialize();
	}
);
