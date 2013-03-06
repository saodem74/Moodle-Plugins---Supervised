<?php

/**
 * Data-driven cross-tester of matchers. Interit this class and
 * implement the engine_name() function for testing a concrete matcher.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*******************************************************************************************************************************************
*
*  The cross-tester searches for files named "cross_tests_<suffix>.php". The search isn't recursive, so put your tests
*  in the "tests" folder. A class with test data should be named the same as the corresponding file is named.
*  For example, a file named "cross_tests_example.php" should contain a class named "cross_tests_example".
*
*  Those classes represent test data as a set of test functions. A test function should:
*    -be named "data_for_test_..."
*    -return an array of input and output data as in the following example:
*
*    array(
*          'regex'=>'.*(.*)',                                     // The regular expression to test the matcher on.
*          'tests'=>array($test1, ... , $testn),                  // Array containing tests in the format described below.
*          'modifiers'=>'i',                                      // (Optional) modifiers, default value is null.
*          'tags'=>array($tag1, ..., $tagn),                      // (Optional) tags for the regex, default value is array().
*          'notation'=>qtype_preg_cross_tester::NOTATION_NATIVE)  // (Optional) regex notation, default value is 'native'.
*          );
*
*  An array of expected results ($testi) should look like:
*
*    array(
*          'str'=>'aaa',                    // A string to match.
*          'is_match'=>true,                // Is there a match?
*          'full'=>true,                    // Is the match full?
*          'index_first'=>array(0=>0,1=>3), // Start indexes of subexpressions; not necessary to define unmatched subexpressions.
*          'length'=>array(0=>3,1=>0),      // Lengths of subexpressions; not necessary to define unmatched subexpressions.
*          'left'=>0,                       // (Defined for partial matches) number of characters left to complete the partial match.
*          'next'=>'',                      // (Defined for partial matches) a regex matching possible next character.
*          'tags'=>array());                // (Optional) tags for the string, default value is array().
*
*  Here's an example test function:
*
*  function data_for_test_att_nullsubexpr_2() {
*          $test1 = array('str'=>'aaaaaa',
*                         'is_match'=>true,
*                         'full'=>true,
*                         'index_first'=>array(0=>0,1=>0),
*                         'length'=>array(0=>6,1=>6));
*
*          return array('regex'=>'(a*)*',
*                       'tests'=>array($test1),
*                       'tags'=>array(qtype_preg_cross_tester::TAG_FROM_ATT));
*  }
*
*******************************************************************************************************************************************/

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

/**
 * Represents auxiliary class for extra checks. The extra checks are performed by cross-tester
 * on tests with partial matching: cross-tester concatenates correct heading and returned ending, then
 * checks this string for full match and some other equalities. The purpose of this class is only to
 * return the name of the matcher to do this check.
 */
abstract class qtype_preg_cross_tests_extra_checker {

    /**
     * Returns name of the engine, implement it in child classes for each engine.
     */
    abstract public function engine_name();

}

abstract class qtype_preg_cross_tester extends PHPUnit_Framework_TestCase {

    // Different sources of test data.
    const TAG_FROM_NFA           = 1;
    const TAG_FROM_DFA           = 2;
    const TAG_FROM_BACKTRACKING  = 4;
    const TAG_FROM_PCRE          = 8;
    const TAG_FROM_ATT           = 16;

    const TAG_CATEGORIZE         = 32;   // The test determines the matcher's associativity.
    const TAG_ASSOC_LEFT         = 64;   // The test should be used for left-associative matchers.
    const TAG_ASSOC_RIGHT        = 128;  // The test should be used for right-associative matchers.

    const TAG_DONT_CHECK_PARTIAL = 256;  // Indicates that if there's no full match, the cross-tester skips partial match checking.
    const TAG_DEBUG_MODE         = 512;  // Informs matchers that it's debug mode.

    // Different notations.
    const NOTATION_NATIVE             = 'native';
    const NOTATION_MDLSHORTANSWER     = 'mdlshortanswer';
    const NOTATION_PCRESTRICT         = 'pcrestrict';

