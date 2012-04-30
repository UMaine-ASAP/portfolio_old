<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('controllers/group.php');
require_once('models/access_map_model.php');
require_once('models/mappings.php');
require_once('models/assignment.php');

/**
 *	Portfolio model object
 *
 *	A Portfolio object represents a single row in the REPO_Portfolios table, along with
 *	all associated data derived from its relations.
 *
 *	@property-read	array	$permissions	Array of permission levels specific to the requesting user.
 *											Each value corresponds to an enumerable value as specified in 'constants.php'.
 *	@property-read	array	$children		Associative array specifying children Portfolio or Project objects underneath the current one.
 *											Each object in the array is structured as follows:
 *												- Key = identifier of child Portfolio / Project object.
 *												- Value = 2-tuple(array) of the following:
 *															-- boolean value specifying whether the child is a sub-Portfolio or Project
 *												  				(true = child is sub-Portfolio, false = child is not sub-Portfolio) at index 0.
 *												  			-- Type of privacy the child object has, as specified in constant.php
 *
 * 	@package Models
 */
class Portfolio extends AccessMapModel
{
	public static $_table = 'REPO_Portfolios';
	public static $_id_column = 'port_id';
	public static $_access_map_name = 'PortfolioAccessMap';
	public static $_access_table = 'REPO_Portfolio_access_map';


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'children':
			$result = Model::factory('PortfolioProjectMap')
				->where('port_id', $this->id())
				->find_many();
			
			$return = array();
			foreach ($result as $map)
			{	// De-reference ORM object
				$return[$map->child_id] = array($map->child_is_portfolio, $map->child_privacy);
			}
			return $return;
			break;
		case 'owner':
			//$result 
			$groupIDs = $this->groupsWithPermission();//Model::factory()
			//return key($groupIDs);
			$groupID = key($groupIDs);

			//return "test";
//			$groupID = $groupIDs[0];
			$group = groupController::viewGroup($groupID);
			$id = $group->members[0];
			return userController::getUser($id);
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
		// Remove all references to this Portfolio by Assignments
		foreach ($assignments = Model::factory('Assignment')
			->where('portfolio_id', $this->id())
			->find_many() as $assign)
		{
			$assign->portfolio_id = NULL;
			$assign->save();
		}

		// Remove all references to this Portfolio by Projects/sub-Portfolios/super-Portfolios
		foreach ($projects = Model::factory('PortfolioProjectMap')
			->where('port_id', $this->id())
			->find_many() as $proj)
		{
			$proj->delete();
		}
		foreach ($superPorts = Model::factory('PortfolioProjectMap')
			->where('child_id', $this->id())
			->find_many() as $port)
		{
			$port->delete();
		}
		
		// Remove all Groups' permissions on this Portfolio
		//	(unfortunately, we cannot clean up Groups specifically made for this Portfolio easily,
		//	thus they will remain and clutter the database.)
		foreach ($groups = $this->groupsWithPermission() as $group=>$permissions)
		{
			foreach ($permissions as $perm)
			{
				Model::factory('PortfolioAccessMap')
					->where('port_id', $this->id())
					->where('group_id', $group)
					->where('access_type', $perm)
					->find_one()
					->delete();
			}
		}

		return parent::delete();
	}

	/**
	 *	Adds a Portfolio as a sub-Portfolio to this Portfolio.
	 *
	 *	@param	int		$child_id		Identifier of the Portfolio to be added underneath this one
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public function addSubPortfolio($child_id, $privacy)
	{
		if (!$map = Model::factory('PortfolioProjectMap')->create())
		{
			return false;
		}
		$map->port_id = $this->id();
		$map->child_id = $child_id;
		$map->child_is_portfolio = 1;
		$map->child_privacy = $privacy;

		return $map->save();
	}

	/**
	 *	Adds a Project to this Portfolio.
	 *
	 *	@param	int		$proj_id		Identifier of the Project to be added
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public function addProject($proj_id)
	{
		if (!$map = Model::factory('PortfolioProjectMap')->create())
		{
			return false;
		}
		$map->port_id = $this->id();
		$map->child_id = $proj_id;
		$map->child_is_portfolio = 0;

		return $map->save();
	}

	/**
	 *	Remove a child from this Portfolio (either Project or sub-Portfolio).
	 *
	 *	@param	int		$child_id				Identifier of the object to be removed from the Portfolio
	 *	@param	bool	$child_is_portfolio		Whether or not the child is a sub-Portfolio
	 *											(true=child is sub-Portfolio)
	 *
	 *	@return	bool							True if successful, false otherwise
	 */
	public function removeChild($child_id, $child_is_portfolio)
	{
		if (!$map = Model::factory('PortfolioProjectMap')
			->where('port_id', $this->id())
			->where('child_id', $child_id)
			->where('child_is_portfolio', $child_is_portfolio)
			->find_one())
		{
			return false;
		}

	    return $map->delete();
	}


}

?>
