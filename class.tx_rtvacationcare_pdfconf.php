<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Class for formatting query-data in various formats
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/
require_once(PATH_tslib.'class.tslib_pibase.php');
if (t3lib_extMgm::isLoaded('dam')) {
	require_once(t3lib_extMgm::extPath('dam') . 'lib/class.tx_dam_media.php');
}
if (t3lib_extMgm::isLoaded('fpdf')) {
  require(t3lib_extMgm::extPath('fpdf').'class.tx_fpdf.php');
}
/*
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(PATH_t3lib.'class.t3lib_cs.php');
*/

/*
if (t3lib_extMgm::isLoaded('fpdf'))	{
	require(t3lib_extMgm::extPath('fpdf').'class.tx_fpdf.php');
}
*/

	// Give the script max. 10 minutes to run (has no effect if PHP is run in safe_mode)
set_time_limit(600);

/**
 * Class for formatting query-data in various formats
 *
 */
class tx_rtvacationcare_pdfconf   {
		/**
	 * This function returns data formatted as PDF (Portable Data Format). The formatted data is returned as a string,
	 * which can for instance be used to download a PDF file. See EXT:partner/inc/class.tx_partner_download_report.php
	 * for an example.
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	array		$params['structure']: The structure in which the data must be formatted. One structure for 'screen'-display and one for 'file'-output.
	 * @param	array		$params['formatOptions']: The options for this format
	 * @param	string		$params['allowedFormats']: Which formats are currently allowed (comma-separated string)
	 * @param	integer		$params['reportUid']: UID of the report calling this function
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */

	function formatAsPDF($vacation) {
		$fileTitle = str_replace(' ', '_', utf8_decode($vacation['title'])).'.pdf';
			// Check if the FPDF library is loaded (extension 'FDPF'). If not, return immediately.
		if (!t3lib_extMgm::isLoaded('fpdf')) return;

			// Get a new instance of the FPDF library
		// content of pdf
		$salutation = utf8_decode('Grüß Gott,');
		$theDate = 'Murnau, im '.$this->getMonth(date('n', time())).' '.date('Y', time());
		
		$header1 = utf8_decode('hiermit bestätigen wir die Teilnahme an der Freizeit:');
		
		$pdf=new myPDF('portrait', 'mm', 'A4');
		
		
		$pdf->payText1 = utf8_decode('Bitte überweisen Sie die Teilnehmergebühr von '.$vacation['price'].' EUR auf folgendes Konto');
		$pdf->payText2 = utf8_decode('Kunterbunt e.V. VR | Bank Murnau | Kontonummer 1860 925 | Bankleitzahl 703 900 00');
		// go
		$pdf->tx_fpdf->template = PATH_site.'fileadmin/templates/pdf/template.pdf';
		$pdf->AliasNbPages();
		$pdf->SetAuthor( 'Kunterbunt e.V.' );
		$pdf->SetTitle(utf8_decode('Bestätigung für Freizeit '.$vacation['title']));
		$pdf->SetTopMargin(40);
		
		// ---------------------------------------------------------------
		// first page
		//
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);

	    $pdf->Cell(30,10,$salutation);
		$pdf->Cell(80);
	    $pdf->Cell(0,10,$theDate,0,'','R');
	    
	    $pdf->Ln();
	    
	    $pdf->Cell(100,10,$header1);
	    $pdf->SetTextColor(255,102,0);
	    $pdf->SetFont('','I');
	    $pdf->Cell(0,10,utf8_decode($vacation['title']));
	    
	    $pdf->Ln();
	    
	    // ort
	    $pdf->SetTextColor(0,0,0);
	    $pdf->SetFont('','');
	    $placeRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
	    						'tx_rtvacationcare_lodgings.*', #SELECT
	    						'tx_rtvacationcare_vacations', # local table
	    						'tx_rtvacationcare_vacations_lodging_mm', # mm table
	    						'tx_rtvacationcare_lodgings', #foreign
	    						' AND uid_local = "'.$vacation['uid'].'" ');
	    $place = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($placeRes);
