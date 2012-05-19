<?php
/**
 * Defines generic token and node classes.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Sergey Pashaev, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class block_formal_langs_ast {

    /**
     * AST root node.
     * @var object of 
     */
    private $root;

    /**
     * Basic lexer constructor.
     *
     * @param array $patterns - array of object of a pattern class
     * @param array $conditions - array of strings/conditions
     * @param string $text - input text
     * @param array $options - hash table of options
     * @param string $condition - initial condition
     */
    public function __construct() {
        
    }

    private function print_node($node, $args = NULL) {//TODO - normal printing, maybe using dot
        printf('Node number: %d\n', $node->number());
        printf('Node type: %s\n', $node->type());
        printf('Node position: [%d, %d, %d, %d]\n',
               $node->position()->linestart(),
               $node->position()->colstart(),
               $node->position()->lineend(),
               $node->position()->colend());
        printf('Node description: %s\n', $node->description());
    }

    public function print_tree() {
        traverse($root, 'print_node');
    }
    
    public function traverse($node, $callback) {
        // entering node
        if ($node->is_leaf()) {
            // leaf action
            $callback($node, $args);//TODO - what is args?
        }

        foreach($node->childs as $child) {//TODO - why no callback for non-leaf nodes?
            traverse($child, $callback);
        }
    }

    /**
     * Returns list of node objects which requires description.
     *
     * @param $answer - moodle answer object
     * @return array of node objects
     */
    public function nodes_requiring_description_list() {
        // TODO: return node objects
        // TODO - get only nodes requiring user-defined description from the trees
    }
}

/**
 * Describes a position of AST node (terminal or non-terminal) in the original text
 */
class block_formal_langs_node_position {
    protected $linestart;
    protected $lineend;
    protected $colstart;
    protected $colend;

    public function linestart(){
        return $this->linestart;
    }

    public function lineend(){
        return $this->lineend;
    }
    
    public function colstart(){
        return $this->colstart;
    }
    
    public function colend(){
        return $this->colend;
    }
    
    public function __construct($linestart, $lineend, $colstart, $colend) {
        $this->linestart = $linestart;
        $this->lineend = $lineend;
        $this->colstart = $colstart;
        $this->colend = $colend;
    }

    /**
     * Summ positions of array of nodes into one position
     *
     * Resulting position is defined from minimum to maximum postion of nodes
     *
     * @param array $nodepositions positions of adjanced nodes
     */
    public function summ($nodepositions) {
        $minlinestart = $nodepositions[0]->linestart;
        $maxlineend = $nodepositions[0]->lineend;
        $mincolstart = $nodepositions[0]->colstart;
        $maxcolend = $nodepositions[0]->colend;

        foreach ($nodepositions as $node) {
            if ($node->linestart < $minlinestart)
                $minlinestart = $node->linestart;
            if ($node->colstart < $mincolstart)
                $mincolstart = $node->colstart;
            if ($node->lineend > $maxlineend)
                $maxlineend = $node->lineend;
            if ($node->colend > $maxcolend)
                $maxcolend = $node->colend;
        }

        return new block_formal_langs_node_position($minlinestart, $maxlinened, $mincolstart, $maxcolend);
    }
}

class block_formal_langs_ast_node_base {

    /**
     * Type of node.
     * @var string
     */
    protected $type;

    /**
     * Node position - c.g. block_formal_langs_node_position object
     */
    protected $position;

    /**
     * Node number in a tree.
     * @var integer
     */
    protected $number;

    /**
     * Child nodes.
     * @var array of ast_node_base
     */
    public $childs;

    /**
     * True if this node needs user-defined description
     * @var bool
     */
    protected $needuserdescription;

    /**
     * Node description.
     * @var string
     */
    protected $description;

    public function __construct($type, $position, $number, $needuserdescription) {
        $this->number = $number;
        $this->type = $type;
        $this->position = $position;
        $this->needuserdescription = $needuserdescription;

        $this->childs = array();
        $this->description = '';
    }

