<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
if (!isset($dbengine)) $dbengine = "";
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "USR_USER (";
$requete .= " ID_USER int(11) NOT NULL auto_increment , ";
$requete .= " USR_USERNAME varchar(20) NOT NULL default '', ";
$requete .= " USR_NICKNAME varchar(20) NOT NULL default '', ";
$requete .= " USR_NAME varchar(50) NOT NULL default '', ";
$requete .= " USR_LEVEL TINYINT NOT NULL default '0', ";
$requete .= " ID_ROLE tinyint UNSIGNED NULL default NULL, ";
$requete .= " USR_STATUS TINYINT NOT NULL default '0', ";
$requete .= " USR_PASSWORD varchar(40) NOT NULL default '', ";
$requete .= " USR_PASS_WEB varchar(40) NOT NULL default '', ";
$requete .= " USR_PASS_SALT char(20) NOT NULL default '', ";
$requete .= " USR_CHECK varchar(15) NOT NULL default '', ";
$requete .= " USR_DATE_CREAT date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " USR_DATE_LAST date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " USR_DATE_PASSWORD date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " USR_DATE_ACTIVITY date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " USR_DATE_BIRTH date NOT NULL default '1999-12-31', ";  // default '0000-00-00'
$requete .= " USR_VERSION varchar(6) NOT NULL default '',";
$requete .= " USR_COUNTRY_CODE char(2) NOT NULL default '',";
$requete .= " USR_LANGUAGE_CODE char(2) NOT NULL default '',";
$requete .= " USR_IP_ADDRESS varchar(39) NOT NULL default '',";
$requete .= " USR_AVATAR varchar(20) NOT NULL default '',";
$requete .= " USR_TIME_SHIFT SMALLINT NOT NULL default '0',";
$requete .= " USR_OS VARCHAR(5) NOT NULL default '',";
$requete .= " USR_EMAIL VARCHAR(80) NOT NULL default '',";
$requete .= " USR_PHONE VARCHAR(20) NOT NULL default '',";
$requete .= " USR_PWD_ERRORS SMALLINT NOT NULL default '0',";
$requete .= " USR_GENDER CHAR(1) NOT NULL default '',";
$requete .= " USR_NB_CONNECT SMALLINT NOT NULL default '0',";
$requete .= " USR_GET_ADMIN_ALERT TINYINT NOT NULL default '0',";
$requete .= " USR_GET_OFFLINE_MSG TINYINT NOT NULL default '2',";
$requete .= " USR_DATE_BACKUP date NOT NULL default '1999-12-31', "; //  default '0000-00-00'
$requete .= " USR_MAC_ADR CHAR(12) NOT NULL default '',";
$requete .= " USR_COMPUTERNAME VARCHAR(20) NOT NULL default '',";
$requete .= " USR_SCREEN_SIZE VARCHAR(10) NOT NULL default '',";
$requete .= " USR_EMAIL_CLIENT VARCHAR(40) NOT NULL default '',";
$requete .= " USR_BROWSER VARCHAR(40) NOT NULL default '',";
$requete .= " USR_OOO VARCHAR(5) NOT NULL default '',";
$requete .= " USR_RATING TINYINT NOT NULL default '0',";
$requete .= " USR_ONLINE  TINYINT NOT NULL default '0',";
$requete .= " USR_TIME_LOCK TIME NOT NULL default '00:00:00', ";
$requete .= " USR_REG VARCHAR(30) NOT NULL default '', ";
$requete .= " PRIMARY KEY (ID_USER),";
$requete .= " UNIQUE KEY USR_USERNAME (USR_USERNAME),";
$requete .= " KEY USR_NICKNAME (USR_NICKNAME),";
$requete .= " KEY USR_NAME (USR_NAME),";
$requete .= " KEY USR_LEVEL (USR_LEVEL),";
$requete .= " KEY USR_DATE_CREAT (USR_DATE_CREAT),";
$requete .= " KEY USR_DATE_LAST (USR_DATE_LAST),";
$requete .= " KEY USR_GET_ADMIN_ALERT (USR_GET_ADMIN_ALERT)";
//$requete .= ") COMMENT='Users';
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-4]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "CNT_CONTACT (";
$requete .= " ID_CONTACT int(11) NOT NULL auto_increment,";
$requete .= " ID_USER_1 int(11) NOT NULL default '0',";
$requete .= " ID_USER_2 int(11) NOT NULL default '0',";
$requete .= " CNT_STATUS TINYINT NOT NULL default '0',";
$requete .= " CNT_NEW_USERNAME varchar(20) NOT NULL default '',";
$requete .= " CNT_USER_GROUP varchar(20) NOT NULL default '',";
$requete .= " CNT_RATING TINYINT NOT NULL default '0',";
$requete .= " CNT_SOUND TINYINT UNSIGNED NOT NULL default '0',";
$requete .= " PRIMARY KEY  (ID_CONTACT),";
$requete .= " KEY ID_USER_1 (ID_USER_1),";
$requete .= " KEY ID_USER_2 (ID_USER_2)";
//$requete .= ") COMMENT='Users contact list'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-1]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "MSG_MESSAGE (";
$requete .= " ID_MESSAGE int(11) NOT NULL auto_increment,";
$requete .= " ID_USER_AUT int(11) NOT NULL default '0',";
$requete .= " ID_USER_DEST int(11) NOT NULL default '0',";
//$requete .= " MSG_TEXT varchar(500) NOT NULL default '',";
//$requete .= " MSG_TEXT text NOT NULL default '',";
$requete .= " MSG_TEXT text NOT NULL,";
$requete .= " MSG_CR char(2) NOT NULL default '',";
$requete .= " MSG_ETAT TINYINT NOT NULL default '0',";
$requete .= " MSG_TIME time NOT NULL default '00:00:00',";
$requete .= " MSG_DATE date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " ID_CONFERENCE INT NOT NULL default '0',";
$requete .= " PRIMARY KEY  (ID_MESSAGE),";
$requete .= " KEY ID_USER_AUT (ID_USER_AUT),";
$requete .= " KEY ID_USER_DEST (ID_USER_DEST),";
$requete .= " KEY ID_CONFERENCE (ID_CONFERENCE)";
//$requete .= ") COMMENT='Messages';";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-2]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "SES_SESSION (";
$requete .= " ID_SESSION int(11) NOT NULL auto_increment,";
$requete .= " ID_USER int(11) NOT NULL default '0',";
$requete .= " SES_STATUS TINYINT NOT NULL default '0',";
$requete .= " SES_STARTDATE date NOT NULL default '1999-12-31', "; //  default '0000-00-00'
$requete .= " SES_STARTTIME time NOT NULL default '00:00:00', ";
$requete .= " SES_LASTTIME time NOT NULL default '00:00:00', ";
$requete .= " SES_IP_ADDRESS varchar(23) NOT NULL default '',";
$requete .= " SES_AWAY_REASON TINYINT NOT NULL default '0',";
$requete .= " PRIMARY KEY  (ID_SESSION),";
$requete .= " UNIQUE KEY ID_USER (ID_USER),";
$requete .= " KEY SES_STATUS (SES_STATUS)";
//$requete .= ") COMMENT='Online users';";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-3]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "GRP_GROUP ( ";
$requete .= " ID_GROUP INT NOT NULL AUTO_INCREMENT, ";
$requete .= " GRP_NAME VARCHAR( 20 ) NOT NULL , ";
$requete .= " GRP_PRIVATE TINYINT NOT NULL DEFAULT '0', ";
$requete .= " GRP_SHOUTBOX TINYINT NOT NULL DEFAULT '0', ";
$requete .= " GRP_SBX_NEED_APPROVAL TINYINT NOT NULL DEFAULT '0', ";
$requete .= " PRIMARY KEY  (ID_GROUP),";
$requete .= " UNIQUE (GRP_NAME) ";
//$requete .= " ) COMMENT = 'Group list'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-5]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "USG_USERGRP ( ";
$requete .= " ID_GROUP INT NOT NULL , ";
$requete .= " ID_USER INT NOT NULL , ";
$requete .= " USG_PENDING TINYINT NOT NULL DEFAULT '0' , ";
$requete .= " INDEX ( ID_GROUP , ID_USER ) ";
//$requete .= " ) COMMENT = 'Users in groups'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-6]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "STA_STATS ( ";
$requete .= " STA_DATE DATE NOT NULL , ";
$requete .= " STA_NB_MSG MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_NB_CREAT SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_NB_SESSION SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_NB_USR MEDIUMINT NOT NULL default '0', ";
$requete .= " STA_SBX_NB_MSG MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_SF_NB_SHARE MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_SF_NB_EXCHANGE MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " STA_SF_NB_DOWNLOAD MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " PRIMARY KEY ( STA_DATE ) ";
//$requete .= ") COMMENT = 'Statistics'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-7]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ( ";
$requete .= " ID_CONFERENCE INT NOT NULL AUTO_INCREMENT, ";
$requete .= " ID_USER INT NOT NULL , ";
$requete .= " CNF_DATE_CREAT date NOT NULL default '1999-12-31', "; //  default '0000-00-00'
$requete .= " CNF_TIME_CREAT time NOT NULL default '00:00:00', ";
$requete .= " PRIMARY KEY  (ID_CONFERENCE), ";
$requete .= " KEY ID_USER (ID_USER) ";
//$requete .= " ) COMMENT = 'Conferences'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-8]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "USC_USERCONF ( ";
$requete .= " ID_CONFERENCE INT NOT NULL , ";
$requete .= " ID_USER INT NOT NULL , ";
$requete .= " USC_ACTIVE TINYINT NOT NULL DEFAULT '0', ";
$requete .= " INDEX ( ID_CONFERENCE , ID_USER ) ";
//$requete .= " ) COMMENT = 'Users in conferences'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-9]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "BAN_BANNED ( ";
$requete .= " BAN_TYPE char(1) NOT NULL, ";
$requete .= " BAN_VALUE varchar(50) default '' NOT NULL, ";
$requete .= " INDEX (BAN_TYPE, BAN_VALUE) ";
//$requete .= " ) COMMENT = 'Banned users/computers/IP'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-10]", $requete);
//
//
$requete  = " select count(*) from " . $PREFIX_IM_TABLE . "BAN_BANNED ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-10a]", $requete);
if ( mysqli_num_rows($result) != 0 )
{
  list ($nb_row) = mysqli_fetch_row ($result);
  if ($nb_row < 5)
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "BAN_BANNED (BAN_TYPE, BAN_VALUE) VALUES ('U', 'admin%'), ('U', 'anonym%'), ";
    $requete .= " ('U', 'azerty%'), ('U', 'banned'), ('U', 'delete'), ('U', 'fuck%'), ('U', 'guest'), ('U', 'inconnu%'), ";
    $requete .= " ('U', 'insert'), ('U', 'invite'), ('U', 'merde'), ('U', 'moderat%'), ('U', 'owner'), ('U', 'password'), ";
    $requete .= " ('U', 'porno'), ('U', 'putain'), ('U', 'putin'), ('U', 'qwerty%'), ('U', 'raped'), ('U', 'raper'), "; 
    $requete .= " ('U', 'salope'), ('U', 'server'), ('U', 'serveur'), ('U', 'sexe'), ('U', 'truncate'), ('U', 'unknown%'), ";
    $requete .= " ('U', 'update'), ('U', 'username'), ('U', 'viagra'), ('U', 'webmaster'), ('U', 'webmestre'), ('U', '1234%'); ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-install_table-10b]", $requete);
  }
}
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ( ";
$requete .= " ID_SERVER SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, ";
$requete .= " SRV_NAME varchar(60) NOT NULL, ";
$requete .= " SRV_IP_ADDRESS varchar(23) NOT NULL default '', ";
$requete .= " SRV_STATE TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " SRV_STATE_DATE date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " SRV_STATE_TIME time NOT NULL default '00:00:00', ";
$requete .= " SRV_STATE_COMMENT varchar(150) default '' NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_SERVER), ";
$requete .= " UNIQUE KEY SRV_NAME (SRV_NAME) ";
//$requete .= " ) COMMENT = 'Servers states'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-11]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ( ";
$requete .= " ID_SHOUT MEDIUMINT UNSIGNED NOT NULL auto_increment, ";
$requete .= " ID_GROUP_DEST SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, ";
$requete .= " SBX_TEXT varchar(250) NOT NULL, ";
$requete .= " SBX_TIME time NOT NULL, ";
$requete .= " SBX_DATE date NOT NULL, ";
$requete .= " SBX_DISPLAY TINYINT NOT NULL default '1', ";
$requete .= " SBX_RATING TINYINT NOT NULL default '0', ";
//$requete .= " SBX_NB_VOTE_M SMALLINT UNSIGNED NOT NULL default '0' "; // COMMENT 'More', 
//$requete .= " SBX_NB_VOTE_L SMALLINT UNSIGNED NOT NULL default '0' "; // COMMENT 'Less', 
$requete .= " PRIMARY KEY  (ID_SHOUT), ";
$requete .= " KEY SBX_IND_1 (SBX_DATE, SBX_TIME), ";
$requete .= " KEY SBX_IND_2 (ID_SHOUT, ID_GROUP_DEST) ";
//$requete .= " ) COMMENT = 'ShoutBox'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-12]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS ( ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, ";
$requete .= " SBS_NB SMALLINT UNSIGNED NOT NULL default '1', ";
$requete .= " SBS_NB_REJECT TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " SBS_NB_VOTE_M SMALLINT UNSIGNED NOT NULL default '0', "; // COMMENT 'More', 
$requete .= " SBS_NB_VOTE_L SMALLINT UNSIGNED NOT NULL default '0', "; // COMMENT 'Less', 
$requete .= " SBS_MAX_VOTE_M SMALLINT UNSIGNED NOT NULL default '0', "; // COMMENT 'Score More',
$requete .= " SBS_MAX_VOTE_L SMALLINT UNSIGNED NOT NULL default '0', "; // COMMENT 'Score Less',
$requete .= " SBS_NB_LAST_DATE SMALLINT UNSIGNED NOT NULL default '1', "; //  COMMENT 'Day quota',
$requete .= " SBS_LAST_DATE date NOT NULL, ";
$requete .= " SBS_NB_LAST_WEEK SMALLINT UNSIGNED NOT NULL default '1', "; // COMMENT 'Week quota', 
$requete .= " SBS_LAST_WEEK TINYINT UNSIGNED NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_USER_AUT) ";
//$requete .= " ) COMMENT = 'Stats shoutbox'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-13]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ( ";
$requete .= " ID_SHOUT MEDIUMINT UNSIGNED NOT NULL, ";
$requete .= " ID_USER_VOTE MEDIUMINT NOT NULL, ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, "; //  COMMENT 'For stats'
$requete .= " SBV_DATE date NOT NULL, ";
$requete .= " SBV_VOTE_M TINYINT NOT NULL, "; // COMMENT 'More', 
$requete .= " SBV_VOTE_L TINYINT NOT NULL, "; // COMMENT 'Less', 
$requete .= " PRIMARY KEY  (ID_SHOUT, ID_USER_VOTE), ";
$requete .= " KEY SBV_IND_1 (ID_USER_AUT) ";
//$requete .= " ) COMMENT = 'Votes shoutbox'; ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-14]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG ( ";
$requete .= " ID_BOOKMCATEG TINYINT UNSIGNED NOT NULL AUTO_INCREMENT, ";
$requete .= " BMC_TITLE varchar(80) NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_BOOKMCATEG) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-15]", $requete);
//
$requete  = " select count(*) from " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-15b]", $requete);
if ( mysqli_num_rows($result) != 0 )
{
  list ($nb_row) = mysqli_fetch_row ($result);
  if ($nb_row < 3)
  {
    $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG (BMC_TITLE) ";
    $requete .= " VALUES ('News'), ('Sport'), ('Entertainment'), ('Finance'), ('Other'); ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-install_table-15c]", $requete);
  }
}
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "BMK_BOOKMARK ( ";
$requete .= " ID_BOOKMARK SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, ";
$requete .= " ID_BOOKMCATEG TINYINT UNSIGNED NULL DEFAULT NULL, ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, ";
$requete .= " BMK_URL varchar(250) NOT NULL, ";
$requete .= " BMK_TITLE varchar(100) NOT NULL, ";
$requete .= " BMK_DATE date NOT NULL, ";
$requete .= " BMK_DISPLAY TINYINT NOT NULL default '1', ";
$requete .= " BMK_RATING TINYINT NOT NULL default '0', ";
$requete .= " PRIMARY KEY  (ID_BOOKMARK), ";
$requete .= " UNIQUE KEY BMK_URL (BMK_URL), ";
$requete .= " UNIQUE KEY BMK_TITLE (BMK_TITLE) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-16]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE ( ";
$requete .= " ID_BOOKMARK SMALLINT UNSIGNED NOT NULL, ";
$requete .= " ID_USER_VOTE MEDIUMINT NOT NULL, ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, "; //  COMMENT 'For stats'
$requete .= " BMV_DATE date NOT NULL, ";
$requete .= " BMV_VOTE_M TINYINT NOT NULL, "; //  COMMENT 'More'
$requete .= " BMV_VOTE_L TINYINT NOT NULL, "; // COMMENT 'Less'
$requete .= " PRIMARY KEY  (ID_BOOKMARK, ID_USER_VOTE), ";
$requete .= " KEY BMV_IND_1 (ID_USER_AUT) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-17]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "ROL_ROLE ( ";
$requete .= " ID_ROLE TINYINT UNSIGNED NOT NULL AUTO_INCREMENT, ";
$requete .= " ROL_NAME varchar(40) NOT NULL, ";
$requete .= " ROL_DEFAULT char(1) NOT NULL, ";       // Role 'OTHERS'
$requete .= " PRIMARY KEY  (ID_ROLE) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-18]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "MDL_MODULE ( ";
$requete .= " ID_MODULE TINYINT UNSIGNED NOT NULL, ";
$requete .= " MDL_NAME varchar(60) NOT NULL, ";
$requete .= " MDL_MAX_VALUE INT UNSIGNED NOT NULL default 0, ";  // valeur numérique
$requete .= " MDL_OTHER CHAR(1) NOT NULL default '', ";                // Pour le role 'OTHERS' uniquement (car nécessite d'activer une option globale).
$requete .= " MDL_ROLE CHAR(1) NOT NULL default '', ";                 // Un role, pas une option (donc pas le 'i' bleu).
$requete .= " PRIMARY KEY  (ID_MODULE) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-19]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE ( ";
$requete .= " ID_ROLE TINYINT UNSIGNED NOT NULL, ";
$requete .= " ID_MODULE TINYINT UNSIGNED NOT NULL, ";
$requete .= " RLM_STATE TINYINT UNSIGNED NOT NULL DEFAULT 1, ";   // 1: refusé / 2: activé / 3: numérique
$requete .= " RLM_VALUE SMALLINT UNSIGNED NOT NULL, ";            // valeur numérique
$requete .= " PRIMARY KEY (ID_ROLE, ID_MODULE), ";
$requete .= " CONSTRAINT im_rlm_rolemodule_fk1 FOREIGN KEY im_rlm_rolemodule_fk1 (ID_ROLE) ";
$requete .= "   REFERENCES " . $PREFIX_IM_TABLE . "ROL_ROLE (ID_ROLE) ";
$requete .= "   ON DELETE NO ACTION   ON UPDATE NO ACTION, ";
$requete .= " CONSTRAINT im_rlm_rolemodule_fk2 FOREIGN KEY im_rlm_rolemodule_fk2 (ID_MODULE) ";
$requete .= "   REFERENCES " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE) ";
$requete .= "   ON DELETE NO ACTION   ON UPDATE NO ACTION ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-20]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA ( ";
$requete .= " ID_FILEMEDIA TINYINT UNSIGNED NOT NULL auto_increment , ";
$requete .= " FMD_NAME VARCHAR(30) NOT NULL, ";
$requete .= " FMD_EXTENSIONS VARCHAR(200) NOT NULL, ";
$requete .= "  PRIMARY KEY  (ID_FILEMEDIA) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-21]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ( ";
$requete .= " ID_PROJET SMALLINT UNSIGNED NOT NULL auto_increment , ";
$requete .= " FPJ_NAME VARCHAR(50) NOT NULL, ";
$requete .= " FPJ_FOLDER VARCHAR(20) NOT NULL, ";
$requete .= " FPJ_DATE_CREAT date NOT NULL, ";
$requete .= " FPJ_DATE_END date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " FPJ_DATE_CLOSE date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " PRIMARY KEY  (ID_PROJET), ";
$requete .= " KEY FPJ_DATE_CLOSE (FPJ_DATE_CLOSE) ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-22]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FIL_FILE ( ";
$requete .= " ID_FILE MEDIUMINT UNSIGNED NOT NULL auto_increment, ";
$requete .= " FIL_NAME VARCHAR(200) NOT NULL, ";
//$requete .= " FIL_REVISION SMALLINT UNSIGNED NOT NULL, ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, ";
$requete .= " ID_USER_DEST MEDIUMINT NULL, ";
$requete .= " ID_GROUP_DEST SMALLINT UNSIGNED NULL, ";
$requete .= " ID_USER_LAST_DL MEDIUMINT NULL, ";
$requete .= " ID_FILEMEDIA TINYINT UNSIGNED NOT NULL, ";
$requete .= " ID_PROJET SMALLINT UNSIGNED NULL, ";
$requete .= " FIL_ONLINE CHAR(1) NOT NULL, "; //  "" (vide) : avant FTP  |  "W" : attente approbation |  "Y" : disponible  | "A" : archivé (suite révision)  | "D" : delete
$requete .= " FIL_DATE date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " FIL_DATE_ADD date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " FIL_DATE_TRASH date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " FIL_SIZE INT UNSIGNED NULL, ";
$requete .= " FIL_HASH CHAR(32) NOT NULL, ";
$requete .= " FIL_PROTECT VARCHAR(32) NOT NULL default '', ";
$requete .= " FIL_PASSWORD VARCHAR(32) NOT NULL default '', ";
$requete .= " FIL_COMPRESS CHAR(1) NOT NULL default '', ";
//$requete .= " FIL_TAGS VARCHAR(100) NOT NULL, ";
//$requete .= " FIL_REMOVE_AFTER_GET CHAR(1) NOT NULL, ";
//$requete .= " FIL_REMOVE_AFTER_DAYS TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " FIL_NB_DOWNLOAD SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FIL_NB_ALERT TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " FIL_RATING TINYINT NOT NULL default '0', ";
$requete .= " FIL_COMMENT varchar(150) default '' NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_FILE), ";
$requete .= " KEY FIL_IND_1 (FIL_NAME), ";
$requete .= " KEY FIL_IND_2 (ID_USER_AUT, ID_USER_DEST, ID_GROUP_DEST, FIL_ONLINE), ";
$requete .= " KEY FIL_IND_3 (ID_FILEMEDIA, ID_PROJET), ";
$requete .= " CONSTRAINT im_fil_file_fk1 FOREIGN KEY im_fil_file_fk1 (ID_FILEMEDIA) ";
$requete .= "   REFERENCES " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA (ID_FILEMEDIA) ";
$requete .= "   ON DELETE NO ACTION   ON UPDATE NO ACTION, ";
$requete .= " CONSTRAINT im_fil_file_fk2 FOREIGN KEY im_fil_file_fk2 (ID_PROJET) ";
$requete .= "   REFERENCES " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET (ID_PROJET) ";
$requete .= "   ON DELETE NO ACTION   ON UPDATE NO ACTION ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-23]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FLV_FILEVOTE ( ";
$requete .= " ID_FILE MEDIUMINT UNSIGNED NOT NULL, ";
$requete .= " ID_USER_VOTE MEDIUMINT NOT NULL, ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, "; // COMMENT 'For stats'
$requete .= " FLV_DATE date NOT NULL, ";
$requete .= " FLV_VOTE_M TINYINT NOT NULL, "; // COMMENT 'More', 
$requete .= " FLV_VOTE_L TINYINT NOT NULL, "; // COMMENT 'Less'
$requete .= " PRIMARY KEY  (ID_FILE, ID_USER_VOTE), ";
$requete .= " KEY FLV_IND_1 (ID_USER_AUT), ";
//$requete .= " KEY FLV_IND_2 (ID_FILE), ";
$requete .= "  CONSTRAINT im_flv_filevote_fk1 FOREIGN KEY im_flv_filevote_fk1 (ID_FILE) ";
$requete .= "   REFERENCES " . $PREFIX_IM_TABLE . "FIL_FILE (ID_FILE) ";
$requete .= "   ON DELETE NO ACTION   ON UPDATE NO ACTION ";
$requete .= ") ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-24]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FST_FILESTATS ( ";
$requete .= " ID_USER_AUT MEDIUMINT NOT NULL, ";
$requete .= " FST_NB_SHARE SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_EXCHANGE SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_ALERT_SEND SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_REJECT SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_VOTE_M SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_VOTE_L SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_MAX_VOTE_M SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_MAX_VOTE_L SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_NB_LAST_DATE SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_LAST_DATE date NOT NULL, ";
$requete .= " FST_NB_LAST_WEEK SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FST_LAST_WEEK TINYINT UNSIGNED NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_USER_AUT) ";
$requete .= " ) ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-25]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD ( ";
$requete .= " ID_USER_DL MEDIUMINT NOT NULL, ";
$requete .= " ID_FILE_LAST_DL MEDIUMINT UNSIGNED NOT NULL, ";
$requete .= " FSD_NB_DOWNLOAD SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_LAST_DATE date NOT NULL, ";
$requete .= " FSD_NB_LAST_DATE SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_TRAFFIC_LAST_DATE MEDIUMINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_LAST_WEEK TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_NB_LAST_WEEK SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_TRAFFIC_LAST_WEEK INT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_LAST_MONTH TINYINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_NB_LAST_MONTH SMALLINT UNSIGNED NOT NULL default '0', ";
$requete .= " FSD_TRAFFIC_LAST_MONTH INT UNSIGNED NOT NULL default '0', ";
$requete .= " PRIMARY KEY  (ID_USER_DL) ";
$requete .= " ) ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-26]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "ADM_ADMINACP ( ";
$requete .= " ID_ADMIN TINYINT UNSIGNED NOT NULL auto_increment, ";
$requete .= " ADM_USERNAME varchar(20) NOT NULL default '', ";
$requete .= " ADM_PASSWORD varchar(40) NOT NULL default '', ";
$requete .= " ADM_SALT char(20) NOT NULL default '', ";
$requete .= " ADM_LEVEL INT UNSIGNED NOT NULL, ";
$requete .= " ADM_DATE_CREAT date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " ADM_DATE_LAST date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " ADM_DATE_PASSWORD date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " PRIMARY KEY  (ID_ADMIN) ";
$requete .= " ) ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-27]", $requete);
//
//
$requete  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP ( ";
$requete .= " ID_FILEBACKUP INT UNSIGNED NOT NULL auto_increment,";
$requete .= " FIB_NAME VARCHAR(20) NOT NULL, ";
$requete .= " ID_USER MEDIUMINT UNSIGNED NOT NULL, ";
$requete .= " FIB_ONLINE CHAR(1) NOT NULL, ";
$requete .= " FIB_DATE_ADD date NOT NULL default '1999-12-31', "; // default '0000-00-00'
$requete .= " FIB_SIZE INT UNSIGNED NULL, ";
$requete .= " FIB_HASH CHAR(32) NOT NULL, ";
$requete .= " FIB_PROTECT VARCHAR(32) NOT NULL, ";
$requete .= " PRIMARY KEY  (ID_FILEBACKUP), ";
$requete .= " KEY FIB_IND_1 (ID_USER, FIB_ONLINE, FIB_NAME) ";
$requete .= " ) ";
if ($dbengine == "myisam") $requete .= " ENGINE = MYISAM ";
if ($dbengine == "innodb") $requete .= " ENGINE = INNODB ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-install_table-28]", $requete);
//
?>