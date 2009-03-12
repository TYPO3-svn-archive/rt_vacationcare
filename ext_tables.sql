#
# Table structure for table 'tx_rtvacationcare_vacations_caretaker_mm'
# 
#
CREATE TABLE tx_rtvacationcare_vacations_caretaker_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_rtvacationcare_vacations_caretakerchief_mm'
# 
#
CREATE TABLE tx_rtvacationcare_vacations_caretakerchief_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);





#
# Table structure for table 'tx_rtvacationcare_vacations_lodging_mm'
# 
#
CREATE TABLE tx_rtvacationcare_vacations_lodging_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_rtvacationcare_vacations_caretakerwish_mm'
# 
#
CREATE TABLE tx_rtvacationcare_vacations_caretakerwish_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_rtvacationcare_vacations'
#
CREATE TABLE tx_rtvacationcare_vacations (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	nr int(2) DEFAULT '0' NOT NULL,
	booked int(11) DEFAULT '0' NOT NULL,
	approved int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	startdate int(11) DEFAULT '0' NOT NULL,
	enddate int(11) DEFAULT '0' NOT NULL,
	luggage text NOT NULL,
	pocketmoney int(11) DEFAULT '0' NOT NULL,
	snack tinyint(3) DEFAULT '0' NOT NULL,
	price int(11) DEFAULT '0' NOT NULL,
	meetingpoint text NOT NULL,
	image tinytext,
	info text NOT NULL,
	info2 text NOT NULL,
	info3 text NOT NULL,
	maxattendees int(11) DEFAULT '0' NOT NULL,
	caretaker int(11) DEFAULT '0' NOT NULL,
	lodging int(11) DEFAULT '0' NOT NULL,
	caretakerwish int(11) DEFAULT '0' NOT NULL,
	caretakerchief int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);




#
# Table structure for table 'tx_rtvacationcare_lodgings_country_mm'
# 
#
CREATE TABLE tx_rtvacationcare_lodgings_country_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_rtvacationcare_lodgings'
#
CREATE TABLE tx_rtvacationcare_lodgings (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	address tinytext NOT NULL,
	zip int(11) DEFAULT '0' NOT NULL,
	city tinytext NOT NULL,
	country int(11) DEFAULT '0' NOT NULL,
	phone tinytext NOT NULL,
	fax tinytext NOT NULL,
	email tinytext NOT NULL,
	website tinytext NOT NULL,
	contact tinytext NOT NULL,
	max int(11) DEFAULT '0' NOT NULL,
	location text NOT NULL,
	image blob NOT NULL,
	notes text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_rtvacationcare_homes_contact_mm'
# 
#
CREATE TABLE tx_rtvacationcare_homes_contact_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_rtvacationcare_homes'
#
CREATE TABLE tx_rtvacationcare_homes (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,
    address tinytext NOT NULL,
    zip tinytext NOT NULL,
    city tinytext NOT NULL,
    phone tinytext NOT NULL,
    fax tinytext NOT NULL,
    contact int(11) DEFAULT '0' NOT NULL,
    email tinytext NOT NULL,
    notes text NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_rtvacationcare_regist_attendeeid_mm'
# 
#
CREATE TABLE tx_rtvacationcare_regist_attendeeid_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_rtvacationcare_regist_vacationid_mm'
# 
#
CREATE TABLE tx_rtvacationcare_regist_vacationid_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_rtvacationcare_regist'
#
CREATE TABLE tx_rtvacationcare_regist (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    attendeeid int(11) DEFAULT '0' NOT NULL,
    vacationid int(11) DEFAULT '0' NOT NULL,
    paid tinyint(3) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_rtvacationcare_start'
#
CREATE TABLE tx_rtvacationcare_start (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
    header tinytext,
    text text,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);