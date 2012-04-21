<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/access_map_model.php');
require_once('models/mappings.php');

/**
 * A Project object represents a single row in the REPO_Projects table.
 *
 * @package Models
 */

class Project extends AccessMapModel
{
	public static $_table = 'REPO_Projects';
	public static $_id_column = 'proj_id';
	public static $_access_map_name = 'ProjectAccessMap';
	public static $_access_table = "REPO_Project_access_map";


	public function __get($name)
	{
		switch ($name)
		{
			case 'media':
				$result = Model::factory('ProjectMediaMap')
					->where('proj_id', $this->id())
					->find_many();
				
				$return = array();
				foreach ($result as $map)
				{	// De-reference ORM object
					$return[] = $map->media_id;
				}
				return $return;
				break;

			default:
				return parent::__get($name);
				break;
		}
	}

	/**
	 *	Overridden delete function to handle the removal of all hanging dependencies on this Portfolio.
	 */
	public function delete()
	{
		foreach ($maps = Model::factory('ProjectAccessMap')
			->where('proj_id', $this->id())
			->find_many() as $map)
		{
			$map->delete();
		}

		foreach ($maps = Model::factory('ProjectMediaMap')
			->where('proj_id', $this->id())
			->find_many() as $map)
		{
			$map->delete();
		}

		foreach ($maps = Model::factory('PortfolioProjectMap')
			->where('child_id', $this->id())
			->where('child_is_portfolio', false)
			->find_many() as $map)
		{
			$map->delete();
		}

		return parent::delete();
	}

	/**
	 *	Add a Media object underneath this Project.
	 *
	 *	@param	int		$media_id	Identifier of the Media object to add
	 *
	 *	@return	bool				True if successful, false otherwise
	 */
	public function addMedia($media_id)
	{
		if (!$map = Model::factory('ProjectMediaMap')->create())
		{
			return false;
		}

		$map->proj_id = $this->id();
		$map->media_id = $media_id;

		return $map->save();
	}

	/**
	 *	Remove a Media object from this Project.
	 *
	 *	@param	int		$media_id		Identifier of the Media object to remove
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public function removeMedia($media_id)
	{
		if (!$map = Model::factory('ProjectMediaMap')
			->where('proj_id', $this->id())
			->where('media_id', $media_id)
			->find_one())
		{
			return false;
		}

		return $map->delete();
	}
}

?>
