<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('model.php');
require_once(dirname(__FILE__) . '/lib.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID

$cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
    
require_login($course, true, $cm);
require_capability('mod/poasassignment:submit', get_context_instance(CONTEXT_MODULE, $cm->id));
add_to_log($course->id, 'poasassignment', 'submission', 
            "submission.php?id=$cm->id", $poasassignment->name, $cm->id);
            
global $OUTPUT,$DB,$PAGE,$USER;
$PAGE->set_url('/mod/poasassignment/view.php?id=' . $cm->id);
$PAGE->set_title(get_string('modulename', 'poasassignment') . ':' . $poasassignment->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));

global $DB,$USER;
$answer_form = new answer_form(null, array('poasassignmentid' => $poasassignment->id, 
                                           'userid' => $USER->id,
                                           'id' => $cm->id));
//$plugins=$DB->get_records('poasassignment_answers');

//$poasmodel = poasassignment_model::get_instance($poasassignment);
$poasmodel = poasassignment_model::get_instance();
$poasmodel->cash_instance($poasassignment->id);
$poasmodel->cash_assignee_by_user_id($USER->id);
$plugins = $poasmodel->get_plugins();
foreach($plugins as $plugin) {
    if (poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignment->id)) {
        require_once($plugin->path);
        $poasassignmentplugin = new $plugin->name();
        $preloadeddata = $poasassignmentplugin->get_answer_values($poasassignment->id);
        $answer_form->set_data($preloadeddata);
    }
}
if ($answer_form->is_cancelled()) {
    redirect(new moodle_url('view.php', array('id' => $cm->id,'page' => 'view')), null, 0);
}
else {
    if ($answer_form->get_data()) {
        $data=$answer_form->get_data();
        //save data
        $assignee = $poasmodel->get_assignee($USER->id);
        $poasmodel->cash_assignee_by_user_id($USER->id);
        $attemptid = $poasmodel->save_attempt($data);
        foreach($plugins as $plugin) {
            if(poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignment->id)) {
                require_once($plugin->path);
                $answerplugin = new $plugin->name();
                $answerplugin->save_submission($attemptid, $data);
            }
        }
        // save attempt as last attempt of this assignee
        $poasmodel->assignee->lastattemptid = $attemptid;
        //echo '...lastattemptid='.$attemptid;
        $DB->update_record('poasassignment_assignee', $poasmodel->assignee);
        
        // trigger poasassignmentevent 
        $poasmodel->trigger_poasassignment_event(ATTEMPT_DONE, $poasmodel->assignee->id);
        
        //noitify teacher if needed
        $poasmodel->email_teachers($poasmodel->assignee);
        
        $poasmodel->test_attempt($attemptid);
        
        redirect(new moodle_url('view.php', 
                                array('id'=>$cm->id, 'page'=>'view')), 
                                null, 
                                0);
    }
}
echo $OUTPUT->header();
echo $OUTPUT->heading($poasassignment->name);
echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
$answer_form->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
