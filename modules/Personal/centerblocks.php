 <?php

if (!defined('CPG_NUKE')) { exit; }
function centerblocks($username) {
global $db, $prefix, $userinfo, $user_prefix, $currentlang, $pagetitle, $MAIN_CFG, $CPG_SESS, $CLASS, $BASEHREF;
		//$user_gallery = 10000+$userinfo['user_id'];
    	// Center blocks
	$centerblocksdir = dir('modules/Personal/blocks/center/');
	while ($centerfunc=$centerblocksdir->read()) {
		if (substr($centerfunc, -3) == 'php') {
			$centerblockslist[] = $centerfunc;
		}
	}
	closedir($centerblocksdir->handle);
	sort($centerblockslist);
	for ($i=0; $i < sizeof($centerblockslist); $i++) {
		require_once('modules/Personal/blocks/center/'.$centerblockslist[$i]);
	}
}
