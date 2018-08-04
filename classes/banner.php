<?php
	/**
	*
	*/
	if ( isset( $_POST['banner'] ) && !empty( $_POST['banner'] ) )
	{
		require __DIR__ . '/helper.php';
		require __DIR__ . '/javascript-packer.php';
		require dirname(__DIR__) . '/vendor/autoload.php';
		require dirname(__DIR__) . '/constants.php';

		if( isset( $_POST['banner_path'] ) && !empty( $_POST['banner_path'] ) )
		{
			$banner_info_arr = banner::create_banner( dirname(__DIR__) . $_POST['banner_path'] );
			echo json_encode([
				banner::set_iframes($banner_info_arr[0]),
				$banner_info_arr[1]
			]);
			exit;
		}

		if( isset( $_POST['project_path'] ) && !empty( $_POST['project_path'] ) )
		{
			if ( file_exists(json_decode($_POST['project_path']) . '_project_config.json') )
			{
				$project_config = file_get_contents( json_decode($_POST['project_path']) . '_project_config.json' );
				banner::call_project( json_decode($project_config)[0], json_decode($project_config)[1], json_decode($project_config)[2] );
				exit;
			}
			else
			{
				$project_start = helper::find_config_path_backwards(dirname(__DIR__) . $_POST['project_path'], '/_project_config.json');

				$project_config = file_get_contents( $project_start . '/_project_config.json' );
				banner::call_project( json_decode($project_config)[0], json_decode($project_config)[1], json_decode($project_config)[2] );
				exit;
			}
		}

		if( isset( $_POST['call_project'] ) && !empty( $_POST['call_project'] ) )
		{
			require dirname(__DIR__) . '/project_config.php';
			exit;
		}
	}

	class banner
	{

		public static function create_banner_config_file($project_path, $format, $files)
		{
			$js_files = [];
			$namespace = 'false';
			foreach ( $files as $file )
			{
				if ( file_exists( dirname(__DIR__) . '/js/animation-library/' . constant($file)) &&
					 pathinfo(constant($file))['extension'] === 'js')
				{
					array_push( $js_files, $file );
				}
				if ( file_exists( dirname(__DIR__) . '/banner-templates/' . constant($file)) &&
					 pathinfo(constant($file))['extension'] === 'php')
				{
					$template = constant($file);
				}
				if ( pathinfo(constant($file))['extension'] === 'namespace' )
				{
					$namespace = 'true';
				}
			}

			if ( count($format) > 1 ) {
				$banner_config_file = fopen( dirname($format[0][0]) . '/config.php', 'w' );
			}
			else {
				$banner_config_file = fopen( $format[0][0] . '/config.php', 'w' );
			}

			if ( is_array( $format[0] ) )
			{
				$count = 0;
				foreach ( $format as $format_key => $format_value )
				{
					if ( count( $format ) > 1)
					{
						if ( $format_key === 0 )
						{
							fwrite( $banner_config_file, sprintf("<?php\r\n" .
								"$" . "banner_config = [\r\n\t" .
									"$" . "main = [\r\n\t\t" .
										"\"directory\" => " . '"%1$s"' . ",\r\n\t\t" .
										"\"project_path\" => " . '"%2$s"' . ",\r\n\t\t" .
										"\"width\" => " . '%3$s' . ",\r\n\t\t" .
										"\"height\" => " . '%4$s' . ",\r\n\t\t" .
										"\"template\" => \"". '%5$s' ."\",\r\n\t\t" .
										"\"namespace\" => ". '%6$s' .",\r\n\t\t" .
										"\"js_files\" => [\r\n\t\t\t" .
											'%7$s' .
									"\r\n\t\t]\r\n\t],",
									$format_value[0], // 1
									$project_path, // 2
									$format_value[1], // 3
									$format_value[2], // 4
									$template, // 5
									$namespace, // 6
									join(",\r\n\t\t\t", $js_files) // 7
							));
						}
						else
						{
							fwrite( $banner_config_file, sprintf("\r\n\t" .
								"$" . "attach_" . sprintf("%02d", $count) . " = [\r\n\t\t" .
									"\"directory\" => " . '"%1$s"' . ",\r\n\t\t" .
									"\"project_path\" => " . '"%2$s"' . ",\r\n\t\t" .
									"\"width\" => " . '%3$s' . ",\r\n\t\t" .
									"\"height\" => " . '%4$s' . ",\r\n\t\t" .
									"\"namespace\" => ". '%5$s' .",\r\n\t\t" .
									"\"js_files\" => [\r\n\t\t\t" .
										'%6$s',
									$format_value[0], // 1
									$project_path, // 2
									$format_value[1], // 3
									$format_value[2], // 4
									$namespace, // 5
									join(",\r\n\t\t\t", $js_files) // 6
							));
							if (count( $format ) === $format_key + 1) {
								fwrite( $banner_config_file, sprintf("\r\n\t\t]\r\n\t]"));
							}
							else {
								fwrite( $banner_config_file, sprintf("\r\n\t\t]\r\n\t],"));
							}
							$count++;
						}
					}
					else
					{
						fwrite( $banner_config_file, sprintf("<?php\r\n" .
							"$" . "banner_config = [\r\n\t" .
								"$" . "main = [\r\n\t\t" .
									"\"directory\" => " . '"%1$s"' . ",\r\n\t\t" .
									"\"project_path\" => " . '"%2$s"' . ",\r\n\t\t" .
									"\"width\" => " . '%3$s' . ",\r\n\t\t" .
									"\"height\" => " . '%4$s' . ",\r\n\t\t" .
									"\"namespace\" => ". '%5$s' .",\r\n\t\t" .
									"\"js_files\" => [\r\n\t\t\t" .
										'%6$s' .
								"\r\n\t\t]\r\n\t]\r\n",
								$format_value[0], // 1
								$project_path, // 2
								$format_value[1], // 3
								$format_value[2], // 4
								$namespace, // 5
								join(",\r\n\t\t\t", $js_files) // 6
							));
					}
				}
				fwrite( $banner_config_file, sprintf("\r\n];"));
			}
			else
			{
				fwrite( $banner_config_file, sprintf("<?php\r\n" .
					"$" . "banner_config = [\r\n\t" .
						"$" . "main = [\r\n\t\t" .
							"\"directory\" => " . '"%1$s"' . ",\r\n\t\t" .
							"\"project_path\" => " . '"%2$s"' . ",\r\n\t\t" .
							"\"width\" => " . '%3$s' . ",\r\n\t\t" .
							"\"height\" => " . '%4$s' . ",\r\n\t\t" .
							"\"namespace\" => ". '%5$s' .",\r\n\t\t" .
							"\"js_files\" => [\r\n\t\t\t" .
								'%6$s' .
						"\r\n\t\t]\r\n\t]\r\n];",
						$format[0], // 1
						$project_path, // 2
						$format[1], // 3
						$format[2], // 4
						$namespace, // 5
						join(",\r\n\t\t\t", $js_files) // 6
				));
			}
			fclose($banner_config_file);

		}
		public static function place_format_files($subdirectory, $files, $format)
		{
			$banner_width = $format[0];
			$banner_height = $format[1];
			$file_arr = [];

			foreach ( $files as $file )
			{
				if ( file_exists( dirname(__DIR__) . '/place-files/' . constant($file)) &&
					 pathinfo(constant($file))['extension'] === 'js')
				{
					$js_file = fopen( $subdirectory . '/js/' . constant($file), 'w' );
					if ( preg_match( '/\#\#\#/', file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file)) ) )
					{
						$js_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#/', function($match) use($banner_width, $banner_height) {
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

						}, file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file)) );
					}
					else
					{
						$js_content = file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file));
					}
					fwrite( $js_file, $js_content );
					fclose( $js_file );
				}

				if ( file_exists( dirname(__DIR__) . '/place-files/' . constant($file)) &&
					 pathinfo(constant($file))['extension'] === 'scss')
				{
					if ( file_exists( $subdirectory . '/scss/' . constant($file)) === false )
					{
						$set_import = "@import \"" . constant($file) . "\";\r\n\r\n";
						$set_import .= file_get_contents( $subdirectory . '/scss/styles.scss', 'w' );
						file_put_contents($subdirectory . '/scss/styles.scss', $set_import);
					}

					$scss_file = fopen( $subdirectory . '/scss/' . constant($file), 'w' );
					if ( preg_match( '/\#\#\#/', file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file)) ) )
					{
						$scss_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#/', function($match) use($banner_width, $banner_height) {
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

						}, file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file)) );
					}
					else
					{
						$scss_content = file_get_contents(dirname(__DIR__) . '/place-files/' . constant($file));
					}
					fwrite( $scss_file, $scss_content );
					fclose( $scss_file );
				}
				array_push( $file_arr, constant($file));
			}

			$scss_files = glob( $subdirectory . '/scss/*');
			$js_files = glob( $subdirectory . '/js/*');
			foreach ( $scss_files as $scss_value )
			{
				if ( !in_array( basename($scss_value), $file_arr ) &&
					  basename($scss_value) !== 'styles.scss' )
				{
					unlink($scss_value);
					$remove_import = file_get_contents( $subdirectory . '/scss/styles.scss', 'w' );
					preg_replace( '/\@import\b.*' . basename($scss_value, '.scss') . '\b.*(?=\;)\;/' , '' , $remove_import );
					file_put_contents($subdirectory . '/scss/styles.scss', $remove_import);
				}
			}
			foreach ( $js_files as $js_value )
			{
				if ( !in_array( basename($js_value), $file_arr ) &&
					  basename($js_value) !== 'functions.js' )
				{
					unlink($js_value);
				}
			}

		}
		public static function create_format_files($subdirectory, $global_files, $format)
		{

			$banner_width = $format[0];
			$banner_height = $format[1];

			mkdir( $subdirectory . '/assets/' );
			mkdir( $subdirectory . '/_output/' );
			mkdir( $subdirectory . '/fallback/' );

			$index_file = fopen( $subdirectory . '/index.php', 'w' );

			mkdir( $subdirectory . '/js/' );
			$js_file = fopen( $subdirectory . '/js/functions.js', 'w' );

			mkdir( $subdirectory . '/scss/' );
			$scss_file = fopen( $subdirectory . '/scss/styles.scss', 'w' );

			foreach ( $global_files as $global_file )
			{
				if ( file_exists( dirname(__DIR__) . '/markup/' . constant($global_file)) &&
					 pathinfo(constant($global_file))['extension'] === 'html')
				{
					fwrite( $index_file, file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file)) );
				}
			}

			foreach ( $global_files as $global_file )
			{
				if ( file_exists( dirname(__DIR__) . '/markup/' . constant($global_file)) &&
					 pathinfo(constant($global_file))['extension'] === 'js')
				{
					if ( preg_match( '/\#\#\#/', file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file)) ) )
					{
						$js_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#/', function($match) use($banner_width, $banner_height) {
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

						}, file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file)) );
					}
					else
					{
						$js_content = file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file));
					}
					fwrite( $js_file, $js_content );
				}
			}

			foreach ( $global_files as $global_file )
			{
				if ( file_exists( dirname(__DIR__) . '/scss/client-base/' . constant($global_file)) &&
					 pathinfo(constant($global_file))['extension'] === 'scss')
				{
					fwrite( $scss_file, sprintf("@import \"" . constant($global_file) . "\";\r\n\r\n" ));
				}
			}

			foreach ( $global_files as $global_file )
			{
				if ( file_exists( dirname(__DIR__) . '/markup/' . constant($global_file)) &&
					 pathinfo(constant($global_file))['extension'] === 'scss')
				{
					if ( preg_match( '/\#\#\#/', file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file)) ) )
					{
						$scss_content = preg_replace_callback( '/\#\#\#width\b\#\#\#|\#\#\#height\b\#\#\#/', function($match) use($banner_width, $banner_height) {
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

						}, file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file)) );
					}
					else
					{
						$scss_content = file_get_contents(dirname(__DIR__) . '/markup/' . constant($global_file));
					}
					fwrite( $scss_file, $scss_content );
				}
			}

			fclose( $index_file );
			fclose( $js_file );
			fclose( $scss_file );
		}

		public static function create_format_folders( $subdirectory, $project_path, $format_values, $global_files )
		{
			if ( file_exists( $subdirectory ) === false )
			{
				mkdir( $subdirectory );
			}

			foreach ( $format_values[1] as $format_value )
			{
				if ( !is_array($format_value[0]) )
				{
					if ( defined( $format_value[0] ) )
					{
						$global_files = array_unique( array_merge( $global_files, $format_value ) );
						foreach ($format_value as $items)
						{
							if ( file_exists( dirname(__DIR__) . '/banner-templates/' . constant($items)) &&
								 pathinfo(constant($items))['extension'] === 'php')
							{
								$count = 0; $attached = []; $attached_check = [];
								foreach ($format_values[1] as $format)
								{
									if ( is_int($format[0]) )
									{
										if ( file_exists( $subdirectory . '/' . implode( 'x', $format ) ) === false )
										{
											mkdir( $subdirectory . '/' . implode( 'x', $format ) );
											banner::create_format_files( $subdirectory . '/' . implode( 'x', $format ), $global_files, $format );
											banner::place_format_files($subdirectory . '/' . implode( 'x', $format ), $global_files, $format);
											array_push( $attached, [$subdirectory . '/' . implode( 'x', $format ), $format[0], $format[1]]);
										}
										elseif ( count( glob( $subdirectory . '/*', GLOB_ONLYDIR ) ) < ( count( $format_values[1] ) - 1 ) &&
												 file_exists( $subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf('%02d', $count) ) === false)
										{
											mkdir( $subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf('%02d', $count) );
											banner::create_format_files( $subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf('%02d', $count), $global_files, $format );
											banner::place_format_files($subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf("%02d", $count), $global_files, $format);
											array_push( $attached, [$subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf('%02d', $count), $format[0], $format[1]]);
											$count++;
										}
										else
										{
											if ( !in_array(join(',', $format), $attached_check) )
											{
												array_push( $attached_check, join(',', $format));
												array_push( $attached, [$subdirectory . '/' . implode( 'x', $format ), $format[0], $format[1]]);
												banner::place_format_files($subdirectory . '/' . implode( 'x', $format ), $global_files, $format);
											}
											else
											{
												array_push( $attached_check, join(',', $format). '_' . sprintf('%02d', $count) );
												array_push( $attached, [$subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf('%02d', $count), $format[0], $format[1]]);
												banner::place_format_files($subdirectory . '/' . implode( 'x', $format ) . '_' . sprintf("%02d", $count), $global_files, $format);
												$count++;
											}
										}
									}
								}
								banner::create_banner_config_file( $project_path, $attached, $global_files );
							}
						}
					}
				}
			}


			foreach ( $format_values[1] as $format_value )
			{
				if ( is_string( $format_value[0] ) && !defined( $format_value[0] ) )
				{
					self::create_format_folders( $subdirectory . '/' . $format_value[0], $project_path, $format_value, $global_files );
				}
			}

			foreach ( $format_values[1] as $format_value )
			{
				if ( is_int( $format_value[0] ) )
				{
					if ( file_exists( $subdirectory . '/' . implode( 'x', $format_value ) ) === false )
					{
						mkdir( $subdirectory . '/' . implode( 'x', $format_value ) );
						banner::create_format_files( $subdirectory . '/' . implode( 'x', $format_value ), $global_files, $format_value );
					}

					if ( file_exists(dirname($subdirectory . '/' . implode( 'x', $format_value )) . '/config.php') === false )
					{
						banner::create_banner_config_file( $project_path, [[ $subdirectory . '/' . implode( 'x', $format_value ), $format_value[0], $format_value[1] ]], $global_files);
						banner::place_format_files($subdirectory . '/' . implode( 'x', $format_value ), $global_files, $format_value);
					}
				}
			}
		}

		public static function call_project( $path, $formats, $global_files )
		{

			$directory = dirname( __DIR__ ) . '/projects/';
			$project_path = dirname( __DIR__ ) . '/projects/' . dirname(join('/', $path));

			foreach ( $path as $client_folder )
			{
				if ( file_exists( $directory . $client_folder ) === false )
				{
					mkdir( $directory . $client_folder );
				}
				$directory .= $client_folder . '/';
			}

			$project_info = [$path, $formats, $global_files];
			$project_file = fopen( $directory . '/_project_config.json', 'w' );
			fwrite( $project_file, json_encode($project_info, JSON_PRETTY_PRINT));
			fclose($project_file);
			$project_content = file_get_contents( $directory . '/_project_config.json' );
			$project_content = preg_replace_callback(
							'/\[[^\[]+?(?=\])\]|\[\s+\"\w+\"\,\s+\[/',
							function ($matches) {
								return preg_replace('/\s+/', '', $matches[0]);
							},
							$project_content
						);
			file_put_contents( $directory . '/_project_config.json', $project_content );

			foreach ( $formats as $format_values )
			{
				if ( is_int( $format_values[0] ) )
				{
					$subdirectory = $directory . implode( 'x', $format_values );
					if ( file_exists( $directory . implode( 'x', $format_values ) ) === false )
					{
						mkdir( $subdirectory );
						banner::create_format_files( $subdirectory, $global_files, $format_values );
					}
					banner::create_banner_config_file( $project_path, [[ $subdirectory, $format_values[0], $format_values[1] ]], $global_files);
					banner::place_format_files($subdirectory, $global_files, $format_values);
				}

				if ( is_string( $format_values[0] ) )
				{
					banner::create_format_folders( $directory . $format_values[0], $project_path, $format_values, $global_files );
				}
			}

			$banner_info = [];
			if ( count( $formats ) < 2 && count( $formats ) !== 0 )
			{
				if ( is_int( $formats[0][0] ))
				{
					$banner_info = banner::create_banner( $directory . join('x', $formats[0]) );
				}
				else
				{
					$banner_info = banner::create_banner_loop( substr($directory, 0, -1), $formats );
				}
			}

			return [
				substr($directory, 0, -1),
				$formats,
				$banner_info
			];

		}

		public static function create_banner_loop( $path, $format )
		{

			if ( is_string($format[0][0]) )
			{
				if ( !defined( $format[0][0] ) )
				{
					return self::create_banner_loop( $path . '/' . $format[0][0], $format[0][1] );
				}
			}
			else
			{
				foreach ($format as $value)
				{
					if ( is_int($value[0]) )
					{
						$banner_info = banner::create_banner( $path . '/' . join('x', $value) );
						return $banner_info;
					}
				}
			}
		}

		public static function create_HTML_output( $banner_config, $project_path )
		{
			if ( count( glob( $banner_config['directory'] . '/_output/*' ) ) > 0 )
			{
				foreach ( glob( $banner_config['directory'] . '/_output/*' ) as $output_file )
				{
					unlink( $output_file );
				}
			}

			if ( file_exists( $banner_config['directory'] . '/_output' ) === false )
			{
				mkdir( $banner_config['directory'] . '/_output' );
			}

			$banner_scss_content = file_get_contents( $banner_config['directory'] . '/scss/styles.scss' );

			$scss = new Leafo\ScssPhp\Compiler();
			$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' );
			$scss->setImportPaths( [ dirname( __DIR__ ) . '/scss/client-base/', $banner_config['directory'] . '/scss/'] );
			$scss_content = $scss->compile( $banner_scss_content );

			$autoprefixer = new Autoprefixer('last 3 version');

			$js_content_container = '';
			foreach ( $banner_config['js_files'] as $js_files )
			{
				$js_content_container .= file_get_contents( dirname( __DIR__ ) . '/js/animation-library/' . $js_files);
			}
			foreach ( glob( $banner_config['directory'] . '/js/*.js' ) as $js_files )
			{
				$js_content_container .= file_get_contents( $js_files );
			}

			foreach ( glob( $banner_config['directory'] . '/assets/*' ) as $files )
			{
				if ( $files !== '.' && $files !== '..' && $files !== '.DS_Store' )
				{
					copy( $files, str_replace( '/assets/', '/_output/', $files ) );
				}
			}
			$HTML_file_path = $banner_config['directory'] . '/index.php';
			$HTML_content = file_get_contents( $HTML_file_path );

			$store_all_IDs_CLASSes_TAGs = helper::find_all_IDs_CLASSes_TAGs($HTML_content, $scss_content, $js_content_container);

			$all_unused_CSS = [];

			foreach ( glob( $banner_config['directory'] . '/js/*.js' ) as $js_file )
			{
				foreach ( glob( $banner_config['directory'] . '/scss/*.scss' ) as $scss_file )
				{
					array_push($all_unused_CSS, unusedCSS::find_unused_CSS($store_all_IDs_CLASSes_TAGs, $HTML_file_path, $scss_file, $js_file, $project_path));
				}
			}

			$HTML_SCSS_JS_content = helper::set_namespace($banner_config, $store_all_IDs_CLASSes_TAGs, $HTML_content, $scss_content, $js_content_container);

			$JS_content = new GK\JavaScriptPacker(
				$HTML_SCSS_JS_content[2]
			);

			ob_start();

			print
				'<!DOCTYPE html>' .
					'<html>' .
					'<head>' .
						'<meta charset="utf-8" />' .
						'<title>'. $banner_config['width'] . 'x' . $banner_config['height'] . '</title>';

					print '<style type="text/css">' . $autoprefixer->compile($HTML_SCSS_JS_content[1]) . '</style>';

				print '</head><body>';

				print $HTML_SCSS_JS_content[0];

				print '<script type="text/javascript">' . $JS_content->pack() . '</script>';

				print '<script type="text/javascript">var HTML5_BC_regex = "/(?:BC).*?HTML5[^\>]/"</script>';

				print '</body></html>';

			$banner = helper::compress_HTML( ob_get_contents() );
			ob_end_clean();

			file_put_contents( $banner_config['directory'] . '/_output/index.html', $banner );

			return $all_unused_CSS;
		}

		public static function create_banner( $path )
		{
			$config_path = helper::find_config_path_backwards( $path, '/config.php' );
			require $config_path . '/config.php';

			$all_unused_CSS = [];
			array_push($all_unused_CSS, banner::create_HTML_output( $main, $main['project_path'] ));
			if ( count( $banner_config ) > 1 )
			{
				for ( $i = 0; $i < ( count( $banner_config ) - 1 ); $i++ )
				{
					if ( ${ 'attach_' . sprintf( "%02d", $i ) } !== 'undefined' )
					{
						array_push($all_unused_CSS, banner::create_HTML_output( ${ 'attach_' . sprintf( "%02d", $i ) }, $main['project_path'] ));
					}
				}
			}

			$all_unused_CSS = helper::flatten_array($all_unused_CSS);
			$all_unused_CSS = array_map('json_encode', $all_unused_CSS);
			$all_unused_CSS = array_values(array_unique($all_unused_CSS));
			$all_unused_CSS = array_map('json_decode', $all_unused_CSS);
			$all_unused_CSS = json_decode(json_encode($all_unused_CSS), True);

			return [
				$banner_config,
				$all_unused_CSS
			];
		}

		public static function set_iframes( $banner_config )
		{
			$iframeArr = [];
			if ( count( $banner_config ) > 1)
			{
				require dirname( __DIR__ ) . '/banner-templates/' . $banner_config[0]['template'];

				foreach ( $banner_config as $banner_key => $banner_values )
				{
					preg_match('/projects\/.*/', $banner_values['directory'], $matches);
					array_push( $iframeArr, '<iframe frameborder="0" scrolling="no" width="' . $banner_values['width'] . '" height="' . $banner_values['height'] . '" src="' . $matches[0] . '/_output/index.html" style="' . $template[$banner_key][0] . '"></iframe>');
				}
			}
			else
			{
				foreach ( $banner_config as $banner_values )
				{
					preg_match('/projects\/.*/', $banner_values['directory'], $matches);
					array_push( $iframeArr, '<iframe frameborder="0" scrolling="no" width="' . $banner_values['width'] . '" height="' . $banner_values['height'] . '" src="' . $matches[0] . '/_output/index.html"></iframe>');
				}
			}

			return $iframeArr;

		}

	}
