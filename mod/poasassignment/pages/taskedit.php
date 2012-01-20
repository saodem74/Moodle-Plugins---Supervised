<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir . '/tablelib.php');

class taskedit_page extends abstract_page {
    private $taskid;
    private $owners;
    
    function __construct($cm,$poasassignment) {
        global $DB;
        $this->taskid = optional_param('taskid', 0, PARAM_INT);
        $this->mode   = optional_param('mode', null, PARAM_INT);
        $this->cm = $cm;
        $this->poasassignment = $poasassignment;
    }
    function get_cap() {
        return 'mod/poasassignment:managetasks';
    }
    
    function has_satisfying_parameters() {
        global $DB;
        if($this->taskid != 0 && !$this->task = $DB->get_record('poasassignment_tasks', array('id' => $this->taskid))) {        
            $this->lasterror = 'errornonexistenttask';
            return false;
        }
        return true;
    }
    function pre_view() {
        global $DB, $PAGE;
		$id = poasassignment_model::get_instance()->get_cm()->id;
		// add navigation nodes
		$tasks = new moodle_url('view.php', array('id' => $id,
														'page' => 'tasks'));
		$PAGE->navbar->add(get_string('tasks','poasassignment'), $tasks);
		
		$taskedit = new moodle_url('view.php', array('id' => $id,
														  'page' => 'taskedit',
														  'taskid' => $this->taskid));
		$PAGE->navbar->add(get_string('taskedit','poasassignment'), $taskedit);
		
        $model = poasassignment_model::get_instance();
        if ($this->mode == SHOW_MODE || $this->mode == HIDE_MODE) {
            if (isset($this->taskid) && $this->taskid > 0) {
                $this->task = $DB->get_record('poasassignment_tasks', array('id'=>$this->taskid));
                if ($this->mode == SHOW_MODE) {
                    $this->task->hidden = 0;
                }
                else {
                    $this->task->hidden = 1;
                }
                $DB->update_record('poasassignment_tasks', $this->task);
                redirect(new moodle_url('view.php',array('id'=>$model->get_cm()->id, 'page'=>'tasks')), null, 0);
            }
            else
                print_error('invalidtaskid','poasassignment');
        }
        if ($this->mode == 'changeconfirmed') {
        	$confirm = optional_param('confirm', get_string('no'), PARAM_TEXT);
        	if ($confirm == get_string('no')) {
        		redirect(new moodle_url('view.php', array('page' => 'tasks', 'id' => $this->cm->id)));
        	}
        	else {
        		$this->update_confirmed();	
        	}
        }
        
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new taskedit_form(null, array('id' => $model->get_cm()->id, 
                                       'taskid' => $this->taskid,
                                       'poasassignmentid' => $poasassignmentid));
        // Cancel editing
        if ($this->mform->is_cancelled()) {
            redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id, 
                                                      'page' => 'tasks')), 
                                                      null, 
                                                      0);
        }
        // Add task if needed
        if ($this->mform->get_data()) {
        	$data = $this->mform->get_data();
        	if ($this->taskid <= 0) {
        		$model->add_task($data);
        		redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id, 'page' => 'tasks')), null, 0);
        	}        	
        }
        
        // Get additional fields to the form
        if ($this->taskid > 0) {
            $data = $model->get_task_values($this->taskid);
            $data->id = $model->get_cm()->id;
            $this->mform->set_data($data);
        }
    }
    
    function view() {
    	$model = poasassignment_model::get_instance();
    	if ($this->mform->get_data()) {
			$data = $this->mform->get_data();
    		if ($this->taskid > 0) {
    			$this->confirm_update($data);
    		}
    	}
    	else {
       		$this->mform->display();
    	}
    }
    
    /**
     * Updates task using settings, sent by POST
     */
    private function update_confirmed() {
    	
    }
    /**
     * Prepare flexible table for using
     * 
     * @access private
     * @return object flexible_table
     */
    private function prepare_flexible_table_owners() {
    	global $PAGE, $OUTPUT;
    	$table = new flexible_table('mod-poasassignment-task-owners');
    	$table->baseurl = $PAGE->url;
    	$columns = array(
    			'fullname', 
    			'usergroups', 
    			'attemptstatus', 
    			'gradestatus', 
    			'changetaskwithprogress',
    			'changetaskwithoutprogress',
    			'leavehiddentask');
    	$headers = array(
    			get_string('fullname', 'poasassignment'),
    			get_string('usergroups', 'poasassignment'),
    			get_string('attemptstatus', 'poasassignment'),
    			get_string('gradestatus', 'poasassignment'),
    			get_string('changetaskwithprogress', 'poasassignment').' '.
    				$OUTPUT->help_icon('changetaskwithprogress', 'poasassignment'),
    			get_string('changetaskwithoutprogress', 'poasassignment').' '.
    				$OUTPUT->help_icon('changetaskwithoutprogress', 'poasassignment'),
    			get_string('leavehiddentask', 'poasassignment').' '.
    				$OUTPUT->help_icon('leavehiddentask', 'poasassignment')
    	);
    	$table->define_columns($columns);
    	$table->define_headers($headers);
    	$table->collapsible(true);
    	$table->initialbars(false);
    	$table->set_attribute('class', 'poasassignment-table task-owners');
    	//$table->set_attribute('width', '100%');
    
    	$table->setup();
    
    	return $table;
    }
    
    /**
     * Get "rating - penaty = total" string 
     *  
     * @access private
     * @param int $rating
     * @param int $penalty
     */
    private function show_rating_methematics($rating, $penalty) {
    	$string = '';
    	
    	$string .= $rating;
		$string .= ' - ';
		$string .= '<span style="color:red;">'.$penalty.'</span>';
		$string .= ' = ';
		$string .= $rating - $penalty;
		
		return $string;
    }
    
    /**
     * Get information about task owner and his task's status
     * 
     * @access private
     * @param object $userinfo assignee object
     * @return array information
     */
    private function get_owner($userinfo) {
    	$model = poasassignment_model::get_instance();
    	$owner = array();
    	
    	// Get student username and profile link
    	$userurl = new moodle_url('/user/profile.php', array('id' => $userinfo->userid));
    	$owner[] = html_writer::link($userurl, fullname($userinfo->userinfo, true));
    	
    	// TODO Get student's groups
    	$owner[] = '?';
    	
    	
    	// Get information about assignee's attempts and grades
    	if ($attempt = $model->get_last_attempt($userinfo->id)) {
    		$owner[] = get_string('hasattempts', 'poasassignment');
    	
    		// If assignee has an attempt(s), show information about his grade
    		if ($attempt->rating != null) {
    			// Show actual grade with penalty
    			$owner[] = 
    				get_string('hasgrade', 'poasassignment').
    				' ('.
    				$this->show_rating_methematics($attempt->rating, $model->get_penalty($attempt->id)).
    				')';
    		}
    		else {
    			// Looks like assignee has no grade or outdated grade
    			if ($lastgraded = $model->get_last_graded_attempt($userinfo->id)) {
    				$owner[] = 
    					get_string('hasoutdatedgrade', 'poasassignment').
    					' ('.
    					$this->show_rating_methematics($lastgraded->rating, $model->get_penalty($lastgraded->id)).
    					')';    	
    			}
    			else {
    				// There is no graded attempts, so show 'No grade'
    				$owner[] = get_string('nograde', 'poasassignment');
    			}
    		}
    	}
    	else {
    		// No attepts => no grade
    		$owner[] = get_string('hasnoattempts', 'poasassignment');
    		$owner[] = get_string('nograde', 'poasassignment');
    	}
    	$owner[] = '<input type="radio" name="action_'.$userinfo->id.'" value="changetaskwithprogress" checked="checked"></input>';
    	$owner[] = '<input type="radio" name="action_'.$userinfo->id.'" value="changetaskwithoutprogress"></input>';
    	$owner[] = '<input type="radio" name="action_'.$userinfo->id.'" value="leavehiddentask"></input>';
    	
    	return $owner;
    }
    /**
     * Show confirm update screen.
     * If noone took the task, it seems like ordinary confirm screen 
     * - are you sure? - yes/no.
     * If someone took the task, page shows table 
     * of taskowners and offer what to do with each student
     * 
     * @access public
     * @param mixed $data - updated task data
     */
    public function confirm_update($data) {    	
    	global $OUTPUT;
    	$model = poasassignment_model::get_instance();
    	$owners = $model->get_task_owners($this->taskid);
    	
    	// Open form
    	echo '<form action="view.php?page=taskedit&id='.$this->cm->id.'" method="post">';
    	
    	// If there are students, that own this task, show them
    	if (count($owners) > 0) {
    		// Show owners table
    		$usersinfo = $model->get_users_info($owners);
    		print_string('ownersofthetask', 'poasassignment');
    		$table = $this->prepare_flexible_table_owners();
    		foreach ($usersinfo as $userinfo) {
    			$table->add_data($this->get_owner($userinfo));
    		}
    		$table->print_html();
    		
    	}
    	else {
    		print_string('nooneownsthetask', 'poasassignment');
    	}
    	// Ask user to confirm delete
    	echo '<br/>';
    	print_string('changetaskconfirmation', 'poasassignment');
    	if (count($owners) > 0) {
    		echo ' <span class="poasassignment-critical">(';
    		print_string('changingtaskwillchangestudentsdata', 'poasassignment');
    		echo ')</span>';
    	}
    	
    	// Add updated task in hidden elements
    	foreach ((array)$data as $name => $field) {
    		echo '<br/>'.$name.'='.$field;
    		echo '<input type="hidden" name="'.$name.'" value="'.$field.'"/>';
    	}
    	$nobutton = '<input type="submit" name="confirm" value="'.get_string('no').'"/>';
    	$yesbutton = '<input type="submit" name="confirm" value="'.get_string('yes').'"/>';
    	echo '<input type="hidden" name="mode" value="changeconfirmed"/>';
    	echo '<div class="poasassignment-confirmation-buttons">'.$yesbutton.$nobutton.'</div>';
    	echo '</form>';
    }
    
    public static function display_in_navbar() {
        return false;
    }
    
}
class taskedit_form extends moodleform {

    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        if($instance['taskid']>0)
            $mform->addElement('header','taskeditheader',get_string('taskeditheader','poasassignment'));
        else
            $mform->addElement('header','taskaddheader',get_string('taskaddheader','poasassignment'));
        
