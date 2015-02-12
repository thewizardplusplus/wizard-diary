$(document).ready(
	function() {
		var import_editor = ace.edit('import-editor');
		import_editor.setTheme('ace/theme/twilight');
		import_editor.setShowInvisibles(true);

		$('.import-form').submit(
			function() {
				$('#points-description').val(import_editor.getValue());
				$('#Import_points_description').val(import_editor.getValue());
			}
		);
	}
);
