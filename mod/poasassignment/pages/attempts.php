<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');

class attempts_page extends abstract_page {
    private $assigneeid;

    function __construct() {
        global $DB, $USER;        
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
    }
    
    function has_satisfying_parameters() {
        global $DB,$USER;
        $context = poasassignment_model::get_instance()->get_context();
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        if ($this->assigneeid > 0) {
            if (! $this->assignee = $DB->get_record('poasassignment_assignee', array('id' => $this->assigneeid))) {
                $this->lasterror = 'errornonexistentassignee';
                return false;
            }
        }
        else {
            $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
            $this->assignee = $DB->get_record('poasassignment_assignee', 
                                              array('userid' => $USER->id,
                                                    'poasassignmentid' => $poasassignmentid));
        }
        // Page exists always for teachers
        if (has_capability('mod/poasassignment:grade', $context) || has_capability('mod/poasassignment:finalgrades', $context)) {
            return true;
        }
        // Page exists, if assignee has attempts
        if ($this->assignee && $this->assignee->lastattemptid > 0) {
            // Page content is available if assignee wants to see his own attempts
            // or teacher wants to see them
            if($this->assignee->userid == $USER->id) {
				if (has_capability('mod/poasassignment:viewownsubmission', $context)) {
					return true;
				}
				else {
					$this->lasterror = 'errorviewownsubmissioncap';
					return false;
				}
            }
            else {
                $this->lasterror = 'erroranothersattempts';
                return false;
            }
        }
        else {
            $this->lasterror = 'errorassigneenoattempts';
            return false;
        }
        return true;
    }
    function view_assignee_block() {
        $poasmodel = poasassignment_model::get_instance();
        if (has_capability('mod/poasassignment:grade', $poasmodel->get_context())) {
            $mform = new assignee_choose_form(null, array('id' => $poasmodel->get_cm()->id));
            $mform->display();
        }
    }
    function view() {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $poasassignmentid = $poasmodel->get_poasassignment()->id;
        //$html = '';
        $this->view_assignee_block();
        // teacher has access to the page even if he has no task or attempts
        if(isset($this->assignee->id)) {
            $attempts = array_reverse($DB->get_records('poasassignment_attempts',
                                                       array('assigneeid'=>$this->assignee->id), 
                                                       'attemptnumber'));
            $plugins = $poasmodel->get_plugins();
            $criterions = $DB->get_records('poasassignment_criterions', array('poasassignmentid'=>$poasassignmentid));
            $latestattempt = $DB->get_record('poasassignment_attempts', array('id'=>$this->assignee->lastattemptid));
            $attemptscount = count($attempts);  
            foreach($attempts as $attempt) {
				echo $OUTPUT->box_start();
				attempts_page::show_attempt($attempt);
                // show disablepenalty/enablepenalty button
                if(has_capability('mod/poasassignment:grade',$poasmodel->get_context())) {
                    $cmid = $poasmodel->get_cm()->id;
                    if(isset($attempt->disablepenalty) && $attempt->disablepenalty==1) {
                        echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$cmid.'&action=enablepenalty&attemptid='.$attempt->id), 
                                                                get_string('enablepenalty','poasassignment'));
                    }
                    else {
                        echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$cmid.'&action=disablepenalty&attemptid='.$attempt->id), 
                                                                get_string('disablepenalty','poasassignment'));
                    }
                }
				$canseecriteriondescr = has_capability('mod/poasassignment:seecriteriondescription', $poasmodel->get_context());
				attempts_page::show_feedback($attempt, $latestattempt, $canseecriteriondescr);
                echo $OUTPUT->box_end();
				echo '<br>';
            }
        }
    }
    public static function use_echo() {
        return false;
    }
	public static function show_attempt($attempt, $showcontent = true) {
		$poasmodel = poasassignment_model::get_instance();
		echo '<table class="poasassignment-table" align="center">';
		
		$values = array(
						'attemptnumber' => $attempt->attemptnumber,
						'attemptdate' => userdate($attempt->attemptdate),
						'draft' => $attempt->draft == 1 ? get_string('yes') : get_string('no'),
						'attemptisfinal' => $attempt->final == 1 ? get_string('yes') : get_string('no'),
						'attempthaspenalty' => $attempt->disablepenalty == 1 ? get_string('no') : get_string('yes'),
						'attempttotalpenalty' => $poasmodel->get_penalty($attempt->id));
		foreach($values as $key => $value) {			
			echo '<tr>';
			echo '<td class="header" >' . get_string($key,'poasassignment') . '</td>';
			if ($key == 'attempthaspenalty' || $key == 'attempttotalpenalty') {
				echo '<td class="critical" style="text-align:center;">' . $value . '</td>';
			}
			else {
				echo '<td style="text-align:center;">' . $value . '</td>';
			}
			echo '</tr>';
		}
		if ($showcontent) {
			$poasmodel = poasassignment_model::get_instance();
			$plugins = $poasmodel->get_plugins();
			$attemptcontent = '';
			foreach($plugins as $plugin) {
				require_once($plugin->path);
				$poasassignmentplugin = new $plugin->name();
				$attemptcontent .= $poasassignmentplugin->show_assignee_answer($attempt->assigneeid, $poasmodel->get_poasassignment()->id, 0, $attempt->id);
			}
			echo '<tr>';
			echo '<td colspan="2">' . $attemptcontent . '</td>';
			echo '</tr>';
		}
		
		echo '</table>';
	}
	public static function show_feedback($attempt, $latestattempt, $showdescription) {
        global $DB,$OUTPUT;
		$poasmodel = poasassignment_model::get_instance();
		$context = $poasmodel->get_context();
		$criterions = $DB->get_records('poasassignment_criterions',
						   		       array('poasassignmentid' => $poasmodel->get_poasassignment()->id));
        if (/*isset($attempt->rating) && */
                            $DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
			$heading = get_string('feedback','poasassignment');
            
            if ($attempt->ratingdate < $latestattempt->attemptdate) {
                $heading .= ' (' . get_string('oldfeedback','poasassignment') . ')';
            }
			$heading .= ' - ' . userdate($attempt->ratingdate);
			echo $OUTPUT->heading($heading);
            //echo $OUTPUT->box_start();
			
			
			$options = new stdClass();
			$options->area    = 'poasassignment_comment';
			$options->component    = 'mod_poasassignment';
			$options->pluginname = 'poasassignment';
			$options->context = $context;
			$options->showcount = true;
            foreach ($criterions as $criterion) {
				
                $ratingvalue=$DB->get_record('poasassignment_rating_values',
											 array('criterionid'=>$criterion->id,
												   'attemptid'=>$attempt->id));
												   
				echo '<table class="poasassignment-table" align="center" width = "90%">';
				echo '<tr>';
				
				echo '<td class="header">';
				echo $criterion->name.' ';
				if ($showdescription) {
						echo $poasmodel->help_icon($criterion->description);
				}
				echo '</td>';
				
				echo '<td width="20%" style="text-align:center">';
				if ($attempt->draft==0) {                        
					echo $ratingvalue->value . ' / 100';
				}
				else {
					echo get_string('draft', 'poasassignment');
				}
				echo '</td>';
				
				echo '</tr>';
				
				echo '<td colspan="2">';
				$options->itemid  = $ratingvalue->id;
				$comment = new comment($options);
				echo $comment->output(true);
				echo '</td>';
				echo '</tr>';
				echo '</table>';
            }
            echo $poasmodel->view_files($context->id, 'commentfiles', $attempt->id);
            
			echo '<table class="poasassignment-table" align="center" width = "90%">';
			echo '<tr>';
			
			echo '<td class="header critical">';
			echo get_string('penalty','poasassignment');
			echo '</td>';
			
			echo '<td class="critical" width="20%" style="text-align:center">';
			echo $poasmodel->get_penalty($attempt->id);
			echo '</td>';
			
			echo '</tr>';
			echo '</table>';
            if ($attempt->draft==0) {    
				$ratingwithpenalty = $attempt->rating - $poasmodel->get_penalty($attempt->id);
				echo '<table class="poasassignment-table" align="center" width = "90%">';
				
				echo '<tr><td class="header">';				
                echo $OUTPUT->heading(get_string('totalratingis','poasassignment'));
				echo '</td>';
				
				echo '<td width="20%">';
				echo $OUTPUT->heading($ratingwithpenalty);
				echo '</td></tr></table>';
            }
            //echo $OUTPUT->box_end();
        }
    }
}