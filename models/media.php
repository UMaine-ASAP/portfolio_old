<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/project.php');

/**
 * A Media object represents a single row in the REPO_Media table.
 *
 * @package Models
 */
class Media extends AccessMapModel
{
	public static $_table = "REPO_Media";
	public static $_id_column = "media_id";
	public static $_access_map_name = "MediaAccessMap";

	public function __get($name)
	{
		switch ($name)
		{
			case 'projects':
				if (!$maps = Model::factory('ProjectMediaMap')
					->where('media_id', $this->media_id)
					->find_many())
				{
					return false;
				}

				$projects = array();

				foreach ($maps as $map)
				{
					if ($project = ProjectController::viewProject($map->proj_id))	// check for user's viewing privilege on the project
					{
						$projects[] = $project;
					}
				}
				return $projects;
				break;

			default:
				return parent::__get($name);
				break;
		}
	}
	/**
	 *	Acquire a handle to the file specified by the media object's filename.
	 *		@param mode|string The mode of the handle. Defaults to read-only.
	 *		@return The handle to the file, or false if the file could not be opened.
	 */
	public function handle($mode = 'r')
	{
		//TODO: check to see if the user that's currently logged in can see this file, or however we're going to manage that

		if (!$this->private) return fopen($this->filename, $mode);
	}
	
}

?>
