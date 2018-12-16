<script type="text/javascript">
	var Floating = (function() {

		var videoPlayer;

		var banner = dhtml.byId('stage'),
			closeButton = dhtml.byId('closeButton'),
			video = dhtml.byId('video'),
			clickArea = dhtml.byId('clickLayer'),
			lib = Adform.RMB.lib;

		function setup(settings) {
			for (var prop in settings) {
				if (_settings[prop] instanceof Object) {
					for (var prop2 in settings[prop]) {
						_settings[prop][prop2] = settings[prop][prop2];
					}
				} else {
					_settings[prop] = settings[prop];
				}
			}
		}

		var _settings = {
			clicktag: null,
			target: null,
			video: null
		};

		function init() {
			createVideoPlayer();
		}

		closeButton.onclick = function (event) {
			dhtml.external.close && dhtml.external.close();
		};

		clickArea.onclick = function() {
			stopVideo();
			window.open(_settings.clicktag, _settings.target);
		};

		function createVideoPlayer() {

			var videoSettings = _settings.video;

			videoPlayer = Adform.Component.VideoPlayer.create({
				sources: videoSettings.sources,
				clicktag: videoSettings.clicktag,
				loop: videoSettings.loop,
				muted: videoSettings.muted,
				poster: videoSettings.poster,
				theme: 'v2'
			});

			if (videoPlayer) {
				videoPlayer.removeClass('adform-video-container');
				videoPlayer.addClass('video-container');
				videoPlayer.appendTo(video);
			}

			function landPoster() {
				if(!lib.isWinPhone) {
					stopVideo();
				}
			}

			videoPlayer.poster.node().onclick = landPoster;

			if (lib.isAndroid && lib.isFF) {
				lib.addEvent(video, 'click', function(){}, false);
			}
		}

		function stopVideo() {
			if (videoPlayer.video.state === 'playing') videoPlayer.video.pause();
		}

		return {
			setup: setup,
			init: init
		};
	})();
	(function() {
		Floating.setup({
			clicktag: dhtml.getVar('clickTAG', '//www.adform.com'),
			target: dhtml.getVar('landingPageTarget', '_blank'),
			video: {
				sources: dhtml.getVar('videoSources'),
				poster: dhtml.getAsset(2),
				clicktag: dhtml.getVar('clickTAG')
			}
		});

		Floating.init();
	})();
</script>
