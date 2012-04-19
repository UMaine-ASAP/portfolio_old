<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/mappings.php');
require_once('models/portfolio.php');
require_once('controllers/group.php');
require_once('controllers/authentication.php');

/**
 * Controller handling all aspects of Portfolio objects within the system.
 *
 * @package Controllers
 */
class PortfolioController
{
	/**
	 *	Create a new Portfolio object.
	 *
	 *	Creates a new Portfolio object in the system with the specified parameters. The currently
	 *	logged in user is given 'owner' level permissions.
	 *	User must therefore have a user account registered with Portfolio creation privileges.
	 *
	 *	@param	string		$title			Specifies the title of the new Portfolio in plain-text (255 char max).
	 *	@param	string|null	$description 	Describes the new Portfolio in plain-text (2^16 char max, optional).
	 *	@param	bool		$private		Specifies whether or not the Portfolio is considered private
	 *										(true = private, false = public).
	 *
	 *	@return	object|bool					The created Portfolio object if successful, false otherwise.
	 */
	public static function createPortfolio($title, $description, $private)
	{
		//Currently, we don't check portfolio creation privileges (because that's not yet a thing)
		//all we do is check to see that a user is in fact currently logged in
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		if (!$port = Model::factory('Portfolio')->create())
		{
			return false;
		}

		if (!is_null($title))	{ $port->title = $title; }
		else					{ return false; }
		if (!is_null($private))	{ $port->private = $private; }
		else					{ return false; }

		$port->owner_user_id = $user->user_id;
		$port->description = $description;

		if (!$port->save())
		{
			$port->delete();
			return false;
		}

		return $port;
	}


	/**
	 *	Edit a specific Portfolio object.
	 *
	 *	Edits paramaters of a Portfolio object in the system.
	 *	Calling user must have editing privileges for the Portfolio object.
	 *
	 *	@param	int				$id				The unique identifier of the Portfolio object being edited. 
	 *	@param	int|null		$owner_user_id	Indentifier of the User to give the Portfolio to
	 *											(requires ownership privileges on the Portfolio)
	 *	@param	string|null		$title			The title of the Portfolio to be set, in plain-text (255 char max),
	 *											or null to leave untouched.
	 *	@param	string|null		$description	The description of the Portfolio to be set, in plain-text (2^16 char max),
	 *											or null to leave untouched.
	 *	@param	bool|null		$private	 	Specifies whether or not the Portfolio is considered private
	 *											(true = private, false = public).
	 *
	 *	@return	bool							True if successfully edited, false otherwise.
	 */
	public static function editPortfolio($id, $owner_user_id = NULL, $title = NULL, $description = NULL, $private = NULL)
	{
		if (!$port = self::getPortfolio($id))
		{
			return false;
		}

		//TODO: check edit privileges here
		// $port->permissions

		if (!is_null($owner_user_id))
		{
			// Check for ownership privileges here
			$port->owner_user_id = $owner_user_id;
		}
		if (!is_null($title)) 		{ $port->title = $title; }
		if (!is_null($description)) { $port->description = $description; }
		if (!is_null($private))		{ $port->private = $private; }
			
		if (!$port->save())
		{
			return false;
		}

		return true;
	}


	/**
	 *	Delete a specific Portfolio object.
	 *
	 *	Deletes a Portfolio object in the system that the current user owns.
	 *	Does _not_ delete Projects or sub-Portfolios.
	 *	Calling user must have deletion privileges on the Portfolio object.
	 *
	 *	@parm	int		$id				The unique identifier of the Portfolio to be deleted.
	 *
	 *	@return	bool					True if successfully deleted, false otherwise.
	 */
	public static function deletePortfolio($id)
	{
		if (!$port = self::getPortfolio($id))
		{
			return false;
		}

		//TODO: check delete privileges here
		// $port->permissions

		return $port->delete();
	}


	/**
	 *	Retrieve a specific Portfolio object for viewing.
	 *
	 *	Retrieves a Portfolio object, constructed as specified within the Portfolio model object description.
	 *	User must have viewing privileges on the Portfolio object requested.
	 *
	 *	@param	int		$id				The specified identifier of the Portfolio to be retrieved.
	 *
	 *	@return	object|bool				The Portfolio object requested if successful, false otherwise.
	 */
	public static function viewPortfolio($id)
	{
		if (!$port = self::getPortfolio($id))
		{
			return false;
		}

		//TODO: check view privileges here

		return $port;
	}

