define(
	'ace/mode/wizard_diary_highlight_rules',
	[
		'require',
		'exports',
		'module',
		'ace/lib/oop',
		'ace/mode/text_highlight_rules'
	],
	function(require, exports, module) {
		var oop = require('../lib/oop');
		var text_highlight_rules =
			require('./text_highlight_rules')
			.TextHighlightRules;

		var highlight_rules = function() {
			this.$rules = {
				start: [
					{
						// first and second hierarchy levels
						token: [
							'keyword.operator.wizard-diary',
							'keyword.control.wizard-diary',
							'variable.language.wizard-diary'
						],
						regex: '^(-\\s\\[[\\sx]\\]\\s)?([^,~]+,|\\s{4})([^,]+,)'
					},
					{
						// only first hierarchy level
						token: [
							'keyword.operator.wizard-diary',
							'keyword.control.wizard-diary'
						],
						regex: '^(-\\s\\[[\\sx]\\]\\s)?([^,~]+,)'
					},
					{
						// double-quoted string
						token: 'string.quoted.double.wizard-diary',
						regex: '"(?:\\\\.|[^"])*"'
					},
					{
						// canceled text
						token: 'markup.italic.wizard-diary',
						regex: '~~.*?~~'
					},
					{
						// operators `-` and `->`
						token: 'keyword.operator.wizard-diary',
						regex: '\\s->?\\s'
					},
					{
						// operators `(key Key)` and `(code Code)`
						token: [
							'keyword.operator.wizard-diary',
							'constant.language.wizard-diary',
							'keyword.operator.wizard-diary'
						],
						regex: '(\\((?:key|code)\\s)((?:\\\\.|[^\\)])+)(\\))'
					},
					{
						// operators `- [ ]` and `- [x]`
						token: 'keyword.operator.wizard-diary',
						regex: '^-\\s\\[[\\sx]\\]\\s'
					}
				]
			};

			this.normalizeRules();
		};
		oop.inherits(highlight_rules, text_highlight_rules);

		exports.WizardDiaryHighlightRules = highlight_rules;
	}
);
