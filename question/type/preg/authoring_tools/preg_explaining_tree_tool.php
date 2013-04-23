<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_dotbased_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

class qtype_preg_explaining_tree_tool extends qtype_preg_dotbased_authoring_tool {

    // TODO: override another functions from qtype_preg_regex_handler?

    protected function is_preg_node_acceptable($pregnode) {
        // Well, everything that was parsed can be displayed to user.
        return true;
    }

    protected function generate_json_for_empty_regex(&$json_array, $id) {
        $dotscript = 'digraph { }';
        $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
    }

    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
        $dotscript = 'digraph { "Ooops! Your regex contains errors, so I can\'t build the interactive tree!" [color=white]; }';
        $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
    }

    /**
     * Generate image and map for interative tree
     *
     * @param array $json_array contains link on image and text map of interactive tree
     */
    protected function generate_json_for_accepted_regex(&$json_array, $id) {
        $styleprovider = new qtype_preg_dot_style_provider();
        $dotscript = $this->get_ast_root()->dot_script($styleprovider);
        if ($id != -1) {
            $dotscript = $styleprovider->select_subtree($dotscript, $id);
        }
        $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
        $json_array['map'] = qtype_preg_regex_handler::execute_dot($dotscript, 'cmapx');
    }
}
