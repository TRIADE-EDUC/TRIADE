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
error_reporting(E_ALL);
//
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
if (isset($_POST['extern_prefix'])) $extern_prefix = trim($_POST['extern_prefix']); else $extern_prefix = "";
if (isset($_POST['extern_dbhost'])) $extern_dbhost = trim($_POST['extern_dbhost']); else $extern_dbhost = "";
if (isset($_POST['extern_database'])) $extern_database = trim($_POST['extern_database']); else $extern_database = "";
if (isset($_POST['extern_dbuname'])) $extern_dbuname = trim($_POST['extern_dbuname']); else $extern_dbuname = "";
if (isset($_POST['extern_dbpass'])) $extern_dbpass = trim($_POST['extern_dbpass']); else $extern_dbpass = "";
if (isset($_POST['extern_dbpass2'])) $extern_dbpass2 = trim($_POST['extern_dbpass2']); else $extern_dbpass2 = "";
if (isset($_POST['LICENSE_KEY'])) $LICENSE_KEY = trim($_POST['LICENSE_KEY']); else $LICENSE_KEY = "";
if (isset($_POST['PASSWORD_SALT'])) $PASSWORD_SALT = trim($_POST['PASSWORD_SALT']); else $PASSWORD_SALT = "";
if (isset($_POST['DC_MASTER_KEY'])) $DC_MASTER_KEY = trim($_POST['DC_MASTER_KEY']); else $DC_MASTER_KEY = "";
if (isset($_POST['SDATA_DB_SALT'])) $SDATA_DB_SALT = trim($_POST['SDATA_DB_SALT']); else $SDATA_DB_SALT = "";
if (isset($_POST['_COOKIE_KEY_'])) $_COOKIE_KEY_ = trim($_POST['_COOKIE_KEY_']); else $_COOKIE_KEY_ = "";
if (isset($_POST['OW_PASSWORD_SALT'])) $OW_PASSWORD_SALT = trim($_POST['OW_PASSWORD_SALT']); else $OW_PASSWORD_SALT = "";
if (isset($_POST['typolight'])) $typolight = $_POST['typolight']; else $typolight = "";
if (isset($_POST['triade'])) $triade = $_POST['triade']; else $triade = "";
if (isset($_POST['phenix_include_in_triade'])) $phenix_include_in_triade = $_POST['phenix_include_in_triade']; else $phenix_include_in_triade = "";
if (isset($_POST['phenix_table_prefix'])) $phenix_table_prefix = $_POST['phenix_table_prefix']; else $phenix_table_prefix = "";
//
$do_not_use_users = '';  
$do_not_use_members = '';  
if ($typolight == "2") $do_not_use_users = "X";
if ($typolight == "3") $do_not_use_members = "X";
//
$do_not_use_student = '';
$do_not_use_school_members = '';  
if ($triade == "2") $do_not_use_student = "X";
if ($triade == "3") $do_not_use_school_members = "X";
//
if ($extern_dbpass != $extern_dbpass2)
{
  $extern_dbpass = "";
  $extern_dbpass2 = "";
}
//
if ( ($extern_dbhost == "") or ($extern_database == "") or ($extern_dbuname == "") )
{
  $extern_dbhost = "";
  $extern_database = "";
  $extern_dbuname = "";
  $extern_dbpass = "";
}
//
//$url = "list_options_auth_updating.php?lang=" . $lang . "&";
$url = "list_options_auth_test.php?lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if (is_writeable("../common/config/extern.config.inc.php"))
  {
    $fp = fopen("../common/config/extern.config.inc.php", "w"); 
    if (flock($fp, 2)); 
    { 
      fputs($fp, "<?php" . "\r\n"); 
      fputs($fp, "/*******************************************************" . "\r\n"); 
      fputs($fp, " **                  IntraMessenger - server          **" . "\r\n"); 
      fputs($fp, " **                                                   **" . "\r\n"); 
      fputs($fp, " **  Copyright:      (C) 2006 - 2015 THeUDS           **" . "\r\n"); 
      fputs($fp, " **  Web:            http://www.theuds.com            **" . "\r\n"); 
      fputs($fp, " **                  http://www.intramessenger.net    **" . "\r\n"); 
      fputs($fp, " **  Licence :       GPL (GNU Public License)         **" . "\r\n"); 
      fputs($fp, " **  http://opensource.org/licenses/gpl-license.php   **" . "\r\n"); 
      fputs($fp, " *******************************************************/" . "\r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "/*******************************************************" . "\r\n"); 
      fputs($fp, " **       This file is part of IntraMessenger-server  **" . "\r\n"); 
      fputs($fp, " **                                                   **" . "\r\n"); 
      fputs($fp, " **  IntraMessenger is a free software.               **" . "\r\n"); 
      fputs($fp, " **  IntraMessenger is distributed in the hope that   **" . "\r\n"); 
      fputs($fp, " **  it will be useful, but WITHOUT ANY WARRANTY.     **" . "\r\n"); 
      fputs($fp, " *******************************************************/" . "\r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "if ( !defined('INTRAMESSENGER') ) die(); ");
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Table prefix :" . "\r\n"); 
      fputs($fp, "$" . "extern_prefix = '" . $extern_prefix . "'; \r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Mysql host (maybe : 'localhost') :" . "\r\n"); 
      fputs($fp, "$" . "extern_dbhost = '" . $extern_dbhost . "'; \r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Mysql port number :" . "\r\n"); 
      fputs($fp, "$" . "extern_dbport = ''; \r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Mysql database :" . "\r\n"); 
      fputs($fp, "$" . "extern_database = '" . $extern_database . "'; \r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Mysql username :" . "\r\n"); 
      fputs($fp, "$" . "extern_dbuname = '" . $extern_dbuname . "'; \r\n"); 
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "# Mysql password :" . "\r\n"); 
      fputs($fp, "$" . "extern_dbpass = '" . $extern_dbpass . "'; \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- activeCollab ---------------" . "\r\n"); 
      fputs($fp, "# Licence number (from file license.php)" . "\r\n"); 
      fputs($fp, "if (!defined('LICENSE_KEY')) define('LICENSE_KEY', '" . $LICENSE_KEY . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- Concrete ---------------" . "\r\n"); 
      fputs($fp, "# see the file concrete/config/site.php" . "\r\n"); 
      fputs($fp, "if (!defined('PASSWORD_SALT')) define('PASSWORD_SALT', '" . $PASSWORD_SALT . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- Dotclear 2 ---------------" . "\r\n"); 
      fputs($fp, "# see the file dotclear/inc/config.php" . "\r\n"); 
      fputs($fp, "if (!defined('DC_MASTER_KEY')) define('DC_MASTER_KEY', '" . $DC_MASTER_KEY . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- ImpressCMS ---------------" . "\r\n"); 
      fputs($fp, "if (!defined('SDATA_DB_SALT')) define('SDATA_DB_SALT', '" . $SDATA_DB_SALT . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- Prestashop ---------------" . "\r\n"); 
      fputs($fp, "if (!defined('_COOKIE_KEY_')) define('_COOKIE_KEY_', '" . $_COOKIE_KEY_ . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- Oxwall ---------------" . "\r\n"); 
      fputs($fp, "if (!defined('OW_PASSWORD_SALT')) define('OW_PASSWORD_SALT', '" . $OW_PASSWORD_SALT . "'); \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- typolight ---------------" . "\r\n"); 
      fputs($fp, "# Only members : \r\n"); 
      fputs($fp, "$" . "do_not_use_users = '" . $do_not_use_users . "'; \r\n"); 
      fputs($fp, "# OR (OU) \r\n"); 
      fputs($fp, "$" . "do_not_use_members = '" . $do_not_use_members . "'; \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# --------------- Triade ---------------" . "\r\n"); 
      fputs($fp, "# Use Phenix in Triade :" . "\r\n"); 
      fputs($fp, "$" . "phenix_include_in_triade = '" . $phenix_include_in_triade . "'; \r\n"); 
      fputs($fp, "# Phenix table prefix :" . "\r\n"); 
      fputs($fp, "$" . "phenix_table_prefix = '" . $phenix_table_prefix . "'; \r\n"); 
      fputs($fp, "\r\n"); 
      fputs($fp, "# Only school (seulement le personnel scolaire) :" . "\r\n"); 
      fputs($fp, "$" . "do_not_use_student = '" . $do_not_use_student . "'; \r\n"); 
      fputs($fp, "# OR (OU)" . "\r\n"); 
      fputs($fp, "# Only student (seulement les élèves) :" . "\r\n"); 
      fputs($fp, "$" . "do_not_use_school_members = '" . $do_not_use_school_members . "'; \r\n"); 
      //
      fputs($fp, "" . "\r\n"); 
      fputs($fp, "?>"); 
      flock($fp, 3); 
    } 
    fclose($fp); 
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>