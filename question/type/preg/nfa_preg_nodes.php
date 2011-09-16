<?php

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * defines a transition between two states
 */
class nfa_transition
{
    public $loops = false;        // true if this transition makes a loop: for example, (...)* contains an epsilon-transition that makes a loop

    public $pregleaf;            // transition data, a reference to an object of preg_leaf

    public $state;                // the state which this transition leads to, a reference to an object of nfa_state

    public $subpatt_start = array();        // an array of subpatterns which start in this transition

    public $subpatt_end = array();            // an array of subpatterns which end in this transition

    public $belongs_to_subpatt = array();    // an array of subpatterns which this transition belongs to

    public function __construct(&$_pregleaf, &$_state, $_loops) {
        $this->pregleaf = $_pregleaf;
        $this->state = $_state;
        $this->loops = $_loops;
    }
    
    /**
     * returns number of characters consumed by this transition
     */
    public function length() {
        // TODO: backrefs
        if ($this->pregleaf->consumes()) {
            return 1;            
        }
        return 0;        
    }

}

/**
 * defines an nfa state
 */
class nfa_state
{

    public $startsinfinitequant = false;    // true if this state starts an infinite quantifier either * or + or {m,}

    public $next = array();                    // an array of objects of nfa_transition

    public $id;                                // id of the state, debug variable

    /**
     * appends a next possible state
     * @param next - a reference to the transition to be appended
     */
    public function append_transition(&$next) {
        $exists = false;
        $size = count($this->next);
        // not unique transitions are not appended
        foreach($this->next as $curnext) {
            if ($curnext->pregleaf == $next->pregleaf && $curnext->state === $next->state) {
                $exists = true;
            }
        }
        if (!$exists) {
            array_push($this->next, $next);
        }
        return !$exists;
    }

    /**
     * replaces oldref with newref in every transition
     * @param oldref - a reference to the old state
     * @param newref - a reference to the new state
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach($this->next as $curnext)
            if ($curnext->state == $oldref) {
                $curnext->state = $newref;
            }
    }

    /**
     * merges two states
     * @param with - a reference to state the to be merged with
     */
    public function merge(&$with) {
        // move all transitions from $with to $this state
        foreach($with->next as $curnext) {
            $this->append_transition($curnext);
        }
        // unite fields by logical "or"
        if ($with->startsinfinitequant) {
            $this->startsinfinitequant = true;
        }
    }

    /**
     * debug function
     */
    public function is_equal(&$to) {
        return $this->next == $to->next;        //this is quite enough
    }

}

/**
 * defines a state of an automaton when running
 * used when matching a string
 */
class processing_state {

    public $state;                        // a reference to the state which automaton is in

    public $matchcnt;                    // the number of characters matched

    public $isfullmatch;                // whether the match is full

    public $nextpossible;                // the next possible character
    
    public $left;                        // number of characters left for matching

    public $subpattern_indexes_first = array();    // key = subpattern number

    public $subpattern_indexes_last = array();    // key = subpattern number
    
    public $subpatterns_captured = array();        // an array containing subpatterns captured at the moment

    public function __construct(&$_state, $_matchcnt, $_isfullmatch, $_nextpossible, $_left, $_subpattern_indexes_first, $_subpattern_indexes_last, $_subpatterns_captured) {
        $this->state = $_state;
        $this->matchcnt = $_matchcnt;
        $this->isfullmatch = $_isfullmatch;
        $this->nextpossible = $_nextpossible;
        $this->left = $_left;
        $this->subpattern_indexes_first = $_subpattern_indexes_first;
        $this->subpattern_indexes_last = $_subpattern_indexes_last;
        $this->subpatterns_captured = $_subpatterns_captured;
    }
}

/**
 * defines a nondeterministic finite automaton
 */
class nfa {

    public $startstate;            // a reference to the start nfa_state of the automaton

    public $endstate;            // a reference to the end nfa_state of the automaton

    public $states = array();    // an array containing references to states of the automaton

    var $graphvizpath = 'C:\Program Files (x86)\Graphviz2.26.3\bin';    // path to dot.exe of graphviz

    /**
     * generates a next possible character by a given path
     * @param lastchar - the last character matched
     * @param path - a reference to the path with merged assertions
     * @param pathindex - index of the current transition
     * @return - a character corresponding to the given path
     */
    private function generate_character($lastchar, &$path, $pathindex) {    // TODO

    }
    
