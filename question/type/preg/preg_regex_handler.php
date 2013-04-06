<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines abstract class of regular expression handler, which is basically anything that works with regexes.
 * By inheriting the handler you can benefit automatic regex parsing, error handling etc.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');

/**
 * Options, generic to all handlers - mainly affects scanning and parsing.
 */
class qtype_preg_handling_options {
    const MODE_PCRE = 0;
    const MODE_POSIX = 1;

    /** @var boolean Regex compatibility mode. */
    public $mode = self::MODE_PCRE;
    /** @var boolean Strict PCRE compatible regex syntax. */
    public $pcrestrict = false;
    /** @var boolean Should lexer and parser try hard to preserve all nodes, including grouping and option nodes. */
    public $preserveallnodes = false;
    /** @var boolean Are we running in debug mode? If so, engines can print debug information during matching. */
    public $debugmode = false;
}

/**
 * Class with information about regular expression anchoring.
 */
class qtype_preg_regex_anchoring {

    //TODO - comment accurately before every field which asserts under which modifiers will lead to it!
    /** @var boolean Regex anchored from start. */
    public $start = false;
    /** @var boolean Regex anchored from start and after each line break.*/
    public $startlinebreak = false;
    /** @var boolean Regex anchored from end.*/
    public $end = false;
    /** @var boolean Regex anchored from end and before each line break.*/
    public $endlinebreak = false;

}

class qtype_preg_regex_handler {

    /** Regular expression as an object of qtype_poasquestion_string. */
    protected $regex;
    /** Modifiers for regular expression as an object of qtype_poasquestion_string. */
    protected $modifiers;
    /** Regular expression handling options, may be different for different handlers. */
    protected $options;
    /** Regex lexer. */
    protected $lexer;
    /** Regex parser. */
    protected $parser;

    /** The root of the regex abstract syntax tree, consists of qtype_preg_node childs. */
    protected $ast_root;
    /** The root of the regex definite syntax tree, consists of xxx_preg_node childs where xxx is engine name. */
    protected $dst_root;
    /** The error objects array. */
    protected $errors;
    /** Anchoring - object, with 'start' and 'end' logical fields, which are true if all regex is anchored. */
    protected $anchor;

    /**
     * Parses the regex and does all necessary preprocessing.
     * @param string regex - regular expression to handle.
     * @param string modifiers - modifiers of the regular expression.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $modifiers = null, $options = null) {
        $this->errors = array();
        $this->lexer = null;
        $this->parser = null;

        if ($regex === '') {
            $regex = null;
        }

        if ($regex === null) {
            return;
        }

        // Options should exist at least as a default object.
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }

        //Are passed modifiers supported?
        if (is_string($modifiers)) {
            $modifiers = new qtype_poasquestion_string($modifiers);
            $supportedmodifiers = $this->get_supported_modifiers();
            for ($i = 0; $i < $modifiers->length(); $i++) {
                $mod = $modifiers[$i];
                if ($supportedmodifiers->contains($mod) === false) {
                    $this->errors[] = new qtype_preg_error_unsupported_modifier($this->name(), $mod->string());
                }
            }
        } else {
            $modifiers = new qtype_poasquestion_string('');
        }

        $this->regex = new qtype_poasquestion_string($regex);
        $this->modifiers = $modifiers;
        $this->options = $options;
        //do parsing
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
            $this->accept_regex();//Sometimes engine that use accept_regex still need parsing to count subexpressions
        } else {
            $this->ast_root = null;
            //In case with no parsing we should stick to accepting whole regex, not nodes
            $this->accept_regex();
        }
    }

    /**
     * Returns class name without 'qtype_preg_' prefix.
     */
    public function name() {
        return 'regex_handler';
    }

    /**
     * Returns notation, actually used by matcher.
     */
    public function used_notation() {
        return 'native';
    }

    /**
     * Returns string of regular expression modifiers supported by this engine
     */
    public function get_supported_modifiers() {
        return new qtype_poasquestion_string('i'); // Any qtype_preg_matcher who intends to work with this question should support case insensitivity.
    }

