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
 * Defines Preg errors displayed to users.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');

class qtype_preg_error {

    // Human-understandable error message.
    public $errormsg;
    //
    public $index_first;
    //
    public $index_last;

    /**
     * Returns a string with first character in upper case and the rest of the string in lower case.
     */
    protected function uppercase_first_letter($str) {
        $head = qtype_poasquestion_string::strtoupper(qtype_poasquestion_string::substr($str, 0, 1));
        $tail = qtype_poasquestion_string::strtolower(qtype_poasquestion_string::substr($str, 1));
        return $head . $tail;
    }

    protected function highlight_regex($regex, $indfirst, $indlast) {
        if ($indfirst >= 0 && $indlast >= 0) {
            return htmlspecialchars(qtype_poasquestion_string::substr($regex, 0, $indfirst)) . '<b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $indfirst, $indlast - $indfirst + 1)) . '</b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $indlast + 1));
        } else {
            return htmlspecialchars($regex);
        }
    }

     public function __construct($errormsg, $regex = '', $index_first = -1, $index_last = -1, $preservemsg = false) {
        $errormsg = $this->uppercase_first_letter($errormsg);
        if (!$preservemsg) {
            $errormsg = htmlspecialchars($errormsg);
        }
        $this->index_first = $index_first;
        $this->index_last = $index_last;
        if ($index_first != -2) {
            $this->errormsg = $this->highlight_regex($regex, $index_first, $index_last) . '<br/>' . $errormsg;
        } else {
            $this->errormsg = $errormsg;
        }
     }
}

// A syntax error occured while parsing a regex.
class qtype_preg_parsing_error extends qtype_preg_error {

    public function __construct($regex, $astnode) {
        parent::__construct($astnode->error_string(), $regex, $astnode->indfirst, $astnode->indlast);
    }
}

// There's an unacceptable node in a regex.
class qtype_preg_accepting_error extends qtype_preg_error {

    public function __construct($regex, $matchername, $nodename, $index_first = -1, $index_last = -1) {
        $a = new stdClass;
        $a->nodename = $nodename;
        $a->indfirst = $index_first;
        $a->indlast = $index_last;
        $a->engine = get_string($matchername, 'qtype_preg');

        $errormsg = get_string('unsupported', 'qtype_preg', $a);

        parent::__construct($errormsg, $regex, $a->indfirst, $a->indlast);
    }
}

// There's an unsupported modifier in a regex.
class qtype_preg_modifier_error extends qtype_preg_error {

    public function __construct($matchername, $modifier) {
        $a = new stdClass;
        $a->modifier = $modifier;
        $a->classname = $matchername;

        $errormsg = get_string('unsupportedmodifier', 'qtype_preg', $a);

        parent::__construct($errormsg);
    }
}

// FA is too large.
class qtype_preg_too_complex_error extends qtype_preg_error {

    public function __construct($regex, $matcher, $index_first = -1, $index_last = -1) {
        global $CFG;

        if ($index_first == -1 || $index_last == -1) {
            $index_first = 0;
            $index_last = qtype_poasquestion_string::strlen($regex) - 1;
        }

        $a = new stdClass;
        $a->indfirst = $index_first;
        $a->indlast = $index_last;
        $a->engine = get_string($matcher->name(), 'qtype_preg');
        $a->link = $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=qtypesettingpreg';

        $errormsg = get_string('too_large_fa', 'qtype_preg', $a);

        parent::__construct($errormsg, $regex, $index_first, $index_last);
    }
}
