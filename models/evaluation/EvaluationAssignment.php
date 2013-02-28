<?php

/**
 * An EvaluationAssignment object represents a single row in the EVAL_Evaluation_Assignment table.
 *
 * @package Models
 */

class EvaluationAssignment extends Model
{
	public static $_table 		= "EVAL_Evaluation_Assignment";
	public static $_id_column 	= "assignment_id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'evaluations':
				$evaluations = Model::factory('Evaluation')
					->where('assignment_id', $this->assignment_id)
					->find_many();
				
				$return = array();
				foreach ($evaluations as $evaluation)
				{	// De-reference ORM object
					$return[] = $evaluation->evaluation_id;
				}
				return $return;		
			break;


			default:
				return parent::__get($name);
			break;
		}
	}

}

