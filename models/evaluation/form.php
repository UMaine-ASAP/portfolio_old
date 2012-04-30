<?php

/**
 * A Form object represents a single row in the EVAL_Form table.
 *
 * @package Models
 */

class Form extends Model
{
	public static $_table = "EVAL_Forms";
	public static $_id_column = "form_id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'Components':
				$result = Model::factory('FormComponentMap')
					->where('form_id', $this->id())
					->find_many();
				
				$return = array();
				foreach ($result as $map)
				{	// De-reference ORM object
					$return[] = $map->component_id;
				}
				return $return;
			break;
			default:
				return parent::__get($name);
			break;
		}
	}

}

