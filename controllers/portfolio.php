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
		//TODO: check privileges here
		$port = Model::factory('Portfolio') -> create();
		$port->title = $title;
		$port->description = $description;
		$port->private = $private;

		// Create owner of the new Portfolio
		$group = createGroup(
			
			$group->name = $port->title . " owners";
		$group->description = "Portfolio owners";
		$group->private = 1;
		$group->save();
		
		$port->addPermissionForGroup($group->id(), OWNER);

		if (!$port->save())
		{
			return false;
		}
		
		return $port;
	}


	/**
	 *	Edit a specific Portfolio object.
	 *
	 *	Edits paramaters of a Portfolio object in the system that the current user has 'editing' (atleast)
	 *	privileges for.
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
	static function editPortfolio($id, $title, $description, $private)
	{
		$port = Model::factory('Portfolio')
			-> find_one($id);

		if (!$port)
		{
			return false;
		}

		//TODO: check privileges here

		if ($title) 		{ $port->title = $title; }
		if ($description) 	{ $port->description = $description; }
		if ($private) 		{ $port->private = $private; }
			
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
	 *
	 *	@parm	int		$id				The unique identifier of the Portfolio to be deleted.
	 *
	 *	@return	bool					True if successfully deleted, false otherwise.
	 */
	static function deletePortfolio($id)
	{
		$port = Model::factory('Portfolio')
			-> find_one($id);

		if (!port)
		{
			return false;
		}

		$port->delete();
		return true;
	}


	/**
	 *	Retrieve a specific Portfolio object.
	 *
	 *	Retrieves a Portfolio object, constructed as specified within the Portfolio model object description.
	 *
	 *	@param	int		$id				The specified identifier of the Portfolio to be retrieved.
	 *
	 *	@return	object|bool				The Portfolio object requested if successful, false otherwise.
	 */
	static function getPortfolio($id)
	{
		$port = Model::factory('Portfolio')
			->find_one($id);

		if ($port)
		{
			return $port;
		}
		else
		{
			return false;
		}
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
	 *
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child sub-Portfolio object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	 static function addSubPortfolio($parent, $child)
	 {
	 	if ($parent == $child)
	 	{
	 		return false; // Can't make a portfolio its own sub-portfolio
	 	}

	 	$parentPortfolio = Model::factory('Portfolio')
	 		-> find_one($parent);

	 	$childPortfolio = Model::factory('Portfolio')
	 		-> find_one($child);

	    // Check to make sure that both portfolios were found
	 	if ($parentPortfolio == false || $childPortfolio == false)
	 	{
	 		return false;
		}

		// Check to make sure no circular references



	 }


	/**
	 *	Add a Project object to a Portfolio object.
	 *
	 *	Adds a specific Project object as a 'child' to a Portfolio object as its 'parent'.
	 *	
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child Project object.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	 static function addProjectToPortfolio($parent, $child)
	 {
	 	$project = ProjectController::getProject($child);
	 	$parentPort = getPortfolio($parent);

	 	if (!$project || !$parentPort)
	 	{
	 		return false;
	 	}

	 	$collectionMap = Model::factory('CollectionProjectMap')->create();
	 	$collectionMap->collect_id = $parentPort->collect_id;
	 	$collectionMap->proj_id = $project->proj_id;

	 	$collectionMap->save();

		 return true;
	 }


	/**
	 *	Remove a 'child' object from a Portfolio object.
	 *
	 *	Removes a 'child' object (a Project or sub-Portfolio) from its 'parent' object (a Portfolio).
	 *	
	 *	@param	int		$parent			The identifier of the parent Portfolio object.
	 *	@param	int		$child			The identifier of the child object.
	 *	@param	bool	$isSubPortfolio	A bool indicating whether a child is a sub-Portfolio or not
	 *									(true = child is sub-Portfolio, false = child is not sub-Portfolio).
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	 static function removeChildFromPortfolio($parent, $child, $isSubPortfolio)
	 {
		 return false;
	 }


	/**
	 *	Retrieve permissions for a Group object in regards to a Portfolio object.
	 *
	 *	Gets all permissions set for a Portfolio object for a specific Group. 
	 *
	 *	@param	int		$portfolio		The identifier of the Portfolio the Group is being assigned to.
	 *	@param	int		$group			The identifier of the Group object recieving permissions for the Portfolio.
	 *
	 *	@return	array|bool				An array of all permissions the specific Group object has on the specific
	 *									Portfolio object, as specified in 'constant.php'. 
	 *									If no permissions, returns empty array.
	 *									If no portfolio with the specified identifier
	 */
	static function getPortfolioPermissionsForUser($portfolio, $user)
	{
		$port = Model::factory('Portfolio')
			->find_one($portfolio);
		
		if ($port)
		{
			return $port->getPortfolioPermissionsForUser($user);
		}
		else
		{
			return false;
		}
	}


	/**
	 *	Add a permission level to a Group in regards to a Portfolio.
	 *
	 *	Appends a specified permission level to the Group's current list of permissions.
	 *	The appended permission cascades down the Portfolio tree (i.e., the Group will have permission for
	 *	all sub-Portfolios as well).
	 *
	 *	@param	int		$portfolio		The identifier of the Portfolio the Group is being assigned to.
	 *	@param	int		$group			The identifier of the Group object recieving permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being granted to the Group object,
	 *	  								as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	 static function addPortfolioPermissionsForGroup($portfolio, $group, $permission)
	 {
		return false;
	 }


	/**
	 *	Remove a permission from a Group in regards to a Porfolio object.
	 *	
	 *	Removes a permission from a Group object associated with a Portfolio. The permission cascades 
	 *	down the Portfolio tree (i.e., the Group will have the permission revoked for all sub-Portfolios as well).
	 *
	 *	@param	int		$portfolio		The identifier of the Portfolio the Group's permissions are revoked from.
	 *	@param	int		$group			The identifier of the Group object losing permissions for the Portfolio.
	 *	@param	int		$permission		The type of permission being removed from the Group object,
	 *									as specified in 'constant.php'.
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	 static function removePortfolioPermissionsForGroup($portfolio, $group, $permission)
	 {
		 return false;
	 }
}


?>
