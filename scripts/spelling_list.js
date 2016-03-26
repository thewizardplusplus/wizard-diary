var SpellingList = {};

$(document).ready(
	function() {
		var spelling_list = $('#spelling-list');
		var UpdateSpellingList = function() {
			spelling_list.yiiGridView(
				'update',
				{
					url:
						location.pathname
							+ location.search
							+ location.hash
				}
			);
		};
		var RequestToSpellingList = function(url, data) {
			data = data || {};
			$.extend(data, CSRF_TOKEN);
			spelling_list.yiiGridView(
				'update',
				{
					type: 'POST',
					url: url,
					data: data,
					success: function() {
						UpdateSpellingList();
					}
				}
			);
		};

		var spellings_clean_button = $('.spellings-clean-button');
		var spellings_clean_url = spellings_clean_button.data(
			'spellings-clean-url'
		);
		spellings_clean_button.click(
			function() {
				SpellingsCleaningDialog.show(
					function() {
						SpellingsCleaningDialog.hide();
						RequestToSpellingList(spellings_clean_url);
					}
				);
			}
		);

		SpellingList = {
			delete: function(link) {
				var url = $(link).attr('href');
				var word_id = URI(url).search(true)['id'];
				var word = $('#word-' + word_id).text();
				SpellingDeletingDialog.show(
					word,
					function() {
						SpellingDeletingDialog.hide();
						RequestToSpellingList(url, {id: word_id});
					}
				);

				return false;
			}
		};
	}
);
