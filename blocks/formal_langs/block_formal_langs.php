<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/lib/accesslib.php');

class block_formal_langs extends block_base {
    //TODO: Implement this
    public function init() {
        $this->title = get_string('pluginname', 'block_formal_langs');
    }

    function has_config() {
        return true;
    }

    /**
     * Returns an array of languages for given context
     *
     * @param int $contextid id of context, null means whole site
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function available_langs($contextid = null) {
        global $CFG;
        $languages = block_formal_langs::all_languages();
        // TODO - create a table with eye icons and set "visible" DB field for the language accordingly instead of using $CFG->xxx.
        $showedlanguages = $CFG->block_formal_langs_showablelangs;
        if (textlib::strlen($showedlanguages) != 0)
        {
            $availablelanguages = array();
            $showedlanguages = explode(',', $showedlanguages);
            foreach($showedlanguages as $langkey)
            {
                // Copy only visible langugages.
                $availablelanguages[$langkey] = $languages[$langkey];
            }
        } else {
            $availablelanguages = $languages;
        }
        return $availablelanguages;
    }

    /**
     * This function returns all languages.
     *
     * It is used in language configuration only and doesn't respect admin setting for available languages. 
     * For interaction with user please use function available_langs().
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function all_languages() {
        global $DB;

        //BUG: When installing moodle 2.5 settings of correctwriting will eventually call this function
        // before table created
        $dbman = $DB->get_manager();
        if ($dbman->table_exists('block_formal_langs') == false) {
            return array();
        }

        //Get all visible records
        $records = $DB->get_records('block_formal_langs', array('visible' => '1'));

        //Map, that checks amount of unique names in table. Populate it with values
        $counts = array();
        foreach($records as $record) {
            if ($record->name !== null) {//Predefined language, uiname is actually a language string, so replace it with actual name.
                $record->uiname = get_string('lang_' . $record->name , 'block_formal_langs');
            }
            if (array_key_exists($record->uiname, $counts)) {
                $counts[$record->uiname] = $counts[$record->uiname] + 1;
            } else {
                $counts[$record->uiname] = 1;
            }
        }
        //Populate result array
        $result = array();
        foreach($records as $record) {
            if ($counts[$record->uiname] > 1) {
                $result[$record->id] = $record->uiname . ' ' . $record->version;
            } else {
                $result[$record->id] = $record->uiname;
            }
        }

        return $result;
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param int $langid id of the language
     * @return block_formal_langs_abstract_language an intialised object of the child of the block_formal_langs_abstract_language class
     */
    public static function lang_object($langid) {
        global $DB, $CFG;
        $record = $DB->get_record('block_formal_langs', array('id' => $langid));
        $result = null;
        $arrayrecord = (array)$record;
        if ($arrayrecord['name'] == null) {
            $result = new block_formal_langs_userdefined_language($langid, $record->version, $record);
        } else {
            require_once($CFG->dirroot.'/blocks/formal_langs/language_' . $record->name . '.php');
            $langname = 'block_formal_langs_language_' . $record->name;
            $result = new $langname($langid, $record);
        }
        return $result;
    }

    /**
     * Finds or inserts language definition.
     * All fields must be set
     * @param array $language as tuple <ui_name, description, name, scanrules, parserules, version visible>.
     * @return int id of inserted language
     */
    public static function find_or_insert_language($language) {
        global $DB, $CFG;
        // Seek for language and insert it if not found, handling some error stuff
        // Also cannot compare strings in some common case.
        $sql = 'SELECT id
                      FROM {block_formal_langs}
                     WHERE ';
        $filternames = array('name', 'version');
        $filtervalues = array($language['name'], $language['version']);
        if ($language['scanrules'] != null || $language['parserules'] != null) {
            $filternames[] = 'scanrules';
            $filternames[] = 'parserules';
            $filtervalues[]  = $language['scanrules'] ;
            $filtervalues[]  = $language['parserules'];
        }
        // Transform columns into sql comparisons
        $sqlfilternames = array();
        foreach($filternames as $name) {
            $sqlfilternames[] = $DB->sql_compare_text($name, 512) . ' = ' . $DB->sql_compare_text('?', 512);
        }
        // Build actual sql request
        $sql .= implode(' AND ', $sqlfilternames);
        $sql .= ';';

        $record = $DB->get_record_sql($sql, $filtervalues);
        if ($record == false) {
            $result = $DB->insert_record('block_formal_langs', $language);
            $setting = block_formal_langs::get_visible_language_setting();
            $showedlanguages = $CFG->block_formal_langs_showablelangs;
            $showedarray = explode(',', $showedlanguages);
            $showedarray[] = $result;
            $realshowedarray = array();
            foreach($showedarray as $id) {
                $realshowedarray[$id] = 1;
            }
            $setting->write_setting($realshowedarray);
            block_formal_langs::sync_contexts_with_config();
        } else {
            $result = $record->id;
        }
        return $result;
    }

