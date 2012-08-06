<?php
/* Driver template for the LEMON parser generator.
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class preg_parser_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof preg_parser_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof preg_parser_yyToken) {
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
                $x = ($value instanceof preg_parser_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof preg_parser_yyToken) {
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

    require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
#line 82 "../preg_parser.php"

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
class preg_parser_yyStackEntry
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
class preg_parser_yyParser
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 7 "../preg_parser.y"

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
        $this->handlingoptions = new qtype_preg_handling_options;
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
        $newnode = new qtype_preg_node_error;
        $newnode->id = $this->idcounter++;
        $newnode->subtype = $subtype;
        $newnode->indfirst = $indfirst;
        $newnode->indlast = $indlast;
        $newnode->operands = $operands;
        $newnode->userinscription = $userinscription;
        $newnode->addinfo = $addinfo;
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
                $result = new qtype_preg_node_subpatt;
            } else if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT || $parens->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY) {
                $result = new qtype_preg_node_subpatt;
                $result->number = $parens->number;
            } else {
                $result = new qtype_preg_node_assert;
            }
            $result->subtype = $parens->subtype;
            $result->operands[0] = $exprnode;
            $result->id = $this->idcounter++;
            $result->userinscription = $parens->userinscription . ' ... )';
        }
        $result->indfirst = $parens->indfirst;
        $result->indlast = $exprnode->indlast + 1;
        return $result;
    }
#line 217 "../preg_parser.php"

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
    const YY_NO_ACTION = 38;
    const YY_ACCEPT_ACTION = 37;
    const YY_ERROR_ACTION = 36;

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
    const YY_SZ_ACTTAB = 57;
static public $yy_action = array(
 /*     0 */    14,    8,    9,   13,   16,    7,    5,   10,   18,    8,
 /*    10 */    34,   13,   16,    7,    5,    2,   15,    8,   34,   13,
 /*    20 */    16,    7,    5,    3,    6,    8,   34,   13,   16,    7,
 /*    30 */     5,    1,   11,   34,   34,   13,   12,    7,    5,   34,
 /*    40 */    13,   12,    7,    5,   13,   16,    7,    5,   34,   16,
 /*    50 */     7,    5,   34,   34,   37,   17,    4,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   14,    7,    8,    9,   10,   14,    4,    5,
 /*    10 */    15,    7,    8,    9,   10,   14,    4,    5,   15,    7,
 /*    20 */     8,    9,   10,   14,    4,    5,   15,    7,    8,    9,
 /*    30 */    10,   14,    4,   15,   15,    7,    8,    9,   10,   15,
 /*    40 */     7,    8,    9,   10,    7,    8,    9,   10,   15,    8,
 /*    50 */     9,   10,   15,   15,   12,   13,   14,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 10;
    static public $yy_shift_ofst = array(
 /*     0 */    28,   20,   12,    4,   -4,   28,   28,   28,   33,   37,
 /*    10 */    41,
);
    const YY_REDUCE_USE_DFLT = -13;
    const YY_REDUCE_MAX = 10;
    static public $yy_reduce_ofst = array(
 /*     0 */    42,   -7,   -7,   -7,   -7,   17,    9,    1,  -12,   -7,
 /*    10 */    -7,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(4, 7, 8, 9, 10, ),
        /* 1 */ array(4, 5, 7, 8, 9, 10, ),
        /* 2 */ array(4, 5, 7, 8, 9, 10, ),
        /* 3 */ array(4, 5, 7, 8, 9, 10, ),
        /* 4 */ array(4, 5, 7, 8, 9, 10, ),
        /* 5 */ array(4, 7, 8, 9, 10, ),
        /* 6 */ array(4, 7, 8, 9, 10, ),
        /* 7 */ array(4, 7, 8, 9, 10, ),
        /* 8 */ array(7, 8, 9, 10, ),
        /* 9 */ array(7, 8, 9, 10, ),
        /* 10 */ array(8, 9, 10, ),
        /* 11 */ array(),
        /* 12 */ array(),
        /* 13 */ array(),
        /* 14 */ array(),
        /* 15 */ array(),
        /* 16 */ array(),
        /* 17 */ array(),
        /* 18 */ array(),
);
    static public $yy_default = array(
 /*     0 */    36,   33,   30,   32,   27,   34,   28,   31,   22,   21,
 /*    10 */    20,   29,   35,   26,   28,   24,   23,   19,   25,
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
**    preg_parser_TOKENTYPE     is the data type used for minor tokens given
**                       directly to the parser from the tokenizer.
**    YYMINORTYPE        is the data type used for all minor tokens.
**                       This is typically a union of many types, one of
**                       which is preg_parser_TOKENTYPE.  The entry in the union
**                       for base tokens is called "yy0".
**    YYSTACKDEPTH       is the maximum depth of the parser's stack.
**    preg_parser_ARG_DECL      A global declaration for the %extra_argument
**    YYNSTATE           the combined number of states.
**    YYNRULE            the number of rules in the grammar
**    YYERRORSYMBOL      is the code number of the error symbol.  If not
**                       defined, then do no error processing.
*/
    const YYNOCODE = 16;
    const YYSTACKDEPTH = 100;
    const preg_parser_ARG_DECL = '0';
    const YYNSTATE = 19;
    const YYNRULE = 17;
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
 /*   4 */ "expr ::= expr QUANT",
 /*   5 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*   6 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr CLOSEBRACK",
 /*   7 */ "expr ::= PARSLEAF",
 /*   8 */ "lastexpr ::= expr",
 /*   9 */ "expr ::= expr CLOSEBRACK",
 /*  10 */ "expr ::= CLOSEBRACK",
 /*  11 */ "expr ::= OPENBRACK expr",
 /*  12 */ "expr ::= OPENBRACK",
 /*  13 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr",
 /*  14 */ "expr ::= CONDSUBPATT expr",
 /*  15 */ "expr ::= CONDSUBPATT",
 /*  16 */ "expr ::= QUANT",
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
     * @param preg_parser_yyParser
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
                        $x = new preg_parser_yyStackEntry;
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
                        $x = new preg_parser_yyStackEntry;
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
        $yytos = new preg_parser_yyStackEntry;
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
  array( 'lhs' => 14, 'rhs' => 3 ),
  array( 'lhs' => 14, 'rhs' => 5 ),
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
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 120 "../preg_parser.y"
    function yy_r0(){
    $this->root = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 872 "../preg_parser.php"
#line 124 "../preg_parser.y"
    function yy_r1(){
    $this->_retvalue = new qtype_preg_node_concat;
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->operands[1] = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->userinscription = '';
    $this->_retvalue->id = $this->idcounter++;
    $this->_retvalue->indfirst = $this->yystack[$this->yyidx + -1]->minor->indfirst;
    $this->_retvalue->indlast = $this->yystack[$this->yyidx + 0]->minor->indlast;
    $this->reducecount++;
    }
#line 884 "../preg_parser.php"
#line 135 "../preg_parser.y"
    function yy_r2(){
    //ECHO 'ALT <br/>';
    $this->_retvalue = new qtype_preg_node_alt;
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->operands[1] = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->userinscription = '|';
    $this->_retvalue->id = $this->idcounter++;
    $this->_retvalue->indfirst = $this->yystack[$this->yyidx + -2]->minor->indfirst;
    $this->_retvalue->indlast = $this->yystack[$this->yyidx + 0]->minor->indlast;
    $this->reducecount++;
    }
#line 897 "../preg_parser.php"
#line 147 "../preg_parser.y"
    function yy_r3(){
    $this->_retvalue = new qtype_preg_node_alt;
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->operands[1] = new qtype_preg_leaf_meta;
    $this->_retvalue->operands[1]->subtype = qtype_preg_leaf_meta::SUBTYPE_EMPTY;
    $this->_retvalue->operands[1]->id = $this->idcounter++;
    $this->_retvalue->userinscription = '|';
    $this->_retvalue->id = $this->idcounter++;
    $this->_retvalue->indfirst = $this->yystack[$this->yyidx + -1]->minor->indfirst;
    $this->_retvalue->indlast = $this->yystack[$this->yyidx + -1]->minor->indlast + 1;
    $this->reducecount++;
    }
#line 911 "../preg_parser.php"
#line 160 "../preg_parser.y"
    function yy_r4(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->_retvalue->indfirst = $this->yystack[$this->yyidx + -1]->minor->indfirst;
    $this->_retvalue->indlast = $this->yystack[$this->yyidx + 0]->minor->indlast;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 922 "../preg_parser.php"
#line 170 "../preg_parser.y"
    function yy_r5(){
    //ECHO 'SUBPATT '.$this->yystack[$this->yyidx + -2]->minor->userinscription.'<br/>';
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -2]->minor);
    $this->reducecount++;
    }
#line 930 "../preg_parser.php"
#line 177 "../preg_parser.y"
    function yy_r6(){
    if ($this->yystack[$this->yyidx + -1]->minor->type != qtype_preg_node::TYPE_NODE_ALT) {
        $this->_retvalue = new qtype_preg_node_cond_subpatt;
        $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    } else {
        if ($this->yystack[$this->yyidx + -1]->minor->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || $this->yystack[$this->yyidx + -1]->minor->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
            //One or two top-level alternative allowed in conditional subpattern
            $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, $this->yystack[$this->yyidx + -4]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, null, null, array($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + -3]->minor));
            $this->reducecount++;
            return;
        } else {
            $this->_retvalue = new qtype_preg_node_cond_subpatt;
            $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor->operands[0];
            $this->_retvalue->operands[1] = $this->yystack[$this->yyidx + -1]->minor->operands[1];
        }
    }
    $this->_retvalue->subtype = $this->yystack[$this->yyidx + -4]->minor->subtype;
    if ($this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        $this->_retvalue->operands[2] = new qtype_preg_node_assert;
        $this->_retvalue->operands[2]->subtype = $this->yystack[$this->yyidx + -4]->minor->subtype;
        $this->_retvalue->operands[2]->operands[0] = $this->yystack[$this->yyidx + -3]->minor;
        $this->_retvalue->operands[2]->userinscription = qtype_poasquestion_string::substr($this->yystack[$this->yyidx + -4]->minor->userinscription, 2) . ' ... )';
        $this->_retvalue->operands[2]->id = $this->idcounter++;
        $this->_retvalue->userinscription = $this->yystack[$this->yyidx + -4]->minor->userinscription . ' ... ) ... | .... )';
    } else {
        if ($this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT) {
            $this->_retvalue->number = $this->yystack[$this->yyidx + -4]->minor->number;
        }
        $this->_retvalue->userinscription = $this->yystack[$this->yyidx + -4]->minor->userinscription . ' ... | .... )';
    }
    $this->_retvalue->id = $this->idcounter++;
    $this->_retvalue->indfirst = $this->yystack[$this->yyidx + -4]->minor->indfirst;
    $this->_retvalue->indlast = $this->yystack[$this->yyidx + -1]->minor->indlast + 1;
    $this->reducecount++;
    }
