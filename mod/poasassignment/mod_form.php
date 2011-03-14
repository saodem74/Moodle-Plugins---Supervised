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
 * The main poasassignment configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_poasassignment
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
class mod_poasassignment_mod_form extends moodleform_mod {

    var $plugins=array();
    /** Displays main options of poasassignment
     */
    function definition() {

        global $COURSE, $CFG;
        $mform =& $this->_form;
        
        // Adding the "general" fieldset
        //----------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('poasassignmentname', 'poasassignment'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor(true, get_string('poasassignmentintro', 'poasassignment'));

        // Adding filemanager field where teracher can attach file to the assignment
        $mform->addElement('filemanager', 'poasassignmentfiles', get_string('poasassignmentfiles', 'poasassignment'));


        $mform->addElement('date_time_selector', 'availabledate', get_string('availabledate', 'poasassignment'), array('optional'=>true));
        $mform->setDefault('availabledate', time());

        $mform->addElement('date_time_selector', 'choicedate', get_string('choicedate', 'poasassignment'), array('optional'=>true));
        $mform->setDefault('choicedate', time()+2*24*3600); // By default student have 2 days to choose task
        $mform->disabledIf('choicedate', 'activateindividualtasks');

        $ynoptions = array(
            0 => get_string('no'),
            1 => get_string('yes'));

        $mform->addElement('checkbox', 'preventlatechoice', get_string('preventlatechoice', 'poasassignment'));

        $mform->addElement('checkbox', 'randomtasksafterchoicedate', get_string('randomtasksafterchoicedate', 'poasassignment'));

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'poasassignment'), array('optional'=>true));
        $mform->setDefault('deadline', time()+7*24*3600); // By default student have 7 days to complete task

        $mform->addElement('checkbox', 'preventlate', get_string('preventlate', 'poasassignment'));
        
        // Adding answers fieldset
        //----------------------------------------------------------------------
        global $COURSE, $CFG,$DB;
        $mform->addElement('header', 'answers', get_string('answers', 'poasassignment'));
        
        $mform->addElement('checkbox', 'severalattempts', get_string('severalattempts', 'poasassignment'));
        $mform->addHelpButton('severalattempts', 'severalattempts', 'poasassignment');
        
        $mform->addElement('checkbox', 'newattemptbeforegrade', get_string('newattemptbeforegrade', 'poasassignment'));
        $mform->addHelpButton('newattemptbeforegrade', 'newattemptbeforegrade', 'poasassignment');
        
        $mform->addElement('text', 'penalty', get_string('penalty', 'poasassignment'));
        $mform->setDefault('penalty', 0);
        $mform->disabledIf('penalty', 'severalattempts', 'notchecked');
        $mform->addHelpButton('penalty', 'penalty', 'poasassignment');
        
        $mform->addElement('checkbox','finalattempts',get_string('finalattempts','poasassignment'));
        $mform->addHelpButton('finalattempts','finalattempts','poasassignment');
        $mform->disabledIf('finalattempts', 'severalattempts', 'notchecked');
        
        $mform->addElement('checkbox', 'notifyteachers', get_string('notifyteachers', 'poasassignment'));
        $mform->addHelpButton('notifyteachers', 'notifyteachers', 'poasassignment');
        
        $mform->addElement('checkbox', 'notifystudents', get_string('notifystudents', 'poasassignment'));
        $mform->addHelpButton('notifystudents', 'notifystudents', 'poasassignment');
        
        // Adding plugins fieldsets
        //----------------------------------------------------------------------
        $this->plugins=$DB->get_records('poasassignment_plugins');
        foreach ($this->plugins as $plugin) { 
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->show_settings($mform, $this->_instance);
        }

        // Adding individual tasks fieldset
        //----------------------------------------------------------------------
        $mform->addElement('header', 'poasassignmentfieldset', get_string('poasassignmentfieldset', 'poasassignment'));

        $mform->addElement('checkbox', 'activateindividualtasks', get_string('activateindividualtasks', 'poasassignment'));
        $mform->addHelpButton('activateindividualtasks', 'activateindividualtasks', 'poasassignment');

        // Adding taskgivers selectbox

        $taskgivers=$DB->get_records('poasassignment_taskgivers');
        $names = array();
        foreach ($taskgivers as $taskgiver) {
            array_push($names, get_string($taskgiver->name,'poasassignment'));
        }
        foreach($names as $name)
            echo $name;

        $mform->addElement('select', 'howtochoosetask', get_string('howtochoosetask', 'poasassignment'),
                            array(
                                get_string('randomtask', 'poasassignment'),
                                get_string('parameterchoice', 'poasassignment'),
                                get_string('studentchoice', 'poasassignment')));
        $mform->disabledIf('howtochoosetask', 'activateindividualtasks');
        $mform->addHelpButton('howtochoosetask', 'howtochoosetask', 'poasassignment');

        $mform->addElement('checkbox', 'secondchoice', get_string('secondchoice', 'poasassignment'));
        $mform->disabledIf('secondchoice', 'activateindividualtasks');
        $mform->addHelpButton('secondchoice', 'secondchoice', 'poasassignment');

        $mform->addElement('select', 'uniqueness', get_string('uniqueness', 'poasassignment'),
                            array(
                                get_string('nouniqueness', 'poasassignment'),
                                get_string('uniquewithingroup', 'poasassignment'),
                                get_string('uniquewithincourse', 'poasassignment')));
        $mform->disabledIf('uniqueness', 'activateindividualtasks');
        $mform->addHelpButton('uniqueness', 'uniqueness', 'poasassignment');
        
        $mform->addElement('checkbox', 'teacherapproval', get_string('teacherapproval', 'poasassignment'));
        $mform->disabledIf('teacherapproval', 'activateindividualtasks');
        $mform->addHelpButton('teacherapproval', 'teacherapproval', 'poasassignment');

        // Adding graders list
        //----------------------------------------------------------------------
        
        $mform->addElement('header', 'poasassignmentgraderslist', get_string('poasassignmentgraderslist', 'poasassignment'));
        // TODO show graders list with checkboxes

        // add standard elements, common to all modules
        //----------------------------------------------------------------------
        $this->standard_coursemodule_elements();

        // add standard buttons, common to all modules
        //----------------------------------------------------------------------
        $this->add_action_buttons();
    }

    /** Load files and flags from existing module
     */
    function data_preprocessing(&$default_values){
        if (isset($default_values['flags'])) {
            $flags = (int)$default_values['flags'];
            unset($default_values['flags']);
            $default_values['preventlatechoice']=$flags & PREVENT_LATE_CHOICE;
            $default_values['randomtasksafterchoicedate']=$flags & RANDOM_TASKS_AFTER_CHOICEDATE;
            $default_values['preventlate']=$flags & PREVENT_LATE;
            $default_values['severalattempts']=$flags & SEVERAL_ATTEMPTS;
            $default_values['notifyteachers']=$flags & NOTIFY_TEACHERS;
            $default_values['notifystudents']=$flags & NOTIFY_STUDENTS;
            $default_values['activateindividualtasks']=$flags & ACTIVATE_INDIVIDUAL_TASKS;
            $default_values['secondchoice']=$flags & SECOND_CHOICE;
            $default_values['teacherapproval']=$flags & TEACHER_APPROVAL;
            $default_values['newattemptbeforegrade']=$flags & ALL_ATTEMPTS_AS_ONE;
            $default_values['finalattempts']=$flags & MATCH_ATTEMPT_AS_FINAL;
        }
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('poasassignmentfiles');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_poasassignment', 'poasassignmentfiles', 0, array('subdirs'=>true));
            $default_values['poasassignmentfiles'] = $draftitemid;
        }
    }

    /** Check dates
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Check open and close times are consistent.
        if ($data['availabledate'] != 0 && $data['choicedate'] != 0 && $data['choicedate'] < $data['availabledate']) {
            $errors['choicedate'] = get_string('choicebeforeopen', 'poasassignment');
        }
        if ($data['availabledate'] != 0 && $data['deadline'] != 0 && $data['deadline'] < $data['availabledate']) {
            $errors['deadline'] = get_string('deadlinebeforeopen', 'poasassignment');
        }
        if ($data['choicedate'] != 0 && $data['deadline'] != 0 && $data['deadline'] < $data['choicedate']) {
            $errors['deadline'] = get_string('deadlinebeforechoice', 'poasassignment');
        }
        
        foreach ($this->plugins as $plugin) { 
            $pluginname=$plugin->name;
            $pluginname::validation($data,$errors);
        }
        
        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }        
        
        // TODO validate graders
    }
}