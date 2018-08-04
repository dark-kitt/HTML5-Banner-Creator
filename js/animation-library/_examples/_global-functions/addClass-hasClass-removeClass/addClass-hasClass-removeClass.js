var getClassName = function(cls) {
	return new RegExp('(?:\\s+|^)(' + cls + '\\b)(?:\\s+|$)');
},
hasClass = function(e, cls) {
	if (e instanceof SVGElement) {
		return e.className.baseVal.match(getClassName(cls)) === null ? false : true;
	} else {
		return e.className.match(getClassName(cls)) === null ? false : true;
	}
},
removeClass = function(e, cls) {
	if (hasClass(e, cls)) {
		if (e instanceof SVGElement) {
			e.className.baseVal = e.className.baseVal.replace(/\s+/g, ' ');
			if (e.className.match('(?:\\w|\\d)(?:\\s+|^)(' + cls + '\\b)(?:\\s+|$)(?:\\w|\\d)') === null) {
				e.className.baseVal = e.className.baseVal.replace(getClassName(cls), '');
			} else {
				e.className.baseVal = e.className.baseVal.replace(getClassName(cls), ' ');
			}
		} else {
			e.className = e.className.replace(/\s+/g, ' ');
			if (e.className.match('(?:\\w|\\d)(?:\\s+|^)(' + cls + '\\b)(?:\\s+|$)(?:\\w|\\d)') === null) {
				e.className = e.className.replace(getClassName(cls), '');
			} else {
				e.className = e.className.replace(getClassName(cls), ' ');
			}
		}
	}
},
addClass = function(e, o) {
if (!o.remove) {
	o.remove = null;
}
if (e instanceof SVGElement) {
	e.className.baseVal = e.className.baseVal.replace(/\s+/g, ' ');
	if (!this.hasClass(e, o.class)) {
		if (o.remove !== null) {
			e.className.baseVal += ' ' + o.class;
			setTimeout(function() {
				removeClass(e, o.class);
			}, o.remove);
		} else {
			e.className.baseVal += ' ' + o.class;
		}
	}
} else {
	if (!this.hasClass(e, o.class)) {
		e.className = e.className.replace(/\s+/g, ' ');
		if (o.remove !== null) {
			e.className += ' ' + o.class;
			setTimeout(function() {
				removeClass(e, o.class);
			}, o.remove);
		} else {
			e.className += ' ' + o.class;
		}
	}
}
};
