<?php

	if ( isset( $_POST['jstree'] ) && !empty( $_POST['jstree'] ) )
	{
		print json_encode(jstree::create_folder_structure( dirname(__DIR__) . '/projects' ));
		exit;
	}

	class jstree
	{
		function create_folder_structure($directory)
		{
			$directory_items = scandir($directory);
			$result = [];
			$ignore = ['.', '..', '.DS_Store', '_project_config.json'];

			foreach ($directory_items as $item)
			{
				if (in_array($item, $ignore) || in_array('config.php', $directory_items))
				{
					continue;
				}
				if (is_dir($directory . '/' . $item) )
				{
					$result[] = [
						'text' => $item,
						'children' => self::create_folder_structure($directory . '/' . $item),
						'icon' => 'assets/jstree-arrow.svg',
						'type' => 'arrow',
						'dnd' => false,
						'state' => [
							'opened' => false,
							'disabled' => false,
							'selected' => true
						]
					];
				}
				else
				{
					$result[] = [
						'text' => $item,
						'children' => true,
						'icon' => 'assets/jstree-arrow.svg',
						'type' => 'arrow',
					];
				}
			}

			return $result;
		}
	}
