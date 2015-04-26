$(document).ready(
	function() {
		var SEARCH_DELAY = 500;

		var tree = $('.project-list').jstree(
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
		tree.on(
			'select_node.jstree',
			function (event, data) {
				data.instance.deselect_node(data.selected, true);
			}
		);

		$('.search-form').on(
			'submit',
			function (event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);

		var search_timer = null;
		var search_input = $('.search-input');
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

		$('.clean-button').click(
			function() {
				search_input.val('');
				search_input.focus();
				search_input.keyup();
			}
		);
	}
);
