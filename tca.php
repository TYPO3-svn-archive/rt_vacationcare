<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_rtvacationcare_vacations"] = array (
	"ctrl" => $TCA["tx_rtvacationcare_vacations"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,nr,booked,approved,title,description,startdate,enddate,luggage,pocketmoney,snack,price,meetingpoint,image,info,info2,info3,maxcaretaker,caretaker,caretakerchief,caretakerwish,lodging"
	),
	"feInterface" => $TCA["tx_rtvacationcare_vacations"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_rtvacationcare_vacations',
				'foreign_table_where' => 'AND tx_rtvacationcare_vacations.pid=19 AND tx_rtvacationcare_vacations.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"nr" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.nr",		
			"config" => Array (
				"type"     => "input",
				"size"     => "3",
				"max"      => "3",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"upper" => "200",
					"lower" => "1"
				),
				"default" => 0
			)
		),
		"booked" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.booked",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"approved" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.approved",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "50",
				"rows" => "6",
			)
		),
		"startdate" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.startdate",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"enddate" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.enddate",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"luggage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.luggage",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"pocketmoney" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.pocketmoney",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"snack" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.snack",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"price" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.price",		
			"config" => Array (
				"type"     => "input",
				"size"     => "6",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"meetingpoint" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.meetingpoint",		
			"config" => Array (
				"type" => "text",
				"cols" => "50",
				"rows" => "6",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		'image' => txdam_getMediaTCA('image_field', 'vacation_image'),
		"info" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.info",		
			"config" => Array (
				"type" => "text",
				"cols" => "70",	
				"rows" => "15",
			)
		),
		"info2" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.info2",		
			"config" => Array (
				"type" => "text",
				"cols" => "60",	
				"rows" => "10",
			)
		),
		"info3" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.info3",		
			"config" => Array (
				"type" => "text",
				"cols" => "60",	
				"rows" => "10",
			)
		),
		"maxcaretaker" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.maxcaretaker",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"range"    => Array (
					"upper" => "1000",
					"lower" => "1"
				),
				"default" => 0
			)
		),
		"caretaker" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.caretaker",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tt_address",	
				"foreign_table_where" => "ORDER BY tt_address.last_name",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 50,	
				"MM" => "tx_rtvacationcare_vacations_caretaker_mm",	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Neuen Betreuer anlegen",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tt_address",
							"pid" => "20",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "Alle Betreuer auflisten",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tt_address",
							"pid" => "20",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Betreuer bearbeiten",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"caretakerwish" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.caretakerwish",
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "fe_users",	
				"foreign_table_where" => "ORDER BY fe_users.name",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 50,	
				"MM" => "tx_rtvacationcare_vacations_caretakerwish_mm",
			)
		),
		"caretakerchief" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.caretakerchief",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tt_address",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,	
				"MM" => "tx_rtvacationcare_vacations_caretakerchief_mm",
			)
		),
		"lodging" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_vacations.lodging",		
			"config" => Array (
				"type" => "select",
				'items' => array (
					array('', 0),
				),
				"foreign_table" => "tx_rtvacationcare_lodgings",	
				"foreign_table_where" => "ORDER BY tx_rtvacationcare_lodgings.title",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,	
				"MM" => "tx_rtvacationcare_vacations_lodging_mm",	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Neue Unterkunft erstellen",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_rtvacationcare_lodgings",
							"pid" => "23",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Unterkunft bearbeiten",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1, nr,booked, approved, title;;;;2-2-2, description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_rtvacationcare/rte/];3-3-3, startdate, enddate, luggage, pocketmoney, snack, price, meetingpoint, image, info, info2, info3, maxcaretaker, caretaker, caretakerchief, caretakerwish, lodging")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

// edit caretaker options
$TCA["tx_rtvacationcare_vacations"]["columns"]["caretaker"]["config"]["wizards"]["add"]["title"] = "Neuen Betreuer anlegen";
$TCA["tx_rtvacationcare_vacations"]["columns"]["caretaker"]["config"]["wizards"]["edit"]["title"] = "Betreuer bearbeiten";
$TCA["tx_rtvacationcare_vacations"]["columns"]["caretaker"]["config"]["foreign_table_where"] = "AND tt_address.pid = '20' ORDER BY tt_address.last_name";
$TCA["tx_rtvacationcare_vacations"]["columns"]["caretaker"]["config"]["wizards"]["add"]["params"]["pid"] = "20";
// edit attendees options

