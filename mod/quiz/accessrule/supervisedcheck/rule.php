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
 * Implementaton of the quizaccess_supervisedcheck plugin.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');
require_once($CFG->dirroot . '/blocks/supervised/sessions/sessionstate.php');

/**
 * A rule for supervised block.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */
class quizaccess_supervisedcheck extends quiz_access_rule_base {

    public function getAppropriatedSessions($userid, $courseid, $lessontypes){
        global $DB;

        // Select all active sessions with $courseid.
        $sessions = $DB->get_records('block_supervised_session', array('state' => StateSession::Active, 'courseid'=>$courseid));

        // Filter sessions by lessontype and userid
        foreach($sessions as $id=>$session){
            // Check if user is in current session's group.
            $useringroup = $session->groupid == 0 ? true : groups_is_member($session->groupid, $userid);
            // Check if current session's lessontype is in passed $lessontypes array.
            $islessontype = in_array($session->lessontypeid, $lessontypes);
            if( !($useringroup && $islessontype) ){
                // Remove current session.
                unset($sessions[$id]);
            }
        }

        return $sessions;
    }




    public function prevent_access() {
        global $DB, $COURSE, $USER;
        // Check capabilities: teachers always have an access
        if(has_capability('block/supervised:supervise', context_course::instance($COURSE->id))){
            return false;
        }

        // Check if current user can start the quiz
        $lessontypesdb = array();
        switch($this->quiz->supervisedmode){
            case 0:     // No check required.
                return false;
                break;
            case 1:     // Check for all lesson types.
                $lessontypesdb = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));
                // We need to add special lessontype "Not specified" to have really all lesson types.
                $lessontypesdb[0] = new stdClass();
                $lessontypesdb[0]->id = 0;
                break;
            case 2:     // Check for custom lesson types.
                $lessontypesdb = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $this->quiz->id), null, 'lessontypeid as id');
                break;
        }

        // If we are here, check is required.
        // Just reorganize $lessontypesdb array.
        $lessontypes = array();
        foreach($lessontypesdb as $lessontype){
            $lessontypes[] = $lessontype->id;
        }

        $sessions = $this->getAppropriatedSessions($USER->id, $COURSE->id, $lessontypes);

        if (!empty($sessions)){
            // Ok, we have an active session(s) with appropriate lesson type. Now check an ip address.
            $isinsubnet = false;
            foreach($sessions as $session){
                $classroom = $DB->get_record('block_supervised_classroom', array('id'=>$session->classroomid));
                if(address_in_subnet($USER->lastip, $classroom->iplist)){
                    $isinsubnet = true;
                    break;
                }
            }
            if($isinsubnet){
                return false;
            }
            else{
                return get_string('iperror', 'quizaccess_supervisedcheck');
            }
        }
        else{
            return get_string('noaccess', 'quizaccess_supervisedcheck');
        }
    }

    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        if (empty($quizobj->get_quiz()->supervisedmode)) {
            return null;
        }

        return new self($quizobj, $timenow);
    }


    public static function add_settings_form_fields(
        mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $DB, $COURSE, $PAGE, $CFG;

        //Radiobuttons
        $radioarray = array();
        $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '', get_string('checknotrequired', 'quizaccess_supervisedcheck'), 0);
        $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '', get_string('checkforall', 'quizaccess_supervisedcheck'), 1);
        $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '', get_string('customcheck', 'quizaccess_supervisedcheck'), 2);
        $mform->addGroup($radioarray, 'radioar', get_string('allowcontrol', 'quizaccess_supervisedcheck'), '<br/>', false);

        $cbarray = array();
        $cbarray[] =& $mform->createElement('advcheckbox', 'supervisedlessontype_0', '', get_string('notspecified', 'block_supervised'));
        $lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$COURSE->id));
        foreach($lessontypes as $id=>$lessontype){
            $cbarray[] =& $mform->createElement('advcheckbox', 'supervisedlessontype_'.$id, '', $lessontype->name);
        }
        $mform->addGroup($cbarray, 'lessontypesgroup', '', '<br/>', false);


        $PAGE->requires->jquery();
        $PAGE->requires->js( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/lib.js') );
        $PAGE->requires->css( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/style.css') );
    }

    public static function save_settings($quiz) {
        global $DB, $COURSE;
        $oldrules = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $quiz->id));



        if($quiz->supervisedmode == 2){
            // Find checked lessontypes.
            $lessontypesincourse = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));
            $lessontypesinquiz = array();

            // Check for "Not specified" lessontype.
            if($quiz->supervisedlessontype_0){
                $lessontypesinquiz[] = 0;
            }
            // Checks for all other lessontypes.
            foreach($lessontypesincourse as $id=>$lessontype){
                if($quiz->{'supervisedlessontype_'.$id}){
                    $lessontypesinquiz[] = $id;
                }

            }

            // Update rules.
            if(empty($lessontypesinquiz)){
                // If user didn't check any lessontype - add special lessontype with id = -1
                $lessontypesinquiz[] = -1;
            }

            for ($i=0; $i<count($lessontypesinquiz); $i++) {
                // Update an existing rule if possible.
                $rule = array_shift($oldrules);
                if (!$rule) {
                    $rule                   = new stdClass();
                    $rule->quizid           = $quiz->id;
                    $rule->lessontypeid     = -1;
                    $rule->supervisedmode   = $quiz->supervisedmode; // must be 2
                    $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
                }
                $rule->lessontypeid         = $lessontypesinquiz[$i];
                $rule->supervisedmode       = $quiz->supervisedmode; // must be 2
                $DB->update_record('quizaccess_supervisedcheck', $rule);
            }
            // Delete any remaining old rules.
            foreach ($oldrules as $oldrule) {
                $DB->delete_records('quizaccess_supervisedcheck', array('id' => $oldrule->id));
            }
        }
        else{
            // Update an existing rule if possible.
            $rule = array_shift($oldrules);
            if (!$rule) {
                $rule                   = new stdClass();
                $rule->quizid           = $quiz->id;
                $rule->lessontypeid     = -1;
                $rule->supervisedmode   = $quiz->supervisedmode;   // 0 or 1
                $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
            }
            $rule->lessontypeid         = -1;
            $rule->supervisedmode       = $quiz->supervisedmode;   // 0 or 1
            $DB->update_record('quizaccess_supervisedcheck', $rule);
            // Delete any remaining old rules.
            foreach ($oldrules as $oldrule) {
                $DB->delete_records('quizaccess_supervisedcheck', array('id' => $oldrule->id));
            }
        }
    }


    public static function get_settings_sql($quizid) {
        return array(
            'supervisedmode',
            'LEFT JOIN {quizaccess_supervisedcheck} ON {quizaccess_supervisedcheck}.quizid = quiz.id',
            array());
    }


    public static function get_extra_settings($quizid) {
        global $DB;
        // Load lesson type fields.
        $res = array();
        $rules = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $quizid));
        foreach($rules as $rule){
            $res['supervisedlessontype_'.$rule->lessontypeid] = 1;
        }
        return $res;
    }

    public static function validate_settings_form_fields(array $errors,
                                                         array $data, $files, mod_quiz_mod_form $quizform) {
        // For custom lesson type selection mode user must check at least one lesson type.
        if($data['supervisedmode'] == 2){
            $isAllUnchecked = true;
            foreach ($data as $key => $value) {
                if (  (substr($key, 0, 21) == 'supervisedlessontype_') && ($value == 1)  ) {
                    $isAllUnchecked = false;
                }
            }

            if($isAllUnchecked){
                $errors["radioar"] = get_string("uncheckedlessontypes", "quizaccess_supervisedcheck");
            }
        }

        return $errors;
    }
}