    /**
     * clears $subpatt_start, $subpatt_end and $belongs_to_subpatt in every transition of the automaton
     */
    public function remove_subpatterns() {
        foreach ($this->states as $curstate) {
            foreach ($curstate->next as $curnext) {
                $curnext->subpatt_start = array();
                $curnext->subpatt_end = array();
                $curnext->belongs_to_subpatt = array();
            }
        }
    }

    /**
     * appends the state to the automaton
     * @param state - a regerence to the state to be appended
     */
    public function append_state(&$state) {
        array_push($this->states, $state);
    }

    /**
     * removes the state from the automaton
     * @param state - a reference to the state to be removed
     */
    public function remove_state(&$state) {
        foreach ($this->states as $key=>$curstate) {
            if ($curstate == $state) {
                unset($this->states[$key]);
            }
        }
    }

    /**
     * moves states from the automaton referred to by $from to this automaton
     * @param from - a reference to the automaton containing states to be moved
     */
    public function move_states(&$from) {
        // iterate until all states are moved
        foreach ($from->states as $curstate) {
            array_push($this->states, $curstate);
        }
        // clear the source
        $from->states = array();
    }

    /**
     * replaces oldref with newref in every transition of the automaton
     * @param oldref - a reference to the old state
     * @param newref - a reference to the new state
     */
    public function update_state_references(&$oldref, &$newref) {
        foreach ($this->states as $curstate) {
            $curstate->update_state_references($oldref, $newref);
        }
    }

