<?php

if (!defined('CPG_NUKE')) { exit; }

global $prefix, $db, $user_prefix, $CONFIG, $cpg_dir, $cpg_all_installs, $first_install_prefix, $BASEHREF;

$cpg_dir = 'coppermine';

// $length=$CONFIG['thumbcols']; //number of thumbs
$length = 3; 		//number of thumbs
$scale = 0.50; 		// scale images down by $scale.
//END USER DEFINABLES


// Here we try to set up just enough of the coppermine environment for our purposes.

if (empty($cpg_all_installs) ) {
	$resulta = $db->sql_query("SELECT prefix, dirname FROM ".$prefix."_cpg_installs ORDER BY cpg_id", false,__FILE__,__LINE__);
	while ($row = $db->sql_fetchrow($resulta)) {
		if (empty($first_install_M_DIR)) { $first_install_M_DIR = $row['dirname']; }
		if (empty($first_install_prefix)) { $first_install_prefix = $row['prefix']; }
		$cpg_all_installs[$row['dirname']]['prefix'] = $row['prefix'];
	}
}

$cpg_prefix = $cpg_all_installs[$cpg_dir]['prefix'];

// Initialise the $CONFIG array
$CONFIG = array();

$CONFIG['TABLE_CONFIG'] = $cpg_prefix . "config";

// Retrieve DB stored configuration
if (!isset($cpg_all_installs[$cpg_dir]['config'])) {
	$results = $db->sql_query('SELECT * FROM '. $CONFIG['TABLE_CONFIG']);
	while ($row = $db->sql_fetchrow($results)) {
		$CONFIG[$row['name']] = $row['value'];
	} // while
	$db->sql_freeresult($results);
	$cpg_all_installs[$cpg_dir]['config'] = $CONFIG;
} else {
	$CONFIG = $cpg_all_installs[$cpg_dir]['config'];
}

if (!is_active($cpg_dir)) {
	echo 'ERROR';
	return trigger_error($cpg_dir.' module is inactive', E_USER_WARNING);
}

// Look up user ID by username?
$query = 'SELECT user_id FROM '.$prefix.'_users WHERE username="'.$username.'" LIMIT 1';

$result = $db->sql_query($query);

while(list($user_id) = $db->sql_fetchrow($result))
{ 
	$user_gallery = 10000+$user_id;
	//$cpg_block = true;
	//$cpg_block = false;
	$ugall_result = $db->sql_query('SELECT p.pid, p.filepath, p.filename AS filename, p.aid, p.title AS title
	FROM '.$cpg_prefix.'pictures AS p
	INNER JOIN '.$cpg_prefix.'albums AS a ON (p.aid = a.aid)
	WHERE approved=1 AND a.category = '.$user_gallery.' GROUP BY pid ORDER BY pid DESC LIMIT '.$length); 
	
	if ($db->sql_numrows($ugall_result) > 0) { 
		OpenTable();
		echo '<table border="1"><tr>';
		$pic = 0;
		$thumb_title = '';
		while ($row = $db->sql_fetchrow($ugall_result)) {
			if ($CONFIG['seo_alts'] == 0) {
				$thumb_title = $row['filename'];
			} else {
				if ($row['title'] != '') {
					$thumb_title = $row['title'];
				} else {
					$thumb_title = substr($row['filename'], 0, -4);
				}
			}
			list($width, $height) = getimagesize(get_pic_urls($row, 'thumb'));
			$width = $width * $scale; $height = $height * $scale;
			echo '<td align=center>
			<a href="' . $BASEHREF.get_pic_urls($row) . '">
			<img src="' . $BASEHREF.get_pic_urls($row, 'thumb') . '" alt="' . $thumb_title . '" title="' . $thumb_title . '" height="'.$height.'" width="'.$width.'" /></a>
			</td>';
			$pic++;
			//if ($pscale) { $scaling = $scaling - $scale; }
		}
		echo '</tr></table>';
		CloseTable(); 
	} 
}
function get_pic_urls(&$pic_row, $mode)
{
	global $CONFIG;
	static $pic_prefix = array();
	static $url_prefix = array();
	if (!count($pic_prefix)) {
		$pic_prefix = array('thumb' => $CONFIG['thumb_pfx'],
			'normal' => $CONFIG['normal_pfx'],
			'fullsize' => ''
		);
		$url_prefix = array(0 => $CONFIG['fullpath']);
	} 
	return path2urls($pic_row['filepath'] . $pic_prefix[$mode] . $pic_row['filename']);
}

// Function to create correct URLs for image name with space or exotic characters
function path2urls($path)
{
	return str_replace("%2F", "/", rawurlencode($path));
} 