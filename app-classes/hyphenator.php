<?php

/**
* hyphenate data
*/

class hyphenator
{
    public static function replace_text(string $dir, string $identifier, string $ext, string $text)
    {

        $abs_path = dirname(__DIR__);
        $ignore = ['/_output', '/banner_config.json', '/fallback', '/assets', '/scss'];
        $items = helper::custom_glob($abs_path . '/projects' . $dir);

        foreach ( $items as $file )
        {
            if ( in_array( $file, $ignore ) )
            {
                continue;
            }
            if ( is_dir($abs_path . '/projects' . $dir . $file) )
            {
                self::replace_text( $dir . $file, $identifier, $ext, $text );
            }
            else
            {
                if ( $ext === 'js' && pathinfo( $file )['extension'] === 'js' )
                {
                    if ( file_exists( $abs_path . '/projects' . $dir . $file) )
                    {
                        $cnt = file_get_contents( $abs_path . '/projects' . $dir . $file );
                        $match = preg_replace( '/(' . $identifier . '\b.*?)(.*?;)/', '$1' . ' = "' . $text . '";', $cnt );
                        file_put_contents( $abs_path . '/projects' . $dir . $file, $match );
                    }

                }
                if ( $ext === 'html' && pathinfo($file)['extension'] === 'php' )
                {
                    if ( file_exists( $abs_path . '/projects' . $dir . $file) )
                    {
                        $cnt = file_get_contents( $abs_path . '/projects' . $dir . $file );
                        $match = preg_replace('/(<.*' . $identifier . '\b.*?>).*[^<]*(<\/.*?>)/', '$1' . $text . '$2', $cnt );
                        file_put_contents( $abs_path . '/projects' . $dir . $file, $match );
                    }
                }
            }
        }
    }

    public static function save_values($data, string $file)
    {
        $ext = ( preg_match('.json', $file) ) ? '' : '.json';
        $slash = ( preg_match('/', substr($file, 0, 1)) ) ? '' : '/';
        if ( !file_exists(dirname(__DIR__) . '/app-assets/hyphenator-cache' ) )
        {
            mkdir(dirname(__DIR__) . '/app-assets/hyphenator-cache' );
        }

        if ( file_exists(dirname(__DIR__) . '/app-assets/hyphenator-cache' . $slash . $file . $ext) )
        {
            unlink(dirname(__DIR__) . '/app-assets/hyphenator-cache' . $slash . $file . $ext);
        }

        $write = fopen(dirname(__DIR__) . '/app-assets/hyphenator-cache' . $slash . $file . $ext, 'w');
        fwrite($write, json_encode(json_decode($data), JSON_PRETTY_PRINT));
        fclose($write);

    }

    public static function delete_json(string $file)
    {
        if ( file_exists(dirname(__DIR__) . '/app-assets/hyphenator-cache' . $file ) )
        {
            unlink(dirname(__DIR__) . '/app-assets/hyphenator-cache' . $file );
        }
    }
}

?>
