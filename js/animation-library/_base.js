(function() {
    var checkVisibility = false,
        allSetTimeouts = [],
        allAnimationTimeouts = [],
        timestamp = (!window.performance.now) ? new Date().getTime() : performance.now();

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
        timeoutObj.triggered = (!window.performance.now) ? new Date().getTime() : performance.now();
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

    window.requestAnimationFrame = (function() {
        return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame;
    })();
    window.cancelAnimationFrame = (function() {
        return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame;
    })();
    document.visibilityState = (function() {
        return document.visibilityState || document.mozVisibilityState || document.webkitVisibilityState;
    })();
    window.performance.now = (function() {
        return window.performance.now || window.performance.webkitNow;
    })();

    window.animationInterval = function(func, interval) {

        if (!window.requestAnimationFrame) {
            return window.setInterval(func, interval);
        }

        var startTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
            request = {},
            done = false;

        function loop() {
            var currentTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
                difference = currentTime - startTime;

            if (difference >= interval && checkVisibility === true) {
                done = func.call();
                startTime = currentTime - (difference % interval);
            }

            request.id = requestAnimationFrame(loop);

            if (done === true) {
                window.clearAnimationInterval(request.id);
            }
        }
        request.id = requestAnimationFrame(loop);
        return request;
    };

    window.clearAnimationInterval = function(clear) {
        if (!window.cancelAnimationFrame) {
            clearInterval(clear);
        } else {
            window.cancelAnimationFrame(clear);
        }
    };

    window.animationTimeout = function(func, delay) {

        if (!window.requestAnimationFrame) {
            return window.setTimeout(func, delay);
        }

        var startTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
            request = {},
            timeoutObj = {};

        function loop() {
            var currentTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
                difference = currentTime - startTime;
            if (difference >= delay && checkVisibility === true) {
                func.call();
            } else {
                request.id = requestAnimationFrame(loop);
                timeoutObj.id = request.id;
            }
        }

        request.id = requestAnimationFrame(loop);

        timeoutObj.name = 'animationTimeout';
        timeoutObj.delay = delay;
        timeoutObj.triggered = (!window.performance.now) ? new Date().getTime() : performance.now();
        timeoutObj.func = func;
        timeoutObj.id = request.id;

        allAnimationTimeouts.push(timeoutObj);

        if (checkVisibility === false) {
            clearAllTimeouts(allAnimationTimeouts);
        }

        return request;
    };

    window.clearAnimationTimeout = function(clear) {
        if (!window.cancelAnimationFrame) {
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

    var cleanArray = function(arr, breakTimestamp) {
        var length = arr.length,
            newArr = [];
        while (length--) {
            if (arr[length].delay >= (breakTimestamp - arr[length].triggered)) {
                newArr.push(arr[length]);
            }
            if (length <= 0) {
                return newArr;
            }
        }
    };

    var buildTimeout = function(timeouts, breakTimestamp) {
        var length = timeouts.length;
        while (length--) {
            if (timeouts[length].name === 'setTimeout') {
                newSetTimeout(timeouts[length].func, (timeouts[length].delay - (breakTimestamp - timeouts[length].triggered)));
            } else {
                animationTimeout(timeouts[length].func, (timeouts[length].delay - (breakTimestamp - timeouts[length].triggered)));
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
            } catch (err) {
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

    var breakTimestamp = 0;

    function visibility(visibility) {
        var newAniArr = [],
            newTimeArr = [];
        if (visibility === 'visible') {
			if (allSetTimeouts.length > 0 && breakTimestamp > 0) {
				newTimeArr = cleanArray(allSetTimeouts, breakTimestamp);
			}
			if (allAnimationTimeouts.length > 0 && breakTimestamp > 0) {
				newAniArr = cleanArray(allAnimationTimeouts, breakTimestamp);
			}

			if (newTimeArr.length > 0) {
				allSetTimeouts = [];
				buildTimeout(newTimeArr, breakTimestamp);
			}
			if (breakTimestamp === 0) {
				buildTimeout(allSetTimeouts, breakTimestamp);
			}
			if (newAniArr.length > 0) {
				allAnimationTimeouts = [];
				buildTimeout(newAniArr, breakTimestamp);
			}
			if (breakTimestamp === 0) {
				buildTimeout(allAnimationTimeouts, breakTimestamp);
			}

            setAnimationPlayState('running');
        } else {

            setAnimationPlayState('paused');
            breakTimestamp = (!window.performance.now) ? new Date().getTime() : performance.now();

            if (document.readyState === 'complete') {
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

        /* partial chrome bugfix
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
var gid = function(id) {
    return id ? document.getElementById(id) : false;
},
gcl = function(cl) {
    return cl ? document.getElementsByClassName(cl) : false;
},
gtn = function(tn) {
    return tn ? document.getElementsByTagName(tn) : false;
},
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
            if (e.className.baseVal.match('(?:\\w|\\d)(?:\\s+|^)(' + cls + '\\b)(?:\\s+|$)(?:\\w|\\d)') === null) {
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
    var can = document.createElement('canvas').getContext('2d'),
        dpr = window.devicePixelRatio || 1,
        bsr = can.webkitBackingStorePixelRatio ||
        can.mozBackingStorePixelRatio ||
        can.msBackingStorePixelRatio ||
        can.oBackingStorePixelRatio ||
        can.backingStorePixelRatio || 1;
    return dpr / bsr;
})(),
hiDPICanvas = function(id, width, height) {
    var can;
    if (document.getElementById(id)) {
        can = document.getElementById(id);
        can.style.width = width + 'px';
        can.style.height = height + 'px';
        can.width = width * pixelRatio;
        can.height = height * pixelRatio;
        can.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    } else {
        can = document.createElement('canvas');
        if (can !== null) {
            can.style.width = width + 'px';
            can.style.height = height + 'px';
            can.width = width * pixelRatio;
            can.height = height * pixelRatio;
            can.setAttribute('id', id);
            can.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
            return can;
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
fixValue = function(num, plc) {
    var place = plc || (plc === 0 ? 0 : 2);
    return parseFloat(num.toFixed(place));
},
randomInt = function(min, max) {
    return Math.round(Math.random() * (max - min) + min);
},
randomRGBA = function(min, max, alpha) {
    return 'rgba(' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + alpha + ')';
},
canvasAnimations = {};
