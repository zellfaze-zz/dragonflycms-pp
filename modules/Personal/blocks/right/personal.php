<?php

if (!defined('CPG_NUKE')) { exit; }

	if (is_active('Personal')) {
		$result = $db->sql_query("SELECT id FROM ".$prefix."_personality WHERE aid='$username' AND private=0 ORDER BY id DESC LIMIT 1");
		if ($db->sql_numrows($result) > 0) { 
			OpenTable();
			$leo = useLEO();
			if ($leo > 0 ) {
				  echo '<strong>'._PTY_BLOG.' URL:</strong><br>
				  <div align="center"><a href='.$BASEHREF.'Personal/u='.$username.'/>'.$BASEHREF.'Personal/u='.$username.'/</a></div>';
			}
			else {
				  echo '<strong>'._PTY_BLOG.' URL:</strong><br>
				  <div align="center"><a href='.$BASEHREF.'?name=Personal&amp;u='.$username.'>'.$BASEHREF.'?name=Personal&amp;u='.$username.'</a></div>';
			}
			CloseTable();
		}
	}
