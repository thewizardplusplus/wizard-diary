$(document).ready(
	function() {
		var diff_viewer = ace.edit('diff-viewer');
		diff_viewer.$blockScrolling = Infinity;
		diff_viewer.setTheme('ace/theme/twilight');
		diff_viewer.getSession().setMode("ace/mode/diff");
		diff_viewer.setShowPrintMargin(false);
		diff_viewer.setReadOnly(true);
	}
);
