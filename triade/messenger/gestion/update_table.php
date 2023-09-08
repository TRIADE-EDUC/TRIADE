<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['action'])) $action = $_GET['action']; else $action = "";
//
define('INTRAMESSENGER',true);
require ("../common/sql.inc.php");
require ("../common/config/config.inc.php");
//
$qry = "";
switch ($action)
{
	case "1" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_VERSION VARCHAR( 6 ) NOT NULL ;";
		break;
	case "2" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "MSG_MESSAGE ADD MSG_DATE DATE NOT NULL ;";
		break;
	case "3" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT ADD CNT_PSEUDO VARCHAR( 20 ) NOT NULL ;";
		break;
	case "4" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_DROITS USR_LEVEL TINYINT( 4 ) NOT NULL DEFAULT '0';";
		break;
	case "5" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_COUNTRY_CODE CHAR( 2 ) NOT NULL ;";
		break;
	case "7" :
    $qry  = " CREATE TABLE " . $PREFIX_IM_TABLE . "STA_STATS ( ";
    $qry .= " STA_DATE DATE NOT NULL , ";
    $qry .= "STA_NB_MSG INT NOT NULL default '0', ";
    $qry .= "STA_NB_CREAT INT NOT NULL default '0', ";
    $qry .= "STA_NB_SESSION INT NOT NULL default '0', ";
    $qry .= "PRIMARY KEY ( STA_DATE ) ";
    //$qry .= ") COMMENT = 'Statistics'; ";
    $qry .= ") ";
		break;
	case "8" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "SES_SESSION ADD SES_STARTDATE DATE NOT NULL AFTER ETA_NUM ;";
		break;
	case "9" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS ADD STA_NB_USR INT NOT NULL default '0';";
		break;
	case "11" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_AVATAR varchar(20) NOT NULL default '';";
		break;
	case "12" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_LANGUAGE_CODE char(2) NOT NULL default '';";
		break;
	case "13" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_TIME_SHIFT SMALLINT NOT NULL default '0';";
		break;
	case "14" :
    //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "MSG_MESSAGE CHANGE MSG_LIBELLE MSG_TEXT VARCHAR(500) NOT NULL ;";
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "MSG_MESSAGE CHANGE MSG_LIBELLE MSG_TEXT TEXT NOT NULL ;";
		break;
	case "15" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "MSG_MESSAGE ADD MSG_CR CHAR(2) NOT NULL ;"; // AFTER MSG_LIBELLE 
		break;
	case "17" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_EMAIL VARCHAR(80) NOT NULL ;";
		break;
	case "18" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_PHONE VARCHAR(20) NOT NULL ;";
		break;
	case "19" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_IP_ADDRESS VARCHAR(23) NOT NULL AFTER USR_LANGUAGE_CODE ;";
		break;
	case "20" :
    //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_OS VARCHAR(5) NOT NULL COMMENT='Operating System' ;";
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_OS VARCHAR(5) NOT NULL ;";
		break;
	case "21" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "SES_SESSION CHANGE SES_IP_ADRESS SES_IP_ADDRESS VARCHAR(23) NOT NULL ;";
		break;
	case "22" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_GENDER CHAR(1) NOT NULL ;";
		break;
	case "23" :
    //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_NOM USR_NAME VARCHAR(50) NOT NULL COMMENT='Name or function' ;"; 
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_NOM USR_NAME VARCHAR(50) NOT NULL ;"; 
		break;
	case "24" :
    //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_AUTORIS USR_STATUS tinyint(4) NOT NULL COMMENT='VIP/hidden' ;"; 
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_AUTORIS USR_STATUS tinyint(4) NOT NULL ;"; 
		break;
	case "25" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT CHANGE CNT_GROUPE CNT_USER_GROUP varchar(20) NOT NULL ;"; 
		break;
	case "26" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT CHANGE CNT_PRIVILEGE CNT_STATUS tinyint(4) NOT NULL ;"; 
		break;
	case "27" :
	  $requete = "select CNT_PSEUDO FROM " . $PREFIX_IM_TABLE . "CNT_CONTACT LIMIT 0, 30 ";
    $result  = mysqli_query($id_connect, $requete);
    if (!$result) 
    {
      //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT CHANGE CNT_PSEUDO CNT_NEW_USERNAME varchar(20) NOT NULL COMMENT='Nickname changed'  ;"; 
      $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT CHANGE CNT_PSEUDO CNT_NEW_USERNAME varchar(20) NOT NULL ;"; 
    }
    else
    {
      //$qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT ADD CNT_NEW_USERNAME varchar(20) NOT NULL COMMENT='Nickname changed' ;"; 
      $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT ADD CNT_NEW_USERNAME varchar(20) NOT NULL ;"; 
    }
    break;
	case "28" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "SES_SESSION CHANGE ETA_NUM SES_STATUS tinyint(4) NOT NULL ;"; 
		break;
	case "29" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_NB_CONNECT SMALLINT NOT NULL default '0' ;"; 
		break;
	case "30" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "SES_SESSION ADD SES_AWAY_REASON tinyint(4) NOT NULL default '0' ;"; 
		break;
	case "31" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_GET_ADMIN_ALERT tinyint(4) NOT NULL default '0' ;"; 
		break;
	case "32" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_TRIADE_PHENIX VARCHAR(80) NOT NULL ;"; 
		break;
	case "33" :
    $qry  = " CREATE TABLE IF NOT EXISTS " . $PREFIX_IM_TABLE . "BAN_BANNED ( ";
    $qry .= " BAN_TYPE char(1) NOT NULL, ";
    $qry .= " BAN_VALUE varchar(50) default NULL, ";
    $qry .= " INDEX (BAN_TYPE, BAN_VALUE) ";
    //$qry .= " ) COMMENT = 'Banned users/computers/IP'; ";
    $qry .= " ) ; ";
		break;
	case "34" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_GET_OFFLINE_MSG tinyint(4) NOT NULL default '2' ;"; 
		break;
	case "35" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_MAC_ADR CHAR(12) NOT NULL default '' ;"; 
		break;
	case "36" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_COMPUTERNAME VARCHAR(20) NOT NULL default '' ;"; 
		break;
	case "37" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_SCREEN_SIZE VARCHAR(10) NOT NULL default '' ;"; 
		break;
	case "38" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_EMAIL_CLIENT VARCHAR(40) NOT NULL default '' ;"; 
		break;
	case "39" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_BROWSER VARCHAR(40) NOT NULL default '' ;"; 
		break;
	case "40" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_OOO VARCHAR(5) NOT NULL default '' ;"; 
		break;
	case "41" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_RATING tinyint(4) NOT NULL default '0' ;"; 
		break;
	case "42" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT ADD CNT_RATING tinyint(4) NOT NULL default '0' ;"; 
		break;
	case "43" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_ONLINE  tinyint(4) NOT NULL default '0' ;"; 
		break;
	case "44" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_TIME_LOCK  TIME NOT NULL default '00:00:00' ;"; 
		break;
	case "45" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_REG VARCHAR(30) NOT NULL default '' ;"; 
		break;
	case "46" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_NICKNAME VARCHAR(20) NOT NULL default '' AFTER USR_USERNAME ;"; 
		break;
	case "47" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_DATE_PASSWORD date NOT NULL default '0000-00-00' AFTER USR_DATE_LAST;"; 
		break;
	case "48" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_DATE_ACTIVITY date NOT NULL default '0000-00-00' AFTER USR_DATE_LAST;"; 
		break;
	//
	//case "49" : VOIR en bas !
	//
	case "50" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USG_USERGRP ADD USG_PENDING TINYINT NOT NULL DEFAULT '0';"; 
		break;
	//
	//case "51" : VOIR en bas !
	//
	case "52" :
    $qry = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD ID_ROLE TINYINT UNSIGNED NULL default NULL AFTER USR_LEVEL;"; 
		break;
	//
	//case "53" : VOIR en bas !
	//
}
//
if ($qry != "")
{
  $result = mysqli_query($id_connect, $qry);
  if (!$result) error_sql_log("[ERR-update_table-1]", $qry);
}
else
{
  if ($action == "6")
  {
    $requete  = " DROP TABLE T_GRP_GROUPE; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-2]", $requete);
    //
    $requete  = " CREATE TABLE " . $PREFIX_IM_TABLE . "GRP_GROUP ( ";
    $requete .= " ID_GROUP INT NOT NULL AUTO_INCREMENT PRIMARY KEY , ";
    $requete .= " GRP_NAME VARCHAR( 20 ) NOT NULL , ";
    $requete .= " GRP_PRIVATE TINYINT NOT NULL DEFAULT '0', ";
    $requete .= " UNIQUE (GRP_NAME) ";
    //$requete .= " ) COMMENT = 'Group list'; ";
    $requete .= " ) ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-3]", $requete);
    //
    $requete  = " DROP TABLE " . $PREFIX_IM_TABLE . "USG_USERGRP; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-4]", $requete);
    //
    $requete  = " CREATE TABLE " . $PREFIX_IM_TABLE . "USG_USERGRP ( ";
    $requete .= " ID_GROUP INT NOT NULL , ";
    $requete .= " ID_USER INT NOT NULL , ";
    $requete .= " INDEX ( ID_GROUP , ID_USER ) ";
    //$requete .= " ) COMMENT = 'Users in groups'; ";
    $requete .= " ) ; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-5]", $requete);
  }
  //
  if ($action == "10")
  {
    $requete  = " CREATE TABLE " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ( ";
    $requete .= " ID_CONFERENCE INT NOT NULL AUTO_INCREMENT, ";
    $requete .= " ID_USER INT NOT NULL , ";
    $requete .= " CNF_DATE_CREAT date NOT NULL default '0000-00-00', ";
    $requete .= " CNF_TIME_CREAT time NOT NULL default '00:00:00', ";
    $requete .= " PRIMARY KEY  (ID_CONFERENCE), ";
    $requete .= " KEY ID_USER (ID_USER) ";
    //$requete .= " ) COMMENT = 'Conferences'; ";
    $requete .= " ) ; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-6]", $requete);
    //
    $requete  = " CREATE TABLE " . $PREFIX_IM_TABLE . "USC_USERCONF ( ";
    $requete .= " ID_CONFERENCE INT NOT NULL , ";
    $requete .= " ID_USER INT NOT NULL , ";
    $requete .= " USC_ACTIVE TINYINT NOT NULL DEFAULT '0', ";
    $requete .= " INDEX ( ID_CONFERENCE , ID_USER ) ";
    //$requete .= " ) COMMENT = 'Users in conferences'; ";
    $requete .= " ) ; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-7]", $requete);
    //
    $requete  = " ALTER TABLE " . $PREFIX_IM_TABLE . "MSG_MESSAGE ADD ID_CONFERENCE INT NOT NULL default '0'; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-8]", $requete);
  }
  //
  if ($action == "16")
  {
    //$requete = " ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_PWD_ERRORS SMALLINT NOT NULL COMMENT='Password errors counter' ; ";
    $requete = " ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_PWD_ERRORS SMALLINT NOT NULL ; ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-9]", $requete);
    //
    $requete = " ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_PASSWORD USR_PASSWORD VARCHAR(40); ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-10]", $requete);
  }
  //
  if ($action == "49")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS CHANGE STA_NB_MSG STA_NB_MSG MEDIUMINT UNSIGNED NOT NULL default '0'"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-11]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS CHANGE STA_NB_CREAT STA_NB_CREAT SMALLINT UNSIGNED NOT NULL default '0'"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-12]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS CHANGE STA_NB_SESSION STA_NB_SESSION SMALLINT UNSIGNED NOT NULL default '0'"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-13]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS CHANGE STA_NB_USR STA_NB_USR MEDIUMINT UNSIGNED NOT NULL default '0'"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-14]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS ADD STA_SBX_NB_MSG MEDIUMINT UNSIGNED NOT NULL default '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-15]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "GRP_GROUP ADD GRP_SHOUTBOX TINYINT NOT NULL DEFAULT '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-16]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "GRP_GROUP ADD GRP_SBX_NEED_APPROVAL TINYINT NOT NULL DEFAULT '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-17]", $requete);
  }
  //
  if ($action == "51")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ADD SBV_VOTE_M TINYINT NOT NULL;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-18]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ADD SBV_VOTE_L TINYINT NOT NULL;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-19]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "CNT_CONTACT ADD CNT_SOUND TINYINT NOT NULL DEFAULT '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-20]", $requete);
    //
    sleep(1);
    //
    $requete = "UPDATE " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE set SBV_VOTE_L = -1 where SBV_VOTE < 0 ;";   //  19b A FAIRE AVANT 18b !!!
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-19b]", $requete);
    //
    $requete = "UPDATE " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE set SBV_VOTE_M = 1 where SBV_VOTE > 0 ;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-18b]", $requete);
    //
    sleep(1);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE DROP SBV_VOTE;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-21]", $requete);
  }
  //
  if ($action == "53")
  {
    $requete = "TRUNCATE TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-22a]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE ADD MDL_MAX_VALUE SMALLINT UNSIGNED NOT NULL default 0;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-22b]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE ADD MDL_OTHER CHAR(1) NOT NULL default '';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-23]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE ADD MDL_ROLE CHAR(1) NOT NULL default '';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-24]", $requete);
  }
  //
  if ($action == "54")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_DATE_BIRTH DATE NOT NULL default '0000-00-00' AFTER USR_DATE_ACTIVITY;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-25]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER CHANGE USR_IP_ADDRESS USR_IP_ADDRESS varchar(39) NOT NULL default '';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-26]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "MDL_MODULE CHANGE MDL_MAX_VALUE MDL_MAX_VALUE INT UNSIGNED NOT NULL default 0;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-27]", $requete);
    //
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')
    {
      // Roles avec options :
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (79, 'ALLOW_SKIN', 0, ''), (80, 'SHARE_FILES', 0, 'X'), (81, 'SHARE_FILES_NEED_APPROVAL', 0, ''), ";
      $requete .= " (82, 'SHARE_FILES_EXCHANGE', 0, ''), (83, 'SHARE_FILES_EXCHANGE_NEED_APPROVAL', 0, ''); ";
      $requete .= " (84, 'SHARE_FILES_MAX_FILE_SIZE', 999999, ''), (85, 'SHARE_FILES_MAX_NB_FILES_USER', 9999, ''); ";
      $requete .= " (86, 'SHARE_FILES_MAX_SPACE_SIZE_USER', 99999, ''), (87, 'SHARE_FILES_QUOTA_FILES_USER_WEEK', 999, ''), ";
      $requete .= " (88, 'SHARE_FILES_VOTE', 0, ''), (89, 'SHARE_FILES_TRASH', 0, ''), (90, 'SHARE_FILES_EXCHANGE_TRASH', 0, ''), ; ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-update_table-28]", $requete);
      //
      // Roles sans options :
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_ROLE) VALUES ";
      $requete .= " (104, 'ROLE_SHARE_FILES_READ_ONLY', 'R'); ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-update_table-29]", $requete);
    }
  }
  //
  if ($action == "55")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS ADD STA_SF_NB_SHARE MEDIUMINT UNSIGNED NOT NULL default '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-30]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS ADD STA_SF_NB_EXCHANGE MEDIUMINT UNSIGNED NOT NULL default '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-31]", $requete);
  }
  //
  if ($action == "56")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "STA_STATS ADD STA_SF_NB_DOWNLOAD MEDIUMINT UNSIGNED NOT NULL default '0';"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-32]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "FIL_FILE ADD FIL_COMPRESS CHAR(1) NOT NULL AFTER FIL_HASH;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-33]", $requete);
  }
  //
  if ($action == "57")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "FIL_FILE ADD FIL_PROTECT VARCHAR(32) NOT NULL AFTER FIL_HASH;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-34]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "FIL_FILE ADD FIL_PASSWORD VARCHAR(32) NOT NULL AFTER FIL_PROTECT;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-35]", $requete);
  }


  //
  if ($action == "58")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_PASS_WEB varchar(40) NOT NULL default '' AFTER USR_PASSWORD ;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-36]", $requete);
    //
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_PASS_SALT char(20) NOT NULL default '' AFTER USR_PASS_WEB;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-37]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_HIDDEN_TO_CONTACTS' WHERE ID_MODULE = 7 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38a]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_HISTORY_MESSAGES' WHERE ID_MODULE = 12 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38b]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'HISTORY_MESSAGES_ON_ACP' WHERE ID_MODULE = 14 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38c]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_MANAGE_CONTACT_LIST' WHERE ID_MODULE = 30 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38d]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_MANAGE_OPTIONS' WHERE ID_MODULE = 31 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38e]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_MANAGE_PROFILE' WHERE ID_MODULE = 32 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38f]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set MDL_NAME = 'ALLOW_CONTACT_RATING' WHERE ID_MODULE = 11 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38g]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "MDL_MODULE set ID_MODULE = 59  WHERE MDL_NAME = 'ALLOW_SKIN' and ID_MODULE = 79 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38ha]", $requete);
    //
    $requete = " UPDATE " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE set ID_MODULE = 59  WHERE ID_MODULE = 79 limit 1;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-38hb]", $requete);
    //
    if (_ROLES_TO_OVERRIDE_PERMISSIONS != '')
    {
      $requete = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_OTHER) VALUES (19, 'ALLOW_HIDDEN_STATUS', '');"; 
      $result = mysqli_query($id_connect, $requete);
      //if (!$result) error_sql_log("[ERR-update_table-39a]", $requete);
      //
      $requete = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_OTHER) VALUES (105, 'ROLE_OFFLINE_MODE', 'R');"; 
      $result = mysqli_query($id_connect, $requete);
      //if (!$result) error_sql_log("[ERR-update_table-39b]", $requete);
      //
      $requete = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_OTHER) VALUES (106, 'ROLE_CHANGE_SERVER_STATUS', 'R');"; 
      $result = mysqli_query($id_connect, $requete);
      //if (!$result) error_sql_log("[ERR-update_table-39c]", $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (73, 'BACKUP_FILES', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (74, 'BACKUP_FILES_ALLOW_MULTI_FOLDERS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (75, 'BACKUP_FILES_ALLOW_SUB_FOLDERS', 0, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (76, 'BACKUP_FILES_MAX_ARCHIVE_SIZE', 999999, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (77, 'BACKUP_FILES_MAX_NB_ARCHIVES_USER', 9, ''); ";
      $result = mysqli_query($id_connect, $requete);
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "MDL_MODULE (ID_MODULE, MDL_NAME, MDL_MAX_VALUE, MDL_OTHER) VALUES ";
      $requete .= " (78, 'BACKUP_FILES_MAX_SPACE_SIZE_USER', 999999, ''); ";
      $result = mysqli_query($id_connect, $requete);
    }
  }
  //
  if ($action == "59")
  {
    $requete = "ALTER TABLE " . $PREFIX_IM_TABLE . "USR_USER ADD USR_DATE_BACKUP date NOT NULL default '0000-00-00' AFTER USR_GET_OFFLINE_MSG ;"; 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-update_table-40]", $requete);
  }
}
//
mysqli_close($id_connect);
//
//header("location:check.php?lang=" . $lang . "&");
echo "<META http-equiv='refresh' content='1;url=check.php?lang=" . $lang . "&'>";
?>