#echo t3lib_div::debug($place,'');
	    $pdf->Cell(40,10,'Ort:');
	    $pdf->Cell(0,10,utf8_decode($place['title']));
	    $pdf->Ln(5);
	    $pdf->Cell(40,10);
	    $pdf->Cell(0,10,utf8_decode($place['address']).', '.utf8_decode($place['zip']).' '.utf8_decode($place['city']));
	    $pdf->Ln(5);
	    $pdf->Cell(40,10);
	    $pdf->Cell(0,10,$place['phone'].' ');
	    
	    // mobile phone
	    $pdf->Ln();
	    $pdf->Cell(40,10, 'Mobiltelefon:');

	    $pdf->Cell(0,10, $this->getMobilePhone($vacation['uid']));
	    
	    // starttime
	    $pdf->Ln(10);
	    $pdf->Cell(40,10, 'Beginn:');
	    $startDay = $this->getDay(date('w', $vacation['startdate']));
	    $startDate = $startDay.', '.date('d.m.Y', $vacation['startdate']).' um '.date('H:i', $vacation['startdate']).'Uhr';
	    $pdf->Cell(0,10, $startDate);
	    
   	    // endtime
	    $pdf->Ln(10);
	    $pdf->Cell(40,10, 'Ende:');
	    $startDate = $this->getDay(date('w', $vacation['enddate'])).', '.date('d.m.Y', $vacation['enddate']).' um '.date('H:i', $vacation['enddate']).'Uhr';
	    $pdf->Cell(0,10, $startDate);
	    
	    // meeting point
	    $pdf->Ln(10);
	    $pdf->Cell(40,5, 'Treffpunkt:');
	    $pdf->MultiCell(0,5, utf8_decode($vacation['meetingpoint']));
	    
	    // luggage
	    $pdf->Ln(5);
	    $pdf->Cell(40,5, utf8_decode('Gepäck:'));
	    $pdf->MultiCell(0,5, utf8_decode($vacation['luggage']));	    
	    
	    // Infotext, pocketmoney
	    $pdf->Ln(5);
	    $pdf->Cell(40,5, utf8_decode('Bitte geben Sie dem Verantwortlichen der Freizeit:'));
		$pdf->Ln(5);
		$pdf->Cell(10,5);
		$pdf->Cell(40,5, utf8_decode('- Das Medikamentenkästchen mit den genauen Angaben für jeden Tag (bitte schon vorstellen)'));
	    $pdf->Ln(5);
		$pdf->Cell(10,5);
		$pdf->Cell(40,5, utf8_decode('- Taschengeld (ca. '.$vacation['pocketmoney'].' EUR in einem eigenen, beschrifteten Geldbeutel)'));
		
		// snack
		if ($vacation['snack'] == 1) {
			$pdf->Ln(8);
			$pdf->SetFont('Arial','B');
		    $pdf->Cell(0,5,utf8_decode('Bitte bringt für '.$startDay.' eine Brotzeit mit'),0,0,'C');
		    $pdf->SetFont('');
		}
		$pdf->Ln(10);
		
		// image
		$img = Array();
		#$img = $this->conf['imagePdf.'];
		$damPics = tx_dam_db::getReferencedFiles('tx_rtvacationcare_vacations', $vacation['uid'],'vacation_image', 'tx_dam_mm_ref');
		list($uidDam, $filePath) = each($damPics['files']);	
		$mediaClass = tx_div::makeInstanceClassName('tx_dam_media');
		$media = new $mediaClass($filePath);
		# Check DAM-Version
	 	if(method_exists($media, 'fetchFullMetaData')) {
	 		$media->fetchFullMetaData();
	 	} else {
	 		$media->fetchFullIndex();
	 	}
	    if($media->meta['uid'] > 0 ) {
	    	$vacationImage = $img['file'] = PATH_site.$media->meta['file_path'].$media->meta['file_name'];
		    $posY = $pdf->GetY();
		    $posX = 30;
		    $pdf->Image($vacationImage,$posX,$posY,150,0);	    	
	    }    

	    
	    // -------------------------------------------------------------------
	    // people page
	    //
	    $pdf->AddPage();
	    
	    // graphical header
	    $posY = $pdf->GetY();
	    $posX = 55;
	    $pdf->Image(PATH_site.'typo3conf/ext/rt_vacationcare/pi2/res/images/wer-kommt-mit-print.png',$posX,$posY,100,0); 
	    
	    // list / table of attendees 
		// get all already for this vacation registered attendees
		// first get registrations for this vacation
		$registrationRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid_local as reg_uid', #SELECT
			'tx_rtvacationcare_regist_vacationid_mm',
			'uid_foreign = "'.$vacation['uid'].'" ');
		
		// all images
		$allImages = array();
		$registeredAttendees = array();
		// then get attendee to this registration
		while ($registrations = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($registrationRes) ) {
			$attendeeRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'tt_address.*', #SELECT
			'tx_rtvacationcare_regist',
			'tx_rtvacationcare_regist_attendeeid_mm',
			'tt_address',
			'AND tx_rtvacationcare_regist_attendeeid_mm.uid_local = "'.$registrations['reg_uid'].'" ', '', 'tt_address.last_name');
			$attendee = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attendeeRes);
			$attendees[]= array('last_name' => utf8_decode($attendee['last_name']), 'first_name' => utf8_decode($attendee['first_name']), 'address' => utf8_decode($attendee['address']), 'zip' => utf8_decode($attendee['zip']).' '.utf8_decode($attendee['city']), 'phone' => $attendee['phone'], 'birthday' => $attendee['birthday']);
			$allImages[]  = array('image' => utf8_decode($attendee['image']), 'name' => utf8_decode($attendee['first_name']));
		}
		
		// table attendees
		$tableHeader = array('Name:', 'Vorname:', 'Strasse:', 'PLZ, Ort:', 'Telefon:', 'Geburtstag:');
		$pdf->peopleTable($tableHeader, $attendees, $vacation);
		
		// graphical header 2
		$pdf->Ln();
	    $posY = $pdf->GetY();
	    $posX = 55;
	    $pdf->Image(PATH_site.'typo3conf/ext/rt_vacationcare/pi2/res/images/wir-kommen-auch-print.png',$posX,$posY,100,0); 
		
		// table caretaker
		$caretakerRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
								'tt_address.*', #SELECT
								'tx_rtvacationcare_vacations', # local table
								'tx_rtvacationcare_vacations_caretaker_mm', # mm table
								'tt_address', #foreign
								' AND uid_local = "'.$vacation['uid'].'" ',
								'',# group by
								'tt_address.last_name',
							300);
		$caretakers = array();
		while ($caretaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($caretakerRes) ) {
			$caretakers[]= array('last_name' => utf8_decode($caretaker['last_name']), 'first_name' => utf8_decode($caretaker['first_name']), 'address' => utf8_decode($caretaker['address']), 'zip' => utf8_decode($caretaker['zip']).' '.utf8_decode($caretaker['city']), 'phone' => $caretaker['phone'], 'birthday' => $caretaker['birthday'], 'uid' => $caretaker['uid']);
			$allImages[] = array('image' => utf8_decode($caretaker['image']), 'name' => utf8_decode($caretaker['first_name']));
		}
		
		$pdf->peopleTable($tableHeader, $caretakers, $vacation);
		
		// --------------------------------------------------------------------------
	    // pictures
		//
		$pdf->AddPage();
		$imagePath = 'uploads/pics/';
		$count = 1;
		$posX = 10;
		$posY = $pdf->GetY();
		for ($i = 0; $i <= count($allImages); $i++) {
			if ($allImages[$i]['image'] != '') {	
		    	$imageSize = getimagesize(PATH_site.$imagePath.utf8_encode($allImages[$i]['image']));
				if ($imageSize[0] > $imageSize[1]) {
					// quer
					$pdf->Image(PATH_site.$imagePath.utf8_encode($allImages[$i]['image']),$posX,$posY,0,30);
					$format = $imageSize[0]/30;
					$newWidth = $imageSize[1]/$format+10;
					$posX += $newWidth+20;
				} else {
					// hoch
					$pdf->Image(PATH_site.$imagePath.utf8_encode($allImages[$i]['image']),$posX,$posY,20,0);
					$posX += 30;
				}
				
				$pdf->Text($posX-25, $posY+35, $allImages[$i]['name']);
		    	
		    	if ($posX >= 180) {
		    		$posX = 10;
		    		$posY+= 50;
		    	}
		    	#$pdf->Ln();
		    	$count++;
			}
		}
		
		// --------------------------------------------------------------------------
		// info text
 		//
 		if ($vacation['info']) {
 			$pdf->AddPage();
 			$pdf->MultiCell(0,5, utf8_decode($vacation['info']));
 		}
 		
 		
 		
		// Convert to PDF
		$content = $pdf->Output($fileTitle,'D');
		return $out;
	}
	
	/**
	 * getDay function.
	 * 
	 * @access public
	 * @param mixed $d
	 * @return void
	 */
	function getDay($d) {
		$days = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
		$out = $days[$d];
		return $out;
	}
	
	/**
	 * getMonth function.
	 * 
	 * @access public
	 * @param mixed $m
	 * @return void
	 */
	function getMonth($m) {
		$out = '';
		$months = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
		$out = utf8_decode($months[$m]);
		return $out;
	}
	
	/**
	 * getMobilePhone function.
	 * 
	 * @access public
	 * @param mixed $vId
	 * @return void
	 */
	function getMobilePhone($vId) {
		$out = '';
		// get chief via mm
		$chiefRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid_foreign as uid',
			'tx_rtvacationcare_vacations_caretakerchief_mm',
			'uid_local = '.$vId);

			$chief = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($chiefRes);
			$chief = $chief['uid'];

			if ((int)$chief > 0) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'mobile',
					'tt_address',
					'uid = '.$chief);
				$theChief = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$out .= $theChief['mobile'];			
			}

		return $out;
	}


}

