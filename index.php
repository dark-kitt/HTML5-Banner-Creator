<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/classes/helper.php';
require __DIR__ . '/classes/banner.php';
require __DIR__ . '/classes/export.php';

require __DIR__ . '/constants.php';
require __DIR__ . '/project_config.php';

$app_scss_content = file_get_contents( __DIR__ . '/scss/main.scss' );

$scss = new Leafo\ScssPhp\Compiler();
$scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
$scss->setImportPaths( [__DIR__ . '/scss/', __DIR__ . '/scss/abstracts', __DIR__ . '/scss/base', __DIR__ . '/scss/components', __DIR__ . '/scss/layout', __DIR__ . '/scss/pages', __DIR__ . '/scss/vendors'] );
$app_scss_content = $scss->compile( $app_scss_content );

$autoprefixer = new Autoprefixer('last 3 version');

$banner_size = 0;
if ( count( $project_config[1] ) < 2 && count( $project_config[1] ) !== 0 )
{
	if ( count($project_config[2][0]) > 1)
	{
		foreach ($project_config[2][0] as $size)
		{
			$banner_size += helper::folderSize($size['directory'] . '/_output');
		}
	}
	else
	{
		$banner_size = helper::folderSize($project_config[2][0][0]['directory'] . '/_output');
	}
}