    /**
     * checks if new result is better than old result
     * @param oldres - old result, an object of processing_state
     * @param newres - new result, an object of processing_state
     * @return - true if new result is more suitable
     */
    public function is_new_result_more_suitable(&$oldres, &$newres) {
        if    (($oldres->state != $this->endstate && $newres->matchcnt >= $oldres->matchcnt) ||                                        // new match is longer
            ($newres->state == $this->endstate && $oldres->state != $this->endstate) ||                                                // new match is full
            ($newres->state == $this->endstate && $oldres->state == $this->endstate && $newres->matchcnt >= $oldres->matchcnt)) {    // new match is full and longer
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * returns the minimal number of characters left for matching
     * @param laststate - the last state of the automaton, an object of processing_state
     * @return - number of characters left for matching
     */
    public function characters_left($laststate) {
        $curstates = array();    // states which the automaton is in
        $result = -1;
        array_push($curstates, $laststate);
        while (count($curstates) != 0) {
            $newstates = array();
            while (count($curstates) != 0) {
                $currentstate = array_pop($curstates);
                if (count($currentstate->state->next) == 0  && ($result == -1 || ($result != -1 && $currentstate->matchcnt < $result))) {
                    $result = $currentstate->matchcnt;
                }
                for ($i = 0; $i < count($currentstate->state->next); $i++) {
                    if (!$currentstate->state->next[$i]->loops) {
                        $next = $currentstate->state->next[$i];
                        $newstate = new processing_state($next->state, $currentstate->matchcnt + $next->length(), false, 0, -1, array(), array(), array());
                        array_push($newstates, $newstate);
                    }
                }
            }
            for ($i = 0; $i < count($newstates); $i++) {
                array_push($curstates, $newstates[$i]);
            }
            $newstates = array();
        }
        return $result - $laststate->matchcnt;
    }

    /**
     * returns the longest match using a string as input. matching is proceeded from a given start position
     * @param str - the original input string
     * @param startpos - index of the start position to match
     * @param cs - is matching case sensitive
     * @return - the longest character sequence matched
     */
    public function match($str, $startpos) {
        $curstates = array();    // states which the automaton is in
        $skipstates = array();    // contains states where infinite quantifiers start. it's used to protect from loops like ()*

        $result = new processing_state($this->startstate, 0, false, 0, -1, array(), array(), array());

        array_push($curstates, $result);
        while (count($curstates) != 0) {
            $newstates = array();
            // we'll replace curstates with newstates by the end of this cycle
            while (count($curstates) != 0) {
                // get the current state
                $currentstate = array_pop($curstates);
                // kill epsilon-cycles
                $skip = false;
                if ($currentstate->state->startsinfinitequant) {
                    // skipstates is sorted by matchcnt because transitions add characters
                    for ($i = count($skipstates) - 1; $i >= 0 && !$skip && $currentstate->matchcnt <= $skipstates[$i]->matchcnt; $i--)
                        if ($skipstates[$i]->state === $currentstate->state && $skipstates[$i]->matchcnt == $currentstate->matchcnt) {
                            $skip = true;
                        }
                    if (!$skip) {
                        array_push($skipstates, $currentstate);
                    }
                }
                // iterate over all transitions
                for ($i = 0; !$skip && $i < count($currentstate->state->next); $i++) {
                    $pos = $currentstate->matchcnt;
                    $length = 0;
                    $next = $currentstate->state->next[$i];
                    if ($next->pregleaf->match($str, $startpos + $pos, &$length, !$next->pregleaf->caseinsensitive )) {
                        // save subpattern indexes
                        foreach ($next->subpatt_start as $key=>$subpatt) {
                            if (!isset($currentstate->subpattern_indexes_first[$key])) {
                                $currentstate->subpattern_indexes_first[$key] = $startpos + $pos;
                            }
                        }
                        foreach ($next->subpatt_end as $key=>$subpatt) {
                            if (isset($currentstate->subpattern_indexes_first[$key]) && !(isset($currentstate->subpatterns_captured[$key]) && $currentstate->subpatterns_captured[$key])) {
                                $currentstate->subpattern_indexes_last[$key] = $startpos + $pos + $length - 1;
                            }
                        }
                        foreach ($currentstate->subpattern_indexes_first as $key=>$subpatt) {
                            if (!isset($next->belongs_to_subpatt[$key]) && isset($currentstate->subpattern_indexes_last[$key])) {
                                $currentstate->subpatterns_captured[$key] = true;
                            }
                        }                        
                        $newstate = new processing_state($next->state, $pos + $length, false, 0, -1, $currentstate->subpattern_indexes_first, $currentstate->subpattern_indexes_last, $currentstate->subpatterns_captured);
                        // save the state
                        array_push($newstates, $newstate);
                        // save the next state as a result if it's a matching state
                        if ($next->state == $this->endstate && $this->is_new_result_more_suitable(&$result, &$newstate)) {
                            $result = $newstate;
                        }
                    } elseif ($this->is_new_result_more_suitable(&$result, &$currentstate)) {
                            $result = $currentstate;
                    }
                }
            }

            // replace curstates with newstates
            for ($i = 0; $i < count($newstates); $i++) {
                array_push($curstates, $newstates[$i]);
            }
            $newstates = array();
        }
        $result->isfullmatch = ($result->state == $this->endstate);
        if ($result->matchcnt > 0) {
            $result->subpattern_indexes_first[0] = $startpos;
            $result->subpattern_indexes_last[0] = $startpos + $result->matchcnt - 1;
        } else {
            $result->subpattern_indexes_first[0] = -1;
            $result->subpattern_indexes_last[0] = -1;
        }
        if (!$result->isfullmatch) {
            // TODO character generation
            $result->left = $this->characters_left($result);
        } else {
            $result->left = 0;
        }
        /*foreach ($result->subpattern_indexes_first as $id=>$sp) {
            echo "id=".$id."index1=".$sp."index2=".$result->subpattern_indexes_last[$id]."<br />";
        }
        echo $result->matchcnt."<br />";*/
        return $result;

    }

    /**
    * debug function for generating dot code for drawing nfa
    * @param dotfilename - name of the dot file
    * @param jpgfilename - name of the resulting jpg file
    */
    public function draw_nfa($dotfilename, $jpgfilename) {
        $dotfile = fopen($dotfilename, 'w');
        // numerate all states
        $tmp = 0;
        foreach ($this->states as $curstate)
        {
            $curstate->id = $tmp;
            $tmp++;
        }
        // generate dot code
        fprintf($dotfile, "digraph {\n");
        fprintf($dotfile, "rankdir = LR;\n");
        foreach ($this->states as $curstate) {
            $index1 = $curstate->id;
            // draw a single state
            if (count($curstate->next) == 0) {
                fprintf($dotfile, "%s\n", "$index1");
            }
            // draw a state with transitions
            else
                foreach ($curstate->next as $curtransition) {
                    $index2 = $curtransition->state->id;
                    $lab = $curtransition->pregleaf->tohr();
                    fprintf($dotfile, "%s\n", "$index1->$index2"."[label=\"$lab\"];");
                }
        }
        fprintf($dotfile, "};");
        chdir($this->graphvizpath);
        exec("dot.exe -Tjpg -o\"$jpgfilename\" -Kdot $dotfilename");
        echo "<IMG src=\"$jpgfilename\" width=\"90%\">";
        fclose($dotfile);
    }

}

/**
* abstract class for both operators and leafs
*/
abstract class nfa_preg_node {

    public $pregnode;    // a reference to the corresponding preg_node

    /**
    * returns true if engine support the node, false otherwise
    * when returning false should also set rejectmsg field
    */
    public function accept() {
        return true; // accepting anything by default
    }

    /**
     * creates an automaton corresponding to this node
     * @param stackofautomatons - a stack which operators pop automatons off and operands push automatons onto
     * @param issubpattern - true if epsilon transitions are needed at the beginning and at the end of the automaton
     */
    abstract public function create_automaton(&$stackofautomatons);

    public function __construct(&$node, &$matcher) {
        $this->pregnode = $node;
    }

}


/**
* class for nfa transitions
*/
class nfa_preg_leaf extends nfa_preg_node {

    public function create_automaton(&$stackofautomatons) {
        // create start and end states of the resulting automaton
        $start = new nfa_state;
        $end = new nfa_state;
        $start->append_transition(new nfa_transition($this->pregnode, $end, false));
        $res = new nfa;
        $res->append_state($start);
        $res->append_state($end);
        $res->startstate = $start;
        $res->endstate = $end;
        array_push($stackofautomatons, $res);
    }

}

/**
* abstract class for nfa operators
*/
abstract class nfa_preg_operator extends nfa_preg_node {

    public $operands = array();    // an array of operands
    
    public function __construct($node, &$matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as &$operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }

}

/**
* defines concatenation
*/
class nfa_preg_node_concat extends nfa_preg_operator {

    public function create_automaton(&$stackofautomatons) {
        // first, operands create their automatons
        $this->operands[0]->create_automaton(&$stackofautomatons);
        $this->operands[1]->create_automaton(&$stackofautomatons);
        // take automata and concatenate them
        $second = array_pop($stackofautomatons);
        $first = array_pop($stackofautomatons);
        // update references because of merging states
        $second->update_state_references($second->startstate, $first->endstate);
        // merge and move states
        $first->endstate->merge($second->startstate);
        $second->remove_state($second->startstate);
        $first->endstate = $second->endstate;
        $first->move_states($second);
        array_push($stackofautomatons, $first);
    }

}

/**
* defines alternation
*/
class nfa_preg_node_alt extends nfa_preg_operator {

    public function create_automaton(&$stackofautomatons) {
        // first, operands create their automatons
        $this->operands[0]->create_automaton(&$stackofautomatons);
        $this->operands[1]->create_automaton(&$stackofautomatons);
        // take automata and alternate them
        $second = array_pop($stackofautomatons);
        $first = array_pop($stackofautomatons);
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        // add a new end state if the end state of the first automaton is looped
        $endlooped = false;
        foreach ($first->endstate->next as $curnext) {
            if ($curnext->loops) {
                $endlooped = true;
            }
        }
        if ($endlooped) {
            $endstate = new nfa_state;
            $first->append_state($endstate);
            $first->endstate->append_transition(new nfa_transition($epsleaf, $endstate, false));
            $first->endstate = $endstate;
        }
        // start states are merged, end states are alternated by an epsilon-transition for correct loop capturing
        $second->update_state_references($second->startstate, $first->startstate);
        $first->startstate->merge($second->startstate);
        $second->remove_state($second->startstate);
        $second->endstate->append_transition(new nfa_transition($epsleaf, $first->endstate, false));
        $first->move_states($second);
        array_push($stackofautomatons, $first);
    }

}

/**
* defines infinite quantifiers * + {m,}
*/
class nfa_preg_node_infinite_quant extends nfa_preg_operator {

    /**
     * creates an automaton for * or {0,} quantifier
     */
    private function create_aster(&$stackofautomatons) {
        $this->operands[0]->create_automaton(&$stackofautomatons);
        $body = array_pop($stackofautomatons);
        foreach ($body->startstate->next as $curnext) {
            $body->endstate->append_transition(new nfa_transition($curnext->pregleaf, $curnext->state, true));
            $curnext->state->startsinfinitequant = true;
        }
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false));
        array_push($stackofautomatons, $body);
    }

