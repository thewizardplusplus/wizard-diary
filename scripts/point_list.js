var PointList = {};

$(document).ready(
	function() {
		var point_list = $('#point-list');
		var UpdatePointList = function() {
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
					}
				}
			);
		};

		PointList = {
			deleting: function(link) {
				var url = $(link).attr('href');
				var text = $('#point-text-' + $.url(url).param('_id'))
					.data('text');
				if (text != '') {
					text =
						'пункт <strong>&laquo;'
							+ text
							+ '&raquo;</strong>';
				} else {
					text = 'пункт-разделитель';
				}
				DeletingDialog.show(
					text,
					function() {
						DeletingDialog.hide();
						RequestToPointList(url, {});
					}
				);

				return false;
			},
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
