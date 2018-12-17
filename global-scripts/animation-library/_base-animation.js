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
            var ctx = this,
                args = arguments;
            if (checkVisibility) {
                if (start === true) {
                    func.apply(ctx, args);
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
    document.visibilityState || document.mozVisibilityState || document.webkitVisibilityState;
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
                difference = currentTime - startTime,
                ctx = this,
                args = arguments;

            if (difference >= interval && checkVisibility === true) {
                done = func.apply(ctx, args);
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
                difference = currentTime - startTime,
                ctx = this,
                args = arguments;

            if (difference >= delay && checkVisibility === true) {
                func.apply(ctx, args);
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
    fixValue = function(num, places) {
        var place = places || (places === 0 ? 0 : 2);
        return parseFloat(num.toFixed(place));
    },
    randomInt = function(min, max) {
        return Math.round(Math.random() * (max - min) + min);
    },
    randomRGBA = function(min, max, alpha) {
        return 'rgba(' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + randomInt(min, max) + ', ' + alpha + ')';
    },
    isMobile = function() {
        var check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true;})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    };
