<script type="text/javascript">
	var clicktag;
	var getUriParams = function() {
		var query_string = {};
		var query = window.location.search.substring(1);
		var parmsArray = query.split('&');
		if(parmsArray.length <= 0) return query_string;
		for(var i = 0; i < parmsArray.length; i++) {
			var pair = parmsArray[i].split('=');
			var val = decodeURIComponent(pair[1]);
			if (val != '' && pair[0] != '') query_string[pair[0]] = val;
		}
		return query_string;
	}();
	document.getElementById("clickLayer").onclick = function() {
		window.open(getUriParams.clicktag,"_blank")
	};
</script>