    /**
     * Returns actual type of the token.
     *
     * Usually will be overloaded in child classes to return constant string.
     */
    public function type() {
        return $this->type;
    }

    public function number() {
        return $this->number;
    }

    public function position() {
        return $this->position;
    }

    public function need_user_description() {
        return $this->needuserdescription;
    }

    public function description() {
        if (!$this->needuserdescription) {
            // TODO: calc description
            return $this->description;
        } else {
            return $this->description;
        }
    }

    public function set_description($str) {
        $this->description = $str;
    }

    public function childs() {
        return $this->childs;
    }
    
    public function set_childs($childs) {
        $this->childs = $childs;
    }

    public function add_child($node) {
        array_push($this->childs, $node);
    }

    public function is_leaf() {
        if (0 == count($this->childs)) {
            return true;
        }
        return false;
    }
}

/**
 * Class for base tokens.
 *
 * Class for storing tokens. Class - token, object of the token class
 * - lexeme.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_token_base extends block_formal_langs_ast_node_base {

    /**
     * Semantic value of node.
     * @var string
     */
    protected $value;

    /**
     * Index of token in the stream it belongs to.
     *
     * For tokens it's often important to know index in the stream array, not just position in the text
     * @var integer
     */
    protected $tokenindex;

    public function value() {
        return $this->value;
    }

    public function token_index() {
        return $this->tokenindex;
    }

    public function set_token_index($newindex) {
        $this->tokenindex = $newindex;
    }

    /**
     * Basic lexeme constructor.
     *
     * @param string $type - type of lexeme
     * @param string $value - semantic value of lexeme
     * @return base_token
     */
    public function __construct($number, $type, $value, $position, $index) {
        $this->if = $number;
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
        $this->tokenindex = $index;
    }

    /**
     * This function returns true if editing distance is
     * applicable to this type of tokens as lexical error weight and
     * threshold.
     *
     * There are kind of tokens for which editing distances are 
     * inapplicable, like numbers.
     *
     * @return boolean
     */
    public function use_editing_distance() {
        return true;
    }

    /**
     * Calculates and return editing distance from
     * $this to $token
     */
    public function editing_distance($token) {
        if ($this->use_editing_distance()) {//Damerau-Levenshtein distance is default now
            $distance = block_formal_langs_token_base::damerau_levenshtein($this->value(), $token->value());
        } else {//Distance not applicable, so return a big number 
            $distance = strlen($this->value()) + strlen($token->value());
        }
    }

    /* Calculates Damerau-Levenshtein distance between two strings.  
     *
     * @return int Damerau-Levenshtein distance
     */
    static public function damerau_levenshtein($str1, $str2) {
    }

    /**
     * Base lexical mistakes handler. Looks for possible matches for this
     * token in other answer and return an array of them.
     *
     * The functions works differently depending on token of which answer it's called.
     * For correct text (e.g. _answer_) $iscorrect == true and it looks for typos, extra separators,
     * typical mistakes (in particular subclasses) etc - i.e. all mistakes with one token from correct text.
     * For compared text (e.g. student's _response_) it looks for missing separators, extra quotes etc,
     * i.e. mistakes which have more than one token from correct, but only one from compared text.
     *
     * @param array $other - array of tokens  (other text)
     * @param integer $threshold - lexical mistakes threshold
     * @param boolean $iscorrect - true if type of token is correct and we should perform full search, false for compared text
     * @return array - array of block_formal_langs_matched_tokens_pair objects with blank
     * $answertokens or $responsetokens field inside (it is filling from outside)
     */
    public function look_for_matches($other, $threshold, $iscorrect) {
        // TODO: generic mistakes handling
    }
    
    
    /**
     * Tests, whether other lexeme is the same as this lexeme
     *  
     * @param block_formal_langs_token_base $other other lexeme
     * @return boolean - if the same lexeme
     */
    public function is_same($other) {
        $result = false;
        if ($this->type == $other->type) {
            $result = $this->value == $other->value;
        }
        return $result;
    }
}

