var GetPointUnit = function(number) {
	var unit = '';
	var modulo = number % 10;
	if (modulo == 1 && (number < 10 || number > 20)) {
		unit = 'пункт';
	} else if (modulo > 1 && modulo < 5 && (number < 10 || number > 20)) {
		unit = 'пункта';
	} else {
		unit = 'пунктов';
	}

	return unit;
};
