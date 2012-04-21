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
		if ((!$port = Model::factory('Portfolio')->create()) ||
			(!$user = AuthenticationController::get_current_user()))
		{
			return false;
		}

		if (!is_null($title))	{ $port->title = $title; }
		else					{ return false; }
		if (!is_null($private))	{ $port->private = $private; }
		else					{ return false; }
		$port->description = $description;
		// Add current User as OWNER
		$port->addPermissionForUser($user, OWNER);

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
	 *	@param	string|null		$title			The title of the Portfolio to be set, in plain-text (255 char max),
	 *											or null to leave untouched.
	 *	@param	string|null		$description	The description of the Portfolio to be set, in plain-text (2^16 char max),
	 *											or null to leave untouched.
	 *	@param	bool|null		$private	 	Specifies whether or not the Portfolio is considered private
	 *											(true = private, false = public).
	 *
	 *	@return	bool							True if successfully edited, false otherwise.
	 */
	public static function editPortfolio($id, $title = NULL, $description = NULL, $private = NULL)
	{
		if (!$port = self::getPortfolio($id))
		{
			return false;
		}

		//TODO: check edit privileges here
		// $port->permissions

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
	 *	@param	int		$parent_id		The identifier of the parent Portfolio object.
	 *	@param	int		$child_id		The identifier of the child sub-Portfolio object.
	 *	@param	int		$privacy		Privacy level of the child within the parent Portfolio,
	 *									as defined in constant.php.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addSubPortfolio($parent_id, $child_id, $privacy)
	{
		if ($parent_id == $child_id)
		{
	   		return false; // Can't make a portfolio its own sub-portfolio
		}

		$parent = self::getPortfolio($parent_id);
		$child = self::getPortfolio($child_id);

		//TODO: check submission/ownership privileges here
		// $parentPortfolio->permissions
		// $childPortfolio->permissions

		// Check to make sure that both portfolios were found
		if (!$parent || !$child)
		{
			return false;
		}

		// Check to make sure no circular references
		if (self::portfolioHasCircularRefs($parent_id, $child_id))
		{
			return false;
		}

		return $parent->addSubPortfolio($child_id->$privacy);
	}

	/**
	 *	Checks the given Portfolio's children for references to the parent Portfolio.
	 *
	 *	Recursively check all sub-Portfolios of the child for a reference to the parent.
	 *	Assumes that until this point, there have been no circular reference created below the parent already.
	 *	NOTE: PHP will smash the stack if there are 100-200 levels of recursion.
	 *
	 *	@param	int		$parent_id		The indentifier of the parent Portfolio for whom we are
	 *									cocnerns a circular reference might exist beneath.
	 *	@param	int		$port_id			The identifier of the Portfolio whose children are to be checked for 
	 * 									circular backreferences.
	 *
	 *	@return	bool					True if there is a circular reference below the parent
	 * 									through the child, false otherwise.
	 */
	private static function portfolioHasCircularRefs($parent_id, $port_id)
	{
		if ($portfolio = self::getPortfolio($port_id))
		{
			$children = $portfolio->children;
		}

		foreach ($children as $child_id=>$arr)
		{
			if (($child_id == $parent_id) ||	// (C) Ross Trundy
				($is_portfolio && self::portfolioHasCircularRefs($parent_id, $child_id)))
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
	 *	@param	int		$parent_id		The identifier of the parent Portfolio object.
	 *	@param	int		$child_id		The identifier of the child Project object.
	 *	@param	int		$privacy		Privacy level of the child within the parent Portfolio,
	 *									as defined in constant.php.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addProjectToPortfolio($parent_id, $child_id)
	{
		if ((!$parent = self::getPortfolio($parent_id)) ||
			(!$project = ProjectController::viewProject($child_id)))	// Requires 'viewing' (or higher, assumed) permissions on the Project
		{
			return false;
		}

		//TODO: check submission/ownership privileges here
		// $parentPort->permissions
		// $project->permissions

		return $parent->addProject($child_id);
	}


	/**
	 *	Remove a 'child' object from a Portfolio object.
	 *
	 *	Removes a 'child' object (a Project or sub-Portfolio) from its 'parent' object (a Portfolio).
	 *	Calling user must have ownership privileges of the Portfolio object.
	 *	
	 *	@param	int		$parent_id		The identifier of the parent Portfolio object.
	 *	@param	int		$child_id		The identifier of the child object.
	 *	@param	bool	$is_portfolio	True if the child is a sub-Portfolio, false otherwise.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function removeChildFromPortfolio($parent_id, $child_id, $is_portfolio)
	{
		if (!$parent = self::getPortfolio($parent_id))
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $parent->permissions

		return $parent->removeChild($child_id, $is_portfolio);
	}


	/**
	 *	Add a permission level to a Group in regards to a Portfolio.
	 *
	 *	Appends a specified permission level to the Group's current list of permissions (or add a new Group).
	 *	The appended permission cascades down the Portfolio tree (i.e., the Group will have permission for
	 *	all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$port_id			The identifier of the Portfolio the Group is being assigned to.
	 *	@param	int		$grpId			The identifier of the Group object recieving permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being granted to the Group object,
	 *	  								as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addPortfolioPermissionsForGroup($port_id, $group_id, $permission)
	{
		if ((!$portfolio = self::getPortfolio($port_id)) ||
			(!$group = GroupController::viewGroup($group_id)))	// Requires 'viewing' (or higher, assumed) permissions on the Group
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $portfolio->permissions

		return $portfolio->addPermissionForGroup($group_id, $permission);
	}


	/**
	 *	Remove a permission from a Group in regards to a Porfolio object.
	 *	
	 *	Removes a permission from a Group object associated with a Portfolio. The permission cascades 
	 *	down the Portfolio tree (i.e., the Group will have the permission revoked for all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$port_id			The identifier of the Portfolio the Group's permissions are revoked from.
	 *	@param	int		$group_id		The identifier of the Group object losing permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being removed from the Group object,
	 *									as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function removePortfolioPermissionsForGroup($port_id, $group_id, $permission)
	{
		if ((!$portfolio = self::getPortfolio($port_id)) ||
			(!$group = GroupController::viewGroup($group_id)))	// Requires 'viewing' (or higher, assumed) permissions on the Group
		{
			return false;
		}

		//TODO: check ownership privileges here
		// $portfolio->permissions
		
		return $portfolio->removePermissionForGroup($group_id, $permission);
	}

	/**
	 *	Add a permission level to a User in regards to a Portfolio.
	 *
	 *	The calling User must have OWNER permissions on the specified Portfolio.
	 *
	 *	@param	int		$port_id		Identifier of the Portfolio to add permissions to
	 *	@param	int		$user_id		Identifier of the User to recieve permissions
	 *	@param	int		$permission		Identifier of the permission level we seek to give to the User
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public static function addPortfolioPermissionsForUser($port_id, $user_id, $permission)
	{
		if ((!$portfolio = self::getPortfolio($port_id)) ||
			(!in_array(OWNER, $portfolio->permissions)) ||
			(!$user = UserController::getUser($user_id)))
		{
			return false;
		}

		return $portfolio->addPermissionForUser($user_id, $permission);
	}

	/**
	 *	Remove a permission from a User in regards to a Portfolio object.
	 *
	 *	Plays by the same rules as the removal of a single User's permissions in the Portfolio model object.
	 *	Please see documentation there for problems.
	 *
	 *	@param	int		$port_id		Identifier of the Portfolio to remove permissions from
	 *	@param	int		$user_id		Identifier of the User to lose permissions
	 *	@param	int		$permission		Identifier of the permission level we seek to take from the User
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public static function removePortfolioPermissionsFromUser($port_id, $user_id, $permission)
	{
		if ((!$portfolio = self::getPortfolio($port_id)) ||
			(!in_array(OWNER, $portfolio->permissions)) ||
			(!$user = UserController::getUser($user_id)))
		{
			return false;
		}

		return $portfolio->removePermissionForUser($user_id, $permission);
	}
}


?>
