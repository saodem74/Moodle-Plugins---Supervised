<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/nfa_preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_preg_matcher.php');

class nfa_match_test extends UnitTestCase {


    function setUp() {
    }

    function tearDown() {
    }

/****************************************************************************************************************************************************************/
/*                                                                                                                                                              */
/*                                                          	      tests for nfa::match()                                                       		        */
/*                                                                                                                                                              */
/****************************************************************************************************************************************************************/

	function test_match_concat() {
		$matcher = new nfa_preg_matcher('^the matcher works');
		$res = $matcher->automaton->match('the matcher works', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 17);
		$res = $matcher->automaton->match('_the matcher works', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 0);
		$res = $matcher->automaton->match('the matcher', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 11);
	}

	function test_match_alt() {
		$matcher = new nfa_preg_matcher('(^abc|def$)');
		$res = $matcher->automaton->match('abcf', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
		$res = $matcher->automaton->match('def', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
		$res = $matcher->automaton->match('deff', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 3);
	}

	function test_match_digit() {
		$matcher = new nfa_preg_matcher('(\d)+a');
		$res = $matcher->automaton->match('29a', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
	}

	function test_match_word_char() {
		$matcher = new nfa_preg_matcher('a\wa');
		$res = $matcher->automaton->match('a_a', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
		$res = $matcher->automaton->match('a{a', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 1);
	}

	function test_match_cs() {
		$matcher = new nfa_preg_matcher('aBcD');
		$res = $matcher->automaton->match('abcd', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 4);
		$res = $matcher->automaton->match('abcd', 0, true);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 1);
	}

	function test_match_brace() {
		$matcher = new nfa_preg_matcher('(a|b|c|d|e|f){2,}');
		$res = $matcher->automaton->match('abcaabcabA', 0, true);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 9);
		$res = $matcher->automaton->match('a', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 1);
		$res = $matcher->automaton->match('SOME CAPS HERE', 0, true);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 0);
		$res = $matcher->automaton->match('abcdef', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 6);
	}

	function test_match_qu() {
		$matcher = new nfa_preg_matcher('(shortstr|verylongstring)?');
		$res = $matcher->automaton->match('verylongstring!!!!!!!!', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 14);
	}

	function test_match_aster() {
		$matcher = new nfa_preg_matcher('(ab)*');
		$res = $matcher->automaton->match('abababababababababababababababab', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 32);
		$res = $matcher->automaton->match('AB', 0, true);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 0);
		$matcher1 = new nfa_preg_matcher('([A-Z]|str1|str2)*');
		$res = $matcher1->automaton->match('CAPSstr2str1str3', 0, true);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 12);
		$res = $matcher1->automaton->match('_AB', 0, true);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 0);
	}

	function test_match_assertions_simple() {
		$matcher = new nfa_preg_matcher('(^[a-z 0-9]\b[a-z 0-9]\B[a-z 0-9])');
		$res = $matcher->automaton->match(' abc', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
		$res = $matcher->automaton->match(' 9bc', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 3);
		$res = $matcher->automaton->match('  bc', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 1);
		$matcher1 = new nfa_preg_matcher('(^abc[a-z.?!]\b[a-z])');
		$res = $matcher1->automaton->match('abcaa', 0, false);
		$this->assertTrue(!$res->isfullmatch && $res->matchcnt == 4);
		$res = $matcher1->automaton->match('abc?z', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 5);
	}

	function test_match_empty_loops() {
		$matcher = new nfa_preg_matcher('^*[a-z 0-9](\b)+a${1,}');
		$res = $matcher->automaton->match(' a', 0, false);
		$this->assertTrue($res->isfullmatch && $res->matchcnt == 2);
	}

/****************************************************************************************************************************************************************/
/*                                                                                                                                                              */
/*                                                          	      tests for nfa_preg_matcher::match()                                                       */
/*                                                                                                                                                              */
/****************************************************************************************************************************************************************/

	function test_match_easy() {
		$matcher = new nfa_preg_matcher('^a((b(c))*)d$');
		$matcher->match('abcbcd');
		$this->assertTrue(	$matcher->is_matching_complete() && $matcher->last_correct_character_index() == 5 &&
							$matcher->first_correct_character_index(1) == 1 && $matcher->last_correct_character_index(1) == 4 &&
							$matcher->first_correct_character_index(2) == 1 && $matcher->last_correct_character_index(2) == 2);
	}
	function test_match_subpatterns_alternated() {
		$matcher = new nfa_preg_matcher('((ab)|(cd)|(efgh))');
		$matcher->match('abcdefgh');
		$this->assertTrue($matcher->is_matching_complete() && $matcher->first_correct_character_index() == 4 && $matcher->last_correct_character_index() == 7);
	}

}

?>