<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

/**
 * A Media object represents a single row in the REPO_Media table.
 *
 * @package Models
 */
class Media extends Model
{
	public static $_table = "REPO_Media";
	public static $_id_column = "media_id";

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

	/**
	 *	Returns the project(s) that use this media.
	 *
	 *	@return An array containing the projects that use this Media object, or false if none do.
	 */
	public function projects()
	{
		//TODO: check to make sure that the projects associated with this are visible to the user who's doing this
		if (!$maps = Model::factory('ProjectMediaMap')->where('media_id', $this->media_id))
		{
			return false;
		}

		$projects = array();

		foreach ($maps as $map)
		{
			if ($project = Model::factory('Project')->where('proj_id', $map->proj_id))
			{
				array_push($projects, $project);
			}
		}

		return $projects;
	}
}

?>