        $mform->addElement('text','name',get_string('taskname','poasassignment'),array('size'=>45));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('htmleditor','description',get_string('taskintro', 'poasassignment'));
        $mform->addElement('checkbox','hidden',get_string('taskhidden', 'poasassignment'));
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$instance['poasassignmentid']));
        $poasmodel= poasassignment_model::get_instance();
        foreach($fields as $field) {
            $name = $field->name.' '.$poasmodel->help_icon($field->description);
            if($field->ftype==STR)
                $mform->addElement('text','field'.$field->id,$name,array('size'=>45));
                
            if($field->ftype==TEXT)
                $mform->addElement('htmleditor','field'.$field->id,$name);
                
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && $field->random) {
                $mform->addElement('static','field'.$field->id,$name,'random field');
            }
            
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && !$field->random) {
                $mform->addElement('text','field'.$field->id,$name,array('size'=>10));
            }

            if($field->ftype==DATE) {
                $mform->addElement('date_selector','field'.$field->id,$name);
            }
            
            if($field->ftype==FILE) {
                $mform->addElement('filemanager','field'.$field->id,$name);
            }
            if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
                if($field->random==0) {
                    /* $tok = strtok($field->variants,"\n");
                    while($tok) {
                        $opt[]=$tok;
                        $tok=strtok("\n");
                    } */
                    $opt=$poasmodel->get_field_variants($field->id);
                    $select=&$mform->addElement('select','field'.$field->id,$name,$opt);
                    if($field->ftype==MULTILIST)
                        $select->setMultiple(true);
                }
                else
                    $mform->addElement('static','field'.$field->id,$name,'random field');
            }
        }
        
        // hidden params
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'taskid', $instance['taskid']);
        $mform->setType('taskid', PARAM_INT);
        $mform->addElement('hidden', 'page', 'taskedit');
        $mform->setType('taskid', PARAM_TEXT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid']));
        foreach($fields as $field) {
            if(!$field->random &&($field->ftype==FLOATING || $field->ftype==NUMBER)) {
                if(!($field->valuemin==0 && $field->valuemax==0 )) {
                    if($data['field'.$field->id]>$field->valuemax || $data['field'.$field->id]<$field->valuemin) {
                    $errors['field'.$field->id]=get_string('valuemustbe','poasassignment').' '.
                                                get_string('morethen','poasassignment').' '.
                                                $field->valuemin.' '.
                                                get_string('and','poasassignment').' '.
                                                get_string('lessthen','poasassignment').' '.
                                                $field->valuemax;
                    return $errors;
                    }
                }
            }
            if($field->ftype==MULTILIST && !isset($data['field'.$field->id])) {
                $errors['field'.$field->id]=get_string('errornovariants','poasassignment');
                return $errors;
            }
            
        }
       
        return true;
    }
}
