<?php
/**
 * read directory
 */
class jstree
{
    public static function get_folder_structure(string $dir)
    {
        $content = helper::custom_glob($dir, '', true);
        $result = [];
        $ignore = ['/_project_config.json'];
        $length = count($content);

        while ($length--)
        {
            if (in_array($content[$length], $ignore) || in_array('/banner_config.json', $content))
            {
                continue;
            }

            if (is_dir($dir . $content[$length]) )
            {
                $result[] = [
                    'text' => basename($content[$length]),
                    'children' => self::get_folder_structure($dir . $content[$length]),
                    'icon' => 'app-assets/img/arrow-light.svg',
                    'type' => 'arrow',
                    'dnd' => false,
                    'li_attr' => [
                        'data-directory' => str_replace(dirname(__DIR__), '', $dir . $content[$length])
                    ]
                ];
            }
        }

        return $result;
    }
}

?>
