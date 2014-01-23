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


require_once('../../../config.php');

global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$id         = required_param('id', PARAM_INT);
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_supervised', $courseid);
}

require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);


if ($site->id == $course->id) {
    // Block can not work in the main course (frontpage).
    print_error("invalidcourseid");
}

if (! $classroom = $DB->get_record("block_supervised_classroom", array("id" => $id))) {
    print_error(get_string("invalidclassroomid", 'block_supervised'));
}

// Change active field.
$classroom->active = (int)!($classroom->active);
// Update DB.
if (!$DB->update_record('block_supervised_classroom', $classroom)) {
    print_error('insertclassroomerror', 'block_supervised');
}
// Redirect.
$url = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
redirect($url);