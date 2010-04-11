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
	 * This class provides functions for extending the mm_forum backend module.
	 *
	 * @author    Martin Helmich <m.helmich@mittwald.de>
	 * @copyright 2009 Martin Helmich <m.helmich@mittwald.de>
	 * @version   2009-12-22
	 * @package   mm_forum_news
	 *
	 */

class tx_mmforumnews_ModInstall {



		/**
		 *
		 * Generates a custom board selection field.
		 * This method generates a <SELECT> element that presents all message
		 * boards in a hierarchical order. This input field will be presented in
		 * the mm_forum configuration backend module.
		 *
		 * @author  Martin Helmich <m.helmich@mittwald.de>
		 * @version 2009-12-22
		 * @param   array  $value     The current value of this input field. The
		 *                            option with this value will be selected.
		 * @param   string $fieldname The name of the input field.
		 * @param   array  $config    A configuration array with additional
		 *                            properties for this field. In this case,
		 *                            this variable is not used.
		 * @return  string            The <SELECT> input field as HTML output.
		 *
		 */

	public function getForumSelector($value, $fieldname, $config) {
		global $TYPO3_DB;
		$content = '';
		$pid = $this->p->conf['storagePID'];

			/*
			 * Get all categories. Iterate over the categories and load the
			 * boards for each category.
			 */
		$res = $TYPO3_DB->exec_SELECTquery( 'uid, forum_name',
		                                    'tx_mmforum_forums',
			                                'parentID = 0 AND deleted=0 AND pid='.$pid, '', 'sorting ASC');
		while($arr = $TYPO3_DB->sql_fetch_assoc($res)) {
			$res2 = $TYPO3_DB->exec_SELECTquery( 'uid, forum_name',
                                                 'tx_mmforum_forums',
			                                     "parentID = {$arr[uid]} AND deleted=0 AND pid=$pid", '', 'sorting ASC' );
			if($TYPO3_DB->sql_num_rows($res2)>0) {
				$content .= '<optgroup label="'.htmlspecialchars($arr['forum_name']).'">'.chr(10);
				while($arr2 = $TYPO3_DB->sql_fetch_assoc($res2))
					$content .= '<option value="'.$arr2['uid'].'" '.($value==$arr2['uid']?'selected="selected"':'').'>'
					            . htmlspecialchars($arr2['forum_name'])
								. '</option>'.chr(10);
				$content .= '<optgroup>'.chr(10);
			}
		}

		return '<select name="tx_mmforum_install[conf][0]['.htmlspecialchars($fieldname).']">'.$content.'</select>';
	}

}

?>
