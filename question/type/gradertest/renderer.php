<?php

defined('MOODLE_INTERNAL') || die();


class qtype_gradertest_renderer extends qtype_renderer {
	
	public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
		
		global $CFG, $DB;
        require_once($CFG->dirroot . '/lib/form/filemanager.php');
		//print_r($options);
		$options = array('subdirs' => 1, 
						 'accepted_types' => '*', 
						 'return_types' => FILE_INTERNAL);
		$filemanager = &MoodleQuickForm::createElement('filemanager', 'files', get_string('files'), array('id' => 'submissionfilesid'), $options);
		$question = $qa->get_question();
		$result = html_writer::tag('div', $question->questiontext, array('class' => 'qtext'));
		return $result . $filemanager->toHtml();
	}
}