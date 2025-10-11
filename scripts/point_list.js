var PointList = {};

$(document).ready(
	function() {
		var day_completed_flag = $('.day-completed-flag');
		var day_completed_inner_flag = $('span.glyphicon', day_completed_flag);
		var stats_url = day_completed_flag.data('stats-url');
		var UpdateDayCompletedFlag = function(data) {
			var is_completed = data.completed == '1';
			var is_skipped = is_completed
				&& data.skipped == '1'
				&& parseInt(data.daily, 10) > 0;

			if (is_skipped) {
				day_completed_flag
					.attr('title', 'Пропущен')
					.removeClass('label-primary label-success')
					.addClass('label-default');
				day_completed_inner_flag
					.removeClass('glyphicon-unchecked glyphicon-check')
					.addClass('glyphicon-modal-window');
			} else if (is_completed) {
				day_completed_flag
					.attr('title', 'Завершён')
					.removeClass('label-primary label-default')
					.addClass('label-success');
				day_completed_inner_flag
					.removeClass('glyphicon-unchecked glyphicon-modal-window')
					.addClass('glyphicon-check');
			} else {
				day_completed_flag
					.attr('title', 'Не завершён')
					.removeClass('label-success label-default')
					.addClass('label-primary');
				day_completed_inner_flag
					.removeClass('glyphicon-check glyphicon-modal-window')
					.addClass('glyphicon-unchecked');
			}
		};
		var UpdateDaySatisfiedView = function(data) {
			var text = '&mdash;';
			if (data.satisfied != -1) {
				text = data.satisfied + '%';
			}

			$('.day-satisfied-view').html(text);
		};
		var UpdateDayStats = function() {
			$.get(
				stats_url,
				function(data) {
					UpdateDayCompletedFlag(data);
					UpdateDaySatisfiedView(data);
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
						UpdateDayStats();
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
