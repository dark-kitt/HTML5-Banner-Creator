var loc = location.href.split('#')[0],
	allUseElements = gtn('use'),
	allPathElements = gtn('path'),
	allGradientElements = gtn('linearGradient'),
	allElements = gtn('*'),
	attr_old = '',
	attr_new = '';

for (var i = 0; i < allUseElements.length; i++) {
	if (allUseElements[i].getAttribute('xlink:href') !== null) {
		attr_old = allUseElements[i].getAttribute('xlink:href');
		attr_new = loc + attr_old;
		allUseElements[i].setAttribute('xlink:href', attr_new);
	}
}

for (var j = 0; j < allPathElements.length; j++) {
	if (allPathElements[j].getAttribute('fill') !== null && allPathElements[j].getAttribute('fill').split('(')[0] === 'url') {
		attr_old = allPathElements[j].getAttribute('fill');
		attr_new = attr_old.split('#')[0] + loc + '#' + attr_old.split('#')[1];
		allPathElements[j].setAttribute('fill', attr_new);
	}
}

for (var k = 0; k < allGradientElements.length; k++) {
	if (allGradientElements[k].getAttribute('xlink:href') !== null) {
		attr_old = allGradientElements[k].getAttribute('xlink:href');
		attr_new = loc + attr_old;
		allGradientElements[k].setAttribute('xlink:href', attr_new);
	}
}

for (var l = 0; l < allElements.length; l++) {
	if (allElements[l].getAttribute('mask') !== null) {
		attr_old = allElements[l].getAttribute('mask');
		attr_new = attr_old.split('#')[0] + loc + '#' + attr_old.split('#')[1];
		allElements[l].setAttribute('mask', attr_new);
	}
	if (allElements[l].getAttribute('filter') !== null) {
		attr_old = allElements[l].getAttribute('filter');
		attr_new = attr_old.split('#')[0] + loc + '#' + attr_old.split('#')[1];
		allElements[l].setAttribute('filter', attr_new);
	}
	if (allElements[l].getAttribute('clip-path') !== null) {
		attr_old = allElements[l].getAttribute('clip-path');
		attr_new = attr_old.split('#')[0] + loc + '#' + attr_old.split('#')[1];
		allElements[l].setAttribute('clip-path', attr_new);
	}
}