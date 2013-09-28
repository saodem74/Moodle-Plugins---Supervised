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
 * Defines class of abstract analyzer for correct writing question.
 *
 * Abstract analyzer class defines an interface any analyzer should implement.
 * Analyzers have state, i.e. for each analyzed pair of strings there will be differrent analyzer
 *
 * @copyright &copy; 2013  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');

abstract class qtype_correctwriting_abstract_analyzer {
    /**
     * A reference to the question object with necessary data (language id, answers, threshold etc).
     * @var qtype_correctwriting_question 
     */
    protected $question;

    /**
     * Language object - contains scaner, parser etc.
     * @var block_formal_langs_abstract_language child classes.
     */
    protected $language;

    /**
     * String pair, passed as input data for the analyzer.
     * @var qtype_correctwriting_string_pair 
     */
    protected $basestringpair;


    /**
     * Best (judging by fitness) string pairs generated as result of analyzer's work.
     *
     * Analyzer should return several string pairs only if they are equivalent from it's point of view.
     * An empty array means error, that don't allow subsequent analyzers to work.
     * @var array of qtype_correctwriting_string_pair 
     */
    protected $resultstringpairs = array();

    /**
     * Mistakes for resulting string pairs; for each pair should be corresponding mistakes array with equivalent index.
     *
     * @var array of arrays of qtype_correctwriting_response_mistake child classes 
     */
    protected $resultmistakes = array();

    /**
     * Returns analyzer internal name, which can be used as an argument to get_string().
     */
    abstract public function name();

    /**
     * Do all processing and fill resultstringpairs and resultmistakes fields.
     *
     * You are normally don't want to overload it. Overload analyze() and bypass() instead.
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     * When called without params just creates empty object to call analyzer-dependent functions on.
     * @throws moodle_exception if invalid number of string pairs
     * @param qtype_correctwriting_question $question
     * @param qtype_correctwriting_string_pair $basepair a pair, passed as input
     * @param block_formal_langs_abstract_language $language a language
     * @param bool $bypass false if analyzer should work, true if it should just allow subsequent analyzers to work.
     */
    public function __construct($question = null, $basepair = null, $language = null, $bypass = true) {
        if ($question === null) {
            return;
        }
        $this->question = $question;
        $this->language = $language;
        $this->basestringpair = $basepair;
        if ($bypass) {
            $this->bypass();
        } else {
            $this->analyze();
            if (count($this->resultstringpairs) != count($this->resultmistakes)) {
                throw new moodle_exception('Invalid number of string pairs or mistake from CorrectWriting question analyzer '// TODO - make a language string and normal exception.
                                            . get_string($this->name(), 'qtype_correctwriting'));
            }
            if (count($this->resultstringpairs) == 0) {
                throw new moodle_exception('There must be at least one output pair in '
                                           . get_string($this->name(), 'qtype_correctwriting'));
            }
        }
    }

    /**
     * Do real analyzing and fill resultstringpairs and resultmistakes fields.
     *
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     */
    abstract protected function analyze();

    /**
     * Fill resultstringpairs with a string pair, that simulates work of this analyzer allowing subsequent analyzers to work.
     *
     * You are normally would overload this, starting overload with parent function call, then add you work.
     * Don't actually analyze something, no mistakes generated: just fill necessary fields in string pair.
     */
    protected function bypass() {
        $this->resultmistakes[] = array();// Add an empty mistakes array.
        $this->resultstringpairs[] = clone $this->basestringpair; //Clone string pair for future use.
    }

    /**
     * Returns resulting string pairs array.
     */
    public function result_pairs() {
        return $this->resultstringpairs;
    }

    /**
     * Returns resulting mistakes array.
     */
    public function result_mistakes() {
        return $this->resultmistakes;
    }

