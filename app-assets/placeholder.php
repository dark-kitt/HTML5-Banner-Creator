<?php
    // dynamic / reserved placeholder
    // gets info from banner_config for each banner
    // e.g. banner_width => width means banner_width => $config->banner_config['width']

    // static placeholder
    // set static placeholders for each banner
    // e.g. class => example_class / id => example_id

    // NOTE: global_markup gets matched only on first load
    //       global_files gets matched on each load

    $placeholder = (object) [
        'dynamic' => [
            ['banner_width', 'width'],
            ['banner_height', 'height']
        ],
        'static' => [
            ['class', 'example_class'],
            ['id', 'example_id']
        ]
    ]
?>
