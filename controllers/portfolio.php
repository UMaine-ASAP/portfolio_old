<?php
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

//ORM::configure('mysql:host=kenai.asap.um.maine.edu;dbname=mainejournal_dev');
//ORM::configure('username', 'root');


/**
 *	Portfolio object
 *
 *	A Portfolio object represents a single row in the REPO_Portfolios table, along with
 *	all associated data derived from its relations.
 *
 *	Information retrieved through relations:
 *	- permissions: specified based on the requesting user. Return value corresponds
 *	  to an enumerable value as specified in 'constants.php'.
 *	- children: any children Portfolio or Project objects underneath the current one.
 *		'children' object is an array of tuples consisting of:
 *		- identifier of child Portfolio / Project object.
 *		- boolean value specifying whether the child is a sub-Portfolio or Project.
 *
 *********************************************************************************************/


/**
 *	Create a new Portfolio object with the specified parameters and the current
 *	user as the owner.
 *	- 'title' is a string specifying the title of the Portfolio (255 char max).
 *	- 'description' is a plain-text string describing the Portfolio (2^16 char max).
 *	- 'private' is a boolean specifying whether or not the Portfolio is considered private
 *	  (true = private, false = public).
 *	Returns: the created Portfolio object if successful, false otherwise.
 *
 *********************************************************************************************/
function createPortfolio($title, $description, $private)
{
	$port = Model::factory('Portfolio') -> create();
	$port->title = $title;
	$port->description = $description;
	$port->private = $private;

	$port->save();
	return true;
}


/**
 *	Edit a specific Portfolio object that the current user has 'editing' (atleast)
 *	privileges for.
 *	- 'id' is the unique identifier of the Portfolio being edited.
 *	- 'title' is a string specifying the title of the Portfolio (255 char max).
 *	- 'description' is a plain-text string describing the Portfolio (2^16 char max).
 *	- 'private' is a boolean specifying whether or not the Portfolio is considered private
 *	  (true = private, false = public).
 *	Returns: true if successfully edited, false otherwise.
 *
 *********************************************************************************************/
function editPortfolio($id, $title, $description, $private)
{
	//TODO: check privileges here
	$port = Model::factory('Portfolio')
		-> find_one($id);

	if (!$port)
	{
		return false;
	}

	$port->title = $title;
	$port->description = $description;
	$port->private = $private;
	$port->save();

	return true;
}


/**
 *	Delete a specific Portfolio object that the current user owns.
 *	NOTE: Does not delete Projects or sub-Portfolios.
 *	- 'id' is the unique identifier of the Portfolio to be deleted.
 *	Returns: true if successfully deleted, false otherwise.
 *
 *********************************************************************************************/
function deletePortfolio($id)
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
 *	- 'id' is the specified identifier of the Portfolio to be retrieved.
 *	Returns: the Portfolio object requested if successful, false otherwise.
 *
 *********************************************************************************************/
 function getPortfolio($id)
 {
 	return Model::factory('Portfolio')
 		-> find_one($id);
 }


/**
 *	Retrieve a specified number of Portfolio objects the current user is a member of.
 *	A User is a 'member' of a Portfolio if they have any privileges specific to them
 *	(i.e. higher than public-level access).
 *	- 'count' is the number of Portfolio objects desired.
 *	- 'order_by' is the field that the Portfolios should be ordered by (ex: date_descending),
 *	  as specified in 'constants.php'.
 *	- 'pos' is the index of the first Portfolio to return in the set 'order_by' specifies.
 *	Returns: an array of Portfolio objects if successful, false otherwise.
 *
 *********************************************************************************************/
 function getMemberPortfolios($count, $order_by, $pos)
 {
	 return false;
 }


/**
 *	Retrieve a specified number of Portfolio objects the current User is included in.
 *	A User is 'included in' a Portfolio if they have any Projects they are a 'member' of
 *	included in the Portfolio or its sub-Portfolios.
 *	- 'count' is the number of Portfolio objects desired (0 = return all such Portfolios).
 *	- 'order_by' is the field that the Portfolios should be ordered by (ex: date_descending),
 *	  as specified in 'constants.php'.
 *	- 'pos' is the index of the first Portfolio to return in the set 'order_by' specifies.
 *	Returns: an array of Portfolio objects if successful, false otherwise.
 *
 *********************************************************************************************/
 function getIncludedPortfolios($count, $order_by, $pos)
 {
	 return false;
 }