#line 968 "../preg_parser.php"
#line 214 "../preg_parser.y"
    function yy_r7(){
    //ECHO 'LEAF <br/>';
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->id = $this->idcounter++;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 977 "../preg_parser.php"
#line 222 "../preg_parser.y"
    function yy_r8(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 983 "../preg_parser.php"
#line 227 "../preg_parser.y"
    function yy_r9(){
    //ECHO 'UNOPENPARENS <br/>';
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, null, null, array($this->yystack[$this->yyidx + -1]->minor));
    $this->reducecount++;
    }
#line 990 "../preg_parser.php"
#line 233 "../preg_parser.y"
    function yy_r10(){
    //ECHO 'CLOSEPARENATSTART <br/>';
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, ')', ')');
    $this->reducecount++;
    }
#line 997 "../preg_parser.php"
#line 239 "../preg_parser.y"
    function yy_r11(){
    //ECHO 'UNCLOSEDPARENS <br/>';
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == $this->yystack[$this->yyidx + -1]->minor->indlast + 1) {//empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            if ($this->handlingoptions->pcrestrict) {//In strict regular expression notation empty parenthesis is OK, they just contains emptyness
                $emptynode = new qtype_preg_leaf_meta;
                $emptynode->subtype = qtype_preg_leaf_meta::SUBTYPE_EMPTY;
                $emptynode->id = $this->idcounter++;
                $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -1]->minor, $emptynode);
            } else {//Give error for empty parenthesis - they are sure a very strange thing to do, probably an error
                $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + -1]->minor->userinscription);
            }
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//regular unclosed parens
        $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    }
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -1]->minor);
    $this->reducecount++;
    }
