<script type="text/javascript">
    var clickArea = document.getElementById("clickLayer");

    clickTAGvalue = dhtml.getVar("clickTAG", "https://www.adform.com");
    landingpagetarget = dhtml.getVar("landingPageTarget", "_blank");

    clickArea.onclick = function() {
        window.open(clickTAGvalue,landingpagetarget);
    };
</script>
