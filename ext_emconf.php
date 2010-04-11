<?php

########################################################################
# Extension Manager/Repository config file for ext "mm_forum_news".
#
# Auto generated 19-12-2009 23:32
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'News comments with mm_forum',
	'description' => 'Provides a commenting function for tt_news entries using the mm_forum extension.',
	'category' => 'fe',
	'author' => 'Martin Helmich',
	'author_email' => 'm.helmich@mittwald.de',
	'shy' => '',
	'dependencies' => 'tt_news,mm_forum',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'Mittwald CM Service GmbH & Co. KG',
	'version' => '1.0.0-alpha1',
	'constraints' => array(
		'depends' => array(
			'tt_news' => '3.0.1-0.0.0',
			'mm_forum' => '1.9.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"b3bf";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"859c";s:20:"doc/wizard_form.html";s:4:"f63b";}',
);

?>