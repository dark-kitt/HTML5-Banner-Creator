/* define custom getElement/s */
var gid = function (id) {
	return id ? document.getElementById(id) : false;
},
gcl = function (cl) {
	return cl ? document.getElementsByClassName(cl) : false;
},
gtn = function (tn) {
	return tn ? document.getElementsByTagName(tn) : false;
},
/* define custom hasClass, removeClass and addClass */
getClassName = function(cls) {
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
},
/* global fix value function to specific decimal place */
fixValue = function(number, places) {
	var place = places || (places === 0 ? 0 : 2);
	number = parseFloat(parseFloat(number).toFixed(place));

	return number;
};
