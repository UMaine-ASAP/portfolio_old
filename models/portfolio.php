<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('models/mappings.php');
require_once('models/assignment.php');
require_once('libraries/constant.php');

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
 *												- Key = identifier of child Portfolio / Project object at index 0.
 *												- Value = boolean value specifying whether the child is a sub-Portfolio or Project
 *												  (true = child is sub-Portfolio, false = child is not sub-Portfolio) at index 1.
 *
 * 	@package Models
 */
class Portfolio extends Model
{
	public static $_table = 'REPO_Portfolios';
	public static $_id_column = 'port_id';


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			$return = array();
			// If curret User's ID is the owner_user_id of the Portfolio, return ownership privilege
			if ($this->owner_user_id == USER_ID)	// Check for user ID here
			{
				$return[] = OWNER; 
				return $return;
			}
			else
			{
				$result = ORM::for_table('REPO_Portfolio_access_map')
					->table_alias('access')
					->select('access.access_type')
					->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
					->where('access.port_id', $this->id())
					->where('AUTH_Group_user_map.user_id', USER_ID)	// add user credentials here
					->find_many();
				
				foreach ($result as $perm)
				{	// Results are returned as ORM objects, de-reference them
					$return[] = $perm->access_type;
				}
				return $return;
			}
			break;
		
		case 'children':
			$result = Model::factory('PortfolioProjectMap')
				->where('port_id', $this->id())
				->find_many();
			
			$return = array();
			foreach ($result as $child)
			{	// De-reference ORM object
				$return[$child->child_id] = $child->child_is_portfolio;
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
	public function addSubPortfolio($child_id)
	{
		if (!$map = Model::factory('PortfolioProjectMap')->create())
		{
			return false;
		}
		$map->port_id = $this->id();
		$map->child_id = $child_id;
		$map->child_is_portfolio = 1;

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

	/**
	 *	Retrieve all Groups with permissions for this Porfolio.
	 *
	 *	@return	array				An array of arrays with keys specifying the identifier of the Group,
	 *								and the value for the key being an array of all permission levels
	 *								of the Group for this Portfolio, as specified in 'constant.php'.
	 */
	public function groupsWithPermission()
	{
		$result = Model::Factory('PortfolioAccessMap')
			->where('port_id', $this->id())
			->find_many();

		$return = array();
		foreach ($result as $perm)
		{
			if (isset($return[$perm->group_id]))
			{
				$return[$perm->group_id][] = $perm->access_type;
			}
			else
			{
				$return[$perm->group_id] = array($perm->access_type);
			}
		}
		return $return;
	}

	/**
	 *	Retrieve a Group's permissions for this Portfolio.
	 *
	 *	@param	int		$group		The identifier of the Group object for which we seek the permissions of.
	 *
	 *	@return	array				An array of permission levels as specified in 'constant.php', or an empty
	 * 								array in the event the Group has no permissions.
	 */
	public function permissionsForGroup($group_id)
	{
		$result = Model::factory('PortfolioAccessMap')
			->where('port_id', $this->id())
			->where('group_id', $group_id)
			->find_many();
		
		$return = array();
		foreach($result as $perm)
		{	// De-reference ORM results into raw array
			$return[] = $perm->access_type;
		}
		return $return;
	}

	/**
	 *	Add a permission level for a Group in regards to this Portfolio.
	 *
	 *	@param	int		$group		The identifier of the Group objects we seek to add permissions to.
	 *	@param	int		$perm		The permission level as specified in 'constant.php' for the Group.
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public function addPermissionForGroup($group_id, $perm_id)
	{
		if (Model::factory('PortfolioAccessMap')
			->where('port_id', $this->id())
			->where('group_id', $group_id)
			->where('access_type', $perm_id)
			->find_one())
		{
			return false;
		}

		if (!$map = Model::factory('PortfolioAccessMap')->create())
		{
			return false;
		}
		
		$map->port_id = $this->id();
		$map->group_id = $group_id;
		$map->access_type = $perm_id;

		return $map->save();
	}

	/**
	 *	Remove a permission level for a Group in regards to this Portfolio.
	 *
	 *	@param	int		$group		The identifier of the Group objects we seek to remove permissions from.
	 *	@param	int		$perm		The permission level as specified in 'constant.php' for the Group.
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public function removePermissionForGroup($group_id, $perm_id)
	{
		if (!$permissions = $this->permissionsForGroup($group_id))
		{
			return false;
		}

		foreach ($permissions as $p)
		{
			if ($p == $perm_id)
			{
				$permORM = Model::factory('PortfolioAccessMap')
					->where('port_id', $this->id())
					->where('group_id', $group_id)
					->where('access_type', $perm_id)
					->find_one();
				return $permORM->delete();
			}
		}

		return false;
	}



}

?>
