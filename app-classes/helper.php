<?php
/**
 * helper functions
 */
class helper
{

    public static function check_duplicates(array $array)
    {
        $return = [];
        array_map( function ($a) use (&$return)
        {
           if (!is_null($a) && !empty($a))
           {
               if (is_Array($a) || $a instanceof stdClass)
               {
                   if ($a instanceof stdClass)
                   {
                       $a = key($a);
                       if (!in_Array($a, $return))
                       {
                           $return[] = $a;
                       }
                       else
                       {
                           $subject = join($return, ',');
                           $return[] = self::build_duplicates($a, preg_match_all('/' . $a . '/', $subject));
                       }
                   }
                   else
                   {
                       if (!in_Array(join($a, 'x'), $return))
                       {
                           $return[] = join($a, 'x');
                       }
                       else
                       {
                           $subject = join($return, ',');
                           $return[] = self::build_duplicates($a, preg_match_all('/' . join($a, 'x') . '/', $subject));
                       }
                   }
               }
           }
        }, $array);

        return (count($return) > 0) ? $return : false;
    }

    public static function build_duplicates($item, int $count)
    {
        return is_Array($item) ? join($item, 'x') . '-' . sprintf('%02d', $count) : $item . '-' . sprintf('%02d', $count);
    }

    public static function flatten_array(array $array)
    {
        $return = [];
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    public static function array_values_int(array $array)
    {
        $return = false;
        array_filter($array, function($a) use (&$return) { $return = (is_int($a)) ? true : false; });
        return $return;
    }

    public static function get_const_dir(array $array)
    {
        $return = false;
        array_walk_recursive($array, function($a) use (&$return) {
            if ( file_exists( dirname(__DIR__) . JS_ANIMATION_LIBRARY . constant($a)) ||
                 file_exists( dirname(__DIR__) . CLIENT_BASE_SCSS . constant($a)) ||
                 file_exists( dirname(__DIR__) . BANNER_TEMPLATES . constant($a)) ||
                 file_exists( dirname(__DIR__) . GLOBAL_MARKUP . constant($a)) ||
                 file_exists( dirname(__DIR__) . GLOBAL_FILES . constant($a)))
            {
                $return[] = constant($a);
            }
        });
        return $return;
    }

    public static function match_placeholder(string $content, stdClass $config = null)
    {
        if ( $config !== null )
        {
            $cfg = $config->banner_config;
        }

        if ( preg_match( '/\#\#\#/', $content) )
        {
            $phDynamic = $GLOBALS['placeholder']->dynamic;
            $phStatic = $GLOBALS['placeholder']->static;

            if ( $config !== null )
            {
                foreach ($phDynamic as $ph) {
                    if (preg_match_all('/\#\#\#' . $ph[0] . '\#\#\#/', $content)) {
                        $content = preg_replace_callback( '/\#\#\#' . $ph[0] . '\#\#\#/', function($match) use($cfg, $ph) {
                            if ( $match[0] === '###' . $ph[0] . '###' )
                            {
                                return str_replace ( $match[0], $cfg->{$ph[1]}, $match[0] );
                            }
                        }, $content );
                    }
                }
            }

            foreach ($phStatic as $ph) {
                if (preg_match_all('/\#\#\#' . $ph[0] . '\#\#\#/', $content)) {
                    $content = preg_replace_callback( '/\#\#\#' . $ph[0] . '\#\#\#/', function($match) use($ph) {
                        if ( $match[0] === '###' . $ph[0] . '###' )
                        {
                            return str_replace ( $match[0], $ph[1], $match[0] );
                        }
                    }, $content );
                }
            }
        }

        return $content;
    }

    public static function compress_HTML(string $html)
    {
        preg_match_all( '!(<(?:code|pre|script).*>[^<]+</(?:code|pre|script)>)!', $html, $pre );

        $html = preg_replace( '/' . REGEX_SELF_SVG . '/s', '<$1 $2></$1>', $html );
        $html = preg_replace( '/' . REGEX_SPACE_CLASS . '/', ' ', $html );
        $html = preg_replace( '/' . REGEX_SPACE_ID . '/', ' ', $html );
        $html = preg_replace( '/' . REGEX_HTML_COMMENTS . '/', '', $html );
        $html = preg_replace( '/' . REGEX_RNT .'/', ' ', $html );
        $html = preg_replace( '/>'. REGEX_SPACES . '</', '><', $html );
        $html = preg_replace( '/'. REGEX_SPACES . '/', ' ', $html );
        if ( !empty( $pre[0] ) )
        {
            foreach ( $pre[0] as $tag )
            {
                $html = preg_replace( '!#pre#!', $tag, $html, 1 );
            }
        }
        return $html;
    }

    public static function build_iframe( stdClass $config, string $style = '' )
    {
        $dir = substr($config->banner_config->banner_dir, 1);
        return '<iframe frameborder="0" scrolling="no" width="' . $config->banner_config->width . '" height="' . $config->banner_config->height . '" src="' . $dir . '/_output/index.html" style="' . $style . '" data-banner="' . $dir . '"></iframe>';
    }

    public static function custom_glob(string $dir, string $ext = '', bool $sort = false, bool $DS_Store = false)
    {
        $arr = [];
        $ignore = ($DS_Store === false) ? ['.', '..', '.DS_Store'] : ['.', '..'];
        if ( is_dir($dir) )
        {
            if ( $dh = opendir($dir) )
            {
                while ( ($file = readdir($dh)) !== false )
                {
                    if ( in_array($file, $ignore) ) continue;
                    if ( $ext === '' ) {
                        $arr[] = '/' . $file;
                        continue;
                    }
                    if ( strpos($file, $ext) ) $arr[] = '/' . $file;
                }
                closedir($dh);
            }
        }
        if ( $sort === true ) rsort($arr);
        return $arr;
    }

    public static function build_checkbox(string $attr, $data)
    {
        $checkbox = [];
        $parent_dir = [];
        if ($attr !== 'banner')
        {
            sort($data, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($data as $file)
            {
                array_push($checkbox, '<li>' .
                    '<label>' .
                        '<input type="checkbox" name="'. $attr . '" value="' . basename($file, '.php') . '">' .
                         basename($file, '.php') .
                    '</label></li>');
            }
        }
        else
        {
            $project_dir = $data->project_dir;
            sort($data->banner_directories, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($data->banner_directories as $file)
            {
                if (!in_array(str_replace( '/' . basename($file), '',  $file), $parent_dir))
                {
                    $parent_dir[] = str_replace( '/' . basename($file), '',  $file);
                    array_push($checkbox, '<li>' .
                        '<span>' . str_replace( dirname($project_dir), '', str_replace( '/' . basename($file), '',  $file)) . '</span></li>' .
                        '<li><label>' .
                            '<input type="checkbox" name="'. $attr . '" value="' . $file . '">' .
                             basename($file) .
                        '</label></li>');
                }
                else
                {
                    array_push($checkbox, '<li>' .
                        '<label>' .
                            '<input type="checkbox" name="'. $attr . '" value="' . $file . '">' .
                             basename($file) .
                        '</label></li>');
                }
            }
        }

        return join($checkbox, '');
    }

    public static function folder_size(string $path)
    {
        $total_size = 0;
        $files = self::custom_glob($path);

        foreach ($files as $t)
        {
            $size = filesize($path . $t);
            $total_size += $size;
        }

        return $total_size;
    }

    public static function format_size(int $size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++)
        {
            $size /= $mod;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public static function get_banner_directories(string $dir)
    {
        $items = self::custom_glob($dir);
        $return = [];
        $length = count($items);

        while ($length--) {

            if ( in_array('/banner_config.json', $items) )
            {
                $return[] = str_replace(dirname(__DIR__), '', $dir);
                self::get_banner_directories($dir . $items[$length] );
                break;
            }
            self::get_banner_directories($dir . $items[$length] );

        }

        array_push($GLOBALS['directories'], self::flatten_array($return));
    }

    public static function glob_recursive(string $dir)
    {
        $abs_path = dirname(__DIR__) . $dir;
        $arr = self::custom_glob( $abs_path );
        array_walk_recursive($arr, function($value) use ($abs_path, $dir)
        {
            if (is_dir($abs_path . $value))
            {
                if (count(self::custom_glob($abs_path . $value)) === 0)
                {
                    array_push($GLOBALS['files'], $abs_path . $value);
                }
                self::glob_recursive($dir . $value);
            }
            if (is_file($abs_path . $value) && !is_dir($abs_path . $value))
            {
                array_push($GLOBALS['files'], $abs_path . $value);
            }
        });
    }

    public static function delete_dir(string $dir)
    {

        $abs_path = dirname(__DIR__);
        $items = self::custom_glob($abs_path . $dir, '', false, true);
        $folder = [];

        foreach ($items as $item)
        {
            if (is_file($abs_path . $dir . $item))
            {
                unlink($abs_path . $dir . $item);
                continue;
            }
            else
            {
                self::delete_dir($dir . $item);
                rmdir($abs_path . $dir . $item);
                continue;
            }
        }
        rmdir($abs_path . $dir);
    }

}