    /**
     * Returns fitness as aggregate measure of how students response fits this particular answer - i.e. more fitness = less mistakes.
     * Used to choose best matched answer.
     * Fitness is negative or zero (no errors, full match).
     * Fitness doesn't necessary equivalent to the number of mistakes as each mistake could have different weight.
     * Each analyzer will calculate fitness only for it's own mistakes, ignoring mistakes from other analyzers.
     * Dev. comment: since all mistakes have weight, we can have common algorithm as reduction operation
     * on this mistakes. It can be replaced, by do anyone care?
     * @param array $mistakes of qtype_correctwriting_response_mistake child classes $mistakes Mistakes to calculate fitness from, can be empty array.
     * @return double
     */
    public function fitness($mistakes) {
        $result = 0;
        if (count($mistakes)) {
            /** qtype_correctwriting_response_mistake $mistake */
            foreach($mistakes as $mistake) {
                $result += $mistake->weight;
            }
        }
        return $result * -1;
    }

    /**
     * Returns an array of hint keys, supported by mistakes from this analyzer.
     */
    abstract public function supported_hints();

    // Question editing form and DB methods starts there.

    /**
     * Returns an array of extra_question_fields used by this analyzer.
     */
     public function extra_question_fields() {
        return array();
     }

    /**
     * Returns array of floating point fields for the form. Subsequent commentaries comments keys:
     * 'name' => field name, there should be label as get_string('name', 'qtype_correctwriting') and help as get_string('name_help', 'qtype_correctwriting')
     * 'default' => default value for the form field
     * 'advanced' => boolean value - whether field is advanced one
     * 'min', 'max' => limits for the field value
     */
    public function float_form_fields() {
        return array();
    }

    /**
     * Called from edit_correctwriting_form::definition_inner() within form section for this analyzer.
     * You will typically call parent, then add other fields.
     * @param MoodleQuickForm $mform
     */
    public function form_section_definition(&$mform) {
        foreach ($this->float_form_fields() as $params) {
            $mform->addElement('text', $params['name'], get_string($params['name'], 'qtype_correctwriting'), array('size' => 6));
            $mform->setType($params['name'], PARAM_FLOAT);
            $mform->setDefault($params['name'], $params['default']);
            if ($params['required']) {
                $mform->addRule($params['name'], null, 'required', null, 'client');
            }
            $mform->addHelpButton($params['name'], $params['name'], 'qtype_correctwriting');
            if ($params['advanced']) {
                $mform->setAdvanced($params['name']);
            }
        }
    }

    /**
     * Called from edit_correctwriting_form::data_preprocessing
     */
    public function form_section_data_preprocessing($question) {
        return $question;
    }

    /**
     * Called from edit_correctwriting_form::validation
     */
    public function form_section_validation ($data, $files) {
        $errors = array();
        return $errors;
    }

    /**
     * If this analyzer requires some other ones to work, not bypass - return an array of such analyzers names.
     */
    public function require_analyzers() {
        return array();
    }

    /**
     * Returns if the language is compatible with this analyzer.
     * I.e. syntax analyzer compatible only with parser containing languages.
     * @param block_formal_langs_abstract_language $lang a language object from block_formal_langs
     * @return boolean
     */
    public function is_lang_compatible($lang) {
        return true; // Accept all by default.
    }

    /**
     * Allows analyzer to replace mistakes from other analyzer.
     * For example syntax_analyzer can replace mistakes from sequence_analyzer.
     *
     * Types of mistakes should be matched against other with replaces_mistake_types.
     * @return array
     */
    public function replaces_mistake_types() {
        return array();
    }

    /**
     * Whether we should filter mistake from list of mistakes.
     * Called if replaces_mistake_types returns one mistake
     * @param qtype_correctwriting_response_mistake  $mistake
     * @return boolean
     */
    public function should_mistake_be_removed($mistake) {
        return false;
    }

}

/**
 * @class qtype_correctwriting_analysis_results
 *
 * This class encapsulates all results of analyzer, providing possibility to work with mistakes,
 * and ending corrected response.
 *
 * Also this class encapsulates algorithm for comparing two pairs of strings, building set of mistakes
 * by full-scan of tree of all possible analyzer combinations.
 */
