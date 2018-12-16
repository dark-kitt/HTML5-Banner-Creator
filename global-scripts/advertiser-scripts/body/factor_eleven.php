<script type="text/javascript">
	function getParameter(name) {
		var query = window.location.search.substring(1),
			vars = query.split('&');
		for (var i = 0; i < vars.length; i++) {
			var itm = vars[i].split('=');
			if (itm[0] == name) {
				return decodeURIComponent(itm[1]);
			}
		}
		return '';
	}
	var clickArea = document.getElementById('clickLayer');
	clickArea.onclick = function() {
		window.open(getParameter('clickTag', '_blank'));
		return false;
	};
</script>
