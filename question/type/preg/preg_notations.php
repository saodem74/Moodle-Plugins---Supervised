<?php

/**
 * Defines classes of notations, used to write regexes.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

/**
 * Abstract notation class
 */
abstract class qtype_preg_notation {

    //Regular expression in this notation
    public $regex;
    //Regular expression modifiers in this notation
    public $modifiers;
    //Regular expression handling options
    public $options;

    /**
    * Constructs notation object, should suit most notations
    */
    public function __construct($regex, $modifiers = '', $options = null) {
        $this->regex = $regex;
        $this->modifiers = $modifiers;
        if ($options === null) {
            $options = new qtype_preg_handling_options;
        }
        $this->options = $options;
    }

    /**
    * Return notation name
    */
    abstract public function name();

    /**
    * Returns regular expression in desired notation, should be overloaded
    */
    public function convert_regex($targetnotation) {
        throw new qtype_preg_exception('Sorry, no conversion from '.$this->name().' to '.$targetnotation.' implemented yet.');
    }

    /**
    * Returns regular expression modifiers in desired notation, should suit most notations
    * When overloading this, you probably would want to add some modifers based on regular expression
    */
    public function convert_modifiers($targetnotation) {
        return $this->modifiers;
    }

    /**
    * Returns regular expression options in desired notation, should suit most notations
    * When overloading this, you probably would want to set some options based on notation
    */
    public function convert_options($targetnotation) {
        return $this->options;
    }

}

/**
 * Native notation, supported by internal regular expression parser and used by any regular expression handlers that using this parser.
 * You would usually convert other regexes to it with notable exception of preg_php_extension engine, that wants PCRE strict notation.
 */
class qtype_preg_notation_native extends qtype_preg_notation {

    public function name() {
        return 'native';
    }

     public function convert_regex($targetnotation) {
        if ($targetnotation == 'pcrestrict') {
            //Escape all empty parenthesis (subpatterns and assrtions)
            //$pattern = '/(?<!\\\\)(\(|\(\?(\:|\||\=|\<\=|\!|\<\!))\)/u';
            $pattern = '/(?<!\\\\)\(\)/u';
            $replacement = '\(\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\:\)/u';
            $replacement = '\(\?\:\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\|\)/u';
            $replacement = '\(\?\|\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\=\)/u';
            $replacement = '\(\?\=\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\<\=\)/u';
            $replacement = '\(\?\<\=\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\!\)/u';
            $replacement = '\(\?\!\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            $pattern = '/(?<!\\\\)\(\?\<\!\)/u';
            $replacement = '\(\?\<\!\)';
            $this->regex = preg_replace($pattern, $replacement, $this->regex);
            return $this->regex;
        }
    }
    //TODO - implement converting from native to PCRE strict notation
}

/**
 * Moodle shortanswer notation is basically a string to match with '*' wildcard for any number of any characters
 * Easily converts to both native and PCRE strict notations
 */
class qtype_preg_notation_mdlshortanswer extends qtype_preg_notation {

    public function name() {
        return 'mdlshortanswer';
    }

    public function convert_regex($targetnotation) {

        if ($targetnotation == 'native' || $targetnotation == 'pcrestrict') {
            //Code from qtype_shortanswer_question::compare_string_with_wildcard with proper respect for Tim Hunt

            // Break the string on non-escaped asterisks.
            $bits = preg_split('/(?<!\\\\)\*/', $this->regex);
            // Escape regexp special characters in the bits.
            $excapedbits = array();
            foreach ($bits as $bit) {
                $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
            }
            // Put it back together to make the regexp.
            return implode('.*', $excapedbits);
        }
        parent::convert_regex($targetnotation);
    }
}

class qtype_preg_notation_pcrestrict extends qtype_preg_notation {

    public function name() {
        return 'pcrestrict';
    }

    public function convert_regex($targetnotation) {
        if ($targetnotation == 'native') {
            return $this->regex;
        }
        parent::convert_regex($targetnotation);
    }

    public function convert_options($targetnotation) {
        if ($targetnotation == 'native') {
            $this->options->pcrestrict = true;
        }
        return $this->options;
    }
}
 ?>
