var SpellingDeletingDialog = {};

$(document).ready(
	function() {
		var spelling_dialog = $('.spelling-deleting-dialog');
		var word_view = $('.word-view', spelling_dialog);
		var ok_button = $('.ok-button', spelling_dialog);

		SpellingDeletingDialog = {
			show: function(word, ok_button_handler) {
				word_view.text(word);

				ok_button.off('click');
				ok_button.click(ok_button_handler);

				spelling_dialog.modal('show');
			},
			hide: function() {
				spelling_dialog.modal('hide');
			}
		};
	}
);