// edit lodgings options
$TCA["tx_rtvacationcare_vacations"]["columns"]["lodging"]["config"]["wizards"]["add"]["title"] = "Neue Unterkunft anlegen";
$TCA["tx_rtvacationcare_vacations"]["columns"]["lodging"]["config"]["wizards"]["edit"]["title"] = "Unterkunft bearbeiten";
$TCA["tx_rtvacationcare_vacations"]["columns"]["lodging"]["config"]["wizards"]["add"]["params"]["pid"] = "23";


$TCA["tx_rtvacationcare_lodgings"] = array (
	"ctrl" => $TCA["tx_rtvacationcare_lodgings"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,title,address,zip,city,country,phone,fax,email,website,contact,max,location,image,notes"
	),
	"feInterface" => $TCA["tx_rtvacationcare_lodgings"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_rtvacationcare_lodgings',
				'foreign_table_where' => 'AND tx_rtvacationcare_lodgings.pid=23 AND tx_rtvacationcare_lodgings.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"eval" => "required",
			)
		),
		"address" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.address",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"zip" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.zip",		
			"config" => Array (
				"type"     => "input",
				"size"     => "5",
				"max"      => "5",
				"eval"     => "int",
				"range"    => Array (
					"upper" => "99999",
					"lower" => "10000"
				),
				"default" => 0
			)
		),
		"city" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.city",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"country" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.country",		
			"config" => Array (
				"type" => "select",    
                "foreign_table" => "static_countries",    
                "foreign_table_where" => "ORDER BY static_countries.uid",    
                "size" => 1,    
                "minitems" => 0,
                "maxitems" => 1,    
                "MM" => "tx_rtvacationcare_lodgings_country_mm",
			)
		),
		"phone" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.phone",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"eval" => "trim",
			)
		),
		"fax" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.fax",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				"eval" => "trim",
			)
		),
		"email" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.email",		
			"config" => Array (
				"type" => "input",	
				"size" => "50",
				"eval" => "trim",
			)
		),
		"website" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.website",		
			"config" => Array (
				"type"     => "input",
				"size"     => "15",
				"max"      => "255",
				"checkbox" => "",
				"eval"     => "trim",
				"wizards"  => array(
					"_PADDING" => 2,
					"link"     => array(
						"type"         => "popup",
						"title"        => "Link eintragen",
						"icon"         => "link_popup.gif",
						"script"       => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"contact" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.contact",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"max" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.max",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"range"    => Array (
					"upper" => "1000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"location" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.location",		
			"config" => Array (
				"type" => "text",
				"cols" => "50",	
				"rows" => "6",
			)
		),
		"image" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.image",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_rtvacationcare",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"notes" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_lodgings.notes",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "6",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, title;;;;2-2-2, address;;;;3-3-3, zip, city, country, phone, fax, email, website, contact, max, location, image, notes")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);


$TCA["tx_rtvacationcare_homes"] = array (
    "ctrl" => $TCA["tx_rtvacationcare_homes"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "title,address,zip,city,phone,fax,contact,email,notes"
    ),
    "feInterface" => $TCA["tx_rtvacationcare_homes"]["feInterface"],
    "columns" => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        "title" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.title",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "address" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.address",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "zip" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.zip",        
            "config" => Array (
                "type" => "input",    
                "size" => "5",    
                "eval" => "nospace,trim",
            )
        ),
        "city" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.city",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
                "eval" => "trim",
            )
        ),
        "phone" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.phone",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
                "eval" => "trim",
            )
        ),
        "fax" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.fax",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
                "eval" => "trim",
            )
        ),
        "contact" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.contact",		
			"config" => Array (
				"type" => "select",
				"items" => Array(
					 Array("",0),
				),
				"foreign_table" => "tt_address",	
				"foreign_table_where" => "AND tt_address.pid=22 ORDER BY tt_address.last_name",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,	
				"MM" => "tx_rtvacationcare_homes_contact_mm",	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 0,
					
					"add" => Array(
						"type" => "script",
						"title" => "Neuen Ansprechpartner anlegen",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tt_address",
							"pid" => "22",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Ansprechpartner bearbeiten",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
        "email" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.email",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "notes" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_homes.notes",        
            "config" => Array (
                "type" => "text",
                "cols" => "50",    
                "rows" => "5",
            )
        ),
    ),
    "types" => array (
        "0" => array("showitem" => "title;;;;2-2-2, address;;;;3-3-3, zip, city, phone, fax, contact, email, notes")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);

