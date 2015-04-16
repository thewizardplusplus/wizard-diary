$(document).ready(
	function() {
		var STATS_DATA_KEYS = Object.keys(STATS_DATA);
		var STATS_DATA_VALUES = STATS_DATA_KEYS.map(
			function(key) {
				return STATS_DATA[key];
			}
		);

		var data = {
			labels: STATS_DATA_KEYS,
			datasets: [
				{
					data: STATS_DATA_VALUES,
					fillColor: 'rgba(0, 0, 0, 0)',
					strokeColor: '#5cb85c',
					pointColor: '#5cb85c'
				}
			]
		};

		var stats_view = $('.stats-view');
		var stats_view_context = stats_view.get(0).getContext('2d');
		var chart =
			new Chart(stats_view_context)
			.Line(data, {responsive: true});
	}
);