    /**
     * Sets regex options.
     * @param options an object containing options to handle the regex.
     */
    public function set_options($options) {
        $this->options = $options;
    }

    /**
     * Was there an errors in regex?
     * @return bool  errors exists
     */
    public function errors_exist() {
        return (!empty($this->errors));
    }

    /**
     * Returns error messages for regex
     * @return array of error messages
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
     * @return array of errors
     */
    public function get_error_objects() {
        return $this->errors;
    }

    /**
     * Access function to AST root.
     * Used mainly for unit-testing and avoiding re-parsing
     */
    public function get_ast_root() {
        return $this->ast_root;
    }

    /**
     * Returns max subexpression number.
     */
    public function get_max_subexpr() {
        if ($this->lexer !== null) {
            return $this->lexer->get_max_subexpr();
        } else {
            return 0;
        }
    }

    /**
     * Returns subexpressions map.
     */
    public function get_subexpr_map() {
        if ($this->lexer !== null) {
            return $this->lexer->get_subexpr_map();
        } else {
            return array();
        }
    }

    /**
     * Returns all backreference nodes in the regex.
     */
    public function get_backrefs() {
        if ($this->lexer !== null) {
            return $this->lexer->get_backrefs();
        } else {
            return array();
        }
    }

    public function is_regex_anchored($start = true, $linebreak = true) {
        if ($start) {
            if ($linebreak) {
                return $this->anchor->start && $this->anchor->startlinebreak;
            } else {
                return $this->anchor->start;
            }
        } else {
            if ($linebreak) {
                return $this->anchor->end && $this->anchor->endlinebreak;
            } else {
                return $this->anchor->end;
            }
        }
    }

    /**
     * Fill anchor field to show if regex is anchored using ast_root
     * If all top-level alternatives starts from ^ or .* then expression is anchored from start (i.e. if matching from start failed, no other matches possible)
     * If all top-level alternatives ends on $ or .* then expression is anchored from end (i.e. if matching from start failed, no other matches possible)
     */
    public function look_for_anchors() {
        $this->anchor = new qtype_preg_regex_anchoring;
        $this->anchor->start = $this->look_for_circumflex($this->ast_root);//TODO - make $this->look_for_circumflex change $this->anchor instead of returning result.
        $this->anchor->startlinebreak = $this->anchor->start;//TODO - temporary for compatibility reasons, remove when change in the string above will be made.
    }