/**
 * Class for matched pairs (correct answer and student response).
 *
 * Instances of this class map groups of tokens from correct answer
 * to groups of token in student response.
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_matched_tokens_pair {

    /**
     * Indexes of the correct text tokens.
     * @var array
     */
    public $correcttokens;

    /**
     * Indexes of the compared text tokens.
     * @var array
     */
    public $comparedtokens;

    /**
     * Mistake weight (Damerau-Levenshtein distance, for example).
     *
     * Zero is no mistake.
     *
     * @var integer
     */
    public $mistakeweight;
}

/**
 * Represents a stream of tokens
 *
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License 
 */
class block_formal_langs_token_stream {
    /**
     * Tokens's array
     *
     * @var array of block_formal_langs_token_base childs
     */
    public $tokens;

    /**
     * Lexical errors
     *
     * @var array of block_formal_langs_lexical_errors object
     */
    public $errors;

    public function __clone() {
        $this->tokens = clone $this->tokens;
        $this->errors = clone $this->errors;
    }

    /**
     * Set token indexes traversing array of tokens from left to right
     *
     * Use to restore indexes after inserting or removing tokens (c.e. correct_mistakes)
     */
    public function set_token_indexes() {
        //TODO Birukova
    }

    /**
     * Compares compared stream of tokens with this (correct) stream looking for
     * matches with possible errors in tokens (but not in their placement)
     *
     * @param comparedstream object of block_formal_langs_token_stream to compare with this, may contain errors
     * @param threshold editing distance threshold (in percents to token length)
     * @return array of block_formal_langs_matched_tokens_pair objects
     */
    public function look_for_token_pairs($comparedstream, $threshold) {
        //TODO Birukova
        //1. Find matched pairs (typos, typical errors etc) - Birukova
        //  - look_for_matches function
        //2. Find best groups of pairs - Birukova
        //  - group_matches function, with criteria defined by compare_matches_groups function
    }

    /**
     * Creates an array of all possible matched pairs using this stream as correct one.
     *
     * Uses token's look_for_matches function and fill necessary fields in matched_tokens_pair objects.
     *
     * @param comparedstream object of block_formal_langs_token_stream to compare with this, may contain errors
     * @param $threshold threshold as a fraction of token length for creating pairs
     * @return array array of matched_tokens_pair objects representing all possible pairs within threshold
     */
    public function look_for_matches($comparedstream, $threshold) {
        //TODO Birukova
    }

    /**
     * Generates array of best groups of matches representing possible set of mistakes in tokens.
     *
     * Use recursive backtracking.
     * No token from correct or compared stream could appear twice in any group, groups are
     * compared using compare_matches_groups function
     *
     * @param array $matches array of matched_tokens_pair objects representing all possible pairs within threshold
     * @return array of  block_formal_langs_matches_group objects
     */
    public function group_matches($matches) {
        //TODO Birukova
    }

    /**
     * Compares two matches groups.
     *
     * Basic strategy is to have as much tokens in both correct and compared streams covered as possible.
     * If the number of tokens covered are equal, than choose group with less summ of mistake weights.
     *
     * @return number <0 if $group1 worse than $group2; 0 if $group1==$group2; >0 if $group1 better than $group2
     */
    public function compare_matches_groups($group1, $group2) {
        //TODO Birukova
    }

    /**
     * Create a copy of this stream and correct mistakes in tokens using given array of matched pairs
     *
     * @param correctstream object of block_formal_langs_token_stream for correct stream
     * @param matchedpairsgroup array of block_formal_langs_matched_tokens_pair
     * @return a new token stream where comparedtokens changed to correcttokens if mistakeweight > 0 for the pair
     */
    public function correct_mistakes($correctstream, $matchedpairsgroup) {
        $newstream = clone $this;
        //TODO Birukova - change tokens using pairs
    }
}

/**
 * Represents possible set of correspondes between tokens of correct and compared streams
 */