    /**
     * Gets showable language setting
     * @return block_formal_langs_admin_setting_visible_languages
     */
    public static function get_visible_language_setting() {
        $cfgname = 'block_formal_langs_showablelangs';
        $label =  get_string('visiblelangslabel', 'block_formal_langs');
        $description = get_string('visiblelangsdescription', 'block_formal_langs');
        $default = array('1' => '1');
        $setting  = new block_formal_langs_admin_setting_visible_languages($cfgname, $label, $description, $default, null);
        $setting->load_choices();
        return $setting;
    }

    /**
     * Synchronizes context informations with config
     */
    public static function sync_contexts_with_config($result = null) {
        global $CFG, $DB;
        // Sometimes this is called during install, so we need to check tables for
        // existence, otherwise it would inevitably fail with dml_exception
        $dbman = $DB->get_manager();
        $langsdoesnotexists = $dbman->table_exists('block_formal_langs') == false;
        $permsdoesnotexists = $dbman->table_exists('block_formal_langs_perms') == false;
        if ($langsdoesnotexists || $permsdoesnotexists)
            return;

        $systemcontextid = context_system::instance()->id;
        $showedlanguages = $CFG->block_formal_langs_showablelangs;
        if ($result !== null)
            $showedlanguages = $result;
        $showedarray = array();
        $showall = true;
        //  Fetch languages
        $languagerecords = $DB->get_records('block_formal_langs', array());
        //  Fetch global permissions
        if (textlib::strlen($showedlanguages) != 0)
        {
            $showedarray = explode(',', $showedlanguages);
            $showall = false;
        }
        // Fetch and build hash-table of permissions
        $globalpermrecords = $DB->get_records('block_formal_langs_perms', array('contextid' => $systemcontextid));
        $globalpermissions = array();
        foreach($globalpermrecords as $record) {
            $globalpermissions[$record->languageid] = $record;
        }

        foreach($languagerecords as $record) {
            // Compute visible flags
            if ($showall) {
                $visible = 1;
            } else {
                $visible = in_array($record->id, $showedarray);
                $visible = ($visible) ? 1 : 0;
            }
            // Select action, based on permissions
            $shouldinsert = false;
            $shouldupdate = false;
            $updateid = -1;
            if (is_array($globalpermissions)) {
                if (array_key_exists($record->id, $globalpermissions)) {
                    $permission =  $globalpermissions[$record->id];
                    $shouldupdate = $permission->visible != $visible;
                    $updateid =  $permission->id;
                } else {
                    $shouldinsert = true;
                }
            }  else {
                $shouldinsert = true;
            }
            $dataobject = new stdClass();
            $dataobject->languageid = $record->id;
            $dataobject->contextid = $systemcontextid;
            $dataobject->visible = $visible;
            if ($updateid > -1) {
                $dataobject->id = $updateid;
            }
            if ($shouldinsert) {
                $DB->insert_record('block_formal_langs_perms', $dataobject, false, true);
            }

            if ($shouldupdate) {
                $DB->update_record('block_formal_langs_perms', $dataobject, true);
            }

        }
    }

    /**
     * Builds visibility table for array of contexts
     * @param  array $contexttree of int list of parent contexts
     * @return array of stdClass <int id, string name, string ui_name, string version, bool visible>
     */
    public function build_visibility_for_all_languages($contexttree) {
        global $DB;
        $languages = $DB->get_records('block_formal_langs', array('visible' => '1'));
        if (count($contexttree) == 0) {
            $contexttree = array( context_system::instance()->id );
        }
        // Fetch associated context data.
        list($insql, $params) = $DB->get_in_or_equal($contexttree);
        $sql = 'SELECT *
                FROM {block_formal_langs_perms}
                WHERE contextid ' . $insql;
        $contextspermissions = $DB->get_records_sql($sql, $params);
        $sql = 'SELECT id, depth
                FROM {context}
                WHERE id ' . $insql;
        $contexts = $DB->get_records_sql($sql, $params);
        foreach($languages as $language) {
            // Fetch permissions for language.
            $permissionsforlanguage = array();
            foreach($contextspermissions as $permission) {
                if ($permission->languageid == $language->id) {
                    $permissionsforlanguage[$permission->contextid] = $permission->visible;
                }
            }
            // Compute permission for context with max depth
            $maxdepth = -1;
            $maxdepthid = -1;
            if (count($permissionsforlanguage)) {
                foreach($permissionsforlanguage as $contextid => $contextvalue) {
                    if (array_key_exists($contextid, $contexts)) {
                        $depth = $contexts[$contextid]->depth;
                        if ($depth > $maxdepth) {
                            $maxdepth = $depth;
                            $maxdepthid = $contextid;
                        }
                    }
                }
            }
            if ($maxdepthid > 0) {
                $language->visible = $permissionsforlanguage[$maxdepthid];
            }
        }
        return $languages;
    }

