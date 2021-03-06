<?php
// This file is part of Student Access Control Kit - https://bitbucket.org/oasychev/moodle-plugins/overview
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * @package     block
 * @subpackage  supervised
 * @author      Hieu Tran <trantrunghieu7492@gmail.com>
 * @copyright   2016 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once('../../../user/selector/lib.php');
require_once('../sessions/lib.php');
require_once('../../../group/lib.php');
global $DB;

$courseid = required_param('courseid', PARAM_INT);
$groupid = required_param('group', PARAM_INT);
$sessionid = required_param('sessionid', PARAM_INT);
$urlreturn = required_param('urlreturn', PARAM_INT);
$cancel  = optional_param('cancel', false, PARAM_BOOL);
$destroy  = optional_param('destroy', false, PARAM_BOOL);
$editmode = required_param('editmode', PARAM_BOOL);
$reload = required_param('reload', PARAM_BOOL);

if ($groupid == -1 && !$reload) {
    $data->name = get_string('internship', 'block_supervised');
    $data->courseid = $courseid;
    $groupid = groups_create_group($data);
}


$group = $DB->get_record('groups', array('id'=>$groupid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$group->courseid), '*', MUST_EXIST);
$usersinsession = $DB->get_records('block_supervised_user', array('sessionid' => $sessionid));

$PAGE->set_url('/blocks/supervised/groups/members.php', array('courseid' => $courseid, 'group' => $groupid, 'sessionid' => $sessionid, 'urlreturn' => $urlreturn, 'editmode' => $editmode, 'reload' => $reload));
$PAGE->set_pagelayout('admin');

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:managegroups', $context);

$groupmembersselector = new group_members_selector('removeselect', array('groupid' => $groupid, 'courseid' => $course->id));
$potentialmembersselector = new group_non_members_selector('addselect', array('groupid' => $groupid, 'courseid' => $course->id));

$returnurl = new moodle_url('/blocks/supervised/groups/refreshing.php', array('courseid' => $course->id, 'group' => $groupid, 'urlreturn' => $urlreturn, 'editmode' => $editmode));
if ($destroy){
    $returnurl = new moodle_url('/blocks/supervised/groups/refreshing.php', array('courseid' => $course->id, 'group' => $groupid,
        'urlreturn' => $urlreturn, 'destroy' => true, 'sessionid' => $sessionid, 'editmode' => $editmode));
    if ($editmode) {
        $currusersingroup = groups_get_members($groupid);
        foreach ($currusersingroup as $curuser) {
            if (!groups_remove_member_allowed($groupid, $curuser->id)) {
                print_error('errorremovenotpermitted', 'group', $returnurl,
                    $curuser->fullname);
            }
            if (!groups_remove_member($groupid, $curuser->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
        foreach ($usersinsession as $curuser) {
            if (!groups_add_member($groupid, $curuser->userid)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
        delete_all_users_in_session($sessionid);
        update_users_in_session($groupid, $courseid, $sessionid);
    }
    redirect($returnurl);
}

if ($cancel) {
    delete_all_users_in_session($sessionid);
    update_users_in_session($groupid, $courseid, $sessionid);
    if (amount_user_in_session($sessionid) == 0) {
        echo $OUTPUT->heading(get_string('emptygroup', 'block_supervised'), 2);
    } else {
        redirect($returnurl);
    }
}

// Get old users from sessionid.
if (!$reload) {
    foreach ($usersinsession as $curuser) {
        if (!groups_add_member($groupid, $curuser->userid)) {
            print_error('erroraddremoveuser', 'group', $returnurl);
        }
        $groupmembersselector->invalidate_selected_users();
        $potentialmembersselector->invalidate_selected_users();
    }
    $reload = true;
    $returnurl = new moodle_url('/blocks/supervised/groups/members.php', array('courseid' => $courseid, 'group' => $groupid,
        'sessionid' => $sessionid, 'urlreturn' => $urlreturn, 'editmode' => $editmode, 'reload' => $reload));
    redirect($returnurl);
}
if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoadd = $potentialmembersselector->get_selected_users();
    if (!empty($userstoadd)) {
        foreach ($userstoadd as $user) {
            if (!groups_add_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoremove = $groupmembersselector->get_selected_users();
    if (!empty($userstoremove)) {
        foreach ($userstoremove as $user) {
            if (!groups_remove_member_allowed($groupid, $user->id)) {
                print_error('errorremovenotpermitted', 'group', $returnurl,
                        $user->fullname);
            }
            if (!groups_remove_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

$groupname = format_string($group->name);

$PAGE->requires->js('/group/clientlib.js');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('adduserstogroup', 'group').": $groupname", 3);

// Store the rows we want to display in the group info.
$groupinforow = array();

// Check if there is a picture to display.
if (!empty($group->picture)) {
    $picturecell = new html_table_cell();
    $picturecell->attributes['class'] = 'left side picture';
    $picturecell->text = print_group_picture($group, $course->id, true, true, false);
    $groupinforow[] = $picturecell;
}

// Check if there is a description to display.
$group->description = file_rewrite_pluginfile_urls($group->description, 'pluginfile.php', $context->id, 'group', 'description', $group->id);
if (!empty($group->description)) {
    if (!isset($group->descriptionformat)) {
        $group->descriptionformat = FORMAT_MOODLE;
    }

    $options = new stdClass;
    $options->overflowdiv = true;

    $contentcell = new html_table_cell();
    $contentcell->attributes['class'] = 'content';
    $contentcell->text = format_text($group->description, $group->descriptionformat, $options);
    $groupinforow[] = $contentcell;
}

// Check if we have something to show.
if (!empty($groupinforow)) {
    $groupinfotable = new html_table();
    $groupinfotable->attributes['class'] = 'groupinfobox';
    $groupinfotable->data[] = new html_table_row($groupinforow);
    echo html_writer::table($groupinfotable);
}

/// Print the editing form
?>

<div id="addmembersform">
    <form id="assignform" method="post" action="<?php echo $CFG->wwwroot; ?>/blocks/supervised/groups/members.php?courseid=<?php echo $courseid;?>&group=<?php echo $groupid;?>&sessionid=<?php echo $sessionid;?>&urlreturn=<?php echo $urlreturn; ?>&editmode=<?php echo $editmode?>&reload=<?php echo $reload ?>">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
    <tr>
      <td id='existingcell'>
          <p>
            <label for="removeselect"><?php print_string('groupmembers', 'group'); ?></label>
          </p>
          <?php $groupmembersselector->display(); ?>
          </td>
      <td id='buttonscell'>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td id='potentialcell'>
          <p>
            <label for="addselect"><?php print_string('potentialmembs', 'group'); ?></label>
          </p>
          <?php $potentialmembersselector->display(); ?>
      </td>
    </tr>
    <tr><td colspan="3" id='backcell'>
        <input type="submit" name="cancel" value="<?php print_string('backtogroups', 'group'); ?>" />
    </td>
        <td colspan="3" id='backcell'>
            <input type="submit" name="destroy" value="<?php print_string('cancel'); ?>" />
        </td>
    </tr>
    </table>
    </div>
    </form>
</div>

<?php
    //outputs the JS array used to display the other groups users are in
    $potentialmembersselector->print_user_summaries($course->id);

    //this must be after calling display() on the selectors so their setup JS executes first
    $PAGE->requires->js_init_call('init_add_remove_members_page', null, false, $potentialmembersselector->get_js_module());

    echo $OUTPUT->footer();
