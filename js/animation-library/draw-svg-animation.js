/* usage
 * drawPath({
        id: 'redBox', set the element by id: 'id' or el: index[i] (required, string)
        delay: 0, set the delay in milliseconds (optional/defalut 1000, number)
        fps: 120, frames per second [the loop is build with requestAnimationFrame] (optional/defalut 120, number)
        calcStep: 1, calcStep is the count value (optional/defalut 1, number)
        direction: 'forwards', draw the path forwards or backwards (optional/defalut forwards, string)
 * });
*/
var drawPath = function (o) {
	var target = gid(o.id) || gcl(o.cl) || o.el,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		fps = o.fps || 120,
		calcStep = o.calcStep || 1,
		direction = o.direction || 'forwards';

	if (o.id || o.el) {
		length = Math.ceil(target.getTotalLength());
		target.setAttribute('stroke-dasharray', length);
		if (direction === 'forwards') {
			target.setAttribute('stroke-dashoffset', length + 0.005);
		}
	} else {
		for (var i = 0; i < target.length; i++) {
			length = Math.ceil(target[i].getTotalLength());
			target[i].setAttribute('stroke-dasharray', length);
			if (direction === 'forwards'){
				target[i].setAttribute('stroke-dashoffset', length + 0.005);
			}
		}
	}

	function buildInterval(elem, elemLength, interval) {
		var count = animationInterval( function() {

			elemLength = direction === 'forwards' ? elemLength - interval : elemLength + interval;

			if (elemLength <=0) {
				elemLength = 0;
			}
			if (elemLength >= Math.ceil(elem.getTotalLength())){
				elemLength = Math.ceil(elem.getTotalLength()) + 0.005;
			}

			elem.setAttribute('stroke-dashoffset', elemLength);

			if (elemLength <= 0 || elemLength >= Math.ceil(elem.getTotalLength())) {
				if( !window.cancelAnimationFrame ) {
					clearAnimationInterval(count);
				}
				return true;
			}
			return false;
		}, fixValue(1000 / fps, 0));
	}

	return animationTimeout(function () {

		if (o.id || o.el) {

			var length = parseInt(target.getAttribute('stroke-dashoffset')), pathLength;

			pathLength = direction === 'forwards' ? length : 0;

			buildInterval(target, pathLength, calcStep);

		} else {

			var objects = Array();

			for (var i = 0; i < target.length; i++) {

				objects[i] = {};

				objects[i].length = parseInt(target[i].getAttribute('stroke-dashoffset'));

				objects[i].pathLength = direction === 'forwards' ? objects[i].length : 0;

				buildInterval(target[i], objects[i].pathLength, calcStep);

			}

		}
	}, delay);
};
