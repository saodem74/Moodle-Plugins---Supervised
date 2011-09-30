<?php

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * defines a transition between two states
 */
class nfa_transition
{
    public $loops = false;       // true if this transition makes a loop: for example, (...)* contains an epsilon-transition that makes a loop

    public $pregleaf;            // transition data, a reference to an object of preg_leaf

    public $state;               // the state which this transition leads to, a reference to an object of nfa_state
    
    public $replaceable;         // eps-transitions are replaced by next non-eps transitions for merging simple assertions

    public $subpatt_start = array();        // an array of subpatterns which start in this transition

    public $subpatt_end = array();          // an array of subpatterns which end in this transition

    public $belongs_to_subpatt = array();   // an array of subpatterns which this transition belongs to

    public function __construct(&$_pregleaf, &$_state, $_loops, $_replaceable = false) {
        $this->pregleaf = $_pregleaf;
        $this->state = $_state;
        $this->loops = $_loops;
        $this->replaceable = $_replaceable;
    }

}

/**
 * defines an nfa state
 */
class nfa_state
{

    public $startsinfinitequant = false;    // true if this state starts an infinite quantifier either * or + or {m,}

    public $next = array();                 // an array of objects of nfa_transition

    public $id;                             // id of the state, debug variable

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
 * defines a nondeterministic finite automaton
 */
class nfa {

    public $startstate;          // a reference to the start nfa_state of the automaton

    public $endstate;            // a reference to the end nfa_state of the automaton

    public $states = array();    // an array containing references to states of the automaton

    var $graphvizpath = 'C:\Program Files (x86)\Graphviz2.26.3\bin';    // path to dot.exe of graphviz

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
    
    public function replace_eps_transitions() {
        // TODO
    }
    
    public function merge_simple_assertions() {
        // TODO
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
        // add a new end state if the current end state is looped    (for both of automata)
        $automata = array($first, $second);
        foreach ($automata as $cur) {
            $endlooped = false;
            foreach ($cur->endstate->next as $curnext) {
                if ($curnext->loops) {
                    $endlooped = true;
                }
            }
            if ($endlooped) {
                $endstate = new nfa_state;
                $cur->append_state($endstate);
                $cur->endstate->append_transition(new nfa_transition($epsleaf, $endstate, false, true));
                $cur->endstate = $endstate;
            }
        }
        // start and end states are merged
        $first->startstate->merge($second->startstate);
        $second->update_state_references($second->startstate, $first->startstate);
        $second->update_state_references($second->endstate, $first->endstate);
        $second->remove_state($second->startstate);
        $second->remove_state($second->endstate);
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
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false, true));
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
                    $cur->startstate->append_transition(new nfa_transition($epsleaf, $cur->endstate, false, true));
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
        $body->startstate->append_transition(new nfa_transition($epsleaf, $body->endstate, false, true));
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
        $endstate = null;   // the end state, required if $leftborder != $rightborder
        if ($leftborder != $rightborder) {
            $endstate = new nfa_state;
        }
        // linking automatons to the resulting one
        $epsleaf = new preg_leaf_meta;
        $epsleaf->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
        for ($i = 0; $i < $rightborder; $i++) {
            $cur = array_pop($stackofautomatons);
            if ($i >= $leftborder && $leftborder != $rightborder) {
                $cur->startstate->append_transition(new nfa_transition($epsleaf, $endstate, false, true));
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