<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class plannedsession_block_form
 *
 * The form for planned session (for supervise capability)
 *
 * @package block_supervised
 * @copyright
 * @licence
 */
class plannedsession_block_form extends moodleform {
 
    function definition() {
        global $DB, $COURSE;

        $mform =& $this->_form;

        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active'=>true))) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }

        // Gets array of all groups in current course.
        $groups[0] = get_string('allgroups', 'block_supervised');
        if ($cgroups = groups_get_all_groups($COURSE->id)) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }

        // Find lessontypes in current course.
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$COURSE->id))) {
            foreach ($clessontypes as $clessontype) {
                $lessontypes[$clessontype->id] = $clessontype->name;
            }
        }


        // add group
        $mform->addElement('header', 'general', get_string('sessioninfo', 'block_supervised'));
        // add classroom combobox
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        $mform->addRule('classroomid', null, 'required', null, 'client');
        // add group combobox
        $mform->addElement('select', 'groupid', get_string('group', 'block_supervised'), $groups);
        $mform->addRule('groupid', null, 'required', null, 'client');
        // add lessontype combobox
        if($clessontypes){
            $mform->addElement('select', 'lessontypeid', get_string('lessontype', 'block_supervised'), $lessontypes);
            $mform->addRule('lessontypeid', null, 'required', null, 'client');
        }
        else{
            $mform->addElement('hidden', 'lessontypeid');
            $mform->setType('lessontypeid', PARAM_INT);
        }
        // add time start
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        // add duration
        $mform->addElement('text', 'duration', get_string('duration', 'block_supervised'), 'size="4"');
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', null, 'numeric', null, 'client');
        // add timeend
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));
        // add comment
        if($this->_customdata['needcomment']){
            $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));
        }
        // hidden elements.
        $mform->addElement('hidden', 'id');     // course id
        $mform->setType('id', PARAM_INT);

        // add submit button
        $mform->addElement('submit', 'submitbutton', get_string('startsession', "block_supervised"));
    }


    // Form validation
    function validation($data, $files) {
        $errors = array();

        // Duration must be greater than zero.
        if($data["duration"] <= 0){
            $errors["duration"] = get_string("durationvalidationerror", "block_supervised");
        }

        return $errors;
    }
}