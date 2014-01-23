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


global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class delete_session_form
 *
 * Delete session form
 */
class delete_session_form extends moodleform {

    protected function definition() {
        $mform =& $this->_form;

        $mform->addElement('static', 'coursename', get_string('course', 'block_supervised'));
        $mform->addElement('static', 'classroomname', get_string('classroom', 'block_supervised'));
        $mform->addElement('static', 'groupname', get_string('group', 'block_supervised'));
        $mform->addElement('static', 'teachername', get_string('teacher', 'block_supervised'));
        $mform->addElement('static', 'lessontypename', get_string('lessontype', 'block_supervised'));
        $mform->addElement('static', 'timestart', get_string('timestart', 'block_supervised'));
        $mform->addElement('static', 'duration', get_string('duration', 'block_supervised'));
        $mform->addElement('static', 'timeend', get_string('timeend', 'block_supervised'));
        $mform->addElement('static', 'sessioncomment', get_string('sessioncomment', 'block_supervised'));

        // add notify teacher by e-mail checkbox
        $mform->addElement('advcheckbox', 'notifyteacher', get_string("notifyteacher", 'block_supervised'));
        $mform->addHelpButton('notifyteacher', 'notifyteacher', 'block_supervised');
        // add comment
        $mform->addElement('textarea', 'messageforteacher', get_string("messageforteacher", "block_supervised"), 'rows="4" cols="30"');

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, get_string('delete'));
    }
}