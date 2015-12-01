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
		var FormatMistakes = function(number) {
			number = number || 0;

			var modulo = number % 10;
			var unit =
				modulo == 1 && (number < 10 || number > 20)
					? 'пункте'
					: (number != 0
						? 'пунктах'
						: 'пунктов');

			return number + ' ' + unit;
		};

		$('.custom-spellings-clean-button').click(
			function() {
				MistakesDialog.show(
					function() {
						MistakesDialog.hide();
						RequestToMistakeList({clean: true});
					}
				);
			}
		);

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

				var total_counter = $('.mistake-list-total-counter').text();
				var formatted_total_counter = FormatMistakes(total_counter);
				$('.mistakes-counter-view').text(formatted_total_counter);
			}
		};

		MistakeList.initialize();
	}
);
