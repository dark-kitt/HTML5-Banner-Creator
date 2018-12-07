<?php

require dirname(__DIR__) . '/app-assets/constants.php';
require __DIR__ . '/helper.php';


if ( isset( $_POST['jstree'] ) && !empty( $_POST['jstree'] ) )
{
    require __DIR__ . '/jstree.php';
    print json_encode(jstree::get_folder_structure( dirname(__DIR__) . '/projects' ));
    exit;
}


if ( isset( $_POST['refresh'] ) && !empty( $_POST['refresh'] ) &&
     isset( $_POST['dir'] ) && !empty( $_POST['dir'] ) )
{
    if ($_POST['refresh'] === 'head') {
        $data = helper::custom_glob( dirname(__DIR__) . AD_SCRIPT_HEAD, '.php');
    }
    if ($_POST['refresh'] === 'body') {
        $data = helper::custom_glob( dirname(__DIR__) . AD_SCRIPT_BODY, '.php');
    }
    if ($_POST['refresh'] === 'banner') {
        $directories = [];
        helper::get_banner_directories(dirname(__DIR__) . $_POST['dir']);

        $data = (object) [
            'project_dir' => $_POST['dir'],
            'banner_directories' => helper::flatten_array($directories)
        ];
    }

    print json_encode(helper::build_checkbox($_POST['refresh'], $data));
    exit;
}


if ( isset( $_POST['export'] ) && !empty( $_POST['export'] ) ||
     isset( $_POST['iframes'] ) && !empty( $_POST['iframes'] ) ||
     isset( $_POST['project'] ) && !empty( $_POST['project'] ) ||
     isset( $_POST['banner'] ) && !empty( $_POST['banner'] ) &&
     isset( $_POST['changed'] ) && !empty( $_POST['changed'] ) )
 {
     require dirname(__DIR__) . '/app-assets/placeholder.php';
     require dirname(__DIR__) . '/app-assets/regex.php';

     require dirname(__DIR__) . '/vendor/autoload.php';

     require __DIR__ . '/files-and-dir.php';
     require __DIR__ . '/namespaceCSS.php';

     $scss_compiler = new Leafo\ScssPhp\Compiler();
     $autoprefixer = new Autoprefixer('last 3 version');

     $scss_compiler->setFormatter( 'Leafo\ScssPhp\Formatter\Crunched' );

     if ( isset( $_POST['export'] ) && !empty( $_POST['export'] ) )
     {

         foreach ($_POST['export']->banner as $banner) {
             filesAndDir::build_output($banner);
         }

         require __DIR__ . '/zip.php';
         zip::build_export( json_decode($_POST['export']) );
         exit;
     }
     else
     {
         require __DIR__ . '/project.php';
         require __DIR__ . '/unusedCSS.php';

         if ( isset( $_POST['iframes'] ) && !empty( $_POST['iframes'] ) )
         {

             $banner_data = project::build_banner('/' . json_decode($_POST['iframes'])[0]);
             print json_encode($banner_data);
             exit;
         }

         if ( isset( $_POST['project'] ) && !empty( $_POST['project'] ) )
         {

             $banner_project = new project(json_decode(file_get_contents(dirname(__DIR__) . $_POST['project'] . '/_project_config.json')));
             $banner_data = project::build_banner($banner_project->banner_directories[0]);

             $directories = [];
             helper::get_banner_directories(dirname(__DIR__) . $_POST['project']);
             $data = (object) [
                 'project_dir' => $_POST['project'],
                 'banner_directories' => helper::flatten_array($directories)
             ];
             $checkbox = helper::build_checkbox('banner', $data);

             print json_encode([
                 'banner_data' => $banner_data,
                 'checkbox' => $checkbox
             ]);
             exit;
         }

         if ( isset( $_POST['banner'] ) && !empty( $_POST['banner'] ) &&
              isset( $_POST['changed'] ) && !empty( $_POST['changed'] ) )
         {

             $banner_data = project::build_banner($_POST['banner']);

             $checkbox = null;
             if ($_POST['changed'] === 'true') {
                 $directories = [];
                 helper::get_banner_directories(dirname(__DIR__) . $banner_data->project_dir);
                 $data = (object) [
                     'project_dir' => $banner_data->project_dir,
                     'banner_directories' => helper::flatten_array($directories)
                 ];
                 $checkbox = helper::build_checkbox('banner', $data);
             }

             print json_encode([
                 'banner_data' => $banner_data,
                 'checkbox' => $checkbox
             ]);
             exit;
         }

     }

 }


if ( isset( $_POST['archive'] ) && !empty( $_POST['archive'] ) &&
     isset( $_POST['dir'] ) && !empty( $_POST['dir'] ) )
{
    $files = [];
    helper::glob_recursive($_POST['dir']);

    require __DIR__ . '/zip.php';
    zip::build_archive($_POST['dir'], $_POST['archive'], helper::flatten_array($files));
    exit;
}


if ( isset( $_POST['dir'] ) && !empty( $_POST['dir'] ) &&
     isset( $_POST['id'] ) && !empty( $_POST['id'] ) &&
     isset( $_POST['file'] ) && !empty( $_POST['file'] ) &&
     isset( $_POST['output'] ) && !empty( $_POST['output'] ) )
{
    require __DIR__ . '/hyphenator.php';
    if ( isset( $_POST['dir'] ) && !empty( $_POST['dir'] ) &&
         !file_exists(dirname(__DIR__) . '/projects' . $_POST['dir']) )
    {
        print 'File or directory doesn\'t exists!';
    }
    else
    {
        hyphenator::replace_text( $_POST['dir'], $_POST['id'], $_POST['file'], $_POST['output'] );
    }
    exit;
}

if ( isset( $_POST['save_options'] ) && !empty( $_POST['save_options'] ) &&
     isset( $_POST['file_name'] ) && !empty( $_POST['file_name'] ))
{
    require __DIR__ . '/hyphenator.php';
    hyphenator::save_values( $_POST['save_options'], $_POST['file_name'] );
    exit;
}

if ( isset( $_POST['delete_json'] ) && !empty( $_POST['delete_json'] ) )
{
    require __DIR__ . '/hyphenator.php';
    hyphenator::delete_json( $_POST['delete_json'] );
    exit;
}

?>
