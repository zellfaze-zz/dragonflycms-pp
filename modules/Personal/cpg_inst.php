<?php

if (!defined('ADMIN_MOD_INSTALL')) { exit; }

class Personal {
    var $radmin;
    var $modname;
    var $version;
    var $author;
    var $website;
    var $description;
    var $dbtables;
// class constructor
    function Personal() {
        $this->radmin = true;
        $this->modname = 'Personal Pages';
        $this->version = 'v2.7.1';
        $this->author = 'personman';
        $this->website = 'AnarchismToday.org';
        $this->description = 'Allow your members to create their own Personal Page.';
        $this->dbtables = array('personality', 'personality_greetings');
    }

// module installer
    function install() {
        global $installer;
        $installer->add_query('CREATE', 'personality', "
  id int(11) NOT NULL auto_increment,
  aid varchar(30) NOT NULL default '',
  title varchar(80) default NULL,
  text text NOT NULL,
  private tinyint(1) NOT NULL default '0',
  timestamp int(10) UNSIGNED NOT NULL default '".gmtime()."',
  PRIMARY KEY  (id),
  KEY id (id),
  KEY aid (aid)", 'personality');
        $installer->add_query('CREATE', 'personality_greetings', "
  cid int(11) NOT NULL auto_increment,
  bid int(11) NOT NULL default '0',
  aid varchar(30) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  text text NOT NULL,
  timestamp int(10) UNSIGNED NOT NULL default '".gmtime()."',
  PRIMARY KEY  (cid),
  KEY cid (cid),
  KEY bid (bid),
  KEY aid (aid)", 'personality_greetings');
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'allow_html', '0'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'anon_comment', '0'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comment_limit', '1024'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comments_allow_html', '0'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comments_allow_bbcode', '0'");
        return true;
    }

// module uninstaller
    function uninstall() {
        global $installer;
        $installer->add_query('DROP', 'personality');
        $installer->add_query('DROP', 'personality_greetings');
	$installer->add_query('DELETE', 'config_custom', "cfg_name='Personal'");
    }

// module upgrader
    function upgrade($prev_version) {
               global $installer; /*
        $installer->add_query('CREATE', 'personality', "
  id int(11) NOT NULL auto_increment,
  aid varchar(30) NOT NULL default '',
  title varchar(80) default NULL,
  text text NOT NULL,
  private tinyint(1) NOT NULL default '0',
  timestamp int(10) UNSIGNED NOT NULL default '".gmtime()."',
  PRIMARY KEY  (id),
  KEY id (id),
  KEY aid (aid)", 'personality');
        $installer->add_query('CREATE', 'personality_greetings', "
  cid int(11) NOT NULL auto_increment,
  bid int(11) NOT NULL default '0',
  aid varchar(30) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  text text NOT NULL,
  timestamp int(10) UNSIGNED NOT NULL default '".gmtime()."',
  PRIMARY KEY  (cid),
  KEY cid (cid),
  KEY bid (bid),
  KEY aid (aid)", 'personality_greetings'); */
  if ($prev_version < 'v2.7.1') { 
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'allow_html', '0'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'anon_comment', '0'"); 
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comment_limit', '1024'"); 
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comments_allow_html', '0'");
	$installer->add_query('INSERT', 'config_custom', "'Personal', 'comments_allow_bbcode', '0'");
	}
        return true;
    }
}

