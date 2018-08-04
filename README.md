HTML5 Banner Creator
==============

A small tool, which helps to create fast and easily HTML5 Display Ads / HTML5 Banner. Set up your project with multiple banner and fill them with markup or place prepared files. Create templates for banner like Wallpapers or Fireplace Ads and preview, while you are style them, as one assembly. Have a multiple export for different advertiser. Create only HTML5 markup in the **`index.php`** file of each banner, which you would place inside of the **`<body></body>`** tag. The rest of the HTML5 markup is created by the tool. Some examples, to figure out the usage, were created.

---

## Requirements

* [php 7.0](http://php.net/downloads.php)
* [node.js](https://nodejs.org/en/)
* [composer](https://getcomposer.org)

No suitable VM unfortunately available? [Download my](https://bitbucket.org/madebykittel/debian_9.5_stretch-vm-for-virtualbox-macos).
### My VM
* Debian 9.5.0 amd64 netinst
* Apache 2.4.25
* php 7.0.3
* php 7.0-zip
* node.js v10.8.0
* mysql 15.1
* curl 7.52.1
Was created on macOS 10.13.6 via [VirtualBox 5.2.12](https://virtualbox.org/)

## Features

* Set up multiple banner
* Displays file weight
* Place markup in files
* Place prepared files
* Compress banner
* Multiple export for different advertiser
* Less kb JavaScript animation library
* Style with SCSS
* CSS Autoprefixer
* CSS Autonamespace
* Hyphenate content

---

## Set up a project

Set up your project with the **`project_config.php`** file in the root directory. Write three arrays for the project structure and place them in the **`call_project()`** function, like in the example below (the **`$project_config`** variable is required). The first array requires strings, for the main folder structure. Only the second array is "dynamic" to create subdirectories for the banners with different dependencies. Place some constants in the last array to edit the banners globally. This is also possible for each banners group, which is defined in the second array. You'll find for each part of the constants a short description in the **`constants.php`** file in the root directory.

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

---

### unusedCSS
Matched only `getElementById`, `$(#id)`, `id: id`, `gid(id)`, `getElementsByClassName`, `addClass`, `hasClass`, `removeClass`, `$(.class)`, `class: class`, `cl: class` and `gcl(class)` in the JavaScript files.

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

This lightweight library is very useful, when jQuery or any other JavaScript library is not available or you don't wanna use them.

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

* [PHP JavaScript's Packer](http://joliclic.free.fr/php/javascript-packer/en/)
* [Leafo scssphp Compiler](https://github.com/leafo/scssphp)
* [vladkens autoprefixer](https://github.com/vladkens/autoprefixer-php)
* [jsTree](https://www.jstree.com)
* [Hyphenator.js](https://github.com/mnater/Hyphenator)
* [Javascript mobile detection](http://detectmobilebrowsers.com/)
