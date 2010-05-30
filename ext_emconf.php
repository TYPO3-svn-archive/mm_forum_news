<?php

########################################################################
# Extension Manager/Repository config file for ext "mm_forum_news".
#
# Auto generated 25-05-2010 01:52
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'News comments with mm_forum',
	'description' => 'Provides a commenting function for tt_news entries using the mm_forum extension.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.0.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Martin Helmich, Hauke Hain',
	'author_email' => 'm.helmich@mittwald.de, hhpreuss@googlemail.com',
	'author_company' => 'Mittwald CM Service GmbH & Co. KG',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'tt_news' => '3.0.1-0.0.0',
			'mm_forum' => '1.9.0-0.0.0',
			'mm_forum_comments' => '1.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:9:"ChangeLog";s:4:"8bf7";s:10:"README.txt";s:4:"3981";s:12:"ext_icon.gif";s:4:"167d";s:17:"ext_localconf.php";s:4:"65b1";s:14:"ext_tables.php";s:4:"982b";s:14:"ext_tables.sql";s:4:"f0b0";s:28:"ext_typoscript_constants.txt";s:4:"5d31";s:14:"doc/manual.sxw";s:4:"3163";s:19:"doc/wizard_form.dat";s:4:"859c";s:20:"doc/wizard_form.html";s:4:"f63b";s:39:"lib/class.tx_mmforumnews_modinstall.php";s:4:"644f";s:36:"lib/class.tx_mmforumnews_tcemain.php";s:4:"5e06";s:28:"res/img/mod/mmforum-conf.png";s:4:"de58";s:22:"res/lang/locallang.xml";s:4:"bd30";s:22:"res/staticts/setup.txt";s:4:"e2e7";s:37:"res/ts/tx_mmforumnews_pagetsconfig.ts";s:4:"621b";}',
);

?>