<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Stefan Voelker <t3x@nyxos.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:rt_vacationcare/mod7/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Registrations' for the 'rt_vacationcare' extension.
 *
 * @author	Stefan Voelker <t3x@nyxos.de>
 * @package	TYPO3
 * @subpackage	tx_rtvacationcare
 */
class  tx_rtvacationcare_module7 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('template');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="POST">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						#$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('template');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
				
					// CSS Styles for backendmodules
					$content = '<style type="text/css">@import url(../../../../typo3conf/ext/rt_vacationcare/res/vcm_mod.css);
</style>';

					$data = t3lib_div::_GET('SET');
					if (is_array($data)) {
						// check, if registrations should be deleted
						if ((int)$data['delRegUid'] > 0) {
							// delete registration
							$res=$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_regist','uid='.$data['delRegUid']);
							// delete mm attendee
							$res=$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_regist_attendeeid_mm','uid_local='.$data['delRegUid']);
							// delete mm vacation
							$res=$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_regist_vacationid_mm','uid_local='.$data['delRegUid']);
						}
						$vacId = $data['vacationId'];
						$content .= $this->getAttendees($vacId);
					} else {
						$vacId = 0;
						$content .= $this->redirectToList();
					}
					
					//t3lib_BEfunc::getSetUpdateSignal('updatePageTree');
					
					$this->content .= $this->doc->section('',$content,0,1);
				}
				
				function redirectToList() {
					global $LANG;
					$out = '';
					$out .= '<a href="../mod2/index.php">';
					$out .= $LANG->getLL('gotoList');
					$out .= '</a>';
					return $out;
				}
				function getAttendees($vacId) {
					global $LANG;
					$out = '';
					// get data from vacation
					$vacation = t3lib_BEfunc::getRecord('tx_rtvacationcare_vacations',$vacId);
					
					// get all already for this vacation registered attendees
					// first get registrations for this vacation
					$registrationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid_local as reg_uid', #SELECT
						'tx_rtvacationcare_regist_vacationid_mm',
						'uid_foreign = "'.$vacId.'" ');
					
					$registeredAttendees = array();
					// then get attendee to this registration
					while ($registrations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($registrationRes) ) {
						$attendeeRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'tt_address.first_name as a_fname, tt_address.last_name as a_lname, tt_address.uid as a_uid', #SELECT
						'tx_rtvacationcare_regist',
						'tx_rtvacationcare_regist_attendeeid_mm',
						'tt_address',
						'AND tx_rtvacationcare_regist_attendeeid_mm.uid_local = "'.$registrations['reg_uid'].'" ');
						$attendee = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attendeeRes);
						$registeredAttendees[]= array('uid' => $attendee['a_uid'], 'first_name' => $attendee['a_fname'], 'last_name' => $attendee['a_lname'], 'reg_uid' => $registrations['reg_uid']);
					}
#echo t3lib_div::debug($registeredAttendees,'');

					
					$editTable = 'tx_rtvacationcare_regist';

					$kcont .= $this->doc->spacer(10);
									
					// listview table
					$kcont .= '<h2>'.$LANG->getLL('title').' '.$LANG->getLL('for').' '.$vacation['title'].'</h2>';
					// back button
					$kcont .= '<a href="../mod2/index.php">'.$LANG->getLL('backToListview').'</a>';
					$kcont .= $this->doc->spacer(10);
					
					// New registration pid: 24
					$editUid = 24;
					$params = '&edit['.$editTable.']['.$editUid.']=new&defVals['.$editTable.'][vacationid]='.$vacId;
					$newLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">';
					$newLink.= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_record.gif','width="11" height="12"').' title="'.$LANG->getLL('newRegistration').'" border="0" alt="" />';
					$newLink.= $LANG->getLL('newRegistration');
					$newLink .='</a>';
					$kcont .= $newLink;
					$kcont .= $this->doc->spacer(10);
					
					// div
					$kcont .= '<div style="height: 80%; width: 400px; overflow: auto;">';
					$kcont .= '<table style="padding:4px; margin: 2px;">';
					// table header
					$kcont .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('attendeeName').'</td><td>'.$LANG->getLL('payed').'</td><td>'.$LANG->getLL('delete').'</td></tr>';
					$counter = 0;
					
					foreach($registeredAttendees as $attendee) {
					# while($allAttendees = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($allAttendeesRes)) {
						// init edit options
						$editUid = $attendee['uid'];
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						// name
						$kcont .= '<tr><td>'.$attendee['first_name'].', '.$attendee['last_name'].'</td>';
						// payed
						$kcont .= '<td style="text-align: center;">'.$this->checkPayment($attendee['reg_uid']).'</td>';
						// delete
						$kcont .= '<td>'.$this->deleteRegistration($attendee['reg_uid'], $vacId, $attendee['uid']).'</td>';
						$kcont .= '</tr>';
						$counter ++;
					}
					
					if ($counter == 0) $kcont .= $LANG->getLL('noRegistrations');
					$kcont .= '</table>';
					$kcont .= '</div>';

					$out .= $kcont;
					// go through all attendees, if attendee is already registered, mark him
					
					
					return $out;
				}
				
				function deleteRegistration($regUid, $vacId, $attendeeUid) {
					global $LANG;
					$out = '<a href="index.php?SET[delRegUid]='.$regUid.'&SET[vacationId]='.$vacId.'" onclick="return confirm(unescape(\''.$LANG->getLL('reallyDeleteRegistration').'\'));">';
					$out .= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/delete.gif','width="11" height="12"').' border="0" alt="" />';
					$out .= '</a>';
					return $out;
				}
				
				function checkPayment($regUid) {
					global $LANG;
					$out = '';
					$params = '&edit[tx_rtvacationcare_regist]['.$regUid.']=edit&columnsOnly=paid';
					$registration = t3lib_BEfunc::getRecord('tx_rtvacationcare_regist',$regUid);
					// link
					$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$LANG->getLL('edit').'">';
					if ($registration['paid'] == 1) {
						$out .= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_ok.gif','width="11" height="12"').' border="0" alt="" />';
					} else {
						$out .= $LANG->getLL('notPayed');
					}
					$out .= '</a>';
					return $out;
				}
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod7/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod7/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rtvacationcare_module7');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>