<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/bank.php');

class restore_qtype_poasquestion_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure() {
        $qtypeobj = question_bank::get_qtype($this->pluginname);
        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elepath = $this->get_pathfor('/' . $qtypeobj->name());
        $paths[] = new restore_path_element($qtypeobj->name(), $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/... element.
     */
    public function process_poasquestion($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_... too.
        if ($questioncreated) {
            $qtypeobj = question_bank::get_qtype($this->pluginname);
            $extraquestionfields = $qtypeobj->extra_question_fields();
            $tablename = array_shift($extraquestionfields);

            // Adjust some columns.
            $data->question = $newquestionid;

            // Map sequence of question_answer ids.
            $answersarr = explode(',', $data->answers);
            foreach ($answersarr as $key => $answer) {
                $answersarr[$key] = $this->get_mappingid('question_answer', $answer);
            }
            $data->answers = implode(',', $answersarr);

            // Insert record
            $newitemid = $DB->insert_record($tablename, $data);

            // Create mapping
            $this->set_mapping($tablename, $oldid, $newitemid);
        }
    }
}
