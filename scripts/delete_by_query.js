$(document).ready(
	function() {
		var query_editor = $('.query-editor');
		var clean_button = $('.clean-button');
		clean_button.click(
			function() {
				query_editor.val('');
				query_editor.focus();
			}
		);

		var delete_by_query_form = $('.delete-by-query-form');
		var delete_by_query_button = $('.delete-by-query-button');
		delete_by_query_button.click(
			function() {
				var query = query_editor.val().trim();
				if (query.length == 0) {
					query_editor.focus();
					return false;
				}

				DeleteByQueryDialog.show(
					query,
					function() {
						delete_by_query_form.submit();
					}
				);

				return false;
			}
		);
	}
);
