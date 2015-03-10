$(document).ready(
	function() {
		var import_button = $('.import-button');
		var import_date = import_button.data('date');
		var import_my_date = import_button.data('my-date');
		var import_url = import_button.data('import-url');
		import_button.click(
			function() {
				ImportDialog.show(
					import_my_date,
					import_date,
					function() {
						window.location = import_url;
					}
				);
			}
		);
	}
);
