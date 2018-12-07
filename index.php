<?php

    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    require __DIR__ . '/app-assets/constants.php';
    require __DIR__ . '/app-assets/regex.php';
    require __DIR__ . '/app-assets/placeholder.php';

    require __DIR__ . '/app-classes/project.php';
    require __DIR__ . '/app-classes/helper.php';
    require __DIR__ . '/app-classes/files-and-dir.php';
    require __DIR__ . '/app-classes/namespaceCSS.php';
    require __DIR__ . '/app-classes/unusedCSS.php';

    get_header();

    $banner_project = new project(json_decode(file_get_contents(__DIR__ . '/project_config.json')));

?>

<main data-project="<?= $banner_project->project_dir ?>">

    <div class="banner-wrapper clearfix">
        <?php
            if ( count($banner_project->banner_directories) > 0 )
            {
                $banner_data = project::build_banner($banner_project->banner_directories[0]);
                foreach (json_decode($banner_data->iframes) as $iframe)
                {
                    print $iframe;
                }
            }
        ?>
    </div>

    <button class="button-light button-iframe">Refresh&nbsp;<xmp><iframes></xmp></button>

    <div class="sidebar sidebar-left" data-sidebar="left">
        <div class="resizable">
            <div class="arrow-con"><img src="<?= set_dir() ?>app-assets/img/arrow-dark.svg" alt="arrow"></div>

            <div class="collapsible">
                <div class="collapsible-head" data-files="jstree">
                    <code><xmp><html></projects></xmp></code>
                    <img class="arrow" src="<?= set_dir() ?>app-assets/img/arrow-light.svg" alt="arrow">
                </div>
                <div class="collapsible-body">
                    <input id="jstree-search" type="text" value="" placeholder="search..">
                    <img class="refresh" src="<?= set_dir() ?>app-assets/img/refresh.svg" alt="refresh" data-files="jstree">
                    <div id="jstree"></div>
                </div>
            </div>

            <nav class="nav-left">
                <ul class="clearfix">
                    <li id="banner-size">size: <?= $banner_data->size; ?></li>
                    <li>|</li>
                    <li><a id="unusedCSS" href="templates/pages/unusedCSS.php" target="_blank">
                        <?php
                            $count = 0;
                            foreach (json_decode($banner_data->unusedCSS) as $value) {
                                $count += count($value);
                            }
                            print 'unusedCSS: (' . $count . ')';
                        ?>
                    </a></li>
                </ul>
            </nav>

        </div>
    </div>

    <div class="sidebar sidebar-right" data-sidebar="right">
        <div class="resizable">
            <div class="arrow-con"><img src="<?= set_dir() ?>app-assets/img/arrow-dark.svg" alt="arrow"></div>

            <?php
                $export_data = [
                    (object) [
                        'head' => 'head></script',
                        'data_attr' => 'head',
                        'data' => helper::custom_glob( __DIR__ . AD_SCRIPT_HEAD, '.php')
                    ],
                    (object) [
                        'head' => 'body></script',
                        'data_attr' => 'body',
                        'data' => helper::custom_glob( __DIR__ . AD_SCRIPT_BODY, '.php')
                    ],
                    (object) [
                        'head' => 'html></banner',
                        'data_attr' => 'banner',
                        'data' => $banner_project
                    ]
                ];

                foreach ($export_data as $value)
                {
                    printf(
                        '<div class="collapsible">' .
                            '<div class="collapsible-head" data-files="%2$s">' .
                                '<code><xmp><%1$s></xmp></code>' .
                                '<img class="arrow" src="' . set_dir() . 'app-assets/img/arrow-light.svg" alt="arrow">' .
                            '</div>' .
                            '<div class="collapsible-body">' .
                                '<img class="refresh" src="' . set_dir() . 'app-assets/img/refresh.svg" alt="refresh" data-files="%2$s">' .
                                '<ul>' .
                                    helper::build_checkbox($value->data_attr, $value->data) .
                                '</ul>' .
                            '</div>' .
                        '</div>',
                        $value->head,
                        $value->data_attr
                    );
                }
            ?>

            <nav class="nav-right">
                <ul class="clearfix">
                    <li><a href="templates/pages/hyphenator.php" target="_blank">hyphenator.js</a></li>
                    <li>|</li>
                    <li><a id="call-project-config" href="#" target="_self">call project config</a></li>
                </ul>
            </nav>

            <button class="button-dark button-export">Export</button>
        </div>
    </div>

    <?php
        print '<script type="text/javascript">' .
                    'window.onload = function() {' .
                        'localStorage.setItem( "unusedCSS", ' . json_encode($banner_data->unusedCSS) .');' .
                '};'.
            '</script>';
    ?>
</main>

<?php get_footer(); ?>
