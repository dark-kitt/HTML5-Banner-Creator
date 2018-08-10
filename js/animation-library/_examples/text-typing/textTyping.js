var textTyping = function (o) {

	var target = gid(o.id) || gcl(o.cl) || gtn(o.tn) || o.el,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		fps = o.fps || 120,
		text = o.text || '',
		cursor = o.cursor || false,
		deleteCursorIn = o.deleteCursorIn || (o.deleteCursorIn === 0 ? 0 : 1000),
		deleteIt = o.deleteIt || false,
		deleteIn = o.deleteIn || (o.deleteIn === 0 ? 0 : 1000),
		deleteTo = o.deleteTo || '';

	function checkForBreaks(val) {
		var breaks = val.match(/<br>|<br\/>/g),
			before, after, arrBr = [];

		if (val !== '' && val !== null) {
			for (var i = 0; i <= breaks.length; i++) {
				if (val.match(/.*?(?=<)/)) {
					before = val.match(/.*?(?=<)/)[0].split('');
					for (var j = 0; j < before.length; j++) {
						arrBr.push(before[j]);
					}
					arrBr.push('<br/>');
				}
				if (i === breaks.length) {
					if (val.match(/r>.*|\/>.*/)) {
						after = val.match(/r>.*|\/>.*/)[0].substring(2).split('');
					}
					else {
						after = val.split('');
					}
					for (var k = 0; k < after.length; k++) {
						arrBr.push(after[k]);
					}
				}
				else {
					after = val.match(/r>.*|\/>.*/)[0].substring(2);
					val = after;
				}
			}
		}
		return arrBr;
	}

	return animationTimeout(function() {

		var textCon = document.createElement('SPAN');

		if(o.id || o.el){
				target.appendChild(textCon);
		} else {
			for (var s = 0; s < target.length; s++) {
				textCon[s] = document.createElement('SPAN');
				target[s].appendChild(textCon[s]);
			}
		}

		if (cursor) {
			var span = document.createElement('SPAN'),
				setCursor = document.createTextNode('|'),
				opacityCounter = 0;

			if(o.id || o.el){
				span.appendChild(setCursor);
				target.appendChild(span);
			} else {
				for (var i = 0; i < target.length; i++) {
					span[i] = document.createElement('SPAN');
					setCursor[i] = document.createTextNode('|');
					span[i].appendChild(setCursor[i]);
					target[i].appendChild(span[i]);
				}
			}

			var cursorBlink = animationInterval(function () {
					opacityCounter++;
					if (o.id || o.el) {
						if (target.childNodes.length > 1) {
							if (opacityCounter % 2 === 0) {
								target.firstChild.nextSibling.style.opacity = 0;
							} else {
								target.firstChild.nextSibling.style.opacity = 1;
							}
						} else {
							if( !window.cancelAnimationFrame ) {
								clearAnimationInterval(cursorBlink);
							}
							return true;
						}
					} else {
						for (var j = 0; j < target.length; j++) {
							if (target[j].childNodes.length > 1) {
								if (opacityCounter % 2 === 0) {
									target[j].firstChild.nextSibling.style.opacity = 0;
								} else {
									target[j].firstChild.nextSibling.style.opacity = 1;
								}
							} else {
								if( !window.cancelAnimationFrame ) {
									clearAnimationInterval(cursorBlink);
								}
								return true;
							}
						}
					}
			}, 500);
		}

		var textArray = checkForBreaks(text),
			loopCounter = -1,
			deleteArray = checkForBreaks(deleteTo),
			deleteCounter = textArray.length;

		var frameLooper = animationInterval(function() {
				loopCounter++;
				if(loopCounter === textArray.length){
					if (deleteIt === false && cursor) {
						animationTimeout(function () {
							if (o.id || o.el) {
								target.removeChild(target.firstChild.nextSibling);
							} else {
								for (var k = 0; k < target.length; k++) {
									target[k].removeChild(target[k].firstChild.nextSibling);
								}
							}
						}, deleteCursorIn);
					} else if (deleteIt && cursor) {
						animationTimeout(function () {
							var deleteText = animationInterval(function () {
								deleteCounter--;
								if (deleteCounter < deleteArray.length) {
									animationTimeout(function () {
										if (o.id || o.el) {
											while (target.firstChild) {
												target.removeChild(target.firstChild);
											}
										} else {
											for (var l = 0; l < target.length; l++) {
												if (deleteArray.length === 0) {
													while (target[l].firstChild) {
														target[l].removeChild(target[l].firstChild);
													}
												} else {
													target[l].removeChild(target[l].firstChild.nextSibling);
												}
											}
										}
									}, deleteCursorIn);
									if( !window.cancelAnimationFrame ) {
										clearAnimationInterval(deleteText);
									}
									return true;
								} else {
									textArray.splice(deleteCounter,1);
									if (o.id || o.el) {
										target.firstChild.innerHTML = textArray.join('');
									} else {
										for (var p = 0; p < target.length; p++) {
											target[p].firstChild.innerHTML = textArray.join('');
										}
									}
								}
								return false;
							}, fixValue(1000 / fps, 0));
						}, deleteIn);
					}
				if( !window.cancelAnimationFrame ) {
					clearAnimationInterval(frameLooper);
				}
				return true;
				} else {
					if (o.id || o.el) {
						target.firstChild.innerHTML += textArray[loopCounter];
					} else {
						for (var p = 0; p < target.length; p++) {
							target[p].firstChild.innerHTML += textArray[loopCounter];
						}
					}
				}
			return false;
		}, fixValue(1000 / fps, 0));
	}, delay);
};