/**
 * myPDF class.
 * 
 * @extends PDF
 */
class myPDF extends PDF { 
    // Überlagerung der Footer() Methode
    function Footer() {
            // Über dem unteren Seitenrand positionieren 
            $this->SetY(-20); 
            // Schriftart festlegen
            $this->SetFont('Arial','',10);
            $this->SetTextColor(255,102,0);
            // Zentrierte Ausgabe der Seitenzahl
            // $this->Cell(0,10, $this->payText1. 'Seite '.$this->PageNo(),0,0,'C'); 
            $this->Cell(0,10, $this->payText1,0,0,'C');     
            $this->SetY(-15);
            $this->SetFont('Arial','U');
            $this->Cell(0,10, $this->payText2,0,0,'C'); 
    }
    
	function peopleTable($header, $data, $vacation) {
		$this->Ln();
		$this->SetFontSize('9');
		$this->SetTextColor(0,200,0);
		//Header
    	foreach($header as $col) {
	        $this->Cell(32,7,$col);
    	}
    	$this->Ln();
    	$this->SetDrawColor(0, 200, 0); // green
    	$this->SetLineWidth(0.5);
		// Linie zeichnen
		$posY = $this->GetY();
		$this->Line(10, $posY, 201, $posY); 
		// table content
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.1);
		
