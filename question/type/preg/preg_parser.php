<?php
/* Driver template for the LEMON parser generator.
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class qtype_preg_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof qtype_preg_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof qtype_preg_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->_string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof qtype_preg_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof qtype_preg_yyToken) {
            $this->string .= $value->string;
            $this->metadata[$offset] = $value->metadata;
        } else {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

// code external to the class is included here
#line 2 "../preg_parser.y"

    global $CFG;
    require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
#line 83 "../preg_parser.php"

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class qtype_preg_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// any extra class_declaration (extends/implements) are defined here
/**
 * The state of the parser is completely contained in an instance of
 * the following structure
 */
class qtype_preg_yyParser
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 8 "../preg_parser.y"

    // Root of the Abstract Syntax Tree (AST).
    private $root;
    // Objects of qtype_preg_node_error for errors during the parsing.
    private $errornodes;
    // Count of reduces made.
    private $reducecount;
    // Node id counter.
    private $idcounter;
    // Handling options
    public $handlingoptions;

    function __construct() {
        $this->errornodes = array();
        $this->reducecount = 0;
        $this->idcounter = 0;
        $this->handlingoptions = new qtype_preg_handling_options();
    }

    function get_root() {
        return $this->root;
    }

    function get_error() {
        return (count($this->errornodes) > 0);
    }

    public function get_error_nodes() {
        return $this->errornodes;
    }

    /**
     * Creates and returns an error node, also adds it to the array of parser errors
     * @param subtype type of error
     * @param indfirst the starting index of the highlited area
     * @param indlast the ending index of the highlited area
     * @param addinfo additional info, supplied for this error
     * @return qtype_preg_node_error object
     */
    protected function create_error_node($subtype, $indfirst = -1, $indlast = -1, $addinfo = null, $userinscription, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($indfirst, $indlast, $userinscription);
        $newnode->operands = $operands;
        $newnode->id = $this->idcounter++;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
     * Creates error node(s) if there is an error in the given node.
     * @param node the node to be checked.
     */
    protected function create_error_node_from_lexer($node) {
        if (isset($node->type) && $node->type === qtype_preg_node::TYPE_NODE_ERROR) {
            $this->create_error_node($node->subtype, $node->indfirst, $node->indlast, $node->addinfo, $node->userinscription);
        }
        if (!isset($node->error)) {
            return;
        }
        if (is_array($node->error)) {
            foreach ($node->error as $error) {
                $this->create_error_node($error->subtype, $error->indfirst, $error->indlast, $error->addinfo, $error->userinscription);
            }
        } else if ($node->error !== null) {
            $this->create_error_node($node->error->subtype, $node->error->indfirst, $node->error->indlast, $node->error->addinfo, $node->error->userinscription);
        }
    }

    /**
      * Creates and return correct parenthesis node (subpattern, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param parens parenthesis token from lexer
      * @param exprnode the node for expression inside parenthesis
      */
    protected function create_parens_node($parens, $exprnode) {
        $result = null;
        if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING && !$this->handlingoptions->preserveallnodes) {
            $result = $exprnode;
        } else {
            if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING) {
                $result = new qtype_preg_node_subpatt(-1, $this->get_nested_subpatts($exprnode));
            } else if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT || $parens->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY) {
                $result = new qtype_preg_node_subpatt($parens->number, $this->get_nested_subpatts($exprnode));
            } else {
                $result = new qtype_preg_node_assert();
            }
            $result->subtype = $parens->subtype;
            $result->operands[0] = $exprnode;
            $result->id = $this->idcounter++;
            $result->userinscription = new qtype_preg_userinscription($parens->userinscription->data . '...)');
        }
        $result->set_user_info($parens->indfirst, $exprnode->indlast + 1, $result->userinscription);
        return $result;
    }

    protected function create_cond_subpatt_assertion_node($paren, $assertnode, $exprnode) {
        if ($assertnode === null) {
            $assertnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $assertnode->set_user_info($paren->indlast, $paren->indlast, new qtype_preg_userinscription());
            $assertnode->id = $this->idcounter++;
        }
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($assertnode->indlast + 1, $assertnode->indlast + 1, new qtype_preg_userinscription());
            $exprnode->id = $this->idcounter++;
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subpatt($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
            // Error: only one or two top-level alternative allowed in a conditional subpattern.
            if ($exprnode->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || $exprnode->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, $paren->indfirst, $exprnode->indlast + 1, null, null, array($exprnode, $assertnode));
                $this->reducecount++;
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subpatt($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }
        $result->operands[2] = new qtype_preg_node_assert($subtype);
        $result->operands[2]->operands[0] = $assertnode;
        $result->operands[2]->userinscription = new qtype_preg_userinscription(qtype_poasquestion_string::substr($paren->userinscription->data, 2) . '...)');
        $result->operands[2]->id = $this->idcounter++;
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...)...|...)'));
        $result->id = $this->idcounter++;
        $this->reducecount++;
        return $result;
    }

    protected function create_cond_subpatt_other_node($paren, $exprnode) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($paren->indlast + 2, $paren->indlast + 2, new qtype_preg_userinscription());
            $exprnode->id = $this->idcounter++;
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subpatt($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
             // Error: only one or two top-level alternative allowed in a conditional subpattern.
            if ($exprnode->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || $exprnode->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, $paren->indfirst, $exprnode->indlast + 1, null, null, array($exprnode));
                $this->reducecount++;
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subpatt($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT) {
            $result->number = $paren->number;
        }
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...|...)'));
        $result->id = $this->idcounter++;
        $this->reducecount++;
        return $result;
    }

    protected function get_nested_subpatts($root) {
        $result = array();
        if ($root->type === qtype_preg_node::TYPE_NODE_SUBPATT) {
            $result[] = $root->number;
        }
        if (is_a($root, 'qtype_preg_operator')) {
            foreach ($root->operands as $operand) {
                $result = array_merge($result, $this->get_nested_subpatts($operand));
            }
        }
        return array_values(array_unique($result));
    }
