$(document).ready(
	function() {
		var data = {
			labels: ['one', 'two', 'three', 'four', 'five'],
			datasets: [
				{
					data: [
						Math.random(),
						Math.random(),
						Math.random(),
						Math.random(),
						Math.random()
					],
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
