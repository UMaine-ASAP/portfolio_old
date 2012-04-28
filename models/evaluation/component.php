<?php

/**
 * A Component object represents a single row in the EVAL_Components table.
 *
 * @package Models
 */

class Component extends Model
{
	public static $_table 		= "EVAL_Components";
	public static $_id_column 	= "form_id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'type':
				$result = Model::factory('ComponentType')
					->where('type_id', parent::__get("type"))
					->find_one();

				return $result;
			break;
			case 'category':
				$result = Model::factory('ComponentCategory')
					->where('category_id', parent::__get("category"))
					->find_one();

				return $result;
			break;

			default:
				return parent::__get($name);
			break;
		}
	}

	public function addCategory()
	{

	}
}

class ComponentType extends Model
{
	public static $_table 		= "EVAL_Component_types";
	public static $_id_column 	= "type_id";
}

class ComponentCategory extends Model
{
	public static $_table 		= "EVAL_Component_categories";
	public static $_id_column 	= "category_id";
}

