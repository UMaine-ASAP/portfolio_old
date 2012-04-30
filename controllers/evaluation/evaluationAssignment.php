<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/evaluation/EvaluationAssignment.php');

/**
 * Evaluation Assignment controller.
 *
 * @package Controllers
 */
class EvaluationAssignmentController
{
	/************************************************************************************
	 * Evaluation Assignment object management											*
	 ***********************************************************************************/

	/**
	 *	Creates a new Evaluation Assignment object in the system.
	 *
	 *	The creating User must have the required privileges on the Group to which the Evaluation belongs
	 *	(if a Class is specified).
	 *
	 *	@param	int							$group_id		Identifier of the Group the Evaluation belongs to (optional)
	 *	@param	int							$form_id		Identifier of the form the Evaluation is based on
	 *  @param  int  						$evaluated_id	Identifier of the item/user to be evaluated
	 *  @param 	string 						$type 					
	 *  @param 	date('MM-DD-YYYY')|NULL		$due_date		The due date for this evaluation, null for no due date (optional)
	 *  @param  int 						$evaluator_id 	Identifier of the user to evaluate the item/user being evaluated
	 *
	 *	@return object|bool					The created Evaluation object if successful, false otherwise
 	 */
	public static function assignEvaluations($group_id, $form_id, $evaluated_id, $type, $due_date, $evaluator_id)
	{
		if (//(!$user_id = EvaluationController::get_current_user_id()) ||
			(!$evaluation = Model::factory('Evaluation')->create()))
		{
			return false;
		}
				
		if (!is_null($group_id) && $class = ClassController::getClass($class_id))
		{
			// Check for permissions on Group object here
			// $class->permissions
		}
		$evaluation->created = date();

		//@TODO: finish evaluation code ...

		// Add current User as OWNER
		$evaluation->assigned_by_user_id = $user_id->id();

		if (!$evaluation->save())
		{
			$evaluation->destroy();		// Assume these succeed
			return false;
		}

		return $evaluation;
	}


}