    // TODO: tags for different capabilities for matchers.

    protected $passcount;              // Number of passes.
    protected $failcount;              // Number of fails.
    protected $skipcount;              // Number of skipped tests.
    protected $exceptionscount;        // Number of exceptions during testing.
    protected $testdataobjects;        // Objects with test data.
    protected $extracheckobjects;      // Objects for extra checks.
    protected $doextrachecks;          // Is it needed to do extra checks.
    protected $question;               // Question object for getting matchers.

    protected $blacklist;              // Blacklist of tags in different modes.

    /**
     * Returns name of the engine to be tested (without qtype_preg_ prefix!). Should be implemented in child classes.
     */
    abstract protected function engine_name();

    /**
     * Returns engine-specific tags, tests with wich will be skipped.
     */
    protected function blacklist_tags() {
        return array();
    }

    function categorize_assoc($enginename) {
        $test1 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,3=>1),
                       'length'=>array(0=>2,1=>1,3=>1),
                       'tags'=>array(qtype_preg_cross_tester::TAG_ASSOC_RIGHT));
        $test2 = array('str'=>'abc',
                       'is_match'=>true,
                       'full'=>true,
                       'index_first'=>array(0=>0,1=>0,2=>0,3=>2),
                       'length'=>array(0=>2,1=>0,2=>2,3=>0),
                       'tags'=>array(qtype_preg_cross_tester::TAG_ASSOC_LEFT));

        $regex = '(a*)(ab)*(b*)';

        $matchoptions = new qtype_preg_matching_options();
        $matcher = $this->question->get_matcher($enginename, $regex, false, false, null, self::NOTATION_NATIVE);
        $matcher->set_options($matchoptions);

        $matcher->match($test1['str']);
        $obtained1 = $matcher->get_match_results();
        $right = $this->compare_results($regex, self::NOTATION_NATIVE, $test1['str'], null, $matcher, $test1, $obtained1, 'categorize', 'associativity', false, false);

        $matcher->match($test2['str']);
        $obtained2 = $matcher->get_match_results();
        $left = $this->compare_results($regex, self::NOTATION_NATIVE, $test2['str'], null, $matcher, $test2, $obtained2, 'categorize', 'associativity', false, false);

        if ($left && !$right) {
            return self::TAG_ASSOC_LEFT;
        } else if (!$left && $right) {
            return self::TAG_ASSOC_RIGHT;
        }
        return false;
    }

    public function __construct() {
        $this->passcount = 0;
        $this->failcount = 0;
        $this->skipcount = 0;
        $this->exceptionscount = 0;
        $this->testdataobjects = array();
        $this->extracheckobjects = array();
        $this->doextrachecks = false;       // TODO: control this field from outside.
        $this->question = new qtype_preg_question();

        $testdir = dirname(__FILE__) . '/';
        $pregdir = dirname($testdir) . '/';

        // Find all available test files.
        $dh = opendir($testdir);
        if (!$dh) {
            return;
        }

        $enginename = $this->engine_name();

        // Include file with matcher to test.
        require_once($pregdir . $enginename . '/' . $enginename . '.php');

        // Include files with test data.
        while ($file = readdir($dh)) {
            if (strpos($file, 'cross_tests_') !== 0 || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }
            require_once($testdir . $file);
            $classname = 'qtype_preg_' . pathinfo($file, PATHINFO_FILENAME);
            if (strpos($file, 'cross_tests_extra_checker') === 0) {
                // Extra checker found.
                if ($this->doextrachecks) {
                    $obj = new $classname;
                    $enginename = $obj->engine_name();
                    require_once($pregdir . $enginename . '/' . $enginename . '.php');
                    $this->extracheckobjects[] = new $obj;
                }
            } else {
                // Test data object found.
                $this->testdataobjects[] = new $classname;
            }
        }
        closedir($dh);


        $assoc = $this->categorize_assoc($enginename);
        if ($assoc === self::TAG_ASSOC_LEFT) {
            echo "\n$enginename has LEFT ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_RIGHT);
        } else if ($assoc === self::TAG_ASSOC_RIGHT) {
            echo "\n$enginename has RIGHT ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_LEFT);
        } else {
            echo "\n$enginename has UNDEFINED ASSOCIATIVITY\n\n";
            $this->blacklist = array(self::TAG_ASSOC_LEFT, self::TAG_ASSOC_RIGHT);
        }
    }

    /**
     * Checks matcher for parsing and accepting errors.
     * @param $matcher - a matcher to be checked.
     * @return true if there are errors, false otherwise.
     */
    function check_for_errors($matcher) {
        if ($matcher->errors_exist()) {
            $errors = $matcher->get_error_objects();
            foreach ($errors as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {    // Error messages are displayed for parsing errors only.
                    echo 'Regex incorrect: ' . $error->errormsg . "\n";
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Prints given matchresults.
     * @param results array with keys 'is_match', 'full', 'index_first', 'length', 'next' and 'left'.
     * @param label array of additional lines to be printed before the results.
     */
    function dump_results($results, $label = array()) {
        $boolstr = array(false => 'FALSE', true => 'TRUE');
        foreach ($label as $line) {
            echo $line . "\n";
        }
        if (array_key_exists('is_match', $results)) {
            echo 'IS_MATCH:    ' . $boolstr[$results['is_match']] . "\n";
        }
        if (array_key_exists('full', $results)) {
            echo 'FULL:        ' . $boolstr[$results['full']] . "\n";
        }
        if (array_key_exists('index_first', $results)) {
            echo 'INDEX_FIRST: ';
            foreach ($results['index_first'] as $key => $value) {
                echo $key . '=>' . $value . ', ';
            }
            echo "\n";
        }
        if (array_key_exists('length', $results)) {
            echo 'LENGTH:      ';
            foreach ($results['length'] as $key => $value) {
                echo $key . '=>' . $value . ', ';
            }
            echo "\n";
        }
        if (array_key_exists('next', $results)) {
            echo 'NEXT:        ' . $results['next'] . "\n";
        }
        if (array_key_exists('left', $results)) {
            echo 'LEFT:        ' . $results['left'] . "\n";
        }
    }

    function check_next_character($regex, $char) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $leaf = $lexer->nextToken()->value;
        $res = $leaf->match(new qtype_poasquestion_string($char), 0, $length, false);
        fclose($pseudofile);
        return $res;
    }

    /**
     * Performs some extra checks on results which contain generated ending of a partial match.
     * @param $regex - regular expression.
     * @param $modifiers - modifiers.
     * @param $obtained - a result to check.
     * @return true if everything is correct, false otherwise.
     */
    function do_extra_check($regex, $notation, $modifiers, $obtained) {
        $str = $obtained->matched_part() . $obtained->string_extension();
        $thisenginename = $this->engine_name();
        $boolstr = array(false => 'FALSE', true => 'TRUE');
        $result = true;
        foreach ($this->extracheckobjects as $obj) {
            $enginename = 'qtype_preg_' . $obj->engine_name();
            $matcher = $this->question->get_matcher($enginename, $regex, false, strpos($modifiers, 'i') === false, null, $notation);
            if ($obtained->extendedmatch->full || $matcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
                $matcher->match($str);
                $newresults = $matcher->get_match_results();

                // Length + left should remain the same.
                $sum1 = $obtained->length() + $obtained->left;
                $sum2 = $obtained->extendedmatch->length() + $obtained->extendedmatch->left;
                if ($obtained->length() === qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $sum1++;
                }

                $full = $newresults->full === $obtained->extendedmatch->full;
                $sum = $sum1 === $sum2;
                if (!$full) {
                    $result = false;
                    echo "extended match field 'full' has the value of " . $boolstr[$obtained->extendedmatch->full] . " which is incorrect (extra-tested by $enginename)\n";
                }
                if (!$sum) {
                    $result = false;
                    echo "extended match fields 'length' and 'left' didn't pass: the old values are " . $obtained->length() . ' and ' . $obtained->left . ', the new values are ' . $obtained->extendedmatch->length() . ' and ' . $obtained->extendedmatch->left . " (extra-tested by $enginename)\n";
                }
            }
        }
        return $result;
    }

    /**
     * Compares obtained results with expected and writes all flags.
     */
    function compare_results($regex, $notation, $str, $modifiers, $matcher, $expected, $obtained, $classname, $methodname, $skippartialcheck, $dumpfails) {
        // Do some initialization.
        $fullpassed = ($expected['full'] === $obtained->full);
        $ismatchpassed = true;
        $indexfirstpassed = true;
        $lengthpassed = true;
        $nextpassed = true;
        $leftpassed = true;

        // Check match existance.
        if (!$skippartialcheck && $matcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) {
            $ismatchpassed = ($expected['is_match'] === $obtained->is_match());
        } else if (!$skippartialcheck) {
            $ismatchpassed = $fullpassed;
        }

        // Check indexes and lengths.
        if (!$skippartialcheck && $matcher->is_supporting(qtype_preg_matcher::SUBPATTERN_CAPTURING)) {
            foreach ($obtained->index_first as $key => $index) {
                $indexfirstpassed = $indexfirstpassed && ((!array_key_exists($key, $expected['index_first']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                          (array_key_exists($key, $expected['index_first']) && $expected['index_first'][$key] === $obtained->index_first[$key]));
                if (!$indexfirstpassed) {
                    break;
                }
            }
            foreach ($obtained->length as $key => $index) {
                $lengthpassed = $lengthpassed && ((!array_key_exists($key, $expected['length']) && $index === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                                  (array_key_exists($key, $expected['length']) && $expected['length'][$key] === $obtained->length[$key]));
                if (!$lengthpassed) {
                    break;
                }
            }
        } else if (!$skippartialcheck) {
            $indexfirstpassed = (!array_key_exists(0, $expected['index_first']) && $obtained->index_first[0] === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                                (array_key_exists(0, $expected['index_first']) && $expected['index_first'][0] === $obtained->index_first[0]);
            $lengthpassed = (!array_key_exists(0, $expected['length']) && $obtained->length[0] === qtype_preg_matching_results::NO_MATCH_FOUND) ||
                            (array_key_exists(0, $expected['length']) && $expected['length'][0] === $obtained->length[0]);
        }

        // Check the next possible character.
        $obtainednext = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER;
        if (!$skippartialcheck && !$expected['full'] && $matcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            if ($obtained->extendedmatch !== null) {
                $obtainednext = $obtained->string_extension();
            }
            $pattern = $expected['next'];
            $char = qtype_poasquestion_string::substr($obtainednext, 0, 1);
            $nextpassed = (($expected['next'] === $obtainednext && $obtainednext === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) ||
                           ($expected['next'] !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->check_next_character($pattern, $char)));
        }

        // Check the number of characters left.
        if (!$skippartialcheck && !$expected['full'] && $matcher->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
            $leftpassed = in_array($obtained->left, $expected['left']);
        }
        if (!$skippartialcheck && $this->doextrachecks && $obtained->extendedmatch !== null) {
            $this->do_extra_check($regex, $notation, $modifiers, $obtained);
        }

        $enginename = $matcher->name();

        // Dump fails.
        if ($dumpfails) {
            // is_match
            if (!$ismatchpassed) {
                $this->dump_results(array('is_match' => $obtained->is_match()),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('is_match' => $expected['is_match']),
                                    array("expected:"));
            }

            // full
            if (!$fullpassed) {
                $this->dump_results(array('full' => $obtained->full),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('full' => $expected['full']),
                                    array("expected:"));
            }

            // index_first
            if (!$indexfirstpassed) {
                $this->dump_results(array('index_first' => $obtained->index_first),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('index_first' => $expected['index_first']),
                                    array("expected:"));
            }

            // length
            if (!$lengthpassed) {
                $this->dump_results(array('length' => $obtained->length),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('length' => $expected['length']),
                                    array("expected:"));
            }

            // next
            if (!$nextpassed) {
                $this->dump_results(array('next' => $obtainednext),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('next' => $expected['next']),
                                    array("expected:"));
            }

            // left
            if (!$leftpassed) {
                $this->dump_results(array('left' => $obtained->left),
                                    array("\n$enginename failed on regex '$regex' and string '$str' ($classname, $methodname"));
                $this->dump_results(array('left' => $expected['left'][0]),
                                    array("expected:"));
            }
        }

        // Return true if everything is correct, false otherwise.
        return $ismatchpassed && $fullpassed && $indexfirstpassed && $lengthpassed && $nextpassed && $leftpassed;
    }

    /**
     * The main function - runs all matchers on test-data sets.
     */
    function test() {
        $matchoptions = new qtype_preg_matching_options();  // Forced subpattern catupring.
        $enginename = $this->engine_name();
        $blacklist = array_merge($this->blacklist_tags(), $this->blacklist);
        foreach ($this->testdataobjects as $testdataobj) {
            $testmethods = get_class_methods($testdataobj);
            $classname = get_class($testdataobj);
            foreach ($testmethods as $methodname) {
                // Filtering class methods by names. A test method name should start with 'data_for_test_'.
                if (strpos($methodname, 'data_for_test_') !== 0) {
                    continue;
                }

                // Get current test data.
                $data = $testdataobj->$methodname();
                $regex = $data['regex'];
                $modifiers = null;
                $regextags = array();
                $notation = self::NOTATION_NATIVE;
                if (array_key_exists('modifiers', $data)) {
                    $modifiers = $data['modifiers'];
                }
                if (array_key_exists('tags', $data)) {
                    $regextags = $data['tags'];
                }
                if (array_key_exists('notation', $data)) {
                    $notation = $data['notation'];
                }

                // Skip regexes with blacklisted tags.
                if (count(array_intersect($blacklist, $regextags)) > 0) {
                    continue;
                }

                // Try to get matcher for the regex.
                try {
                    $matchoptions->debugmode = in_array(self::TAG_DEBUG_MODE, $regextags);
                    $matcher = $this->question->get_matcher($enginename, $regex, false, strpos($modifiers, 'i') === false, null, $notation);
                    $matcher->set_options($matchoptions);
                } catch (Exception $e) {
                    echo 'EXCEPTION CATCHED DURING BUILDING MATCHER, test name is ' . $methodname .  "\n" . $e->getMessage() . "\n";
                    $this->exceptionscount++;
                    continue;
                }

                // Skip to the next regex if there's something wrong.
                if ($this->check_for_errors($matcher)) {
                    $this->skipcount += count($data['tests']);
                    continue;
                }

                // Iterate over all tests.
                foreach ($data['tests'] as $expected) {
                    $str = $expected['str'];
                    $strtags = array();
                    if (array_key_exists('tags', $expected)) {
                        $strtags = $expected['tags'];
                    }

                    $tags = array_merge($regextags, $strtags);

                    // Skip tests with blacklisted tags.
                    if (count(array_intersect($blacklist, $tags)) > 0) {
                        continue;
                    }

                    // There can be exceptions during matching.
                    try {
                        $matcher->match($str);
                        $obtained = $matcher->get_match_results();
                    } catch (Exception $e) {
                        echo "EXCEPTION CATCHED DURING MATCHING, test name is " . $methodname .  "\n" . $e->getMessage() . "\n";
                        $this->exceptionscount++;
                        continue;
                    }

                    // Results obtained, check them.
                    $skippartialcheck = in_array(self::TAG_DONT_CHECK_PARTIAL, $tags);
                    if ($this->compare_results($regex, $notation, $str, $modifiers, $matcher, $expected, $obtained, $classname, $methodname, $skippartialcheck, true)) {
                        $this->passcount++;
                    } else {
                        $this->failcount++;
                    }
                }
            }
        }
        echo "\n=======================\n";
        echo 'PASSED:     ' . $this->passcount . "\n";
        echo 'FAILED:     ' . $this->failcount . "\n";
        echo 'SKIPPED:    ' . $this->skipcount . "\n";
        echo 'EXCEPTIONS: ' . $this->exceptionscount . "\n";
        echo "=======================\n";
    }
}
