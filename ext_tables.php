<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{
		
	// add module after 'File'
	if (!isset($TBE_MODULES['txrtvacationcareM1']))	{
		$temp_TBE_MODULES = array();
		foreach($TBE_MODULES as $key => $val) {
			if ($key=='file') {
				$temp_TBE_MODULES[$key] = $val;
				$temp_TBE_MODULES['txrtvacationcareM1'] = $val;
			} else {
				$temp_TBE_MODULES[$key] = $val;
			}
		}
		$TBE_MODULES = $temp_TBE_MODULES;
		unset($temp_TBE_MODULES);
	}
	
	t3lib_extMgm::addModule('txrtvacationcareM1','','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
		
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM8','',t3lib_extMgm::extPath($_EXTKEY).'mod8/');
	
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM2','',t3lib_extMgm::extPath($_EXTKEY).'mod2/');
		
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM3','',t3lib_extMgm::extPath($_EXTKEY).'mod3/');
		
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM4','',t3lib_extMgm::extPath($_EXTKEY).'mod4/');
		
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM5','',t3lib_extMgm::extPath($_EXTKEY).'mod5/');
		
	t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM6','',t3lib_extMgm::extPath($_EXTKEY).'mod6/');
	
	// t3lib_extMgm::addModule('txrtvacationcareM1','txrtvacationcareM7','',t3lib_extMgm::extPath($_EXTKEY).'mod7/');
		
}


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:rt_vacationcare/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Show vacations");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_rtvacationcare_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_rtvacationcare_pi1_wizicon.php';

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';


t3lib_extMgm::addPlugin(array(
    'LLL:EXT:rt_vacationcare/locallang_db.xml:tt_content.list_type_pi2',
    $_EXTKEY . '_pi2',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Caretaker Management");

if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_rtvacationcare_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_rtvacationcare_pi2_wizicon.php';
}


$TCA["tx_rtvacationcare_vacations"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations',		
		'label'     => 'nr',
		'label_alt' => 'title',
		'label_alt_force' => 'true',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY startdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rtvacationcare_vacations.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, nr, booked, approved, title, description, startdate, enddate, luggage, pocketmoney, snack, price, meetingpoint, info, info2, info3, maxcaretaker, caretaker, caretakerwish, lodging",
	)
);

$TCA["tx_rtvacationcare_lodgings"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY title",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rtvacationcare_lodgings.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, address, zip, city, country, phone, fax, email, website, contact, max, location, image, notes",
	)
);

$TCA["tx_rtvacationcare_homes"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes',        
        'label'     => 'title',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY title",    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rtvacationcare_homes.gif',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "hidden, title, address, zip, city, phone, fax, contact, email, notes",
    )
);

$TCA["tx_rtvacationcare_regist"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_regist',        
        'label'     => 'vacationid', 
        'label_alt' => 'attendeeid',
		'label_alt_force' => 'true',   
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate",    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rtvacationcare_regist.gif',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "hidden, attendeeid, vacationid, paid",
    )
);

$TCA['tx_rtvacationcare_start'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_start',        
        'label'     => 'header',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',    
            'starttime' => 'starttime',    
            'endtime' => 'endtime',    
            'fe_group' => 'fe_group',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rtvacationcare_start.gif',
    ),
);
?>