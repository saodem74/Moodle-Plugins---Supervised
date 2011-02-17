<?php

// This file is part of Moodle - http://moodle.org/
//
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
 * Library of interface functions and constants for module poasassignment
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the poasassignment specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_poasassignment
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('PREVENT_LATE_CHOICE', 1);
define('RANDOM_TASKS_AFTER_CHOICEDATE', 2);
define('PREVENT_LATE', 4);
define('SEVERAL_ATTEMPTS', 8);
define('NOTIFY_TEACHERS', 16);
define('NOTIFY_STUDENTS', 32);
define('ACTIVATE_INDIVIDUAL_TASKS', 64);
define('SECOND_CHOICE', 128);
define('TEACHER_APPROVAL', 256);
define('ALL_ATTEMPTS_AS_ONE', 512);
define('MATCH_ATTEMPT_AS_FINAL', 1024);

define('ADD_MODE', 0);
define('EDIT_MODE',1);
define('DELETE_MODE',2);
define('SHOW_MODE',3);
define('HIDE_MODE',4);

define('FULLRANDOM',0);
define('PARAMETERRANDOM',1);
define('STUDENTSCHOICE',2);

define('STR',0);
define('TEXT',1);
define('FLOATING',2);
define('NUMBER',3);
define('DATE',4);
define('FILE',5);
define('LISTOFELEMENTS',6);
define('MULTILIST',7);

define('TASK_RECIEVED',0);
define('ATTEMPT_DONE',1);
define('GRADE_DONE',2);
/**
 * If you for some reason need to use global variables instead of constants, do not forget to make them
 * global as this file can be included inside a function scope. However, using the global variables
 * at the module level is not a recommended.
 */

require_once(dirname(dirname(dirname(__FILE__))).'/lib/navigationlib.php');
require_once('model.php');
//global $NEWMODULE_GLOBAL_VARIABLE;
//$NEWMODULE_QUESTION_OF = array('Life', 'Universe', 'Everything');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $poasassignment An object from the form in mod_form.php
 * @return int The id of the newly inserted poasassignment record
 */
