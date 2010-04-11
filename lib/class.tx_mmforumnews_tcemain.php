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

 require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_div.php');
 require_once(t3lib_extMgm::extPath('mm_forum_comments').'lib/class.tx_mmforumcomments_createcomments.php');


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
		 * class. If a new tt_news record is updated, this function creates a
		 * mm_forum topic and stores it's UID in the new tt_news record
		 * OR
		 * the related topic is updated.
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
		if($table === 'tt_news' && $status === 'update' && intval($id) > 0) {
		  $this->relationTable = 'tx_mmforumcomments_links';

      $completeNewsRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid='.$id));

      if ($fieldArray['hidden'] === null) {
        $fieldArray['hidden'] = $completeNewsRecord['hidden'];
      }

      if ($fieldArray['tx_mmforumnews_createtopic'] === null) {
        $fieldArray['tx_mmforumnews_createtopic'] = $completeNewsRecord['tx_mmforumnews_createtopic'];
      }

			if (!$fieldArray['hidden'] && $fieldArray['tx_mmforumnews_createtopic']) {
        $setup = tx_mmforumcomments_div::loadTSSetupForPage($completeNewsRecord['pid']);
    		$parameters = array('tx_ttnews->tt_news', $id, 'tx_ttnews');

    		$topicID = tx_mmforumcomments_div::getTopicID($completeNewsRecord['pid'], $parameters, $this->relationTable);

 			  if ($topicID === 0) {
          $this->createTopicForRecord($parameters, $setup['plugin.']['tx_mmforumcomments_pi1.'], $completeNewsRecord['pid'], $setup['plugin.']['tx_mmforum.']['storagePID']);
        } else {
  				//TODO: use $topicID
          /*
          $completeRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid='.$id));
  				if($completeRecord['tx_mmforumnews_topic'])
  					$this->updateTopicForRecord($fieldArray, $completeRecord);
   				*/
  			}
      }
		}
	}

  /**
		 *
		 * Hooks into the TCEmain data-mapping process.
		 * This methods hooks into the "process_datamap" method of the TCEmain
		 * class. If a new tt_news record is created, this function creates a
		 * mm_forum topic and stores it's UID in the new tt_news record.
		 *
		 * @param  string        $status      The status of the edited record.
		 *                                    This may either be "new" or
		 *                                    "update".
		 * @param  string        $table       The record's table
		 * @param  string        $id          The record's UID
		 * @param  array         &$fieldArray The entire record that is being
		 *                                    edited.
		 * @param  t3lib_TCEmain $pObj        The TCEmain instance that calls
		 *                                    this hook.
		 * @return void
		 */

	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
		if($table === 'tt_news' && $fieldArray['tx_mmforumnews_createtopic'] && $status === 'new') {
		  $this->relationTable = 'tx_mmforumcomments_links';
		  $id = intval($pObj->substNEWwithIDs[$id]);

      if (intval($id) > 0 && !$fieldArray['hidden']) {
        $setup = tx_mmforumcomments_div::loadTSSetupForPage($fieldArray['pid']);
      	$parameters = array('tx_ttnews->tt_news', $id, 'tx_ttnews');

      	$this->createTopicForRecord($parameters, $setup['plugin.']['tx_mmforumcomments_pi1.'], $fieldArray['pid'] ,$setup['plugin.']['tx_mmforum.']['storagePID']);
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
		 * @param  array    &$parameters The URL parameters.
		 * @param  array    &$conf The mm_forum_comments TypoScript setup.
		 * @param  integer  $pid page id for the relation table.
		 * @param  integer  $storagePID storage page id of mm_forum.
		 * @return void
		 *
		 */

	protected function createTopicForRecord(&$parameters, &$conf, $pid, $storagePID) {
		$data = tx_mmforumcomments_div::getTypoScriptData($parameters[2], intval($parameters[1])==0 ? $pid : intval($parameters[1]), $conf);

  	$commcat = tx_mmforumcomments_div::getCommentCategoryUID($parameters[2], $conf);
  	$commaut = tx_mmforumcomments_div::getTopicAuthorUID($parameters[2], $conf);
  	$subject = tx_mmforumcomments_div::getTSparsedString('subject', $parameters[2], $conf, $data);
  	$posttext = tx_mmforumcomments_div::getTSparsedString('posttext', $parameters[2], $conf, $data);
  	$link = tx_mmforumcomments_div::getTSparsedString('linktopage', $parameters[2], $conf, $data);
  	$date = tx_mmforumcomments_div::getDate($parameters[2], $conf, $data);

    tx_mmforumcomments_createcomments::createTopic($pid, $parameters,
            $commcat, $commaut,
            tx_mmforumcomments_div::prepareString($subject),
            tx_mmforumcomments_div::prepareString($posttext.$link),
            $date, $this->relationTable,
            $storagePID);
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
