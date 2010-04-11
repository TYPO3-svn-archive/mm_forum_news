<?php

	/*
	 * Add the static template for this extension.
	 */

t3lib_extMgm::addStaticFile($_EXTKEY, 'res/staticts', 'mm_forum_news (comments)');


	/*
	 * Extend the tt_news table by an additional column.
	 */

$tempColumns = array(
	'tx_mmforumnews_createtopic' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:mm_forum_news/res/lang/locallang.xml:db.ttnews_createtopic',
		'config'  => array(
			'type'          => 'check',
		)
	)
);

t3lib_div::loadTCA('tt_news');
t3lib_extMgm::addTCAcolumns    ( 'tt_news' , $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes ( 'tt_news' , 'tx_mmforumnews_createtopic');

?>