#line 300 "../preg_parser.php"

/* Next is all token values, in a form suitable for use by makeheaders.
** This section will be null unless lemon is run with the -m switch.
*/
/*
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands.
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const ERROR_PREC_VERY_SHORT          =  1;
    const ERROR_PREC_SHORT               =  2;
    const ERROR_PREC                     =  3;
    const CLOSEBRACK                     =  4;
    const ALT                            =  5;
    const CONC                           =  6;
    const PARSLEAF                       =  7;
    const QUANT                          =  8;
    const OPENBRACK                      =  9;
    const CONDSUBPATT                    = 10;
    const YY_NO_ACTION = 51;
    const YY_ACCEPT_ACTION = 50;
    const YY_ERROR_ACTION = 49;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < YYNSTATE                  Shift N.  That is, push the lookahead
**                                      token onto the stack and goto state N.
**
**   YYNSTATE <= N < YYNSTATE+YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == YYNSTATE+YYNRULE              A syntax error has occurred.
**
**   N == YYNSTATE+YYNRULE+1            The parser accepts its input.
**
**   N == YYNSTATE+YYNRULE+2            No such action.  Denotes unused
**                                      slots in the yy_action[] table.
**
** The action table is constructed as a single large table named yy_action[].
** Given state S and lookahead X, the action is computed as
**
**      yy_action[ yy_shift_ofst[S] + X ]
**
** If the index value yy_shift_ofst[S]+X is out of range or if the value
** yy_lookahead[yy_shift_ofst[S]+X] is not equal to X or if yy_shift_ofst[S]
** is equal to YY_SHIFT_USE_DFLT, it means that the action is not in the table
** and that yy_default[S] should be used instead.
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the yy_reduce_ofst[] array is used in place of
** the yy_shift_ofst[] array and YY_REDUCE_USE_DFLT is used in place of
** YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  yy_action[]        A single table containing all actions.
**  yy_lookahead[]     A table containing the lookahead for each entry in
**                     yy_action.  Used to detect hash collisions.
**  yy_shift_ofst[]    For each state, the offset into yy_action for
**                     shifting terminals.
**  yy_reduce_ofst[]   For each state, the offset into yy_action for
**                     shifting non-terminals after a reduce.
**  yy_default[]       Default action for each state.
*/
    const YY_SZ_ACTTAB = 94;