$TCA["tx_rtvacationcare_regist"] = array (
    "ctrl" => $TCA["tx_rtvacationcare_regist"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "hidden,attendeeid,vacationid,paid"
    ),
    "feInterface" => $TCA["tx_rtvacationcare_regist"]["feInterface"],
    "columns" => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        "attendeeid" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_regist.attendeeid",        
            "config" => Array (
                "type" => "select",    
                "foreign_table" => "tt_address",    
                "foreign_table_where" => "AND tt_address.pid = 21 ORDER BY tt_address.last_name",    
                "size" => 10,    
                "minitems" => 0,
                "maxitems" => 1,    
                "MM" => "tx_rtvacationcare_regist_attendeeid_mm",
                "wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Neuen Teilnehmer erstellen",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tt_address",
							"pid" => "21",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"list" => Array(
						"type" => "script",
						"title" => "Alle Teilnehmer auflisten",
						"icon" => "list.gif",
						"params" => Array(
							"table"=>"tt_address",
							"pid" => "21",
						),
						"script" => "wizard_list.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Teilnehmer bearbeiten",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
            )
        ),
        "vacationid" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_regist.vacationid",        
            "config" => Array (
                "type" => "select",    
                "foreign_table" => "tx_rtvacationcare_vacations",    
                "foreign_table_where" => "ORDER BY tx_rtvacationcare_vacations.nr",     
                "size" => 10,    
                "minitems" => 0,
                "maxitems" => 1,    
                "MM" => "tx_rtvacationcare_regist_vacationid_mm",
                "wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"edit" => Array(
						"type" => "popup",
						"title" => "Freizeit bearbeiten",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
            )
        ),
        "paid" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_regist.paid",        
            "config" => Array (
                "type" => "check",
            )
        ),
    ),
    "types" => array (
        "0" => array("showitem" => "attendeeid, vacationid, paid")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);

$TCA['tx_rtvacationcare_start'] = array (
    'ctrl' => $TCA['tx_rtvacationcare_start']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,header,text'
    ),
    'feInterface' => $TCA['tx_rtvacationcare_start']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config'  => array (
                'type'     => 'input',
                'size'     => '8',
                'max'      => '20',
                'eval'     => 'date',
                'default'  => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config'  => array (
                'type'     => 'input',
                'size'     => '8',
                'max'      => '20',
                'eval'     => 'date',
                'checkbox' => '0',
                'default'  => '0',
                'range'    => array (
                    'upper' => mktime(3, 14, 7, 1, 19, 2038),
                    'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
                )
            )
        ),
        'fe_group' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
            'config'  => array (
                'type'  => 'select',
                'items' => array (
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'header' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_start.header',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',
            )
        ),
        'text' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_start.text',        
            'config' => array (
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, header, text;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_rtvacationcare/rte/]')
    ),
    'palettes' => array (
        '1' => array('showitem' => 'starttime, endtime, fe_group')
    )
);

/*
"vacationid" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:rt_vacationcare/locallang_db.xml:tx_rtvacationcare_regist.vacationid",        
            "config" => Array (
                "type" => "group",    
                "internal_type" => "db",    
                "allowed" => "tx_rtvacationcare_vacations",    
                "size" => 10,    
                "minitems" => 0,
                "maxitems" => 1,    
                "MM" => "tx_rtvacationcare_regist_vacationid_mm",
            )
        ),
*/

?>