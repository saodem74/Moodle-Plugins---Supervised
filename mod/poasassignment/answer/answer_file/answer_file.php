<?php
require_once(dirname(dirname(__FILE__)).'/answer.php');
//require_once('answer.php');

class answer_file extends poasassignment_answer {
    var $checked;
    var $fieldnames = array ( 'fileamount','maxfilesize','fileextensions');
    function answer_file() {
        global $DB;
        $plugin = $DB->get_record('poasassignment_answers',
                                  array('name' => 'answer_file'));
        if ($plugin) {
            $this->answerid = $plugin->id;
        }
    }
    
    /** Display plugin settings 
     *
     *  Display separate fieldset with plugin settings
     */
    function show_settings($mform,$poasassignmentid) {
        global $CFG, $COURSE, $DB;
        
        // Adding header
        //----------------------------------------------------------------------
        $mform->addElement('header',
                           'answerfileheader',
                           get_string('pluginname', 'poasassignmentanswertypes_answer_file'));
                           
        // Adding selection checkbox
        //----------------------------------------------------------------------
        $mform->addElement('checkbox',
                           'answerfile',
                           get_string('answerfile', 'poasassignmentanswertypes_answer_file'));
        
        $conditions = array('poasassignmentid' => $poasassignmentid, 
                            'answerid' => $this->answerid);
        if ($DB->record_exists('poasassignment_ans_stngs', $conditions))
            $mform->setDefault('answerfile', 'true');
        $mform->addHelpButton('answerfile', 
                              'answerfile', 
                              'poasassignmentanswertypes_answer_file');
        
        // Adding file amount counter
        //----------------------------------------------------------------------
        $mform->addElement('select', 
                           'fileamount', 
                           get_string('submissionfilesamount', 
                                      'poasassignmentanswertypes_answer_file'), 
                           array(1,2,3,4,5,6,7,8,9,10));
        $conditions = array('poasassignmentid' => $poasassignmentid,
                            'answerid' => $this->answerid,
                            'name' => 'fileamount');
        if ($DB->record_exists('poasassignment_ans_stngs', $conditions)) {
            $rec = $DB->get_record('poasassignment_ans_stngs', $conditions);
            $mform->setDefault('fileamount', $rec->value);
        }
        $mform->disabledIf('fileamount', 'answerfile');
        $mform->addHelpButton('fileamount', 
                              'submissionfilesamount', 
                              'poasassignmentanswertypes_answer_file');
        
        // Adding maximum upload size selectbox
        //----------------------------------------------------------------------
        
        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        $choices[0] = get_string('courseuploadlimit') . ' (' . display_size($COURSE->maxbytes) . ')';
        $mform->addElement('select', 
                           'maxfilesize', 
                           get_string('submissionfilemaxsize', 
                                      'poasassignmentanswertypes_answer_file'), 
                           $choices);
        $conditions = array('poasassignmentid' => $poasassignmentid,
                            'answerid' => $this->answerid,
                            'name' => 'maxfilesize');
        if ($DB->record_exists('poasassignment_ans_stngs', $conditions)) {
            $rec = $DB->get_record('poasassignment_ans_stngs', $conditions);
            $mform->setDefault('maxfilesize', $rec->value);
        }
        $mform->disabledIf('maxfilesize', 'answerfile');
        $mform->addHelpButton('maxfilesize', 
                              'submissionfilemaxsize', 
                              'poasassignmentanswertypes_answer_file');
        
        // Adding file extensions string
        //----------------------------------------------------------------------
        $mform->addElement('text', 
                           'fileextensions', 
                           get_string('fileextensions', 'poasassignmentanswertypes_answer_file'), 
                           array('size' => '64'));
        $conditions = array('poasassignmentid' => $poasassignmentid,
                            'answerid' => $this->answerid,
                            'name' => 'fileextensions');
        if ($DB->record_exists('poasassignment_ans_stngs', $conditions)) {
            $rec = $DB->get_record('poasassignment_ans_stngs', $conditions);
            $mform->setDefault('fileextensions', $rec->value);
            }
        $mform->addHelpButton('fileextensions', 
                              'fileextensions', 
                              'poasassignmentanswertypes_answer_file');
        $mform->disabledIf('fileextensions', 'answerfile');
    }
    