static public $yy_action = array(
 /*     0 */    24,   11,   14,   21,   15,   10,    2,   13,   20,    4,
 /*    10 */    39,   21,   26,   10,    2,    5,   16,   11,   39,   21,
 /*    20 */    15,   10,    2,    1,   22,   11,   39,   21,   15,   10,
 /*    30 */     2,    7,   18,    4,   39,   21,   26,   10,    2,   12,
 /*    40 */    19,    4,   39,   21,   26,   10,    2,    8,   17,    4,
 /*    50 */    39,   21,   26,   10,    2,   39,    3,   11,   39,   21,
 /*    60 */    15,   10,    2,   39,    6,    4,   39,   21,   26,   10,
 /*    70 */     2,   39,   23,   11,   39,   21,   15,   10,    2,   39,
 /*    80 */    21,   26,   10,    2,   21,   15,   10,    2,   50,   25,
 /*    90 */     9,   15,   10,    2,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   14,    7,    8,    9,   10,   14,    4,    5,
 /*    10 */    15,    7,    8,    9,   10,   14,    4,    5,   15,    7,
 /*    20 */     8,    9,   10,   14,    4,    5,   15,    7,    8,    9,
 /*    30 */    10,   14,    4,    5,   15,    7,    8,    9,   10,   14,
 /*    40 */     4,    5,   15,    7,    8,    9,   10,   14,    4,    5,
 /*    50 */    15,    7,    8,    9,   10,   15,    4,    5,   15,    7,
 /*    60 */     8,    9,   10,   15,    4,    5,   15,    7,    8,    9,
 /*    70 */    10,   15,    4,    5,   15,    7,    8,    9,   10,   15,
 /*    80 */     7,    8,    9,   10,    7,    8,    9,   10,   12,   13,
 /*    90 */    14,    8,    9,   10,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 14;
    static public $yy_shift_ofst = array(
 /*     0 */     4,   52,   60,   44,    4,   12,   28,   68,   20,   -4,
 /*    10 */    36,   73,   77,   77,   83,
);
    const YY_REDUCE_USE_DFLT = -13;
    const YY_REDUCE_MAX = 14;
    static public $yy_reduce_ofst = array(
 /*     0 */    76,  -12,    9,   33,   25,  -12,    1,  -12,  -12,  -12,
 /*    10 */    17,   -7,  -12,  -12,  -12,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(4, 5, 7, 8, 9, 10, ),
        /* 1 */ array(4, 5, 7, 8, 9, 10, ),
        /* 2 */ array(4, 5, 7, 8, 9, 10, ),
        /* 3 */ array(4, 5, 7, 8, 9, 10, ),
        /* 4 */ array(4, 5, 7, 8, 9, 10, ),
        /* 5 */ array(4, 5, 7, 8, 9, 10, ),
        /* 6 */ array(4, 5, 7, 8, 9, 10, ),
        /* 7 */ array(4, 5, 7, 8, 9, 10, ),
        /* 8 */ array(4, 5, 7, 8, 9, 10, ),
        /* 9 */ array(4, 5, 7, 8, 9, 10, ),
        /* 10 */ array(4, 5, 7, 8, 9, 10, ),
        /* 11 */ array(7, 8, 9, 10, ),
        /* 12 */ array(7, 8, 9, 10, ),
        /* 13 */ array(7, 8, 9, 10, ),
        /* 14 */ array(8, 9, 10, ),
        /* 15 */ array(),
        /* 16 */ array(),
        /* 17 */ array(),
        /* 18 */ array(),
        /* 19 */ array(),
        /* 20 */ array(),
        /* 21 */ array(),
        /* 22 */ array(),
        /* 23 */ array(),
        /* 24 */ array(),
        /* 25 */ array(),
        /* 26 */ array(),
);
    static public $yy_default = array(
 /*     0 */    49,   46,   47,   41,   49,   49,   42,   43,   45,   40,
 /*    10 */    44,   30,   31,   29,   28,   32,   37,   36,   38,   33,
 /*    20 */    42,   39,   35,   34,   41,   27,   48,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    YYCODETYPE         is the data type used for storing terminal
**                       and nonterminal numbers.  "unsigned char" is
**                       used if there are fewer than 250 terminals
**                       and nonterminals.  "int" is used otherwise.
**    YYNOCODE           is a number of type YYCODETYPE which corresponds
**                       to no legal terminal or nonterminal number.  This
**                       number is used to fill in empty slots of the hash
**                       table.
**    YYFALLBACK         If defined, this indicates that one or more tokens
**                       have fall-back values which should be used if the
**                       original value of the token will not parse.
**    YYACTIONTYPE       is the data type used for storing terminal
**                       and nonterminal numbers.  "unsigned char" is
**                       used if there are fewer than 250 rules and
**                       states combined.  "int" is used otherwise.
**    qtype_preg_TOKENTYPE     is the data type used for minor tokens given
**                       directly to the parser from the tokenizer.
**    YYMINORTYPE        is the data type used for all minor tokens.
**                       This is typically a union of many types, one of
**                       which is qtype_preg_TOKENTYPE.  The entry in the union
**                       for base tokens is called "yy0".
**    YYSTACKDEPTH       is the maximum depth of the parser's stack.
**    qtype_preg_ARG_DECL      A global declaration for the %extra_argument
**    YYNSTATE           the combined number of states.
**    YYNRULE            the number of rules in the grammar
**    YYERRORSYMBOL      is the code number of the error symbol.  If not
**                       defined, then do no error processing.
*/
    const YYNOCODE = 16;
    const YYSTACKDEPTH = 100;
    const qtype_preg_ARG_DECL = '0';
    const YYNSTATE = 27;
    const YYNRULE = 22;
    const YYERRORSYMBOL = 11;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     *
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL
     *
     * Inputs:
     *
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     *
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    static public $yyTraceFILE;
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    //public $???????;      /* A place to hold %extra_argument - dynamically added */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array(
  '$',             'ERROR_PREC_VERY_SHORT',  'ERROR_PREC_SHORT',  'ERROR_PREC',  
  'CLOSEBRACK',    'ALT',           'CONC',          'PARSLEAF',    
  'QUANT',         'OPENBRACK',     'CONDSUBPATT',   'error',       
  'start',         'lastexpr',      'expr',        
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= lastexpr",
 /*   1 */ "expr ::= expr expr",
 /*   2 */ "expr ::= expr ALT expr",
 /*   3 */ "expr ::= expr ALT",
 /*   4 */ "expr ::= ALT expr",
 /*   5 */ "expr ::= expr QUANT",
 /*   6 */ "expr ::= OPENBRACK CLOSEBRACK",
 /*   7 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*   8 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr CLOSEBRACK",
 /*   9 */ "expr ::= CONDSUBPATT expr CLOSEBRACK CLOSEBRACK",
 /*  10 */ "expr ::= CONDSUBPATT CLOSEBRACK expr CLOSEBRACK",
 /*  11 */ "expr ::= CONDSUBPATT CLOSEBRACK CLOSEBRACK",
 /*  12 */ "expr ::= PARSLEAF",
 /*  13 */ "lastexpr ::= expr",
 /*  14 */ "expr ::= expr CLOSEBRACK",
 /*  15 */ "expr ::= CLOSEBRACK",
 /*  16 */ "expr ::= OPENBRACK expr",
 /*  17 */ "expr ::= OPENBRACK",
 /*  18 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr",
 /*  19 */ "expr ::= CONDSUBPATT expr",
 /*  20 */ "expr ::= CONDSUBPATT",
 /*  21 */ "expr ::= QUANT",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /* The following function deletes the value associated with a
    ** symbol.  The symbol can be either a terminal or nonterminal.
    ** "yymajor" is the symbol code, and "yypminor" is a pointer to
    ** the value.
    */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param qtype_preg_yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new qtype_preg_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    function yy_is_expected_token($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new qtype_preg_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;

        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new qtype_preg_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * static const struct {
     *  YYCODETYPE lhs;         Symbol on the left-hand side of the rule
     *  unsigned char nrhs;     Number of right-hand side symbols in the rule
     * }
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 12, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 3 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 3 ),
  array( 'lhs' => 14, 'rhs' => 5 ),
  array( 'lhs' => 14, 'rhs' => 4 ),
  array( 'lhs' => 14, 'rhs' => 4 ),
  array( 'lhs' => 14, 'rhs' => 3 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 13, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 4 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     *
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 203 "../preg_parser.y"
    function yy_r0(){
    $this->root = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 987 "../preg_parser.php"
#line 207 "../preg_parser.y"
    function yy_r1(){
    $this->_retvalue = new qtype_preg_node_concat();
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription());
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->operands[1] = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->reducecount++;
    }
#line 997 "../preg_parser.php"
#line 216 "../preg_parser.y"
    function yy_r2(){
    $this->_retvalue = new qtype_preg_node_alt();
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -2]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription('|'));
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->operands[1] = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->reducecount++;
    }
#line 1007 "../preg_parser.php"
#line 225 "../preg_parser.y"
    function yy_r3(){
    $this->_retvalue = new qtype_preg_node_finite_quant(0, 1, false, true, false);
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, new qtype_preg_userinscription('|'));
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->reducecount++;
    }
