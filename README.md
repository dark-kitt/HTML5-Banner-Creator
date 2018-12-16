HTML5 Banner Creator
==============

A small web application based on php (easy to modify), which helps to create fast and dynamic HTML5 Display Ads / HTML5 Banner. Set up your project with multiple banner and fill them with markup or place prepared js or scss files. Create templates for banner like Wallpapers or Fireplace Ads and view them as one ad, while designing. Have a multiple export for different advertisers. The HTML5 markup is mainly created by the tool itself. When creating HTML5 markup in the **`index.php`** file for each banner, just place the contents you need within the **`<body></body>`** tag and style them as usual. Some examples, to show the usage, are available in the tool.

---

## Requirements

* [php 5.6+](http://php.net/downloads.php)
* [node.js](https://nodejs.org/en/) (for CSS autoprefixer)
* [composer](https://getcomposer.org) (run install in `/app/`)

No suitable VM available? [Download my.](https://bitbucket.org/madebykittel/debian_9.5_stretch-vm-for-virtualbox-macos)

### My VM
* Debian 9.5.0 amd64 netinst
* Apache 2.4.25
* php 7.0.3
* php 7.0-zip
* node.js v10.8.0
* mysql 15.1
* curl 7.52.1

Created on macOS 10.13.6 via [VirtualBox 5.2.12](https://virtualbox.org/)

## Features

* Set up multiple banners
* Display file weight
* Place markup in files
* Place prepared files
* Style with SCSS
* CSS autoprefixer
* CSS autonamespace
* Unused CSS detection
* Separated CSS compressor
* Minified output
* Multiple export for different advertisers
* Less kb JavaScript animation library
* Tree view with search bar
* Archive old projects
* Hyphenate content
* Set dynamic or static placeholder

---

## Set up a project

Set up your project with the global **`/project_config.json`** file in the root directory. Only the second array is "dynamic" to create subdirectories for banner with different dependencies. Place some constants in the last array to edit the banners globally or place them in each banner group. You'll find for each part of the constants a short description in the **`/constants.php`** file. NOTE: After you set up the project globally you will work with the **`project_config.json`** file in each project.

    {
        "project_dir": ["client","product","campagne","motif"],
        "banner_formats": [
            [120,600],
            [160,600],
            [200,600],
            [300,600],
            [300,250],
            [300,250],
            [728,90],
            {
                "fireplace-ad": [
                    [160,600],
                    [728,90],
                    [160,600],
                    ["FAD_160x600_728x90_160x600","NAMESPACE"]
                ]
            },
            {
                "floor-ad": [
                    {
                        "15-sec": [
                            [1200,200]
                        ]
                    },
                    {
                        "30-sec": [
                            [1200,200]
                        ]
                    },
                    ["TEXT_TYPING"]
                ]
            },
            {
                "namespace-banner": [
                    [120,600],
                    [160,600],
                    [200,600],
                    [300,600],
                    [300,250],
                    [728,90],
                    ["DRAW_SVG","NAMESPACE"]
                ]
            },
            {
                "wallpaper-rb": [
                    [160,600],
                    [728,90],
                    ["WP_RIGHT_BOTTOM_728x90_160x600"]
                ]
            },
            {
                "wallpaper-rt": [
                    [160,600],
                    [728,90],
                    ["WP_RIGHT_TOP_728x90_160x600"]
                ]
            }
        ],
        "global_files": ["BASE_JS","FADE_JS","CLIENT_BASE_STYLES","CLIENT_INDEX_MARKUP","CLIENT_SCSS_MARKUP","CLIENT_JS_MARKUP","CLIENT_JS_FILE","CLIENT_SCSS_FILE"]
    }


---

### hotkeys

* `crtl + a` // call project config
* `crtl + s` // refresh banner
* `arrow keys`, `enter` // navigate in tree

---

Please, take a look in the `/constants.php` and `/placeholder.php` file.

### create templates
Take a look into the `/banner-templates/*.json` files. JSON files are required. NOTE: the key is important to match the right folder.

### unusedCSS
Matched only `getElementById`, `$(#id)`, `id: id`, `gid(id)`, `getElementsByClassName`, `addClass`, `hasClass`, `removeClass`, `$(.class)`, `class: class`, `cl: class` and `gcl(class)` in the JavaScript files and the class doesn't search HTML tags in the SCSS or JavaScript files (otherwise you'll get multiple unused HTML tags).

### export
Place the necessary advertiser scripts in `/global-scripts/advertiser-scripts/head/*.php` or `/global-scripts/advertiser-scripts/body/*.php`. PHP files are required. Set placeholder: `###banner_width###`, `###banner_height###` or define your own in the `/placeholder.php` file.

### namespaceCSS
The namespace is editable in the `/constants.php` file. All constants are placeable for each banner group or globally for all banner in the last array.

### create markup
Place your markup files in `/global-markups/` and link them to your projects with constants in the `/constants.php` file. Only HTML, SCSS and JavaScript files, like examples. Set placeholder: `###banner_width###`, `###banner_height###` or define your own in the `/placeholder.php` file.

### place files
Place your prepared files in `/global-files/` and link them to your projects with constants in the `/constants.php` file. Only SCSS and JavaScript files, like examples. Set placeholder: `###banner_width###`, `###banner_height###` or define your own in the `/placeholder.php` file.

### archive old projects
Archive old projects in the tree view. You'll find the archived projects in `/banner-archive/` or define your own directory in the `/app/classes/zip.php` file.

### CSS autoprefixer and compressor
The autoprefixer and the compressor is optional because of the performance. If you think you don't need them, just set it on false in the `/constants.php` file.

---

## Animation library

This lightweight library is very useful, when jQuery or any other JavaScript library is not available or you don't want to use them.

### The library includes:

	gid(id); -> document.getElementById(id);
	gcl(class); -> document.getElementsByClassName(class);
	gtn(tag); -> document.getElementsByTagName(tag);

Just shorthand.

	animationInterval(func, interval);
	animationTimeout(func, delay);

	clearAnimationInterval(clear);
	clearAnimationTimeout(clear);

Created with requestAnimationFrame. If requestAnimationFrame is not supported, the function returns an expended setInterval / setTimeout.

	isMobile(); // returns bool

Mobile browser detection.

	addClass(elem, {
		class: class,
		remove: delay
	});
	hasClass(elem, class);
	removeClass(elem, class);

When jQuery is not available.

	fade();
	alongPath();
	colorSwap();
	distortPath(); // (!!! NOTE: the new path has to have the same numbers of anchor as the old one!!!)
	drawPath();
	textTyping();

Usage of all functions is explained in `/global-scripts/animation-library/*.js` the files.

---

## License

![](https://upload.wikimedia.org/wikipedia/commons/d/d0/CC-BY-SA_icon.svg)

---

## Integrated projects

* [PHP JavaScript's Packer](https://github.com/tholu/php-packer)
* [Leafo scssphp Compiler](https://github.com/leafo/scssphp)
* [vladkens autoprefixer](https://github.com/vladkens/autoprefixer-php)
* [jsTree](https://www.jstree.com)
* [Hyphenator.js](https://github.com/mnater/Hyphenator)
* [Javascript mobile detection](http://detectmobilebrowsers.com/)
