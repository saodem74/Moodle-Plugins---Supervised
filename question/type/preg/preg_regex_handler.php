<?php
/**
 * Defines abstract class of regular expression handler, which is basically anything that want to work with regex
 * Beeing handler you could benefit from automatic regex parsing, error handling etc
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_regex_handler {

    //////Initial data
    //Regular expression as string
    protected $regex;
    //Modifiers for regular expression
    protected $modifiers;
    //Max number of a subpattern available in regular expression
    protected $maxsubpatt;
    //A map where keys are subpattern names and values are their numbers
    protected $subpatternmap;
    //Number of lexems (defined by user) in regular expression
    protected $lexemcount;

    //The root of abstract syntax tree of the regular expression - tree consists of preg_node childs
    protected $ast_root;
    //The root of definite syntax tree of the regular expression - tree consists of xxx_preg_node childs where xxx is engine name
    protected $dst_root;
    //The error objects array
    protected $errors;
    //Anchoring - object,  with 'start' and 'end' logical fields, which are true if all regex is anchored
    protected $anchor;

    public function name() {
        return 'preg_regex_handler';
    }

    /**
    * Parse regex and do all necessary preprocessing
    @param regex - regular expression for which will be build finite automate
    @param modifiers - modifiers of regular expression
    */
    public function __construct($regex = null, $modifiers = null) {
        $this->errors = array();
        $this->maxsubpatt = 0;
        $this->subpatternmap = array();
        $this->lexemcount = 0;
        if ($regex === null) {
            return;
        }

        //Are passed modifiers supported?
        if (is_string($modifiers)) {
            $supportedmodifiers = $this->get_supported_modifiers();
            for ($i = 0; $i < qtype_preg_strlen($modifiers); $i++) {
                $mod = qtype_preg_substr($modifiers, $i, 1);
                if (qtype_preg_strpos($supportedmodifiers, $mod) === false) {
                    $this->errors[] = new qtype_preg_error_unsupported_modifier($this->name(), $mod);
                }
            }
        }

        $this->regex = $regex;
        $this->modifiers = $modifiers;
        //do parsing
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
        } else {
            $this->ast_root = null;
            //In case with no parsing we should stick to accepting whole regex, not nodes
            $this->accept_regex();
        }
    }

    protected function accept_regex() {
        //Accept anything by default
        return true;
    }

    /**
    * returns notation, actually used by matcher
    */
    public function used_notation() {
        return 'native';//TODO - php_preg_matcher should really used PCRE strict notation when conversion will be supported
    }

    /**
    * returns subpatterns map
    */
    public function get_subpattern_map() {
        return $this->subpatternmap;
    }

    /**
    * returns string of regular expression modifiers supported by this engine
    */
    public function get_supported_modifiers() {
        return 'i';//any qtype_preg_matcher who intends to work with this question should support case insensitivity
    }

    /**
    * is this engine need a parsing of regular expression?
    @return bool if parsing needed
    */
    protected function is_parsing_needed() {
        //most engines will need parsing
        return true;
    }

    /**
    * Was there an errors in regex?
    @return bool  errors exists
    */
    public function is_error_exists() {
        return (!empty($this->errors));
    }

    /**
    * Returns error messages for regex
    @return array of error messages
    */
    public function get_errors() {
        $res = array();
        foreach($this->errors as $error) {
            $res[] = $error->errormsg;
        }
        return $res;
    }

    /**
    * Returns errors as objects
    @return array of errors
    */
    public function get_error_objects() {
        return $this->errors;
    }

    /**
    * Is a preg_node_... or a preg_leaf_... supported by the engine?
    * Returns true if node is supported or user interface string describing
    *   what properties of node isn't supported.
    */
    protected function is_preg_node_acceptable($pregnode) {
        return false;    // Should be overloaded by child classes
    }


    /**
    * Function does lexical and syntaxical analysis of regex and builds abstract syntax tree, saving root node in $this->ast_root
    @param $regex - regular expression for building tree
    */
    protected function build_tree($regex) {

        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->matcher =& $this;//Set matcher field, to allow creating preg_leaf nodes that require interaction with matcher
        /*old-style modifier support
        $lexer->globalmodifiers = $this->modifiers;
        $lexer->localmodifiers = $this->modifiers;*/
        $lexer->mod_top_opt($this->modifiers, '');
        $parser = new preg_parser_yyParser;
        while ($token = $lexer->nextToken()) {
            if (!is_array($token)) {
                $parser->doParse($token->type, $token->value);
            } else {
                foreach ($token as $curtoken) {
                    $parser->doParse($curtoken->type, $curtoken->value);
                }
            }
        }

        $this->maxsubpatt = $lexer->get_max_subpattern();
        $this->subpatternmap = $lexer->get_subpattern_map();
        $this->lexemcount = $lexer->get_lexem_count();
        $lexerrors = $lexer->get_errors();
        foreach ($lexerrors as $lexerror) {
            $parser->doParse(preg_parser_yyParser::LEXERROR, $lexerror);
        }
        $parser->doParse(0, 0);
        if ($parser->get_error()) {
            $errornodes = $parser->get_error_nodes();
            $parseerrors = array();
            //Generate parser error messages
            foreach($errornodes as $node) {
                $parseerrors[] = new qtype_preg_parsing_error($regex, $node);
            }
            $this->errors = array_merge($this->errors, $parseerrors);
        } else {
            $this->ast_root = $parser->get_root();
            $this->dst_root = $this->from_preg_node($this->ast_root);
            $this->look_for_anchors();
        }
        fclose($pseudofile);
    }

    /**
     * Copy Abstract Syntax Tree from another qtype_preg_regex_handler class and build DST on it
     *
     * Create handler with no parameters, than call this function to avoid re-parsing if you have
     *   two handlers working on one regex.
     */
    public function get_tree_from_another_handler($handler) {
        $this->errors = $handler->get_error_objects();
        if (!$this->is_error_exists()) {
            $srcroot = $handler->get_ast_root();
            $this->ast_root = clone $srcroot;
            $this->dst_root = $this->from_preg_node($this->ast_root);
            $this->look_for_anchors();
        }
    }

    /*
    * Access function to AST root.
    * Used mainly for unit-testing and avoiding re-parsing
    */
    public function get_ast_root() {
        return $this->ast_root;
    }

    /**
    * Definite syntax tree (DST) node factory creates node objects for given engine from abstract syntax tree
    * @param pregnode preg_node child class instance
    * @return corresponding xxx_preg_node child class instance
    */
    public function &from_preg_node($pregnode) {
        if (is_a($pregnode,'preg_node')) {//checking that the node isn't already converted
            $enginenodename = $this->get_engine_node_name($pregnode->name());
            if (class_exists($enginenodename)) {
                $enginenode = new $enginenodename($pregnode, $this);
                $acceptresult = $enginenode->accept();
                if ($acceptresult !== true && !array_key_exists($enginenodename,  $this->errors)) {//highlighting first occurence of unaccepted node
                    $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast));
                }
            } else {
                $enginenode = $pregnode;
                $acceptresult = $this->is_preg_node_acceptable($pregnode);
                if ($acceptresult !== true && !array_key_exists($enginenodename,  $this->errors)) {//highlighting first occurence of unaccepted node
                    $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast));
                }
            }
            return $enginenode;
        } else {
            return $pregnode;
        }
    }

    /**
    * Returns engine node name having preg node name
    * Overload in case of sophisticated node name schemes
    */
    protected function get_engine_node_name($pregname) {
        return $this->node_prefix().'_preg_'.$pregname;
    }

    /**
    * Returns prefix for engine specific node classes
    */
    protected function node_prefix() {
        return null;
    }

    /**
    * Returns path to the temporary directory for given component
    */
    public function get_temp_dir($componentname) {
        global $CFG;
        $dir = $CFG->dataroot.'/temp/preg/'.$componentname.'/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
    * Checks if dot.exe of graphviz exists
    */
    public function is_dot_installed() {
        global $CFG;
        if (!isset($CFG->qtype_preg_graphvizpath)) {
            return false;
        }
        $dotexefilename = $CFG->qtype_preg_graphvizpath.'/dot.exe';
        if (!file_exists($dotexefilename)) {
            return false;
        }
        return true;
    }

    /**
    * Runs dot.exe of graphviz on the given .dot file
    */
    public function execute_dot($dotfilename, $jpegfilename = null) {
        global $CFG;
        if (!$this->is_dot_installed()) {
            return;
        }
        $jpgpath = pathinfo($dotfilename, PATHINFO_DIRNAME);
        if ($jpegfilename === null) {
            $filename = pathinfo($dotfilename, PATHINFO_FILENAME);
            $jpgfn = $jpgpath.'/'.$filename.'.jpg';
        } else {
            $jpgfn = $jpgpath.'/'.$jpegfilename;
        }
        chdir($CFG->qtype_preg_graphvizpath);
        exec("dot.exe -Tjpg -o\"$jpgfn\" -Kdot $dotfilename");
    }

}
?>