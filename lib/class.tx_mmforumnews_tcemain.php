<?php

/*                                                                     *
 *  COPYRIGHT NOTICE                                                   *
 *                                                                     *
 *  (c) 2010 Martin Helmich <m.helmich@mittwald.de>                    *
 *      All rights reserved                                            *
 *                                                                     *
 *  This script is part of the TYPO3 project. The TYPO3 project is     *
 *  free software; you can redistribute it and/or modify               *
 *  it under the terms of the GNU General Public License as published  *
 *  by the Free Software Foundation; either version 2 of the License,  *
 *  or (at your option) any later version.                             *
 *                                                                     *
 *  The GNU General Public License can be found at                     *
 *  http://www.gnu.org/copyleft/gpl.html.                              *
 *                                                                     *
 *  This script is distributed in the hope that it will be useful,     *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of     *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      *
 *  GNU General Public License for more details.                       *
 *                                                                     *
 *  This copyright notice MUST APPEAR in all copies of the script!     *
 *                                                                     */



	/**
	 *
	 * This class is the main backend class of the mm_forum_news extension. It
	 * hooks into the t3lib_TCEmain class and creates a new mm_forum topic every
	 * time a tt_news record is created.
	 * If a tt_news record is edited, the post text and topic title are updated
	 * to match the news text and subject.
	 *
	 * @author    Martin Helmich <m.helmich@mittwald.de>
	 * @copyright 2009 Martin Helmich <m.helmich@mittwald.de>
	 * @version   2009-12-20
	 * @package   mm_forum_news
	 *
	 */

class tx_mmforumnews_TCEMain {



