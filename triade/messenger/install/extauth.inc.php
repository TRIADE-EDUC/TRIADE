<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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

$fichier = "";
$chemin = "../../";
if ($external_path == 2) $chemin .= $external_path_value . "/";
//
if ($external_auth == "68kb")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "achievo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "activecollab")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "adheo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "admidio")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "aef")
{
  $fichier = $chemin . "universal.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $globals['server'];
    $database = $globals['database'];
    $dbuname = $globals['user'];
    $dbpass = $globals['password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "agora")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "aphpkb")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "artiphp")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "atutor")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "b2evolution")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "bewelcome")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "bitweaver")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "bigace")
{
  $fichier = $chemin . "system/config/config.system.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $_BIGACE['db']['host'];
    $database = $_BIGACE['db']['name'];
    $dbuname = $_BIGACE['db']['user'];
    $dbpass = $_BIGACE['db']['pass'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "bonfire")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "chamilo")
{
  $fichier = $chemin . "main/inc/conf/configuration.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $_configuration['db_host'];
    $database = $_configuration['main_database'];
    $dbuname = $_configuration['db_user'];
    $dbpass = $_configuration['db_password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "claroline")
{
  $fichier = $chemin . "platform/conf/claro_main.conf.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $GLOBALS['dbHost'] = 'localhost';
    $database = $GLOBALS['mainDbName'];
    $dbuname = $GLOBALS['dbLogin'];
    $dbpass = $GLOBALS['dbPass'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "cmsmadesimple")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $config['db_hostname'];
    $database = $config['db_name'];
    $dbuname = $config['db_username'];
    $dbpass = $config['db_password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "collabtive")
{
  $fichier = $chemin . "config/standard/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $db_host;
    $database = $db_name;
    $dbuname = $db_user;
    $dbpass = $db_pass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "concrete")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
/*
if ($external_auth == "connectixboards")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
*/
if ($external_auth == "contao")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "cotonti")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "cpg")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "cscart")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "cuteflow")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dmanager")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "docebo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dokeos")
{
  $fichier = $chemin . "main/inc/conf/configuration.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $_configuration['db_host'];
    $database = $_configuration['main_database'];
    $dbuname = $_configuration['db_user'];
    $dbpass = $_configuration['db_password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dolibarr")
{
  $fichier = $chemin . "conf/conf.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $dolibarr_main_db_host;
    $database = $dolibarr_main_db_name;
    $dbuname = $dolibarr_main_db_user;
    $dbpass = $dolibarr_main_db_pass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dotclear2")
{
  $fichier = $chemin . "inc/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = DC_DBHOST ;
    $database = DC_DBNAME ;
    $dbuname = DC_DBUSER ;
    $dbpass = DC_DBPASSWORD ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dotproject")
{
  $fichier = $chemin . "includes/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $dPconfig['dbhost'];
    $database = $dPconfig['dbname'];
    $dbuname = $dPconfig['dbuser'];
    $dbpass = $dPconfig['dbpass'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "dragonflycms")
{
  $fichier = $chemin . "includes/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    //$dbhost = $dbhost;
    $database = $dbname;
    //$dbuname = $dbuname;
    //$dbpass = $dbpass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "drupal7")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "e107")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "egroupware")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "elgg")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "etano")
{
  $fichier = $chemin . "includes/defines.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _DBHOST_ ;
    $database = _DBNAME_ ;
    $dbuname = _DBUSER_ ;
    $dbpass = _DBPASS_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "etraxis")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "epesi")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "ezpublish")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "fengoffice")
{
  $fichier = $chemin . "config/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = DB_HOST ;
    $database = DB_NAME ;
    $dbuname = DB_USER ;
    $dbpass = DB_PASS ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "fluxbb")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $db_host;
    $database = $db_name;
    $dbuname = $db_username;
    $dbpass = $db_password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "freeway")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "friendika")
{
  $fichier = $chemin . ".htconfig.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $db_host;
    $database = $db_data;
    $dbuname = $db_user;
    $dbpass = $db_pass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "frontaccount")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "fudforum")
{
  $fichier = $chemin . "include/GLOBALS.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $GLOBALS['DBHOST'];
    $database = $GLOBALS['DBHOST_DBNAME'];
    $dbuname = $GLOBALS['DBHOST_USER'];
    $dbpass = $GLOBALS['DBHOST_PASSWORD'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "geeklog")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "gepi")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "groupoffice")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "helpcenterlive")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "hesk")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "impresscms")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "ipboard3")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "issuemanager")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "joomla")
{
  $fichier = $chemin . "configuration.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $host;
    $database = $db;
    $dbuname = $user;
    $dbpass = $password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "kimai")
{
  $fichier = $chemin . "includes/autoconf.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $server_hostname;
    $database = $server_database;
    $dbuname = $server_username;
    $dbpass = $server_password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "livecart")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "lodel")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "malleo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "magento")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "mahara")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "mambo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "mantisbt")
{
  $fichier = $chemin . "config_inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $g_hostname;
    $database = $g_database_name;
    $dbuname = $g_db_username;
    $dbpass = $g_db_password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "minibb")
{
  $fichier = $chemin . "setup_options.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $DBhost;
    $database = $DBname;
    $dbuname = $DBusr;
    $dbpass = $DBpwd;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "modx")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "moodle")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "mound")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "mybb")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "npds")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "nucleus")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "nukedklan")
{
  $fichier = $chemin . "conf.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $global['db_host'];
    $database = $global['db_name'];
    $dbuname = $global['db_user'];
    $dbpass = $global['db_pass'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "obm")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "ocportal")
{
  $fichier = $chemin . "info.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $SITE_INFO['db_site_host'];
    $database = $SITE_INFO['db_site'];
    $dbuname = $SITE_INFO['db_site_user'];
    $dbpass = $SITE_INFO['db_site_password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "oozaims")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "opengoo")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "openrealty")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "oscmax")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "oscommerce")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "osticket")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "ovidentia")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "owl")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "pcpin_chat")
{
  $fichier = $chemin . "config/db.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $_pcpin_db_server;
    $database = $_pcpin_db_database;
    $dbuname = $_pcpin_db_user;
    $dbpass = $_pcpin_db_password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phenix")
{
  $fichier = $chemin . "inc/conf.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $cfgHote;
    $database = $cfgBase;
    $dbuname = $cfgUser;
    $dbpass = $cfgPass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phorum")
{
  define('PHORUM', 'X');
  $fichier = $chemin . "include/db/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $PHORUM['DBCONFIG']['server'];
    $database = $PHORUM['DBCONFIG']['name'];
    $dbuname = $PHORUM['DBCONFIG']['user'];
    $dbpass = $PHORUM['DBCONFIG']['password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpbb3")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    //$dbhost = $dbhost;
    $database = $dbname;
    $dbuname = $dbuser;
    $dbpass = $dbpasswd;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpbms")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpboost")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpcollab")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpdug")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "php_fusion")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpgroupware")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpizabi")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpmyfaq")
{
  $fichier = $chemin . "config/database.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $DB["server"];
    $database = $DB["db"];
    $dbuname = $DB["user"];
    $dbpass = $DB["password"];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpnuke")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    //$dbhost = $dbhost;
    $database = $dbname;
    //$dbuname = $dbuname;
    //$dbpass = $dbpass;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phprojekt6")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpscheduleit")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "phpwcms")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "pligg")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "pms")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "pragmamx")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "prestashop")
{
  $fichier = $chemin . "config/settings.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _DB_SERVER_ ;
    $database = _DB_NAME_ ;
    $dbuname = _DB_USER_ ;
    $dbpass = _DB_PASSWD_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "projectpier")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "projelead")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "promethee")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "punbb1.4")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $db_host;
    $database = $db_name;
    $dbuname = $db_username;
    $dbpass = $db_password;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "pyrocms")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "qdpm")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "sharetronix")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "silverstripe")
{
  $fichier = $chemin . "mysite/_config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $databaseConfig['server'];
    $database = $databaseConfig['database'];
    $dbuname = $databaseConfig['username'];
    $dbpass = $databaseConfig['password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "simplegroupware")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "sit")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "skadate")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "smf")
{
  $fichier = $chemin . "Settings.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $db_server;
    $database = $db_name;
    $dbuname = $db_user;
    $dbpass = $db_passwd;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "socialengine")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
/*
if ($external_auth == "spip")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
*/
if ($external_auth == "statusnet")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "streber")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "sugarcrm")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "taskfreak")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "textcube")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "textpattern")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
/*
if ($external_auth == "thebuggenie")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
*/
if ($external_auth == "thelia")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "tikiwiki")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "tine")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "todoyu")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "tomatocart")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "toutateam")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "trellisdesk")
{
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $config['host'];
    $database = $config['name'];
    $dbuname = $config['user'] ;
    $dbpass = $config['pass']  ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "typo3")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "typolight")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "vanilla")
{
  define('APPLICATION', 'X');
  $fichier = $chemin . "conf/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $Configuration['Database']['Host'];
    $database = $Configuration['Database']['Name'];
    $dbuname = $Configuration['Database']['User'];
    $dbpass = $Configuration['Database']['Password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "vbulletin")
{
  $fichier = $chemin . "includes/config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $config['MasterServer']['servername'];
    $database = $config['Database']['dbname'];
    $dbuname = $config['MasterServer']['username'];
    $dbpass = $config['MasterServer']['password'];
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "vcalendar")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "vtigercrm")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "wbblite")
{
  $fichier = $chemin . "wcf/config.inc.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $dbHost;
    $database = $dbName;
    $dbuname = $dbUser;
    $dbpass = $dbPassword;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "web2project")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "webcalendar")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "webcollab")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "weberp")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "webissues")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "websitebaker")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "wordpress")
{
  $fichier = $chemin . "wp-config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = DB_HOST ;
    $database = DB_NAME ;
    $dbuname = DB_USER ;
    $dbpass = DB_PASSWORD ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "xmb")
{
  define('IN_CODE', 'X');
  $fichier = $chemin . "config.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = $dbhost;
    $database = $dbname;
    $dbuname = $dbuser;
    $dbpass = $dbpw;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "xoops")
{
  $fichier = $chemin . "xoops_data/data/secure.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = XOOPS_DB_HOST ;
    $database = XOOPS_DB_NAME ;
    $dbuname = XOOPS_DB_USER ;
    $dbpass = XOOPS_DB_PASS ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "yacs")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "zazavi")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "zencart")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "zikula")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
/*
if ($external_auth == "efront")
{
  $fichier = $chemin . "libraries/configuration.php";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
*/
//
if ($external_auth == "")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
if ($external_auth == "")
{
  $fichier = $chemin . "xxxxxxxxxx/yyyyyyyyyyyyyyyyyyy";
  if (file_exists($fichier))
  {
    require($fichier);
    $dbhost = _XXXXXXXXXXX_ ;
    $database = _XXXXXXXXXXX_ ;
    $dbuname = _XXXXXXXXXXX_ ;
    $dbpass = _XXXXXXXXXXX_ ;
  }
  else
    $chemin = "KO";
}
//
//
if (strstr($fichier, "xx/yy") != "") 
{
  $fichier = "";
  $chemin  = "";
}
?>