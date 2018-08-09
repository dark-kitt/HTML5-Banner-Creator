<?php

    if( isset( $_POST['archive_name'] ) && !empty($_POST['archive_name'] ) &&
        isset( $_POST['archive_path'] ) && !empty($_POST['archive_path'] ) )
	{
        require __DIR__ . '/helper.php';
        archive::archive_project( $_POST['archive_name'], '/projects/' . $_POST['archive_path']);
	}

    class archive {
        public static function archive_project($name, $path)
		{
            if ( strpos($name, '/') !== false )
            {
                $archive_zip_name = dirname(__DIR__) . '/banner-archive/' . str_replace('/', '-', $name) . '.zip';
            }
            else
            {
                $archive_zip_name = dirname(__DIR__) . '/banner-archive/' . $name . '.zip';
            }

			$archive_zip = new ZipArchive();
			$archive_zip->open( $archive_zip_name, ZipArchive::CREATE);

            $files = helper::find_all_file_paths( dirname(__DIR__) . $path );

            foreach ($files as $file) {
                $archive_zip->addFile( $file, str_replace(dirname(dirname(__DIR__) . $path), '', $file) );
            }

            $archive_zip->close();

            helper::delete_dir(dirname(__DIR__) . $path);
        }
    }
