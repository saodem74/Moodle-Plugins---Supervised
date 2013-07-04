<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

global $CFG;
global $PAGE;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool.php');

/**
 * Generates json array which stores authoring tools' content.
 */
function qtype_preg_get_json_array() {
    global $CFG;
    $json_array = array();
    $regextext = optional_param('regex', '', PARAM_RAW);
	$answer = optional_param('answer', '', PARAM_RAW);

	$regex_testing_tool = new preg_regex_testing_tool($regex, array('answer' => $answer));
	$regex_testing_tool->generate_json($json_array);
	
    return $json_array;
}

$json_array = qtype_preg_get_json_array();
echo json_encode($json_array);
