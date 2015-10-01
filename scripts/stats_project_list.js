$(document).ready(
	function() {
		var SEARCH_DELAY = 500;
		var DATE_PATTERN = /^\d+\.\d+$/;

		var search_form = $('.search-form');
		var project_list = $('.project-list');
		if (STATS_DATA.length == 0) {
			search_form.hide();
			project_list.hide();
			$('.empty-label').show();

			return;
		}

		var tree = project_list.jstree(
			{
				core: {
					data: STATS_DATA,
					themes: {stripes: true, responsive: true},
					strings: {'Loading ...': 'Загрузка...'}
				},
				plugins: ['search'],
				search: {
					show_only_matches: true,
					search_callback: function(query, node) {
						var queries =
							query
							.replace(/[^\wА-Яа-я\s]/g, '')
							.split(/\s+/);
						var words =
							node
							.text
							.replace(/[^\wА-Яа-я\s]/g, '')
							.split(/\s+/);
						for (var i = 0; i < queries.length; i++) {
							var query = queries[i].toLowerCase();
							for (var j = 0; j < words.length; j++) {
								var word = words[j].toLowerCase();
								if (word.substr(0, query.length) == query) {
									return true;
								}
							}
						}

						return false;
					}
				}
			}
		);

		var GetSelectedPoints = function(instance, node_id) {
			var node_text = instance.get_node(node_id).text;
			if (DATE_PATTERN.test(node_text)) {
				return '';
			}

			var text = node_text + (!instance.is_leaf(node_id) ? ', ' : '');
			while (true) {
				var parent_id = instance.get_parent(node_id);
				if (parent_id == '#') {
					break;
				}

				var node_text = instance.get_node(parent_id).text;
				if (!DATE_PATTERN.test(node_text)) {
					text = node_text + ', ' + text;
				}

				node_id = parent_id;
			}

			return text;
		};

		var selected_points_text_view = $('.selected-points-text-view');
		// http://stackoverflow.com/a/5797700/3884331
		selected_points_text_view.focus(
			function() {
				var self = $(this);
				self.select();

				// work around Chrome's little problem
				self.mouseup(
					function() {
						// prevent further mouseup intervention
						self.unbind("mouseup");
						return false;
					}
				);
			}
		);

		tree.on(
			'select_node.jstree',
			function (event, data) {
				var points_text = GetSelectedPoints(
					data.instance,
					data.selected
				);
				if (points_text) {
					selected_points_text_view.val(points_text);
				}

				data.instance.deselect_node(data.selected, true);
			}
		);

		search_form.on(
			'submit',
			function (event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);

		var search_timer = null;
		var search_input = $('.search-input', search_form);
		search_input.keyup(
			function () {
				clearTimeout(search_timer);

				var self = $(this);
				search_timer = setTimeout(
					function() {
						var query = self.val();
						tree.jstree(true).search(query);
					},
					SEARCH_DELAY
				);
			}
		);

		$('.clean-button', search_form).click(
			function() {
				search_input.val('');
				search_input.focus();
				search_input.keyup();
			}
		);
	}
);
