<?php
    /**
    * build zip files
    */

    class zip
    {

        public static function build_export(stdClass $data)
        {

            if ( file_exists( dirname(__DIR__) . '/app-assets/export-cache' ) === false )
            {
                mkdir( dirname(__DIR__) . '/app-assets/export-cache' );
            }
            if ( file_exists( dirname(__DIR__) . '/app-assets/export-cache/banner-package.zip' ) )
            {
                unlink( dirname(__DIR__) . '/app-assets/export-cache/banner-package.zip' );
            }

            $zip = new ZipArchive();
            $zip->open( dirname(__DIR__) . '/app-assets/export-cache/banner-package.zip', ZipArchive::CREATE);

            $fallbacks = [];
            $scripts = array_unique( array_merge( $data->head, $data->body ) );
            if (count($scripts) === 0) {
                $scripts = [''];
            }
            $project_dir = $data->project_dir;

            foreach ($scripts as $script) {
                $zip->addEmptyDir( $script );

                foreach ($data->banner as $banner) {

                    if (file_exists(dirname(__DIR__) . $banner . '/_output/index.html')) {

                        $config = json_decode(file_get_contents(dirname(__DIR__) . $banner . '/banner_config.json'));
                        if (file_exists(dirname(__DIR__) . AD_SCRIPT_HEAD . '/' . $script . '.php')) {
                            $head_script = helper::match_placeholder(file_get_contents(dirname(__DIR__) . AD_SCRIPT_HEAD . '/' . $script . '.php'), $config);
                        }
                        if (file_exists(dirname(__DIR__) . AD_SCRIPT_BODY . '/' . $script . '.php')) {
                            $body_script = helper::match_placeholder(file_get_contents(dirname(__DIR__) . AD_SCRIPT_BODY . '/' . $script . '.php'), $config);
                        }

                        $html = file_get_contents(dirname(__DIR__) . $banner . '/_output/index.html');
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

                        $assets = array_diff( helper::custom_glob( dirname(__DIR__) . $banner . '/_output' ), ['/index.html']);

                        $dir_name = str_replace('/', '-', str_replace($project_dir, '', $banner));
                        $zip->addEmptyDir( $script . '/' . substr($dir_name, 1));

                        foreach ( $assets as $file )
                        {
                            $zip->addFile( $file, $script . '/' . substr($dir_name, 1) . $file );
                        }

                        $zip->addFromString( $script . '/' . substr($dir_name, 1) . '/index.html', preg_replace( '/\r|\n/', '', $html ));
                    }

                    $fallback = helper::custom_glob( dirname(__DIR__) . $banner . '/fallback' );
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

            print '/app-assets/export-cache/banner-package.zip';
        }

        public static function build_archive(string $dir, string $name, array $files)
        {

            if (!file_exists(dirname(__DIR__) . '/banner-archive/' . $name . '.zip'))
            {
                $zip = new ZipArchive();
                $zip->open(dirname(__DIR__) . '/banner-archive/' . $name . '.zip', ZipArchive::CREATE);

                foreach ($files as $file)
                {
                    if (is_dir($file))
                    {
                        $zip->addEmptyDir( str_replace(dirname(__DIR__) . $dir, '', $file) );
                    }
                    else
                    {
                        $zip->addFile( $file, str_replace(dirname(__DIR__) . $dir, '', $file) );
                    }
                }

                $zip->close();
            }

            helper::delete_dir($dir);
        }
    }