    /**
     * creates an automaton for {m,} quantifier
     */
    private function create_brace(&$stackofautomatons) {
        // create an automaton for body ($leftborder + 1) times
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder + 1; $i++) {
            $this->operands[0]->create_automaton(&$stackofautomatons);
        }
        $res = null;    // the resulting automaton
        // linking automatons to the resulting one
        for ($i = 0; $i < $leftborder + 1; $i++) {
            $cur = array_pop($stackofautomatons);
            if ($i > 0) {
                $cur->remove_subpatterns();
                // the last block is repeated
                if ($i == $leftborder) {
                    foreach ($cur->startstate->next as $curnext) {
                        $cur->endstate->append_transition(new nfa_transition($curnext->pregleaf, $curnext->state, true));
                        $curnext->state->startsinfinitequant = true;
                    }
                    $epsleaf = new preg_leaf_meta;
                    $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
                    $cur->startstate->append_transition(new nfa_transition($epsleaf, $cur->endstate, false));
                }
                // merging
                $cur->update_state_references($cur->startstate, $res->endstate);
                $res->endstate->merge($cur->startstate);
                $cur->remove_state($cur->startstate);
                $res->move_states($cur);
                $res->endstate = $cur->endstate;
            } else {
                $res = $cur;
            }
        }
        array_push($stackofautomatons, $res);
    }

    public function create_automaton(&$stackofautomatons) {
        if ($this->pregnode->leftborder == 0) {
            $this->create_aster(&$stackofautomatons);
        } else {
            $this->create_brace(&$stackofautomatons);
        }
    }

}

