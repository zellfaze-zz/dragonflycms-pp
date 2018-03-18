<?php
/*********************************************
  PERSONALITY - MODULE FOR CPGNUKE 9.0.6.1
  ********************************************

  author:       gtown
  date:         2006/10/14
  version:      2
  authemail:    admin@germeringer.de
  authurl:      http://www.germeringer.de

  This module is based on the blogs module of
  DJMaze and Trevor from www.cpgnuke.com
  Special thanks to dcollis!

**********************************************/


if (!defined('CPG_NUKE')) { exit; }

get_lang('Personal');

$module = _PTY_BLOG;

list($module_title) = $db->sql_ufetchrow("SELECT custom_title FROM ".$prefix."_modules WHERE title='$module'", SQL_NUM, __FILE__, __LINE__);
    if (empty($module_title)) { $my_title = $module; }

echo '<br />';
OpenTable();
echo '<div align="left"><strong>'.$my_title.':</strong><ul>';
    
/***********************  MODULSTART */
/* Configuration */
//$cfg_comment_limit = 4096; // maximum length in characters for a greeting | set to 0 for no limit
//$cfg_comment_anon = false; // allow anonymous users to post greetings?
/* END Configuration */

global $MAIN_CFG;

if (is_array($MAIN_CFG['Personal'])) {
	$personal_conf = $MAIN_CFG['Personal'];
}

$pagetitle .= _PTY_TITLE;
require_once('includes/nbbcode.php');
require_once('modules/Your_Account/functions.php');

$username = $userinfo['username'];
$mode = (isset($_POST['mode'])&& $_POST['mode']!='') ? $_POST['mode'] : (isset($_GET['mode'])&& $_GET['mode']!='') ? $_GET['mode'] : '';

/* LINES FOR PERSONAL PAGE USE */
list($num_blogs) = $db->sql_ufetchrow("SELECT COUNT(*) FROM ".$prefix."_personality WHERE aid='$username' AND private=0 LIMIT 1");
/* LINES FOR PERSONAL PAGE USE END*/


    $result = $db->sql_query("SELECT * FROM ".$prefix."_personality WHERE aid='$username'");
    if ($db->sql_numrows($result) < 1) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'.sprintf(_PTY_PERSONALERROR2).''.$username.''.sprintf("!").'</td></tr>
        </table>');
    }
    list($blog_id, $blog_author, $blog_title, $blog_text, $blog_private, $blog_timestamp) = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($blog_private && ($blog_author != $userinfo['username'])) {
        cpg_error('<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="row1" colspan="2" align="center" height="30">'._PTY_PRIVATE.'</td></tr>
        </table>');
    }

    $result = $db->sql_query("SELECT * FROM ".$prefix."_personality_greetings WHERE bid='$blog_id' ORDER BY timestamp ASC");
    $blog_comments = $db->sql_numrows($result);
    $blog_text = decode_bb_all($blog_text, 1, $personal_conf['allow_html']);
    $blog_timestamp = formatDateTime($blog_timestamp, _DATESTRING);


/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON START*/
    $result2 = $db->sql_query("SELECT id FROM ".$prefix."_personality WHERE aid='$username'");
    $id = $db->sql_fetchrow($result2);
    $db->sql_freeresult($result2);
    $id = $id[0];
    if ($num_blogs > 0){
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.$blog_title.'</span></b></td></tr>
    <tr><td class="row1" colspan="2" align="center">'.$blog_timestamp.'</td></tr>
    <tr><td class="row1" colspan="2"><span class="gen">'.$blog_text.'</span></td></tr>
    </table><br /><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2" align="center"><a href="'.'index.php?name=Personal&mode=display&id='.$id.'&comments=show'.'">'._PTY_COMMENTS_POST.'</a></td></tr>
    </table>';
    }

    if ($num_blogs == 0){
    echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="catleft" colspan="2" align="center"><b><span class="gen">'.$blog_title.'</span></b></td></tr>
    <tr><td class="row1" colspan="2" align="center">'.$blog_timestamp.'</td></tr>
    <tr><td class="row1" colspan="2"><span class="gen">'.$blog_text.'</span></td></tr>
    </table><br /><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
    <tr><td class="row1" colspan="2" align="center"><a href="'.'index.php?name=Personal&mode=display&id='.$id.'&comments=show'.'">'._PTY_COMMENTS_POST.'</a></td></tr>
    </table>';
    }
/* PERSONAL PAGE USE: MENU WITH/WITHOUT "ADD" BUTTON END*/

	//*************************************
	// Display Comments
	//*************************************

        echo '<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
        <tr><td class="catleft" colspan="3" align="center"><b><span class="gen">'._PTY_COMMENTS.'</span></b></td></tr>';
        while (list($comment_id, $comment_blog, $comment_author, $comment_email, $comment_ip, $comment_text, $comment_timestamp) = $db->sql_fetchrow($result)) {
            $comment_ip = decode_ip($comment_ip);

            echo '<tr><td class="row1" valign="top" width="100%"><span class="gen">'.decode_bb_all($comment_text, 1).'</span></td><td valign="top"><center><a href="'.getlink('Your_Account&profile='.$comment_author).'">'.$comment_author.'</a><br>';

global $MAIN_CFG, $CPG_SESS;

//$username = $comment_author;
//$username = substr($username, strlen(_BY)+1);
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
	    echo '</center></td></tr>
            <tr><td class="row1" colspan="2" align="left">'._PTY_GREETPOST.' '.((!empty($comment_email)) ? '<a href="'.getlink('Your_Account&profile='.$comment_author).'">'.$comment_author.'</a>' : $comment_author).' | '.formatDateTime($comment_timestamp, _DATESTRING).'</td>
<br><table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">';



}
echo '</table>';

/***********************  MODULENDE */
echo '</ul></div>';
CloseTable();
