<?php
/*********************************************
  Personal Pages, module for DragonflyCMS
  ********************************************

  author:       Andy Rink aka personman
  date:         4/12/09
  version:      v2.7.1
  authemail:    personman_145@hotmail.com
  authurl:      http://AnarchismToday.org

  This module is based on the Personality 
  module by gtown, which was based on the
  blogs module of DJMaze and Trevor from 
  www.cpgnuke.com
  Special thanks to dcollis!

**********************************************/


if (!defined('CPG_NUKE')) { exit; }

$version = "v2.7.1";

// All configuration has been moved to the Administration Panel as of v2.7.1

//**********************************************
// Modding Personal Pages:
//
// Personal Pages are displayed in either User Mode, or Display mode.
// In general you will want to make a given change to both modes.
// Comments can only be shown in Display Mode.
//**********************************************

if (is_array($MAIN_CFG['Personal'])) {
	$personal_conf = $MAIN_CFG['Personal'];
}

$pagetitle .= _PTY_TITLE;

require_once('includes/nbbcode.php');
require_once('modules/Your_Account/functions.php');
require_once('modules/Personal/userinfo.php');
//require_once('modules/Personal/centerblocks.php');
//global $userinfo;
$username = $userinfo['username'];

$mode = (isset($_POST['mode'])&& $_POST['mode']!='') ? $_POST['mode'] : (isset($_GET['mode'])&& $_GET['mode']!='') ? $_GET['mode'] : '';

/* LINES FOR PERSONAL PAGE USE */
list($num_blogs) = $db->sql_ufetchrow("SELECT COUNT(*) FROM ".$prefix."_personality WHERE aid='$username' AND private=0 LIMIT 1");
/* LINES FOR PERSONAL PAGE USE END*/

/* edited 
	//global $db, $prefix, $user_prefix, $currentlang, $pagetitle, $MAIN_CFG, $CPG_SESS, $CLASS;
	$owninfo = (is_user() && ($username == is_user() || strtolower($username) == strtolower($CLASS['member']->members[is_user()]['username'])));
	
	
	$imgpath = 'themes/'.$CPG_SESS['theme'].'/images/forums/lang_';
	$imgpath .= (file_exists($imgpath.$currentlang.'/icon_email.gif') ? $currentlang : 'english');

 edited */

