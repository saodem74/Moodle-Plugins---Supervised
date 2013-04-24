<?php
/**
 * Defines abstract class which is common for all authoring tools.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

abstract class qtype_preg_dotbased_authoring_tool extends qtype_preg_authoring_tool {

    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
    	// TODO
    }

}

?>
