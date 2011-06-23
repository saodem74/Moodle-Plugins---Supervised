<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');

class submission_page extends abstract_page {
    
    function __construct() {
    }
    function get_cap() {
        return 'mod/poasassignment:submit';
    }
    function has_satisfying_parameters() {
        // TODO
        return true;
    }
    function view() {
        global $DB, $OUTPUT, $USER;
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $answer_form = new answer_form(null, array('poasassignmentid' => $poasassignmentid, 
                                           'userid' => $USER->id,
                                           'id' => $model->get_cm()->id));
        $plugins = $model->get_plugins();
        foreach($plugins as $plugin) {
            if (poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignmentid)) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $preloadeddata = $poasassignmentplugin->get_answer_values($poasassignmentid);
            $answer_form->set_data($preloadeddata);
            }
        }
        if ($answer_form->is_cancelled()) {
            redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id,'page' => 'view')), null, 0);
        }
        else {
            if ($answer_form->get_data()) {
                $data = $answer_form->get_data();
                //save data
                $assignee = $model->get_assignee($USER->id);
                $model->cash_assignee_by_user_id($USER->id);
                $attemptid = $model->save_attempt($data);
                foreach($plugins as $plugin) {
                    if(poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignmentid)) {
                        require_once($plugin->path);
                        $answerplugin = new $plugin->name();
                        $answerplugin->save_submission($attemptid, $data);
                    }
                }
                // save attempt as last attempt of this assignee
                $model->assignee->lastattemptid = $attemptid;
                //echo '...lastattemptid='.$attemptid;
                $DB->update_record('poasassignment_assignee', $model->assignee);
                
                // trigger poasassignmentevent 
                $model->trigger_poasassignment_event(ATTEMPT_DONE, $model->assignee->id);
                
                //noitify teacher if needed
                $model->email_teachers($model->assignee);
                
                $model->test_attempt($attemptid);
                
                redirect(new moodle_url('view.php', 
                                        array('id'=>$model->get_cm()->id, 'page'=>'view')), 
                                        null, 
                                        0);
            }
        }
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        $answer_form->display();
        echo $OUTPUT->box_end();
    }
    public static function display_in_navbar() {
        return false;
    }
}