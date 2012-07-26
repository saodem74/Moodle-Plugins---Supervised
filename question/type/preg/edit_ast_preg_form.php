<?php
/**
 * Defines authors tool form class.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
global $CFG;
require_once($CFG->libdir.'/formslib.php');
//require_once($CFG->dirroot.'/question/type/preg/ast_tree_nodes.php');
require_once($CFG->dirroot.'/question/type/preg/explain_graph/explain_graph_tool.php');
require_once($CFG->dirroot.'/question/type/preg/question.php');
require_once($CFG->dirroot.'/question/type/preg/preg_hints.php');
require_once($CFG->dirroot.'/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot.'/question/type/preg/preg_dotstyleprovider.php');
require_once($CFG->dirroot.'/question/type/preg/author_tool_description/preg_description.php');

class qtype_preg_authors_tool_form extends moodleform {

    /*function __constructor(){
        parent::moodleform();
    }*/
    
    //Add elements to form
    function definition() {
        global $CFG;
        global $PAGE;
 
        $mform =& $this->_form;//Create form 
        
        //$PAGE->requires->js('/question/type/preg/author_tool.js');
        $mform->addElement('html', '<div id="script_test"><script src="http://yui.yahooapis.com/3.5.1/build/yui/yui-min.js"></script></div>');
        $mform->addElement('html', '<div id="script_test"><script type="text/javascript" src="http://localhost/moodle/question/type/preg/preg_authors_tool_script.js"></script></div>');
        
        //Add header
        $mform->addElement('html', '<div align="center"><h2>Test regex</h2></div>');
        
        //Add widget on form
        $mform->addElement('header', 'regexeditheader', 'Input regex here:');
        $mform->addHelpButton('regexeditheader','regexeditheader', 'qtype_preg');
        
        $mform->addElement('text', 'regextext', 'Input regex', array('size' => 100));        
        $mform->addElement('button', 'regexcheck', 'Check');
        $mform->addElement('button', 'regexback', 'Back (and save regex in this field)');
        
        //Add images with graph and tree on form
        $mform->addElement('header', 'regeximgheader', 'Image here:');
        $mform->addHelpButton('regeximgheader','regeximgheader','qtype_preg');
        
        $regextext = optional_param('regex', '', PARAM_TEXT);
        //var_dump($regextext);
        if(!empty($regextext)) {

            $mform->setDefault('regextext', $regextext);//Add regex in line edit
            
            //Generate tree image
            /*$tree = new qtype_preg_author_tool_explain_tree($regextext);
            $dot_instructions_tree = $tree->create_dot();
            qtype_preg_regex_handler::execute_dot($dot_instructions_tree,'/var/www/moodle/question/type/preg/tmp_img/tree.png');//Generate image
            qtype_preg_regex_handler::execute_dot($dot_instructions_tree,'/var/www/moodle/question/type/preg/tmp_img/tree.cmapx');//Generate map*/
            
            $regexhandler = new qtype_preg_regex_handler($regextext);
            $dir = $regexhandler->get_temp_dir('tmp_img');
            qtype_preg_regex_handler::execute_dot($regexhandler->get_ast_root()->dot_script(new qtype_preg_dot_style_provider()), '/var/www/moodle/question/type/preg/tmp_img/tree.png');//Generate image
            qtype_preg_regex_handler::execute_dot($regexhandler->get_ast_root()->dot_script(new qtype_preg_dot_style_provider()), '/var/www/moodle/question/type/preg/tmp_img/tree.cmapx');//Generate map
            
            //Generate graph image
            $tmp_graph = new qtype_preg_author_tool_explain_graph($regextext);
            $graph = $tmp_graph->create_graph();
            $dot_instructions_graph = $graph->create_dot();
            qtype_preg_regex_handler::execute_dot($dot_instructions_graph, '/var/www/moodle/question/type/preg/tmp_img/graph.png');//Generate image
            
            /*if(!rename($dir . 'tree.png', '/var/www/moodle/question/type/preg/tmp_img/tree.png')){
                echo "Can\'t reneme $dir tree.png";
            }
            if(!rename($dir . 'tree.cmapx', '/var/www/moodle/question/type/preg/tmp_img/tree.cmapx')){
                echo "Can\'t reneme $dir tree.cmapx";
            }
            if(!rename($dir . 'graph.png', '/var/www/moodle/question/type/preg/tmp_img/graph.png')){
                echo "Can\'t reneme $dir graph.png";
            }*/
            
            //Add generated images
            $mform->addElement('html', 'Interactive tree</br><div id="tree_handler"><img src="http://localhost/moodle/question/type/preg/tmp_img/tree.png" id="id_tree" usemap="_anonymous_0" /></div></br>');        
            //$mform->addElement('html', 'Interactive tree</br><div id="tree_handler"><frameset rows="80,*" cols="*"><frame src="http://localhost/moodle/question/type/preg/tmp_img/tree.png" name="topFrame" scrolling="yes"></frameset></div></br>'); 
            $mform->addElement('html', 'Graph</br><div id="graph_handler"><img src="http://localhost/moodle/question/type/preg/tmp_img/graph.png" id="id_graph" /></div></br>');
            //$mform->addElement('html', 'Graph</br><div class="ttt_graph_output" title="RegExp input" style="overflow:auto;width:100%;max-width:100%;position:relative" ><img src="http://localhost/moodle/question/type/preg/tmp_img/graph.png" alt="alt" /></div></br>');
            
            //Add generated maps
            //Read tree map            
            $tree_map ='';//tag <map>
            //Open and read tag <map> from file 
            $tree_handle = fopen('/var/www/moodle/question/type/preg/tmp_img/tree.cmapx', 'r');            
            while (!feof($tree_handle)) {
                $tree_map .= fgets($tree_handle);
            }
            fclose($tree_handle);
            
            $mform->addElement('html', $tree_map.'</br>');
            
            /*if(!unlink('/var/www/moodle/question/type/preg/tmp_img/tree.cmapx')){
                echo "Can't delete file";
            }*/
            
            //Create description
            $description = new qtype_preg_author_tool_description($regextext);
            
            //Add description on form
            $mform->addElement('header', 'regexdescriptionheader', 'Description here:');
            $mform->addHelpButton('regexdescriptionheader','regexmatchheader','qtype_preg');
            $mform->addElement('html', $description->default_description());
            
        } else {
            $mform->setDefault('regextext', 'input regex');
            $mform->addElement('html', 'Interactive tree</br><img src="http://localhost/moodle/question/type/preg/tmp_img/tree_def.png" /></br>');        
            $mform->addElement('html', 'Graph</br><img src="http://localhost/moodle/question/type/preg/tmp_img/graph_def.png" />');
            
            //Add description on form
            $mform->addElement('header', 'regexdescriptionheader', 'Description here:');
            $mform->addHelpButton('regexdescriptionheader','regexmatchheader','qtype_preg');
            $mform->addElement('html', '<div id="description_handler">This is description</div>');
            
            //$question = qtype_preg_question::question_from_regex('regex', false, true, 'nfa_matcher', 'native');
            
            //$rend = new qtype_preg_hintnextchar($question);
            
            //$preg_hint = new qtype_preg_hintmatchingpart($question);
            //var_dump($preg_hint);
            //$rend = new qtype_preg_renderer();
            //var_dump($rend);
            //var_dump($preg_hint->render_hint(new qtype_preg_renderer() , array('answer' => 'Do rats eat bat?') ) );
            //$mform->addElement('html',  $preg_hint->render_hint($rend , array('answer' => 'Do rats eat bat?') ) );
        }
        
        //Add tool for check regexp match        
        $mform->addElement('header', 'regexmatchheader', 'Input string for check here:');
        $mform->addHelpButton('regexmatchheader','regexmatchheader','qtype_preg');
        
        $mform->addElement('text', 'regexmatchtext', 'Input string', array('size' => 100));
        $mform->registerNoSubmitButton('regexcheckstring');
        $mform->addElement('button', 'regexcheckstring', 'Check string');

        $mform->registerNoSubmitButton('regexnextcharacter');
        $mform->addElement('button', 'regexnextcharacter', 'Get next character');
        
        $mform->addElement('textarea', 'mustmatch', 'Must match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regexcheckmatch', 'Check match');
        
        $mform->addElement('textarea', 'mustnotmatch', 'Must not match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regexchecknotmatch', 'Check no match');
        
        //$mform->addElement('html', '<div id="script_test"><script type="text/javascript" src="http://localhost/moodle/question/type/preg/preg_authors_tool_script.js"></script></div>');
    }
    
    /*function definition_inner($mform){
        //$mform->addElement('button', 'testbuton1', 'PRESS ME!!!');
        return true;
    }*/
    
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
?>
