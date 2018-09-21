(function() {
	/* make sure that all CSS animation, setInterval and setTimeout be paused while the Tab isn't visible */
	var checkVisibility = false,
		allSetTimeouts = [],
		allAnimationTimeouts = [],
		timestamp = ( !window.performance.now ) ? new Date().getTime() : performance.now();

	if (document.hasFocus()) {
		checkVisibility = true;
	} else {
		if (!document.hidden) {
			checkVisibility = true;
		}
	}

	window.newSetTimeout = window.setTimeout;
	window.newSetInterval = window.setInterval;

	window.setTimeout = function(func, delay) {

		var timeoutObj = {};
			timeoutObj.name = 'setTimeout';
			timeoutObj.delay = delay;
			timeoutObj.triggered = ( !window.performance.now ) ? new Date().getTime() : performance.now();
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
			request = {},
			done = false;

		function loop() {
			var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
				difference = currentTime - startTime;

			if( difference >= interval && checkVisibility === true) {
				done = func.call();
				startTime = currentTime - ( difference % interval );
			}

			request.id = requestAnimationFrame(loop);

			if (done === true) {
				window.clearAnimationInterval(request.id);
			}
		}
		request.id = requestAnimationFrame(loop);
		return request;
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
			request = {},
			timeoutObj = {};

		function loop() {
			var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now(),
				difference = currentTime - startTime;
			if ( difference >= delay && checkVisibility === true ) {
				func.call();
			} else {
				request.id = requestAnimationFrame(loop);
				timeoutObj.id = request.id;
			}
		}

		request.id = requestAnimationFrame(loop);

		timeoutObj.name = 'animationTimeout';
		timeoutObj.delay = delay;
		timeoutObj.triggered = ( !window.performance.now ) ? new Date().getTime() : performance.now();
		timeoutObj.func = func;
		timeoutObj.id = request.id;

		allAnimationTimeouts.push(timeoutObj);

		if (checkVisibility === false) {
			clearAllTimeouts(allAnimationTimeouts);
		}

		return request;
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

	var cleanArray = function (arr, breaktimestamp) {
		var length = arr.length, newArr = [];
		while (length--) {
			if (arr[length].delay >= (breaktimestamp - arr[length].triggered)) {
				newArr.push(arr[length]);
			}
			if (length <= 0) {
				return newArr;
			}
		}
	};

	var buildTimeout = function(timeouts, breaktimestamp) {
		var length = timeouts.length;
		while (length--) {
			if (timeouts[length].name === 'setTimeout') {
				newSetTimeout(timeouts[length].func, (timeouts[length].delay - (breaktimestamp - timeouts[length].triggered)));
			} else {
				animationTimeout(timeouts[length].func, (timeouts[length].delay - (breaktimestamp - timeouts[length].triggered)));
			}
		}
	};

	var allAniSelectors = [];
	document.addEventListener('DOMContentLoaded', function() {
		for (var i = 0; i < document.styleSheets.length; i++) {
			try {
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
			catch(err) {
				console.warn('The cssRules object is empty (document.styleSheets.cssRules). ' + err + ' ' + document.styleSheets[i].ownerNode.outerHTML);
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

	var breaktimestamp = 0;
	function visibility(visibility) {
		var newAniArr = [], newTimeArr = [];
		if (visibility === 'visible') {
			if (allSetTimeouts.length > 0 && breaktimestamp > 0) {
				newTimeArr = cleanArray(allSetTimeouts, breaktimestamp);
			}
			if (allAnimationTimeouts.length > 0 && breaktimestamp > 0) {
				newAniArr = cleanArray(allAnimationTimeouts, breaktimestamp);
			}

			if (newTimeArr.length > 0) {
				allSetTimeouts = [];
				buildTimeout(newTimeArr, breaktimestamp);
			}
			if (breaktimestamp === 0) {
				buildTimeout(allSetTimeouts, breaktimestamp);
			}
			if (newAniArr.length > 0) {
				allAnimationTimeouts = [];
				buildTimeout(newAniArr, breaktimestamp);
			}
			if (breaktimestamp === 0) {
				buildTimeout(allAnimationTimeouts, breaktimestamp);
			}

			setAnimationPlayState('running');
		} else {

			setAnimationPlayState('paused');
			breaktimestamp = ( !window.performance.now ) ? new Date().getTime() : performance.now();

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
/* bugfix Chrome | visibilitychange
window.addEventListener('focus', function() {
	checkVisibility = true;
	visibility('visible');
}, false);
window.addEventListener('blur', function() {
	checkVisibility = false;
	visibility('hidden');
}, false);
*/
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
