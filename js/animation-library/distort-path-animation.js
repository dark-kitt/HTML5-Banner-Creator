/* usage
 * distortPath({
        id: 'redBox', set the element by id: 'id' or el: index[i] (required, string)
        delay: 0, set the delay in milliseconds (optional/defalut 1000, number)
        fps: 120, frames per second [the loop is build with requestAnimationFrame] (optional/defalut 120, number)
        loop: 5, how many loops (optional/defalut 1, number)
        calcStep: 1, calcStep is the count value (optional/defalut 1, number)
        newPath: 'M3.58,26.48c151-136,274,172,463,0', is the new path for the element (required, string)
 * });
*/
var distortPath = function (o) {
	var target = gid(o.id) || o.el,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		fps = o.fps || 120,
		calcStep = o.calcStep || 1,
		newPath = o.newPath || gid(o.id).getAttribute('d'),
		loops = o.loops || 1,
		infinite = o.infinite || false,
		d = target.getAttribute('d'),
		endPathArr = newPath.match(/[A-Za-z]+|-?[\d+\.\d?]+/g),
		startPathArr = d.match(/[A-Za-z]+|-?[\d+\.\d?]+/g),
		loopCount = 0,
		currentVal;

	return animationTimeout(function () {
		var interval = animationInterval(function () {
			var currentD = target.getAttribute('d'),
				currentPath = currentD.match(/[A-Za-z]+|-?[\d+\.\d?]+/g);

			for (var i = 0; i < endPathArr.length; i++) {
				if (!isNaN(endPathArr[i] * 1) && !isNaN(currentPath[i] * 1)) {
					if (parseFloat(endPathArr[i]) > parseFloat(currentPath[i])) {
						if ((parseFloat(currentPath[i]) + calcStep) > parseFloat(endPathArr[i])) {
							currentVal = fixValue(endPathArr[i]);
							currentPath[i] = currentVal;
						} else {
							currentVal = fixValue(currentPath[i]) + calcStep;
							currentPath[i] = currentVal;
						}
					} else if ((parseFloat(endPathArr[i]) < parseFloat(currentPath[i]))) {
						if ((parseFloat(currentPath[i]) - calcStep) < parseFloat(endPathArr[i])) {
							currentVal = fixValue(endPathArr[i]);
							currentPath[i] = currentVal;
						} else {
							currentVal = fixValue(currentPath[i]) - calcStep;
							currentPath[i] = currentVal;
						}
					} else {
						currentVal = fixValue(endPathArr[i]);
						currentPath[i] = currentVal;
					}
				} else {
					if (endPathArr[i] === endPathArr[i].toUpperCase()) {
						currentVal = endPathArr[i].toUpperCase();
						currentPath[i] = currentVal;
					}
					if (endPathArr[i] === endPathArr[i].toLowerCase()){
						currentVal = endPathArr[i].toLowerCase();
						currentPath[i] = currentVal;
					}
				}
			}

			target.setAttribute('d', currentPath.join(' '));

			var equal = true;

			for (var j = 0; j < endPathArr.length; j++) {
				if (!isNaN(endPathArr[j] * 1) && !isNaN(currentPath[j] * 1)){
					equal = equal && (fixValue(endPathArr[j]) === parseFloat(currentPath[j]));
				}
			}

			if (equal) {
				loopCount++;
				if (loopCount % 2 === 0){
					endPathArr = newPath.match(/[A-Za-z]+|-?[\d+\.\d?]+/g);
				} else {
					endPathArr = startPathArr;
				}
				if (loops === loopCount && infinite !== true) {
					if( !window.cancelAnimationFrame ) {
						clearAnimationInterval(interval);
					}
					return true;
				}
			}
			return false;
		}, fixValue(1000 / fps, 0));
	}, delay);
};
