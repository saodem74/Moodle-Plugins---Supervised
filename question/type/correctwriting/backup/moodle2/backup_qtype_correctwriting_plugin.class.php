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
 * A backup provider for correctwriting question
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once ($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/backup_poasquestion_preg_plugin.class.php');

class backup_qtype_correctwriting_plugin extends backup_qtype_poasquestion_plugin {


    /**
     * Includes another dependent data into plugin
     * @return backup_plugin_element
     */
    protected function define_question_plugin_structure() {
        $plugin = $this->define_question_plugin_structure_inner();
        /**
         * @var backup_plugin_element $pluginwrapper
         */
        $pluginwrapper = $plugin->get_child($this->get_recommended_name());

        $qtypeobj = question_bank::get_qtype($this->pluginname);

        // Why we add those into plugin wrapper? Because there
        // are no way we could reach a question id otherwise
        // It will cause a errors in backup otherwise, since id structure
        // will be broken

        $langfields = array('ui_name', 'description', 'name', 'scanrules', 'parserules', 'version', 'visible');
        $child = new backup_nested_element($qtypeobj->name() . '_language', array('id'), $langfields);
        $pluginwrapper->add_child($child);
        $child->set_source_sql('
            SELECT * FROM {block_formal_langs}
             WHERE id IN (SELECT langid FROM {qtype_correctwriting}
             WHERE questionid = :question);
        ',
        array('question' => backup::VAR_PARENTID)
        );

        // Because we can't include descriptions in answer, we
        // include them as one table part

        $dscrfields = array('tableid', 'number', 'description');
        $child = new backup_nested_element($qtypeobj->name() . '_descriptions', array('id'), $dscrfields);
        $pluginwrapper->add_child($child);
        $child->set_source_sql('
            SELECT * FROM {block_formal_langs_node_dscr}
            WHERE tablename = \'question_answers\' AND tableid IN (
              SELECT id FROM {question_answers} WHERE question = :question
            );
        ',
        array('question' => backup::VAR_PARENTID)
        );


        return $plugin;
    }
    /**
     * Returns the qtype information to attach to question element.
     * This code must go to poasquestion as define_question_plugin_structure()
     */
    protected function define_question_plugin_structure_inner() {
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

        $child->set_source_table($tablename, array($qtypeobj->questionid_column_name() => backup::VAR_PARENTID));

        // Don't need to annotate ids nor files.
        return $plugin;
    }
}
 
 