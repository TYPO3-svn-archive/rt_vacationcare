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

require_once(t3lib_extMgm::extPath('rt_vacationcare') . 'class.tx_rtvacationcare_pdfconf.php');

$LANG->includeLLFile('EXT:rt_vacationcare/mod2/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

$attendeeFields = 'gender,first_name,last_name,birthday,email,phone,address,zip,city,image,user_attendeeaddress_dossier,user_attendeeaddress_wheelchair,user_attendeeaddress_blind,user_attendeeaddress_specials,user_attendeeaddress_c_address,user_attendeeaddress_c_zip,user_attendeeaddress_c_city,user_attendeeaddress_c_phone,user_attendeeaddress_c_mobile,user_attendeeaddress_c_mail,user_attendeeaddress_c_name,user_attendeeaddress_c_relation,user_attendeeaddress_c_fax,user_attendeeaddress_c_invoice,user_attendeeaddress_c_invoice_address';
$caretakerFields = 'gender,first_name,last_name,birthday,email,phone,fax,mobile,address,zip,city,country,image';


/**
 * Module 'Vacations' for the 'rt_vacationcare' extension.
 *
 * @author	Stefan Voelker <t3x@nyxos.de>
 * @package	TYPO3
 * @subpackage	tx_rtvacationcare
 */
class  tx_rtvacationcare_module2 extends t3lib_SCbase {
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
					
					// year-selection
					$yearArray = array();
					$lastYear = date("Y",time())+2;
					for ($i = 2006; $i <= $lastYear; $i++) {
						$yearArray[$i]=$i;
					}
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function2'),
							'4' => $LANG->getLL('function4'),
						),
						'year' => $yearArray
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
					// we don't need pages here...
					if ($this->id == 0) $this->id = 1;
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
				function moduleContent() {
					global $LANG;
					// CSS Styles for backendmodules
					$content = '<style type="text/css">@import url(../../../../typo3conf/ext/rt_vacationcare/res/vcm_mod.css);
</style>';
					$data = t3lib_div::_GET('SET');
					$vacId = (int)$data['vacationId'];
					if (!$vacId) {
						$vacId = (int)t3lib_div::_POST('vacationId');
					}
					$setNewCaretaker = t3lib_div::_POST('setNewCaretaker');
					$setNewAttendee = t3lib_div::_POST('setNewAttendee');
#echo t3lib_div::debug($data,'');
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
							// make Invoice ?
							if ((int)$data['getInvoice'] > 0 && (int)$data['recipient'] > 0 ) {
								// first let user choose name and checkbox for pictures
								$vacation = t3lib_BEfunc::getRecord('tx_rtvacationcare_vacations', $data['getInvoice']);					
								if ($pdfShowPictures != 1) $pdfShowPictures = 0;
								if ($pdfShowInfo != 1) $pdfShowInfo = 0;
									$pdfClass = t3lib_div::makeInstance('tx_rtvacationcare_pdfconf');
									$makePdf = $pdfClass->formatInvoice($data['recipient'], $vacation);
							}							
							
							// make pdf ?
							if ((int)$data['getPdf'] > 0 ) {
								// first let user choose name and checkbox for pictures
								$nameForPdf = t3lib_div::_POST('nameForPdf');
								$pdfShowPictures = t3lib_div::_POST('pdfShowPictures');
								$pdfShowInfo = t3lib_div::_POST('pdfShowInfo');
								$pdfShowBirthdays = t3lib_div::_POST('pdfShowBirthdays');
								$reallyMakePdfNow = (int)t3lib_div::_POST('reallyMakePdfNow');
								$vacation = t3lib_BEfunc::getRecord('tx_rtvacationcare_vacations', $data['getPdf']);
								$content .= '<h3>PDF erstellen für -'.$vacation['title'].'-</h3>';
								$content .= '<label for="nameForPdf">Grüß Gott, </label><input type="text" name="nameForPdf" size="50" />.<br />';
								$content .= '<input type="hidden" name="reallyMakePdfNow" value="1" />';
								$content .= '<label for="pdfShowPictures">Bilder drucken?</label> <input type="checkbox" name="pdfShowPictures" value="1" /><br />';
								$content .= '<label for="pdfShowInfo">Infotext drucken?</label> <input type="checkbox" name="pdfShowInfo" value="1" /><br />';
								$content .= '<label for="pdfShowBirthdays">Geburtstage drucken?</label> <input type="checkbox" name="pdfShowBirthdays" value="1" checked="checked" /><br />';
								$content .= '<input type="submit" value="PDF jetzt erstellen" />';
								$content .= $this->doc->spacer(5);
								$content .= '<a href="index.php?SET[function]=1">Abbrechen</a>';
								$content .= $this->doc->spacer(10);
								if ($reallyMakePdfNow == 1) {					
									if ($pdfShowPictures != 1) $pdfShowPictures = 0;
									if ($pdfShowInfo != 1) $pdfShowInfo = 0;
									if ($pdfShowBirthdays != 1) $pdfShowBirthdays = 0;
									$pdfClass = t3lib_div::makeInstance('tx_rtvacationcare_pdfconf');
									$makePdf = $pdfClass->formatAsPDF($vacation, $nameForPdf, $pdfShowPictures, $pdfShowInfo, $pdfShowBirthdays);
								}
							}
							// listview
							
							$content .= $this->listVacations();
							break;
						case 2:
							// registration mode
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
								
								// new easy registration
								$newRegistration = (int)t3lib_div::_POST('newRegistration');		
								if ($newRegistration == 1 && $setNewAttendee && is_array($setNewAttendee) && $vacId > 0) {
									foreach ($setNewAttendee as $at) {
#echo t3lib_div::debug($insertArray,'');
										// save new entries
										$registrationCount = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
											'COUNT(*) as registrations',
											'tx_rtvacationcare_regist_attendeeid_mm',
											'uid_local = '.$at.' AND uid_foreign = '.$vacId);
										$registrated = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($registrationCount);
										// but check, if regist not already saved
										if ((int)$registrated['registrations'] <= 0) {
											$theTime = time();
											// new entry: table _regist
											$insertArray = array('pid' => '24', 'tstamp' => $theTime, 'crdate' => $theTime, 'cruser_id' => $BE_USER->user['uid'], 'attendeeid' => '1', 'vacationid' => '1');
											$setConf = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_regist', $insertArray);
											$newId = mysql_insert_id();
											
											// new entry: table _regist_attendeeid_mm
											$insertArray2 = array('uid_local' => $newId, 'uid_foreign' => $at, 'sorting' => 1);
											$setConf = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_regist_attendeeid_mm', $insertArray2);
											
											// new entry: table _regist_vacationid_mm
											$insertArray3 = array('uid_local' => $newId, 'uid_foreign' => $vacId, 'sorting' => 1);
											$setConf = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_regist_vacationid_mm', $insertArray3);
										}
									}
								}
								
								// show registration site again
								$vacId = $data['vacationId'];
								$content .= $this->getAttendeeList($vacId);
							} else {
								$content = '<a href="index.php?SET[function]=1">'.$LANG->getLL('backToListview').'</a>';
							}
							break;
						case 3:
							// quickview mode
							if (is_array($data)) {
								$vacId = $data['vacationId'];
								$content .= $this->getQuickview($vacId);
							} else {
								$content .= $this->listVacations();
							}
							break;
						case 4:
							// manage caretaker wishes and confirmations
							if (is_array($data)) {
								$setConf = (int)$data['setConfirmation'];
								$delConf = (int)$data['deleteConfirmation'];
								// confirmation deletion ?
								if ($delConf > 0 && $vacId > 0) {
									$delConfRes = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_vacations_caretaker_mm', 'uid_foreign = '.$delConf.' AND uid_local = '.$vacId);
									// if this caretaker was chief -> delete chief
echo t3lib_div::debug($vacId.' '.$delConf,'');
									$deleteChief = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_vacations_caretakerchief_mm', 'uid_local = '.$vacId.' AND uid_foreign = '.$delConf);
								}
								
								// set confirmation (via the wish-list)
								if ($setConf > 0 && $vacId > 0) {
									// only if user is not yet saved
									$countCaretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
										'COUNT(*) as counter',
										'tx_rtvacationcare_vacations_caretaker_mm',
										'uid_foreign = '.$setConf.' AND uid_local = '.$vacId);
									$countCaretaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($countCaretakerRes);
									$countCaretaker = $countCaretaker['counter'];
									if ($countCaretaker == 0) {
										$insertArray=array('uid_local' => $vacId, 'uid_foreign' => $setConf, 'sorting' => 1);
										$setConf = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_vacations_caretaker_mm', $insertArray);
									}
								}
								
								// set new confirmations (via the multiple-select)
								$newConfirmations = t3lib_div::_POST('newConfirmations');
								if ((int)$newConfirmations == 1 && $setNewCaretaker && is_array($setNewCaretaker) && $vacId > 0) {
									foreach ($setNewCaretaker as $ct) {
										$insertArray = array('uid_local' => $vacId, 'uid_foreign' => $ct, 'sorting' => 1);
										$setConf = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_vacations_caretaker_mm', $insertArray);
									}
								}
								
								// delete chief for vacation (also if new chief is set...)
								$delChief = (int)$data['delChief'];
								$setChief = (int)$data['setChief'];
								if ( ($vacId > 0 && $delChief) == 1 || ($vacId > 0 && $setChief > 0 ) ) {
									$deleteChief = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_vacations_caretakerchief_mm', 'uid_local = '.$vacId);
								}
								
								// set chief for vacation
								if ($vacId > 0 && $setChief > 0) {
									$insertChief = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
										'tx_rtvacationcare_vacations_caretakerchief_mm',
										array('uid_local' => $vacId, 'uid_foreign' => $setChief, 'sorting' => '1')
									);
								}
								
								$content .= $this->manageCaretakers($vacId);
							} else {
								$content .= $this->listVacations();
							}
							break;
						default:
					}					
					$this->content.=$this->doc->section('',$content,0,1);
				}
				
				function getQuickview($vacId) {
					global $LANG, $attendeeFields, $caretakerFields;
					$out = '';
					// get attendees
					
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
					
					$out .= $this->doc->spacer(10);
					// back button
					$out .= '<a href="index.php?SET[function]=1">'.$LANG->getLL('backToListview').'</a>';
					$out .= $this->doc->spacer(10);
					$out .= '<h2>'.$LANG->getLL('attendees').'</h2>';
					$out .= '<table style="padding:4px; margin: 2px;">';
					// table header
					$out .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('nr').'</td><td>'.$LANG->getLL('nameFirstname').'</td><td>'.$LANG->getLL('altR').'</td><td>'.$LANG->getLL('street').'</td><td>'.$LANG->getLL('zipCity').'</td><td>'.$LANG->getLL('birthday').'</td><td>'.$LANG->getLL('phone').'</td><td>'.$LANG->getLL('wheelchair').'</td></tr>';
					$counter = 1;
					
					foreach($registeredAttendees as $attendee) {
						// get all data from attendee
						$theAttendee = t3lib_BEfunc::getRecord('tt_address',$attendee['uid']);
						// init edit options
						$editUid = $attendee['uid'];
						$editTable = 'tt_address';
						$params='&edit['.$editTable.']['.$editUid.']=edit&columnsOnly='.$attendeeFields;
						
						// nr
						$out .= '<tr><td>'.$counter.'</td>';
						// name
						$out .= '<td><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$attendee['first_name'].', '.$attendee['last_name'].'</a></td>';
						// altR
						$out .= '<td style="text-align: center;">';
						if ($theAttendee['user_attendeeaddress_c_invoice'] != '' ) {
							$invoiceParam = '&edit[tt_address]['.$attendee['uid'].']=edit&columnsOnly=user_attendeeaddress_c_invoice';
							$out.= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($invoiceParam,$GLOBALS['BACK_PATH'])).'">>R<</a>';
							// PDF link
						$out .= ' <a href="index.php?SET[function]=1&SET[getInvoice]='.$vacId.'&SET[recipient]='.$editUid.'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/fileicons/pdf.gif','width="11" height="12"').' title="Bestätigung erstellen" border="0" alt="" /></a>';
						}
						$out.= '</td>';
						// street
						$out .= '<td>'.$theAttendee['address'].'</td>';
						// plz, city
						$out .= '<td>'.$theAttendee['zip'].', '.$theAttendee['city'].'</td>';
						// birthday
						$out .= '<td>'.date('d.m.Y', $theAttendee['birthday']).'</td>';
						// phone
						$out .= '<td>'.$theAttendee['phone'].'</td>';
						// wheelchair
						$out .= '<td>'.$this->needWheelchair($theAttendee['user_attendeeaddress_wheelchair']).'</td>';
						$out .= '</tr>';
						$counter ++;
					}
					
					if ($counter == 0) $kcont .= $LANG->getLL('noRegistrations');
					$out .= '</table>';
					$out .= $this->doc->spacer(10);
					// get caretaker
					$caretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'* ', #SELECT
						'tx_rtvacationcare_vacations',
						'tx_rtvacationcare_vacations_caretaker_mm',
						'tt_address',
						'AND tx_rtvacationcare_vacations_caretaker_mm.uid_local = "'.$vacId.'" ');
					$counter = 1;
					$out .= '<table>';
					$out .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('nr').'</td><td>'.$LANG->getLL('nameFirstname').'</td><td>'.$LANG->getLL('street').'</td><td>'.$LANG->getLL('zipCity').'</td><td>'.$LANG->getLL('birthdate').'</td><td>'.$LANG->getLL('phone').'</td></tr>';
					while($allCaretakers = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerRes) ) {
						// init edit options
						$editUid = $allCaretakers['uid'];
						$editTable = 'tt_address';
						$params='&edit['.$editTable.']['.$editUid.']=edit&columnsOnly='.$caretakerFields;
						// nr
						$out .= '<tr><td>'.$counter.'</td>';
						// name
						$out .= '<td><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$allCaretakers['first_name'].', '.$allCaretakers['last_name'].'</a></td>';
						// street
						$out .= '<td>'.$allCaretakers['address'].'</td>';
						// plz, city
						$out .= '<td>'.$allCaretakers['zip'].', '.$allCaretakers['city'].'</td>';
						// birthday
						$out .= '<td>'.date('d.m.Y', $allCaretakers['birthday']).'</td>';
						// phone
						$out .= '<td>'.$allCaretakers['phone'].'</td>';
						$out .= '</tr>';
						$counter ++;
					}
					$out .= '</table>';
					return $out;
				}

				function needWheelchair($data) {
					$out = '';
					if ((int)$data == 1) {
						$out .= '<img src="../res/rs-1.gif" />';
					}
					return $out;
				}
				
				
				/**
				 * getAttendeeList function.
				 * 
				 * @access public
				 * @param mixed $vacId
				 * @return void
				 */
				function getAttendeeList($vacId) {
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
					$registeredAttendeesUid = array();
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
						$registeredAttendeesUid[] = $attendee['a_uid'];
					}
