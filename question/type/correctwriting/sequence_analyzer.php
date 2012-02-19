<?php
/**
 * Defines class of sequence analyzer for correct writing question.
 *
 * Sequence analyzer object is created for each possible set of lexical mistakes and 
 * is responsible for finding common parts of answer regarding sequence of tokens.
 * Longest common sequence algorithm is used to determine it.
 *
 * Sequence analyzers create and use syntax analyzers to determine structural mistakes using
 * language grammar. When using grammar analyzer is impossible, it determines sequence mistakes 
 * using lcs, i.e. misplaced, extra and missing tokens.
 * There may be more than one syntax analyzer created if there are several LCS'es of 
 * answer and response.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

//Other necessary requires
require_once($CFG->dirroot.'/question/type/correctwriting/syntax_analyzer.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_mistakes.php');



class  qtype_correctwriting_sequence_analyzer {

    protected $language;             // Language object - contains scaner, parser etc
    protected $errors;               // Array of error objects - teacher errors when entering answer

    protected $answer;               // Array of answer tokens
    protected $correctedresponse;    // Array of response tokens where lexical errors are corrected
    protected $mistakes;             // Array of mistake objects - student errors (structural errors)

    private   $fitness;              // Fitness for response
    
    private   $question;             // Used question by analyzer
    
    private   $movedmistakeweight;   // Moved lexeme error weight
    private   $skippedmistakeweight; // Removed lexeme error weight
    private   $addedmistakeweight;   // Added lexeme error weight
    
    /**
     * Do all processing and fill all member variables
     * Passed response could be null, than object used just to find errors in the answers, token count etc...
     */
    public function __construct($question, $answer, $language, $correctedresponse=null) {
        $this->answer = $answer;
        $this->correctedresponse = $correctedresponse;
        // If question is set null we suppose this is a unit-test mode and don't do stuff
        if ($question != null) {
            $this->language = $language;
            $this->question = $question;
            if ($correctedresponse == null) {
                // Scan errors by syntax_analyzer
                if ($language->could_parse()) {
                    $analyzer = new qtype_correctwriting_syntax_analyzer($answer, $language, null, null);
                    $this->errors = $analyzer->errors();
                } 
            } else {
                // Scan for errors, computing lcs
                $this->scan_response_mistakes();
            }
        }
        //TODO:
        //1. Compute LCS - Mamontov
        //  - lcs function  (done)
        //2. For each LCS create  qtype_correctwriting_syntax_analyzer object - Mamontov (done)
        //  - if there is exception thrown, skip syntax analysis
        //3. Select best fitted syntax_analyzer using their fitness method - Mamontov
        //4. Set array of mistakes accordingly - Mamontov (done)
        //  - if syntax analyzer is able to return mistakes, use it's mistakes
        //  - otherwise generate own mistakes for individual tokens, using lcs_to_mistakes function
        //NOTE: if response is null just check for errors using syntax analyzer- Mamontov (Done)
        //NOTE: if some stage create errors, stop processing right there (done?)
    }
    /**
     * Scans for a mistakes in response, computing lcs and 
     * performing syntax analysis
     */
    private function scan_response_mistakes() {
        // TODO Extract these  values from question
        $this->movedmistakeweight = 1;
        $this->skippedmistakeweight = 1;
        $this->addedmistakeweight = 1;
        
        $alllcs = $this->lcs();
        if (count($alllcs) == 0) {
            // If no LCS found perform searching with empty array
            $this->mistakes = $this->lcs_to_mistakes(array());
        }
        else {
            //Otherwise scan all of lcs
            $maxmistakes = array();
            $maxfitness = 0;
            $isfirst = true;
            
            //Find fitting analyzer
            foreach($alllcs as $currentlcs) {
                $currentmistakes = $this->lcs_to_mistakes($currentlcs);
                if ($isfirst == true or $this->fitness > $maxfitness) { 
                    $maxmistakes = $currentmistakes;
                    $maxfitness = $this->fitness;
                    $isfirst = false;
                }
            }

            
            //Set self-properties to return proper values
            $this->mistakes = $maxmistakes;
            $this->fitness = $maxfitness;
        }
    }
    /**
     * Compute and return longest common subsequence (tokenwise) of answer and corrected response.
     *
     * Array of individual lcs contains answer indexes as keys and response indexes as values.
     * There may be more than one lcs for a given pair of strings.
     * @return array array of individual lcs arrays
     */
    public function lcs() {
        $answer = &$this->answer;
        $response = &$this->correctedresponse;

        
        // An array of matches, where keys are indexes of answer and values are arrays of 
        // indexes from response
        $matches = array();
        // Fill an array of matches filling an lcs data
        $answercount = count($answer);
        $responsecount = count($response);
        // Flag, that determines whether we found a match
        $hasmatch = false;
        for ($i = 0;$i < $answercount;$i++) {
            $matches[$i] = array();
            for ($j = 0;$j < $responsecount;$j++) {
                if ($answer[$i]->is_same($response[$j])) {
                    $matches[$i][] = $j;
                    $hasmatch = true;
                }
            }
        }
    
        // If no matches are found, stop right there
        if ($hasmatch == false) {
            return array();
        }
        // An array of found common subsequences, where a subsequence is hash data, to current subsequence,
        // where ['maxind'] - maximum index, which can be taken when appending 
        //       ['lcs']    - array, which is represented an lcs, as described in description of function
        $tmplcs = array();
    
        // Compute temporary lcs data
        for($currenttoken = $answercount - 1;$currenttoken > -1;$currenttoken--) {
            $newtmplcs = $tmplcs;
            for($currentmatch = 0;$currentmatch < count($matches[$currenttoken]);$currentmatch++) {
                // Scan existing suffixes and push match to it if can, changing maxind to current match
                for ($currentcs = 0;$currentcs < count($tmplcs);$currentcs++) {
                    // If we can append to current match (found symbol index is lesser then bound)
                    if($tmplcs[$currentcs]['maxind'] > $matches[$currenttoken][$currentmatch]) {
                        // Copy suffix and prepend our token to it
                        $suffix = $tmplcs[$currentcs];
                        $suffix['maxind'] = $matches[$currenttoken][$currentmatch];
                        $suffix['lcs'][$currenttoken] = $matches[$currenttoken][$currentmatch];
                        $newtmplcs[] = $suffix;
                    }
                }
                // Create new suffix and add it to a tmplcs
                $suffix['maxind'] = $matches[$currenttoken][$currentmatch];
                $suffix['lcs'] = array();
                $suffix['lcs'][$currenttoken] = $matches[$currenttoken][$currentmatch];
                $newtmplcs[] = $suffix;
            }
            $tmplcs = $newtmplcs;
        }
    
        // Find length of LCS
        $lcslen = 0;
        for($i = 0;$i < count($tmplcs);$i++) {
            if (count($tmplcs[$i]['lcs']) > $lcslen) {
                $lcslen = count($tmplcs[$i]['lcs']);
            }
        }
    
        // Filter LCS from array of CS
        $lcs = array();
        for($i=0;$i < count($tmplcs);$i++) {
            if (count($tmplcs[$i]['lcs']) == $lcslen) {
                $lcs[] = $tmplcs[$i]['lcs'];
            }
        }
    
        return $lcs;        
    }
    /**
     * Creates a new mistake, that represents case, when one lexeme moved to other position
     * @param int $answerindex   index of lexeme in answer
     * @param int $responseindex index of lexeme in response
     * @return object a mistake
     */
    private function create_moved_mistake($answerindex,$responseindex) {
        return new qtype_correctwriting_lexeme_moved_mistake($this->language, $this->answer, $answerindex,
                                                             $this->correctedresponse, $responseindex);
    }
    /**
     * Creates a new mistake, that represents case, when odd lexeme is insert to index
     * @param int $responseindex index of lexeme in response
     * @return object a mistake
     */
    private function create_added_mistake($responseindex) {
        return new qtype_correctwriting_lexeme_added_mistake($this->language, $this->answer,
                                                             $this->correctedresponse, $responseindex);
    }
    /**
     * Creates a new mistake, that represents case, when lexeme is skipped
     * @param int $answerindex   index of lexeme in answer
     * @return object a mistake
     */
    private function create_skipped_mistake($answerindex) {
        return new qtype_correctwriting_lexeme_skipped_mistake($this->language, $this->answer, $answerindex,
                                                               $this->correctedresponse);
    }
    /**
     * Returns an array of mistakes objects for given individual lcs array, using analyzer if can
     * Also sets fitness to fitness, that computed from function.
     * @param array $lcs LCS
     * @return array array of mistake objects    
     */
    private function lcs_to_mistakes($lcs) {
        if ($this->language->could_parse()) {
            $analyzer = new qtype_correctwriting_syntax_analyzer($this->answer, $this->language,
                                                                 $this->correctedresponse,
                                                                 $lcs);
            $fitness = $analyzer->fitness();
            return $analyzer->mistakes();
        } else {
            return $this->matches_to_mistakes(lcs);
        }
    }
    /**
     * Returns an array of mistakes objects for given individual lcs array.
     * Also sets fitness to fitness, that computed from function.
     * @param array $lcs LCS
     * @return array array of mistake objects
     */	
    public function matches_to_mistakes($lcs) {
        $answer = &$this->answer;
        $response = &$this->correctedresponse;
        // Determines, whether answer tokens are used in mistake computation
        $answerused = array();
        $answercount = count($answer);
        for ($i = 0;$i < $answercount;$i++) {
            $answerused[] = false;
        }
    
        // Determines, whether response tokens are used in mistake computation
        $responseused = array();
        $responsecount = count($response);
        for ($i = 0;$i < $responsecount;$i++) {
            $responseused[] = false;
        }
    
        // This result will be returned from function 
        $result = array();
        
        // These are counts of each types of errors, used to compute fitness
        $movedcount = 0;
        $addedcount = 0;
        $skippedcount = 0;
    
        // Scan lcs to mark excluded lexemes
        foreach($lcs as $answerindex => $responseindex) {
            // Mark lexemes as used
            $answerused[$answerindex] = true;
            $responseused[$responseindex] = true;
        }
    
        // Determine removed and moved lexemes by scanning answer 
        for ($i = 0;$i < $answercount;$i++) {
            // If this lexeme is not in LCS
            if ($answerused[$i] == false) {
                // Determine, whether lexeme is simply moved by scanning response or removed
                $ismoved = false;
                $movedpos = -1;
                for ($j = 0;$j < $responsecount and $ismoved == false;$j++) {
                    // Check whether lexemes are equal
                    $isequal = $answer[$i]->is_same($response[$j]);
                    if ($isequal == true and $responseused[$j] == false) {
                        $ismoved = true;
                        $movedpos = $j;
                        $responseused[$j] = true;
                    }
                }
                // Determine type of mistake (moved or removed)
                if ($ismoved) {
                    $result[] = $this->create_moved_mistake($i, $movedpos);
                    $movedcount = $movedcount + 1;
                } else {
                    $result[] = $this->create_skipped_mistake($i);
                    $skippedcount = $skippedcount + 1;
                }
            }
        }
    
        //Determine added lexemes from reponse
        for ($i = 0;$i < $responsecount;$i++) {
            if ($responseused[$i] == false) {
                $result[] = $this->create_added_mistake($i);
                $addedcount = $addedcount + 1;          
            }
        }        

        //Compute fitness-function
        $movedmistakesfitness = $this->movedmistakeweight * $movedcount;
        $skippedmistakesfitness = $this->skippedmistakeweight * $skippedcount;
        $addedmistakesfitness = $this->addedmistakeweight * $addedcount;
        $this->fitness = -1 * ($movedmistakesfitness + $skippedmistakesfitness + $addedmistakesfitness);
        return $result;
    }

    /**
    * Returns fitness as aggregate measure of how students response fits this particular answer - i.e. more fitness = less mistakes
    * Used to choose best matched answer
    * Fitness is negative or zero (no errors, full match)
    * Fitness doesn't necessary equivalent to the number of mistakes as each mistake could have different weight
    */
    public function fitness() {
        return $this->fitness;
    }

    public function mistakes() {
        return $this->mistakes;
    }

    public function is_errors() {
        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    //Other necessary access methods
}
?>