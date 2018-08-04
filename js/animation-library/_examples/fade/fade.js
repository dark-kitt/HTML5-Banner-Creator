var fade = function (o) {
	var target = gid(o.id) || gcl(o.cl) || gtn(o.tn) || o.el,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		fps = o.fps || 120,
		calcStep = o.calcStep || 0.02,
		direction = o.direction || 'out';

	return animationTimeout(function () {
		var value = direction === 'out' ? 1 : 0,
			count = animationInterval(function () {
				if (fixValue(value - calcStep) >= 0 || fixValue(value + calcStep) <= 1) {
					value = direction === 'out' ? fixValue(value - calcStep) : fixValue(value + calcStep);
				} else {
					value = direction === 'out' ? 0 : 1;
				}
				if (o.id || o.el) {
					target.style.opacity = value;
				} else {
					for (var i = 0; i < target.length; i++) {
						target[i].style.opacity = value;
					}
				}
				if (value <= 0 || value >= 1) {
					if( !window.cancelAnimationFrame ) {
						clearAnimationInterval(count);
					}
					return true;
				}
				return false;
			}, fixValue(1000 / fps, 0));
	}, delay);
};
