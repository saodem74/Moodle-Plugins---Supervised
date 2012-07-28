<?php
/**
 * Defines the editing form for the preg question type.
 *
 * @copyright &copy; 2008  Sychev Oleg
 * @author Sychev Oleg, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
require_once($CFG->dirroot.'/question/type/shortanswer/edit_shortanswer_form.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');
/**
 * preg editing form definition.
 */
class qtype_preg_edit_form extends qtype_shortanswer_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner($mform) {
        global $CFG;

        question_bank::load_question_definition_classes($this->qtype());
        $qtypeclass = 'qtype_'.$this->qtype();
        $qtype = new $qtypeclass;

        $engines = $qtype->available_engines();
        $mform->addElement('select','engine',get_string('engine','qtype_preg'),$engines);
        $mform->setDefault('engine',$CFG->qtype_preg_defaultengine);
        $mform->addHelpButton('engine','engine','qtype_preg');

        $notations = $qtype->available_notations();
        $mform->addElement('select','notation', get_string('notation', 'qtype_preg'), $notations);
        $mform->setDefault('notation', $CFG->qtype_preg_defaultnotation);
        $mform->addHelpButton('notation', 'notation', 'qtype_preg');

        $mform->addElement('selectyesno', 'usecharhint', get_string('usecharhint','qtype_preg'));
        $mform->setDefault('usecharhint',0);
        $mform->addHelpButton('usecharhint','usecharhint','qtype_preg');
        $mform->addElement('text', 'charhintpenalty', get_string('charhintpenalty','qtype_preg'), array('size' => 3));
        $mform->setDefault('charhintpenalty','0.2');
        $mform->setType('charhintpenalty', PARAM_NUMBER);
        $mform->addHelpButton('charhintpenalty','charhintpenalty','qtype_preg');

        $mform->addElement('selectyesno', 'uselexemhint', get_string('uselexemhint','qtype_preg'));
        $mform->setDefault('uselexemhint',0);
        $mform->addHelpButton('uselexemhint','uselexemhint','qtype_preg');
        $mform->addElement('text', 'lexemhintpenalty', get_string('lexemhintpenalty','qtype_preg'), array('size' => 3));
        $mform->setDefault('lexemhintpenalty','0.4');
        $mform->setType('lexemhintpenalty', PARAM_NUMBER);
        $mform->addHelpButton('lexemhintpenalty','lexemhintpenalty','qtype_preg');
        $langs = block_formal_langs::available_langs();//TODO - add context
        $mform->addElement('select','langid',get_string('langselect','qtype_preg'),$langs);
        $mform->setDefault('langid',2);//TODO - add admin setting
        $mform->addHelpButton('langid','langselect','qtype_preg');
        $mform->addElement('text', 'lexemusername', get_string('lexemusername','qtype_preg'), array('size' => 54));
        $mform->setDefault('lexemusername','word');
        $mform->addHelpButton('lexemusername','lexemusername','qtype_preg');
        $mform->setAdvanced('lexemusername');

        $creategrades = get_grade_options();
        $mform->addElement('select','hintgradeborder',get_string('hintgradeborder','qtype_preg'),$creategrades->gradeoptions);
        $mform->setDefault('hintgradeborder',1);
        $mform->addHelpButton('hintgradeborder','hintgradeborder','qtype_preg');
        $mform->setAdvanced('hintgradeborder');

        $mform->addElement('selectyesno', 'exactmatch', get_string('exactmatch','qtype_preg'));
        $mform->addHelpButton('exactmatch','exactmatch','qtype_preg');
        $mform->setDefault('exactmatch',1);

        $mform->addElement('text', 'correctanswer', get_string('correctanswer','qtype_preg'), array('size' => 54));
        $mform->addHelpButton('correctanswer','correctanswer','qtype_preg');

        //Set hint availability determined by engine capabilities
        foreach ($engines as $engine => $enginename) {
            $questionobj = new qtype_preg_question;
            $querymatcher = $questionobj->get_query_matcher($engine);
            if (!$querymatcher->is_supporting(qtype_preg_matcher::PARTIAL_MATCHING) ||
                !$querymatcher->is_supporting(qtype_preg_matcher::CORRECT_ENDING)
                ) {
                $mform->disabledIf('hintgradeborder','engine', 'eq', $engine);
                $mform->disabledIf('usecharhint','engine', 'eq', $engine);
                $mform->disabledIf('charhintpenalty','engine', 'eq', $engine);
                $mform->disabledIf('uselexemhint','engine', 'eq', $engine);
                $mform->disabledIf('lexemhintpenalty','engine', 'eq', $engine);
                $mform->disabledIf('langid','engine', 'eq', $engine);
                $mform->disabledIf('lexemusername','engine', 'eq', $engine);
            }
        }

