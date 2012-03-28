<?php

require_once(dirname(__FILE__) . '/../libraries/Idiorm/idiorm.php');
require_once(dirname(__FILE__) . '/../libraries/Paris/paris.php');
include_once(dirname(__FILE__) . '/../libraries/constant.php');

ORM::configure('mysql:host=localhost;dbname=mj_dev');
ORM::configure('username', 'asap');
ORM::configure('password', 'asap4u');

DEFINE("USER_ID", 2);

/**
 *	Portfolio model object
 *
 *	A Portfolio object represents a single row in the REPO_Portfolios table, along with
 *	all associated data derived from its relations.
 *
 *	Information retrieved through relations:
 *	- permissions: specified based on the requesting user. Return value corresponds
 *	  to an enumerable value as specified in 'constants.php'.
 *	- children: any children Portfolio or Project objects underneath the current one.
 *		'children' object is an array of tuples consisting of:
 *		x identifier of child Portfolio / Project object.
 *		x boolean value specifying whether the child is a sub-Portfolio or Project.
 *
 * 	@package Models
 */
class Portfolio extends Model
{
	public static $_table = 'REPO_Portfolios';
	public static $_id_column = 'port_id';


	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			$result = ORM::for_table('REPO_Portfolio_access_map')
				->table_alias('access')
				->select('access.access_type')
				->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
				->where('access.port_id', $this->id())
				->where('AUTH_Group_user_map.user_id', USER_ID)
				->find_many();
			return $result;
			break;

		default:
			parent::__get($name);
			break;
		}
	}

	public function __set($name, $value)
	{
		switch ($name)
		{
		case 'permissions':
			break;
		default:
			parent::__set($name, $value);
			break;
		}
	}
}

?>
