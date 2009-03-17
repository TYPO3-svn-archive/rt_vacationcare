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

$LANG->includeLLFile('EXT:rt_vacationcare/mod4/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
$caretakerFields = 'gender,first_name,last_name,birthday,email,phone,fax,mobile,address,zip,city,country,image';
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$feuPid = 25;

/**
 * Module 'Caretakers' for the 'rt_vacationcare' extension.
 *
 * @author	Stefan Voelker <t3x@nyxos.de>
 * @package	TYPO3
 * @subpackage	tx_rtvacationcare
 */
class  tx_rtvacationcare_module4 extends t3lib_SCbase {
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

						$this->doc = t3lib_div::makeInstance('mediumDoc');
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

					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
							$content .= $this->showCaretakers();
							$this->content.=$this->doc->section('',$content,0,1);
						break;
					}
				}
				
				function showCaretakers() {
					global $LANG,$caretakerFields,$feuPid;
					$out = '';
					//
/*
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'caretaker',
						'',
						'',
						'');
						
					while ($erg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
						$insertArray=array(
							'name'=>$erg['vorname']." ".$erg['nachname'],
							'first_name'=>$erg['vorname'],
							'last_name'=>$erg['name'],
							'email' =>$erg['email'],
							'phone' => $erg['tel'],
							'mobile' => $erg['mobil'],
							'address'=>$erg['strasse'],
							'city'=>$erg['ort'],
							'zip'=>$erg['plz'],
							'description' => $erg['notiz'],
							'pid'=>20,
							'birthday'=>$erg['time'],
							'addressgroup'=>$this->conf['userKat'],
							'gender'=>$userGender,
							'tstamp'=>time()		
						);
						$go = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tt_address',$insertArray);
					}
*/
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					$caretakerPid = 20;
					$caretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tt_address',
						'pid = '.$caretakerPid.t3lib_BEfunc::deleteClause('tt_address'),
						'',
						'last_name, first_name');
						
					$editTable = 'tt_address';
					$params = '&edit['.$editTable.']['.$caretakerPid.']=new&columnsOnly='.$caretakerFields;
					$newLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">';
					$newLink.= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_record.gif','width="11" height="12"').' title="'.$LANG->getLL('createCaretaker').'" border="0" alt="" />';
					$newLink.= $LANG->getLL('createCaretaker');
					$newLink .='</a>';
					$out .= $newLink;
					$out .= $this->doc->spacer(10);
					// count Caretaker
					$caretakerCountRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*) as anzahl','tt_address','pid = '.$caretakerPid.t3lib_BEfunc::deleteClause('tt_address') );
					$caretakerCount = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerCountRes);
					// jumpmenue
					$out .= '<script LANGUAGE="JavaScript">
<!--

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location=\'"+selObj.options[selObj.selectedIndex].value+"\'");
  if (restore) selObj.selectedIndex=0;
}
// -->
</script>
<div id="jumpy">
  <select name="alphabet" onchange="MM_jumpMenu(\'this\',this,0)">
  	<option value="#top">Nach Oben</option>
    <option value="#A">A</option>
    <option value="#B">B</option>
    <option value="#C">C</option>

    <option value="#D">D</option>
    <option value="#E">E</option>
    <option value="#F">F</option>
    <option value="#G">G</option>
    <option value="#H">H</option>
    <option value="#I">I</option>

    <option value="#J">J</option>
    <option value="#K">K</option>
    <option value="#L">L</option>
    <option value="#M">M</option>
    <option value="#N">N</option>
    <option value="#O">O</option>

    <option value="#P">P</option>
    <option value="#Q">Q</option>
    <option value="#R">R</option>
    <option value="#S">S</option>
    <option value="#T">T</option>
    <option value="#U">U</option>

    <option value="#V">V</option>
    <option value="#W">W</option>
    <option value="#X">X</option>
    <option value="#Y">Y</option>
    <option value="#Y">Z</option>
  </select>