if (isset($_POST['post_entry']) && is_user()) {
    $blog_title = Fix_Quotes(check_words($_POST['title']), 1);
    //$blog_text = Fix_Quotes(check_words($_POST['bodytext']), 1);
    $blog_text = Fix_Quotes($_POST['bodytext']);
    $blog_private = intval($_POST['private']);
    if (strlen($blog_title) < 3) {
        cpg_error(sprintf(_ERROR_NOT_SET, 'Blog title'));
    }
    if (strlen($blog_text) < 5) {
        cpg_error(sprintf(_ERROR_NOT_SET, 'Blog text'));
    }
    if ($db->sql_ufetchrow("SELECT id FROM ".$prefix."_personality WHERE aid='$username' LIMIT 1")) {
	cpg_error(sprintf(_PTY_PERSONALERROR));
    }
    else {
	$db->sql_query("INSERT INTO ".$prefix."_personality VALUES (NULL, '$username', '$blog_title', '$blog_text', '$blog_private', '".gmtime()."')");
	url_redirect(getlink('Personal'));
    }

} elseif (isset($_POST['post_comment'])) {
    if (!is_user() && $personal_conf['anon_comment'] && !validate_secimg()) { cpg_error(_SECURITYCODE.' incorrect'); }
    $comment_blog = intval($_POST['blog_id']);
    $comment_name = Fix_Quotes(check_words($_POST['comment_name']), 1);
    $comment_email = Fix_Quotes(check_words($_POST['comment_email']), 1);
    //$comment_text = Fix_Quotes(check_words($_POST['comment_text']), 1);
    $comment_text = Fix_Quotes($_POST['comment_text']);
    $comment_ip = $userinfo['user_ip'];
    if (strlen($comment_name) < 3) {
        cpg_error(sprintf(_ERROR_NOT_SET, 'Comment name'));
    }
    if (strlen($comment_email) < 3) {
        cpg_error(sprintf(_ERROR_NOT_SET, 'Comment email'));
    }
    if (strlen($comment_text) < 3) {
        cpg_error(sprintf(_ERROR_NOT_SET, 'Comment text'));
    }
    if ($personal_conf['comment_limit'] >= 1) {
        $comment_text = substr($comment_text, 0, $personal_conf['comment_limit']);
    }
    $db->sql_query("INSERT INTO ".$prefix."_personality_greetings VALUES (NULL, '$comment_blog', '$comment_name', '$comment_email', '$comment_ip', '$comment_text', '".gmtime()."')");
    url_redirect(getlink('&mode=display&id='.$comment_blog.'&comments=show'));
} elseif (isset($_POST['revise_entry']) && (is_user() || is_admin())) {
    $blog_title = Fix_Quotes(check_words($_POST['title']), 1);
    //$blog_text = Fix_Quotes(check_words($_POST['bodytext']), 1);
    $blog_text = Fix_Quotes($_POST['bodytext']);
    $blog_private = intval($_POST['private']);
    $blog_id = intval($_POST['blog_id']);
    $db->sql_query("UPDATE ".$prefix."_personality SET title='$blog_title', text='$blog_text', private='$blog_private', timestamp='".gmtime()."' WHERE id='$blog_id'");
    url_redirect(getlink('&mode=display&id='.$blog_id));

    //*************************************
    // Display Personal Page in User Mode
    //*************************************

} elseif ($mode == 'user' || isset($_GET['u']) ) {
    $lookup_username = isset($_POST['nick'])&& (!empty($_POST['nick'])) ? $_POST['nick'] : (isset($_GET['nick'])) ? $_GET['nick'] : (isset($_GET['u']) ? $_GET['u'] : cpg_error(sprintf(_ERROR_NOT_SET, _NICKNAME), _SEC_ERROR));
    /* This doesn't allow for UTF-8 usernames
    if (!ereg("^([a-zA-Z0-9_\-]+)$", $lookup_username)) {
        cpg_error(sprintf(_ERROR_BAD_CHAR, strtolower(_PTY_BLOG)), _SEC_ERROR);
    }
    */
    if (isset($_GET['u']) && $_GET['u'] != '') { $lookup_username = $_GET['u']; }
    $lookup_username = Fix_Quotes($lookup_username);
    $priv = ($lookup_username == $userinfo['username']) ? '' : "AND private='0'"; 
    //$result = $db->sql_query("SELECT id, title, timestamp FROM ".$prefix."_personality WHERE aid='".$lookup_username."' $priv  ORDER BY timestamp DESC");
    $resulta = $db->sql_query("SELECT * FROM ".$prefix."_personality WHERE aid='".$lookup_username."' $priv  ORDER BY timestamp DESC");
    if ($db->sql_numrows($resulta) < 1) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_PTY_PERSONALERROR2).''.$username.''.sprintf("!").'</td></tr>
        </table>');
    }
    if ($lookup_username) { $pagetitle .= ' - '.$lookup_username; }
    $lookup_username = ucwords($lookup_username);
    $entry = 0;
    $loop = 0;
    $offset = (isset($_GET['offset'])) ? intval($_GET['offset']) : 0;
    $offset_display = ($offset != 0) ? $offset : 7;
    if (is_user()) {
        define('MEMBER_BLOCK', true);
    }
    require_once('header.php');
    $num = 1;

/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON START*/
    if ($num_blogs == 0){
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" align="left"><a href="'.getlink().'">'._PTY_MAIN.'</a> | '.((is_user() && $lookup_username == $userinfo['username']) ? '<b>'._PTY_MYBLOG.'</b> | ' : (is_user() ? '<a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a> | ' : '')).'<a href="'.getlink('&amp;mode=add').'">'._PTY_CREATE_TITLE.'</a></td></tr>
    <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.sprintf(_PTY_POSESSION,$lookup_username).' '._PTY_BLOG.'</span></b></td></tr>';
    }


    if ($num_blogs > 0){
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" align="left"><a href="'.getlink().'">'._PTY_MAIN.'</a> | '.((is_user() && $lookup_username == $userinfo['username']) ? '<b>'._PTY_MYBLOG.'</b>   ' : (is_user() ? '<a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a>   ' : '')).'</td></tr>
    <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.sprintf(_PTY_POSESSION,$lookup_username).' '._PTY_BLOG.'</span></b></td></tr>';
    }
