<?php
    /**
    * build zip files
    */

    class zip
    {

        public static function build_export(stdClass $data)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);

            if ( file_exists( dirname(__DIR__) . '/export-cache' ) === false )
            {
                mkdir( dirname(__DIR__) . '/export-cache' );
            }
            if ( file_exists( dirname(__DIR__) . '/export-cache/banner-package.zip' ) )
            {
                unlink( dirname(__DIR__) . '/export-cache/banner-package.zip' );
            }

            $zip = new ZipArchive();
            $zip->open( dirname(__DIR__) . '/export-cache/banner-package.zip', ZipArchive::CREATE);

            $fallbacks = [];
            $scripts = array_unique( array_merge( $data->head, $data->body ) );
            if (count($scripts) === 0) {
                $scripts = [''];
            }
            $project_dir = $data->project_dir;

            foreach ($scripts as $script) {
                $zip->addEmptyDir( $script );

                foreach ($data->banner as $banner) {

                    if (file_exists($abs_path . $banner . '/_output/index.html')) {

                        $config = json_decode(file_get_contents($abs_path . $banner . '/banner_config.json'));

                        $head_script = '';
                        if (file_exists($abs_path . AD_SCRIPT_HEAD . '/' . $script . '.php')) {
                            $head_script = helper::match_placeholder(file_get_contents($abs_path . AD_SCRIPT_HEAD . '/' . $script . '.php'), $config);
                        }
                        $body_script = '';
                        if (file_exists($abs_path . AD_SCRIPT_BODY . '/' . $script . '.php')) {
                            $body_script = helper::match_placeholder(file_get_contents($abs_path . AD_SCRIPT_BODY . '/' . $script . '.php'), $config);
                        }

                        $html = file_get_contents($abs_path . $banner . '/_output/index.html');
                        $html = preg_replace_callback( '/<\/head>|<\/body>/', function($match) use($head_script, $body_script) {
                            if ( $match[0] === '</head>' )
                            {
                                return str_replace( $match[0], $head_script . $match[0], $match[0] );
                            }
                            if ( $match[0] === '</body>' )
                            {
                                return str_replace( $match[0], $body_script . $match[0], $match[0] );
                            }
                        }, $html );

                        $assets = array_diff( helper::custom_glob( $abs_path . $banner . '/_output' ), ['/index.html']);

                        $dir_name = str_replace('/', '-', str_replace($project_dir, '', $banner));
                        $zip->addEmptyDir( $script . '/' . substr($dir_name, 1));

                        foreach ( $assets as $file )
                        {
                            $file_dir = $abs_path . $config->banner_config->banner_dir . '/_output' . $file;
                            $zip->addFile( $file_dir, $script . '/' . substr($dir_name, 1) . $file );
                        }

                        $zip->addFromString( $script . '/' . substr($dir_name, 1) . '/index.html', preg_replace( '/\r|\n/', '', $html ));
                    }

                    $fallback = helper::custom_glob( $abs_path . $banner . '/fallback' );
                    array_push($fallbacks, $fallback);
                    helper::flatten_array($fallbacks);
                }

                $zip->addEmptyDir( $script . '/_fallbacks' );
                $fallbacks = helper::flatten_array($fallbacks);
                foreach ( $fallbacks as $fallback )
                {
                    $zip->addFile( $fallback, $script . '/_fallbacks' . $fallback);
                }
            }

            $zip->close();

            print '/app/export-cache/banner-package.zip';
        }

        public static function build_archive(string $dir, string $name, array $files)
        {
            $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);

            if (!file_exists($abs_path . '/banner-archive/' . $name . '.zip'))
            {
                $zip = new ZipArchive();
                $zip->open($abs_path . '/banner-archive/' . $name . '.zip', ZipArchive::CREATE);

                foreach ($files as $file)
                {
                    if (is_dir($file))
                    {
                        $zip->addEmptyDir( str_replace($abs_path . $dir, '', $file) );
                    }
                    else
                    {
                        $zip->addFile( $file, str_replace($abs_path . $dir, '', $file) );
                    }
                }

                $zip->close();
            }

            helper::delete_dir($dir);
        }
    }
