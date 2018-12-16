<?php
    /**
     * create files and directories
     */
    class filesAndDir
    {

        public static function build_project_dir( string $abs_path, string $project_folder, array $rel_path = ['client','product'] )
        {
            $directory = '';
            if ( is_Array($rel_path) )
            {
                foreach ( $rel_path as $format )
                {
                    if ( is_String($format) )
                    {
                        if ( !file_exists( $abs_path . $project_folder . $directory . '/' . $format ) )
                        {
                            mkdir( $abs_path . $project_folder . $directory . '/' . $format );
                        }
                        $directory .= '/' . $format;
                        continue;
                    }
                }
            }
            return $project_folder . $directory;
        }

        public static function build_project_conf_file( string $path, stdClass $project )
        {
            $project_file = fopen( $path . '/_project_config.json', 'w' );
            fwrite( $project_file, json_encode($project, JSON_PRETTY_PRINT));
            fclose($project_file);
            /* WORKAROUND: custom JSON prettify */
            $project_content = file_get_contents( $path . '/_project_config.json' );
            $project_content = preg_replace_callback(
                            '/' . REGEX_JSON_PRETTIFY . '/',
                            function ($matches) {
                                return preg_replace('/' . REGEX_SPACES . '/', '', $matches[0]);
                            },
                            $project_content
                        );
            file_put_contents( $path . '/_project_config.json', $project_content );
        }

        public static function build_banner_formats_data( string $abs_path, array $banner = [], string $directory = '', array $global_files =[] )
        {
            $directories = [];

            if ( is_Array($banner) )
            {

                $check = helper::check_duplicates($banner);
                $currFormats = [];
                $const_dir = [];
                $namespace = false;

                $length = count($banner);
                while ($length--)
                {
                    if ($banner[$length] instanceof stdClass) {
                        continue;
                    }
                    if ( helper::array_values_int($banner[$length]) === false ) {
                        $const_dir[] = helper::get_const_dir($abs_path, $banner[$length]);
                        if (in_Array('NAMESPACE', $banner[$length])) {
                            $namespace = true;
                        }
                    }
                }
                $const_dir[] = helper::get_const_dir($abs_path, $global_files);
                $const_dir = helper::flatten_array($const_dir);
                if (in_Array('NAMESPACE', $global_files))
                {
                    $namespace = true;
                }

                foreach ( $banner as $format )
                {

                    if ( is_Array($format) && helper::array_values_int($format) )
                    {

                        if ( !file_exists( $abs_path . $directory . '/' . join($format, 'x') ) )
                        {
                            mkdir( $abs_path . $directory . '/' . join($format, 'x') );

                            $directories[] = $directory . '/' . join($format, 'x');
                            $banner_config = self::build_banner_conf_file($abs_path, $directory . '/' . join($format, 'x'), $format, $const_dir, $namespace);

                            self::build_banner_files($banner_config);
                            self::set_base_scss($banner_config);
                            self::copy_global_files($abs_path, $banner_config);
                            continue;
                        }
                        else
                        {
                            $currFormats[] = join($format, 'x');
                            $subject = join($currFormats, ',');
                            $count = preg_match_all('/' . join($format, 'x') . '\b/', $subject);

                            if ( in_Array(helper::build_duplicates(join($format, 'x'), $count), $check) &&
                                 !file_exists( $abs_path . $directory . '/' . helper::build_duplicates($format, $count) ) )
                            {
                                $dub_val = helper::build_duplicates($format, $count);
                                mkdir( $abs_path . $directory . '/' . $dub_val );

                                $directories[] = $directory . '/' . $dub_val;
                                $banner_config = self::build_banner_conf_file($abs_path, $directory . '/' . $dub_val, $format, $const_dir, $namespace);

                                self::build_banner_files($banner_config);
                                self::set_base_scss($banner_config);
                                self::copy_global_files($abs_path, $banner_config);
                                continue;
                            }

                            if ( ($count - 1) > 0 )
                            {
                                $dub_val = helper::build_duplicates($format, ($count - 1));
                                $directories[] = $directory . '/' . $dub_val;

                                $banner_config = self::build_banner_conf_file($abs_path, $directory . '/' . $dub_val, $format, $const_dir, $namespace);
                                self::set_base_scss($banner_config);
                                self::copy_global_files($abs_path, $banner_config);
                            }
                            else
                            {
                                $directories[] = $directory . '/' . join($format, 'x');

                                $banner_config = self::build_banner_conf_file($abs_path, $directory . '/' . join($format, 'x'), $format, $const_dir, $namespace);
                                self::set_base_scss($banner_config);
                                self::copy_global_files($abs_path, $banner_config);
                            }

                        }
                    }

                    if ($format instanceof stdClass)
                    {

                        if ( !file_exists( $abs_path . $directory . '/' . key($format) ) )
                        {
                            mkdir( $abs_path . $directory . '/' . key($format) );
                            self::build_banner_formats_data( $abs_path, $format->{key($format)}, $directory . '/' . key($format), $global_files );
                            continue;
                        }
                        else
                        {
                            $currFormats[] = key($format);
                            $subject = join($currFormats, ',');
                            $count = preg_match_all('/' . key($format) . '\b/', $subject);

                            if ( in_Array(helper::build_duplicates(key($format), $count), $check) &&
                                 !file_exists( $abs_path . $directory . '/' . helper::build_duplicates(key($format), $count) ) )
                            {
                                $dub_val = helper::build_duplicates(key($format), $count);

                                mkdir( $abs_path . $directory . '/' . $dub_val );
                                self::build_banner_formats_data( $abs_path, $format->{key($format)}, $directory . '/' . $dub_val, $global_files );
                                continue;
                            }

                            if ( ($count - 1) > 0 )
                            {
                                $dub_val = helper::build_duplicates(key($format), ($count -1));

                                self::build_banner_formats_data( $abs_path, $format->{key($format)}, $directory . '/' . $dub_val, $global_files );
                                continue;
                            }
                            else
                            {
                                self::build_banner_formats_data( $abs_path, $format->{key($format)}, $directory . '/' . key($format), $global_files );
                                continue;
                            }
                        }

                    }
                }
            }

            array_push($GLOBALS['banner_directories'], $directories);
        }

        public static function build_banner_conf_file(string $abs_path, string $path, array $format, array $files, bool $namespace)
        {
            $template = null;
            $js_animations = [];
            $base_scss = [];
            $global_markups = [];
            $global_files = [];

            foreach ($files as $file) {
                if (preg_match('/'. REGEX_TEMPLATE . '/', $file)) {
                    $template = $file;
                }
                if (preg_match('/'. REGEX_ANIMATION . '/', $file)) {
                    $js_animations[] = $file;
                }
                if (preg_match('/'. REGEX_BASE_SCSS . '/', $file)) {
                    $base_scss[] = $file;
                }
                if (preg_match('/'. REGEX_MARKUP . '/', $file)) {
                    $global_markups[] = $file;
                }
                if (preg_match('/'. REGEX_FILES . '/', $file)) {
                    $global_files[] = $file;
                }
            }

            $json = (object) [
                'banner_config' => (object) [
                    'banner_dir' => $path,
                    'project_dir' => $GLOBALS['project_dir'],
                    'width' => $format[0],
                    'height' => $format[1],
                    'namespace' => $namespace,
                    'template' => $template,
                    'js_animations' => $js_animations,
                    'base_scss' => $base_scss,
                    'global_markups' => $global_markups,
                    'global_files' => $global_files,

                ]
            ];

            $banner_config = fopen( $abs_path . $path . '/banner_config.json', 'w' );
            fwrite( $banner_config, json_encode($json, JSON_PRETTY_PRINT));
            fclose($banner_config);

            return $json;

        }

        public static function build_banner_files(stdClass $config)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
            $dir = $abs_path . $config->banner_config->banner_dir;

            mkdir( $dir . '/assets/' );
            mkdir( $dir . '/fallback/' );
            mkdir( $dir . '/js/' );
            mkdir( $dir . '/scss/' );

            $index_file = fopen( $dir . '/index.php', 'w' );
            $scss_file = fopen( $dir . '/scss/styles.scss', 'w' );
            $js_file = fopen( $dir . '/js/functions.js', 'w' );

            foreach ($config->banner_config->global_markups as $markup) {

                if ( file_exists( $abs_path . GLOBAL_MARKUP . $markup) )
                {
                    $content = file_get_contents($abs_path . GLOBAL_MARKUP . $markup );
                    $content = helper::match_placeholder($content, $config);
                }

                if ( pathinfo($markup)['extension'] === 'html' )
                {
                    fwrite( $index_file, $content);
                }
                if ( pathinfo($markup)['extension'] === 'scss' )
                {
                    fwrite( $scss_file, $content);
                }
                if ( pathinfo($markup)['extension'] === 'js' )
                {
                    fwrite( $js_file, $content);
                }
            }

            fclose( $index_file );
            fclose( $scss_file );
            fclose( $js_file );

        }

        public static function copy_global_files(string $abs_path, stdClass $config)
        {
            $dir = $abs_path . $config->banner_config->banner_dir;
            $global_files = $config->banner_config->global_files;

            foreach ($global_files as $file)
            {

                if ( file_exists( $abs_path . GLOBAL_FILES . $file) )
                {
                    $content = file_get_contents($abs_path . GLOBAL_FILES . $file );
                    $content = helper::match_placeholder($content, $config);
                }

                if ( pathinfo($file)['extension'] === 'js' )
                {
                    if (!file_exists($dir . '/js' . $file) )
                    {
                        $js_file = fopen( $dir . '/js' . $file, 'w');
                        fwrite( $js_file, $content);
                        fclose( $js_file );
                    }

                }

                if ( pathinfo($file)['extension'] === 'scss' )
                {
                    if (!file_exists($dir . '/scss' . $file) )
                    {
                        $pattern = '/\@import\b.*' . basename($file, '.scss') . '\.scss\b.*(?=\;)\;\r\n\r\n/';
                        if (!preg_match($pattern, file_get_contents( $dir . '/scss/styles.scss', 'w' )))
                        {
                            $set_import = "@import \"/" . basename($file, '.scss') . "\";\r\n\r\n";
                            $set_import .= file_get_contents( $dir . '/scss/styles.scss', 'w' );
                            file_put_contents($dir . '/scss/styles.scss', $set_import);

                            $scss_file = fopen( $dir . '/scss' . $file, 'w');
                            fwrite( $scss_file, $content);
                            fclose( $scss_file );
                        }
                    }
                }

            }

            $scss_global_files = helper::custom_glob($abs_path . GLOBAL_FILES, '.scss');
            $js_global_files = helper::custom_glob($abs_path . GLOBAL_FILES, '.js');
            $scss_files = helper::custom_glob($dir . '/scss', '.scss');
            $js_files = helper::custom_glob($dir . '/js', '.js');

            foreach ( $scss_files as $file )
            {
                if ( !in_array( '/' . basename($file), $global_files ) &&
                     in_array( '/' . basename($file), $scss_global_files) &&
                     basename($file) !== 'styles.scss' )
                {
                    unlink($file);
                    $pattern = '/\@import\b.*' . basename($file, '.scss') . '\.scss\b.*(?=\;)\;\r\n\r\n/';
                    $remove_import = file_get_contents( $dir . '/scss/styles.scss', 'w' );
                    $remove_import = preg_replace( $pattern, '' , $remove_import );
                    file_put_contents($dir . '/scss/styles.scss', $remove_import);
                }
            }

            foreach ( $js_files as $file )
            {
                if ( !in_array( '/' . basename($file), $global_files ) &&
                     in_array( '/' . basename($file), $js_global_files) &&
                     basename($file) !== 'functions.js' )
                {
                    unlink($file);
                }
            }
        }

        public static function set_base_scss(stdClass $config)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
            $dir = $abs_path . $config->banner_config->banner_dir;
            $base_scss = $config->banner_config->base_scss;

            foreach ($base_scss as $file)
            {
                if ( pathinfo($file)['extension'] === 'scss' )
                {
                    if ( file_exists($abs_path . CLIENT_BASE_SCSS . $file) )
                    {
                        $pattern = '/\@import\b.*' . basename($file, '.scss') . '\.scss\b.*(?=\;)\;\r\n\r\n/';
                        if (!preg_match($pattern, file_get_contents( $dir . '/scss/styles.scss', 'w' )))
                        {
                            $set_import = "@import \"/" . basename($file) . "\";\r\n\r\n";
                            $set_import .= file_get_contents( $dir . '/scss/styles.scss', 'w' );
                            file_put_contents($dir . '/scss/styles.scss', $set_import);
                        }
                    }
                }
            }

            $scss_files = helper::custom_glob($abs_path . CLIENT_BASE_SCSS, '.scss');
            foreach ( $scss_files as $file )
            {
                $pattern = '/\@import\b.*' . basename($file, '.scss') . '\.scss\b.*(?=\;)\;\r\n\r\n/';
                if ( preg_match($pattern, file_get_contents( $dir . '/scss/styles.scss', 'w' )) &&
                     !in_array('/' . basename($file), $base_scss) )
                {
                    $remove_import = file_get_contents( $dir . '/scss/styles.scss', 'w' );
                    $remove_import = preg_replace( $pattern, '' , $remove_import );
                    file_put_contents($dir . '/scss/styles.scss', $remove_import);
                }
            }
        }

        public static function bundle_banner_data(stdClass $config)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
            $dir = $abs_path . $config->banner_config->banner_dir;
            $project_dir = $abs_path . $config->banner_config->project_dir;

            $banner_data = self::build_output($config);

            $unusedCSS = new unusedCSS(
                $banner_data->selectors,
                $banner_data->html_dirs,
                $banner_data->scss_dirs,
                $banner_data->js_dirs,
                $project_dir,
                $dir
            );

            return $unusedCSS->unusedCSS;
        }

        public static function build_output(stdClass $config)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
            $dir = $abs_path . $config->banner_config->banner_dir;
            $namespace = $config->banner_config->namespace;

            $selectors = null;
            $output_files = helper::custom_glob( $dir . '/_output' );
            $assets_files = helper::custom_glob( $dir . '/assets' );
            $js_files = helper::custom_glob( $dir . '/js', '.js' );
            $js_animations = $config->banner_config->js_animations;
            $scss_files = helper::custom_glob( $dir . '/scss', '.scss' );

            if ( count( $output_files ) > 0 )
            {
                foreach ( $output_files as $output )
                {
                    unlink( $dir . '/_output' . $output );
                }
            }
            if ( !file_exists( $dir . '/_output' ) )
            {
                mkdir( $dir . '/_output' );
            }
            fclose( fopen( $dir . '/_output/index.html', 'w' ) );

            foreach ( $assets_files as $assets_file )
            {
                copy( $assets_file, str_replace( '/assets' . basename($assets_file), '/_output' . basename($assets_file), $assets_file ) );
            }

            $js_ani = '';
            foreach ( $js_animations as $js_file )
            {
                $js_ani .= file_get_contents( $abs_path . JS_ANIMATION_LIBRARY . $js_file);
            }
            $js = '';
            foreach ( $js_files as $js_file )
            {
                $js .= file_get_contents( $dir . '/js' . $js_file );
            }

            $scss = file_get_contents( $dir . '/scss/styles.scss' );
            $GLOBALS['scss_compiler']->setImportPaths( [ $abs_path . CLIENT_BASE_SCSS, $dir . '/scss' ] );
            $scss = $GLOBALS['scss_compiler']->compile( $scss );

            $html = file_get_contents( $dir . '/index.php' );

            if (COMPRESS_CSS)
            {
                $compressCSS = new compressCSS(
                    $selectors,
                    [$html],
                    [$scss],
                    [$js],
                    $dir
                );

                $selectors = $compressCSS->new_selectors;

                $html = $compressCSS->html;
                $scss = $compressCSS->scss;
                $js = $compressCSS->js;
            }

            if ($namespace)
            {
                $NS_content = new namespaceCSS(
                    $selectors,
                    [$html],
                    [$scss],
                    [$js],
                    $dir
                );

                $selectors = $NS_content->selectors;

                $html = $NS_content->html;
                $scss = $NS_content->scss;
                $js = $NS_content->js;
            }

            $output_html = $html;
            $output_scss = $scss;
            $output_js = new Tholu\Packer\Packer($js_ani . $js, 'Normal', true, false, true);

            if (AUTOPREFIXER)
            {
                $output_scss = $GLOBALS['autoprefixer']->compile($output_scss);
            }

            if (COMPRESS_CSS)
            {
                $selectors = $compressCSS->selectors;
            }

            ob_start();

            print
                '<!DOCTYPE html>' .
                    '<html>' .
                    '<head>' .
                        '<meta charset="utf-8"/>' .
                        '<title>' . $config->banner_config->width . 'x' . $config->banner_config->height . '</title>';

                print '<style type="text/css">' . $output_scss . '</style>';

                print '</head><body>';

                print $output_html;

                print '<script type="text/javascript">' . $output_js->pack() . '</script>';

                print '</body></html>';

            $banner = helper::compress_HTML( ob_get_contents() );
            ob_end_clean();

            file_put_contents( $dir . '/_output/index.html', $banner );

            return (object) [
                'selectors' => $selectors,
                'html_dirs' => [ '/index.php' ],
                'html_cnt' => $html,
                'scss_dirs' => $scss_files,
                'scss_cnt' => $output_scss,
                'js_dirs' => $js_files,
                'js_cnt' => $js
            ];

        }

    }

?>
