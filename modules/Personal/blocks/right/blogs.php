<?php

if (!defined('CPG_NUKE')) { exit; }

	if (is_active('Blogs')) {
		$result = $db->sql_query("SELECT title, id FROM ".$prefix."_blogs WHERE aid='$username' AND private=0 ORDER BY id DESC LIMIT 3");
		if ($db->sql_numrows($result) > 0) { 
			OpenTable();
			echo '<strong>'._BLATEST.' '._BlogsLANG.':</strong><div align="center">';
			while(list($blog_title, $blog_id) = $db->sql_fetchrow($result))
			{
				echo '<li><a href="'.getlink('Blogs&amp;mode=display&amp;id='.$blog_id).'">'.$blog_title.'</a></li>';
			}
			echo '</div>';
			CloseTable();
		}
	}
