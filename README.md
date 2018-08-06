HTML5 Banner Creator
==============

A web application based on php, which helps to create fast and easily HTML5 Display Ads / HTML5 Banner. Set up your project with multiple banner and fill them with markup or place prepared files. Create templates for banner like Wallpapers or Fireplace Ads and view them as one assembly, while designing. Have multiple export options for different advertisers. The HTML5 markup is mainly created by the tool itself. When creating HTML5 markup in the **`index.php`** file for each banner, just place the contents you need within the **`<body></body>`** tag. Some examples, to show the usage, are available in the tool.

---

## Requirements

* [php 5.6+](http://php.net/downloads.php)
* [node.js](https://nodejs.org/en/)
* [composer](https://getcomposer.org)

No suitable VM available? [Download my.](https://bitbucket.org/madebykittel/debian_9.5_stretch-vm-for-virtualbox-macos).

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
* Compress banners
* Multiple export for different advertisers
* Less kb JavaScript animation library
* Style with SCSS
* CSS Autoprefixer
* CSS Autonamespace
* Hyphenate content

---

## Set up a project

Set up your project with the **`project_config.json`** file in the root directory. Write three arrays for the project structure, like in the example below. The first array requires strings for the main folder structure. Only the second array is "dynamic" to create subdirectories for the banners with different dependencies. Place some constants in the last array to edit the banners globally. This is also possible for each banner group, which is defined in the second array. You'll find for each part of the constants a short description in the **`constants.php`** file in the root directory.

	[
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
							[1200,200]
						]
					],
					[ "30-sec", [
							[1200,200]
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
	]

---

### Hotkey list

* cmd + s or crtl + s // to refresh the banner
* tab // switch between tree and banners
* arrow keys (in tree) // open and close folder or navigate to a banner
* enter (in tree) // open a banner

---

### unusedCSS
Matched only `getElementById`, `$(#id)`, `id: id`, `gid(id)`, `getElementsByClassName`, `addClass`, `hasClass`, `removeClass`, `$(.class)`, `class: class`, `cl: class` and `gcl(class)` in the JavaScript files and the function doesn't compare HTML tags in the SCSS or JavaScript files (otherwise you get multiple unused HTML tags).

### export
Place the required advertiser scripts in `/js/advertiser-scripts`. Only PHP files, like the examples. Placeholder: `###width###` `###height###`

### namespace
The namespace is editable in the `/constants.php` file. All constants are placeable for each banner group or globally for all banner in the last array.

### markup
Place your markup files in `/markup`. Only HTML, SCSS and JavaScript files, like the examples. Placeholder: `###width###` `###height###`

### place files
Place your prepared files in `/place-files`. Only SCSS and JavaScript files, like the examples. Placeholder: `###width###` `###height###`

---

## Animation library

This lightweight library is very useful, when jQuery or any other JavaScript library is not available or you don't want to use them.

### The library includes:

	gid(id); -> document.getElementById(id);
	gcl(class); -> document.getElementsByClassName(class);
	gtn(tag); -> document.getElementsByTagName(tag);

Just a reduced version.

	animationInterval(func, interval);
	animationTimeout(func, delay);

	clearAnimationInterval(clear);
	clearAnimationTimeout(clear);

Created with requestAnimationFrame. If requestAnimationFrame is not supported, the function returns an expended setInterval / setTimeout.

	isMobile(); // returns true

Mobile browser detection.

	addClass(elem, {
		class: class,
		remove: delay
	});
	hasClass(elem, class);
	removeClass(elem, class);

Must have when jQuery is not available.

	fade();
	alongPath();
	colorSwap();
	distortPath();
	drawPath();
	textTyping();

Usage of all functions is explained in `js/animation-library/_examples`. Open the html file in your editor to see what is required.

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
