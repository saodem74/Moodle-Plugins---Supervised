<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/gradertest/questiontype.php');


/**
 * Represents a numerical question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gradertest_question extends question_graded_automatically {
    
    public $answers = array();

    
    public $unitdisplay;
    
    public $unitgradingtype;
    
    public $unitpenalty;

    
    public $ap;

    public function get_expected_data() {
        $expected = array('answer' => PARAM_RAW_TRIMMED);
        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            $expected['unit'] = PARAM_RAW_TRIMMED;
        }
        return $expected;
    }

    public function start_attempt(question_attempt_step $step, $variant) {
        //$step->set_qt_var('_separators',
        //        $this->ap->get_point() . '$' . $this->ap->get_separator());
    }

    public function apply_attempt_state(question_attempt_step $step) {
        list($point, $separator) = explode('$', $step->get_qt_var('_separators'));
                $this->ap->set_characters($point, $separator);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            $resp = $response['answer'];
        } else {
            $resp = null;
        }

        if ($this->unitdisplay == qtype_numerical::UNITSELECT && !empty($response['unit'])) {
            $resp = $this->ap->add_unit($resp, $response['unit']);
        }

        return $resp;
    }

    public function is_gradable_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0' || $response['answer'] === 0);
    }

    public function is_complete_response(array $response) {
        if (!$this->is_gradable_response($response)) {
            return false;
        }

        list($value, $unit) = $this->ap->apply_units($response['answer']);
        if (is_null($value)) {
            return false;
        }

        if ($this->unitdisplay != qtype_numerical::UNITINPUT && $unit) {
            return false;
        }

        if ($this->unitdisplay == qtype_numerical::UNITSELECT && empty($response['unit'])) {
            return false;
        }

        return true;
    }

    public function get_validation_error(array $response) {
        if (!$this->is_gradable_response($response)) {
            return get_string('pleaseenterananswer', 'qtype_numerical');
        }

        list($value, $unit) = $this->ap->apply_units($response['answer']);
        if (is_null($value)) {
            return get_string('invalidnumber', 'qtype_numerical');
        }

        if ($this->unitdisplay != qtype_numerical::UNITINPUT && $unit) {
            return get_string('invalidnumbernounit', 'qtype_numerical');
        }

        if ($this->unitdisplay == qtype_numerical::UNITSELECT && empty($response['unit'])) {
            return get_string('unitnotselected', 'qtype_numerical');
        }

        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        if (!question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer')) {
            return false;
        }

        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'unit');
        }

        return false;
    }

    public function get_correct_response() {
        $answer = $this->get_correct_answer();
        if (!$answer) {
            return array();
        }

        $response = array('answer' => $answer->answer);

        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            $response['unit'] = $this->ap->get_default_unit();
        } else if ($this->unitdisplay == qtype_numerical::UNITINPUT) {
            $response['answer'] = $this->ap->add_unit($answer->answer);
        }

        return $response;
    }

    /**
     * Get an answer that contains the feedback and fraction that should be
     * awarded for this resonse.
     * @param number $value the numerical value of a response.
     * @return question_answer the matching answer.
     */
    public function get_matching_answer($value) {
        foreach ($this->answers as $aid => $answer) {
            if ($answer->within_tolerance($value)) {
                $answer->id = $aid;
                return $answer;
            }
        }
        return null;
    }

    public function get_correct_answer() {
        foreach ($this->answers as $answer) {
            $state = question_state::graded_state_for_fraction($answer->fraction);
            if ($state == question_state::$gradedright) {
                return $answer;
            }
        }
        return null;
    }

    public function apply_unit_penalty($fraction, $unit) {
        if (!empty($unit) && $this->ap->is_known_unit($unit)) {
            return $fraction;
        }

        if ($this->unitgradingtype == qtype_numerical::UNITGRADEDOUTOFMARK) {
            $fraction -= $this->unitpenalty * $fraction;
        } else if ($this->unitgradingtype == qtype_numerical::UNITGRADEDOUTOFMAX) {
            $fraction -= $this->unitpenalty;
        }
        return max($fraction, 0);
    }

    public function grade_response(array $response) {
        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            $selectedunit = $response['unit'];
        } else {
            $selectedunit = null;
        }
        list($value, $unit) = $this->ap->apply_units($response['answer'], $selectedunit);
        $answer = $this->get_matching_answer($value);
        if (!$answer) {
            return array(0, question_state::$gradedwrong);
        }

        $fraction = $this->apply_unit_penalty($answer->fraction, $unit);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function classify_response(array $response) {
        if (empty($response['answer'])) {
            return array($this->id => question_classified_response::no_response());
        }

        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            $selectedunit = $response['unit'];
        } else {
            $selectedunit = null;
        }
        list($value, $unit) = $this->ap->apply_units($response['answer'], $selectedunit);
        $ans = $this->get_matching_answer($value);
        if (!$ans) {
            return array($this->id => question_classified_response::no_response());
        }

        $resp = $response['answer'];
        if ($this->unitdisplay == qtype_numerical::UNITSELECT) {
            $resp = $this->ap->add_unit($resp, $unit);
        }

        return array($this->id => new question_classified_response($ans->id,
                $resp,
                $this->apply_unit_penalty($ans->fraction, $unit)));
    }

    public function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $qa->get_question()->get_matching_answer(array('answer' => $currentanswer));
            $answerid = reset($args); // itemid is answer id.
            return $options->feedback && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}


/**
 * Subclass of {@link question_answer} with the extra information required by
 * the numerical question type.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_answer extends question_answer {
    /** @var float allowable margin of error. */
    public $tolerance;
    /** @var integer|string see {@link get_tolerance_interval()} for the meaning of this value. */
    public $tolerancetype = 2;

    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat, $tolerance) {
        parent::__construct($id, $answer, $fraction, $feedback, $feedbackformat);
        $this->tolerance = abs($tolerance);
    }

    public function get_tolerance_interval() {
        if ($this->answer === '*') {
            throw new coding_exception('Cannot work out tolerance interval for answer *.');
        }

        // We need to add a tiny fraction depending on the set precision to make
        // the comparison work correctly, otherwise seemingly equal values can
        // yield false. See MDL-3225.
        $tolerance = (float) $this->tolerance + pow(10, -1 * ini_get('precision'));

        switch ($this->tolerancetype) {
            case 1: case 'relative':
                $range = abs($this->answer) * $tolerance;
                return array($this->answer - $range, $this->answer + $range);

            case 2: case 'nominal':
                $tolerance = $this->tolerance + pow(10, -1 * ini_get('precision')) *
                        max(1, abs($this->answer));
                return array($this->answer - $tolerance, $this->answer + $tolerance);

            case 3: case 'geometric':
                $quotient = 1 + abs($tolerance);
                return array($this->answer / $quotient, $this->answer * $quotient);

            default:
                throw new coding_exception('Unknown tolerance type ' . $this->tolerancetype);
        }
    }

    public function within_tolerance($value) {
        if ($this->answer === '*') {
            return true;
        }
        list($min, $max) = $this->get_tolerance_interval();
        return $min <= $value && $value <= $max;
    }
}
