<?php

$project_config = banner::call_project(
    ["client","product","campagne","motif"],
    [
        [160,600],
        [300,250],
        [728,90],
        [ "fireplace-ad", [
                [160,600],
                [728,90],
                [160,600],
                ["FAD_160x600_728x90_160x600","SET_NAMESPACE"]
            ]
        ],
        [ "floor-ad", [
                [ "15-sec", [
                        [1200,400]
                    ]
                ],
                [ "30-sec", [
                        [1200,400]
                    ]
                ],
                ["DRAW_SVG","CLIENT_SCSS_FILE"]
            ]
        ],
        [ "wallpaper", [
                [ "right-top", [
                        [728,90],
                        [160,600],
                        ["WP_RIGHT_TOP_728x90_160x600"]
                    ]
                ]
            ]
        ],
        [ "banner-namespace", [
                [728,90],
                [160,600],
                [200,600],
                [300,600],
                [120,600]
            ],
            ["DRAW_SVG","CLIENT_SCSS_FILE","SET_NAMESPACE"]
        ]
    ],
    ["BASE_JS","CLIENT_BASE_STYLES","CLIENT_INDEX_MARKUP","CLIENT_SCSS_MARKUP","CLIENT_JS_MARKUP","CLIENT_JS_FILE"]
);
