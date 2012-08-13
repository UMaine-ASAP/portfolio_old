<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/authentication.php');
require_once('controllers/class.php');
require_once('models/section.php');

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
     * @param type $classID
     * @param type $sectionNumber
     * @param type $daySched
     * @param type $time
     * @param type $instructorID
     * @param type $semester
     * @param type $year
     * @param type $designator
     * @param type $description 
     * 
     * @return True if the section creation succeeded, false otherwise 
     */
    public function createSection($classID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        if (!$userID = AuthenticationController::getCurrentUserID())
        {
            return false;
        }
        
        if (is_null($sectionNumber) || is_null($daySched) || is_null($time) || is_null($instructorID) || is_null($semester) || is_null($year) || is_null($designator) || is_null($description))
        {
            error_log("Parameters passed to createSection must not be null");
            return false;
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
     *
     * @param type $classID 
     */
    public function addClassToSection($sectionID, $classID)
    {
        //TODO: placeholder
    }
    
    /**
     *
     * @param type $sectionID
     * @param type $classID 
     */
    public function removeClassFromSection($sectionID, $classID)
    {
        //TODO: placeholder
    }
    
    
    public function editSection($sectionID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        //TODO: placeholder
    }
}

?>
