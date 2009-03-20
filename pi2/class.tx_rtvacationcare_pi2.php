<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Stefan Voelker <t3x@nyxos.de>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * 
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

if (t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam') . 'lib/class.tx_dam_media.php');
}
require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

require_once(t3lib_extMgm::extPath('rt_vacationcare') . 'class.tx_rtvacationcare_pdfconf.php');

/**
 * Plugin 'caretakeradmin' for the 'rt_vacationcare' extension.
 *
 * @author	Stefan Voelker <t3x@nyxos.de>
 * @package	TYPO3
 * @subpackage	tx_rtvacationcare
 */
class tx_rtvacationcare_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_rtvacationcare_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_rtvacationcare_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'rt_vacationcare';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$this->path = t3lib_extMgm::siteRelPath($this->extKey);
		$this->activeYear = $this->conf['activeYear'];
		# Template but only for header data (at the moment)
		$templateFile = $this->conf['templateFile'];
		# template-fallback
		if(!$templateFile) {
			$templateFile = 'EXT:'.$this->extKey.'/res/caretaker-manage.html';
		}	
		$this->templateCode = $this->cObj->fileResource($templateFile);
		
		# add header data

		$subPart = $this->cObj->getSubpart($this->templateCode, '###HEADER_ADDITIONS###');
		$key = $this->prefixId.'_'.md5($subPart);
		if (!isset($GLOBALS['TSFE']->additionalHeaderData[$key] )) {
			$templateOut = $this->cObj->substituteMarkerArray($subPart, array('###SITE_REL_PATH###' => t3lib_extMgm::siteRelPath($this->extKey),));
			$GLOBALS['TSFE']->additionalHeaderData[$key] = $templateOut;
		}
	
		# pid of caretaker admin page (via constants)
		$this->caretakeradmin = $this->conf['caretakeradmin'];
		$this->logoutpid = $this->conf['logoutpid'];
		
		$feUser = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
		$realUser = $this->getTTaddress($feUser);