/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON END*/


    echo '<tr><td class="row1" colspan="2" align="center">'._PTY_SEARCH_LIST.' '.sprintf(_PTY_POSESSION,$lookup_username).' '._PTY_SEARCH_LIST2.'</td></tr>';

    // Edited
    userinfo($lookup_username);

    while (list($blog_id, $blog_author, $blog_title, $blog_text, $blog_private, $blog_timestamp) = $db->sql_fetchrow($resulta)) {
        $resultc = $db->sql_query("SELECT * FROM ".$prefix."_personality_greetings WHERE bid='$blog_id' ORDER BY timestamp ASC");
        $blog_comments = $db->sql_numrows($resultc);
        $blog_text = decode_bb_all($blog_text, 1, $personal_conf['allow_html']);
        $blog_timestamp = formatDateTime($blog_timestamp, _DATESTRING);
  if ($entry < $offset) {
            $entry++;
        } else {
            if ($loop < 7) {
                echo '<tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.$blog_title.'</span></b></td></tr>
    <tr><td class="row1" colspan="2" align="center">'.$blog_timestamp.'</td></tr>
    <tr><td class="row1" colspan="2"><span class="gen">'.$blog_text.'</span></td></tr>
    </table><table border="0" width="100%"><td class="row1" colspan="2" border="0"><span class="gen">
    </span></td></tr></table><table width="100%" colspan="2" class="forumline">
    <td class="row1" colspan="2" align="center"><a href="'.getlink('&amp;mode=display&amp;id='.$blog_id.'&amp;comments=show').'">'._PTY_COMMENTS.' ('.$blog_comments.')</a>'.' | <a href="'.getlink('Your_Account&amp;op=userinfo&amp;username='.$blog_author).'">'.$blog_author.'\'s '._PTY_PROFILE.'</a> '.((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=edit&amp;id='.$blog_id).'">'._PTY_EDIT_TITLE.'</a>' : '').((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=remove&amp;type=blog&amp;id='.$blog_id).'">'._PTY_REMOVE_BLOG.'</a>' : '').'</td></tr>
    ';
                $num++;
                $loop++;
            }
        }
    }
    $numrows = $db->sql_numrows($resulta);
    if ($numrows < 1) {
        echo '<tr><td class="row1" colspan="2" align="center"><span class="gen">'._PTY_SEARCH_NOENTRIES.' '.$lookup_username.'</span></td></tr>';
    }
    if (!$offset) {
        $page_num = 7;
    } else {
        $page_num = $loop + $offset;
    }
    if ($numrows > 7) {
         echo '<tr><td class="row1" colspan="2" align="right">';   
    }
    if ($numrows > 7 && $offset) {
        echo '<a href="'.getlink('&amp;mode=user&amp;nick='.$lookup_username.'&amp;offset='.($offset-7)).'">'._PTY_PREV.'</a>';
    }
    if ($numrows > 7 && $offset && $numrows > $page_num) {
        echo ' | ';
    }
    if ($numrows > 7 && $numrows > $page_num) {
        echo '<a href="'.getlink('&amp;mode=user&amp;nick='.$lookup_username.'&amp;offset='.$page_num).'">'._PTY_NEXT.'</a> ';
    }
    if ($numrows > 7) {
        echo'</td></tr>';
    }
    $db->sql_freeresult($resulta);
       
        echo '</table>';
    
    // End of User Mode?

} elseif ($mode == 'remove') {
    $remove_type = Fix_Quotes($_GET['type'], 1);
    $remove_id = intval($_GET['id']);
    if (($remove_type == 'comment') && is_admin()) {
        $resulta = $db->sql_query("SELECT bid FROM ".$prefix."_personality_greetings WHERE cid='$remove_id'");
        if ($db->sql_numrows($resulta) < 1) {
            cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_ERROR_NO_EXIST, sprintf(_PTY_COMMENTS_COMMENT)).'</td></tr>
            </table>');
        }
        list($blog_id) = $db->sql_fetchrow($resulta);
        $db->sql_freeresult($resulta);
        if ($_GET['ok'] == 1) {
            $db->sql_query("DELETE FROM ".$prefix."_personality_greetings WHERE cid='$remove_id' LIMIT 1");
            url_redirect(getlink('&mode=display&id='.$blog_id.'&comments=show'));
        } else {
            $pagetitle .= ' '._BC_DELIM.' '._PTY_REMOVE_COMMENT;
            require_once('header.php');
            cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30"><span class="gen"><img src="images/warning.png" alt="'._WARNING.'" title="'._WARNING.'" /><br /><br />'.sprintf(_ERROR_DELETE_CONF, _PTY_THIS_COMMENT).'<br /><br />
            [ <a href="'.getlink().'">'._NO.'</a> | <a href="'.getlink('&amp;mode=remove&amp;type=comment&amp;id='.$remove_id.'&amp;ok=1').'">'._YES.'</a> ]</span></td></tr>
            </table>');
        }
    } elseif ($remove_type == 'blog') {
        $resulta = $db->sql_query("SELECT aid FROM ".$prefix."_personality WHERE id='$remove_id'");
        if ($db->sql_numrows($resulta) < 1) {
            cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_ERROR_NO_EXIST, _PTY_BLOG).'</td></tr>
            </table>');
        }
        list($author_id) = $db->sql_fetchrow($resulta);
        $db->sql_freeresult($resulta);
        if (($author_id != $userinfo['username']) && !is_admin()) {
            cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30">'._PTY_AUTHREQUIRED.'</td></tr>
            </table>');
        }
        if ($_GET['ok'] == 1) {
            $db->sql_query("DELETE FROM ".$prefix."_personality WHERE id='$remove_id' LIMIT 1");
            $db->sql_query("DELETE FROM ".$prefix."_personality_greetings WHERE bid='$remove_id'");
            url_redirect(getlink());
        } else {
            $pagetitle .= ' '._BC_DELIM.' '._PTY_REMOVE_BLOG;
            require_once('header.php');
            cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30"><span class="gen"><img src="images/warning.png" alt="'._WARNING.'" title="'._WARNING.'" /><br /><br />'.sprintf(_ERROR_DELETE_CONF, _PTY_THIS_BLOG).'<br /><br />
            [ <a href="'.getlink().'">'._NO.'</a> | <a href="'.getlink('&amp;mode=remove&amp;type=blog&amp;id='.$remove_id.'&amp;ok=1').'">'._YES.'</a> ]</span></td></tr>
            </table>');
        }
    } else {
        cpg_error(sprintf(_ERROR_NOT_SET, _PTY_TYPE), _SEC_ERROR);
    }

