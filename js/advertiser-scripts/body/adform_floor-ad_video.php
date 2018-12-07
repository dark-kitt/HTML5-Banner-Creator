<script type="text/javascript">
	/* Set expanding and collapsing events and animations, add/remove classes and background images */
	(function(Adf) {

		'use strict';

		var SingleExpanding = Adform.Component && Adform.Component.SingleExpanding;

		/* REUSABLE UTILITIES */
		/* SETS IMAGE AS BACKGROUND */
		function setBackgroundImage(elem, background, position, size) {
			if (elem && background) {
				elem.style.background = 'url("' + background + '") no-repeat ' + (position || '50% 50%') + ' / ' + (size || 'cover');
			}
		}

		/* REMOVES ELEMENTS WHEN IMAGE IS SET AS BACKGROUND */
		function removeElems() {
			[].forEach.call(arguments, function(id) {
				var elem = document.getElementById(id);
				elem && elem.parentNode.removeChild(elem);
			});
		}

		/* REAL CUSTOM FUNCTION */
		function FloorAd(options) {
			var bannerIsCollapsed = true;
			var lastHoveredExpandedState;

			var settings = {
				responsive: true,
				x: 0,
				y: dhtml.height - dhtml.collapsedHeight,
				collapsedWidth: dhtml.collapsedWidth,
				collapsedHeight: dhtml.collapsedHeight,
				expandedHeight: dhtml.height,
				expandedWidth: dhtml.width,
				expandEasing: 'linear',
				collapseEasing: 'linear',
				expandTime: Adform.getVar('expandTime') || 0,
				collapseTime: Adform.getVar('collapseTime') || 0,
				clicktag: null,
				target: null
			};

			function setup(options, settings) {
				var prop;
				var prop2;
				for (prop in options) {
					if (options.hasOwnProperty(prop)) {
						if (settings[prop] instanceof Object) {
							for (prop2 in options[prop]) {
								if (options[prop].hasOwnProperty(prop2)) {
									settings[prop][prop2] = options[prop][prop2];
								}
							}
						} else {
							settings[prop] = options[prop];
						}
					}
				}
			}

			function setSizes() {
				if (settings.banner) {
					settings.banner.style.height = settings.collapsedHeight + 'px';
					settings.banner.style.top = dhtml.height - dhtml.collapsedHeight + 'px';
				}

				if (settings.collapsed) {
					settings.collapsed.style.height = settings.collapsedHeight + 'px';
				}

				if (settings.expanded) {
					settings.expanded.style.height = (settings.expandedHeight - collapsed.offsetHeight) + 'px';
					settings.expanded.style.width = settings.expandedWidth + 'px';
				}
			}

			function addEvents() {
				if (settings.closeButton) {
					settings.closeButton.addEventListener('click', function(event) {
						stopPropagation(event);
						collapseAnimation();
						dhtml.sendEvent(1, 'Close Collapsed');
					});
				}

				if (settings.closeButtonExpanded) {
					settings.closeButtonExpanded.addEventListener('click', function(event) {
						stopPropagation(event);
						dhtml.external.close();
						dhtml.sendEvent(3, 'Close Expanded');
					});
				}

				if (settings.expandButton) {
					settings.expandButton.addEventListener('click', function(event) {
						stopPropagation(event);
						dhtml.external.expand();
						dhtml.sendEvent(2, 'Click Collapsed');
					});
				}

				if (settings.clickAreaExpanded) {
					settings.clickAreaExpanded.addEventListener('click', function(event) {
						stopPropagation(event);
						window.open(options.clicktag, options.target || '_blank');
					});
				}

				if (settings.clickAreaCollapsed) {
					settings.clickAreaCollapsed.addEventListener('click', function(event) {
						stopPropagation(event);

						if (bannerIsCollapsed) {
							dhtml.external.expand();
							dhtml.sendEvent(2, 'Click Collapsed');
						} else {
							window.open(options.clicktag, options.target || '_blank');
						}
					});
				}

				if (_AdformContent.options.showType === 0) {
					addMouseEnterEvent(true);
				}

				if (_AdformContent.options.hideType === 0) {
					settings.expanded.addEventListener('mouseleave', function() {
						setTimeout(function() {
							if (lastHoveredExpandedState) {
								dhtml.external.close();
							}
						});
					});
					settings.collapsed.addEventListener('mouseleave', function() {
						setTimeout(function() {
							if (!lastHoveredExpandedState) {
								dhtml.external.close();
							}
						});
					});

					addMouseEnterEvent();
				}
			}

			function addMouseEnterEvent(shouldExpand) {
				/*settings.expanded.addEventListener('mouseenter', function() {
					lastHoveredExpandedState = true;
					if (shouldExpand) {
						dhtml.external.expand();
					}
				});*/
				settings.collapsed.addEventListener('mouseenter', function() {
					lastHoveredExpandedState = false;
					if (shouldExpand) {
						dhtml.external.expand();
					}
				});
			}

			function stopPropagation(event) {
				event.preventDefault();
				event.stopPropagation();
			}

			function collapseAnimation() {
				settings.expanded.style.visibility = 'hidden';
				dhtml.external.superClose();
			}

			function expandAnimation() {
				settings.banner.style.transition = 'all ' + settings.expandTime + 's linear';
				settings.banner.style.height = dhtml.height + 'px';
				settings.banner.style.top = 0;
				settings.expanded.style.visibility = 'visible';
				settings.expandButton.style.display = 'none';
				settings.collapsed.style.borderTop = 'none';
			}

			function collapseAnimationExpanded() {
				settings.banner.style.transition = 'all ' + settings.collapseTime + 's linear';
				settings.banner.style.height = dhtml.collapsedHeight + 'px';
				settings.banner.style.top = dhtml.height - dhtml.collapsedHeight + 'px';
			}

			SingleExpanding.on(SingleExpanding.EXPAND_START, function() {
				bannerIsCollapsed = false;
				expandAnimation();
			});

			SingleExpanding.on(SingleExpanding.COLLAPSE_START, function() {
				bannerIsCollapsed = true;
				collapseAnimationExpanded();
			});

			SingleExpanding.on(SingleExpanding.COLLAPSE_END, function() {
				settings.expanded.style.visibility = 'hidden';
				settings.expandButton.style.display = 'block';
				settings.collapsed.style.borderTop = '1px solid #000';
			});

			function setBG() {
				if (settings.collapsedBackground) {
					setBackgroundImage(settings.collapsed, settings.collapsedBackground);
					removeElems('adf-logo-collapsed', 'adf-info-collapsed');
					settings.collapsed.classList.remove('adf-Background', 'adf-Border');
				}
				if (settings.expandedBackground) {
					setBackgroundImage(settings.expanded, settings.expandedBackground);
					removeElems('adf-logo-expanded', 'adf-info-expanded');
					settings.expanded.classList.remove('adf-Background', 'adf-Border');
				}
			}

			this.init = function() {
				setup(options, settings);
				setSizes();
				SingleExpanding && SingleExpanding.init(settings);
				if (settings.responsive === true) {
					SingleExpanding.responsiveWidth();
				}
				addEvents();
				setBG();
			};

			/* EXPOSE COMPONENTS */
			this.SingleExpanding = SingleExpanding;
		}

		Adf.FloorAd = FloorAd;

	}(Adf = window.Adf || {}));


	var banner = document.getElementById('banner');
	var collapsed = document.getElementById('collapsed');
	var expanded = document.getElementById('expanded');
	var closeButton = document.getElementById('close-button');
	var expandButton = null;
	if (document.getElementById('expand-button')) {
		var expandButton = document.getElementById('expand-button');
	}
	var clickAreaExpanded = document.getElementById('click-area-expanded');
	var clickAreaCollapsed = document.getElementById('click-area-collapsed');
	var clickTAGvalue = Adform.getVar('clickTAG') || 'http://www.adform.com'; /* Adform.getVar() gets clickTAG variable from Adform, if it is not defined (e.g. banner is being tested locally) it will fallback to second value */
	var landingpagetarget = Adform.getVar('landingPageTarget') || '_blank'; /* same as above - landingPageTarget from Adform or falls back to _blank */

	var player = new Adf.VideoContainer({
		container: '#video', /* id or class of an element where the video should be rendered */
		clicktag: clickTAGvalue,
		target: landingpagetarget
	});

	player.init(); /* initialize video player */

	var settings = {
		responsive: false,
		banner: banner,
		collapsed: collapsed,
		expanded: expanded,
		closeButton: closeButton,
		expandButton: expandButton,
		clickAreaExpanded: clickAreaExpanded,
		clickAreaCollapsed: clickAreaCollapsed,
		clicktag: clickTAGvalue,
		target: landingpagetarget,
		collapsedBackground: Adform.getAsset(4), /* set background image from additional assets for collapsed stage */
		expandedBackground: Adform.getAsset(5) /* set background image from additional assets for expanded stage */
	};

	var floorAd = new Adf.FloorAd(settings);

	floorAd.init();
</script>
