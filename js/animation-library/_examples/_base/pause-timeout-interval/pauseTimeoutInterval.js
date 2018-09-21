document.visibilityState = (function() {
	return document.visibilityState ||  document.mozVisibilityState || document.webkitVisibilityState;
})();

(function() {
	window.newSetTimeout = window.setTimeout;
	window.newSetInterval = window.setInterval;

	var checkVisibility = true,
		allSetTimeouts = [],
		timestamp = new Date().getTime();

	window.setTimeout = function(func, delay) {

		var timeoutObj = {};
			timeoutObj.delay = delay;
			timeoutObj.called = new Date().getTime() - timestamp;
			timeoutObj.func = func;
			timeoutObj.id = newSetTimeout(func, delay);

		allSetTimeouts.push(timeoutObj);

		if (checkVisibility === false) {
			clearAllTimeouts(allSetTimeouts);
		}
	};

	window.setInterval = function(func, timer) {
		var start, loop = function() {
			if (checkVisibility) {
				if (start === true) {
					func.call();
				}
				start = true;
			} else {
				start = false;
			}
		};
		return newSetInterval(loop, timer);
	};

	var clearAllTimeouts = function(timeouts) {
		var count = timeouts.length;
		while (count--) {
			clearTimeout(timeouts[count].id);
		}
		if (length <= 0) {
			return true;
		}
	};

	var buildTimeout = function(timeouts) {
		var a, b, length = timeouts.length;
		while (length--) {
			a = timeouts[length].delay;
			b = timeouts[length].called;
			if (a + b < runtime && a + b !== runtime) {
				timeouts.splice(length, 1);
			} else {
				timeouts[length].id = newSetTimeout(timeouts[length].func, timeouts[length].delay = a + b - runtime);
			}
		}
		if (length <= 0) {
			return true;
		}
	};

	var breaktimestamp,
		runtime,
		done;
	function visibility(visibility) {
		if (visibility === 'visible') {
			runtime = breaktimestamp - timestamp;
			done = buildTimeout(allSetTimeouts, runtime);

			if (done) {
				timestamp = new Date().getTime();
			}
		} else {
			breaktimestamp = new Date().getTime();

			if(document.readyState === 'complete') {
				clearAllTimeouts(allSetTimeouts);
			}
		}
	}

	var evt,
		vendorprefixes = {
			mozHidden: 'mozvisibilitychange',
			webkitHidden: 'webkitvisibilitychange',
			hidden: 'visibilitychange'
	};
	function buildVisibilitychange(evt) {
		document.addEventListener(vendorprefixes[evt], function() {
			if (document.visibilityState === 'visible') {
				visibility('visible');
				checkVisibility = true;
			} else {
				visibility('hidden');
				checkVisibility = false;
			}
		});
	}

	if (document.visibilityState) {
		// Standards:
		for (evt in vendorprefixes) {
			if (evt in document) {
				buildVisibilitychange(evt);
				break;
			}
		}
	} else if (/(iPad|iPhone|iPod)/g.test(navigator.userAgent)) {
		// for IOS:
		document.onpageshow = function() {
			if (!checkVisibility) {
				visibility('visible');
				checkVisibility = true;
			}
		};
		document.onpagehide = function() {
			visibility('hidden');
			checkVisibility = false;
		};
	} else if (document.documentMode !== undefined) {
		// IE 9 and lower:
			document.onfocusin = function() {
				if (!checkVisibility) {
					visibility('visible');
					checkVisibility = true;
				}
			};
			document.onfocusout = function() {
				visibility('hidden');
				checkVisibility = false;
			};
	} else {
		//  All others
		document.onfocus = function() {
			if (!checkVisibility) {
				visibility('visible');
				checkVisibility = true;
			}
		};
		document.onblur = function() {
			visibility('hidden');
			checkVisibility = false;
		};
	}
})();
