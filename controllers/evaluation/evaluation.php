<?php

// Libraries
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');

// Controllers
require_once('controllers/authentication.php');

// Models
require_once('models/evaluation/evaluation.php');
require_once('models/evaluation/component.php');
require_once('models/evaluation/score.php');
require_once('models/evaluation/form.php');

/**
 * Evaluation controller.
 *
 * @package Controllers
 */
class EvaluationController
{
	/************************************************************************************
	 * Evaluation object management														*
	 ***********************************************************************************/

	/**
	 *	Creates a new Evaluation object in the system.
	 *
	 *	The creating User must have the required privileges on the Group to which the Evaluation belongs
	 *	(if a Class is specified).
	 *
	 *	@param	int							$group_id		Identifier of the Group the Evaluation belongs to (optional)
	 *	@param	int							$form_id		Identifier of the form the Evaluation is based on
	 *  @param  int  						$evaluated_id	Identifier of the item/user to be evaluated
	 *  @param  int 						$evaluator_id 	Identifier of the user to evaluate the item/user being evaluated
	 *  @param 	int 						$type 			
	 *  @param 	date('MM-DD-YYYY')|NULL		$due_date		The due date for this evaluation, null for no due date (optional)
	 *
	 *	@return object|bool					The created Evaluation object if successful, false otherwise
 	 */
	public static function createEvaluation($form_id, $evaluated_id, $evaluator_id, $type, $due_date=null)
	{
		if (//(!$user_id = EvaluationController::get_current_user_id()) ||
			(!$evaluation = Model::factory('Evaluation')->create()))
		{
			return false;
		}
				
//		if (!is_null($group_id) && $class = ClassController::getClass($class_id))
//		{
			// Check for permissions on Group object here
			// $class->permissions
//		}
		$evaluation->created = date("Y-M-D H:m:s");

		$evaluation->form_id 			= $form_id;
		$evaluation->evaluated_id 		= $evaluated_id;
		$evaluation->evaluator_user_id 	= $evaluator_id;
		$evaluation->type 				= $type;
		$evaluation->due_date 			= $due_date;
		$evaluation->completed_date 	= NULL;
		$evaluation->status 			= 1;

		// Add current User as OWNER
		$evaluation->assigned_by_user_id = AuthenticationController::get_current_user_id();

		if (!$evaluation->save())
		{
			return false;
		}

		return $evaluation;
	}

	/**
	 * Submit evaluation scores and update status appropriately
	 * 
	 * @param 	int 	$id 		Identifier of the Evaluation object to get
	 * @param 	array 	$scores 	Array of scores in the format (component_id => value)
	 * 
	 * @return  bool 				Returns true if successful, false otherwise
	 */
	public static function submitScores($id, $scores) {
		$evaluation = EvaluationController::getEvaluation($id);

		if( ! $evaluation instanceOf Evaluation ) {
			return false;
		}

		//@TODO: Check permissions for submission?

		// Get quiz and check if all required fields have been submitted
		$components = FormController::buildQuiz($evaluation->form_id);
		foreach( $components as $component) {
			$component_id = $component->component_id;
			if( ($component->required == 1) && 
					( (in_array($component_id, array_keys($scores)) === false)  || ($scores[$component_id] == null) )) 
			{
				return false;
			}
		}

		//Create scores
		foreach ($scores as $component_id => $value) {
			//Check required fields - this should be done by getting the quiz ...
			$component = ComponentController::viewComponent($component_id);


			$new_score = Model::factory('Score')->create();

			$new_score->component_id 	= $component_id;
			$new_score->evaluation_id 	= $id;
			$new_score->value 			= $value;

			if (!$new_score->save()) { return false; }
		}

		//Update evaluation status
		$evaluation->status = 2;

		if(!$evaluation->save()) { return false; }

		return true;
	}

	/**
	 *	Gets a specific Evaluation object for private use.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int			$id		Identifier of the Evaluation object to get
	 *
	 *	@return	object|bool			The Evaluation object if found, false otherwise
	 */
	private static function getEvaluation($id)
	{
		return Model::factory('Evaluation')->find_one($id);
	}

	/**
	 * Get evaluation results for complete evaluations
	 * 
	 * 
	 * @param 	int 		$id 	Identifier of the Evaluation results to get
	 * 
	 * @return array|bool 			Array of results if available, false if unsuccessful
	 */
	public static function getEvaluationResults($id)
	{
		$evaluation = self::getEvaluation($id);
		if( $evaluation->status->name != 'complete' )
		{
			return false;
		}

		//@TODO: format scores and return
	}


}

