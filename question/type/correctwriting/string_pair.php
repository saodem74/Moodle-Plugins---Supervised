<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/formal_langs/tokens_base.php');

// A string pair with lcs, used in sequence analyzer
class qtype_correctwriting_string_pair extends block_formal_langs_string_pair {
    /**
     * LCS for sequence analyzer
     * @var array
     */
    protected $lcs = array();
    /**
     * Set to true, when two answers are equal
     *
     * If two answers are equal, we should not care about results - because a student response is
     * correct
     *
     * @var bool
     */
    protected $aretokensequencesequal = false;

    /**
     * A string with processed enumerations
     * @var null|block_formal_langs_processed_string
     */
    protected $enumcorrectstring = null;

    /**
     * Array, where keys are indexes from string , filled by enum analyzer
     * and values, are indexes from correct string
     * @var array
     */
    protected $enumcorrecttocorrect = array();

    /**
     * A mistake set for arrays
     *
     * @var array
     */
    protected $mistakes = array();

    /**
     * Holds sequence of scanned analyzers to know how far we should go
     * @var array
     */
    public $analyzersequence = array();


    /**
     * Returns mapped index from correct string to enum correct string
     * @param int $index index part
     * @return int resulting index
     */
    public function map_from_correct_string_to_enum_correct_string($index) {
        return $this->map($index, $this->enumcorrecttocorrect, true, $index);
    }

    /**
     * Returns mapped index from enum correct string to correct string
     * @param $index
     * @return int
     */
    public function map_from_enum_correct_string_to_correct_string($index) {
        return $this->map($index, $this->enumcorrecttocorrect, false, $index);
    }


    /**
     * Returns matches as set by enum analyzer
     * @return arrray|null
     */
    public function enum_correct_to_correct() {
        return $this->enumcorrecttocorrect;
    }

    /**
     * A matches, as set by enum analyzer
     * @param array $matches
     */
    public function set_enum_correct_to_correct($matches) {
        $this->enumcorrecttocorrect = $matches;
    }

    /**
     * Returns processed string, returned enum_analyzer.
     * It's like corrected, but with enum positions swapped
     * @return block_formal_langs_processed_string|null
     */
    public function enum_correct_string()  {
        if ($this->enumcorrectstring == null) {
            return $this->correctstring();
        }
        return $this->enumcorrectstring;
    }

    /**
     * Sets processed correct string, returned by enum analyzer
     * It's like corrected, but with enum positions swapped
     * @param block_formal_langs_processed_string $string
     */
    public function set_enum_correct_string($string) {
        $this->enumcorrectstring = $string;
    }

    public function __clone() {
        parent::__clone();
        if (is_object($this->enumcorrectstring)) {
            $this->enumcorrectstring = clone $this->enumcorrectstring;
        }
        $oldmistakes = $this->mistakes;
        if (is_array($oldmistakes)) {
            foreach($oldmistakes as $type => $mistakes) {
                $this->mistakes[$type] = array();
                if (count($mistakes)) {
                    foreach($mistakes as $mistake) {
                        $this->mistakes[$type][] = $mistake;
                    }
                }
            }
        }
    }

    public function are_strings_equal() {
        return $this->aretokensequencesequal;
    }

    public function assert_that_strings_are_equal() {
        $this->aretokensequencesequal = true;
    }

    public function mistakes() {
        $result = array();
        foreach($this->mistakes as $type => $mistakes) {
            if (count($result) == 0) {
                $result = $mistakes;
            } else {
                $result = array_merge($result, $mistakes);
            }
        }
        return $result;
    }

    public function set_mistakes($mistakes) {
        $this->mistakes = array();
        $this->append_mistakes($mistakes);
    }


    public function append_mistake($mistake) {
        $type = get_class($mistake);
        if (array_key_exists($type, $this->mistakes) == false) {
            $this->mistakes[$type] = array();
        }
        $this->mistakes[$type][] = $mistake;
    }

    public function append_mistakes($mistakes) {
        if (count($mistakes) != 0) {
            foreach($mistakes as $mistake) {
                $this->append_mistake($mistake);
            }
        }
    }

    public function mistakes_by_type($type) {
        $totaltype = 'qtype_correctwriting_' . $type;
        $result = array();
        if (array_key_exists($totaltype, $this->mistakes)) {
            $result = $this->mistakes[$totaltype];
        }
        return $result;
    }

    public function set_mistakes_by_type($type, $set) {
        $totaltype = 'qtype_correctwriting_' . $type;
        $this->mistakes[$totaltype] = $set;
    }

    /**
     * Creates a new string as a copy of this with a lcs
     * @param array $lcs LCS
     * @return qtype_correctwriting_string_pair
     */
    public function copy_with_lcs($lcs) {
        $pair = clone $this;
        $pair->lcs = $lcs;
        return $pair;
    }

    /**
     * Returns an LCS for tokens
     * @return array
     */
    public function lcs() {
        return $this->lcs;
    }



}