<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2011 THeUDS           **
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
GLOBAL $lang;
//
$lang_file = "";
if ($lang == '') 
{ 
  if (!function_exists('autoSelectLanguage'))  require("../common/detect.lang.inc.php");
  $lang = autoSelectLanguage(array('fr', 'en', 'it', 'pt', 'it', 'ro', 'de', 'es', 'nl'), 'en');
}
$lang = strtoupper($lang);
if ($lang == '') $lang = 'EN';
//
if ($lang == 'EN') $lang_file = "../common/lang/english-iso-8859-1.inc.php";
if ($lang == 'FR') $lang_file = "../common/lang/french-iso-8859-1.inc.php";
if ($lang == 'PT') $lang_file = "../common/lang/portuguese-iso-8859-1.inc.php";
if ($lang == 'BR') $lang_file = "../common/lang/brazilian-iso-8859-1.inc.php";
if ($lang == 'IT') $lang_file = "../common/lang/italian-iso-8859-1.inc.php";
if ($lang == 'RO') $lang_file = "../common/lang/romana-iso-8859-1.inc.php";
if ($lang == 'DE') $lang_file = "../common/lang/german-iso-8859-1.inc.php";
if ($lang == 'ES') $lang_file = "../common/lang/spanish-iso-8859-1.inc.php";
if ($lang == 'NL') $lang_file = "../common/lang/nederlands-iso-8859-1.inc.php";
//
if (is_readable($lang_file)) 
{
	include($lang_file);
}
else
{
	include("../common/lang/english-iso-8859-1.inc.php");
	$lang = 'EN';
}
?>