	/**
	 * 	Helper function to retrieve a Portfolio object.
	 * 
	 * 	Retrieves a Portfolio object, without checking for permissions.
	 *
	 * 	@param	int		$id				The identifier of the requested Portfolio object.
	 *
	 * 	@return	object|bool				The Portfolio object requested if successful, false otherwise.
	 */
	private static function getPortfolio($id)
	{
		return Model::factory('Portfolio')->find_one($id);
	}


	/**
	 *	Retrieve a number of Portfolio objects the current user is a member of.
	 *
	 *	Retrieves a specified number of Portfolio objects. A User is a 'member' of a Portfolio if they 
	 *	have any privileges specific to them (i.e. higher than public-level access).
	 *
	 *	@param	int		$count			The number of Portfolio objects desired.
	 *	@param	int		$order_by		The field that the Portfolios should be ordered by (ex: date_descending),
	 *									as specified in 'constant.php'.
	 *	@param	int		$pos			The index of the first Portfolio to return in the set 'order_by' specifies.
	 *
	 *	@return	array|bool				An array of Portfolio objects if successful, false otherwise.
	 */
	public static function getMemberPortfolios($count, $order_by, $pos)
	{
		//TODO: check privileges here
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		//use $user's privileges to determine the results
		
	    return false;
	}


