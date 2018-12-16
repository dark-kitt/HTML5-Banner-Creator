<?php

// CSS/HTML YOUR NAMESPACE
// SET NAMESPACE global or for each banner group
define('NAMESPACE', 'HTML5-BC__');

// Autoprefixer for banner project
// NOTE: the autoprefixer costs performance
define('AUTOPREFIXER', true);

// Compress CSS for each banner
// NOTE: the compressor costs performance
define('COMPRESS_CSS', true);

// AD_SCRIPT_HEAD files // directory => /global-scripts/animation-library
define('AD_SCRIPT_HEAD', '/global-scripts/advertiser-scripts/head');
// AD_SCRIPT_BODY files // directory => /global-scripts/animation-library
define('AD_SCRIPT_BODY', '/global-scripts/advertiser-scripts/body');


// JS_ANIMATION_LIBRARY files // directory => /global-scripts/animation-library
define('JS_ANIMATION_LIBRARY', '/global-scripts/animation-library');
// this files will be included by creating the _output
// NOTE! include animation as keyword to match the right file
// base js animation file
define('BASE_JS', '/_base-animation.js');
// elem follow SVG path
define('ALONG_PATH', '/along-path-animation.js');
// change color of elements
define('COLOR_SWAP', '/color-swap-animation.js');
// distort SVG path
define('DISTORT_PATH', '/distort-path-animation.js');
// draw SVG path animation
define('DRAW_SVG', '/draw-svg-animation.js');
// fade in or out
define('FADE_JS', '/fade-animation.js');
// text typing animation
define('TEXT_TYPING', '/text-typing-animation.js');


// CLIENT_SCSS BASE FILES // directory => /global-base-scss/client-base
define('CLIENT_BASE_SCSS', '/global-base-scss');
// this files are set as @import in the style.scss file
// NOTE! include base as keyword to match the right file
// e.g. client base SCSS styles
define('CLIENT_BASE_STYLES', '/client-base.scss');


// BANNER_TEMPLATES // directory => /banner-templates
define('BANNER_TEMPLATES', '/banner-templates');
// NOTE! include template at the end as keyword to match the right file
// NOTE! the order in the template file must be the same as in the project conf
// Wallpaper right top 728x90/160x600
define('WP_RIGHT_TOP_728x90_160x600', '/wp-right-top-728x90-160x600-template.json');
// Wallpaper right bottom 728x90/160x600
define('WP_RIGHT_BOTTOM_728x90_160x600', '/wp-right-bottom-728x90-160x600-template.json');
// Wallpaper left bottom 728x90/160x600
define('WP_LEFT_BOTTOM_728x90_160x600', '/wp-left-bottom-728x90-160x600-template.json');
// Wallpaper left top 160x600/728x90
// NOTE! change the order of the formats for this Wallpaper
define('WP_LEFT_TOP_160x600_728x90', '/wp-left-top-160x600-728x90-template.json');
// FireplaceAd template 160x600/728x90/160x600
define('FAD_160x600_728x90_160x600', '/fad-160x600-728x90-160x600-template.json');


// GLOBAL_MARKUP, HTML | SCSS | JS // directory => /global-markups
define('GLOBAL_MARKUP', '/global-markups');
// NOTE! include markup as keyword to match the right file
// this files fill directly the created files
// e.g. markup index.php
define('CLIENT_INDEX_MARKUP', '/client-index-markup.html');
// e.g. markup styles.scss
define('CLIENT_SCSS_MARKUP', '/client-scss-markup.scss');
// e.g. markup function.js
define('CLIENT_JS_MARKUP', '/client-js-markup.js');


// GLOBAL_FILES, SCSS | JS // directory => /global-files
define('GLOBAL_FILES', '/global-files');
// NOTE! include file as keyword to match the right file
// SCSS TEMPLATE FILES
// this files will be placed in each banner scss folder
define('CLIENT_SCSS_FILE', '/client-scss-file.scss');
// JS TEMPLATE FILES
// this files will be placed in each banner js folder
define('CLIENT_JS_FILE', '/client-js-file.js');
