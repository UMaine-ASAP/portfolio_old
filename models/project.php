<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/mappings.php');

/**
 * A Project object represents a single row in the REPO_Projects table.
 *
 * @package Models
 */

class Project extends Model
{
	public static $_table = 'REPO_Projects';
	public static $_id_column = 'proj_id';

	public function __get($name)
	{
		switch ($name)
		{
			case 'media':
				return Model::factory('Media')
							-> where('content_id', $this->mediaMap()->content_id)
							-> find_pointer();
			default:
				return parent::__get($name);
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
}

?>
