<?php 
/**
 * Defines a simple  english language lexer for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot.'/blocks/formal_langs/simple_english_tokens.php');

class block_formal_langs_language_simple_english extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    /** Preprocesses a string before scanning. This can be used for simplifying analyze
        and some other purposes, like merging some different variations of  same character
        into one
        @param string $string input string for scanning
        @return string
     */
    protected function preprocess_for_scan($string) {
        return str_replace('’', "'", $string);
    }
    
    public function name() {
        return 'simple_english';
    }
        
}

%%

%function next_token
%char
%line
%unicode
%full
%class block_formal_langs_predefined_simple_english_lexer_raw


%{
  
    // @var int number of  current parsed lexeme.
    private  $counter = 0;
  
    public function get_errors() {
        return array();
    }
  
    private function create_token($name, $value) {
        // get name of object
        $objectname = 'block_formal_langs_token_simple_english_' . $name;
        // create token object
        $res = new $objectname(null, strtoupper($name), $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }
    private function is_white_space($string) {
        $whitespace = array(' ', "\t", "\n", "\r", "f", "\v");
        return in_array($string[0], $whitespace);
    }
    private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;

        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines - 1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext()) - 1;
        }
        
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);
        
        return $res;
    }
    
%}


A = '

%%

({A}twou{A}dn{A}t|{A}e{A}ll|{A}e{A}s|{A}tisn{A}t|{A}twasn{A}t|{A}twon{A}t|{A}twou{A}d|{A}twouldn{A}t|{A}n{A}|{A}kay|{A}sfoot|{A}taint|{A}tweren{A}t|{A}tshall|{A}twixt|{A}twon{A}t|{A}twou{A}dn{A}t|{A}zat) { return $this->create_token('word',$this->yytext()); }
({A}cause|{A}d|{A}fraid|{A}hood|i{A}|a{A}|-in{A}|{A}m|mo{A}|{A}neath|o{A}|o{A}th{A}|po{A}|{A}pon|{A}re|{A}round|{A}s|{A}sblood|{A}scuse|{A}sup)                                                             { return $this->create_token('word',$this->yytext()); }
({A}t|t{A}|th{A}|{A}tis|{A}twas|{A}tween|{A}twere|{A}twill|{A}twould|{A}um|{A}ve|{A}em)                                                                                                                     { return $this->create_token('word',$this->yytext()); }
[a-zA-Z]+([\u2019'\-][a-zA-Z]+)*([sS]{A}|[oO]{A}|[hH]{A})?                                                                                                                                                  { return $this->create_token('word',$this->yytext()); }
[0-9]+                                                                                                                                      { return $this->create_token('numeric',$this->yytext()); }
("."|","|";"|":"|"!"|"?"|"?!"|"!!"|"!!!"|"\""|'|"("|")"|"...")                                                                       { return $this->create_token('punctuation',$this->yytext()); }
("+"|"-"|"="|"<"|">"|"@"|"#"|"%"|"^"|"&"|"*"|"$")                                                                                           { return $this->create_token('typographic_mark',$this->yytext()); }
.                                                                                                                                           { if (!$this->is_white_space($this->yytext())) return $this->create_token('other',$this->yytext());}