    /**
     * Definite syntax tree (DST) node factory creates node objects for given engine from abstract syntax tree
     * @param pregnode qtype_preg_node child class instance
     * @return corresponding xxx_preg_node child class instance
     */
    public function from_preg_node($pregnode) {
        if (is_a($pregnode,'qtype_preg_node')) {//checking that the node isn't already converted
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
     * Returns path to the temporary directory for the given component.
     * @param componentname name of the component calling this function.
     * @return absolute path to the temporary directory for the given component.
     */
    public static function get_temp_dir($componentname) {
        global $CFG;
        $dir = $CFG->dataroot . '/temp/preg/' . $componentname . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * Runs dot of graphviz on the given dot script.
     * @param dotscript a string containing the dot script.
     * @param type type of the resulting image, should be 'png' or something.
     * @param filename the absolute path to the resulting image file.
     * @return binary representation of the image if filename is null.
     */
    public static function execute_dot($dotscript, $type, $filename = null) {
        global $CFG;
        $dir = !empty($CFG->pathtodot) ? dirname($CFG->pathtodot) : null;
        $cmd = 'dot -T' . $type;
        if ($filename !== null) {
            $cmd .= ' -o' . escapeshellarg($filename);
        }
        $descriptorspec = array(0 => array('pipe', 'r'),  // Stdin is a pipe that the child will read from.
                                1 => array('pipe', 'w'),  // Stdout is a pipe that the child will write to.
                                2 => array('pipe', 'w')); // Stderr is a pipe that the child will write to.

        $process = proc_open($cmd, $descriptorspec, $pipes, $dir);
        $output = null;
        if (is_resource($process)) {
            fwrite($pipes[0], $dotscript);
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }
        return $output;
    }

    /**
     * Returns the infix for DST node names which are named like 'qtype_preg_' . $infix . '_' . $pregnodename.
     * Should be overloaded in child classes.
     */
    protected function node_infix() {
        return '';
    }

    /**
     * Returns the engine-specific node name for the given preg_node name.
     * Overload in case of sophisticated node name schemes.
     */
    protected function get_engine_node_name($pregname) {
        return 'qtype_preg_' . $this->node_infix() . '_' . $pregname;
    }

    protected function accept_regex() {
        return true; // Accept anything by default.
    }

    /**
     * is this engine need a parsing of regular expression?
     * @return bool if parsing needed
     */
    protected function is_parsing_needed() {
        return true;    // Most engines will need parsing.
    }

    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing
     * what properties of node isn't supported.
     */
    protected function is_preg_node_acceptable($pregnode) {
        // Do not show accepting errors for error nodes.
        if ($pregnode->type === qtype_preg_node::TYPE_NODE_ERROR) {
            return true;
        }
        return false;    // Should be overloaded by child classes
    }

    protected function look_for_circumflex($node, $wasconcat = false) {
        if (is_a($node, 'qtype_preg_leaf')) {
            // Expression starts from ^
            return ($node->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        } else if (isset($node->type) && $node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder == 0) {
            // Expression starts from .*
            $operand = $node->operands[0];
            return ($node->leftborder === 0 && isset($operand->type) && $operand->type == qtype_preg_node::TYPE_LEAF_CHARSET &&
                    count($operand->flags) > 0 && $operand->flags[0][0]->data === qtype_preg_charset_flag::META_DOT);
        } else if (isset($node->type) && $node->type == qtype_preg_node::TYPE_NODE_CONCAT || isset($node->type) && $node->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
            // Check the first operand for concatenation and subexpressions.
            return $this->look_for_circumflex($node->operands[0], $wasconcat || isset($node->type) && $node->type == qtype_preg_node::TYPE_NODE_CONCAT);
        } else if (isset($node->type) && $node->type == qtype_preg_node::TYPE_NODE_ALT) {
            // Every branch of alternative is anchored.
            $cf = true;
            $empty = false;
            foreach ($node->operands as $operand) {
                $empty = $empty || isset($operand->subtype) && $operand->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY;
                $cf = $cf && $this->look_for_circumflex($operand, $wasconcat);
            }
            $empty = $empty && !$wasconcat;
            return $cf || $empty;
        }
        return false;
    }

    /**
     * Does lexical and syntaxical analysis of the regex and builds an abstract syntax tree, saving root node in $this->ast_root.
     * @param string regex - regular expression for building tree.
     */
    protected function build_tree($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $this->lexer = new qtype_preg_lexer($pseudofile);
        $this->lexer->matcher = $this;        // Set matcher field, to allow creating qtype_preg_leaf nodes that require interaction with matcher
        $this->lexer->mod_top_opt($this->modifiers, new qtype_poasquestion_string(''));
        $this->lexer->handlingoptions = $this->options;

        $this->parser = new qtype_preg_yyParser($this->options);

        while (($token = $this->lexer->nextToken()) !== null) {
            if (!is_array($token)) {
                $this->parser->doParse($token->type, $token->value);
            } else {
                foreach ($token as $curtoken) {
                    $this->parser->doParse($curtoken->type, $curtoken->value);
                }
            }
        }
        $this->parser->doParse(0, 0);
        // Lexer returns errors for an unclosed character set or wrong modifiers: they don't create AST nodes.
        $lexerrors = $this->lexer->get_errors();
        foreach ($lexerrors as $node) {
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }
        // Parser contains other errors inside AST nodes.
        $parseerrors = $this->parser->get_error_nodes();
        foreach($parseerrors as $node) {
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }
        //if (count($this->errors) === 0) { //Fill trees even if there are errors, so author tools could show them.
            $this->ast_root = $this->parser->get_root();
            $this->look_for_anchors();
            $this->dst_root = clone $this->ast_root;
            $this->dst_root = $this->from_preg_node($this->dst_root);
        //}
        fclose($pseudofile);
    }
}
