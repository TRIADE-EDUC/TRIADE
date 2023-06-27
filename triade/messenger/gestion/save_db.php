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
require ("../common/display_errors.inc.php"); 
//
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['action'])) $action = $_POST['action']; else $action = "";
if (isset($_POST['sxd'])) $sxd = $_POST['sxd']; else $sxd = "";
//
//
$url = "saving.php?lang=" . $lang . "&sxd=" . $sxd . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($action == "save")
  {
    define('INTRAMESSENGER',true);
    require("../common/library/phpmysqldump.pclass.php");
    require_once("../common/config/mysql.config.inc.php");
    //
    $sav = new phpmysqldump( $dbhost, $dbuname, $dbpass, $database, "en", "");

    //$sav->format_out="no_comment";	// si on ne veux pas les commentaires dans le dump
    //$sav->nettoyage();				// facultatif enleve les ancien fichiers de sauvegarde
    //$sav->fly=1;					// pas de creation de fichier sauvegarde au vol
    //$sav->data_yes=0;				// structure seulement
    //$sav->compress_ok=1;			// flag pour activer la compression
    $sav->backup();					// lance la sauvegarde

    // $sav->backup("test.sql");	// lance la sauvegarde avec un nom de fichier defini par l'utilisateur
    //$sav->compress(); 				// facultatif compresse au format gz sans utiliser le shell

    if(!$sav->errr && $sav->fly)
    {
      echo $sav->errr;
      die();
    }
    //
    header("location:" . $url);
  }
}
else
  require("redirect_acp_demo.inc.php");
?>