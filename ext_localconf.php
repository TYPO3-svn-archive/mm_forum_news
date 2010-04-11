<?php

	/*
	 * Add some additional page TSConfig. This is used to dynamically extend the
	 * mm_forum backend module.
	 */
t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mm_forum_news/res/ts/tx_mmforumnews_pagetsconfig.ts">');

	/*
	 * Now hook into the TCEmain class. This is done in order to create new
	 * mm_forum topics alongside tt_news records.
	 */
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:mm_forum_news/lib/class.tx_mmforumnews_tcemain.php:&tx_mmforumnews_TCEMain';

?>