#line 1016 "../preg_parser.php"
#line 233 "../preg_parser.y"
    function yy_r4(){
    $this->_retvalue = new qtype_preg_node_finite_quant(0, 1, false, true, false);
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast + 1, new qtype_preg_userinscription('|'));
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->reducecount++;
    }
#line 1025 "../preg_parser.php"
#line 241 "../preg_parser.y"
    function yy_r5(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1035 "../preg_parser.php"
#line 250 "../preg_parser.y"
    function yy_r6(){
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info($this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->indlast, new qtype_preg_userinscription());
    $emptynode->id = $this->idcounter++;
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -1]->minor, $emptynode);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -1]->minor);
    $this->reducecount++;
    }
#line 1045 "../preg_parser.php"
#line 259 "../preg_parser.y"
    function yy_r7(){
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -2]->minor);
    $this->reducecount++;
    }
#line 1052 "../preg_parser.php"
#line 265 "../preg_parser.y"
    function yy_r8(){
    if ($this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subpatt_assertion_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subpatt_other_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
    }
#line 1062 "../preg_parser.php"
#line 274 "../preg_parser.y"
    function yy_r9(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subpatt_assertion_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -2]->minor, null);
    } else {
        $this->_retvalue = $this->create_cond_subpatt_other_node($this->yystack[$this->yyidx + -3]->minor, null);
    }
    }