#line 1022 "../preg_parser.php"
#line 263 "../preg_parser.y"
    function yy_r12(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1029 "../preg_parser.php"
#line 269 "../preg_parser.y"
    function yy_r13(){
    //ECHO 'UNCLOSEDPARENS <br/>';
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == $this->yystack[$this->yyidx + -1]->minor->indlast + 1) {//empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            //TODO check if such empty parens are allowed in PCRE and upgrade like expr($this->_retvalue) ::= OPENBRACK($this->yystack[$this->yyidx + -3]->minor) expr($this->yystack[$this->yyidx + 0]->minor). [ERROR_PREC] { if needed
            $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, $this->yystack[$this->yyidx + -3]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -3]->minor->userinscription, $this->yystack[$this->yyidx + -3]->minor->userinscription, array ($this->yystack[$this->yyidx + -2]->minor));
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//regular unclosed parens
        $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -3]->minor->indfirst, $this->yystack[$this->yyidx + -3]->minor->indlast, $this->yystack[$this->yyidx + -3]->minor->userinscription, $this->yystack[$this->yyidx + -3]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -2]->minor));
    }
    $this->reducecount++;
    }
#line 1047 "../preg_parser.php"
#line 286 "../preg_parser.y"
    function yy_r14(){
    //ECHO 'UNCLOSEDPARENS <br/>';
    //Two unclosed parens for conditional subpatterns
    //Create only one error node to avoid confusion when reporting errors to the user
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == $this->yystack[$this->yyidx + -1]->minor->indlast + 1) {//unclosed parens + empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            //TODO check if such empty parens are allowed in PCRE and upgrade like expr($this->_retvalue) ::= OPENBRACK($this->yystack[$this->yyidx + -1]->minor) expr($this->yystack[$this->yyidx + 0]->minor). [ERROR_PREC] { if needed
            $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + -1]->minor->userinscription);
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//two unclosed parens
        $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    }
    $this->reducecount++;
    }
#line 1067 "../preg_parser.php"
#line 305 "../preg_parser.y"
    function yy_r15(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->reducecount++;
    }
#line 1073 "../preg_parser.php"
#line 310 "../preg_parser.y"
    function yy_r16(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1080 "../preg_parser.php"

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
        //preg_parser_yyStackEntry $yymsp;            /* The top of the parser's stack */
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
                $x = new preg_parser_yyStackEntry;
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
#line 106 "../preg_parser.y"

    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR);
    }
#line 1185 "../preg_parser.php"
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
     * "preg_parser_Alloc" which describes the current state of the parser.
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
        if (self::preg_parser_ARG_DECL && $extraargument !== null) {
            $this->{self::preg_parser_ARG_DECL} = $extraargument;
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
            $x = new preg_parser_yyStackEntry;
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
