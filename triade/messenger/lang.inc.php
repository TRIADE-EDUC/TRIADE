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
$lang_file = "";
$c_lang = "";
if ($lang != "") $c_lang = $lang;
if ($c_lang == "") 
{
  if (!function_exists('autoSelectLanguage'))  require("common/detect.lang.inc.php");
  $c_lang = autoSelectLanguage(array('fr', 'en', 'it', 'pt', 'br', 'it', 'ro', 'de', 'es', 'nl'), 'en');
}
$c_lang = strtoupper($c_lang);
//
if ($c_lang == 'EN') $lang_file = "common/lang/english-iso-8859-1.inc.php";
if ($c_lang == 'FR') $lang_file = "common/lang/french-iso-8859-1.inc.php";
if ($c_lang == 'BR') $lang_file = "common/lang/brazilian-iso-8859-1.inc.php";
if ($c_lang == 'PT') $lang_file = "common/lang/portuguese-iso-8859-1.inc.php";
if ($c_lang == 'IT') $lang_file = "common/lang/italian-iso-8859-1.inc.php";
if ($c_lang == 'RO') $lang_file = "common/lang/romana-iso-8859-1.inc.php";
if ($c_lang == 'DE') $lang_file = "common/lang/german-iso-8859-1.inc.php";
if ($c_lang == 'ES') $lang_file = "common/lang/spanish-iso-8859-1.inc.php";
if ($c_lang == 'NL') $lang_file = "common/lang/nederlands-iso-8859-1.inc.php";
//
if (is_readable($lang_file)) 
{
	require($lang_file);
}
else
{
	require("common/lang/english-iso-8859-1.inc.php");
	$c_lang = 'EN';
}
?>