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
pixelRatio = (function() {
    var canvas = document.createElement('canvas').getContext('2d'),
        dpr = window.devicePixelRatio || 1,
        bsr = canvas.webkitBackingStorePixelRatio ||
        canvas.mozBackingStorePixelRatio ||
        canvas.msBackingStorePixelRatio ||
        canvas.oBackingStorePixelRatio ||
        canvas.backingStorePixelRatio || 1;
    return dpr / bsr;
})(),
/* create hiDPI canvas / if canvas exists update it (onresize)*/
hiDPICanvas = function(id, width, height) {
    var canvas;
    if (document.getElementById(id)) {
        canvas = document.getElementById(id);
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    } else {
        canvas = document.createElement('canvas');
        if (canvas !== null) {
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            canvas.width = width * pixelRatio;
            canvas.height = height * pixelRatio;
            canvas.setAttribute('id', id);
            canvas.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
            return canvas;
        }
    }
},
clearCanvas = function(can) {
    ctx = can.getContext('2d');
    ctx.clearRect(0, 0, can.width, can.height);
},
startAnimation = function(can, ctx, objs, loop) {
    return window.requestAnimationFrame(function() {
        loop(can, ctx, objs);
    });
},
stopAnimation = function(req) {
    window.cancelAnimationFrame(req);
},
fixValue = function(num, places) {
	var place = places || (places === 0 ? 0 : 2);
	return parseFloat(num.toFixed(place));
},
randomInt = function(min, max) {
    return Math.round(Math.random() * (max - min) + min);
},
randomRGBA = function(min, max, alpha) {
    return 'rgba(' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + alpha + ')';
},
canvasAnimations = {};