</div>';
					// listview table
					$out .= '<h2 id="top">'.$LANG->getLL('title').' ('.$caretakerCount['anzahl'].')</h2>';
					$out .= '<table style="padding:4px; margin: 2px;" width="90%">';
					// table header
					$out .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('nameFirstname').'</td><td>'.$LANG->getLL('feAccount').'</td><td>'.$LANG->getLL('email').'</td><td>'.$LANG->getLL('birthdate').'</td><td>'.$LANG->getLL('phone').'</td><td></td></tr>';
					// frontend account pid
					while($theCaretaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerRes)) {
						// is there a fe_user for this address
						/*
$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'COUNT(*) as count',
							'fe_users_user_feloginaddress_tt_address_mm',
							'uid_foreign = '.$theCaretaker['uid']);
						$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						
						$username = strtolower($theCaretaker['first_name'].'.'.$theCaretaker['last_name']);
						$pw = md5($this->rand_string(8));
						$email = strtolower($theCaretaker['email']);
						$now = time();
						$insertArray = array('pid' => '25', 'tstamp' => $now, 'username' => $username, 'password' => $pw, 'usergroup' => '1', 'email' => $email, 'crdate' => $now, 'cruser_id' => '1', 'user_feloginaddress_tt_address' => '1');
#echo t3lib_div::debug($insertArray,'');
						if ($row['count'] == 0) {
							// create new fe_user
							
							#$insertFE = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users',$insertArray);
							$newId = $GLOBALS['TYPO3_DB']->sql_insert_id();
							
							// mm 
							$mmArray = array('uid_foreign' => $theCaretaker['uid'], 'uid_local' => $newId, 'sorting' => '1');

							#$insertMM = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users_user_feloginaddress_tt_address_mm',$mmArray);
						}*/
						// init edit options
						$editUid = $theCaretaker['uid'];
						$editTable = 'tt_address';
						$params='&edit['.$editTable.']['.$editUid.']=edit&columnsOnly='.$caretakerFields;
						// name
						$out .= '<tr';
						# anker for jumpmenue
						$letter = substr($theCaretaker['last_name'] , 0, 1);
						if ($$letter != "1") {
							$$letter = "1";
							$out .= ' id="'.$letter.'"';
						}
						$out .= '><td><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$theCaretaker['first_name'].', '.$theCaretaker['last_name'].'</a></td>';
						// frontend account ?
						$out .= '<td>'.$this->checkFEaccount($theCaretaker['uid']).'</td>';
						// email
						$out .= '<td>'.$theCaretaker['email'].'</td>';
						// birthday
						$out .= '<td>'.date('d.m.Y', $theCaretaker['birthday']).'</td>';
						// phone
						$out .= '<td>'.$theCaretaker['phone'].'</td>';
						// editlink
						$link = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' - '.$theCaretaker['first_name'].'- '.$LANG->getLL('edit').'" border="0" alt="" /></a>';
						$out .= '<td style="text-align: center;"> '.$link.' </td>';
						$out .= '</tr>';
					}
					$out .= '</table>';
					return $out;				
				}
				
				function checkFEaccount($userId) {
					global $LANG,$feuPid;
					$out = '';
					$feAccounts = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
						'fe_users.uid as fe_id', #SELECT
						'fe_users', # local table
						'fe_users_user_feloginaddress_tt_address_mm', # mm table
						'tt_address', #foreign
						' AND fe_users_user_feloginaddress_tt_address_mm.uid_foreign = "'.$userId.'" ');
					$feData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($feAccounts);
					// get all userdata
					$userData = t3lib_BEfunc::getRecord('tt_address',$userId);
					// edit data
					$editUid = $feData['fe_id'];
					$editTable = 'fe_users';
					// edit link
					if ((int)$feData['fe_id'] > 0) {
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/fe_users.gif','width="11" height="12"').' title="bearbeiten" border="0" alt="" /></a>'; 
					} else {
						$params='&edit['.$editTable.']['.$feuPid.']=new&defVals['.$editTable.'][user_feloginaddress_tt_address]='.$userId.'&defVals['.$editTable.'][usergroup]=1&defVals['.$editTable.'][email]='.$userData['email'].'&defVals['.$editTable.'][name]='.$userData['first_name'].' '.$userData['last_name'];
						$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/fe_users__h.gif','width="11" height="12"').' title="erstellen" border="0" alt="" /></a>'; 
					}
					
					return $out;
				}
				
				function rand_string($lng) {
   mt_srand(crc32(microtime()));

   //Welche Buchstaben benutzt werden sollen (Charset)
   $buchstaben = "abcdefghijkmnpqrstuvwxyz123456789";
   
   $str_lng = strlen($buchstaben)-1;
   $rand= "";

   for($i=0;$i<$lng;$i++)        
      $rand.= $buchstaben{mt_rand(0, $str_lng)};
      
   return $rand;
   } 
				
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod4/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod4/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rtvacationcare_module4');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>