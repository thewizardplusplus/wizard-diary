var MistakeList = {};

$(document).ready(
	function() {
		var mistake_list = $('#mistake-list');
		var mistakes_counter_view = $('.mistakes-counter-view');
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
		var UpdateMistakeCounter = function() {
			var number_of_mistakes = $('.mistake-list-total-counter').text();
			var formatted_number_of_mistakes = FormatMistakes(
				number_of_mistakes
			);
			mistakes_counter_view.text(formatted_number_of_mistakes);
		};

		MistakeList = {
			initialize: function() {
				$('.add-word-button').click(
					function() {
						var word = $(this).data('word');
						MistakesAddingDialog.show(
							word,
							function() {
								MistakesAddingDialog.hide();
								RequestToMistakeList({word: word});
							}
						);
					}
				);
			},
			afterUpdate: function() {
				MistakeList.initialize();
				UpdateMistakeCounter();
			}
		};

		MistakeList.initialize();
	}
);
