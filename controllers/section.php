<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/authentication.php');
require_once('controllers/class.php');
require_once('models/section.php');
require_once('controllers/group.php');
/**
 * 	Section controller.
 *
 * 	@package Controllers
 */
class SectionController
{
    /**
     *
     * @param type $sectionID
     * @return type 
     */
    public static function getSection($sectionID)
    {
        return Model::factory('Section')->find_one($sectionID);
    }
    
    /**
     * Creates a section with the specified parameters and saves it to the database.
     * @param type $classID The class that the section will belong to.
     * @param type $sectionNumber The section number.
     * @param type $daySched The day schedule of the section
     * @param type $time The time of the section
     * @param type $instructorID The ID of the section's instructor
     * @param type $semester The semester of the section
     * @param type $year The year of the section
     * @param type $designator The section desigator
     * @param type $description The section description
     * 
     * @return True if the section creation succeeded, false otherwise 
     */
    public function createSection($classID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        if (!$userID = AuthenticationController::getCurrentUserID())
        {
            return false;
        }
        
        foreach(func_get_args() as $arg)
        {
            if (is_null($arg))
            {
                error_log("Parameters passed to createSection must not be null. Offending parameter: $arg");
            }
        }
        
        if (!$class = ClassController::getClass($classID))
        {
            error_log("Class $classID does not exist");
            return false;
        }
        
        $section = Model::factory('Section')->create();
        
        $section->section_number = $sectionNumber;
        $section->day_sched = $daySched;
        $section->time = $time;
        $section->instructor_user_id = $instructorID;
        $section->semester = $semester;
        $section->year = $year;
        $section->designator = $designator;
        $section->description = $description;
        
        $result = $section->save();
        
        if ($result)
        {
            //if saving succeeded
            $section->addPermissionForUser($userID, OWNER);
            return $result;
        }
        
        return $result;
    }
    
    /**
     * Edits the section specified by $sectionID. Passing a variable as null results in it not being changed.
     * @param type $sectionID The ID of the section to edit
     * @param type $sectionNumber The new section number, or null
     * @param type $daySched The new day schedule, or null
     * @param type $time The new time, or null
     * @param type $instructorID The new instructor ID, or null
     * @param type $semester The new semester, or null
     * @param type $year The new year, or null
     * @param type $designator The new class designation, or null
     * @param type $description The new class description, or null
     * @return boolean True if the edit succeeded, false otherwise
     */
    public function editSection($sectionID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        if (!$userID = AuthenticationController::getCurrentUserID() ||!$user = UserController::getUser($userID) || !$section = self::getSection($sectionID) || !$section->havePermissionOrHigher(EDIT) || UserController::getUser($instructorID))
        {
            return false;
        }
        
        if (!is_null($sectionNumber)) $section->section_number = $sectionNuber;
        if (!is_null($daySched)) $section->day_sched = $daySched;
        if (!is_null($time)) $section->time = $time;
        if (!is_null($instructorID)) $section->instructor_user_id = $instructorID;
        if (!is_null($semester)) $section->semester = $semester;
        if (!is_null($year)) $section->year = $year;
        if (!is_null($designator)) $section->designator = $designator;
        if (!is_null($description)) $section->description = $description;
        
        return $section->save();
    }
    
    /**
     * Creates a group for the specified section, returning the group ORM object if successful, or false otherwise
     * @param type $sectionID
     * @return boolean 
     */
    public function createGroupForSection($sectionID)
    {
        //a few basic checks
        if (!$userID = AuthenticationController::getCurrentUserID() || !$user = UserController::getUser($userID) || !$section = self::getSection($sectionID))
        {
            return false;
        }
        
        //check to see if they have permissions
        if ($userID == $section->owner_user_id || $section->havePermissionOrHigher(OWNER))
        {
            //they do have permission
            $groupName = "Official group for " . $section->getClass()->title . "($sectionID)";
            
            //create the group automatically and then return it to the calling method
            return GroupController::createGroup($groupName, $groupName . " -- " . $section->instructor()->first . " " . $section->instructor()->last, false);
        }
        
        return false;
    }
}

?>
