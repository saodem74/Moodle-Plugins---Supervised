<?php

defined('MOODLE_INTERNAL') || die;

if($ADMIN->fulltree) {
$settings->add(new admin_setting_configtext('qtype_preg_maxerrorsshown', get_string('maxerrorsshownlabel', 'qtype_preg'),
                                                get_string('maxerrorsshowndescription', 'qtype_preg'), 5, PARAM_INT));

$settings->add(new admin_setting_heading('dfaheading', get_string('dfaheading', 'qtype_preg'), get_string('engineheadingdescriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_dfastatecount', get_string('maxfasizestates', 'qtype_preg'),
                                                get_string('dfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_dfapasscount', get_string('maxfasizetransitions', 'qtype_preg'),
                                                get_string('dfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));

$settings->add(new admin_setting_heading('nfaheading', get_string('nfaheading', 'qtype_preg'), get_string('engineheadingdescriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_nfastatelimit', get_string('maxfasizestates', 'qtype_preg'),
                                                get_string('nfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_nfatransitionlimit', get_string('maxfasizetransitions', 'qtype_preg'),
                                                get_string('nfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));

$settings->add(new admin_setting_heading('debugheading', get_string('debugheading', 'qtype_preg'), ''));
$settings->add(new admin_setting_configtext('qtype_preg_graphvizpath', get_string('gvpath', 'qtype_preg'),
                                                get_string('gvdescription', 'qtype_preg'), 'C:\\Program Files\\GraphViz\\bin\\', PARAM_RAW_TRIMMED));
}
?>