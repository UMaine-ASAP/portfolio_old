<?php

require_once(__DIR__ . '/../libraries/Idiorm/idiorm.php');
require_once(__DIR__ . '/../libraries/Paris/paris.php');

class ProjectMediaMap extends Model
{
	public static $_table = 'REPO_Project_media_map';
	public static $_id_column = 'id';

	public function __get($name)
	{
		switch ($name)
		{
			case 'project_id':
				return $this->proj_id; //these are redundant, but in the interest of issue #13 and clarity...
				break;

			case 'project':
				return Model::factory('Project')->find_one($this->proj_id);
				break;

			case 'media_id':
				return $this->media_id;
				break;

			case 'media':
				return Model::factory('Media')->find_one($this->media_id);
				break;

			default:
				return parent::__get($name);
				break;
		}
	}
}

class SectionAccessMap extends Model
{
	public static $_table = 'REPO_Section_access_map';
	public static $_id_column = 'id';

	public function __get($name)
	{
		switch ($name)
		{
			case 'section_id':
				return $this->section_id;
				break;

			case 'section'
				return Model::factory('Section')->find_one($this->section_id);
				break;

			case 'group_id':
				return $this->group_id;
				break;

			case 'group':
				return return Model::factory('Group')->find_one($this->group_id);
				break;

			default:
				return parent::__get($name);
				break;
		}
	}
}

class PortfolioProjectMap extends Model
{
	public static $_table = 'REPO_Portfolio_project_map';
	public static $_id_column = 'id';

	public function portfolio()
	{
		return Model::factory('Portfolio')->where('port_id', $this->port_id)->find_one();
	}

	public function project()
	{
		return Model::factory('Project')->where('proj_id', $this->proj_id)->find_one();
	}
}

class AssignmentAccessMap extends Model
{
	public static $_table = 'REPO_Assignment_access_map';
	public static $_id_column = 'id';
}

class AssignmentInstanceAccessMap extends Model
{
	public static $_table = 'REPO_Assignment_instance_access_map';
	public static $_is_column = 'id';
}

class PortfolioAccessMap extends Model
{
	public static $_table = 'REPO_Portfolio_access_map';
	public static $_id_column = 'id';
}

class MediaAccessMap extends Model
{
	public static $_table = 'REPO_Media_access_map';
	public static $_id_column = 'id';
}

class ProjectAccessMap extends Model
{
	public static $_table = 'REPO_Project_access_map';
	public static $_id_column = 'id';
}

class GroupUserMap extends Model
{
	public static $_table = 'AUTH_Group_user_map';
	public static $_id_column = 'id';
}

?>