class qtype_correctwriting_analysis_results {
    /**
     * A question. All analyzers should work with this question
     * @var qtype_correctwriting_question
     */
    protected $question = null;
    /**
     * Language for working with pairs
     * @var block_formal_langs_abstract_language
     */
    protected $language = null;
    /**
     * Set to true, when two answers are equal
     *
     * If two answers are equal, we should not care about results - because a student response is
     * correct
     *
     * @var bool
     */
    protected $equal = false;
    /**
     * Set of results from collected from each
     * analyzer, defined as array of stdClass with following fields
     * int index - index of string pair with best fitness for analyzer
     * qtype_correctwriting_abstract_analyzer analyzer - analyzer of this part
     * array set - mistake set, chosen from analyzer and cleaned from replaced mistakes
     * @var bool|array
     */
    protected $results = false;
    /**
     * Creates new results
     * @param qtype_correctwriting_question $question  a question
     * @param block_formal_langs_string_pair $pair a pair of strings
     * @param block_formal_langs_abstract_language $language  a language
     */
    public function __construct($question, $pair, $language) {
        $this->question = $question;
        $this->stringpair = $pair;
        $this->language = $language;
        $this->equal = $this->question->are_lexeme_sequences_equal($pair);
        if ($this->equal == false) {
            $this->perform_deep_analysis($pair);
        }
    }