#echo t3lib_div::debug($registeredAttendees,'');
					
					$editTable = 'tx_rtvacationcare_regist';

					$kcont .= $this->doc->spacer(10);
									
					// listview table
					$kcont .= '<h2>'.$LANG->getLL('regTitle').' '.$LANG->getLL('for').' '.$vacation['title'].' ('.count($registeredAttendeesUid).')</h2>';
					$kcont .= $this->doc->spacer(10);
					
					// div
					$kcont .= '<div style="width: 400px; overflow: auto;">';
					$kcont .= '<table style="padding:4px; margin: 2px;">';
					// table header
					$kcont .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('attendeeName').'</td><td>'.$LANG->getLL('payed').'</td><td>'.$LANG->getLL('delete').'</td></tr>';
					$counter = 0;
					
					foreach($registeredAttendees as $attendee) {
						// init edit options
						$editUid = $attendee['uid'];
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						// name
						$kcont .= '<tr><td>'.$attendee['first_name'].', '.$attendee['last_name'].'</td>';
						// payed
						$kcont .= '<td style="text-align: center;">'.$this->checkPayment($attendee['reg_uid']).'</td>';
						// delete
						$kcont .= '<td style="text-align: center;">'.$this->deleteRegistration($attendee['reg_uid'], $vacId, $attendee['uid']).'</td>';
						$kcont .= '</tr>';
						$counter ++;
					}
					
					if ($counter == 0) $kcont .= $LANG->getLL('noRegistrations');
					$kcont .= '</table>';

					// show all attendees to add to vacation == new registration
					$kcont .= '<h3>Alle Teilnehmer</h3>';
					#$kcont .= '<form action="index.php?SET[function]=2" name="newRegistrations" method="post" enctype="multipart/form-data">';
					$kcont .= '<input type="hidden" name="newRegistration" value="1" />
					<input type="hidden" name="vacationId" value="'.$vacId.'" />';
					$kcont .= '<select name="setNewAttendee[]" size="15" style="width:250px;" multiple="multiple">';
					$allAttendeeRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tt_address',
						'pid = 21'.t3lib_BEfunc::deleteClause('tt_address'),
						'',
						'last_name');
					while($attendee = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($allAttendeeRes)){
						if (!in_array($attendee['uid'], $registeredAttendeesUid)) {
							$kcont .= '<option value="'.$attendee['uid'].'">'.$attendee['first_name'].' '.$attendee['last_name'].'</option>';
						}
					}
					$kcont .= '</select>';
					$kcont .= '<br /><input type="submit" value="Teilnehmer jetzt anmelden" />';
					#$kcont .= '</form>';
										
					$kcont .= '</div>';

					$out .= $kcont;	
					// back button
					$out .= $this->doc->spacer(10);
					$out .= '<a href="index.php?SET[function]=1">'.$LANG->getLL('backToListview').'</a>';				
					$out .= $this->doc->spacer(10);
					$out .= '<a href="index.php?SET[function]=4&SET[vacationId]='.$vacId.'">zu den Betreuern &raquo;</a>';
					return $out;
				}
				
				function countAttendees($vacId) {
					global $LANG;
					$out = '';
					// get data from vacation
					$vacation = t3lib_BEfunc::getRecord('tx_rtvacationcare_vacations',$vacId);
					
					// get all already for this vacation registered attendees
					// first get registrations for this vacation
					$registrationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'COUNT(*) as anzahl', #SELECT
						'tx_rtvacationcare_regist_vacationid_mm',
						'uid_foreign = "'.$vacId.'" ');
					$registrations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($registrationRes);
					return $registrations['anzahl'];
				}
				
				function deleteRegistration($regUid, $vacId, $attendeeUid) {
					global $LANG;
					$out = '<a href="index.php?SET[delRegUid]='.$regUid.'&SET[vacationId]='.$vacId.'" onclick="return confirm(unescape(\''.$LANG->getLL('reallyDeleteRegistration').'\'));">';
					$out .= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/garbage.gif','width="11" height="12"').' border="0" alt="" />';
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
				
				############################################
				#
				# generates a list of all vacations in the set year
				#
				
				function listVacations() {
					global $LANG;
					
/*				
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'vacations_old',
						'',
						'',
						'');
					while ($erg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
					// get first two letters from "titel"
					$the_nr = substr($erg['titel'],0,2);
						$insertArray=array(
							'booked'=>$erg['bookeddate'],
							'approved'=>$erg['confdate'],
							'title'=>$erg['titel'],
							'description' =>$erg['text'],
							'startdate' => $erg['begindatetime'],
							'enddate' => $erg['enddatetime'],
							'luggage'=>$erg['gepaeck'],
							'pocketmoney'=>$erg['taschengeld'],
							'snack'=>$erg['brotzeit'],
							'price'=>$erg['preis'],
							'meetingpoint'=>$erg['treffpunkt'],
							'info' => $erg['info'],
							'pid'=>19,
							'info2'=>$erg['finanzen'],
							'info3'=>$erg['bus'],
							'maxattendees'=>$erg['maxTeilnehmer'],							
							'cruser_id' => 1,
							'nr' => $the_nr,
							'crdate' => time(),
						'tstamp'=>time()		
						);
						$go = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_vacations',$insertArray);
					}
*/					
					$content = $this->yearMenu();
					$activeYear = $this->MOD_SETTINGS['year'];
					// get all vacations from active year
					$vacationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tx_rtvacationcare_vacations',
						'FROM_UNIXTIME(startdate, "%Y" ) = '.$activeYear,
						'',
						'nr');
					
					// count vacations
					$vacationCountRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'COUNT(*) as anzahl',
						'tx_rtvacationcare_vacations',
						'FROM_UNIXTIME(startdate, "%Y" ) = '.$activeYear);
					$vacationCount = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($vacationCountRes);
					$vacationCount = $vacationCount['anzahl'];
					$editTable = 'tx_rtvacationcare_vacations';
					// New vacation pid: 19
					$editUid = 19;
					$startTime = mktime(10,0,0,1,1,$activeYear);
					$endTime = mktime(17,0,0,1,1,$activeYear);
					$params = '&edit['.$editTable.']['.$editUid.']=new&defVals['.$editTable.'][title]=Neu&defVals['.$editTable.'][startdate]='.$startTime.'&defVals['.$editTable.'][enddate]='.$endTime;
					$newLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">';
					$newLink.= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_record.gif','width="11" height="12"').' title="'.$LANG->getLL('createVacation').'" border="0" alt="" />';
					$newLink.= $LANG->getLL('createVacation');
					$newLink .='</a>';
					$kcont = $newLink;
					$kcont .= $this->doc->spacer(10);
					
					// weekdays
					$weekDaysArray = array($LANG->getLL('sun'),
						$LANG->getLL('mon'),
						$LANG->getLL('tue'),
						$LANG->getLL('wed'),
						$LANG->getLL('thu'),
						$LANG->getLL('fri'),
						$LANG->getLL('sat'));
					// listview table
					// count all registrations
					$allRegistrationsRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'COUNT(*) as amount',
						'tx_rtvacationcare_regist',
						'tx_rtvacationcare_regist_vacationid_mm',
						'tx_rtvacationcare_vacations',
						'AND FROM_UNIXTIME(tx_rtvacationcare_vacations.startdate, "%Y" ) = '.$activeYear
					);
					$numberOfRegistrationsRows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($allRegistrationsRes);
					$numberOfRegistrations = $numberOfRegistrationsRows['amount'];
					
					$kcont .= '<h2>'.$vacationCount.' '.$LANG->getLL('title').' '.$LANG->getLL('for').' '.$activeYear.' '.$LANG->getLL('and').' '.$numberOfRegistrations.' '.$LANG->getLL('registrations').'</h2>';
					$kcont .= '<table style="padding:4px; margin: 2px;" width="760">';
					// table header
					$kcont .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('nr').'</td><td>'.$LANG->getLL('title').'</td><td>'.$LANG->getLL('date').'</td><td>'.$LANG->getLL('confirmed').'</td><td>'.$LANG->getLL('booked').'</td><td>'.$LANG->getLL('attendees').'</td><td>'.$LANG->getLL('caretakers').'</td><td>'.$LANG->getLL('quickview').'</td><td>Infofeld</td><td>'.$LANG->getLL('edit').'</td></tr>';
					$counter = 0;
					while($allVacations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($vacationRes)) {
						// init edit options
						$editUid = $allVacations['uid'];
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						// colours
						// get lodging and therefore the max number of places
						$lodgingRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
							'tx_rtvacationcare_lodgings.max as lodging_max', #SELECT
							'tx_rtvacationcare_vacations', # local table
							'tx_rtvacationcare_vacations_lodging_mm', # mm table
							'tx_rtvacationcare_lodgings', #foreign
							'AND tx_rtvacationcare_vacations_lodging_mm.uid_local = "'.$editUid.'" ');
						$theLodge = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($lodgingRes);
						// count attendees
						$attendeeAmount = $this->countAttendees($editUid);
						// count caretaker
						$caretakerAmount = $this->getCaretaker($editUid,0,1);
						
						// but check with countAttendees and countCaretaker
						if($theLodge['lodging_max'] >= ($attendeeAmount + $caretakerAmount) ) {
							$cellColour = 'green';
						} else {
							$cellColour = 'red';
						}
						
						// number and title
						$kcont .= '<tr><td>'.$allVacations['nr'].'</td><td style="color:'.$cellColour.'">'.$allVacations['title'];
						// PDF link
						$kcont .= ' <a href="index.php?SET[function]=1&SET[getPdf]='.$editUid.'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/fileicons/pdf.gif','width="11" height="12"').' title="Bestätigung erstellen" border="0" alt="" /></a>';
						$kcont .= '</td>';
						// date
						$kcont .= '<td>'.date('d.m', $allVacations['startdate']).'. - '.date('d.m', $allVacations['enddate']).'.</td>';
						// confirmed
						$kcont .= '<td style="text-align: center;">'.$this->checkAppBook($allVacations['approved'], $editTable, $editUid, 'approved').'</td>';
						// booked
						$kcont .= '<td style="text-align: center;">'.$this->checkAppBook($allVacations['booked'], $editTable, $editUid, 'booked').'</td>';
						// attendees
						$kcont .= '<td style="text-align: center;">'.$this->getAttendees($allVacations['uid']).'</td>';
						// caretakers
						$kcont .= '<td style="text-align: center;">'.$this->getCaretaker($allVacations['uid'], $allVacations['maxcaretaker']).'</td>';
						// quickview
						$kcont .= '<td style="text-align: center;"><a href="index.php?SET[vacationId]='.$editUid.'&SET[function]=3"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_note.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' -'.$allVacations['nr'].' '.$allVacations['title'].'- '.$LANG->getLL('quickview').'" border="0" alt="" /></a></td>';
						// infofield
						$paramsInfo = '&edit['.$editTable.']['.$editUid.']=edit&columnsOnly=info';
						$kcont .= '<td style="text-align: center;"><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($paramsInfo,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/info.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' -'.$allVacations['nr'].' '.$allVacations['title'].'- '.$LANG->getLL('edit').'" border="0" alt="" /></a></td>';
						// editlink
						$link = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' -'.$allVacations['nr'].' '.$allVacations['title'].'- '.$LANG->getLL('edit').'" border="0" alt="" /></a>';
						$kcont .= '<td style="text-align: center;"> '.$link.' </td>';
						$kcont .= '</tr>';
						$counter ++;
					}
					if ($counter == 0) $kcont .= $LANG->getLL('noVacations');
					$kcont.="</table>";
					
					
					$content .= $kcont;
					return $content;
				}
				
				function registrations() {
					global $LANG;
					$out = $this->yearMenu();
					$activeYear = $this->MOD_SETTINGS['year'];
					// get all vacations from active year
					$vacationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tx_rtvacationcare_vacations',
						'FROM_UNIXTIME(startdate, "%Y" ) = '.$activeYear,
						'',
						'title');
					
					$editTable = 'tx_rtvacationcare_vacations';

					$kcont .= $this->doc->spacer(10);
					
					// listview table
					$kcont .= '<h2>'.$LANG->getLL('registrationsTitle').$activeYear.'</h2>';
					$kcont .= '<table style="padding:4px; margin: 2px;">';
					// table header
					$kcont .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('nr').'</td><td>'.$LANG->getLL('title').'</td><td>'.$LANG->getLL('attendees').'</td><td>'.$LANG->getLL('edit').'</td></tr>';
					$counter = 0;
					while($allVacations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($vacationRes)) {
						// init edit options
						$editUid = $allVacations['uid'];
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						// number and title
						$kcont .= '<tr><td>'.$allVacations['nr'].'</td><td>'.$allVacations['title'].'</td>';
						// attendees
						$kcont .= '<td style="text-align: center;">'.$this->getAttendees($allVacations['uid']).'</td>';
						// editlink
						$link = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' -'.$allVacations['nr'].' '.$allVacations['title'].'- '.$LANG->getLL('edit').'" border="0" alt="" /></a>';
						$kcont .= '<td style="text-align: center;"> '.$link.' </td>';
						$kcont .= '</tr>';
						$counter ++;
					}
					if ($counter == 0) $kcont .= $LANG->getLL('noVacations');
					$kcont.="</table>";

					$out .= $kcont;
					return $out;
				}
				
				
				function getCaretaker($vacationId, $maxCaretaker, $onlyAmount=0) {
					global $LANG;
					$out = '';
					$params = '&edit[tx_rtvacationcare_vacations]['.$vacationId.']=edit&columnsOnly=caretaker';
					$caretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'COUNT(*) as anzahl ', #SELECT
						'tx_rtvacationcare_vacations_caretaker_mm',
						'uid_local = "'.$vacationId.'" ');
					
					$res = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerRes);
					$count = $res['anzahl'];
					// get caretaker wishes
					$wishRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'COUNT(*) as wishes',
						'tx_rtvacationcare_vacations_caretakerwish_mm',
						'uid_local = '.$vacationId);
					$wishes = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($wishRes);
					$wishes = $wishes['wishes'];
					if ($wishes > 0) {
						$theWishes .= ' W:'.$wishes;
					}					
					// make link
					$out .= '<a href="index.php?SET[vacationId]='.$vacationId.'&SET[function]=4" title="'.$LANG->getLL('edit').'">';
					if ((int)$maxCaretaker>0) {
						$out .= 'M:'.$maxCaretaker;
					}
					$out .= ' E:'.$count.$theWishes.'</a>';
					
					if ($onlyAmount == 1) {
						$out = $count;
					}

					return $out;
				}
				
				/**
				 * getAttendees function.
				 * 
				 * @access public
				 * @param mixed $vacationId
				 * @return void
				 */
				function getAttendees($vacationId) {
					global $LANG;
					$out = '';
					// get all registrations -> tx_rtvacationcare_regist_vacationid_mm
					$registrationsRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'COUNT(*) as anzahl', #SELECT
						'tx_rtvacationcare_regist', # local table
						'tx_rtvacationcare_regist_vacationid_mm', # mm table
						'tx_rtvacationcare_vacations', #foreign
						' AND tx_rtvacationcare_regist_vacationid_mm.uid_foreign = "'.$vacationId.'" ');
					$registrations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($registrationsRes);
					$registrations = $registrations['anzahl'];
					
					// make link to mode "registrations"
					$out .= '<a href="index.php?SET[vacationId]='.$vacationId.'&SET[function]=2">';
					$out .= '[ '.$registrations.' ]';
					$out .= '</a>';
					return $out;
				}
				
				######################################
				#
				# returns either a ok-grafic, or a edit-linked plus-grafic
				#
				
				function checkAppBook($approved, $editTable, $editUid, $onlyField) {
					global $LANG;
					$params='&edit['.$editTable.']['.$editUid.']=edit&columnsOnly='.$onlyField;
					if($approved != 0) {
						$out = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_ok.gif','width="11" height="12"').' border="0" alt="" />';
					} else {
						$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$LANG->getLL('edit').'">';
						$out .= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/add.gif','width="11" height="12"').' border="0" alt="" />';
						$out .= '</a>';
					}
					return $out;
				}
				
				#####################################
				#
				# returns the year-menu
				#
				
				function yearMenu() {
					global $LANG; 
					$yearMenu = $LANG->getLL('chooseYear').t3lib_BEfunc::getFuncMenu($this->id,"SET[year]",$this->MOD_SETTINGS['year'],$this->MOD_MENU['year']).$this->doc->spacer(10);
					return $yearMenu;
				}
				
				function manageCaretakers($vId) {
					global $LANG;
					$out = '';
					$vacation = t3lib_BEfunc::getRecord('tx_rtvacationcare_vacations', $vId);
					// get chief -> tt_address
					$chiefRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid_foreign as uid',
						'tx_rtvacationcare_vacations_caretakerchief_mm',
						'uid_local = '.$vId);
						$chief = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($chiefRes);
						$chief = $chief['uid'];

					// get all wishes
					$wishRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'fe_users.uid', #SELECT
						'tx_rtvacationcare_vacations', # local table
						'tx_rtvacationcare_vacations_caretakerwish_mm', # mm table
						'fe_users', #foreign
						' AND uid_local = "'.$vId.'" ',
						'',# group by
						'',
					300);
					$allWishes = array();
					while($wishes = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($wishRes) ) {
						$allWishes[] = $wishes;
					}
					// wishes are with fe_users
