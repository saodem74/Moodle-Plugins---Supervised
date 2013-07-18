<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

class preg_fa_read_fa_tests extends PHPUnit_Framework_TestCase {

    public function test_disclosure_tags() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[((/(a-z)/]"];
                    1->2[label="[b-k/)]"];
                    2->3[label="[(/c-z/))]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }
    
    public function test_loop() {
        $dotdescription = 'digraph example {
                    0;
                    4;
                    0->1[label="[0-9]"]; 
                    1->2[label="[abc]"]; 
                    1->4[label="[01]"]; 
                    2->2[label="[a-z]"]; 
                    2->3[label="[-?,]"]; 
                    3->4[label="[a]"]; 
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('4');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 4);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(4,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_indirect_loop() {
        $dotdescription = 'digraph example {
                    0;
                     4;
                    0->1[label="[a-c]"];
                    1->2[label="[0-9]"];
                    2->4[label="[a-f]"];
                    0->3[label="[01]"];
                    3->4[label="[y]"];
                    4->0[label="[bc]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('4');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 4);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(4,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(1,$pregleaf, 0);
        $resultautomata->add_transition($transition);
        
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_hidden_characters() {
        $dotdescription = 'digraph example {
                    0;
                    6;
                    0->1[label="[\\\\\\-]"];
                    1->2[label="[\\$\\Z]"];
                    2->3[label="[\\[\\]]"];
                    3->4[label="[\\^\\A]"];
                    4->5[label="[\\"\\/\\.]"];
                    5->6[label="[\\(\\)]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('6');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('3');
        $resultautomata->add_state('4');
        $resultautomata->add_state('5');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 4);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(4,$pregleaf, 5);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(5,$pregleaf, 6);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(6,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_several_endstates() {
        $dotdescription = 'digraph example {
                    0;
                    1;2;4;
                    0->1[label="[a-c]"];
                    1->2[label="[0-9]"];
                    2->4[label="[a-f]"];
                    0->3[label="[01]"];
                    3->4[label="[y]"];
                    4->0[label="[bc]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_state('4');
        $resultautomata->add_state('3');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        $resultautomata->add_end_state(2);
        $resultautomata->add_end_state(3);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(1,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 4);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(4,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 0);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_character_ranges() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[a-kn-z]"];
                    1->2[label="[a-jxy]"];
                    2->3[label="[abc-hl-x]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_asserts() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->1[label="[0-9]"];
                    1->2 [label="[$\\\\z]"];
                    2->3 [label="[^a-z]"];
                    0->3[label="[xy]"];
                    1->3 [label="[\\\\A]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('1');
        $resultautomata->add_state('2');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 1);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_unitedstate() {
        $dotdescription = 'digraph example {
                    0;
                    3;
                    0->"1   2"[label="[0-9]"];
                    "1   2"->3 [label="[\\\\A0-9]"];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('0');
        $resultautomata->add_state('3');
        $resultautomata->add_state('"1   2"');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_different_automata() {
        $dotdescription = 'digraph example {
                    "0,";
                    ",2";
                    "0,"->"1,0"[label="[a-z]",color=violet];
                    "1,0"->"2,1"[label="[0-9]",color=red];
                    "2,1"->",2"[label="[a-z]",color=blue];
                    }';

        $resultautomata = new qtype_preg_nfa(0, 0, 0, array());
        $resultautomata->add_state('"0,"');
        $resultautomata->add_state('",2"');
        $resultautomata->add_state('"1,0"');
        $resultautomata->add_state('"2,1"');
        $resultautomata->add_start_state(0);
        $resultautomata->add_end_state(1);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(0,$pregleaf, 2);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(2,$pregleaf, 3);
        $resultautomata->add_transition($transition);
        //fill pregleaf
        $pregleaf = new qtype_preg_leaf_charset();
        $transition = new qtype_preg_fa_transition(3,$pregleaf, 1);
        $resultautomata->add_transition($transition);

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $automata = new qtype_preg_nfa(0, 0, 0, array());
        $automata->read_fa($dotdescription, $origin);

        $this->assertEquals($automata, $resultautomata, 'Result automata is not equal to expected');
    }
}