    /**
     * Updates language visibility for language list.
     * @param int $languageid id of language
     * @param int $visibility visibility of data
     * @param int $contextid id of context for items
     */
    public function update_language_visibility($languageid, $visibility, $contextid) {
        global $DB;
        $contexttree = context::instance_by_id($contextid)->get_parent_context_ids(true);
        list($insql, $params) = $DB->get_in_or_equal($contexttree);
        array_unshift($params, $languageid);
        $sql = 'SELECT perms.*, ctx.depth
                FROM {block_formal_langs_perms} perms,
                {context} ctx WHERE perms.languageid = ? AND perms.contextid '
               . $insql
               . ' AND  ctx.id = perms.contextid ORDER BY depth DESC LIMIT 2';
        $contextpermissions = $DB->get_records_sql($sql, $params);
        $visibility = ($visibility > 0) ? 1 : 0;
        $existentobject = new stdClass();
        $existentobject->languageid = $languageid;
        $existentobject->contextid = $contextid;
        $existentobject->visible = $visibility;
        if (count($contextpermissions) == 0) {
            $DB->insert_record('block_formal_langs_perms', $existentobject);
        } else {
            $currentpermission =  array_shift($contextpermissions);
            /* If settings exists for current context,
               we could either update or delete setting.
               Otherwise we could either insert new setting or do nothing.
             */
            if ($currentpermission->contextid == $contextid) {
                $parentvisibilitymatches = false;
                if (count($contextpermissions) > 0) {
                    $parentpermission = array_shift($contextpermissions);
                    $parentvisibilitymatches = $parentpermission->visible == $visibility;
                }
                // If current permission matches visibility - we won't change anything.
                // Otherwise, we could delete data.
                if ($parentvisibilitymatches  && $currentpermission->visible != $visibility) {
                    $DB->delete_records('block_formal_langs_perms', array('id' => $currentpermission->id));
                } else {
                    if ($currentpermission->visible != $visibility) {
                        $existentobject->id = $currentpermission->id;
                        $DB->update_record('block_formal_langs_perms', $existentobject);
                    }
                }
            } else {
                // First permission will be parent.
                $parentpermission = $currentpermission;
                if ($parentpermission->visible != $visibility) {
                    $DB->insert_record('block_formal_langs_perms', $existentobject);
                }
            }
        }
    }

    /**
     * @deprecated
     * DO NOT ADD THIS BLOCK! This code is for testing only!
     * @return null|stdClass
     */
    public function deprecated_get_content() {
        global $CFG, $USER, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // Insert language.
        $language = new stdClass();
        $language->name = 'Test language';
        $language->uiname = 'Test';
        $language->description = 'Description';
        $language->scanrules = 'a';
        $language->parserules = 'a';
        $language->version = '1.0';
        $language->visible = '1';
        $language->lexemname = 'lexeme';
        $language->id = $this->find_or_insert_language((array)$language);
        if ($language->id <= 0) {
            $this->content->text .= 'Failed to insert language<br />';
        } else {
            $this->content->text .= 'Successfully inserted language <br />';
        }
        $language->scanrules = 'b';

        // Update language.
        $DB->update_record('block_formal_langs', $language);
        $testlanguage  = $DB->get_record('block_formal_langs', array('id' => $language->id));
        if ($testlanguage->scanrules != $language->scanrules) {
            $this->content->text .= 'Failed to update language <br />';
        } else {
            $this->content->text .= 'Successfully updated language<br />';
        }
        // Delete language.
        $DB->delete_records('block_formal_langs', array('id' => $language->id));
        $testlanguage  = $DB->get_record('block_formal_langs', array('id' => $language->id));
        if ($testlanguage != false) {
            $this->content->text .= 'Failed to remove language<br />';
        } else {
            $this->content->text .= 'Successfully removed language <br />';
        }

        $tree = array_keys( $this->page->context->get_parent_contexts(true) );
        // Somehow inner context id differs from outer context id, So we add it.
        $tree[] = $this->context->id;

        $this->content->text .= '<pre>';
        $this->content->text .= var_export($this->build_visibility_for_all_languages($tree), true);
        $this->content->text .= '</pre>';

        $this->update_language_visibility(1, 0, $this->context->id);
        $this->content->text .= 'After first update <br /><pre>';
        $this->content->text .= var_export($this->build_visibility_for_all_languages($tree), true);
        $this->content->text .= '</pre>';
        $this->update_language_visibility(1, 1, $this->context->id);
        $this->content->text .= 'After second update <br /><pre>';
        $this->content->text .= var_export($this->build_visibility_for_all_languages($tree), true);
        $this->content->text .= '</pre>';


        return null;
    }

    public function get_content() {
        global $_REQUEST;
        if ($_REQUEST['debug'] == 'Y') {
            return $this->deprecated_get_content();
        }
        return null;
    }
}