		$candleImage = PATH_site.'typo3conf/ext/rt_vacationcare/pi2/res/images/kerze.jpg';
		
		// get chief
		$chiefRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid_foreign as uid',
			'tx_rtvacationcare_vacations_caretakerchief_mm',
			'uid_local = '.$vacation['uid']);
		if ($chiefRes) {
			$chief = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($chiefRes);
			$chief = $chief['uid'];
		}
		if (is_array($data)) {
			foreach ($data as $at) {
				$this->SetTextColor(0,0,0);
				$this->Cell(32,6,$at['last_name']);
				$firstName = $at['first_name'];
				if ($chief == $at['uid']) {
					$firstName .= ' (Leitung)';
				}
				$this->Cell(32,6,$firstName);
				$this->Cell(32,6,$at['address']);
				$this->Cell(32,6,$at['zip'].' '.$at['city']);
				$this->Cell(32,6,$at['phone']);
				// birthday...
				$birthday = date('d.m', $at['birthday']);
				$geb = explode(".",$birthday);  // Das Datum des Geburtstages wird aufgeteilt
	
				$duration = ($vacation['enddate']-$vacation['startdate'])/86400;
				for ($i = 0; $i <= $duration; $i++) {
					$today = date('d.m',mktime(0,0,0,date('m', $vacation['startdate']),date('d', $vacation['startdate'])+$i, date('Y', $vacation['startdate'])));
					if ($birthday == $today) {
						$this->Cell(32,6,date('d.m.Y', $at['birthday']));
						$posY = $this->GetY();
		    			$posX = $this->GetX();
		    			$this->Image($candleImage,$posX,$posY,5,0);
					}
				}
				
				$this->Ln();
				$posY = $this->GetY();
				$this->Line(10, $posY, 201, $posY);		
			}		
		}

	
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_format.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_format.php']);
}

?>