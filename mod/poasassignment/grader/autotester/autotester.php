<?php
require_once(dirname(dirname(__FILE__)).'\grader\grader.php');

class autotester extends grader{
    
    public function get_test_mode() {
        return POASASSIGNMENT_GRADER_INDIVIDUAL_TESTS;
    }
    
    public static function name() {
        return get_string('autotester','poasassignment_autotester');
    }
    public static function prefix() {
        return __CLASS__;
    }
    public static function validation($data, &$errors) {
        if(isset($data[self::prefix()]) && !isset($data['answertext']))
            $errors['answertext'] = get_string('textanswermustbeenabled',
                                               'poasassignment_autotester');
        //TODO ��������� ����� ���. �������
    }
    
    public function test_attempt($attemptid) {
        global $DB;
        
        // step 1: compile student's program
        $textanswerrec = $DB->get_record('poasassignment_answers', array('name' => 'answer_text'));
        if($textanswerrec) {
            $submission = $DB->get_record('poasassignment_submissions', array('attemptid' => $attemptid, 'answerid' => $textanswerrec->id));
            //echo $submission->value;
            $f = fopen('grader\autotester\attempts\attempt' . $attemptid . '.cpp', 'w+');
            fwrite($f, $submission->value);
            fclose($f);
            
            $runf = fopen('grader\autotester\runattempt' . $attemptid . '.bat', 'w+');
            $text = 'cd grader\autotester';  
            $text .= "\n";
            $text .= 'call C\vcvarsall.bat';
            $text .= "\n";
            $text .= 'C\bin\cl.exe ';
            $text .= '/Feattempts\attempt' . $attemptid . '.exe ';
            $text .= '/Foattempts\attempt' . $attemptid . '.obj ';
            $text .= 'attempts\attempt' . $attemptid . '.cpp ';
            $text .= "\n";
            fwrite($runf, $text);
            fclose($runf);            
        }
        
        // step 2: create test files
        
        // step 2.1 get task id
        $attempt = $DB->get_record('poasassignment_attempts', array('id' => $attemptid));
        $assignee = $DB->get_record('poasassignment_assignee', array('id' => $attempt->assigneeid));
        
        // step 2.2 get grader tests
        
        $rec = $DB->get_record('poasassignment_gr_autotester', array('taskid' => $assignee->taskid));
        $gradertestrec = $DB->get_record('question_gradertest', array('questionid' => $rec->questionid));
        $gradertests = $DB->get_records('question_gradertest_tests', array('gradertestid' => $gradertestrec->id));
        
        $this->create_test_files($gradertests, 'grader\autotester\attempts\tests\\');
        
        // step 3: call each test and update testing result table
        return 50;
    }
    
    public function create_test_files($tests, $path) {
        foreach($tests as $test) {
            $f = fopen($path . $test->id . '.txt', 'w+');
            fwrite($f, $test->testin);
            fclose($f);
        }
    }
    public function clean_files($attemptid) {
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.cpp');
        unlink('grader\autotester\runattempt' . $attemptid . '.bat');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.exe');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.obj');
    }
    
    // ����������� ����� ���������� ���������� (������ simple_test_result'��)
    private $testresults;
    private $successfultestscount;
    
    public function show_result($options) {
        //TODO: �������� ����� ��� ����� ����� �������, ��������� ����� ����� ���������� ��� �����
        $html = "";
        if($options & POASASSIGNMENT_GRADER_SHOW_RATING) 
            $html += "<br>Rating : ".(100 * $successfultestscount / count($testresults));
        if($options & POASASSIGNMENT_GRADER_SHOW_NUMBER_OF_PASSED_TESTS)
            $html += "<br>Passed tests : ".$successfultestscount;
        
        foreach ($testresults as $testresult) {
            if($options & POASASSIGNMENT_GRADER_SHOW_TESTS_NAMES)
                $html += "<br>".$testresult->testname;
            if($options & POASASSIGNMENT_GRADER_SHOW_TEST_INPUT_DATA)
                $html += "<br>".$testresult->testinputdata;
        }
    }
    
    // TODO: ������ � ������� ����� ��� ������� �����
        
    // �������������� ������( �������� �� ���������� ����� ������ � �������������� ������������)
    function edit_tests($tests) {
        return null;
    }
    // ��������� ����
    function turn_off_test($testid) {
        return null;
    }
    // ��������� ������� ����
    function delete_test($testid) {
        return null;
    }
    
    // ������� ������
    function tests_export($exportParams) {
        return null;
    }
    
    // ������ ������
    function tests_import($importParams) {
        return null;
    }
        
    // ���������� ������ ������ $submission �� ������� $taskid ����������� poasassignment'a
    function evaluate($submission, $poasassignmentid, $taskid = -1) {
        return array();
    }
        
    // ���������� ������ ������
    function show_tests($poasassignmentid, $taskid=-1){
    
    }
    
    public static function show_settings($mform, $usedgraderid, $poasassignmentid) {
        global $DB;
        $tasksrecs = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $poasassignmentid));
        $testrecs = $DB->get_records('question_gradertest');
        $tests = array();
        foreach($testrecs as $testrec) {
            $question = $DB->get_record('question', array('id' => $testrec->questionid));
            $tests[$question->id] = $question->name;
        }
        foreach($tasksrecs as $taskrec) {
            $mform->addElement('select', 'autotester_task' . $taskrec->id, $taskrec->name, $tests);
        }
    }
    public static function save_settings($data, $poasassignmentid) {
        global $DB;
        $tasksrecs = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $poasassignmentid));
        foreach ($tasksrecs as $taskrec) {
            $DB->delete_records('poasassignment_gr_autotester', array('taskid' => $taskrec->id));
            $rec = new stdClass();
            $rec->taskid = $taskrec->id;
            $name = 'autotester_task' . $taskrec->id;
            $rec->questionid = $data->$name;
            $DB->insert_record('poasassignment_gr_autotester', $rec);
        }
    }
    public static function get_settings($poasassignmentid) {
        global $DB;
        $recs = $DB->get_records('poasassignment_gr_autotester', array());
        $data = array();
        foreach($recs as $rec) {
            $data['autotester_task' . $rec->taskid] = $rec->questionid;
        }
        return $data;
    }
}