/**
* defines finite quantifiers {m, n}
*/
class nfa_preg_node_finite_quant extends nfa_preg_operator {

    /**
     * creates an automaton for ? quantifier
     */
    private function create_qu(&$stackofautomatons) {
        $this->operands[0]->create_automaton(&$stackofautomatons);
        $body = array_pop($stackofautomatons);
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false));
        array_push($stackofautomatons, $body);
    }

    /**
     * creates an automaton for {m, n} quantifier
     */
    private function create_brace(&$stackofautomatons) {
        // create an automaton for body ($leftborder + 1) times
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;
        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton(&$stackofautomatons, ($i == $rightborder - 1));
        }
        $res = null;        // the resulting automaton
        $endstate = null;    // the end state, required if $leftborder != $rightborder
        if ($leftborder != $rightborder) {
            $endstate = new nfa_state;
        }
        // linking automatons to the resulting one
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($stackofautomatons);
            if ($i >= $leftborder && $leftborder != $rightborder) {
                $cur->startstate->append_transition(new nfa_transition($epsleaf, $endstate, false));
            }
            if ($i > 0) {
                $cur->remove_subpatterns();
                $cur->update_state_references($cur->startstate, $res->endstate);
                $res->endstate->merge($cur->startstate);
                $cur->remove_state($cur->startstate);
                $res->move_states($cur);
                $res->endstate = $cur->endstate;
            } else {
                $res = $cur;
            }
        }
        if ($leftborder != $rightborder) {
            $res->update_state_references($endstate, $res->endstate);
        }
        array_push($stackofautomatons, $res);
    }

    public function create_automaton(&$stackofautomatons) {
        if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            $this->create_qu(&$stackofautomatons);
        } else {
            $this->create_brace(&$stackofautomatons);
        }
    }

}

/**
* defines subpatterns
*/
class nfa_preg_node_subpatt extends nfa_preg_operator {

    public function create_automaton(&$stackofautomatons) {
        $this->operands[0]->create_automaton(&$stackofautomatons);
        $body = array_pop($stackofautomatons);
        foreach ($body->startstate->next as $next) {
            $next->subpatt_start[$this->pregnode->number] = true;            
        }
        foreach ($body->states as $state) {
            foreach ($state->next as $next) {
                $next->belongs_to_subpatt[$this->pregnode->number] = true;
                if ($next->state === $body->endstate) {
                    $next->subpatt_end[$this->pregnode->number] = true;
                }
            }
        }
        array_push($stackofautomatons, $body);
    }

}

?>