/*PERSONAL SITE USE OF BLOGS: ADDED ERRORMESSAGE IF USER HAS A PAGE ALREADY*/

} elseif ($mode == 'add'  && $num_blogs > 0) {
     cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
            <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_PTY_PERSONALERROR).'</td></tr>
            </table>');

/*PERSONAL SITE USE OF BLOGS: CHANGED elseif (added the "&& $num_blogs == 0") */
} elseif ($mode == 'add'  && $num_blogs == 0) {
    if (is_user()) {
        define('MEMBER_BLOCK', true);
    } else {
        url_redirect(getlink('Your_Account'), true);
    }
    $pagetitle .= ' '._BC_DELIM.' '._PTY_CREATE_TITLE;
    require_once('header.php');
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2"><a href="'.getlink().'">'._PTY_MAIN.'</a> | <a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a> | <b>'._PTY_CREATE_TITLE.'</b></td></tr>
    <form name="add_entry" action="'.getlink().'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
    <tr><td class="row1"><span class="gen">'._TITLE.'</span></td>
    <td class="row2"><input type="text" value="" size="50" maxlength="80" name="title" /></td></tr>
    <tr><td class="row1"><span class="gen">'._PTY_CREATE_TEXT.'</span></td>
    <td class="row2">'.bbcode_table('bodytext', 'add_entry', 1).'<textarea name="bodytext" wrap="virtual" cols="70" rows="15"></textarea>'.smilies_table('onerow', 'bodytext', 'add_entry').'</td></tr>
    <tr><td class="catbottom" colspan="2" align="center" height="28">
    <input type="submit" name="post_entry" class="mainoption" value="'._PTY_CREATE_ADDNEW.'" />
    </td></tr></table></form>';

} elseif ($mode == 'edit') {
    $edit_id = intval($_GET['id']);
    if (!is_user() && !is_admin()) {
        url_redirect(getlink('Your_Account'), true);
    }
    if (is_user()) {
        define('MEMBER_BLOCK', true);
    }
    $pagetitle .= ' '._BC_DELIM.' '._PTY_EDIT_TITLE;
    require_once('header.php');
    $resulta = $db->sql_query("SELECT * FROM ".$prefix."_personality WHERE id='$edit_id'");
    list($blog_id, $blog_author, $blog_title, $blog_text, $blog_private, $blog_timestamp) = $db->sql_fetchrow($resulta);
    if (($blog_author != $userinfo['username']) && !is_admin()) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'._PTY_AUTHREQUIRED.'</td></tr>
        </table>');
    }
    if ($blog_private && ($blog_author != $userinfo['username'])) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'._PTY_PRIVATE.'</td></tr>
        </table>');
    }
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2"><a href="'.getlink().'">'._PTY_MAIN.'</a> | <a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a></td></tr>
    <form name="edit_entry" action="'.getlink().'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
    <tr><td class="row1"><span class="gen">'._TITLE.'</span></td>
    <td class="row2"><input type="text" value="'.$blog_title.'" size="50" maxlength="80" name="title" /></td></tr>
    <tr><td class="row1"><span class="gen">'._PTY_CREATE_TEXT.'</span></td>
    <td class="row2">'.bbcode_table('bodytext', 'edit_entry', 1).'<textarea name="bodytext" wrap="virtual" cols="70" rows="15">'.$blog_text.'</textarea>'.smilies_table('onerow', 'bodytext', 'edit_entry').'</td></tr>
    <tr><td class="catbottom" colspan="2" align="center" height="28">
    <input type="hidden" name="blog_id" value="'.$blog_id.'" />
    <input type="submit" name="revise_entry" class="mainoption" value="'._SAVECHANGES.'" />&nbsp;&nbsp;<input type="reset" value="'._PTY_RESET.'" name="reset" class="liteoption" />
    </td></tr></table></form>';

    //***************************************
    // Display Personal Page in Display mode
    //***************************************

} elseif ($mode == 'display') {
    $disp_id = intval($_GET['id']);
    $resulta = $db->sql_query("SELECT * FROM ".$prefix."_personality WHERE id='$disp_id'");
    if ($db->sql_numrows($resulta) < 1) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_PTY_PERSONALERROR2).''.$username.''.sprintf("!").'</td></tr>
        </table>');
    }
    list($blog_id, $blog_author, $blog_title, $blog_text, $blog_private, $blog_timestamp) = $db->sql_fetchrow($resulta);
    $db->sql_freeresult($resulta);

    if ($blog_private && ($blog_author != $userinfo['username'])) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'._PTY_PRIVATE.'</td></tr>
        </table>');
    }
    if ($blog_author) { $pagetitle .= ' - '.$blog_author; }
    require_once('header.php');
    $resulta = $db->sql_query("SELECT * FROM ".$prefix."_personality_greetings WHERE bid='$blog_id' ORDER BY timestamp ASC");
    $blog_comments = $db->sql_numrows($resulta);
    $blog_text = decode_bb_all($blog_text, 1, $personal_conf['allow_html']);
    $blog_timestamp = formatDateTime($blog_timestamp, _DATESTRING);