#echo t3lib_div::debug($realUser,'');		
		$statusMessage = '';
		
		# get action and save parameter
		$userAction = t3lib_div::_GET('tx_rtvacationcare_pi2');
		$saveIt = htmlspecialchars($this->piVars['saveIt']);
		
		# should we save new data ?
		if ((int)$saveIt == 1) {
			$pwMessage = $this->saveData(htmlspecialchars($this->piVars['userId']));
			$statusMessage = 'Daten wurden aktualisiert.'.'<br />'.$pwMessage;
		}
		
		// userwish
		if ((int)$userAction['vacwish'] > 0 && $feUser > 0) {
			$vacationWish = (int)$userAction['vacwish'];
			$vacation = $this->pi_getRecord('tx_rtvacationcare_vacations', $vacationWish);
			// check, if wish already saved
			if ($this->checkStatus($realUser['uid'], $feUser, $vacationWish, 1) < 2) {
				// save wish
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_rtvacationcare_vacations_caretakerwish_mm', array('uid_local' => $vacationWish, 'uid_foreign' => $feUser, 'sorting' => 1 ) );
				// caretakerwish in vacation anpassen ?
				
				
				// message		
				$statusMessage = 'Dein Wunsch für die Freizeit "'.$vacation['title'].'" wurde gespeichert.';			
			} else {
				// wish is already saved
				$statusMessage = 'Du hast bereits einen Wunsch für "'.$vacation['title'].'" abgegeben.';
			}
		}
		
		// cancel wish
		if ((int)$userAction['cancelwish'] > 0 && $feUser > 0) {
			$cancelwish = $userAction['cancelwish'];
			$vacation = $this->pi_getRecord('tx_rtvacationcare_vacations', $cancelwish);
			// check, if wish already saved
			if ($this->checkStatus($realUser['uid'], $feUser, $cancelwish, 1) == 2) {
				// delete wish
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rtvacationcare_vacations_caretakerwish_mm', 'uid_local = '.$cancelwish.' AND uid_foreign = '.$feUser );
				// caretakerwish in vacation anpassen ?
				
				
				// message		
				$statusMessage = 'Dein Wunsch für die Freizeit "'.$vacation['title'].'" wurde gelöscht.';			
			} else {
				// wish is already saved
				$statusMessage = 'Du hast noch keinen Wunsch für "'.$vacation['title'].'" abgegeben.';
			}
		}
		
		// send mail of cancelation
		if ((int)$this->piVars['cvId'] > 0 && $feUser > 0) {
			// send mail
			$vacation = $this->pi_getRecord('tx_rtvacationcare_vacations', $this->piVars['cvId']);
			$message = 'Achtung:
'.$realUser['name'].' will sich von der Freizeit '.$vacation['title'].' wieder abmelden.
Seine Begründung:

'.htmlspecialchars($this->piVars['reason']);
			$mail = $mailCaretaker = t3lib_div::makeInstance('t3lib_htmlmail');
			$mail->start();
			$mail->useQuotedPrintable();
			$mail->returnPath = $mail->from_email = $mail->replyto_email = $realUser['email'];
			$mail->from_name = $mail->replyto_name = $realUser['name'];
			$mail->recipient = '"'.addslashes('Christian Balzer').'" <s.voelker@redukt.de>';
			$mail->subject = $realUser['name'].' kann nicht betreuen: '.$vacation['title'];
			$mail->addPlain($message);
			$result = $mail->send('');
			$statusMessage = 'Christian wurde informiert, dass du bei - '.$vacation['title'].' - nicht betreuen willst.';
			
			// mail to caretaker
			$mailCaretaker->start();
			$mailCaretaker->useQuotedPrintable();
			$mailCaretaker->returnPath = $mailCaretaker->from_email = $mailCaretaker->replyto_email = 'noreply@kunterbunt-reisen.de';
			$mailCaretaker->from_name = $mailCaretaker->replyto_name = 'Kunterbunt Freizeitenadmin';
			$mailCaretaker->recipient = '"'.addslashes($realUser['name']).'" <'.$realUser['email'].'>';
			$mailCaretaker->subject = 'Deine stornierte Kunterbunt-Freizeit';
			$messageCaretaker = $statusMessage.'

Deine Begründung:
			'.htmlspecialchars($this->piVars['reason']);
			$mailCaretaker->addPlain($messageCaretaker);
			$result = $mailCaretaker->send('');
		}
		
		// export PDF ?
		if ((int)$userAction['makePdf'] > 0 && $feUser > 0) {
			$vacation = $this->pi_getRecord('tx_rtvacationcare_vacations', $userAction['makePdf']);
			// $this->formatAsPDF($vacation);
			
			$pdfClass = t3lib_div::makeInstance('tx_rtvacationcare_pdfconf');
			$makePdf = $pdfClass->formatAsPDF($vacation);

			exit();
		}
	
		# get Image-TS
		$image = array();
		$image = $this->conf['image.'];
		$image['file'] = 'uploads/pics/'.$realUser['image'];
		$imageOutput = $this->cObj->IMAGE($image);
		
		$imgTooltip = array();
		$imgTooltip = $this->conf['imageTooltip.'];
		$imgTooltip['file'] = 'uploads/pics/'.$realUser['image'];
		
		$content .= '<div id="userImage"><a rel="'.$this->cObj->IMG_RESOURCE($imgTooltip).'"  class="preview">'.$imageOutput.'</a></div>';
		$content .= '<h1>Hallo '.$realUser['first_name'].'!</h1>';
		$content .= '<h3>Was möchtest du tun?</h3>';
		if ($statusMessage != '') $content .= '<p class="message">'.$statusMessage.'</p>';
		$content .= '<ul id="tabs1">
			<li><a href="#startTab">Startseite</a></li>
			<li><a href="#manageProfile">Mein Profil verwalten</a></li>
			<li><a href="#allVacations">Alle Freizeiten ansehen</a></li>
			<li><a href="#myVacations">Meine Freizeiten ansehen</a></li>
			<li><a href="#myLogout">Mich wieder abmelden</a></li>
		</ul>';
		// which tab should be selected ?

		
		$content .= '<div id="startTab" class="ui-tabs-hide">'.$this->startTab().'</div><div id="manageProfile" class="ui-tabs-hide">'.$this->manageProfileForm($realUser).'</div>
		<div id="allVacations" class="ui-tabs-hide">'.$this->showVacations($realUser['uid']).'</div>
		<div id="myVacations" class="ui-tabs-hide">'.$this->showMyVacations($realUser['uid'], $feUser).'</div>';
		$content_conf = array(
		    'tables' => 'tt_content',
		    'source' => 1,
		    'dontCheckPid' => 1,
		);
		$logoutContent .= $this->cObj->RECORDS($content_conf);
		$content .= '<div class="ui-tabs-hide" id="myLogout">'.$logoutContent.'</div>';
		
		// mail div for canceling confirmed vacations
		$content .= $this->showMailFormDiv();
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * startTab function.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function startTab() {
		$out = '';
		// next vacation widget
		$out .= $this->widgetNextVacation();
		
		// start infos
		$startContentRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_rtvacationcare_start',
			'1=1'.$this->cObj->enableFields('tx_rtvacationcare_start'),'','crdate DESC' );
		while ($startContent = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($startContentRes) ) {
			$out .= '<div class="startcontentbox">';
			$out .= '<h3>'.$startContent['header'].'</h3>';
			$out .= '<div class="startcontenttext">'.$this->pi_RTEcssText($startContent['text']).'</div>';
			$out .= '</div>';
		}
		$out .= '<div class="clearer zero">&nbsp;</div>';
		return $out;
	}
	
	/*
		generates the form for changing tt_address data
		
		expects: real-user data (== tt_address data)
		returns: complete and prefilled form
	*/
	
	protected function manageProfileForm($theUser) {
		$cont = '';
		# echo t3lib_div::debug($theUser);	
		# show edit form with prefilled data from tt_adress-data
		$cont .= '<h2>Mein Profil:</h2>';
		$cont .= '<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'#manageProfile" method="post" enctype="multipart/form-data" name="saveMember" id="saveMember">
		<fieldset>
		<legend>Deine Daten</legend>
		<input type="hidden" name="'.$this->prefixId.'[saveIt]" value="1" />
		<input type="hidden" name="'.$this->prefixId.'[userId]" value="'.$theUser['uid'].'" />
			  <label for="last_name">Name:</label>

			  <input name="'.$this->prefixId.'[last_name]" type="text" id="name" value="'.$theUser['last_name'].'" size="30" />
			  <span class="red">*</span><br />
			 <label for="first_name">Vorname:</label>
			  <input name="'.$this->prefixId.'[first_name]" type="text" id="vorname" value="'.$theUser['first_name'].'" size="30" />
			  <span class="red">*</span><br />
			  <label for="birthday">Geburtsdatum:</label>
			  <input name="'.$this->prefixId.'[birthday]" type="text" id="geb" value="'.date('d.m.Y', $theUser['birthday']).'" size="30" />

			  <span class="red">*</span><br />
			  <label for="address">Strasse:</label>
			  <input name="'.$this->prefixId.'[address]" type="text" id="strasse" value="'.$theUser['address'].'" size="30" />
			  <span class="red">*</span><br />
			 <label for="zip">PLZ:</label>
			  <input name="'.$this->prefixId.'[zip]" type="text" id="plz" value="'.$theUser['zip'].'" size="8" maxlength="5" />
			  <span class="red">*</span><br />

			  <label for="city">Ort:</label>
			  <input name="'.$this->prefixId.'[city]" type="text" id="ort" value="'.$theUser['city'].'" size="30" />
			  <span class="red">*</span><br />
			    <label for="phone">Telefon:</label>
			  <input name="'.$this->prefixId.'[phone]" type="text" id="tel" value="'.$theUser['phone'].'" size="30" /><br />
			    <label for="mobile">Handy:</label>
			  <input name="'.$this->prefixId.'[mobile]" type="text" id="mobil" value="'.$theUser['mobile'].'" size="30" /><br />

			  <label for="email">E-Mail:</label>
			  <input name="'.$this->prefixId.'[email]" type="text" id="email" value="'.$theUser['email'].'" size="30" /><br /><br />
			  <label for"picture">neues Foto:</label>
			  <input name="'.$this->prefixId.'[picture]" type="file" /><br /><br />
				</fieldset>
				<fieldset>
				<legend>Passwort:</legend>
				<label for="pwnew1">Neues Passwort:</label>
			  <input name="'.$this->prefixId.'[pwnew1]" type="password" id="email" value="" size="30" /><br /><br />
			  <label for="pwnew2">Neues Passwort (Wiederholung):</label>
			  <input name="'.$this->prefixId.'[pwnew2]" type="password" id="email" value="" size="30" /><br /><br />
			  <label for="pwold">altes Passwort:</label>
			  <input name="'.$this->prefixId.'[pwold]" type="password" id="email" value="" size="30" /><br /><br />
				</fieldset>
			  <input name="'.$this->prefixId.'[save]" type="submit" id="save" value="Profil speichern" title="Speichert die Daten in der Datenbank" />
			</form>
			<div style="clear: both;">&nbsp</div>';
		return $cont;
	}
	

	/**
	 * checkStatus function.
	 *  checks the status (has wish/ is confirmed) for the tt_address user
	 * @access protected
	 * @param mixed $cId -> caretaker ID (tt_address), $feu_uid = fe_users.uid and vacation ID
	 * @param mixed $vId
	 * @param mixed $onlyStat
	 * @return void -> integer (status number) or link for canceling, etc.
	 */
	protected function checkStatus($cId, $feu_uid, $vId, $onlyStat) {
		$out = '';
		// 1 = no wish, not confirmed
		// 2 = wished, but not yet confirmed
		// 3 = wished and confirmed
		// 4 = no wish, but still confirmed
		// 5 = confirmed and leader
		$status = 1;
		
		$vacation = $this->pi_getRecord('tx_rtvacationcare_vacations',$vId);
		
		// the wish -mm table need fe_user uid...
		$checkWishRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) as counter',
			'tx_rtvacationcare_vacations_caretakerwish_mm',
			'uid_foreign = '.$feu_uid.' AND uid_local = '.$vId);
		$checkWish = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($checkWishRes);

		if ($checkWish['counter'] >= 1) {
			$status = 2;
		}

		// check, if caretaker is already confirmed, warning: tt_address uid is saved in mm-table !
		$confRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								'COUNT(*) as anzahl', #SELECT
								'tx_rtvacationcare_vacations_caretaker_mm', # table
								'uid_foreign = "'.$cId.'" AND uid_local = '.$vId);
		$confirmation = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($confRes);
		$confirmation = $confirmation['anzahl'];
		
		if ($confirmation == 1) {
			// caretaker is confirmed for this vacation
			if ($status == 2) {
				// wish == true
				$status = 3;
			} else {
				// confirmed, but no wish
				$status = 4;
			}
		}
		if ($onlyStat == 1) {
			// only return status
			$out = $status;
		} else {
			// also return link
			$conf = array(
			  'parameter' => $GLOBALS['TSFE']->id.'#allVacations',
			  // Set additional parameters
			  'additionalParams' => '&tx_rtvacationcare_pi2[vacwish]='.$vId,
			  // We must add cHash because we use parameters
			  'useCacheHash' => true,
			  // We want link only
			  'returnLast' => 'url',
			);
			// check chief
			$chiefRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid_foreign as uid',
				'tx_rtvacationcare_vacations_caretakerchief_mm',
				'uid_local = '.$vId);
			if ($chiefRes) {
				$chief = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($chiefRes);
				$chiefText = '';
				if ($chief['uid'] == $cId) {
					$chiefText = ' (L)';
				}
			}
			switch ($status) {
				case 1:
					$makeWishUrl = $this->cObj->typoLink('', $conf);
					$out .='<a href="'.$makeWishUrl.'">Anmelden</a>';
					break;
				case 2:
					$conf['additionalParams'] = '&tx_rtvacationcare_pi2[cancelwish]='.$vId;
					$cancelWishUrl = $this->cObj->typoLink('', $conf);
					$out .='<a href="'.$cancelWishUrl.'" title="'.$this->pi_getLL('cancelWishLinkText').'">'.$this->pi_getLL('cancelWish').'</a>';
					break;
				case 3:
					$cancelWishUrl = '#mailformdiv';
					$out .='<a href="'.$cancelWishUrl.'" title="'.$this->pi_getLL('wishConfirmedLinkText').'" rel="facebox"  onclick="javascript:changeForm(\''.htmlspecialchars($vacation['title']).' stornieren\', '.$vId.');">'.$this->pi_getLL('wishConfirmed').$chiefText.'</a>';
					break;
				case 4:
					$cancelWishUrl = '#mailformdiv';
					$out .='<a href="'.$cancelWishUrl.'" title="'.$this->pi_getLL('justConfirmedLinkText').'" rel="facebox" onclick="javascript:changeForm(\''.htmlspecialchars($vacation['title']).' stornieren\', '.$vId.');">'.$this->pi_getLL('justConfirmed').$chiefText.'</a>';
					break;
				default:
			}			
		}
		#$out .= ' stat: '.$status;
		return $out;
	}
	
	/*
		expects: a fe-user ID
		returns: an array with the tt_address data
	*/
	
	protected function getTTaddress($userId) {
		$realUserData = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'tt_address.*', #SELECT
			'fe_users', # local table
			'fe_users_user_feloginaddress_tt_address_mm', # mm table
			'tt_address', #foreign
			' AND fe_users_user_feloginaddress_tt_address_mm.uid_local = "'.$userId.'" ');

		$theUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($realUserData);
		return $theUser;
	}
	
	 /*
	 	expects: tt_address.uid
	 	returns: nothing. just saves the data from the form in tt_address, and uploads a picture
	 */
	 
	protected function saveData($userId) {
		$this->uploadDir = 'uploads/pics/';
	//	echo t3lib_div::debug($_FILES['tx_rtvacationcare_pi2'],'Formdata');
		
		$this->fileFunc=t3lib_div::makeInstance('t3lib_basicFileFunctions'); 
		$newPic = $_FILES['tx_rtvacationcare_pi2'];
		$cleanFileName = $this->fileFunc->cleanFileName($newPic['name']['picture']);
		$destFile = $this->fileFunc->getUniqueName($cleanFileName, $this->uploadDir);
		$destFileNoPath = pathinfo($destFile);
		$destFileNoPath = $destFileNoPath['basename'];
		
	
	//	echo t3lib_div::debug($destFile,'Filename w/path');
	//	echo t3lib_div::debug($newPic,'Filename');
		
		t3lib_div::upload_copy_move($_FILES['tx_rtvacationcare_pi2']['tmp_name']['picture'],$destFile);
	
	// check, if password should be updates
	$pw1 = htmlspecialchars($this->piVars['pwnew1']);
	$pw2 = htmlspecialchars($this->piVars['pwnew2']);
	$pw3 = htmlspecialchars($this->piVars['pwold']);
	$pwCounter = 0;
	if ($pw1 != '') {
		$pwCounter++;
	}
	if ($pw2 != '') {
		$pwCounter++;
	}
	if ($pw3 != '') {
		$pwCounter++;
	}
	if ($pwCounter > 0 && $pwCounter < 3 ) {
		$pwMessage = 'Um dein Passwort zu ändern, musst du sowohl dein Altes als auch zweimal das neue Passwort eingeben.';
	} else if ($pwCounter == 3) {
		// change password
		
		// is old password correct ?
		if ($GLOBALS['TSFE']->fe_user->user['password'] == md5($pw3) ) {
			// is new password entered 2 times correctly ?
			if ($pw2 == $pw1) {
				$pwMessage = 'Passwort erfolgreich geändert !';
				// save new password
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					'fe_users',
					'uid='.$GLOBALS['TSFE']->fe_user->user['uid'],
					array('password' => md5($pw1) ));  	
			} else {
				$pwMessage = 'Bitte gib dein neues Passwort zweimal in der gleichen Schreibweise ein.';
			}
			
		} else {
			$pwMessage = 'Das alte Passwort wurde nicht richtig eingegeben.';
		}
		
	}
	
		$updateFields = array(
			'last_name'=>htmlspecialchars($this->piVars['last_name']),
			'first_name'=>htmlspecialchars($this->piVars['first_name']),
			'birthday'=>strtotime(htmlspecialchars($this->piVars['birthday'])),
			'address'=>htmlspecialchars($this->piVars['address']),
			'zip'=>htmlspecialchars($this->piVars['zip']),
			'city'=>htmlspecialchars($this->piVars['city']),
			'phone'=>htmlspecialchars($this->piVars['phone']),
			'mobile'=>htmlspecialchars($this->piVars['mobile']),
			'email'=>htmlspecialchars($this->piVars['email']),
		);
		
		if ($newPic['error']['picture'] == 0) {
			// change image-field in tt_address
			$updateFields['image'] = $destFileNoPath;
		}		
		$update = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tt_address',
			'uid = '.$userId,
			$updateFields);
		return $pwMessage;
	}
	
	/*
		shows all vacations of active year
		returns: table with data and links
	*/
	
	protected function showVacations($userId) {
		$out = '';
		$activeYear = $this->activeYear;
		// $userId = tt_address id

		// get all vacations from active year
		$vacationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_rtvacationcare_vacations',
			'FROM_UNIXTIME(startdate, "%Y" ) = '.$activeYear,
			'',
			'title');
		$out .= '<h2>Alle '.$this->pi_getLL('titlePlural').' in '.$activeYear.'</h2>';
		$out .= '<table style="padding:4px; margin: 6px;">';
		// table header
		$out .= '<tr class="tableHeader"><td>'.$this->pi_getLL('nr').'</td><td>'.$this->pi_getLL('title').'</td><td>'.$this->pi_getLL('date').'</td><td>'.$this->pi_getLL('confirmed').'</td><td>'.$this->pi_getLL('openCaretakers').'</td><td>'.$this->pi_getLL('status').'</td></tr>';
		$counter = 0;

		while($allVacations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($vacationRes)) {
			# confirmed ?
			$listIcon = array();
			$listIcon = $this->conf['vacListIcon.'];
			$listIcon['file'] = $this->path.'res/betreuer/pfeil-rot.gif';
			
			if ($allVacations['approved'] == 0) {
				$booked = $this->cObj->IMAGE($listIcon);
			} else {
				$listIcon['file'] = $this->path.'res/betreuer/pfeil-gruen.gif';
				 $booked = $this->cObj->IMAGE($listIcon);
			}
			if($counter%2 == 0 ? $rowClass='even' : $rowClass = 'odd'); 
			$out .= '<tr class="'.$rowClass.'">';
			$out .= '<td style="text-align: right;">'.$allVacations['nr'].'</td><td>';
			
			// vacation title
			$out .= '<h3 class="vacationtitle">'.$allVacations['title'].'</h3>';
			
			// vacation description
			$out .= '<div class="vacationdescription">'.$allVacations['description'].'</div>';
			
			// vacation date
			$out .= '</td><td>'.date('d.m.Y H:i', $allVacations['startdate']).'</td><td style="text-align: center;">'.$booked.'</td>';
			
			// open caretaker
			$out .= '<td>';
			if ($allVacations['maxcaretaker'] > 0) {
				$planedCaretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'COUNT(*) as planed',
					'tx_rtvacationcare_vacations_caretaker_mm',
					'uid_local = '.$allVacations['uid']);
				$planedCaretaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($planedCaretakerRes);
				$planedCaretakers = $allVacations['maxcaretaker']-$planedCaretaker['planed'];
				if ($planedCaretakers <= 0) $planedCaretakers = 0;
				$out .= $planedCaretakers;
			}
			$out .= '</td>';
			
			// link to make wish
			$fe_userId = $GLOBALS['TSFE']->fe_user->user['uid'];
			$out .= '<td>'.$this->checkStatus($userId, $fe_userId, $allVacations['uid'], 0).'</td>';
			$out .= '</tr>';
			$counter ++;
		}

		$out.="</table>";
		return $out;
	}

	
	/*
		generates a list of vacations, for which the user has send a wish, or is selected
		expects: userId == tt_addres
		returns: table with data and links
	*/
	
	protected function showMyVacations($userId, $feUser) {
		$activeYear = $this->activeYear;
		$out = '<h2>Meine Freizeiten in '.$this->activeYear.'</h2>';
		// $userId = tt_address id

		// get all vacations from active year for this user
		$vacationRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
								'tx_rtvacationcare_vacations.*', #SELECT
								'tx_rtvacationcare_vacations', # local table
								'tx_rtvacationcare_vacations_caretaker_mm', # mm table
								'tt_address', #foreign
								' AND uid_foreign = "'.$userId.'" AND FROM_UNIXTIME(startdate, "%Y" ) = '.$activeYear,
								'',# group by
								'tx_rtvacationcare_vacations.startdate');
		
		$out .= '<table style="padding:4px; margin: 6px;">';
		// table header
		$out .= '<tr class="tableHeader"><td>'.$this->pi_getLL('nr').'</td><td>'.$this->pi_getLL('title').'</td><td>'.$this->pi_getLL('date').'</td><td>'.$this->pi_getLL('status').'</td><td>'.$this->pi_getLL('download').'</td></tr>';
		$counter = 0;

		while($allVacations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($vacationRes)) {
			# confirmed ?
/*
			$listIcon = array();
			$listIcon = $this->conf['vacListIcon.'];
			$listIcon['file'] = $this->path.'res/betreuer/pfeil-rot.gif';
			
			if ($allVacations['approved'] == 0) {
				$booked = $this->cObj->IMAGE($listIcon);
			} else {
				$listIcon['file'] = $this->path.'res/betreuer/pfeil-gruen.gif';
				 $booked = $this->cObj->IMAGE($listIcon);
			}
*/
			if($counter%2 == 0 ? $rowClass='even' : $rowClass = 'odd'); 
			$out .= '<tr class="'.$rowClass.'">';
			$out .= '<td style="text-align: right;">'.$allVacations['nr'].'</td><td>';
			
			// vacation title
			$out .= '<h3 class="vacationtitle">'.$allVacations['title'].'</h3>';
			
			// vacation description
			$out .= '<div class="vacationdescription">'.$allVacations['description'].'</div>';
			
			// vacation date
			#$out .= '</td><td>'.date('d.m.Y H:i', $allVacations['startdate']).'</td><td style="text-align: center;">'.$booked.'</td>';
			$out .= '</td><td>'.date('d.m.Y H:i', $allVacations['startdate']).'</td>';
			
			// link to make wish
			$fe_userId = $GLOBALS['TSFE']->fe_user->user['uid'];
			$out .= '<td>'.$this->checkStatus($userId, $fe_userId, $allVacations['uid'], 0).'</td>';
			
			// download confirmation
			$conf = array(
			  'parameter' => $GLOBALS['TSFE']->id.'#myVacations',
			  // Set additional parameters
			  'additionalParams' => '&tx_rtvacationcare_pi2[makePdf]='.$allVacations['uid'],
			  // We must add cHash because we use parameters
			  'useCacheHash' => true,
			  // We want link only
			  'returnLast' => 'url',
			);

			$makePDFurl = $this->cObj->typoLink('', $conf);
			$out .= '<td><a href="'.$makePDFurl.'">Herunterladen</a></td>';
			$out .= '</tr>';
			$counter ++;
		}

		$out.="</table>";
		return $out;
	}
	
	protected function showMailFormDiv() {
		$out = '<script type="text/javascript">
function changeForm (vTitle, vacId) {
	document.getElementById("cancelHeader").innerHTML = vTitle;
	document.getElementById("hiddenVacationId").value = vacId;
}

</script>
';
		$form = '<form action="'.$this->pi_getPageLink($GLOBALS['TSFE']->id).'#allVacations" method="post" enctype="multipart/form-data" name="cancelVacation" id="cancelVacation">
		<h2 id="cancelHeader">Test</h2>
		<input type="hidden" id="hiddenVacationId" name="'.$this->prefixId.'[cvId]" value="" />';
		$form .= '<label for="'.$this->prefixId.'[reason]">Begründung</label><textarea id="reason" name="'.$this->prefixId.'[reason]" length="30" rows="8"></textarea>';
		$form .= '<input id="sendReason" type="submit" value="Stornowunsch an Christian schicken" />';
		$form .= '</form>';
		$out .= '<div id="mailformdiv" style="display:none;">'.$form.'</div>';
		return $out;
	}
	
	protected function widgetNextVacation() {
		$out .= '<div class="startcontentbox widget">';
		$out .= '<h3>'.$this->pi_getLL('widget.nextvacation.header').'</h3>';
		$today = time();
		// get tt_address uid for fe_user
		$feuId = $GLOBALS['TSFE']->fe_user->user['uid'];
		$ttaddressRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid_foreign',
			'fe_users_user_feloginaddress_tt_address_mm',
			'uid_local = '.$feuId);
		$ttAddress = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($ttaddressRes);
		$ttAddress = $ttAddress['uid_foreign'];

		$nextVacRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'tx_rtvacationcare_vacations.*', #SELECT
			'tx_rtvacationcare_vacations', # local table
			'tx_rtvacationcare_vacations_caretaker_mm', # mm table
			'tt_address', #foreign
			'AND uid_foreign = "'.$ttAddress.'" AND tx_rtvacationcare_vacations.startdate > '.$today,
			'',# group by
			'tx_rtvacationcare_vacations.startdate ASC',
		1);
		
		$nextVac = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($nextVacRes);
		$conf = array(
		  // Link to current page
		  'parameter' => $GLOBALS['TSFE']->id.'#myVacations',
		  // fake additional param for selecting different tab	
		  'additionalParams' => '&move[tab]=1',
		  // We must add cHash because we use parameters
		  'useCacheHash' => true,
		  // We want link only
		  'returnLast' => 'url',
		);
		$url = $this->cObj->typoLink('', $conf);
		$out .= '<p><a href="'.$url.'">'.$nextVac['title'].'<br />'.date('d.m.Y', $nextVac['startdate']).' bis '.date('d.m.Y', $nextVac['enddate']).'</a></p>';
		$out .= '</div>';
		return $out;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/pi2/class.tx_rtvacationcare_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rt_vacationcare/pi2/class.tx_rtvacationcare_pi2.php']);
}

?>