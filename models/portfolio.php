<?php

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

?>