#line 1072 "../preg_parser.php"
#line 283 "../preg_parser.y"
    function yy_r10(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subpatt_assertion_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + -1]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subpatt_other_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
    }
#line 1082 "../preg_parser.php"
#line 292 "../preg_parser.y"
    function yy_r11(){
    if ($this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subpatt_assertion_node($this->yystack[$this->yyidx + -2]->minor, null, null);
    } else {
        $this->_retvalue = $this->create_cond_subpatt_other_node($this->yystack[$this->yyidx + -2]->minor, null);
    }
    }
#line 1092 "../preg_parser.php"
#line 301 "../preg_parser.y"
    function yy_r12(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1100 "../preg_parser.php"
#line 308 "../preg_parser.y"
    function yy_r13(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 1106 "../preg_parser.php"
#line 313 "../preg_parser.y"
    function yy_r14(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, null, null, array($this->yystack[$this->yyidx + -1]->minor));
    $this->reducecount++;
    }
#line 1112 "../preg_parser.php"
#line 318 "../preg_parser.y"
    function yy_r15(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, ')', new qtype_preg_userinscription(')'));
    $this->reducecount++;
    }
#line 1118 "../preg_parser.php"
#line 323 "../preg_parser.y"
    function yy_r16(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription->data, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -1]->minor);
    $this->reducecount++;
    }
#line 1125 "../preg_parser.php"
#line 329 "../preg_parser.y"
    function yy_r17(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1132 "../preg_parser.php"
#line 335 "../preg_parser.y"
    function yy_r18(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -3]->minor->indfirst, $this->yystack[$this->yyidx + -3]->minor->indlast, $this->yystack[$this->yyidx + -3]->minor->userinscription->data, $this->yystack[$this->yyidx + -3]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -2]->minor));
    $this->reducecount++;
    }
#line 1138 "../preg_parser.php"
#line 340 "../preg_parser.y"
    function yy_r19(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription->data, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    $this->reducecount++;
    }
#line 1144 "../preg_parser.php"
#line 345 "../preg_parser.y"
    function yy_r20(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->reducecount++;
    }
#line 1150 "../preg_parser.php"
#line 350 "../preg_parser.y"
    function yy_r21(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1157 "../preg_parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     *
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     *
     * The parser will translate to something like:
     *
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     *
     * For a rule such as:
     *
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     *
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //qtype_preg_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new qtype_preg_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
#line 189 "../preg_parser.y"

    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR);
    }
#line 1262 "../preg_parser.php"
    }

    /**
     * The following code executes when a syntax error first occurs.
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
    }

    /*
    ** The following is executed when the parser accepts
    */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     *  The main parser program.
     * The first argument is a pointer to a structure obtained from
     * "qtype_preg_Alloc" which describes the current state of the parser.
     * The second argument is the major token number.  The third is
     * the minor token.  The fourth optional argument is whatever the
     * user wants (and specified in the grammar) and is available for
     * use by the action routines.
     *
     * Inputs:
     *
     * - A pointer to the parser (an opaque structure.)
     * - The major token number.
     * - The minor token number (token value).
     * - An option argument of a grammar-specified type.
     *
     * Outputs:
     * None.
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
     */
    function doParse($yymajor, $yytokenvalue, $extraargument = null)
    {
        if (self::qtype_preg_ARG_DECL && $extraargument !== null) {
            $this->{self::qtype_preg_ARG_DECL} = $extraargument;
        }
//        YYMINORTYPE yyminorunion;
//        int yyact;            /* The parser action. */
//        int yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */

        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new qtype_preg_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);

        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
