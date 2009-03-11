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

$LANG->includeLLFile('EXT:rt_vacationcare/mod6/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Lodgings' for the 'rt_vacationcare' extension.
 *
 * @author	Stefan Voelker <t3x@nyxos.de>
 * @package	TYPO3
 * @subpackage	tx_rtvacationcare
 */
class  tx_rtvacationcare_module6 extends t3lib_SCbase {
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
							$content .= $this->showLodgings();
							$this->content.=$this->doc->section('',$content,0,1);
						break;
					}
				}
				
				function showLodgings() {
					global $LANG;
					$out = '';					
					$editTable = 'tx_rtvacationcare_lodgings';
					$lodgingsRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						$editTable,
						' 1=1 '.t3lib_BEfunc::deleteClause($editTable),
						'',
						'title');
						
					
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					$lodgingPid = 23;
					$params = '&edit['.$editTable.']['.$lodgingPid.']=new';
					$newLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">';
					$newLink.= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_record.gif','width="11" height="12"').' title="'.$LANG->getLL('createHome').'" border="0" alt="" />';
					$newLink.= $LANG->getLL('createHome');
					$newLink .='</a>';
					$out .= $newLink;
					$out .= $this->doc->spacer(10);
					// count Lodgings
					$lodgingCountRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*) as anzahl',$editTable,'1=1'.t3lib_BEfunc::deleteClause($editTable) );
					$lodgingsCount = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($lodgingCountRes);
					// listview table
					$out .= '<h2>'.$LANG->getLL('title').' ('.$lodgingsCount['anzahl'].')</h2>';
					$out .= '<table style="padding:4px; margin: 2px;">';
					// table header
					$out .= '<tr style="font-weight: bold; background-color: #ccc"><td>'.$LANG->getLL('titleSingular').'</td><td>'.$LANG->getLL('email').'</td><td>'.$LANG->getLL('zipCity').'</td><td>'.$LANG->getLL('country').'</td><td>'.$LANG->getLL('phone').'</td><td></td></tr>';
					while($theLodging = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($lodgingsRes)) {
						// init edit options
						$editUid = $theLodging['uid'];
						$params='&edit['.$editTable.']['.$editUid.']=edit';
						// title
						$out .= '<td><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$theLodging['title'].'</a></td>';
						// email
						$out .= '<td><a href="mailto:'.$theLodging['email'].'" title="'.$LANG->getLL('sendMail').'">'.$theLodging['email'].'</a></td>';
						// plz, city
						$out .= '<td>'.$theLodging['zip'].', '.$theLodging['city'].'</td>';
						// country
						$out .= '<td>';
						if ($theLodging['country'] == 1) {
							$countryRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
								'static_countries.cn_iso_2 as c_name', #SELECT
								$editTable, # local table
								'tx_rtvacationcare_lodgings_country_mm', # mm table
								'static_countries', #foreign
								' AND tx_rtvacationcare_lodgings_country_mm.uid_local = "'.$theLodging['uid'].'" ');
							$country = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($countryRes);
							$out .= $country['c_name'];
						}
						$out .= '</td>';
						// phone
						$out .= '<td>'.$theLodging['phone'].'</td>';
						// editlink
						$link = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif','width="11" height="12"').' title="'.$LANG->getLL('titleSingular').' - '.$theHome['title'].'- '.$LANG->getLL('edit').'" border="0" alt="" /></a>';
						$out .= '<td style="text-align: center;"> '.$link.' </td>';
						$out .= '</tr>';
					}
					$out .= '</table>';
					return $out;				
				}
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod6/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/mod6/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rtvacationcare_module6');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>