    /**
     * Returns a mistake set, collecting it from all of analyzers
     * @return array mistakes
     */
    public function mistakes() {
        $result = array();
        if ($this->equal == false) {
            foreach($this->results as $analysisresult) {
                $mistakeset = $analysisresult->set;
                if (count($result) == 0)  {
                    $result = $mistakeset;
                }  else {
                    if (count($mistakeset) != 0) {
                        $result = array_merge($result, $mistakeset);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Returns a corrected response string pair, taking it from last analyzer, since last analyzer should contain
     * all fixed results
     * @return block_formal_langs_processed_string
     */
    public function get_corrected_response() {
        if ($this->equal)
            return $this->stringpair->correctedstring();

        // We take last result, because first string can be fixed multiple times by other
        // analyzers, and only last analyzer has this string.

        /** @var stdClass $lastresult */
        $lastresult = $this->results[count($this->results) - 1];
        /** @var qtype_correctwriting_abstract_analyzer $analyzer */
        $analyzer = $lastresult->analyzer;
        $pairs = $analyzer->result_pairs();
        // An index contains a needed index of row
        /** @var qtype_correctwriting_string_pair $pair */
        $pair = $pairs[$lastresult->index];
        return $pair->correctedstring();
    }

    /**
     * Peforms deep analysis using depth-first scanning
     * for all possible combinations of allowed analysers
     * @param qtype_correctwriting_string_pair $pair a results will be built for this pair
     */
    protected function perform_deep_analysis($pair) {
        $this->results = $this->perform_depth_first_scan(0, $pair);
    }

    /**
     * Performs recursive depth first scan, working with analyzer tree and trying to hold this tree
     * as small as possible. This function should build a result set, which can be saved into results field
     * @throws moodle_exception No mistake sets! - if analyzer, which is implemented is not valid and does not have
     * at least one mistake sets
     * @param $index
     * @param $stringpair
     * @return array of stdClass pairs < analyzer, index -  index of most efficient string, set - set of mistakes>
     */
    private function perform_depth_first_scan($index, $stringpair) {
        $result = new stdClass();
        $childresults = array();
        /** @var qtype_correctwriting $qtype */
        $qtype = $this->question->qtype;
        $analyzers = array_values($qtype->analyzers());
        $analyzername = $analyzers[$index];
        $createdanalyzername = 'qtype_correctwriting_' . $analyzername;
        $bypass  = $this->question->is_analyzer_enabled($analyzername) == false;
        /** @var qtype_correctwriting_abstract_analyzer $analyzer */
        $analyzer = new $createdanalyzername($this->question, $stringpair, $this->language, $bypass);

        if (count($analyzer->result_mistakes()) == 0)
            throw new moodle_exception('No mistake sets!');


        //  Scan pair with max fitness
        $foundmaxfitness = false;
        $maxfitness = -1;
        $maxfitnessindex = -1;
        $maxfitnessset = array();

        // If this is last analyzer, pick string with largest fitness
        if ($index == count($analyzers) - 1) {
            foreach($analyzer->result_mistakes() as $index => $mistakeset) {
                $fitness = $analyzer->fitness($mistakeset);
                if ($foundmaxfitness == false || $fitness > $maxfitness) {
                    $maxfitness = $fitness;
                    $maxfitnessindex = $index;
                    $maxfitnessset = $mistakeset;
                }
            }
        } else {
            $mymistakesets = $analyzer->result_mistakes();

            foreach($analyzer->result_pairs() as $pairindex => $pair) {
                $childanalyzerresults =  $this->perform_depth_first_scan($index + 1, $pair);
                // Compute fitness, based on results
                $mymistakeset = $mymistakesets[$pairindex];
                $mymistakeset = $this->cleanup_mistake_set($mymistakeset, $childanalyzerresults);
                $fitness = $analyzer->fitness($mymistakeset);

                // Add children fitnesses to have fitness as whole
                /** @var stdClass $childanalyzerresult */
                foreach($childanalyzerresults as $childanalyzerresult) {
                    $fitness += $childanalyzerresult->analyzer->fitness($childanalyzerresult->set);
                }

                if ($foundmaxfitness == false || $fitness > $maxfitness) {
                    $maxfitness = $fitness;
                    $maxfitnessindex = $index;
                    $maxfitnessset = $mymistakeset;
                    $childresults = $childanalyzerresults;
                }
            }

        }

        // Write result
        $result->index = $maxfitnessindex;
        $result->analyzer = $analyzer;
        $result->set = $maxfitnessset;

        $result = array( $result );
        if (count($childresults)) {
            $result = array_merge($result, $childresults);
        }

        return $result;
    }

    /**
     * Cleanups a mistake set of array out of mistakes, replaced by other analyzers.
     * For instance, syntax_analyzer can replace a set of mistakes from sequence_analyzer,
     * but not all. So we need this function to compute fitness properly, without erased mistake - since
     * they will be in mistakes of child analyzer
     *
     * @param array $mymistakeset set of mistakes
     * @param array $childanalyzerresults array of stdClass with other analyzers
     * @return array of mistake, cleaned from other mistakes
     */
    public function cleanup_mistake_set($mymistakeset, $childanalyzerresults) {
        $result = array();
        // Build a map from type to analyzer to filter mistakes
        $filteredtypes = array();
        if (count($childanalyzerresults)) {
            foreach($childanalyzerresults as $childanalyzerresult) {
                /** @var qtype_correctwriting_abstract_analyzer $analyzer */
                $analyzer = $childanalyzerresult->analyzer;
                $analyzertinyname = str_replace('qtype_correctwriting_', '', get_class($analyzer));
                // We don't care for abount non-enabled analyzers, since they don't change
                // mistake set
                if ($this->question->is_analyzer_enabled($analyzertinyname)) {
                    $types = $analyzer->replaces_mistake_types();
                    if (count($types)) {
                        foreach($types as $type) {
                            $filteredtypes[$type] = $analyzer;
                        }
                    }
                }
            }
        }


        if (count($filteredtypes) == 0) {
            $result = $mymistakeset;
        } else {
            if (count($mymistakeset)) {
                // Scan through set and filter mistakes
                foreach($mymistakeset as $mistake) {

                    // Get set of analyzers, that could filter mistake
                    $filteredanalyzers = array();
                    foreach($filteredtypes as $type => $analyzer) {
                        if (is_a($mistake, $type)) {
                            $filteredanalyzers[] = $analyzer;
                        }
                    }

                    if (count($filteredanalyzers)) {
                        // Check if mistake should be removed
                        $shouldberemoved = false;
                        foreach($filteredanalyzers as $analyzer) {
                            $shouldberemoved = $shouldberemoved || $analyzer->should_mistake_be_removed($mistake);
                        }

                        if (!$shouldberemoved) {
                            $result[] = $mistake;
                        }
                    } else {
                        $result[] = $mistake;
                    }

                }
            }
        }
        return $result;
    }

}
