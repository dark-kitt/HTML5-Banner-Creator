(function() {
	/* make sure that all CSS animation, setInterval and setTimeout be paused while the Tab isn't visible */
	var checkVisibility = true,
		allSetTimeouts = [],
		allAnimationTimeouts = [],
		timeStamp = ( !window.performance.now ) ? new Date().getTime() : performance.now();

	window.newSetTimeout = window.setTimeout;
	window.newSetInterval = window.setInterval;

	window.setTimeout = function(func, delay) {

		var timeoutValues = {};
			timeoutValues.name = 'setTimeout';
			timeoutValues.delay = delay;
			timeoutValues.triggered = ( !window.performance.now ) ? new Date().getTime() : performance.now();
			timeoutValues.func = func;
			timeoutValues.id = newSetTimeout(func, delay);

		allSetTimeouts.push(timeoutValues);

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

	/* define requestAnimationFrame vendorprefixes */
	window.requestAnimationFrame = (function() {
		return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame;
	})();
	window.cancelAnimationFrame = (function() {
		return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame;
	})();
	/* define visibilityState vendorprefixes */
	document.visibilityState = (function() {
		return document.visibilityState ||  document.mozVisibilityState || document.webkitVisibilityState;
	})();
	/* define performance.now vendorprefixes */
	window.performance.now = (function() {
		return window.performance.now || window.performance.webkitNow;
	})();

	/* define custom setInterval function with the original setInterval as fallback */
	window.animationInterval = function(func, interval) {

		if( !window.requestAnimationFrame ) {
			return window.setInterval(func, interval);
		}

		var startTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
			requestID = {},
			done = false;

		function loop() {
			var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
				difference = currentTime - startTime;

			if( difference >= interval && checkVisibility === true) {
				done = func.call();
				startTime = currentTime - ( difference % interval );
			}

			requestID.id = requestAnimationFrame(loop);

			if (done === true) {
				window.clearAnimationInterval(requestID.id);
			}
		}
		requestID.id = requestAnimationFrame(loop);
		return requestID;
	};
	/* define custom clearInterval function with the original clearInterval as fallback */
	window.clearAnimationInterval = function(clear) {
		if( !window.cancelAnimationFrame ) {
			clearInterval(clear);
		} else {
			window.cancelAnimationFrame(clear);
		}
	};
	/* define custom setTimeout function with the original setTimeout as fallback */
	window.animationTimeout = function(func, delay) {

		if( !window.requestAnimationFrame ) {
			return window.setTimeout(func, delay);
		}

		var startTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
			requestID = {},
			timeoutValues = {};

		function loop() {
			var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
				difference = currentTime - startTime;
			if ( difference >= delay && checkVisibility === true ) {
				func.call();
			} else {
				requestID.id = requestAnimationFrame(loop);
				timeoutValues.id = requestID.id;
			}
		}

		requestID.id = requestAnimationFrame(loop);

		timeoutValues.name = 'animationTimeout';
		timeoutValues.delay = delay;
		timeoutValues.triggered = ( !window.performance.now ) ? new Date().getTime() : performance.now();
		timeoutValues.func = func;
		timeoutValues.id = requestID.id;

		allAnimationTimeouts.push(timeoutValues);

		if (checkVisibility === false) {
			clearAllTimeouts(allAnimationTimeouts);
		}

		return requestID;
	};
	/* define custom clearTimeout function with the original clearTimeout as fallback */
	window.clearAnimationTimeout = function(clear) {
		if( !window.cancelAnimationFrame ) {
			return clearTimeout(clear);
		}
		window.cancelAnimationFrame(clear);
	};

	var clearAllTimeouts = function(timeouts) {
		var length = timeouts.length;
		while (length--) {
			if (timeouts[length].name === 'setTimeout') {
				clearTimeout(timeouts[length].id);
			} else {
				clearAnimationTimeout(timeouts[length].id);
			}
		}
	};

	var cleanArray = function (arr, breakTimeStamp) {
		var length = arr.length, newArr = [];
		while (length--) {
			if (arr[length].delay >= (breakTimeStamp - arr[length].triggered)) {
				newArr.push(arr[length]);
			}
			if (length <= 0) {
				return newArr;
			}
		}
	};

	var buildTimeout = function(timeouts, breakTimeStamp) {
		var length = timeouts.length;
		while (length--) {
			if (timeouts[length].name === 'setTimeout') {
				newSetTimeout(timeouts[length].func, (timeouts[length].delay - (breakTimeStamp - timeouts[length].triggered)));
			} else {
				animationTimeout(timeouts[length].func, (timeouts[length].delay - (breakTimeStamp - timeouts[length].triggered)));
			}
		}
	};

	var allAniSelectors = [];
	document.addEventListener('DOMContentLoaded', function() {
		for (var i = 0; i < document.styleSheets.length; i++) {
			if (document.styleSheets[i].cssRules !== null) {
				if (document.styleSheets[i].cssRules.length > 0) {
					for (var j = 0; j < document.styleSheets[i].cssRules.length; j++) {
						if (document.styleSheets[i].cssRules[j].cssText.match(/\banimation(\s+|):/g)) {
							var selector = document.styleSheets[i].cssRules[j].cssText.match(/(.*?)(\s+|)(\{)/g);
								allAniSelectors.push(selector[0].replace(/\s+\{/g, ''));
						}
					}
				}
			}
		}
	}, false);

	var setAnimationPlayState = function(value) {
		if (allAniSelectors.length > 0) {
			var length = allAniSelectors.length;
			while (length--) {
				if (allAniSelectors[length].match(/\#/g)) {
					document.getElementById(allAniSelectors[length].replace(/\#/g, '')).style.animationPlayState = value;
				} else if ((allAniSelectors[length].match(/\./g))) {
					var elemClass = document.getElementsByClassName(allAniSelectors[length].replace(/\./g, ''));
					for (var i = 0; i < elemClass.length; i++) {
						elemClass[i].style.animationPlayState = value;
					}
				} else {
					var elemTag = document.getElementsByTagName(allAniSelectors[length]);
					for (var j = 0; j < elemTag.length; j++) {
						elemTag[j].style.animationPlayState = value;
					}
				}
			}
		}
	};

	var breakTimeStamp = 0;
	function visibility(visibility) {
		var newAniArr = [], newTimeArr = [];
		if (visibility === 'visible') {
			if (typeof allSetTimeouts !== 'undefined') {
				newTimeArr = cleanArray(allSetTimeouts, breakTimeStamp);
			}
			if (typeof allAnimationTimeouts !== 'undefined') {
				newAniArr = cleanArray(allAnimationTimeouts, breakTimeStamp);
			}

			if (typeof newTimeArr !== 'undefined') {
				allSetTimeouts = [];
				buildTimeout(newTimeArr, breakTimeStamp);
			}
			if (typeof newAniArr !== 'undefined') {
				allAnimationTimeouts = [];
				buildTimeout(newAniArr, breakTimeStamp);
			}

			setAnimationPlayState('running');
		} else {

			setAnimationPlayState('paused');
			breakTimeStamp = ( !window.performance.now ) ? new Date().getTime() : performance.now();

			if(document.readyState === 'complete') {
				clearAllTimeouts(allSetTimeouts);
				clearAllTimeouts(allAnimationTimeouts);
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
				checkVisibility = true;
				visibility('visible');
			} else {
				checkVisibility = false;
				visibility('hidden');
			}
		}, false);
		/* bugfix Chrome for visibilitychange when you change the window/programm */
		window.addEventListener('focus', function() {
			checkVisibility = true;
			visibility('visible');
		}, false);
		window.addEventListener('blur', function() {
			checkVisibility = false;
			visibility('hidden');
		}, false);
		/* bugfix Chrome for visibilitychange when you change the window/programm */
	}
	if (document.visibilityState) {
		for (evt in vendorprefixes) {
			if (evt in document) {
				buildVisibilitychange(evt);
				break;
			}
		}
	} else if (/(iPad|iPhone|iPod)/g.test(navigator.userAgent)) {
		document.onpageshow = function() {
			if (!checkVisibility) {
				checkVisibility = true;
				visibility('visible');
			}
		};
		document.onpagehide = function() {
			checkVisibility = false;
			visibility('hidden');
		};
	} else if (document.documentMode !== undefined) {
			document.onfocusin = function() {
				if (!checkVisibility) {
					checkVisibility = true;
					visibility('visible');
				}
			};
			document.onfocusout = function() {
				checkVisibility = false;
				visibility('hidden');
			};
	} else {
		document.onfocus = function() {
			if (!checkVisibility) {
				checkVisibility = true;
				visibility('visible');
			}
		};
		document.onblur = function() {
			checkVisibility = false;
			visibility('hidden');
		};
	}
})();
