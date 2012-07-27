<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * Perl-compatible regular expression question hints classes.
 *
 * @package    qtype
 * @subpackage preg
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');

/**
 * Hint class for showing matching part of a response (along with unmatched head and tail)
 *
 * Also contains some methods common to the all hints, based on $matchresults
 *
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_hintmatchingpart extends qtype_specific_hint {

    /**
     * Is hint based on response or not?
     *
     * @return boolean true if response is used to calculate hint (and, possibly, penalty)
     */
    public function hint_response_based() {
        return true;//All matching hints are based on the response
    }

    /**
     * Returns whether response allows for the hint to be done
     */
    public function hint_available($response = null) {
        if ($response !== null) {
            $bestfit = $this->question->get_best_fit_answer($response);
            $matchresults = $bestfit['match'];
            return $this->could_show_hint($matchresults);
        }
        return false;
    }

    /**
     * Returns penalty for using specific hint of given hint type (possibly for given response)
     */
    public function penalty_for_specific_hint($response = null) {
            return $this->question->penalty;
    }

    /**
     * Render colored string with specific hint value for given response using correct ending, returned by the matcher
     *
     * Supposed to be called from render_hint() function of subclasses implementing hinted_string() and to_be_continued()
     */
    public function render_stringextension_hint($renderer, $response) {
        $bestfit = $this->question->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];

        if ($this->could_show_hint($matchresults)) {//hint could be computed
            if (!$matchresults->full) {//there is a hint to show
                $wronghead = $renderer->render_unmatched($matchresults->match_heading());
                $correctpart = $renderer->render_matched($matchresults->correct_before_hint());
                $hint = $renderer->render_hinted($this->hinted_string($matchresults));
                if ($this->to_be_continued($matchresults)) {
                    $hint .= $renderer->render_tobecontinued();
                }
                $wrongtail = '';
                if (qtype_poasquestion_string::strlen($hint) == 0) {
                    $wrongtail = $renderer->render_deleted($matchresults->tail_to_delete());
                }
                return $wronghead.$correctpart.$hint.$wrongtail;
            } else {//No hint, due to full match
                return qtype_preg_hintmatchingpart::render_hint($renderer, $response);
            }
        }
        return '';
    }

    /**
     * Implement in child classes to show hint
     */
    public function hinted_string($matchresults) {
        return '';
    }

    /**
     * Implement in child classes to show to be continued after hint
     */
    public function to_be_continued($matchresults) {
        return false;
    }

    /**
     * Render colored string showing matched and non-matched parts of response
     */
    public function render_hint($renderer, $response) {
        $bestfit = $this->question->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];

        if ($this->could_show_hint($matchresults)) {
            $wronghead = $renderer->render_unmatched($matchresults->match_heading());
            $correctpart = $renderer->render_matched($matchresults->matched_part());
            $wrongtail = $renderer->render_unmatched($matchresults->match_tail());
            return $wronghead.$correctpart.$wrongtail;
        }
        return '';
    }

    public function could_show_hint($matchresults) {
        $queryengine = $this->question->get_query_matcher($this->question->engine);
        //Correctness should be shown if engine support partial matching or a full match is achieved
        //Also correctness should be shown if this is not pure-assert match
        return ($matchresults->is_match() || $queryengine->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING)) && $matchresults->length[0] !== 0;
    }

}

/**
 * Hint class for next character hint
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_hintnextchar extends qtype_preg_hintmatchingpart {

    ////Abstract hint class functions implementation

    /**
     * Returns whether response allows for the hint to be done
     */
    public function hint_available($response = null) {
        $bestfit = $this->question->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        return parent::hint_available($response) && $this->question->usecharhint && !$matchresults->full;
    }

    /**
     * Returns penalty for using specific hint of given hint type (possibly for given response)
     */
    public function penalty_for_specific_hint($response = null) {
            return $this->question->charhintpenalty;
    }

    ////qtype_preg_matching_hint functions implementation
    public function render_hint($renderer, $response) {
        return $this->render_stringextension_hint($renderer, $response);
    }

    public function hinted_string($matchresults) {
        //One-character hint
        $hintedstring = $matchresults->string_extension();
        if (qtype_poasquestion_string::strlen($hintedstring) > 0) {
            return qtype_poasquestion_string::substr($hintedstring, 0, 1);
        }
        return '';
    }

    public function to_be_continued($matchresults) {
        $hintedstring = $matchresults->string_extension();
        return qtype_poasquestion_string::strlen($hintedstring) > 1 || (is_object($matchresults->extendedmatch) && $matchresults->extendedmatch->full === false);
    }

}

/**
 * Hint class for next lexem hint
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_hintnextlexem extends qtype_preg_hintmatchingpart {

    //Cache values, filled by hinted_string function
    protected $hinttoken;
    protected $endmatchindx;
    protected $inside;


    ////Abstract hint class functions implementation

    /**
     * Returns whether response allows for the hint to be done
     */
    public function hint_available($response = null) {
        $bestfit = $this->question->get_best_fit_answer($response);
        $matchresults = $bestfit['match'];
        return parent::hint_available($response) && $this->question->uselexemhint && !$matchresults->full && is_object($matchresults->extendedmatch);//TODO check that there is lexem after current situation
    }

    /**
     * Returns penalty for using specific hint of given hint type (possibly for given response)
     */
    public function penalty_for_specific_hint($response = null) {
            return $this->question->lexemhintpenalty;
    }

    ////qtype_preg_matching_hint functions implementation
    public function render_hint($renderer, $response) {
        return $this->render_stringextension_hint($renderer, $response);
    }

    public function hinted_string($matchresults) {
        //////Lexem hint
        $langobj = block_formal_langs::lang_object($this->question->langid);
        $extendedmatch = $matchresults->extendedmatch;
        $endmatchindx = $extendedmatch->index_first() + $matchresults->length();//Index of first non-matched character after match in extended match.
        $procstr = $langobj->create_from_string($extendedmatch->str());
        $stream = $procstr->stream;
        $tokens = $stream->tokens;

        if ($endmatchindx < 0) {//No match at all, but we still could give hint from the start of the string
            $endmatchindx = 0;
        }

        //Look for hint token.
        $hinttoken = null;
        $inside = false;//Whether match ended inside lexem.
        foreach ($tokens as $token) {
            if  ($token->position()->colstart() >= $endmatchindx) {//Token starts after match ends.
                //Match ended between tokens, or we would have loop breaked already.
                $hinttoken = $token; //Next token hint, $inside == false.
                break;
            } else if ($token->position()->colend() >= $endmatchindx) {//Match ends inside this token.
                $hinttoken = $token;
                $inside = true;//Token completion hint.
                break;
            }
        }

        //Cache values
        $this->hinttoken = $hinttoken;
        $this->inside = $inside;
        $this->endmatchindx = $endmatchindx;

        if ($hinttoken !== null) {//Found hint token.
            return qtype_poasquestion_string::substr($extendedmatch->str(), $endmatchindx, $hinttoken->position()->colend() - $endmatchindx + 1);
        } else {//There are some non-matched separators after end of last token. Just hint the end of generated string.
            return qtype_poasquestion_string::substr($extendedmatch->str(), $endmatchindx,  qtype_poasquestion_string::strlen($extendedmatch->str()) - $endmatchindx);
        }
    }

    public function to_be_continued($matchresults) {
        return  $this->hinttoken->position()->colend() + 1 < qtype_poasquestion_string::strlen($matchresults->extendedmatch->str())
                || $matchresults->extendedmatch->full === false;
    }

}