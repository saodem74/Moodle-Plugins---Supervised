<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/bank.php');

class backup_qtype_poasquestion_plugin extends backup_qtype_plugin {

    /**
     * Returns the qtype information to attach to question element.
     */
    protected function define_question_plugin_structure() {
        $qtypeobj = question_bank::get_qtype($this->pluginname);

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', $qtypeobj->name());

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // This qtype uses standard question_answers, add them here
        // to the tree before any other information that will use them.
        $this->add_question_question_answers($pluginwrapper);

        // Now create the qtype own structures.
        $extraquestionfields = $qtypeobj->extra_question_fields();
        $tablename = array_shift($extraquestionfields);

        $child = new backup_nested_element($qtypeobj->name(), array('id'), $extraquestionfields);

        // Now the own qtype tree.
        $pluginwrapper->add_child($child);

        // Set source to populate the data.
        $child->set_source_table($tablename, array('question' => backup::VAR_PARENTID));

        // Don't need to annotate ids nor files.
        return $plugin;
    }
}