	/**
	 *	Retrieve a number of Portfolio objects the current User is included in.
	 *
	 *	Retrieves a specified number of Portfolio objects. A User is 'included in' a Portfolio if they
	 *	have any Projects they are a 'member' of included in the Portfolio or its sub-Portfolios.
	 *
	 *	@param	int		$count			The number of Portfolio objects desired (0 = return all such Portfolios).
	 *	@param	int		$order_by		The field that the Portfolios should be ordered by (ex: date_descending),
	 *									as specified in 'constant.php'.
	 *	@param	int		$pos			The index of the first Portfolio to return in the set 'order_by' specifies.
	 *
	 *	@return	array|bool				An array of Portfolio objects if successful, false otherwise.
	 */
	public static function getIncludedPortfolios($count, $order_by, $pos)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}
		//TODO: check privileges here

	    return false;
	}


	/**
	 *	Retrieve a number of public Portfolio objects.
	 *
	 *	Retrieves a specified number of Portfolio objects. Public Portfolios are Portfolio objects
	 *	with their 'private' property set to false.
	 *
	 *	@param	int		$count			The number of Portfolio objects desired (0 = return all such Portfolios).
	 *	@param	int		$order_by		The field that the Portfolios should be ordered by (ex: date_descending),
	 *									as specified in 'constant.php'.
	 *	@param	int		$pos			The index of the first Portfolio to return in the set 'order_by' specifies.
	 *
	 *	@return	array|bool				An array of Portfolio objects if successful, false otherwise.
	 */
	public static function getPublicPortfolios($count, $order_by, $pos)
	{
	    return false;
	}


	/**
	 *	Add a Portfolio as a sub-Porfolio of another Portfolio.
	 *
	 *	Adds a specific Portfolio as a 'child' to another Portfolio as its 'parent'.
	 *	In the event of an attempt at a circular reference, the method will return false.
	 *	Calling user must have submission privileges on the parent Portfolio, as well as
	 *	ownership privieges on the child Portfolio.
	 *
	 *	@param	int		$parentId		The identifier of the parent Portfolio object.
	 *	@param	int		$childId		The identifier of the child sub-Portfolio object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addSubPortfolio($parentId, $childId)
	{
		if ($parentId == $childId)
		{
	   		return false; // Can't make a portfolio its own sub-portfolio
		}

		$parent = self::getPortfolio($parentId);
		$child = self::getPortfolio($childId);

		//TODO: check submission/ownership privileges here
		// $parentPortfolio->permissions
		// $childPortfolio->permissions

		// Check to make sure that both portfolios were found
		if (!$parent || !$child)
		{
			return false;
		}

		// Check to make sure no circular references
		if (self::portfolioHasCircularRefs($parentId, $childId))
		{
			return false;
		}

		return $parent->addSubPortfolio($childId);
	}

	/**
	 *	Checks the given Portfolio's children for references to the parent Portfolio.
	 *
	 *	Recursively check all sub-Portfolios of the child for a reference to the parent.
	 *	Assumes that until this point, there have been no circular reference created below the parent already.
	 *	NOTE: PHP will smash the stack if there are 100-200 levels of recursion.
	 *
	 *	@param	int		$parentId		The indentifier of the parent Portfolio for whom we are
	 *									cocnerns a circular reference might exist beneath.
	 *	@param	int		$portId			The identifier of the Portfolio whose children are to be checked for 
	 * 									circular backreferences.
	 *
	 *	@return	bool					True if there is a circular reference below the parent
	 * 									through the child, false otherwise.
	 */
	private static function portfolioHasCircularRefs($parentId, $portId)
	{
		if ($portfolio = self::getPortfolio($portId))
		{
			$children = $portfolio->children;
		}

		foreach ($children as $childId=>$isPortfolio)
		{
			if ($childId == $parentId ||	// (C) Ross Trundy
				($isPortfolio && self::portfolioHasCircularRefs($parentId, $childId)))
			{
				return true;
			}
		}

		return false;
	}


	/**
	 *	Add a Project object to a Portfolio object.
	 *
	 *	Adds a specific Project object as a 'child' to a Portfolio object as its 'parent'.
	 *	Calling user must have submission privileges to the parent Portfolio, and owner
	 *	privileges to the child Project
	 *	
	 *	@param	int		$parentId		The identifier of the parent Portfolio object.
	 *	@param	int		$childId		The identifier of the child Project object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addProjectToPortfolio($parentId, $childId)
	{
		if (!$parent = self::getPortfolio($parentId))
		{
			return false;
		}
		if (!$project = ProjectController::viewProject($childId))	// Requires 'viewing' (or higher, assumed) permissions on the Project
		{
			return false;
		}

		//TODO: check submission/ownership privileges here
		// $parentPort->permissions
		// $project->permissions

		return $parent->addProject($childId);
	}


	/**
	 *	Remove a 'child' object from a Portfolio object.
	 *
	 *	Removes a 'child' object (a Project or sub-Portfolio) from its 'parent' object (a Portfolio).
	 *	Calling user must have ownership privileges of the Portfolio object.
	 *	
	 *	@param	int		$parentId		The identifier of the parent Portfolio object.
	 *	@param	int		$childId		The identifier of the child object.
	 *	@param	bool	$isPortfolio	True if the child is a sub-Portfolio, false otherwise.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function removeChildFromPortfolio($parentId, $childId, $isPortfolio)
	{
		if (!$parent = self::getPortfolio($parentId))
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $parent->permissions

		return $parent->removeChild($childId, $isPortfolio);
	}


	/**
	 *	Add a permission level to a Group in regards to a Portfolio.
	 *
	 *	Appends a specified permission level to the Group's current list of permissions.
	 *	The appended permission cascades down the Portfolio tree (i.e., the Group will have permission for
	 *	all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$portId			The identifier of the Portfolio the Group is being assigned to.
	 *	@param	int		$grpId			The identifier of the Group object recieving permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being granted to the Group object,
	 *	  								as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addPortfolioPermissionsForGroup($portId, $groupId, $permission)
	{
		if (!$portfolio = self::getPortfolio($portId))
		{
			return false;
		}
		if (!$group = GroupController::viewGroup($groupId))	// Requires 'viewing' (or higher, assumed) permissions on the Group
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $portfolio->permissions

		return $portfolio->addPermissionForGroup($groupId, $permission);
	}


	/**
	 *	Remove a permission from a Group in regards to a Porfolio object.
	 *	
	 *	Removes a permission from a Group object associated with a Portfolio. The permission cascades 
	 *	down the Portfolio tree (i.e., the Group will have the permission revoked for all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$portId			The identifier of the Portfolio the Group's permissions are revoked from.
	 *	@param	int		$groupId		The identifier of the Group object losing permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being removed from the Group object,
	 *									as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function removePortfolioPermissionsForGroup($portId, $groupId, $permission)
	{
		if (!$portfolio = self::getPortfolio($portId))
		{
			return false;
		}
		if (!$group = GroupController::viewGroup($groupId))	// Requires 'viewing' (or higher, assumed) permissions on the Group
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $portfolio->permissions
		
		return $portfolio->removePermissionForGroup($groupId, $permission);
	}
}


?>
