<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/access_map_model.php');
require_once('models/mappings.php');
require_once('controllers/user.php');
require_once('controllers/authentication.php');
require_once('controllers/section.php');
require_once('controllers/portfolio.php');

/**
 *	The object pertaining to "templates" or non-instantiated, saved Assignments 
 *	by instructors or otherwise.
 *	
 *	@property-read	array	permissions		Array of permission levels specific to the requesting user
 *	@property-read	object	owner			User object of the User who currently owns the Assignment
 *	@property-read	array	instances		Array of AssignmentInstances derived from this Assignment
 *
 *	@package Models
 */
class Assignment extends AccessMapModel
{
	public static $_table = "REPO_Assignments";
	public static $_id_column = "assign_id";
	public static $_access_map_name = "AssignmentAccessMap";
	public static $_access_table = "REPO_Assignment_access_map";

	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'instances':
			return Model::factory('AssignmentInstance')
				->where('assign_id', $this->id())
				->find_many();
			break;

		default:
			return parent::__get($name);
			break;
		}
	}

	/**
	 *	Adds a User with OWNER permissions to this Assignment.
	 *
	 *	@param	int		$id		Identifier of the User to give OWNER permissions to
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public function addOwner($id)
	{
	}

	/**
	 *	Removes a User with OWNER permissiosn from this Assignment.
	 *
	 *	@param	int		$id		Identifier of the User to remove OWNER permissions from
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public function removeOwner($id)
	{
	}

	/**
	 *	Overridden delete function to handle the deactivation of this Assignment.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function delete()
	{
		$this->deactivated = true;
		return $this->save();
	}

	/**
	 *	Allow for un-deletion of previously deactivated Assignments.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function unDelete()
	{
		$this->deactivated = false;
		return $this->save();
	}

	/**
	 *	Allow for the complete removal of the Assignment and all instantiations of it from the system.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function destroy()
	{
		// Delete all AssignmentInstances referencing this Assignment
		foreach ($this->instances as $instance)
		{
			$instance->delete();
		}	

		return parent::delete();
	}
}

/**
 *	The object pertaining to instantiated Assignment objects, assigned to 
 *	a section or other Group of users.
 *	
 *	@property-read	object	$section		Section object the Assignment has been instantiated for
 *	@property-read	object	$portfolio		Portfolio object the Assignment's works are contained within
 *	@property-read	array	$children		Associative array specifying children Portfolio or Project objects underneath the current one.
 *											Each object in the array is structured as follows:
 *												- Key = identifier of child Portfolio / Project object.
 *												- Value = 2-tuple(array) of the following:
 *															-- boolean value specifying whether the child is a sub-Portfolio or Project
 *												  				(true = child is sub-Portfolio, false = child is not sub-Portfolio) at index 0.
 *												  			-- Type of privacy the child object has, as specified in constant.php
 *	@property-read	object	$assignment		The Assignment object this Instance was derived from
 *
 *	@package Models
 */
class AssignmentInstance extends AccessMapModel
{
	public static $_table = "REPO_Assignment_instances";
	public static $_id_column = "instance_id";
	public static $_access_map_name = "AssignmentInstanceAccessMap";
	public static $_access_table = 'REPO_Assignment_instance_access_map';

	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'section':
			return SectionController::getSection($this->section_id);
			break;

		case 'portfolio':
			return PortfolioController::viewPortfolio($this->portfolio_id);
			break;

		case 'children':
			$result = Model::factory('PortfolioProjectMap')
				->where('port_id', $this->portfolio_id)
				->find_many();
			
			$return = array();
			foreach ($result as $map)
			{	// De-reference ORM object
				$return[$map->child_id] = array($map->child_is_portfolio, $map->child_privacy);
			}
			return $return;

		case 'assignment':
			return AssignmentController::viewAssignment($this->assign_id);
			break;

		case 'title':
			// Check whether this Instance has overridden its Assignment's title,
			// or if we need to pull the title from its Assignment
			if (($title = parent::__get('title')) ||
				($title = $this->__get('assignment')->title) ||
				(!$title = NULL))	// (c) Josh Komusin
			{
				return $title; 
			}
			break;

		case 'description':
			// See above
			if (($desc = parent::__get('description')) ||
				($desc = $this->__get('assignment')->description) ||
				(!$desc = NULL))
			{
				return $desc; 
			}
			break;

		case 'requirements':
			// See above
			if (($reqs = parent::__get('requirements')) ||
				($reqs = self::__get('assignment')->requirements) ||
				(!$reqs = NULL))
			{
				return $reqs; 
			}
			break;

		default:
			return parent::__get($name);
			break;
		}
	}

	/**
	 *	Add unit of work (Project or Portfolio) to this Instance as a submission needing approval.
	 *
	 *	@param	int		$work_id			Identifier of the piece of work to be added
	 *	@param	int		$work_is_portfolio	Whether or not piece of work is a Portfolio (true=portfolio)
	 *
	 *	@return	bool						True if successful, false otherwise
	 */
	public function submitWork($work_id, $work_is_portfolio)
	{
		// This is pretty kludgy (*should* use PortfolioController do to this,
		// but we need to circumvent typical access checks due to the current implementation
		// of permissions as a hierarchy). Should be changes when permission checking is
		// more robust.
		if ((!$port = Model::factory('Portfolio')->find_one($this->portfolio_id)) ||
			(!$map = Model::factory('PortfolioProjectMap')->create()))
		{
			return false;
		}
			
		$map->port_id = $this->portfolio_id;
		$map->child_id = $work_id;
		$map->child_is_portfolio = $work_is_portfolio;
		$map->child_privacy = SUBMITTED;

		return $map->save();
	}


	

}

?>
