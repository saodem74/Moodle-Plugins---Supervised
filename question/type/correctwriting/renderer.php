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
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
/**
 * Generates the output for short answer questions.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_renderer extends qtype_shortanswer_renderer {

    /**
     * Overloading feedback method to pass options to specific_feedback
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        $output = '';
        if ($options->feedback) {
            $output .= $this->specific_feedback_with_options($qa, $options);
        }

        $output .= parent::feedback($qa, $options);

        return $output;
    }

    protected function specific_feedback_with_options(question_attempt $qa, question_display_options $options) {
        global $PAGE;
        $question = $qa->get_question();
        $shortanswerfeedback = parent::specific_feedback($qa);
        $myfeedback = '';
        $analyzer = $question->matchedanalyzer;
        $br = html_writer::empty_tag('br');

        $currentanswer = $qa->get_last_qt_var('answer');
        if(!$currentanswer) {
            $currentanswer = '';
        }
        $hints = $question->available_specific_hints(array('answer' => $currentanswer));
        if ($analyzer!=null) {
            //Output mistakes messages
            if (count($analyzer->mistakes()) > 0) {
                $mistakescnt = count($analyzer->mistakes());
                if ($mistakescnt == 1) {
                    $myfeedback = get_string('foundmistake', 'qtype_correctwriting');
                } else {
                    $myfeedback = get_string('foundmistakes', 'qtype_correctwriting');
                }
                $myfeedback .= $br;

                $i = 1;
                $behaviour = $qa->get_behaviour();
                $behaviourrenderer =$behaviour->get_renderer($PAGE);
                foreach($analyzer->mistakes() as $mistake) {
                    //Render mistake message.
                    $msg = $i.') '.$mistake->get_mistake_message();
                    if ($i < $mistakescnt) {
                        $msg .= ';';
                    } else {
                        $msg .= '.';
                    }
                    //Render "what is" hint button or hint.
                    $hintkey = 'hintwhatis_' . $mistake->mistake_key();
                    if (array_key_exists($hintkey, $hints)) {//There is "what is" hint for that mistake.
                        $hintobj = $question->hint_object($hintkey, array('answer' => $currentanswer));

                        if (is_object($hintobj)) {//There could be no hint object if response was changed in adaptive behaviour.
                            if ($qa->get_last_step()->has_behaviour_var('_render_'.$hintkey)) {//Hint is requested, so render hint.
                                $msg .= $br . $hintobj->render_hint($this, array('answer' => $currentanswer));
                            } else if ($hintobj->hint_available(array('answer' => $currentanswer))){//Hint is not requested, render button to be able to request it.
                                $msg .= $br . $behaviourrenderer->render_hint_button($qa, $options, $hintobj);
                            }
                        }
                    }
                    $myfeedback .= $msg;
                    $myfeedback .= $br;
                    $i++;
                }
            }
        }
        return $myfeedback . $shortanswerfeedback;
   }

   //This wil be shown only if show right answer is setup
   public function correct_response(question_attempt $qa) {
       global $CFG;
       $question = $qa->get_question();
       $resulttext  = html_writer::empty_tag('br');
       // This data should contain base64_encoded data about user mistakes
       $analyzer = $question->matchedanalyzer;
       if ($analyzer!=null) {
           if (count($analyzer->mistakes()) != 0) {
               $mistakecodeddata = $question->create_image_information($analyzer);
               $url  = $CFG->wwwroot . '/question/type/correctwriting/mistakesimage.php?data=' . urlencode($mistakecodeddata);
               $imagesrc = html_writer::empty_tag('image', array('src' => $url));
               $resulttext = $imagesrc . $resulttext;
           }
       }
       // TODO: Uncomment if we need original shortanswer hint
       return $resulttext /*. parent::correct_response($qa) */;
   }

}