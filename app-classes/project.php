<?php

/**
 * setup banner project
 */

$project_folder = '/projects';
$banner_directories = [];
$project_config;

class project
{

    public $project_dir;
    public $banner_formats;
    public $banner_directories;
    public $global_files;

    function __construct( stdClass $project )
    {

        $abs_path = dirname( __DIR__ );

        if ( !file_exists( $abs_path . $GLOBALS['project_folder'] ) )
        {
            mkdir( $abs_path . $GLOBALS['project_folder'] );
        }

        if ( is_Array( $project->project_dir ) )
        {
            $this->project_dir = filesAndDir::build_project_dir( $abs_path, $GLOBALS['project_folder'], $project->project_dir );
            $GLOBALS['project_dir'] = $this->project_dir;
            if ( !file_exists($abs_path . $this->project_dir . '/_project_config.json') )
            {
                filesAndDir::build_project_conf_file( $abs_path . $this->project_dir, $project );
            }
            else
            {
                $project = json_decode(file_get_contents($abs_path . $this->project_dir . '/_project_config.json'));
            }
        }

        if ( is_Array( $project->banner_formats ) )
        {
            $GLOBALS['banner_formats'] = $project->banner_formats;
            $this->banner_formats = $project->banner_formats;
        }

        if ( is_Array( $project->global_files ) )
        {
            $GLOBALS['global_files'] = $project->global_files;
            $this->global_files = $project->global_files;
        }

        filesAndDir::build_banner_formats_data( $this->banner_formats, $this->project_dir, $this->global_files );
        $this->banner_directories = helper::flatten_array($GLOBALS['banner_directories']);
    }

    public static function build_banner(string $dir)
    {

        $unusedCSS = [];
        $iframes = [];
        $config = json_decode(file_get_contents( dirname( __DIR__ ) . $dir . '/banner_config.json'));
        $size = 0;
        $project_dir = $config->banner_config->project_dir;

        if ($config->banner_config->template === null)
        {
            $unusedCSS[] = filesAndDir::bundle_banner_data($config);
            $iframes[] = helper::build_iframe($config);
            $size += helper::folder_size(dirname(__DIR__) . '/' . $config->banner_config->banner_dir . '/_output');

            $data = (object) [
                'iframes' => json_encode($iframes),
                'size' => helper::format_size($size),
                'unusedCSS' => json_encode($unusedCSS),
                'project_dir' => $project_dir
            ];
        }
        else
        {
            $template = json_decode(file_get_contents( dirname( __DIR__ ) . '/banner-templates' . $config->banner_config->template));
            foreach ($template as $key => $value)
            {
                $config = json_decode(file_get_contents( dirname( __DIR__ ) . dirname( $dir ) . '/' . $key . '/banner_config.json'));

                $unusedCSS[] = filesAndDir::bundle_banner_data($config);
                $iframes[] = helper::build_iframe($config, $value);
                $size += helper::folder_size(dirname(__DIR__) . '/' . $config->banner_config->banner_dir . '/_output');

            }

            $data = (object) [
                'iframes' => json_encode($iframes),
                'size' => helper::format_size($size),
                'unusedCSS' => json_encode($unusedCSS),
                'project_dir' => $project_dir
            ];
        }

        return $data;
    }

    function __destruct()
    {
        // code
    }
}


?>
