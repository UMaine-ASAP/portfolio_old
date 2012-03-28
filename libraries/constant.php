<?php
/**
 * Library of global constants in the system.
 *
 * @package Constants
 */


/**
 * Global ID column overrides.
 */
ORM::configure('id_column_overrides', array(
	'REPO_Portfolios' => 'port_id',
));


/**
 * Permission types.
 * Constant mapping of integer values to standard permission names.
 */
/**
 * OWNER
 * Has overarching total permission of an object (read, write, edit, delete).
 */
DEFINE("OWNER",	0);
/**
 * WRITE
 * Has ability to write to or add to an object (i.e. can add to a Portfolio).
 */
DEFINE("WRITE", 1);
/**
 * EDIT
 * Has the ability to write to an object's properties, but not add additional objects
 * (i.e. cannot add to Portfolio, Group, etc. but can modify name, etc.).
 */
DEFINE("EDIT", 	2);
/**
 * READ
 * Has the ability to read an object's values only.
 */
DEFINE("READ",	3);


?>
