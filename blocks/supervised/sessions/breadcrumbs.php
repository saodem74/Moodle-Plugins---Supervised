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


$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_supervised'));
$url = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
$PAGE->navbar->add(get_string('sessionsbreadcrumb', 'block_supervised'), $url);