        parent::definition_inner($mform);

        $answersinstruct = $mform->getElement('answersinstruct');
        $answersinstruct->setText(get_string('answersinstruct', 'qtype_preg'));

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $trimmedcorrectanswer = trim($data['correctanswer']);
        //If no correct answer is entered, we should think it is correct to not force techer; otherwise we must check that it match with at least one 100% grade answer.
        $correctanswermatch = ($trimmedcorrectanswer=='');
        $passhintgradeborder = false;
        $fractions = $data['fraction'];

        //Fill in some default data that could be absent due to disabling relevant form controls
        if (!array_key_exists('hintgradeborder', $data)) {
            $data['hintgradeborder'] = 1;
        }

        if (!array_key_exists('usecharhint', $data)) {
            $data['usecharhint'] = false;
        }

        if (!array_key_exists('uselexemhint', $data)) {
            $data['uselexemhint'] = false;
        }

        $i = 0;
        question_bank::load_question_definition_classes($this->qtype());
        $questionobj = new qtype_preg_question;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $hintused = ($data['usecharhint'] || $data['uselexemhint']) && $fractions[$key] >= $data['hintgradeborder'];
                //Not using exactmatch option to not confuse user in error messages by things it adds to regex.
                $matcher = $questionobj->get_matcher($data['engine'], $trimmedanswer, /*$data['exactmatch']*/false, $data['usecase'], (-1)*$i, $data['notation'], $hintused);
                if($matcher->is_error_exists()) {//there are errors in the matching process
                    $regexerrors = $matcher->get_errors();
                    $errors['answer['.$key.']'] = '';
                    $i=0;
                    $maxerrors = 5;
                    if (isset($CFG->qtype_preg_maxerrorsshown)) {//show no more than max errors
                        $maxerrors = $CFG->qtype_preg_maxerrorsshown;
                    }
                    foreach ($regexerrors as $regexerror) {
                        if ($i < $maxerrors) {
                            $errors['answer['.$key.']'] .= $regexerror.'<br />';
                        }
                        $i++;
                    }
                    if ($i > $maxerrors) {
                        $errors['answer['.$key.']'] .= get_string('toomanyerrors', 'qtype_preg' , $i - $maxerrors).'<br />';
                    }
                } elseif ($trimmedcorrectanswer != '' && $data['fraction'][$key] == 1 && $matcher->match($trimmedcorrectanswer)->full) {
                    $correctanswermatch=true;
                }
                if ($fractions[$key] >= $data['hintgradeborder']) {
                    $passhintgradeborder = true;
                }
            }
            $i++;
        }

        if ($correctanswermatch == false) {
            $errors['correctanswer']=get_string('nocorrectanswermatch','qtype_preg');
        }

        if ($passhintgradeborder == false && $data['usecharhint']) {//no asnwer pass hint grade border
            $errors['hintgradeborder']=get_string('nohintgradeborderpass','qtype_preg');
        }

        $querymatcher = $questionobj->get_query_matcher($data['engine']);
        //If engine doesn't support subpattern capturing, than no placeholders should be in feedback
        if (!$querymatcher->is_supporting(qtype_preg_matcher::SUBPATTERN_CAPTURING)) {
            $feedbacks = $data['feedback'];
            foreach ($feedbacks as $key => $feedback) {
                if (is_array($feedback)) {//On some servers feedback is HTMLEditor, on another it is simple text area
                    $feedback = $feedback['text'];
                }
                if (!empty($feedback) && preg_match('/\{\$([1-9][0-9]*|\w+)\}/', $feedback) == 1) {
                    $errors['feedback['.$key.']'] = get_string('nosubpatterncapturing','qtype_preg',$querymatcher->name());
                }
            }
        }

        return $errors;
    }

    function qtype() {
        return 'preg';
    }
}
?>
