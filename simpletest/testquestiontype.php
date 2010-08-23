<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/10 21:40:20 dvkolesov Exp $
/**
 * Unit tests for (some of) question/type/preg/dfa_preg_matcher.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');
//see carefully commented example of test on lines 617-644
class dfa_preg_matcher_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
        $this->qtype = new dfa_preg_matcher();
    }
    
    function tearDown() {
        $this->qtype = null;   
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'dfa_preg_matcher');
    }
    //Unit test for nullable function
    function test_nullable_leaf() {
        $this->qtype->build_tree('a');
        $this->assertFalse(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_leaf_iteration_node() {
        $this->qtype->build_tree('a*');
        $this->assertTrue(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_leaf_concatenation_node() {
        $this->qtype->build_tree('ab');
        $this->assertFalse(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_leaf_alternative_node() {
        $this->qtype->build_tree('a|b');
        $this->assertFalse(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_node_concatenation_node() {
        $this->qtype->build_tree('a*bc');
        $this->assertFalse(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_node_alternative_node() {
        $this->qtype->build_tree('a*|bc');
        $this->assertTrue(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_third_level_node() {
        $this->qtype->build_tree('(?:(?:a|b)|c*)|d*');
        $this->assertTrue(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_question_quantificator() {
        $this->qtype->build_tree('a?');
        $this->assertTrue(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_negative_character_class() {
        $this->qtype->build_tree('[^a]');
        $this->assertFalse(dfa_preg_matcher::nullable($this->qtype->roots[0]));
    }
    function test_nullable_assert() {
        $this->qtype->build_tree('a(?=.*b)[xcvbnm]*');
        $this->assertTrue(dfa_preg_matcher::nullable($this->qtype->roots[0]->firop->secop));
    }
    //Unit test for firstpos function
    function test_firstpos_leaf() {
        $this->qtype->build_tree('a');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_leaf_concatenation_node() {
        $this->qtype->build_tree('ab');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_leaf_alternative_node() {
        $this->qtype->build_tree('a|b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result=dfa_preg_matcher::firstpos($this->qtype->roots[0]);
		$this->assertTrue(count($result) == 2 && $result[0] == 1 && $result[1] == 2);
    }
    function test_firstpos_three_leaf_alternative() {
        $this->qtype->build_tree('a|b|c');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_firstpos_leaf_iteration_node() {
        $this->qtype->build_tree('a*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_node_concatenation_node() {
        $this->qtype->build_tree('c*(?:a|b)');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_firstpos_node_alternative_node() {
        $this->qtype->build_tree('a|b|c*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_firstpos_node_iteration_node() {
        $this->qtype->build_tree('(?:a*)*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_question_quantificator() {
        $this->qtype->build_tree('a?');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_negative_character_class() {
        $this->qtype->build_tree('[^a]b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($this->qtype->roots[0]->firstpos) == 1 && $this->qtype->roots[0]->firstpos[0] == -1);
        $this->assertTrue(count($this->qtype->roots[0]->firop->firstpos) == 1 && $this->qtype->roots[0]->firop->firstpos[0] == -1);
    }
    function test_firstpos_assert() {
        $this->qtype->build_tree('a(?=.*b)[xcvbnm]*');
        $this->qtype->numeration($this->qtype->roots[0], ASSERT + 2);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        $this->assertTrue(count($this->qtype->roots[0]->firop->secop->firstpos) == 1 && $this->qtype->roots[0]->firop->secop->firstpos[0]>ASSERT);
    }
    //Unit test for lastpos function
    function test_lastpos_leaf() {
        $this->qtype->build_tree('a');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_leaf_concatenation_node() {
        $this->qtype->build_tree('ab');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 2);
    }
    function test_lastpos_leaf_alterbative_node() {
        $this->qtype->build_tree('a|b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 2 && $result[0] == 1 && $result[1] == 2);
    }
    function test_lastpos_leaf_iteration_node() {
        $this->qtype->build_tree('a*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_node_concatenation_node() {
        $this->qtype->build_tree('(?:a|b)c*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_lastpos_node_alternative_node() {
        $this->qtype->build_tree('a|b|c*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_lastpos_node_iteration_node() {
        $this->qtype->build_tree('(?:a*)*');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_question_quantificator() {
        $this->qtype->build_tree('a?');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_negative_character_class() {
        $this->qtype->build_tree('[^a]|b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        $result = dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($result) == 2 && $result[0] == -1 && $result[1] == 2);
    }
    function test_lastpos_assert() {
        $this->qtype->build_tree('a(?=.*b)[xcvbnm]*');
        $this->qtype->numeration($this->qtype->roots[0], ASSERT + 2);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $this->assertTrue(count($this->qtype->roots[0]->firop->secop->lastpos) && $this->qtype->roots[0]->firop->secop->lastpos[0]>ASSERT);
    }
    //Unit tests for followpos function
    function test_followpos_node_concatenation_node() {
        $this->qtype->build_tree('(?:a|b)*ab');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::nullable($this->qtype->roots[0]);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $result=null;
        dfa_preg_matcher::followpos($this->qtype->roots[0], $result);
        $res1 = (count($result[1]) == 3 && $result[1][0] == 1 && $result[1][1] == 2 && $result[1][2] == 3);
        $res2 = (count($result[2]) == 3 && $result[2][0] == 1 && $result[2][1] == 2 && $result[2][2] == 3);
        $res3 = (count($result[3]) == 1 && $result[3][0] == 4);
        $this->assertTrue($res1 && $res2 && $res3);
    }
    function test_followpos_three_node_alternative() {
        $this->qtype->build_tree('ab|cd|ef');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $result=null;
        dfa_preg_matcher::followpos($this->qtype->roots[0], $result);
        $this->assertTrue(count($result[1]) == 1 && $result[1][0] == 2);
        $this->assertTrue(count($result[3]) == 1 && $result[3][0] == 4);
        $this->assertTrue(count($result[5]) == 1 && $result[5][0] == 6);
    }
    function test_followpos_question_quantificator() {
        $this->qtype->build_tree('a?b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $result=null;
        dfa_preg_matcher::followpos($this->qtype->roots[0], $result);
        $this->assertTrue(count($result[1]) == 1 && $result[1][0] == 2);
    }
    function test_followpos_negative_character_class() {
        $this->qtype->build_tree('[^a]b');
        $this->qtype->numeration($this->qtype->roots[0], 0);
        dfa_preg_matcher::firstpos($this->qtype->roots[0]);
        dfa_preg_matcher::lastpos($this->qtype->roots[0]);
        $result=null;
        dfa_preg_matcher::followpos($this->qtype->roots[0], $result);
        $this->assertTrue(count($result[-1]) == 1 && $result[-1][0] == 2);
    }
    //Unit test for buildfa function
    function test_buildfa_easy() {//ab
        $this->qtype->build_tree('ab');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 1 && $this->qtype->finiteautomates[0][0]->passages[1] == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 1 && $this->qtype->finiteautomates[0][1]->passages[2] == 2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][2]->passages) == 1 && $this->qtype->finiteautomates[0][2]->passages[STREND] == -1);
    }
    function test_buildfa_iteration() {//ab*
        $this->qtype->build_tree('ab*');
		$this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 1);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 2);
        $this->assertTrue($this->qtype->finiteautomates[0][$n1]->passages[STREND] == -1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == $n1);
    }
    function test_buildfa_alternative() {//a|b
        $this->qtype->build_tree('a|b');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2 && $this->qtype->finiteautomates[0][0]->passages[1] == 1 && 
                            $this->qtype->finiteautomates[0][0]->passages[2] == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 1 && $this->qtype->finiteautomates[0][1]->passages[STREND] == -1);
    }
    function test_buildfa_alternative_and_iteration() {//(a|b)c*
        $this->qtype->build_tree('(?:a|b)c*');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 2 && $this->qtype->finiteautomates[0][$n1]->passages[3] == $n1 && 
                            $this->qtype->finiteautomates[0][$n1]->passages[STREND] == -1);
    }
    function test_buildfa_nesting_alternative_and_iteration() {//(ab|cd)*
        $this->qtype->build_tree('(?:ab|cd)*');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 3 && $this->qtype->finiteautomates[0][0]->passages[STREND] == -1);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[3];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == 0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[4] == 0);
    }
    function test_buildfa_question_quantificator() {//a?b
        $this->qtype->build_tree('a?b');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[2];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == $n2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[STREND] == -1);
    }
    function test_buildfa_negative_character_class() {//(a[^b]|c[^d])*
        $this->qtype->build_tree('(?:a[^b]|c[^d])*');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 3);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[3];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[-2] == 0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[-4] == 0);
    }
    function test_buildfa_assert() {//a(?=.*b)[xcvbnm]*
        $this->qtype->build_tree('a(?=.*b)[xcvbnm]*');
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->asserts) == 1 && count($this->qtype->finiteautomates[0][0]->passages) == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 2 && $this->qtype->finiteautomates[0][1]->passages[3] == 1 && 
                            $this->qtype->finiteautomates[0][1]->passages[STREND] == -1);
        $this->assertTrue(count($this->qtype->roots) == 2 && $this->qtype->roots[ASSERT + 2] == $this->qtype->roots[0]->firop->firop->secop->firop);
        $this->qtype->append_end(ASSERT+2);
        $this->qtype->buildfa(ASSERT+2);
        $this->assertTrue(count($this->qtype->finiteautomates[ASSERT+2][0]->passages) == 2 && $this->qtype->finiteautomates[ASSERT+2][0]->passages[DOT+1] == 0 && 
                            $this->qtype->finiteautomates[ASSERT+2][0]->passages[2] == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[ASSERT+2][1]->passages) == 1 && $this->qtype->finiteautomates[ASSERT+2][1]->passages[STREND] == -1);
    }
    //Unit tests for compare function
    function test_compare_full_incorrect() {//ab
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->connection[0][1] = 'a';
        $this->connection[0][2] = 'b';
        $result=$this->qtype->compare('b',0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
    }
    function test_compare_first_character_incorrect() {//ab
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $this->qtype->connection[0][3] = 'c';
        $result = $this->qtype->compare('cb',0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
    }
    function test_compare_particular_correct() {//ab
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $this->qtype->connection[0][3] = 'c';
        $result = $this->qtype->compare('ac',0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
    }
    function test_compare_full_correct() {//ab
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $result = $this->qtype->compare('ab',0);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
    }
    function test_compare_question_quantificator() {//a?b
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][0]->passages[2] = 2;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $result1 = $this->qtype->compare('ab', 0);
        $result2 = $this->qtype->compare('b', 0);
        $result3 = $this->qtype->compare('Incorrect string', 0);
        $this->assertTrue($result1->full);
        $this->assertTrue($result1->index == 1 && $result1->next == 0);
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 0 && $result2->next == 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == -1 && $result3->next == 'b' || $result3->next == 'a');
    }
    function test_compare_negative_character_class() {//[^a][b]
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[-1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $result1 = $this->qtype->compare('ab',0);
        $result2 = $this->qtype->compare('bb',0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result1->index == -1 && isset($result1->next) && $result1->next != 'a');
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 1 && $result2->next == 0);
    }
    function test_compare_dot() {//.b
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[DOT+1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][2] = 'b';
        $result1 = $this->qtype->compare('ab',0);
        $result2 = $this->qtype->compare('fbf',0);
        $result3 = $this->qtype->compare('fff',0);
        $this->assertTrue($result1->full);
        $this->assertTrue($result1->index == 1 && $result1->next == 0);
        $this->assertFalse($result2->full);
        $this->assertTrue($result2->index == 1 && $result2->next == 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == 0 && $result3->next == 'b');
    }
    function test_compare_assert() {//a(?=.*b)[xcvbnm]*
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[ASSERT+2][0] = new finite_automate_state;
        $this->qtype->finiteautomates[ASSERT+2][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[3] = 1;
        $this->qtype->finiteautomates[0][1]->passages[STREND] = -1;
        $this->qtype->finiteautomates[0][0]->asserts[0] = ASSERT+2;
        $this->qtype->finiteautomates[ASSERT+2][0]->passages[DOT+1] = 0;
        $this->qtype->finiteautomates[ASSERT+2][0]->passages[2] = 1;
        $this->qtype->finiteautomates[ASSERT+2][1]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][3] = 'xcvbnm';
        $this->qtype->connection[ASSERT+2][2] = 'b';
        $result1 = $this->qtype->compare('an',0);
        $result2 = $this->qtype->compare('annvnvb',0);
        $result3 = $this->qtype->compare('annvnvv',0);
        $result4 = $this->qtype->compare('abnm',0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result1->index == 1 && ($result1->next === 'b' || $result1->next ===  'D'));
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 6 && $result2->next === 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == 6 && ($result3->next === 'b' || $result3->next ===  'D'));
        $this->assertTrue($result4->full);
        $this->assertTrue($result4->index == 3 && $result4->next === 0);
    }
    function test_compare_unclock() {//ab
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[STREND] = -1;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $result = $this->qtype->compare('OabO', 0, 0, false);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next === 'a' && $result->offset == 0);
        $result = $this->qtype->compare('OabO', 0, 1, false);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 2 && $result->next === 0 && $result->offset == 1);
        $result = $this->qtype->compare('OabO', 0, 1, true);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 2 && $result->next === 0 && $result->offset == 1);
        $result = $this->qtype->compare('OabO', 0, 2, false);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next === 'a' && $result->offset == 2);
    }
    function test_compare_unlock_iteration() {//(?:abc)*
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][0]->passages[STREND] = -1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[3] = 0;
        $this->qtype->connection[0][1] = 'a';
        $this->qtype->connection[0][2] = 'b';
        $this->qtype->connection[0][3] = 'c';
        $result = $this->qtype->compare('abcabcab', 0, 0, false);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 5 && $result->next === 0 && $result->offset == 0);
    }
    //General tests, testing parser + buildfa + compare (also nullable, firstpos, lastpos, followpos and other in buildfa)
    //dfa_preg_matcher without input and output data.
    function test_general_repeat_characters() {
        $matcher = new dfa_preg_matcher('^(?:a|b)*abb$');
        $matcher->match('cd');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == -1 && $matcher->next_char() === 'a');
        $matcher->match('ca');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == -1 && $matcher->next_char() === 'a');
        $matcher->match('ac');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && ($matcher->next_char() === 'b') || $matcher->next_char() === 'a');
        $matcher->match('bb');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && $matcher->next_char() === 'a');
        $matcher->match('abb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 2 && $matcher->next_char() === 0);
        $matcher->match('ababababababaabbabababababababaabb');//34 characters
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 33 && $matcher->next_char() ===0);
    }
    function test_general_assert() {
        $matcher = new dfa_preg_matcher('^a(?=.*b)[xcvbnm]*$');
        $result1 = $matcher->match('an');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 1 && ($matcher->next_char() === 'b' || $matcher->next_char() ===  'D'));
        $matcher->match('anvnvb');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 5 && $matcher->next_char() === 0);
        $matcher->match('avnvnv');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 5 && ($matcher->next_char() === 'b' || $matcher->next_char() ===  'D'));
        $matcher->match('abnm');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === 0);
    }
    /*
    *   this is overall test for dfa_preg_matcher class
    *   you may use it as example of test
    */
    function test_general_two_asserts() {
        $matcher = new dfa_preg_matcher('^a(?=b)(?=.*c)[xcvbnm]*$');//put regular expirience in constructor for building dfa.
        /*  
        *   call match method for matching string with regex, string is argument, regex was got in constructor,
        *   results of matching get with method
        *   1)index - last_correct_character_index()
        *   2)full  - is_matching_complete()
        *   3)next  - next_char()
        */
        //use === but not == for next_char, because 'b' == 0 is true
        $matcher->match('avnm');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() === 'b');
        $matcher->match('acnm');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 0 && $matcher->next_char() === 'b');
        $matcher->match('abnm');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && ($matcher->next_char() === 'c' || $matcher->next_char() ===  'D'));
        $matcher->match('abnc');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index() == 3 && $matcher->next_char() === 0);
    }
    //Unit test for copy_subtree()
    function test_copy_subtree() {
        $this->qtype->build_tree('(?:[original][original])(?:[original][original])');
        $this->qtype->roots[1] = dfa_preg_matcher::copy_subtree($this->qtype->roots[0]);
        $this->assertTrue($this->qtype->roots[1]->firop->firop->chars == 'original' && $this->qtype->roots[1]->firop->secop->chars == 'original' &&
                          $this->qtype->roots[1]->secop->firop->chars == 'original' && $this->qtype->roots[1]->secop->secop->chars == 'original');
        $this->qtype->roots[1]->firop->firop->chars = 'cloned';
        $this->qtype->roots[1]->firop->secop->chars = 'cloned';
        $this->qtype->roots[1]->secop->firop->chars = 'cloned';
        $this->qtype->roots[1]->secop->secop->chars = 'cloned';
        $this->assertTrue($this->qtype->roots[0]->firop->firop->chars == 'original' && $this->qtype->roots[0]->firop->secop->chars == 'original' &&
                          $this->qtype->roots[0]->secop->firop->chars == 'original' && $this->qtype->roots[0]->secop->secop->chars == 'original');
    }
    //Unit tests for convert_tree()
    function test_convert_tree_quantificator_plus() {//a+b
        $this->qtype->build_tree('a+b');
        $this->qtype->roots[0]->firop->subtype = NODE_PLUSQUANT;
        dfa_preg_matcher::convert_tree($this->qtype->roots[0]);
        $this->assertTrue($this->qtype->roots[0]->firop->subtype == NODE_CONC && $this->qtype->roots[0]->firop->firop->type == LEAF &&
                          $this->qtype->roots[0]->firop->secop->type == NODE && $this->qtype->roots[0]->firop->secop->subtype == NODE_ITER);
    }
    function test_convert_tree_quantificator_l2r4() {//a{2,4}b
        $this->qtype->build_tree('a{2,4}b');
        $this->qtype->roots[0]->firop->subtype = NODE_QUANT;
        $this->qtype->roots[0]->firop->leftborder = 2;
        $this->qtype->roots[0]->firop->rightborder = 4;
        dfa_preg_matcher::convert_tree($this->qtype->roots[0]);
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $result1 = $this->qtype->compare('ab', 0);
        $result2 = $this->qtype->compare('aab', 0);
        $result3 = $this->qtype->compare('aaab', 0);
        $result4 = $this->qtype->compare('aaaab', 0);
        $result5 = $this->qtype->compare('aaaaab', 0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertFalse($result5->full);
    }
    function test_convert_tree_quantificator_l0r4() {//a{,4}b
        $this->qtype->build_tree('a{,4}b');
        $this->qtype->roots[0]->firop->subtype = NODE_QUANT;
        $this->qtype->roots[0]->firop->leftborder = 0;
        $this->qtype->roots[0]->firop->rightborder = 4;
        dfa_preg_matcher::convert_tree($this->qtype->roots[0]);
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $result0 = $this->qtype->compare('b', 0);
        $result1 = $this->qtype->compare('ab', 0);
        $result2 = $this->qtype->compare('aab', 0);
        $result3 = $this->qtype->compare('aaab', 0);
        $result4 = $this->qtype->compare('aaaab', 0);
        $result5 = $this->qtype->compare('aaaaab', 0);
        $this->assertTrue($result0->full);
        $this->assertTrue($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertFalse($result5->full);
    }
    function test_convert_tree_quantificator_l2rinf() {//a{2,}b
        $this->qtype->build_tree('a{2,}b');
        $this->qtype->roots[0]->firop->subtype = NODE_QUANT;
        $this->qtype->roots[0]->firop->leftborder = 2;
        $this->qtype->roots[0]->firop->rightborder = -1;
        dfa_preg_matcher::convert_tree($this->qtype->roots[0]);
        $this->qtype->append_end(0);
        $this->qtype->buildfa(0);
        $result1 = $this->qtype->compare('ab', 0);
        $result2 = $this->qtype->compare('aab', 0);
        $result3 = $this->qtype->compare('aaab', 0);
        $result4 = $this->qtype->compare('aaaab', 0);
        $result5 = $this->qtype->compare('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaab', 0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertTrue($result5->full);
    }
    //Unit test for wave
    function test_wave_easy() {
        $matcher = new dfa_preg_matcher('abcd');
        $matcher->match('abce');
        $this->assertTrue($matcher->next_char() === 'd');
    }
    function test_wave_iteration() {
        $matcher = new dfa_preg_matcher('abc*d');
        $matcher->match('abB');
        $this->assertTrue($matcher->next_char() === 'd');
    }
    function test_wave_alternative() {
        $matcher = new dfa_preg_matcher('a(?:cdgfhghghgdhgfhdgfydgfdhgfdhgfdhgfhdgfhdgfhdgfydgfy|b)');
        $matcher->match('a_incorrect');
        $this->assertTrue($matcher->next_char() === 'b');
    }
    function test_wave_repeat_chars() {
        $matcher = new dfa_preg_matcher('^(?:a|b)*abb$');
        $matcher->match('ababababbbbaaaabbbabbbab');
        $this->assertTrue($matcher->next_char() === 'b');
    }
    function test_wave_complex() {
        $matcher = new dfa_preg_matcher('(?:fgh|ab?c)+');
        $matcher->match('something');
        $this->assertTrue($matcher->next_char() === 'a');
    }
    //Unit tests for left character count determined by wave function
    function test_wave_left_full_true() {
        $matcher = new dfa_preg_matcher('abcd');
        $matcher->match('abcd');
        $this->assertTrue($matcher->characters_left() == 0);
    }
    function test_wave_left_easy_regex() {
        $matcher = new dfa_preg_matcher('abcdefghi');
        $matcher->match('abcd');
        $this->assertTrue($matcher->characters_left() == 5);
    }
    function test_wave_left_complex_regex() {
        $matcher = new dfa_preg_matcher('ab+c{5,9}(?:ab?c|dfg)|averylongword');
        $matcher->match('a');
        $this->assertTrue($matcher->characters_left() == 8);
    }
}
?>