/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON START*/

    if ($num_blogs > 0){
 
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2"><a href="'.getlink().'">'._PTY_MAIN.'</a> | '.((is_user()) ? '<a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a>   ' : '').'</td></tr>';
    // Edited
    userinfo($blog_author);
    echo '<tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.$blog_title.'</span></b></td></tr>
    <tr><td class="row1" colspan="2" align="center">'.$blog_timestamp.'</td></tr>
    <tr><td class="row1" colspan="2"><span class="gen">'.$blog_text.'</span></td></tr>
    </table><br><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2" align="center">'.(($_GET['comments'] == 'show') ? '<a href="'.getlink('&amp;mode=display&amp;id='.$blog_id).'">'._PTY_COMMENTS_HIDE.'</a>' : '<a href="'.getlink('&amp;mode=display&amp;id='.$blog_id.'&amp;comments=show').'">'._PTY_COMMENTS.' ('.$blog_comments.')</a>').' | <a href="'.getlink('Your_Account&amp;op=userinfo&amp;username='.$blog_author).'">'.$blog_author.'\'s '._PTY_PROFILE.'</a>'.((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=edit&amp;id='.$blog_id).'">'._PTY_EDIT_TITLE.'</a>' : '').((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=remove&amp;type=blog&amp;id='.$blog_id).'">'._PTY_REMOVE_BLOG.'</a>' : '').'</td></tr>
    </table>';
    }
    
    if ($num_blogs == 0){
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2"><a href="'.getlink().'">'._PTY_MAIN.'</a> | '.((is_user()) ? '<a href="'.getlink('&amp;mode=user&amp;nick='.$userinfo['username']).'">'._PTY_MYBLOG.'</a> | ' : '').'<a href="'.getlink('&amp;mode=add').'">'._PTY_CREATE_TITLE.'</a></td></tr>';
    // Edited
    userinfo($blog_author);
    echo '<tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.$blog_title.'</span></b></td></tr>
    <tr><td class="row1" colspan="2" align="center">'.$blog_timestamp.'</td></tr>
    <tr><td class="row1" colspan="2"><span class="gen">'.$blog_text.'</span></td></tr>
    </table><br><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2" align="center">'.(($_GET['comments'] == 'show') ? '<a href="'.getlink('&amp;mode=display&amp;id='.$blog_id).'">'._PTY_COMMENTS_HIDE.'</a>' : '<a href="'.getlink('&amp;mode=display&amp;id='.$blog_id.'&amp;comments=show').'">'._PTY_COMMENTS.' ('.$blog_comments.')</a>').' | <a href="'.getlink('Your_Account&amp;op=userinfo&amp;username='.$blog_author).'">'.$blog_author.'\'s '._PTY_PROFILE.'</a>'.((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=edit&amp;id='.$blog_id).'">'._PTY_EDIT_TITLE.'</a>' : '').((($blog_author == $userinfo['username']) || is_admin()) ? ' | <a href="'.getlink('&amp;mode=remove&amp;type=blog&amp;id='.$blog_id).'">'._PTY_REMOVE_BLOG.'</a>' : '').'</td></tr>
    </table>';
    }

