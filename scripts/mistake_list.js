var MistakeList = {};

$(document).ready(
	function() {
		var mistake_list = $('#mistake-list');
		var UpdateMistakeList = function() {
			mistake_list.yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToMistakeList = function(data) {
			$.extend(data, CSRF_TOKEN);
			mistake_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: DEFAULT_CURRENT_URL,
					data: data,
					success: function() {
						UpdateMistakeList();
					}
				}
			);
		};

		MistakeList = {
			initialize: function() {
				$('.add-word-button').click(
					function() {
						var word = $(this).data('word');
						RequestToMistakeList({word: word});
					}
				);
			},
			afterUpdate: function() {
				MistakeList.initialize();
			}
		};

		MistakeList.initialize();
	}
);