    static function validation($data, &$errors) {
        // TODO check 'fileextensions' element, it must look like "ext,ext,ext"
    }
    function save_settings($poasassignment, $id) {
        global $DB;
        if ($this->checked) {
            //$plugin=$DB->get_record('poasassignment_answers',array('name'=>'answer_file'));
            $settingsrecord->poasassignmentid = $id;
            //$settingsrecord->answerid=$plugin->id;
            $settingsrecord->answerid=$this->answerid;
            
            $settingsrecord->name='fileamount';
            $settingsrecord->value=$poasassignment->fileamount;
            $DB->insert_record('poasassignment_ans_stngs',$settingsrecord);
            
            $settingsrecord->name='maxfilesize';
            $settingsrecord->value=$poasassignment->maxfilesize;
            $DB->insert_record('poasassignment_ans_stngs',$settingsrecord);
            
            $settingsrecord->name='fileextensions';
            $settingsrecord->value=$poasassignment->fileextensions;
            $DB->insert_record('poasassignment_ans_stngs',$settingsrecord);
        }
    }
    function update_settings($poasassignment) {
        global $DB;
        //$plugin=$DB->get_record('poasassignment_answers',array('name'=>'answer_file'));
        $conditions = array('poasassignmentid'=>$poasassignment->id,
                //'answerid'=>$plugin->id);
                'answerid'=>$this->answerid);
        $recordexists = $DB->record_exists('poasassignment_ans_stngs',$conditions);
        if (!$recordexists)
            $this->save_settings($poasassignment,$poasassignment->id);
        //$temp=$poasassignment->flags&64;
        if ($recordexists && !$this->checked)
            $this->delete_settings($poasassignment->id);
        if ($recordexists && $this->checked) {
            $settingsrecord->poasassignmentid=$poasassignment->id;
            //$settingsrecord->answerid=$plugin->id;
            $settingsrecord->answerid=$this->answerid;
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'fileamount');
            $currentsetting=$DB->get_record('poasassignment_ans_stngs',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='fileamount';
            $settingsrecord->value=$poasassignment->fileamount;
            $DB->update_record('poasassignment_ans_stngs',$settingsrecord);
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'maxfilesize');
            $currentsetting=$DB->get_record('poasassignment_ans_stngs',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='maxfilesize';
            $settingsrecord->value=$poasassignment->maxfilesize;
            $DB->update_record('poasassignment_ans_stngs',$settingsrecord);
            
            $conditions = array('poasassignmentid'=>$poasassignment->id,
                    'name'=>'fileextensions');
            $currentsetting=$DB->get_record('poasassignment_ans_stngs',$conditions);
            $settingsrecord->id=$currentsetting->id;
            $settingsrecord->name='fileextensions';
            $settingsrecord->value=$poasassignment->fileextensions;
            $DB->update_record('poasassignment_ans_stngs',$settingsrecord);
        }
    }
    function delete_settings($poasassignmentid) {
        global $DB;
        //$plugin=$DB->get_record('poasassignment_answers',array('name'=>'answer_file'));
        $conditions = array('poasassignmentid'=>$poasassignmentid,
                //'answerid'=>$plugin->id);
                'answerid'=>$this->answerid);
        return $DB->delete_records('poasassignment_ans_stngs',$conditions);
    }
    function show_answer_form($mform,$poasassignmentid) {
        global $DB;
        /* $plugin_settings = $DB->get_records('poasassignment_ans_stngs',array('poasassignmentid'=>$poasassignmentid,
                                                            'answerid'=>$this->answerid)); */
        /* $mform = new answer_form_file();
        $mform->display(); */
        
        //answer options
        $mform->addElement('header', 
                           'answerfileheader', 
                           get_string('pluginname','poasassignmentanswertypes_answer_file'));
                
        $options = array();
        $options['subdirs'] = 0;
        $plugin_settings_size = $DB->get_record('poasassignment_ans_stngs', 
                                                array('poasassignmentid' => $poasassignmentid,
                                                       'answerid' => $this->answerid,
                                                       'name' => 'maxfilesize'));
        $plugin_settings_amount = $DB->get_record('poasassignment_ans_stngs',
                                                  array('poasassignmentid' => $poasassignmentid,
                                                        'answerid' => $this->answerid,
                                                        'name'=>'fileamount'));  
                                                                    
        $options['maxbytes'] = $plugin_settings_size->value;
        $options['maxfiles'] = ($plugin_settings_amount->value) + 1;
        $mform->addElement('filemanager', 
                           'answerfiles_filemanager', 
                           get_string('pluginname','poasassignmentanswertypes_answer_file'),
                           $options);
        $mform->closeHeaderBefore('answerfileheader');
    }
    function configure_flag($poasassignment) {
        if (isset($poasassignment->answerfile)) {
            $this->checked=true;
            unset($poasassignment->answerfile);
            }
        else
            $this->checked=false;
    }
    
    public function save_submission($attemptid, $data) {
        global $DB;
        $poasmodel = poasassignment_model::get_instance();
        $submission = new stdClass();
        
        $submission->attemptid = $attemptid;
        $submission->answerid = $this->answerid;
        $submission->value = $data->answerfiles_filemanager;
        $submission->id =  $DB->insert_record('poasassignment_submissions', $submission);
        $poasmodel->save_files($data->answerfiles_filemanager,'submissionfiles',$submission->id);
        return $submission->id;
    }
    
    function show_assignee_answer($assigneeid,$poasassignmentid,$needbox=1) {
        global $DB,$OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $html='';
        if(!$assigneeid)
            return $html;
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        if($attempt) {
            $submission=$DB->get_record('poasassignment_submissions',array('answerid'=>$this->answerid,'attemptid'=>$attempt->id));
            if($submission) {
                if($needbox)
                    $html.=$OUTPUT->box_start();
                $cm = get_coursemodule_from_instance('poasassignment',$poasassignmentid);
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                $html.= $poasmodel->view_files($context->id,'submissionfiles',$submission->id);
                if($needbox) 
                    $html.= $OUTPUT->box_end();                
            }
            return $html;
                //echo $submission->value;
        }
    }
    
    function get_answer_values($poasassignmentid) {
        global $DB;
        $cm = get_coursemodule_from_instance('poasassignment',$poasassignmentid);
        $context=get_context_instance(CONTEXT_MODULE, $cm->id);
        $data = new stdclass();
        $poasmodel=poasassignment_model::get_instance();
        $filemanager_options = array('subdirs'=>0);
        if($poasmodel->assignee) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id));
                $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$poasmodel->assignee->id,'attemptnumber'=>$attemptscount));
                if($attempt) {
                    $submission=$DB->get_record('poasassignment_submissions',array('answerid'=>$this->answerid,'attemptid'=>$attempt->id));
                    if($submission) {
                        $data = file_prepare_standard_filemanager($data, 'answerfiles', $filemanager_options, $context, 'mod_poasassignment', 'submissionfiles', $submission->id);    
                    }
                }
            }
        return $data;
    // set file manager itemid, so it will find the files in draft area
    }
}
/* class answer_form_file extends moodleform
{
    function definition() {        
        $mform = $this->_form;
        $mform->addElement('filemanager', 'answerfiles', 'answerfiles');
    }
} */