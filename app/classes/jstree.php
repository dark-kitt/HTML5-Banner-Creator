<?php
/**
 * read directory
 */
class jstree
{
    public static function get_folder_structure(string $dir)
    {
        $abs_path = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
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
                    'icon' => 'img/arrow-light.svg',
                    'type' => 'arrow',
                    'dnd' => false,
                    'li_attr' => [
                        'data-directory' => str_replace($abs_path, '', $dir . $content[$length])
                    ]
                ];
            }
        }

        return $result;
    }
}

?>
