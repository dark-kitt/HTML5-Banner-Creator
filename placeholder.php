<?php
    // dynamic / reserved placeholder
    // gets info from banner_config from each banner
    // NOTE: if you want to have more dynamic placeholder, you have to extend the banner_config file
    //       take a look in the /app/classes/files-and-dir.php on line 184
    // e.g. banner_width => width; means banner_width => $config->banner_config['width']

    // static placeholder
    // set static placeholders for each banner
    // NOTE: this means that this placeholders set the same value in each banner
    // e.g. class => example_class / id => example_id

    // NOTE: global_markup gets matched only on first load
    //       global_files gets matched on each load / refresh iframes

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
