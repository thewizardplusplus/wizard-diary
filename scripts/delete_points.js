$(document).ready(
	function() {
		var SEARCH_DELAY = 500;

		var points_ids = [];
		var points_dates = [];

		var search_points_form = $('.search-points-form');
		search_points_form.on(
			'submit',
			function(event) {
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		);

		var points_found_empty_view = $('.points-found-empty-view');
		var points_found_controls_view = $('.points-found-controls-view');
		var quantity_label_plural_form = $('.quantity-label-plural-form');
		var points_quantity_view = $('.points-quantity-view');
		var days_quantity_view = $('.days-quantity-view');
		var points_found_view = $('.points-found-view');
		var GetPointsIds = function(points) {
			var points_ids = [];
			for (var i = 0; i < points.length; i++) {
				points_ids.push(points[i].id);
			}

			return points_ids;
		};
		var GetPointsDates = function(points) {
			var points_dates = [];
			for (var i = 0; i < points.length; i++) {
				points_dates.push(points[i].date);
			}

			return $.unique(points_dates);
		};
		var GetDayUnit = function(number) {
			var unit = '';
			if (number % 10 == 1 && (number < 10 || number > 20)) {
				unit = 'дне';
			} else {
				unit = 'днях';
			}

			return unit;
		};
		var PreparePointsData = function(points, search_options) {
			var marked_query = `<mark class="bg-success">${search_options.query}</mark>`;

			var points_data = {};
			for (var i = 0; i < points.length; i++) {
				var point = points[i];
				if (!points_data.hasOwnProperty(point.date)) {
					points_data[point.date] = {
						text: point.my_date,
						icon: 'glyphicon glyphicon-folder-open',
						state: {opened: true},
						children: []
					};
				}

				points_data[point.date].children.push(
					{
						text: search_options.search_from_beginning
							? point.text.replace(search_options.query, marked_query)
							: point.text.replaceAll(search_options.query, marked_query),
						icon: point.daily
							? 'glyphicon glyphicon-calendar'
							: 'glyphicon glyphicon-file'
					}
				);
			}

			return $.map(
				points_data,
				function(point) {
					return point;
				}
			);
		};
		var ProcessPoints = function(points, search_options) {
			var points_quantity = points.length;
			if (points_quantity == 0) {
				points_ids = [];
				points_dates = [];

				points_found_empty_view.show();

				points_found_controls_view.hide();
				points_found_view.hide();
			} else {
				points_ids = GetPointsIds(points);
				points_dates = GetPointsDates(points);

				points_found_empty_view.hide();

				points_found_controls_view.show();
				quantity_label_plural_form
					.toggle((points_quantity % 10) != 1 || points_quantity == 11);
				var points_unit = GetPointUnit(points_quantity);
				points_quantity_view.text(points_quantity + ' ' + points_unit);
				var days_unit = GetDayUnit(points_dates.length);
				days_quantity_view.text(points_dates.length + ' ' + days_unit);

				points_found_view.show();
				points_found_view.jstree('destroy');
				var points_data = PreparePointsData(points, search_options);
				points_found_view.jstree(
					{
						core: {
							data: points_data,
							themes: {stripes: true, responsive: true},
							strings: {'Loading ...': 'Загрузка...'}
						}
					}
				).on(
					'select_node.jstree',
					function(event, data) {
						data.instance.deselect_node(data.selected, true);
					}
				);
			}
		};

		var find_url = search_points_form.data('find-url');
		var FindPosts = function(search_options) {
			if (search_options.query.length == 0) {
				points_found_empty_view.hide();

				points_found_controls_view.hide();
				points_found_view.hide();

				return;
			}

			search_points_form.addClass('loading');

			$.get(
				find_url,
				search_options,
				function(points) {
					search_points_form.removeClass('loading');
					ProcessPoints(points, search_options);
				},
				'json'
			).fail(AjaxErrorDialog.handler);
		};

		var search_input = $('.search-input', search_points_form);
		var ForceSearch = function() {
			search_input.focus();
			search_input.keyup();
		};
		var search_from_beginning_checkbox =
			$('.search-from-beginning', search_points_form);
		search_from_beginning_checkbox.change(ForceSearch);
		var search_timer = null;
		search_input.keyup(
			function() {
				clearTimeout(search_timer);

				var self = $(this);
				search_timer = setTimeout(
					function() {
						var query = self.val().trim();
						var search_from_beginning =
							search_from_beginning_checkbox.prop('checked');
						FindPosts({query, search_from_beginning});
					},
					SEARCH_DELAY
				);
			}
		);

		var search_input_cleaning_button =
			$('.search-input-cleaning-button', search_points_form);
		search_input_cleaning_button.click(
			function() {
				search_input.val('');
				ForceSearch();
			}
		);

		var delete_points_form = $('.delete-points-form');
		var AddDataToForm = function(form, data_name, data) {
			form.append(
				'<input '
					+ 'type = "hidden" '
					+ 'name = "' + data_name + '" '
					+ 'value = "' + data + '" />'
			);
		};
		var AddArrayDataToForm = function(form, data_name, data) {
			for (var i = 0; i < data.length; i++) {
				AddDataToForm(form, data_name + '[]', data[i]);
			}
		};
		$('.delete-button', delete_points_form).click(
			function() {
				DeletePointsDialog.show(
					function() {
						AddArrayDataToForm(delete_points_form, 'points_ids', points_ids);
						AddArrayDataToForm(delete_points_form, 'points_dates', points_dates);

						delete_points_form.submit();
					}
				);

				return false;
			}
		);
	}
);
