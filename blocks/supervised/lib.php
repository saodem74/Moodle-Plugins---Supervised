<?php
function can_delete_lessontype($lessontypeid) {
    global $DB;

    // Can not remove lessontype used in session(s)
    if($DB->record_exists('block_supervised_session', array('lessontypeid'=>$lessontypeid))){
        return false;
    }

    // Can not remove lessontype used in quiz_access_rules table
    if($DB->record_exists('quizaccess_supervisedcheck', array('lessontypeid'=>$lessontypeid))){
        return false;
    }

    return true;
}

function can_delete_classroom($classroomid) {
    global $DB;
    // Can not remove classroom used in session(s)
    return ! $DB->record_exists('block_supervised_session', array('classroomid'=>$classroomid));
}

function can_showhide_classroom($classroomid) {
    require_once('sessions/sessionstate.php');
    global $DB;
    // Can not showhide classroom used in active session(s)
    return ! $DB->record_exists('block_supervised_session', array('classroomid'=>$classroomid, 'state'=>StateSession::Active));
}