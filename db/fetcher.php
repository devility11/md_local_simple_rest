<?php

/**
 * File containing enrolled users
 *
 * @package    local_simple_rest
 * @copyright  2019 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



defined('MOODLE_INTERNAL') || die();

/**
 * Class used to list and count online users
 *
 * @package    local_simple_rest
 * @copyright  2019 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class Simple_Rest_Fetcher {
 
   
    public function getEnrolledUsers(string $contextId): array {
        global $USER, $DB;
       
        if(!$contextId) { return array(); }
        
        $result = array();
        $result = $DB->get_records_sql('
            SELECT 
                u.*
            FROM 
                mdl_user u, mdl_role_assignments r
            WHERE
                u.id=r.userid AND r.contextid = ?', array($contextId));
        
        return $result;
    }
    
      
    public function getCourseName(string $courseId): object {
        global $DB;
        
        if(!$courseId) { return array(); }
        
        $result = array();
        $result = $DB->get_record_sql('
            SELECT 
                fullname
            FROM 
                mdl_course
            WHERE
                id = ?', array($courseId));
        
        return $result;
    }   
}
