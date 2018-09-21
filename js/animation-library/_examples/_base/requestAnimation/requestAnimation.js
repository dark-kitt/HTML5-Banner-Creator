/**
* Set vendorprefixes
*/
window.requestAnimationFrame = (function() {
	return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame;
})();
window.cancelAnimationFrame = (function() {
	return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame;
})();
window.performance.now = (function() {
	return window.performance.now || window.performance.webkitNow;
})();

/**
* Create custom interval function with requestAnimationFrame.
* While creating your function return always false in the end.
*
* If you want to clear the interval in your function return true and
* call the clearAnimationInterval(interval) before, for older Browsers.
* e.g.
* if (animationDone === true) {
* 	if( !window.cancelAnimationFrame ) {
* 		clearAnimationInterval(interval);
* 	}
* 	return true;
* }
*/
window.animationInterval = function(func, interval) {

	if( !window.requestAnimationFrame ) {
		return window.setInterval(func, interval);
	}

	var startTime = ( !window.performance.now ) ? new Date().getTime() : performance.now();
	var request = {},
		done;

	function loop() {
		var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now();
		var difference = currentTime - startTime;

		if( difference >= interval ) {
			done = func.call();
			startTime = currentTime - ( difference % interval );
		}

		request.value = requestAnimationFrame(loop);

		if (done === true) {
			window.clearAnimationInterval(request);
		}
	}
	request.value = requestAnimationFrame(loop);
	return request;
};

/**
* Clear the animationInterval function
*/
window.clearAnimationInterval = function(clear) {
	if( !window.cancelAnimationFrame ) {
		clearInterval(clear);
	} else {
		window.cancelAnimationFrame(clear.value);
	}
};


/**
 * Create custom timeout function with animationFrame
 */
window.animationTimeout = function(func, delay) {
	if( !window.requestAnimationFrame ) {
			return window.setTimeout(func, delay);
		}

	var startTime = ( !window.performance.now ) ? new Date().getTime() : performance.now();
	var request = {};

	function loop() {
		var currentTime = ( !window.performance.now ) ? new Date().getTime() : performance.now();
		var difference = currentTime - startTime;
		if ( difference >= delay ) {
			func.call();
		} else {
			request.value = requestAnimationFrame(loop);
		}
	}

	request.value = requestAnimationFrame(loop);
	return request;
};

/**
 * Clear the animationTimeout function
 */
window.clearAnimationTimeout = function(clear) {
	if( !window.cancelAnimationFrame ) {
		return clearTimeout(clear);
	}
	window.cancelAnimationFrame(clear.value);
};
