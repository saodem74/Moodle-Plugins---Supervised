<?php # vim:ft=php
require_once($CFG->dirroot . '/question/type/preg/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

%%
%function nextToken
%line
%char
%state CHARCLASS
%{
    protected $errors = array();

    public function get_errors() {
        return $this->errors;
    }

    protected function form_node($name, $subtype = null, $charclass = null, $leftborder = null, $rightborder = null, $greed = true) {
        $result = new $name;
        if (isset($subtype)) {
            $result->subtype = $subtype;
        }
        if ($name == 'preg_leaf_charset') {
            $result->charset = $charclass;
        } elseif ($name == 'preg_leaf_backref') {
            $result->number = $charclass;//TODO: rename $charclass argument, because it may be number of backref
        } elseif ($name == 'preg_node_finite_quant' || $name == 'preg_node_infinite_quant') {
            $result->greed = $greed;
            $result->leftborder = $leftborder;
            if ($name == 'preg_node_finite_quant') {
                $result->rightborder = $rightborder;
            }
        }
        return $result;
    }

    protected function form_res($type, $value) {
        $result->type = $type;
        $result->value = $value;
        return $result;
    }

    protected function form_num_interval(&$cc, $startchar, $endchar) {
        if(ord($startchar) < ord($endchar)) {
            $char = ord($startchar);
            while($char <= ord($endchar)) {
                $cc->charset .= chr($char);
                $char++;
            }
        } else {
            $cc->error = 1;
        }
    }
%}
%eof{
        if (isset($this->cc) && is_object($this->cc)) {//End of expression inside character class
            $this->errors[] = 'unclosedsqbrackets';
            $this->cc = null;
        }
%eof}
%%

<YYINITIAL> \? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1));
    return $res;
}
<YYINITIAL> \* {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0));
    return $res;
}
<YYINITIAL> \+ {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1));
    return $res;
}
<YYINITIAL> \?\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1, false));
    return $res;
}
<YYINITIAL> \*\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0, null, false));
    return $res;
}
<YYINITIAL> \+\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1, null, false));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ','))));
    return $res;
}
<YYINITIAL> \{[0-9]+,\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
<YYINITIAL> \{,[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3)));
    return $res;
}
<YYINITIAL> \{[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ',')), false));
    return $res;
}
<YYINITIAL> \{[0-9]+,\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') -1), null, false));
    return $res;
}
<YYINITIAL> \{,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3), false));
    return $res;
}
<YYINITIAL> \{[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1), false));
    return $res;
}
<YYINITIAL> \[ {
    $this->cc = new preg_leaf_charset;
    $this->cc->negative = false;
    $this->cccharnumber = 0;
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> \( {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node::TYPE_NODE_SUBPATT);
    return $res;
}
<YYINITIAL> \) {
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, 0);
    return $res;
}
<YYINITIAL> \(\?> {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK,preg_node_subpatt::SUBTYPE_ONCEONLY);
    return $res;
}
<YYINITIAL> \(\?: {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node_subpatt::SUBTYPE_GROUPING);
    return $res;
}
<YYINITIAL> \(\?\(\?= {
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, preg_node_cond_subpatt::SUBTYPE_PLA);
    return $res;
}
<YYINITIAL> \(\?\(\?! {
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, preg_node_cond_subpatt::SUBTYPE_NLA);
    return $res;
}
<YYINITIAL> \(\?\(\?<= {
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, preg_node_cond_subpatt::SUBTYPE_PLB);
    return $res;
}
<YYINITIAL> \(\?\(\?<! {
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, preg_node_cond_subpatt::SUBTYPE_NLB);
    return $res;
}
<YYINITIAL> \(\?= {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node_assert::SUBTYPE_PLA);
    return $res;
}
<YYINITIAL> \(\?! {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node_assert::SUBTYPE_NLA);
    return $res;
}
<YYINITIAL> \(\?<= {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node_assert::SUBTYPE_PLB);
    return $res;
}
<YYINITIAL> \(\?<! {
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, preg_node_assert::SUBTYPE_NLB);
    return $res;
}
<YYINITIAL> \. {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_DOT));
    return $res;
}
<YYINITIAL> [^\[\]\\*+?{}()|.^$] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $this->yytext()));
    return $res;
}
<YYINITIAL> \| {
    $res = $this->form_res(preg_parser_yyParser::ALT, 0);
    return $res;
}
<YYINITIAL> \\[\[\]?*+{}|().] {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $text[1]));
    return $res;
}
<YYINITIAL> \\[0-9][0-9]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, substr($this->yytext(), 1)));
    return $res;
}
<YYINITIAL> \\0[0-9][0-9]?|[0-9][0-9][0-9] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\x[0-9][0-9] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(hexdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\\\ {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '\\'));
    return $res;
}
<YYINITIAL> \\b {
    $res = $this->form_res(preg_parser_yyParser::WORDBREAK, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    return $res;
}
<YYINITIAL> \\B {
    $res = $this->form_res(preg_parser_yyParser::WORDBREAK, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    $res->value->negative = true;
    return $res;
}
<YYINITIAL> \\d {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '0123456789'));
    return $res;
}
<YYINITIAL> \\D {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, '0123456789');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\w {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR));
    return $res;
}
<YYINITIAL> \\W {
    $PARSLEAF = $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR);
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\s {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, ' '));
    return $res;
}
<YYINITIAL> \\S {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, ' ');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\t {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(9)));
    return $res;
}
<YYINITIAL> "^" {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
    $res = $this->form_res(preg_parser_yyParser::STARTANCHOR, $leaf);
    return $res;
}
<YYINITIAL> "$" {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR);
    $res = $this->form_res(preg_parser_yyPARSER::ENDANCHOR, $leaf);
    return $res;
}
<CHARCLASS> \\\\ {
    $this->cc->charset .= '\\';
    $this->cccharnumber++;
}
<CHARCLASS> \\\[ {
    $this->cc->charset .= '[';
    $this->cccharnumber++;
}
<CHARCLASS> \\\] {
    $this->cc->charset .= ']';
    $this->cccharnumber++;
}
<CHARCLASS> \\0[0-9][0-9]|[0-9][0-9][0-9] {
    $this->cc->charset .= chr(octdec(substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
<CHARCLASS> \\x[0-9][0-9] {
    $this->cccharnumber++;
    $this->cc->charset .= chr(hexdec(substr($this->yytext(), 1)));
}
<CHARCLASS> \\d {
    $this->cccharnumber++;
    $this->cc->charset .= '0123456789';
}
<CHARCLASS> \\w {
    $this->cc->w = true;
}
<CHARCLASS> \\W {
    $this->cc->W = true;
}
<CHARCLASS> \\s {
    $this->cccharnumber++;
    $this->cc->charset .= ' ';
}
<CHARCLASS> \\t {
    $this->cccharnumber++;
    $this->cc->charset .= chr(9);
}
<CHARCLASS> "^" {
    if ($this->cccharnumber) {
        $this->cc->charset .= '^';
    } else {
        $this->cc->negative = true;
    }
    $this->cccharnumber++;
}
<CHARCLASS> "^-" {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
        $this->cc->negative = true;
        $this->cccharnumber++;
    }
}
<CHARCLASS> - {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
    }
    $this->cccharnumber++;
}
<CHARCLASS> [0-9]-[0-9]|[a-z]-[a-z]|[A-Z]-[A-Z] {
    $text = $this->yytext();
    $this->form_num_interval($this->cc, $text[0], $text[2]);
}
<CHARCLASS> \\- {
    $this->cc->charset .= '-';
    $this->cccharnumber++;
}
<CHARCLASS> [^-\[\]\\^] {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
<CHARCLASS> \] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    $this->cc = null;
    return $res;
}