/**
 *	Retrieve a specified number of public Portfolio objects.
 *	- 'count' is the number of Portfolio objects desired (0 = return all such Portfolios).
 *	- 'order_by' is the field that the Portfolios should be ordered by (ex: date_descending),
 *	  as specified in 'constants.php'.
 *	- 'pos' is the index of the first Portfolio to return in the set 'order_by' specifies.
 *	Returns: an array of Portfolio objects if successful, false otherwise.
 *
 *********************************************************************************************/
 function getPublicPortfolios($count, $order_by, $pos)
 {
	 return false;
 }


/**
 *	Add a specific Portfolio as a sub-Porfolio of a specified Portfolio.
 *	NOTE: Must take care to prevent circular references!
 *	- 'parent' is the identifier of the parent Portfolio object.
 *	- 'child' is the identifier of the child sub-Portfolio object.
 *	Returns: true if successful, false otherwise.
 *
 *********************************************************************************************/
 function addSubPortfolio($parent, $child)
 {
 	if ($parent == $child)
 	{
 		return false; //can't make a portfolio its own sub-portfolio
 	}

 	$parentPortfolio = Model::factory('Portfolio')
 		-> find_one($parent);

 	$childPortfolio = Model::factory('Portfolio')
 		-> find_one($child);

    //check to make sure that both portfolios were found
 	if ($parentPortfolio == false || $childPortfolio == false)
 	{
 		return false;
 	}


 }


/**
 *	Add a specific Project object to a specific Portfolio object.
 *	- 'parent' is the identifier of the parent Portfolio object.
 *	- 'child' is the identifier of the child Project object.
 *	Returns: true if successful, false otherwise.
 *
 *********************************************************************************************/
 function addProjectToPortfolio($parent, $child)
 {
 	$project = ProjectController::getProject($child);
 	$parentPort = getPortfolio($parent);

 	if (!$project || !$parentPort)
 	{
 		return false;
 	}

 	$collectionMap = Model::factory('collection_project_map')->create();
 	$collectionMap->collect_id = $parentPort->collect_id;
 	$collectionMap->proj_id = $project->proj_id;

 	$collectionMap->save();

	 return true;
 }


/**
 *	Remove a specific child object from a specific Portfolio object.
 *	- 'parent' is the identifier of the parent Portfolio object.
 *	- 'child' is the identifier of the child object.
 *	- 'isSubPortfolio' is a boolean indicating whether a child is a sub-Portfolio or not
 *	  (true = child is sub-Portfolio, false = child is not sub-Portfolio).
 *	Returns: true if successful, false otherwise.
 *
 *********************************************************************************************/
 function removeChildFromPortfolio($parent, $child, $isSubPortfolio)
 {
	 return false;
 }


/**
 *	Gets permissions for a specific Group object in regards to a specific Portfolio object.
 *	- 'portfolio' is the identifier of the Portfolio the Group is being assigned to.
 *	- 'group' is the Group object recieving permissions for the Portfolio.
 *	Returns: an array of all permissions the specific Group object has on the specific
 *	Portfolio object, as specified in 'constants.php'. If no permissions, returns empty array.
 *
 *********************************************************************************************/
 function getPortfolioPermissionsForGroup($portfolio, $group)
 {
	 return array();
 }


/**
 *	Adds a specific permission to a specific Group object in regards to a specific Portfolio 
 *	object.
 *	NOTE: Permission cascades down the Portfolio tree (i.e., the Group will have permission for
 *	all sub-Portfolios as well).
 *	- 'portfolio' is the identifier of the Portfolio the Group is being assigned to.
 *	- 'group' is the Group object recieving permissions for the Portfolio.
 *	- 'permission' is the type of permission being granted to the Group object,
 *	  as specified in 'constants.php'.
 *	Returns: true if successful, false otherwise.
 *
 *********************************************************************************************/
 function addPortfolioPermissionsForGroup($portfolio, $group, $permission)
 {
	return false;
 }


/**
 *	Removes a specific permission from a specific Group object in regards to a specific Porfolio
 *	object.
 *	NOTE: Permission cascades down the Portfolio tree (i.e., the Group will have permission
 *	revoked for	all sub-Portfolios as well).
 *	- 'portfolio' is the identifier of the Portfolio the Group's permissions are revoked from.
 *	- 'group' is the Group object losing permissions for the Portfolio.
 *	- 'permission' is the type of permission being removed from the Group object,
 *		as specified in 'constants.php'.
 *	Returns: true if successful, false otherwise.
 *
 *********************************************************************************************/
 function removePortfolioPermissionsForGroup($portfolio, $group, $permission)
 {
	 return false;
 }



?>
