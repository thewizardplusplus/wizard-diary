var DeleteByQueryDialog = {};

$(document).ready(
	function() {
		var delete_by_query_dialog = $('.delete-by-query-dialog');
		var query_view = $('.query-view', delete_by_query_dialog);
		var ok_button = $('.ok-button', delete_by_query_dialog);

		DeleteByQueryDialog = {
			show: function(query, ok_button_handler) {
				query_view.text(query);

				ok_button.off('click');
				ok_button.click(ok_button_handler);

				delete_by_query_dialog.modal('show');
			},
			hide: function() {
				delete_by_query_dialog.modal('hide');
			}
		};
	}
);
