<?php
// match spaces
define('REGEX_SPACES', '\s+');
//return newline tab
define('REGEX_RNT', '[\r\n\t]+');
// html comments
define('REGEX_HTML_COMMENTS', '<!-[^\[]+->');
// multiple spaces in id
define('REGEX_SPACE_ID', 'id(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)\"');
// multiple spaces in class
define('REGEX_SPACE_CLASS', 'class(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)\"');
// correct selfclosing tags in SVG
define('REGEX_SELF_SVG', '<(rect|circle|ellipse|line|polyline|polygon|path|use|view|linearGradient|stop|feTurbulence|feFuncR|feFuncG|feFuncB|feFuncA|feComposite|feOffset|feGaussianBlur|feMergeNode) ([^<]*?)\/>');

// custom JSON prettify
define('REGEX_JSON_PRETTIFY', '\[[^\[]+?(?=\])\]|\[\s+\"\w+\"\,\s+\[');


// define keywords to match the right const files
define('REGEX_ANIMATION', 'animation\.\b');
define('REGEX_BASE_SCSS', 'base\.\b');
define('REGEX_TEMPLATE', 'template\.\b');
define('REGEX_MARKUP', 'markup\.\b');
define('REGEX_FILES', 'file\.\b');


// define patterns to match CSS selectors
// SCSS
define('REGEX_ALL_SCSS', '(?|(?:\{(?(?=\s+)\s+)[^\{\$]*?(?=\}))|(?=\{)\{[^\{\}]*?(?=\{(?(?=\s+)\s+)\$)\{[^\{]*?(?=\})\}(?(?=[^\{]*?(?=\#\{)\#\{[^\{]*?(?=\})\})(?:[^\{]*?(?=\#\{)\#\{[^\{]*?(?=\})\})+)[^\{]*?(?=\})|(?(?=\s+)\s+)(?:\@(?:-webkit-keyframes)).*?(?=\}(?(?=\s+)\s+)\})|(?(?=\s+)\s+)(?:\@(?:keyframes)).*?(?=\}(?(?=\s+)\s+)\}))(*SKIP)(*FAIL)|(?|(?(?=(?:\{(?(?=\s+)\s+)\{)|(?:\{(?(?=\s+)\s+)\})|(?:\}(?(?=\s+)\s+)\})|(?:\}(?(?=\s+)\s+)\{))\0|(?:\{|\})(?(?=\s+)\s+)(?(?=\@)\0|([^\%\;\@]*?(?(?=\#\{\$)\#\{\$.*?(?=\})))(?(?=\s+)\s+))(?(?=\}|\{\$)\0)(?=\{))|(?=\;)\;(?(?=\s+)\s+)([^\%\;\@\{\}]*?(?(?=\#\{\$)\#\{\$.*?(?=\})\}))(?(?=\s+)\s+)(?=\{)|(^[^\%\;\@]*?(?(?=\#\{\$)\#\{\$.*?(?=\})))(?=\{))');
define('REGEX_ID_SCSS', '(?(?=\#\w+)\#([\w\-]+)(?:(?=\,)|(?=\:)|(?=\s+)|(?=\.)|(?=\+)|(?=\~)|(?=\>)|(?=\[)|\#[\w\-]+|)|\0)');
define('REGEX_CLASS_SCSS', '(?(?=\.\w+)\.([\w\-]+)(?:(?=\,)|(?=\:)|(?=\s+)|(?=\#)|(?=\+)|(?=\~)|(?=\>)|(?=\[)|\.[\w\-]+|)|\0)');
define('REGEX_TAG_SCSS', '(?(?=\#\w+)\#\w+|\0)(*SKIP)(*FAIL)|(?(?=\.\w+)\.\w+|\0)(*SKIP)(*FAIL)|(?(?=\-\w+)\-\w+|\0)(*SKIP)(*FAIL)|(?(?=\_\w+)\_\w+|\0)(*SKIP)(*FAIL)|(?(?=(?:\:lang\b|\:nth-child\b|\:nth-last-child\b|\:nth-last-of-type\b|\:nth-of-type\b)\((?(?=\s+)\s+).+?(?=\))\))(?:\:lang\b|\:nth-child\b|\:nth-last-child\b|\:nth-last-of-type\b|\:nth-of-type\b)\((?(?=\s+)\s+).+?(?=\))\)|\0)(*SKIP)(*FAIL)|(?(?=\:\w+)\:\w+|\0)(*SKIP)(*FAIL)|(?(?=\[(?(?=\s+)\s+)\w+)\[(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\=(?(?=\s+)\s+)\w+)\=(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\"(?(?=\s+)\s+)\w+)\"(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\'(?(?=\s+)\s+)\w+)\'(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(\w+)');
// HTML
define('REGEX_ID_HTML', 'id\b(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)(?=\")');
define('REGEX_CLASS_HTML', 'class\b(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)(.*?)(?(?=\s+)\s+)(?=\")');
define('REGEX_TAG_HTML', '<(?(?=\s+)\s+)(\w+)');
// JS
define('REGEX_GET_ID_JS', '(?:getElementById\b)\(.*?[\"\'](?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)[\"\']');
define('REGEX_GET_CLASS_JS', '(?:getElementsByClassName\b)\(.*?[\"\'](?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)[\"\']');
// JQUERY
define('REGEX_ADD_REM_HASCLASS_JQUERY', '(?:\.addClass\b|\.hasClass\b|\.removeClass\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)(.+?)(?=\"|\')[\"\']\)');
define('REGEX_SELECTORS_JQUERY', '(?:\$)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)(?(?=[a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\'\"\_\d]+?)[a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\'\"\_\d]+?)(?=\#|\.)(?|([\#\.a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\"\'\_\d\(\)]+?))(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)\)');
define('REGEX_ID_SELECTORS_JQUERY', '\#[\w\-]+');
define('REGEX_CLASS_SELECTORS_JQUERY', '\.[\w\-]+');
// animation library in /js
define('REGEX_REM_HASCLASS_KSJS', '(?:removeClass\b|hasClass\b)\(.*?(?=\,).*?(?=\'|\")(?:\'|\")(?(?=\s+)\s+)(.*)(?=\'|\")');
define('REGEX_ADDCLASS_KSJS', '(?:addClass\b\(.*?(?(?=\s+)\s+).*\s+(?(?=\s+)\s+).*(?(?=\s+)\s+)(?=(?:class)).*(?(?=class:)class\:(?(?=\s+)\s+)(?:\'|\")(.*)(?=\'|\")|\0))');
define('REGEX_ID_OBJ_KSJS', '(?:id\b)\:(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)');
define('REGEX_CLASS_OBJ_KSJS', '(?:cl\b)\:(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)');
define('REGEX_GID_KSJS', '(?:gid\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)[\"\'](?(?=\s+)\s+)\)');
define('REGEX_GCL_KSJS', '(?:gcl\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)[\"\'](?(?=\s+)\s+)\)');


// define patterns for namespaceCSS
define('REGEX_SVG_ALL', '<svg[^>]*?[^>]*?>([^<]*(?(?!<\/svg>)<))*<\/svg>');