#echo t3lib_div::debug($allWishes,'Wünsche');				
					// get all confirmed caretakers
					$confirmationRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'tt_address.*', #SELECT
						'tx_rtvacationcare_vacations', # local table
						'tx_rtvacationcare_vacations_caretaker_mm', # mm table
						'tt_address', #foreign
						' AND uid_local = "'.$vId.'" ',
						'',# group by
						'',
					300);
					$allConfirmations = array();
					$allConfirmationIds = array();
					while ($confirmations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($confirmationRes) ) {
						$allConfirmations[] = $confirmations;
						$allConfirmationIds[] = $confirmations['uid'];
					}
					// confirmations are with tt_address...
		
					// show wishes
					// wishes should be changeable to confirmatinons via one click
					$out .= '<h2>'.$vacation['title'].'</h2>';
					$out .= '<h3>Betreuerwünsche ('.count($allWishes).')</h3>';
					$out .= '<table><tr style="font-weight: bold;"><td>Name:</td><td>einplanen</td></tr>';
					
					foreach ($allWishes as $wish ) {
						// this is the fe_user
						// we need tt_address
						$caretakerAddressRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid_foreign as uid',
							'fe_users_user_feloginaddress_tt_address_mm',
							'uid_local = '.$wish['uid']);
						$caretakerAddress = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerAddressRes);
						$caretakerAddressUid = $caretakerAddress['uid'];
						$caretaker = t3lib_BEfunc::getRecord('tt_address', $caretakerAddressUid);
