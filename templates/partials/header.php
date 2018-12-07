<?php

    $file = basename(debug_backtrace()[1]['file']);
    $serverROOT = $_SERVER['DOCUMENT_ROOT'];

    require $serverROOT . 'vendor/autoload.php';
    $GLOBALS['scss_compiler'] = new Leafo\ScssPhp\Compiler();
    $GLOBALS['autoprefixer'] = new Autoprefixer('last 3 version');

    $GLOBALS['scss_compiler']->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' );
    $GLOBALS['scss_compiler']->setImportPaths( [ $serverROOT . 'scss' ] );
    $app_css = $GLOBALS['autoprefixer']->compile($GLOBALS['scss_compiler']->compile(file_get_contents($serverROOT . 'scss/main.scss')));

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8" />
        <title>HTML5-Banner-Creator</title>
        <link rel="shortcut icon" href="<?= set_dir(); ?>app-assets/img/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Mukta:200,300,400,500,600,700,800" rel="stylesheet">
        <style media="screen"> <?= print $app_css; ?> </style>
        <script src="<?= set_dir(); ?>js/vendor/jquery-3.3.1.min.js"></script>
        <?php
            if ( $file === 'hyphenator.php' )
            {
                print '<script src="' . set_dir() . 'js/hyphenator.js"></script>';
                print '<script src="' . set_dir() . 'js/vendor/hyphenator.js"></script>';
                foreach ( glob(set_dir() . 'js/vendor/hyphenator-patterns/*') as $pattern )
                {
                    print '<script src="' . set_dir() . 'js/vendor/hyphenator-patterns/' . basename($pattern) . '" type="text/javascript"></script>';
                }
            }
            elseif ($file === 'unusedCSS.php')
            {
                print '<script src="' . set_dir() . 'js/unusedCSS.js"></script>';
            }
            else
            {
                print '<script src="' . set_dir() . 'js/vendor/jquery-ui.min.js"></script>';
                print '<link href="' . set_dir() . 'scss/vendors/jquery-ui.css" rel="stylesheet">';
                print '<link href="' . set_dir() . 'scss/vendors/jquery-ui.structure.css" rel="stylesheet">';
                print '<script src="' . set_dir() . 'js/vendor/jstree.min.js"></script>';
                print '<script src="' . set_dir() . 'js/index.js"></script>';
            }
        ?>
    </head>
    <body>
        <header></header>
