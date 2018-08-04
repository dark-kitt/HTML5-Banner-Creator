<?php
    require __DIR__ . '/../vendor/autoload.php';

    $app_scss_content = file_get_contents( __DIR__ . '/../scss/main.scss' );

    $scss = new Leafo\ScssPhp\Compiler();
    $scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
    $scss->setImportPaths( ['../scss/', '../scss/abstracts', '../scss/base', '../scss/components', '../scss/layout', '../scss/pages', '../scss/vendors'] );
    $app_scss_content = $scss->compile( $app_scss_content );

    $autoprefixer = new Autoprefixer('last 3 version');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>unusedCSS</title>
        <link rel="shortcut icon" href="../assets/favicon.ico">
		<script src="../js/jquery-3.3.1.min.js"></script>
		<style media="screen"> <?php print $autoprefixer->compile($app_scss_content); ?> </style>
	</head>
	<body class="unusedCSS">
		<script type="text/javascript">
            $(document).ready(function() {

                function flatten_arrays(arrays) {
                    return [].concat.apply([], arrays);
                }

                var unusedCSSJSON = window.localStorage.getItem('unusedCSS');

                if (unusedCSSJSON === "no unused CSS found.")
                {
                    $('body').append('<ul class="clearfix">' +
                        '<li class="text text-size-normal">' + unusedCSSJSON + '</li>' +
                    '</ul>');
                }
                else
                {
                    var unusedCSSArr = JSON.parse(unusedCSSJSON);

                    $.each(unusedCSSArr, function(key, value){+
                        $('body').append('<ul class="clearfix">' +
                            '<li class="text text-size-normal">' + value['identifier'] + '</li>' +
                            '<li class="text text-size-normal">' + value['message'] + '</li>' +
                            '<li class="text text-size-small">row: ' + value['line'].join() + '</li>' +
                            '<li class="text text-size-small">' + value['directory'] + '</li>'+
                        '</ul>');
                    });
                }
            });
        </script>
	</body>
</html>
