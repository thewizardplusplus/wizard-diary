$(document).ready(
	function() {
		var import_editor = ace.edit('import-editor');
		import_editor.setTheme('ace/theme/twilight');
		import_editor.setShowInvisibles(true);

		var form = $('.import-form');
		form.submit(
			function() {
				var points_description = import_editor.getValue();
				$('#Import_points_description').val(points_description);
			}
		);

		$('.save-button').click(
			function() {
				form.submit();
			}
		);
		$('.save-and-import-button').click(
			function() {
				$('#Import_import').val('true');
				form.submit();
			}
		);
	}
);
