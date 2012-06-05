<?php
/**
 * Library of global constants in the system.
 *
 * @package Constants
 */

//for testing purposes
error_reporting(E_ALL);

/**
 * Permission types.
 * Constant mapping of integer values to standard permission names.
 * Permissions constitute a heirarchy, whereby each permission also grants all permissions
 * below it (i.e. EDIT privilege also grants READ and SUBMIT to the User/Group)
 */
/**
 * OWNER
 * Has overarching total permission of an object (including deletion).
 */
DEFINE("OWNER",	1);
/**
 * WRITE
 * Has ability to write to or add to an object (i.e. can add to a Portfolio).
 */
DEFINE("WRITE", 2);
/**
 * EDIT
 * Has the ability to write to an object's properties, but not add additional objects
 * (i.e. cannot add to Portfolio, Group, etc. but can modify name, etc.).
 */
DEFINE("EDIT", 	3);
/**
 * READ
 * Has the ability to read an object's values only.
 */
DEFINE("READ",	4);
/**
 * SUBMIT
 * Has the ability to submit resources as sub-resources (i.e. submit a Project/Portfolio to a Portfolio)
 */
DEFINE("SUBMIT", 5);


/**
 * Project types.
 */
DEFINE("ARTICLE", 1);
DEFINE("GALLERY", 2);

/**
 *	Statuses of Portfolio children
 */
DEFINE("PUBLIC", 0);	// Child is fully viewable to Users with READ or higher privileges on the parent Portfolio
DEFINE("PRIVATE", 1);	// Child is only viewable by Users with WRITE or higher permissions on the parent Portfolio
DEFINE("SUBMITTED", 2);	// Child is awaiting approval from a submissions, and can be viewable by Users with WRITE 
						//	or higher permissions on the parent Portfolio

DEFINE("MIN_USERNAME_LENGTH", 3);

function arrayFlatten($array)
{
	$ret = array();
	array_walk_recursive($array, function($value) use(&$ret) {
		$ret[] = $value;
	});

	return $ret;
}

?>
