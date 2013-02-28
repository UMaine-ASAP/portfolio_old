<?php

/**
 * A Score object represents a single row in the EVAL_Scores table.
 *
 * @package Models
 */

class Score extends Model
{
	public static $_table 		= "EVAL_Scores";
	public static $_id_column 	= "id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'type':
				$assoc_component = Model::factory('Component')
					->where('component_id', $this->component_id)
					->find_one();
				return $assoc_component->type;
			break;

			case 'component':
				$assoc_component = Model::factory('Component')
					->where('component_id', $this->component_id)
					->find_one();					
				return $assoc_component;
			break;

			case 'evaluation':
				$assoc_evaluation = Model::factory('Evaluation')
					->where('evaluation_id', $this->evaluation_id)
					->find_one();
				return $assoc_evaluation;
			break;

			default:
				return parent::__get($name);
			break;
		}
	}

}

