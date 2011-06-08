<?php
//global $CFG;
//require_once($CFG->dirroot.'/course/moodleform_mod.php');
define('POASASSIGNMENT_GRADER_NO_TEST', 0);
define('POASASSIGNMENT_GRADER_COMMON_TEST', 1);
define('POASASSIGNMENT_GRADER_INDIVIDUAL_TESTS', 2);

/* define('POASASSIGNMENT_GRADER_SHOW_TESTING_PROGRAM_FEEDBACK', 1);
define('POASASSIGNMENT_GRADER_SHOW_TEST_INPUT_DATA', 2);
define('POASASSIGNMENT_GRADER_SHOW_OUTPUT_STUDENT_DATA', 4);
define('POASASSIGNMENT_GRADER_SHOW_DIFF', 8);
define('POASASSIGNMENT_GRADER_SHOW_TESTS_NAMES', 16);
define('POASASSIGNMENT_GRADER_SHOW_NUMBER_OF_PASSED_TESTS', 32);
define('POASASSIGNMENT_GRADER_SHOW_RATING', 64); */
        
/**
 * Saves data generated by testing program 
 */
class simple_test_result {
    /**
     * Test name
     */
    public $testname;
    /** 
     * Shows, was the test completed successfully
     */
    public $issuccessful;
    /**
     * Input data for the test
     */
    public $testinputdata;
    /**
     * Result data of student's submission
     */
    public $studentoutputdata;
    /**
     * Testing program comments for the test
     */
    public $feedback;
    /**
     * Difference between test output data and student's submission
     */
    public $outputdiff;
}

/* 
 * Parent of all graders classes
 */
abstract class grader {
    
    /**
     * Returns test mode of this grader (NO_TEST, COMMON_TEST or INDIVIDUAL_TEST)
     * @return int test mode
     */
    public function get_test_mode() {
        return POASASSIGNMENT_GRADER_NO_TEST;
    }
    
    /**
     * Returns name of the grader
     * @return string name of the grader
     */
    public static function name() {
        return get_string('grader','poasassignment_grader');
    }
    
    public static function prefix() {
        return __CLASS__;
    }
    
    /**
     * Checks poasassignment options to be sure this grader can be 
     * used
     * @param array $data poasassignment options
     * @param array $errors denied options
     */
    public static function validation($data, &$errors) {
    }
    
    public function test_attempt($attemptid) {
    }
    
    /* private static $settings = array('feedback' => POASASSIGNMENT_GRADER_SHOW_TESTING_PROGRAM_FEEDBACK,
                                     'testinputdata' => POASASSIGNMENT_GRADER_SHOW_TEST_INPUT_DATA,
                                     'testoutputdata' => POASASSIGNMENT_GRADER_SHOW_OUTPUT_STUDENT_DATA,
                                     'diff' => POASASSIGNMENT_GRADER_SHOW_DIFF,
                                     'testsnames' => POASASSIGNMENT_GRADER_SHOW_TESTS_NAMES,
                                     'numberofpassedtest' => POASASSIGNMENT_GRADER_SHOW_NUMBER_OF_PASSED_TESTS,
                                     'rating' => POASASSIGNMENT_GRADER_SHOW_RATING); */
                                     
    public static function save_settings($data, $poasassignmentid) {
        /* global $DB;
        // Save settings of all used graders
        $usedgraderrecords = $DB->get_records('poasassignment_used_graders', 
                                              array('poasassignmentid' => $poasassignmentid));
                                              
        foreach($usedgraderrecords as $usedgraderrecord) {
            $graderrecord = $DB->get_record('poasassignment_graders', 
                                            array('id' => $usedgraderrecord->graderid));
                                            
            $gradername = $graderrecord->name;
                
            $studentrslt = 0;
            $teacherrslt = 0;
            foreach(self::$settings as $field => $flag) {
                $studentfield = $gradername::prefix().'studentshow'.$field;
                if(isset($data->$studentfield))
                    $studentrslt += $flag;
                    
                $teacherfield = $gradername::prefix().'teachershow'.$field;
                if(isset($data->$teacherfield))
                    $teacherrslt += $flag;
            }
            $usedgraderrecord->studentresultoptions = $studentrslt;
            $usedgraderrecord->teacherresultoptions = $teacherrslt;
            $DB->update_record('poasassignment_used_graders',$usedgraderrecord);
        } */
    }
    /**
     * Shows settings of the grader. 
     * @param $mform moodle form, where grader's options are placed
     * @param $usedgraderid id of grader in poasassignment_used_graders table
     * @param $poasassignmentid id 
     */
    public static function show_settings($mform, $usedgraderid, $poasassignmentid) {
    }
    
    public static function get_settings($poasassignmentid) {
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
            $html += "<br>Passed tests : " . $successfultestscount;
        
        foreach ($testresults as $testresult) {
            if($options & POASASSIGNMENT_GRADER_SHOW_TESTS_NAMES)
                $html += "<br>" . $testresult->testname;
            if($options & POASASSIGNMENT_GRADER_SHOW_TEST_INPUT_DATA)
                $html += "<br>" . $testresult->testinputdata;
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
    function evaluate($submission,$poasassignmentid,$taskid=-1) {
        return array();
    }
        
    // ���������� ������ ������
    function show_tests($poasassignmentid,$taskid=-1){
    
    }
}