		/**
		 *
		 * Hooks into the TCEmain data-mapping process.
		 * This methods hooks into the "process_datamap" method of the TCEmain
		 * class. If a new tt_news record is created, this function creates a
		 * mm_forum topic and stores it's UID in the new tt_news record.
		 * If a news record is updated, the related topic is updated.
		 *
		 * @param  string        $status      The status of the edited record.
		 *                                    This may either be "new" or
		 *                                    "update".
		 * @param  string        $table       The record's table
		 * @param  string        $id          The record's UID
		 * @param  array         &$fieldArray The entire record that is being
		 *                                    edited.
		 * @param  t3lib_TCEmain $parent      The TCEmain instance that calls
		 *                                    this hook.
		 * @return void
		 */

	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $parent) {
		if($table === 'tt_news') {
			$this->loadTSSetupForPage($fieldArray['pid']);

			if($status === 'new' && !$fieldArray['tx_mmforumnews_topic'])
				$this->createTopicForRecord($fieldArray);
			elseif($status === 'update') {
				$completeRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid='.$id));
				if($completeRecord['tx_mmforumnews_topic'])
					$this->updateTopicForRecord($fieldArray, $completeRecord);
			}
		}
	}



		/**
		 *
		 * Creates a new commenting topic for a news record.
		 * This methods creates a new mm_forum topic that will be used for
		 * commenting a tt_news record. This method uses the mm_forum postfactory
		 * interface in order to create the new topic.
		 *
		 * @param  array &$fieldArray The new tt_news record.
		 * @return void
		 *
		 */

	protected function createTopicForRecord(&$fieldArray) {
		if(   isset($this->setup['plugin.']['tx_mmforumnews.']['forumId'])
		   && isset($this->setup['plugin.']['tx_mmforumnews.']['topicAuthorId']))
			$fieldArray['tx_mmforumnews_topic'] = $this->getPostFactory()->create_topic(
				$this->setup['plugin.']['tx_mmforumnews.']['forumId'],
				$this->setup['plugin.']['tx_mmforumnews.']['topicAuthorId'],
				$fieldArray['title'], $fieldArray['bodytext'], $fieldArray['crdate'],
				dechex(ip2long(t3lib_div::getIndpEnv('REMOTE_ADDR'))), array(),
				0, false, false, false
			);
	}



		/**
		 *
		 * Updates a mm_forum topic record.
		 * This function updates a mm_forum topic record, when the related
		 * tt_news record is altered. Thus, if the subject or text of the tt_news
		 * record change, the subject and the post text of the topic will be
		 * changed alongside the news record.
		 *
		 * @param array &$fieldArray    An array containing the modified fields
		 *                              in the tt_news record. The topic record
		 *                              will only be updated, if the subject or
		 *                              bodytext column of the tt_news record
		 *                              have been modified.
		 * @param array $completeRecord Since the &$fieldArray parameter contains
		 *                              only the modified columns, the complete
		 *                              and original record is submitted in this
		 *                              parameter.
		 * @return void
		 * 
		 */

	protected function updateTopicForRecord(&$fieldArray, $completeRecord) {
		global $TYPO3_DB;

		$topic = $TYPO3_DB->sql_fetch_assoc($TYPO3_DB->exec_SELECTquery('*','tx_mmforum_topics','uid='.$completeRecord['tx_mmforumnews_topic']));
		$post  = $TYPO3_DB->sql_fetch_assoc($TYPO3_DB->exec_SELECTquery('*','tx_mmforum_posts','uid='.$topic['topic_first_post_id']));

		if(isset($fieldArray['bodytext'])) {
			$TYPO3_DB->exec_UPDATEquery('tx_mmforum_posts_text',
			                            'post_id='.$post['uid'],
			                            array( 'post_text'    => $fieldArray['bodytext'],
			                                   'tstamp'       => time(),
			                                   'cache_text'   => '',
			                                   'cache_tstamp' => 0 ) );
			$TYPO3_DB->exec_UPDATEquery('tx_mmforum_posts',
			                            'uid='.$post['uid'],
				                        array( 'tstamp'     => time(),
			                                   'edit_time'  => time(),
			                                   'edit_count' => 'edit_count + 1' ),
										array( 'edit_count' ) );
		}

		if(isset($fieldArray['title']))
			$TYPO3_DB->exec_UPDATEquery('tx_mmforum_topics',
			                            'uid='.$topic['uid'],
			                            array( 'tstamp'      => time(),
			                                   'topic_title' => $fieldArray['title'] ) );

	}


		/**
		 *
		 * Instantiates and returns the mm_forum post factory. The post factory
		 * is handled as a singleton instance, i.e. it will only be instantiated
		 * ONCE and then reused.
		 *
		 * @return tx_mmforum_postfactory An instance of the tx_mmforum_postfactory
		 *                                class.
		 *
		 */

	protected function getPostFactory() {
		if(!$this->postFactory) {
			$this->postFactory = t3lib_div::getUserObj('EXT:mm_forum/pi1/class.tx_mmforum_postfactory.php:tx_mmforum_postfactory');
			$this->postFactory->init(array('storagePID' => $this->setup['plugin.']['tx_mmforum.']['storagePID']));
		} return $this->postFactory;
	}



		/**
		 *
		 * Loads the TypoScript setup for a specific page.
		 * This function loads the complete TypoScript setup for a specific
		 * page -- usually the page the tt_news record is saved on. The setup
		 * is needed in order to determine the mm_forum storage PID.
		 * The t3lib_tsparser_ext class is used for doing this.
		 *
		 * @param  int $pid The page UID for which the setup is to be loaded.
		 * @return void
		 *
		 */

	protected function loadTSSetupForPage($pid) {
		if(!$this->setup) {
			$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");
			$tmpl->tt_track = 0;
			$tmpl->init();

			$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
			$rootLine = $sys_page->getRootLine($pid);
			$tmpl->runThroughTemplates($rootLine,0);
			$tmpl->generateConfig();
			$this->setup = $tmpl->setup;
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_news/lib/class.tx_mmforumnews_tcemain.php'])
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_news/lib/class.tx_mmforumnews_tcemain.php']);

?>
