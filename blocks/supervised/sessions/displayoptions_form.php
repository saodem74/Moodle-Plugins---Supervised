<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class displayoptions_sessions_form
 *
 * Session dasplay oprions form (filtering, number of sessions per page, ...)
 */
class displayoptions_sessions_form extends moodleform {
    function definition() {
        global $DB, $USER;
        $mform =& $this->_form;

        $selectedcourse = $this->_customdata['course'];
        if($selectedcourse != 0){
            $coursecontext = context_course::instance($selectedcourse);
        }

        // Teachers. Fill only if user can view other teachers' sessions.
        $teachers[0] = get_string('allteachers', '');
        if($selectedcourse == 0){
            // Find teachers from all courses.
            if ($courses = get_courses()) {
                foreach ($courses as $course) {
                    $teachers += $this->teachers_from_course($course->id);
                }
            }
        }
        else if (has_capability('block/supervised:viewallsessions', $coursecontext)){
            $teachers += $this->teachers_from_course($selectedcourse);
        }
        else{
            // User can see only his own sessions.
        }
        // Classrooms.
        $classrooms[0] = get_string('allclassrooms', 'block_supervised');
        if ($cclassrooms = $DB->get_records('block_supervised_classroom', array('active'=>true))) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }
        // Lesson types.
        $lessontypes[-1] = get_string('alllessontypes', 'block_supervised');
        $lessontypes[0] = get_string('notspecified', 'block_supervised');
        if($selectedcourse != 0){
            // Lessontypes in current courses.
            if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$selectedcourse))) {
                foreach ($clessontypes as $clessontype) {
                    $lessontypes[$clessontype->id] = $clessontype->name;
                }
            }
        }
        // States.
        $states[0] = get_string('allstates', 'block_supervised');
        $states[StateSession::Planned] = StateSession::getStateName(StateSession::Planned);
        $states[StateSession::Active] = StateSession::getStateName(StateSession::Active);
        $states[StateSession::Finished] = StateSession::getStateName(StateSession::Finished);


        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);
        if($selectedcourse == 0 || has_capability('block/supervised:viewallsessions', $coursecontext)){
            $mform->addElement('select', 'teacher', get_string('teacher', 'block_supervised'), $teachers);
        }
        else{
            $mform->addElement('hidden', 'teacher');
            $mform->setType('teacher', PARAM_INT);
        }
        $mform->addElement('date_time_selector', 'from', get_string('sessionstartsafter', 'block_supervised'));
        $mform->addElement('date_time_selector', 'to', get_string('sessionendsbefore', 'block_supervised'));
        $mform->addElement('select', 'classroom', get_string('classroom', 'block_supervised'), $classrooms);
        if($selectedcourse != 0 && $clessontypes){
            $mform->addElement('select', 'lessontype', get_string('lessontype', 'block_supervised'), $lessontypes);
        }
        else{
            $mform->addElement('hidden', 'lessontype');
            $mform->setType('lessontype', PARAM_INT);
        }
        $mform->addElement('select', 'state', get_string('state', 'block_supervised'), $states);


        $mform->addElement('submit', 'submitbutton', get_string('showsessions', 'block_supervised'));

        // hidden elements
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);
    }

    // Form validation
    function validation($data, $files) {
        $errors = array();

        // Page size must be greater than zero.
        if($data["pagesize"] <= 0){
            $errors["pagesize"] = get_string("pagesizevalidationerror", "block_supervised");
        }

        // Time from must be <= than time to .
        if($data["from"] > $data["to"]){
            $errors["to"] = get_string("timetovalidationerror", "block_supervised");
        }

        return $errors;
    }

    /**
     * Returns teachers from specified course
     *
     * @param $courseid
     * @return array array of teachers (id=>fullname)
     */
    function teachers_from_course($courseid){
        // Find teachers from selected course.
        $teachers = array();
        $coursecontext = context_course::instance($courseid);
        if ($cteachers = get_users_by_capability($coursecontext, array('block/supervised:supervise'))) {
            foreach ($cteachers as $cteacher) {
                $teachers[$cteacher->id] = fullname($cteacher);
            }
        }
        return $teachers;
    }
}
