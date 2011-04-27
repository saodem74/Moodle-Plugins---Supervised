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
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package   mod_poasassignment
 * @copyright 2010 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 */
function xmldb_poasassignment_install() {
    global $DB;
    
    // Add info about default plugins into table
    //$record->name='poasassignment_answer_file';
    //$record->path='answer/answer_file.php';
    //if (!$DB->record_exists('poasassignment_answers',array('name'=>$record->name,'path'=>$record->path)))
    //    $DB->insert_record('poasassignment_answers',$record);
    
        
    //$record->name='poasassignment_answer_text';
    //$record->path='answer/answer_text.php';
    //if (!$DB->record_exists('poasassignment_answers',array('name'=>$record->name,'path'=>$record->path)))
    //    $DB->insert_record('poasassignment_answers',$record);

    // Add taskgivers in table
    /* $record = new stdClass();

    $files = scandir( dirname(dirname(__FILE__)).'\\taskgivers');
    foreach($files as $file) {
        if(is_dir(dirname(dirname(__FILE__)).'\\taskgivers\\'.$file) && $file !== '.' && $file !== '..') {
            $record->name = $file;
            //echo $record->name.'<br>';
            $record->path = 'taskgivers\\'.$file.'\\'.$file.'.php';
            //echo $record->path.'<br>';
            $record->langpath = 'taskgivers\\'.$file.'\\lang';
            //echo $record->langpath.'<br>';
            if (!$DB->record_exists('poasassignment_taskgivers',array('path'=>$record->path)))
                $DB->insert_record('poasassignment_taskgivers',$record);
        }
     }*/

    // Add message provider
    $provider = new stdClass();
    $provider->name = 'poasassignment_updates';
    $provider->component='mod_poasassignment';
    if(!$DB->record_exists('message_providers',array('name'=>$provider->name, 'component'=>$provider->component)))
        $DB->insert_record('message_providers',$provider);
}
