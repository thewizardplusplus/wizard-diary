$(document).ready(
	function() {
		var delete_by_query_button = $('.delete-by-query-button');
		var query_editor = $('.query-editor');
		var delete_by_query_form = $('.delete-by-query-form');
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