#echo t3lib_div::debug($caretaker,'');
						$out .= '<tr><td>'.$caretaker['first_name'].' '.$caretaker['last_name'].'</td><td>';
						// if caretaker is already confirmed - leave this empty
						if (in_array($caretaker['uid'], $allConfirmationIds) && is_array($allConfirmationIds)) {
							$out .= '-';
						} else {
							$out .= '<a href="index.php?SET[vacationId]='.$vId.'&SET[function]=4&SET[setConfirmation]='.$caretaker['uid'].'">Jetzt einplanen</a>';
						}
						$out .= '</td></tr>';
					} 
					if (!$caretakerAddressRes) {
						$out .= '<tr><td colspan="2">Keine Wünsche</td></tr>';
					}
					$out.='</table>';
					
					// show confirmed caretakers
					// incl. deletion of confirmations
					$out .= '<h3>Eingeplante Betreuer ('.count($allConfirmationIds).')</h3>';
					$out .= '<table><tr style="font-weight: bold;"><td>Name:</td><td>austragen</td></tr>';

					foreach ($allConfirmations as $confirms ) {
						$out .= '<tr><td>';
						if ($chief != $confirms['uid']) {
							$out .= '<a href="index.php?SET[function]=4&SET[setChief]='.$confirms['uid'].'&SET[vacationId]='.$vId.'">';
							$out .= $confirms['first_name'].' '.$confirms['last_name'];
							$out .= '</a>';
						} else {
							$out .= '<strong><a href="index.php?SET[function]=4&SET[delChief]=1&SET[vacationId]='.$vId.'">'.$confirms['first_name'].' '.$confirms['last_name'].'</a></strong>';
						}
												
						$out .= '</td><td>';
						// cancel confirmation
						$out .= '<a href="index.php?SET[vacationId]='.$vId.'&SET[function]=4&SET[deleteConfirmation]='.$confirms['uid'].'">austragen</a>';
						$out .= '</td></tr>';
					}
					$out .= '</table>';
					
										
					// show all caretakers to add to vacation
					$out .= '<h3>Alle Betreuer</h3>';
					#$out .= '<form  action="index.php?SET[vacationId]='.$vId.'&SET[function]=4&SET[newConfirmations]=1" name="newConfirmations" method="post" enctype="multipart/form-data">';
					$out .= '<input type="hidden" name="newConfirmations" value="1" />
					<input type="hidden" name="vacationId" value="'.$vId.'" />';
					$out .= '<select name="setNewCaretaker[]" size="15" style="width:250px;" multiple="multiple">';
					$allCaretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tt_address',
						'pid = 20'.t3lib_BEfunc::deleteClause('tt_address'),
						'',
						'last_name');
					while($caretaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($allCaretakerRes)){
						if (!in_array($caretaker['uid'], $allConfirmationIds)) {
							$out .= '<option value="'.$caretaker['uid'].'">'.$caretaker['first_name'].' '.$caretaker['last_name'].'</option>';
						}
					}
					$out .= '</select>';
					$out .= '<br /><input type="submit" value="Betreuer jetzt einplanen" />';
					#$out .= '</form>';
					
					// backlink
					$out .= $this->doc->spacer(20);
					$out .= '<a href="index.php?SET[function]=1">'.$LANG->getLL('backToListview').'</a>';
					$out .= $this->doc->spacer(10);
					$out .= '<a href="index.php?SET[function]=2&SET[vacationId]='.$vId.'">zu den Teilnehmern &raquo;</a>';
					return $out;
				}
				
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod2/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod2/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rtvacationcare_module2');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>