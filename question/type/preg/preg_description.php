<?php
/**
 * Defines handler for generating description of reg exp
 * Also defines specific tree, containing methods for generating descriptions of current node
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Handler, generating information for regular expression
 */
class qtype_preg_author_tool_description extends qtype_regex_handler{
    
    /*
     * Construct of parent class parses the regex and does all necessary preprocessing.
     *
     * @param string regex - regular expression to handle.
     * @param string modifiers - modifiers of the regular expression.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $modifiers = null, $options = null){
        parent::__construct($regex, $modifiers, $options);
    }
    
    /**
     * Genegates description of regexp
     * Example of calling:
     * 
     * description('<span class="description_node_%n%o">%s</span>',' operand','<span class="description">%s</span>');
     * 
     * Operator with id=777 will be plased into: <span class="description_node_777">abc</span>.
     * User defined parts of regex with id=777 will be placed id: <span class="description_node_777  operand">%1 or %2</span>.
     * Whole string will be placed into <span class="description">string</span>
     * 
     * @param string $whole_pattern Pattern for whole decription. Must contain %s - description.
     * @param string $numbering_pattern Pattern to track numbering. 
     * Must contain: %s - description of node;
     * May contain:  %n - id node; %o - substring to highlight operands, determined by $operand_pattern.
     * @param string $operand_pattern Will be substituted in place %o in $numbering_pattern
     * @return string description.
     */
    public function description($numbering_pattern,$operand_pattern,$whole_pattern=null){
        return '123';
    }
    
    /**
     * Calling default description($numbering_pattern,$operand_pattern,$whole_pattern=null with default params
     */
    public function default_description(){
        custum_description('<span class="description_node_%n%o">%s</span>',' operand','<span class="description">%s</span>');
    }
    
    /**
     * Returns the engine-specific node name for the given preg_node name.
     * Overload in case of sophisticated node name schemes.
     */
    protected function get_engine_node_name($pregname) {
        return 'qtype_preg_description_'.$pregname;
    }
    
    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing
     *   what properties of node isn't supported.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return false;    // Should be overloaded by child classes
    }
}


/**
 * Generic node class.
 */
abstract class qtype_preg_description_node{
    /** @var qtype_preg_node Aggregates a pointer to the automatically generated abstract node */
    public $pattern;
    
    /** @var string pattern for description of current node */
    public $pregnode;
    
    /**
     * Constructs node.
     * 
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.                                    
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct(&$node, &$matcher) {
        $this->pregnode = $node;
    }
    
    /**
     * Chooses pattern for current node.
     * 
     * @param qtype_preg_description_node $node_parent Reference to the parent.
     * @param string $form Required form.
     * @return string Chosen pattern.
     */
    abstract public function pattern($node_parent=null,$form=null);
    
    /**
     * Recursively generates description of tree (subtree).
     * 
     * @param string $numbering_pattern Pattern to track numbering. 
     * Must contain: %s - description of node;
     * May contain:  %n - id node; %o - substring to highlight operands, determined by $operand_pattern.
     * @param string $operand_pattern Will be substituted in place %o in $numbering_pattern
     * @param qtype_preg_description_node $node_parent Reference to the parent.
     * @param string $form Required form.
     * @return string
     */
    abstract public function description($numbering_pattern,$operand_pattern,$node_parent=null,$form=null);
    
    /**
     * if $s nor defined in lang file throw exeption
     * 
     * @param string $s same as in get_string
     */
    public function get_string_s($s){
        $return = get_string($s);
        if($return == null){
            throw new coding_exception($s.' is missing in current lang file of preg description', 'ask localizator of preg description module');
        }
        return $return;
    }
}

/**
 * Generic leaf class.
 */
abstract class qtype_preg_description_leaf extends qtype_preg_description_node{
    
}

/**
 * Represents a character or a charcter set.
 */
class qtype_preg_description_leaf_charset extends qtype_preg_description_leaf{
    
    /**
     * Generates description of current flag
     * 
     * @param qtype_preg_charset_flag $flag flag gor description
     */
    private function flag_pattern($flag){
        $pattern_name = 'description_charflag_'.$flag->type;
        $pattern = get_string_s($pattern_name);
        if($flag->negative == true){
            $pattern = str_replace('%char',$pattern,get_string_s('description_charset_char_neg'));
        }
        return $pattern;
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $characters = array();//array of strings
        $result_pattern = '';
        foreach ($pregnode->flags as $i => $outer) {
            foreach ($outer as $j => $flag){
                array_push($symbols,$this->flag_pattern($flag));
            }
        }
        if(count($symbols)==1){
            $result_pattern = get_string_s('description_charset_one');
            $result_pattern = str_replace('%character', $symbols[0], $result_pattern);
        }
        else{
            $count = count($characters);
            $characters_string = '';
            foreach ($characters as $i => $char) {
                $characters_string .= $char.(($i==$count) ? '' : ', ');
            }
            if($pregnode->negative == false){
                $result_pattern = get_string_s('description_charset');
            }
            else{
                $result_pattern = get_string_s('description_charset_negative');
            }
            $result_pattern = str_replace('%characters', $characters_string, $result_pattern);
        }
        return $result_pattern;
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$operand_pattern,$node_parent=null,$form=null){
        return
    }
}


/**
 * Defines meta-characters that can't be enumerated.
 */
class qtype_preg_description_leaf_meta extends qtype_preg_description_leaf{
}

/**
 * Defines simple assertions.
 */
class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf{
}

/**
 * Defines backreferences.
 */
class qtype_preg_description_leaf_backref extends qtype_preg_description_leaf{
}

class qtype_preg_description_leaf_option extends qtype_preg_description_leaf{
}

class qtype_preg_description_leaf_recursion extends qtype_preg_description_leaf{
}

/**
 * Reperesents backtracking control, newline convention etc sequences like (*...).
 */
class qtype_preg_description_leaf_control extends qtype_preg_description_leaf{
}

/**
 * Defines operator nodes.
 */
abstract class qtype_preg_description_operator extends qtype_preg_description_node{
    /** @var qtype_preg_author_tool_description[] Array of operands */
    public $operands = array();

    /**
     * Construct array of operands, using method qtype_regex_handler::from_preg_node()
     * 
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.                                      
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct(&$node, &$matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }
}

/**
 * Defines finite quantifiers with left and right borders, unary operator.
 * Possible errors: left border is greater than right one.
 */
class qtype_preg_description_node_finite_quant extends qtype_preg_description_operator{
}

/**
 * Defines infinite quantifiers node with the left border only, unary operator.
 */
class qtype_preg_description_node_infinite_quant extends qtype_preg_description_operator{
}

/**
 * Defines concatenation, binary operator.
 */
class qtype_preg_description_node_concat extends qtype_preg_description_operator{
}

/**
 * Defines alternative, binary operator.
 */
class qtype_preg_description_node_alt extends qtype_preg_description_operator{
}

/**
 * Defines lookaround assertions, unary operator.
 */
class qtype_preg_description_node_assert extends qtype_preg_description_operator{
}

/**
 * Defines subpatterns, unary operator.
 */
class qtype_preg_description_node_subpatt extends qtype_preg_description_operator{
}

/**
 * Defines conditional subpatterns, unary, binary or ternary operator.
 * The first operand yes-pattern, second - no-pattern, third - the lookaround assertion (if any).
 * Possible errors: there is no backreference with such number in expression
 */
class qtype_preg_description_node_cond_subpatt extends qtype_preg_description_operator{
}
