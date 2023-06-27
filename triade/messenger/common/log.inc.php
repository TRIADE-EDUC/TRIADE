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




// pour éviter l'erreur : 
// Strict Standards: date(): It is not safe to rely on the system's timezone settings
if (function_exists('date_default_timezone_set'))
{
  $fus = ini_get('date.timezone');
  if ($fus != "") 
    date_default_timezone_set($fus);
  else
    date_default_timezone_set("Europe/Paris");
}

// If not works, use :
#date_default_timezone_set("Europe/Paris");




function write_log($action, $text)
{
  GLOBAL $l_date_format_display, $l_time_format_display;
  //
  if ( ($l_date_format_display == "") or ($l_time_format_display == "") ) include ("lang.inc.php");
  if ($l_date_format_display == "") $l_date_format_display = "d/m/Y";
  if ($l_time_format_display == "") $l_time_format_display = "H:i:s";
	$can_use_flock = "X";
	$ip = $_SERVER['REMOTE_ADDR'];	
	$username_and_domaine = gethostbyaddr("$ip");   //. gethostbyaddr("");
	//
	$plus = ";" . $ip . ";" . $username_and_domaine . ";";
	//
	$folder = "log/";
	$fic = $action . ".txt";
	if ( ( (file_exists($folder . $fic)) and (is_writeable($folder . $fic)) ) or (is_writeable($folder)) )
	{
    //if (ini_get("disable_functions") != "")
    //{
    //  if ( strpos(ini_get("disable_functions"), "flock")) $can_use_flock = "";
    //}
    if (!function_exists('flock')) $can_use_flock = "";
    //
    if ($fp = fopen($folder . $fic, "a"))
    {
      if ($can_use_flock != "")
      {
        if (flock($fp, 2));
        {
          fputs($fp, date($l_date_format_display . ";" . $l_time_format_display) . ";" . $text . $plus ."\r\n");
        }
        flock($fp, 3);
        fclose($fp);
      }
      else
      {
        fputs($fp, date($l_date_format_display . ";" . $l_time_format_display) . ";" . $text . $plus ."\r\n");
        fclose($fp);
      }
    }
  }
}

Function error_session_log($t_user)
{
	write_log("error_session__end_session_log", $t_user . " " );

	die ("Session finish/terminée.");
}

Function error_check_log($t_user, $t_check)
{
	write_log("error_check_log", $t_user . " ; " . $t_check);
}

?>