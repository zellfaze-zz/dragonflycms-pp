<?php

global $MAIN_CFG;

if (!defined('ADMIN_PAGES')) { exit; }
if (!can_admin()) { die('Denied Access'); }
if (is_array($MAIN_CFG['Personal'])) {
	$personal_conf = $MAIN_CFG['Personal'];
}

$pagetitle .= ' '._BC_DELIM.' '._PTY_TITLE.' '._ADMINISTRATION.'';

require_once('header.php');
GraphicAdmin('_AMENU9');

if (isset($_POST['updateConfig'])) {
	if ($CPG_SESS['admin']['page'] != $module_name) {
		cpg_error(_ERROR_BAD_LINK, _SEC_ERROR);
	}

	foreach ($MAIN_CFG[$module_name] AS $key => $val) {
		if (isset($_POST[$key])) {
			$value = $_POST[$key];
			echo "<pre>".$_POST[$key]."</pre>";
			$db->sql_query('UPDATE '.$prefix."_config_custom 
			SET cfg_value='".Fix_Quotes($value)."' 
			WHERE cfg_field='$key' AND cfg_name='$module_name'");
		}
	}
	cache_delete_array('MAIN_CFG');
	url_redirect(adminlink($module_name));

}

OpenTable();
echo '<div align=center><b>'._PTY_TITLE.' '._ADMINISTRATION.'</b></div>';
echo open_form(adminlink($module_name), 0, 0, 'style="border:none;"');
echo '<table border="0" width="100%" cellpadding="3" cellspacing="1" class="forumline">';

echo '<tr><td colspan="2" class="cat">'._PTY_TITLE.':</td></tr>';
echo '<tr><td class="row1"><span class="genmed"><b>Allow HTML on Personal Pages:</b></span></td><td class="row2">'.yesno_option('allow_html', $personal_conf['allow_html']).'</td></tr>';
echo '<tr><td colspan="2" class="cat">'._PTY_TITLE.' '._COMMENTSOPT.':</td></tr>';
echo '<tr><td class="row1"><span class="genmed"><b>Allow Anonymous Comments:</b></span></td><td class="row2">' .yesno_option('anon_comment', $personal_conf['anon_comment']).'</td></tr>';
$options = array('128','256','512','1024','2048', '4096');
echo '<tr><td class="row1"><span class="genmed"><b>Max. Comment Length:</b></span></td><td class="row2">' .select_option('comment_limit',$personal_conf['comment_limit'],$options).'</td></tr>';
echo '<tr><td class="row1"><span class="genmed"><b>Allow BBCODE in Comments:</b></span></td><td class="row2">'.yesno_option('comments_allow_bbcode', $personal_conf['comments_allow_bbcode']).'</td></tr>';
echo '<tr><td class="row1"><span class="genmed"><b>Allow HTML in Comments (also enables bbcode):</b></span></td><td class="row2">'.yesno_option('comments_allow_html', $personal_conf['comments_allow_html']).'</td></tr>';

echo '<tr><td class="catbottom" colspan="2" align="center" style="height:28px;">';
echo '<input type="submit" name="updateConfig" value="'._SAVECHANGES.'"';
echo 'class="mainoption" />&nbsp;&nbsp; ';
echo '<input type="reset" value="Reset" name="reset" class="liteoption" /></td></tr>';
echo '</table>';
echo close_form();
CloseTable();

require_once('footer.php');
?>
