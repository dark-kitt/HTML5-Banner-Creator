<?php
	/**
	*
	*/

	if ( isset( $_POST['export'] ) && !empty( $_POST['export'] ) &&
		 isset( $_POST['project_path'] ) && !empty($_POST['project_path'] ) )
	{
		require __DIR__ . '/helper.php';
		require __DIR__ . '/banner.php';
		require dirname(__DIR__) . '/vendor/autoload.php';
		require dirname(__DIR__) . '/constants.php';

		$project_start = helper::find_config_path_backwards( $_POST['project_path'], '/_project_config.json');

		if( isset( $_POST['checkbox_data'] ) && !empty($_POST['checkbox_data'] ) )
		{
			foreach ($_POST['checkbox_data'][2] as $banner) {
				banner::create_banner($banner);
			}
			export::create_zip_file( $_POST['checkbox_data'], $_POST['project_path'] );
			exit;
		}

		if( isset( $_POST['file_info'] ) && !empty( $_POST['file_info'] ) )
		{

			if ( $_POST['file_info'] === 'head-files' )
			{
				$script_files = glob( dirname(__DIR__) . '/js/advertiser-scripts/head/*');
			}
			if ( $_POST['file_info'] === 'body-files' )
			{
				$script_files = glob( dirname(__DIR__) . '/js/advertiser-scripts/body/*');
			}

			if ( $_POST['file_info'] !== 'banner-files' )
			{
				echo json_encode([
					export::create_checkboxes( $_POST['file_info'], $script_files ),
					[$project_start]
				]);
				exit;
			}
			else
			{
				$banner_directories_arr = helper::find_all_config_paths( $project_start );

				echo json_encode([
					export::create_checkboxes( $_POST['file_info'], $banner_directories_arr, $project_start ),
					[$project_start]
				]);
				exit;
			}

		}
	}

	class export
	{
		public static function create_checkboxes($name, $files, $project_directory = null)
		{
			$checkboxes_arr = [];

			foreach ( $files as $file )
			{

				if ( strpos($file, '.php') !== false )
				{
					array_push($checkboxes_arr, '<li>' .
						'<label class="checkbox text text-size-small">' .
							'<input type="checkbox" name="'. $name . '" value="' . basename($file) . '">' . basename($file) .
						'</label></li>');
				}
				else
				{
					if ( !glob( $file . "/index.php" ) )
					{
						$format_arr = [];
						foreach ( glob( $file . "/*", GLOB_ONLYDIR ) as $format_value )
						{
							array_push( $format_arr, basename($format_value) );
						}
						array_push($checkboxes_arr, '<li>' .
							'<label class="checkbox text text-size-small">' .
								'<span class="text-size-extra-small">' . basename( $project_directory ) . str_replace ( $project_directory , '', $file ) . '</span>' .
								'<input type="checkbox" name="'. $name . '" value="' . $file . '">' . join(', ', $format_arr) .
							'</label></li>');
					}
					else {
						array_push($checkboxes_arr, '<li>' .
							'<label class="checkbox text text-size-small">' .
								'<span class="text-size-extra-small">' . basename( $project_directory ) . str_replace ( $project_directory , '', $file ) . '</span>' .
								'<input type="checkbox" name="'. $name . '" value="' . $file . '">' . basename($file) .
							'</label></li>');
					}

				}

			}

			return $checkboxes_arr;

		}

		public static function create_zip_file($data, $project_path)
		{

			$head_scripts = $data[0];
			$body_scripts = $data[1];
			$banners = $data[2];

			$script_array = array_unique( array_merge( $head_scripts, $body_scripts ) );
			if ( count( $script_array ) === 0) {
				$script_array = [''];
			}

			if ( file_exists( dirname(__FILE__) . '/export-cache' ) === false )
			{
				mkdir( dirname(__FILE__) . '/export-cache' );
			}

			if ( file_exists( dirname(__FILE__) . '/export-cache/banner_package.zip' ) )
			{
				unlink( dirname(__FILE__) . '/export-cache/banner_package.zip' );
			}

			$banners_zip_name = dirname(__FILE__) . '/export-cache/banner_package.zip';
			$banners_zip = new ZipArchive();
			$banners_zip->open( $banners_zip_name, ZipArchive::CREATE);
			$fallback_files = [];

			foreach ($script_array as $script_value )
			{
				$banners_zip->addEmptyDir( basename( $script_value, '.php' ) );

				foreach ( $banners as $banner_directory )
				{
					require $banner_directory . '/config.php';

					foreach ( $banner_config as $banner_values )
					{

						$banner_html = file_get_contents( $banner_values['directory'] . '/_output/index.html' );
						$head_script_content = null;
						$body_script_content = null;

						$banner_width = $banner_values['width'];
						$banner_height = $banner_values['height'];

						if ( file_exists( '../js/advertiser-scripts/head/' . $script_value ) )
						{
							if ( preg_match( '/\#\#\#/', file_get_contents( '../js/advertiser-scripts/head/' . $script_value ) ) )
							{
								$head_script_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#|\s+/', function($match) use($banner_width, $banner_height) {
									if ( $match[0] === "###width###" )
									{
										return str_replace ( $match[0] , $banner_width, $match[0] );
									}
									elseif ( $match[0] === "###height###" )
									{
										return str_replace ( $match[0] , $banner_height, $match[0] );
									}
									else
									{
										return str_replace ( $match[0] , ' ', $match[0] );
									}

								}, file_get_contents( '../js/advertiser-scripts/head/' . $script_value ) );
							}
							else
							{
								$head_script_content = preg_replace( '/\s+/', ' ', file_get_contents( '../js/advertiser-scripts/head/' . $script_value ) );
							}
						}

						if ( file_exists( '../js/advertiser-scripts/body/' . $script_value ) )
						{
							if ( preg_match( '/\#\#\#/', file_get_contents( '../js/advertiser-scripts/head/' . $script_value ) ) )
							{

								$body_script_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#|\s+/', function($match) use($banner_width, $banner_height) {
									if ( $match[0] === "###width###" )
									{
										return str_replace ( $match[0] , $banner_width, $match[0] );
									}
									elseif ( $match[0] === "###height###" )
									{
										return str_replace ( $match[0] , $banner_height, $match[0] );
									}
									else
									{
										return str_replace ( $match[0] , ' ', $match[0] );
									}

								}, file_get_contents( '../js/advertiser-scripts/body/' . $script_value ) );
							}
							else
							{
								$body_script_content = preg_replace( '/\s+/', ' ', file_get_contents( '../js/advertiser-scripts/body/' . $script_value ) );
							}
						}

						$result = preg_replace_callback( '/<\/head>|<\/body>/', function($match) use($head_script_content, $body_script_content) {
							if ( $match[0] === "</head>" && $head_script_content !== null )
							{
								return str_replace ( $match[0] , $head_script_content . $match[0], $match[0] );
							}
							elseif ( $match[0] === "</body>" && $body_script_content !== null )
							{
								return str_replace ( $match[0] , $body_script_content . $match[0], $match[0] );
							}
							else
							{
								return str_replace ( $match[0] , $match[0], $match[0] );
							}

						}, $banner_html );

						preg_match( '/.*\/(.*)\/(.*?)$/', $banner_values['directory'], $parent_directory );
						$banner_files = array_diff( glob( $banner_values['directory'] . "/_output/*" ), ['index.html']);

						if ( $parent_directory[1] === basename($project_path) )
						{
							$banner_name = basename( $banner_values['directory'] );
						}
						else
						{
							$banner_name = $parent_directory[1] . '-' . basename( $banner_values['directory'] );
						}

						foreach (glob( $banner_values['directory'] . "/fallback/*" ) as $fallback_file) {
							array_push($fallback_files, $fallback_file);
						}

						$banners_zip->addEmptyDir( basename( $script_value, '.php' ) . '/' . $banner_name );

						foreach ( $banner_files as $banner_file )
						{
							$banners_zip->addFile( $banner_file, basename( $script_value, '.php' ) . '/' . $banner_name . '/' . basename( $banner_file ) );
						}

						$banners_zip->addFromString( basename( $script_value, '.php' ) . '/' . $banner_name . '/index.html', preg_replace( '/\r|\n/', '', $result ));
					}
				}
				$banners_zip->addEmptyDir( basename( $script_value, '.php' ) . '/_fallbacks' );
				foreach ( $fallback_files as $fallback )
				{
					$banners_zip->addFile( $fallback, basename( $script_value, '.php' ) . '/_fallbacks/' . basename( $fallback ));
				}
			}

			$banners_zip->close();

			/*
			* workaround for zip download underneath
			*
				header( "Pragma: public");
				header( "Expires: 0");
				header( "Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header( "Cache-Control: public");
				header( "Content-Description: File Transfer");
				header( "Content-type: application/octet-stream");
				header( "Content-Disposition: attachment; filename=\"" . $banners_zip_name . "\"");
				header( "Content-Transfer-Encoding: binary");
				header( 'Content-Length: ' . filesize( $banners_zip_name ) );

				ob_clean();
				flush();

				readfile( $banners_zip_name );
				unlink( $banners_zip_name );
				exit;
			*/

			echo '/classes/export-cache/banner_package.zip';
		}
	}
