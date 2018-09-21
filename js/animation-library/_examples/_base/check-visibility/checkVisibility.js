document.visibilityState = (function() {
	return document.visibilityState ||  document.mozVisibilityState || document.webkitVisibilityState;
})();
(function() {
	var checkVisibility = false;
	if (document.hasFocus()) {
		checkVisibility = true;
	} else {
		if (!document.hidden) {
			checkVisibility = true;
		}
	}

	function visibility(visibility) {
		if (visibility === 'visible') {
			document.title = 'visible';
		} else {
			document.title = 'hidden';
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
				visibility('visible');
				checkVisibility = true;
			} else {
				visibility('hidden');
				checkVisibility = false;
			}
		});
		/* bugfix Chrome/Safari for visibilitychange when you change the window/programm */
		document.addEventListener('focus', function() {
			visibility('visible');
			checkVisibility = true;
		}, false);
		document.addEventListener('blur', function() {
			visibility('hidden');
			checkVisibility = false;
		}, false);
		window.addEventListener('focus', function() {
			visibility('visible');
			checkVisibility = true;
		}, false);
		window.addEventListener('blur', function() {
			visibility('hidden');
			checkVisibility = false;
		}, false);
		/* bugfix Chrome/Safari for visibilitychange when you change the window/programm */
	}

	if (document.visibilityState) {
		// Standards:
		for (evt in vendorprefixes) {
			if (evt in document) {
				buildVisibilitychange(evt);
				break;
			}
		}
	} else if (/(iPad|iPhone|iPod)/g.test(navigator.userAgent)) {
		// for IOS:
		document.onpageshow = function() {
			if (!checkVisibility) {
				visibility('visible');
				checkVisibility = true;
			}
		};
		document.onpagehide = function() {
			visibility('hidden');
			checkVisibility = false;
		};
	} else if (document.documentMode !== undefined) {
		// IE 9 and lower:
			document.onfocusin = function() {
				if (!checkVisibility) {
					visibility('visible');
					checkVisibility = true;
				}
			};
			document.onfocusout = function() {
				visibility('hidden');
				checkVisibility = false;
			};
	} else {
		//  All others
		document.onfocus = function() {
			if (!checkVisibility) {
				visibility('visible');
				checkVisibility = true;
			}
		};
		document.onblur = function() {
			visibility('hidden');
			checkVisibility = false;
		};
	}
})();
