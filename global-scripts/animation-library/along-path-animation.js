/* usage
 * alongPath({
        id: 'redBox', set the element by id: 'id' or el: index[i] (required, string)
        path: 'circle-path', set the path by id: 'id' or el: index[i] (e.g. pel: index[i]) (required, string)
        setPathPoints: 2, this value is for the loop which gets the coordinates from the path e.g. every second points (optional/defalut 2, number)
        align: 'center', the alignment for the element [left,left-top,left-bottom,center,center-top,center-bottom,right,right-top,right-bottom] (optional/defalut center, string)
        fps: 120, frames per second [the loop is build with requestAnimationFrame] (optional/defalut 120, number)
        direction: 'forwards', the element moves forwards or backwards (optional/defalut forwards, string)
        loop: true, should the element loop (optional/defalut false, boolean)
        loops: 0, how many loops / 0 is for endless (optional/defalut 2, number)
        alternate: false, shall the element move forwards and then backwards (optional/defalut false, boolean)
        percentage: false, is the path drawn in percentage values (optional/defalut false, boolean)
        delay: 0, set the delay in milliseconds (optional/defalut 1000, number)
        rotate: false, shall the element rotate along the path (optional/defalut false, boolean)
        stop: 70 is a percentage value where the element should stop (optional/defalut 100, number)
 * });
*/
var alongPath = function(o) {
	var target = gid(o.id) || o.el,
		path = gid(o.path) || o.pel,
		direction = o.direction || 'forwards',
		loop = o.loop || false,
		loops = o.loops || (o.loops === 0 ? 0 : 2),
		setPathPoints = o.setPathPoints || 2,
		align = o.align || 'center',
		fps = o.fps || 120,
		alternate = o.alternate || false,
		percentage = o.percentage || false,
		rotate = o.rotate || false,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		stop = o.stop || 100,
		pathSize = getSize(path),
		pathArray = calcAngle(path2Array(path)),
		alignObj = setAlign(target, align),
		counter = direction === 'forwards' ? 1 : pathArray.length - 1,
		next = direction === 'forwards' ? 1 : pathArray.length - 1,
		saveDir = o.direction,
		setTx = 0,
		setTy = 0,
		loopCount = 0,
		currentLoop = 0,
		angle = 0;

	function setPrefix(element, property, value) {
		element.style["webkit" + property] = value;
		element.style["moz" + property] = value;
		element.style["ms" + property] = value;
		element.style["o" + property] = value;
		element.style[property.toLowerCase()] = value;
	}

	var stopAni = (function(stop) {
		if (stop !== 100 && direction !== 'forwards') {
			return fixValue((((pathArray.length / 100 * stop) - pathArray.length) * -1), 0);
		} else {
			return fixValue(pathArray.length / 100 * stop, 0);
		}
	})(stop);

	function path2Array(path) {
		var pointsArray = [],
			point,
			tx,
			ty,
			cordinatesXY,
			correction;

		for (var i = 0; i < path.getTotalLength(); i++) {
			point = path.getPointAtLength(i);
			tx = point.x;
			ty = point.y;
			cordinatesXY = {
				x: tx,
				y: ty
			};
			pointsArray.push(cordinatesXY);
		}
		pointsArray.splice(0,1);
		return pointsArray;
	}

	function getSize(element) {
		var elemntWidth,
			elemntHeight;

		if (element instanceof SVGElement) {
			elemntWidth = element.getBBox().width;
			elemntHeight = element.getBBox().height;
		} else {
			elemntWidth = element.offsetWidth;
			elemntHeight = element.offsetHeight;
		}

		return {
			w: fixValue(elemntWidth, 0),
			h: fixValue(elemntHeight, 0)
		};
	}

	function setAlign(element, align) {
		var size = getSize(element),
			x = 0,
			y = 0;

		switch (align) {
			case 'center':
			case 'center-top':
			case 'center-bottom':
				x = size.w / 2;
				break;
			case 'right':
			case 'right-top':
			case 'right-bottom':
				x = size.w;
				break;
			default:
				break;
		}

		switch (align) {
			case 'left':
			case 'center':
			case 'right':
				y = size.h / 2;
				break;
			case 'left-bottom':
			case 'center-bottom':
			case 'right-bottom':
				y = size.h;
				break;
			default:
				break;
		}

		return {
			x: fixValue(x, 0),
			y: fixValue(y, 0)
		};

	}

	function setAngleAlpha(a, b) {
		var tan = Math.abs(b) / Math.abs(a);
		return Math.atan(tan) * ( 180 / Math.PI );
	}

	function calcAngle(arr) {
		for (var i = 0; i < arr.length; i++) {
			arr[i].a = setAngleAlpha(arr[i].x - arr[(i + 1) % arr.length].x, arr[i].y - arr[(i + 1) % arr.length].y);
		}
		return arr;
	}

	if (rotate === true && target instanceof HTMLElement) {
		setPrefix(target, 'TransformOrigin', alignObj.x + 'px' + ' ' + alignObj.y + 'px');
	}

	return animationTimeout(function () {
		var interval = animationInterval(function () {

			if (direction === 'forwards' && (next + setPathPoints) <= pathArray.length) {
				next += setPathPoints;
			} else if (direction === 'backwards' && (next - setPathPoints) >= 0) {
				next -= setPathPoints;
			}

			if (next < pathArray.length && rotate === true) {
				if (pathArray[next].y > pathArray[counter].y && pathArray[next].x > pathArray[counter].x && pathArray[counter].a <= 90 && pathArray[counter].a >= 0)
				{
					angle = pathArray[counter].a;
				}
				else if (pathArray[next].y < pathArray[counter].y && pathArray[next].x > pathArray[counter].x && Math.abs(pathArray[counter].a * -1) <= 90 && (pathArray[counter].a * -1) <= 0)
				{
					angle = pathArray[counter].a * -1;
				}
				else if (pathArray[next].y < pathArray[counter].y && pathArray[next].x < pathArray[counter].x && Math.abs(pathArray[counter].a - 180) >= 90)
				{
					angle = pathArray[counter].a - 180;
				}
				else if (pathArray[next].y > pathArray[counter].y && pathArray[next].x < pathArray[counter].x && (180 - pathArray[counter].a) <= 180 && (180 - pathArray[counter].a) >= 90)
				{
					angle = 180 - pathArray[counter].a;
				}
			}

			if (percentage === true) {
				var browserW = window.innerWidth,
					browserH = window.innerHeight;

				elemW = (browserW / 100) * pathSize.w;
				elemH = (browserH / 100) * pathSize.h;

				switch (align) {
					case 'center':
					case 'center-top':
					case 'center-bottom':
					case 'right':
					case 'right-top':
					case 'right-bottom':
						setTx = fixValue(((elemW / 100) * pathArray[counter].x) - alignObj.x);
						break;
					default:
						setTx = fixValue(((elemW / 100) * pathArray[counter].x));
				}

				switch (align) {
					case 'left':
					case 'center':
					case 'right':
					case 'left-bottom':
					case 'center-bottom':
					case 'right-bottom':
						setTy = fixValue(((elemH / 100) * pathArray[counter].y) - alignObj.y);
						break;
					default:
						setTy = fixValue(((elemH / 100) * pathArray[counter].y));
				}
			} else {
				switch (align) {
					case 'center':
					case 'center-top':
					case 'center-bottom':
					case 'right':
					case 'right-top':
					case 'right-bottom':
						setTx = fixValue(pathArray[counter].x - alignObj.x);
						break;
					default:
						setTx = fixValue(pathArray[counter].x);
				}

				switch (align) {
					case 'left':
					case 'center':
					case 'right':
					case 'left-bottom':
					case 'center-bottom':
					case 'right-bottom':
						setTy = fixValue(pathArray[counter].y - alignObj.y);
						break;
					default:
						setTy = fixValue(pathArray[counter].y);
				}
			}

			if (target instanceof SVGElement) {
				target.setAttribute('transform', 'matrix(1, 0, 0, 1,' + setTx + ',' + setTy + ') rotate(' + fixValue(angle) + ' ' + alignObj.x + ' ' + alignObj.y + ')');
			} else {
				setPrefix(target, 'Transform', 'matrix(1, 0, 0, 1,' + setTx + ',' + setTy + ') rotate(' + fixValue(angle) + 'deg)');
			}

			counter = direction === 'forwards' ? counter + setPathPoints : counter - setPathPoints;

			if (counter <= 0 || counter >= pathArray.length ||
				 counter >= stopAni && saveDir === 'forwards' && stop !== 100 || counter <= stopAni && saveDir === 'backwards' && stop !== 100) {
				if (loops > 0) {
					loopCount++;
				}
				if (loop === true && loopCount <= loops) {
					if (alternate === true && stop !== 100) {
						if (saveDir === 'backwards') {
							counter = (currentLoop % 2 === 0) ? stopAni : pathArray.length - 1;
							next = (currentLoop % 2 === 0) ? stopAni : pathArray.length - 1;
						} else {
							counter = (currentLoop % 2 === 0) ? stopAni : 1;
							next = (currentLoop % 2 === 0) ? stopAni : 1;
						}
						direction = direction === 'forwards' ? 'backwards' : 'forwards';
					} else if (alternate === true) {
						direction = direction === 'forwards' ? 'backwards' : 'forwards';
						counter = direction === 'forwards' ? 1 : pathArray.length - 1;
						next = direction === 'forwards' ? 1 : pathArray.length - 1;
					} else {
						counter = direction === 'forwards' ? 1 : pathArray.length - 1;
						next = direction === 'forwards' ? 1 : pathArray.length - 1;
					}
				} else {
					if( !window.cancelAnimationFrame ) {
						clearAnimationInterval(interval);
					}
					return true;
				}
				currentLoop++;
			}
			return false;
		}, fixValue(1000 / fps, 0));
	}, delay);
};
