<?php

	/**
	*
	*/

	if ( isset( $_POST['banner_size'] ) && !empty( $_POST['banner_size'] ) )
	{
		if( isset( $_POST['banner_path'] ) && !empty( $_POST['banner_path'] ) )
		{
			require dirname(__DIR__) . '/constants.php';
			$config_path = helper::find_config_path_backwards( dirname(__DIR__) . $_POST['banner_path'], '/config.php' );
			require $config_path . '/config.php';

			$banner_size = 0;
			foreach ($banner_config as $size)
			{
				$banner_size += helper::folderSize($size['directory'] . '/_output');
			}
			print helper::formatSize($banner_size);
			exit;
		}
	}

	class helper
	{
		public static function folderSize($path)
		{
			$total_size = 0;
			$files = scandir($path);

			foreach ($files as $t)
			{
				if (is_dir(rtrim($path, '/') . '/' . $t))
				{
					if ($t !== '.' && $t !== '..')
					{
						$size = self::folderSize(rtrim($path, '/') . '/' . $t);

						$total_size += $size;
					}
				}
				else
				{
					if ($t !== '.DS_Store') {
						$size = filesize(rtrim($path, '/') . '/' . $t);
						$total_size += $size;
					}
				}
			}

			return $total_size;
		}

		public static function formatSize($size)
		{
			$mod = 1024;
			$units = explode(' ', 'B KB MB GB TB PB');
			for ($i = 0; $size > $mod; $i++)
			{
				$size /= $mod;
			}

			return round($size, 2) . ' ' . $units[$i];
		}

		public static function delete_dir($dir)
		{
			$items = scandir($dir);
			foreach ($items as $item) {
				if ($item === '.' || $item === '..') {
					continue;
				}
				$path = $dir.'/'.$item;
				if (is_dir($path)) {
					self::delete_dir($path);
				}
				else
				{
					unlink($path);
				}
			}
			rmdir($dir);
		}

		public static function find_all_file_paths( $path )
		{
			$directory_items = scandir($path);
			$result = [];
			$ignore = ['.', '..', '.DS_Store'];

			foreach ($directory_items as $item) {
				if ( !in_array($item, $ignore) )
				{
					if ( is_file($path . '/' . $item) )
					{
						$result[] = [ $path . '/' . $item ];
					}
					if ( is_dir($path . '/' . $item) )
					{
						$result[] = [ self::find_all_file_paths($path . '/' . $item ) ];
					}
				}
			}
			return array_unique( helper::flatten_array($result));
		}

		public static function find_all_config_paths( $path )
		{
			$directory_items = scandir($path);
			$result = [];
			$ignore = ['.', '..', '.DS_Store', '_project_config.json', 'index.php'];
			foreach ($directory_items as $item) {
				if ( !in_array($item, $ignore) )
				{
					if ($item === 'config.php')
					{
						$result[] = [ $path ];
					}
					if (is_dir($path . '/' . $item) )
					{
						$result[] = [ self::find_all_config_paths($path . '/' . $item ) ];
					}
				}
			}
			return array_unique( helper::flatten_array($result));
		}

		public static function find_config_path_backwards($path, $file)
		{
			if ( file_exists( $path . $file ) )
			{
				return $path;
			}
			else
			{
				return self::find_config_path_backwards( dirname($path, 1), $file );
			}
		}

		public static function flatten_array(array $array)
		{
			$return = array();
			array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
			return $return;
		}

		public static function compress_HTML($html)
		{
			preg_match_all( '!(<(?:code|pre|script).*>[^<]+</(?:code|pre|script)>)!', $html, $pre );

			$html = preg_replace( '/<(rect|circle|ellipse|line|polyline|polygon|path|use|view|linearGradient|stop|feTurbulence|feFuncR|feFuncG|feFuncB|feFuncA|feComposite|feOffset|feGaussianBlur|feMergeNode) ([^<]*?)\/>/s', '<$1 $2></$1>', $html );
			$html = preg_replace( '/class(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)\"/', ' ', $html );
			$html = preg_replace( '/id(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)\"/', ' ', $html );
			$html = preg_replace( '/<!-[^\[]+->/', '', $html );
			$html = preg_replace( '/[\r\n\t]+/', ' ', $html );
			$html = preg_replace( '/>[\s]+</', '><', $html );
			$html = preg_replace( '/[\s]+/', ' ', $html );
			if ( !empty( $pre[0] ) )
			{
				foreach ( $pre[0] as $tag )
				{
					$html = preg_replace( '!#pre#!', $tag, $html, 1 );
				}
			}
			return $html;
		}

		public static function set_namespace($banner_config, $all_IDs_CLASSes_TAGs, $HTML_content = null , $SCSS_content = null, $JS_content = null, $force_set_namespace = null)
		{
			$SCSS_ids = $all_IDs_CLASSes_TAGs[0][0];
			$SCSS_classes = $all_IDs_CLASSes_TAGs[0][1];

			$HTML_ids = $all_IDs_CLASSes_TAGs[1][0];
			$HTML_classes = $all_IDs_CLASSes_TAGs[1][1];

			$JS_get_ids = $all_IDs_CLASSes_TAGs[2][0][0];
			$JS_selector_id_jQuery = $all_IDs_CLASSes_TAGs[2][0][1];
			$JS_id_in_object_KSJS = $all_IDs_CLASSes_TAGs[2][0][2];
			$JS_gid_KSJS = $all_IDs_CLASSes_TAGs[2][0][3];

			$JS_get_classes = $all_IDs_CLASSes_TAGs[2][1][0];
			$JS_add_rem_hasClass_JQuery = $all_IDs_CLASSes_TAGs[2][1][1];
			$JS_selector_class_jQuery = $all_IDs_CLASSes_TAGs[2][1][2];
			$JS_rem_hasClass_KSJS = $all_IDs_CLASSes_TAGs[2][1][3];
			$JS_addClass_KSJS = $all_IDs_CLASSes_TAGs[2][1][4];
			$JS_cl_in_object_KSJS = $all_IDs_CLASSes_TAGs[2][1][5];
			$JS_gcl_KSJS = $all_IDs_CLASSes_TAGs[2][1][6];

			if ( $banner_config['namespace'] || $force_set_namespace )
			{
				if ( count($HTML_ids) > 0 )
				{
					foreach ($HTML_ids as $HTML_id_search_value)
					{
						$HTML_content = preg_replace_callback(
										'/(id(?(?=\s+)\s+)\=(?(?=\s+)\s+)\".*?)(' . $HTML_id_search_value . '\b.*?(?=\"))/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$HTML_content
									);
						$HTML_content = preg_replace_callback(
										'/<svg[^>]*?[^>]*?>([^<]*(?(?!<\/svg>)<))*<\/svg>/',
										function ($match) use($HTML_id_search_value) {
											if (preg_match('/(?:xlink:href|href).*?\#' . $HTML_id_search_value . '\b/', $match[0]))
											{
												return $match[0] = preg_replace_callback(
																'/((?:xlink:href|href).*?\#)(' . $HTML_id_search_value . '\b)/',
																function ($matches) {
																	if ( preg_match('/' . constant('NAMESPACE') . '\b/', $matches[0]) )
																	{
																		return $matches[1] . $matches[2];
																	}
																	else
																	{
																		return $matches[1] . constant('NAMESPACE') . $matches[2];
																	}
																},
																$match[0]
															);
											}
											else
											{
												return $match[0];
											}
										},
										$HTML_content
									);
					}
				}
				if ( count($HTML_classes) > 0 )
				{
					foreach ($HTML_classes as $HTML_class_search_value)
					{
						$HTML_content = preg_replace_callback(
										'/(class\b(?(?=\s+)\s+)\=(?(?=\s+)\s+).*?(?|(?:(?=\s+)\s+)|(?:(?=\")\")|(?:(?=\')\')))(' . $HTML_class_search_value . '\b.*?(?=\"))/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$HTML_content
									);
					}
				}
				if ( count($SCSS_ids) > 0 )
				{
					foreach ($SCSS_ids as $SCSS_id_search_value)
					{
						$SCSS_content = preg_replace_callback(
										'/(\#)(' . $SCSS_id_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$SCSS_content
									);
					}
				}
				if ( count($SCSS_classes) > 0 )
				{
					foreach ($SCSS_classes as $SCSS_class_search_value)
					{
						$SCSS_content = preg_replace_callback(
										'/(\.)(' . $SCSS_class_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$SCSS_content
									);
					}
				}
				if ( count($JS_get_ids) > 0 )
				{
					foreach ($JS_get_ids as $JS_get_id_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:getElementById\b)\(.*?)(' . $JS_get_id_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_selector_id_jQuery) > 0 )
				{
					foreach ($JS_selector_id_jQuery as $JS_selector_id_jQuery_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:\$)\(.*?(?=\#)\#)(' . $JS_selector_id_jQuery_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_id_in_object_KSJS) > 0 )
				{
					foreach ($JS_id_in_object_KSJS as $JS_id_in_object_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:id\b)\:.*?)(' . $JS_id_in_object_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_gid_KSJS) > 0 )
				{
					foreach ($JS_gid_KSJS as $JS_gid_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:gid\b)\(.*?)(' . $JS_gid_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_get_classes) > 0 )
				{
					foreach ($JS_get_classes as $JS_get_class_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:getElementsByClassName\b)\(.*?)(' . $JS_get_class_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_add_rem_hasClass_JQuery) > 0 )
				{
					foreach ($JS_add_rem_hasClass_JQuery as $JS_add_rem_hasClass_JQuery_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:\.addClass\b|\.hasClass\b|\.removeClass\b)\(.*?)(' . $JS_add_rem_hasClass_JQuery_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_selector_class_jQuery) > 0 )
				{
					foreach ($JS_selector_class_jQuery as $JS_selector_class_jQuery_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:\$)\(.*?(?=\.)\.)(' . $JS_selector_class_jQuery_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_rem_hasClass_KSJS) > 0 )
				{
					foreach ($JS_rem_hasClass_KSJS as $JS_rem_hasClass_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:removeClass\b|hasClass\b)\(.*?(?=\,).*?(?=\'|\")(?:\'|\").*?)(' . $JS_rem_hasClass_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_addClass_KSJS) > 0 )
				{
					foreach ($JS_addClass_KSJS as $JS_addClass_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:class\b)\:.*?)(' . $JS_addClass_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_cl_in_object_KSJS) > 0 )
				{
					foreach ($JS_cl_in_object_KSJS as $JS_cl_in_object_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:cl\b)\:.*?)(' . $JS_cl_in_object_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
				if ( count($JS_gcl_KSJS) > 0 )
				{
					foreach ($JS_gcl_KSJS as $JS_gcl_KSJS_search_value)
					{
						$JS_content = preg_replace_callback(
										'/((?:gid\b)\(.*?)(' . $JS_gcl_KSJS_search_value . '\b)/',
										function ($match) {
											if ( preg_match('/' . constant('NAMESPACE') . '\b/', $match[0]) )
											{
												return $match[1] . $match[2];
											}
											else
											{
												return $match[1] . constant('NAMESPACE') . $match[2];
											}
										},
										$JS_content
									);
					}
				}
			}

			return [
				$HTML_content,
				$SCSS_content,
				$JS_content
			];
		}

		public static function find_all_IDs_CLASSes_TAGs($HTML_content = null , $SCSS_content = null, $JS_content = null)
		{

			$pattern_all_ids_classes_tags_SCSS = '/(?|(?:\{(?(?=\s+)\s+)[^\{\$]*?(?=\}))|(?=\{)\{[^\{\}]*?(?=\{(?(?=\s+)\s+)\$)\{[^\{]*?(?=\})\}(?(?=[^\{]*?(?=\#\{)\#\{[^\{]*?(?=\})\})(?:[^\{]*?(?=\#\{)\#\{[^\{]*?(?=\})\})+)[^\{]*?(?=\})|(?(?=\s+)\s+)(?:\@(?:-webkit-keyframes)).*?(?=\}(?(?=\s+)\s+)\})|(?(?=\s+)\s+)(?:\@(?:keyframes)).*?(?=\}(?(?=\s+)\s+)\}))(*SKIP)(*FAIL)|(?|(?(?=(?:\{(?(?=\s+)\s+)\{)|(?:\{(?(?=\s+)\s+)\})|(?:\}(?(?=\s+)\s+)\})|(?:\}(?(?=\s+)\s+)\{))\0|(?:\{|\})(?(?=\s+)\s+)(?(?=\@)\0|([^\%\;\@]*?(?(?=\#\{\$)\#\{\$.*?(?=\})))(?(?=\s+)\s+))(?(?=\}|\{\$)\0)(?=\{))|(?=\;)\;(?(?=\s+)\s+)([^\%\;\@\{\}]*?(?(?=\#\{\$)\#\{\$.*?(?=\})\}))(?(?=\s+)\s+)(?=\{)|(^[^\%\;\@]*?(?(?=\#\{\$)\#\{\$.*?(?=\})))(?=\{))/';

			$pattern_all_ids_SCSS = '/(?(?=\#\w+)\#([\w\-]+)(?:(?=\,)|(?=\:)|(?=\s+)|(?=\.)|(?=\+)|(?=\~)|(?=\>)|(?=\[)|\#[\w\-]+|)|\0)/';
			$pattern_all_classes_SCSS = '/(?(?=\.\w+)\.([\w\-]+)(?:(?=\,)|(?=\:)|(?=\s+)|(?=\#)|(?=\+)|(?=\~)|(?=\>)|(?=\[)|\.[\w\-]+|)|\0)/';
			$pattern_all_tags_SCSS = '/(?(?=\#\w+)\#\w+|\0)(*SKIP)(*FAIL)|(?(?=\.\w+)\.\w+|\0)(*SKIP)(*FAIL)|(?(?=\-\w+)\-\w+|\0)(*SKIP)(*FAIL)|(?(?=\_\w+)\_\w+|\0)(*SKIP)(*FAIL)|(?(?=(?:\:lang\b|\:nth-child\b|\:nth-last-child\b|\:nth-last-of-type\b|\:nth-of-type\b)\((?(?=\s+)\s+).+?(?=\))\))(?:\:lang\b|\:nth-child\b|\:nth-last-child\b|\:nth-last-of-type\b|\:nth-of-type\b)\((?(?=\s+)\s+).+?(?=\))\)|\0)(*SKIP)(*FAIL)|(?(?=\:\w+)\:\w+|\0)(*SKIP)(*FAIL)|(?(?=\[(?(?=\s+)\s+)\w+)\[(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\=(?(?=\s+)\s+)\w+)\=(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\"(?(?=\s+)\s+)\w+)\"(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(?(?=\'(?(?=\s+)\s+)\w+)\'(?(?=\s+)\s+)\w+|\0)(*SKIP)(*FAIL)|(\w+)/';

			$pattern_all_ids_HTML = '/id\b(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)(?=\")/';
			$pattern_all_classes_HTML = '/class\b(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"(?(?=\s+)\s+)(.*?)(?(?=\s+)\s+)(?=\")/';
			$pattern_all_tags_HTML = '/<(?(?=\s+)\s+)(\w+)/';

			/*
			 * filter result beacause of variables in the string
			 * e.g. => <div class="text ' + test + ' text-size-normal">
			 */
			$pattern_var_in_string_JS = '/(?:\'|\")(?(?=\s+)\s+)\+.+?(?=\'|\")(?:\'|\")/';

			$pattern_get_id_JS = '/(?:getElementById\b)\(.*?[\"\'](?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)[\"\']/';
			$pattern_get_class_JS = '/(?:getElementsByClassName\b)\(.*?[\"\'](?(?=\s+)\s+)([\w+\-]+)(?(?=\s+)\s+)[\"\']/';

			$pattern_add_rem_hasClass_JQuery = '/(?:\.addClass\b|\.hasClass\b|\.removeClass\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)(.+?)(?=\"|\')[\"\']\)/';
			$pattern_selector_all_JQuery = '/(?:\$)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)(?(?=[a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\'\"\_\d]+?)[a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\'\"\_\d]+?)(?=\#|\.)(?|([\#\.a-zA-Z0-9\*\s\-\:\>\[\]\~\+\*\,\|\=\$\^\"\'\_\d\(\)]+?))(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)\)/';

			$pattern_filter_id_JQuery = '/\#[\w\-]+/';
			$pattern_filter_class_JQuery = '/\.[\w\-]+/';

			$pattern_rem_hasClass_KSJS = '/(?:removeClass\b|hasClass\b)\(.*?(?=\,).*?(?=\'|\")(?:\'|\")(?(?=\s+)\s+)(.*)(?=\'|\")/';
			$pattern_addClass_KSJS = '/(?:addClass\b\(.*?(?(?=\s+)\s+).*\s+(?(?=\s+)\s+).*(?(?=\s+)\s+)(?=(?:class)).*(?(?=class:)class\:(?(?=\s+)\s+)(?:\'|\")(.*)(?=\'|\")|\0))/';
			$pattern_id_in_object_KSJS = '/(?:id\b)\:(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)/';
			$pattern_cl_in_object_KSJS = '/(?:cl\b)\:(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)(?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)/';

			$pattern_gcl_KSJS = '/(?:gcl\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)[\"\'](?(?=\s+)\s+)\)/';
			$pattern_gid_KSJS = '/(?:gid\b)\((?(?=\s+)\s+)[\"\'](?(?=\s+)\s+)([\w\-]+)[\"\'](?(?=\s+)\s+)\)/';

			if ($SCSS_content !== null)
			{
				preg_match_all( $pattern_all_ids_classes_tags_SCSS, $SCSS_content, $SCSS_matches );
				$SCSS_match_result = str_replace(',,', ',', preg_replace( '/\s+/', ',', join( ',', $SCSS_matches[1] )));

				preg_match_all( $pattern_all_ids_SCSS, $SCSS_match_result, $SCSS_ids );
				preg_match_all( $pattern_all_classes_SCSS, $SCSS_match_result, $SCSS_classes );
				preg_match_all( $pattern_all_tags_SCSS, $SCSS_match_result, $SCSS_tags );

				foreach( $SCSS_ids as $id_key => $id_value )
				{
					$SCSS_ids[$id_key] = str_replace( '#', '', $id_value );
				}
				$SCSS_ids = array_filter( array_unique( helper::flatten_array( $SCSS_ids ) ), function($value) { return $value !== ''; } );

				foreach( $SCSS_classes as $class_key => $class_value )
				{
					$SCSS_classes[$class_key] = str_replace( '.', '', $class_value );
				}
				$SCSS_classes = array_filter( array_unique( helper::flatten_array( $SCSS_classes ) ), function($value) { return $value !== ''; } );

				$SCSS_tags = array_filter( array_unique( helper::flatten_array( $SCSS_tags ) ), function($value) { return $value !== ''; } );
			}

			if ($HTML_content !== null)
			{
				preg_match_all( $pattern_all_ids_HTML, $HTML_content, $HTML_ids );
				preg_match_all( $pattern_all_classes_HTML, $HTML_content, $HTML_class_match );
				/*workaround for whitespaces in HTML e.g. class="class    class class"*/
				preg_match_all('/(?|(.+?)(?:\,)|(.+))/', preg_replace( '/\s+/', ',', join(',',array_filter($HTML_class_match[1], function($value) { return $value !== ''; } ))), $HTML_classes);
				preg_match_all( $pattern_all_tags_HTML, $HTML_content, $HTML_tags );

				$HTML_ids = array_filter( array_unique( helper::flatten_array( $HTML_ids[1] ) ), function($value) { return $value !== ''; } );

				$HTML_classes = array_unique( $HTML_classes[1] );

				$HTML_tags = array_filter( array_unique( helper::flatten_array( $HTML_tags[1] ) ), function($value) { return $value !== ''; } );
			}

			if ($JS_content !== null)
			{
				preg_match_all( $pattern_get_id_JS, $JS_content, $JS_get_ids );
				$JS_get_ids = array_filter( array_unique( helper::flatten_array( $JS_get_ids[1] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_get_class_JS, $JS_content, $JS_get_classes );
				$JS_get_classes = array_filter( array_unique( helper::flatten_array( $JS_get_classes[1] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_add_rem_hasClass_JQuery, $JS_content, $JS_add_rem_hasClass_match_JQuery );
				preg_match_all( '/[\w\-]+/', join(',', $JS_add_rem_hasClass_match_JQuery[1]), $JS_add_rem_hasClass_JQuery);

				$JS_add_rem_hasClass_JQuery = array_filter( array_unique( helper::flatten_array( $JS_add_rem_hasClass_JQuery[0] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_selector_all_JQuery, $JS_content, $JS_selector_all_JQuery );

				preg_match_all( $pattern_filter_id_JQuery, join(',', $JS_selector_all_JQuery[1]), $JS_selector_id_jQuery );
				foreach( $JS_selector_id_jQuery as $id_key => $id_value )
				{
					$JS_selector_id_jQuery[$id_key] = str_replace( '#', '', $id_value );
				}
				$JS_selector_id_jQuery = array_filter( array_unique( helper::flatten_array( $JS_selector_id_jQuery[0] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_filter_class_JQuery, join(',', $JS_selector_all_JQuery[1]), $JS_selector_class_jQuery );
				foreach( $JS_selector_class_jQuery as $class_key => $class_value )
				{
					$JS_selector_class_jQuery[$class_key] = str_replace( '.', '', $class_value );
				}
				$JS_selector_class_jQuery = array_filter( array_unique( helper::flatten_array( $JS_selector_class_jQuery[0] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_rem_hasClass_KSJS, $JS_content, $JS_rem_hasClass_KSJS );
				$JS_rem_hasClass_KSJS = array_filter( array_unique( helper::flatten_array( $JS_rem_hasClass_KSJS[1] ) ), function($value) { return $value !== ''; } );
				preg_match_all( $pattern_addClass_KSJS, $JS_content, $JS_addClass_KSJS );
				$JS_addClass_KSJS = array_filter( array_unique( helper::flatten_array( $JS_addClass_KSJS[1] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_id_in_object_KSJS, $JS_content, $JS_id_in_object_KSJS );
				$JS_id_in_object_KSJS = array_filter( array_unique( helper::flatten_array( $JS_id_in_object_KSJS[1] ) ), function($value) { return $value !== ''; } );
				preg_match_all( $pattern_cl_in_object_KSJS, $JS_content, $JS_cl_in_object_KSJS );
				$JS_cl_in_object_KSJS = array_filter( array_unique( helper::flatten_array( $JS_cl_in_object_KSJS[1] ) ), function($value) { return $value !== ''; } );

				preg_match_all( $pattern_gid_KSJS, $JS_content, $JS_gid_KSJS );
				$JS_gid_KSJS = array_filter( array_unique( helper::flatten_array( $JS_gid_KSJS[1] ) ), function($value) { return $value !== ''; } );
				preg_match_all( $pattern_gcl_KSJS, $JS_content, $JS_gcl_KSJS );
				$JS_gcl_KSJS = array_filter( array_unique( helper::flatten_array( $JS_gcl_KSJS[1] ) ), function($value) { return $value !== ''; } );
			}

			if ($HTML_content !== null && $SCSS_content !== null && $JS_content !== null)
			{
				return [
					[
						$SCSS_ids,
						$SCSS_classes,
						$SCSS_tags
					],
					[
						$HTML_ids,
						$HTML_classes,
						$HTML_tags
					],
					[
						$JS_ids = [
							$JS_get_ids,
							$JS_selector_id_jQuery,
							$JS_id_in_object_KSJS,
							$JS_gid_KSJS
						],
						$JS_classes = [
							$JS_get_classes,
							$JS_add_rem_hasClass_JQuery,
							$JS_selector_class_jQuery,
							$JS_rem_hasClass_KSJS,
							$JS_addClass_KSJS,
							$JS_cl_in_object_KSJS,
							$JS_gcl_KSJS
						]
					]
				];
			}
			else
			{
				if ($HTML_content !== null)
				{
					return [
						$HTML_ids,
						$HTML_classes,
						$HTML_tags
					];
				}
				if ($SCSS_content !== null)
				{
					return [
						$SCSS_ids,
						$SCSS_classes,
						$SCSS_tags
					];
				}
				if ($JS_content !== null)
				{
					return [
						$JS_ids = [
							$JS_get_ids,
							$JS_selector_id_jQuery,
							$JS_id_in_object_KSJS,
							$JS_gid_KSJS
						],
						$JS_classes = [
							$JS_get_classes,
							$JS_add_rem_hasClass_JQuery,
							$JS_selector_class_jQuery,
							$JS_rem_hasClass_KSJS,
							$JS_addClass_KSJS,
							$JS_cl_in_object_KSJS,
							$JS_gcl_KSJS
						]
					];
				}

			}
		}

	}

	/*
	 * filter CSS, JS, and HTML for unused id, class or tag selectors
	 *
	 */
	class unusedCSS
	{

		public static function find_unused_CSS($all_IDs_CLASSes_TAGs, $HTML_file, $SCSS_file, $JS_file, $project_directory)
		{
			$unused_CSS = [];

			$SCSS_ids = $all_IDs_CLASSes_TAGs[0][0];
			$SCSS_classes = $all_IDs_CLASSes_TAGs[0][1];
			$SCSS_tags = $all_IDs_CLASSes_TAGs[0][2];

			$HTML_ids = $all_IDs_CLASSes_TAGs[1][0];
			$HTML_classes = $all_IDs_CLASSes_TAGs[1][1];
			$HTML_tags = $all_IDs_CLASSes_TAGs[1][2];

			$JS_ids = $all_IDs_CLASSes_TAGs[2][0];
			$JS_classes = $all_IDs_CLASSes_TAGs[2][1];

			$JS_ids_flatten_array = helper::flatten_array( $JS_ids );
			$JS_class_flatten_array = helper::flatten_array( $JS_classes );

			$SCSS_content = file_get_contents( $SCSS_file );
			$current_SCSS_IDs_CLASSes_TAGs = helper::find_all_IDs_CLASSes_TAGs(null, $SCSS_content, null);
			$current_SCSS_ids = $current_SCSS_IDs_CLASSes_TAGs[0];
			$current_SCSS_classes = $current_SCSS_IDs_CLASSes_TAGs[1];
			$current_SCSS_tags = $current_SCSS_IDs_CLASSes_TAGs[2];

			$JS_content = file_get_contents( $JS_file );
			$current_JS_IDs_CLASSes_TAGs = helper::find_all_IDs_CLASSes_TAGs(null, null, $JS_content);
			$current_JS_ids = $current_JS_IDs_CLASSes_TAGs[0];
			$current_JS_classes = $current_JS_IDs_CLASSes_TAGs[1];

			$current_JS_ids_flatten_array = helper::flatten_array( $current_JS_ids );
			$current_JS_class_flatten_array = helper::flatten_array( $current_JS_classes );

			if ( count($SCSS_ids) > 0 )
			{
				foreach ( $SCSS_ids as $SCSS_id_search_value )
				{
					if ( !in_array( $SCSS_id_search_value, $HTML_ids ) &&
						 !in_array( $SCSS_id_search_value, $JS_ids_flatten_array ) &&
						  in_array( $SCSS_id_search_value, $current_SCSS_ids ) )
					{
						array_push( $unused_CSS, $unused = (object) [
							"identifier" => '#' . $SCSS_id_search_value,
							"message" => 'CSS id not found in HTML or JS file.',
							"line" => unusedCSS::find_row('\#', $SCSS_id_search_value, $SCSS_file),
							"directory" => str_replace ( $project_directory , '', $SCSS_file )
						]);
					}
				}
			}

			if ( count($SCSS_classes) > 0 )
			{
				foreach ( $SCSS_classes as $SCSS_class_search_value )
				{
					if ( !in_array( $SCSS_class_search_value, $HTML_classes ) &&
						 !in_array( $SCSS_class_search_value, $JS_class_flatten_array )  &&
						  in_array( $SCSS_class_search_value, $current_SCSS_classes ) )
					{
						array_push( $unused_CSS, $unused = (object) [
							"identifier" => '.' . $SCSS_class_search_value,
							"message" => "CSS class not found in HTML or JS file.",
							"line" => unusedCSS::find_row('\.', $SCSS_class_search_value, $SCSS_file),
							"directory" => str_replace ( $project_directory , '', $SCSS_file )
						]);
					}
				}
			}

			if ( count($SCSS_tags) > 0 )
			{
				foreach ( $SCSS_tags as $SCSS_tag_search_value )
				{
					if ( !in_array( $SCSS_tag_search_value, $HTML_tags ) &&
						  $SCSS_tag_search_value !== 'body' &&
						  $SCSS_tag_search_value !== 'html'  &&
 						  in_array( $SCSS_tag_search_value, $current_SCSS_tags ))
					{
						array_push( $unused_CSS, $unused = (object) [
							"identifier" => $SCSS_tag_search_value,
							"message" => "CSS tag not found in HTML file.",
							"line" => unusedCSS::find_row('\<', $SCSS_tag_search_value, $SCSS_file),
							"directory" => str_replace ( $project_directory , '', $SCSS_file )
						]);
					}
				}
			}

			if ( count($HTML_ids) > 0 )
			{
				foreach ( $HTML_ids as $HTML_id_search_value )
				{
					if ( !in_array( $HTML_id_search_value, $SCSS_ids ) &&
						 !in_array( $HTML_id_search_value, $JS_ids_flatten_array ))
					{
						array_push( $unused_CSS, $unused = (object) [
							"identifier" => 'id="' . $HTML_id_search_value . '"',
							"message" => "HTML id not found in SCSS or JS file.",
							"line" => unusedCSS::find_row('id\b', $HTML_id_search_value, $HTML_file),
							"directory" => str_replace ( $project_directory , '', $HTML_file )
						]);
					}
				}
			}

			if ( count($HTML_classes) > 0 )
			{
				foreach ( $HTML_classes as $HTML_class_search_value )
				{
					if ( !in_array( $HTML_class_search_value, $SCSS_classes ) &&
						 !in_array( $HTML_class_search_value, $JS_class_flatten_array ) )
					{
						array_push( $unused_CSS, $unused = (object) [
							"identifier" => 'class="' . $HTML_class_search_value . '"',
							"message" => "HTML class not found in SCSS or JS file.",
							"line" => unusedCSS::find_row('class\b', $HTML_class_search_value, $HTML_file),
							"directory" => str_replace ( $project_directory , '', $HTML_file )
						]);
					}
				}
			}

			if ( count($JS_ids) > 0 )
			{
				if ( count($JS_ids[0]) > 0 )
				{
					foreach ( $JS_ids[0] as $JS_get_id_search_value )
					{
						if ( !in_array( $JS_get_id_search_value, $SCSS_ids ) &&
							 !in_array( $JS_get_id_search_value, $HTML_ids )  &&
							  in_array( $JS_get_id_search_value, $current_JS_ids_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => '.getElementById("' . $JS_get_id_search_value . '")',
								"message" => "CSS id in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('\.getElementById\b\(', $JS_get_id_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_ids[1]) > 0 )
				{
					foreach ( $JS_ids[1] as $JS_selector_id_jQuery_search_value )
					{
						if ( !in_array( $JS_selector_id_jQuery_search_value, $SCSS_ids ) &&
							 !in_array( $JS_selector_id_jQuery_search_value, $HTML_ids ) &&
							  in_array( $JS_selector_id_jQuery_search_value, $current_JS_ids_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => '$("#' . $JS_selector_id_jQuery_search_value . '")',
								"message" => "CSS id in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('\$\(', '\#' . $JS_selector_id_jQuery_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_ids[2]) > 0 )
				{
					foreach ( $JS_ids[2] as $JS_id_in_object_KSJS_search_value )
					{
						if ( !in_array( $JS_id_in_object_KSJS_search_value, $SCSS_ids ) &&
							 !in_array( $JS_id_in_object_KSJS_search_value, $HTML_ids ) &&
							  in_array( $JS_id_in_object_KSJS_search_value, $current_JS_ids_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'id: "' . $JS_id_in_object_KSJS_search_value . '"',
								"message" => "CSS id in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('id\b\:', '(?:\'|\")' . $JS_id_in_object_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_ids[3]) > 0 )
				{
					foreach ( $JS_ids[3] as $JS_gid_KSJS_search_value )
					{
						if ( !in_array( $JS_gid_KSJS_search_value, $SCSS_ids ) &&
							 !in_array( $JS_gid_KSJS_search_value, $HTML_ids ) &&
							  in_array( $JS_gid_KSJS_search_value, $current_JS_ids_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'gid("' . $JS_gid_KSJS_search_value . '")',
								"message" => "CSS id in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('gid\b\(', '(?:\'|\")' . $JS_gid_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
			}

			if ( count($JS_classes) > 0 )
			{
				if ( count($JS_classes[0]) > 0 )
				{
					foreach ( $JS_classes[0] as $JS_get_class_search_value )
					{
						if ( !in_array( $JS_get_class_search_value, $SCSS_classes ) &&
							 !in_array( $JS_get_class_search_value, $HTML_classes ) &&
							  in_array( $JS_get_class_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => '.getElementsByClassName("' . $JS_get_class_search_value . '")',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('\.getElementsByClassName\b\(', $JS_get_class_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[1]) > 0 )
				{
					foreach ( $JS_classes[1] as $JS_add_rem_hasClass_JQuery_search_value )
					{
						if ( !in_array( $JS_add_rem_hasClass_JQuery_search_value, $SCSS_classes ) &&
							 !in_array( $JS_add_rem_hasClass_JQuery_search_value, $HTML_classes ) &&
							  in_array( $JS_add_rem_hasClass_JQuery_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => '.addClass | .hasClass | .removeClass("' . $JS_add_rem_hasClass_JQuery_search_value . '")',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('(?:\.addClass\b|\.hasClass\b|\.removeClass\b)', $JS_add_rem_hasClass_JQuery_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[2]) > 0 )
				{
					foreach ( $JS_classes[2] as $JS_selector_class_jQuery_search_value )
					{
						if ( !in_array( $JS_selector_class_jQuery_search_value, $SCSS_classes ) &&
							 !in_array( $JS_selector_class_jQuery_search_value, $HTML_classes ) &&
							  in_array( $JS_selector_class_jQuery_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => '$(".' . $JS_selector_class_jQuery_search_value . '")',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('(?:\.addClass\b|\.hasClass\b|\.removeClass\b)', $JS_selector_class_jQuery_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[3]) > 0 )
				{
					foreach ( $JS_classes[3] as $JS_rem_hasClass_KSJS_search_value )
					{
						if ( !in_array( $JS_rem_hasClass_KSJS_search_value, $SCSS_classes ) &&
							 !in_array( $JS_rem_hasClass_KSJS_search_value, $HTML_classes ) &&
							  in_array( $JS_rem_hasClass_KSJS_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'removeClass | hasClass("' . $JS_rem_hasClass_KSJS_search_value . '")',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('(?:hasClass\b|removeClass\b)', $JS_rem_hasClass_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[4]) > 0 )
				{
					foreach ( $JS_classes[4] as $JS_addClass_KSJS_search_value )
					{
						if ( !in_array( $JS_addClass_KSJS_search_value, $SCSS_classes ) &&
							 !in_array( $JS_addClass_KSJS_search_value, $HTML_classes ) &&
							  in_array( $JS_addClass_KSJS_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'class: "' . $JS_addClass_KSJS_search_value . '"',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('class\b\:', $JS_addClass_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[5]) > 0 )
				{
					foreach ( $JS_classes[5] as $JS_cl_in_object_KSJS_search_value )
					{
						if ( !in_array( $JS_cl_in_object_KSJS_search_value, $SCSS_classes ) &&
							 !in_array( $JS_cl_in_object_KSJS_search_value, $HTML_classes ) &&
							  in_array( $JS_cl_in_object_KSJS_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'cl: "' . $JS_cl_in_object_KSJS_search_value . '"',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('cl\b\:', $JS_cl_in_object_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
				if ( count($JS_classes[6]) > 0 )
				{
					foreach ( $JS_classes[6] as $JS_gcl_KSJS_search_value )
					{
						if ( !in_array( $JS_gcl_KSJS_search_value, $SCSS_classes ) &&
							 !in_array( $JS_gcl_KSJS_search_value, $HTML_classes ) &&
							  in_array( $JS_gcl_KSJS_search_value, $current_JS_class_flatten_array ))
						{
							array_push( $unused_CSS, $unused = (object) [
								"identifier" => 'gcl("' . $JS_gcl_KSJS_search_value . '")',
								"message" => "CSS class in JS file not found in HTML or SCSS file.",
								"line" => unusedCSS::find_row('gcl\b\(', $JS_gcl_KSJS_search_value, $JS_file),
								"directory" => str_replace ( $project_directory , '', $JS_file )
							]);
						}
					}
				}
			}

			return $unused_CSS;
		}

		/*
		 * search each row number of match
		 *
		 */
		public static function find_row( $identifier, $search, $inputFile )
		{
			$line_number = false;
			$lines = [];

			if ( $identifier === '\#' || $identifier === '\.' )
			{
				$pattern = '/' . $identifier . $search . '\b/';
			}
			elseif ( $identifier === '\<' )
			{
				$pattern = '/(?(?=\#' . $search . '\b)\#' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\.' . $search . '\b)\.' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\-' . $search . '\b)\-' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=' . $search . '\b\-)' . $search . '\b\-|\0)(*SKIP)(*FAIL)|(?(?=\_' . $search . '\b)\_' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\w' . $search . '\b)\w' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\:' . $search . '\b)\:' . $search . '\b|\0)(*SKIP)(*FAIL)|' . $search . '\b/';
			}
			else
			{
				$pattern = '/' . $identifier . '.*?' . $search . '\b/';
			}

			if ($handle = fopen($inputFile, "r"))
			{
				$count = 0;

				while (($line = fgets($handle)))
				{
					$count++;
					$line_number = (preg_match( $pattern, $line ) !== 0) ? $count : $line_number;

					if ($line_number !== false && $line_number === $count)
					{
						array_push($lines, $line_number);
					}
				}
				fclose($handle);

				return $lines;
			}

		}
	}