/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON END*/

    //*************************************************
    // Here in Display Mode we display Personal Page Comments
    //*************************************************

    if ($_GET['comments'] == 'show') {
        echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="catleft" colspan="3" align="center"><b><span class="gen">'._PTY_COMMENTS.'</span></b></td></tr>';
        while (list($comment_id, $comment_blog, $comment_author, $comment_email, $comment_ip, $comment_text, $comment_timestamp) = $db->sql_fetchrow($resulta)) {
            $comment_ip = decode_ip($comment_ip);
	    if ( $personal_conf['comments_allow_bbcode'] || $personal_conf['comments_allow_html'] ) {
		    $comment_text = decode_bb_all($comment_text, 1, $personal_conf['comments_allow_html']);
	    }
            echo '<tr><td class="row1" colspan="2" valign="top" width="100%"><span class="gen">'.$comment_text.'</span></td><td valign="top"><center><a href="'.getlink('Your_Account&profile='.$comment_author).'">'.$comment_author.'</a><br>';


global $MAIN_CFG, $CPG_SESS;

$commenterinfo = getusrdata($comment_author,'user_avatar_type,user_avatar');

  if ($commenterinfo['user_avatar_type'] == 1) {
    $avatar = $MAIN_CFG['avatar']['path'].'/'.$commenterinfo['user_avatar'];
  } else if ($commenterinfo['user_avatar_type'] == 2) {
    $avatar = $commenterinfo['user_avatar'];
  } else if ($commenterinfo['user_avatar_type'] == 3 && !empty($commenterinfo['user_avatar'])) {
    $avatar = $MAIN_CFG['avatar']['gallery_path'].'/'.$commenterinfo['user_avatar'];
  } else if (file_exists('themes/'.$CPG_SESS['theme'].'/'.$MAIN_CFG['avatar']['gallery_path'].'/'.$MAIN_CFG['avatar']['default'])) {
    $avatar = 'themes/'.$CPG_SESS['theme'].'/'.$MAIN_CFG['avatar']['gallery_path'].'/'.$MAIN_CFG['avatar']['default'];
  } else {
    $avatar = $MAIN_CFG['avatar']['gallery_path'].'/'.$MAIN_CFG['avatar']['default'];
  }
  if ($avatar) {
    $avatar = '<a href="'.getlink('Your_Account&profile='.$comment_author).'"><img src="'.$avatar.'" alt="" /></a>';
    echo $avatar;
  }
	    echo '<br></center></td>';
            if (is_admin()) {
                echo '<tr><td class="row1" align="left">'._PTY_GREETPOST.' '.((!empty($comment_email)) ? '<a href="index.php?name=Your_Account&op=userinfo&username='.$comment_author.'">'.$comment_author.'</a>' : $comment_author).' | '.formatDateTime($comment_timestamp, _DATESTRING).'</td>
		      <td class="row2" align="right">IP: <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput='.$comment_ip.'" target="ResourceWindow">'.$comment_ip.'</a> | <a href="'.getlink('&amp;mode=remove&amp;type=comment&amp;id='.$comment_id).'">'._PTY_REMOVE.'</a></td><td></td>';
            } else {
		echo '<tr><td colspan="3" align="left">'._PTY_GREETPOST.' '.((!empty($comment_email)) ? '<a href="index.php?name=Your_Account&op=userinfo&username='.$comment_author.'">'.$comment_author.'</a>' : $comment_author).' | '.formatDateTime($comment_timestamp, _DATESTRING).'</td>';
	    }
            echo '</tr><br /><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">';
        }
        if ($db->sql_numrows($resulta) < 1) {
            echo '<tr><td class="row1" align="center"><span class="gen">'._PTY_COMMENTS_NONEYET.'</span></td></tr>';
        }
        $db->sql_freeresult($resulta);
        $my_name = $my_email = $disabled = '';
        if (is_user()) {
            $my_name = $userinfo['username'];
            $my_email = $userinfo['user_email'];
            $disabled = ' readonly="readonly"';
        } else {
	    $my_name = _ANONYMOUS;
            $my_email = 'disabled';
            $disabled = ' readonly="readonly"';
	}
        echo '</table><br />
        <table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'._PTY_LEAVE_A_COMMENT.'</span></b></td></tr>';
        if (!is_user() && !$personal_conf['anon_comment']) {
            echo '<tr><td class="row1" colspan="2" align="center"><span class="gen">'._PTY_COMMENTS_REG.'</span></td></tr></table>';
        } else {
            echo '<form name="add_comment" action="'.getlink().'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
            <tr><td class="row1"><span class="gen">'._NAME.'</span></td><td class="row2"><input type="text" name="comment_name" size="30" value="'.$my_name.'" maxlength="30"'.$disabled.' /></td></tr>
            <tr><td class="row1"><span class="gen">'._EMAIL.'</span></td><td class="row2"><input type="text" name="comment_email" size="30" value="'.$my_email.'" maxlength="255"'.$disabled.' /></td></tr>
            <tr><td class="row1"><span class="gen">'._PTY_COMMENTS_COMMENT.'</span>'.(($personal_conf['comment_limit'] >= 1) ? '<br />'.sprintf(_M_CHARS, $personal_conf['comment_limit']) : '').'</td><td class="row2"><textarea name="comment_text" wrap="virtual" cols="70" rows="7"></textarea>'.smilies_table('onerow', 'comment_text', 'add_comment').'</td></tr>
            <tr><td class="catbottom" colspan="2" align="center" height="28">
            <input type="hidden" name="blog_id" value="'.$blog_id.'" />';
	    if (!is_user() && $personal_conf['anon_comment']) {
		  echo '
		  <table align="center" border="0">
		  <tr>
		  <td>'._SECURITYCODE.':</td>
		  <td>'.generate_secimg().'</td></tr>
		  <tr><td>'._TYPESECCODE.':</td>
		  <td><input type="text" name="gfx_check" size="7" maxlength="6" /></td>
		  </tr></table>';
	    }
            echo '<input type="submit" name="post_comment" class="mainoption" value="'._PTY_COMMENTS_POST.'" />
            </td></tr></form></table>';
        }
    }
} else {
    require_once('header.php');
    $resulta = $db->sql_query("SELECT id, aid, title, timestamp FROM ".$prefix."_personality WHERE private='0' OR aid='".$userinfo['username']."' ORDER BY timestamp DESC");
    if ($db->sql_numrows($resulta) < 1) {
        echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center"><span class="gen">'.sprintf(_PTY_PERSONALERROR3).'</span></td></tr>';
    } else {
        echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center">'._PTY_USERINFO.'</td></tr>
        <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'._PTY_FRESH.'</span></b></td></tr>';
    }
    $num = 1;
    while (list($blog_id, $blog_author, $blog_title, $blog_timestamp) = $db->sql_fetchrow($result)) {
	if (useLEO() > 0 ) {
		  $blog_link = $BASEHREF.'Personal/u='.$blog_author.'/';
	}
	else {
		  $blog_link = $BASEHREF.'?name=Personal&amp;u='.$blog_author.'';
	}
        echo '<tr><td class="row1" align="left"><span class="gen">'.$num.'. <a href="'.$blog_link.'">'.$blog_title.'</a></span></td><td class="row2" align="right">'._PTY_PAGEPOST.' <a href="'.$blog_link.'">'.$blog_author.'</a> '._ON.' '.formatDateTime($blog_timestamp, _DATESTRING2).'</td></tr>';
        $num++;
    }
    $db->sql_freeresult($resulta);



/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON START*/
    if ($num_blogs == 0){
                echo '<tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'._PTY_SEARCH_USERS.'</span></b></td></tr>
                <form action="'.getlink("&amp;mode=user").'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                <tr><td class="row1" colspan="2" align="center"><input type="text" id="nick" name="nick" size="25" maxlength="50" />&nbsp;&nbsp;<input type="submit" value="'._PTY_SEARCH_BEGIN.'" class="mainoption" /></td></tr></form>
                <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'._PTY_STARTBLOGGING.'</span></b></td></tr>
                <tr><td class="row1" colspan="2" align="center"><span class="gen"><a href="'.getlink('&amp;mode=add').'">'._PTY_CREATE_TITLE.'</a></span><br /><br />'._PTY_CREATE_REG.'</td></tr>
                </table>';
                }

    if ($num_blogs > 0){
                echo '<tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'._PTY_SEARCH_USERS.'</span></b></td></tr>
                <form action="'.getlink("&amp;mode=user").'" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                <tr><td class="row1" colspan="2" align="center"><input type="text" id="nick" name="nick" size="25" maxlength="50" />&nbsp;&nbsp;<input type="submit" value="'._PTY_SEARCH_BEGIN.'" class="mainoption" /></td></tr></form>
                </table>';
                }
/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON END*/

}
echo '<br><div align="center"><a href="http://anarchismtoday.org/Downloads/details/id=18.html">Personal Pages '.$version.'</a> by <a href="http://AnarchismToday.org/">personman</a></div><br>';