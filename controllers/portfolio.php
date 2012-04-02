<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/group.php');

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
	 *	@param	string	$title			Specifies the title of the new Portfolio in plain-text (255 char max).
	 *	@param	string	$description 	Describes the new Portfolio in plain-text (2^16 char max).
	 *	@param	bool	$private		Specifies whether or not the Portfolio is considered private
	 *									(true = private, false = public).
	 *
	 *	@return	object|bool				The created Portfolio object if successful, false otherwise.
	 */
	static function createPortfolio($title, $description, $private)
	{
		//TODO: check creation privileges here
		//
		if (!$port = Model::factory('Portfolio')->create())
		{
			return false;
		}

		$port->title = $title;
		$port->description = $description;
		$port->private = $private;
		$port->owner_user_id = USER_ID;	// check user credentials
		
		if (!$port->save())
		{
			$port->delete();	// we assume this succeeds, else garbage collects in DB
			return false;
		}

		// Create owner of the new Portfolio
		$group = GroupController::createGroup($title . " owners", "Portfolio owners", 1);
		$port->addPermissionForGroup($group->id(), OWNER);
		
		return $port;
	}


	/**
	 *	Edit a specific Portfolio object.
	 *
	 *	Edits paramaters of a Portfolio object in the system.
	 *	Calling user must have editing privileges for the Portfolio object.
	 *
	 *	@param	int|null		$id				The unique identifier of the Portfolio object being edited, 
	 *											or null to leave untouched.
	 *	@param	string|null		$title			The title of the Portfolio to be set, in plain-text (255 char max),
	 *											or null to leave untouched.
	 *	@param	string|null		$description	The description of the Portfolio to be set, in plain-text (2^16 char max),
	 *											or null to leave untouched.
	 *	@param	bool|null		$private	 	Specifies whether or not the Portfolio is considered private
	 *											(true = private, false = public).
	 *
	 *	@return	bool					True if successfully edited, false otherwise.
	 */
	static function editPortfolio($id, $title = NULL, $description = NULL, $private = NULL)
	{
		//TODO: check edit privileges here

		if (!$port = PortfolioController::getPortfolio($id))
		{
			return false;
		}

		if (isset($title)) 			{ $port->title = $title; }
		if (isset($description)) 	{ $port->description = $description; }
		if (isset($private)) 		{ $port->private = $private; }
			
		if (!$port->save())
		{
			$port->delete();	// assume this succeeds (see above)
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
	static function deletePortfolio($id)
	{
		//TODO: check delete privileges here

		if (!$port = PortfolioController::getPortfolio($id))
		{
			return false;
		}

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
	static function viewPortfolio($id)
	{
		//TODO: check view privileges here

		return PortfolioController::getPortfolio($id);
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
	private function getPortfolio($id)
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
	static function getMemberPortfolios($count, $order_by, $pos)
	{
		//TODO: check privileges (if user is logged in) here

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
	static function getIncludedPortfolios($count, $order_by, $pos)
	{
		//TODO: check privileges (if user is logged in) here

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
	static function getPublicPortfolios($count, $order_by, $pos)
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
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child sub-Portfolio object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	static function addSubPortfolio($parent, $child)
	{
		//TODO: check submission/ownership privileges here

		if ($parent == $child)
		{
	   		return false; // Can't make a portfolio its own sub-portfolio
		}

		$parentPortfolio = PortfolioController::getPortfolio($parent);
		$childPortfolio = PortfolioController::getPortfolio($child);

		// Check to make sure that both portfolios were found
		if (!$parentPortfolio || !$childPortfolio)
		{
			return false;
		}

		// Check to make sure no circular references
		if (PortfolioController::portfolioHasCircularRefs($parent, $child))
		{
			return false;
		}

		$map = Model::factory('PortfolioProjectMap')->create();
		$map->port_id = $parent;
		$map->child_id = $child;
		$map->child_is_portfolio = 1;

		return $map->save();
	}

	/**
	 *	Checks the given Portfolio's children for references to the parent Portfolio.
	 *
	 *	Recursively check all sub-Portfolios of the child for a reference to the parent.
	 *	Assumes that until this point, there have been no circular reference created below the parent already.
	 *
	 *	@param	int		$parent			The indentifier of the parent Portfolio for whom we are
	 *									cocnerns a circular reference might exist beneath.
	 *	@param	int		$port			The identifier of the Portfolio whose children are to be checked for 
	 * 									circular backreferences.
	 *
	 *	@return	bool					True if there is a circular reference below the parent
	 * 									through the child, false otherwise.
	 */
	private function portfolioHasCircularRefs($parent, $port)
	{
		if ($portfolio = PortfolioController::getPortfolio($port))
		{
			$children = $portfolio->children;
		}

		foreach ($children as $id=>$isPortfolio)
		{
			if ($id == $parent)
			{
				return true;
			}
			elseif ($isPortfolio && PortfolioController::portfolioHasCircularRefs($parent, $id))
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
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child Project object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	static function addProjectToPortfolio($parent, $child)
	{
		//TODO: check submission/ownership privileges here

		$parentPort = PortfolioController::getPortfolio($parent);
		$project = ProjectController::getProject($child);

		if (!$project || !$parentPort)
		{
			return false;
		}

		$map = Model::factory('PortfolioProjectMap')->create();
		$map->port_id = $parentPort->id();
		$map->child_id = $project->id();
		$map->child_is_portfolio = 0;

		return $map->save();
	}


	/**
	 *	Remove a 'child' object from a Portfolio object.
	 *
	 *	Removes a 'child' object (a Project or sub-Portfolio) from its 'parent' object (a Portfolio).
	 *	Calling user must have ownership privileges of the Portfolio object.
	 *	
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	static function removeChildFromPortfolio($parent, $child)
	{
		//TODO: check ownership privileges here

		$map = Model::factory('PortfolioProjectMap')
			->where('port_id', $parent)
			->where('child_id', $child)
			->find_one();

	    return $map->delete();
	}


	/**
	 *	Add a permission level to a Group in regards to a Portfolio.
	 *
	 *	Appends a specified permission level to the Group's current list of permissions.
	 *	The appended permission cascades down the Portfolio tree (i.e., the Group will have permission for
	 *	all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$port			The identifier of the Portfolio the Group is being assigned to.
	 *	@param	int		$group			The identifier of the Group object recieving permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being granted to the Group object,
	 *	  								as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	static function addPortfolioPermissionsForGroup($port, $group, $permission)
	{
		//TODO: check ownership privileges here

		if (!$portfolio = PortfolioController::getPortfolio($port))
		{
			return false;
		}

		return $portfolio->addPermissionForGroup($group, $permission);
	}


	/**
	 *	Remove a permission from a Group in regards to a Porfolio object.
	 *	
	 *	Removes a permission from a Group object associated with a Portfolio. The permission cascades 
	 *	down the Portfolio tree (i.e., the Group will have the permission revoked for all sub-Portfolios as well).
	 *	Calling user must have ownership privileges for the Portfolio object.
	 *
	 *	@param	int		$port			The identifier of the Portfolio the Group's permissions are revoked from.
	 *	@param	int		$group			The identifier of the Group object losing permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being removed from the Group object,
	 *									as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	static function removePortfolioPermissionsForGroup($port, $group, $permission)
	{
		//TODO: check ownership privileges here
		
		if (!$portfolio = PortfolioController::getPortfolio($port))
		{
			return false;
		}

		return $portfolio->removePermissionForGroup($group, $perm);
	}
}


?>
