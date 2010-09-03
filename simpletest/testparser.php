<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_parser.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

/*
*   Need test for next operations:
*nothing
*/
 
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher.php');

//$err = find_illegal_isnt_object($matcher->roots[0], 'root');if($err !== false){ echo '<br/> NON OBJECT!!!' . $err . '<br/>';}

function find_illegal_isnt_object($node, $path) {
    if(!is_object($node)) {
        return $path;
    } elseif ($node->type == NODE) {
        if ($node->subtype == NODE_CONC || $node->subtype == NODE_ALT) {
            $result = find_illegal_isnt_object($node->secop, $path . '->secop');
            if ($result !== false) {
                return $result;
            }
        }
        $result = find_illegal_isnt_object($node->firop, $path . '->firop');
        if ($result !== false) {
            return $result;
        }
    }
    return false;
}

class parser_test extends UnitTestCase {

    //Unit test for lexer
    function test_lexer_quantificators() {
        $regex = '?*+{1,5}{,5}{1,}{5}*???+?{1,5}?{,5}?{1,}?{5}?';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//?
        $this->assertTrue($token->type === preg_parser_yyParser::QUEST);
        $token = $lexer->nextToken();//*
        $this->assertTrue($token->type === preg_parser_yyParser::ITER);
        $token = $lexer->nextToken();//+
        $this->assertTrue($token->type === preg_parser_yyParser::PLUS);
        $token = $lexer->nextToken();//{1,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{,5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//{1,}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == -1 && $token->value->greed);
        $token = $lexer->nextToken();//{5}
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && $token->value->greed);
        $token = $lexer->nextToken();//*?
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_ITER);
        $token = $lexer->nextToken();//??
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_QUEST);
        $token = $lexer->nextToken();//+?
        $this->assertTrue($token->type == preg_parser_yyParser::LAZY_PLUS);
        $token = $lexer->nextToken();//{1,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{,5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 5 && !$token->value->greed);
        $token = $lexer->nextToken();//{1,}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 1 && $token->value->rightborder == -1 && !$token->value->greed);
        $token = $lexer->nextToken();//{5}?
        $this->assertTrue($token->type === preg_parser_yyParser::LAZY_QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 5 && $token->value->rightborder == 5 && !$token->value->greed);
    }
    function test_lexer_backslach() {
        $regex = '\\\\\\*\\[\\23\\023\\x23\\d\\s\\t\\b\\B';//\\\*\[\23\023\x23\d\s\t\b\B
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//\\
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '\\');
        $token = $lexer->nextToken();//\*
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '*');
        $token = $lexer->nextToken();//\[
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '[');
        $token = $lexer->nextToken();//\23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_LINK);
        $token = $lexer->nextToken();//\023
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && ord($token->value->chars) == 023);
        $token = $lexer->nextToken();//\x23
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && ord($token->value->chars) == 0x23);
        $token = $lexer->nextToken();//\d
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '0123456789');
        $token = $lexer->nextToken();//\s
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == ' ');
        $token = $lexer->nextToken();//\t
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == chr(9));
        $token = $lexer->nextToken();//\b
        $this->assertTrue($token->type === preg_parser_yyParser::WORDBREAK);
        $token = $lexer->nextToken();//\B
        $this->assertTrue($token->type === preg_parser_yyParser::WORDNOTBREAK);
    }
    function test_lexer_charclass() {
        //[a][abc][ab{][ab\\][ab\]][a\db][a-d][3-6]
        $regex = '[a][abc][ab{][ab\\\\][ab\\]][a\\db][a-d][3-6]';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();//[a]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'a');
        $token = $lexer->nextToken();//[abc]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'abc');
        $token = $lexer->nextToken();//[ab{]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab{');
        $token = $lexer->nextToken();//[ab\\]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab\\');
        $token = $lexer->nextToken();//[ab\]]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'ab]');
        $token = $lexer->nextToken();//[a\db]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'a0123456789b');
        $token = $lexer->nextToken();//[a-d]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == 'abcd');
        $token = $lexer->nextToken();//[3-6]
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF);
        $this->assertTrue($token->value->type == LEAF && $token->value->subtype == LEAF_CHARCLASS && $token->value->chars == '3456');
    }
    function test_lexer_few_number_in_quant() {
        //{135,12755139}{135,}{,12755139}{135}
        $regex = '{135,12755139}{135,}{,12755139}{135}';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 12755139);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == -1);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 0 && $token->value->rightborder == 12755139);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::QUANT);
        $this->assertTrue($token->value->type == NODE && $token->value->subtype == NODE_QUANT && $token->value->leftborder == 135 && $token->value->rightborder == 135);
    }
    function test_lexer_unchors() {
        //^a|b$
        $regex = '^a|b$';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::STARTUNCHOR);
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::ENDUNCHOR);
    }
    function test_lexer_asserts() {
        $regex = '(?=(?!(?<=(?<!';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::ASSERT_TF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::ASSERT_FF);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::ASSERT_TB);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::ASSERT_FB);
    }
    function test_lexer_metasymbol_dot() {
        $regex = '.';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type === preg_parser_yyParser::PARSLEAF && $token->value->subtype === LEAF_METASYMBOLDOT);
    }
    function test_lexer_subpatterns() {
        $regex = '((?(?:(?>';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::OPENBRACK);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::CONDSUBPATT);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::GROUPING);
        $token = $lexer->nextToken();
        $this->assertTrue($token->type == preg_parser_yyParser::ONETIMESUBPATT);
    }
    //Unit tests for parser
    function test_parser_easy_regex() {//a|b
        $parser = new preg_parser_yyParser;
        $regex = 'a|b';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_quantification() {//ab+
        $parser = new preg_parser_yyParser;
        $regex = 'ab+';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_CONC);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == NODE && $root->secop->subtype == NODE_PLUSQUANT);
        $this->assertTrue($root->secop->firop->type == LEAF && $root->secop->firop->subtype == LEAF_CHARCLASS && $root->secop->firop->chars == 'b');
    }
    function test_parser_alt_and_quantif() {//a*|b
        $parser = new preg_parser_yyParser;
        $regex = 'a*|b';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == NODE && $root->firop->subtype == NODE_ITER);
        $this->assertTrue($root->firop->firop->type == LEAF && $root->firop->firop->subtype == LEAF_CHARCLASS && $root->firop->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_concatenation() {//ab
        $parser = new preg_parser_yyParser;
        $regex = 'ab';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_CONC);
        $this->assertTrue($root->firop->type == LEAF && $root->firop->subtype == LEAF_CHARCLASS && $root->firop->chars == 'a');
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_CHARCLASS && $root->secop->chars == 'b');
    }
    function test_parser_alt_and_conc() {//ab|cd
        $parser = new preg_parser_yyParser;
        $regex = 'ab|cd';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == NODE && $root->subtype == NODE_ALT);
        $this->assertTrue($root->firop->type == NODE && $root->firop->subtype == NODE_CONC);
        $this->assertTrue($root->firop->firop->type == LEAF && $root->firop->firop->subtype == LEAF_CHARCLASS && $root->firop->firop->chars == 'a');
        $this->assertTrue($root->firop->secop->type == LEAF && $root->firop->secop->subtype == LEAF_CHARCLASS && $root->firop->secop->chars == 'b');
        $this->assertTrue($root->secop->type == NODE && $root->secop->subtype == NODE_CONC);
        $this->assertTrue($root->secop->firop->type == LEAF && $root->secop->firop->subtype == LEAF_CHARCLASS && $root->secop->firop->chars == 'c');
        $this->assertTrue($root->secop->secop->type == LEAF && $root->secop->secop->subtype == LEAF_CHARCLASS && $root->secop->secop->chars == 'd');
    }
    function test_parser_long_regex() {//(?:a|b)*abb
        $parser = new preg_parser_yyParser;
        $regex = '(?:a|b)*abb';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $matcher = new dfa_preg_matcher;
        $matcher->roots[0] = $parser->get_root();
        $matcher->append_end(0);
        $matcher->buildfa(0);
        $res = $matcher->compare('ab', 0);
        $this->assertTrue(!$res->full && $res->index == 1 && ($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abb', 0);
        $this->assertTrue($res->full && $res->index == 2 && $res->next == 0);
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbab', 0);
        $this->assertTrue(!$res->full && $res->index == 57 && ($res->next == 'a' || $res->next == 'b'));
        $res = $matcher->compare('abababababababababababababababbabababbababababbbbbaaaabbabb', 0);
        $this->assertTrue($res->full && $res->index == 58 && $res->next == 0);  
    }
    function test_parser_two_unchors() {
        $parser = new preg_parser_yyParser;
        $regex = '^a$';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $unchor = $parser->get_unchor();
        $this->assertTrue($root->type == LEAF && $root->subtype == LEAF_CHARCLASS && $root->chars === 'a');
        $this->assertTrue($unchor->start === true && $unchor->end === true);
    }
    function test_parser_start_unchor() {
        $parser = new preg_parser_yyParser;
        $regex = '^a';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $unchor = $parser->get_unchor();
        $this->assertTrue($root->type == LEAF && $root->subtype == LEAF_CHARCLASS && $root->chars === 'a');
        $this->assertTrue($unchor->start === true && $unchor->end === false);
    }
    function test_parser_end_unchor() {
        $parser = new preg_parser_yyParser;
        $regex = 'a$';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $unchor = $parser->get_unchor();
        $this->assertTrue($root->type == LEAF && $root->subtype == LEAF_CHARCLASS && $root->chars === 'a');
        $this->assertTrue($unchor->start === false && $unchor->end === true);
    }
    function test_parser_no_unchors() {
        $parser = new preg_parser_yyParser;
        $regex = 'a';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $unchor = $parser->get_unchor();
        $this->assertTrue($root->type == LEAF && $root->subtype == LEAF_CHARCLASS && $root->chars === 'a');
        $this->assertTrue($unchor->start === false && $unchor->end === false);
    }
    function test_parser_error() {
        $parser = new preg_parser_yyParser;
        $regex = '^((ab|cd)ef$';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $this->assertTrue($parser->get_error());
    }
    function test_parser_no_error() {
        $parser = new preg_parser_yyParser;
        $regex = '((ab|cd)ef)';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $this->assertFalse($parser->get_error());
    }
    function test_parser_asserts() {
        $parser = new preg_parser_yyParser;
        $regex = '(?<=\w)(?<!_)a*(?=\w)(?!_)';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $ff = $root->secop;
        $tf = $root->firop->secop;
        $fb = $root->firop->firop->firop->secop;
        $tb = $root->firop->firop->firop->firop;
        $this->assertTrue($tf->type == NODE && $tf->subtype == NODE_ASSERTTF);
        $this->assertTrue($ff->type == NODE && $ff->subtype == NODE_ASSERTFF);
        $this->assertTrue($fb->type == NODE && $fb->subtype == NODE_ASSERTFB);
        $this->assertTrue($tb->type == NODE && $tb->subtype == NODE_ASSERTTB);
    }
    function test_parser_metasymbol_dot() {
        $parser = new preg_parser_yyParser;
        $regex = '.';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->type == LEAF && $root->subtype == LEAF_METASYMBOLDOT);
    }
    function test_parser_word_break() {
        $parser = new preg_parser_yyParser;
        $regex = 'a\b';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_WORDBREAK);
    }
    function test_parser_word_not_break() {
        $parser = new preg_parser_yyParser;
        $regex = 'a\B';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->secop->type == LEAF && $root->secop->subtype == LEAF_WORDNOTBREAK);
    }
    function test_parser_subpatterns() {
        $parser = new preg_parser_yyParser;
        $regex = '((?:(?(?=a)a|(?>b))))';
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $curr = -1;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $root = $parser->get_root();
        $this->assertTrue($root->subtype == NODE_SUBPATT);
        $this->assertTrue($root->firop->subtype == NODE_CONDSUBPATT);
        $this->assertTrue($root->firop->secop->subtype == NODE_ONETIMESUBPATT);
    }
}
?>