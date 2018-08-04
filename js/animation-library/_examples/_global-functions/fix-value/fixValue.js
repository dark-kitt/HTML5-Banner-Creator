function fixValue(number, places) {
	var place;

	if (!places) {
		place = 2;
	} else {
		place = places;
	}
	number = parseFloat(number);
	number = parseFloat(number.toFixed(place));

	return number;
}