function poasassignment_add_instance($poasassignment) {
    global $DB;

    $poasassignment->timecreated = time();
    
    $poasassignmentmodelinstance = poasassignment_model::get_instance($poasassignment);
    $poasassignment->id=$poasassignmentmodelinstance->add_instance();
    poasassignment_grade_item_update($poasassignment);
    return $poasassignment->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $poasassignment An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function poasassignment_update_instance($poasassignment) {
    global $DB;
    
    $poasassignment->timemodified = time();
    $poasassignment->id = $poasassignment->instance;
    
    $poasassignmentmodelinstance = poasassignment_model::get_instance($poasassignment);
    $id=$poasassignmentmodelinstance->update_instance();
    poasassignment_grade_item_update($poasassignment);
    return $id;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function poasassignment_delete_instance($id) {
    global $DB;
    $poasassignment = $DB->get_record('poasassignment', array('id'=>$id));    
    $poasassignmentmodelinstance = poasassignment_model::get_instance($poasassignment);
    poasassignment_grade_item_delete($poasassignment);
    return $poasassignmentmodelinstance->delete_instance($id);
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function poasassignment_user_outline($course, $user, $mod, $poasassignment) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function poasassignment_user_complete($course, $user, $mod, $poasassignment) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in poasassignment activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function poasassignment_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Return grade for given user or all users.
 *
 * @param int $assignmentid id of assignment
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function poasassignment_cron () {
    return true;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of poasassignment. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $poasassignmentid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function poasassignment_get_participants($poasassignmentid) {
    return false;
}


/**
 * This function returns if a scale is being used by one poasassignment
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $poasassignmentid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function poasassignment_scale_used($poasassignmentid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("poasassignment", array("id" => "$poasassignmentid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of poasassignment.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any poasassignment
 */
function poasassignment_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('poasassignment', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
// function poasassignment_uninstall() {
    // return true;
// }
function poasassignment_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;

        default: return null;
    }
}

function poasassignment_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }
    
    require_course_login($course, true, $cm);

    /* if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    } */

    $fs = get_file_storage();
    if($filearea=='poasassignmentfiles') {
        array_shift($args); // ignore revision - designed to prevent caching problems only
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_poasassignment/$filearea/0/$relativepath";
    }
    if($filearea=='poasassignmenttaskfiles') {

        $taskvalueid = (int)array_shift($args);
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_poasassignment/$filearea/$taskvalueid/$relativepath";
        //echo "/$context->id/mod_poasassignment/$filearea/$taskvalueid/$relativepath";
    }
    if($filearea=='submissionfiles') {

        $submissionid = (int)array_shift($args);
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_poasassignment/$filearea/$submissionid/$relativepath";
        //echo "/$context->id/mod_poasassignment/$filearea/$submissionid/$relativepath";
    }
    if($filearea=='commentfiles') {

        $attemptid = (int)array_shift($args);
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_poasassignment/$filearea/$attemptid/$relativepath";
        //echo "/$context->id/mod_poasassignment/$filearea/$submissionid/$relativepath";
    }
    $file = $fs->get_file_by_hash(sha1($fullpath));
    /* if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        
        $resource = $DB->get_record('resource', array('id'=>$cminfo->instance), 'id, legacyfiles', MUST_EXIST);
       // if ($resource->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
           // return false;
       // }
        // if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_poasassignment', 'content', 0)) {
           // return false;
       // }
        // file migrate - update flag
        $resource->legacyfileslast = time();
        $DB->update_record('resource', $resource);
    } */

    // should we apply filters?
    $mimetype = $file->get_mimetype();
    if ($mimetype = 'text/html' or $mimetype = 'text/plain') {
        $filter = $DB->get_field('resource', 'filterfiles', array('id'=>$cm->instance));
    } else {
        $filter = 0;
    }

    // finally send the file
    send_stored_file($file, 86400, $filter, $forcedownload);
}

/**
 * Create grade item for given poasassignment
 *
 * @param object $poasassignment object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function poasassignment_grade_item_update($poasassignment, $grades=NULL) {

    $poasassignmentmodelinstance = poasassignment_model::get_instance($poasassignment);
    return($poasassignmentmodelinstance->grade_item_update($grades));
     
}

/**
 * Delete grade item for given poasassignment
 *
 * @param object $poasassignment object
 * @return object poasassignment
 */
function poasassignment_grade_item_delete($poasassignment) {
    $poasassignmentmodelinstance = poasassignment_model::get_instance($poasassignment);
    return($poasassignmentmodelinstance->grade_item_delete());
}

/**
 * Return grade for given user or all users.
 *
 * @param int $poasassignmentid id of poasassignment
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function poasassignment_get_user_grades($poasassignment, $userid=0) {
    global $CFG, $DB;

    /* if ($userid) {
        $user = "AND u.id = :userid";
        $params = array('userid'=>$userid);
    } else {
        $user = "";
    }
    $params['aid'] = $poasassignment->id;

    $sql = "SELECT u.id, u.id AS userid, s.grade AS rawgrade, s.submissioncomment AS feedback, s.format AS feedbackformat,
                   s.teacher AS usermodified, s.timemarked AS dategraded, s.timemodified AS datesubmitted
              FROM {user} u, {assignment_submissions} s
             WHERE u.id = s.userid AND s.assignment = :aid
                   $user";

    return $DB->get_records_sql($sql, $params); */
    if($userid) {
        // return user's last attempt rating
        $assignee = $DB->get_record('poasassignment_attempts',array('userid'=>$userid));
        $lastattempt = $DB->get_record('poasassignment_attempts',array('id'=>$assignee->lastattemptid));
        return $lastattempt->rating;
    }
    
}


/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $poasassignmentnode The node to add module settings to
 */
function poasassignment_extend_settings_navigation(settings_navigation $settings, navigation_node $poasassignmentnode) {
    //echo '123!!!!';
}

function poasassignment_extend_navigation(navigation_node $navigation, $course, $module, $cm) {
    global $PAGE,$DB;
    $tabs = array('tasksfields', 'tasks', 'view', 'criterions', 'submissions');

    foreach($tabs as $tab) {
        $tabtype = $tab.'_tab';
        require_once($tabtype.'.php');

        // If user has ability to view <tabname>_tab - add tab on panel
        $poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
        $tabinstance = new $tabtype($cm, $poasassignment);
        if ($tabinstance->has_ability_to_view()) {
            $navigation->add(get_string($tab,'poasassignment'),new moodle_url('/mod/poasassignment/view.php',array('id' => $cm->id, 'tab' => $tab)));
        }
    }
}