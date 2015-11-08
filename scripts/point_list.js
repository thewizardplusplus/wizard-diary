var PointList = {};

$(document).ready(
	function() {
		var day_completed_flag = $('.day-completed-flag');
		var day_completed_inner_flag = $('span.glyphicon', day_completed_flag);
		var stats_url = day_completed_flag.data('stats-url');
		var UpdateDayCompletedFlag = function() {
			$.get(
				stats_url,
				function(data) {
					if (data.completed == "1") {
						day_completed_flag
							.attr('title', 'Завершён')
							.removeClass('label-primary')
							.addClass('label-success');
						day_completed_inner_flag
							.removeClass('glyphicon-unchecked')
							.addClass('glyphicon-check');
					} else {
						day_completed_flag
							.attr('title', 'Не завершён')
							.removeClass('label-success')
							.addClass('label-primary');
						day_completed_inner_flag
							.removeClass('glyphicon-check')
							.addClass('glyphicon-unchecked');
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
