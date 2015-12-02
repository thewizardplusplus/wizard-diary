self.onmessage = function(event) {
	self.postMessage({type: 'message', data: 'start'});
	var typo = new event.data.typo(
		'ru_RU',
		event.data.aff_data,
		event.data.dic_data
	);
	self.postMessage({type: 'message', data: 'end'});
	self.postMessage({type: 'result', data: typo});
};