?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8" />
		<title>HTML5 Banner Creator</title>
		<link rel="shortcut icon" href="assets/favicon.ico">
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jstree.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<link href="scss/vendors/jquery-ui.css" rel="stylesheet">
		<link href="scss/vendors/jquery-ui.structure.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Mukta:200,300,400,500,600,700,800" rel="stylesheet">
		<style media="screen"> <?php print $autoprefixer->compile($app_scss_content); ?> </style>
	</head>
	<body>
		<div class="wrapper">
			<div id="banner" class="clearfix">
				<?php
					if ( count( $project_config[1] ) < 2 && count( $project_config[1] ) !== 0 )
					{
						if ( count($project_config[2][0]) > 1)
						{
							$iframes = banner::set_iframes( $project_config[2][0] );
							foreach ($iframes as $value) {
								print $value;
							}
						}
						else
						{
							print banner::set_iframes( $project_config[2][0] )[0];
						}
					}
				?>
			</div>
			<button class="button button-bright iframe-button button-center text text-dark text-regular text-size-normal">Refresh&nbsp;<xmp><iframes></xmp></button>
		</div>

		<div class="sidebar sidebar-left">
			<div class="resizable-wrapper-left">
				<div class="sidebar-arrow-container sidebar-arrow-container-left">
					<div class="sidebar-arrow-wrapper">
						<svg class="sidebar-arrow sidebar-arrow-left" xmlns="http://www.w3.org/2000/svg" width="40" height="30" viewBox="0 0 116.21 67.47">
							<polyline points="107.71 58.97 58.97 8.5 8.5 57.24" fill="none" stroke="#343334" stroke-linecap="round" stroke-linejoin="round" stroke-width="17"/>
						</svg>
					</div>
				</div>

				<div class="collapse">
					<div class="collapse-head text text-size-normal clearfix">
						<code><xmp><HTML5></projects></xmp></code>
						<i class="collapse-arrow"></i>
					</div>
					<div class="collapse-body tree-collapse">
						<div class="collapse-body-content">
							<div class="refresh-body-content" data-files="tree"></div>
							<div id="jstree" class="text text-size-small"></div>
						</div>
					</div>
				</div>

				<div class="nav nav-left clearfix">
					<div class="nav-item nav-item-left text text-dark text-regular text-size-normal">
						<span id="banner-size">size:&nbsp;<?php print helper::formatSize($banner_size); ?></span>
						<span>&nbsp;|&nbsp;</span>
						<span>
							<a id="unusedCSS" href="classes/unusedCSS.php" target="_blank" class="text-dark">
								<?php
									if( count($project_config[2]) > 0 )
									{
										print 'unusedCSS:&nbsp;( ' . count($project_config[2][1]) . ' )';
									}
									else
									{
										print 'unusedCSS:&nbsp;( 0 )';
									}
								?>
							</a>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="sidebar sidebar-right">
			<div class="resizable-wrapper-right">
				<div class="sidebar-arrow-container sidebar-arrow-container-right">
					<div class="sidebar-arrow-wrapper">
						<svg class="sidebar-arrow sidebar-arrow-right" xmlns="http://www.w3.org/2000/svg" width="40" height="30" viewBox="0 0 116.21 67.47">
							<polyline points="107.71 58.97 58.97 8.5 8.5 57.24" fill="none" stroke="#343334" stroke-linecap="round" stroke-linejoin="round" stroke-width="17"/>
						</svg>
					</div>
				</div>

				<div class="collapse">
					<div class="collapse-head text text-size-normal clearfix">
						<code><xmp><head></script></xmp></code>
						<i class="collapse-arrow"></i>
					</div>
					<div class="collapse-body scripts-collapse">
						<div class="collapse-body-content">
							<div class="refresh-body-content" data-files="head"></div>
							<ul>
								<?php
									$banner_head_files = glob('js/advertiser-scripts/head/*');
									$head_checkboxes = export::create_checkboxes('head-files', $banner_head_files );

									foreach ($head_checkboxes as $head_checkbox)
									{
										print $head_checkbox;
									}
								?>
							</ul>
						</div>
					</div>
				</div>

				<div class="collapse">
					<div class="collapse-head text text-size-normal clearfix">
						<code><xmp><body></script></xmp></code>
						<i class="collapse-arrow"></i>
					</div>
					<div class="collapse-body scripts-collapse">
						<div class="collapse-body-content">
							<div class="refresh-body-content" data-files="body"></div>
							<ul>
								<?php
									$banner_body_files = glob('js/advertiser-scripts/body/*');
									$body_checkboxes = export::create_checkboxes('body-files', $banner_body_files );

									foreach ($body_checkboxes as $body_checkbox)
									{
										print $body_checkbox;
									}
								?>
							</ul>
						</div>
					</div>
				</div>

				<div class="collapse">
					<div class="collapse-head text text-size-normal clearfix">
						<code><xmp><HTML5></banner></xmp></code>
						<i class="collapse-arrow"></i>
					</div>
					<div class="collapse-body banner-collapse">
						<div class="collapse-body-content">
							<div class="refresh-body-content" data-files="banner"></div>
							<ul class="banner-checkboxes">
								<?php
									$banner_directories_arr = helper::find_all_config_paths( $project_config[0] );
									$banner_checkboxes = export::create_checkboxes('banner-files', $banner_directories_arr, $project_config[0]);

									foreach ($banner_checkboxes as $banner_checkbox)
									{
										print $banner_checkbox;
									}
								?>
							</ul>
						</div>
					</div>
				</div>

				<div class="nav nav-right clearfix">
					<div class="nav-item nav-item-right text text-dark text-regular text-size-normal">
						<a id="callProject" href="#" target="_self" class="text-dark">call project config</a>
						<span>&nbsp;|&nbsp;</span>
						<a href="classes/hyphenator.php" target="_blank" class="text-dark">hyphenator.js</a>
					</div>
				</div>

				<button class="button button-dark export-button text text-regular text-size-normal">Export</button>
			</div>
		</div>

		<script type="text/javascript" src="js/functions.js"></script>
		<?php
			print '<script type="text/javascript">' .
						'window.onload = function() {' .
							'var current_project_path = ' . json_encode($project_config[0]) . ';' .
							'localStorage.setItem( "current_project_path", JSON.stringify(current_project_path));';

			if( count($project_config[2]) > 0 )
			{
					print 'var unusedData = ' . json_encode($project_config[2][1]) . ';' .
							'localStorage.setItem( "unusedCSS", JSON.stringify(unusedData));' .
						'};';
			}
			else
			{
					print 'var unusedData = "no unused CSS found.";' .
							'localStorage.setItem( "unusedCSS", unusedData);' .
						'};';
			}
			print '</script>';
		?>
	</body>
</html>
