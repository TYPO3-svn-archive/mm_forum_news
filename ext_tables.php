<?php

	/*
	 * Add the static template for this extension.
	 */

t3lib_extMgm::addStaticFile($_EXTKEY, 'res/staticts', 'mm_forum News comments');


	/*
	 * Extend the tt_news table by an additional column.
	 */

$tempColumns = array(
	'tx_mmforumnews_topic' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:mm_forum_news/res/lang/locallang.xml:db.ttnews_topic',
		'config'  => array(
			'type'          => 'group',
			'internal_type' => 'db',
			'allowed'       => 'tx_mmforum_topics',
			'max_size'      => 1,
			'size'          => 1,
			'minitems'      => 0,
			'maxitems'      => 1,
			'readOnly'      => 1
		)
	)
);

t3lib_div::loadTCA('tt_news');
t3lib_extMgm::addTCAcolumns    ( 'tt_news' , $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes ( 'tt_news' , 'tx_mmforumnews_topic');

?>