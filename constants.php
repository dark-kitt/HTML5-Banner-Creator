<?php
// DEFINE YOUR NAMESPACE
define('NAMESPACE', 'HTML5-BC__');
// SET NAMESPACE global or for each banner group
define('SET_NAMESPACE', 'set.namespace');

// JS ANIMATION LIBRARY files /directory/ => js/animation-library
// this files will be included by loading the banner
// base js animation file
define('BASE_JS', '_base.js');
// elem follow SVG path
define('ALONG_PATH', 'along-path.js');
// change color of elements
define('COLOR_SWAP', 'color-swap.js');
// distort SVG path
define('DISTORT_PATH', 'distort-path.js');
// draw SVG path animation
define('DRAW_SVG', 'draw-svg.js');
// SVG xlink:href bugfix
define('SVG_LOACTION_FIX', 'svg-location-fix.js');
// text typing animation
define('TEXT_TYPING', 'text-typing.js');


// CSS CLIENT BASE FILES /directory/ => scss/client-base/
// this files are set as @import in the style.scss file
// base CSS styles client
define('CLIENT_BASE_STYLES', 'client-base.scss');


// HTML FORMAT TEMPLATES /directory/ => banner-templates/
// NOTE! the order is important
// Wallpaper right top 728x90/160x600
define('WP_RIGHT_TOP_728x90_160x600', 'wp-right-top-728x90-160x600.php');
// Wallpaper right bottom 728x90/160x600
define('WP_RIGHT_BOTTOM_728x90_160x600', 'wp-right-bottom-728x90-160x600.php');
// Wallpaper left bottom 728x90/160x600
define('WP_LEFT_BOTTOM_728x90_160x600', 'wp-left-bottom-728x90-160x600.php');
// Wallpaper left top 160x600/728x90
// NOTE! change the order of the formats for this Wallpaper
define('WP_LEFT_TOP_160x600_728x90', 'wp-left-top-160x600-728x90.php');

// FireplaceAd template 160x600/728x90/160x600
define('FAD_160x600_728x90_160x600', 'fad-160x600-728x90-160x600.php');


// HTML | SCSS | JS MARKUP TEMPLATES /directory/ => markup/
// this files fill directly the created files
// client markup index.php
define('CLIENT_INDEX_MARKUP', 'client-index-markup.html');
// client markup styles.scss
define('CLIENT_SCSS_MARKUP', 'client-scss-markup.scss');
// client markup function.js
define('CLIENT_JS_MARKUP', 'client-js-markup.js');


// JS FILE TEMPLATES /directory/ => place-files/
// this files will be placed in the js folder
// client file animation.js
define('CLIENT_JS_FILE', 'client-js-file.js');

// SCSS FILE TEMPLATES /directory/ => place-files/
// this files will be placed in the scss folder
// client file animation.scss
define('CLIENT_SCSS_FILE', 'client-scss-file.scss');