class  block_formal_langs_matches_group {
    /**
     * Array of matched pairs
     */
    public $matchedpairs;

    //Sum of mistake weights
    public $mistakeweight;

    //Sorted array of all correct token indexes for tokens, covered by pairs from this group
    public $correctcoverage;

    //Sorted array of all compared token indexes for tokens, covered by pairs from this group
    public $comparedcoverage;
}

/**
 * Represents a lexical error in the token
 *
 * A lexical error is a rare case where single lexem violates the rules of the language
 * and can not be interpreted.
 */
class  block_formal_langs_lexical_error {

    public $tokenindex;

    /** @var User interface string (i.e. received using get_string) describing error to the user*/
    public $errormessage;

    /** @var Corrected token object if possible, null otherwise */
   public $correctedtoken; 
}

/**
 * Represents a processed string
 *
 * Contains a string, a token stream (if the string is tokenized) and a syntax tree (or array of trees) if parsed
 * This class is needed to encapsulate a processed string and centralize a code for it's handling while having
 *   language, lexer and parser objects stateless.
 */
class block_formal_langs_processed_string {
   
    /**
     * @var string table, where string belongs
     */
    public $tablename;
    /**
     *@var integer an id to load/store user descriptions
     */
    public $tableid;
    
    /**
     *@var string a string to process
     */
    protected $string='';

    /**
     *@var object a link to the language object
     */
    public $language;

    /**
     *@var object a token stream if the string is tokenized
     */
    protected $tokenstream=null;

    /**
     *@var object a syntax tree if the string is parsed
     */
    protected $syntaxtree=null;

    /**
     * @var array strings of token descriptions
     */
    protected $descriptions=null;
    
    
    public function set_string($string)  {
        $this->string=$string;
        $this->tokenstream=null;
        $this->syntaxtree=null;
        $this->descriptions=null;
    }
    
    public function set_stream($stream) {
        //TODO: What we do with a stream and string
        $this->tokenstream = $stream;
    }
    
    public function set_syntax_tree($tree) {
    
    }
    
    public function set_descriptions($descriptions)  {
        //TODO: Connect here to DB and insert/update descriptions
    }
    
    public function set_table_params($tablename, $tableid) {
        $this->tablename=$tablename;
        $this->tableid=$tableid;
    }
    
    /**
     * Returns count of nodes which needs description or special name.
     *
     * @return integer
     */
    public function nodes_requiring_description_count() {//TODO - name
        if ($this->language->could_parse()) {
            return count($this->syntaxtree->nodes_requiring_description_list());
        } else {
            return count($this->tokenstream->tokens);
        }
    }

    /**
     * Returns list of node objects which requires description.
     *
     * @param $answer - moodle answer object
     * @return array of node objects
     */
    public function nodes_requiring_description_list() {
        // TODO: return node objects
        if ($this->language->could_parse()) {
            return $this->syntaxtree->nodes_requiring_description_list();
        } else {
            return $this->tokenstream->tokens;
        }
    }

    /**
     * Returns description string for passed node.
     *
     * @param $nodenumber number of node
     * @return string - description of node
     */
    public function node_description($nodenumber) {
        // TODO: change SELECT to adapt to new circumstances
        // connect moodle DB
        // SELECT description FROM moodle_descriptions_table
        // AS mtul WHERE mtut.langid == $this->id AND mtut.number
        // == $node->id AND mtut.answerid == $this->answerid;
        //cache descriptions
        //Parser, if enabled, could generate descriptions for the nodes not stored in DB
        $desc = '';
        return $desc;
    }

    /**
     * Returns list of node descriptions.
     *
     * @return array of strings, keys are node numbers
     */
    public function node_descriptions_list() {
        //TODO: Connect here to DB and list descriptions
        // connect moodle DB by answerid
        //cache descriptions
        //Parser, if enabled, could generate descriptions for the nodes not stored in DB
        //TODO - should the function return only nodes with user-defined description or descpriptions for all nodes? Probably first...
    }
}
?>