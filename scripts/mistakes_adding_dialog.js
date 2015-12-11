var MistakesAddingDialog = {};

$(document).ready(
	function() {
		var mistakes_dialog = $('.custom-spellings-adding-dialog');
		var wrong_word_view = $('.wrong-word', mistakes_dialog);
		var ok_button = $('.ok-button', mistakes_dialog);

		MistakesAddingDialog = {
			show: function(wrong_word, ok_button_handler) {
				wrong_word_view.text(wrong_word);

				ok_button.off('click');
				ok_button.click(ok_button_handler);

				mistakes_dialog.modal('show');
			},
			hide: function() {
				mistakes_dialog.modal('hide');
			}
		};
	}
);
