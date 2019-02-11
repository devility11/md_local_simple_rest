<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    simple_rest
 * @copyright  2019 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");
require_once("db/fetcher.php");

class local_simple_rest_external extends external_api {
    
    public static function enrolled_users() {
        $data = array();
        if(empty($_GET['courseid']) || !isset($_GET['courseid'])) {
            return json_encode("wrong courseid");		    
        }
        //DB queries
        $fetcher = new Simple_Rest_Fetcher();	    
        
        $context = context_course::instance($_GET['courseid']);
        $contextId = $context->id;
        if($contextId) {
            $result = $fetcher->getEnrolledUsers($contextId);
            if(count($result) > 0) {
                $course = $fetcher->getCourseName($_GET['courseid']);
                if(!$course->fullname || !isset($course->fullname) ) {
                    return json_encode("No data");
                }
                $data['course'] = $course->fullname;
                foreach($result as $r) {
                    $data[$r->id]['id'] = $r->id;
                    $data[$r->id]['country'] = $r->country;
                    $data[$r->id]['name'] = $r->lastname.' '.$r->firstname;
                }
            }
            return self::jsonToCsv(json_encode($data,JSON_UNESCAPED_SLASHES),false,true);
        }
        return json_encode("No data");
    }
        
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function enrolled_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id', VALUE_DEFAULT, 0)
            )
        );
    }
    
     /**
     * Returns description of method result value
     * @return external_description
     */
    public static function enrolled_users_returns() {
        return new external_value(PARAM_TEXT, 'List of the actual online users');
    }

    /* 
    * Usage:
    * 
    *     require '/path/to/json-to-csv.php';        
    *     // echo a JSON string as CSV
    *     jsonToCsv($strJson);
    *     
    *     // echo an arrayJSON string as CSV
    *     jsonToCsv($arrJson);
    *     
    *     // save a JSON string as CSV file
    *     jsonToCsv($strJson,"/save/path/csvFile.csv");
    *     
    *     // save a JSON string as CSV file through the browser (no file saved on server)
    *     jsonToCsv($strJson,false,true);
    *     
    *     
	*/     
    private function jsonToCsv ($json, $csvFilePath = false, $boolOutputFile = false) {
    
    	// See if the string contains something
        if (empty($json)) { die("The JSON string is empty!"); 	}
    
        // If passed a string, turn it into an array
        if (is_array($json) === false) { $json = json_decode($json, true); }	

        // If a path is included, open that file for handling. Otherwise, use a temp file (for echoing CSV string)
        if ($csvFilePath !== false) {
            $f = fopen($csvFilePath,'w+');
            if ($f === false) {
                die("Couldn't create the file to store the CSV, or the path is invalid. Make sure you're including the full path, INCLUDING the name of the output file (e.g. '../save/path/csvOutput.csv')");
            }
    	} else {
            $boolEchoCsv = true;
            if ($boolOutputFile === true) {
                $boolEchoCsv = false;
            }
            $strTempFile = 'csvOutput' . date("U") . ".csv";
            $f = fopen($strTempFile,"w+");
    	}
    
        $firstLineKeys = false;
        foreach ($json as $line) {
            if (empty($firstLineKeys)) {
                $firstLineKeys = array_keys($line);
                fputcsv($f, $firstLineKeys);
                $firstLineKeys = array_flip($firstLineKeys);
            }
      
            // Using array_merge is important to maintain the order of keys acording to the first element
            fputcsv($f, array_merge($firstLineKeys, $line));
    	}
        fclose($f);
    
        // Take the file and put it to a string/file for output (if no save path was included in function arguments)
        if ($boolOutputFile === true) {
            if ($csvFilePath !== false) {
                $file = $csvFilePath;
            } else {
                $file = $strTempFile;
            }
      
            // Output the file to the browser (for open/save)
            if (file_exists($file)) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Length: ' . filesize($file));
                readfile($file);
            }
    	} elseif ($boolEchoCsv === true) {
            if (($handle = fopen($strTempFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    echo implode(",",$data);
                    echo "<br />";
                }
                fclose($handle);
            }
    	}    
        // Delete the temp file
        unlink($strTempFile);    
    }     
            
}
