<?php
/**
 * Defines an implementation of mistakes, that are determined by computing LCS and comparing answer and response
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from sequence_analyzer
class qtype_correctwriting_sequence_mistake extends qtype_correctwriting_response_mistake {

}


// A mistake, that consists from moving one lexeme to different position, than original
class qtype_correctwriting_lexeme_moved_mistake extends qtype_correctwriting_sequence_mistake {
   /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     * @param int    $responseindex index of response token
     */
   public function __construct($language,$answer,$answerindex,$response,$responseindex) {
       $this->languagename = $language->name();
        
       $this->answer = $answer->stream->tokens;
       $this->response = $response->stream->tokens;
        
       $this->position = $this->response[$responseindex]->position();
        
       //Fill answer data
       $this->answermistaken = array();
       $this->answermistaken[] = $answerindex;
       //Fill response data
       $this->responsemistaken = array();
       $this->responsemistaken[] = $responseindex;
        
       //Create a mistake message
       $a = new stdClass();
       if ($answer->has_description($answerindex)) {
           $a->description = $answer->node_description($answerindex);
           $this->mistakemsg = get_string('movedmistakemessage','qtype_correctwriting',$a);
       } else {
           $a->value = $this->answer[$answerindex]->value();
           $a->line = $this->answer[$answerindex]->position()->linestart();
           $a->position = $this->response[$answerindex]->position()->colstart();
           $this->mistakemsg = get_string('movedmistakemessagenodescription','qtype_correctwriting',$a);
       }
   }
}

// A mistake, that consists from adding a lexeme to response, that is not in answer
class qtype_correctwriting_lexeme_added_mistake extends qtype_correctwriting_sequence_mistake {
   /**
    * Constructs a new error, filling it with constant message
    * @param object $language      a language object
    * @param array  $answer        answer tokens
    * @param array  $response      array response tokens
    * @param int    $responseindex index of response token
    */
   public function __construct($language,$answer,$response,$responseindex) { 
        $this->languagename = $language->name();
        
        $this->answer = $answer->stream->tokens;
        $this->response = $response->stream->tokens;
        
        $this->position = $this->response[$responseindex]->position();
        //Fill answer data
        $this->answermistaken = array();
        //Fill response data
        $this->responsemistaken = array();
        $this->responsemistaken[] = $responseindex;
        
        //Create a mistake message
        $a = new stdClass();
        $a->value = $this->response[$responseindex]->value();
        $a->line = $this->position->linestart();
        $a->position = $this->position->colstart();
        $this->mistakemsg = get_string('addedmistakemessage','qtype_correctwriting',$a);
   }
}

// A mistake, that consists of  skipping a lexeme from answer
class qtype_correctwriting_lexeme_absent_mistake extends qtype_correctwriting_sequence_mistake {
    /**
     * Constructs a new error, filling it with constant message
     * @param object $language      a language object
     * @param array  $answer        answer tokens
     * @param int    $answerindex   index of answer token
     * @param array  $response      array response tokens
     */
    public function __construct($language,$answer,$answerindex,$response) {
       $this->languagename = $language->name();
        
       $this->answer = $answer->stream->tokens;
       $this->response = $response->stream->tokens;
        
       $this->position = $this->answer[$answerindex]->position();
       //Fill answer data
       $this->answermistaken=array();
       $this->answermistaken[] = $answerindex;
       //Fill response data
       $this->responsemistaken = array();
        
       //Create a mistake message
       $a = new stdClass();
       if ($answer->has_description($answerindex)) {
           $a->description = $answer->node_description($answerindex);
           $this->mistakemsg = get_string('absentmistakemessage','qtype_correctwriting',$a);
       } else {
           $a->value = $answer->stream->tokens[$answerindex]->value();
           $this->mistakemsg = get_string('absentmistakemessagenodescription','qtype_correctwriting',$a);
       }
    }
}

?>