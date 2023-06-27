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
if (isset($_GET['os'])) $os = $_GET['os']; else $os = "";
if (isset($_GET['username'])) $username = $_GET['username']; else $username = "";
//
if (function_exists('file_put_contents') == false) 
{
  function file_put_contents($file, $string) 
  {
    $f = fopen($file, 'w');
    fwrite($f, $string);
    fclose($f);
  }
}
//
$rep = dirname($_SERVER['SCRIPT_FILENAME']) . "/";
$rep_pass = $rep;
if (substr($rep_pass, 1, 1) == ":") $rep_pass = substr($rep_pass, 2, strlen($rep_pass) -2);
$file_passwd = $rep . "/" . ".htpasswd";
$file_access = $rep . "/" . ".htaccess";
//
//
if ( ($action == "create") and ($os != "") )
{
  if ( (file_exists($file_passwd) == false) or (is_writable($file_passwd) == true) )
  {
    if (strlen($username) < 4) $username = "admin";
    if ($os == "windows")
      $data = $username . ":www.theuds.com\n";  // password : www.theuds.com    (no crypt on windows server)
    else
    {
      $data  = $username . ":41tFJRs9favFU\n\n\n";   // password : www.theuds.com
      $data .= "# Password generator : http://cobalt.golden.net/generator/index.cgi   \n";
      $data .= "# Password generator : http://www.thejackol.com/scripts/htpasswdgen.php   \n";
    }
    //
    file_put_contents($file_passwd, $data);
    //
    if ( (file_exists($file_access) == false) or (is_writable($file_access) == true) )
    {
      $data  = "AuthUserFile " . $rep_pass . ".htpasswd \n";
      $data .= "AuthGroupFile /dev/null \n";
      $data .= "AuthName 'IntraMessenger ACP' \n";
      $data .= "AuthType Basic \n";
      $data .= "<Limit GET POST> \n";
      $data .= "require valid-user \n";
      $data .= "</Limit> \n";
      //
      $data .= "Options -Indexes \n"; // Interdire de lister le contenu du dossier
      //
      $data .= "<Files .htaccess> \n"; // Interdire l'accès à ce fichier depuis un navigateur web
      $data .= "order allow,deny \n";
      $data .= "deny from all \n";
      $data .= "</Files> \n";
      $data .= " \n";
      $data .= " \n";
      file_put_contents($file_access, $data);
    }
  }
}
//
//
if ($action == "delete")
{
  if (is_writable($file_access) == true)  unlink($file_access);
  if (is_writable($file_passwd) == true)  unlink($file_passwd);
}
//
header("location:htaccess.php?lang=" . $lang . "&");  
?>